<div class="flex items-center">
    @if ($canSwitch)
        <div class="flex items-center text-xs text-zinc-600 dark:text-zinc-300">
            <label for="km100-entity-switcher" class="sr-only">Active Entity Scope</label>
            <select
                id="km100-entity-switcher"
                wire:model.live="entityId"
                aria-label="Active Entity Scope"
                class="km-focus-ring cursor-pointer rounded-md border border-zinc-200 bg-white dark:bg-zinc-800 py-1.5 pe-7 ps-2.5 text-xs font-semibold text-zinc-800 dark:border-zinc-700 dark:text-zinc-200 shadow-2xs"
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
        <div class="flex items-center text-xs text-zinc-600 dark:text-zinc-300">
            <span class="rounded-md border border-zinc-200 bg-zinc-50 px-2.5 py-1 font-semibold text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                {{ $lockedEntity->short_name }}
            </span>
        </div>
    @endif
</div>
