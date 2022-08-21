@if (count($errors) > 0)
    <div class="alert alert-danger" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="form-group">
    <label for="id_enterprise">Operacion:</label>
    <select required class="form-control" name="id_enterprise" id="id_enterprise">
        <option disabled selected value="">-Seleccionar una opcion-</option>
        @foreach ($enterprises as $enterprise)
            @if ((isset($stockcenter->id_enterprise) ? $stockcenter->id_enterprise : old('id_enterprise')) == $enterprise->id)
                <option selected value="{{ $enterprise->id }}"> {{ $enterprise->name }}</option>
            @else
                <option value="{{ $enterprise->id }}"> {{ $enterprise->name }}</option>
            @endif
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="name">Nombre:</label>
    <input required class="form-control" type="text" name="name" maxlength="60"
        value="{{ isset($stockcenter->name) ? $stockcenter->name : old('name') }}" id="name">
</div>
<div class="form-group">
    <label for="type">Tipo:</label>
    <select required class="form-control" name="type" id="type">
        <option disabled selected value="">-Seleccionar una opcion-</option>
        <option {{(isset($stockcenter->type) ? $stockcenter->type : old('type')) == "D" ? "selected" : ""}} value="D"> Deposito</option>
        <option {{(isset($stockcenter->type) ? $stockcenter->type : old('type')) == "M" ? "selected" : ""}} value="M"> Movil</option>
        <option {{(isset($stockcenter->type) ? $stockcenter->type : old('type')) == "T" ? "selected" : ""}} value="T"> Taller</option>
        <option {{(isset($stockcenter->type) ? $stockcenter->type : old('type')) == "C" ? "selected" : ""}} value="C"> Consumo</option>
        <option {{(isset($stockcenter->type) ? $stockcenter->type : old('type')) == "P" ? "selected" : ""}} value="P"> Proveedor</option>
    </select>
</div>
<div class="form-group">
    <label for="id_direction">Direccion:</label>
    <select class="form-control" name="id_direction" id="id_direction">
        <option disabled selected value="">-Seleccionar una opcion-</option>
        @foreach ($directions as $direction)
            @if ((isset($stockcenter->id_direction) ? $stockcenter->id_direction : old('id_direction')) == $direction->id)
                <option selected value="{{ $direction->id }}"> {{ $direction->street . $direction->number }}</option>
            @else
                <option value="{{ $direction->id }}"> {{ $direction->street . $direction->number }}</option>
            @endif
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="id_person">Encargado:</label>
    <select class="form-control" name="id_person" id="id_person">
        <option disabled selected value="">-Seleccionar una opcion-</option>
        @foreach ($persons as $person)
            @if ((isset($stockcenter->id_person) ? $stockcenter->id_person : old('id_person')) == $person->id)
                <option selected value="{{ $person->id }}"> {{ $person->name . ' ' . $person->surname }}</option>
            @else
                <option value="{{ $person->id }}"> {{ $person->name . ' ' . $person->surname }}</option>
            @endif
        @endforeach
    </select>
</div>
<br>
<input class="btn btn-success" type="submit" value="{{ $modo }} Datos">
<a class="btn btn-primary" href="{{ url('stockcenter/') }}">Regresar</a>
