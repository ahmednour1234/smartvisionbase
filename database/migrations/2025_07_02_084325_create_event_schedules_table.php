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
    Schema::create('event_schedules', function (Blueprint $table) {
        $table->id();
        $table->foreignId('event_id')->constrained('events')->onDelete('cascade');

        $table->string('title_ar')->nullable();
        $table->string('title_en')->nullable();
        $table->text('description_ar')->nullable();
        $table->text('description_en')->nullable();

        $table->string('location_ar')->nullable();
        $table->string('location_en')->nullable();

        $table->dateTime('start_datetime');
        $table->dateTime('end_datetime')->nullable();

        $table->unsignedInteger('max_attendees')->nullable();

        $table->enum('status', ['upcoming', 'ongoing', 'completed', 'canceled'])->default('upcoming');

        $table->string('logo')->nullable(); // شعار الجلسة

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_schedules');
    }
};
