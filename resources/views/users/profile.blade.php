@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Perfil de {{ $user->name }}</h1>
        <p>Email: {{ $user->email }}</p>
        <!-- Agrega más información del usuario según tus necesidades -->
    </div>
@endsection
