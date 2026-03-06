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
                <br>
                <input class="btn btn-success" type="submit" value="{{ $modo }} Datos">
                <a class="btn btn-primary" href="{{ url('operation/') }}">Regresar</a>
            </div>
        </div>
    </div>  
</div>