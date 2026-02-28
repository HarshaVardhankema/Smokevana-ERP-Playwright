<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoaList extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(CoaCategory::class, 'coa_category_id');
    }
}
