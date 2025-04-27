@extends('layouts.theme.app')

@section('content')
  <h1>Editar Combo</h1>

  <form action="{{ route('combos.update', $combo) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- Nombre --}}
    <div class="mb-3">
      <label for="name" class="form-label">Nombre</label>
      <input type="text"
             name="name"
             id="name"
             class="form-control"
             value="{{ old('name', $combo->name) }}"
             required>
    </div>

    {{-- Código --}}
    <div class="mb-3">
      <label for="code" class="form-label">Código</label>
      <input type="text"
             name="code"
             id="code"
             class="form-control"
             value="{{ old('code', $combo->code) }}"
             required>
    </div>

    {{-- Precio --}}
    <div class="mb-3">
      <label for="price" class="form-label">Precio</label>
      <input type="number"
             name="price"
             id="price"
             class="form-control"
             step="0.01"
             value="{{ old('price', $combo->price) }}"
             required>
    </div>

    {{-- Estado --}}
    <div class="mb-3">
      <label for="status" class="form-label">Activo</label>
      <select name="status" id="status" class="form-select">
        <option value="1" {{ old('status', $combo->status) ? 'selected' : '' }}>Sí</option>
        <option value="0" {{ ! old('status', $combo->status) ? 'selected' : '' }}>No</option>
      </select>
    </div>

    {{-- Productos y cantidades --}}
    <div class="mb-3">
      <label class="form-label">Productos</label>
      @php
        // IDs seleccionados en el combo
        $selected = old('products', $combo->products->pluck('id')->toArray());
      @endphp
      @foreach($products as $p)
        @php
          // cantidad anterior o del pivot
          $cant = old("quantities.{$p->id}", optional($combo->products->firstWhere('id', $p->id))->pivot->quantity ?? 1);
        @endphp
        <div class="d-flex align-items-center mb-2">
          <input type="checkbox"
                 name="products[]"
                 value="{{ $p->id }}"
                 id="prod-{{ $p->id }}"
                 {{ in_array($p->id, $selected) ? 'checked' : '' }}>
          <label for="prod-{{ $p->id }}" class="ms-2 me-3">{{ $p->name }}</label>

          <input type="number"
                 name="quantities[{{ $p->id }}]"
                 value="{{ $cant }}"
                 step="0.01"
                 class="form-control form-control-sm"
                 style="width: 100px;"
                 placeholder="Cant.">
        </div>
      @endforeach
    </div>

    <button type="submit" class="btn btn-primary">Actualizar Combo</button>
    <a href="{{ route('combos.index') }}" class="btn btn-secondary">Cancelar</a>
  </form>
@endsection
