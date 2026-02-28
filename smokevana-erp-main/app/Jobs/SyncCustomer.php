<?php

namespace App\Jobs;

use App\Contact;
use App\Utils\Util;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncCustomer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $nextPage;
    public function __construct($nextPage = 'https://ad4.phantasm.solutions/api/sync-user')
    {
        $this->nextPage = $nextPage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::get($this->nextPage);
        $data = $response->json();
        $allCustomers = $data['users']['data'] ?? [];
        foreach ($allCustomers as $customer) {
            $syncCustomer = [];
            $syncCustomer['username'] = $customer['user_login'] ?? '';
            $syncCustomer['customer_email'] = $customer['user_email'] ?? '';
            $syncCustomer['password'] = $customer['user_pass'] ?? '';
            $syncCustomer['customer_id'] = $customer['ID'] ?? '';
            $syncCustomer['user_registered'] = $customer['user_registered'] ?? '';
            $ref_count = (new Util())->setAndGetReferenceCount('contacts',1);
            $syncCustomer['contact_id'] = (new Util())->generateReferenceNumber('contacts', $ref_count, 1);
            $customerData = $customer['meta'] ?? [];

            foreach ($customerData as $meta) {
                $metaKey = $meta['meta_key'] ?? '';
                $metaValue = $meta['meta_value'] ?? '';

                switch ($metaKey) {
                    case 'first_name':
                        $syncCustomer['firstName'] = $metaValue;
                        break;
                    case 'last_name':
                        $syncCustomer['lastName'] = $metaValue;
                        break;
                    case 'billing_address_1':
                        $syncCustomer['billingAddress'] = $metaValue;
                        break;
                    case 'billing_address_2':
                        $syncCustomer['billingAddress2'] = $metaValue;
                        break;
                    case 'billing_city':
                        $syncCustomer['billingCity'] = $metaValue;
                        break;
                    case 'billing_postcode':
                        $syncCustomer['billingPostcode'] = $metaValue;
                        break;
                    case 'billing_phone':
                        $syncCustomer['billingPhone'] = $metaValue;
                        break;
                    case 'billing_email':
                        $syncCustomer['billingEmail'] = $metaValue;
                        break;
                    case 'billing_country':
                        $syncCustomer['billingCountry'] = $metaValue;
                        break;
                    case 'billing_state':
                        $syncCustomer['billingState'] = $metaValue;
                        break;
                    case 'billing_first_name':
                        $syncCustomer['billingFirstName'] = $metaValue;
                        break;
                    case 'billing_last_name':
                        $syncCustomer['billingLastName'] = $metaValue;
                        break;
                    case 'billing_company':
                        $syncCustomer['billingCompany'] = $metaValue;
                        break;
                    case 'shipping_address_1':
                        $syncCustomer['shippingAddress1'] = $metaValue;
                        break;
                    case 'shipping_address_2':
                        $syncCustomer['shippingAddress2'] = $metaValue;
                        break;
                    case 'shipping_city':
                        $syncCustomer['shippingCity'] = $metaValue;
                        break;
                    case 'shipping_postcode':
                        $syncCustomer['shippingPostcode'] = $metaValue;
                        break;
                    case 'shipping_country':
                        $syncCustomer['shippingCountry'] = $metaValue;
                        break;
                    case 'shipping_state':
                        $syncCustomer['shippingState'] = $metaValue;
                        break;
                    case 'shipping_first_name':
                        $syncCustomer['shippingFirstName'] = $metaValue;
                        break;
                    case 'shipping_last_name':
                        $syncCustomer['shippingLastName'] = $metaValue;
                        break;
                    case 'shipping_company':
                        $syncCustomer['shippingCompany'] = $metaValue;
                        break;
                    case 'orders':
                        $syncCustomer['orders'] = $metaValue;
                        break;
                    case 'wp_capabilities':
                        $user_capabilities = unserialize($metaValue) ?? [];
                        if (isset($user_capabilities['wholesale_customer'])) {
                            $syncCustomer['priceGroupID'] = 1;
                        } elseif (isset($user_capabilities['mm_price_2'])) {
                            $syncCustomer['priceGroupID'] = 2;
                        } elseif (isset($user_capabilities['mm_price_3'])) {
                            $syncCustomer['priceGroupID'] = 3;
                        } elseif (isset($user_capabilities['mm_price_4'])) {
                            $syncCustomer['priceGroupID'] = 4;
                        } else {
                            $syncCustomer['priceGroupID'] = null;
                        }
                        break;
                    case 'ur_user_status':
                        if ($metaValue == '0') {
                            $syncCustomer['isApproved'] = null;
                        } elseif ($metaValue == '2') {
                            $syncCustomer['isApproved'] = false;
                        } else {
                            $syncCustomer['isApproved'] = true;
                        }
                        break;
                }
            }

            // create or update customer
            if (isset($syncCustomer['isApproved']) && $syncCustomer['isApproved'] === false) {
                continue;
            }
            $erpData = [
                "business_id" => 1,
                "type" => "customer",
                "contact_type" => "business",
                "supplier_business_name" => $syncCustomer['billingCompany'] ?? null,
                "name" => ($syncCustomer['firstName'] ?? '') . ' ' . ($syncCustomer['lastName'] ?? ''),
                "prefix" => "Mr",
                "first_name" => $syncCustomer['firstName'] ?? '',
                "middle_name" => null,
                "last_name" => $syncCustomer['lastName'] ?? '',
                "email" => $syncCustomer['customer_email'] ?? '',
                "contact_id" => $syncCustomer['contact_id'] ?? '',
                "contact_status" => isset($syncCustomer['isApproved']) && $syncCustomer['isApproved'] ? "active" : "inactive",
                "tax_number" => null,
                "city" => $syncCustomer['billingCity'] ?? '',
                "state" => $syncCustomer['billingState'] ?? '',
                "country" => $syncCustomer['billingCountry'] ?? '',
                "address_line_1" => $syncCustomer['billingAddress'] ?? '',
                "address_line_2" => $syncCustomer['billingAddress2'] ?? '',
                "zip_code" => $syncCustomer['billingPostcode'] ?? '',
                "dob" => null,
                "mobile" => $syncCustomer['billingPhone'] ?? '',
                "landline" => null,
                "alternate_number" => null,
                "pay_term_number" => null,
                "pay_term_type" => null,
                "credit_limit" => null,
                "created_by" => "1",
                "total_rp" => "0",
                "total_rp_used" => "0",
                "total_rp_expired" => "0",
                "is_default" => "0",
                "shipping_address" => ($syncCustomer['shippingAddress1'] ?? '') . ' ' . ($syncCustomer['shippingAddress2'] ?? '') . ' ' . ($syncCustomer['shippingCity'] ?? '') . ' ' . ($syncCustomer['shippingState'] ?? '') . ' ' . ($syncCustomer['shippingPostcode'] ?? '') . ' ' . ($syncCustomer['shippingCountry'] ?? ''),
                "shipping_custom_field_details" => null,
                "is_export" => "0",
                "position" => null,
                "customer_group_id" => $syncCustomer['priceGroupID'] ?? null,
                "custom_field1" => null,
                "deleted_at" => null,
                "created_at" => $syncCustomer['user_registered'] ?? now(),
                "updated_at" => $syncCustomer['user_registered'] ?? now(),
                "password" => $syncCustomer['password'] ?? '',
                "isApproved" => isset($syncCustomer['isApproved']) && $syncCustomer['isApproved'] ? $syncCustomer['isApproved'] : null,
                "remember_token" => null,
                "role" => null,
                "fcmToken" => null,
                "usermeta" => null,
                "customer_u_name" => $syncCustomer['username'] ?? '',
                "shipping_first_name" => $syncCustomer['shippingFirstName'] ?? $syncCustomer['firstName'] ?? '',
                "shipping_last_name" => $syncCustomer['shippingLastName'] ?? $syncCustomer['lastName'] ?? '',
                "shipping_company" => $syncCustomer['shippingCompany'] ?? $syncCustomer['billingCompany'] ?? '',
                "shipping_address1" => $syncCustomer['shippingAddress1'] ?? $syncCustomer['billingAddress'] ?? '',
                "shipping_address2" => $syncCustomer['shippingAddress2'] ?? $syncCustomer['billingAddress2'] ?? '',
                "shipping_city" => $syncCustomer['shippingCity'] ?? $syncCustomer['billingCity'] ?? '',
                "shipping_state" => $syncCustomer['shippingState'] ?? $syncCustomer['billingState'] ?? '',
                "shipping_zip" => $syncCustomer['shippingPostcode'] ?? $syncCustomer['billingPostcode'] ?? '',
                "shipping_country" => $syncCustomer['shippingCountry'] ?? $syncCustomer['billingCountry'] ?? ''
            ];
            $customer = Contact::where('email', $syncCustomer['customer_email'])->first();
            if ($customer) {
                $customer->update($erpData);
            } else {
                $customer = Contact::create($erpData);
            }
        }
        // ask for next page
        $nextPage = $data['users']['next_page_url'] ?? null;
        if ($nextPage) {
            // SyncCustomer::dispatch($nextPage);
            Log::info('Syncing '.$nextPage.' page of customers');
        } else {
            Log::info('No more Customer to sync');
        }
    }
}
