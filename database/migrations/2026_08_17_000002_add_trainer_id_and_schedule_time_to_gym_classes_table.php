<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gym_classes', function (Blueprint $table): void {
            $table->foreignId('trainer_id')->nullable()->after('trainer_user_id')->constrained('trainers')->nullOnDelete();
            $table->string('schedule_time')->nullable()->default('09:00')->after('duration_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('gym_classes', function (Blueprint $table): void {
            $table->dropForeign(['trainer_id']);
            $table->dropColumn(['trainer_id', 'schedule_time']);
        });
    }
};
