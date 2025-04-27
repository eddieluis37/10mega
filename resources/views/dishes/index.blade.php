@extends('layouts.theme.app')
@section('content')
<h1>Platos</h1>
 <a href="{{ route('dishes.create') }}" class="btn btn-primary">Nuevo Plato</a>
 <table class="table mt-3">
   <thead><tr><th>Nombre</th><th>Código</th><th>Precio</th><th>Items</th><th>Acciones</th></tr></thead>
   <tbody>
   @foreach($dishes as $d)
     <tr>
       <td>{{ $d->name }}</td>
       <td>{{ $d->code }}</td>
       <td>{{ number_format($d->price,2) }}</td>
       <td>{{ $d->products->count() }}</td>
       <td>
         <a href="{{ route('dishes.edit',$d) }}" class="btn btn-sm btn-warning">Editar</a>
         <form action="{{ route('dishes.destroy',$d) }}" method="POST" style="display:inline">
           @csrf @method('DELETE')
           <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar plato?')">Eliminar</button>
         </form>
       </td>
     </tr>
   @endforeach
   </tbody>
 </table>
 @endsection
