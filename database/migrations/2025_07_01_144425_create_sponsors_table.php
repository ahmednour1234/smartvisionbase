<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
       Schema::create('sponsors', function (Blueprint $table) {
    $table->id();
    $table->string('name_ar');
    $table->string('name_en');
    $table->string('title_ar')->nullable();
    $table->string('title_en')->nullable();
    $table->string('company_name_ar')->nullable();
    $table->string('company_name_en')->nullable();
    $table->string('phone')->nullable();
    $table->string('image')->nullable();

    $table->boolean('active')->default(true);
    $table->foreignId('category_sponsor_id')->constrained('sponsor_categories')->onDelete('cascade');

    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('sponsors');
    }
};
