<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $html,
    ) {
    }

    public function handle(): void
    {
        try {
            Mail::html($this->html, function ($m) {
                $m->to($this->order->user->email)->subject('Order confirmation');
            });
        } catch (\Throwable $e) {
            Log::info('mail failed');
        }
    }
}
