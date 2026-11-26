<?php

namespace Tests\Feature;

use App\Filament\Employee\Pages\MyPayslips;
use App\Filament\Resources\EmployeeLoans\EmployeeLoanResource;
use App\Filament\Resources\PayrollRuns\PayrollRunResource;
use App\Filament\Resources\SssContributionBrackets\SssContributionBracketResource;
use App\Filament\Resources\ThirteenthMonths\ThirteenthMonthResource;
use App\Models\DailyTimeRecord;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\Entity;
use App\Models\PagibigSetting;
use App\Models\PayrollItem;
use App\Models\PayrollRun;
use App\Models\PhilhealthSetting;
use App\Models\SalaryStructure;
use App\Models\SssContributionBracket;
use App\Models\User;
use App\Models\WithholdingTaxBracket;
use App\Services\BankExportService;
use App\Services\PayrollComputationService;
use App\Services\ThirteenthMonthService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase6PayrollTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_statutory_tables_are_seeded_with_2026_schedules(): void
    {
        $this->assertGreaterThan(50, SssContributionBracket::count());

        $ph = PhilhealthSetting::first();
        $this->assertNotNull($ph);
        $this->assertEquals(5.00, (float) $ph->premium_rate_percent);
        $this->assertEquals(10000.00, (float) $ph->income_floor);
        $this->assertEquals(100000.00, (float) $ph->income_ceiling);

        $pagibig = PagibigSetting::first();
        $this->assertNotNull($pagibig);
        $this->assertEquals(200.00, (float) $pagibig->mandatory_employee_share);

        $this->assertGreaterThanOrEqual(6, WithholdingTaxBracket::count());
    }

    public function test_statutory_contributions_computation_matches_philippine_schedule(): void
    {
        $service = app(PayrollComputationService::class);
        $statutory = $service->calculateStatutoryContributions(25000.00);

        // MSC 25,000: Regular MSC 20,000 (EE 900, ER 1900) + WISP MSC 5,000 (EE 225, ER 475)
        $this->assertEquals(900.00, $statutory['sss_ee']);
        $this->assertEquals(1900.00, $statutory['sss_er']);
        $this->assertEquals(225.00, $statutory['sss_wisp_ee']);
        $this->assertEquals(475.00, $statutory['sss_wisp_er']);

        // PhilHealth: 25,000 * 5% = 1,250 -> 50% EE = 625, 50% ER = 625
        $this->assertEquals(625.00, $statutory['philhealth_ee']);
        $this->assertEquals(625.00, $statutory['philhealth_er']);

        // Pag-IBIG: 200 EE / 200 ER
        $this->assertEquals(200.00, $statutory['pagibig_ee']);
        $this->assertEquals(200.00, $statutory['pagibig_er']);
    }

    public function test_minimum_wage_earner_is_exempt_from_withholding_tax(): void
    {
        $employee = Employee::withoutGlobalScopes()->firstOrFail();
        SalaryStructure::updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'basic_salary' => 14000.00,
                'is_minimum_wage_earner' => true,
                'pay_frequency' => 'semi-monthly',
                'effective_date' => '2026-01-01',
                'is_active' => true,
            ]
        );

        $entity = Entity::find($employee->entity_id);
        $run = PayrollRun::create([
            'entity_id' => $entity->id,
            'name' => 'Min Wage Test Cutoff',
            'cutoff_start' => '2026-01-01',
            'cutoff_end' => '2026-01-15',
            'payout_date' => '2026-01-20',
            'period_type' => 'first_half',
            'year' => 2026,
            'month' => 1,
            'status' => 'draft',
        ]);

        $item = app(PayrollComputationService::class)->computeEmployeePayrollItem($run, $employee);

        $this->assertEquals(0.00, (float) $item->withholding_tax);
        $this->assertGreaterThan(0.00, (float) $item->net_pay);
    }

    public function test_train_law_withholding_tax_bracket_computation(): void
    {
        $service = app(PayrollComputationService::class);

        // Bracket 1: <= 10,416.99 -> 0
        $this->assertEquals(0.00, $service->calculateWithholdingTax(10000.00));

        // Bracket 2: 15,000 -> 0 + 15% of (15,000 - 10,417) = 15% of 4,583 = 687.45
        $tax = $service->calculateWithholdingTax(15000.00);
        $this->assertEquals(round((15000 - 10417) * 0.15, 2), $tax);
    }

    public function test_payroll_run_computation_generates_items_with_dtr_and_loans(): void
    {
        $employee = Employee::withoutGlobalScopes()->firstOrFail();
        $entity = Entity::find($employee->entity_id);

        // Seed a DTR with 30 mins late and 2 hours OT
        DailyTimeRecord::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'record_date' => '2026-01-05',
            ],
            [
                'entity_id' => $entity->id,
                'time_in' => '08:30:00',
                'time_out' => '19:00:00',
                'late_minutes' => 30,
                'overtime_hours' => 2.00,
                'regular_hours' => 8.00,
                'status' => 'late',
            ]
        );

        $loan = EmployeeLoan::create([
            'employee_id' => $employee->id,
            'loan_type' => 'Company Cash Advance',
            'principal_amount' => 5000.00,
            'term_months' => 5,
            'monthly_amortization' => 1000.00,
            'amortization_per_cutoff' => 500.00,
            'balance_remaining' => 5000.00,
            'start_date' => '2026-01-01',
            'status' => 'active',
        ]);

        $run = PayrollRun::create([
            'entity_id' => $entity->id,
            'name' => 'January 2026 1st Cutoff Run',
            'cutoff_start' => '2026-01-01',
            'cutoff_end' => '2026-01-15',
            'payout_date' => '2026-01-20',
            'period_type' => 'first_half',
            'year' => 2026,
            'month' => 1,
            'status' => 'approved',
        ]);

        app(PayrollComputationService::class)->computePayrollRun($run);

        $item = PayrollItem::where('payroll_run_id', $run->id)->where('employee_id', $employee->id)->firstOrFail();

        $this->assertGreaterThan(0, $item->gross_pay);
        $this->assertGreaterThan(0, $item->regular_ot_pay);
        $this->assertGreaterThan(0, $item->late_deduction);
        $this->assertGreaterThan(0, $item->loan_deductions);
        $this->assertGreaterThan(0, $item->net_pay);

        // Verify loan balance was amortized
        $loan->refresh();
        $this->assertEquals(4500.00, (float) $loan->balance_remaining);
    }

    public function test_thirteenth_month_pay_computation_and_tax_exemption_cap(): void
    {
        $employee = Employee::withoutGlobalScopes()->firstOrFail();
        $entityId = $employee->entity_id;

        $service = app(ThirteenthMonthService::class);
        $record = $service->computeEmployeeThirteenthMonth($employee, 2026);

        $this->assertNotNull($record);
        $this->assertGreaterThan(0, (float) $record->thirteenth_month_pay);
        $this->assertLessThanOrEqual(90000.00, (float) $record->tax_exempt_amount);
        $this->assertEquals(0.00, (float) $record->taxable_amount);
    }

    public function test_bank_export_csv_generation(): void
    {
        $run = PayrollRun::with('items')->firstOrFail();
        $service = app(BankExportService::class);
        $response = $service->exportPayrollRunCsv($run);

        $this->assertEquals('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('BANK-PAYROLL-', $response->headers->get('Content-Disposition'));
    }

    public function test_payslip_pdf_generation(): void
    {
        $item = PayrollItem::with(['payrollRun', 'payrollRun.entity', 'employee'])->firstOrFail();

        $pdf = Pdf::loadView('pdf.payslip', ['item' => $item]);
        $output = $pdf->output();

        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF', $output);
    }

    public function test_admin_and_portal_payroll_resources_render(): void
    {
        $admin = User::where('email', 'superadmin@km100group.ph')->firstOrFail();
        $this->actingAs($admin);

        $this->get(PayrollRunResource::getUrl('index'))->assertOk();
        $this->get(EmployeeLoanResource::getUrl('index'))->assertOk();
        $this->get(SssContributionBracketResource::getUrl('index'))->assertOk();
        $this->get(ThirteenthMonthResource::getUrl('index'))->assertOk();
        $this->get('/portal/my-payslips')->assertOk();
    }
}
