<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductState extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_state';

    /**
     * Get the product that owns the state restriction.
     */
    public function product()
    {
        return $this->belongsTo(\App\Product::class);
    }
}

