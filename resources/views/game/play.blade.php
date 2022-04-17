@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header alert alert-info text-center">
            <b><em>Código de partida:</em></b> {{ $game->id . '-' . $round }}
        </div>
        <div class="card-body">
            <div class="text-center font-italic f-size-1-5 mb-3">
                {{ $game->first_player }} | {{ $game->second_player }}
            </div>
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <p class="mb-0">{{ __('Errors') }}</p>
                    <ul class="mb-0">

                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>
                </div>
            @endif
            @if (empty($prepareData['gameHistories']))
                <div class="text-center">
                    {{ __('Comenzar el juego') }}
                </div>
            @endif
            @if ($isFullGameField || $prepareData['gameOver'])
                <div class="text-center">
                    {{ __('El juego ha terminado') }}
                </div>
                @if ($prepareData['playerWinner'])
                    <div class="text-center f-size-2">
                        {{ __('Ganador') }} "{{ strtoupper($game::getPlayerTypes($prepareData['playerWinner'])) }}"
                    </div>
                @else
                    <div class="text-center f-size-2">
                        {{ __('Es un empate') }}
                    </div>
                @endif
            @else
                @if ($prepareData['playerType'] && $prepareData['playerType'] === $firstPlayerType)
                    <div class="d-flex align-items-center justify-content-center">
                        <div>{{ __('Turno de ') }}&nbsp;</div>
                        <div class="mb-2 f-size-2">{{ $game::getPlayerTypes($secondPlayerType) }}</div>
                    </div>
                @endif
                @if ($prepareData['playerType'] && $prepareData['playerType'] === $secondPlayerType)
                    <div class="d-flex align-items-center justify-content-center">
                        <div>{{ __('Turno de ') }}&nbsp;</div>
                        <div class="mb-2 f-size-2">{{ $game::getPlayerTypes($firstPlayerType) }}</div>
                    </div>
                @endif
            @endif
            <div class="ttt-content mt-3">
                @for ($row = 1; $row <= 3; $row++)
                    <div class="d-flex justify-content-center ttt-row">
                        @for ($col = 1; $col <= 3; $col++)
                            <div class="align-self-center ttt-col">
                                @if (isset($prepareData['gameHistories'][$row][$col]))
                                    <div class="ttt-element">
                                        @if ($prepareData['horizontalSuccess'][$row] ?? null)
                                            <div class="line"></div>
                                        @elseif($prepareData['verticalSuccess'][$col] ?? null)
                                            <div class="line rotate-90"></div>
                                        @elseif ($prepareData['diagonalRightSuccess'][$row][$col] ?? null)
                                            <div class="line rotate-135"></div>
                                        @elseif ($prepareData['diagonalLeftSuccess'][$row][$col] ?? null)
                                            <div class="line rotate-45"></div>
                                        @endif
                                        {{ $game::getPlayerTypes($prepareData['gameHistories'][$row][$col]) }}
                                    </div>
                                @else
                                    <div class="ttt-element">
                                        @if (!$prepareData['gameOver'])
                                            {!! Form::open(['route' => 'record', 'method' => 'post']) !!}
                                            {!! Form::hidden('game_id', $game->id) !!}
                                            {!! Form::hidden('game_round_id', $round) !!}
                                            {!! Form::hidden('game_row', $row) !!}
                                            {!! Form::hidden('game_column', $col) !!}
                                            @if (!$prepareData['playerType'])
                                                {!! Form::hidden('player_type', $firstPlayerType) !!}
                                            @endif

                                            @if ($prepareData['playerType'] && $prepareData['playerType'] === $firstPlayerType)
                                                {!! Form::hidden('player_type', $secondPlayerType) !!}
                                            @endif

                                            @if ($prepareData['playerType'] && $prepareData['playerType'] === $secondPlayerType)
                                                {!! Form::hidden('player_type', $firstPlayerType) !!}
                                            @endif

                                            {!! Form::submit('', ['class' => 'btn btn-link btn-block']) !!}
                                            {!! Form::close() !!}
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endfor
                    </div>
                @endfor
            </div>
            <hr>
            <div class="row">
                <div class="col-sm text-center pb-3">
                    {!! Form::open(['route' => 'round', 'method' => 'post']) !!}
                    {!! Form::hidden('game_id', $game->id) !!}
                    {!! Form::submit(__('Jugar de nuevo'), ['class' => 'btn btn-outline-primary']) !!}
                    {!! Form::close() !!}
                </div>
                <div class="col-sm text-center pb-3">
                    {{ link_to_route('game', __('Nuevo juego'), [], ['class' => 'btn btn-outline-success']) }}
                </div>
                <div class="col-sm text-center pb-3">
                    {{ link_to_route('join-me', __('Unirme a partida'), [], ['class' => 'btn btn-outline-warning']) }}
                </div>
                <div class="col-sm text-center pb-3">
                    {{ link_to_route('statistics', __('Estadísticas'), [], ['class' => 'btn btn-outline-info']) }}
                </div>
            </div>
        </div>
    </div>
@endsection
