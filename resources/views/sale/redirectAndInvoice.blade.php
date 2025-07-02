<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Redireccionando...</title>
</head>
<body>
    @php
        switch ($tipoVenta) {
            case 2:
            case 3:
                $redirectUrl = route('sale.index_parrilla');
                break;
            case 4:
            case 5:
                $redirectUrl = route('sale.index_autoservicio');
                break;
            default:
                $redirectUrl = route('sale.index');
        }
    @endphp

    <p>{{ session('success') }}</p>
    <p>Si no se abre la factura automáticamente, por favor haga clic en el siguiente enlace:</p>
    <a id="facturaLink" href="{{ url('sale/showFactura/'.$ventaId) }}" target="_blank">Abrir Factura</a>

    <script>
        window.onload = function() {
            // Abrir la factura en una nueva pestaña
            document.getElementById('facturaLink').click();

            // Redirigir tras un pequeño retardo (para asegurar que abra la factura)
            setTimeout(function(){
                window.location.href = "{{ $redirectUrl }}";
            }, 500);
        }
    </script>
</body>
</html>
