@extends('layouts.theme.app')
@section('content')
<h1>Nueva Orden</h1>
<form action="{{ route('restaurant-orders.store') }}" method="POST">@csrf
    <input name="table_number" placeholder="Mesa" />
    <select name="waiter_id">@foreach(Auth::user()->whereRole('waiter')->get() as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach</select>
    <h3>Items</h3>
    <select class="item-type">
        <option value="product">Producto</option>
        <option value="dish">Plato</option>
        <option value="combo">Combo</option>
    </select>
    <select class="item-id">
        <optgroup label="Productos">@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</optgroup>
    </select>
    <input name="items[0][quantity]" placeholder="Cantidad" />
    <input name="items[0][unit_price]" placeholder="Precio Unitario" />
    <!-- JS para clonar grupos y cambiar indices -->
    <button type="submit">Enviar Orden</button>
</form>
@endsection