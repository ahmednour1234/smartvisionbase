<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount_price', 10, 2)->nullable(); // سعر الخصم
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->integer('sort')->default(0);
            $table->boolean('active')->default(true);
            $table->integer('duration')->nullable(); // مدة الباقة بالأيام
            $table->string('image')->nullable(); // صورة للباقة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
