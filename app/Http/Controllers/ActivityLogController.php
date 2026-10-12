<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = ActivityLog::query()
            ->with('user')
            ->when($request->input('search'), fn ($query, $search) => $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($user) => $user
                        ->where('fullname', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.activity-logs.index', [
            'logs' => $logs,
        ]);
    }

    public function destroy(Request $request, ActivityLog $log): RedirectResponse
    {
        $log->delete();

        return back()->with('success', 'Log aktivitas berhasil dihapus');
    }

    public function clear(Request $request): RedirectResponse
    {
        ActivityLog::query()->delete();

        ActivityLogger::log('activity.cleared', 'Menghapus seluruh log aktivitas');

        return back()->with('success', 'Log aktivitas berhasil dibersihkan');
    }
}
