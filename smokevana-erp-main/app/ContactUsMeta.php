<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ContactUsMeta extends Model
{
    use HasFactory;
    protected $fillable = ['contact_id', 'key', 'value', 'user_id', 'reactions'];

    protected $casts = [
        'reactions' => 'array',
    ];

    public function contact()
    {
        return $this->belongsTo(ContactUs::class, 'contact_id');
    }
}
