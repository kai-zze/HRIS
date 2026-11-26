<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee 201 File - {{ $employee->employee_number }}</title>
    <style>
        @page {
            margin: 20mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #18181b;
            line-height: 1.4;
        }
        .header {
            border-bottom: 2px solid #f2851d;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .header table {
            width: 100%;
        }
        .company-title {
            font-size: 16px;
            font-weight: bold;
            color: #18181b;
            text-transform: uppercase;
        }
        .doc-title {
            font-size: 13px;
            font-weight: bold;
            color: #c2510a;
            margin-top: 3px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #ffffff;
            background-color: #3b3b3d;
            padding: 4px 8px;
            margin-top: 15px;
            margin-bottom: 8px;
            text-transform: uppercase;
            border-radius: 2px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.data-table th, table.data-table td {
            padding: 5px 6px;
            border: 1px solid #e4e4e7;
            text-align: left;
        }
        table.data-table th {
            background-color: #f4f4f5;
            font-weight: bold;
            width: 25%;
            color: #52525b;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-weight: bold;
            font-size: 10px;
            border-radius: 3px;
            background-color: #f4f4f5;
            border: 1px solid #d4d4d8;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #71717a;
            border-top: 1px solid #e4e4e7;
            padding-top: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="company-title">{{ $employee->entity?->name ?? 'KM100 GROUP' }}</div>
                    <div class="doc-title">CONFIDENTIAL 201 EMPLOYEE FILE RECORD</div>
                    <div style="color: #71717a; font-size: 10px; margin-top: 2px;">
                        Data Privacy Act (RA 10173) Protected Document &bull; Generated: {{ now()->format('F d, Y h:i A') }}
                    </div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div style="font-size: 12px; font-weight: bold; font-family: monospace;">
                        ID: {{ $employee->employee_number }}
                    </div>
                    <div style="margin-top: 4px;">
                        <span class="badge" style="background-color: #ffedd5; color: #9a3412; border-color: #fdba74;">
                            {{ $employee->employment_status?->getLabel() ?? 'Active' }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">1. Personal Information</div>
    <table class="data-table">
        <tr>
            <th>Full Name</th>
            <td><strong>{{ $employee->full_name }}</strong></td>
            <th>Sex / Civil Status</th>
            <td>{{ $employee->sex?->getLabel() }} / {{ $employee->civil_status?->getLabel() }}</td>
        </tr>
        <tr>
            <th>Birthdate</th>
            <td>{{ $employee->birthdate?->format('F d, Y') }} ({{ $employee->birthdate?->age }} yrs old)</td>
            <th>Contact Number</th>
            <td>{{ $employee->contact_number ?? '—' }}</td>
        </tr>
        <tr>
            <th>Personal Email</th>
            <td>{{ $employee->email ?? '—' }}</td>
            <th>Work Email</th>
            <td>{{ $employee->user?->email ?? '—' }}</td>
        </tr>
        <tr>
            <th>Residential Address</th>
            <td colspan="3">{{ $employee->residential_address ?? '—' }}</td>
        </tr>
    </table>

    <div class="section-title">2. Employment & Assignment</div>
    <table class="data-table">
        <tr>
            <th>Company / Entity</th>
            <td>{{ $employee->entity?->name }} ({{ $employee->entity?->code }})</td>
            <th>Department</th>
            <td>{{ $employee->department?->name ?? '—' }}</td>
        </tr>
        <tr>
            <th>Position Title</th>
            <td><strong>{{ $employee->position?->title ?? '—' }}</strong></td>
            <th>Immediate Supervisor</th>
            <td>{{ $employee->supervisor?->full_name ?? '—' }}</td>
        </tr>
        <tr>
            <th>Date Hired</th>
            <td>{{ $employee->date_hired?->format('F d, Y') }}</td>
            <th>Regularization Date</th>
            <td>{{ $employee->regularization_date?->format('F d, Y') ?? '—' }}</td>
        </tr>
        <tr>
            <th>Work Schedule</th>
            <td>{{ $employee->work_schedule }}</td>
            <th>Basic Monthly Pay</th>
            <td style="font-family: monospace; font-weight: bold;">
                PHP {{ number_format($employee->basic_salary, 2) }}
            </td>
        </tr>
    </table>

    <div class="section-title">3. Statutory Registrations (Masked)</div>
    <table class="data-table">
        <tr>
            <th>BIR TIN</th>
            <td style="font-family: monospace;">{{ $employee->masked_tin }}</td>
            <th>SSS Number</th>
            <td style="font-family: monospace;">{{ $employee->masked_sss }}</td>
        </tr>
        <tr>
            <th>PhilHealth Number</th>
            <td style="font-family: monospace;">{{ $employee->masked_philhealth }}</td>
            <th>Pag-IBIG Number</th>
            <td style="font-family: monospace;">{{ $employee->masked_pagibig }}</td>
        </tr>
        <tr>
            <th>Payroll Bank</th>
            <td>{{ $employee->bank_name ?? '—' }}</td>
            <th>Bank Account No.</th>
            <td style="font-family: monospace;">{{ $employee->masked_bank_account }}</td>
        </tr>
    </table>

    @if ($employee->movements->count() > 0)
        <div class="section-title">4. Employment Movement History</div>
        <table class="data-table">
            <thead>
                <tr style="background-color: #f4f4f5;">
                    <th style="width: 20%;">Date</th>
                    <th style="width: 25%;">Movement Type</th>
                    <th style="width: 30%;">Details / Positions</th>
                    <th style="width: 25%;">Salary</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employee->movements as $movement)
                    <tr>
                        <td>{{ $movement->effective_date?->format('F d, Y') }}</td>
                        <td><strong>{{ $movement->movement_type?->getLabel() }}</strong></td>
                        <td>
                            @if ($movement->toEntity && $movement->from_entity_id !== $movement->to_entity_id)
                                Transfer to {{ $movement->toEntity->short_name }}<br>
                            @endif
                            {{ $movement->toPosition?->title ?? $employee->position?->title ?? '—' }}
                        </td>
                        <td style="font-family: monospace;">
                            @if ($movement->new_salary)
                                PHP {{ number_format($movement->new_salary, 2) }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        KM100 HR Information System &bull; Confidential &bull; For authorized HR and Management personnel only &bull; Page 1 of 1
    </div>
</body>
</html>
