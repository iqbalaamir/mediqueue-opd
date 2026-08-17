<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_slots', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->date('slot_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('max_patients')->default(10);
            $table->unsignedSmallInteger('booked_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['doctor_id', 'slot_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_slots');
    }
};
