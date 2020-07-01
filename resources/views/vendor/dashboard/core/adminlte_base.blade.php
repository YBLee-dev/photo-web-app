@extends('dashboard::core.base')

@push('styles')
    <link rel="stylesheet" href="{{asset('webmagic/dashboard/css/style.css')}}">
    {{--<link rel="stylesheet" href="{{asset('webmagic/dashboard/css/bootstrap.min.css')}}">--}}
    <!-- Font Awesome -->
    {{--<link rel="stylesheet" href="{{asset('webmagic/dashboard/css/font-awesome.min.css')}}">--}}
    {{--<link rel="stylesheet" href="{{asset('webmagic/dashboard/css/AdminLTE.min.css')}}">--}}
    {{--<link rel="stylesheet" href="{{asset('webmagic/dashboard/css/_all-skins.min.css')}}">--}}
    <!-- Google Font -->
    <link rel="stylesheet"
      href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <style>
        .form-status:not(.with_error){
            display: none;
        }
    </style>
@endpush
@push('scripts')
<script src="{{asset('webmagic/dashboard/js/libs.js')}}"></script>
<script src="{{asset('webmagic/dashboard/js/script.js')}}"></script>
{{--<script src="//cdn.ckeditor.com/4.11.1/full/ckeditor.js"></script>--}}
<script src="//cdn.ckeditor.com/4.11.1/standard/ckeditor.js"></script>
@endpush
