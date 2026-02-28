<?php

namespace Modules\Subscription\Console;

use Illuminate\Console\Command;
use Modules\Subscription\Entities\CustomerSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ExpireSubscriptionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:expire 
                            {--dry-run : Run without making changes}
                            {--business= : Process only for a specific business}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically expire subscriptions that have passed their expiry date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting subscription expiry check...');
        $dryRun = $this->option('dry-run');
        $businessId = $this->option('business');

        if ($dryRun) {
            $this->warn('Running in DRY-RUN mode. No changes will be made.');
        }

        // Find all active subscriptions that have passed their expiry date
        $query = CustomerSubscription::where('status', CustomerSubscription::STATUS_ACTIVE)
            ->where(function ($q) {
                $q->whereNotNull('expires_at')
                  ->where('expires_at', '<=', Carbon::now());
            })
            ->orWhere(function ($q) {
                $q->where('status', CustomerSubscription::STATUS_ACTIVE)
                  ->whereNotNull('current_period_end')
                  ->where('current_period_end', '<=', Carbon::now())
                  ->where('auto_renew', false);
            });

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        $expiredSubscriptions = $query->with(['contact', 'plan', 'business'])->get();

        $this->info("Found {$expiredSubscriptions->count()} subscription(s) to expire.");

        $expiredCount = 0;
        $errorCount = 0;

        foreach ($expiredSubscriptions as $subscription) {
            try {
                $customerName = $subscription->contact->name ?? 'Unknown';
                $planName = $subscription->plan->name ?? 'Unknown Plan';
                $businessName = $subscription->business->name ?? 'Unknown Business';

                $this->line("Processing: {$customerName} - {$planName} (Business: {$businessName})");

                if (!$dryRun) {
                    // Call the expire method which handles customer group revert
                    $subscription->expire();
                    
                    Log::info('Subscription expired via cron job', [
                        'subscription_id' => $subscription->id,
                        'subscription_code' => $subscription->subscription_code,
                        'contact_id' => $subscription->contact_id,
                        'plan_id' => $subscription->plan_id,
                        'business_id' => $subscription->business_id,
                        'expired_at' => $subscription->expires_at,
                    ]);
                }

                $this->info("  ✓ Expired: {$subscription->subscription_code}");
                $expiredCount++;

            } catch (\Exception $e) {
                $errorCount++;
                $this->error("  ✗ Error expiring {$subscription->subscription_code}: {$e->getMessage()}");
                
                Log::error('Failed to expire subscription via cron job', [
                    'subscription_id' => $subscription->id,
                    'subscription_code' => $subscription->subscription_code,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        // Also check for subscriptions in grace period that need to be fully expired
        $this->processGracePeriodExpiry($dryRun, $businessId, $expiredCount, $errorCount);

        $this->newLine();
        $this->info('=== Summary ===');
        $this->info("Total processed: " . ($expiredCount + $errorCount));
        $this->info("Successfully expired: {$expiredCount}");
        if ($errorCount > 0) {
            $this->error("Errors: {$errorCount}");
        }

        if ($dryRun) {
            $this->warn('DRY-RUN complete. No actual changes were made.');
        }

        return $errorCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Process subscriptions that are past their grace period
     */
    protected function processGracePeriodExpiry($dryRun, $businessId, &$expiredCount, &$errorCount)
    {
        $this->newLine();
        $this->info('Checking for subscriptions past grace period...');

        // Find subscriptions that are in grace period status and past their grace period end
        $query = CustomerSubscription::where('status', CustomerSubscription::STATUS_PAST_DUE)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', Carbon::now());

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        $pastDueSubscriptions = $query->with(['contact', 'plan', 'business'])->get();

        $this->info("Found {$pastDueSubscriptions->count()} subscription(s) past grace period.");

        foreach ($pastDueSubscriptions as $subscription) {
            try {
                $customerName = $subscription->contact->name ?? 'Unknown';
                
                $this->line("Processing grace period expiry: {$customerName}");

                if (!$dryRun) {
                    $subscription->expire();
                    
                    Log::info('Subscription expired after grace period via cron job', [
                        'subscription_id' => $subscription->id,
                        'subscription_code' => $subscription->subscription_code,
                    ]);
                }

                $this->info("  ✓ Expired (past grace): {$subscription->subscription_code}");
                $expiredCount++;

            } catch (\Exception $e) {
                $errorCount++;
                $this->error("  ✗ Error: {$e->getMessage()}");
                
                Log::error('Failed to expire subscription past grace period', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
