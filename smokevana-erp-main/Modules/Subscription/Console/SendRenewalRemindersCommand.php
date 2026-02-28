<?php

namespace Modules\Subscription\Console;

use Illuminate\Console\Command;
use Modules\Subscription\Entities\CustomerSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRenewalRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:send-reminders 
                            {--dry-run : Run without sending emails}
                            {--days=7 : Days before expiry to send reminder}
                            {--business= : Process only for a specific business}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send renewal reminder emails for subscriptions about to expire';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting renewal reminder check...');
        
        $dryRun = $this->option('dry-run');
        $days = (int) $this->option('days');
        $businessId = $this->option('business');

        if ($dryRun) {
            $this->warn('Running in DRY-RUN mode. No emails will be sent.');
        }

        $this->info("Looking for subscriptions expiring within {$days} days...");

        // Calculate the date range
        $reminderDate = Carbon::now()->addDays($days);
        $today = Carbon::now();

        // Find active subscriptions expiring within the specified days
        $query = CustomerSubscription::where('status', CustomerSubscription::STATUS_ACTIVE)
            ->where('send_reminders', true)
            ->where(function ($q) use ($today, $reminderDate) {
                $q->whereNotNull('expires_at')
                  ->whereBetween('expires_at', [$today, $reminderDate]);
            })
            ->orWhere(function ($q) use ($today, $reminderDate) {
                $q->where('status', CustomerSubscription::STATUS_ACTIVE)
                  ->where('send_reminders', true)
                  ->whereNotNull('current_period_end')
                  ->whereBetween('current_period_end', [$today, $reminderDate])
                  ->where('auto_renew', false);
            });

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        $subscriptions = $query->with(['contact', 'plan', 'business'])->get();

        $this->info("Found {$subscriptions->count()} subscription(s) needing reminders.");

        $sentCount = 0;
        $errorCount = 0;

        foreach ($subscriptions as $subscription) {
            try {
                $contact = $subscription->contact;
                $plan = $subscription->plan;

                if (!$contact || !$contact->email) {
                    $this->warn("  Skipping {$subscription->subscription_code}: No contact email");
                    continue;
                }

                $expiryDate = $subscription->expires_at ?? $subscription->current_period_end;
                $daysRemaining = Carbon::now()->diffInDays($expiryDate, false);

                $this->line("Processing: {$contact->name} ({$contact->email})");
                $this->line("  Plan: {$plan->name}, Expires: {$expiryDate->format('Y-m-d')}, Days remaining: {$daysRemaining}");

                if (!$dryRun) {
                    // Log the reminder event
                    $subscription->logEvent(
                        'renewal_reminder_sent',
                        "Renewal reminder sent to {$contact->email}. Subscription expires in {$daysRemaining} days."
                    );

                    // You can integrate with your notification system here
                    // For now, we'll just log the event
                    // Mail::to($contact->email)->send(new RenewalReminderMail($subscription));
                    
                    Log::info('Renewal reminder sent', [
                        'subscription_id' => $subscription->id,
                        'subscription_code' => $subscription->subscription_code,
                        'contact_email' => $contact->email,
                        'days_remaining' => $daysRemaining,
                        'expiry_date' => $expiryDate->toDateString(),
                    ]);
                }

                $this->info("  ✓ Reminder queued for: {$contact->email}");
                $sentCount++;

            } catch (\Exception $e) {
                $errorCount++;
                $this->error("  ✗ Error: {$e->getMessage()}");
                
                Log::error('Failed to send renewal reminder', [
                    'subscription_id' => $subscription->id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info('=== Summary ===');
        $this->info("Total processed: " . ($sentCount + $errorCount));
        $this->info("Reminders sent: {$sentCount}");
        if ($errorCount > 0) {
            $this->error("Errors: {$errorCount}");
        }

        if ($dryRun) {
            $this->warn('DRY-RUN complete. No actual emails were sent.');
        }

        return $errorCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
