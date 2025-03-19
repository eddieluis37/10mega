@extends('layouts.theme.app')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mt-4">
    <h4>Devolución Parcial de Venta #{{ $sale->id }}</h4>
    <form id="partialReturnForm">
        @csrf
        <input type="hidden" name="ventaId" value="{{ $sale->id }}">
        <!-- En caso de requerir store_id para el inventario -->
        <input type="hidden" name="store_id" value="{{ $sale->store_id }}">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad Vendida</th>
                    <th>Cantidad a Devolver</th>
                </tr>
            </thead>
            <tbody>
                @foreach($saleDetails as $detail)
                <tr>
                    <td>{{ $detail->nameprod }}</td>
                    <td class="text-center">{{ number_format($detail->quantity, 2) }}</td>
                    <td class="text-center">
                        <!-- El input se llama "returns[detalle_id]" para formar un arreglo -->
                        <input type="number" step="0.01" min="0" max="{{ $detail->quantity }}"
                            name="returns[{{ $detail->id }}]" class="form-control" placeholder="0">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="text-right">
            <button type="button" class="btn btn-primary" onclick="confirmPartialReturnSubmit()">
                Procesar Devolución Parcial
            </button>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    // Espera a que el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        // Obtenemos el token CSRF
        var token = document.querySelector('meta[name="csrf-token"]').getAttribute("content");


        const sendDataForm = async (dataform, ruta) => {
            let response = await fetch(ruta, {
                headers: {
                    "X-CSRF-TOKEN": token,
                },
                method: "POST",
                body: dataform,
            });
            let data = await response.json();
            //console.log(data);
            return data;
        };

        // Definimos confirmPartialReturnSubmit y la asignamos a window para que esté disponible globalmente
        window.confirmPartialReturnSubmit = function() {
            swal({
                title: "Confirmar Devolución Parcial",
                text: "¿Estás seguro de procesar la devolución parcial?",
                icon: "warning",
                buttons: {
                    cancel: "Cancelar",
                    confirm: "Sí, procesar"
                },
                dangerMode: true,
            }).then(function(willProcess) {
                if (willProcess) {
                    var form = document.getElementById('partialReturnForm');
                    var dataform = new FormData(form);

                    sendDataForm(dataform, '/sale/partial-return', token)
                        .then(function(data) {
                            console.log("Respuesta del servidor:", data);
                            if (data.message) {
                                swal("Éxito", data.message, "success").then(function() {
                                    window.location.href = "/sales";
                                });
                            } else if (data.error) {
                                swal("Error", data.error, "error");
                            }
                        })
                        .catch(function(error) {
                            console.error("Error al procesar la devolución parcial:", error);
                            swal("Error", "Ocurrió un error al procesar la devolución.", "error");
                        });
                }
            });
        };
    });
</script>
@endsection