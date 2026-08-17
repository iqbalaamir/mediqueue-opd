<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Enums\DoctorStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\QueueActionRequest;
use App\Models\Doctor;
use App\Models\QueueEntry;
use App\Services\Notification\NotificationManager;
use App\Services\Queue\QueueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QueueDeskController extends Controller
{
    public function index(Request $request, QueueService $queueService): View
    {
        $date = $request->input('date', today()->toDateString());
        $doctors = Doctor::query()->active()->with('hospital')->orderBy('name')->get();
        $selectedDoctor = null;
        $queue = collect();

        if ($request->filled('doctor')) {
            $selectedDoctor = Doctor::query()->with('hospital')->where('uuid', $request->input('doctor'))->firstOrFail();
            $queue = $queueService->getDoctorQueue($selectedDoctor, $date);
        }

        return view('admin.queues.index', [
            'title' => 'Queue Desk',
            'date' => $date,
            'doctors' => $doctors,
            'selectedDoctor' => $selectedDoctor,
            'queue' => $queue,
        ]);
    }

    public function callNext(QueueActionRequest $request, QueueService $queueService, NotificationManager $notifications): RedirectResponse
    {
        $doctor = Doctor::query()->where('uuid', $request->input('doctor_uuid'))->firstOrFail();
        $entry = $queueService->callNext($doctor, $request->input('date'));

        if ($entry?->appointment) {
            $notifications->dispatch($entry->appointment, \App\Domain\Enums\NotificationType::YourTurn);
        }

        return back()->with($entry ? 'success' : 'error', $entry
            ? "Called token {$entry->token_number}."
            : 'No waiting patients in queue.');
    }

    public function serve(QueueEntry $queueEntry, QueueService $queueService): RedirectResponse
    {
        $entry = $queueService->serve($queueEntry);

        return back()->with('success', "Now serving token {$entry->token_number}.");
    }

    public function complete(QueueEntry $queueEntry, QueueService $queueService): RedirectResponse
    {
        $entry = $queueService->complete($queueEntry);

        return back()->with('success', "Completed token {$entry->token_number}.");
    }

    public function skip(QueueEntry $queueEntry, QueueService $queueService): RedirectResponse
    {
        $entry = $queueService->skip($queueEntry);

        return back()->with('success', "Skipped token {$entry->token_number}.");
    }

    public function recall(QueueEntry $queueEntry, QueueService $queueService): RedirectResponse
    {
        $entry = $queueService->recall($queueEntry);

        return back()->with('success', "Recalled token {$entry->token_number}.");
    }

    public function doctorDelay(QueueActionRequest $request, QueueService $queueService, NotificationManager $notifications): RedirectResponse
    {
        $doctor = Doctor::query()->where('uuid', $request->input('doctor_uuid'))->firstOrFail();
        $minutes = (int) $request->input('delay_minutes', 15);
        $queueService->setDoctorDelay($doctor, $minutes);

        $waiting = $queueService->getDoctorQueue($doctor, $request->input('date'))
            ->filter(fn ($entry) => $entry->status === \App\Domain\Enums\QueueEntryStatus::Waiting);

        foreach ($waiting as $entry) {
            if ($entry->appointment) {
                $notifications->dispatch($entry->appointment, \App\Domain\Enums\NotificationType::DoctorDelayed);
            }
        }

        return back()->with('success', "Doctor delay set to {$minutes} extra minutes.");
    }

    public function doctorStatus(QueueActionRequest $request, QueueService $queueService): RedirectResponse
    {
        $doctor = Doctor::query()->where('uuid', $request->input('doctor_uuid'))->firstOrFail();
        $status = DoctorStatus::from($request->input('doctor_status'));
        $queueService->setDoctorStatus($doctor, $status);

        return back()->with('success', 'Doctor status updated to '.$status->label().'.');
    }
}
