<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrashSource extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trash_sources';

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
        'json_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who created this record
     */
    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    /**
     * Get the business that owns this record
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }

    /**
     * Get the model instance (polymorphic)
     */
    public function model()
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    /**
     * Get the target model instance (for merges)
     */
    public function targetModel()
    {
        if ($this->target_model_type && $this->target_model_id) {
            return $this->morphTo('targetModel', 'target_model_type', 'target_model_id');
        }
        return null;
    }

    /**
     * Scope a query to only include records for a specific business.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $business_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForBusiness($query, $business_id)
    {
        return $query->where('business_id', $business_id);
    }

    /**
     * Scope a query to only include records of a specific action type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $action_type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActionType($query, $action_type)
    {
        return $query->where('action_type', $action_type);
    }

    /**
     * Scope a query to only include records for a specific model type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $model_type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeModelType($query, $model_type)
    {
        return $query->where('model_type', $model_type);
    }
}
