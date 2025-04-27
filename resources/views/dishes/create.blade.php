@extends('layouts.theme.app')
@section('content')
<h1>Crear Plato</h1>
<form action="{{ route('dishes.store') }}" method="POST">@csrf
    <div class="mb-3"><label class="form-label">Nombre</label><input name="name" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Código</label><input name="code" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Descripción</label><textarea name="description" class="form-control"></textarea></div>
    <div class="mb-3"><label class="form-label">Precio</label><input type="number" name="price" class="form-control" step="0.01" required></div>
    <div class="mb-3"><label class="form-label">Imagen URL</label><input name="image" class="form-control"></div>
    <div class="mb-3"><label class="form-label">Estado</label><select name="status" class="form-select">
            <option value="1">Activo</option>
            <option value="0">Inactivo</option>
        </select></div>
    <div class="mb-3"><label class="form-label">Ingredientes</label>
        @foreach($products as $p)
        <div class="form-check mb-2">
            <input type="checkbox"
                name="ingredients[]"
                value="{{ $p->id }}"
                id="ing-{{ $p->id }}"
                class="form-check-input">
            <label for="ing-{{ $p->id }}" class="form-check-label">{{ $p->name }}</label>

            {{-- Cantidad --}}
            <input type="number"
                name="quantities[{{ $p->id }}]"
                value="1"
                step="0.01"
                class="form-control form-control-sm mt-1"
                style="width:80px;"
                placeholder="Cant.">

            {{-- Unidad de medida (si la usas) --}}
            <select name="units[{{ $p->id }}]" class="form-select form-select-sm mt-1" style="width:100px;">
                @foreach(\App\Models\UnitOfMeasure::all() as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        @endforeach
    </div>
    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="{{ route('dishes.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection