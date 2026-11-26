<?php

namespace App\Filament\Employee\Widgets;

use App\Models\Announcement;
use App\Models\Employee;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;

class EmployeeAnnouncementsWidget extends Widget
{
    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    protected string $view = 'filament.employee.widgets.employee-announcements-widget';

    public function getAnnouncementsProperty(): Collection
    {
        $user = auth()->user();
        $employee = $user?->employee ?? Employee::withoutGlobalScopes()->where('email', $user?->email)->first();
        $entityId = $employee?->entity_id ?? $user?->entity_id;

        return Announcement::query()
            ->withoutGlobalScopes()
            ->where(function ($q) use ($entityId) {
                $q->whereNull('entity_id');
                if ($entityId) {
                    $q->orWhere('entity_id', $entityId);
                }
            })
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();
    }
}
