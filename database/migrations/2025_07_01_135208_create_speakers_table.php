<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('speakers', function (Blueprint $table) {
    $table->id();
    $table->string('name_ar');
    $table->string('name_en');
    $table->string('title_ar')->nullable();
    $table->string('title_en')->nullable();
    $table->string('company_name_ar')->nullable();
    $table->string('company_name_en')->nullable();
    $table->string('linkedin')->nullable();
    $table->text('social_links')->nullable();
    $table->string('image')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speakers');
    }
};
