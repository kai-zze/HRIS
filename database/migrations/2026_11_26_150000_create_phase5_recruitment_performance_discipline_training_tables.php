<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Job Postings
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->string('title');
            $table->string('employment_type')->default('Full-Time'); // Full-Time, Contractual, Part-Time
            $table->integer('openings_count')->default(1);
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->string('salary_range')->nullable();
            $table->string('status')->default('Open'); // Draft, Open, Closed, On Hold
            $table->date('posted_at')->nullable();
            $table->date('closing_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['entity_id', 'status']);
        });

        // 2. Job Applicants & Pipeline
        Schema::create('job_applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained('job_postings')->cascadeOnDelete();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email');
            $table->string('contact_number');
            $table->string('resume_path')->nullable();
            $table->string('stage')->default('Applied'); // Applied, Screening, Interview, Offer, Hired, Rejected
            $table->integer('rating')->nullable(); // 1-5 stars
            $table->text('interview_notes')->nullable();
            $table->decimal('expected_salary', 12, 2)->nullable();
            $table->foreignId('hired_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('hired_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['job_posting_id', 'stage']);
            $table->index(['entity_id', 'email']);
        });

        // 3. Onboarding Checklist
        Schema::create('onboarding_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('item_name');
            $table->string('category')->default('General'); // Document Submission, IT & Equipment, Orientation, Statutory
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['employee_id', 'is_completed']);
        });

        // 4. Evaluation Periods
        Schema::create('evaluation_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->nullable()->constrained('entities')->cascadeOnDelete(); // null = group-wide
            $table->string('name'); // e.g. Q1 2026 Evaluation Cycle
            $table->string('period_type')->default('quarterly'); // monthly, quarterly, yearly
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('Active'); // Draft, Active, Closed, Finalized
            
            // Configurable ranking factor weights (percentage integers adding to 100)
            $table->integer('weight_evaluation')->default(50);
            $table->integer('weight_attendance')->default(20);
            $table->integer('weight_training')->default(15);
            $table->integer('weight_discipline')->default(10);
            $table->integer('weight_commendations')->default(5);

            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Performance Evaluations
        Schema::create('performance_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_period_id')->constrained('evaluation_periods')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete();
            $table->string('evaluator_role')->default('supervisor'); // supervisor, peer, hr
            $table->string('evaluation_type')->default('regular_kpi'); // regular_kpi, probation_review
            $table->decimal('score', 5, 2)->default(0); // 0-100
            $table->text('strengths')->nullable();
            $table->text('improvements')->nullable();
            $table->text('supervisor_comments')->nullable();
            $table->text('employee_acknowledgment')->nullable();
            $table->string('status')->default('Submitted'); // Draft, Submitted, Approved
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['evaluation_period_id', 'employee_id', 'evaluator_id'], 'pe_period_emp_eval_unique');
        });

        // 6. Evaluation Criteria (KPI items)
        Schema::create('evaluation_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_evaluation_id')->constrained('performance_evaluations')->cascadeOnDelete();
            $table->string('criterion_name');
            $table->integer('weight_percentage')->default(20);
            $table->decimal('score', 5, 2)->default(0); // 0-100 or 1-5 scale
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // 7. Commendations & Recognitions
        Schema::create('employee_commendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('awarded_by');
            $table->date('awarded_at');
            $table->integer('score_points')->default(10);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'awarded_at']);
        });

        // 8. Disciplinary Records
        Schema::create('disciplinary_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->string('case_number');
            $table->date('incident_date');
            $table->string('violation_category'); // Tardiness/Attendance, Misconduct, Negligence, Policy Breach
            $table->text('description');
            $table->string('status')->default('Incident Reported'); // Incident Reported, NTE Issued, Hearing, Sanction Imposed, Case Closed
            $table->string('sanction')->nullable(); // Verbal Warning, Written Warning, Written Reprimand, Suspension, Termination, Exonerated
            $table->integer('suspension_days')->nullable();
            $table->integer('penalty_points')->default(10); // Deducted from discipline ranking score
            $table->date('memo_date')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->text('employee_explanation')->nullable();
            $table->text('hr_findings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'incident_date']);
            $table->index(['entity_id', 'case_number']);
        });

        // 9. Training Programs
        Schema::create('training_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->nullable()->constrained('entities')->cascadeOnDelete(); // null = group-wide
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('provider')->default('Internal KM100 Academy'); // TESDA, Internal, External
            $table->string('category')->default('Technical'); // Technical, Safety, Compliance, Leadership, Soft Skills
            $table->decimal('duration_hours', 5, 2)->default(8.0);
            $table->string('trainer_name')->nullable();
            $table->string('trainer_credentials')->nullable(); // TESDA NTTC, PRC License, etc.
            $table->date('start_date');
            $table->date('end_date');
            $table->string('location')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['entity_id', 'category']);
        });

        // 10. Training Participants & Certifications
        Schema::create('training_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_program_id')->constrained('training_programs')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('status')->default('Completed'); // Nominated, Attended, Completed, Failed, No Show
            $table->string('certificate_number')->nullable();
            $table->date('certificate_issued_at')->nullable();
            $table->date('certificate_expiry_date')->nullable();
            $table->decimal('score_rating', 5, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['training_program_id', 'employee_id']);
            $table->index(['employee_id', 'certificate_expiry_date']);
        });

        // 11. Top Performers Rankings
        Schema::create('employee_performance_rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_period_id')->constrained('evaluation_periods')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();

            // 5 component scores (normalized 0-100)
            $table->decimal('evaluation_score', 5, 2)->default(0);
            $table->decimal('attendance_score', 5, 2)->default(0);
            $table->decimal('training_score', 5, 2)->default(0);
            $table->decimal('discipline_score', 5, 2)->default(0);
            $table->decimal('commendation_score', 5, 2)->default(0);

            // Total & Adjustments
            $table->decimal('calculated_total_score', 5, 2)->default(0);
            $table->decimal('manual_adjustment_score', 5, 2)->default(0);
            $table->text('manual_adjustment_reason')->nullable();
            $table->decimal('final_score', 5, 2)->default(0);

            // Rankings
            $table->integer('rank_overall')->nullable();
            $table->integer('rank_department')->nullable();
            $table->integer('rank_position')->nullable();

            $table->boolean('is_finalized')->default(false);
            $table->timestamps();

            $table->unique(['evaluation_period_id', 'employee_id'], 'epr_period_emp_unique');
            $table->index(['entity_id', 'evaluation_period_id', 'final_score'], 'epr_ent_per_score_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_performance_rankings');
        Schema::dropIfExists('training_participants');
        Schema::dropIfExists('training_programs');
        Schema::dropIfExists('disciplinary_records');
        Schema::dropIfExists('employee_commendations');
        Schema::dropIfExists('evaluation_criteria');
        Schema::dropIfExists('performance_evaluations');
        Schema::dropIfExists('evaluation_periods');
        Schema::dropIfExists('onboarding_checklists');
        Schema::dropIfExists('job_applicants');
        Schema::dropIfExists('job_postings');
    }
};
