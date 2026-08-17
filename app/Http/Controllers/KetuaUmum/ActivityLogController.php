<?php

namespace App\Http\Controllers\KetuaUmum;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->when($request->user_id, fn ($q) => $q->where('user_id', $request->user_id))
            ->when($request->action, fn ($q) => $q->where('action', $request->action))
            ->when($request->subject_type, fn ($q) => $q->where('subject_type', 'like', "%{$request->subject_type}%"))
            ->when($request->tanggal_dari, fn ($q) => $q->whereDate('created_at', '>=', $request->tanggal_dari))
            ->when($request->tanggal_sampai, fn ($q) => $q->whereDate('created_at', '<=', $request->tanggal_sampai))
            ->latest()
            ->paginate(50);

        return view('ketua-umum.activity-log.index', [
            'logs' => $logs,
            'users' => User::orderBy('name')->get(['id', 'name']),
            'availableActions' => ActivityLog::distinct()->pluck('action'),
        ]);
    }

    public function show(ActivityLog $activityLog)
    {
        return view('ketua-umum.activity-log.show', compact('activityLog'));
    }
}
