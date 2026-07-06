<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->unique(['branch_id', 'name']);
        });

        Schema::create('equipment_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('equipment_category_id')->nullable()->constrained('equipment_categories')->nullOnDelete();
            $table->string('asset_tag', 100);
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('barcode')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expires_at')->nullable();
            $table->string('condition', 50)->nullable();
            $table->string('location')->nullable();
            $table->enum('status', ['available', 'in_use', 'under_repair', 'retired'])->default('available');
            $table->timestamps();

            $table->unique(['branch_id', 'asset_tag']);
            $table->unique(['branch_id', 'barcode']);
        });

        Schema::create('stock_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('sku', 100);
            $table->string('name');
            $table->string('unit', 30);
            $table->string('barcode')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('current_stock')->default(0);
            $table->integer('reorder_level')->default(0);
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['branch_id', 'sku']);
            $table->unique(['branch_id', 'barcode']);
        });

        Schema::create('stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
            $table->enum('movement_type', ['in', 'out', 'adjustment', 'transfer']);
            $table->integer('quantity');
            $table->string('location_from')->nullable();
            $table->string('location_to')->nullable();
            $table->morphs('reference');
            $table->foreignId('performed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('occurred_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'stock_item_id', 'movement_type']);
        });

        Schema::create('maintenance_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('equipment_item_id')->constrained('equipment_items')->cascadeOnDelete();
            $table->timestamp('performed_at');
            $table->text('description');
            $table->decimal('cost', 12, 2)->nullable();
            $table->timestamp('next_due_at')->nullable();
            $table->enum('status', ['open', 'completed', 'cancelled'])->default('open');
            $table->timestamps();

            $table->index(['branch_id', 'equipment_item_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_items');
        Schema::dropIfExists('equipment_items');
        Schema::dropIfExists('equipment_categories');
    }
};
