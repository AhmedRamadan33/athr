<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __construct(protected ActivityLogService $activityLogService) {}

    public function index(Request $request): View
    {
        return view('admin.activity-log.index', [
            'activities' => $this->activityLogService->paginate($request->only(['search', 'log_name', 'causer_id', 'from', 'to'])),
            'logNames' => $this->activityLogService->logNames(),
            'causers' => $this->activityLogService->causers(),
        ]);
    }
}
