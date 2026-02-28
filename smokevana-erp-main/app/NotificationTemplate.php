<?php

    namespace App;

    use Illuminate\Database\Eloquent\Model;

    class NotificationTemplate extends Model
    {
        /**
         * The attributes that aren't mass assignable.
         *
         * @var array
         */
        protected $guarded = ['id'];

        /**
         * Retrives notification template from database
         *
         * @param  int  $business_id
         * @param  string  $template_for
         * @return array $template
         */
        public static function getTemplate($business_id, $template_for)
        {
            $notif_template = NotificationTemplate::where('business_id', $business_id)
                ->where('template_for', $template_for)
                ->first();
            $template = [
                'subject' => ! empty($notif_template->subject) ? $notif_template->subject : '',
                'sms_body' => ! empty($notif_template->sms_body) ? $notif_template->sms_body : '',
                'whatsapp_text' => ! empty($notif_template->whatsapp_text) ? $notif_template->whatsapp_text : '',
                'email_body' => ! empty($notif_template->email_body) ? $notif_template->email_body
                    : '',
                'template_for' => $template_for,
                'cc' => ! empty($notif_template->cc) ? $notif_template->cc : '',
                'bcc' => ! empty($notif_template->bcc) ? $notif_template->bcc : '',
                'auto_send' => ! empty($notif_template->auto_send) ? 1 : 0,
                'auto_send_sms' => ! empty($notif_template->auto_send_sms) ? 1 : 0,
                'auto_send_wa_notif' => ! empty($notif_template->auto_send_wa_notif)
                    ? 1 : 0,
            ];

            return $template;
        }

        public static function customerNotifications()
        {
            return [
                'new_sale' => [
                    'name' => __('lang_v1.new_sale'),
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{contact_name}', '{invoice_number}', '{invoice_url}', '{total_amount}', '{paid_amount}', '{due_amount}', '{cumulative_due_amount}', '{due_date}'],
                        ['{location_name}', '{location_address}', '{location_email}', '{location_phone}',],
                        // [ '{location_custom_field_1}', '{location_custom_field_2}', '{location_custom_field_3}', '{location_custom_field_4}'],
                        // ['{contact_custom_field_1}', '{contact_custom_field_2}', '{contact_custom_field_3}', '{contact_custom_field_4}', '{contact_custom_field_5}', '{contact_custom_field_6}', '{contact_custom_field_7}', '{contact_custom_field_8}', '{contact_custom_field_9}', '{contact_custom_field_10}'],
                        // ['{sell_custom_field_1}', '{sell_custom_field_2}', '{sell_custom_field_3}', '{sell_custom_field_4}'],
                        // ['{shipping_custom_field_1}', '{shipping_custom_field_2}', '{shipping_custom_field_3}', '{shipping_custom_field_4}', '{shipping_custom_field_5}'],
                    ],
                ],
                // 'shipment' => [
                //     'name' => 'Shipment',
                //     'extra_tags' => [
                //         ['{business_name}', '{business_logo}'],
                //         ['{invoice_number}', '{invoice_url}', '{total_amount}', '{paid_amount}', '{due_amount}', '{cumulative_due_amount}', '{due_date}'],
                //         ['{shipment_number}', ' {carrier_name} ', '{tracking_number}', '{estimated_delivery}', '{shipping_address}', '{tracking_url}'],
                //     ],
                // ],
                'local_pickup' => [
                    'name' => 'Local Pickup',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{invoice_number}', '{invoice_url}',],
                    ],
                ],
                'sell_return_pickup' => [
                    'name' => 'Sell Return Pickup',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{invoice_number}', '{invoice_url}',],
                    ],
                ],
                'sell_return_shipment' => [
                    'name' => 'Sell Return Shipment',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{invoice_number}', '{invoice_url}'],
                    ],
                ],
                'payment_received' => [
                    'name' => __('lang_v1.payment_received'),
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{invoice_number}', '{payment_ref_number}', '{received_amount}'],
                        ['{contact_name}', '{contact_custom_field_1}', '{contact_custom_field_2}', '{contact_custom_field_3}', '{contact_custom_field_4}', '{contact_custom_field_5}', '{contact_custom_field_6}', '{contact_custom_field_7}', '{contact_custom_field_8}', '{contact_custom_field_9}', '{contact_custom_field_10}'],
                    ],
                ],
                'purchase_order' => [
                    'name' => __('lang_v1.purchase_order'),
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{invoice_number}', '{payment_ref_number}', '{received_amount}'],
                        ['{contact_name}', '{contact_custom_field_1}', '{contact_custom_field_2}', '{contact_custom_field_3}', '{contact_custom_field_4}', '{contact_custom_field_5}', '{contact_custom_field_6}', '{contact_custom_field_7}', '{contact_custom_field_8}', '{contact_custom_field_9}', '{contact_custom_field_10}'],
                    ],
                ],
                'payment_reminder' => [
                    'name' => __('lang_v1.payment_reminder'),
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{invoice_number}', '{due_amount}', '{cumulative_due_amount}', '{due_date}'],
                        ['{contact_name}', '{contact_custom_field_1}', '{contact_custom_field_2}', '{contact_custom_field_3}', '{contact_custom_field_4}', '{contact_custom_field_5}', '{contact_custom_field_6}', '{contact_custom_field_7}', '{contact_custom_field_8}', '{contact_custom_field_9}', '{contact_custom_field_10}'],

                    ],
                ],
                'new_booking' => [
                    'name' => __('lang_v1.new_booking'),
                    'extra_tags' => self::bookingNotificationTags(),
                ],
                'new_quotation' => [
                    'name' => __('lang_v1.new_quotation'),
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{invoice_number}', '{total_amount}', '{quote_url}'],
                        ['{location_name}', '{location_address}', '{location_email}', '{location_phone}', '{location_custom_field_1}', '{location_custom_field_2}', '{location_custom_field_3}', '{location_custom_field_4}'],
                        ['{contact_name}', '{contact_custom_field_1}', '{contact_custom_field_2}', '{contact_custom_field_3}', '{contact_custom_field_4}', '{contact_custom_field_5}', '{contact_custom_field_6}', '{contact_custom_field_7}', '{contact_custom_field_8}', '{contact_custom_field_9}', '{contact_custom_field_10}'],

                    ],
                ],
                'order_packed' => [
                    'name' => 'Order Packed',
                    'extra_tags' => [
                      ['{business_name}', '{business_logo}'],
                      ['{contact_name}', '{invoice_number}', '{invoice_url}', '{total_amount}', '{paid_amount}', '{due_amount}', '{due_date}'],
                      ['{location_name}', '{location_address}', '{location_email}', '{location_phone}'],
                      ['{order_date}', '{packed_date}'],
                    ],
                ],
                'order_shipped' => [
                    'name' => 'Order Shipped',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{invoice_number}', '{invoice_url}', '{total_amount}', '{paid_amount}', '{due_amount}', '{cumulative_due_amount}', '{due_date}'],
                        ['{shipment_number}', '{carrier_name}', '{tracking_number}', '{estimated_delivery}', '{shipping_address}', '{tracking_url}'],
                    ],
                ],
            ];
        }

        public static function generalNotifications()
        {
            return [
                'send_ledger' => [
                    'name' => __('lang_v1.send_ledger'),
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{balance_due}'],
                        ['{contact_name}', '{contact_custom_field_1}', '{contact_custom_field_2}', '{contact_custom_field_3}', '{contact_custom_field_4}', '{contact_custom_field_5}', '{contact_custom_field_6}', '{contact_custom_field_7}', '{contact_custom_field_8}', '{contact_custom_field_9}', '{contact_custom_field_10}'],
                    ],
                ],
                'send_payment_notification' => [
                    'name' => 'Send Payment Notification',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{payment_link},{payment_amount},{contact_name}']
                    ],
                ],
                'contact_us_send' => [
                    'name' => 'Contact Us Custom',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                    ],
                ],
                'forget_password' => [
                    'name' => 'Forgot Password',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{contact_name}', '{url_complete}']
                    ],
                ],
                'forget_password_web' => [
                    'name' => 'Forgot Password (Web link only)',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{contact_name}', '{url_complete}']
                    ],
                ],
                'password_reset_success' => [
                    'name' => 'Password Reset Success',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{contact_name}', '{url_business}']

                    ],
                ],
                'subscribe_newsletter' => [
                    'name' => 'Subscribe Newsletter',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{url_business}', '{unsubscribe_url}']
                    ],
                ],
                'registration_confirmation' => [
                    'name' => 'Registration',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{contact_name}', '{url_business}']
                    ],
                ],
                'email_confirmation' => [
                    'name' => 'Registration Email Validation',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{contact_name}', '{url_business}', '{email_confirmation_link}','{brand_logo}']
                    ],
                ],
                'registration_approve_confirmation' => [
                    'name' => 'Approve Registration',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{contact_name}', '{url_business}']
                    ],
                ],
                'contact_us_success' => [
                    'name' => 'Contact Us Success',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{contact_name}', '{url_business},{ref_no}']
                    ],
                ],
                'test_notification' => [
                    'name' => 'Test Notification',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{contact_name}', '{url_business}']
                    ],
                ],
                'stock_alert' => [
                    'name' => 'Notify Us',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{contact_name}', '{product_name}', '{product_sku}', '{product_url}', '{unsubscribe_url}']
                    ],
                ],
                'credit_approved' => [
                    'name' => 'Credit Approved',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{contact_name}', '{credit_limit}', '{requested_credit_amount}', '{url_business}']
                    ],
                ],
                'gift_card_code' => [
                    'name' => 'Gift Card Code',
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{contact_name}'],
                        ['{gift_card_code}', '{gift_card_amount}', '{gift_card_balance}', '{gift_card_currency}', '{gift_card_expires_at}', '{gift_card_message}']
                    ],
                ],
              //   'order_packed' => [
              //     'name' => 'Order Packed',
              //     'extra_tags' => [
              //         ['{business_name}', '{business_logo}'],
              //         ['{contact_name}', '{invoice_number}', '{invoice_url}', '{total_amount}', '{paid_amount}', '{due_amount}', '{due_date}'],
              //         ['{location_name}', '{location_address}', '{location_email}', '{location_phone}'],
              //         ['{order_date}', '{packed_date}', '{tracking_number}', '{estimated_delivery}']
              //     ],
              // ],
              // 'shipped' => [
              //     'name' => 'Shipped',
              //     'extra_tags' => [
              //         ['{business_name}', '{business_logo}'],
              //         ['{contact_name}', '{invoice_number}', '{invoice_url}', '{total_amount}', '{paid_amount}', '{due_amount}', '{due_date}'],
              //         ['{shipment_number}', '{carrier_name}', '{tracking_number}', '{estimated_delivery}', '{shipping_address}', '{tracking_url}'],
              //         ['{location_name}', '{location_address}', '{location_email}', '{location_phone}']
              //     ],
              // ],
            ];
        }

        public static function supplierNotifications()
        {
            return [
                'new_order' => [
                    'name' => __('lang_v1.new_order'),
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{order_ref_number}', '{total_amount}', '{received_amount}', '{due_amount}'],
                        ['{location_name}', '{location_address}', '{location_email}', '{location_phone}', '{location_custom_field_1}', '{location_custom_field_2}', '{location_custom_field_3}', '{location_custom_field_4}'],
                        ['{purchase_custom_field_1}', '{purchase_custom_field_2}', '{purchase_custom_field_3}', '{purchase_custom_field_4}', '{contact_business_name}'],
                        ['{contact_name}', '{contact_custom_field_1}', '{contact_custom_field_2}', '{contact_custom_field_3}', '{contact_custom_field_4}', '{contact_custom_field_5}', '{contact_custom_field_6}', '{contact_custom_field_7}', '{contact_custom_field_8}', '{contact_custom_field_9}', '{contact_custom_field_10}'],
                        ['{shipping_custom_field_1}', '{shipping_custom_field_2}', '{shipping_custom_field_3}', '{shipping_custom_field_4}', '{shipping_custom_field_5}'],
                    ],
                ],
                'payment_paid' => [
                    'name' => __('lang_v1.payment_paid'),
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{order_ref_number}', '{payment_ref_number}', '{paid_amount}'],
                        ['{contact_name}', '{contact_business_name}', '{contact_custom_field_1}', '{contact_custom_field_2}', '{contact_custom_field_3}', '{contact_custom_field_4}', '{contact_custom_field_5}', '{contact_custom_field_6}', '{contact_custom_field_7}', '{contact_custom_field_8}', '{contact_custom_field_9}', '{contact_custom_field_10}'],
                    ],
                ],
                'items_received' => [
                    'name' => __('lang_v1.items_received'),
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{order_ref_number}'],
                        ['{contact_business_name}', '{contact_name}', '{contact_custom_field_1}', '{contact_custom_field_2}', '{contact_custom_field_3}', '{contact_custom_field_4}', '{contact_custom_field_5}', '{contact_custom_field_6}', '{contact_custom_field_7}', '{contact_custom_field_8}', '{contact_custom_field_9}', '{contact_custom_field_10}'],
                    ],
                ],
                'items_pending' => [
                    'name' => __('lang_v1.items_pending'),
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{order_ref_number}'],
                        ['{contact_business_name}', '{contact_name}', '{contact_custom_field_1}', '{contact_custom_field_2}', '{contact_custom_field_3}', '{contact_custom_field_4}', '{contact_custom_field_5}', '{contact_custom_field_6}', '{contact_custom_field_7}', '{contact_custom_field_8}', '{contact_custom_field_9}', '{contact_custom_field_10}'],
                    ],
                ],

                'purchase_order' => [
                    'name' => __('lang_v1.purchase_order'),
                    'extra_tags' => [
                        ['{business_name}', '{business_logo}'],
                        ['{order_ref_number}'],
                        ['{contact_business_name}', '{contact_name}', '{contact_custom_field_1}', '{contact_custom_field_2}', '{contact_custom_field_3}', '{contact_custom_field_4}', '{contact_custom_field_5}', '{contact_custom_field_6}', '{contact_custom_field_7}', '{contact_custom_field_8}', '{contact_custom_field_9}', '{contact_custom_field_10}'],
                    ],
                ],
            ];
        }

        public static function notificationTags()
        {
            return [
                '{contact_name}',
                '{invoice_number}',
                '{total_amount}',
                '{paid_amount}',
                '{due_amount}',
                '{business_name}',
                '{business_logo}',
                '{cumulative_due_amount}',
                '{due_date}',
                '{contact_business_name}',
            ];
        }

        public static function bookingNotificationTags()
        {
            return [
                ['{business_name}', '{business_logo}'],
                ['{table}', '{start_time}', '{end_time}', '{service_staff}', '{correspondent}'],
                ['{location}', '{location_name}', '{location_address}', '{location_email}', '{location_phone}', '{location_custom_field_1}', '{location_custom_field_2}', '{location_custom_field_3}', '{location_custom_field_4}'],
                ['{contact_name}', '{contact_custom_field_1}', '{contact_custom_field_2}', '{contact_custom_field_3}', '{contact_custom_field_4}', '{contact_custom_field_5}', '{contact_custom_field_6}', '{contact_custom_field_7}', '{contact_custom_field_8}', '{contact_custom_field_9}', '{contact_custom_field_10}'],
            ];
        }

        public static function defaultNotificationTemplates($business_id = null)
        {
            $notification_template_data = [
                [
                    'business_id' => $business_id,
                    'template_for' => 'new_sale',
                    'email_body' => '<p>Dear {contact_name},</p>

                    <p>Your invoice number is {invoice_number}<br />
                    Total amount: {total_amount}<br />
                    Paid amount: {received_amount}</p>

                    <p>Thank you for shopping with us.</p>

                    <p>{business_logo}</p>

                    <p>&nbsp;</p>',
                    'sms_body' => 'Dear {contact_name}, Thank you for shopping with us. {business_name}',
                    'subject' => 'Thank you from {business_name}',
                    'auto_send' => '0',
                ],
                [
                    'business_id' => $business_id,
                    'template_for' => 'payment_received',
                    'email_body' => '<p>Dear {contact_name},</p>

                <p>We have received a payment of {received_amount}</p>

                <p>{business_logo}</p>',
                    'sms_body' => 'Dear {contact_name}, We have received a payment of {received_amount}. {business_name}',
                    'subject' => 'Payment Received, from {business_name}',
                    'auto_send' => '0',
                ],
                [
                    'business_id' => $business_id,
                    'template_for' => 'payment_reminder',
                    'email_body' => '<p>Dear {contact_name},</p>

                    <p>This is to remind you that you have pending payment of {due_amount}. Kindly pay it as soon as possible.</p>

                    <p>{business_logo}</p>',
                    'sms_body' => 'Dear {contact_name}, You have pending payment of {due_amount}. Kindly pay it as soon as possible. {business_name}',
                    'subject' => 'Payment Reminder, from {business_name}',
                    'auto_send' => '0',
                ],
                [
                    'business_id' => $business_id,
                    'template_for' => 'new_booking',
                    'email_body' => '<p>Dear {contact_name},</p>

                    <p>Your booking is confirmed</p>

                    <p>Date: {start_time} to {end_time}</p>

                    <p>Table: {table}</p>

                    <p>Location: {location}</p>

                    <p>{business_logo}</p>',
                    'sms_body' => 'Dear {contact_name}, Your booking is confirmed. Date: {start_time} to {end_time}, Table: {table}, Location: {location}',
                    'subject' => 'Booking Confirmed - {business_name}',
                    'auto_send' => '0',
                ],
                [
                    'business_id' => $business_id,
                    'template_for' => 'new_order',
                    'email_body' => '<p>Dear {contact_name},</p>

                    <p>We have a new order with reference number {order_ref_number}. Kindly process the products as soon as possible.</p>

                    <p>{business_name}<br />
                    {business_logo}</p>',
                    'sms_body' => 'Dear {contact_name}, We have a new order with reference number {order_ref_number}. Kindly process the products as soon as possible. {business_name}',
                    'subject' => 'New Order, from {business_name}',
                    'auto_send' => '0',
                ],
                [
                    'business_id' => $business_id,
                    'template_for' => 'payment_paid',
                    'email_body' => '<p>Dear {contact_name},</p>

                    <p>We have paid amount {paid_amount} again invoice number {order_ref_number}.<br />
                    Kindly note it down.</p>

                    <p>{business_name}<br />
                    {business_logo}</p>',
                    'sms_body' => 'We have paid amount {paid_amount} again invoice number {order_ref_number}.
                    Kindly note it down. {business_name}',
                    'subject' => 'Payment Paid, from {business_name}',
                    'auto_send' => '0',
                ],
                [
                    'business_id' => $business_id,
                    'template_for' => 'items_received',
                    'email_body' => '<p>Dear {contact_name},</p>

                    <p>We have received all items from invoice reference number {order_ref_number}. Thank you for processing it.</p>

                    <p>{business_name}<br />
                    {business_logo}</p>',
                    'sms_body' => 'We have received all items from invoice reference number {order_ref_number}. Thank you for processing it. {business_name}',
                    'subject' => 'Items received, from {business_name}',
                    'auto_send' => '0',
                ],
                [
                    'business_id' => $business_id,
                    'template_for' => 'items_pending',
                    'email_body' => '<p>Dear {contact_name},<br />
                    This is to remind you that we have not yet received some items from invoice reference number {order_ref_number}. Please process it as soon as possible.</p>

                    <p>{business_name}<br />
                    {business_logo}</p>',
                    'sms_body' => 'This is to remind you that we have not yet received some items from invoice reference number {order_ref_number} . Please process it as soon as possible.{business_name}',
                    'subject' => 'Items Pending, from {business_name}',
                    'auto_send' => '0',
                ],

                [
                    'business_id' => $business_id,
                    'template_for' => 'new_quotation',
                    'email_body' => '<p>Dear {contact_name},</p>

                    <p>Your quotation number is {invoice_number}<br />
                    Total amount: {total_amount}</p>

                    <p>Thank you for shopping with us.</p>

                    <p>{business_logo}</p>

                    <p>&nbsp;</p>',
                    'sms_body' => 'Dear {contact_name}, Thank you for shopping with us. {business_name}',
                    'subject' => 'Thank you from {business_name}',
                    'auto_send' => '0',
                ],
                [
                    'business_id' => $business_id,
                    'template_for' => 'purchase_order',
                    'email_body' => '<p>Dear {contact_name},</p>

                    <p>We have a new purchase order with reference number {order_ref_number}. The respective invoice is attached here with.</p>

                    <p>{business_logo}</p>',
                    'sms_body' => 'We have a new purchase order with reference number {order_ref_number}. {business_name}',
                    'subject' => 'New Purchase Order, from {business_name}',
                    'auto_send' => '0',
                ],
                [
                    'business_id' => $business_id,
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
                    'auto_send' => '1',
                ],
                [
                    'business_id' => $business_id,
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
                    'subject' => 'Registration Sucessfull',
                    'whatsapp_text' => 'Hello {contact_name}, 👋

Thank you for registering with *{business_name}*!  
Your registration was successful. ✅  
We will notify you once it’s approved.

For more info, visit: {url_business}

Best regards,  
{business_name}
',
                    'auto_send' => '1',
                ],
                [
                    'business_id' => $business_id,
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
                    'subject' => 'Thank you for subscribing to <strong>{business_name}</strong> newsletter!',
                    'whatsapp_text' => "",
                    'auto_send' => '1',
                ],
                [
                    'business_id' => $business_id,
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
                    'subject' => 'Your password has been successfully reset for your {business_name} account.',
                    'whatsapp_text' => "",
                    'auto_send' => '1',
                ],
                [
                    'business_id' => $business_id,
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
                Click the button below to set a new password:
              </p>
              <p style="text-align:center; margin:20px 0;">
                <a href="{url_complete}" style="background-color:#004aad; color:#ffffff; padding:12px 20px; border-radius:4px; text-decoration:none; font-size:16px;">
                  Reset Password
                </a>
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                If you did not request this, you can safely ignore this email.
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
                    'sms_body' => 'Hi {contact_name}, we received a request to reset your {business_name} password. Reset it using this link: {url_complete}',
                    'subject' => 'Forgot Your Password?',
                    'whatsapp_text' => "Hello {contact_name}, 👋

We received a request to reset your password for your *{business_name}* account.  
You can reset it here:  
{url_complete}

If you didn’t request this, just ignore this message.

– {business_name} Team
",
                    'auto_send' => '1',
                ],
                [
                    'business_id' => $business_id,
                    'template_for' => 'forget_password_web',
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
                Click the button below to set a new password:
              </p>
              <p style="text-align:center; margin:20px 0;">
                <a href="{url_complete}" style="background-color:#004aad; color:#ffffff; padding:12px 20px; border-radius:4px; text-decoration:none; font-size:16px;">
                  Reset Password
                </a>
              </p>
              <p style="font-size:16px; line-height:1.5; margin:0 0 15px;">
                If you did not request this, you can safely ignore this email.
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
                    'sms_body' => 'Hi {contact_name}, we received a request to reset your {business_name} password. Reset it using this link: {url_complete}',
                    'subject' => 'Forgot Your Password?',
                    'whatsapp_text' => "",
                    'auto_send' => '1',
                ],
                [
                    'business_id' => $business_id,
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
                    'subject' => ' Thank you for contacting Us',
                    'whatsapp_text' => "",
                    'auto_send' => '1',
                ],
                [
                    'business_id' => $business_id,
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
                    'subject' => 'Payment Request',
                    'whatsapp_text' => "Hello {contact_name}, 👋

You have a pending payment of *{payment_amount}* with *{business_name}*.  
Please complete it using the link below:  
{payment_link}

Thank you,  
{business_name}
",
                    'auto_send' => '1',
                ],
                [
                    'business_id' => $business_id,
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
                <strong>Tracking Number:</strong> {tracking_number}<br>
                <strong>Estimated Delivery:</strong> {estimated_delivery}<br>
                <strong>Shipping Address:</strong> {shipping_address}
              </p>
              <p style="font-size:16px; margin:0 0 15px;">
                You can track your shipment here:  
                <a href="{tracking_url}" style="color:#004aad;">Track Shipment</a>
              </p>
              <hr style="border:none; border-top:1px solid #ddd; margin:30px 0;">
              <h3 style="color:#004aad; font-size:18px; margin:0 0 15px;">Invoice Summary</h3>
              <p style="font-size:16px; margin:0 0 10px;">
                <strong>Invoice No:</strong> {invoice_number}  
                (<a href="{invoice_url}" style="color:#004aad;">View Invoice</a>)<br>
                <strong>Total:</strong> {total_amount}<br>
                <strong>Paid:</strong> {paid_amount}<br>
                <strong>Due:</strong> {due_amount} (Due by: {due_date})<br>
                <strong>Total Outstanding:</strong> {cumulative_due_amount}
              </p>
              <p style="font-size:16px; margin:30px 0 0;">
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
  </table>',
                    'sms_body' => 'Your shipment #{shipment_number} via {carrier_name} has been dispatched. Track here: {tracking_url}. Invoice #{invoice_number}, Due: {due_amount} by {due_date}. - {business_name}',
                    'subject' => 'Shipment Dispatched',
                    'whatsapp_text' => "Hello 👋,

Your shipment *#{shipment_number}* has been dispatched via *{carrier_name}*.

📦 *Tracking Number:* {tracking_number}  
🗓️ *Estimated Delivery:* {estimated_delivery}  
🏠 *Shipping Address:* {shipping_address}  
🔗 *Track Shipment:* {tracking_url}

💳 *Invoice:* #{invoice_number}  
💰 *Total:* {total_amount}  
✅ *Paid:* {paid_amount}  
❗ *Due:* {due_amount} (by {due_date})  
📌 *Total Outstanding:* {cumulative_due_amount}

Thank you for choosing *{business_name}*.
",
                    'auto_send' => '1',
                ],
                [
                    'business_id' => $business_id,
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
                    'subject' => 'Your Order is Ready for Pickup',
                    'whatsapp_text' => "Hello 👋

Your order with *{business_name}* is ready for pickup!  
🧾 *Invoice:* #{invoice_number}  
📄 View Invoice: {invoice_url}

Thank you for choosing *{business_name}*!
",
                    'auto_send' => '1',
                ],
                [
                    'business_id' => $business_id,
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
                    'subject' => 'Test Notification',
                    'whatsapp_text' => "This is a test notification.",
                    'auto_send' => '1',
                    'auto_send_sms' => '1',
                    'auto_send_wa_notif' => '1',
                ],

            ];

            return $notification_template_data;
        }
    }
