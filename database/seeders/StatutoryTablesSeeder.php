<?php

namespace Database\Seeders;

use App\Models\PagibigSetting;
use App\Models\PhilhealthSetting;
use App\Models\SssContributionBracket;
use App\Models\WithholdingTaxBracket;
use Illuminate\Database\Seeder;

class StatutoryTablesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. SSS Contribution Brackets 2026 (Brackets with ₱500 MSC increments from ₱4,000 to ₱35,000)
        SssContributionBracket::query()->delete();

        $brackets = [];
        // First bracket: below ₱4,250 -> MSC ₱4,000
        $brackets[] = [
            'range_min' => 0.00,
            'range_max' => 4249.99,
            'monthly_salary_credit' => 4000.00,
            'regular_employee_share' => 180.00, // 4.5% of 4000
            'regular_employer_share' => 380.00, // 9.5% of 4000
            'ec_employer_share' => 10.00,
            'wisp_employee_share' => 0.00,
            'wisp_employer_share' => 0.00,
            'effective_year' => 2026,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Incremental brackets: ₱4,250 to ₱34,750
        $currentMin = 4250.00;
        $msc = 4500.00;

        while ($msc <= 35000.00) {
            $currentMax = ($msc == 35000.00) ? null : ($currentMin + 499.99);

            $regularMsc = min($msc, 20000.00);
            $wispMsc = max(0.00, $msc - 20000.00);

            $regularEe = round($regularMsc * 0.045, 2);
            $regularEr = round($regularMsc * 0.095, 2);
            $ec = ($msc >= 15000.00) ? 30.00 : 10.00;

            $wispEe = round($wispMsc * 0.045, 2);
            $wispEr = round($wispMsc * 0.095, 2);

            $now = now();
            $brackets[] = [
                'range_min' => $currentMin,
                'range_max' => $currentMax,
                'monthly_salary_credit' => $msc,
                'regular_employee_share' => $regularEe,
                'regular_employer_share' => $regularEr,
                'ec_employer_share' => $ec,
                'wisp_employee_share' => $wispEe,
                'wisp_employer_share' => $wispEr,
                'effective_year' => 2026,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $currentMin += 500.00;
            $msc += 500.00;
        }

        SssContributionBracket::insert($brackets);

        // 2. PhilHealth 2026 Settings (5.00% split equally, floor ₱10,000, ceiling ₱100,000)
        PhilhealthSetting::query()->delete();
        PhilhealthSetting::create([
            'premium_rate_percent' => 5.00,
            'employee_share_percent' => 50.00,
            'employer_share_percent' => 50.00,
            'income_floor' => 10000.00,
            'income_ceiling' => 100000.00,
            'effective_year' => 2026,
            'is_active' => true,
        ]);

        // 3. Pag-IBIG 2026 Settings (Mandatory ₱200 EE / ₱200 ER based on ₱10,000 cap @ 2%)
        PagibigSetting::query()->delete();
        PagibigSetting::create([
            'mandatory_employee_share' => 200.00,
            'mandatory_employer_share' => 200.00,
            'max_compensation_cap' => 10000.00,
            'employee_rate_percent' => 2.00,
            'employer_rate_percent' => 2.00,
            'effective_year' => 2026,
            'is_active' => true,
        ]);

        // 4. BIR TRAIN Law Semi-Monthly Withholding Tax Brackets (Effective 2026)
        WithholdingTaxBracket::query()->delete();
        $taxBrackets = [
            [
                'pay_frequency' => 'semi-monthly',
                'bracket_level' => 1,
                'range_min' => 0.00,
                'range_max' => 10416.99,
                'base_tax' => 0.00,
                'excess_rate_percent' => 0.00,
                'effective_year' => 2026,
                'is_active' => true,
            ],
            [
                'pay_frequency' => 'semi-monthly',
                'bracket_level' => 2,
                'range_min' => 10417.00,
                'range_max' => 16666.99,
                'base_tax' => 0.00,
                'excess_rate_percent' => 15.00,
                'effective_year' => 2026,
                'is_active' => true,
            ],
            [
                'pay_frequency' => 'semi-monthly',
                'bracket_level' => 3,
                'range_min' => 16667.00,
                'range_max' => 33332.99,
                'base_tax' => 937.50,
                'excess_rate_percent' => 20.00,
                'effective_year' => 2026,
                'is_active' => true,
            ],
            [
                'pay_frequency' => 'semi-monthly',
                'bracket_level' => 4,
                'range_min' => 33333.00,
                'range_max' => 83332.99,
                'base_tax' => 4270.70,
                'excess_rate_percent' => 25.00,
                'effective_year' => 2026,
                'is_active' => true,
            ],
            [
                'pay_frequency' => 'semi-monthly',
                'bracket_level' => 5,
                'range_min' => 83333.00,
                'range_max' => 333332.99,
                'base_tax' => 16770.70,
                'excess_rate_percent' => 30.00,
                'effective_year' => 2026,
                'is_active' => true,
            ],
            [
                'pay_frequency' => 'semi-monthly',
                'bracket_level' => 6,
                'range_min' => 333333.00,
                'range_max' => null,
                'base_tax' => 91770.70,
                'excess_rate_percent' => 35.00,
                'effective_year' => 2026,
                'is_active' => true,
            ],
        ];

        foreach ($taxBrackets as $tb) {
            WithholdingTaxBracket::create($tb);
        }
    }
}
