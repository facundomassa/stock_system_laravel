@extends('layouts.app')

@section('content')
    <div class="container">
        @include('layouts/alert')

        <form action="{{ url('/stock/') }}" method="get">
            @csrf
            {{ method_field('GET') }}
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
                        value="{{ isset($code) ? $code : old("code") }}" id="code" onchange="this.form.submit()">
                </div>
                <div class="col-auto">
                    <label for="articlename">Articulo:</label>
                    <input class="form-control" type="text" name="articlename" maxlength="40"
                        value="{{ isset($articlename) ? $articlename : old("articlename") }}" id="articlename" onchange="this.form.submit()">
                </div>
                <div class="col-auto">
                    <label for="type">Tipo:</label>
                    <input class="form-control" type="text" name="type" maxlength="40"
                        value="{{ isset($type) ? $type : old("type") }}" id="type" onchange="this.form.submit()">
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
                    <th class="table-light">Unidad</th>
                    <th class="table-light">Tipo</th>
                    <th class="table-light">Cantidad</th>
                    <th class="table-light">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stocks as $stock)
                    <tr>
                        <td>{{ $stock->id }}</td>
                        <td>{{ $stock->StockCenter->name }}</td>
                        <td>{{ $stock->Article->code }}</td>
                        <td>{{ $stock->Article->name }}</td>
                        <td>{{ $stock->Article->UnitName }}</td>
                        <td>{{ $stock->Article->type }}</td>
                        <td>{{ $stock->quantity }}</td>
                        <td>
                            <a class="btn btn-outline-dark py-0" href="{{ url('/stock/' . $stock->id) }}"><i
                                    class="bi bi-eye-fill"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        {!! $stocks->links('vendor.pagination.bootstrap-5') !!}
    </div>
@endsection
