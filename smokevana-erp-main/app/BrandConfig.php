<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BrandConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'brand_config';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_settings' => 'array',
        'template_settings' => 'array',
    ];

    /**
     * Get the brand that owns the config.
     */
    public function brand()
    {
        return $this->belongsTo(Brands::class, 'brand_id');
    }

    /**
     * Get email settings for a brand
     *
     * @param  int  $brand_id
     * @return array|null
     */
    public static function getEmailSettings($brand_id)
    {
        $config = self::where('brand_id', $brand_id)->first();
        
        return $config ? $config->email_settings : null;
    }

    /**
     * Get template for a specific template type
     *
     * @param  int  $brand_id
     * @param  string  $template_type
     * @return array|null
     */
    public static function getTemplate($brand_id, $template_type)
    {
        $config = self::where('brand_id', $brand_id)->first();
        
        if (!$config || !$config->template_settings) {
            return null;
        }

        $templates = $config->template_settings;
        foreach ($templates as $template) {
            if (isset($template['template_type']) && $template['template_type'] === $template_type) {
                return [
                    'subject' => $template['subject'] ?? '',
                    'template_body' => $template['template_body'] ?? '',
                    'cc' => $template['cc'] ?? '',
                    'bcc' => $template['bcc'] ?? '',
                ];
            }
        }
        
        return null;
    }

    /**
     * Get notification template types for brand emails
     * Organized: General notifications first, then transaction-related
     *
     * @return array
     */
    public static function brandNotificationTemplates()
    {
        return [
            // === GENERAL NOTIFICATIONS ===
            'forget_password' => [
                'name' => 'Forgot Password',
                'extra_tags' => [
                    ['{contact_name}', '{verification_link}']
                ],
            ],
            'password_reset_success' => [
                'name' => 'Password Reset Success',
                'extra_tags' => [
                    ['{contact_name}', '{brand_ecom_login_url}']
                ],
            ],
            'subscribe_newsletter' => [
                'name' => 'Subscribe Newsletter',
                'extra_tags' => [
                    ['{unsubscribe_url}']
                ],
            ],
            'registration_email_validation' => [
                'name' => 'Registration Email Validation',
                'extra_tags' => [
                    ['{contact_name}', '{front_url}', '{email_confirmation_link}']
                ],
            ],
            'email_confirmation' => [
                'name' => 'Email Validation',
                'extra_tags' => [
                    ['{contact_name}', '{front_url}', '{email_confirmation_link}']
                ],
            ],
            'contact_us_success' => [
                'name' => 'Contact Us Success',
                'extra_tags' => [
                    ['{contact_name}', '{front_url}', '{ref_no}']
                ],
            ],
            'stock_alert' => [
                'name' => 'Notify Us (Stock Alert)',
                'extra_tags' => [
                    ['{contact_name}', '{product_name}', '{product_sku}', '{product_url}']
                ],
            ],
            
            // === TRANSACTION/ORDER RELATED NOTIFICATIONS ===
            'new_sale' => [
                'name' => __('lang_v1.new_sale'),
                'extra_tags' => [
                    ['{contact_name}', '{invoice_number}', '{invoice_url}', '{total_amount}'],
                    ['{shipping_address}', '{billing_address}', '{order_date}']
                ],
            ],
            'sell_return_pickup' => [
                'name' => 'Sell Return Pickup',
                'extra_tags' => [
                    ['{contact_name}', '{invoice_number}', '{invoice_url}', '{pickup_address}']
                ],
            ],
            'order_packed' => [
                'name' => 'Order Packed',
                'extra_tags' => [
                    ['{contact_name}', '{invoice_number}', '{invoice_url}', '{total_amount}'],
                    ['{order_date}', '{packed_date}']
                ],
            ],
            'shipped' => [
                'name' => 'Shipped',
                'extra_tags' => [
                    ['{contact_name}', '{invoice_number}', '{invoice_url}', '{total_amount}'],
                    ['{shipment_number}', '{carrier_name}', '{tracking_number}', '{tracking_url}'],
                    ['{estimated_delivery}', '{shipping_address}']
                ],
            ],
            'refral_notification' => [
                'name' => 'Refral Notification',
                'extra_tags' => [
                    ['{contact_name}', '{cupon_code}']
                ],
            ],
        ];
    }
}

