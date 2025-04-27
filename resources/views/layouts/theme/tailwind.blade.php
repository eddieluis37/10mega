<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>ERP - CarnesFrias Mega</title>
    <link rel="icon" type="image/x-icon" href="{{asset('assets/img/favicon.ico')}}" />
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    @include('layouts.theme.styles')
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->

</head>

<body class="dashboard-analytics">

    <!--  BEGIN MAIN CONTAINER  -->

    <!--  BEGIN CONTENT AREA  -->
  

        <div class="">

            <!-- Bloque de mensajes flash -->
            @if(session('success'))
            <div id="flash-message-success" class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div id="flash-message-error" class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <script>
                // Utilizando vanilla JavaScript para ocultar los mensajes después de 15 segundos (15000 ms)
                setTimeout(function() {
                    var flashSuccess = document.getElementById('flash-message-success');
                    if (flashSuccess) {
                        flashSuccess.style.display = 'none';
                    }
                    var flashError = document.getElementById('flash-message-error');
                    if (flashError) {
                        flashError.style.display = 'none';
                    }
                }, 15000);
            </script>



        </div>

        @yield('content')


  
    <!--  END CONTENT AREA  -->



    <!-- END MAIN CONTAINER -->

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    @include('layouts.theme.scripts')
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->

    <!--SCRIPTS FOR EACH COMPONENT-->
    @yield('script')
</body>

</html>