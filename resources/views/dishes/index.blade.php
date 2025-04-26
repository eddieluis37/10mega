@extends('layouts.theme.app')
@section('content')
 <h1>Platos</h1>
 <a href="{{ route('dishes.create') }}" class="btn btn-primary">Nuevo Plato</a>
 <table>...@foreach($dishes as $dish)<tr><td>{{ $dish->name }}</td>...@endforeach</table>
 @endsection

