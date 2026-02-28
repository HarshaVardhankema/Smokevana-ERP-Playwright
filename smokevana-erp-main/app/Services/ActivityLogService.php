<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class ActivityLogService
{
    protected array $originalSnapshots = [];

    protected array $sensitiveAttributes = [
        'password',
        'remember_token',
        'api_token',
        'api_key',
        'token',
        'secret',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected array $ignoredAttributes = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function storeOriginal(Model $model): void
    {
        $this->originalSnapshots[spl_object_hash($model)] = $model->getOriginal();
    }

    public function logModelEvent(Model $model, string $event): void
    {
        if ($this->shouldSkip($model)) {
            return;
        }

        $properties = $this->buildProperties($model, $event);
        if ($event === 'updated' && empty($properties['attributes'])) {
            return;
        }

        $activity = activity()
            ->performedOn($model);

        if (Auth::check()) {
            $activity->causedBy(Auth::user());
        }

        if (! empty($properties)) {
            $activity->withProperties($properties);
        }

        $business_id = $this->resolveBusinessId($model, Auth::user());
        if (! empty($business_id)) {
            $activity->tap(function ($log) use ($business_id) {
                $log->business_id = $business_id;
            });
        }

        $activity->log($event);

        if ($event === 'updated') {
            unset($this->originalSnapshots[spl_object_hash($model)]);
        }
    }

    public function logAuthEvent(Model $user, string $event): void
    {
        if ($this->shouldSkip($user)) {
            return;
        }

        $properties = [
            'model' => class_basename($user),
            'module' => $this->resolveModule($user),
            'table' => $user->getTable(),
            'subject_id' => $user->getKey(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        $activity = activity()
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties($properties);

        $business_id = $this->resolveBusinessId($user, $user);
        if (! empty($business_id)) {
            $activity->tap(function ($log) use ($business_id) {
                $log->business_id = $business_id;
            });
        }

        $activity->log($event);
    }

    protected function shouldSkip(Model $model): bool
    {
        if ($model instanceof Activity || $model instanceof Pivot) {
            return true;
        }

        if (in_array(LogsActivity::class, class_uses_recursive($model), true)) {
            return true;
        }

        return false;
    }

    protected function buildProperties(Model $model, string $event): array
    {
        $properties = [
            'model' => class_basename($model),
            'module' => $this->resolveModule($model),
            'table' => $model->getTable(),
            'subject_id' => $model->getKey(),
        ];

        if ($event === 'updated') {
            $changes = $model->getChanges();
            $original = $this->originalSnapshots[spl_object_hash($model)] ?? [];

            $attributes = [];
            $old = [];
            foreach ($changes as $key => $new_value) {
                if ($this->shouldIgnoreAttribute($model, $key)) {
                    continue;
                }

                $attributes[$key] = $new_value;
                $old[$key] = $original[$key] ?? null;
            }

            if (! empty($attributes)) {
                $properties['attributes'] = $attributes;
                $properties['old'] = $old;
            }

            return $properties;
        }

        $attributes = $this->filterAttributes($model->getAttributes(), $model);
        if (! empty($attributes)) {
            $properties['attributes'] = $attributes;
        }

        return $properties;
    }

    protected function filterAttributes(array $attributes, Model $model): array
    {
        foreach ($attributes as $key => $value) {
            if ($this->shouldIgnoreAttribute($model, $key)) {
                unset($attributes[$key]);
            }
        }

        return $attributes;
    }

    protected function shouldIgnoreAttribute(Model $model, string $key): bool
    {
        if (in_array($key, $this->ignoredAttributes, true)) {
            return true;
        }

        if (in_array($key, $this->sensitiveAttributes, true)) {
            return true;
        }

        if (in_array($key, $model->getHidden(), true)) {
            return true;
        }

        return false;
    }

    protected function resolveBusinessId(Model $model, ?Model $user): ?int
    {
        if (Session::has('user.business_id')) {
            return (int) Session::get('user.business_id');
        }

        if (Session::has('business.id')) {
            return (int) Session::get('business.id');
        }

        if (isset($model->business_id)) {
            return (int) $model->business_id;
        }

        if (! empty($user) && isset($user->business_id)) {
            return (int) $user->business_id;
        }

        return null;
    }

    public function resolveBusinessIdForLogging(?Model $model = null, ?Model $user = null): ?int
    {
        if ($model) {
            return $this->resolveBusinessId($model, $user);
        }

        if (Session::has('user.business_id')) {
            return (int) Session::get('user.business_id');
        }

        if (Session::has('business.id')) {
            return (int) Session::get('business.id');
        }

        if (! empty($user) && isset($user->business_id)) {
            return (int) $user->business_id;
        }

        return null;
    }

    protected function resolveModule(Model $model): string
    {
        $class = get_class($model);
        if (str_starts_with($class, 'Modules\\')) {
            $parts = explode('\\', $class);
            return $parts[1] ?? 'Modules';
        }

        return 'App';
    }
}
