<?php

namespace App\Console\Commands;

use App\Product;
use Illuminate\Console\Command;

class PopulatePriceRanges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:populate-price-ranges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate price_range column for all existing products';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        $this->info('Starting to populate price ranges for all products...');

        $products = Product::with(['variations.group_prices.groupInfo'])->get();
        $total = $products->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updated = 0;
        $skipped = 0;

        foreach ($products as $product) {
            try {
                $product->calculateAndUpdatePriceRange();
                $updated++;
            } catch (\Exception $e) {
                $this->error("\nError processing product ID {$product->id}: " . $e->getMessage());
                $skipped++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Completed! Updated: {$updated}, Skipped: {$skipped}");

        return 0;
    }
}

