<?php

namespace App\Jobs;

use App\Utils\NotificationUtil;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

    /**
     * SendNotificationJob class is used to send notifications to the user
     * Required parameters are is_custom, business_id, notification_type, user, contact
     * Optional parameters are transaction, custom_data
     * @param bool $is_custom arg1
     * @param int $business_id arg2
     * @param string $notification_type arg3
     * @param object $user arg4
     * @param object $contact arg5
     * @param object|null $transaction arg6
     * @param array|null $custom_data arg7
     * @return void
     */
class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $business_id;
    protected $notification_type;
    protected $transaction;
    protected $contact;
    protected $custom_data;
    protected $is_custom;
    protected $user;
    /**
     * Create a new job instance.
     * @param bool $is_custom arg1
     * @param int $business_id arg2
     * @param string $notification_type arg3
     * @param object $user arg4
     * @param object $contact arg5
     * @param object|null $transaction arg6
     * @param array|null $custom_data arg7
     * @return void
     */
    public function __construct($is_custom, $business_id, $notification_type, $user, $contact, $transaction = null, $custom_data = null)
    {
        $this->is_custom = $is_custom;
        $this->business_id = $business_id;
        $this->notification_type = $notification_type;
        $this->user = $user;
        $this->contact = $contact;
        $this->transaction = $transaction;
        $this->custom_data = $custom_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(NotificationUtil $notificationUtil)
    {
        try {           
            $whatsapp_link = null;
            
            // Check if this is a B2C notification (user or contact has is_b2c flag, or custom_data has brand_id)
            $is_b2c = false;
            $brand_id = null;
            
            if ($this->user && isset($this->user->is_b2c) && $this->user->is_b2c) {
                $is_b2c = true;
                $brand_id = $this->user->brand_id ?? null;
            } elseif ($this->contact && isset($this->contact->is_b2c) && $this->contact->is_b2c) {
                $is_b2c = true;
                $brand_id = $this->contact->brand_id ?? null;
            } elseif (!empty($this->custom_data)) {
                // Check if custom_data has brand_id (for guest orders)
                $custom_data_array = is_array($this->custom_data) ? $this->custom_data : (is_object($this->custom_data) ? json_decode(json_encode($this->custom_data), true) : []);
                if (isset($custom_data_array['brand_id']) && isset($custom_data_array['is_b2c']) && $custom_data_array['is_b2c']) {
                    $is_b2c = true;
                    $brand_id = $custom_data_array['brand_id'];
                }
            }
            
            if ($is_b2c && !empty($brand_id)) {
                // Build custom_data array - merge all available data
                $custom_data = [];
                
                // Add user data
                if ($this->user) {
                    $custom_data['user'] = $this->user;
                }
                // Add transaction if available
                if ($this->transaction) {
                    $custom_data['transaction'] = $this->transaction;
                }
                if (!empty($this->contact->cupon_code)) {
                    $custom_data['cupon_code'] = $this->contact->cupon_code;
                }
                
                // Merge any additional custom_data that was passed
                if (!empty($this->custom_data)) {
                    if (is_array($this->custom_data)) {
                        $custom_data = array_merge($custom_data, $this->custom_data);
                    } elseif (is_object($this->custom_data)) {
                        // Convert object to array and merge
                        $custom_data = array_merge($custom_data, json_decode(json_encode($this->custom_data), true));
                    }
                }
                
                $whatsapp_link = $notificationUtil->autoSendNotificationBrand(
                    $this->business_id, 
                    $this->notification_type, 
                    $custom_data,
                    $this->contact,
                    $brand_id,
                    $this->transaction
                );
            } elseif ($this->is_custom) {
                // Build template data: user attributes + custom_data (e.g. OTP) so tag replacement works after queue
                $customDataForTemplate = $this->user;
                if (!empty($this->custom_data) && is_array($this->custom_data) && $this->user) {
                    $customDataForTemplate = new \stdClass();
                    foreach (['name', 'email', 'remember_token', 'ref_no', 'brand_id'] as $attr) {
                        $customDataForTemplate->$attr = $this->user->$attr ?? null;
                    }
                    foreach ($this->custom_data as $key => $value) {
                        $customDataForTemplate->$key = $value;
                    }
                }
                $whatsapp_link = $notificationUtil->autoSendNotificationCustom(
                    $this->business_id, 
                    $this->notification_type, 
                    $customDataForTemplate, 
                    $this->contact
                );
            } else {
                // Use regular autoSendNotification for transactions
                $whatsapp_link = $notificationUtil->autoSendNotification(
                    $this->business_id, 
                    $this->notification_type, 
                    $this->transaction, 
                    $this->contact
                );
            }           
            return $whatsapp_link;
        } catch (\Exception $e) {
            Log::error("Notification job failed: {$this->notification_type}", [
                'error' => $e->getMessage(),
                'business_id' => $this->business_id,
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        $identifier = $this->transaction ? $this->transaction->id : 'custom';
        Log::error("{$this->notification_type} notification job failed for: {$identifier} - Error: " . $exception->getMessage());
    }
} 