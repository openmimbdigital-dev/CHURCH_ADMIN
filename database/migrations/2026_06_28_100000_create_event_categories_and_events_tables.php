<?php

use App\Enums\EventCategoryType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_categories', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', array_column(EventCategoryType::cases(), 'value'));
            $table->boolean('active')->default(true);
            $table->boolean('general')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['active', 'deleted_at']);
            $table->index(['general', 'active', 'deleted_at']);
            $table->index('name');
            $table->index('type');
            $table->index('active');
        });

        Schema::create('church_event_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('church_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['event_category_id', 'church_id']);
            $table->index('business_id');
            $table->index('church_id');
            $table->index('event_category_id');
            $table->index(['business_id', 'church_id']);
            $table->index(['business_id', 'event_category_id']);
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('church_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('business_id');
            $table->index('church_id');
            $table->index('event_category_id');
            $table->index(['church_id', 'date', 'deleted_at']);
            $table->index(['church_id', 'active', 'deleted_at']);
            $table->index(['business_id', 'church_id', 'deleted_at']);
            $table->index('date');
            $table->index('name');
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('church_event_category');
        Schema::dropIfExists('event_categories');
    }
};
