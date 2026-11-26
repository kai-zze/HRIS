<?php

namespace Tests\Feature;

use App\Enums\ApplicantStage;
use App\Enums\EmploymentStatus;
use App\Models\DisciplinaryRecord;
use App\Models\Employee;
use App\Models\EmployeeCommendation;
use App\Models\EmployeePerformanceRanking;
use App\Models\Entity;
use App\Models\EvaluationPeriod;
use App\Models\JobApplicant;
use App\Models\JobPosting;
use App\Models\OnboardingChecklist;
use App\Models\PerformanceEvaluation;
use App\Models\TrainingParticipant;
use App\Models\TrainingProgram;
use App\Models\User;
use App\Services\PerformanceRankingService;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase5RecruitmentPerformanceDisciplineTrainingTest extends TestCase
{
    use RefreshDatabase;

    protected Entity $entity;
    protected Employee $employee;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->user = User::query()->where('email', 'emp1.km100@km100group.ph')->first()
            ?? User::query()->whereHas('employee')->firstOrFail();
        $this->employee = $this->user->employee;
        $this->entity = $this->employee->entity;
    }

    public function test_job_postings_and_applicants_are_seeded(): void
    {
        $this->assertGreaterThan(0, JobPosting::count());
        $this->assertGreaterThan(0, JobApplicant::count());

        $applicant = JobApplicant::firstOrFail();
        $this->assertNotNull($applicant->stage);
        $this->assertInstanceOf(ApplicantStage::class, $applicant->stage);
    }

    public function test_applicant_can_be_converted_to_employee_with_onboarding_checklist(): void
    {
        $posting = JobPosting::firstOrFail();

        $applicant = JobApplicant::create([
            'job_posting_id' => $posting->id,
            'entity_id' => $posting->entity_id,
            'first_name' => 'Carlos',
            'last_name' => 'Padilla',
            'email' => 'carlos.padilla@example.com',
            'contact_number' => '+63 917 111 2233',
            'stage' => ApplicantStage::Interview,
            'expected_salary' => 32000.00,
        ]);

        $this->assertNull($applicant->hired_employee_id);

        // Convert applicant to employee
        $entity = $applicant->entity;
        $empNumber = sprintf('%s-2026-%04d', $entity->code, 9876);

        $employee = Employee::withoutGlobalScopes()->create([
            'entity_id' => $applicant->entity_id,
            'department_id' => $posting->department_id,
            'position_id' => $posting->position_id,
            'employee_number' => $empNumber,
            'first_name' => $applicant->first_name,
            'last_name' => $applicant->last_name,
            'email' => $applicant->email,
            'contact_number' => $applicant->contact_number,
            'birthdate' => '1995-01-01',
            'sex' => 'male',
            'civil_status' => 'single',
            'residential_address' => 'Sample',
            'permanent_address' => 'Sample',
            'employment_status' => EmploymentStatus::Probationary,
            'basic_salary' => 32000.00,
            'date_hired' => today(),
        ]);

        $applicant->update([
            'stage' => ApplicantStage::Hired,
            'hired_employee_id' => $employee->id,
            'hired_at' => now(),
        ]);

        OnboardingChecklist::create([
            'employee_id' => $employee->id,
            'item_name' => 'Submit NBI & Police Clearances',
            'category' => 'Document Submission',
            'is_completed' => false,
        ]);

        $this->assertEquals(ApplicantStage::Hired, $applicant->fresh()->stage);
        $this->assertEquals($employee->id, $applicant->fresh()->hired_employee_id);
        $this->assertDatabaseHas('onboarding_checklists', [
            'employee_id' => $employee->id,
            'item_name' => 'Submit NBI & Police Clearances',
        ]);
    }

    public function test_performance_ranking_service_computes_5_factor_scores(): void
    {
        $period = EvaluationPeriod::firstOrFail();
        $service = app(PerformanceRankingService::class);

        $rankings = $service->computePeriodRankings($period);

        $this->assertNotEmpty($rankings);

        $firstRanking = $rankings->first();
        $this->assertGreaterThan(0, $firstRanking->final_score);
        $this->assertNotNull($firstRanking->rank_overall);
        $this->assertGreaterThanOrEqual(1, $firstRanking->rank_overall);

        // Assert 5 component scores are populated
        $this->assertGreaterThan(0, $firstRanking->evaluation_score);
        $this->assertGreaterThan(0, $firstRanking->attendance_score);
        $this->assertGreaterThan(0, $firstRanking->training_score);
        $this->assertGreaterThan(0, $firstRanking->discipline_score);
        $this->assertGreaterThan(0, $firstRanking->commendation_score);
    }

    public function test_hr_manual_adjustment_updates_final_score_and_recalculates_ranks(): void
    {
        $period = EvaluationPeriod::firstOrFail();
        $service = app(PerformanceRankingService::class);
        $rankings = $service->computePeriodRankings($period);

        $target = $rankings->last();
        $initialScore = (float) $target->final_score;

        // Apply +10 HR adjustment
        $service->applyManualAdjustment($target, 10.0, 'Exceptional innovation contribution during audit');

        $target->refresh();
        $this->assertEquals(10.0, (float) $target->manual_adjustment_score);
        $this->assertEquals('Exceptional innovation contribution during audit', $target->manual_adjustment_reason);
        $this->assertGreaterThan($initialScore, (float) $target->final_score);
    }

    public function test_evaluation_period_finalization_locks_period_and_rankings(): void
    {
        $period = EvaluationPeriod::firstOrFail();
        $service = app(PerformanceRankingService::class);

        $service->finalizePeriod($period, $this->user->id);

        $period->refresh();
        $this->assertEquals('Finalized', $period->status);
        $this->assertNotNull($period->finalized_at);
        $this->assertEquals($this->user->id, $period->finalized_by);

        $finalizedRankings = EmployeePerformanceRanking::withoutGlobalScopes()
            ->where('evaluation_period_id', $period->id)
            ->where('is_finalized', true)
            ->count();

        $this->assertGreaterThan(0, $finalizedRankings);
    }

    public function test_disciplinary_records_are_linked_to_employee_and_penalize_ranking(): void
    {
        $this->assertGreaterThan(0, DisciplinaryRecord::count());

        $disc = DisciplinaryRecord::firstOrFail();
        $this->assertNotNull($disc->employee);
        $this->assertGreaterThan(0, $disc->penalty_points);
    }

    public function test_training_programs_track_participants_and_certificate_expiry(): void
    {
        $this->assertGreaterThan(0, TrainingProgram::count());
        $this->assertGreaterThan(0, TrainingParticipant::count());

        $participant = TrainingParticipant::whereNotNull('certificate_expiry_date')->firstOrFail();
        $this->assertNotNull($participant->certificate_number);
        $this->assertTrue($participant->certificate_expiry_date->isFuture());
    }

    public function test_employee_can_access_own_performance_and_ranking_page(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('portal'));

        $this->actingAs($this->user)
            ->get('/portal/my-performance')
            ->assertSuccessful();
    }
}
