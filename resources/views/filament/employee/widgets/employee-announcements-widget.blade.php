<x-filament-widgets::widget>
    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex items-center justify-between border-b border-zinc-100 pb-3 dark:border-zinc-800">
            <div class="flex items-center gap-2">
                <x-heroicon-o-megaphone class="h-5 w-5 text-primary-500" />
                <h3 class="font-bold text-sm text-zinc-900 dark:text-white">Bulletins & Notices</h3>
            </div>
            <a href="{{ url('/portal/company-announcements') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400">
                View All &rarr;
            </a>
        </div>

        @if ($this->announcements->isEmpty())
            <div class="py-6 text-center text-xs text-zinc-400">
                No active announcements for your company.
            </div>
        @else
            <div class="mt-3 divide-y divide-zinc-100 dark:divide-zinc-800">
                @foreach ($this->announcements as $notice)
                    <div class="py-2.5">
                        <div class="flex items-center gap-2">
                            @if ($notice->is_pinned)
                                <span class="rounded bg-primary-100 px-1.5 py-0.5 text-[10px] font-bold text-primary-700 dark:bg-primary-950/60 dark:text-primary-300">
                                    PINNED
                                </span>
                            @endif
                            <span class="rounded bg-zinc-100 px-1.5 py-0.5 text-[10px] font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                {{ $notice->category }}
                            </span>
                            <span class="text-[11px] text-zinc-400">
                                {{ $notice->published_at?->format('M d') }}
                            </span>
                        </div>
                        <h4 class="mt-1 font-semibold text-xs text-zinc-800 dark:text-zinc-200 line-clamp-1">
                            {{ $notice->title }}
                        </h4>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
