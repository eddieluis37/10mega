<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>ERP - CarnesFrias Mega</title>
    <link rel="icon" type="image/x-icon" href="{{asset('assets/img/favicon.ico')}}" />


    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    @include('layouts.theme.styles')
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->

</head>

<body class="dashboard-analytics">
    @include('layouts.theme.header')
    @include('layouts.theme.sidebar')

    <div id="app" class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <productos-component></productos-component>
            </div>
            @include('layouts.theme.footer')
        </div>
    </div>

    @include('layouts.theme.scripts')
</body>

</html>