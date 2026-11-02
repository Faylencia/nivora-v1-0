<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_store_floor_plans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            // Outlets were merged into businesses, so outlet_id references the business id.
            $table->foreignId('outlet_id')->nullable()->constrained('businesses')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->enum('status', ['draft', 'saved'])->default('draft');
            $table->decimal('room_length_m', 8, 2)->default(0);
            $table->decimal('room_width_m', 8, 2)->default(0);
            $table->json('input_summary')->nullable();
            $table->json('analysis_result')->nullable();
            $table->json('plan')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
            $table->index(['business_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_store_floor_plans');
    }
};
