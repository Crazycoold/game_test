<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Styles CSS -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <title>{{ config('app.name', 'TicTacToe') }}</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col mt-3">

                <h1 class="text-center">{{ config('app.name', 'TicTacToe') }}</h1>

            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col mt-3">

                @yield('content')
            </div>
        </div>
    </div>
</body>

</html>
