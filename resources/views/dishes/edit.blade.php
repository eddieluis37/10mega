@extends('layouts.theme.app')
@section('content')
<h1>Editar Plato</h1>
<form action="{{ route('dishes.update', $dish) }}" method="POST">@csrf @method('PUT')
    <div class="mb-3"><label class="form-label">Nombre</label><input name="name" value="{{ old('name',$dish->name) }}" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Código</label><input name="code" value="{{ old('code',$dish->code) }}" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Descripción</label><textarea name="description" class="form-control">{{ old('description', $dish->description) }}</textarea></div>
    <div class="mb-3"><label class="form-label">Precio</label><input type="number" name="price" value="{{ old('price',$dish->price) }}" class="form-control" step="0.01" required></div>
    <div class="mb-3"><label class="form-label">Imagen URL</label><input name="image" value="{{ old('image',$dish->image) }}" class="form-control"></div>
    <div class="mb-3"><label class="form-label">Estado</label><select name="status" class="form-select">
            <option value="1" {{ $dish->status? 'selected':'' }}>Activo</option>
            <option value="0" {{ !$dish->status? 'selected':'' }}>Inactivo</option>
        </select></div>
    @php
    $selected = old('ingredients', $dish->products->pluck('id')->toArray());
    @endphp

    {{-- Tabla de Ingredientes --}}
    <div class="mb-3">
        <label class="form-label">Ingredientes</label>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Seleccionar</th>
                    <th scope="col">Ingrediente</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Unidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $p)
                @php
                $qty = old("quantities.{$p->id}", optional($dish->products->firstWhere('id', $p->id))->pivot->quantity ?? 1);
                $unit = old("units.{$p->id}", optional($dish->products->firstWhere('id', $p->id))->pivot->unitofmeasure_id ?? 1);
                @endphp
                <tr>
                    <td class="align-middle text-center">
                        <input type="checkbox"
                            name="ingredients[]"
                            value="{{ $p->id }}"
                            id="ing-{{ $p->id }}"
                            class="form-check-input"
                            {{ in_array($p->id, $selected) ? 'checked' : '' }}>
                    </td>
                    <td class="align-middle">
                        <label for="ing-{{ $p->id }}" class="mb-0">{{ $p->name }}</label>
                    </td>
                    <td>
                        <input type="number"
                            name="quantities[{{ $p->id }}]"
                            value="{{ $qty }}"
                            step="0.01"
                            class="form-control form-control-sm"
                            placeholder="Cant.">
                    </td>
                    <td>
                        <select name="units[{{ $p->id }}]" class="form-select form-select-sm">
                            @foreach(\App\Models\UnitOfMeasure::all() as $u)
                            <option value="{{ $u->id }}" {{ $unit == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="{{ route('dishes.index') }}" class="btn btn-secondary">Cancelar</a>
</form>

@endsection