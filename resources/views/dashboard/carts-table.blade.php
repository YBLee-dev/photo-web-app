<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr role="row">
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;">Package/add-on name</th>
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;">Product name</th>
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;">Selected photo</th>
            <th class="text-center" style="border-color: #ddd; border-bottom-width: 0; border-top: 1px solid #ddd;">Size</th>
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
                        <img src="{{$product->image}}" alt="" style="max-width: 150px; max-height: 150px;">
                    </td>
                    <td style="vertical-align: middle; border-color: #ddd;">
                        @if($product->size)
                            {{$product->size->name}}
                        @endif
                    </td>
                    @if ($loop->first)
                        <td rowspan="{{$products->count()}}" style="vertical-align: middle; border-color: #ddd;">
                            {{$products->first()->quantity}}
                        </td>
                        <td rowspan="{{$products->count()}}" style="vertical-align: middle; border-color: #ddd;">
                            {{'$ '.$products->first()->price}}
                        </td>
                        <td rowspan="{{$products->count()}}" style="vertical-align: middle; border-color: #ddd;">
                            {{'$ '.$products->first()->sum}}
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
                    <img src="{{$addon->image}}" alt="" style="max-width: 150px; max-height: 150px;">
                </td>
                <td style="vertical-align: middle; border-color: #ddd;">
                    @if($addon->size)
                        {{$addon->size->name}}
                    @endif
                </td>
                <td style="vertical-align: middle; border-color: #ddd;">
                    {{$addon->quantity}}
                </td>
                <td style="vertical-align: middle; border-color: #ddd;">
                    {{'$ '.$addon->price}}
                </td>
                <td style="vertical-align: middle; border-color: #ddd;">
                    {{'$ '.$addon->sum}}
                </td>
            </tr>

        @endforeach

        </tbody>
    </table>
</div>
