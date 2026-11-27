@php
    $context = app(\App\Services\EntityContextService::class);
    $activeEntity = $context->activeEntity();
    $user = auth()->user();

    if (!$activeEntity && $user && $user->entity) {
        $activeEntity = $user->entity;
    }

    $title = $activeEntity ? ($activeEntity->short_name ?: $activeEntity->name) : 'KM100 Group';
    $logoUrl = $activeEntity ? $activeEntity->logo_url : asset('images/km100-logo.jpg');
@endphp

<div class="flex items-center py-1" style="display: flex; align-items: center;">
    <div
        class="flex shrink-0 items-center justify-center overflow-hidden"
        style="height: 50px; min-width: 50px; max-width: 220px; padding: 1px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; overflow: hidden; border: none !important; outline: none !important; box-shadow: none !important; background: transparent !important;"
    >
        <img
            src="{{ $logoUrl }}"
            alt="{{ $title }} Logo"
            class="object-contain"
            style="height: 48px; width: auto; max-width: 210px; max-height: 48px; object-fit: contain; display: block; border: none !important; outline: none !important; box-shadow: none !important;"
            loading="eager"
        />
    </div>
</div>
