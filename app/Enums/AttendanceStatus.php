<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AttendanceStatus: string implements HasColor, HasLabel
{
    case Present = 'present';
    case Late = 'late';
    case Undertime = 'undertime';
    case LateAndUndertime = 'late_and_undertime';
    case Absent = 'absent';
    case OnLeave = 'on_leave';
    case RestDay = 'rest_day';
    case Holiday = 'holiday';

    public function getLabel(): string
    {
        return match ($this) {
            self::Present => 'Present / On Time',
            self::Late => 'Late Arrival',
            self::Undertime => 'Undertime',
            self::LateAndUndertime => 'Late & Undertime',
            self::Absent => 'Absent (AWOL / Unfiled)',
            self::OnLeave => 'On Approved Leave',
            self::RestDay => 'Rest Day / Day Off',
            self::Holiday => 'Official Holiday',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Present, self::Holiday => 'success',
            self::Late, self::Undertime, self::LateAndUndertime => 'warning',
            self::Absent => 'danger',
            self::OnLeave => 'info',
            self::RestDay => 'gray',
        };
    }
}
