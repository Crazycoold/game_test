@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header alert alert-info">
            Inicia una nueva partida
        </div>
        <div class="card-body">
            <div class="text-center">
                {{ link_to_route('game', __('Inicia un nuevo juego'), [], ['class' => 'btn btn-outline-primary']) }}
            </div>
        </div>
    </div>
@endsection
