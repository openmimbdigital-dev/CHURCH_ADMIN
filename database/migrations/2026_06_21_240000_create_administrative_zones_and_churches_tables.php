<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administrative_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('address')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('business_id');
            $table->index('city_id');
            $table->index('name');
        });

        Schema::create('churches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('administrative_zone_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('churches')->nullOnDelete();
            $table->string('name');
            $table->string('address')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('business_id');
            $table->index('administrative_zone_id');
            $table->index('parent_id');
            $table->index('city_id');
            $table->index('category');
        });

        Schema::create('leadables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('leadable');
            $table->timestamps();

            $table->unique(['user_id', 'leadable_id', 'leadable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leadables');
        Schema::dropIfExists('churches');
        Schema::dropIfExists('administrative_zones');
    }
};
