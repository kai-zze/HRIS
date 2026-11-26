<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Work Schedules / Shifts per entity
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->string('name', 100); // e.g. Regular Day Shift, Training Center Shift
            $table->time('time_in')->default('08:00:00');
            $table->time('time_out')->default('17:00:00');
            $table->integer('break_minutes')->default(60);
            $table->integer('grace_period_minutes')->default(15);
            $table->json('working_days'); // ['mon', 'tue', 'wed', 'thu', 'fri']
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Link employee to work schedule
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('work_schedule_id')->nullable()->after('position_id')->constrained('work_schedules')->nullOnDelete();
        });

        // 2. Philippine Holidays Table
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->nullable()->constrained('entities')->nullOnDelete(); // null = nationwide/all entities
            $table->string('name', 150);
            $table->date('holiday_date');
            $table->string('holiday_type', 40)->default('regular'); // regular, special_non_working, special_working
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['entity_id', 'holiday_date', 'name']);
        });

        // 3. Leave Types (Vacation, Sick, Maternity, Paternity, Solo Parent, Emergency, etc.)
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->nullable()->constrained('entities')->nullOnDelete(); // null = group standard
            $table->string('code', 30);
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->integer('default_credit_days')->default(15);
            $table->boolean('is_paid')->default(true);
            $table->boolean('is_convertible_to_cash')->default(false);
            $table->boolean('requires_medical_certificate')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Employee Leave Credits (Balances per year)
        Schema::create('leave_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
            $table->integer('year');
            $table->decimal('allocated_days', 6, 2)->default(0);
            $table->decimal('used_days', 6, 2)->default(0);
            $table->decimal('pending_days', 6, 2)->default(0);
            $table->decimal('remaining_days', 6, 2)->default(0);
            $table->timestamps();

            $table->unique(['employee_id', 'leave_type_id', 'year']);
        });

        // 5. Leave Requests & Approval Workflow (Employee -> Manager -> HR)
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('number_of_days', 5, 2);
            $table->boolean('is_half_day')->default(false);
            $table->text('reason');
            $table->string('status', 40)->default('pending'); // pending, approved_by_manager, approved, rejected, cancelled
            
            // Workflow approvers
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('manager_action_at')->nullable();
            $table->text('manager_notes')->nullable();

            $table->foreignId('hr_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hr_action_at')->nullable();
            $table->text('hr_notes')->nullable();

            $table->string('attachment_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'status']);
        });

        // 6. Overtime Requests & Approval Workflow
        Schema::create('overtime_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('overtime_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('hours_requested', 5, 2);
            $table->text('purpose');
            $table->string('status', 40)->default('pending'); // pending, approved_by_manager, approved, rejected, cancelled

            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('manager_action_at')->nullable();
            $table->text('manager_notes')->nullable();

            $table->foreignId('hr_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hr_action_at')->nullable();
            $table->text('hr_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // 7. Daily Time Record (DTR)
        Schema::create('daily_time_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->date('record_date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->integer('late_minutes')->default(0);
            $table->integer('undertime_minutes')->default(0);
            $table->decimal('regular_hours', 5, 2)->default(0);
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->decimal('night_diff_hours', 5, 2)->default(0);
            $table->string('status', 40)->default('present'); // AttendanceStatus
            $table->boolean('is_holiday')->default(false);
            $table->foreignId('holiday_id')->nullable()->constrained('holidays')->nullOnDelete();
            $table->foreignId('leave_request_id')->nullable()->constrained('leave_requests')->nullOnDelete();
            $table->foreignId('overtime_request_id')->nullable()->constrained('overtime_requests')->nullOnDelete();
            $table->string('source', 30)->default('manual'); // biometric, manual, web
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'record_date']);
            $table->index(['entity_id', 'record_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_time_records');
        Schema::dropIfExists('overtime_requests');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('leave_credits');
        Schema::dropIfExists('leave_types');
        Schema::dropIfExists('holidays');

        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['work_schedule_id']);
            $table->dropColumn('work_schedule_id');
        });

        Schema::dropIfExists('work_schedules');
    }
};
