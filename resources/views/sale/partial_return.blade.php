@extends('layouts.theme.app')
@section('content')
<div class="container mt-4">
    <h4>Devolución Parcial de Venta #{{ $sale->id }}</h4>
    <!-- El formulario envía la información a la ruta definida -->
    <form action="{{ route('sale.partial-return') }}" method="POST">
        @csrf
        <input type="hidden" name="ventaId" value="{{ $sale->id }}">
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
                        <!-- Se crea un arreglo de retornos, uno por detalle -->
                        <input type="number" step="0.01" min="0" max="{{ $detail->quantity }}"
                            name="returns[{{ $detail->id }}]" class="form-control" placeholder="0" required>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="text-right">
            <!-- Sin JavaScript, el botón simplemente envía el formulario -->
            <button type="submit" class="btn btn-primary">Procesar Devolución Parcial</button>
        </div>
    </form>
</div>
@endsection
