<x-filament-panels::page>
    <div class="space-y-6 max-w-4xl">
        {{-- General Configuration Card --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="font-bold text-base text-zinc-900 dark:text-white mb-1">Corporate & Regional Parameters</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-6">Configured in strict accordance with the Philippine Labor Code and Bureau of Internal Revenue (BIR) standards.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Corporate Holding Name</label>
                    <input
                        type="text"
                        wire:model="companyName"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Standard Timezone</label>
                    <input
                        type="text"
                        wire:model="timezone"
                        disabled
                        class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-xs text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/40 dark:text-zinc-400"
                    />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Operating Currency</label>
                    <input
                        type="text"
                        wire:model="currency"
                        disabled
                        class="w-full rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-xs text-zinc-500 dark:border-zinc-800 dark:bg-zinc-800/40 dark:text-zinc-400"
                    />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Annual Work Days Factor</label>
                    <input
                        type="number"
                        wire:model="workDaysPerYear"
                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                    />
                    <span class="text-[11px] text-zinc-400">Used for Daily Rate calculation: (Basic Salary × 12) ÷ 261</span>
                </div>
            </div>
        </div>

        {{-- Labor Standards & Statutory Settings Card --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="font-bold text-base text-zinc-900 dark:text-white mb-1">Philippine Statutory Rules & Caps</h3>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-6">Governing Republic Act 10963 (TRAIN Law) and Presidential Decree 851 (13th Month Pay).</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-zinc-900 dark:text-white">Minimum Wage Tax Exemption</span>
                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Active</span>
                    </div>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                        Statutory Minimum Wage Earners are completely exempt from income tax on basic pay, OT, night diff, and holiday pay.
                    </p>
                </div>
                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-zinc-900 dark:text-white">13th Month Tax-Exempt Ceiling</span>
                        <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 font-mono">₱90,000.00</span>
                    </div>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                        Under the TRAIN Law, 13th month and other bonus benefits up to ₱90,000 are 100% tax-free. Excess is subject to withholding tax.
                    </p>
                </div>
            </div>
        </div>

        {{-- Data Privacy Act (RA 10173) Governance Card --}}
        <div class="rounded-xl border border-emerald-200 bg-emerald-50/40 p-6 dark:border-emerald-900/60 dark:bg-emerald-950/20">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400">
                    <x-heroicon-o-shield-check class="h-6 w-6" />
                </div>
                <div>
                    <h3 class="font-bold text-sm text-emerald-950 dark:text-emerald-200">Data Privacy Act Compliance (RA 10173)</h3>
                    <p class="text-xs text-emerald-800 dark:text-emerald-300 mt-1 leading-relaxed">
                        All government-issued identification numbers (<strong>TIN</strong>, <strong>SSS</strong>, <strong>PhilHealth</strong>, <strong>Pag-IBIG</strong>) and employee bank account numbers are protected with <strong>AES-256-CBC encryption</strong> at rest in the database. Records are dynamically masked with asterisks in all UI table views and only unmasked upon explicit individual inspection by authorized personnel.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button
                wire:click="saveSettings"
                class="inline-flex items-center gap-2 rounded-lg bg-[var(--km-brand-orange-aa)] hover:opacity-95 text-white px-5 py-2.5 text-xs font-bold transition shadow-sm"
            >
                <x-heroicon-o-check class="h-4 w-4" />
                <span>Save Configuration</span>
            </button>
        </div>
    </div>
</x-filament-panels::page>
