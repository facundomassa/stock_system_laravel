@extends('layouts.app')

@section('content')
    <div class="container">
        @include('layouts/alert')
        <form action="{{ url('/refer/') }}" method="get">
            <p class="m-0 filter">Filtros:</p>
            <div class="row align-items-center border border-secondary p-2 mb-2 rounded">
                <div class="col-auto">
                    <label for="stockselectorigen">Centro de Stock Origen:</label>
                    <select required class="form-control" name="stockselectorigen" maxlength="60" id="stockselectorigen"
                        onchange="this.form.submit()">
                        <option selected value="*">-Todos-</option>
                        @foreach ($stockcenters as $stockcenter)
                            @if ($stockselectorigen == $stockcenter->id)
                                <option selected value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}</option>
                            @else
                                <option value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label for="stockselectdestiny">Centro de Stock Destino:</label>
                    <select required class="form-control" name="stockselectdestiny" maxlength="60" id="stockselectdestiny"
                        onchange="this.form.submit()">
                        <option selected value="*">-Todos-</option>
                        @foreach ($stockcenters as $stockcenter)
                            @if ($stockselectdestiny == $stockcenter->id)
                                <option selected value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}</option>
                            @else
                                <option value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label for="status">Estatus:</label>
                    <select required class="form-control" name="status" maxlength="60" id="status"
                        onchange="this.form.submit()">
                        <option selected value="*">-Todos-</option>
                        <option {{$status == "I" ? "selected" : ""}} value="I"> Ingresado</option>
                        <option {{$status == "E" ? "selected" : ""}} value="E"> Emitido</option>
                        <option {{$status == "C" ? "selected" : ""}} value="C"> Cancelado</option>
                        <option {{$status == "F" ? "selected" : ""}} value="F"> Finalizado</option>
                    </select>
                </div>
            </div>
        </form>
        <table class="table table-striped table-hover table-md">
            <thead>
                <tr>
                    <th class="table-light">#</th>
                    <th class="table-light">Origen</th>
                    <th class="table-light">Destino</th>
                    <th class="table-light">Fecha Inicio</th>
                    <th class="table-light">Fecha Finalizado</th>
                    <th class="table-light">Creado por</th>
                    <th class="table-light">Estaus</th>
                    <th class="table-light">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($refers as $refer)
                    <tr>
                        <td>{{ $refer->id }}</td>
                        <td>{{ $refer->nameOrigin }}</td>
                        <td>{{ $refer->nameDestiny }}</td>
                        <td>{{ $refer->dateUpFormatted }}</td>
                        <td>{{ $refer->dateEndedFormatted }}</td>
                        <td>{{ $refer->fullNameUser }}</td>
                        <td class="{{ $refer->status }}">{{ $refer->statusName }}</td>
                        <td class="text-center">
                            <div class="row me-1">
                                <div class="col p-0">
                                    @if ($refer->status == 'I')
                                        <a class="btn btn-primary py-0 " href="{{ url('refer/emited/' . $refer->id) }}"><i
                                                class="bi bi-send"></i></a>
                                    @endif
                                </div>
                                <div class="col p-0">
                                    @if ($refer->status == 'I' || $refer->status == 'E')
                                        <a class="btn btn-warning py-0" href="{{ url('/refer/' . $refer->id . '/edit') }}"><i
                                                class="bi bi-pencil"></i></a>
                                    @endif
                                </div>
                                <div class="col p-0">
                                    @if ($refer->status == 'I' || $refer->status == 'E')
                                        <form class="d-inline" action="{{ url('/refer/finalized/' . $refer->id) }}"
                                            method="post">
                                            @csrf
                                            {{ method_field('PATCH') }}
                                            <button class="btn btn-success py-0" type="submit"
                                                onclick="return confirm('¿Finalizar remito?')"><i
                                                    class="bi bi-check2-square"></i></button>
                                        </form>
                                    @endif
                                </div>
                                <div class="col p-0">
                                    @if ($refer->status == 'I' || $refer->status == 'E')
                                        <form class="d-inline" action="{{ url('/refer/' . $refer->id) }}" method="post">
                                            @csrf
                                            {{ method_field('DELETE') }}
                                            <button class="btn btn-danger py-0" type="submit"
                                                onclick="return confirm('¿Cancelar remito?')"><i
                                                    class="bi bi-x-circle"></i></button>
                                        </form>
                                    @endif
                                </div>
                                <div class="col p-0">
                                    <a class="btn btn-outline-dark py-0" href="{{ url('/refer/' . $refer->id) }}"><i
                                            class="bi bi-eye-fill"></i></a>
                                </div>
                            </div>
                            
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        {!! $refers->appends(['stockselectorigen' => $stockselectorigen,
                                'stockselectdestiny' => $stockselectdestiny,
                                'status' => $status])
                                ->links('vendor.pagination.bootstrap-5') !!}
        <a class="btn btn-success" href="{{ url('/refer/create') }}">Nuevo ingreso</a>
    </div>
@endsection
