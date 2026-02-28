<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Contact;

class CreditApplication extends Model
{
    use HasFactory;

    protected $table = 'credit_applications';

    protected $fillable = [
        'contact_id',
        'requested_credit_amount',
        'average_revenue_per_month',
        'application_data',
        'supporting_documents_paths',
        'authorized_signatory_name',
        'authorized_signatory_email',
        'authorized_signatory_phone',
        'digital_signatures_paths',
        'credit_application_status',
        // Owner fields
        'owner_name',
        'owner_email',
        'owner_date_of_birth',
        'owner_ssn',
        'owner_title',
        'owner_address',
        'owner_city_state_zip',
        'owner_phone',
        'owner_ownership_percentage',
        'owner_dl_number',
        'owner_dl_state',
        'additional_owners',
    ];

    protected $casts = [
        'application_data' => 'array',
        'supporting_documents_paths' => 'array',
        'digital_signatures_paths' => 'array',
        'requested_credit_amount' => 'decimal:2',
        'average_revenue_per_month' => 'decimal:2',
        'owner_date_of_birth' => 'date',
        'owner_ownership_percentage' => 'decimal:2',
        'additional_owners' => 'array',
    ];

    /**
     * Get the contact that owns the credit application
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }
}
