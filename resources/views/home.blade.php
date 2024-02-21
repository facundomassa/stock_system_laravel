@extends('layouts.app')

@section('content')
    <div class="container">

        {{-- @include('home/graphMovements') --}}
        <div class="row text-center" >
            <div class="col">
                <h3>Alertas totales</h3>
                @include('home/alerts')
            </div>
            <div class="col">
                <h3>Reportes</h3>
                @include('home/reports')
            </div>
            
        </div>
        
    </div>
@endsection
