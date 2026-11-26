<?php

namespace App\Filament\Employee\Widgets;

use App\Models\Employee;
use Filament\Widgets\Widget;

class EmployeeWelcomeWidget extends Widget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.employee.widgets.employee-welcome-widget';

    public function getEmployeeProperty(): ?Employee
    {
        $user = auth()->user();

        return $user?->employee ?? Employee::withoutGlobalScopes()->where('email', $user?->email)->first();
    }
}
