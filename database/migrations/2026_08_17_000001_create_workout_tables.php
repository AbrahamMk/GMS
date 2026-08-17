<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_plans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->default('General');
            $table->string('difficulty')->default('Beginner');
            $table->unsignedSmallInteger('duration_minutes')->default(45);
            $table->unsignedSmallInteger('calories_est')->nullable();
            $table->string('target_muscle')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('workout_plan_exercises', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workout_plan_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedTinyInteger('sets')->default(3);
            $table->string('reps')->default('10');
            $table->string('rest_seconds')->nullable();
            $table->string('weight_note')->nullable();
            $table->string('day_label')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('member_workout_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workout_plan_id')->constrained()->cascadeOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add registration_type to members
        Schema::table('members', function (Blueprint $table): void {
            $table->string('registration_type')->default('walkin')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table): void {
            $table->dropColumn('registration_type');
        });
        Schema::dropIfExists('member_workout_assignments');
        Schema::dropIfExists('workout_plan_exercises');
        Schema::dropIfExists('workout_plans');
    }
};
