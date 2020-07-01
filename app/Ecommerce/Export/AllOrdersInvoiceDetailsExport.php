<?php

namespace App\Ecommerce\Export;

use App\Ecommerce\Orders\OrderService;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class AllOrdersInvoiceDetailsExport implements FromCollection
{
    use Exportable;

    protected $orders;

    /**
     * AllOrdersDetailsExport constructor.
     *
     * @param $orders
     * @throws \Exception
     */
    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    /**
     * Collect export data
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $prepared_data[] = $this->headings();

        /** @var OrderService $service */
        $service = app()->make(OrderService::class);

        //Sort by classroom
        $orders = $this->orders->sortBy(function($order, $key) {
            return $order->subgallery->person->classroom;
        });

        foreach ($orders as $order)
        {
            if(!$order->isDigitalFull()){
                $items = collect($service->getPrintableItems($order));
                $count = $items->sum('quantity');

                //Add count of photos if has free gift
                if($order->free_gift){
                    $count++;
                }

                //Add count for personal classroom photo
                $count++;

                //Add count for staff id cards
                if($order->subgallery->person->isStaff()){
                    $count = $count+2;
                }

                $prepared_data[] = [
                    $order->id,
                    $order->subgallery->person->classroom,
                    $order->subgallery->name,
                    $count,
                    $order->free_gift ? 'Yes' : 'Not',
                    ''
                ];
            }
        }

        return collect($prepared_data);
    }

    /**
     * Set headers
     *
     * @return array
     */
    public function headings(): array
    {
        return $base_fields = [
            'Number',
            'Classroom',
            'Sub gallery name',
            'Photos count',
            'Free gift',
            'Signature',
        ];
    }
}
