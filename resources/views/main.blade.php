@extends('core.base')

{{--Header--}}
@include('parts/_header')

@section('content')
    <div class="login">
        <div class="login-container container">
            <div class="login-cnt">
                <h1 class="login-cnt-ttl">View My Gallery</h1>
                <form method="POST" action="{{route('check_code')}}" class="login-form js_form-code" novalidate>
                    <div class="login-form-ttl">Online Code</div>
                    <div class="wrap-inp">
                        <label for="code" class="inp-lbl"></label>
                        <input type="text" id="code" name="code" placeholder="Enter code" required>
                    </div>
                    <input type="hidden" name="form_type" value="Enter code">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <div class="wrap-sub">
                        <button type="submit" class="btn-sub">Enter</button>
                    </div>
                    <div class="form-status with_error">
                        <p class="form-status-hidden"></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection