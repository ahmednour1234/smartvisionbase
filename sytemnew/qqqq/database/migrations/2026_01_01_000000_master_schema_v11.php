<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

            $table->enum('role', ['admin', 'manager', 'sales'])->default('sales');
            $table->boolean('is_active')->default(true);

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('iso2', 2);
            $table->timestamps();
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('bank_details')->nullable();
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');

            // V11 FIX: Hard guard against duplicates (prevents race-condition duplicates).
            $table->string('normalized_company_name')->unique();

            // V11 FIX: Performance indexes for p95.
            $table->foreignId('owner_id')->nullable()->index()->constrained('users')->nullOnDelete();

            $table->enum('status', ['new', 'contacted', 'meeting', 'negotiation', 'won', 'lost'])
                ->default('new')
                ->index();

            $table->foreignId('event_id')->nullable()->index()->constrained('events')->nullOnDelete();
            $table->foreignId('package_id')->nullable()->index()->constrained('packages')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->index()->constrained('countries')->nullOnDelete();

            $table->string('contact_person')->nullable();
            $table->string('contact_mobile')->nullable();
            $table->string('contact_email')->nullable();
            $table->date('next_followup_date')->nullable()->index();

            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('job_runs', function (Blueprint $table) {
            $table->id();
            $table->string('job_name');
            $table->enum('status', ['success', 'failed']);
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_runs');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('events');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('users');
    }
};
