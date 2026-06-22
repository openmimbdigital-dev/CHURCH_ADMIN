<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::table('users')->truncate();

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['name', 'email_verified_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('username')->unique()->after('last_name');
            $table->string('email')->nullable()->change();
            $table->string('phone_number')->nullable()->after('email');
            $table->boolean('status')->default(true)->after('password');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['first_name', 'last_name', 'username', 'phone_number', 'status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email')->nullable(false)->change();
        });
    }
};
