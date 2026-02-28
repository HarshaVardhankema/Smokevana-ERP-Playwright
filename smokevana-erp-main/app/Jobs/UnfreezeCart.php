<?php

namespace App\Jobs;

use App\Cart;
use App\CartItem;
use App\GuestCartItem;
use App\GuestSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnfreezeCart implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $guestSessionId;
    protected $agentId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $userId=null, int $guestSessionId=null, int $agentId=null)
    {
        $this->userId = $userId;
        $this->guestSessionId = $guestSessionId;
        $this->agentId = $agentId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->userId){
            $data = Cart::where('user_id', $this->userId)->first();
            if ($data && $data->isFreeze) {
                $cartItems = CartItem::where('user_id', $this->userId)->get();
                
                // Get user's location for proper stock restoration
                $contact = \App\Contact::find($this->userId);
                $location_id = $contact ? $contact->location_id : null;
                
                foreach ($cartItems as $cartItem) {
                    try {
                        $variationId = $cartItem->variation_id;
                        
                        // Check if stock management is enabled for this product
                        $variation = \App\Variation::with('product')->find($variationId);
                        if (!$variation || !$variation->product || $variation->product->enable_stock != 1) {
                            // Skip stock restoration if enable_stock is 0
                            continue;
                        }
                        
                        if ($location_id) {
                            // Restore stock to specific location
                            $updated = DB::table('variation_location_details')
                                ->where('variation_id', $variationId)
                                ->where('location_id', $location_id)
                                ->increment('in_stock_qty', $cartItem->qty);
                            
                            if (!$updated) {
                                Log::warning("Stock restore failed for variation {$variationId} at location {$location_id}");
                            }
                        } else {
                            // Fallback: restore to all locations (old behavior)
                            $updated = DB::table('variation_location_details')
                                ->where('variation_id', $variationId)
                                ->increment('in_stock_qty', $cartItem->qty);
                        }
                    } catch (\Throwable $th) {
                        Log::error("Stock restore error: " . $th->getMessage());
                    }
                }
                Cart::where('user_id', $this->userId)->update(['isFreeze' => false]);
            }
        }else if($this->guestSessionId){
            $data = GuestSession::where('id', $this->guestSessionId)->first();
            if ($data && $data->isFreeze) {
                $cartItems = GuestCartItem::where('guest_session_id', $this->guestSessionId)->get();
                
                // Get guest location from session
                $location_id = $data->location_id ?? null;
                
                foreach ($cartItems as $cartItem) {
                    try {
                        $variationId = $cartItem->variation_id;
                        
                        // Check if stock management is enabled for this product
                        $variation = \App\Variation::with('product')->find($variationId);
                        if (!$variation || !$variation->product || $variation->product->enable_stock != 1) {
                            // Skip stock restoration if enable_stock is 0
                            continue;
                        }
                        
                        if ($location_id) {
                            // Restore stock to specific location
                            $updated = DB::table('variation_location_details')
                                ->where('variation_id', $variationId)
                                ->where('location_id', $location_id)
                                ->increment('in_stock_qty', $cartItem->qty);
                            
                            if (!$updated) {
                                Log::warning("Guest stock restore failed for variation {$variationId}");
                            }
                        } else {
                            // Fallback: restore to all locations
                            $updated = DB::table('variation_location_details')
                                ->where('variation_id', $variationId)
                                ->increment('in_stock_qty', $cartItem->qty);
                        }
                    } catch (\Throwable $th) {
                        Log::error("Guest stock restore error: " . $th->getMessage());
                    }
                }
                GuestSession::where('id', $this->guestSessionId)->update(['isFreeze' => false]);
            }
        } else if($this->agentId){
            Log::info('Unfreezing cart for agent-> ' . $this->agentId);
            Log::info('Not implemented yet');
            // $data = AgentCart::where('id', $this->agentId)->first();
            // if ($data && $data->isFreeze) {
            //     $cartItems = AgentCartItem::where('agent_id', $this->agentId)->get();
            // }
            // foreach ($cartItems as $cartItem) {
            //     try {
            //         $variationId = $cartItem->variation_id; //
            //         $updated = DB::table('variation_location_details')
            //             ->where('variation_id', $variationId) // 
            //             ->increment('in_stock_qty', $cartItem->qty);
            //     } catch (\Throwable $th) {
            //         Log::info($th->getMessage());
            //     }
            // }
            // AgentCart::where('id', $this->agentId)->update(['isFreeze' => false]);
        }
    }
}
