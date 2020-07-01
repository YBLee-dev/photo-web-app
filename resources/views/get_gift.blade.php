@extends('core.base')

@section('content')

<form action="{{ route('set-free-gift') }}" method="POST" class="order-gift-form js_submit-review-email">
    <input type="hidden" name="order_id" value="{{$order_id}}">
    <input type="hidden" name="form_type" value="Add gift">
    <input type="hidden" name="_token" value="{{csrf_token()}}">
    <button type="submit"
            class="btn-transparent js_review-add-email"
            data-href="https://search.google.com/local/writereview?placeid=ChIJjdBqOS9u3IAR1QprQRqkhus"></button>
</form>
@endsection
