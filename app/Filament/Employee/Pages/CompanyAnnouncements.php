<?php

namespace App\Filament\Employee\Pages;

use App\Models\Announcement;
use App\Models\Employee;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

class CompanyAnnouncements extends Page
{
    protected static string | array $routeMiddleware = [];

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-megaphone';

    protected static string | UnitEnum | null $navigationGroup = 'Company Info';

    protected static ?string $navigationLabel = 'Company Announcements';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.employee.pages.company-announcements';

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
            ->get();
    }
}
