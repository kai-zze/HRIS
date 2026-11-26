<x-filament-panels::page>
    @if ($employee)
        {{-- Profile Header Card --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
                <div class="flex items-center gap-5">
                    <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-primary-500 font-semibold text-2xl text-white shadow-sm dark:bg-primary-600">
                        {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-bold text-xl text-zinc-900 tracking-tight dark:text-white">
                                {{ $employee->first_name }} {{ $employee->middle_name ? $employee->middle_name . ' ' : '' }}{{ $employee->last_name }} {{ $employee->suffix }}
                            </h2>
                            <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 font-mono font-medium text-xs text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ $employee->employee_number }}
                            </span>
                            <span class="inline-flex items-center rounded-md bg-primary-50 px-2.5 py-0.5 font-medium text-xs text-primary-700 dark:bg-primary-950/50 dark:text-primary-300">
                                {{ $employee->entity->name }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $employee->position?->title ?? 'N/A' }} &bull; {{ $employee->department?->name ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 self-stretch border-t border-zinc-100 pt-4 md:self-auto md:border-t-0 md:pt-0 dark:border-zinc-800">
                    <div class="rounded-lg bg-zinc-50 px-3 py-2 text-center dark:bg-zinc-800/50">
                        <span class="block text-[11px] font-medium text-zinc-400 uppercase tracking-wider">Status</span>
                        <span class="font-semibold text-sm text-zinc-800 dark:text-zinc-200">{{ $employee->employment_status?->getLabel() ?? 'Regular' }}</span>
                    </div>
                    <div class="rounded-lg bg-zinc-50 px-3 py-2 text-center dark:bg-zinc-800/50">
                        <span class="block text-[11px] font-medium text-zinc-400 uppercase tracking-wider">Date Hired</span>
                        <span class="font-semibold text-sm text-zinc-800 dark:text-zinc-200">{{ $employee->date_hired ? $employee->date_hired->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="rounded-lg bg-zinc-50 px-3 py-2 text-center dark:bg-zinc-800/50">
                        <span class="block text-[11px] font-medium text-zinc-400 uppercase tracking-wider">Supervisor</span>
                        <span class="font-semibold text-sm text-zinc-800 dark:text-zinc-200">{{ $employee->supervisor ? $employee->supervisor->first_name . ' ' . $employee->supervisor->last_name : 'Direct Report' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Section --}}
        <form wire:submit="save">
            {{ $this->form }}

            <div class="mt-6 flex justify-end">
                <x-filament::button type="submit" size="md">
                    Save Profile Changes
                </x-filament::button>
            </div>
        </form>

        {{-- Statutory & Government Identifiers (Masked - RA 10173 Compliant) --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-3 dark:border-zinc-800">
                <div>
                    <h3 class="font-semibold text-base text-zinc-900 dark:text-white">Government Statutory Records (RA 10173 Protected)</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Values are encrypted at rest and masked for privacy compliance. Contact HR for revisions.</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 font-medium text-emerald-700 text-xs dark:bg-emerald-950/40 dark:text-emerald-400">
                    <x-heroicon-m-lock-closed class="h-3.5 w-3.5" />
                    Encrypted
                </span>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border border-zinc-100 bg-zinc-50/50 p-3.5 dark:border-zinc-800/60 dark:bg-zinc-800/30">
                    <span class="text-xs font-medium text-zinc-500 uppercase tracking-wider">SSS Number</span>
                    <p class="mt-1 font-mono font-medium text-sm text-zinc-800 dark:text-zinc-200">
                        {{ $employee->sss_number ? '••••••••' . substr($employee->sss_number, -3) : 'Not Registered' }}
                    </p>
                </div>

                <div class="rounded-lg border border-zinc-100 bg-zinc-50/50 p-3.5 dark:border-zinc-800/60 dark:bg-zinc-800/30">
                    <span class="text-xs font-medium text-zinc-500 uppercase tracking-wider">PhilHealth Number</span>
                    <p class="mt-1 font-mono font-medium text-sm text-zinc-800 dark:text-zinc-200">
                        {{ $employee->philhealth_number ? '••••••••' . substr($employee->philhealth_number, -3) : 'Not Registered' }}
                    </p>
                </div>

                <div class="rounded-lg border border-zinc-100 bg-zinc-50/50 p-3.5 dark:border-zinc-800/60 dark:bg-zinc-800/30">
                    <span class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Pag-IBIG / HDMF</span>
                    <p class="mt-1 font-mono font-medium text-sm text-zinc-800 dark:text-zinc-200">
                        {{ $employee->pagibig_number ? '••••••••' . substr($employee->pagibig_number, -4) : 'Not Registered' }}
                    </p>
                </div>

                <div class="rounded-lg border border-zinc-100 bg-zinc-50/50 p-3.5 dark:border-zinc-800/60 dark:bg-zinc-800/30">
                    <span class="text-xs font-medium text-zinc-500 uppercase tracking-wider">TIN (BIR)</span>
                    <p class="mt-1 font-mono font-medium text-sm text-zinc-800 dark:text-zinc-200">
                        {{ $employee->tin ? '••••••••' . substr($employee->tin, -3) : 'Not Registered' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- My 201 File Documents --}}
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-3 dark:border-zinc-800">
                <div>
                    <h3 class="font-semibold text-base text-zinc-900 dark:text-white">My 201 File Documents</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Official employment credentials, certificates, and submitted requirements.</p>
                </div>
                <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                    {{ $employee->documents->count() }} file(s) on record
                </span>
            </div>

            @if ($employee->documents->isEmpty())
                <div class="py-8 text-center text-zinc-500 dark:text-zinc-400 text-sm">
                    <x-heroicon-o-document-text class="mx-auto h-8 w-8 text-zinc-400 mb-2" />
                    No digital document files currently attached to your 201 file.
                </div>
            @else
                <div class="mt-4 divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($employee->documents as $doc)
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-950/40 dark:text-primary-400">
                                    <x-heroicon-o-document-check class="h-5 w-5" />
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $doc->title }}</h4>
                                    <p class="text-xs text-zinc-500">
                                        Type: {{ $doc->document_type?->getLabel() ?? 'General Document' }}
                                        @if ($doc->expiry_date)
                                            &bull; Valid until: {{ $doc->expiry_date->format('M d, Y') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-md bg-zinc-50 px-2.5 py-1 text-xs text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                Filed on {{ $doc->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <div class="rounded-xl border border-zinc-200 bg-white p-8 text-center dark:border-zinc-800 dark:bg-zinc-900">
            <x-heroicon-o-user-minus class="mx-auto h-12 w-12 text-zinc-400 mb-3" />
            <h3 class="font-semibold text-lg text-zinc-800 dark:text-zinc-200">No Employee Profile Found</h3>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Your login account is not currently linked to an active employee profile. Please contact your entity's HR administrator.
            </p>
        </div>
    @endif
</x-filament-panels::page>
