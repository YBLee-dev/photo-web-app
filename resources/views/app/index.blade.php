<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PlayfulApp</title>
    <base href="/">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
</head>
    <body>
        <app-root></app-root>
        <script type="text/javascript" src="{{asset('dist/runtime.js')}}{{"?v=".uniqid()}}"></script>
        <script type="text/javascript" src="{{asset('dist/polyfills.js')}}{{"?v=".uniqid()}}"></script>
        <script type="text/javascript" src="{{asset('dist/styles.js')}}{{"?v=".uniqid()}}"></script>
{{--        <script type="text/javascript" src="{{asset('dist/scripts.js')}}{{"?v=".uniqid()}}"></script>--}}
        <script type="text/javascript" src="{{asset('dist/vendor.js')}}{{"?v=".uniqid()}}"></script>
        <script type="text/javascript" src="{{asset('dist/main.js')}}{{"?v=".uniqid()}}"></script>
    </body>
</html>
