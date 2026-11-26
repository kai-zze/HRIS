<x-filament-panels::page>
    <div class="space-y-6">
        @if ($ranking)
            {{-- Performance Score & Department Rank Header --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-md bg-primary-100 px-2.5 py-0.5 text-xs font-bold text-primary-700 dark:bg-primary-950/60 dark:text-primary-300">
                                {{ $ranking->evaluationPeriod?->name ?? 'Current Evaluation Cycle' }}
                            </span>
                            <span class="text-xs text-zinc-400">
                                Period: {{ $ranking->evaluationPeriod?->start_date?->format('M Y') }} — {{ $ranking->evaluationPeriod?->end_date?->format('M Y') }}
                            </span>
                        </div>
                        <h2 class="mt-2 text-xl font-bold text-zinc-900 dark:text-white">
                            Personal Performance Score & Ranking
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            Evaluated across 5 institutional factors: Supervisor Appraisal (50%), Attendance (20%), Training (15%), Discipline (10%), and Commendations (5%).
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <span class="block text-xs font-semibold uppercase text-zinc-400">Final Weighted Score</span>
                            <span class="text-3xl font-extrabold text-primary-600 dark:text-primary-400">
                                {{ number_format($ranking->final_score, 1) }}<span class="text-sm font-medium text-zinc-400">/100</span>
                            </span>
                        </div>
                        <div class="rounded-xl bg-zinc-50 p-3 text-center border border-zinc-100 dark:bg-zinc-800/50 dark:border-zinc-800">
                            <span class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Department Rank</span>
                            <span class="font-extrabold text-lg text-zinc-800 dark:text-zinc-200">
                                {{ $ranking->rank_department ? "#{$ranking->rank_department}" : '—' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Component Score Breakdown Grid --}}
                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5 border-t border-zinc-100 pt-6 dark:border-zinc-800">
                    <div class="rounded-lg bg-zinc-50/60 p-3.5 border border-zinc-100 dark:bg-zinc-800/30 dark:border-zinc-800">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-zinc-500 font-medium">Supervisor Eval (50%)</span>
                            <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ number_format($ranking->evaluation_score, 1) }}</span>
                        </div>
                        <div class="mt-2 h-1.5 w-full rounded-full bg-zinc-200 dark:bg-zinc-700 overflow-hidden">
                            <div class="h-full bg-primary-500" style="width: {{ $ranking->evaluation_score }}%"></div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-zinc-50/60 p-3.5 border border-zinc-100 dark:bg-zinc-800/30 dark:border-zinc-800">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-zinc-500 font-medium">Attendance (20%)</span>
                            <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ number_format($ranking->attendance_score, 1) }}</span>
                        </div>
                        <div class="mt-2 h-1.5 w-full rounded-full bg-zinc-200 dark:bg-zinc-700 overflow-hidden">
                            <div class="h-full bg-emerald-500" style="width: {{ $ranking->attendance_score }}%"></div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-zinc-50/60 p-3.5 border border-zinc-100 dark:bg-zinc-800/30 dark:border-zinc-800">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-zinc-500 font-medium">Training (15%)</span>
                            <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ number_format($ranking->training_score, 1) }}</span>
                        </div>
                        <div class="mt-2 h-1.5 w-full rounded-full bg-zinc-200 dark:bg-zinc-700 overflow-hidden">
                            <div class="h-full bg-sky-500" style="width: {{ $ranking->training_score }}%"></div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-zinc-50/60 p-3.5 border border-zinc-100 dark:bg-zinc-800/30 dark:border-zinc-800">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-zinc-500 font-medium">Discipline (10%)</span>
                            <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ number_format($ranking->discipline_score, 1) }}</span>
                        </div>
                        <div class="mt-2 h-1.5 w-full rounded-full bg-zinc-200 dark:bg-zinc-700 overflow-hidden">
                            <div class="h-full bg-purple-500" style="width: {{ $ranking->discipline_score }}%"></div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-zinc-50/60 p-3.5 border border-zinc-100 dark:bg-zinc-800/30 dark:border-zinc-800">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-zinc-500 font-medium">Commendations (5%)</span>
                            <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ number_format($ranking->commendation_score, 1) }}</span>
                        </div>
                        <div class="mt-2 h-1.5 w-full rounded-full bg-zinc-200 dark:bg-zinc-700 overflow-hidden">
                            <div class="h-full bg-amber-500" style="width: {{ $ranking->commendation_score }}%"></div>
                        </div>
                    </div>
                </div>

                @if ($ranking->manual_adjustment_score != 0)
                    <div class="mt-4 rounded-lg bg-amber-50/70 p-3 text-xs text-amber-900 border border-amber-200 dark:bg-amber-950/30 dark:border-amber-900 dark:text-amber-200 flex items-start gap-2">
                        <x-heroicon-m-information-circle class="h-4 w-4 text-amber-600 mt-0.5 shrink-0" />
                        <div>
                            <strong>HR Management Adjustment ({{ sprintf('%+0.1f pts', $ranking->manual_adjustment_score) }}):</strong>
                            {{ $ranking->manual_adjustment_reason ?: 'Management calibration.' }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Qualitative Feedback & Recommendations --}}
            @if ($evaluation)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <h3 class="font-semibold text-sm text-zinc-900 dark:text-white flex items-center gap-2">
                            <x-heroicon-m-hand-thumb-up class="h-4 w-4 text-emerald-500" />
                            Key Strengths & Commendations
                        </h3>
                        <p class="mt-3 text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed">
                            {{ $evaluation->strengths ?: 'Consistently demonstrates strong commitment to departmental standards and team collaboration.' }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <h3 class="font-semibold text-sm text-zinc-900 dark:text-white flex items-center gap-2">
                            <x-heroicon-m-arrow-trending-up class="h-4 w-4 text-primary-500" />
                            Development & Action Plan
                        </h3>
                        <p class="mt-3 text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed">
                            {{ $evaluation->improvements ?: 'Continue pursuing advanced competency certificates and maintain zero-tardiness attendance.' }}
                        </p>
                    </div>
                </div>
            @endif
        @else
            <div class="rounded-xl border border-zinc-200 bg-white p-12 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <x-heroicon-o-chart-bar-square class="mx-auto h-12 w-12 text-zinc-400 mb-3" />
                <h3 class="font-semibold text-lg text-zinc-800 dark:text-zinc-200">No Ranking Data Yet</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Performance evaluations and 5-factor rankings for your department are currently in review by HR.
                </p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
