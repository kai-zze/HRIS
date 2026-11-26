<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->string('employee_number', 50)->unique();

            // Personal Info
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('suffix', 20)->nullable();
            $table->date('birthdate');
            $table->string('sex', 20); // male, female
            $table->string('civil_status', 30)->default('single');
            $table->string('email', 150)->nullable();
            $table->string('contact_number', 50)->nullable();
            $table->text('residential_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('photo_path')->nullable();

            // Statutory Numbers (RA 10173 Encrypted)
            $table->text('tin')->nullable();
            $table->text('sss_number')->nullable();
            $table->text('philhealth_number')->nullable();
            $table->text('pagibig_number')->nullable();
            $table->text('bank_account_number')->nullable();
            $table->string('bank_name', 100)->nullable();

            // Employment Details
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('employment_status', 30)->default('probationary'); // probationary, regular, contractual, part_time, resigned, terminated
            $table->date('date_hired');
            $table->date('regularization_date')->nullable();
            $table->string('work_schedule', 100)->default('Monday - Friday (8:00 AM - 5:00 PM)');
            $table->decimal('basic_salary', 12, 2)->default(0);

            // Offboarding / Separation
            $table->date('separation_date')->nullable();
            $table->string('separation_type', 50)->nullable(); // resignation, termination, end_of_contract, retirement
            $table->text('separation_reason')->nullable();
            $table->boolean('clearance_completed')->default(false);
            $table->string('final_pay_status', 30)->default('pending'); // pending, processing, released

            $table->timestamps();
            $table->softDeletes();

            $table->index(['entity_id', 'employment_status']);
            $table->index(['entity_id', 'department_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
