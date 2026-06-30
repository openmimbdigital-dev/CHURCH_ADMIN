<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('nit')->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('address')->nullable();
            $table->string('logo')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('city_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('business_id')
                ->nullable()
                ->after('status')
                ->constrained()
                ->nullOnDelete();

            $table->index('business_id');
            $table->index(['business_id', 'deleted_at']);
            $table->index(['business_id', 'status', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->dropColumn('business_id');
        });

        Schema::dropIfExists('businesses');
    }
};
