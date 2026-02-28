<?php

namespace App\Listeners;

use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogAuthActivity
{
    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function handle($event): void
    {
        if ($event instanceof Login && $event->user) {
            $this->activityLogService->logAuthEvent($event->user, 'login');
        }

        if ($event instanceof Logout && $event->user) {
            $this->activityLogService->logAuthEvent($event->user, 'logout');
        }
    }
}
