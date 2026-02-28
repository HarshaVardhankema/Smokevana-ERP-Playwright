<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserContactAccess extends Model
{
    protected $table = 'user_contact_access';
    
    protected $fillable = [
        'user_id',
        'contact_id'
    ];
    
    public $timestamps = false;
}
