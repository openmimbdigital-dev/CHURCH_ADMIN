<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('current_church_id')
                ->nullable()
                ->after('business_id')
                ->constrained('churches')
                ->nullOnDelete();

            $table->index('current_church_id');
            $table->index(['business_id', 'current_church_id', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['current_church_id']);
            $table->dropColumn('current_church_id');
        });
    }
};
