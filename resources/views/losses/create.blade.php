@extends('layouts.theme.app')
@section('content')
<h1>Registrar Pérdida</h1>
<form action="{{ route('losses.store') }}" method="POST">@csrf
    <select name="product_id">@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select>
    <input name="quantity" placeholder="Cantidad Perdida" />
    <textarea name="reason" placeholder="Motivo"></textarea>
    <input type="hidden" name="reported_by" value="{{ Auth::id() }}" />
    <button type="submit">Reportar</button>
</form>
@endsection