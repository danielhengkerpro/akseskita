<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('account_type', 30)->default('contributor')->after('role');
            $table->string('disability_type', 50)->nullable()->after('account_type');
            $table->json('accessibility_preferences')->nullable()->after('disability_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['account_type', 'disability_type', 'accessibility_preferences']);
        });
    }
};
