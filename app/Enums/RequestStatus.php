<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RequestStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case ApprovedByManager = 'approved_by_manager';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Pending Review',
            self::ApprovedByManager => 'Manager Approved (Pending HR)',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::ApprovedByManager => 'info',
            self::Approved => 'success',
            self::Rejected, self::Cancelled => 'danger',
        };
    }
}
