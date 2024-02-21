
<div class="row p-2">
    <a class="btn p-2" type="button" data-bs-toggle="collapse" href="#report1" role="button" aria-expanded="false" aria-controls="report1"">
        Retiro de materiales por fecha y centro de stock
    </a>
    <div class="collapse p-2" id="report1">
        <p class="text-muted m-0">Codigo - Articulo - Unidad - Tipo - Cantidad</p>
        <form action="{{ url('/home/reportRpFyS') }}" method="get">
            <div class="row">
                <div class="col-4 p-2">
                    <p class="m-0 filter">Fecha de finalizado:</p>
                    <div class="align-items-center border border-secondary p-3 rounded">
                        <label for="date_start">Desde:</label>
                        <input class="form-control" type="date" 
                            name="date_start" id="date_start" required>
                        <label for="date_end">Hasta:</label>
                        <input class="form-control" type="date" name="date_end"
                            id="date_end" required>
                    </div>
                </div>
                <div class="col-4 p-2">
                    <p class="m-0 filter">Centro de stock Origen:</p>
                    <div class="align-items-center border border-secondary p-3 rounded">
                        @foreach ($stockcenters as $stockcenter)
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="check_origin[]" value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="col-4 p-2">
                    <p class="m-0 filter">Centro de stock Destino:</p>
                    <div class="align-items-center border border-secondary p-3 rounded">
                        @foreach ($stockcenters as $stockcenter)
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="check_destiny[]" value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            
        <button type="submit" class="btn btn-primary">Generar reporte</button>
        </form>
    </div>
    <a class="btn p-2" type="button" data-bs-toggle="collapse" href="#report2" role="button" aria-expanded="false" aria-controls="report1"">
        Todos los movimientos por fecha
    </a>
    <div class="collapse p-2" id="report2">
        <p class="text-muted m-0">Numero de remito - Origen - Destino - Fecha emitido - Fecha finalizado - Usuario - Estado - Codigo - Articulo - Unidad - Tipo - Cantidad</p>
        <form action="{{ url('/home/reportAllMovement') }}" method="get">
            <div class="row">
                <div class="p-2">
                    <p class="m-0 filter">Fecha de finalizado:</p>
                    <div class="align-items-center border border-secondary p-3 rounded">
                        <label for="date_start">Desde:</label>
                        <input class="form-control" type="date" 
                            name="date_start" id="date_start" required>
                        <label for="date_end">Hasta:</label>
                        <input class="form-control" type="date" name="date_end"
                            id="date_end" required>
                    </div>
                </div>
            </div>
            
        <button type="submit" class="btn btn-primary">Generar reporte</button>
        </form>
    </div>
</div>