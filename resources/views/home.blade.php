@extends('layouts.app')

@section('content')
    <div class="h-100 p-5 bg-light border rounded-3">
        <h2>Portal del {{ env('APP_SS_NAME') }}</h2>
        <p>Bienvenido NeoSalud.<br>
            Acá encontrarás la información que esté disponible para tí.</p>
    </div>
@endsection
