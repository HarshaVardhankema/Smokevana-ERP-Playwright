<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShippingStation extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the business location that owns this shipping station.
     */
    public function businessLocation()
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }

    /**
     * Get the business that owns this shipping station.
     */
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    /**
     * Get the user assigned to this shipping station.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope a query to only include active shipping stations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Return list of shipping stations for a business
     *
     * @param  int  $business_id
     * @param  bool  $show_all = false
     * @return array
     */
    public static function forDropdown($business_id, $show_all = false, $check_permission = true)
    {
        $query = ShippingStation::where('business_id', $business_id)->Active();

        if ($check_permission) {
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('location_id', $permitted_locations);
            }
        }

        $result = $query->get();

        $stations = $result->pluck('name', 'id');

        if ($show_all) {
            $stations->prepend(__('lang_v1.all'), '');
        }

        return $stations;
    }
}

