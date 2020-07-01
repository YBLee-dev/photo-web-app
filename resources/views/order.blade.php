@extends('core.base')

{{--Header--}}
@include('parts/_header-order')

@section('content')
    <div class="order">
        <div class="order-container">
            <div class="order-cnt">
                <div class="container">
                    @if(\App\Ecommerce\Orders\OrderPaymentStatusEnum::PAID()->is($order->payment_status))
                        <div class="order-message __success">
                            Your transaction is complete
                        </div>
                    @else
                        <div class="order-message __error">
                            Your transaction is not complete
                        </div>
                    @endif

                    @if(!$order->gallery->isDeadlineCame())
                        @if($order->free_gift)
                            <div class="order-message __success">
                                Thank you for your online review. Your free gift will be included with your order
                            </div>
                        @else
                            <div class="order-message __error @if($order->status == 'New') __add-btn @endif">
                                <span>No online review. No free gift included with order</span>
                                @if($order->status == 'New')
                                    <form action="{{ route('set-free-gift') }}" method="POST" class="order-gift-form js_submit-review">
                                        <input type="hidden" name="order_id" value="{{$order->id}}">
                                        <input type="hidden" name="form_type" value="Add gift">
                                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                                        <button type="submit"
                                                class="btn-sub-warning order-message-link js_review-add"
                                                data-href="https://search.google.com/local/writereview?placeid=ChIJjdBqOS9u3IAR1QprQRqkhus">Leave a review</button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    @endif
                    <div class="order-cnt-t">
                        <h1 class="order-cnt-ttl">Thank you <span class="order-cnt-subttl">for your purchase!</span></h1>
                        <p  class="order-cnt-txt">You have also been sent an email regarding your order. If you do not receive the email within 30 minutes please check your spam / bulk folders. If you still do not have it after an hour contact us to resend it to you.</p>
                    </div>

                    <div class="order-info">
                        <div class="order-info-card">
                            <div class="order-info-name">Date:</div>
                            <img src="{{asset('img/Icon-date.png')}}" class="order-info-img" alt="order date">
                            <div class="order-info-ttl">{{$order->created_at->format('M j, Y')}}</div>
                        </div>

                        <div class="order-info-card">
                            <div class="order-info-name">Order Number:</div>
                            <img src="{{asset('img/Icon-order.png')}}" class="order-info-img" alt="order number">
                            <div class="order-info-ttl">{{$order->id}}</div>
                        </div>

                        <div class="order-info-card">
                            <div class="order-info-name">Total Amount:</div>
                            <img src="{{asset('img/icon-total.png')}}" class="order-info-img" alt="order total">
                            <div class="order-info-ttl">${{$order->total}}</div>
                        </div>

                        <div class="order-info-card">
                            <div class="order-info-name">Order Payment:</div>
                            <img src="{{asset('img/Icon-payment.png')}}" class="order-info-img" alt="order payment status">
                            <div class="order-info-ttl">{{$order->payment_status}}</div>
                        </div>

                        <div class="order-info-card">
                            <div class="order-info-name">Order Status:</div>
                            <img src="{{asset('img/icon-delivery.png')}}" class="order-info-img" alt="order status">
                            <div class="order-info-ttl green bold">{{$order->status}}</div>
                        </div>
                    </div>

                    <div class="order-details">
                        <h2 class="order-details-ttl gray bold">Order details</h2>
                        <div class="order-details-cnt">
                            <div class="order-details-l">
                                <div class="order-details-subttl brown bold">Billing Address</div>
                                <ul class="order-lst">
                                    <li class="order-i">
                                        <span class="order-i-l gray">name</span>
                                        <span class="order-i-r">{{$order->customer_first_name .' '. $order->customer_last_name}}</span>
                                    </li>
                                    <li class="order-i">
                                        <span class="order-i-l gray">email</span>
                                        <span class="order-i-r">{{$order->customer->email}}</span>
                                    </li>
                                    <li class="order-i">
                                        <span class="order-i-l gray">address</span>
                                        <span class="order-i-r">{{$order->address}}, {{$order->city}}, {{$order->state}}, {{$order->postal}}, {{$order->country}}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="order-details-r">
                                <div class="order-details-subttl brown bold">Pick up instructions</div>
                                <div class="order-details-txt default-cnt">
                                    {!!html_entity_decode($text->delivery ?? '')!!}
                                    {!!html_entity_decode($text_warning ?? '')!!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @foreach($packages as $package)

                <!-- ONE PACKAGE -->
                    <div class="order-product">
                        <div class="order-product-cnt container">
                            <div class="order-price-tbl">
                                <div class="order-price-tbl-row __header">
                                    <div class="order-price-tbl-row-i u-Flex-grow-2">
                                        <div class="order-price-ttl brown">Product</div>
                                        <div class="order-price-subttl __L brown">{{$package[0]->package_name}}
                                            <div class="order-price-ttl gray __mobile">Name <span class="order-price-ttl-arrow"></span> {{$package[0]->subGallery()->first()->name}}</div>
                                        </div>
                                        <div class="order-price-arrow js_ui-accordion-ttl __mobile __active"></div>
                                    </div>
                                    <div class="order-price-tbl-row-i u-Flex-grow-6 __desktop">
                                        <div class="order-price-ttl gray">Name <span class="order-price-ttl-arrow"></span> {{$package[0]->subGallery()->first()->name}}</div>
                                    </div>
                                    <div class="order-price-tbl-row-i u-Flex-grow-1">
                                        <div class="order-price-ttl brown text-right __middle">Price</div>
                                        <div class="order-price-subttl gray text-right">${{$package[0]->price}}</div>
                                    </div>
                                    <div class="order-price-tbl-row-i u-Flex-grow-1">
                                        <div class="order-price-ttl brown text-right __middle">Qty</div>
                                        <div class="order-price-subttl gray text-right">{{$package[0]->quantity}}</div>
                                    </div>
                                    <div class="order-price-tbl-row-i __relative u-Flex-grow-1">
                                        <div class="order-price-ttl brown text-right __middle">Extended</div>
                                        <div class="order-price-subttl red text-right">${{$package[0]->sum}}</div>
                                        <div class="order-price-arrow js_ui-accordion-ttl __desktop __active"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="order-price-cnt js_ui-accordion-cnt __show" style="display: block">
                                <div class="product-row">

                                    @foreach($package as $product)
                                        @if(!$product->isDownloadable())
                                            <div class="product-col">
                                                <div class="product-card">
                                                    <div class="product-card-t">
                                                        <img src="{{$product->image}}" alt="" class="product-card-img">
                                                        @if($product->size)
                                                            <div class="product-card-size">{{$product->size->name}}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($product->isDigital() || $product->isDigitalFull()) {{--  Digital product in package --}}
                                            <div class="product-col __L">
                                                <div class="order-wrap">
                                                    <div class="order-preview">

                                                        <div class="order-price-tbl-row-i u-Flex-grow-2 order-preview-ttl brown">{{$product->name}}</div>

                                                        <div class="order-price-tbl-row-i u-Flex-grow-6 order-preview-cnt __digital-product-image">
                                                            @foreach($product->subGallery()->first()->photos as $image)
                                                                <div class="order-preview-img">
                                                                    <img src="{{ $image->present()->previewUrl() }}" alt="">
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <div class="order-download-wrap order-price-tbl-row-i u-Flex-grow-2 js-get-info-zip-cnt __digital-product" @if(! $product->order()->first()->isDigitalZipPrepared())style="display: none" @endif>
                                                            <a href="{{ $product->order()->first()->present()->zipDigitalUrl() }}" class="product-card-btn js_zip-download" download="download">Download all photos  <br><span class="hidden-xs">(zip file: advanced)</span></a>
                                                            <a href="{{route('downloadable-photos-page', $product->order()->first()->id)}}" class="product-card-btn js_mob-preview-images" >Download photos <span class="hidden-xs">(Individually: easy)</span></a>
                                                        </div>
                                                        @if(! $product->order()->first()->isDigitalZipPrepared())
                                                            <div class="order-details-download blue bold js-get-info-zip" data-action="{{ $order->routs()->zipDigitalPreparingStatus()}}" data-method="GET">
                                                                <span class="order-loading"> <i class="form-loading"></i></span>
                                                                Photos are preparing
                                                            </div>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($product->isSingleDigital()) {{--  Single Digital product in package --}}
                                        <div class="product-col __L">
                                            <div class="order-wrap">
                                                <div class="order-preview">

                                                    <div class="order-price-tbl-row-i u-Flex-grow-2 order-preview-ttl brown">{{$product->name}}</div>

                                                    <div class="order-price-tbl-row-i u-Flex-grow-6 order-preview-cnt __digital-product-image">
                                                            <div class="order-preview-img">
                                                                <img src="{{ $product->image }}" alt="">
                                                            </div>
                                                    </div>

                                                    <div class="order-download-wrap order-price-tbl-row-i u-Flex-grow-2 js-get-info-zip-cnt __digital-product">
                                                        <a href="{{route('downloadable-photo-page', $product)}}" class="product-card-btn js_mob-preview-images" >Download photos <span class="hidden-xs">(Individually: easy)</span></a>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END ONE PACKAGE -->
            @endforeach

            @foreach($addons as $product)
                <!-- ONE ADDON -->
                    <div class="order-product">
                        <div class="order-product-cnt container">
                            <div class="order-price-tbl">
                                <div class="order-price-tbl-row __header">
                                    <div class="order-price-tbl-row-i u-Flex-grow-2">
                                        <div class="order-price-ttl brown">Product</div>
                                        <div class="order-price-subttl __L brown">{{$product->name}}
                                            @if(!$product->retouch && !$product->isDownloadable())
                                                <div class="order-price-ttl gray __mobile">Name <span class="order-price-ttl-arrow"></span> {{$product->order()->first()->subgallery->name}}</div>
                                            @elseif($product->isDownloadable())
                                                <div class="order-download-wrap order-price-tbl-row-i u-Flex-grow-2 js-get-info-zip-cnt" @if(! $product->order()->first()->isDigitalZipPrepared())style="display: none" @endif>
                                                    <a href="{{ $product->order()->first()->present()->zipDigitalUrl() }}" class="product-card-btn js_zip-download" download="download">Download all photos
                                                        <br><span class="hidden-xs">(zip file: advanced)</span></a>
                                                    <a href="{{route('downloadable-photos-page', $product->order()->first()->id)}}" class="product-card-btn js_mob-preview-images" >Download photos  <span class="hidden-xs">(Individually: easy)</span></a>
                                                </div>
                                                @if(! $product->order()->first()->isDigitalZipPrepared())
                                                    <div class="order-details-download blue bold js-get-info-zip" data-action="{{ $product->order()->first()->routs()->zipDigitalPreparingStatus()}}" data-method="GET">
                                                        <span class="order-loading"> <i class="form-loading"></i></span>
                                                        Photos are preparing
                                                    </div>
                                                @endif
                                            @endif
                                        </div>

                                        @if($product->retouch || $product->isDownloadable())
                                            <!-- FOR RETOUCH PRODUCT -->
                                                <div class="order-price-ttl"></div>
                                                <!-- END FOR RETOUCH PRODUCT -->
                                        @else
                                            <!-- FOR ALL PRODUCT-->
                                            <div class="order-price-arrow js_ui-accordion-ttl __mobile __active"></div>
                                            <!-- END FOR ALL PRODUCT-->
                                        @endif

                                    </div>
                                    @if(!$product->retouch && !$product->isDownloadable())
                                        <div class="order-price-tbl-row-i u-Flex-grow-6 __desktop">
                                            <div class="order-price-ttl gray">Name <span class="order-price-ttl-arrow"></span> {{$product->order()->first()->subgallery->name}}</div>
                                        </div>
                                    @endif

                                    @if($product->retouch)
                                        <!-- FOR RETOUCH PRODUCT -->
                                        <div class="order-price-tbl-row-i u-Flex-grow-6">
                                            <div class="order-price-comment">
                                                {{ $product->retouch }}
                                            </div>
                                        </div>

                                        <div class="order-price-tbl-row-i u-Flex-grow-1">
                                            <div class="order-price-ttl brown text-right __middle">Price</div>
                                            <div class="order-price-subttl gray text-right">${{$product->price}}</div>
                                        </div>
                                        <div class="order-price-tbl-row-i u-Flex-grow-1">
                                            <div class="order-price-ttl brown text-right __middle">Qty</div>
                                            <div class="order-price-subttl gray text-right">{{$product->quantity}}</div>
                                        </div>
                                        <div class="order-price-tbl-row-i __relative u-Flex-grow-1">
                                            <div class="order-price-ttl brown text-right __middle">Extended</div>
                                            <div class="order-price-subttl red text-right">${{$product->sum}}</div>
                                        </div>
                                        <!-- END FOR RETOUCH PRODUCT -->
                                    @elseif($product->isDigitalFull() || $product->isDigital())
                                        <div class="order-price-tbl-row-i u-Flex-grow-6">

                                            <div class="order-wrap">
                                                <div class="order-preview">
                                                    <div class="order-price-tbl-row-i order-preview-cnt">
                                                        @foreach($product->subGallery()->first()->photos as $image)
                                                            <div class="order-preview-img">
                                                                <img src="{{ $image->present()->previewUrl() }}" alt="">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="order-price-tbl-row-i u-Flex-grow-1">
                                            <div class="order-price-ttl brown text-right __middle">Price</div>
                                            <div class="order-price-subttl gray text-right">${{$product->price}}</div>
                                        </div>
                                        <div class="order-price-tbl-row-i u-Flex-grow-1">
                                            <div class="order-price-ttl brown text-right __middle">Qty</div>
                                            <div class="order-price-subttl gray text-right">{{$product->quantity}}</div>
                                        </div>
                                        <div class="order-price-tbl-row-i __relative u-Flex-grow-1">
                                            <div class="order-price-ttl brown text-right __middle">Extended</div>
                                            <div class="order-price-subttl red text-right">${{$product->sum}}</div>
                                        </div>
                                    @else
                                    <!-- FOR ALL PRODUCT-->
                                        <div class="order-price-tbl-row-i u-Flex-grow-1">
                                            <div class="order-price-ttl brown text-right __middle">Price</div>
                                            <div class="order-price-subttl gray text-right">${{$product->price}}</div>
                                        </div>
                                        <div class="order-price-tbl-row-i u-Flex-grow-1">
                                            <div class="order-price-ttl brown text-right __middle">Qty</div>
                                            <div class="order-price-subttl gray text-right">{{$product->quantity}}</div>
                                        </div>
                                        <div class="order-price-tbl-row-i __relative u-Flex-grow-1">
                                            <div class="order-price-ttl brown text-right __middle">Extended</div>
                                            <div class="order-price-subttl red text-right">${{$product->sum}}</div>
                                            @if(!$product->isDownloadable())
                                                <div class="order-price-arrow js_ui-accordion-ttl __desktop __active"></div>
                                            @endif
                                        </div>
                                        <!-- END FOR ALL PRODUCT-->
                                    @endif

                                </div>
                            </div>
                            @if(!$product->retouch && !$product->isDownloadable())
                                <div class="order-price-cnt js_ui-accordion-cnt __show" style="display: block">
                                    <div class="product-row">
                                        <div class="product-col">
                                            <div class="product-card">
                                                <div class="product-card-t">
                                                    <img src="{{$product->image}}" alt="" class="product-card-img">
                                                    @if($product->size)
                                                        <div class="product-card-size">{{$product->size->name}}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- END ONE ADDON -->
                @endforeach

                <div class="order-price-b">
                    <div class="order-price-tbl container">
                        <div class="order-price-tbl-row">
                            <div class="order-price-tbl-row-i u-Flex-grow-7 __no-pr hidden-md">
                                <div class="order-price-ttl"></div>
                            </div>
                            <div class="order-price-tbl-row-i u-Flex-grow-3 __pr">
                                <div class="order-price-ttl brown">Subtotal</div>
                            </div>

                            <div class="order-price-tbl-row-i u-Flex-grow-2 __no-pr">
                                <div class="order-price-ttl text-right">${{$order->subtotal}}</div>
                            </div>
                        </div>

                        @if($order->tax)
                            <div class="order-price-tbl-row">
                                <div class="order-price-tbl-row-i u-Flex-grow-7 __no-pr hidden-md">
                                    <div class="order-price-ttl"></div>
                                </div>
                                <div class="order-price-tbl-row-i u-Flex-grow-3 __pr">
                                    <div class="order-price-ttl brown">Tax</div>
                                </div>

                                <div class="order-price-tbl-row-i u-Flex-grow-2 __no-pr">
                                    <div class="order-price-ttl text-right">${{$order->tax}}</div>
                                </div>
                            </div>
                        @endif

                        @if($order->discount_name)
                            <div class="order-price-tbl-row">
                                <div class="order-price-tbl-row-i u-Flex-grow-7 __no-pr hidden-md">
                                    <div class="order-price-ttl"></div>
                                </div>
                                <div class="order-price-tbl-row-i u-Flex-grow-3 __pr">
                                    <div class="order-price-ttl brown">Discount ({{$order->discount_name}})</div>
                                </div>

                                <div class="order-price-tbl-row-i u-Flex-grow-2 __no-pr">
                                    <div class="order-price-ttl text-right">${{$order->total_coupon}}</div>
                                </div>
                            </div>
                        @endif

                        <div class="order-price-tbl-row __bottom">
                            <div class="order-price-tbl-row-i u-Flex-grow-7 __no-pr hidden-md">
                                <div class="order-price-ttl"></div>
                            </div>
                            <div class="order-price-tbl-row-i u-Flex-grow-3 __pr">
                                <div class="order-price-ttl __L red">Grand Total</div>
                            </div>

                            <div class="order-price-tbl-row-i u-Flex-grow-2 __no-pr">
                                <div class="order-price-ttl __L text-right red">${{$order->total}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--if order has package digital--}}
    @if($order->isDigital() || $order->isDigitalFull())
        {{-- MODAL FOR DIGITAL PACKAGE--}}
        <div id="modal-digital-package" class="popup mfp-hide">
            <a href="#" class=" popup-btn-close js_mfpopup-popup-close" aria-label="Close"></a>

            <div class="order-details-subttl brown bold txt-center">You can download all photos</div>

            <div class="txt-center popup-b">
                <div class="order-download-wrap js-get-info-zip-cnt" @if(! $order->isDigitalZipPrepared())style="display: none" @endif>
                    <a href="{{ $order->present()->zipDigitalUrl() }}" class="product-card-btn js_zip-download " download="download">Download all photos <span class="hidden-xs">(zip file: advanced)</span></a>
                    <p class="mt-10">
                        <a href="{{route('downloadable-photos-page', $order->id)}}" class="product-card-btn js_mob-preview-images " >Download photos <span class="hidden-xs">(Individually: easy)</span></a>
                    </p>
                </div>
                @if(! $order->isDigitalZipPrepared())
                    <div class="order-details-download blue bold js-get-info-zip" data-action="{{ $order->routs()->zipDigitalPreparingStatus()}}" data-method="GET">
                        <span class="order-loading"> <i class="form-loading"></i></span>
                        Photos are preparing
                    </div>
                @endif
            </div>
        </div>
        {{-- END MODAL FOR DIGITAL PACKAGE--}}
    @endif
@endsection
