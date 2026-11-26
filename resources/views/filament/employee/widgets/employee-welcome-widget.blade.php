<x-filament-widgets::widget>
    @if ($this->employee)
        <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-primary-500 font-bold text-xl text-white shadow-sm dark:bg-primary-600">
                        {{ substr($this->employee->first_name, 0, 1) }}{{ substr($this->employee->last_name, 0, 1) }}
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-bold text-lg text-zinc-900 tracking-tight dark:text-white">
                                Welcome, {{ $this->employee->first_name }}!
                            </h2>
                            <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 font-mono text-xs font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ $this->employee->employee_number }}
                            </span>
                            <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-700 dark:bg-primary-950/50 dark:text-primary-300">
                                {{ $this->employee->entity->name }}
                            </span>
                        </div>
                        <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $this->employee->position?->title ?? 'Employee' }} &bull; {{ $this->employee->department?->name ?? 'Staff' }}
                        </p>
                    </div>
                </div>

                {{-- Quick Self-Service Action Shortcuts --}}
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ url('/portal/my-leave-requests/create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-primary-500 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-primary-600 focus:outline-none dark:bg-primary-600 dark:hover:bg-primary-500">
                        <x-heroicon-m-calendar-days class="h-4 w-4" />
                        File Leave
                    </a>
                    <a href="{{ url('/portal/my-overtime-requests/create') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 shadow-sm hover:bg-zinc-50 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                        <x-heroicon-m-clock class="h-4 w-4 text-zinc-500" />
                        File Overtime
                    </a>
                    <a href="{{ url('/portal/my-daily-time-records') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 shadow-sm hover:bg-zinc-50 focus:outline-none dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                        <x-heroicon-m-finger-print class="h-4 w-4 text-zinc-500" />
                        My DTR
                    </a>
                </div>
            </div>
        </div>
    @endif
</x-filament-widgets::widget>
