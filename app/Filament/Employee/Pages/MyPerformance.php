<?php

namespace App\Filament\Employee\Pages;

use App\Models\DisciplinaryRecord;
use App\Models\Employee;
use App\Models\EmployeePerformanceRanking;
use App\Models\EvaluationPeriod;
use App\Models\PerformanceEvaluation;
use App\Models\TrainingParticipant;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class MyPerformance extends Page
{
    protected static string | array $routeMiddleware = [];

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string | UnitEnum | null $navigationGroup = 'My Self-Service';

    protected static ?string $navigationLabel = 'My Performance & Ranking';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.employee.pages.my-performance';

    public ?Employee $employee = null;
    public ?EmployeePerformanceRanking $ranking = null;
    public ?PerformanceEvaluation $evaluation = null;

    public function mount(): void
    {
        $user = auth()->user();
        $this->employee = $user?->employee ?? Employee::withoutGlobalScopes()->where('email', $user?->email)->first();

        if ($this->employee) {
            $latestPeriod = EvaluationPeriod::query()->latest('start_date')->first();
            if ($latestPeriod) {
                $this->ranking = EmployeePerformanceRanking::withoutGlobalScopes()
                    ->where('employee_id', $this->employee->id)
                    ->where('evaluation_period_id', $latestPeriod->id)
                    ->first();

                $this->evaluation = PerformanceEvaluation::query()
                    ->where('employee_id', $this->employee->id)
                    ->where('evaluation_period_id', $latestPeriod->id)
                    ->with('criteria')
                    ->latest()
                    ->first();
            }
        }
    }
}
