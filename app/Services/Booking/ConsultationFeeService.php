<?php

namespace App\Services\Booking;

use App\Domain\Enums\VisitType;
use App\Models\Doctor;
use App\Repositories\Contracts\AppointmentRepositoryInterface;

class ConsultationFeeService
{
    public function __construct(
        protected AppointmentRepositoryInterface $appointments,
    ) {}

    public function quote(Doctor $doctor, string $mobile, string $appointmentDate): array
    {
        $previous = $this->appointments->findPreviousVisitForFollowUp(
            $doctor->id,
            $mobile,
            $appointmentDate,
            $doctor->follow_up_validity_days
        );

        $visitType = $previous ? VisitType::FollowUp : VisitType::FirstVisit;
        $fee = $visitType === VisitType::FollowUp
            ? $doctor->getFollowUpFeeAmount()
            : (float) $doctor->consultation_fee;

        return [
            'visit_type' => $visitType,
            'visit_type_label' => $visitType->label(),
            'consultation_fee' => $fee,
            'is_follow_up' => $visitType === VisitType::FollowUp,
            'previous_appointment_id' => $previous?->id,
        ];
    }
}
