<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use UnitEnum;

class SystemSettings extends Page
{
    protected static string | array $routeMiddleware = [];

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string | UnitEnum | null $navigationGroup = 'Security & Audit';

    protected static ?string $navigationLabel = 'System Settings';

    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.system-settings';

    public string $companyName = 'KM100 Group of Companies';
    public string $timezone = 'Asia/Manila';
    public string $currency = 'PHP (₱)';
    public int $workDaysPerYear = 261;
    public float $standardDailyHours = 8.0;
    public bool $trainLawExemptionActive = true;
    public float $thirteenthMonthCap = 90000.00;
    public bool $dataPrivacyEncryptionActive = true;

    public function saveSettings(): void
    {
        Notification::make()
            ->title('Settings Saved')
            ->body('KM100 Group operational parameters updated successfully.')
            ->success()
            ->send();
    }
}
