<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $reportService): View
    {
        $from = $request->input('from', today()->subDays(7)->toDateString());
        $to = $request->input('to', today()->toDateString());
        $hospitalId = $request->input('hospital_id');
        $doctorId = $request->input('doctor_id');

        $appointments = $reportService->appointmentsReport($from, $to, $hospitalId, $doctorId);

        return view('admin.reports.index', [
            'appointments' => $appointments,
            'from' => $from,
            'to' => $to,
            'hospitalId' => $hospitalId,
            'doctorId' => $doctorId,
            'hospitals' => $reportService->hospitalsForFilter(),
            'doctors' => $reportService->doctorsForFilter($hospitalId),
            'title' => 'Reports',
        ]);
    }
}
