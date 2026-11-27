<div class="flex items-center">
    @if ($canSwitch)
        <div class="flex items-center gap-2.5 text-xs text-zinc-600 dark:text-zinc-300">
            @if ($activeEntity)
                <div class="flex h-8.5 min-w-[34px] max-w-[85px] shrink-0 items-center justify-center rounded-md bg-white px-1.5 py-0.5 border border-zinc-200 shadow-2xs dark:border-zinc-700 dark:bg-zinc-800" title="{{ $activeEntity->name }}">
                    <img
                        src="{{ $activeEntity->logo_url }}"
                        alt="{{ $activeEntity->short_name }} Logo"
                        class="h-7 w-auto max-w-[75px] object-contain"
                        loading="lazy"
                    />
                </div>
            @else
                <div class="flex h-8.5 w-8.5 shrink-0 items-center justify-center rounded-md bg-white p-0.5 border border-zinc-200 shadow-2xs dark:border-zinc-700 dark:bg-zinc-800" title="KM100 Group - All Operating Businesses">
                    <img
                        src="{{ asset('images/km100-logo.jpg') }}"
                        alt="KM100 Group Logo"
                        class="h-7 w-7 object-contain"
                        loading="lazy"
                    />
                </div>
            @endif
            <span class="hidden sm:inline text-zinc-600 dark:text-zinc-400 font-medium">Business Scope:</span>
            <label for="km100-entity-switcher" class="sr-only">Active Entity Scope</label>
            <select
                id="km100-entity-switcher"
                wire:model.live="entityId"
                aria-label="Active Entity Scope"
                class="km-focus-ring cursor-pointer rounded-md border border-zinc-200 bg-white dark:bg-zinc-800 py-1 pe-7 ps-2.5 text-xs font-semibold text-zinc-800 dark:border-zinc-700 dark:text-zinc-200 shadow-2xs"
            >
                <option value="" class="bg-white text-zinc-900 dark:bg-zinc-900 dark:text-zinc-100">
                    All Operating Businesses
                </option>
                @foreach ($entities as $entity)
                    <option value="{{ $entity->id }}" class="bg-white text-zinc-900 dark:bg-zinc-900 dark:text-zinc-100">
                        {{ $entity->short_name }}
                    </option>
                @endforeach
            </select>
        </div>
    @elseif ($lockedEntity)
        <div class="flex items-center gap-2.5 text-xs text-zinc-600 dark:text-zinc-300">
            <div class="flex h-8.5 min-w-[34px] max-w-[85px] shrink-0 items-center justify-center rounded-md bg-white px-1.5 py-0.5 border border-zinc-200 shadow-2xs dark:border-zinc-700 dark:bg-zinc-800" title="{{ $lockedEntity->name }}">
                <img
                    src="{{ $lockedEntity->logo_url }}"
                    alt="{{ $lockedEntity->short_name }} Logo"
                    class="h-7 w-auto max-w-[75px] object-contain"
                    loading="lazy"
                />
            </div>
            <span class="text-zinc-600 dark:text-zinc-400 font-medium">Company:</span>
            <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $lockedEntity->short_name }}</span>
        </div>
    @endif
</div>
