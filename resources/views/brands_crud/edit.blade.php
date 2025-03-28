@extends('layouts.theme.app')

@section('content')
<div class="container">
    <h1>Editar Marca</h1>

    @if($errors->any())
      <div class="alert alert-danger">
         <ul>
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
         </ul>
      </div>
    @endif

    <form action="{{ route('brand-crud.update', $brand) }}" method="POST">
       @csrf
       @method('PUT')
       <div class="form-group">
         <label for="name">Nombre</label>
         <input type="text" name="name" id="name" class="form-control" value="{{ $brand->name }}" required>
       </div>
       <div class="form-group">
         <label for="description">Descripción</label>
         <textarea name="description" id="description" class="form-control">{{ $brand->description }}</textarea>
       </div>
       <div class="form-group">
         <label for="status">Status</label>
         <select name="status" id="status" class="form-control">
             <option value="1" {{ $brand->status ? 'selected' : '' }}>Activo</option>
             <option value="0" {{ !$brand->status ? 'selected' : '' }}>Inactivo</option>
         </select>
       </div>
       <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@endsection
