<!doctype html>
<html lang="@yield('language', config('app.locale'))">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    {{-- Styles loading --}}
    @stack('styles')
    @stack('after-styles')
    {{-- END Styles loading --}}
</head>
<body class="@yield('body_class') {{$class}}" {!! $dynamic_fields !!}>
    <div class="wrapper">

    {{-- Content area --}}
    @yield('base_content')

    <div class="col-xs-12 alert-section"></div>
    </div>


    @include('dashboard::components._modal')

    {{-- Scripts area --}}
    <script id="data-locale" type="application/json">
        {"locale": "{{App::getLocale()}}"}
    </script>
    <div style="display: none;" id="_token-csrf">{{csrf_token()}}</div>

    {{-- Scripts loading --}}
    <script>
        if(localStorage.getItem("sidebarCollapse") == 'true'){
            document.getElementsByTagName('body')[0].classList += ' ' + 'sidebar-collapse';
        }
    </script>
    @stack('scripts')
    @stack('after-scripts')
    <script>

        let $selectWithDynamicOption = $('.js-select2-dynamic-options');
        $selectWithDynamicOption.select2({
            tags: true,
            dropdownParent: $selectWithDynamicOption.closest('form')
        });

        let timerUpdate;
        let input = $('.js_autoupdate').find('input');

        reloadPage = function(){

            let autoupdate = localStorage.getItem("autoupdate");


            if(autoupdate === 'false'){
                autoupdate = false;
            }

            $(input).prop('checked', autoupdate);

            if(autoupdate){
                timerUpdate = setTimeout(function () {
                    location.reload();
                }, 10000)
            }
        };
        reloadPage();

        $(input).on('change', function () {

            let autoupdate = $(this).prop('checked');

            localStorage.setItem("autoupdate", autoupdate);

            reloadPage();

            if(!autoupdate){
                clearTimeout(timerUpdate);
            }
        });
    </script>
    {{-- END Scripts --}}
</body>
</html>
