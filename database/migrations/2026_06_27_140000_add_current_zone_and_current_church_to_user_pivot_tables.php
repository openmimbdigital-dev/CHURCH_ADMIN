<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('administrative_zone_user', 'current_zone')) {
            Schema::table('administrative_zone_user', function (Blueprint $table) {
                $table->foreignId('current_zone')->nullable()->after('administrative_zone_id')->constrained('administrative_zones')->nullOnDelete();
                $table->index('current_zone');
            });
        }

        if (! Schema::hasColumn('church_user', 'current_church')) {
            Schema::table('church_user', function (Blueprint $table) {
                $table->foreignId('current_church')->nullable()->after('church_id')->constrained('churches')->nullOnDelete();
                $table->index('current_church');
            });
        }
    }

    public function down(): void
    {
        Schema::table('church_user', function (Blueprint $table) {
            $table->dropForeign(['current_church']);
            $table->dropColumn('current_church');
        });

        Schema::table('administrative_zone_user', function (Blueprint $table) {
            $table->dropForeign(['current_zone']);
            $table->dropColumn('current_zone');
        });
    }
};
