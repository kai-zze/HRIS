<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('name', 150);
            $table->string('short_name', 60);
            $table->text('tin')->nullable();
            $table->text('sss_employer_number')->nullable();
            $table->text('philhealth_employer_number')->nullable();
            $table->text('pagibig_employer_number')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_number', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
