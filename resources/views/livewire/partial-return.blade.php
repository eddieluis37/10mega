<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu aplicación</title>
    <!-- Tus estilos CSS -->
    <link rel="stylesheet" href="{{ public_path('css/app.css') }}">
    @livewireStyles
</head>
<body>


<h1> eddie </h1>
    @yield('content')

    
    
    <!-- Tus scripts JS -->
    <script src="{{ public_path('js/app.js') }}"></script>
    @livewireScripts
</body>
</html>
