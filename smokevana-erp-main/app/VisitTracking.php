<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisitTracking extends Model
{
    protected $table = 'visit_tracking';
    
    protected $fillable = [
        'business_id',
        'sales_rep_id', 
        'lead_id',
        'start_time',
        'checkin_latitude',
        'checkin_longitude',
        'checkout_time',
        'checkout_latitude',
        'checkout_longitude',
        'duration',
        'status',
        'visit_type',
        'location_proof',
        'photo_proof',
        'signature_proof',
        'video_proof',
        'location_proof_path',
        'photo_proof_paths',
        'signature_proof_path',
        'video_proof_path',
        'remarks',
        'created_by'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'checkout_time' => 'datetime',
        'location_proof' => 'boolean',
        'photo_proof' => 'boolean',
        'signature_proof' => 'boolean',
        'video_proof' => 'boolean',
        'photo_proof_paths' => 'array'
    ];

    // Relationships
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function salesRep()
    {
        return $this->belongsTo(User::class, 'sales_rep_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
