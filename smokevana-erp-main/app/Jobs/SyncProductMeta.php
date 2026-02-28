<?php

namespace App\Jobs;

use App\Product;
use App\SyncRecord;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncProductMeta implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $productSlug;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->productSlug = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            try {
                Log::info('Executing: ' . $this->productSlug);
                $apiUrl = 'https://ad2.phantasm.solutions/api/synProductMeta/' . $this->productSlug;
                // $apiUrl = 'http://127.0.0.1:8080/api/synProductMeta/'.$this->productSlug;
                $response = Http::get($apiUrl);
                $data = $response->json();
                $nextProduct = $data['next'];
                Log::info('slug: ' . $data['sku']);
                Log::info('value: ' .$data['value']);
                DB::beginTransaction();
                if ($data['sku'] && $data['value'] != null) {
                    // if ($data['value'] == 1 || $data['value'] == 6 || $data['value'] == 2 || $data['value'] == 5 || $data['value'] == 3) {
                        $val = (string) $data['value'];
                        
                        $product = Product::updateOrCreate(
                            ['slug' => $data['sku']],
                            [
                                "created_at" => Carbon::now(),
                                "updated_at" => Carbon::now(),
                                "locationTaxType" => ($val>0)? [$val] : null,
                            ]
                        );
                        Log::info('updated product: ' . $data['sku']);
                    // }
                }
                Log::info('Commited: ' . $this->productSlug);
                DB::commit();

                if (isset($nextProduct)) {
                    Log::info("Dispatched: " . $nextProduct);
                    SyncProductMeta::dispatch($nextProduct); 
                }
            } catch (\Throwable $th) {
                Log::info('Rolling back: ' . $this->productSlug);
                DB::rollback();
                Log::error($th->getMessage() . ' ' . $th->getLine() . ' ' . $th->getFile());
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage() . ' ' . $th->getLine() . ' ' . $th->getFile());
        }
    }
}
