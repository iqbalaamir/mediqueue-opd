<?php

namespace App\Http\Controllers\Patient;

use App\Domain\Enums\AppointmentStatus;
use App\Domain\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StoreBookingRequest;
use App\Models\City;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Repositories\Contracts\CityRepositoryInterface;
use App\Repositories\Contracts\DoctorRepositoryInterface;
use App\Repositories\Contracts\DoctorSlotRepositoryInterface;
use App\Repositories\Contracts\HospitalRepositoryInterface;
use App\Services\Booking\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    protected const STEPS = ['City', 'Hospital', 'Doctor', 'Schedule', 'Details'];

    public function __construct(
        protected CityRepositoryInterface $cities,
        protected HospitalRepositoryInterface $hospitals,
        protected DoctorRepositoryInterface $doctors,
        protected DoctorSlotRepositoryInterface $slots,
    ) {}

    public function index(Request $request): View
    {
        $cities = $this->cities->activeOrdered($request->query('q'));

        return view('patient.book.cities', [
            'cities' => $cities,
            'steps' => self::STEPS,
            'currentStep' => 1,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('home', absolute: false)],
                ['label' => 'Book Appointment'],
            ],
        ]);
    }

    public function hospitals(Request $request, City $city): View
    {
        abort_unless($city->is_active, 404);

        $hospitals = $this->hospitals->activeByCity($city->id, $request->query('q'));

        return view('patient.book.hospitals', [
            'city' => $city,
            'hospitals' => $hospitals,
            'steps' => self::STEPS,
            'currentStep' => 2,
            'breadcrumbs' => $this->breadcrumbs($city),
        ]);
    }

    public function doctors(Request $request, Hospital $hospital): View
    {
        abort_unless($hospital->is_active, 404);

        $hospital->load('city');
        $doctors = $this->doctors->activeByHospital($hospital->id, $request->query('q'));

        return view('patient.book.doctors', [
            'city' => $hospital->city,
            'hospital' => $hospital,
            'doctors' => $doctors,
            'steps' => self::STEPS,
            'currentStep' => 3,
            'breadcrumbs' => $this->breadcrumbs($hospital->city, $hospital),
        ]);
    }

    public function schedule(Doctor $doctor): View
    {
        abort_unless($doctor->is_active, 404);

        $doctor->load(['hospital.city', 'department']);
        $slots = $this->slots->bookableForDoctor($doctor->id);
        $slotsByDate = $slots->groupBy(fn ($slot) => $slot->slot_date->toDateString());

        return view('patient.book.schedule', [
            'city' => $doctor->hospital->city,
            'hospital' => $doctor->hospital,
            'doctor' => $doctor,
            'slotsByDate' => $slotsByDate,
            'steps' => self::STEPS,
            'currentStep' => 4,
            'breadcrumbs' => $this->breadcrumbs(
                $doctor->hospital->city,
                $doctor->hospital,
                $doctor,
            ),
        ]);
    }

    public function details(Request $request): View|RedirectResponse
    {
        $slotUuid = $request->query('slot');

        if (! $slotUuid) {
            return redirect()->to(route('book.index', absolute: false))
                ->with('error', 'Please select a time slot to continue.');
        }

        $slot = $this->slots->findByUuid($slotUuid);

        if (! $slot || ! $slot->isBookable()) {
            return redirect()->back()
                ->with('error', 'Selected slot is no longer available. Please choose another.');
        }

        $doctor = $slot->doctor;
        $doctor->load(['hospital.city', 'department']);

        return view('patient.book.details', [
            'city' => $doctor->hospital->city,
            'hospital' => $doctor->hospital,
            'doctor' => $doctor,
            'slot' => $slot,
            'steps' => self::STEPS,
            'currentStep' => 5,
            'otpRequired' => app(\App\Services\Booking\BookingOtpService::class)->isRequired(),
            'breadcrumbs' => $this->breadcrumbs(
                $doctor->hospital->city,
                $doctor->hospital,
                $doctor,
                'Patient details',
            ),
        ]);
    }

    public function store(StoreBookingRequest $request, BookingService $booking): RedirectResponse
    {
        $appointment = $booking->book($request->validated());

        $requiresPayment = $appointment->status === AppointmentStatus::Pending
            && $appointment->payment_status === PaymentStatus::Pending;

        if ($requiresPayment) {
            return redirect()->to(route('book.pay', $appointment, absolute: false))
                ->with('success', 'Appointment reserved. Complete payment to confirm.');
        }

        return redirect()->to(route('appointments.show', $appointment, absolute: false))
            ->with('success', 'Appointment confirmed successfully!');
    }

    protected function breadcrumbs(
        ?City $city = null,
        ?Hospital $hospital = null,
        ?Doctor $doctor = null,
        ?string $finalLabel = null,
    ): array {
        $items = [
            ['label' => 'Home', 'url' => route('home', absolute: false)],
            ['label' => 'Book', 'url' => route('book.index', absolute: false)],
        ];

        if ($city) {
            $items[] = [
                'label' => $city->name,
                'url' => $hospital || $doctor || $finalLabel
                    ? route('book.hospitals', $city, absolute: false)
                    : null,
            ];
        }

        if ($hospital) {
            $items[] = [
                'label' => $hospital->name,
                'url' => $doctor || $finalLabel
                    ? route('book.doctors', $hospital, absolute: false)
                    : null,
            ];
        }

        if ($doctor) {
            $items[] = [
                'label' => $doctor->name,
                'url' => $finalLabel
                    ? route('book.schedule', $doctor, absolute: false)
                    : null,
            ];
        }

        if ($finalLabel) {
            $items[] = ['label' => $finalLabel];
        }

        return $items;
    }
}
