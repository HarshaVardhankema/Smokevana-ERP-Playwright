<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'next_follow_up_date' => 'datetime',
        'last_contact_date' => 'datetime',
        'converted_at' => 'datetime',
        'best_contact_time_start' => 'datetime',
        'best_contact_time_end' => 'datetime',
        'tags' => 'array',
        'custom_fields' => 'array',
        'is_qualified' => 'boolean',
        'is_hot_lead' => 'boolean',
        'requires_immediate_attention' => 'boolean',
        'estimated_value' => 'decimal:2',
        'actual_value' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the user who created the lead.
     */
    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    /**
     * Get the user who visited the lead.
     */
    public function visitor()
    {
        return $this->belongsTo(\App\User::class, 'visited_by');
    }

    /**
     * Get the user assigned to the lead.
     */
    public function assignedTo()
    {
        return $this->belongsTo(\App\User::class, 'assigned_to');
    }

    /**
     * The user who created this lead
     */
    public function createdByUser()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    /**
     * Get the sales rep assigned to the lead.
     */
    public function salesRep()
    {
        return $this->belongsTo(\App\User::class, 'sales_rep_id');
    }

    /**
     * Get the contact this lead was converted to.
     */
    public function convertedToContact()
    {
        return $this->belongsTo(\App\Contact::class, 'converted_to_contact_id');
    }

    /**
     * Get the customer this lead was converted to.
     */
    public function convertedToCustomer()
    {
        return $this->belongsTo(\App\Contact::class, 'converted_to_customer_id');
    }

    /**
     * Get the business that owns the lead.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Get all visit tracking records for this lead.
     */
    public function visits()
    {
        return $this->hasMany(\App\VisitTracking::class);
    }

    /**
     * Alias for visits() relationship.
     */
    public function visitTracking()
    {
        return $this->hasMany(\App\VisitTracking::class);
    }

    /**
     * Get all tickets for this lead.
     */
    public function tickets()
    {
        return $this->hasMany(\App\Ticket::class, 'lead_id');
    }

    /**
     * Scope a query to only include leads for a specific business.
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
     * Scope a query to only include leads created by a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $user_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedBy($query, $user_id)
    {
        return $query->where('created_by', $user_id);
    }

    /**
     * Scope a query to only include leads assigned to a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $user_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAssignedTo($query, $user_id)
    {
        return $query->where('assigned_to', $user_id);
    }

    /**
     * Scope a query to only include leads for a specific sales rep.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $sales_rep_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSalesRep($query, $sales_rep_id)
    {
        return $query->where('sales_rep_id', $sales_rep_id);
    }

    /**
     * Scope a query to only include leads with a specific status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('lead_status', $status);
    }

    /**
     * Scope a query to only include leads with a specific priority.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $priority
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to only include leads from a specific source.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $source
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromSource($query, $source)
    {
        return $query->where('lead_source', $source);
    }

    /**
     * Scope a query to only include leads in a specific funnel stage.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $stage
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInFunnelStage($query, $stage)
    {
        return $query->where('funnel_stage', $stage);
    }

    /**
     * Scope a query to only include hot leads.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHotLeads($query)
    {
        return $query->where('is_hot_lead', true);
    }

    /**
     * Scope a query to only include qualified leads.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeQualified($query)
    {
        return $query->where('is_qualified', true);
    }

    /**
     * Scope a query to only include leads requiring immediate attention.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequiresAttention($query)
    {
        return $query->where('requires_immediate_attention', true);
    }

    /**
     * Scope a query to only include leads with follow-up due.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFollowUpDue($query)
    {
        return $query->where('next_follow_up_date', '<=', now());
    }

    /**
     * Scope a query to only include converted leads.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConverted($query)
    {
        return $query->where('lead_status', 'converted');
    }

    /**
     * Get the full address attribute.
     *
     * @return string
     */
    public function getFullAddressAttribute()
    {
        $address_parts = [];
        
        if (!empty($this->address_line_1)) {
            $address_parts[] = $this->address_line_1;
        }
        
        if (!empty($this->address_line_2)) {
            $address_parts[] = $this->address_line_2;
        }
        
        if (!empty($this->city)) {
            $address_parts[] = $this->city;
        }
        
        if (!empty($this->state)) {
            $address_parts[] = $this->state;
        }
        
        if (!empty($this->country)) {
            $address_parts[] = $this->country;
        }
        
        if (!empty($this->zip_code)) {
            $address_parts[] = $this->zip_code;
        }
        
        return implode(', ', $address_parts);
    }

    /**
     * Get the formatted address for display.
     *
     * @return string
     */
    public function getFormattedAddressAttribute()
    {
        $address_lines = [];
        
        if (!empty($this->address_line_1)) {
            $address_lines[] = $this->address_line_1;
        }
        
        if (!empty($this->address_line_2)) {
            $address_lines[] = $this->address_line_2;
        }
        
        $city_state = [];
        if (!empty($this->city)) {
            $city_state[] = $this->city;
        }
        if (!empty($this->state)) {
            $city_state[] = $this->state;
        }
        if (!empty($city_state)) {
            $address_lines[] = implode(', ', $city_state);
        }
        
        if (!empty($this->country)) {
            $address_lines[] = $this->country;
        }
        
        if (!empty($this->zip_code)) {
            $address_lines[] = $this->zip_code;
        }
        
        return implode('<br>', $address_lines);
    }

    /**
     * Return list of leads dropdown for a business
     *
     * @param $business_id int
     * @param $prepend_none = true (boolean)
     * @return array leads
     */
    public static function leadsDropdown($business_id, $prepend_none = true)
    {
        $leads = Lead::where('business_id', $business_id)
            ->select('id', 'store_name', 'city', 'state')
            ->get()
            ->map(function ($lead) {
                $location = '';
                if (!empty($lead->city) || !empty($lead->state)) {
                    $location_parts = array_filter([$lead->city, $lead->state]);
                    $location = ' (' . implode(', ', $location_parts) . ')';
                }
                return [
                    'id' => $lead->id,
                    'name' => $lead->store_name . $location
                ];
            });

        $leads_dropdown = $leads->pluck('name', 'id');

        //Prepend none
        if ($prepend_none) {
            $leads_dropdown = $leads_dropdown->prepend(__('lang_v1.none'), '');
        }

        return $leads_dropdown;
    }

    /**
     * Get the lead's full contact information.
     *
     * @return string
     */
    public function getContactInfoAttribute()
    {
        $info = [];
        
        if (!empty($this->contact_name)) {
            $info[] = $this->contact_name;
        }
        
        if (!empty($this->contact_phone)) {
            $info[] = $this->contact_phone;
        }
        
        if (!empty($this->contact_email)) {
            $info[] = $this->contact_email;
        }
        
        return implode(' | ', $info);
    }

    /**
     * Get the lead's display name with company.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        $name = $this->store_name;
        
        if (!empty($this->company_name)) {
            $name .= ' (' . $this->company_name . ')';
        }
        
        return $name;
    }

    /**
     * Check if the lead is overdue for follow-up.
     *
     * @return bool
     */
    public function isOverdueForFollowUp()
    {
        return $this->next_follow_up_date && $this->next_follow_up_date < now();
    }

    /**
     * Check if the lead is due for follow-up today.
     *
     * @return bool
     */
    public function isDueForFollowUpToday()
    {
        return $this->next_follow_up_date && $this->next_follow_up_date->isToday();
    }

    /**
     * Get the lead's priority color for UI display.
     *
     * @return string
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'urgent' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray'
        };
    }

    /**
     * Get the lead's status color for UI display.
     *
     * @return string
     */
    public function getStatusColorAttribute()
    {
        return match($this->lead_status) {
            'new' => 'blue',
            'in_progress' => 'yellow',
            'follow_up' => 'orange',
            'converted' => 'green',
            'lost' => 'red',
            'qualified' => 'purple',
            'unqualified' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get the lead's location coordinates as an array.
     *
     * @return array|null
     */
    public function getLocationAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
                'accuracy' => $this->location_accuracy
            ];
        }
        
        return null;
    }

    /**
     * Get the lead's estimated value formatted.
     *
     * @return string
     */
    public function getFormattedEstimatedValueAttribute()
    {
        if (!$this->estimated_value) {
            return 'N/A';
        }
        
        return $this->currency . ' ' . number_format($this->estimated_value, 2);
    }

    /**
     * Get the lead's actual value formatted.
     *
     * @return string
     */
    public function getFormattedActualValueAttribute()
    {
        if (!$this->actual_value) {
            return 'N/A';
        }
        
        return $this->currency . ' ' . number_format($this->actual_value, 2);
    }

    /**
     * Get the lead's tags as a comma-separated string.
     *
     * @return string
     */
    public function getTagsStringAttribute()
    {
        if (!$this->tags || !is_array($this->tags)) {
            return '';
        }
        
        return implode(', ', $this->tags);
    }

    /**
     * Check if the lead has been converted.
     *
     * @return bool
     */
    public function isConverted()
    {
        return $this->lead_status === 'converted' && !empty($this->converted_at);
    }

    /**
     * Check if the lead is lost.
     *
     * @return bool
     */
    public function isLost()
    {
        return $this->lead_status === 'lost';
    }

    /**
     * Check if the lead is active (not converted or lost).
     *
     * @return bool
     */
    public function isActive()
    {
        return !in_array($this->lead_status, ['converted', 'lost']);
    }
}