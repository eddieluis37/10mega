@extends('layouts.theme.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
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
                                    <th>Proveedores</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($brandThirds as $brandThird)
                                    <tr>
                                        <td>{{ $brandThird->name }}</td>
                                        <td>
                                            @foreach ($brandThird->brand->thirds as $third)
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
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Usted esta seguro?')">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

