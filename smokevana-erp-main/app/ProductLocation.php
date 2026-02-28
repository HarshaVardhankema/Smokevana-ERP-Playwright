<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLocation extends Model
{
    use HasFactory;
    protected $table = 'product_locations';
    protected $guarded = ['id'];
    protected $timestamp = false;

}
