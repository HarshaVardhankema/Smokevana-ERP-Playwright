<?php

namespace App\Exports;

use App\Contact;
use Maatwebsite\Excel\Concerns\FromArray;

class ContactsExport implements FromArray
{
    protected $location_id;
    protected $business_id;
    protected $contact_type;

    public function __construct($location_id, $business_id, $contact_type = 'all')
    {
        $this->location_id = $location_id;
        $this->business_id = $business_id;
        $this->contact_type = $contact_type;
    }

    public function array(): array
    {
        $query = Contact::where('business_id', $this->business_id);
        
        // Filter by contact type
        if ($this->contact_type == 'customer') {
            $query->whereIn('type', ['customer', 'both']);
        } elseif ($this->contact_type == 'supplier') {
            $query->whereIn('type', ['supplier', 'both']);
        }
        
        if (!empty($this->location_id)) {
            $query->where('location_id', $this->location_id);
        }
        
        $contacts = $query->with(['brand'])->get();

        // Headers matching import format - 39 columns (added brand_id)
        $contacts_array = [[
            'Contact Type', 'Prefix', 'First Name', 'Middle Name', 'Last Name', 
            'Business Name', 'Contact ID', 'Tax No', 'Opening Balance', 
            'Pay Term', 'Pay Term Period', 'Credit Limit', 'Email', 'Mobile', 
            'Alternate Contact Number', 'Landline', 'City', 'State', 'Country', 
            'Address Line 1', 'Address Line 2', 'Zip Code', 'DOB', 
            'Password', 'Is Approved', 'Customer Username', 
            'Shipping First Name', 'Shipping Last Name', 'Shipping Company', 
            'Shipping Address 1', 'Shipping Address 2', 'Shipping City', 
            'Shipping State', 'Shipping Zip', 'Shipping Country', 
            'Contact Status', 'Customer Group', 'Location ID', 'Brand ID'
        ]];

        foreach ($contacts as $contact) {
            $contact_arr = [
                $contact->type ?? '',                                    // 0: Contact Type
                $contact->prefix ?? '',                                  // 1: Prefix
                $contact->first_name ?? '',                              // 2: First Name
                $contact->middle_name ?? '',                             // 3: Middle Name
                $contact->last_name ?? '',                               // 4: Last Name
                $contact->supplier_business_name ?? '',                  // 5: Business Name
                $contact->contact_id ?? '',                             // 6: Contact ID
                $contact->tax_number ?? '',                             // 7: Tax No
                '',                                                      // 8: Opening Balance (not stored directly)
                $contact->pay_term_number ?? '',                        // 9: Pay Term
                $contact->pay_term_type ?? '',                          // 10: Pay Term Period
                $contact->credit_limit ?? '',                           // 11: Credit Limit
                $contact->email ?? '',                                  // 12: Email
                $contact->mobile ?? '',                                 // 13: Mobile
                $contact->alternate_number ?? '',                       // 14: Alternate Contact Number
                $contact->landline ?? '',                               // 15: Landline
                $contact->city ?? '',                                   // 16: City
                $contact->state ?? '',                                  // 17: State
                $contact->country ?? '',                                // 18: Country
                $contact->address_line_1 ?? '',                         // 19: Address Line 1
                $contact->address_line_2 ?? '',                         // 20: Address Line 2
                $contact->zip_code ?? '',                               // 21: Zip Code
                $contact->dob ?? '',                                    // 22: DOB
                $contact->password ?? '',                               // 23: Password
                $contact->is_approved ?? '',                            // 24: Is Approved
                $contact->customer_u_name ?? '',                        // 25: Customer Username
                $contact->shipping_first_name ?? '',                    // 26: Shipping First Name
                $contact->shipping_last_name ?? '',                     // 27: Shipping Last Name
                $contact->shipping_company ?? '',                       // 28: Shipping Company
                $contact->shipping_address1 ?? '',                      // 29: Shipping Address 1
                $contact->shipping_address2 ?? '',                      // 30: Shipping Address 2
                $contact->shipping_city ?? '',                          // 31: Shipping City
                $contact->shipping_state ?? '',                         // 32: Shipping State
                $contact->shipping_zip ?? '',                           // 33: Shipping Zip
                $contact->shipping_country ?? '',                        // 34: Shipping Country
                $contact->contact_status ?? '',                         // 35: Contact Status
                $contact->customer_group_id ?? '',                      // 36: Customer Group
                $contact->location_id ?? '',                            // 37: Location ID
                $contact->brand_id ?? '',                              // 38: Brand ID
            ];

            $contacts_array[] = $contact_arr;
        }

        return $contacts_array;
    }
}

