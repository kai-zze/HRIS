<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip - {{ $item->employee->employee_number }} - {{ $item->payrollRun->name }}</title>
    <style>
        @page {
            margin: 12mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #18181b;
            line-height: 1.35;
        }
        .header {
            border-bottom: 2px solid #f2851d;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header table {
            width: 100%;
        }
        .company-title {
            font-size: 14px;
            font-weight: bold;
            color: #18181b;
            text-transform: uppercase;
        }
        .doc-title {
            font-size: 12px;
            font-weight: bold;
            color: #c2510a;
            margin-top: 2px;
        }
        .meta-text {
            font-size: 9px;
            color: #52525b;
        }
        table.info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            background-color: #f8f8f9;
            border: 1px solid #e4e4e7;
        }
        table.info-grid td {
            padding: 4px 8px;
            font-size: 9.5px;
        }
        .label {
            font-weight: bold;
            color: #52525b;
        }
        .value {
            color: #18181b;
        }
        table.two-col {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.two-col > tbody > tr > td {
            vertical-align: top;
            width: 50%;
        }
        .section-header {
            background-color: #3b3b3d;
            color: #ffffff;
            font-size: 9.5px;
            font-weight: bold;
            padding: 3px 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table.breakdown {
            width: 100%;
            border-collapse: collapse;
        }
        table.breakdown td {
            padding: 3.5px 6px;
            border-bottom: 1px solid #f4f4f5;
        }
        table.breakdown td.amount {
            text-align: right;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 600;
        }
        .total-row {
            background-color: #fafafa;
            font-weight: bold;
            border-top: 1px solid #d4d4d8;
            border-bottom: 1px solid #d4d4d8;
        }
        .net-pay-box {
            background-color: #fff8ed;
            border: 2px solid #f2851d;
            border-radius: 4px;
            padding: 8px 12px;
            margin-top: 10px;
            margin-bottom: 12px;
        }
        .net-pay-amount {
            font-size: 16px;
            font-weight: bold;
            color: #c2510a;
            font-family: 'Courier New', Courier, monospace;
        }
        .statutory-er-box {
            background-color: #f4f4f5;
            border: 1px dashed #d4d4d8;
            padding: 5px 8px;
            font-size: 8.5px;
            color: #52525b;
            margin-bottom: 10px;
        }
        .footer-table {
            width: 100%;
            margin-top: 15px;
            border-top: 1px solid #e4e4e7;
            padding-top: 8px;
            font-size: 8.5px;
            color: #71717a;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                @if ($item->payrollRun->entity && file_exists($item->payrollRun->entity->logo_absolute_path))
                    <td style="width: 140px; vertical-align: middle;">
                        <img src="{{ $item->payrollRun->entity->logo_absolute_path }}" alt="Entity Logo" style="max-height: 60px; max-width: 130px; object-fit: contain; border-radius: 4px;">
                    </td>
                @endif
                <td style="width: 65%; vertical-align: middle;">
                    <div class="company-title">{{ $item->payrollRun->entity->name ?? 'KM100 Group' }}</div>
                    <div class="doc-title">CONFIDENTIAL SALARY PAYSLIP</div>
                    <div class="meta-text">
                        Cutoff Period: {{ \Carbon\Carbon::parse($item->payrollRun->cutoff_start)->format('F d, Y') }} — {{ \Carbon\Carbon::parse($item->payrollRun->cutoff_end)->format('F d, Y') }}
                        | Payout Date: {{ \Carbon\Carbon::parse($item->payrollRun->payout_date)->format('F d, Y') }}
                    </div>
                </td>
                <td style="width: 30%; text-align: right;">
                    <div style="font-size: 12px; font-weight: bold; color: #18181b;">{{ $item->payrollRun->name }}</div>
                    <div class="meta-text">Run Status: {{ strtoupper($item->payrollRun->status) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Employee Information Grid -->
    <table class="info-grid">
        <tr>
            <td style="width: 20%;"><span class="label">Employee ID:</span> <span class="value">{{ $item->employee->employee_number }}</span></td>
            <td style="width: 35%;"><span class="label">Employee Name:</span> <span class="value">{{ $item->employee->last_name }}, {{ $item->employee->first_name }} {{ $item->employee->suffix }}</span></td>
            <td style="width: 25%;"><span class="label">Department:</span> <span class="value">{{ $item->employee->department->name ?? 'N/A' }}</span></td>
            <td style="width: 20%;"><span class="label">Position:</span> <span class="value">{{ $item->employee->position->title ?? 'N/A' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">TIN:</span> <span class="value">{{ $item->employee->masked_tin }}</span></td>
            <td><span class="label">SSS No:</span> <span class="value">{{ $item->employee->masked_sss }}</span></td>
            <td><span class="label">PhilHealth:</span> <span class="value">{{ $item->employee->masked_philhealth }}</span></td>
            <td><span class="label">Pag-IBIG:</span> <span class="value">{{ $item->employee->masked_pagibig }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Monthly Basic:</span> <span class="value">₱{{ number_format($item->basic_salary, 2) }}</span></td>
            <td><span class="label">Daily Rate:</span> <span class="value">₱{{ number_format($item->daily_rate, 2) }}</span> (₱{{ number_format($item->hourly_rate, 2) }}/hr)</td>
            <td><span class="label">Disbursement:</span> <span class="value">{{ ucwords(str_replace('_', ' ', $item->payment_method)) }}</span></td>
            <td><span class="label">Account No:</span> <span class="value">{{ $item->bank_account_number ?: 'N/A' }} ({{ $item->bank_name ?: 'Bank' }})</span></td>
        </tr>
    </table>

    <!-- Two-column Earnings & Deductions -->
    <table class="two-col">
        <tr>
            <!-- Left Column: Earnings -->
            <td style="padding-right: 6px;">
                <div class="section-header">Earnings</div>
                <table class="breakdown">
                    <tr>
                        <td>Basic Pay (Semi-Monthly)</td>
                        <td class="amount">₱{{ number_format($item->basic_pay_earned, 2) }}</td>
                    </tr>
                    @if($item->regular_ot_hours > 0)
                    <tr>
                        <td>Regular Overtime ({{ $item->regular_ot_hours }} hrs @ 125%)</td>
                        <td class="amount">₱{{ number_format($item->regular_ot_pay, 2) }}</td>
                    </tr>
                    @endif
                    @if($item->rest_day_ot_hours > 0)
                    <tr>
                        <td>Rest Day Overtime ({{ $item->rest_day_ot_hours }} hrs @ 130%)</td>
                        <td class="amount">₱{{ number_format($item->rest_day_ot_pay, 2) }}</td>
                    </tr>
                    @endif
                    @if($item->holiday_ot_hours > 0)
                    <tr>
                        <td>Holiday Premium ({{ $item->holiday_ot_hours }} hrs @ 200%)</td>
                        <td class="amount">₱{{ number_format($item->holiday_ot_pay, 2) }}</td>
                    </tr>
                    @endif
                    @if($item->taxable_allowances > 0)
                    <tr>
                        <td>Taxable Allowances</td>
                        <td class="amount">₱{{ number_format($item->taxable_allowances, 2) }}</td>
                    </tr>
                    @endif
                    @if($item->non_taxable_allowances > 0)
                    <tr>
                        <td>Non-Taxable Allowances (De Minimis)</td>
                        <td class="amount">₱{{ number_format($item->non_taxable_allowances, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td><strong>TOTAL GROSS PAY</strong></td>
                        <td class="amount"><strong>₱{{ number_format($item->gross_pay, 2) }}</strong></td>
                    </tr>
                </table>
            </td>

            <!-- Right Column: Deductions -->
            <td style="padding-left: 6px;">
                <div class="section-header">Deductions</div>
                <table class="breakdown">
                    @if($item->absent_deduction > 0)
                    <tr>
                        <td>Absences ({{ $item->absent_days }} days)</td>
                        <td class="amount">-₱{{ number_format($item->absent_deduction, 2) }}</td>
                    </tr>
                    @endif
                    @if($item->late_deduction > 0)
                    <tr>
                        <td>Tardiness / Late ({{ $item->late_minutes }} mins)</td>
                        <td class="amount">-₱{{ number_format($item->late_deduction, 2) }}</td>
                    </tr>
                    @endif
                    @if($item->undertime_deduction > 0)
                    <tr>
                        <td>Undertime ({{ $item->undertime_minutes }} mins)</td>
                        <td class="amount">-₱{{ number_format($item->undertime_deduction, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>SSS Contribution (EE)</td>
                        <td class="amount">-₱{{ number_format($item->sss_ee, 2) }}</td>
                    </tr>
                    @if($item->sss_wisp_ee > 0)
                    <tr>
                        <td>SSS WISP / MPF (EE)</td>
                        <td class="amount">-₱{{ number_format($item->sss_wisp_ee, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>PhilHealth Contribution (EE)</td>
                        <td class="amount">-₱{{ number_format($item->philhealth_ee, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Pag-IBIG / HDMF Contribution (EE)</td>
                        <td class="amount">-₱{{ number_format($item->pagibig_ee, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Withholding Tax (TRAIN Law)</td>
                        <td class="amount">-₱{{ number_format($item->withholding_tax, 2) }}</td>
                    </tr>
                    @if($item->loan_deductions > 0)
                    <tr>
                        <td>Loan Amortization</td>
                        <td class="amount">-₱{{ number_format($item->loan_deductions, 2) }}</td>
                    </tr>
                    @endif
                    @if($item->other_deductions > 0)
                    <tr>
                        <td>Other Deductions</td>
                        <td class="amount">-₱{{ number_format($item->other_deductions, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td><strong>TOTAL DEDUCTIONS</strong></td>
                        <td class="amount"><strong>-₱{{ number_format($item->total_deductions, 2) }}</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Net Pay High-Density Callout -->
    <div class="net-pay-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 65%;">
                    <div style="font-size: 10px; font-weight: bold; color: #52525b; text-transform: uppercase;">Net Take-Home Pay (Philippine Pesos)</div>
                    <div class="net-pay-amount">₱{{ number_format($item->net_pay, 2) }}</div>
                    <div style="font-size: 8.5px; color: #71717a; margin-top: 2px;">
                        Gross Pay: ₱{{ number_format($item->gross_pay, 2) }} &nbsp;|&nbsp; Deductions: -₱{{ number_format($item->total_deductions, 2) }}
                    </div>
                </td>
                <td style="width: 35%; text-align: right; vertical-align: middle;">
                    <div style="font-size: 9px; color: #52525b;">Disbursed to:</div>
                    <div style="font-weight: bold; font-size: 10px;">{{ $item->bank_name ?: 'BDO Unibank' }} - {{ $item->bank_account_number ?: 'Direct Credit' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Employer Contribution Summary (Transparency) -->
    <div class="statutory-er-box">
        <strong>EMPLOYER STATUTORY COUNTERPART (Non-deductible from employee):</strong> &nbsp;
        SSS ER: ₱{{ number_format($item->sss_er, 2) }} &nbsp;|&nbsp;
        SSS EC: ₱{{ number_format($item->sss_ec, 2) }} &nbsp;|&nbsp;
        SSS WISP ER: ₱{{ number_format($item->sss_wisp_er, 2) }} &nbsp;|&nbsp;
        PhilHealth ER: ₱{{ number_format($item->philhealth_er, 2) }} &nbsp;|&nbsp;
        Pag-IBIG ER: ₱{{ number_format($item->pagibig_er, 2) }}
    </div>

    <!-- Footer & Signatures -->
    <table class="footer-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <strong>NOTICE:</strong> This payslip is strictly private and confidential. Any discrepancy must be reported to KM100 Group Human Resources within five (5) working days from payout date.
            </td>
            <td style="width: 40%; text-align: right; vertical-align: top;">
                <div style="margin-top: 15px; border-top: 1px solid #71717a; display: inline-block; width: 180px; text-align: center; padding-top: 3px;">
                    Employee Signature / Acknowledgment
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
