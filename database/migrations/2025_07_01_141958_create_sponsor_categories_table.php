<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sponsor_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // الاسم بالعربي
            $table->string('name_en'); // الاسم بالإنجليزي
            $table->boolean('active')->default(true); // الحالة
            $table->string('logo')->nullable(); // الشعار
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_categories');
    }
};
