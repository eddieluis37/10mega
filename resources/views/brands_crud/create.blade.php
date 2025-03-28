@extends('layouts.theme.app')

@section('content')
<div class="container">
    <h1>Crear Marca</h1>

    @if($errors->any())
      <div class="alert alert-danger">
         <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
         </ul>
      </div>
    @endif

    <form action="{{ route('brand-crud.store') }}" method="POST">
       @csrf
       <div class="form-group">
         <label for="name">Nombre</label>
         <input type="text" name="name" id="name" class="form-control" required>
       </div>
       <div class="form-group">
         <label for="description">Descripción</label>
         <textarea name="description" id="description" class="form-control"></textarea>
       </div>
       <div class="form-group">
         <label for="status">Status</label>
         <select name="status" id="status" class="form-control">
             <option value="1" selected>Activo</option>
             <option value="0">Inactivo</option>
         </select>
       </div>
       <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
