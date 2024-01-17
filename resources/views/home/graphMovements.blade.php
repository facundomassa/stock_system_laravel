@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="{{ url('/home') }}" method="get">
            <p class="m-0 filter">Filtros:</p>
            <div class="row align-items-center border border-secondary p-2 mb-2 rounded">
                <div class="col-6">
                    <label for="stockselectorigen">Centro de Stock Origen:</label>
                    <select required class="form-control" name="stockselectorigen" maxlength="60" id="stockselectorigen">
                        <option selected value="*">-Todos-</option>
                        @foreach ($stockcenters as $stockcenter)
                            @if ($stockselectorigen == $stockcenter->id)
                                <option selected value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}
                                </option>
                            @else
                                <option value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label for="stockselectdestiny">Centro de Stock Destino:</label>
                    <select required class="form-control" name="stockselectdestiny" maxlength="60" id="stockselectdestiny">
                        <option selected value="*">-Todos-</option>
                        @foreach ($stockcenters as $stockcenter)
                            @if ($stockselectdestiny == $stockcenter->id)
                                <option selected value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}
                                </option>
                            @else
                                <option value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-6 p-3">
                    <p class="m-0 filter">Fechas de creado:</p>
                    <div class="align-items-center border border-secondary p-2 rounded">
                        <label for="date_ended">Desde:</label>
                        <input class="form-control" type="date" value="{{ isset($mes) ? $mes : old('mes') }}"
                            name="date_up" id="date_up">
                        <label for="date_ended">Hasta:</label>
                        <input class="form-control" type="date"
                            value="{{ isset($hoy) ? $hoy : old('hoy') ?: date('Y-m-d') }}" name="date_ended"
                            id="date_ended">
                    </div>
                </div>
<<<<<<< Updated upstream
                <div class="col-6 p-3">
                    <p class="m-0 filter">Fechas de finalizado:</p>
                    <div class="align-items-center border border-secondary p-2 rounded">
                        <label for="date_ended">Desde:</label>
                        <input class="form-control" type="date" value="{{ isset($mes) ? $mes : old('mes') }}"
                            name="date_up" id="date_up">
                        <label for="date_ended">:</label>
                        <input class="form-control" type="date"
                            value="{{ isset($hoy) ? $hoy : old('hoy') ?: date('Y-m-d') }}" name="date_ended"
                            id="date_ended">
=======
            </form>
            <div class="row">
                <div class="col-md-12 ">
                    <div class="panel panel-default">
                        <div class="panel-heading my-2">Movimiento de materiales finalizados del
                            {{ Helpers::x_fechaEspañol($mes) }} al {{ Helpers::x_fechaEspañol($hoy)}}</div>
                        <canvas id="userChart" width="100%" height="90px" class="rounded shadow"></canvas>
>>>>>>> Stashed changes
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Refrescar</button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-md-12 ">
                <div class="panel panel-default">
                    <div class="panel-heading my-2">Movimiento de materiales finalizados de depositos del
                        {{ x_fechaEspañol($mes) }} al {{ x_fechaEspañol($hoy) }}</div>
                    <canvas id="userChart" width="100%" height="90px" class="rounded shadow"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('userChart').getContext('2d');
        var chart = new Chart(ctx, {
            // The type of chart we want to create
            type: 'bar',
            // The data for our dataset
            data: {
                labels: {!! json_encode($chart->labels) !!},
                datasets: [{
                    label: 'Cantidad de articulos',
                    backgroundColor: {!! json_encode($chart->colours) !!},
                    data: {!! json_encode($chart->dataset) !!},
                }, ]
            },
            // Configuration options go here
            options: {
                indexAxis: 'y',
                scales: {
                    // x: {
                    //     type: 'linear',
                    //     min: -200,
                    //     max: 1000
                    // }
                },
                legend: {
                    labels: {
                        // This more specific font property overrides the global property
                        fontColor: '#122C4B',
                        fontFamily: "'Muli', sans-serif",
                        padding: 25,
                        boxWidth: 25,
                        fontSize: 14,
                    }
                },
            }
        });
    </script>
@endsection
