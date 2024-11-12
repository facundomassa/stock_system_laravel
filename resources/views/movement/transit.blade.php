@extends('layouts.app')

@section('content')
    <div class="container">
        @include('layouts/alert')
        <div class="d-grid gap-2"">
            @foreach ($refermovement as $refers => $movements)
            <a class="btn p-0" type="button" data-bs-toggle="collapse" href={{'#refer'.$refers}} role="button" aria-expanded="false" aria-controls={{'refer'.$refers}}">
                <table class="table table-striped table-hover table-md mb-0">
                    <thead>
                        <tr>
                            <th class="table-light">{{ $refer[$refers]->id }}</th>
                            <th class="table-light">{{ $refer[$refers]->nameOrigin }}</th>
                            <th class="table-light"><i class='bx bxs-chevrons-right bx-sm bx-fade-right' style='color:#6cb06c' ></i></i></th>
                            <th class="table-light">{{ $refer[$refers]->nameDestiny }}</th>
                            <th class="table-light">{{ $refer[$refers]->dateEndedFormatted }}</th>
                        </tr>
                    </thead>
                </table>
            </a>
            <div class="collapse" id={{ 'refer'.$refers }}>
            <table class="table table-striped table-hover table-md">
                <thead>
                    <tr>
                        <th class="table-light">#</th>
                        <th class="table-light">Articulo</th>
                        <th class="table-light">Cantidad</th>
                        <th class="table-light">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                        @foreach ($movements as $movement)
                        <tr>
                            <td><a href="{{ url('/refer/' . $movement->id_refer) }}">{{ $movement->id }}</a></td>
                            <td>{{ $movement->Article->name }}</td>
                            <td>{{ $movement->quantity }}</td>
                            <td>
                                <a class="btn btn-outline-dark py-0" href="{{ url('/article/' . $movement->id_article) }}"><i
                                        class="bi bi-eye-fill"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    
                </tbody>

            </table>
            </div>
            @endforeach
        </div>
        {!! $paginatedResults->links('vendor.pagination.bootstrap-5') !!}
    </div>
@endsection
