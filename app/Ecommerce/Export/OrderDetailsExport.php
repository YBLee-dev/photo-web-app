<?php

namespace App\Ecommerce\Export;

use App\Ecommerce\Orders\Order;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

class OrderDetailsExport implements FromCollection
{
    use Exportable;

    /** @var Order  */
    protected $order;

    protected $productFields;

    /**
     * OrderDetailsExport constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;

        $this->productFields = $order->items->unique('name')->pluck('name')->toArray();
    }

    /**
     * @return Collection
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function collection()
    {
        $prepared_data[] = $this->headings();
        $packages = $this->order->productsInPackages();

        /*
         * Prepare packages
         */
        foreach ($packages as $package) {
            foreach ($package as $product) {
                $product_data = [
                    $this->order->id,
                    $product->package_name,
                    '',
                    $this->order->gallery->present()->name(),
                    $this->order->subgallery->name,
                    '',
                ];
                foreach ($this->productFields as $field) {
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
        foreach ($this->order->addons as $addon) {
            $addon_data = [
                $this->order->id,
                '',
                $addon->name,
                $this->order->gallery->present()->name(),
                $this->order->subgallery->name,
                '',
            ];
            foreach ($this->productFields as $field) {
                if ($field === $addon->name) {
                    $addon_data[] = last(explode('/', $addon->image));
                } else {
                    $addon_data[] = '';
                }
            }

            $prepared_data[] = $addon_data;
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

        return array_merge($base_fields, $this->productFields);
    }
}
