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
                <div class="col-auto ms-auto">
                    <a class="btn btn-outline-primary py-0 " title="Generar Pdf" onclick="openPdf('{{ url('stock/get/pdf?stockselect='.$stockselect.'&code='.$code.'&articlename='.$articlename.'&type='.$type) }}')">
                    <i class="bi bi-file-pdf"></i></a>
                    <a class="btn btn-outline-success py-0 " title="Generar Excel" 
                    href="{{ url('stock/get/excel?stockselect='.$stockselect.'&code='.$code.'&articlename='.$articlename.'&type='.$type) }}">
                    <i class="bi bi-file-earmark-spreadsheet"></i></a>
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
                    <th class="table-light">Alerta</th>
                    <th class="table-light">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stocks as $stock)
                    <tr {{ $stock->warning ? "class=table-alert" : "class=bg-light"}}>
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
        
        <!-- Modal -->
        <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pdfModalLabel">PDF Viewer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body bg-dark">
                        <!-- Aquí se cargará el iframe -->
                        <iframe id="pdfIframe" src="" style="width: 100%; height: 500px;"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    @vite('resources/js/ajax/generatepdf.js')
    <script>
        function openPdf(pdfUrl) {
            $('#pdfIframe').attr('src', pdfUrl);
            $('#pdfModal').modal('show');
        }
    </script>
@endsection