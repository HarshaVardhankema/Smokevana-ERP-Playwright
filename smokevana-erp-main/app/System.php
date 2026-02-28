<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class System extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Return the value of the property
     *
     * @param $key string
     * @return mixed
     */
    public static function getProperty($key)
    {
        // Check if table exists before querying (for migrations)
        try {
            if (!Schema::hasTable('system')) {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }

        try {
            $row = System::where('key', $key)
                    ->first();

            if (isset($row->value)) {
                return $row->value;
            } else {
                return null;
            }
        } catch (\Exception $e) {
            // If table doesn't exist or any other error, return null
            return null;
        }
    }

    /**
     * Return the value of the multiple properties
     *
     * @param $keys array
     * @return array
     */
    public static function getProperties($keys, $pluck = false)
    {
        // Check if table exists before querying (for migrations)
        try {
            if (!Schema::hasTable('system')) {
                return $pluck ? [] : [];
            }
        } catch (\Exception $e) {
            return $pluck ? [] : [];
        }

        try {
            if ($pluck == true) {
                return System::whereIn('key', $keys)
                    ->pluck('value', 'key');
            } else {
                return System::whereIn('key', $keys)
                    ->get()
                    ->toArray();
            }
        } catch (\Exception $e) {
            // If table doesn't exist or any other error, return empty array
            return $pluck ? [] : [];
        }
    }

    /**
     * Return the system default currency details
     *
     * @return object|null
     */
    public static function getCurrency()
    {
        // Check if table exists before querying (for migrations)
        try {
            if (!Schema::hasTable('system')) {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }

        try {
            $row = System::where('key', 'app_currency_id')
                    ->first();

            if (!$row || !isset($row->value)) {
                return null;
            }

            $c_id = $row->value;
            $currency = Currency::find($c_id);

            return $currency;
        } catch (\Exception $e) {
            // If table doesn't exist or any other error, return null
            return null;
        }
    }

    /**
     * Set the property
     *
     * @param $key
     * @param $value
     * @return void
     */
    public static function setProperty($key, $value): void
    {
        System::where('key', $key)
            ->update(['value' => $value]);
    }

    /**
     * Remove the specified property
     *
     * @param $key
     * @return void
     */
    public static function removeProperty($key)
    {
        System::where('key', $key)
            ->delete();
    }

    /**
     * Add a new property, if exist update the value
     *
     * @param $key
     * @param $value
     * @return void
     */
    public static function addProperty($key, $value)
    {
        System::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
