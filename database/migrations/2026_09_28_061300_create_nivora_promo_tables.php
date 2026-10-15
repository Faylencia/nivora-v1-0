<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->enum('type', ['product', 'transaction']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_transaction', 15, 2)->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->index(['business_id', 'status', 'start_date', 'end_date']);
        });

        Schema::create('promo_products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('promo_id')->constrained('promos')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unique(['promo_id', 'product_id'], 'promo_products_unique');
        });

        Schema::create('sale_promos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('promo_id')->constrained('promos')->restrictOnDelete();
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->dateTime('created_at')->useCurrent();
            $table->unique(['sale_id', 'promo_id'], 'sale_promos_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_promos');
        Schema::dropIfExists('promo_products');
        Schema::dropIfExists('promos');
    }
};
