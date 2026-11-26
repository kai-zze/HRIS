<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Salary Structures
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->decimal('basic_salary', 12, 2);
            $table->string('pay_frequency')->default('semi-monthly'); // semi-monthly, monthly, weekly
            $table->string('payment_method')->default('bank_transfer'); // bank_transfer, cash, check
            $table->string('bank_name')->nullable(); // BDO, BPI, etc.
            $table->string('bank_account_number')->nullable();
            $table->boolean('is_minimum_wage_earner')->default(false); // Tax-exempt under TRAIN Law
            $table->decimal('daily_rate', 10, 2)->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->date('effective_date')->default('2026-01-01');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['employee_id', 'is_active'], 'sal_struct_emp_active_idx');
        });

        // 2. Recurring Allowances
        Schema::create('salary_allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('name'); // Transportation, Meal, Communication, Housing
            $table->decimal('amount', 10, 2);
            $table->string('frequency')->default('per_cutoff'); // per_cutoff, monthly
            $table->boolean('is_taxable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['employee_id', 'is_active'], 'sal_allow_emp_active_idx');
        });

        // 3. Recurring Deductions
        Schema::create('salary_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('name'); // Uniform, Savings, Cooperative, Union
            $table->decimal('amount', 10, 2);
            $table->string('frequency')->default('per_cutoff'); // per_cutoff, monthly
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['employee_id', 'is_active'], 'sal_deduct_emp_active_idx');
        });

        // 4. SSS Contribution Table (2026 Philippine Schedule)
        Schema::create('sss_contribution_brackets', function (Blueprint $table) {
            $table->id();
            $table->decimal('range_min', 10, 2);
            $table->decimal('range_max', 10, 2)->nullable();
            $table->decimal('monthly_salary_credit', 10, 2);
            $table->decimal('regular_employee_share', 10, 2);
            $table->decimal('regular_employer_share', 10, 2);
            $table->decimal('ec_employer_share', 10, 2)->default(10.00); // ₱10 or ₱30 EC
            $table->decimal('wisp_employee_share', 10, 2)->default(0.00); // WISP / MPF for MSC > ₱20,000
            $table->decimal('wisp_employer_share', 10, 2)->default(0.00);
            $table->integer('effective_year')->default(2026);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['effective_year', 'range_min', 'range_max'], 'sss_bracket_range_idx');
        });

        // 5. PhilHealth Contribution Parameters (2026 Schedule: 5.0% split equally)
        Schema::create('philhealth_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('premium_rate_percent', 5, 2)->default(5.00);
            $table->decimal('employee_share_percent', 5, 2)->default(50.00);
            $table->decimal('employer_share_percent', 5, 2)->default(50.00);
            $table->decimal('income_floor', 10, 2)->default(10000.00);
            $table->decimal('income_ceiling', 10, 2)->default(100000.00);
            $table->integer('effective_year')->default(2026);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Pag-IBIG (HDMF) Contribution Settings (Mandatory ₱200 EE / ₱200 ER)
        Schema::create('pagibig_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('mandatory_employee_share', 10, 2)->default(200.00);
            $table->decimal('mandatory_employer_share', 10, 2)->default(200.00);
            $table->decimal('max_compensation_cap', 10, 2)->default(10000.00);
            $table->decimal('employee_rate_percent', 5, 2)->default(2.00);
            $table->decimal('employer_rate_percent', 5, 2)->default(2.00);
            $table->integer('effective_year')->default(2026);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 7. Withholding Tax Brackets (BIR TRAIN Law semi-monthly & monthly brackets)
        Schema::create('withholding_tax_brackets', function (Blueprint $table) {
            $table->id();
            $table->string('pay_frequency')->default('semi-monthly'); // semi-monthly, monthly
            $table->integer('bracket_level'); // 1, 2, 3, 4, 5, 6
            $table->decimal('range_min', 10, 2);
            $table->decimal('range_max', 10, 2)->nullable();
            $table->decimal('base_tax', 10, 2)->default(0.00);
            $table->decimal('excess_rate_percent', 5, 2)->default(0.00);
            $table->integer('effective_year')->default(2026);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['pay_frequency', 'effective_year', 'bracket_level'], 'withholding_tax_freq_idx');
        });

        // 8. Employee Loans
        Schema::create('employee_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('loan_type'); // SSS Salary Loan, Pag-IBIG Multi-Purpose, Pag-IBIG Calamity, Company Cash Advance
            $table->string('reference_number')->nullable();
            $table->decimal('principal_amount', 10, 2);
            $table->integer('term_months');
            $table->decimal('monthly_amortization', 10, 2);
            $table->decimal('amortization_per_cutoff', 10, 2);
            $table->decimal('balance_remaining', 10, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status')->default('active'); // active, paid, suspended
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'status'], 'emp_loans_status_idx');
        });

        // 9. Payroll Runs (Multi-Company Cutoffs)
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->string('name'); // e.g. "January 1-15, 2026 Cutoff - KM100-STC"
            $table->date('cutoff_start');
            $table->date('cutoff_end');
            $table->date('payout_date');
            $table->string('period_type')->default('first_half'); // first_half, second_half, thirteenth_month, special
            $table->integer('year')->default(2026);
            $table->integer('month')->default(1);
            $table->string('status')->default('draft'); // draft, review, approved, locked
            $table->decimal('total_gross', 14, 2)->default(0.00);
            $table->decimal('total_deductions', 14, 2)->default(0.00);
            $table->decimal('total_net', 14, 2)->default(0.00);
            $table->integer('total_employees')->default(0);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['entity_id', 'cutoff_start', 'cutoff_end'], 'pr_entity_cutoff_idx');
            $table->index(['status'], 'pr_status_idx');
        });

        // 10. Individual Payroll Items per Employee
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained('payroll_runs')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('basic_pay_earned', 10, 2);
            $table->decimal('daily_rate', 10, 2);
            $table->decimal('hourly_rate', 10, 2);

            // Attendance deductions & OT earnings from DTR
            $table->decimal('absent_days', 5, 2)->default(0.00);
            $table->decimal('absent_deduction', 10, 2)->default(0.00);
            $table->integer('late_minutes')->default(0);
            $table->decimal('late_deduction', 10, 2)->default(0.00);
            $table->integer('undertime_minutes')->default(0);
            $table->decimal('undertime_deduction', 10, 2)->default(0.00);
            $table->decimal('regular_ot_hours', 6, 2)->default(0.00);
            $table->decimal('regular_ot_pay', 10, 2)->default(0.00);
            $table->decimal('rest_day_ot_hours', 6, 2)->default(0.00);
            $table->decimal('rest_day_ot_pay', 10, 2)->default(0.00);
            $table->decimal('holiday_ot_hours', 6, 2)->default(0.00);
            $table->decimal('holiday_ot_pay', 10, 2)->default(0.00);

            // Allowances
            $table->decimal('taxable_allowances', 10, 2)->default(0.00);
            $table->decimal('non_taxable_allowances', 10, 2)->default(0.00);
            $table->decimal('gross_pay', 12, 2);

            // Statutory Contributions (Mandatory Philippine Split)
            $table->decimal('sss_ee', 10, 2)->default(0.00);
            $table->decimal('sss_er', 10, 2)->default(0.00);
            $table->decimal('sss_ec', 10, 2)->default(0.00);
            $table->decimal('sss_wisp_ee', 10, 2)->default(0.00);
            $table->decimal('sss_wisp_er', 10, 2)->default(0.00);

            $table->decimal('philhealth_ee', 10, 2)->default(0.00);
            $table->decimal('philhealth_er', 10, 2)->default(0.00);

            $table->decimal('pagibig_ee', 10, 2)->default(0.00);
            $table->decimal('pagibig_er', 10, 2)->default(0.00);

            // Taxable Income & Withholding Tax
            $table->decimal('taxable_income', 12, 2)->default(0.00);
            $table->decimal('withholding_tax', 10, 2)->default(0.00);

            // Loans & Other Deductions
            $table->decimal('loan_deductions', 10, 2)->default(0.00);
            $table->decimal('other_deductions', 10, 2)->default(0.00);
            $table->decimal('total_deductions', 12, 2);
            $table->decimal('net_pay', 12, 2);

            // Payment Details
            $table->string('payment_method')->default('bank_transfer');
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->json('metadata')->nullable(); // detail breakdowns

            $table->timestamps();

            $table->unique(['payroll_run_id', 'employee_id'], 'pr_item_run_emp_unique');
            $table->index(['employee_id'], 'pr_item_employee_idx');
        });

        // 11. 13th Month Pay Records
        Schema::create('thirteenth_month_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->integer('year');
            $table->decimal('total_basic_pay_earned', 14, 2);
            $table->decimal('months_worked', 4, 2)->default(12.00);
            $table->decimal('thirteenth_month_pay', 12, 2);
            $table->decimal('tax_exempt_amount', 12, 2); // Up to ₱90,000 exempt under TRAIN Law
            $table->decimal('taxable_amount', 12, 2)->default(0.00);
            $table->string('status')->default('pending'); // pending, paid
            $table->timestamps();

            $table->unique(['entity_id', 'employee_id', 'year'], 'tm_entity_emp_year_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thirteenth_month_records');
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payroll_runs');
        Schema::dropIfExists('employee_loans');
        Schema::dropIfExists('withholding_tax_brackets');
        Schema::dropIfExists('pagibig_settings');
        Schema::dropIfExists('philhealth_settings');
        Schema::dropIfExists('sss_contribution_brackets');
        Schema::dropIfExists('salary_deductions');
        Schema::dropIfExists('salary_allowances');
        Schema::dropIfExists('salary_structures');
    }
};
