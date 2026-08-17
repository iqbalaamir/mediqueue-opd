<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Services\Report\ReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(ReportService $reportService): View
    {
        return view('admin.dashboard', [
            'stats' => $reportService->dashboardStats(),
            'title' => 'Dashboard',
        ]);
    }
}
