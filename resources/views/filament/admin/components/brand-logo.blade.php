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

    $title = $activeEntity ? ($activeEntity->short_name ?: $activeEntity->name) : 'Enterprise HRIS';
    $subtitle = $activeEntity ? ($industryMeta[$activeEntity->code] ?? 'Operating Business Unit') : 'Multi-Business Group';
@endphp

<div class="flex flex-col justify-center leading-tight py-0.5">
    <span class="text-sm font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
        {{ $title }}
    </span>
    <span class="text-[11px] font-normal text-zinc-500 dark:text-zinc-400">
        {{ $subtitle }}
    </span>
</div>
