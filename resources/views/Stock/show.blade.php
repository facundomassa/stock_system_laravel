@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between">
            <div class="flex-grow-1">
                    
                <form class="d-inline" action="{{ url('/stock/' . $stock->id) }}" method="post">
                    @csrf
                    {{ method_field('PUT') }}
                    <p>{{ $stock->id }}</p>
                    <div class="form-group">
                        <label for="name">Stock Center:</label>
                        <p>{{ $stock->StockCenter->name }}</p>
                    </div>
                    <div class="form-group">
                        <label for="code">Codigo:</label>
                        <p>{{ $stock->Article->code }}</p>
                    </div>
                    <div class="form-group">
                        <label for="article">Articulo:</label>
                        <p>{{ $stock->Article->name }}</p>
                    </div>
                    <div class="form-group">
                        <label for="unit">Unidad:</label>
                        <p>{{ $stock->Article->UnitName }}</p>
                    </div>
                    <div class="form-group">
                        <label for="type">Tipo:</label>
                        <p>{{ $stock->Article->type }}</p>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Cantidad:</label>
                        <p>{{ $stock->quantity }}</p>
                    </div>

                    <div class="form-group">
                        <label for="quantity_alert">Limite de alerta:</label>
                        <input class="form-control" type="number" name="quantity_alert" maxlength="11"
                            value="{{ isset($stock->quantity_alert) ? $stock->quantity_alert : old('quantity_alert') }}" id="quantity_alert">
                    </div>

                    <div class="form-group">
                        <label for="telephone">Ultima Actualizacion:</label>
                        <p>{{ $stock->updated_at }}</p>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-success">Editar</button>
                    <a class="btn btn-primary" href="{{ url('stock/') }}">Regresar</a>
                </form>
                <form class="d-inline" action="{{ url('/stock/' . $stock->id) }}" method="post">
                    @csrf
                    {{ method_field('DELETE') }}
                    <input class="btn btn-danger" type="submit" onclick="return confirm('¿Quieres borrar?')" value="Borrar">
                </form>
            </div>
        <div class="d-flex flex-column w-50">
            <h3 class="text-center">Historial</h3>
            <table class="table table-striped table-hover table-md">
                <thead>
                    <tr>
                        <th class="table-light">#</th>
                        <th class="table-light">N° Remito</th>
                        <th class="table-light">Origen</th>
                        <th class="table-light">Destino</th>
                        <th class="table-light">Fecha</th>
                        <th class="table-light">Cantidad</th>
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
                            @if ($movement->Refer->nameOrigin == $stock->StockCenter->name) 
                            <td class="C"> -  {{$movement->quantity }}</td>
                            @else
                            <td class="F"> + {{$movement->quantity }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
    
            </table>
            {!! $movements->links('vendor.pagination.bootstrap-5') !!}
        </div>
    </div>
@endsection
