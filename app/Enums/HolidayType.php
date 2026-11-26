<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum HolidayType: string implements HasColor, HasLabel
{
    case RegularHoliday = 'regular';
    case SpecialNonWorking = 'special_non_working';
    case SpecialWorking = 'special_working';

    public function getLabel(): string
    {
        return match ($this) {
            self::RegularHoliday => 'Regular Holiday (200% Pay)',
            self::SpecialNonWorking => 'Special Non-Working Holiday (130% Pay)',
            self::SpecialWorking => 'Special Working Holiday (100% Pay)',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::RegularHoliday => 'danger',
            self::SpecialNonWorking => 'warning',
            self::SpecialWorking => 'info',
        };
    }

    public function payMultiplier(): float
    {
        return match ($this) {
            self::RegularHoliday => 2.0,
            self::SpecialNonWorking => 1.3,
            self::SpecialWorking => 1.0,
        };
    }
}
