@extends('layouts.theme.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Editar Marca</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('brands.update', $brandThird) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name">Nombre</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $brandThird->name }}" required>
                            </div>
                            <div class="form-group">
                                <label for="brand_id">Marca</label>
                                <select class="form-control" id="brand_id" name="brand_id" required>
                                    <option value="">Seleccione una marca</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ $brandThird->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="third_id">Proveedores</label>
                                <select class="form-control" id="third_id" name="third_id[]" multiple required>
                                    @foreach ($thirds as $third)
                                        <option value="{{ $third->id }}"
                                            {{ in_array($third->id, $brandThird->thirds->pluck('id')->toArray()) ? 'selected' : '' }}>
                                            {{ $third->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Utilice Ctrl (Windows) o Cmd (Mac) para seleccionar múltiples proveedores.</small>
                            </div>
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
