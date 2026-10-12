<?php

namespace App\Support;

use App\Models\ActivityLog;

/**
 * Mencatat aktivitas admin ke tabel activity_logs dengan konteks request
 * yang sedang berjalan (user, IP, user agent).
 */
class ActivityLogger
{
    public static function log(string $action, ?string $description = null, array $meta = []): ?ActivityLog
    {
        $request = request();

        if (! $request) {
            return null;
        }

        return ActivityLog::create([
            'user_id' => $request->user()?->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent() ? substr($request->userAgent(), 0, 500) : null,
            'meta' => $meta ?: null,
        ]);
    }
}
