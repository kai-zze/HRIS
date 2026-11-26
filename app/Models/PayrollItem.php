<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_run_id',
        'employee_id',
        'basic_salary',
        'basic_pay_earned',
        'daily_rate',
        'hourly_rate',
        'absent_days',
        'absent_deduction',
        'late_minutes',
        'late_deduction',
        'undertime_minutes',
        'undertime_deduction',
        'regular_ot_hours',
        'regular_ot_pay',
        'rest_day_ot_hours',
        'rest_day_ot_pay',
        'holiday_ot_hours',
        'holiday_ot_pay',
        'taxable_allowances',
        'non_taxable_allowances',
        'gross_pay',
        'sss_ee',
        'sss_er',
        'sss_ec',
        'sss_wisp_ee',
        'sss_wisp_er',
        'philhealth_ee',
        'philhealth_er',
        'pagibig_ee',
        'pagibig_er',
        'taxable_income',
        'withholding_tax',
        'loan_deductions',
        'other_deductions',
        'total_deductions',
        'net_pay',
        'payment_method',
        'bank_name',
        'bank_account_number',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'basic_pay_earned' => 'decimal:2',
            'daily_rate' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'absent_days' => 'decimal:2',
            'absent_deduction' => 'decimal:2',
            'late_minutes' => 'integer',
            'late_deduction' => 'decimal:2',
            'undertime_minutes' => 'integer',
            'undertime_deduction' => 'decimal:2',
            'regular_ot_hours' => 'decimal:2',
            'regular_ot_pay' => 'decimal:2',
            'rest_day_ot_hours' => 'decimal:2',
            'rest_day_ot_pay' => 'decimal:2',
            'holiday_ot_hours' => 'decimal:2',
            'holiday_ot_pay' => 'decimal:2',
            'taxable_allowances' => 'decimal:2',
            'non_taxable_allowances' => 'decimal:2',
            'gross_pay' => 'decimal:2',
            'sss_ee' => 'decimal:2',
            'sss_er' => 'decimal:2',
            'sss_ec' => 'decimal:2',
            'sss_wisp_ee' => 'decimal:2',
            'sss_wisp_er' => 'decimal:2',
            'philhealth_ee' => 'decimal:2',
            'philhealth_er' => 'decimal:2',
            'pagibig_ee' => 'decimal:2',
            'pagibig_er' => 'decimal:2',
            'taxable_income' => 'decimal:2',
            'withholding_tax' => 'decimal:2',
            'loan_deductions' => 'decimal:2',
            'other_deductions' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'net_pay' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
