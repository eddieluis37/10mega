@extends('layouts.theme.app')

@section('content')
  <h1>Combos</h1>
  <a href="{{ route('combos.create') }}" class="btn btn-primary">Nuevo Combo</a>

  <table class="table mt-3">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Código</th>
        <th>Precio</th>
        <th>Items</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($combos as $combo)
        <tr>
          <td>{{ $combo->name }}</td>
          <td>{{ $combo->code }}</td>
          <td>{{ number_format($combo->price, 2) }}</td>
          <td>{{ $combo->products->count() }}</td>
          <td>
            <a href="{{ route('combos.edit', $combo) }}" class="btn btn-sm btn-warning">Editar</a>
            <form action="{{ route('combos.destroy', $combo) }}" method="POST" style="display:inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar combo?')">Eliminar</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endsection
