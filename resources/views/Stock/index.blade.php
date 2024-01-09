@extends('layouts.app')

@section('content')
    <div class="container">
        @include('layouts/alert')

        <form action="{{ url('/stock/') }}" method="get">
            <p class="m-0 filter">Filtros:</p>
            <div class="row align-items-center border border-secondary p-2 mb-2 rounded">
                <div class="col-auto">
                    <label for="stockselect">Centro de Stock:</label>
                    <select required class="form-control" name="stockselect" maxlength="60" id="stockselect"
                        onchange="this.form.submit()">
                        <option selected value="*">-Todos-</option>
                        @foreach ($stockcenters as $stockcenter)
                            @if ($stockselect == $stockcenter->id)
                                <option selected value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}</option>
                            @else
                                <option value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label for="code">Codigo:</label>
                    <input class="form-control" type="text" name="code" maxlength="40"
                        value="{{ isset($code) ? $code : old('code') }}" id="code" onchange="this.form.submit()">
                </div>
                <div class="col-auto">
                    <label for="articlename">Articulo:</label>
                    <input class="form-control" type="text" name="articlename" maxlength="40"
                        value="{{ isset($articlename) ? $articlename : old('articlename') }}" id="articlename"
                        onchange="this.form.submit()">
                </div>
                <div class="col-auto">
                    <label for="type">Tipo:</label>
                    <input class="form-control" type="text" name="type" maxlength="40"
                        value="{{ isset($type) ? $type : old('type') }}" id="type" onchange="this.form.submit()">
                </div>
            </div>
        </form>
        <table class="table table-striped table-hover table-md">
            <thead>
                <tr>
                    <th class="table-light">#</th>
                    <th class="table-light">Centro de Stock</th>
                    <th class="table-light">Codigo</th>
                    <th class="table-light">Articulo</th>
                    <th class="taable-light">Unidad</th>
                    <th class="table-light">Tipo</th>
                    <th class="table-light">Cantidad</th>
                    <th class="table-light">Alerta</th>
                    <th class="tble-light">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stocks as $stock)
                    <tr {{ $stock->warning ? "class=bg-warning" : "class=bg-light"}}>
                        <td>{{ $stock->id }}</td>
                        <td>{{ $stock->StockCenter->name }}</td>
                        <td>{{ $stock->Article->code }}</td>
                        <td>{{ $stock->Article->name }}</td>
                        <td>{{ $stock->Article->UnitName }}</td>
                        <td>{{ $stock->Article->type }}</td>
                        <td>{{ $stock->quantity }}</td>
                        <td>{{ isset($stock->quantity_alert) ? $stock->quantity_alert : "-" }}</td>
                        <td>
                            <a class="btn btn-outline-dark py-0" href="{{ url('/stock/' . $stock->id) }}"><i
                                    class="bi bi-eye-fill"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {!! $stocks->appends(['stockselect' => $stockselect, 'code' => $code, 'articlename' => $articlename, 'type' => $type])->links('vendor.pagination.bootstrap-5') !!}
        <a class="btn btn-primary py-0 " href="{{ url('stock/get/pdf?stockselect='.$stockselect.'&code='.$code.'&articlename='.$articlename.'&type='.$type) }}">Generar PDF</a>
        <a class="btn btn-primary py-0 " href="{{ url('stock/get/excel?stockselect='.$stockselect.'&code='.$code.'&articlename='.$articlename.'&type='.$type) }}">Generar Excel</a>
        <div class="fixed-top aticle-container collapse" id="article-t">
            <div class="article-store">
                <div class="article-tittle d-flex gap-2 flex-column">
                    <button class="btn btn-dark article-btn" type="button" data-bs-toggle="collapse"
                        data-bs-target="#article-t" aria-expanded="true" aria-controls="article-t">X</button>
                        <iframe id="framepdf" height="100%" class="frame" src="" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    @vite('resources/js/ajax/generatepdf.js')
@endsection