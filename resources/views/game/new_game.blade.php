@extends('layouts.app')
@section('content')
    <div class="card text-center">
        <div class="card-header">
            Jugadores
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <p class="mb-0">{{ __('Excepciones') }}</p>
                </div>
                <ol class="list-group list-group-numbered">
                    @foreach ($errors->all() as $error)
                        <li class="list-group-item text-danger">{{ $error }}</li>
                    @endforeach
                </ol>
            @endif
            <br>
            {!! Form::open(['route' => 'new', 'method' => 'post']) !!}
            {{ csrf_field() }}
            <div class="nput-group flex-nowrap">
                {{ Form::label('Jugador # 1') }}
                {{ Form::text('first_player', old('first_player'), [
                    'class' => 'text-uppercase form-control' . ($errors->has('first_player') ? ' is-invalid' : ''),
                    'placeholder' => __('Nombre Jugador # 1'),
                    'autocomplete' => 'off',
                ]) }}
                @if ($errors->has('first_player'))
                    <div class="invalid-feedback">
                        {{ $errors->first('first_player') }}
                    </div>
                @endif
                <br>
                {{ Form::label('Jugador # 2') }}
                {{ Form::text('second_player', old('second_player'), [
                    'class' => 'text-uppercase form-control' . ($errors->has('second_player') ? ' is-invalid' : ''),
                    'placeholder' => __('Nombre Jugador # 2'),
                    'autocomplete' => 'off',
                ]) }}
                @if ($errors->has('second_player'))
                    <div class="invalid-feedback">
                        {{ $errors->first('second_player') }}
                    </div>
                @endif
            </div>
            <br>
            {!! Form::submit(__('Iniciar'), ['class' => 'btn btn-outline-primary']) !!}
            {!! Form::close() !!}
        </div>
        <div class="card-footer text-muted">
            Renombra tus jugadores e inicia una nueva partida
        </div>
    </div>
@endsection
