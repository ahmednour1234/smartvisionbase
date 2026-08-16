<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->dateTime('event_date');
            $table->string('address_ar');
            $table->string('address_en');
            $table->string('location')->nullable(); // رابط خرائط أو إحداثيات
            $table->integer('attendees_limit')->nullable();
            $table->string('main_image')->nullable(); // مسار الصورة
            $table->boolean('active')->default(true);
            $table->timestamps(); // created_at و updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
