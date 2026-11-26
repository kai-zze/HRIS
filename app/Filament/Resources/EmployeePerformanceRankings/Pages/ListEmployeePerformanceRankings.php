<?php

namespace App\Filament\Resources\EmployeePerformanceRankings\Pages;

use App\Filament\Resources\EmployeePerformanceRankings\EmployeePerformanceRankingResource;
use App\Models\EmployeePerformanceRanking;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListEmployeePerformanceRankings extends ListRecords
{
    protected static string $resource = EmployeePerformanceRankingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label('Export Rankings (CSV)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $rankings = EmployeePerformanceRanking::withoutGlobalScopes()
                        ->with(['employee', 'entity', 'department', 'position', 'evaluationPeriod'])
                        ->orderByDesc('final_score')
                        ->get();

                    $filename = 'KM100-Performance-Rankings-' . now()->format('Y-m-d') . '.csv';
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ];

                    $callback = function () use ($rankings) {
                        $handle = fopen('php://output', 'w');
                        fputcsv($handle, [
                            'Overall Rank',
                            'Employee Number',
                            'Employee Name',
                            'Entity',
                            'Department',
                            'Position',
                            'Evaluation (50%)',
                            'Attendance (20%)',
                            'Training (15%)',
                            'Discipline (10%)',
                            'Commendations (5%)',
                            'Calculated Score',
                            'HR Adjustment',
                            'Adjustment Reason',
                            'Final Score',
                            'Department Rank',
                            'Position Rank',
                        ]);

                        foreach ($rankings as $row) {
                            fputcsv($handle, [
                                $row->rank_overall,
                                $row->employee?->employee_number,
                                $row->employee?->full_name,
                                $row->entity?->name,
                                $row->department?->name,
                                $row->position?->title,
                                $row->evaluation_score,
                                $row->attendance_score,
                                $row->training_score,
                                $row->discipline_score,
                                $row->commendation_score,
                                $row->calculated_total_score,
                                $row->manual_adjustment_score,
                                $row->manual_adjustment_reason,
                                $row->final_score,
                                $row->rank_department,
                                $row->rank_position,
                            ]);
                        }
                        fclose($handle);
                    };

                    Notification::make()->title('Export Ready')->success()->send();

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
