<div style="width:100%">
    <table class="table table-bordered">
        <thead>
        <tr role="row">
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;">Package/add-on name</th>
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;">Product name</th>
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;">Selected photo</th>
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;">Size/Details</th>
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;">Count</th>
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;">Price</th>
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;">Sum</th>
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;" rowspan="1" colspan="1" aria-label=""></th>
        </tr>
        </thead>
        <tbody>

        @foreach ($packages as $package_id => $products)
            @foreach ($products as $product)
                <tr class="text-center js_item_{{$product->item_id}}">
                    @if ($loop->first)
                        <td rowspan="{{$products->count()}}" style="vertical-align: middle; border-color: #ddd;">
                            <a href="{{route('dashboard::packages.show', $products->first()->package_id)}}">
                                {{$products->first()->package_name}}
                            </a>
                        </td>
                    @endif
                    <td style="vertical-align: middle; border-color: #ddd;">
                        {{$product->name}}
                    </td>
                    <td class="text-center" style="border-color: #ddd;">
                        @if(!$product->isDigitalFull() && !$product->isDigital())
                            <img src="{{$product->image}}" alt="" style="max-width: 150px; max-height: 150px;">
                            <a href="" class="btn btn-flat js_ajax-by-click-btn"
                               data-action="{{route('dashboard::orders.item.image.edit', [$order->id, $product->id])}}" data-modal="true" data-method="GET"
                               data-modal-ttl="Change product image" data-reload-after-close-modal="true" data-original-title="" title="">
                                <i class="fa fa-pencil-square-o" data-original-title="" title=""></i>
                            </a>
                        @endif
                    </td>
                    <td style="vertical-align: middle; border-color: #ddd;">
                        @if($product->size)
                            {{$product->size->name}}
                            <a href="" class="btn btn-flat js_ajax-by-click-btn"
                               data-action="{{route('dashboard::orders.item.size.edit', [$order->id, $product->id])}}" data-modal="true" data-method="GET"
                               data-modal-ttl="Change product size" data-reload-after-close-modal="true" data-original-title="" title="">
                                <i class="fa fa-pencil-square-o" data-original-title="" title=""></i>
                            </a>
                        @endif
                    </td>
                    @if ($loop->first)
                        <td rowspan="{{$products->count()}}" style="vertical-align: middle; border-color: #ddd;">
                            {{$products->first()->quantity}}
                            <a href="" class="btn btn-flat js_ajax-by-click-btn"
                               data-action="{{route('dashboard::orders.item.count.edit', [$order->id, $product->item_id])}}" data-modal="true" data-method="GET"
                               data-modal-ttl="Change item quantity" data-reload-after-close-modal="true" data-original-title="" title="">
                                <i class="fa fa-pencil-square-o" data-original-title="" title=""></i>
                            </a>
                        </td>
                        <td rowspan="{{$products->count()}}" style="vertical-align: middle; border-color: #ddd;">
                            {{'$ '.$products->first()->price}}
                        </td>
                        <td rowspan="{{$products->count()}}" style="vertical-align: middle; border-color: #ddd;">
                            {{'$ '.$products->first()->sum}}
                        </td>
                        <td rowspan="{{$products->count()}}" style="vertical-align: middle; border-color: #ddd;">
                            <a href="" class="js_delete"
                               data-item=".js_item_{{$product->item_id}}"
                               data-request="{{route('dashboard::orders.item.delete', [$order->id, $product->item_id]) }}"
                               data-method="POST">
                                <i class="fa fa-times text-red" aria-hidden="true"></i>
                            </a>
                        </td>
                    @endif
                </tr>
            @endforeach
        @endforeach

        @foreach ($addons as $addon)
            <tr class="text-center js_item_{{$addon->item_id}}">
                <td style="vertical-align: middle; border-color: #ddd;">
                    <a href="{{route('dashboard::products.edit', $addon->product_id)}}">
                        {{$addon->name}}
                    </a>
                </td>
                <td style="vertical-align: middle; border-color: #ddd;"></td>
                <td class="text-center" style="border-color: #ddd;">
                    @if(!$addon->product->isDigitalFull() && !$addon->product->isDigital() && !$addon->product->isRetouch())
                        <img src="{{ $addon->image }}" alt="" style="max-width: 150px; max-height: 150px;">
                        <a href="" class="btn btn-flat js_ajax-by-click-btn"
                           data-action="{{route('dashboard::orders.item.image.edit', [$order->id, $addon->id])}}" data-modal="true" data-method="GET"
                           data-modal-ttl="Change product image" data-reload-after-close-modal="true" data-original-title="" title="">
                            <i class="fa fa-pencil-square-o" data-original-title="" title=""></i>
                        </a>
                    @endif
                </td>
                <td style="vertical-align: middle; border-color: #ddd;">
                    @if($addon->retouch)
                        {{ $addon->retouch }}
                    @elseif($addon->size)
                        {{$addon->size->name}}
                        <a href="" class="btn btn-flat js_ajax-by-click-btn"
                           data-action="{{route('dashboard::orders.item.size.edit', [$order->id, $addon->id])}}" data-modal="true" data-method="GET"
                           data-modal-ttl="Change product size" data-reload-after-close-modal="true" data-original-title="" title="">
                            <i class="fa fa-pencil-square-o" data-original-title="" title=""></i>
                        </a>
                    @endif
                </td>
                <td style="vertical-align: middle; border-color: #ddd;">
                    {{$addon->quantity}}
                    <a href="" class="btn btn-flat js_ajax-by-click-btn"
                       data-action="{{route('dashboard::orders.item.count.edit', [$order->id, $addon->item_id])}}" data-modal="true" data-method="GET"
                       data-modal-ttl="Change item quantity" data-reload-after-close-modal="true" data-original-title="" title="">
                        <i class="fa fa-pencil-square-o" data-original-title="" title=""></i>
                    </a>
                </td>
                <td style="vertical-align: middle; border-color: #ddd;">
                    {{'$ '.$addon->price}}
                </td>
                <td style="vertical-align: middle; border-color: #ddd;">
                    {{'$ '.$addon->sum}}
                </td>
                <td style="vertical-align: middle; border-color: #ddd;">
                    <a href="" class="js_delete"
                       data-item=".js_item_{{$addon->item_id}}"
                       data-request="{{route('dashboard::orders.item.delete', [$order->id, $addon->item_id]) }}"
                        data-method="POST">
                        <i class="fa fa-times text-red" aria-hidden="true"></i>
                    </a>
                </td>
            </tr>

        @endforeach
        <tr class="text-center">
            <td colspan="5" style="border-color: #ddd;"></td>
            <td class="text-bold" style="vertical-align: middle; border-color: #ddd;">
                Subtotal:
            </td>
            <td style="vertical-align: middle; border-color: #ddd;">
                ${{$order->subtotal}}
            </td>
            <td style="vertical-align: middle; border-color: #ddd;"></td>
        </tr>
        <tr class="text-center">
            <td colspan="5" style="border-color: #ddd;"></td>
            <td class="text-bold" style="vertical-align: middle; border-color: #ddd;">
                {{$order->discount ? $order->discount_name : 'Discount'}}:
            </td>
            <td style="vertical-align: middle; border-color: #ddd;">
                {{($order->discount ? ($order->discount. ' ' . $order->discount_type) : 0)}}
            </td>
            <td style="vertical-align: middle; border-color: #ddd;">
                <a href="" class="btn btn-flat js_ajax-by-click-btn" data-action="{{route('dashboard::orders.promo-code.edit', $order)}}" data-modal="true" data-method="GET" data-modal-ttl="Change promo code for order">
                    <i class="fa fa-pencil-square-o"></i></a>
            </td>
        </tr>
        <tr class="text-center">
            <td colspan="5" style="border-color: #ddd;"></td>
            <td class="text-bold" style="vertical-align: middle; border-color: #ddd;">
                Total:
            </td>
            <td style="vertical-align: middle; border-color: #ddd;">
                ${{$order->total}}
            </td>
            <td style="vertical-align: middle; border-color: #ddd;"></td>
        </tr>
        </tbody>
    </table>
</div>
