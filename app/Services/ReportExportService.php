<?php

namespace App\Services;

use App\Models\DailyTimeRecord;
use App\Models\Employee;
use App\Models\Entity;
use App\Models\LeaveBalance;
use App\Models\PayrollItem;
use App\Models\PayrollRun;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportService
{
    /**
     * 1. Export Headcount & Workforce Demographics Report
     */
    public function exportHeadcountReport(?int $entityId = null): StreamedResponse
    {
        $filename = 'KM100-Headcount-Report-' . date('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($entityId) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Entity Code',
                'Entity Name',
                'Employee Number',
                'Last Name',
                'First Name',
                'Department',
                'Position',
                'Employment Status',
                'Sex / Gender',
                'Civil Status',
                'Date Hired',
                'Regularization Date',
                'Monthly Basic Salary (PHP)',
                'Daily Rate (PHP)',
            ]);

            $query = Employee::withoutGlobalScopes()->with(['entity', 'department', 'position']);
            if ($entityId) {
                $query->where('entity_id', $entityId);
            }

            foreach ($query->get() as $emp) {
                fputcsv($handle, [
                    $emp->entity->code ?? '',
                    $emp->entity->name ?? '',
                    $emp->employee_number,
                    $emp->last_name,
                    $emp->first_name,
                    $emp->department->name ?? 'N/A',
                    $emp->position->title ?? 'N/A',
                    $emp->employment_status->getLabel(),
                    $emp->sex?->value ?? '',
                    $emp->civil_status?->value ?? '',
                    $emp->date_hired?->format('Y-m-d') ?? '',
                    $emp->regularization_date?->format('Y-m-d') ?? '',
                    number_format((float) $emp->basic_salary, 2, '.', ''),
                    number_format((float) ($emp->salaryStructure?->daily_rate ?? round(($emp->basic_salary * 12) / 261, 2)), 2, '.', ''),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * 2. Export Attendance, Tardiness & Overtime Summary Report
     */
    public function exportAttendanceReport(string $startDate, string $endDate, ?int $entityId = null): StreamedResponse
    {
        $filename = "KM100-Attendance-Report-{$startDate}-to-{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($startDate, $endDate, $entityId) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Entity',
                'Emp #',
                'Employee Name',
                'Date',
                'Day',
                'Time In',
                'Time Out',
                'Status',
                'Late (mins)',
                'Undertime (mins)',
                'Regular Hours',
                'Regular OT Hours',
                'Night Diff Hours',
            ]);

            $query = DailyTimeRecord::withoutGlobalScopes()
                ->with(['employee', 'employee.entity'])
                ->whereBetween('record_date', [$startDate, $endDate]);

            if ($entityId) {
                $query->where('entity_id', $entityId);
            }

            foreach ($query->orderBy('record_date')->get() as $dtr) {
                fputcsv($handle, [
                    $dtr->employee->entity->code ?? '',
                    $dtr->employee->employee_number,
                    sprintf('%s, %s', $dtr->employee->last_name, $dtr->employee->first_name),
                    $dtr->record_date->format('Y-m-d'),
                    $dtr->record_date->format('D'),
                    $dtr->time_in ?? '—',
                    $dtr->time_out ?? '—',
                    strtoupper($dtr->status),
                    $dtr->late_minutes,
                    $dtr->undertime_minutes,
                    number_format((float) $dtr->regular_hours, 2, '.', ''),
                    number_format((float) $dtr->overtime_hours, 2, '.', ''),
                    number_format((float) $dtr->night_diff_hours, 2, '.', ''),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * 3. Export Statutory Remittance Summary Report (SSS, PhilHealth, Pag-IBIG, BIR)
     */
    public function exportStatutoryRemittanceReport(int $year, ?int $month = null, ?int $entityId = null): StreamedResponse
    {
        $filename = "KM100-Statutory-Remittance-{$year}-M{$month}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($year, $month, $entityId) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Entity',
                'Emp #',
                'Employee Name',
                'TIN',
                'SSS Number',
                'PhilHealth Number',
                'Pag-IBIG Number',
                'Basic Salary',
                'Gross Pay',
                'SSS EE',
                'SSS ER',
                'SSS EC',
                'SSS WISP EE',
                'SSS WISP ER',
                'Total SSS Remittance',
                'PhilHealth EE',
                'PhilHealth ER',
                'Total PhilHealth Remittance',
                'Pag-IBIG EE',
                'Pag-IBIG ER',
                'Total Pag-IBIG Remittance',
                'Withholding Tax (BIR)',
                'Net Take-Home Pay',
            ]);

            $query = PayrollItem::with(['employee', 'employee.entity', 'payrollRun'])
                ->whereHas('payrollRun', function ($q) use ($year, $month, $entityId) {
                    $q->where('year', $year)
                        ->whereIn('status', ['approved', 'locked']);
                    if ($month) {
                        $q->where('month', $month);
                    }
                    if ($entityId) {
                        $q->where('entity_id', $entityId);
                    }
                });

            foreach ($query->get() as $item) {
                $totalSss = $item->sss_ee + $item->sss_er + $item->sss_ec + $item->sss_wisp_ee + $item->sss_wisp_er;
                $totalPh = $item->philhealth_ee + $item->philhealth_er;
                $totalHdmf = $item->pagibig_ee + $item->pagibig_er;

                fputcsv($handle, [
                    $item->employee->entity->code ?? '',
                    $item->employee->employee_number,
                    sprintf('%s, %s', $item->employee->last_name, $item->employee->first_name),
                    $item->employee->masked_tin,
                    $item->employee->masked_sss,
                    $item->employee->masked_philhealth,
                    $item->employee->masked_pagibig,
                    number_format((float) $item->basic_salary, 2, '.', ''),
                    number_format((float) $item->gross_pay, 2, '.', ''),
                    number_format((float) $item->sss_ee, 2, '.', ''),
                    number_format((float) $item->sss_er, 2, '.', ''),
                    number_format((float) $item->sss_ec, 2, '.', ''),
                    number_format((float) $item->sss_wisp_ee, 2, '.', ''),
                    number_format((float) $item->sss_wisp_er, 2, '.', ''),
                    number_format((float) $totalSss, 2, '.', ''),
                    number_format((float) $item->philhealth_ee, 2, '.', ''),
                    number_format((float) $item->philhealth_er, 2, '.', ''),
                    number_format((float) $totalPh, 2, '.', ''),
                    number_format((float) $item->pagibig_ee, 2, '.', ''),
                    number_format((float) $item->pagibig_er, 2, '.', ''),
                    number_format((float) $totalHdmf, 2, '.', ''),
                    number_format((float) $item->withholding_tax, 2, '.', ''),
                    number_format((float) $item->net_pay, 2, '.', ''),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * 4. Export Leave Utilization & Balance Report
     */
    public function exportLeaveUtilizationReport(int $year, ?int $entityId = null): StreamedResponse
    {
        $filename = "KM100-Leave-Utilization-{$year}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($year, $entityId) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Entity',
                'Emp #',
                'Employee Name',
                'Department',
                'Leave Type',
                'Year',
                'Allocated Credits',
                'Used Days',
                'Pending Days',
                'Remaining Balance',
            ]);

            $query = LeaveBalance::with(['employee', 'employee.entity', 'employee.department', 'leaveType'])
                ->where('year', $year);

            if ($entityId) {
                $query->whereHas('employee', fn ($q) => $q->where('entity_id', $entityId));
            }

            foreach ($query->get() as $lb) {
                fputcsv($handle, [
                    $lb->employee->entity->code ?? '',
                    $lb->employee->employee_number,
                    sprintf('%s, %s', $lb->employee->last_name, $lb->employee->first_name),
                    $lb->employee->department->name ?? 'N/A',
                    $lb->leaveType->name ?? 'N/A',
                    $lb->year,
                    number_format((float) $lb->allocated_days, 1, '.', ''),
                    number_format((float) $lb->used_days, 1, '.', ''),
                    number_format((float) $lb->pending_days, 1, '.', ''),
                    number_format((float) $lb->remaining_days, 1, '.', ''),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
