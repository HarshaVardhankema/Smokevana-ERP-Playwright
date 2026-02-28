<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellingPriceGroup extends Model
{
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('selling_price_groups.is_active', 1);
    }

    /**
     * Return list of selling price groups
     *
     * @param  int  $business_id
     * @param  bool  $with_default
     * @param  bool  $use_sequence  Whether to sort by sequence from common_settings
     * @return array
     */
    public static function forDropdown($business_id, $with_default = true, $use_sequence = true)
    {
        $price_groups = SellingPriceGroup::where('business_id', $business_id)
                                    ->active()
                                    ->get();

        // Get sequence mapping from common_settings if available
        $sequence_map = [];
        if ($use_sequence) {
            $business = Business::find($business_id);
            if ($business && !empty($business->common_settings)) {
                $common_settings = is_string($business->common_settings) 
                    ? json_decode($business->common_settings, true) 
                    : $business->common_settings;
                
                if (!empty($common_settings['price_group_sequence'])) {
                    $sequence_map = $common_settings['price_group_sequence'];
                }
            }
        }

        // Sort price groups by sequence if available
        if (!empty($sequence_map)) {
            $price_groups = $price_groups->sortBy(function ($price_group) use ($sequence_map) {
                return $sequence_map[$price_group->id] ?? 9999; // Put unsequenced items at the end
            })->values();
        }

        $dropdown = [];

        if ($with_default && auth()->user()->can('access_default_selling_price')) {
            $dropdown[0] = __('lang_v1.default_selling_price');
        }

        foreach ($price_groups as $price_group) {
            if (auth()->user()->can('selling_price_group.'.$price_group->id)) {
                $dropdown[$price_group->id] = $price_group->name;
            }
        }

        return $dropdown;
    }

    /**
     * Get price groups sorted by sequence
     *
     * @param  int  $business_id
     * @return \Illuminate\Support\Collection
     */
    public static function getSortedBySequence($business_id)
    {
        $price_groups = SellingPriceGroup::where('business_id', $business_id)
                                    ->active()
                                    ->get();

        // Get sequence mapping from common_settings
        $sequence_map = [];
        $business = Business::find($business_id);
        if ($business && !empty($business->common_settings)) {
            $common_settings = is_string($business->common_settings) 
                ? json_decode($business->common_settings, true) 
                : $business->common_settings;
            
            if (!empty($common_settings['price_group_sequence'])) {
                $sequence_map = $common_settings['price_group_sequence'];
            }
        }

        // Sort by sequence if available
        if (!empty($sequence_map)) {
            $price_groups = $price_groups->sortBy(function ($price_group) use ($sequence_map) {
                return $sequence_map[$price_group->id] ?? 9999;
            })->values();
        }

        return $price_groups;
    }

    /**
     * Counts total number of selling price groups
     *
     * @param  int  $business_id
     * @return array
     */
    public static function countSellingPriceGroups($business_id)
    {
        $count = SellingPriceGroup::where('business_id', $business_id)
                                ->active()
                                ->count();

        return $count;
    }
    public function meta(){
        return $this->hasOne(VariationGroupPrice::class,'price_group_id');
    }
}
