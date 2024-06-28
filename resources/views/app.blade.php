<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{env('APP_NAME')}}</title>

    <link rel="icon" type="image/png" href="{{ asset('asset/images/favicon.ico')}}" />

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Vuetify CSS -->
    {{-- <link href="https://cdn.jsdelivr.net/npm/vuetify/dist/vuetify.min.css" rel="stylesheet"> --}}
    <!-- App CSS -->
    <link href="{{ asset('css/app.css')}}" rel="stylesheet">
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>

<body>
    <noscript>
        <strong>
            We're sorry but this app doesn't work properly without JavaScript enabled. Please enable it to continue.
        </strong>
    </noscript>
    <div id="app"></div>
    <!-- built files will be auto injected -->

    <script src="{{ asset('js/app.js')}}"></script>
</body>

</html>