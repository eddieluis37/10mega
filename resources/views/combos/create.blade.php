@extends('layouts.theme.app')
@section('content')
<h1>Crear Combo</h1>
<form action="{{ route('combos.store') }}" method="POST">@csrf
    <input name="name" placeholder="Nombre">
    <input name="code" placeholder="Código">
    <textarea name="description" placeholder="Descripción"></textarea>
    <input name="price" placeholder="Precio">
    <label>Productos:</label>
    @foreach($products as $p)
    <div>
        <input type="checkbox" name="products[{{ $p->id }}]" value="{{ $p->id }}"> {{ $p->name }}
        <input name="quantities[{{ $p->id }}]" placeholder="Cantidad">
    </div>
    @endforeach
    <button type="submit">Guardar</button>
</form>
@endsection