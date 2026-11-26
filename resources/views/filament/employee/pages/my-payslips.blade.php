<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Overview Card --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-600 dark:bg-orange-950/40 dark:text-orange-400">
                        <x-heroicon-o-banknotes class="h-6 w-6" />
                    </div>
                    <div>
                        <h2 class="font-bold text-lg text-zinc-900 dark:text-white">Digital Payslip Vault</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            Access official semi-monthly salary statements, statutory deductions (SSS, PhilHealth, Pag-IBIG), and withholding tax breakdowns.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-zinc-50 px-3 py-2 text-xs text-zinc-600 dark:bg-zinc-800/60 dark:text-zinc-300">
                        Monthly Basic: <strong class="text-zinc-900 dark:text-white">₱{{ number_format($this->employee?->basic_salary ?? 0, 2) }}</strong>
                    </div>
                    <div class="rounded-lg bg-zinc-50 px-3 py-2 text-xs text-zinc-600 dark:bg-zinc-800/60 dark:text-zinc-300">
                        Daily Rate: <strong class="text-zinc-900 dark:text-white">₱{{ number_format(($this->employee?->salaryStructure?->daily_rate ?? round((($this->employee?->basic_salary ?? 0)*12)/261, 2)), 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payslips Table --}}
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm overflow-hidden dark:border-zinc-800 dark:bg-zinc-900">
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                <h3 class="font-bold text-sm text-zinc-900 dark:text-white">Issued Salary Statements</h3>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">Total Issued: {{ count($this->payslips) }}</span>
            </div>

            @if(count($this->payslips) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs divide-y divide-zinc-200 dark:divide-zinc-800">
                        <thead class="bg-zinc-50 text-zinc-500 uppercase tracking-wider dark:bg-zinc-800/50 dark:text-zinc-400">
                            <tr>
                                <th class="px-6 py-3.5 font-semibold">Pay Period</th>
                                <th class="px-6 py-3.5 font-semibold">Payout Date</th>
                                <th class="px-6 py-3.5 font-semibold text-right">Gross Earnings</th>
                                <th class="px-6 py-3.5 font-semibold text-right">Total Deductions</th>
                                <th class="px-6 py-3.5 font-semibold text-right">Net Take-Home Pay</th>
                                <th class="px-6 py-3.5 font-semibold text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                            @foreach($this->payslips as $item)
                                <tr class="hover:bg-zinc-50/70 transition dark:hover:bg-zinc-800/40">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-zinc-900 dark:text-white">{{ $item->payrollRun->name }}</div>
                                        <div class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                            {{ $item->payrollRun->cutoff_start->format('M d, Y') }} — {{ $item->payrollRun->cutoff_end->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-zinc-600 dark:text-zinc-300">
                                        {{ $item->payrollRun->payout_date->format('F d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium text-zinc-900 dark:text-zinc-100">
                                        ₱{{ number_format($item->gross_pay, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium text-rose-600 dark:text-rose-400">
                                        -₱{{ number_format($item->total_deductions, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-sm text-emerald-600 dark:text-emerald-400">
                                        ₱{{ number_format($item->net_pay, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button
                                            wire:click="downloadPayslip({{ $item->id }})"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-orange-50 px-3 py-1.5 text-xs font-semibold text-[var(--km-brand-orange-aa)] hover:bg-orange-100 transition border border-orange-200 dark:bg-orange-950/40 dark:border-orange-800/60 dark:text-[var(--km-brand-orange)] dark:hover:bg-orange-900/60"
                                        >
                                            <x-heroicon-o-document-arrow-down class="h-4 w-4" />
                                            <span>Download PDF</span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center text-zinc-500 dark:text-zinc-400">
                    <x-heroicon-o-document-magnifying-glass class="h-10 w-10 mx-auto text-zinc-400 mb-3" />
                    <p class="text-sm font-medium">No approved payslips available yet for this account.</p>
                    <p class="text-xs text-zinc-400 mt-1">Once HR finalizes and approves your entity's cutoff run, your statement will appear here.</p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
