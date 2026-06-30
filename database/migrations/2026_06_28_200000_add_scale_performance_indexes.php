<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexIfMissing('users', ['business_id', 'deleted_at'], 'users_business_deleted_idx');
        $this->addIndexIfMissing('users', ['business_id', 'current_church_id', 'deleted_at'], 'users_business_church_deleted_idx');
        $this->addIndexIfMissing('users', ['business_id', 'status', 'deleted_at'], 'users_business_status_deleted_idx');
        $this->addIndexIfMissing('users', 'email', 'users_email_idx');
        $this->addIndexIfMissing('users', 'status', 'users_status_idx');

        $this->addIndexIfMissing('churches', ['business_id', 'is_active', 'deleted_at'], 'churches_business_active_deleted_idx');
        $this->addIndexIfMissing('churches', ['administrative_zone_id', 'deleted_at'], 'churches_zone_deleted_idx');
        $this->addIndexIfMissing('churches', ['business_id', 'administrative_zone_id', 'category'], 'churches_business_zone_category_idx');
        $this->addIndexIfMissing('churches', ['business_id', 'deleted_at'], 'churches_business_deleted_idx');

        $this->addIndexIfMissing('administrative_zones', ['business_id', 'is_active', 'deleted_at'], 'zones_business_active_deleted_idx');

        $this->addIndexIfMissing('church_user', ['church_id', 'user_id'], 'church_user_church_user_idx');

        $this->addIndexIfMissing('administrative_zone_user', ['administrative_zone_id', 'user_id'], 'zone_user_zone_user_idx');

        $this->addIndexIfMissing('event_categories', ['active', 'deleted_at'], 'event_categories_active_deleted_idx');
        $this->addIndexIfMissing('event_categories', ['general', 'active', 'deleted_at'], 'event_categories_general_active_deleted_idx');

        $this->addIndexIfMissing('church_event_category', ['business_id', 'church_id', 'event_category_id'], 'cec_business_church_category_idx');

        $this->addIndexIfMissing('events', ['church_id', 'date', 'deleted_at'], 'events_church_date_deleted_idx');
        $this->addIndexIfMissing('events', ['church_id', 'active', 'deleted_at'], 'events_church_active_deleted_idx');
        $this->addIndexIfMissing('events', ['business_id', 'church_id', 'deleted_at'], 'events_business_church_deleted_idx');

        $this->addIndexIfMissing('model_has_roles', ['role_id', 'model_type'], 'model_has_roles_role_type_idx');
    }

    public function down(): void
    {
        $this->dropIndexIfExists('users', 'users_business_deleted_idx');
        $this->dropIndexIfExists('users', 'users_business_church_deleted_idx');
        $this->dropIndexIfExists('users', 'users_business_status_deleted_idx');
        $this->dropIndexIfExists('users', 'users_email_idx');
        $this->dropIndexIfExists('users', 'users_status_idx');

        $this->dropIndexIfExists('churches', 'churches_business_active_deleted_idx');
        $this->dropIndexIfExists('churches', 'churches_zone_deleted_idx');
        $this->dropIndexIfExists('churches', 'churches_business_zone_category_idx');
        $this->dropIndexIfExists('churches', 'churches_business_deleted_idx');

        $this->dropIndexIfExists('administrative_zones', 'zones_business_active_deleted_idx');

        $this->dropIndexIfExists('church_user', 'church_user_church_user_idx');

        $this->dropIndexIfExists('administrative_zone_user', 'zone_user_zone_user_idx');

        $this->dropIndexIfExists('event_categories', 'event_categories_active_deleted_idx');
        $this->dropIndexIfExists('event_categories', 'event_categories_general_active_deleted_idx');

        $this->dropIndexIfExists('church_event_category', 'cec_business_church_category_idx');

        $this->dropIndexIfExists('events', 'events_church_date_deleted_idx');
        $this->dropIndexIfExists('events', 'events_church_active_deleted_idx');
        $this->dropIndexIfExists('events', 'events_business_church_deleted_idx');

        $this->dropIndexIfExists('model_has_roles', 'model_has_roles_role_type_idx');
    }

    /**
     * @param  array<int, string>|string  $columns
     */
    protected function addIndexIfMissing(string $table, array|string $columns, string $name): void
    {
        if ($this->indexExists($table, $name)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($columns, $name) {
            $table->index($columns, $name);
        });
    }

    protected function dropIndexIfExists(string $table, string $name): void
    {
        if (! $this->indexExists($table, $name)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($name) {
            $table->dropIndex($name);
        });
    }

    protected function indexExists(string $table, string $name): bool
    {
        return collect(Schema::getIndexes($table))
            ->contains(fn (array $index) => ($index['name'] ?? '') === $name);
    }
};
