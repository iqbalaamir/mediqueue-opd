<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('appointment_number')->unique();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_slot_id')->constrained()->cascadeOnDelete();
            $table->date('appointment_date');
            $table->time('slot_start_time');
            $table->time('slot_end_time');
            $table->string('patient_name');
            $table->string('patient_mobile', 20);
            $table->unsignedTinyInteger('patient_age')->nullable();
            $table->string('patient_gender', 20)->nullable();
            $table->text('patient_address')->nullable();
            $table->text('remark')->nullable();
            $table->string('visit_type')->default('first_visit');
            $table->decimal('consultation_fee', 10, 2)->default(0);
            $table->decimal('amount_due', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->string('payment_mode')->nullable();
            $table->string('payment_status')->default('not_required');
            $table->timestamp('payment_due_at')->nullable();
            $table->foreignId('previous_appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->text('qr_payload')->nullable();
            $table->string('otp', 10)->nullable();
            $table->timestamp('otp_verified_at')->nullable();
            $table->timestamp('booked_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'patient_mobile', 'appointment_date']);
            $table->index(['status', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
