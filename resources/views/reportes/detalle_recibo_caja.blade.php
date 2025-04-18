@extends('layouts.theme.tailwind')
@section('content')
<div class="container mx-auto py-6">
  <div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="bg-gradient-to-r from-blue-600 to-cyan-400 text-white p-4 text-center">
      <h2 class="text-xl font-semibold">REPORTE DETALLADO DEL RECIBO DE CAJA</h2>
    </div>

    <div class="flex flex-wrap bg-gray-100 p-4 border-b text-black">
      <div class="w-1/2 sm:w-1/4 mb-2"><strong>Recibo #:</strong> {{ $recibo->consecutivo ?? $recibo->id }}</div>
      <div class="w-1/2 sm:w-1/4 mb-2"><strong>Cliente:</strong> {{ optional($recibo->third)->name }}</div>
      <div class="w-1/2 sm:w-1/4 mb-2"><strong>Usuario:</strong> {{ $recibo->user->name }}</div>
      <div class="w-1/2 sm:w-1/4 mb-2"><strong>Fecha:</strong> {{ $recibo->fecha_elaboracion->format('d/m/Y H:i') }}</div>      
    </div>

    <table class="min-w-full divide-y divide-gray-200 text-sm text-black">
      <thead class="bg-blue-600 text-white">
        <tr>
          <th class="px-4 py-2 text-left">CLIENTE</th>
          <th class="px-4 py-2 text-left">#.FACTURA</th>
          <th class="px-4 py-2 text-right">VR.DEUDA</th>
          <th class="px-4 py-2 text-right">FORMA.PAGO</th>
          <th class="px-4 py-2 text-right">VR.PAGO</th>
          <th class="px-4 py-2 text-right">NVO.SALDO</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
      @foreach ($recibo->details as $detail)
      @php
        $ctc     = $detail->cuentaPorCobrar;
        $sale    = optional($ctc)->sale;
        $cliente = optional(optional($sale)->third)->name;
        $factura = $sale->consecutivo ?? '–';
        // Nombre de la forma de pago:
        $forma   = optional($detail->formaPago)->nombre ?? 'No especificada';
      @endphp
      <tr>
        <td class="px-4 py-2">{{ $cliente }}</td>
        <td class="px-4 py-2">{{ $factura }}</td>
        <td class="px-4 py-2 text-right">
          {{ number_format($detail->vr_deuda, 0, ',', '.') }}
        </td>
        <td class="px-4 py-2">
          {{ $forma }}
        </td>
        <td class="px-4 py-2 text-right">
          {{ number_format($detail->vr_pago, 0, ',', '.') }}
        </td>
        <td class="px-4 py-2 text-right">
          {{ number_format($detail->nvo_saldo, 0, ',', '.') }}
        </td>
      </tr>
    @endforeach
      </tbody>
      <tfoot class="bg-yellow-100 font-semibold">
        <tr>          
          <td colspan="2" class="px-4 py-2 text-right">TOTALES:</td>          
          <td class="px-4 py-2 text-right">{{ number_format($recibo->vr_total_deuda, 0, ',', '.') }}</td>
          <td></td>
          <td class="px-4 py-2 text-right">{{ number_format($recibo->vr_total_pago, 0, ',', '.') }}</td>
          <td class="px-4 py-2 text-right">{{ number_format($recibo->nvo_total_saldo, 0, ',', '.') }}</td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
@endsection
