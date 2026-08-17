<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('qualification')->nullable();
            $table->string('specialization')->nullable();
            $table->string('token_prefix', 10)->default('A');
            $table->string('status')->default('available');
            $table->unsignedSmallInteger('avg_consult_minutes')->default(15);
            $table->unsignedTinyInteger('experience_years')->nullable();
            $table->decimal('consultation_fee', 10, 2)->default(0);
            $table->decimal('follow_up_fee', 10, 2)->nullable();
            $table->unsignedSmallInteger('follow_up_validity_days')->default(15);
            $table->boolean('online_payment_required')->nullable();
            $table->string('payment_mode')->nullable();
            $table->decimal('advance_payment_amount', 10, 2)->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['hospital_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
