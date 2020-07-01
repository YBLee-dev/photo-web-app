<?php

namespace App\Console\Commands;

use App\Ecommerce\Orders\OrderRepo;
use App\Events\RemindAboutUnpaidOrderEvent;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemindAboutUnpaidOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:unpaid-order-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email for customers that make a purchase but not paid it';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(OrderRepo $orderRepo)
    {
        $orders = $orderRepo->getAllUnpaid();

        foreach ($orders as $order)
        {
            if($order->gallery->deadline >= Carbon::now()->toDateString()){
                $packages = $order->items()->whereNotNull('package_id')->get()->groupBy('item_id');
                $addons = $order->items()->whereNull('package_id')->get();

                event(new RemindAboutUnpaidOrderEvent($order, $packages, $addons));
            }
        }

    }
}
