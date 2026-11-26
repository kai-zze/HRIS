<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\DailyTimeRecord;
use App\Models\DisciplinaryRecord;
use App\Models\Employee;
use App\Models\EmployeeCommendation;
use App\Models\EmployeePerformanceRanking;
use App\Models\EvaluationPeriod;
use App\Models\PerformanceEvaluation;
use App\Models\TrainingParticipant;
use Illuminate\Support\Collection;

class PerformanceRankingService
{
    /**
     * Compute 5-factor weighted performance scores and rankings for an evaluation period.
     */
    public function computePeriodRankings(EvaluationPeriod $period): Collection
    {
        $employeeQuery = Employee::withoutGlobalScopes();
        if ($period->entity_id) {
            $employeeQuery->where('entity_id', $period->entity_id);
        }

        $employees = $employeeQuery->get();

        $wEval = $period->weight_evaluation ?: 50;
        $wAtt = $period->weight_attendance ?: 20;
        $wTrain = $period->weight_training ?: 15;
        $wDisc = $period->weight_discipline ?: 10;
        $wComm = $period->weight_commendations ?: 5;
        $totalWeight = $wEval + $wAtt + $wTrain + $wDisc + $wComm;

        foreach ($employees as $employee) {
            // 1. Evaluation Score (Average of submitted evaluations in period)
            $evaluations = PerformanceEvaluation::query()
                ->where('evaluation_period_id', $period->id)
                ->where('employee_id', $employee->id)
                ->whereIn('status', ['Submitted', 'Approved'])
                ->get();

            $evalScore = $evaluations->isNotEmpty()
                ? (float) $evaluations->avg('score')
                : 85.0; // Default baseline if pending evaluation

            // 2. Attendance & Punctuality Score (100 minus tardiness & absence penalties)
            $dtrRecords = DailyTimeRecord::withoutGlobalScopes()
                ->where('employee_id', $employee->id)
                ->whereBetween('record_date', [$period->start_date, $period->end_date])
                ->get();

            $totalLateMins = $dtrRecords->sum('late_minutes');
            $totalUndertimeMins = $dtrRecords->sum('undertime_minutes');
            $absentDays = $dtrRecords->where('status', AttendanceStatus::Absent)->count();

            $attPenalty = ($totalLateMins * 0.1) + ($totalUndertimeMins * 0.1) + ($absentDays * 10);
            $attScore = (float) max(0, min(100, 100 - $attPenalty));

            // 3. Training & Certification Score
            $trainings = TrainingParticipant::query()
                ->where('employee_id', $employee->id)
                ->where('status', 'Completed')
                ->whereHas('program', function ($q) use ($period) {
                    $q->whereBetween('start_date', [$period->start_date, $period->end_date]);
                })
                ->get();

            $trainingScore = $trainings->isNotEmpty()
                ? (float) min(100, 70 + ($trainings->count() * 15))
                : 75.0; // Standard training baseline

            // 4. Discipline Record Score (100 minus disciplinary sanction points)
            $disciplinaryRecords = DisciplinaryRecord::withoutGlobalScopes()
                ->where('employee_id', $employee->id)
                ->whereBetween('incident_date', [$period->start_date, $period->end_date])
                ->whereNotIn('sanction', ['Exonerated'])
                ->get();

            $penaltyPoints = $disciplinaryRecords->sum('penalty_points');
            $discScore = (float) max(0, min(100, 100 - $penaltyPoints));

            // 5. Commendations & Recognitions
            $commendations = EmployeeCommendation::withoutGlobalScopes()
                ->where('employee_id', $employee->id)
                ->whereBetween('awarded_at', [$period->start_date, $period->end_date])
                ->get();

            $commScore = $commendations->isNotEmpty()
                ? (float) min(100, 60 + $commendations->sum('score_points'))
                : 70.0;

            // Weighted Total
            $rawWeighted = (
                ($evalScore * $wEval) +
                ($attScore * $wAtt) +
                ($trainingScore * $wTrain) +
                ($discScore * $wDisc) +
                ($commScore * $wComm)
            ) / ($totalWeight ?: 100);

            $calculatedTotal = round($rawWeighted, 2);

            // Fetch or instantiate existing ranking to preserve HR manual adjustment
            $ranking = EmployeePerformanceRanking::withoutGlobalScopes()->firstOrNew([
                'evaluation_period_id' => $period->id,
                'employee_id' => $employee->id,
            ]);

            $manualAdj = (float) ($ranking->manual_adjustment_score ?? 0);
            $finalScore = max(0, min(100, round($calculatedTotal + $manualAdj, 2)));

            $ranking->fill([
                'entity_id' => $employee->entity_id,
                'department_id' => $employee->department_id,
                'position_id' => $employee->position_id,
                'evaluation_score' => $evalScore,
                'attendance_score' => $attScore,
                'training_score' => $trainingScore,
                'discipline_score' => $discScore,
                'commendation_score' => $commScore,
                'calculated_total_score' => $calculatedTotal,
                'manual_adjustment_score' => $manualAdj,
                'final_score' => $finalScore,
            ]);
            $ranking->save();
        }

        // Recompute rank positions across overall entity, department, and position peer groups
        $this->recalculateRanks($period);

        return EmployeePerformanceRanking::withoutGlobalScopes()
            ->where('evaluation_period_id', $period->id)
            ->orderBy('rank_overall')
            ->get();
    }

