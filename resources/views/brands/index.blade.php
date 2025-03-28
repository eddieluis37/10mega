@extends('layouts.theme.app')

@section('content')
<div class="container">
    <br>
    <h1>Listado de Marcas</h1>

    <!-- Barra de búsqueda -->
    <form action="{{ route('brands.index') }}" method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o proveedor" value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
    </form>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Marcas</h3>
            <div class="card-tools">
                <a href="{{ route('brands.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Crear Marca
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Marca</th>
                        <th>Proveedores</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($brandThirds as $brandThird)
                        <tr>
                            <td>{{ $brandThird->name }}</td>
                            <td>{{ $brandThird->brand->name }}</td>
                            <td>
                                @foreach ($brandThird->thirds as $third)
                                    {{ $third->name }}<br>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('brands.edit', $brandThird) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('brands.destroy', $brandThird) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if($brandThirds->isEmpty())
                        <tr>
                            <td colspan="4">No se encontraron registros.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
