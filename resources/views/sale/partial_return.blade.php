@extends('layouts.theme.app')
@section('content')
<div class="container mt-4">
    <h4>Devolución Parcial de Venta #{{ $sale->id }}</h4>
    <form action="{{ route('sale.partial_return') }}" method="POST">
        @csrf
        <!-- Se envía el ID de la venta -->
        <input type="hidden" name="ventaId" value="{{ $sale->id }}">
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
                    <!-- Se muestra el nombre del producto a través de la relación -->
                    <td>{{ $detail->product->name }}</td>
                    <td class="text-center">{{ number_format($detail->quantity, 2) }}</td>
                    <td class="text-center">
                        <!-- Se envía el store_id correspondiente a este detalle -->
                        <input type="hidden" name="store_ids[{{ $detail->id }}]" value="{{ $detail->store_id }}">
                        <!-- Se crea un arreglo de retornos, uno por detalle -->
                        <input type="number" step="0.01" min="0" max="{{ $detail->quantity }}"
                            name="returns[{{ $detail->id }}]" class="form-control" placeholder="0" required>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="text-right">
            <!-- Botón que envía el formulario -->
            <button type="submit" class="btn btn-primary">Procesar Devolución Parcial</button>
        </div>
    </form>
</div>
@endsection
