<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\CentralLogics\Helpers;
class CheckOrderStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $backoff = [10, 30, 60];
    public $timeout = 60;

    protected $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

  public function handle()
{
    try {
        $order = Order::find($this->orderId);
        if (!$order) {
            \Log::warning("CheckOrderStatus: Order ID {$this->orderId} not found.");
            return;
        }
        if ($order->order_status === 'pending') {
            $createdAt = \Carbon\Carbon::parse($order->created_at)->setTimezone('UTC'); // Ensure UTC for consistency

\Log::info("mo : --- ".$createdAt->diffInMinutes(now()) >= 15);


            $now = \Carbon\Carbon::now('UTC'); 

              $time = (int) \DB::table('business_settings')
->where('key','max_orders_to_cancel')
->first()->value ?? 5 ;
            \Log::warning("CheckOrderStatus: Order Id 1111111111111111111111111111111111111111111111111111111.");
            if ($createdAt->diffInMinutes(now()) >= $time ) {
                $order->order_status = 'canceled';
  $order->cancellation_reason = 'عذرًا، جميع سائقينا مشغولون حالياً. حاول مرة أخرى بعد قليل، ونحن هنا لخدمتك دائمًا.';             
//canceled
//$order->cancellation_reason = 'عذرًا، جميع سائقينا مشغولون حالياً. حاول مرة أخرى بعد قليل، ونحن هنا لخدمتك دائمًا.';
// $order->order_amount =120 ;
           ///     $order->save();
               // $order->cancellation_reason = 'عذرًا، جميع سائقينا مشغولون حالياً. حاول مرة أخرى بعد قليل، ونحن هنا لخدمتك دائمًا.';
                $order->canceled_by = 'admin';
             //   $order->save();
               // $order?->store ?   Helpers::increment_order_count($order?->store) : '';
     $order->save();

                Helpers::send_order_notification($order);
                \Log::info("CheckOrderStatus: Order ID {$this->orderId} status updated to cancelled.");
            } else {
                \Log::info("22222");
                \Log::info("CheckOrderStatus: Order ID {$this->orderId} not updated. Age: {$createdAt->diffInMinutes(now())} minutes.");
            }
        } else {
            \Log::info("333333");
            \Log::info("CheckOrderStatus: Order ID {$this->orderId} not updated. Status: {$order->status}.");
        }
    } catch (\Exception $e) {
        \Log::error("CheckOrderStatus failed for Order ID {$this->orderId}: {$e->getMessage()}");
        throw $e; 
    }
}
}
