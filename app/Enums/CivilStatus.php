<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CivilStatus: string implements HasColor, HasLabel
{
    case Single = 'single';
    case Married = 'married';
    case Widowed = 'widowed';
    case Separated = 'separated';

    public function getLabel(): string
    {
        return match ($this) {
            self::Single => 'Single',
            self::Married => 'Married',
            self::Widowed => 'Widowed',
            self::Separated => 'Separated',
        };
    }

    public function getColor(): string|array|null
    {
        return 'gray';
    }
}
