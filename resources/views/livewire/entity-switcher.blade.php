<div class="flex items-center">
    @if ($canSwitch)
        <div class="flex items-center gap-2.5 text-xs text-zinc-600 dark:text-zinc-300" style="display: flex; align-items: center; gap: 0.6rem;">
            @if ($activeEntity)
                <div
                    class="flex shrink-0 items-center justify-center overflow-hidden"
                    style="height: 40px; min-width: 40px; max-width: 150px; padding: 1px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border: none !important; outline: none !important; box-shadow: none !important; background: transparent !important;"
                    title="{{ $activeEntity->name }}"
                >
                    <img
                        src="{{ $activeEntity->logo_url }}"
                        alt="{{ $activeEntity->short_name }} Logo"
                        class="object-contain"
                        style="height: 36px; width: auto; max-width: 145px; max-height: 36px; object-fit: contain; display: block; border: none !important; outline: none !important; box-shadow: none !important;"
                        loading="lazy"
                    />
                </div>
            @else
                <div
                    class="flex shrink-0 items-center justify-center overflow-hidden"
                    style="height: 40px; width: 40px; padding: 1px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border: none !important; outline: none !important; box-shadow: none !important; background: transparent !important;"
                    title="KM100 Group - All Operating Businesses"
                >
                    <img
                        src="{{ asset('images/km100-logo.jpg') }}"
                        alt="KM100 Group Logo"
                        class="object-contain"
                        style="height: 36px; width: 36px; max-height: 36px; max-width: 36px; object-fit: contain; display: block; border: none !important; outline: none !important; box-shadow: none !important;"
                        loading="lazy"
                    />
                </div>
            @endif
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
        <div class="flex items-center gap-2.5 text-xs text-zinc-600 dark:text-zinc-300" style="display: flex; align-items: center; gap: 0.6rem;">
            <div
                class="flex shrink-0 items-center justify-center overflow-hidden"
                style="height: 40px; min-width: 40px; max-width: 150px; padding: 1px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border: none !important; outline: none !important; box-shadow: none !important; background: transparent !important;"
                title="{{ $lockedEntity->name }}"
            >
                <img
                    src="{{ $lockedEntity->logo_url }}"
                    alt="{{ $lockedEntity->short_name }} Logo"
                    class="object-contain"
                    style="height: 36px; width: auto; max-width: 145px; max-height: 36px; object-fit: contain; display: block; border: none !important; outline: none !important; box-shadow: none !important;"
                    loading="lazy"
                />
            </div>
            <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $lockedEntity->short_name }}</span>
        </div>
    @endif
</div>
