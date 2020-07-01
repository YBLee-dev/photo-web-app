<?php

namespace App\Console\Commands;

use App\Events\ReminderForPotentialCustomersEvent;
use App\Mail\SendReminderForPotentialCustomers;
use App\Photos\Galleries\GalleryRepo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class RemindAboutSubGallery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:customer-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email for customers that logged in and did not make a purchase';

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
    public function handle(GalleryRepo $galleryRepo)
    {
        $galleries = $galleryRepo->getAllBeforeDeadline();
        $potentialCustomers = [];

        foreach ($galleries as $gallery)
        {
            $subGalleries = $gallery->subGalleries()
                ->with('customers')
                ->with('orders')
                ->get();

            foreach ($subGalleries as $subgallery){
                if($subgallery->customers){
                    $customersWithOrder = $subgallery->orders->pluck('customer_id');

                    $allCustomers = $subgallery->customers->filter(function ($customer) use ($customersWithOrder) {
                        return ! ($customersWithOrder->contains($customer->id));
                    });

                    if($allCustomers->count() > 0){
                        $potentialCustomers[] = [
                            'customers' => $allCustomers->pluck('email'),
                            'subgallery' => $subgallery
                        ];
                    }
                }
            }
        }

        foreach ($potentialCustomers as $potentialCustomerBySubGallery)
        {
            event(new ReminderForPotentialCustomersEvent(
                $potentialCustomerBySubGallery['customers']->toArray(),
                $potentialCustomerBySubGallery['subgallery']->password)
            );
        }
    }
}
