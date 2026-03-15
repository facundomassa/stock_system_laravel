@if (count($errors) > 0)
    <div class="alert alert-danger" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">{{ $modo }} Operación</div>

            <div class="card-body">
                <div class="form-group">
                    <label for="name">Nombre:</label>
                    <input required class="form-control" type="text" name="name" maxlength="60"
                        value="{{ isset($operation->name) ? $operation->name : old('name') }}" id="name">
                </div>

                <h5 class="mt-4 border-bottom pb-2">Configuración para Técnicos</h5>

                <div class="form-group mb-3 mt-3">
                    <label for="request_origin_id">Origen de solicitudes (De dónde salen los materiales):</label>
                    <select class="form-select" name="request_origin_id" id="request_origin_id">
                        <option value="">-- Sin asignar --</option>
                        @foreach($stockcenters as $stockcenter)
                            <option value="{{ $stockcenter->id }}" {{ (isset($operation->config) && $operation->config->request_origin_id == $stockcenter->id) ? 'selected' : '' }}>
                                {{ $stockcenter->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label for="consume_destiny_id">Destino de consumos (A dónde van los materiales gastados):</label>
                    <select class="form-select" name="consume_destiny_id" id="consume_destiny_id">
                        <option value="">-- Sin asignar --</option>
                        @foreach($stockcenters as $stockcenter)
                            <option value="{{ $stockcenter->id }}" {{ (isset($operation->config) && $operation->config->consume_destiny_id == $stockcenter->id) ? 'selected' : '' }}>
                                {{ $stockcenter->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <input class="btn btn-success" type="submit" value="{{ $modo }} Datos">
                <a class="btn btn-primary" href="{{ url('operation/') }}">Regresar</a>
            </div>
        </div>
    </div>
</div>