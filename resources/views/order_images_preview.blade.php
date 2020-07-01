@extends('core.base')

{{--Header--}}
@include('parts/_header-order')

@section('content')
    <div class="container preview-img-blk">

        @isset($image_url)
            <div class="preview-img">
                <img src="{{ $image_url }}" alt="">
            </div>
        @endisset

        @isset($downloadableItems)
            @foreach($downloadableItems as $photo)
                <div class="preview-img">
                    <img src="{{ $photo->present()->originalUrl() }}" alt="">
                </div>
            @endforeach
        @endisset
        <a class="btn btn-gray"  href="{{route('order_page', $order->id)}}"><i class="btn-gray-ic"></i> Back to order</a>
    </div>
@endsection


