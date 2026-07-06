<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('member_code', 50);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();
            $table->string('photo_path')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'member_code']);
            $table->index(['branch_id', 'status']);
        });

        Schema::create('membership_plans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('code', 50);
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['time_based', 'pack_based', 'hybrid']);
            $table->unsignedInteger('duration_days')->nullable();
            $table->unsignedInteger('visit_limit')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('currency', 10)->default('KES');
            $table->unsignedInteger('grace_period_days')->default(0);
            $table->unsignedInteger('allowed_check_in_window_hours')->default(24);
            $table->unsignedInteger('max_daily_visits')->nullable();
            $table->unsignedInteger('freeze_allowance_days')->default(0);
            $table->boolean('auto_renewable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['branch_id', 'code']);
            $table->index(['branch_id', 'type', 'is_active']);
        });

        Schema::create('memberships', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->foreignId('membership_plan_id')->constrained('membership_plans')->restrictOnDelete();
            $table->enum('status', ['pending', 'active', 'expired', 'paused', 'cancelled'])->default('pending');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('last_renewed_at')->nullable();
            $table->unsignedInteger('remaining_visits')->nullable();
            $table->unsignedInteger('total_visits')->nullable();
            $table->boolean('auto_renew_enabled')->default(false);
            $table->enum('renewal_source', ['manual', 'auto', 'promo'])->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'member_id', 'status']);
            $table->index(['branch_id', 'status', 'ends_at']);
        });

        Schema::create('membership_freezes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('membership_id')->nullable()->constrained('memberships')->nullOnDelete();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->text('reason')->nullable();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->index(['branch_id', 'membership_id', 'status']);
        });

        Schema::create('membership_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('membership_id')->nullable()->constrained('memberships')->nullOnDelete();
            $table->enum('type', ['activation', 'renewal', 'pause', 'resume', 'adjustment', 'expiry']);
            $table->timestamp('previous_starts_at')->nullable();
            $table->timestamp('previous_ends_at')->nullable();
            $table->timestamp('new_starts_at')->nullable();
            $table->timestamp('new_ends_at')->nullable();
            $table->unsignedInteger('previous_remaining_visits')->nullable();
            $table->unsignedInteger('new_remaining_visits')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->morphs('reference');
            $table->foreignId('performed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'membership_id', 'type']);
        });

        Schema::create('member_qr_tokens', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('token_hash', 255)->unique();
            $table->timestamp('rotated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['branch_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_qr_tokens');
        Schema::dropIfExists('membership_transactions');
        Schema::dropIfExists('membership_freezes');
        Schema::dropIfExists('memberships');
        Schema::dropIfExists('membership_plans');
        Schema::dropIfExists('members');
    }
};
