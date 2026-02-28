<?php

namespace App\Utils;

use App\Business;
use App\Notifications\CustomerNotification;
use App\Notifications\RecurringExpenseNotification;
use App\Notifications\RecurringInvoiceNotification;
use App\Notifications\SupplierNotification;
use App\NotificationTemplate;
use App\Restaurant\Booking;
use App\System;
use Config;
use Illuminate\Support\Facades\Notification;
use App\Contact;
use Illuminate\Support\Facades\Log;
use App\BrandConfig;
use App\Brands;


class NotificationUtil extends Util
{
    /**
     * Automatically send notification to customer/supplier if enabled in the template setting
     *
     * @param  int  $business_id
     * @param  string  $notification_type
     * @param  obj  $transaction
     * @param  obj  $contact
     * @return void
     */
    public function autoSendNotification($business_id, $notification_type, $transaction, $contact)
    {
        Log::info('NotificationUtil: autoSendNotification called', [
            'business_id' => $business_id,
            'notification_type' => $notification_type,
            'transaction_id' => $transaction->id ?? null,
            'contact_id' => $contact->id ?? null,
            'contact_email' => $contact->email ?? null
        ]);

        $notification_template = NotificationTemplate::where('business_id', $business_id)
                ->where('template_for', $notification_type)
                ->first();

        $business = Business::findOrFail($business_id);
        $data['email_settings'] = $business->email_settings;
        $data['sms_settings'] = $business->sms_settings;
        $whatsapp_link = '';
        
        if (! empty($notification_template)) {
            Log::info('NotificationUtil: Template found', [
                'business_id' => $business_id,
                'notification_type' => $notification_type,
                'auto_send' => $notification_template->auto_send,
                'auto_send_sms' => $notification_template->auto_send_sms,
                'auto_send_wa_notif' => $notification_template->auto_send_wa_notif
            ]);
            
            // Check auto_send flags - handle both string '1'/'0' and integer 1/0 (same as stock alert)
            $auto_send_enabled = ($notification_template->auto_send == '1' || $notification_template->auto_send == 1);
            $auto_send_sms_enabled = ($notification_template->auto_send_sms == '1' || $notification_template->auto_send_sms == 1);
            $auto_send_wa_enabled = ($notification_template->auto_send_wa_notif == '1' || $notification_template->auto_send_wa_notif == 1);
            
            if ($auto_send_enabled || $auto_send_sms_enabled || $auto_send_wa_enabled) {
                $orig_data = [
                    'email_body' => $notification_template->email_body,
                    'sms_body' => $notification_template->sms_body,
                    'subject' => $notification_template->subject,
                    'whatsapp_text' => $notification_template->whatsapp_text,
                ];
                $tag_replaced_data = $this->replaceTags($business_id, $orig_data, $transaction);

                $data['email_body'] = $tag_replaced_data['email_body'];
                $data['sms_body'] = $tag_replaced_data['sms_body'];
                $data['whatsapp_text'] = $tag_replaced_data['whatsapp_text'];

                //Auto send email
                if ($auto_send_enabled && ! empty($contact->email)) {
                    Log::info('NotificationUtil: Sending email notification', [
                        'notification_type' => $notification_type,
                        'business_id' => $business_id,
                        'contact_email' => $contact->email,
                        'auto_send_value' => $notification_template->auto_send
                    ]);
                    $data['subject'] = $tag_replaced_data['subject'];
                    $data['to_email'] = $contact->email;
                    // Ensure email_settings and business_id are passed to notification
                    $data['email_settings'] = $business->email_settings;
                    $data['business_id'] = $business_id;
                    if ($transaction) {
                        $data['transaction'] = $transaction;
                    }

                    // IMPORTANT: Configure email BEFORE creating notification
                    // This ensures mail config is set before Laravel initializes MailManager
                    $this->configureEmail($data, true);
                    
                    Log::info('NotificationUtil: Email configuration applied', [
                        'notification_type' => $notification_type,
                        'mail_host' => config('mail.mailers.smtp.host'),
                        'mail_port' => config('mail.mailers.smtp.port'),
                        'mail_from' => config('mail.from.address'),
                    ]);

                    $customer_notifications = NotificationTemplate::customerNotifications();
                    $supplier_notifications = NotificationTemplate::supplierNotifications();
                    
                    Log::info('NotificationUtil: Checking notification type', [
                        'notification_type' => $notification_type,
                        'in_customer_notifications' => array_key_exists($notification_type, $customer_notifications),
                        'in_supplier_notifications' => array_key_exists($notification_type, $supplier_notifications),
                    ]);

                    try {
                        if (array_key_exists($notification_type, $customer_notifications)) {
                            Log::info('NotificationUtil: Sending customer notification', [
                                'notification_type' => $notification_type,
                                'to_email' => $data['to_email']
                            ]);
                            Notification::route('mail', $data['to_email'])
                                            ->notify(new CustomerNotification($data));
                            Log::info('NotificationUtil: Customer notification sent successfully', [
                                'notification_type' => $notification_type,
                                'to_email' => $data['to_email']
                            ]);
                        } elseif (array_key_exists($notification_type, $supplier_notifications)) {
                            Log::info('NotificationUtil: Sending supplier notification', [
                                'notification_type' => $notification_type,
                                'to_email' => $data['to_email']
                            ]);
                            Notification::route('mail', $data['to_email'])
                                            ->notify(new SupplierNotification($data));
                            Log::info('NotificationUtil: Supplier notification sent successfully', [
                                'notification_type' => $notification_type,
                                'to_email' => $data['to_email']
                            ]);
                        } else {
                            Log::warning('NotificationUtil: Notification type not found in customer or supplier notifications', [
                                'notification_type' => $notification_type
                            ]);
                        }
                        $this->activityLog($transaction, 'email_notification_sent', null, [], false, $business_id);
                    } catch (\Exception $e) {
                        Log::error('NotificationUtil: Failed to send notification', [
                            'notification_type' => $notification_type,
                            'to_email' => $data['to_email'] ?? null,
                            'error' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
                    }
                } else {
                    Log::warning('NotificationUtil: Email not sent - auto_send disabled or no email', [
                        'notification_type' => $notification_type,
                        'auto_send_enabled' => $auto_send_enabled,
                        'has_email' => !empty($contact->email),
                        'auto_send_value' => $notification_template->auto_send ?? null,
                        'contact_email' => $contact->email ?? null
                    ]);
                }

                //Auto send sms
                if ($auto_send_sms_enabled) {
                    $data['mobile_number'] = $contact->mobile;
                    if (! empty($contact->mobile)) {
                        try {
                            $this->sendSms($data);

                            $this->activityLog($transaction, 'sms_notification_sent', null, [], false, $business_id);
                        } catch (\Exception $e) {
                            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
                        }
                    }
                }

                if ($auto_send_wa_enabled) {
                    $data['mobile_number'] = $contact->mobile;
                    if (! empty($contact->mobile)) {
                        $whatsapp_link = $this->getWhatsappNotificationLink($data);
                    }
                }
            } else {
                Log::warning('NotificationUtil: All auto_send flags are disabled', [
                    'notification_type' => $notification_type,
                    'auto_send' => $notification_template->auto_send,
                    'auto_send_sms' => $notification_template->auto_send_sms,
                    'auto_send_wa_notif' => $notification_template->auto_send_wa_notif,
                    'business_id' => $business_id
                ]);
            }
        } else {
            Log::warning('NotificationUtil: Template not found', [
                'business_id' => $business_id,
                'notification_type' => $notification_type
            ]);
        }

        return $whatsapp_link;
    }


    public function autoSendNotificationCustom($business_id, $notification_type, $custom_data=null, $contact)
    {
        $notification_template = NotificationTemplate::where('business_id', $business_id)
                ->where('template_for', $notification_type)
                ->first();
        $business = Business::findOrFail($business_id);
        $data['email_settings'] = $business->email_settings;
        $data['sms_settings'] = $business->sms_settings;
        $whatsapp_link = '';
        if (! empty($notification_template)) {
            if (! empty($notification_template->auto_send) || ! empty($notification_template->auto_send_sms) || ! empty($notification_template->auto_send_wa_notif)) {
                $orig_data = [
                    'email_body' => $notification_template->email_body,
                    'sms_body' => $notification_template->sms_body,
                    'subject' => $notification_template->subject,
                    'whatsapp_text' => $notification_template->whatsapp_text,
                ];
                
                $tag_replaced_data = $this->replaceCustomTags($business_id, $orig_data, $custom_data);

                $data['email_body'] = $tag_replaced_data['email_body'];
                $data['sms_body'] = $tag_replaced_data['sms_body'];
                $data['whatsapp_text'] = $tag_replaced_data['whatsapp_text'];

                //Auto send email
                if (! empty($notification_template->auto_send) && ! empty($contact->email)) {
                    $data['subject'] = $tag_replaced_data['subject'];
                    $data['to_email'] = $contact->email;
                    $generalNotifications = NotificationTemplate::generalNotifications();
                    // Configure email settings before sending
                    $this->configureEmail($data, true);
                    
                    // Check if mail host is configured after setup
                    $mailHost = config('mail.mailers.smtp.host');
                    
                    if (empty($mailHost)) {
                        Log::warning("Mail configuration missing. Skipping email notification.", [
                            'notification_type' => $notification_type,
                            'business_id' => $business_id,
                            'email' => $contact->email
                        ]);
                    } else {
                        try {
                            if (array_key_exists($notification_type, $generalNotifications)) {
                                Log::info('Sending email via Notification::route', ['to_email' => $data['to_email']]);
                                Notification::route('mail', $data['to_email'])
                                ->notify(new CustomerNotification($data));
                                Log::info('Email notification sent successfully', ['to_email' => $data['to_email']]);
                            } else {
                                Log::warning('Notification type not in generalNotifications', [
                                    'notification_type' => $notification_type
                                ]);
                            }
                        } catch (\TypeError $e) {
                            Log::error("Mail configuration TypeError: " . $e->getMessage(), [
                                'notification_type' => $notification_type,
                                'business_id' => $business_id,
                                'email' => $contact->email
                            ]);
                        } catch (\Exception $e) {
                            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
                        }
                    }
                } else {
                    Log::info('Email not sent - conditions not met', [
                        'auto_send' => $notification_template->auto_send ?? null,
                        'has_email' => !empty($contact->email)
                    ]);
                }

                //Auto send sms
                if (! empty($notification_template->auto_send_sms)) {
                    $data['mobile_number'] = $contact->mobile;
                    if (! empty($contact->mobile)) {
                        try {
                            $this->sendSms($data);
                        } catch (\Exception $e) {
                            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
                        }
                    }
                }

                if (! empty($notification_template->auto_send_wa_notif)) {
                    $data['mobile_number'] = $contact->mobile;
                    if (! empty($contact->mobile)) {
                        $whatsapp_link = $this->getWhatsappNotificationLink($data);
                    }
                }
            }
        }

        return ['status'=>true , 'msg'=> 'notification send successful!'];
    }

    public function autoSendNotificationCombineService($business_id, $notification_type, $custom_data=null, $notifiable)
    {
        $notification_template = NotificationTemplate::where('business_id', $business_id)
                ->where('template_for', $notification_type)
                ->first();
        $business = Business::findOrFail($business_id);
        $data['email_settings'] = $business->email_settings;
        $data['sms_settings'] = $business->sms_settings;
        $whatsapp_link = '';
        
        if (! empty($notification_template)) {
            if (! empty($notification_template->auto_send) || ! empty($notification_template->auto_send_sms) || ! empty($notification_template->auto_send_wa_notif)) {
                $orig_data = [
                    'email_body' => $notification_template->email_body,
                    'sms_body' => $notification_template->sms_body,
                    'subject' => $notification_template->subject,
                    'whatsapp_text' => $notification_template->whatsapp_text,
                ];
                
                $tag_replaced_data = $this->replaceCustomTags($business_id, $orig_data, $notifiable);

                $data['email_body'] = $tag_replaced_data['email_body'];
                $data['sms_body'] = $tag_replaced_data['sms_body'];
                $data['whatsapp_text'] = $tag_replaced_data['whatsapp_text'];
                $data['subject'] = $tag_replaced_data['subject'];

                // Create notification record in DB (using Laravel's notification system)
                $notification = $notifiable->notify(new \App\Notifications\CustomerNotification($data));

                // Try WebSocket (broadcast event)
                event(new \App\Events\NotificationBroadcasted($notifiable, $notification));
                dd($notification);

                // Dispatch FCM job with short delay (e.g., 10 seconds)
                \App\Jobs\SendFcmNotificationJob::dispatch($notifiable, $notifiable->notifications()->latest()->first()->id)->delay(now()->addSeconds(10));

                // Dispatch Email job with 1 hour delay
                \App\Jobs\SendEmailNotificationJob::dispatch($notifiable, $notifiable->notifications()->latest()->first()->id)->delay(now()->addHour());

                // Dispatch SMS job with 12 hour delay
                \App\Jobs\SendSmsNotificationJob::dispatch($notifiable, $notifiable->notifications()->latest()->first()->id)->delay(now()->addHours(12));
            }
        }

        return ['status'=>true , 'msg'=> 'notification send successful!'];
    }


    /**
     * Replaces tags from notification body with original value
     *
     * @param  text  $body
     * @param  int  $booking_id
     * @return array
     */
    public function replaceBookingTags($business_id, $data, $booking_id)
    {
        $business = Business::findOrFail($business_id);
        $booking = Booking::where('business_id', $business_id)
                    ->with(['customer', 'table', 'correspondent', 'waiter', 'location', 'business'])
                    ->findOrFail($booking_id);
        foreach ($data as $key => $value) {
            //Replace contact name
            if (strpos($value, '{contact_name}') !== false) {
                $contact_name = $booking->customer->name;

                $data[$key] = str_replace('{contact_name}', $contact_name, $data[$key]);
            }

            if (strpos($value, '{contact_custom_field_1}') !== false) {
                $contact_custom_field_1 = $booking->customer->custom_field1 ?? '';
                $data[$key] = str_replace('{contact_custom_field_1}', $contact_custom_field_1, $data[$key]);
            }

            if (strpos($value, '{contact_custom_field_2}') !== false) {
                $contact_custom_field_2 = $booking->customer->custom_field2 ?? '';
                $data[$key] = str_replace('{contact_custom_field_2}', $contact_custom_field_2, $data[$key]);
            }

            if (strpos($value, '{contact_custom_field_3}') !== false) {
                $contact_custom_field_3 = $booking->customer->custom_field3 ?? '';
                $data[$key] = str_replace('{contact_custom_field_3}', $contact_custom_field_3, $data[$key]);
            }

            if (strpos($value, '{contact_custom_field_4}') !== false) {
                $contact_custom_field_4 = $booking->customer->custom_field4 ?? '';
                $data[$key] = str_replace('{contact_custom_field_4}', $contact_custom_field_4, $data[$key]);
            }

            if (strpos($value, '{contact_custom_field_5}') !== false) {
                $contact_custom_field_5 = $booking->customer->custom_field5 ?? '';
                $data[$key] = str_replace('{contact_custom_field_5}', $contact_custom_field_5, $data[$key]);
            }

            if (strpos($value, '{contact_custom_field_6}') !== false) {
                $contact_custom_field_6 = $booking->customer->custom_field6 ?? '';
                $data[$key] = str_replace('{contact_custom_field_6}', $contact_custom_field_6, $data[$key]);
            }

            if (strpos($value, '{contact_custom_field_7}') !== false) {
                $contact_custom_field_7 = $booking->customer->custom_field7 ?? '';
                $data[$key] = str_replace('{contact_custom_field_7}', $contact_custom_field_7, $data[$key]);
            }
            if (strpos($value, '{contact_custom_field_8}') !== false) {
                $contact_custom_field_8 = $booking->customer->custom_field8 ?? '';
                $data[$key] = str_replace('{contact_custom_field_8}', $contact_custom_field_8, $data[$key]);
            }
            if (strpos($value, '{contact_custom_field_9}') !== false) {
                $contact_custom_field_9 = $booking->customer->custom_field9 ?? '';
                $data[$key] = str_replace('{contact_custom_field_9}', $contact_custom_field_9, $data[$key]);
            }
            if (strpos($value, '{contact_custom_field_10}') !== false) {
                $contact_custom_field_10 = $booking->customer->custom_field10 ?? '';
                $data[$key] = str_replace('{contact_custom_field_10}', $contact_custom_field_10, $data[$key]);
            }

            //Replace table
            if (strpos($value, '{table}') !== false) {
                $table = ! empty($booking->table->name) ? $booking->table->name : '';

                $data[$key] = str_replace('{table}', $table, $data[$key]);
            }

            //Replace start_time
            if (strpos($value, '{start_time}') !== false) {
                $start_time = $this->format_date($booking->booking_start, true);

                $data[$key] = str_replace('{start_time}', $start_time, $data[$key]);
            }

            //Replace end_time
            if (strpos($value, '{end_time}') !== false) {
                $end_time = $this->format_date($booking->booking_end, true);

                $data[$key] = str_replace('{end_time}', $end_time, $data[$key]);
            }
            //Replace location
            if (strpos($value, '{location}') !== false) {
                $location = $booking->location->name;

                $data[$key] = str_replace('{location}', $location, $data[$key]);
            }

            if (strpos($value, '{location_name}') !== false) {
                $location = $booking->location->name;

                $data[$key] = str_replace('{location_name}', $location, $data[$key]);
            }

            if (strpos($value, '{location_address}') !== false) {
                $location_address = $booking->location->location_address;

                $data[$key] = str_replace('{location_address}', $location_address, $data[$key]);
            }

            if (strpos($value, '{location_email}') !== false) {
                $location_email = $booking->location->email;

                $data[$key] = str_replace('{location_email}', $location_email, $data[$key]);
            }

            if (strpos($value, '{location_phone}') !== false) {
                $location_phone = $booking->location->mobile;

                $data[$key] = str_replace('{location_phone}', $location_phone, $data[$key]);
            }

            if (strpos($value, '{location_custom_field_1}') !== false) {
                $location_custom_field_1 = $booking->location->custom_field1;

                $data[$key] = str_replace('{location_custom_field_1}', $location_custom_field_1, $data[$key]);
            }

            if (strpos($value, '{location_custom_field_2}') !== false) {
                $location_custom_field_2 = $booking->location->custom_field2;

                $data[$key] = str_replace('{location_custom_field_2}', $location_custom_field_2, $data[$key]);
            }

            if (strpos($value, '{location_custom_field_3}') !== false) {
                $location_custom_field_3 = $booking->location->custom_field3;

                $data[$key] = str_replace('{location_custom_field_3}', $location_custom_field_3, $data[$key]);
            }

            if (strpos($value, '{location_custom_field_4}') !== false) {
                $location_custom_field_4 = $booking->location->custom_field4;

                $data[$key] = str_replace('{location_custom_field_4}', $location_custom_field_4, $data[$key]);
            }

            //Replace service_staff
            if (strpos($value, '{service_staff}') !== false) {
                $service_staff = ! empty($booking->waiter) ? $booking->waiter->user_full_name : '';

                $data[$key] = str_replace('{service_staff}', $service_staff, $data[$key]);
            }

            //Replace service_staff
            if (strpos($value, '{correspondent}') !== false) {
                $correspondent = ! empty($booking->correspondent) ? $booking->correspondent->user_full_name : '';

                $data[$key] = str_replace('{correspondent}', $correspondent, $data[$key]);
            }

            //Replace business_name
            if (strpos($value, '{business_name}') !== false) {
                $business_name = $business->name;
                $data[$key] = str_replace('{business_name}', $business_name, $data[$key]);
            }

            //Replace business_logo
            if (strpos($value, '{business_logo}') !== false) {
                $logo_name = $business->logo;
                $business_logo = ! empty($logo_name) ? '<img style="max-width: 300px; height: auto;"  src="'.url('uploads/business_logos/'.$logo_name).'" alt="Business Logo" >' : '';

                $data[$key] = str_replace('{business_logo}', $business_logo, $data[$key]);
            }
        }

        return $data;
    }

    public function recurringInvoiceNotification($user, $invoice)
    {
        $user->notify(new RecurringInvoiceNotification($invoice));
    }

    public function recurringExpenseNotification($user, $expense)
    {
        $user->notify(new RecurringExpenseNotification($expense));
    }

    public function configureEmail($notificationInfo = [], $check_superadmin = true)
    {
        $email_settings = ! empty($notificationInfo['email_settings']) ? $notificationInfo['email_settings'] : [];
        
        // If email_settings is a JSON string, decode it to array
        if (is_string($email_settings)) {
            $email_settings = json_decode($email_settings, true) ?? [];
        }

        if (empty($email_settings) && session()->has('business')) {
            $email_settings = request()->session()->get('business.email_settings');
            // Also decode if it's a string from session
            if (is_string($email_settings)) {
                $email_settings = json_decode($email_settings, true) ?? [];
            }
        }

        $is_superadmin_settings_allowed = System::getProperty('allow_email_settings_to_businesses');

        //Check if prefered email setting is superadmin email settings
        if (! empty($is_superadmin_settings_allowed) && ! empty($email_settings['use_superadmin_settings']) && $check_superadmin) {
            $email_settings['mail_driver'] = config('mail.mailers.smtp.transport');
            $email_settings['mail_host'] = config('mail.mailers.smtp.host');
            $email_settings['mail_port'] = config('mail.mailers.smtp.port');
            $email_settings['mail_username'] = config('mail.mailers.smtp.username');
            $email_settings['mail_password'] = config('mail.mailers.smtp.password');
            $email_settings['mail_encryption'] = config('mail.mailers.smtp.encryption');
            $email_settings['mail_from_address'] = config('mail.mailers.smtp.address');
        }
        
        // IMPORTANT: If business email_settings are empty/null, use current config from .env
        // Don't overwrite valid config with empty business settings
        if (empty($email_settings) || (empty($email_settings['mail_host']) && empty($email_settings['mail_driver']))) {
            // Use current config values (from .env/config)
            $current_config = config('mail');
            if (!empty($current_config['mailers']['smtp']['host'])) {
                $email_settings['mail_driver'] = $current_config['default'] ?? 'smtp';
                $email_settings['mail_host'] = $current_config['mailers']['smtp']['host'];
                $email_settings['mail_port'] = $current_config['mailers']['smtp']['port'] ?? 587;
                $email_settings['mail_username'] = $current_config['mailers']['smtp']['username'] ?? '';
                $email_settings['mail_password'] = $current_config['mailers']['smtp']['password'] ?? '';
                $email_settings['mail_encryption'] = $current_config['mailers']['smtp']['encryption'] ?? 'tls';
                $email_settings['mail_from_address'] = $current_config['from']['address'] ?? '';
                $email_settings['mail_from_name'] = $current_config['from']['name'] ?? '';
            }
        }

        // Fallback to .env settings if business email settings are empty
        if (empty($email_settings['mail_host'])) {
            $email_settings['mail_host'] = env('MAIL_HOST');
            $email_settings['mail_port'] = env('MAIL_PORT');
            $email_settings['mail_username'] = env('MAIL_USERNAME');
            $email_settings['mail_password'] = env('MAIL_PASSWORD');
            $email_settings['mail_encryption'] = env('MAIL_ENCRYPTION');
            $email_settings['mail_from_address'] = env('MAIL_FROM_ADDRESS');
            $email_settings['mail_from_name'] = env('MAIL_FROM_NAME');
            $email_settings['mail_driver'] = env('MAIL_MAILER');
            
            // If still empty, try from config
            if (empty($email_settings['mail_host'])) {
                $email_settings['mail_host'] = config('mail.mailers.smtp.host');
                $email_settings['mail_port'] = config('mail.mailers.smtp.port');
                $email_settings['mail_username'] = config('mail.mailers.smtp.username');
                $email_settings['mail_password'] = config('mail.mailers.smtp.password');
                $email_settings['mail_encryption'] = config('mail.mailers.smtp.encryption');
                $email_settings['mail_from_address'] = config('mail.from.address');
                $email_settings['mail_from_name'] = config('mail.from.name');
                $email_settings['mail_driver'] = config('mail.default');
            }
        }

        // Ensure we have valid defaults - host MUST never be null
        $mail_driver = ! empty($email_settings['mail_driver']) ? $email_settings['mail_driver'] : 'smtp';
        $mail_host = ! empty($email_settings['mail_host']) ? trim($email_settings['mail_host']) : 'smtp.mailgun.org';
        $mail_port = ! empty($email_settings['mail_port']) ? $email_settings['mail_port'] : 587;
        $mail_username = $email_settings['mail_username'] ?? '';
        $mail_password = $email_settings['mail_password'] ?? '';
        $mail_encryption = ! empty($email_settings['mail_encryption']) ? $email_settings['mail_encryption'] : 'tls';
        $mail_from_address = ! empty($email_settings['mail_from_address']) ? $email_settings['mail_from_address'] : 'hello@example.com';
        $mail_from_name = ! empty($email_settings['mail_from_name']) ? $email_settings['mail_from_name'] : 'Example';
        
        // Final safety check - host must never be empty
        if (empty($mail_host)) {
            $mail_host = 'smtp.mailgun.org';
        }
        
        // IMPORTANT: Clear cached mailers before setting new config
        // This ensures MailManager picks up the new configuration
        \Illuminate\Support\Facades\Mail::forgetMailers();
        
        // Configure mail using Laravel's newer mailers format
        $mail_config = config('mail');
        $mail_config['default'] = $mail_driver;
        $mail_config['mailers']['smtp'] = [
            'transport' => 'smtp',
            'host' => $mail_host,
            'port' => $mail_port,
            'username' => $mail_username,
            'password' => $mail_password,
            'encryption' => $mail_encryption,
            'timeout' => null,
            'auth_mode' => null,
        ];
        $mail_config['from']['address'] = $mail_from_address;
        $mail_config['from']['name'] = $mail_from_name;
        
        // Set the entire mail config at once
        Config::set('mail', $mail_config);
        
        // Also set individual keys for compatibility
        Config::set('mail.default', $mail_driver);
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $mail_host);
        Config::set('mail.mailers.smtp.port', $mail_port);
        Config::set('mail.mailers.smtp.username', $mail_username);
        Config::set('mail.mailers.smtp.password', $mail_password);
        Config::set('mail.mailers.smtp.encryption', $mail_encryption);

        // Also set the old format for backward compatibility
        Config::set('mail.driver', $mail_driver);
        Config::set('mail.host', $mail_host);
        Config::set('mail.port', $mail_port);
        Config::set('mail.username', $mail_username);
        Config::set('mail.password', $mail_password);
        Config::set('mail.encryption', $mail_encryption);

        Config::set('mail.from.address', $mail_from_address);
        Config::set('mail.from.name', $mail_from_name);
    }

    public function replaceHmsBookingTags($data, $transaction, $adults, $childrens, $customer){
        
        $business = Business::findOrFail($transaction->business_id);

        foreach ($data as $key => $value) {
            //Replace contact name
            if (strpos($value, '{customer_name}') !== false) {
                $data[$key] = str_replace('{customer_name}',$customer->name , $data[$key]);
            }

            //Replace business name
             if (strpos($value, '{business_name}') !== false) {
                $data[$key] = str_replace('{business_name}',$business->name, $data[$key]);
            }
            //Replace business name
            if (strpos($value, '{business_name}') !== false) {
                $data[$key] = str_replace('{business_name}',$business->name, $data[$key]);
            }
             //Replace business_logo
             if (strpos($value, '{business_logo}') !== false) {
                $logo_name = $business->logo;
                $business_logo = ! empty($logo_name) ? '<img src="'.url('storage/business_logos/'.$logo_name).'" alt="Business Logo" >' : '';
                $data[$key] = str_replace('{business_logo}', $business_logo, $data[$key]);
            }

            //Replace business id
             if (strpos($value, '{booking_id}') !== false) {
                $data[$key] = str_replace('{booking_id}',$transaction->ref_no, $data[$key]);
            }

            //Replace business status
            if (strpos($value, '{booking_status}') !== false) {
                $data[$key] = str_replace('{booking_status}',$transaction->status, $data[$key]);
            }

            //Replace arrival date
            if (strpos($value, '{arrival_date}') !== false) {

                $start_date = $this->format_date($transaction->hms_booking_arrival_date_time, true);
                
                $data[$key] = str_replace('{arrival_date}',$start_date, $data[$key]);
            }

            //Replace arrival date
            if (strpos($value, '{departure_date}') !== false) {
                $end_time = $this->format_date($transaction->hms_booking_departure_date_time, true);
                $data[$key] = str_replace('{departure_date}',$end_time, $data[$key]);
            }

            //Replace adults
            if (strpos($value, '{adults}') !== false) {
            $data[$key] = str_replace('{adults}',$adults, $data[$key]);
            }
            //Replace childrens
            if (strpos($value, '{childrens}') !== false) {
                $data[$key] = str_replace('{childrens}',$childrens, $data[$key]);
            }

        }

        return $data;
    }

    /**
     * Automatically send brand notification to customer if enabled
     * Complete function that handles everything internally
     * Failsafe: Works even if custom_data is null - uses user and contact objects
     *
     * @param  int  $business_id
     * @param  string  $notification_type
     * @param  mixed  $custom_data - Array containing transaction, product, user, or other data (can be null)
     * @param  obj  $contact - Contact/Customer object (always required)
     * @param  int  $brand_id
     * @return bool
     */
    public function autoSendNotificationBrand($business_id, $notification_type, $custom_data, $contact, $brand_id, $transaction = null)
    {
        try {
            // Ensure custom_data is an array
            if (is_object($custom_data) && !($custom_data instanceof \Illuminate\Database\Eloquent\Model)) {
                $custom_data = json_decode(json_encode($custom_data), true);
            }
            if (!is_array($custom_data)) {
                $custom_data = [];
            }

            // Get brand configuration
            $brandConfig = BrandConfig::where('brand_id', $brand_id)->first();
            
            if (empty($brandConfig)) {
                return false;
            }

            // Get brand and business details
            $brand = Brands::findOrFail($brand_id);
            $business = Business::findOrFail($business_id);

            // Get template settings for this notification type
            $template = null;
            if (!empty($brandConfig->template_settings) && is_array($brandConfig->template_settings)) {
                foreach ($brandConfig->template_settings as $tpl) {
                    if (isset($tpl['template_type']) && $tpl['template_type'] === $notification_type) {
                        $template = $tpl;
                        break;
                    }
                }
            }

            if (empty($template) || (empty($template['template_body']) && empty($template['subject']))) {
                return false;
            }
            
            // Extract email from custom_data (handle both array and object)
            $to_email = null;
            if (is_array($custom_data) && isset($custom_data['email'])) {
                $to_email = $custom_data['email'];
            } elseif (is_object($custom_data) && isset($custom_data->email)) {
                $to_email = $custom_data->email;
            }
            
            // Fallback to contact email
            if (empty($to_email) && !empty($contact->email)) {
                $to_email = $contact->email;
            }

            // Check if we have an email to send to
            if (empty($to_email)) {
                return false;
            }
            
            

            // Prepare data for tag replacement
            $orig_data = [
                'email_body' => $template['template_body'] ?? '',
                'subject' => $template['subject'] ?? '',
            ];

            // Replace tags with dynamic data
            $tag_replaced_data = $this->replaceBrandTags($business_id, $brand_id, $orig_data, $custom_data, $contact, $transaction);

            // Prepare email data
            $email_data = [
                'email_body' => $tag_replaced_data['email_body'],
                'subject' => $tag_replaced_data['subject'],
                'to_email' => $to_email,
                'business_id' => $business_id,
            ];
            // Add CC if provided
            if (!empty($template['cc'])) {
                $email_data['cc'] = $template['cc'];
            }

            // Add BCC if provided
            if (!empty($template['bcc'])) {
                $email_data['bcc'] = $template['bcc'];
            }

            // Configure email settings from brand config
            $email_settings = $brandConfig->email_settings ?? [];
            
            if (!empty($email_settings) && is_array($email_settings)) {
                $email_data['email_settings'] = $email_settings;
                $this->configureBrandEmail($email_settings);
            } else {
                // Fallback to business email settings
                $email_data['email_settings'] = $business->email_settings;
                $this->configureEmail($email_data, true);
            }

            // Send email notification
            Notification::route('mail', $email_data['to_email'])
                ->notify(new CustomerNotification($email_data));

            // Log activity if transaction is available
            if (!empty($transaction)) {
                $this->activityLog($transaction, 'brand_email_notification_sent', null, [], false, $business_id);
            }
            return true;

        } catch (\Exception $e) {
            Log::error('BrandNotification: Failed to send notification', [
                'brand_id' => $brand_id,
                'notification_type' => $notification_type,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            
            return false;
        }
    }

    /**
     * Configure email settings for brand
     *
     * @param  array  $email_settings
     * @return void
     * @throws \Exception if email configuration is invalid
     */
    private function configureBrandEmail($email_settings)
    {
        $mail_driver = !empty($email_settings['mail_driver']) ? $email_settings['mail_driver'] : 'smtp';
        $mail_host = !empty($email_settings['mail_host']) ? trim($email_settings['mail_host']) : '';
        $mail_port = !empty($email_settings['mail_port']) ? $email_settings['mail_port'] : 587;
        $mail_username = !empty($email_settings['mail_username']) ? $email_settings['mail_username'] : '';
        $mail_password = !empty($email_settings['mail_password']) ? $email_settings['mail_password'] : '';
        $mail_from_address = !empty($email_settings['mail_from_address']) ? $email_settings['mail_from_address'] : '';
        $mail_from_name = !empty($email_settings['mail_from_name']) ? $email_settings['mail_from_name'] : config('mail.from.name');
        $mail_encryption = !empty($email_settings['mail_encryption']) ? $email_settings['mail_encryption'] : 'tls';

        // Validate required fields
        if (empty($mail_host)) {
            throw new \Exception('Email configuration error: Mail host is required but not configured for this brand.');
        }

        if (empty($mail_from_address)) {
            throw new \Exception('Email configuration error: From address is required but not configured for this brand.');
        }

        // Validate email format
        if (!filter_var($mail_from_address, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Email configuration error: Invalid from email address format.');
        }

        // Clear any cached mailer instances
        \Illuminate\Support\Facades\Mail::forgetMailers();
        
        // Configure mail using Laravel's mailers format
        $mail_config = config('mail');
        $mail_config['default'] = $mail_driver;
        $mail_config['mailers']['smtp'] = [
            'transport' => 'smtp',
            'host' => $mail_host,
            'port' => $mail_port,
            'encryption' => $mail_encryption,
            'username' => $mail_username,
            'password' => $mail_password,
            'timeout' => 30,
            'auth_mode' => null,
        ];
        $mail_config['from'] = [
            'address' => $mail_from_address,
            'name' => $mail_from_name,
        ];

        Config::set('mail', $mail_config);
    }

    /**
     * Test brand email configuration
     *
     * @param  int  $brand_id
     * @param  string  $test_email
     * @return array
     */
    public function testBrandEmailConfiguration($brand_id, $test_email)
    {
        try {
            // Validate email format
            if (!filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'msg' => 'Invalid email address format.',
                ];
            }

            // Get brand configuration
            $brandConfig = BrandConfig::where('brand_id', $brand_id)->first();
            
            if (empty($brandConfig)) {
                return [
                    'success' => false,
                    'msg' => 'Brand configuration not found.',
                ];
            }

            $email_settings = $brandConfig->email_settings ?? [];
            
            if (empty($email_settings) || empty($email_settings['mail_host'])) {
                return [
                    'success' => false,
                    'msg' => 'Email settings not configured for this brand. Please configure SMTP settings first.',
                ];
            }

            // Configure brand email
            $this->configureBrandEmail($email_settings);

            // Prepare test email data
            $email_data = [
                'email_body' => '<p>This is a test email from Brand Email Configuration.</p><p>If you received this, your email settings are working correctly!</p>',
                'subject' => 'Test Email - Brand Configuration',
                'to_email' => $test_email,
            ];

            // Send test email
            Notification::route('mail', $test_email)
                ->notify(new CustomerNotification($email_data));

            return [
                'success' => true,
                'msg' => 'Test email sent successfully! Please check your inbox.',
            ];

        } catch (\Exception $e) {
            Log::error('BrandNotification: Test email failed', [
                'brand_id' => $brand_id,
                'test_email' => $test_email,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $error_msg = $e->getMessage();
            
            // Provide user-friendly error messages for common issues
            if (strpos($error_msg, 'getaddrinfo') !== false || strpos($error_msg, 'No such host') !== false) {
                $error_msg = 'DNS Error: Cannot resolve mail server hostname. Please check your internet connection and mail host configuration.';
            } elseif (strpos($error_msg, 'Connection refused') !== false) {
                $error_msg = 'Connection Error: Mail server refused connection. Please check if the host and port are correct.';
            } elseif (strpos($error_msg, 'Connection timed out') !== false) {
                $error_msg = 'Timeout Error: Mail server is not responding. Please check your firewall settings and port configuration.';
            } elseif (strpos($error_msg, 'Authentication') !== false) {
                $error_msg = 'Authentication Error: Invalid username or password. Please check your credentials.';
            }

            return [
                'success' => false,
                'msg' => $error_msg,
                'technical_details' => $e->getMessage(),
            ];
        }
    }

     public function sendPushNotification($title, $message, $user_id, $data = [], $priority = 'non_urgent')
    {
        try {
            Log::info("NotificationUtil: Sending push notification", [
                "title" => $title,
                "user_id" => $user_id,
                "data" => $data
            ]);

            // Extract order_id and order_type from data array
            $orderId = $data['order_id'] ?? null;
            $orderType = $data['order_type'] ?? null;
            
            // If order_id is provided but order_type is not, try to fetch it from the transaction
            if ($orderId && !$orderType) {
                try {
                    $transaction = \App\Transaction::find($orderId);
                    if ($transaction && isset($transaction->type)) {
                        $orderType = $transaction->type;
                    }
                } catch (\Exception $e) {
                    // Silently fail if transaction not found
                    Log::debug("Could not fetch order_type for order_id: {$orderId}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Save message to database first
            $sentMessage = \App\Models\SentMessage::create([
                'title' => $title,
                'message' => $message,
                'user_id' => $user_id,
                'order_id' => $orderId,
                'order_type' => $orderType,
                'status' => 'unread',
                'priority' => $priority,
                'deleted' => false
            ]);

            // Get all active tokens for the user
            $allTokens = \App\Models\FirebasePushNotification::where("user_id", $user_id)
                ->where("is_active", true)
                ->pluck("token")
                ->toArray();

            if (empty($allTokens)) {
                Log::warning("NotificationUtil: No active tokens found for user/contact", [
                    "user_id" => $user_id,
                    "hint" => "For ECOM customers, ensure the app registers the push token with the contact id (same as transaction.contact_id). Token API accepts both users.id and contacts.id."
                ]);
                
                return [
                    "success" => false,
                    "message" => "No active tokens found for user",
                    "user_id" => $user_id,
                    "message_id" => $sentMessage->id
                ];
            }

            // Separate Expo tokens from FCM tokens
            $expoTokens = [];
            $fcmTokens = [];
            
            foreach ($allTokens as $token) {
                if ($this->isExpoToken($token)) {
                    $expoTokens[] = $token;
                } else {
                    $fcmTokens[] = $token;
                }
            }

            // Prepare notification payload
            $notificationPayload = [
                "title" => $title,
                "body" => $message
            ];

            $dataPayload = array_merge($data, [
                "notification_type" => "general",
                "timestamp" => now()->toISOString(),
                "user_id" => (string)$user_id,
                "sound" => "default",
                "badge" => "1",
                "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                "message_id" => (string)$sentMessage->id // Include message ID for tracking
            ]);

            $results = [
                "expo" => null,
                "fcm" => null
            ];
            $totalTokensSent = 0;

            // Send Expo notifications if any Expo tokens exist
            if (!empty($expoTokens)) {
                Log::info("NotificationUtil: Sending Expo notifications", [
                    "expo_tokens_count" => count($expoTokens)
                ]);
                $results["expo"] = $this->sendExpoNotification($expoTokens, $notificationPayload, $dataPayload);
                $totalTokensSent += count($expoTokens);
                // Deactivate Expo tokens that returned DeviceNotRegistered (app uninstalled / token expired)
                if (!empty($results["expo"]["results"]) && is_array($results["expo"]["results"])) {
                    foreach ($results["expo"]["results"] as $idx => $receipt) {
                        if (isset($receipt["status"]) && $receipt["status"] === "error") {
                            $details = $receipt["details"] ?? [];
                            $error = is_array($details) ? ($details["error"] ?? null) : null;
                            if ($error === "DeviceNotRegistered" && isset($expoTokens[$idx])) {
                                $invalidToken = $expoTokens[$idx];
                                \App\Models\FirebasePushNotification::where("user_id", $user_id)
                                    ->where("token", $invalidToken)
                                    ->update(["is_active" => false]);
                                Log::warning("NotificationUtil: Deactivated invalid Expo token (DeviceNotRegistered)", [
                                    "user_id" => $user_id,
                                    "token_preview" => substr($invalidToken, 0, 20) . "...",
                                    "expo_message" => $receipt["message"] ?? null
                                ]);
                            }
                        }
                    }
                }
            }

            // Send FCM notifications if any FCM tokens exist (existing flow)
            if (!empty($fcmTokens)) {
                Log::info("NotificationUtil: Sending FCM notifications", [
                    "fcm_tokens_count" => count($fcmTokens)
                ]);
                $results["fcm"] = $this->sendFCMNotification($fcmTokens, $notificationPayload, $dataPayload);
                $totalTokensSent += count($fcmTokens);
            }

            // High-level summary of which channels were actually used
            $channels = [];
            if (!empty($expoTokens)) {
                $channels[] = 'expo';
            }
            if (!empty($fcmTokens)) {
                $channels[] = 'fcm';
            }

            Log::info("NotificationUtil: Push notification sent successfully", [
                "title" => $title,
                "user_id" => $user_id,
                "channels_used" => $channels,
                "expo_tokens_count" => count($expoTokens),
                "fcm_tokens_count" => count($fcmTokens),
                "total_tokens_sent" => $totalTokensSent,
                "results" => $results,
                "message_id" => $sentMessage->id
            ]);

            return [
                "success" => true,
                "message" => "Push notification sent successfully",
                "user_id" => $user_id,
                "tokens_sent" => $totalTokensSent,
                "expo_tokens_count" => count($expoTokens),
                "fcm_tokens_count" => count($fcmTokens),
                "response" => $results,
                "message_id" => $sentMessage->id
            ];

        } catch (\Exception $e) {
            Log::error("NotificationUtil: Failed to send push notification", [
                "title" => $title,
                "message" => $message,
                "user_id" => $user_id,
                "error" => $e->getMessage()
            ]);

            return [
                "success" => false,
                "message" => "Failed to send push notification: " . $e->getMessage(),
                "user_id" => $user_id
            ];
        }
    }

    /**
     * Check if a token is an Expo push token
     *
     * @param string $token
     * @return bool
     */
    private function isExpoToken($token)
    {
        return strpos($token, 'ExponentPushToken[') === 0;
    }

    /**
     * Send notification via Expo Push Notification API
     *
     * @param array $tokens Array of Expo push tokens
     * @param array $notification Notification payload with title and body
     * @param array $data Additional data payload
     * @return array
     */
    private function sendExpoNotification($tokens, $notification, $data)
    {
        try {
            $url = "https://exp.host/--/api/v2/push/send";
            
            // Prepare messages for Expo API
            // Expo API accepts multiple messages in a single request
            $messages = [];
            
            foreach ($tokens as $token) {
                // Expo expects the full ExponentPushToken[...] value.
                // Do NOT strip the wrapper; send exactly what the app provided.
                $expoToken = $token;

                $message = [
                    "to" => $expoToken,
                    "sound" => "default",
                    "title" => $notification["title"] ?? "",
                    "body" => $notification["body"] ?? "",
                    "data" => $data,
                    "badge" => isset($data["badge"]) ? (int)$data["badge"] : 1,
                    "priority" => "default",
                    "channelId" => "default"
                ];
                
                // Add Android-specific options
                if (isset($data["click_action"])) {
                    $message["data"]["click_action"] = $data["click_action"];
                }
                
                $messages[] = $message;
            }

            // Expo API accepts up to 100 messages per request
            // Split into batches if needed
            $batchSize = 100;
            $batches = array_chunk($messages, $batchSize);
            $allResults = [];
            
            foreach ($batches as $batch) {
                $batchSize = count($batch);
                $headers = [
                    "Content-Type: application/json",
                    "Accept: application/json",
                    "Accept-Encoding: gzip, deflate"
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($batch));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);

                if ($error) {
                    Log::error("NotificationUtil: Expo API CURL error", [
                        "error" => $error,
                        "batch_size" => $batchSize
                    ]);
                    $allResults[] = ["error" => $error, "batch_size" => $batchSize];
                } else {
                    $result = json_decode($response, true);
                    if ($httpCode !== 200) {
                        Log::warning("NotificationUtil: Expo API request failed", [
                            "http_code" => $httpCode,
                            "response" => $response,
                            "batch_size" => $batchSize
                        ]);
                        $allResults[] = ["error" => "HTTP {$httpCode}", "response" => $result, "batch_size" => $batchSize];
                    } else {
                        $allResults[] = ["result" => $result, "batch_size" => $batchSize];
                    }
                }
            }

            // Combine results from all batches
            $successCount = 0;
            $failureCount = 0;
            $combinedResults = [];
            
            foreach ($allResults as $batchResult) {
                if (isset($batchResult["error"])) {
                    $failureCount += $batchResult["batch_size"] ?? 0;
                } elseif (isset($batchResult["result"]) && is_array($batchResult["result"])) {
                    // Expo API returns { "data": [ receipt, ... ] } - one receipt per message
                    $receipts = isset($batchResult["result"]["data"]) ? $batchResult["result"]["data"] : $batchResult["result"];
                    if (!is_array($receipts) || isset($receipts["status"])) {
                        $receipts = [$batchResult["result"]];
                    }
                    foreach ($receipts as $result) {
                        $combinedResults[] = $result;
                        if (isset($result["status"]) && $result["status"] === "ok") {
                            $successCount++;
                        } else {
                            $failureCount++;
                        }
                    }
                }
            }

            if ($failureCount > 0 && !empty($combinedResults)) {
                foreach ($combinedResults as $i => $r) {
                    if (isset($r["status"]) && $r["status"] === "error") {
                        Log::warning("NotificationUtil: Expo delivery failed", [
                            "index" => $i,
                            "message" => $r["message"] ?? null,
                            "details" => $r["details"] ?? null,
                            "hint" => "DeviceNotRegistered = token expired or app uninstalled; token will be deactivated so user can re-register."
                        ]);
                        break;
                    }
                }
            }
            Log::info("NotificationUtil: Expo notifications sent", [
                "tokens_count" => count($tokens),
                "success_count" => $successCount,
                "failure_count" => $failureCount
            ]);

            return [
                "success" => $successCount,
                "failure" => $failureCount,
                "results" => $combinedResults
            ];

        } catch (\Exception $e) {
            Log::error("NotificationUtil: Failed to send Expo notification", [
                "error" => $e->getMessage(),
                "tokens_count" => count($tokens)
            ]);

            return [
                "success" => 0,
                "failure" => count($tokens),
                "error" => $e->getMessage()
            ];
        }
    }

    /**
     * Send notification via Firebase Cloud Messaging (HTTP v1)
     *
     * @param array $tokens
     * @param array $notification
     * @param array $data
     * @return array
     */
    private function sendFCMNotification($tokens, $notification, $data)
    {
        // Get Firebase credentials from config
        $credentialsPath = config("services.firebase.credentials_path");
        
        // Check if credentials file exists (use absolute path)
        $fullCredentialsPath = base_path($credentialsPath);
        
        if (!$credentialsPath || !file_exists($fullCredentialsPath)) {
            Log::warning("Firebase credentials file not found, falling back to legacy API", [
                "credentials_path" => $credentialsPath,
                "full_path" => $fullCredentialsPath,
                "file_exists" => file_exists($fullCredentialsPath)
            ]);
            // Fallback to legacy API if no credentials file
            return $this->sendLegacyFCMNotification($tokens, $notification, $data);
        }

        Log::info("Using Firebase FCM HTTP v1 API", ["credentials_path" => $fullCredentialsPath]);

        try {
            // Get OAuth2 access token
            $accessToken = $this->getFCMAccessToken($fullCredentialsPath);
            
            // Use HTTP v1 endpoint
            $projectId = config("services.firebase.project_id");
            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
            
            $results = [];
            foreach ($tokens as $token) {
                $message = [
                    "message" => [
                        "token" => $token,
                        "notification" => $notification,
                        "data" => array_map('strval', $data), // Convert all data values to strings
                        "android" => [
                            "priority" => "high",
                            "notification" => [
                                "sound" => "default",
                                "click_action" => "FLUTTER_NOTIFICATION_CLICK"
                            ]
                        ],
                        "apns" => [
                            "payload" => [
                                "aps" => [
                                    "sound" => "default",
                                    "badge" => 1
                                ]
                            ]
                        ]
                    ]
                ];

                $headers = [
                    "Authorization: Bearer " . $accessToken,
                    "Content-Type: application/json"
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                
                $response = curl_exec($ch);
                $error = curl_error($ch);
                curl_close($ch);

                if ($error) {
                    $results[] = ["error" => $error];
                } else {
                    $result = json_decode($response, true);
                    $results[] = $result;
                }
            }

            return [
                "multicast_id" => uniqid(),
                "success" => count(array_filter($results, fn($r) => !isset($r["error"]))),
                "failure" => count(array_filter($results, fn($r) => isset($r["error"]))),
                "results" => $results
            ];

        } catch (\Exception $e) {
            // Fallback to legacy if v1 fails
            Log::warning("FCM v1 failed, falling back to legacy", ["error" => $e->getMessage()]);
            return $this->sendLegacyFCMNotification($tokens, $notification, $data);
        }
    }

    /**
     * Get OAuth2 access token for FCM HTTP v1
     */
    private function getFCMAccessToken($credentialsPath)
    {
        $credentials = json_decode(file_get_contents($credentialsPath), true);
        
        // Create JWT for OAuth2
        $header = json_encode(["alg" => "RS256", "typ" => "JWT"]);
        $now = time();
        $payload = json_encode([
            "iss" => $credentials["client_email"],
            "scope" => "https://www.googleapis.com/auth/firebase.messaging",
            "aud" => $credentials["token_uri"],
            "exp" => $now + 3600,
            "iat" => $now
        ]);

        $base64UrlHeader = str_replace(["+", "/", "="], ["-", "_", ""], base64_encode($header));
        $base64UrlPayload = str_replace(["+", "/", "="], ["-", "_", ""], base64_encode($payload));
        
        $privateKey = $credentials["private_key"];
        openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        $base64UrlSignature = str_replace(["+", "/", "="], ["-", "_", ""], base64_encode($signature));
        
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
        
        // Exchange JWT for access token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $credentials["token_uri"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
            "assertion" => $jwt
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $tokenData = json_decode($response, true);
        return $tokenData["access_token"];
    }

    /**
     * Legacy FCM notification method (fallback)
     */
    private function sendLegacyFCMNotification($tokens, $notification, $data)
    {
        $serverKey = config("services.firebase.server_key");
        
        if (!$serverKey) {
            throw new \Exception("Firebase server key not configured and no v1 credentials available");
        }

        $url = "https://fcm.googleapis.com/fcm/send";
        
        $fields = [
            "registration_ids" => $tokens,
            "notification" => $notification,
            "data" => $data,
            "priority" => "high"
        ];

        $headers = [
            "Authorization: key=" . $serverKey,
            "Content-Type: application/json"
        ];

        Log::info("NotificationUtil: Using Legacy FCM API", [
            "url" => $url,
            "tokens_count" => count($tokens)
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception("CURL error: " . $error);
        }

        $result = json_decode($response, true);

        if ($httpCode !== 200) {
            throw new \Exception("Legacy FCM request failed with HTTP code: " . $httpCode . ", Response: " . $response);
        }

        return $result;
    }

    // Keep all the existing email notification methods below...
    // (Your existing email notification code continues here)
}



