<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RoleName: string implements HasColor, HasLabel
{
    case SuperAdmin = 'Super Admin';
    case HrAdmin = 'HR Admin';
    case Manager = 'Manager';
    case Employee = 'Employee';

    public function getLabel(): string
    {
        return $this->value;
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SuperAdmin => 'primary',
            self::HrAdmin => 'info',
            self::Manager => 'warning',
            self::Employee => 'gray',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Full access across all KM100 group entities, settings, and audit logs.',
            self::HrAdmin => 'Manages employees, attendance, leave, payroll, recruitment, performance, and training for their assigned entity.',
            self::Manager => 'Views team records, approves leave and overtime, and submits performance evaluations within their entity.',
            self::Employee => 'Self-service access to own profile, DTR, leave requests, and payslips.',
        };
    }
}
