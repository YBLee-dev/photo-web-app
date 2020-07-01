<!DOCTYPE html>
<html lang="{{App::getLocale()}}">
<head>
    <!--[if IE 9]>
    <link rel="stylesheet" type="text/css" href="{{asset('css/ie.css')}}">

    <![endif]-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title></title>


    {{--base style css--}}
    @section('base-styles')
        @include('core.base_styles')
    @show

</head>
<body class="{{$body_class}}">
<div class="wrapper">
    <div class="cnt-wrap">

        {{--Content--}}
        @section('content')
            TEst
        @show
    </div>

    {{--Footer--}}
    @include('parts/_footer')
</div>
{{--Forms--}}
{{--@include('parts/_forms')--}}

{{--###Styles###--}}
<link rel="stylesheet" type="text/css" href="{{asset('css/style.css?version=2019-09-20T08:32:14.740Z')}}">
<link rel="stylesheet" type="text/css" href="{{asset('css/custom.css?version=2019-09-20T08:32:14.740Z')}}">
{{--###Styles###--}}

{{--###Scripts###--}}
<script src="{{asset('js/libs.js?version=2019-09-20T08:32:12.182Z')}}"></script>
<script src="{{asset('js/script.js?version=2019-09-20T08:32:12.182Z')}}"></script>
{{--###Scripts###--}}


{{--Counters--}}
@if(!app()->environment('local'))
    @include('parts/_counters')
@endif
<div style="display: none;">{{csrf_token()}}</div>
</body>
</html>
