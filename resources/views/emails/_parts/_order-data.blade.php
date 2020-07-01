@if(!$order->gallery->isDeadlineCame())
    @if(!$admInformation && !$order->free_gift)
        <a href="{{route('order_page', $order->id)}}" style="display: block; line-height: 45px; width: 220px; height: 45px; color: white; font-size: 24px; text-decoration: none; text-align: center; background: orange;">View My Order</a>
        <br>
    @endif
@else
    <a href="{{route('order_email', $order->id)}}"
        style="display: block; line-height: 45px; width: 220px; height: 45px; color: white; font-size: 24px; text-decoration: none; text-align: center; background: orange;">I
        want a FREE gift</a>
    <br>
@endif

@if(!$admInformation && $order->free_gift)
    <p style="display: block; line-height: 45px; width: 220px; height: 45px; color: white; font-size: 24px; text-decoration: none; text-align: center; background: green; border-radius: .3rem">FREE gift included</p>
@endif

@if($order->payment_status == \App\Ecommerce\Orders\OrderPaymentStatusEnum::NOT_PAID)
<p style="color:red;">Attention! Your order is unpaid. You can purchase the order <a href="{{route('payment-page', $order->hash)}}">here</a></p>
@endif

<h4>Client details</h4>
First name: {{$order->customer_first_name}}
<br>Last name: {{$order->customer_last_name}}
<br>Address: {{$order->address}}
<br>City: {{$order->city}}
<br>State: {{$order->state}}
<br>Postal: {{$order->postal}}
<br>Country: {{$order->country}}
<br>Message: {{$order->message}}
{{--<br>Receive promotions: {{$order->receive_promotions_by_email ? 'yes' : 'no'}}--}}
<br>E-mail: {{$order->customer->email}}
<br>

<h4>Order details</h4>
Date: {{$order->created_at->format('M j, Y')}}
<br>Order # {{$order->id}}
<br>Payment status: {{$order->payment_status}}
<br>Placing date: {{$order->created_at}}
@if($admInformation)
    <br>Free gift: {{$order->free_gift ? 'yes' : 'no'}}
    <br>Gallery name: {{$order->gallery->present()->name}}
    <br>Price list: {{$order->priceList->name}}
@else
<br>School: {{$order->gallery->present()->name}}
@endif
<br>Sub-gallery name: {{$order->subgallery->name}}
<br>Items count: {{$order->items_count}}
<br>Subtotal: $ {{$order->subtotal}}
<br>Tax: $ {{$order->tax ?? 0}}
<br>
@if($order->discount)
    Discount ({{$order->discount_name}}): $ {{$order->total_coupon}}
@else
    Discount: 0
@endif
<br>Total: $ {{$order->total}}
<br>

@if(! $admInformation && ($order->isDownloadable()) && ($order->payment_status == \App\Ecommerce\Orders\OrderPaymentStatusEnum::PAID))
    <p>To download your item(s), view the order on the website with the link above and click the download link next to the item(s).</p>
    <a href="{{route('order_page', $order->id)}}"  style="font-size: 24px; text-decoration: none; color: white; background-color: #007bff; border-color: #007bff; vertical-align: middle; padding: .375rem .75rem; border-radius: .5rem">DOWNLOAD DIGITAL IMAGES</a>
    <br><br>
@endif
