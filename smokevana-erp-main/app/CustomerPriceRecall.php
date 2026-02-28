<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerPriceRecall extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
