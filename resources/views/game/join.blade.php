@extends('layouts.app')
@section('content')
    <div class="card text-center">
        <div class="card-header">
            Unirme a partida
        </div>
        <div class="card-body">
            {!! Form::open(['route' => 'join', 'method' => 'post']) !!}
            <div class="nput-group flex-nowrap">
                {{ Form::label('Código de partida') }}
                {{ Form::text('code', old('code'), [
                    'class' => 'text-uppercase form-control',
                    'placeholder' => __('Código de partida'),
                    'autocomplete' => 'off',
                ]) }}
            </div>
            <br>
            {!! Form::submit(__('Unirme'), ['class' => 'btn btn-outline-success']) !!}
            {!! Form::close() !!}
        </div>
        <div class="card-footer text-muted">
            Renombra tus jugadores e inicia una nueva partida
        </div>
    </div>
@endsection
