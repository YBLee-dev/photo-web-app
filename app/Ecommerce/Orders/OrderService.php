<?php

namespace App\Ecommerce\Orders;


use App\Ecommerce\Products\ProductTypesEnum;
use App\Ecommerce\PromoCodes\PromoCodeTypesEnum;
use Exception;
use Illuminate\Database\Eloquent\Model;

class OrderService
{
    /**
     * Recalculated and update order info by recounting order items
     *
     * @param Model $order
     *
     * @throws Exception
     */
    public function recalculateByItems(Model $order)
    {
        $orderRepo =  new OrderRepo();

        $packages = $order->items()->whereNotNull('package_id')->get()->groupBy('item_id');
        $addons = $order->items()->whereNull('package_id')->get();

        $subtotal = 0;
        $items_count = 0;
        foreach ($packages as $package){
            $subtotal += $package->first()->sum;
            $items_count += $package->first()->quantity;
        }
        foreach ($addons as $addon){
            $subtotal += $addon->sum;
            $items_count += $addon->quantity;
        }

        $discount_type = $order->discount_type;
        $discount_amount = $order->discount;

        switch (true) {
            case !$discount_type:
                $total = $subtotal;
                break;
            case PromoCodeTypesEnum::PERCENT()->is($discount_type):
                $discount = $subtotal * $discount_amount / 100;
                $total = $subtotal - $discount;
                break;
            case PromoCodeTypesEnum::MONEY()->is($discount_type):
                $total = $subtotal - $discount_amount;
                break;
        }

        $orderRepo->update($order->id, [
            'total' => round($total, 2),
            'subtotal' => round($subtotal, 2),
            'items_count' => $items_count,
        ]);
    }

    /**
     * Update total and subtotal by new promo code
     *
     * @param Model $order
     * @param Model $promo_code
     *
     * @throws Exception
     */
    public function recalculateWithPromo(Model $order, $promo_code)
    {
        $orderRepo =  new OrderRepo();

        switch ($promo_code) {
            case null:
                $total = $order->subtotal;
                $discount_amount = $type = '';
                break;
            case PromoCodeTypesEnum::PERCENT()->is($promo_code->type):
                $discount = $order->subtotal * $promo_code->discount_amount / 100;
                $total = $order->subtotal - $discount;
                break;
            case PromoCodeTypesEnum::MONEY()->is($promo_code->type):
                $total = $order->subtotal - $promo_code->discount_amount;
                break;
        }

        $orderRepo->update($order->id, [
            'discount' => $discount_amount ?? $promo_code->discount_amount,
            'discount_type' => $type ?? $promo_code->type,
            'discount_name' => $type ?? $promo_code->name,
            'total' => round($total, 2),
        ]);
    }

    /**
     * Find all printable items from order and sorted it by image and size with counting quantity for print
     *
     * @param Order $order
     *
     * @return array
     */
    public function getPrintableItems(Order $order)
    {
        /** @var OrderItem [] $items */
        $items = $order->printableItems;

        $prepared_data = [];
        foreach ($items as $item) {
            $image = str_replace('preview', 'original', $item->image);

            $same_size_id = array_search($item->size->name, array_column($prepared_data, 'size'));
            $same_image_id = array_search($image, array_column($prepared_data, 'image'));

            if($same_image_id !== false && $same_size_id !== false){
                $prepared_data[$same_image_id]['quantity'] += $item->quantity;
            } else {
                $prepared_data[] = [
                    'image' => $image,
                    'quantity' => $item->quantity,
                    'size' => $item->size->name,
                    'height' => $item->crop_info_height ?? 0,
                    'width' => $item->crop_info_width ?? 0,
                    'x' => $item->crop_info_x ?? 0,
                    'y' => $item->crop_info_y ?? 0,
                    'order_item' => $item,
                ];
            }
        }

        return $prepared_data;
    }
}
