<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_recommendations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->nullOnDelete();
            $table->string('type', 50);
            $table->string('title', 150);
            $table->text('insight');
            $table->text('recommendation');
            $table->enum('priority', ['low', 'medium', 'high']);
            $table->enum('status', ['new', 'accepted', 'rejected', 'completed'])->default('new');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->index(['business_id', 'status']);
        });

        Schema::create('ai_actions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('recommendation_id')->constrained('ai_recommendations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('action', 100);
            $table->text('result')->nullable();
            $table->string('status', 30)->default('pending');
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('store_analyses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('outlet_id')->constrained('outlets')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('image_url', 500);
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->text('analysis_result')->nullable();
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_analyses');
        Schema::dropIfExists('ai_actions');
        Schema::dropIfExists('ai_recommendations');
    }
};
