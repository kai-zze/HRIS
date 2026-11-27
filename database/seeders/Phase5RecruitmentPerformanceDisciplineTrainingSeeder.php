<?php

namespace Database\Seeders;

use App\Enums\ApplicantStage;
use App\Enums\EmploymentStatus;
use App\Models\Department;
use App\Models\DisciplinaryRecord;
use App\Models\Employee;
use App\Models\EmployeeCommendation;
use App\Models\Entity;
use App\Models\EvaluationPeriod;
use App\Models\JobApplicant;
use App\Models\JobPosting;
use App\Models\OnboardingChecklist;
use App\Models\PerformanceEvaluation;
use App\Models\Position;
use App\Models\TrainingParticipant;
use App\Models\TrainingProgram;
use App\Models\User;
use App\Services\PerformanceRankingService;
use Illuminate\Database\Seeder;

class Phase5RecruitmentPerformanceDisciplineTrainingSeeder extends Seeder
{
    public function run(): void
    {
        $km100 = Entity::where('code', 'STC')->first();
        $knoah = Entity::where('code', 'KNOAH')->first();
        $redGoose = Entity::where('code', 'REDGOOSE')->first();
        $superAdmin = User::where('email', 'superadmin@km100group.ph')->first();

        // 1. Job Postings
        $jobPosting1 = JobPosting::firstOrCreate(
            ['title' => 'Senior Technical Trainer (Automotive Servicing NC II)'],
            [
                'entity_id' => $km100?->id,
                'department_id' => Department::withoutEntityScope()->where('entity_id', $km100?->id)->where('code', 'TRG')->value('id'),
                'position_id' => Position::withoutEntityScope()->where('entity_id', $km100?->id)->where('code', 'TRG-LEAD')->value('id'),
                'employment_type' => 'Full-Time',
                'openings_count' => 2,
                'description' => '<p>Conduct specialized competency assessment and technical modular training for registered TESDA scholars.</p>',
                'requirements' => '<p>Must hold valid TESDA NTTC-I and National TVET Trainer Certificate.</p>',
                'salary_range' => '₱35,000 — ₱45,000 / month',
                'status' => 'Open',
                'posted_at' => now()->subDays(20),
                'closing_date' => now()->addDays(20),
            ]
        );

        $jobPosting2 = JobPosting::firstOrCreate(
            ['title' => 'Client Operations Supervisor'],
            [
                'entity_id' => $knoah?->id,
                'department_id' => Department::withoutEntityScope()->where('entity_id', $knoah?->id)->where('code', 'OPS')->value('id'),
                'position_id' => Position::withoutEntityScope()->where('entity_id', $knoah?->id)->where('code', 'OPS-SUP')->value('id'),
                'employment_type' => 'Full-Time',
                'openings_count' => 1,
                'description' => '<p>Oversee daily SLA metrics, queue monitoring, and client coordination.</p>',
                'salary_range' => '₱40,000 — ₱50,000 / month',
                'status' => 'Open',
                'posted_at' => now()->subDays(10),
                'closing_date' => now()->addDays(30),
            ]
        );

        // 2. Job Applicants
        $applicant1 = JobApplicant::firstOrCreate(
            ['email' => 'arturo.mercado@gmail.com'],
            [
                'job_posting_id' => $jobPosting1->id,
                'entity_id' => $km100->id,
                'first_name' => 'Arturo',
                'middle_name' => 'Dela Rosa',
                'last_name' => 'Mercado',
                'contact_number' => '+63 917 444 8899',
                'stage' => ApplicantStage::Interview,
                'rating' => 4,
                'interview_notes' => 'Strong background in technical automotive instruction with 6 years experience. Clear communicator.',
                'expected_salary' => 38000.00,
            ]
        );

        $applicant2 = JobApplicant::firstOrCreate(
            ['email' => 'bianca.reyes@yahoo.com'],
            [
                'job_posting_id' => $jobPosting2->id,
                'entity_id' => $knoah->id,
                'first_name' => 'Bianca',
                'middle_name' => 'Flores',
                'last_name' => 'Reyes',
                'contact_number' => '+63 918 333 7711',
                'stage' => ApplicantStage::Hired,
                'rating' => 5,
                'interview_notes' => 'Exceptional team leadership and client engagement experience from top BPO firm.',
                'expected_salary' => 45000.00,
                'hired_at' => now()->subDays(5),
            ]
        );

        // 3. Evaluation Period
        $period = EvaluationPeriod::firstOrCreate(
            ['name' => 'Q1 2026 Group Performance Evaluation'],
            [
                'entity_id' => null, // Group-wide
                'period_type' => 'quarterly',
                'start_date' => '2026-01-01',
                'end_date' => '2026-03-31',
                'status' => 'Active',
                'weight_evaluation' => 50,
                'weight_attendance' => 20,
                'weight_training' => 15,
                'weight_discipline' => 10,
                'weight_commendations' => 5,
            ]
        );

        // 4. Training Programs & Participants
        $training1 = TrainingProgram::firstOrCreate(
            ['title' => 'TESDA Competency Assessor Methodology Training (NTTC)'],
            [
                'entity_id' => $km100?->id,
                'provider' => 'TESDA Registered / Accredited',
                'category' => 'Technical',
                'duration_hours' => 40.0,
                'trainer_name' => 'Engr. Dominador Santos',
                'trainer_credentials' => 'TESDA Master Assessor No. MA-09412',
                'location' => 'KM100 Training Room A',
                'start_date' => '2026-01-10',
                'end_date' => '2026-01-15',
                'description' => 'Comprehensive assessor moderation and evidence-gathering techniques.',
            ]
        );

        $training2 = TrainingProgram::firstOrCreate(
            ['title' => 'DOLE BOSH (Basic Occupational Safety & Health) Certification'],
            [
                'entity_id' => null, // Group-wide
                'provider' => 'DOLE / OSHC Accredited',
                'category' => 'Safety',
                'duration_hours' => 40.0,
                'trainer_name' => 'Arch. Rommel Custodio, SO3',
                'trainer_credentials' => 'DOLE Accredited Safety Consultant 1033-049',
                'location' => 'Corporate Main Auditorium',
                'start_date' => '2026-02-01',
                'end_date' => '2026-02-05',
                'description' => 'Mandatory DOLE Safety Officer 2 compliance training.',
            ]
        );

        $employees = Employee::withoutGlobalScopes()->limit(10)->get();

        foreach ($employees as $index => $emp) {
            // Training participation
            TrainingParticipant::firstOrCreate(
                ['training_program_id' => ($index % 2 === 0 ? $training1->id : $training2->id), 'employee_id' => $emp->id],
                [
                    'status' => 'Completed',
                    'certificate_number' => sprintf('CERT-2026-%04d', rand(1000, 9999)),
                    'certificate_issued_at' => now()->subDays(15),
                    'certificate_expiry_date' => now()->addYears(3),
                    'score_rating' => rand(88, 98),
                ]
            );

            // Supervisor Evaluations
            PerformanceEvaluation::firstOrCreate(
                ['evaluation_period_id' => $period->id, 'employee_id' => $emp->id, 'evaluator_id' => $superAdmin?->id ?? 1],
                [
                    'evaluator_role' => 'supervisor',
                    'evaluation_type' => 'regular_kpi',
                    'score' => rand(82, 96),
                    'strengths' => 'Consistently meets project milestones and demonstrates strong team commitment.',
                    'improvements' => 'Continue enhancing digital collaboration tools and attending safety seminars.',
                    'supervisor_comments' => 'Recommended for merit review.',
                    'status' => 'Approved',
                ]
            );
        }

        // 5. Sample Commendations
        if ($emp1 = $employees->first()) {
            EmployeeCommendation::firstOrCreate(
                ['title' => 'Outstanding TESDA Institutional Compliance Citation'],
                [
                    'employee_id' => $emp1->id,
                    'entity_id' => $emp1->entity_id,
                    'description' => 'Commended for achieving 100% compliance rating during unannounced TESDA regional audit.',
                    'awarded_by' => 'Board of Trustees',
                    'awarded_at' => now()->subDays(12),
                    'score_points' => 20,
                ]
            );
        }

        // 6. Sample Disciplinary Record
        if ($emp2 = $employees->skip(1)->first()) {
            DisciplinaryRecord::firstOrCreate(
                ['case_number' => 'DISC-2026-0041'],
                [
                    'employee_id' => $emp2->id,
                    'entity_id' => $emp2->entity_id,
                    'incident_date' => now()->subDays(25),
                    'violation_category' => 'Tardiness & Habitual Absenteeism',
                    'description' => 'Incurred 4 consecutive unexcused late arrivals in violation of Section 3.2 of the Employee Handbook.',
                    'status' => 'Sanction Imposed',
                    'sanction' => 'Written Warning',
                    'penalty_points' => 10,
                    'memo_date' => now()->subDays(20),
                    'served_at' => now()->subDays(19),
                    'employee_explanation' => 'Unforeseen public transportation disruption due to monsoon flooding.',
                    'hr_findings' => 'First formal offense. Valid explanation noted; formal reminder on backup route planning issued.',
                ]
            );
        }

        // 7. Calculate and populate top performers leaderboard
        $rankingService = app(PerformanceRankingService::class);
        $rankingService->computePeriodRankings($period);
    }
}
