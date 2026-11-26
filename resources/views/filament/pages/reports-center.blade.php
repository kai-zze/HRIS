<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Card with Generous Inset --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg border border-zinc-200 bg-zinc-50 text-zinc-600 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                        <x-heroicon-o-chart-bar-square class="h-5 w-5" />
                    </div>
                    <div>
                        <h2 class="font-bold text-lg text-zinc-900 dark:text-white">Compliance & Reports Center</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                            Generate and download official statutory schedules, attendance analytics, and multi-entity workforce registries.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <label class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Filter Entity:</label>
                    <select
                        wire:model.live="selectedEntityId"
                        class="rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-xs text-zinc-800 focus:border-orange-500 focus:ring-orange-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"
                    >
                        <option value="">All 5 Operating Entities</option>
                        @foreach(\App\Models\Entity::all() as $ent)
                            <option value="{{ $ent->id }}">{{ $ent->short_name }} — {{ $ent->code }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- 4 Primary Export Cards Grid with Proper Padding & Tokenized Actions --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- 1. Headcount & Demographics --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <x-heroicon-o-users class="h-5 w-5 text-zinc-500 dark:text-zinc-400" />
                            <h3 class="font-bold text-base text-zinc-900 dark:text-white">Headcount & Demographics</h3>
                        </div>
                        <span class="text-xs text-zinc-400 font-medium">DOLE Compliant</span>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed mb-6">
                        Comprehensive employee roster broken down by entity, department, position, regularization status, civil status, and salary rates.
                    </p>
                </div>
                <button
                    wire:click="downloadHeadcountReport"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white px-4 py-2.5 text-xs font-semibold transition shadow-sm"
                >
                    <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                    <span>Download Headcount CSV</span>
                </button>
            </div>

            {{-- 2. Attendance & Tardiness --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <x-heroicon-o-clock class="h-5 w-5 text-zinc-500 dark:text-zinc-400" />
                            <h3 class="font-bold text-base text-zinc-900 dark:text-white">Attendance, Tardiness & OT</h3>
                        </div>
                        <span class="text-xs text-zinc-400 font-medium">Biometric DTR</span>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed mb-4">
                        Export daily punch analytics, calculated late minutes, undertime, absences, and overtime multipliers.
                    </p>
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <div>
                            <label class="block text-[11px] font-medium text-zinc-600 dark:text-zinc-400 mb-1">Start Date:</label>
                            <input
                                type="date"
                                wire:model="attendanceStartDate"
                                class="w-full rounded-lg border border-zinc-300 px-2.5 py-1.5 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                            />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-zinc-600 dark:text-zinc-400 mb-1">End Date:</label>
                            <input
                                type="date"
                                wire:model="attendanceEndDate"
                                class="w-full rounded-lg border border-zinc-300 px-2.5 py-1.5 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                            />
                        </div>
                    </div>
                </div>
                <button
                    wire:click="downloadAttendanceReport"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white px-4 py-2.5 text-xs font-semibold transition shadow-sm"
                >
                    <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                    <span>Download Attendance CSV</span>
                </button>
            </div>

            {{-- 3. Statutory Remittance --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <x-heroicon-o-banknotes class="h-5 w-5 text-zinc-500 dark:text-zinc-400" />
                            <h3 class="font-bold text-base text-zinc-900 dark:text-white">Statutory Remittance</h3>
                        </div>
                        <span class="text-xs text-zinc-400 font-medium">2026 Tables</span>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed mb-4">
                        Itemized remittance schedule comparing employee deductions against employer counterparts for government filing (SSS, PhilHealth, Pag-IBIG, BIR).
                    </p>
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <div>
                            <label class="block text-[11px] font-medium text-zinc-600 dark:text-zinc-400 mb-1">Calendar Year:</label>
                            <input
                                type="number"
                                wire:model="statutoryYear"
                                class="w-full rounded-lg border border-zinc-300 px-2.5 py-1.5 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                            />
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-zinc-600 dark:text-zinc-400 mb-1">Month (1-12):</label>
                            <input
                                type="number"
                                min="1"
                                max="12"
                                wire:model="statutoryMonth"
                                class="w-full rounded-lg border border-zinc-300 px-2.5 py-1.5 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                            />
                        </div>
                    </div>
                </div>
                <button
                    wire:click="downloadStatutoryReport"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white px-4 py-2.5 text-xs font-semibold transition shadow-sm"
                >
                    <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                    <span>Download Remittance CSV</span>
                </button>
            </div>

            {{-- 4. Leave Utilization & Forfeiture --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <x-heroicon-o-calendar-days class="h-5 w-5 text-zinc-500 dark:text-zinc-400" />
                            <h3 class="font-bold text-base text-zinc-900 dark:text-white">Leave Balances & Utilization</h3>
                        </div>
                        <span class="text-xs text-zinc-400 font-medium">Year-End Audit</span>
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed mb-6">
                        Annual leave reconciliation showing allocated credits, approved days taken, pending requests, and balances available for conversion or forfeiture.
                    </p>
                </div>
                <button
                    wire:click="downloadLeaveUtilizationReport"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white px-4 py-2.5 text-xs font-semibold transition shadow-sm"
                >
                    <x-heroicon-o-arrow-down-tray class="h-4 w-4" />
                    <span>Download Leave Utilization CSV</span>
                </button>
            </div>

        </div>
    </div>
</x-filament-panels::page>
