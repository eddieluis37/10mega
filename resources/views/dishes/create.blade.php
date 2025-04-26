@extends('layouts.theme.app')
@section('content')
<h1>Crear Plato</h1>
<form action="{{ route('dishes.store') }}" method="POST">@csrf
    <input name="name" />
    <select name="ingredients[]" multiple>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select>
    <button type="submit">Guardar</button>
</form>
@endsection