<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('relationship', 60);
            $table->string('contact_number', 50);
            $table->string('alternate_contact', 50)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('dependents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('relationship', 60); // Child, Spouse, Parent, etc.
            $table->date('birthdate')->nullable();
            $table->boolean('is_tax_dependent')->default(true);
            $table->timestamps();
        });

        Schema::create('education_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('level', 60); // Elementary, High School, Vocational, College, Post-Graduate
            $table->string('school_name', 200);
            $table->string('degree_or_course', 200)->nullable();
            $table->string('year_graduated', 10)->nullable();
            $table->timestamps();
        });

        Schema::create('work_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('company_name', 200);
            $table->string('position_title', 150);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('reason_for_leaving')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('movement_type', 50); // promotion, transfer, salary_adjustment, demotion, regularization
            $table->foreignId('from_entity_id')->nullable()->constrained('entities')->nullOnDelete();
            $table->foreignId('to_entity_id')->nullable()->constrained('entities')->nullOnDelete();
            $table->foreignId('from_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('to_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('from_position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->foreignId('to_position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->decimal('previous_salary', 12, 2)->nullable();
            $table->decimal('new_salary', 12, 2)->nullable();
            $table->date('effective_date');
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('document_type', 60); // contract, nbi_clearance, medical_certificate, etc.
            $table->string('title', 150);
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'expiry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
        Schema::dropIfExists('employee_movements');
        Schema::dropIfExists('work_histories');
        Schema::dropIfExists('education_records');
        Schema::dropIfExists('dependents');
        Schema::dropIfExists('emergency_contacts');
    }
};
