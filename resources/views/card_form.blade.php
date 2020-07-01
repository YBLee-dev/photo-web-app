@extends('core.base')

{{--Header--}}
@include('parts/_header-order')

@section('content')

<div class="login">
    <div class="login-container container">
        <div class="login-cnt">
            <h1 class="login-cnt-ttl">Order payment</h1>
            <div class="card-wrapper"></div>

            <div class="login-info">
                <div class="login-info-ttl">Order details</div>
                <div class="login-info-i">
                    <div class="login-info-row">
                        <div class="login-info-col">Items</div>
                        <div class="login-info-points">........................................................................................................................................................................................................................................................................................................</div>
                        <div class="login-info-col__count">{{$order->items_count}}</div>
                    </div>
                    <div class="login-info-row">
                        <div class="login-info-col">Subtotal</div>
                        <div class="login-info-points">........................................................................................................................................................................................................................................................................................................</div>
                        <div class="login-info-col__count">${{$order->subtotal}}</div>
                    </div>
                    @if($order->tax)
                        <div class="login-info-row">
                            <div class="login-info-col">Tax</div>
                            <div class="login-info-points">........................................................................................................................................................................................................................................................................................................</div>
                            <div class="login-info-col__count">${{$order->tax}}</div>
                        </div>
                    @endif
                    @if($order->discount)
                        <div class="login-info-row">
                            <div class="login-info-col">Discount <br>({{$order->discount_name}})</div>
                            <div class="login-info-points">........................................................................................................................................................................................................................................................................................................</div>
                            <div class="login-info-col__count">(${{$order->total_coupon}})</div>
                        </div>
                    @endif
                    <div class="login-info-row __bold">
                        <div class="login-info-col">Total</div>
                        <div class="login-info-points">........................................................................................................................................................................................................................................................................................................</div>
                        <div class="login-info-col__count">${{$order->total}}</div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{route('pay')}}" class="login-form js_form-pay" novalidate>
                <div class="login-form-ttl">Enter card details</div>
                <div class="wrap-inp">
                    <label for="card" class="wrap-inp-lbl">Card number</label>
                    <input type="text" class="js_number" id="card" name="card_number" placeholder="Card number" required>
                </div>

                <div class="wrap-inp">
                    <label for="month" class="wrap-inp-lbl">mm/yy</label>
                    <input type="tel" class="js_date" id="month" name="expiration_date" placeholder="MM/YY" required>
                </div>
                <div class="wrap-inp">
                    <label for="cvc" class="wrap-inp-lbl">CVV</label>
                    <input type="tel" class="js_cvv" id="cvc" name="code" placeholder="CVV" maxlength="3" required>
                </div>

                <input type="hidden" name="order_id" value="{{ $order->id}}">
                <input type="hidden" name="form_type" value="Card">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <div class="wrap-sub">
                    <button type="submit" class="btn-sub">Pay</button>
                </div>
                <div class="form-status with_error">
                    <p class="form-status-hidden"></p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
