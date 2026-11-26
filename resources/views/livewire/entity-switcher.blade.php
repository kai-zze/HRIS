<div class="flex items-center">
    @if ($canSwitch)
        <div class="flex items-center gap-1.5 text-xs text-zinc-600 dark:text-zinc-300">
            <span class="hidden sm:inline text-zinc-400 dark:text-zinc-500">Business Scope:</span>
            <label for="km100-entity-switcher" class="sr-only">Active Entity Scope</label>
            <select
                id="km100-entity-switcher"
                wire:model.live="entityId"
                aria-label="Active Entity Scope"
                class="km-focus-ring cursor-pointer rounded border border-zinc-200 bg-transparent py-1 pe-7 ps-2 text-xs font-medium text-zinc-800 dark:border-zinc-700 dark:text-zinc-200"
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
        <div class="text-xs text-zinc-600 dark:text-zinc-300">
            <span class="text-zinc-400 dark:text-zinc-500">Company:</span>
            <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $lockedEntity->short_name }}</span>
        </div>
    @endif
</div>
