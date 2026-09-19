<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan index untuk mempercepat filter & urutan yang sering
     * dipakai: places (is_verified + overall_score, created_at) dan
     * reports (created_at untuk latest()).
     */
    public function up(): void
    {
        Schema::table('places', function (Blueprint $table) {
            $table->index(['is_verified', 'overall_score'], 'places_verified_score_idx');
            $table->index('created_at', 'places_created_at_idx');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->index('created_at', 'reports_created_at_idx');
            $table->index(['status', 'created_at'], 'reports_status_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('places', function (Blueprint $table) {
            $table->dropIndex('places_verified_score_idx');
            $table->dropIndex('places_created_at_idx');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('reports_created_at_idx');
            $table->dropIndex('reports_status_created_idx');
        });
    }
};
