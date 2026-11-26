<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('entity_id')
                ->nullable()
                ->after('id')
                ->constrained('entities')
                ->nullOnDelete();
            $table->boolean('is_active')
                ->default(true)
                ->after('password')
                ->index();
            $table->timestamp('last_login_at')
                ->nullable()
                ->after('remember_token');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropForeign(['entity_id']);
            $table->dropColumn(['entity_id', 'is_active', 'last_login_at']);
        });
    }
};
