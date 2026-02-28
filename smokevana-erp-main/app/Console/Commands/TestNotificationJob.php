<?php

namespace App\Console\Commands;

use App\Jobs\SendNotificationJob;
use App\Transaction;
use Illuminate\Console\Command;

class TestNotificationJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification-job {transaction_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the notification job with a specific transaction';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $transaction_id = $this->argument('transaction_id');
        
        $transaction = Transaction::with('contact')->find($transaction_id);
        
        if (!$transaction) {
            $this->error("Transaction with ID {$transaction_id} not found.");
            return 1;
        }
        
        if (!$transaction->contact) {
            $this->error("Transaction {$transaction_id} has no associated contact.");
            return 1;
        }
        
        $this->info("Dispatching notification job for transaction {$transaction_id}...");
        
        try {
            SendNotificationJob::dispatch(
                false,
                $transaction->business_id,
                'shipment',
                $transaction->user,
                $transaction->contact,
                $transaction,
                null
            );
            
            $this->info("Notification job dispatched successfully!");
            $this->info("Check the jobs table to see the queued job.");
            $this->info("Run 'php artisan queue:work' to process the job.");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to dispatch notification job: " . $e->getMessage());
            return 1;
        }
    }
} 