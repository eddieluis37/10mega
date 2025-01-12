<!-- resources/views/inventario-inicial.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Inicial</title>
    <!-- Opcional: Incluye Bootstrap o estilos CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Registrar Inventario Inicial</h1>

        <form action="{{ route('inventario.inicial') }}" method="POST" id="inventarioForm">
            @csrf <!-- Protección contra CSRF -->

            <div class="mb-3">
                <label for="store_id" class="form-label">Bodega</label>
                <select name="store_id" id="store_id" class="form-select" required>
                    <option value="" disabled selected>Seleccione una bodega</option>
                    <!-- Opcional: Pasa las bodegas desde el controlador -->
                    @foreach ($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="lote_id" class="form-label">Lote</label>
                <select name="lote_id" id="lote_id" class="form-select" required>
                    <option value="" disabled selected>Seleccione un lote</option>
                    @foreach ($lotes as $lote)
                        <option value="{{ $lote->id }}">{{ $lote->codigo }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="producto_id" class="form-label">Producto</label>
                <select name="producto_id" id="producto_id" class="form-select" required>
                    <option value="" disabled selected>Seleccione un producto</option>
                    @foreach ($productos as $producto)
                        <option value="{{ $producto->id }}">{{ $producto->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="cantidad" class="form-label">Cantidad Inicial</label>
                <input type="number" name="cantidad" id="cantidad" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Inventario</button>
        </form>
    </div>

    <!-- Opcional: Scripts para JS -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const form = document.getElementById('inventarioForm');
        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Evita que el formulario se envíe de forma predeterminada

            const formData = new FormData(form);

            axios.post('{{ route('inventario.inicial') }}', Object.fromEntries(formData))
                .then(response => {
                    alert('Inventario registrado correctamente');
                    form.reset(); // Reinicia el formulario
                })
                .catch(error => {
                    alert('Error al registrar el inventario');
                    console.error(error);
                });
        });
    </script>
</body>
</html>
