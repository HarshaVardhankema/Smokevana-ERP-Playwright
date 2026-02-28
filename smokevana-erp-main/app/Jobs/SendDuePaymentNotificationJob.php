<?php

namespace App\Jobs;

use App\Business;
use App\Contact;
use App\Notifications\CustomerNotification;
use App\NotificationTemplate;
use App\Transaction;
use App\Utils\NotificationUtil;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendDuePaymentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transaction_id;
    protected $business_id;
    protected $delay_minutes;

    /**
     * Create a new job instance.
     *
     * @param int $transaction_id
     * @param int $business_id
     * @param int $delay_minutes
     * @return void
     */
    public function __construct($transaction_id, $business_id, $delay_minutes = 0)
    {
        $this->transaction_id = $transaction_id;
        $this->business_id = $business_id;
        $this->delay_minutes = $delay_minutes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(NotificationUtil $notificationUtil)
    {
        try {
            $transaction = Transaction::with(['contact', 'payment_lines'])
                ->where('id', $this->transaction_id)
                ->where('business_id', $this->business_id)
                ->first();

            if (!$transaction) {
                Log::warning("Transaction {$this->transaction_id} not found for due payment notification");
                return;
            }
            if($transaction){
                $contact=Contact::where('id',$transaction->contact_id)->first();
                if($contact->is_auto_send_due_notification==0){
                    Log::info("Contact {$transaction->contact_id} has auto send due notification disabled, skipping due notification");
                    return;
                }
            }

            // Check if payment is still due/partial
            if (!in_array($transaction->payment_status, ['due', 'partial'])) {
                Log::info("Transaction {$this->transaction_id} payment status is {$transaction->payment_status}, skipping due notification");
                return;
            }

            // Check if payment terms are set
            if (empty($transaction->pay_term_number) || empty($transaction->pay_term_type)) {
                Log::info("Transaction {$this->transaction_id} has no payment terms, skipping due notification");
                return;
            }

            // Get notification template
            $template = NotificationTemplate::where('business_id', $this->business_id)
                ->where('template_for', 'payment_reminder')
                ->first();

            if (!$template) {
                Log::info("No payment reminder template found for business {$this->business_id}");
                return;
            }

            // Check if auto-send is enabled
            if (empty($template->auto_send) && empty($template->auto_send_sms) && empty($template->auto_send_wa_notif)) {
                Log::info("Auto-send is disabled for payment reminder template in business {$this->business_id}");
                return;
            }

            $business = Business::with(['currency'])->where('id', $this->business_id)->first();

            $data = [
                'subject' => $template->subject ?? '',
                'sms_body' => $template->sms_body ?? '',
                'whatsapp_text' => $template->whatsapp_text ?? '',
                'email_body' => $template->email_body ?? '',
                'template_for' => 'payment_reminder',
                'cc' => $template->cc ?? '',
                'bcc' => $template->bcc ?? '',
                'auto_send' => !empty($template->auto_send) ? 1 : 0,
                'auto_send_sms' => !empty($template->auto_send_sms) ? 1 : 0,
                'auto_send_wa_notif' => !empty($template->auto_send_wa_notif) ? 1 : 0,
            ];

            $orig_data = [
                'email_body' => $data['email_body'],
                'sms_body' => $data['sms_body'],
                'subject' => $data['subject'],
                'whatsapp_text' => $data['whatsapp_text'],
            ];

            // Replace tags in notification content
            $tag_replaced_data = $notificationUtil->replaceTags($business, $orig_data, $transaction);

            $data['email_body'] = $tag_replaced_data['email_body'];
            $data['sms_body'] = $tag_replaced_data['sms_body'];
            $data['subject'] = $tag_replaced_data['subject'];
            $data['whatsapp_text'] = $tag_replaced_data['whatsapp_text'];

            $data['email_settings'] = $business->email_settings ?? [];
            $data['sms_settings'] = $business->sms_settings ?? [];

            // Send email notification
            if (!empty($data['auto_send']) && !empty($transaction->contact->email)) {
                try {
                    Notification::route('mail', [$transaction->contact->email])
                        ->notify(new CustomerNotification($data));

                    $notificationUtil->activityLog($transaction, 'payment_reminder', null, [
                        'email' => $transaction->contact->email, 
                        'is_automatic' => true,
                        'sent_at' => now()
                    ], false, $this->business_id);

                    Log::info("Due payment email notification sent for transaction {$this->transaction_id} to {$transaction->contact->email}");
                } catch (\Exception $e) {
                    Log::error("Failed to send due payment email notification for transaction {$this->transaction_id}: " . $e->getMessage());
                }
            }

            // Send SMS notification
            if (!empty($data['auto_send_sms']) && !empty($transaction->contact->mobile)) {
                try {
                    $notificationUtil->sendSms($data);

                    $notificationUtil->activityLog($transaction, 'payment_reminder', null, [
                        'mobile' => $transaction->contact->mobile, 
                        'is_automatic' => true,
                        'sent_at' => now()
                    ], false, $this->business_id);

                    Log::info("Due payment SMS notification sent for transaction {$this->transaction_id} to {$transaction->contact->mobile}");
                } catch (\Exception $e) {
                    Log::error("Failed to send due payment SMS notification for transaction {$this->transaction_id}: " . $e->getMessage());
                }
            }

            // TODO: Implement WhatsApp notification
            if (!empty($data['auto_send_wa_notif']) && !empty($transaction->contact->mobile)) {
                try {
                    $whatsapp_link = $notificationUtil->getWhatsappNotificationLink($data);
                    
                    $notificationUtil->activityLog($transaction, 'payment_reminder', null, [
                        'whatsapp' => $transaction->contact->mobile, 
                        'is_automatic' => true,
                        'whatsapp_link' => $whatsapp_link,
                        'sent_at' => now()
                    ], false, $this->business_id);

                    Log::info("Due payment WhatsApp notification prepared for transaction {$this->transaction_id} to {$transaction->contact->mobile}");
                } catch (\Exception $e) {
                    Log::error("Failed to prepare due payment WhatsApp notification for transaction {$this->transaction_id}: " . $e->getMessage());
                }
            }

            Log::info("Due payment notification job completed successfully for transaction {$this->transaction_id}");

        } catch (\Exception $e) {
            Log::error("Due payment notification job failed for transaction {$this->transaction_id}: " . $e->getMessage());
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
        Log::error("Due payment notification job failed for transaction {$this->transaction_id}: " . $exception->getMessage());
    }
}
