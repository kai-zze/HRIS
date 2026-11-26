<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EmploymentStatus: string implements HasColor, HasLabel
{
    case Probationary = 'probationary';
    case Regular = 'regular';
    case Contractual = 'contractual';
    case PartTime = 'part_time';
    case Resigned = 'resigned';
    case Terminated = 'terminated';

    public function getLabel(): string
    {
        return match ($this) {
            self::Probationary => 'Probationary',
            self::Regular => 'Regular',
            self::Contractual => 'Contractual',
            self::PartTime => 'Part-Time',
            self::Resigned => 'Resigned',
            self::Terminated => 'Terminated',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Probationary => 'warning',
            self::Regular => 'success',
            self::Contractual => 'info',
            self::PartTime => 'gray',
            self::Resigned => 'danger',
            self::Terminated => 'danger',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::Probationary, self::Regular, self::Contractual, self::PartTime], true);
    }
}
