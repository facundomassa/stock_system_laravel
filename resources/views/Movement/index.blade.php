@extends('layouts.app')

@section('content')
    <div class="container">
        @include('layouts/alert')
        <table class="table table-striped table-hover table-md">
            <thead>
                <tr>
                    <th class="table-light">#</th>
                    <th class="table-light">N° Remito</th>
                    <th class="table-light">Origen</th>
                    <th class="table-light">Destino</th>
                    <th class="table-light">Fecha</th>
                    <th class="table-light">Articulo</th>
                    <th class="table-light">Cantidad</th>
                    <th class="table-light">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($movements as $movement)
                    <tr>
                        <td>{{ $movement->id }}</td>
                        <td>{{ $movement->Refer->id }} </td>
                        <td>{{ $movement->Refer->nameOrigin }} </td>
                        <td>{{ $movement->Refer->nameDestiny }} </td>
                        <td>{{ $movement->Refer->dateEndedFormatted }} </td>
                        <td>{{ $movement->Article->name }}</td>
                        <td>{{ $movement->quantity }}</td>
                        <td>
                            <a class="btn btn-outline-dark py-0" href="{{ url('/refer/' . $movement->id_refer) }}"><i
                                    class="bi bi-eye-fill"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        {!! $movements->links('vendor.pagination.bootstrap-5') !!}
    </div>
@endsection
