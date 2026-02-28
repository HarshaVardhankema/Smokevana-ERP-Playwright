<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class BusinessRegister extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create default admin user FIRST with business_id = null (circular dependency: user needs business, business needs user)
        // We'll update business_id after business is created
        // Only insert if user doesn't exist
        if (DB::table('users')->where('id', 1)->doesntExist()) {
            $password = Hash::make('123456');
            DB::table('users')->insert([
                'id' => 1,
                'user_type' => 'user',
                'surname' => 'Mr',
                'first_name' => 'Phantasm',
                'last_name' => 'Solutions',
                'username' => 'admin',
                'email' => 'utkarsh@phantasm.co.in',
                'password' => $password,
                'language' => 'en',
                'contact_no' => null,
                'address' => null,
                'remember_token' => null,
                'business_id' => null, // Will update after business is created
                'available_at' => null,
                'paused_at' => null,
                'max_sales_discount_percent' => 1,
                'allow_login' => 1,
                'is_online' => 0,
                'status' => 'active',
                'is_enable_service_staff_pin' => 0,
                'service_staff_pin' => null,
                'fcmToken' => null,
                'crm_contact_id' => null,
                'is_cmmsn_agnt' => 0,
                'cmmsn_percent' => 0.00,
                'max_discount_percent' => null,
                'selected_contacts' => false,
                'dob' => null,
                'gender' => null,
                'marital_status' => null,
                'blood_group' => null,
                'contact_number' => null,
                'alt_number' => null,
                'family_number' => null,
                'fb_link' => null,
                'twitter_link' => null,
                'social_media_1' => null,
                'social_media_2' => null,
                'permanent_address' => null,
                'current_address' => null,
                'guardian_name' => null,
                'custom_field_1' => null,
                'custom_field_2' => null,
                'custom_field_3' => null,
                'custom_field_4' => null,
                'bank_details' => null,
                'id_proof_name' => null,
                'id_proof_number' => null,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'quarterly_bonus_amount' => null,
                'quarterly_sales_target' => null,
                'yearly_bonus_amount' => null,
                'yearly_sales_target' => null,
            ]);
        }

        // Insert barcode templates for label printing (only if table is empty)
        if (DB::table('barcodes')->count() == 0) {
            DB::table('barcodes')->insert([
                [
                    'id' => 1,
                    'name' => '20 Labels per Sheet',
                    'description' => 'Sheet Size: 8.5" x 11", Label Size: 4" x 1", Labels per sheet: 20',
                    'width' => 4.0000,
                    'height' => 1.0000,
                    'paper_width' => 8.5000,
                    'paper_height' => 11.0000,
                    'top_margin' => 0.5000,
                    'left_margin' => 0.1250,
                    'row_distance' => 0.0000,
                    'col_distance' => 0.1875,
                    'stickers_in_one_row' => 2,
                    'is_default' => 0,
                    'is_continuous' => 0,
                    'stickers_in_one_sheet' => 20,
                    'business_id' => null,
                    'created_at' => '2017-12-18 00:43:44',
                    'updated_at' => '2017-12-18 00:43:44',
                ],
                [
                    'id' => 2,
                    'name' => '30 Labels per sheet',
                    'description' => 'Sheet Size: 8.5" x 11", Label Size: 2.625" x 1", Labels per sheet: 30',
                    'width' => 2.6250,
                    'height' => 1.0000,
                    'paper_width' => 8.5000,
                    'paper_height' => 11.0000,
                    'top_margin' => 0.5000,
                    'left_margin' => 0.1880,
                    'row_distance' => 0.0000,
                    'col_distance' => 0.1250,
                    'stickers_in_one_row' => 3,
                    'is_default' => 0,
                    'is_continuous' => 0,
                    'stickers_in_one_sheet' => 30,
                    'business_id' => null,
                    'created_at' => '2017-12-18 00:34:39',
                    'updated_at' => '2017-12-18 00:40:40',
                ],
                [
                    'id' => 3,
                    'name' => '32 Labels per sheet',
                    'description' => 'Sheet Size: 8.5" x 11", Label Size: 2" x 1.25", Labels per sheet: 32',
                    'width' => 2.0000,
                    'height' => 1.2500,
                    'paper_width' => 8.5000,
                    'paper_height' => 11.0000,
                    'top_margin' => 0.5000,
                    'left_margin' => 0.2500,
                    'row_distance' => 0.0000,
                    'col_distance' => 0.0000,
                    'stickers_in_one_row' => 4,
                    'is_default' => 0,
                    'is_continuous' => 0,
                    'stickers_in_one_sheet' => 32,
                    'business_id' => null,
                    'created_at' => '2017-12-18 00:25:40',
                    'updated_at' => '2017-12-18 00:25:40',
                ],
                [
                    'id' => 4,
                    'name' => '40 Labels per sheet',
                    'description' => 'Sheet Size: 8.5" x 11", Label Size: 2" x 1", Labels per sheet: 40',
                    'width' => 2.0000,
                    'height' => 1.0000,
                    'paper_width' => 8.5000,
                    'paper_height' => 11.0000,
                    'top_margin' => 0.5000,
                    'left_margin' => 0.2500,
                    'row_distance' => 0.0000,
                    'col_distance' => 0.0000,
                    'stickers_in_one_row' => 4,
                    'is_default' => 0,
                    'is_continuous' => 0,
                    'stickers_in_one_sheet' => 40,
                    'business_id' => null,
                    'created_at' => '2017-12-18 00:28:40',
                    'updated_at' => '2017-12-18 00:28:40',
                ],
                [
                    'id' => 5,
                    'name' => '50 Labels per Sheet',
                    'description' => 'Sheet Size: 8.5" x 11", Label Size: 1.5" x 1", Labels per sheet: 50',
                    'width' => 1.5000,
                    'height' => 1.0000,
                    'paper_width' => 8.5000,
                    'paper_height' => 11.0000,
                    'top_margin' => 0.5000,
                    'left_margin' => 0.5000,
                    'row_distance' => 0.0000,
                    'col_distance' => 0.0000,
                    'stickers_in_one_row' => 5,
                    'is_default' => 0,
                    'is_continuous' => 0,
                    'stickers_in_one_sheet' => 50,
                    'business_id' => null,
                    'created_at' => '2017-12-18 00:21:10',
                    'updated_at' => '2017-12-18 00:21:10',
                ],
                [
                    'id' => 6,
                    'name' => 'Continuous Rolls - 31.75mm x 25.4mm',
                    'description' => 'Label Size: 31.75mm x 25.4mm, Gap: 3.18mm',
                    'width' => 1.2500,
                    'height' => 1.0000,
                    'paper_width' => 1.2500,
                    'paper_height' => 0.0000,
                    'top_margin' => 0.1250,
                    'left_margin' => 0.0000,
                    'row_distance' => 0.1250,
                    'col_distance' => 0.0000,
                    'stickers_in_one_row' => 1,
                    'is_default' => 0,
                    'is_continuous' => 1,
                    'stickers_in_one_sheet' => null,
                    'business_id' => null,
                    'created_at' => '2017-12-18 00:21:10',
                    'updated_at' => '2017-12-18 00:21:10',
                ],
            ]);
        }


        // Insert default business record (only if it doesn't exist)
        if (DB::table('business')->where('id', 1)->doesntExist()) {
            DB::table('business')->insert([
                'id' => 1,
                'name' => 'ZeperGo',
                'currency_id' => 2,
                'start_date' => '2025-11-20',
                'tax_number_1' => null,
                'tax_label_1' => null,
                'tax_number_2' => null,
                'tax_label_2' => null,
                'code_label_1' => null,
                'code_1' => null,
                'code_label_2' => null,
                'code_2' => null,
                'default_sales_tax' => null,
                'default_profit_percent' => 25.00,
                'owner_id' => 1,
                'time_zone' => 'America/Chicago',
                'fy_start_month' => 1,
                'accounting_method' => 'fifo',
                'default_sales_discount' => null,
                'sell_price_tax' => 'includes',
                'logo' => null,
                'sku_prefix' => null,
                'enable_product_expiry' => 0,
                'expiry_type' => 'add_expiry',
                'on_product_expiry' => 'keep_selling',
                'stop_selling_before' => 0,
                'enable_tooltip' => 1,
                'purchase_in_diff_currency' => 0,
                'purchase_currency_id' => null,
                'p_exchange_rate' => 1.000,
                'transaction_edit_days' => 30,
                'stock_expiry_alert_days' => 30,
                'keyboard_shortcuts' => '{"pos":{"express_checkout":"shift+e","pay_n_ckeckout":"shift+p","draft":"shift+d","cancel":"shift+c","edit_discount":"shift+i","edit_order_tax":"shift+t","add_payment_row":"shift+r","finalize_payment":"shift+f","recent_product_quantity":"f2","add_new_product":"f4"}}',
                'pos_settings' => null,
                'weighing_scale_setting' => '',
                'woocommerce_api_settings' => null,
                'woocommerce_skipped_orders' => null,
                'woocommerce_wh_oc_secret' => null,
                'woocommerce_wh_ou_secret' => null,
                'woocommerce_wh_od_secret' => null,
                'woocommerce_wh_general_secret' => null,
                'woocommerce_wh_or_secret' => null,
                'enable_brand' => 1,
                'enable_category' => 1,
                'enable_sub_category' => 1,
                'enable_price_tax' => 1,
                'enable_purchase_status' => 1,
                'enable_lot_number' => 0,
                'default_unit' => null,
                'enable_sub_units' => 0,
                'enable_racks' => 0,
                'enable_row' => 0,
                'enable_position' => 0,
                'enable_editing_product_from_purchase' => 1,
                'sales_cmsn_agnt' => null,
                'item_addition_method' => 1,
                'enable_inline_tax' => 0,
                'currency_symbol_placement' => 'before',
                'enabled_modules' => '["purchases","add_sale","pos_sale","stock_transfers","stock_adjustment","expenses"]',
                'date_format' => 'm/d/Y',
                'time_format' => '24',
                'currency_precision' => 2,
                'quantity_precision' => 2,
                'ref_no_prefixes' => '{"purchase":"PO","stock_transfer":"ST","stock_adjustment":"SA","sell_return":"CN","expense":"EP","contacts":"CO","purchase_payment":"PP","sell_payment":"SP","business_location":"BL"}',
                'theme_color' => null,
                'created_by' => null,
                'enable_rp' => 0,
                'rp_name' => null,
                'amount_for_unit_rp' => 1.0000,
                'min_order_total_for_rp' => 1.0000,
                'max_rp_per_order' => null,
                'redeem_amount_per_unit_rp' => 1.0000,
                'min_order_total_for_redeem' => 1.0000,
                'min_redeem_point' => null,
                'max_redeem_point' => null,
                'rp_expiry_period' => null,
                'rp_expiry_type' => 'year',
                'email_settings' => null,
                'sms_settings' => null,
                'custom_labels' => null,
                'common_settings' => null,
                'is_active' => 1,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
                'templateData' => null,
                'manage_order_module' => 'manual',
                'overselling_qty_limit' => null,
            ]);
        }

        // Update user's business_id now that business exists (fix circular dependency)
        DB::table('users')->where('id', 1)->update(['business_id' => 1]);

        // Insert into invoice layouts FIRST (required for business_locations foreign key)
        if (DB::table('invoice_layouts')->count() == 0) {
            DB::table('invoice_layouts')->insert([
                'id' => 1,
                'name' => 'Default',
                'header_text' => null,
                'invoice_no_prefix' => 'Invoice No.',
                'quotation_no_prefix' => null,
                'invoice_heading' => 'Invoice',
                'sub_heading_line1' => null,
                'sub_heading_line2' => null,
                'sub_heading_line3' => null,
                'sub_heading_line4' => null,
                'sub_heading_line5' => null,
                'invoice_heading_not_paid' => '',
                'invoice_heading_paid' => '',
                'quotation_heading' => null,
                'sub_total_label' => 'Subtotal',
                'discount_label' => 'Discount',
                'tax_label' => 'Tax',
                'total_label' => 'Total',
                'round_off_label' => null,
                'total_due_label' => 'Total Due',
                'paid_label' => 'Total Paid',
                'show_client_id' => 0,
                'client_id_label' => null,
                'client_tax_label' => null,
                'date_label' => 'Date',
                'date_time_format' => null,
                'show_time' => 1,
                'show_brand' => 0,
                'show_sku' => 1,
                'show_cat_code' => 1,
                'show_expiry' => 0,
                'show_lot' => 0,
                'show_image' => 0,
                'show_sale_description' => 0,
                'sales_person_label' => null,
                'show_sales_person' => 0,
                'table_product_label' => 'Product',
                'table_qty_label' => 'Quantity',
                'table_unit_price_label' => 'Unit Price',
                'table_subtotal_label' => 'Subtotal',
                'cat_code_label' => null,
                'logo' => null,
                'show_logo' => 0,
                'show_business_name' => 0,
                'show_location_name' => 1,
                'show_landmark' => 1,
                'show_city' => 1,
                'show_state' => 1,
                'show_zip_code' => 1,
                'show_country' => 1,
                'show_mobile_number' => 1,
                'show_alternate_number' => 0,
                'show_email' => 0,
                'show_tax_1' => 1,
                'show_tax_2' => 0,
                'show_barcode' => 0,
                'show_payments' => 1,
                'show_customer' => 1,
                'customer_label' => 'Customer',
                'commission_agent_label' => null,
                'show_commission_agent' => 0,
                'show_reward_point' => 0,
                'highlight_color' => '#000000',
                'footer_text' => '',
                'module_info' => null,
                'common_settings' => null,
                'is_default' => 1,
                'business_id' => 1,
                'show_letter_head' => 0,
                'letter_head' => null,
                'show_qr_code' => 0,
                'qr_code_fields' => null,
                'design' => 'classic',
                'cn_heading' => null,
                'cn_no_label' => null,
                'cn_amount_label' => null,
                'table_tax_headings' => null,
                'show_previous_bal' => 0,
                'prev_bal_label' => null,
                'change_return_label' => null,
                'product_custom_fields' => null,
                'contact_custom_fields' => null,
                'location_custom_fields' => null,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ]);
        }

        // Insert into invoice schemes SECOND (required for business_locations foreign key)
        if (DB::table('invoice_schemes')->count() == 0) {
            DB::table('invoice_schemes')->insert([
                'id' => 1,
                'business_id' => 1,
                'name' => 'Default',
                'scheme_type' => 'blank',
                'number_type' => 'sequential',
                'prefix' => '',
                'start_number' => 1,
                'invoice_count' => 0,
                'total_digits' => 4,
                'is_default' => 1,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ]);
        }

        // Insert default business location (only if it doesn't exist)
        if (DB::table('business_locations')->where('id', 1)->doesntExist()) {
            DB::table('business_locations')->insert([
                'id' => 1,
                'business_id' => 1,
                'location_id' => 'BL00001',
                'name' => 'ZeperGo B2B',
                'is_b2c' => 0,
                'landmark' => 'Industrial Drive',
                'country' => 'US',
                'state' => 'IL',
                'city' => 'Chicago',
                'zip_code' => '60656',
                'invoice_scheme_id' => 1,
                'sale_invoice_scheme_id' => null,
                'invoice_layout_id' => 1,
                'sale_invoice_layout_id' => 1,
                'selling_price_group_id' => null,
                'print_receipt_on_invoice' => 1,
                'receipt_printer_type' => 'browser',
                'printer_id' => null,
                'mobile' => '9876543210',
                'alternate_number' => '',
                'email' => '',
                'website' => '',
                'featured_products' => null,
                'is_active' => 1,
                'default_payment_accounts' => '{"cash":{"is_enabled":1,"account":null},"card":{"is_enabled":1,"account":null},"cheque":{"is_enabled":1,"account":null},"bank_transfer":{"is_enabled":1,"account":null},"other":{"is_enabled":1,"account":null},"custom_pay_1":{"is_enabled":1,"account":null},"custom_pay_2":{"is_enabled":1,"account":null},"custom_pay_3":{"is_enabled":1,"account":null},"custom_pay_4":{"is_enabled":1,"account":null},"custom_pay_5":{"is_enabled":1,"account":null},"custom_pay_6":{"is_enabled":1,"account":null},"custom_pay_7":{"is_enabled":1,"account":null}}',
                'custom_field1' => null,
                'custom_field2' => null,
                'custom_field3' => null,
                'custom_field4' => null,
                'deleted_at' => null,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ]);
        }

        $activity_table = config('activitylog.table_name', 'activity_logs');
        if (DB::table($activity_table)->where('id', 1)->doesntExist()) {
            DB::table($activity_table)->insert([
                'id' => 1,
                'log_name' => 'default',
                'description' => 'login',
                'subject_id' => 1,
                'subject_type' => 'App\\User',
                'event' => null,
                'business_id' => 1,
                'causer_id' => 1,
                'causer_type' => 'App\\User',
                'properties' => '[]',
                'batch_uuid' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Insert default walk-in customer contact (only if it doesn't exist)
        if (DB::table('contacts')->where('id', 1)->doesntExist()) {
            DB::table('contacts')->insert([
                'id' => 1,
                'business_id' => 1,
                'location_id' => null,
                'brand_id' => null,
                'type' => 'customer',
                'contact_type' => null,
                'supplier_business_name' => null,
                'name' => 'Walk-In Customer',
                'prefix' => null,
                'first_name' => null,
                'middle_name' => null,
                'last_name' => null,
                'email' => null,
                'contact_id' => 'CO00001',
                'contact_status' => 'active',
                'tax_number' => null,
                'city' => null,
                'state' => null,
                'country' => null,
                'address_line_1' => null,
                'address_line_2' => null,
                'zip_code' => null,
                'dob' => null,
                'mobile' => '',
                'landline' => null,
                'alternate_number' => null,
                'pay_term_number' => null,
                'pay_term_type' => null,
                'credit_limit' => 0.0000,
                'transaction_limit' => null,
                'is_auto_send_due_notification' => 0,
                'created_by' => 1,
                'balance' => 0.0000,
                'total_rp' => 0,
                'total_rp_used' => 0,
                'total_rp_expired' => 0,
                'is_default' => 1,
                'shipping_address' => null,
                'shipping_custom_field_details' => null,
                'is_export' => 0,
                'export_custom_field_1' => null,
                'export_custom_field_2' => null,
                'export_custom_field_3' => null,
                'export_custom_field_4' => null,
                'export_custom_field_5' => null,
                'export_custom_field_6' => null,
                'position' => null,
                'customer_group_id' => null,
                'custom_field1' => null,
                'custom_field2' => null,
                'custom_field3' => null,
                'custom_field4' => null,
                'custom_field5' => null,
                'custom_field6' => null,
                'custom_field7' => null,
                'custom_field8' => null,
                'custom_field9' => null,
                'custom_field10' => null,
                'deleted_at' => null,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
                'password' => null,
                'isApproved' => '0',
                'remember_token' => null,
                'role' => null,
                'fcmToken' => null,
                'usermeta' => null,
                'customer_u_name' => null,
                'shipping_first_name' => null,
                'shipping_last_name' => null,
                'shipping_company' => null,
                'shipping_address1' => null,
                'shipping_address2' => null,
                'shipping_city' => null,
                'shipping_state' => null,
                'shipping_zip' => null,
                'shipping_country' => null,
                'is_createdby_commission_agent' => null,
            ]);
        }

        // Insert into roles FIRST (required for model_has_roles foreign key)
        if (DB::table('roles')->count() == 0) {
            DB::table('roles')->insert([
                [
                    'id' => 1,
                    'name' => 'Admin#1',
                    'guard_name' => 'web',
                    'business_id' => 1,
                    'is_default' => 1,
                    'is_service_staff' => 0,
                    'created_at' => '2025-11-20 08:39:44',
                    'updated_at' => '2025-11-20 08:39:44',
                ],
                [
                    'id' => 2,
                    'name' => 'Cashier#1',
                    'guard_name' => 'web',
                    'business_id' => 1,
                    'is_default' => 0,
                    'is_service_staff' => 0,
                    'created_at' => '2025-11-20 08:39:44',
                    'updated_at' => '2025-11-20 08:39:44',
                ],
            ]);
        }

        // Assign admin role to user (only if it doesn't exist)
        if (DB::table('model_has_roles')->where('model_id', 1)->where('role_id', 1)->doesntExist()) {
            DB::table('model_has_roles')->insert([
                'role_id' => 1,
                'model_type' => 'App\\User',
                'model_id' => 1,
            ]);
        }

        // Insert into reference counts if table is empty
        if (DB::table('reference_counts')->count() == 0) {
            DB::table('reference_counts')->insert([
                [
                    'id' => 1,
                    'ref_type' => 'transaction_payment_groups_count',
                    'ref_count' => 1,
                    'business_id' => 1,
                    'created_at' => '2025-08-09 00:12:19',
                    'updated_at' => '2025-08-09 00:12:19',
                ],
                [
                    'id' => 2,
                    'ref_type' => 'contacts',
                    'ref_count' => 1,
                    'business_id' => 1,
                    'created_at' => '2025-11-20 08:39:44',
                    'updated_at' => '2025-11-20 08:39:44',
                ],
                [
                    'id' => 3,
                    'ref_type' => 'business_location',
                    'ref_count' => 1,
                    'business_id' => 1,
                    'created_at' => '2025-11-20 08:39:44',
                    'updated_at' => '2025-11-20 08:39:44',
                ],
            ]);
        }

        // Create or update role_has_permissions
        DB::table('role_has_permissions')->insertOrIgnore([
            ['permission_id' => 30, 'role_id' => 2],
            ['permission_id' => 31, 'role_id' => 2],
            ['permission_id' => 53, 'role_id' => 2],
            ['permission_id' => 54, 'role_id' => 2],
            ['permission_id' => 55, 'role_id' => 2],
            ['permission_id' => 56, 'role_id' => 2],
            ['permission_id' => 85, 'role_id' => 2],
        ]);

        // Create or update the units model if it doesn't exist
        if (DB::table('units')->where('id', 1)->count() == 0) {
            DB::table('units')->insert([
                'id' => 1,
                'business_id' => 1,
                'actual_name' => 'Pieces',
                'short_name' => 'Pc(s)',
                'allow_decimal' => 0,
                'base_unit_id' => null,
                'base_unit_multiplier' => null,
                'created_by' => 1,
                'deleted_at' => null,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ]);
        }



        // Seed the default notification templates using Eloquent to ensure validation and flexibility.
        // This creates or updates each template as needed.
        $notificationTemplates = 
        [
            [
                'id' => 1,
                'business_id' => 1,
                'template_for' => 'new_sale',
                'email_body' => '<p>Dear {contact_name},</p>

                    <p>Your invoice number is {invoice_number}<br />
                    Total amount: {total_amount}<br />
                    Paid amount: {received_amount}</p>

                    <p>Thank you for shopping with us.</p>

                    <p>{business_logo}</p>

                    <p>&nbsp;</p>',
                'sms_body' => 'Dear {contact_name}, Thank you for shopping with us. {business_name}',
                'whatsapp_text' => null,
                'subject' => 'Thank you from {business_name}',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 0,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 2,
                'business_id' => 1,
                'template_for' => 'payment_received',
                'email_body' => '<p>Dear {contact_name},</p>

                <p>We have received a payment of {received_amount}</p>

                <p>{business_logo}</p>',
                'sms_body' => 'Dear {contact_name}, We have received a payment of {received_amount}. {business_name}',
                'whatsapp_text' => null,
                'subject' => 'Payment Received, from {business_name}',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 0,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 3,
                'business_id' => 1,
                'template_for' => 'payment_reminder',
                'email_body' => '<p>Dear {contact_name},</p>

                    <p>This is to remind you that you have pending payment of {due_amount}. Kindly pay it as soon as possible.</p>

                    <p>{business_logo}</p>',
                'sms_body' => 'Dear {contact_name}, You have pending payment of {due_amount}. Kindly pay it as soon as possible. {business_name}',
                'whatsapp_text' => null,
                'subject' => 'Payment Reminder, from {business_name}',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 0,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 4,
                'business_id' => 1,
                'template_for' => 'new_booking',
                'email_body' => '<p>Dear {contact_name},</p>

                    <p>Your booking is confirmed</p>

                    <p>Date: {start_time} to {end_time}</p>

                    <p>Table: {table}</p>

                    <p>Location: {location}</p>

                    <p>{business_logo}</p>',
                'sms_body' => 'Dear {contact_name}, Your booking is confirmed. Date: {start_time} to {end_time}, Table: {table}, Location: {location}',
                'whatsapp_text' => null,
                'subject' => 'Booking Confirmed - {business_name}',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 0,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 5,
                'business_id' => 1,
                'template_for' => 'new_order',
                'email_body' => '<p>Dear {contact_name},</p>

                    <p>We have a new order with reference number {order_ref_number}. Kindly process the products as soon as possible.</p>

                    <p>{business_name}<br />
                    {business_logo}</p>',
                'sms_body' => 'Dear {contact_name}, We have a new order with reference number {order_ref_number}. Kindly process the products as soon as possible. {business_name}',
                'whatsapp_text' => null,
                'subject' => 'New Order, from {business_name}',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 0,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 6,
                'business_id' => 1,
                'template_for' => 'payment_paid',
                'email_body' => '<p>Dear {contact_name},</p>

                    <p>We have paid amount {paid_amount} again invoice number {order_ref_number}.<br />
                    Kindly note it down.</p>

                    <p>{business_name}<br />
                    {business_logo}</p>',
                'sms_body' => 'We have paid amount {paid_amount} again invoice number {order_ref_number}.
                    Kindly note it down. {business_name}',
                'whatsapp_text' => null,
                'subject' => 'Payment Paid, from {business_name}',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 0,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 7,
                'business_id' => 1,
                'template_for' => 'items_received',
                'email_body' => '<p>Dear {contact_name},</p>

                    <p>We have received all items from invoice reference number {order_ref_number}. Thank you for processing it.</p>

                    <p>{business_name}<br />
                    {business_logo}</p>',
                'sms_body' => 'We have received all items from invoice reference number {order_ref_number}. Thank you for processing it. {business_name}',
                'whatsapp_text' => null,
                'subject' => 'Items received, from {business_name}',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 0,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 8,
                'business_id' => 1,
                'template_for' => 'items_pending',
                'email_body' => '<p>Dear {contact_name},<br />
                    This is to remind you that we have not yet received some items from invoice reference number {order_ref_number}. Please process it as soon as possible.</p>

                    <p>{business_name}<br />
                    {business_logo}</p>',
                'sms_body' => 'This is to remind you that we have not yet received some items from invoice reference number {order_ref_number} . Please process it as soon as possible.{business_name}',
                'whatsapp_text' => null,
                'subject' => 'Items Pending, from {business_name}',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 0,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 9,
                'business_id' => 1,
                'template_for' => 'new_quotation',
                'email_body' => '<p>Dear {contact_name},</p>

                    <p>Your quotation number is {invoice_number}<br />
                    Total amount: {total_amount}</p>

                    <p>Thank you for shopping with us.</p>

                    <p>{business_logo}</p>

                    <p>&nbsp;</p>',
                'sms_body' => 'Dear {contact_name}, Thank you for shopping with us. {business_name}',
                'whatsapp_text' => null,
                'subject' => 'Thank you from {business_name}',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 0,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 10,
                'business_id' => 1,
                'template_for' => 'purchase_order',
                'email_body' => '<p>Dear {contact_name},</p>

                    <p>We have a new purchase order with reference number {order_ref_number}. The respective invoice is attached here with.</p>

                    <p>{business_logo}</p>',
                'sms_body' => 'We have a new purchase order with reference number {order_ref_number}. {business_name}',
                'whatsapp_text' => null,
                'subject' => 'New Purchase Order, from {business_name}',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 0,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 11,
                'business_id' => 1,
                'template_for' => 'contact_us_success',
                'email_body' => '<table style="padding: 20px; background-color: #f4f4f4;" border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td align="center">
<table style="background-color: #ffffff; border-radius: 6px;" border="0" width="600" cellspacing="0" cellpadding="0">
<tbody>
<tr style="background-color: #004aad;">
<td style="padding: 20px; text-align: center;"><img style="max-width: 150px; height: auto;" src="{business_logo}" alt="{business_name}" /></td>
</tr>
<tr>
<td style="padding: 30px; color: #333;">
<h2 style="color: #004aad; font-size: 24px; margin: 0 0 20px;">Thanks for Contacting {business_name}!</h2>
<p style="font-size: 16px; line-height: 1.5; margin: 0 0 15px;">Hello {contact_name},</p>
<p style="font-size: 16px; line-height: 1.5; margin: 0 0 15px;">We have received your message. Your reference number is <strong>{ref_no}</strong>.</p>
<p style="font-size: 16px; line-height: 1.5; margin: 0 0 15px;">We will get back to you shortly. Meanwhile, you can visit our website: <a style="color: #004aad; text-decoration: none;" href="{url_business}">{url_business}</a></p>
<p style="font-size: 16px; line-height: 1.5; margin: 0;">Best regards,<br />The {business_name} Team</p>
</td>
</tr>
<tr>
<td style="background-color: #eeeeee; padding: 20px; text-align: center; font-size: 14px; color: #777;">&copy; {business_name} | All rights reserved.</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>',
                'sms_body' => 'Hi {contact_name}, thank you for contacting {business_name}. Your ref no. is {ref_no}. We will get back to you soon. Visit: {url_business}',
                'whatsapp_text' => 'Hello {contact_name}, 👋

Thank you for reaching out to *{business_name}*!  
We have received your message. Your reference number is *{ref_no}*.

We will get back to you soon. Meanwhile, feel free to visit: {url_business}

Best regards,  
{business_name}',
                'subject' => 'Thankyou for contacting  {business_name}',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 1,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 12,
                'business_id' => 1,
                'template_for' => 'registration_confirmation',
                'email_body' => '<table style="padding: 20px; background-color: #f4f4f4;" border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td align="center">
<table style="background-color: #ffffff; border-radius: 6px;" border="0" width="600" cellspacing="0" cellpadding="0">
<tbody>
<tr style="background-color: #004aad;">
<td style="padding: 20px; text-align: center;"><img style="max-width: 150px; height: auto;" src="{business_logo}" alt="{business_name}" /></td>
</tr>
<tr>
<td style="padding: 30px; color: #333;">
<h2 style="color: #004aad; font-size: 24px; margin: 0 0 20px;">Registration Received</h2>
<p style="font-size: 16px; line-height: 1.5; margin: 0 0 15px;">Hello {contact_name},</p>
<p style="font-size: 16px; line-height: 1.5; margin: 0 0 15px;">Thank you for registering with <strong>{business_name}</strong>. Your registration has been successfully submitted.</p>
<p style="font-size: 16px; line-height: 1.5; margin: 0 0 15px;">We will notify you once your registration is approved.</p>
<p style="font-size: 16px; line-height: 1.5; margin: 0 0 15px;">For more information, visit our website: <a style="color: #004aad; text-decoration: none;" href="{url_business}">{url_business}</a></p>
<p style="font-size: 16px; line-height: 1.5; margin: 0;">Regards,<br />The {business_name} Team</p>
</td>
</tr>
<tr>
<td style="background-color: #eeeeee; padding: 20px; text-align: center; font-size: 14px; color: #777;">&copy; {business_name} | All rights reserved.</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>',
                'sms_body' => 'Hi {contact_name}, your registration with {business_name} was successful. We’ll notify you once it’s approved. Visit: {url_business}',
                'whatsapp_text' => 'Hello {contact_name}, 👋

Thank you for registering with *{business_name}*!  
Your registration was successful. ✅  
We will notify you once it’s approved.

For more info, visit: {url_business}

Best regards,  
{business_name}
',
                'subject' => 'Registration Sucessfull',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 1,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 13,
                'business_id' => 1,
                'template_for' => 'subscribe_newsletter',
                'email_body' => '<table style="padding: 20px; background-color: #f4f4f4;" border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td align="center">
<table style="background-color: #ffffff; border-radius: 6px;" border="0" width="600" cellspacing="0" cellpadding="0">
<tbody>
<tr style="background-color: #004aad;">
<td style="padding: 20px; text-align: center;"><img style="max-width: 150px; height: auto;" src="{business_logo}" alt="{business_name}" /></td>
</tr>
<tr>
<td style="padding: 30px; color: #333;">
<h2 style="color: #004aad; font-size: 24px; margin: 0 0 20px;">You are Subscribed!</h2>
<p style="font-size: 16px; line-height: 1.5; margin: 0 0 15px;">Thank you for subscribing to <strong>{business_name}</strong> newsletter!</p>
<p style="font-size: 16px; line-height: 1.5; margin: 0 0 15px;">You will now receive the latest news, updates, and special offers directly to your inbox.</p>
<p style="font-size: 16px; line-height: 1.5; margin: 0 0 15px;">You can also visit our website for more updates: <a style="color: #004aad; text-decoration: none;" href="{url_business}">{url_business}</a></p>
<p style="font-size: 16px; line-height: 1.5; margin: 0;">Cheers, <br />The {business_name} Team</p>
</td>
</tr>
<tr>
<td style="background-color: #eeeeee; padding: 20px; text-align: center; font-size: 14px; color: #777;">&copy; {business_name} | All rights reserved.</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>',
                'sms_body' => '',
                'whatsapp_text' => '',
                'subject' => 'Thank you for subscribing to <strong>{business_name}</strong> newsletter!',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 1,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 14,
                'business_id' => 1,
                'template_for' => 'password_reset_success',
                'email_body' => ' <table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding:20px; background-color:#f4f4f4;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff; border-radius:6px;">
          <tr style="background-color:#004aad;">
            <td style="padding:20px; text-align:center;">
              <img src="{business_logo}" alt="{business_name}" style="max-width:150px; height:auto;">
            </td>
          </tr>
          <tr>
            <td style="padding:30px; color:#333;">
              <h2 style="color:#004aad; font-size:24px; margin:0 0 20px;">Password Reset Successful</h2>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                Hello {contact_name},
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                Your password has been successfully reset for your {business_name} account.
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                If you did not request this change, please contact our support team immediately.
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                You can now log in at: 
                <a href="{url_business}" style="color:#004aad; text-decoration:none;">{url_business}</a>
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0;">
                Regards, <br>
                The {business_name} Team
              </p>
            </td>
          </tr>
          <tr>
            <td style="background-color:#eeeeee; padding:20px; text-align:center; font-size:14px; color:#777;">
              &copy; {business_name} | All rights reserved.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>',
                'sms_body' => 'Hi {contact_name}, your password has been successfully reset for your {business_name} account. Log in at: {url_business}
',
                'whatsapp_text' => '',
                'subject' => 'Your password has been successfully reset for your {business_name} account.',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 1,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 15,
                'business_id' => 1,
                'template_for' => 'forget_password',
                'email_body' => ' <table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding:20px; background-color:#f4f4f4;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff; border-radius:6px;">
          <tr style="background-color:#004aad;">
            <td style="padding:20px; text-align:center;">
              <img src="{business_logo}" alt="{business_name}" style="max-width:150px; height:auto;">
            </td>
          </tr>
          <tr>
            <td style="padding:30px; color:#333;">
              <h2 style="color:#004aad; font-size:24px; margin:0 0 20px;">Forgot Your Password?</h2>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                Hello {contact_name},
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                We received a request to reset your password for your {business_name} account.
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                Your password reset code is: <strong>{otp}</strong>. It expires in 15 minutes.
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                Enter this code on the password reset page to set a new password.
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0;">
                If you did not request a password reset, you may ignore this message.
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0;">
                Regards, <br>
                The {business_name} Team
              </p>
            </td>
          </tr>
          <tr>
            <td style="background-color:#eeeeee; padding:20px; text-align:center; font-size:14px; color:#777;">
              &copy; {business_name} | All rights reserved.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>',
                'sms_body' => 'Hello {contact_name}, your {business_name} password reset code is: {otp}. It expires in 15 minutes.',
                'whatsapp_text' => '',
                'subject' => 'Password Reset Request',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 1,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 16,
                'business_id' => 1,
                'template_for' => 'contact_us_send',
                'email_body' => ' <table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding:20px; background-color:#f4f4f4;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff; border-radius:6px;">
          <tr style="background-color:#004aad;">
            <td style="padding:20px; text-align:center;">
              <img src="{business_logo}" alt="{business_name}" style="max-width:150px; height:auto;">
            </td>
          </tr>
          <tr>
            <td style="padding:30px; color:#333;">
              <h2 style="color:#004aad; font-size:24px; margin:0 0 20px;">Thank You for Reaching Out</h2>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                We appreciate you taking the time to contact <strong>{business_name}</strong>.
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                Your message has been successfully received, and a member of our team will get back to you as soon as possible. We strive to respond promptly and provide you with the assistance you need.
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0;">
                Thank you for choosing <strong>{business_name}</strong>. We look forward to assisting you.
              </p>
            </td>
          </tr>
          <tr>
            <td style="background-color:#eeeeee; padding:20px; text-align:center; font-size:14px; color:#777;">
              &copy; {business_name} | All rights reserved.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>',
                'sms_body' => '',
                'whatsapp_text' => '',
                'subject' => ' Thank you for contacting Us',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 1,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 17,
                'business_id' => 1,
                'template_for' => 'send_payment_notification',
                'email_body' => ' <table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding:20px; background-color:#f4f4f4;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff; border-radius:6px;">
          <tr style="background-color:#004aad;">
            <td style="padding:20px; text-align:center;">
              <img src="{business_logo}" alt="{business_name}" style="max-width:150px; height:auto;">
            </td>
          </tr>
          <tr>
            <td style="padding:30px; color:#333;">
              <h2 style="color:#004aad; font-size:24px; margin:0 0 20px;">Payment Request</h2>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                Dear {contact_name},
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                You have a pending payment of <strong>{payment_amount}</strong> with <strong>{business_name}</strong>.
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                To complete the transaction, please click the link below:
              </p>
              <p style="text-align:center; margin:20px 0;">
                <a href="{payment_link}" style="background-color:#004aad; color:#ffffff; padding:12px 20px; border-radius:4px; text-decoration:none; font-size:16px;">
                  Pay Now
                </a>
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0;">
                Thank you for your prompt attention.
                <br><br>
                Best regards, <br>
                The {business_name} Team
              </p>
            </td>
          </tr>
          <tr>
            <td style="background-color:#eeeeee; padding:20px; text-align:center; font-size:14px; color:#777;">
              &copy; {business_name} | All rights reserved.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>',
                'sms_body' => 'Hi {contact_name}, you have a pending payment of {payment_amount} with {business_name}. Please complete it here: {payment_link}
',
                'whatsapp_text' => 'Hello {contact_name}, 👋

You have a pending payment of *{payment_amount}* with *{business_name}*.  
Please complete it using the link below:  
{payment_link}

Thank you,  
{business_name}
',
                'subject' => 'Payment Request',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 1,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 18,
                'business_id' => 1,
                'template_for' => 'shipment',
                'email_body' => '<body style="margin:0; padding:0; background-color:#f9f9f9; font-family:Arial, sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding:20px;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff; border-radius:6px;">
          <tr style="background-color:#004aad;">
            <td style="padding:20px; text-align:center;">
              <img src="{business_logo}" alt="{business_name}" style="max-width:150px; height:auto;">
            </td>
          </tr>
          <tr>
            <td style="padding:30px; color:#333;">
              <h2 style="color:#004aad; font-size:24px; margin:0 0 20px;">Shipment Dispatched</h2>
              <p style="font-size:16px; margin:0 0 10px;">
                Dear Customer,
              </p>
              <p style="font-size:16px; margin:0 0 15px;">
                Your shipment <strong>#{shipment_number}</strong> has been dispatched via <strong>{carrier_name}</strong>.
              </p>
              <p style="font-size:16px; margin:0 0 15px;">
                Track your shipment here: <a href="{tracking_url}" style="color:#004aad;">Track shipment</a>
              </p>
              <p style="font-size:16px; margin:0;">
                Thank you for choosing <strong>{business_name}</strong>.
              </p>
            </td>
          </tr>
          <tr>
            <td style="background-color:#eeeeee; padding:20px; text-align:center; font-size:14px; color:#777;">
              &copy; {business_name} | All rights reserved.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>',
                'sms_body' => 'Hi! Shipment #{shipment_number} dispatched via {carrier_name}. Track: {tracking_url}',
                'whatsapp_text' => 'Hello 👋

Your shipment #{shipment_number} has been dispatched via {carrier_name}.
Track here: {tracking_url}

Thank you for choosing {business_name}!',
                'subject' => 'Shipment Dispatched',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 1,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 19,
                'business_id' => 1,
                'template_for' => 'local_pickup',
                'email_body' => '<table style="padding: 20px;" border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td align="center">
<table style="background-color: #ffffff; border-radius: 6px;" border="0" width="600" cellspacing="0" cellpadding="0">
<tbody>
<tr style="background-color: #004aad;">
<td style="padding: 20px; text-align: center;"><img style="max-width: 150px; height: auto;" src="{business_logo}" alt="{business_name}" /></td>
</tr>
<tr>
<td style="padding: 30px; color: #333;">
<h2 style="color: #004aad; font-size: 24px; margin: 0 0 20px;">Your Order is Ready for Pickup</h2>
<p style="font-size: 16px; margin: 0 0 15px;">Dear Customer,</p>
<p style="font-size: 16px; margin: 0 0 15px;">We&rsquo;re pleased to inform you that your order associated with invoice <strong>#{invoice_number}</strong> is now ready for pickup.</p>
<p style="font-size: 16px; margin: 0 0 15px;">You can view or download your invoice using the link below:</p>
<p style="text-align: center; margin: 20px 0;"><a style="background-color: #004aad; color: #ffffff; padding: 12px 20px; border-radius: 4px; text-decoration: none; font-size: 16px;" href="{invoice_url}"> View Invoice </a></p>
<p style="font-size: 16px; margin: 0;">Thank you for shopping with <strong>{business_name}</strong>. We look forward to seeing you!</p>
</td>
</tr>
<tr>
<td style="background-color: #eeeeee; padding: 20px; text-align: center; font-size: 14px; color: #777;">&copy; {business_name} | All rights reserved.</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>',
                'sms_body' => 'Hi! Your order (Invoice #{invoice_number}) is ready for pickup at {business_name}. View your invoice: {invoice_url}',
                'whatsapp_text' => 'Hello 👋

Your order with *{business_name}* is ready for pickup!  
🧾 *Invoice:* #{invoice_number}  
📄 View Invoice: {invoice_url}

Thank you for choosing *{business_name}*!
',
                'subject' => 'Your Order is Ready for Pickup',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 1,
                'auto_send_sms' => 0,
                'auto_send_wa_notif' => 0,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
            [
                'id' => 20,
                'business_id' => 1,
                'template_for' => 'test_notification',
                'email_body' => ' <table width="100%" cellpadding="0" cellspacing="0" border="0" style="padding:20px; background-color:#f4f4f4;">
                    <tr>
                        <td align="center">
                            <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff; border-radius:6px;">
                                <tr style="background-color:#004aad;">
                                    <td style="padding:20px; text-align:center;">
                                        <img src="{business_logo}" alt="{business_name}" style="max-width:150px; height:auto;">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:30px; color:#333;">
                                        <h2 style="color:#004aad; font-size:24px; margin:0 0 20px;">Test Notification</h2>
                                        <p style="font-size:16px; line:0 0 15px;">
                                            This is a test notification.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>',
                'sms_body' => 'This is a test notification.',
                'whatsapp_text' => 'This is a test notification.',
                'subject' => 'Test Notification',
                'cc' => null,
                'bcc' => null,
                'auto_send' => 1,
                'auto_send_sms' => 1,
                'auto_send_wa_notif' => 1,
                'created_at' => '2025-11-20 08:39:44',
                'updated_at' => '2025-11-20 08:39:44',
            ],
        ];

        foreach ($notificationTemplates as $template) {
            // Use updateOrInsert for idempotence, so running the seeder multiple times won't duplicate.
            DB::table('notification_templates')->updateOrInsert(
                ['email_body' => $template['email_body']],
                $template
            );
        }
    }
}
