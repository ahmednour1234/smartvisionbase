<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('event_schedule_speaker', function (Blueprint $table) {
        $table->id();
        $table->foreignId('event_schedule_id')->constrained('event_schedules')->onDelete('cascade');
        $table->foreignId('speaker_id')->constrained('speakers')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_schedule_speaker');
    }
};
