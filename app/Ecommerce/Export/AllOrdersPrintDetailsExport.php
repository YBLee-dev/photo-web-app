<?php

namespace App\Ecommerce\Export;

use App\Ecommerce\Orders\OrderItemRepo;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class AllOrdersPrintDetailsExport implements FromCollection
{
    use Exportable;

    protected $orders;

    protected $product_fields;

    /**
     * AllOrdersPrintDetailsExport constructor.
     *
     * @param $orders
     * @throws \Exception
     */
    public function __construct($orders)
    {
        $this->orders = $orders;
        $orderItemsRepo = new OrderItemRepo();
        $this->product_fields = $orderItemsRepo->getAll()->unique('name')->pluck('name')->toArray();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $prepared_data[] = $this->headings();


        foreach ($this->orders as $order)
        {
            if($order->isDigitalFull()){
                continue;
            }

            $packages = $order->items()->whereNotNull('package_id')->get()->groupBy('item_id');
            $addons = $order->items()->whereNull('package_id')->get();

            /*
            * Prepare packages
            */
            foreach ($packages as $package_name => $package) {
                foreach ($package as $product) {
                    $product_data = [
                        $order->id,
                        $product->package_name,
                        '',
                        $order->gallery->present()->name(),
                        $order->subgallery->name,
                        '',
                    ];
                    foreach ($this->product_fields as $field) {
                        if ($field === $product->name) {
                            $product_data[] = last(explode('/', $product->image));
                        } else {
                            $product_data[] = '';
                        }
                    }

                    $prepared_data[] = $product_data;
                }
            }

            /*
             * Prepare addons
             */
            foreach ($addons as $addon) {
                $addon_data = [
                    $order->id,
                    '',
                    $addon->name,
                    $order->gallery->present()->name(),
                    $order->subgallery->name,
                    '',
                ];
                foreach ($this->product_fields as $field) {
                    if ($field === $addon->name) {
                        $addon_data[] = last(explode('/', $addon->image));
                    } else {
                        $addon_data[] = '';
                    }
                }

                $prepared_data[] = $addon_data;
            }

        }


        return collect($prepared_data);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $base_fields = [
            'order_no',
            'package',
            'addon',
            'gallery',
            'sub',
            'Class Photo',
        ];

        return array_merge($base_fields, $this->product_fields);
    }
}
