<x-filament-panels::page>
    <div class="space-y-6">
        @if ($this->announcements->isEmpty())
            <div class="rounded-xl border border-zinc-200 bg-white p-12 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <x-heroicon-o-megaphone class="mx-auto h-12 w-12 text-zinc-400 mb-3" />
                <h3 class="font-semibold text-lg text-zinc-800 dark:text-zinc-200">No Announcements at this Time</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    All upcoming group-wide and entity advisories will be published here.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6">
                @foreach ($this->announcements as $item)
                    <div class="relative overflow-hidden rounded-xl border {{ $item->is_pinned ? 'border-primary-400 bg-primary-50/20 dark:border-primary-700/60 dark:bg-primary-950/20' : 'border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900' }} p-6 shadow-sm">
                        @if ($item->is_pinned)
                            <div class="absolute top-0 right-0 rounded-bl-lg bg-primary-500 px-3 py-1 font-semibold text-[11px] text-white uppercase tracking-wider shadow-sm">
                                Pinned Advisory
                            </div>
                        @endif

                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="inline-flex items-center rounded-md bg-zinc-100 px-2.5 py-0.5 text-xs font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                {{ $item->category }}
                            </span>
                            @if ($item->entity)
                                <span class="inline-flex items-center rounded-md bg-zinc-100 px-2.5 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                    {{ $item->entity->name }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-primary-50 px-2.5 py-0.5 text-xs font-medium text-primary-700 dark:bg-primary-950/50 dark:text-primary-300">
                                    All KM100 Entities
                                </span>
                            @endif
                            <span class="text-xs text-zinc-400">
                                &bull; Published on {{ $item->published_at?->format('F d, Y \a\t h:i A') }}
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white mt-1">
                            {{ $item->title }}
                        </h3>

                        <div class="prose prose-zinc dark:prose-invert max-w-none mt-3 text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed">
                            {!! $item->content !!}
                        </div>

                        @if ($item->author)
                            <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs text-zinc-500">
                                <span>Announced by <strong>{{ $item->author->name }}</strong></span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-panels::page>
