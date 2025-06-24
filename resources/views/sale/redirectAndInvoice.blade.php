<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Redireccionando...</title>
</head>
<body>
    <p>{{ session('success') }}</p>
    <p>Si no se abre la factura automáticamente, por favor haga clic en el siguiente enlace:</p>
    <a id="facturaLink" href="{{ url('sale/showFactura/'.$ventaId) }}" target="_blank">Abrir Factura</a>

    <script>
        window.onload = function() {
            // Abrir la factura en una nueva pestaña
            document.getElementById('facturaLink').click();

            // Calcular la URL de redirección según el tipo de venta
            let redirectUrl = "{{ $tipoVenta == 2 || $tipoVenta == 3
                ? route('sale.index_parrilla')
                : route('sale.index') }}";

            // Redirigir después de un pequeño retardo (para asegurar que abra la factura)
            setTimeout(function(){
                window.location.href = redirectUrl;
            }, 500);
        }
    </script>
</body>
</html>
