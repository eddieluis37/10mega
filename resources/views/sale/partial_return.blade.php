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
<script src="{{asset('rogercode/js/sale/partial-return.js')}}" type="module"></script>
@endsection