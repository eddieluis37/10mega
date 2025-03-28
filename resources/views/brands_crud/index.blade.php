@extends('layouts.theme.app')

@section('content')
<div class="container">
    <h1>Listado de Marcas</h1>

    @if(session('success'))
       <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('brand-crud.create') }}" class="btn btn-primary mb-3">Crear Marca</a>

    <table class="table table-bordered">
       <thead>
         <tr>
           <th>Nombre</th>
           <th>Descripción</th>
           <th>Status</th>
           <th>Acciones</th>
         </tr>
       </thead>
       <tbody>
         @forelse ($brands as $brand)
           <tr>
              <td>{{ $brand->name }}</td>
              <td>{{ $brand->description }}</td>
              <td>{{ $brand->status ? 'Activo' : 'Inactivo' }}</td>
              <td>
                <a href="{{ route('brand-crud.edit', $brand) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('brand-crud.destroy', $brand) }}" method="POST" class="d-inline">
                   @csrf
                   @method('DELETE')
                   <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar esta marca?')">Eliminar</button>
                </form>
              </td>
           </tr>
         @empty
           <tr>
             <td colspan="4">No se encontraron registros.</td>
           </tr>
         @endforelse
       </tbody>
    </table>
</div>
@endsection

