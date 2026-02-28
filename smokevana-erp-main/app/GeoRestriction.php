<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeoRestriction extends Model
{
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'target_entities' => 'array',
        'locations' => 'array',
        'is_active' => 'boolean',
        'meta' => 'array'
    ];

    /**
     * Get the logs for this geo restriction.
     */
    public function logs()
    {
        return $this->hasMany(\App\GeoRestrictionLog::class);
    }

    /**
     * Scope a query to only include active restrictions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by rule type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('rule_type', $type);
    }

    /**
     * Scope a query to filter by scope.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $scope
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByScope($query, $scope)
    {
        return $query->where('scope', $scope);
    }

    /**
     * Check if a location is restricted by this rule.
     *
     * @param  array  $location
     * @return bool
     */
    public function isLocationRestricted($location)
    {
        if (!$this->is_active) {
            return false;
        }

        foreach ($this->locations as $restrictedLocation) {
            if ($this->matchesLocation($location, $restrictedLocation)) {
                return $this->rule_type === 'disallow';
            }
        }

        // If rule_type is 'allow' and no location matched, it means restriction
        return $this->rule_type === 'allow';
    }

    /**
     * Check if a location matches a restricted location.
     *
     * @param  array  $location
     * @param  array  $restrictedLocation
     * @return bool
     */
    protected function matchesLocation($location, $restrictedLocation)
    {
        $type = $restrictedLocation['type'] ?? null;
        $value = $restrictedLocation['value'] ?? null;

        if (!$type || !$value) {
            return false;
        }

        if ($type === 'state') {
            return isset($location['state']) && 
                   strtolower($location['state']) === strtolower($value);
        }

        if ($type === 'city') {
            return isset($location['city']) && 
                   strtolower($location['city']) === strtolower($value);
        }

        if ($type === 'zip') {
            return isset($location['zip']) && 
                   $location['zip'] === $value;
        }

        return false;
    }
}

