<?php

namespace App\Exports;

use App\Models\DailyTimeRecord;
use App\Models\Entity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyAttendanceSummaryExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected ?int $entityId;
    protected int $year;
    protected int $month;

    public function __construct(?int $entityId = null, ?int $year = null, ?int $month = null)
    {
        $this->entityId = $entityId;
        $this->year = $year ?? (int) date('Y');
        $this->month = $month ?? (int) date('m');
    }

    public function collection()
    {
        $query = DailyTimeRecord::with(['employee.entity', 'employee.department'])
            ->whereYear('record_date', $this->year)
            ->whereMonth('record_date', $this->month);

        if ($this->entityId) {
            $query->where('entity_id', $this->entityId);
        }

        return $query->orderBy('record_date')->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Employee ID',
            'Employee Name',
            'Entity',
            'Department',
            'Time In',
            'Time Out',
            'Status',
            'Late (mins)',
            'Undertime (mins)',
            'Regular Hours',
            'Overtime Hours',
            'Source',
        ];
    }

    public function map($dtr): array
    {
        return [
            $dtr->record_date?->format('F d, Y'),
            $dtr->employee?->employee_number,
            $dtr->employee?->full_name,
            $dtr->employee?->entity?->short_name,
            $dtr->employee?->department?->name ?? '—',
            $dtr->time_in,
            $dtr->time_out,
            $dtr->status?->getLabel() ?? ucfirst($dtr->status?->value ?? ''),
            $dtr->late_minutes,
            $dtr->undertime_minutes,
            $dtr->regular_hours,
            $dtr->overtime_hours,
            strtoupper($dtr->source),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3B3B3D'],
                ],
            ],
        ];
    }
}
