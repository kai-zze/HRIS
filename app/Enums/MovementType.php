<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MovementType: string implements HasColor, HasLabel
{
    case Promotion = 'promotion';
    case Transfer = 'transfer';
    case SalaryAdjustment = 'salary_adjustment';
    case Demotion = 'demotion';
    case Regularization = 'regularization';

    public function getLabel(): string
    {
        return match ($this) {
            self::Promotion => 'Promotion',
            self::Transfer => 'Transfer Between Entities',
            self::SalaryAdjustment => 'Salary Adjustment',
            self::Demotion => 'Demotion',
            self::Regularization => 'Regularization',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Promotion, self::Regularization => 'success',
            self::Transfer => 'primary',
            self::SalaryAdjustment => 'info',
            self::Demotion => 'danger',
        };
    }
}
