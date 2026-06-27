<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administrative_zone_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('administrative_zone_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'administrative_zone_id']);
            $table->index('administrative_zone_id');
        });

        Schema::create('church_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('church_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'church_id']);
            $table->index('church_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('church_user');
        Schema::dropIfExists('administrative_zone_user');
    }
};
