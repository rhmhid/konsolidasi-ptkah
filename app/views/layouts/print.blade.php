<!DOCTYPE html>
<html lang="id">
    <!--begin::Head-->
    <head>
        <base href="" />
        <title>{{ dataConfigs('app_name') }} - Based Integrated Application Management Solution</title>

        <meta charset="utf-8" />
        <meta name="description" content="{{ dataConfigs('app_name') }} - Based Integrated Application Management Solution" />
        <meta name="keywords" content="{{ dataConfigs('app_name_long') }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <link rel="shortcut icon" href="{{ asset(dataConfigs('app_icon')) }}" />

        <!--begin::Global Stylesheets Bundle(used by all pages)-->
        <link rel="stylesheet/less" type="text/css" media="all" href="{{ asset('assets/prints/styles/reset.css') }}" />
        <link rel="stylesheet/less" type="text/css" media="all" href="{{ asset('assets/prints/styles/prints-fancy.css') }}" />
        <!--end::Global Stylesheets Bundle-->

        <script type="text/javascript" src="{{ asset('assets/prints/scripts/jquery.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/custom/popUp.js') }}"></script>

        <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
        <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

        <script src="{{ asset('assets/js/custom/customs.js') }}"></script>

        @stack('css')

        @stack('script')
    </head>
    <!--end::Head-->

    <!--begin::Body-->
    <body class="bg-putih">
        @stack('function')

        <div id="previews" style="overflow: visible !important;">
            @stack('kop')

            @yield('content')

            @stack('sign')
        </div>
    </body>
    <!--end::Body-->
</html>