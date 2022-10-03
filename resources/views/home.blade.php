@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-evenly">
                <div class="card col-12 col-lg-6">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="panel panel-default">
                                    <div class="panel-heading my-2">Chart Demo</div>
                                    <canvas id="userChart" width="100%" height="90px" class="rounded shadow"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card col-12 col-lg-6">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading my-2">Chart Demo</div>
                                    <canvas id="userChart2" width="100%" height="90px" class="rounded shadow"></canvas>
                                </div>
                            </div>
                        </div>
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
        var ctx2 = document.getElementById('userChart2').getContext('2d');
        var chart2 = new Chart(ctx2, {
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
