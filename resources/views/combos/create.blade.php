@extends('layouts.theme.app')

@section('content')
  <h1>Crear Nuevo Combo</h1>

  <form action="{{ route('combos.store') }}" method="POST">
    @csrf

    <div class="mb-3">
      <label for="name" class="form-label">Nombre</label>
      <input type="text" name="name" id="name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="code" class="form-label">Código</label>
      <input type="text" name="code" id="code" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="price" class="form-label">Precio</label>
      <input type="number" name="price" id="price" class="form-control" step="0.01" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Productos</label>
      @foreach($products as $p)
        <div class="d-flex align-items-center mb-2">
          <input type="checkbox" name="products[]" value="{{ $p->id }}" id="prod-{{ $p->id }}">
          <label for="prod-{{ $p->id }}" class="ms-2 me-3">{{ $p->name }}</label>
          <input type="number"
                 name="quantities[{{ $p->id }}]"
                 value="1"
                 step="0.01"
                 class="form-control form-control-sm"
                 style="width: 100px;"
                 placeholder="Cant.">
        </div>
      @endforeach
    </div>

    <button type="submit" class="btn btn-success">Guardar Combo</button>
    <a href="{{ route('combos.index') }}" class="btn btn-secondary">Cancelar</a>
  </form>
@endsection
