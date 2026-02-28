<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogUserAccess
{
    protected ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (! Auth::check() || $this->shouldSkip($request)) {
            return $response;
        }

        $route = $request->route();
        $action = $route ? $route->getActionName() : null;
        $routeName = $route ? $route->getName() : null;
        $module = $this->resolveModuleFromAction($action);

        $properties = [
            'module' => $module,
            'route_name' => $routeName,
            'controller_action' => $action,
            'method' => $request->method(),
            'path' => $request->path(),
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        $activity = activity()
            ->causedBy(Auth::user())
            ->withProperties($properties);

        $business_id = $this->activityLogService->resolveBusinessIdForLogging(null, Auth::user());
        if (! empty($business_id)) {
            $activity->tap(function ($log) use ($business_id) {
                $log->business_id = $business_id;
            });
        }

        $activity->log('accessed');

        return $response;
    }

    protected function shouldSkip(Request $request): bool
    {
        if ($request->is('login') || $request->is('logout')) {
            return true;
        }

        if ($request->is('reports/activity-log')) {
            return true;
        }

        $path = $request->path();
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|woff|woff2|ttf|eot|map|ico)$/i', $path)) {
            return true;
        }

        return false;
    }

    protected function resolveModuleFromAction(?string $action): string
    {
        if (empty($action)) {
            return 'App';
        }

        if (str_contains($action, 'Modules\\')) {
            $parts = explode('\\', $action);
            $modulesIndex = array_search('Modules', $parts, true);
            if ($modulesIndex !== false && isset($parts[$modulesIndex + 1])) {
                return $parts[$modulesIndex + 1];
            }
        }

        return 'App';
    }
}
