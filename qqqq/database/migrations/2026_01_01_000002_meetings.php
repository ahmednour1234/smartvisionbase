<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('meetings')) {
            return;
        }

        Schema::create('meetings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete()
                ->index();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->index();

            $table->dateTime('meeting_at')->index();
            $table->unsignedSmallInteger('duration_minutes')->default(0);

            $table->enum('type', ['call', 'visit', 'online', 'other'])->default('call')->index();
            $table->enum('outcome', ['no_answer', 'interested', 'not_interested', 'follow_up', 'won', 'lost'])
                ->nullable()
                ->index();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'meeting_at']);
            $table->index(['company_id', 'meeting_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