    /**
     * Recalculate peer-group ranks (overall entity, department, position).
     */
    public function recalculateRanks(EvaluationPeriod $period): void
    {
        $entities = EmployeePerformanceRanking::withoutGlobalScopes()
            ->where('evaluation_period_id', $period->id)
            ->pluck('entity_id')
            ->unique();

        foreach ($entities as $entityId) {
            // Overall entity rank
            $rankings = EmployeePerformanceRanking::withoutGlobalScopes()
                ->where('evaluation_period_id', $period->id)
                ->where('entity_id', $entityId)
                ->orderByDesc('final_score')
                ->get();

            $rank = 1;
            foreach ($rankings as $item) {
                $item->update(['rank_overall' => $rank++]);
            }

            // Department rank
            $deptIds = $rankings->pluck('department_id')->filter()->unique();
            foreach ($deptIds as $deptId) {
                $deptRankings = EmployeePerformanceRanking::withoutGlobalScopes()
                    ->where('evaluation_period_id', $period->id)
                    ->where('entity_id', $entityId)
                    ->where('department_id', $deptId)
                    ->orderByDesc('final_score')
                    ->get();

                $deptRank = 1;
                foreach ($deptRankings as $deptItem) {
                    $deptItem->update(['rank_department' => $deptRank++]);
                }
            }

            // Position rank
            $posIds = $rankings->pluck('position_id')->filter()->unique();
            foreach ($posIds as $posId) {
                $posRankings = EmployeePerformanceRanking::withoutGlobalScopes()
                    ->where('evaluation_period_id', $period->id)
                    ->where('entity_id', $entityId)
                    ->where('position_id', $posId)
                    ->orderByDesc('final_score')
                    ->get();

                $posRank = 1;
                foreach ($posRankings as $posItem) {
                    $posItem->update(['rank_position' => $posRank++]);
                }
            }
        }
    }

    /**
     * Apply HR manual adjustment with an audit justification.
     */
    public function applyManualAdjustment(EmployeePerformanceRanking $ranking, float $adjustment, string $reason): void
    {
        $calculated = (float) $ranking->calculated_total_score;
        $finalScore = max(0, min(100, round($calculated + $adjustment, 2)));

        $ranking->update([
            'manual_adjustment_score' => $adjustment,
            'manual_adjustment_reason' => $reason,
            'final_score' => $finalScore,
        ]);

        $this->recalculateRanks($ranking->evaluationPeriod);
    }

    /**
     * Finalize the evaluation period and freeze rankings.
     */
    public function finalizePeriod(EvaluationPeriod $period, int $userId): void
    {
        $period->update([
            'status' => 'Finalized',
            'finalized_at' => now(),
            'finalized_by' => $userId,
        ]);

        EmployeePerformanceRanking::withoutGlobalScopes()
            ->where('evaluation_period_id', $period->id)
            ->update(['is_finalized' => true]);
    }
}
