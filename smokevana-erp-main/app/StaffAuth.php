<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
class StaffAuth extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use HasRoles;
    public function getJWTIdentifier()
    {
        return $this->getKey();  // You can return the primary key or any custom identifier
    }

    /**
     * Get the custom claims to add to the JWT payload.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];  // You can add custom claims to the token if needed
    }
    protected $table = 'users'; // Specify the WordPress users table

    protected $primaryKey = 'id'; // Set the primary key to 'ID'

    protected $guarded = [];
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function scopeUser($query)
    {
        return $query->where('users.user_type', 'user');
    }

    public function contactAccess()
    {
        return $this->belongsToMany(\App\Contact::class, 'user_contact_access');
    }

}
