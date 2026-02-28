<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\NotificationTemplate;
use App\User;
use App\Contact;

class FireBaseServices
{
   
    public function sendNotification($payload)
    {
        try{
            $FCM_KEY = config('services.firebase.key');
            $headers = [
                'Authorization' => 'key=' . $FCM_KEY,
                'Content-Type' => 'application/json'
            ];
                $response = Http::withHeaders($headers)->post('https://fcm.googleapis.com/fcm/send', $payload);
                return $response->json();
        }catch(\Exception $e){
            Log::error('Error sending notification at FireBaseServices.php line 45: ' . $e->getMessage());
            return false;
        }
    }

    
    public function newProductNotification($product)
    {
        $notification = NotificationTemplate::where('notification_type', 'newProduct')->first();
        if(!$notification){
            return false;
        }
    }

    /**
     * Send notification to picker when order is assigned
     */
    public function sendOrderAssignmentNotification($pickerId, $orderIds, $businessId)
    {
        try {
            // Get the picker user
            $picker = User::where('id', $pickerId)
                         ->where('business_id', $businessId)
                         ->first();
            
            if (!$picker) {
                Log::error('Picker not found for ID: ' . $pickerId);
                return false;
            }

            // Get notification template for order assignment
            $notificationTemplate = NotificationTemplate::where('business_id', $businessId)
                                                       ->where('template_for', 'order_assignment')
                                                       ->first();

            // If no template exists, create a default one
            if (!$notificationTemplate) {
                $notificationTemplate = $this->createDefaultOrderAssignmentTemplate($businessId);
            }

            // Get order details
            $orders = \App\Transaction::whereIn('id', $orderIds)
                                     ->where('business_id', $businessId)
                                     ->with('contact')
                                     ->get();

            if ($orders->isEmpty()) {
                Log::error('No orders found for IDs: ' . implode(',', $orderIds));
                return false;
            }

            // Prepare notification data
            $orderCount = count($orders);
            $firstOrder = $orders->first();
            
            $notificationTitle = str_replace(
                ['{picker_name}', '{order_count}', '{first_order_number}'],
                [$picker->first_name . ' ' . $picker->last_name, $orderCount, $firstOrder->invoice_no ?? 'N/A'],
                $notificationTemplate->subject ?? 'New Order Assignment'
            );

            $notificationBody = str_replace(
                ['{picker_name}', '{order_count}', '{first_order_number}', '{customer_name}'],
                [
                    $picker->first_name . ' ' . $picker->last_name, 
                    $orderCount, 
                    $firstOrder->invoice_no ?? 'N/A',
                    $firstOrder->contact->name ?? 'Customer'
                ],
                $notificationTemplate->sms_body ?? 'You have been assigned {order_count} new order(s) to pick.'
            );

            // Prepare FCM payload
            $payload = [
                'to' => $picker->fcmToken ?? null,
                'notification' => [
                    'title' => $notificationTitle,
                    'body' => $notificationBody,
                    'icon' => 'ic_launcher',
                    'sound' => 'default',
                    'badge' => '1'
                ],
                'data' => [
                    'type' => 'order_assignment',
                    'order_ids' => implode(',', $orderIds),
                    'picker_id' => $pickerId,
                    'order_count' => $orderCount,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'status' => 'done'
                ],
                'priority' => 'high'
            ];

            // Send notification if FCM token exists
            if ($picker->fcmToken) {
                $result = $this->sendNotification($payload);
                Log::info('Order assignment notification sent to picker ' . $pickerId . ': ' . json_encode($result));
                return $result;
            } else {
                Log::warning('No FCM token found for picker: ' . $pickerId);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Error sending order assignment notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create default notification template for order assignment
     */
    private function createDefaultOrderAssignmentTemplate($businessId)
    {
        return NotificationTemplate::create([
            'business_id' => $businessId,
            'template_for' => 'order_assignment',
            'subject' => 'New Order Assignment - {order_count} order(s)',
            'sms_body' => 'Hi {picker_name}, you have been assigned {order_count} new order(s) to pick. First order: {first_order_number} for {customer_name}.',
            'email_body' => '<p>Hi {picker_name},</p><p>You have been assigned {order_count} new order(s) to pick.</p><p>First order: {first_order_number} for {customer_name}</p><p>Please start picking immediately.</p>',
            'auto_send' => 1
        ]);
    }

    
}
