<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_sessions', function (Blueprint $table): void {
            $table->foreignId('trainer_id')->nullable()->constrained('trainers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('class_sessions', function (Blueprint $table): void {
            $table->dropForeign(['trainer_id']);
            $table->dropColumn('trainer_id');
        });
    }
};
