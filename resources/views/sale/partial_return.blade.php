@extends('layouts.theme.app')
@section('content')
<div class="container mt-4">
  <h4>Devolución Parcial de Venta #{{ $sale->id }}</h4>
  <form action="{{ route('sale.partial_return') }}" method="POST">
    @csrf
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
          <td>{{ $detail->product->name }}</td>
          <td class="text-center">{{ number_format($detail->quantity,2) }}</td>
          <td class="text-center">
            <input type="number"
                   name="returns[{{ $detail->id }}]"
                   step="0.01" min="0"
                   max="{{ $detail->quantity }}"
                   class="form-control" placeholder="0">
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <div class="form-group mt-3">
      <label for="forma_pago">Forma de pago de la devolución</label>
      <select name="forma_pago" id="forma_pago" class="form-control" required>
        <option value="">Seleccione...</option>
        @foreach(App\Models\Formapago::efectivoTarjeta()->get() as $fp)
          <option value="{{ $fp->id }}">{{ $fp->nombre }}</option>
        @endforeach
      </select>
    </div>

    <div class="text-right mt-3">
      <button type="submit" class="btn btn-primary">Procesar Devolución Parcial</button>
    </div>
  </form>
</div>
@endsection