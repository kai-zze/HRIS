<?php

namespace App\Services;

use App\Models\PayrollRun;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BankExportService
{
    /**
     * Generate BDO/BPI standard batch payroll CSV stream.
     */
    public function exportPayrollRunCsv(PayrollRun $run): StreamedResponse
    {
        $filename = sprintf(
            'BANK-PAYROLL-%s-%s.csv',
            $run->entity->code ?? 'KM100',
            $run->payout_date->format('Ymd')
        );

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($run) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                'Entity Code',
                'Employee Number',
                'Employee Name',
                'Bank Name',
                'Bank Account Number',
                'Net Amount (PHP)',
                'Payment Description',
                'Payout Date',
            ]);

            $items = $run->items()->with(['employee', 'employee.entity'])->get();

            foreach ($items as $item) {
                fputcsv($handle, [
                    $item->employee->entity->code ?? '',
                    $item->employee->employee_number,
                    sprintf('%s, %s %s', $item->employee->last_name, $item->employee->first_name, $item->employee->suffix ?? ''),
                    $item->bank_name ?: 'BDO Unibank',
                    $item->bank_account_number ?: 'N/A',
                    number_format((float) $item->net_pay, 2, '.', ''),
                    $run->name,
                    $run->payout_date->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
