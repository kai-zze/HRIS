<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query ?? Employee::query();
    }

    public function collection()
    {
        return $this->query->with(['entity', 'department', 'position', 'supervisor'])->get();
    }

    public function headings(): array
    {
        return [
            'Employee ID',
            'Entity',
            'Full Name',
            'Sex',
            'Civil Status',
            'Birthdate',
            'Email',
            'Contact Number',
            'Department',
            'Position',
            'Supervisor',
            'Employment Status',
            'Date Hired',
            'Regularization Date',
            'Work Schedule',
            'Basic Salary (PHP)',
        ];
    }

    public function map($employee): array
    {
        return [
            $employee->employee_number,
            $employee->entity?->short_name,
            $employee->full_name,
            $employee->sex?->getLabel() ?? ucfirst($employee->sex?->value ?? ''),
            $employee->civil_status?->getLabel() ?? ucfirst($employee->civil_status?->value ?? ''),
            $employee->birthdate?->format('F d, Y'),
            $employee->email,
            $employee->contact_number,
            $employee->department?->name ?? '—',
            $employee->position?->title ?? '—',
            $employee->supervisor?->full_name ?? '—',
            $employee->employment_status?->getLabel() ?? ucfirst($employee->employment_status?->value ?? ''),
            $employee->date_hired?->format('F d, Y'),
            $employee->regularization_date?->format('F d, Y') ?? '—',
            $employee->work_schedule,
            number_format($employee->basic_salary, 2),
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
