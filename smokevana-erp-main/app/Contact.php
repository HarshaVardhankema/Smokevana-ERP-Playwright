<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Contact extends Authenticatable
{
    use Notifiable, HasApiTokens;
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
        'shipping_custom_field_details' => 'array',
    ];
    protected $hidden = [
        // 'password',
        // 'remember_token',
    ];
    
    /**
     * Get the business that owns the user.
     */
    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    /**
     * Get the business location that owns the contact.
     */
    public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class);
    }

    /**
     * Get the brand that owns the contact.
     */
    public function brand()
    {
        return $this->belongsTo(\App\Brands::class);
    }

    /**
     * Get the customer group that owns the contact.
     */
    public function customerGroup()
    {
        return $this->belongsTo(\App\CustomerGroup::class);
    }

    /**
     * Get all addresses for the contact.
     */
    public function addresses()
    {
        return $this->hasMany(\App\CustomerAddress::class, 'contact_id');
    }

    /**
     * Get all credit applications for the contact.
     */
    public function creditApplications()
    {
        return $this->hasMany(\App\Models\CreditApplication::class, 'contact_id');
    }

    /**
     * Get the latest credit application for the contact.
     */
    public function latestCreditApplication()
    {
        return $this->hasOne(\App\Models\CreditApplication::class, 'contact_id')->latest();
    }

    public function scopeActive($query)
    {
        return $query->where('contacts.contact_status', 'active');
    }

    /**
     * Scope a query to only include contacts for a specific location.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $location_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForLocation($query, $location_id)
    {
        return $query->where('contacts.location_id', $location_id);
    }

    /**
     * Scope a query to only include contacts for a specific brand.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $brand_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForBrand($query, $brand_id)
    {
        return $query->where('contacts.brand_id', $brand_id);
    }

    /**
     * Scope a query to only include contacts for a specific location and brand.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $location_id
     * @param  int  $brand_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForLocationAndBrand($query, $location_id, $brand_id)
    {
        return $query->where('contacts.location_id', $location_id)
                    ->where('contacts.brand_id', $brand_id);
    }

    //custom
    public function getPriceTierAttribute()
    {
        $customerGroupId = $this->customer_group_id;

        if ($customerGroupId) {
            $customerGroup = CustomerGroup::where('id', $customerGroupId)->first(['selling_price_group_id', 'name']);
            $priceGroupId = $customerGroup->selling_price_group_id ?? null;

            if ($priceGroupId) {
                $priceGroup = SellingPriceGroup::where('id', $priceGroupId)
                ->where('is_active', 1)
                ->select('id', 'name')
                ->first();
                if ($priceGroup) {
                    return [
                        $priceGroup->id => $priceGroup->name
                    ];
                }
            }
            // Customer group has no selling price group: use group name for display (e.g. "Sliver Customers") instead of "default_sell_price"
            $displayName = $customerGroup->name ?? 'default_sell_price';
            return [
                0 => $displayName
            ];
        }

        return [
            0 => 'default_sell_price'
        ]; 
    }


    /**
     * Filters only own created suppliers or has access to the supplier
     */
    public function scopeOnlySuppliers($query)
    {
        if (auth()->check() && ! auth()->user()->can('supplier.view') && ! auth()->user()->can('supplier.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        $query->whereIn('contacts.type', ['supplier', 'both']);

        if (auth()->check() && ! auth()->user()->can('supplier.view') && auth()->user()->can('supplier.view_own')) {
            $query->leftjoin('user_contact_access AS ucas', 'contacts.id', 'ucas.contact_id');
            $query->where(function ($q) {
                $user_id = auth()->user()->id;
                $q->where('contacts.created_by', $user_id)
                    ->orWhere('ucas.user_id', $user_id);
            });
        }

        return $query;
    }

    /**
     * Filters only own created customers or has access to the customer
     */
    public function scopeOnlyCustomers($query)
    {
        //Commented because of issue in woocommerce sync
        // if (auth()->check() && !auth()->user()->can('customer.view') && !auth()->user()->can('customer.view_own')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $query->whereIn('contacts.type', ['customer', 'both']);

        if (auth()->check() && ! auth()->user()->can('customer.view') && auth()->user()->can('customer.view_own')) {
            $query->leftjoin('user_contact_access AS ucas', 'contacts.id', 'ucas.contact_id');
            $query->where(function ($q) {
                $user_id = auth()->user()->id;
                $q->where('contacts.created_by', $user_id)
                    ->orWhere('ucas.user_id', $user_id);
            });
        }

        return $query;
    }

    /**
     * Filters only own created contact or has access to the contact
     */
    public function scopeOnlyOwnContact($query)
    {
        $query->leftjoin('user_contact_access AS ucas', 'contacts.id', 'ucas.contact_id');
        $query->where(function ($q) {
            $user_id = auth()->user()->id;
            $q->where('contacts.created_by', $user_id)
                ->orWhere('ucas.user_id', $user_id);
        });

        return $query;
    }

    /**
     * Get all of the contacts's notes & documents.
     */
    public function documentsAndnote()
    {
        return $this->morphMany(\App\DocumentAndNote::class, 'notable');
    }

    /**
     * Return list of contact dropdown for a business
     *
     * @param $business_id int
     * @param $exclude_default = false (boolean)
     * @param $prepend_none = true (boolean)
     * @return array users
     */
    public static function contactDropdown($business_id, $exclude_default = false, $prepend_none = true, $append_id = true ,$isViewContact=null, $location_id = null)
    {
        // Auto-detect location if not provided
        if (empty($location_id) && auth()->check()) {
            $location_id = self::getAutoDetectedLocation($business_id);
        }
        
        if($isViewContact){
            $query = Contact::where('business_id', $business_id)
            ->where('type', '!=', 'lead')
            ->active();
            
            // Filter by location if provided (null means show all contacts for super admins)
            if (!empty($location_id)) {
                $query->where('location_id', $location_id);
            }
    
        if ($exclude_default) {
            $query->where('is_default', 0);
        }
    
        $query->select(
            'contacts.id',
            'contacts.name',
            'contacts.type',
            'contacts.contact_id',
            'contacts.supplier_business_name'
        );
    
        if (auth()->check() && ! auth()->user()->can('supplier.view') && auth()->user()->can('supplier.view_own')) {
            $query->leftJoin('user_contact_access AS ucas', 'contacts.id', 'ucas.contact_id');
            $query->where(function ($q) {
                $user_id = auth()->user()->id;
                $q->where('contacts.created_by', $user_id)
                  ->orWhere('ucas.user_id', $user_id);
            });
        }
    
        $contacts = $query->get()->map(function ($contact) {
            return [
                'id' => $contact->id,
                'name' => $contact->contact_id 
                    ? trim("{$contact->name} - {$contact->supplier_business_name} ({$contact->contact_id})")
                    : $contact->name,
                'type' => $contact->type,
            ];
        })->values();
    
        return $contacts;
        }else{
            $query = Contact::where('business_id', $business_id)
            ->where('type', '!=', 'lead')
            ->active();
            
            // Filter by location if provided (null means show all contacts for super admins)
            if (!empty($location_id)) {
                $query->where('location_id', $location_id);
            }

        if ($exclude_default) {
            $query->where('is_default', 0);
        }

        if ($append_id) {
            $query->select(
                DB::raw("IF(contacts.contact_id IS NULL OR contacts.contact_id='', name, CONCAT(name, ' - ', COALESCE(supplier_business_name, ''), '(', contacts.contact_id, ')')) AS supplier"),
                'contacts.id'
            );
        } else {
            $query->select(
                'contacts.id',
                DB::raw("IF (supplier_business_name IS not null, CONCAT(name, ' (', supplier_business_name, ')'), name) as supplier")
            );
        }

        if (auth()->check() && ! auth()->user()->can('supplier.view') && auth()->user()->can('supplier.view_own')) {
            $query->leftjoin('user_contact_access AS ucas', 'contacts.id', 'ucas.contact_id');
            $query->where(function ($q) {
                $user_id = auth()->user()->id;
                $q->where('contacts.created_by', $user_id)
                    ->orWhere('ucas.user_id', $user_id);
            });
        }

        $contacts = $query->pluck('supplier', 'contacts.id');

        //Prepend none
        if ($prepend_none) {
            $contacts = $contacts->prepend(__('lang_v1.none'), '');
        }

        return $contacts;
        }
        
    }

    /**
     * Return list of suppliers dropdown for a business
     *
     * @param $business_id int
     * @param $prepend_none = true (boolean)
     * @return array users
     */
    public static function suppliersDropdown($business_id, $prepend_none = true, $append_id = true, $location_id = null)
    {
        // Auto-detect location if not provided
        if (empty($location_id) && auth()->check()) {
            $location_id = self::getAutoDetectedLocation($business_id);
        }
        
        $all_contacts = Contact::where('contacts.business_id', $business_id)
            ->whereIn('contacts.type', ['supplier', 'both'])
            ->active();
            
        // Filter by location if provided (null means show all contacts for super admins)
        if (!empty($location_id)) {
            $all_contacts->where('contacts.location_id', $location_id);
        }

        if ($append_id) {
            $all_contacts->select(
                DB::raw("IF(contacts.contact_id IS NULL OR contacts.contact_id='', name, CONCAT(contacts.name, ' - ', COALESCE(contacts.supplier_business_name, ''), '(', contacts.contact_id, ')')) AS supplier"),
                'contacts.id'
            );
        } else {
            $all_contacts->select(
                'contacts.id',
                DB::raw("CONCAT(contacts.name, ' (', contacts.supplier_business_name, ')') as supplier")
            );
        }

        if (auth()->check() && ! auth()->user()->can('supplier.view') && auth()->user()->can('supplier.view_own')) {
            $all_contacts->onlyOwnContact();
        }

        $suppliers = $all_contacts->pluck('supplier', 'id');

        //Prepend none
        if ($prepend_none) {
            $suppliers = $suppliers->prepend(__('lang_v1.none'), '');
        }

        return $suppliers;
    }

    /**
     * Return list of customers dropdown for a business
     *
     * @param $business_id int
     * @param $prepend_none = true (boolean)
     * @return array users
     */
    public static function customersDropdown($business_id, $prepend_none = true, $append_id = true, $location_id = null)
    {
        // Auto-detect location if not provided
        if (empty($location_id) && auth()->check()) {
            $location_id = self::getAutoDetectedLocation($business_id);
        }
        
        $all_contacts = Contact::where('contacts.business_id', $business_id)
            ->whereIn('contacts.type', ['customer', 'both'])
            ->active();
            
        // Filter by location if provided (null means show all contacts for super admins)
        if (!empty($location_id)) {
            $all_contacts->where('contacts.location_id', $location_id);
        }

        if ($append_id) {
            $all_contacts->select(
                DB::raw("IF(contacts.contact_id IS NULL OR contacts.contact_id='', CONCAT( COALESCE(contacts.supplier_business_name, ''), ' - ', contacts.name), CONCAT(COALESCE(contacts.supplier_business_name, ''), ' - ', name, ' (', contacts.contact_id, ')')) AS customer"),
                'contacts.id'
            );
        } else {
            $all_contacts->select('contacts.id', DB::raw('contacts.name as customer'));
        }

        if (auth()->check() && ! auth()->user()->can('customer.view') && auth()->user()->can('customer.view_own')) {
            $all_contacts->onlyOwnContact();
        }

        $customers = $all_contacts->pluck('customer', 'id');

        //Prepend none
        if ($prepend_none) {
            $customers = $customers->prepend(__('lang_v1.none'), '');
        }

        return $customers;
    }

    /**
     * Return list of contact type.
     *
     * @param $prepend_all = false (boolean)
     * @return array
     */
    public static function typeDropdown($prepend_all = false)
    {
        $types = [];

        if ($prepend_all) {
            $types[''] = __('lang_v1.all');
        }

        $types['customer'] = __('report.customer');
        $types['supplier'] = __('report.supplier');
        $types['both'] = __('lang_v1.both_supplier_customer');

        return $types;
    }

    /**
     * Return list of contact type by permissions.
     *
     * @return array
     */
    public static function getContactTypes()
    {
        $types = [];
        if (auth()->check() && auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->check() && auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->check() && auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }

        return $types;
    }

    public function getContactAddressAttribute()
    {
        $address_array = [];
        if (! empty($this->supplier_business_name)) {
            $address_array[] = $this->supplier_business_name;
        }
        if (! empty($this->name)) {
            $address_array[] = ! empty($this->supplier_business_name) ? '<br>' . $this->name : $this->name;
        }
        if (! empty($this->address_line_1)) {
            $address_array[] = '<br>' . $this->address_line_1;
        }
        if (! empty($this->address_line_2)) {
            $address_array[] = '<br>' . $this->address_line_2;
        }
        if (! empty($this->city)) {
            $address_array[] = '<br>' . $this->city;
        }
        if (! empty($this->state)) {
            $address_array[] = $this->state;
        }
        if (! empty($this->country)) {
            $address_array[] = $this->country;
        }

        $address = '';
        if (! empty($address_array)) {
            $address = implode(', ', $address_array);
        }
        if (! empty($this->zip_code)) {
            $address .= ',<br>' . $this->zip_code;
        }

        return $address;
    }

    public function getFullNameAttribute()
    {
        $name_array = [];
        if (! empty($this->prefix)) {
            $name_array[] = $this->prefix;
        }
        if (! empty($this->first_name)) {
            $name_array[] = $this->first_name;
        }
        if (! empty($this->middle_name)) {
            $name_array[] = $this->middle_name;
        }
        if (! empty($this->last_name)) {
            $name_array[] = $this->last_name;
        }

        return implode(' ', $name_array);
    }

    public function getFullNameWithBusinessAttribute()
    {
        $name_array = [];
        if (! empty($this->prefix)) {
            $name_array[] = $this->prefix;
        }
        if (! empty($this->first_name)) {
            $name_array[] = $this->first_name;
        }
        if (! empty($this->middle_name)) {
            $name_array[] = $this->middle_name;
        }
        if (! empty($this->last_name)) {
            $name_array[] = $this->last_name;
        }

        $full_name = implode(' ', $name_array);
        $business_name = ! empty($this->supplier_business_name) ? $this->supplier_business_name . ', ' : '';

        return $business_name . $full_name;
    }

    public function getContactAddressArrayAttribute()
    {
        $address_array = [];
        if (! empty($this->address_line_1)) {
            $address_array[] = $this->address_line_1;
        }
        if (! empty($this->address_line_2)) {
            $address_array[] = $this->address_line_2;
        }
        if (! empty($this->city)) {
            $address_array[] = $this->city;
        }
        if (! empty($this->state)) {
            $address_array[] = $this->state;
        }
        if (! empty($this->country)) {
            $address_array[] = $this->country;
        }
        if (! empty($this->zip_code)) {
            $address_array[] = $this->zip_code;
        }

        return $address_array;
    }

    /**
     * All user who have access to this contact
     * Applied only when selected_contacts is true for a user in
     * users table
     */
    public function userHavingAccess()
    {
        return $this->belongsToMany(\App\User::class, 'user_contact_access');
    }

    public function invoices(){
        return $this->hasMany(Transaction::class, 'contact_id', 'id')
                    ->select(['id', 'invoice_no', 'type', 'status', 'payment_status', 'contact_id','transaction_date','shipping_address','shipment']);
    }

    public function haveSelesRep(){
        return $this->belongsToMany(\App\User::class, 'user_contact_access', 'contact_id', 'user_id');
    }

    /**
     * Auto-detect location for the current user
     *
     * @param int $business_id
     * @return int|null
     */
    public static function getAutoDetectedLocation($business_id)
    {
        if (!auth()->check()) {
            return null;
        }

        $user = auth()->user();
        
        // Check if user is super admin or has access to all locations
        $is_super_admin = $user->can('access_all_locations') || $user->can('admin');
        
        if ($is_super_admin) {
            // Super admin can see all contacts, return null to not filter by location
            return null;
        }
        
        $permitted_locations = $user->permitted_locations($business_id);
        
        if ($permitted_locations == 'all') {
            // User has access to all locations, get first available location
            $default_location = BusinessLocation::where('business_id', $business_id)
                ->where('is_active', 1)
                ->first();
            return $default_location ? $default_location->id : null;
        } elseif (is_array($permitted_locations) && !empty($permitted_locations)) {
            // User has specific location permissions, use the first one
            return $permitted_locations[0];
        }
        
        return null;
    }
}
