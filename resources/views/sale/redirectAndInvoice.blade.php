<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Redireccionando...</title>
</head>
<body>
    <p>{{ session('success') }}</p>
    <p>Si no se abre la factura automáticamente, por favor haga clic en el siguiente enlace:</p>
    <a id="facturaLink" href="/sale/showFactura/{{ $ventaId }}" target="_blank">Abrir Factura</a>
    <script>
        window.onload = function() {
            // Simular clic en el enlace
            document.getElementById('facturaLink').click();
            // Luego redirigir a sale.index
            window.location.href = "{{ route('sale.index') }}";
        }
    </script>
</body>
</html>
