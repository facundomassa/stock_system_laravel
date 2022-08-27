@extends('layouts.app')

@section('content')
    <div class="container">
        @if (Session::has('mensaje'))
            <div class="alert alert-success alert-dismissible" role="alert">

                <strong>{{ Session::get('mensaje') }}</strong>

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <table class="table table-striped table-hover table-md">
            <thead>
                <tr>
                    <th class="table-light">#</th>
                    <th class="table-light">N° Remito</th>
                    <th class="table-light">Articulo</th>
                    <th class="table-light">Cantidad</th>
                    <th class="table-light">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($movements as $movement)
                    <tr>
                        <td>{{ $movement->id }}</td>
                        <td><a class="btn btn-outline-dark py-0"
                                href="{{ url('/refer/' . $movement->id_refer) }}"><i class="bi bi-eye-fill"> </i> </a>
                                 {{"   " . $movement->id_refer }} </td>
                        <td>{{ $movement->id_article }}</td>
                        <td>{{ $movement->quantity }}</td>
                        <td>
                            <a class="btn btn-outline-dark py-0" href="{{ url('/movement/' . $movement->id) }}"><i
                                    class="bi bi-eye-fill"></i></a>

                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        {!! $movements->links() !!}
    </div>
@endsection
