@extends('layouts.app')

@section('content')
    <div class="container">
        @include('layouts/alert')
        <form action="{{ url('/stock/') }}" method="post">
            @csrf
            {{ method_field('GET') }}
            <label for="sc">Centro de Stock:</label>
            <select required class="form-control" name="sc" maxlength="60" id="stockcenter" onchange="this.form.submit()">
                <option selected value="*">-Todos-</option>
                @foreach ($stockcenters as $stockcenter)
                    @if ($sc == $stockcenter->id)
                        <option selected value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}</option>
                    @else
                        <option value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}</option>
                    @endif
                @endforeach
            </select>
            <label for="ar">Articulo:</label>
            <input class="form-control" type="text" name="ar" maxlength="40"
                value="{{ isset($ar) ? $ar : old('ar') }}" id="ar" onchange="this.form.submit()">
        </form>
        <table class="table table-striped table-hover table-md">
            <thead>
                <tr>
                    <th class="table-light">#</th>
                    <th class="table-light">Centro de Stock</th>
                    <th class="table-light">Articulo</th>
                    <th class="table-light">Cantidad</th>
                    <th class="table-light">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stocks as $stock)
                    <tr>
                        <td>{{ $stock->id }}</td>
                        <td>{{ $stock->StockCenter->name }}</td>
                        <td>{{ $stock->Article->name }}</td>
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
