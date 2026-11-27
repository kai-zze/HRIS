@php
    $context = app(\App\Services\EntityContextService::class);
    $activeEntity = $context->activeEntity();
    $user = auth()->user();

    if (!$activeEntity && $user && $user->entity) {
        $activeEntity = $user->entity;
    }

    $industryMeta = [
        'REDGOOSE'  => 'Restaurant Operations',
        'KNOAH'     => 'Coffee & Hospitality',
        'PETC'      => 'Emission Testing Center',
        'KM100-STC' => 'Skills & Training Center',
        'STC'       => 'Technical Vocational Center',
    ];

    $title = $activeEntity ? ($activeEntity->short_name ?: $activeEntity->name) : 'KM100 Group';
    $subtitle = $activeEntity ? ($industryMeta[$activeEntity->code] ?? 'Operating Business Unit') : 'Enterprise Multi-Business HRIS';
    $logoUrl = $activeEntity ? $activeEntity->logo_url : asset('images/km100-logo.jpg');
@endphp

<div class="flex items-center gap-3 py-1" style="display: flex; align-items: center; gap: 0.75rem;">
    <div
        class="flex shrink-0 items-center justify-center overflow-hidden rounded-lg bg-white p-1 border border-zinc-200/90 shadow-xs dark:border-zinc-700 dark:bg-zinc-800"
        style="height: 44px; min-width: 44px; max-width: 110px; padding: 3px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; overflow: hidden;"
    >
        <img
            src="{{ $logoUrl }}"
            alt="{{ $title }} Logo"
            class="object-contain"
            style="height: 38px; width: auto; max-width: 100%; max-height: 38px; object-fit: contain; display: block;"
            loading="eager"
        />
    </div>
    <div class="flex flex-col justify-center leading-tight" style="display: flex; flex-direction: column; justify-content: center;">
        <span class="text-sm font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
            {{ $title }}
        </span>
        <span class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400">
            {{ $subtitle }}
        </span>
    </div>
</div>
