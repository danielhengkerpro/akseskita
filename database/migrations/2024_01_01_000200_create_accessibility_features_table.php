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
        Schema::create('accessibility_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
            $table->boolean('has_ramp')->default(false);
            $table->boolean('has_lift')->default(false);
            $table->boolean('has_accessible_toilet')->default(false);
            $table->boolean('has_guiding_block')->default(false);
            $table->boolean('wide_door')->default(false);
            $table->boolean('braille_lift')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessibility_features');
    }
};
