<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr role="row">
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;">Count</th>
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;">Package/add-on name</th>
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;">Product name</th>
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;">Selected photo</th>
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;">Size</th>
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;">Price</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($packages as $package_id => $products)
            @foreach ($products as $product)
                <tr class="text-center js_item_{{$product->item_id}}">
                    @if ($loop->first)
                        <td rowspan="{{$products->count()}}" style="vertical-align: middle; border-color: #ddd;">
                            {{$products->first()->quantity}}
                        </td>
                        <td rowspan="{{$products->count()}}" style="vertical-align: middle; border-color: #ddd;">
                            {{$products->first()->package_name}}
                        </td>
                    @endif
                    <td style="vertical-align: middle; border-color: #ddd;">
                        {{$product->name}}
                    </td>
                    <td class="text-center" style="border-color: #ddd;">
                        @if(!$product->isDigitalFull() && !$product->isDigital())
                            <img src="{{ $product->image }}" alt="" style="max-width: 150px; max-height: 150px;">
                        @endif
                    </td>
                    <td style="vertical-align: middle; border-color: #ddd;">
                        @if($product->size)
                            {{$product->size->name}}
                        @endif
                    </td>
                    @if ($loop->first)
                        <td rowspan="{{$products->count()}}" style="vertical-align: middle; border-color: #ddd;">
                            {{'$ '.$products->first()->price}}
                        </td>
                    @endif
                </tr>
            @endforeach
        @endforeach

        <tr>
            <td colspan="6" style="border-top: 2px solid #ddd;"></td>
        </tr>
        <tr class="text-center">
            <td colspan="4" style="border-color: #ddd;"></td>
            <td class="text-bold" style="vertical-align: middle; border-color: #ddd;">
                Subtotal:
            </td>
            <td style="vertical-align: middle; border-color: #ddd;">
                $ {{$order->subtotal}}
            </td>
            <td style="vertical-align: middle; border-color: #ddd;"></td>
        </tr>
        <tr class="text-center">
            <td colspan="4" style="border-color: #ddd;"></td>
            <td class="text-bold" style="vertical-align: middle; border-color: #ddd;">
                Tax:
            </td>
            <td style="vertical-align: middle; border-color: #ddd;">
                $ {{$order->tax ?? 0}}
            </td>
            <td style="vertical-align: middle; border-color: #ddd;"></td>
        </tr>
        <tr class="text-center">
            <td colspan="4" style="border-color: #ddd;"></td>
            <td class="text-bold" style="vertical-align: middle; border-color: #ddd;">
                @if($order->discount)
                    Discount ({{$order->discount_name}}):
                @else
                    Discount: 0
                @endif
            </td>
            @if($order->discount)
                <td style="vertical-align: middle; border-color: #ddd;">
                    $ {{$order->total_coupon}}
                </td>
            @endif
            <td style="vertical-align: middle; border-color: #ddd;"></td>
        </tr>
        <tr class="text-center">
            <td colspan="4" style="border-color: #ddd;"></td>
            <td class="text-bold" style="vertical-align: middle; border-color: #ddd;">
                Total:
            </td>
            <td style="vertical-align: middle; border-color: #ddd;">
                $ {{$order->total}}
            </td>
            <td style="vertical-align: middle; border-color: #ddd;"></td>
        </tr>
        </tbody>
    </table>
</div>
