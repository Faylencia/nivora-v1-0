<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_store_floor_plans', function (Blueprint $table): void {
            if (! Schema::hasColumn('ai_store_floor_plans', 'concept')) {
                $table->string('concept', 50)->default('minimalis')->after('status');
            }

            if (! Schema::hasColumn('ai_store_floor_plans', 'original_photos')) {
                $table->json('original_photos')->nullable()->after('input_summary');
            }

            if (! Schema::hasColumn('ai_store_floor_plans', 'generated_image_path')) {
                $table->string('generated_image_path', 500)->nullable()->after('original_photos');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ai_store_floor_plans', function (Blueprint $table): void {
            $table->dropColumn(['concept', 'original_photos', 'generated_image_path']);
        });
    }
};
