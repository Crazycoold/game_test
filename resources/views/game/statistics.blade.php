@extends('layouts.app')
@section('content')
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">{{ __('Jugadores') }}</th>
                    <th scope="col">{{ __('Rondas') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($game as $game)
                    <tr>
                        <th scope="row">{{ $game->id }}</th>
                        <td>{{ $game->first_player }} | {{ $game->second_player }}</td>
                        <td>{{ $game->countGameRounds() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <hr>
    <div class="row">
        <div class="col text-center">
            {{ link_to_route('game', __('Nuevo juego'), [], ['class' => 'btn btn-outline-success']) }}
        </div>
    </div>
@endsection
