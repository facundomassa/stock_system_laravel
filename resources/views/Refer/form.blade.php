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
    <label for="origen_id_stockcenter">Origen:</label>
    <select required class="form-control" name="origen_id_stockcenter" id="origen_id_stockcenter">
        <option disabled selected value="">-Seleccionar una opcion-</option>
        @foreach ($stockcenters as $stockcenter)
            @if ((isset($refer->origen_id_stockcenter) ? $refer->origen_id_stockcenter : old('origen_id_stockcenter')) == $stockcenter->id)
                <option selected value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}</option>
            @else
                <option value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}</option>
            @endif
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="destiny_id_stockcenter">Origen:</label>
    <select required class="form-control" name="destiny_id_stockcenter" id="destiny_id_stockcenter">
        <option disabled selected value="">-Seleccionar una opcion-</option>
        @foreach ($stockcenters as $stockcenter)
            @if ((isset($refer->destiny_id_stockcenter) ? $refer->destiny_id_stockcenter : old('destiny_id_stockcenter')) == $stockcenter->id)
                <option selected value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}</option>
            @else
                <option value="{{ $stockcenter->id }}"> {{ $stockcenter->name }}</option>
            @endif
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="status">Estatus:</label>
    <P>{{isset($refer->statusName) ? $refer->statusName : "INGRESADO"}}</P>
</div>
<div class="form-group">
    <label for="date_ended">Fecha Finalizado:</label>
    <input class="form-control" min="{{date("Y-m-d") . "T" . date("h:i")}}" type="datetime-local" value="{{isset($refer->date_ended) ? $refer->date_ended : old('date_ended')}}" name="date_ended" id="date_ended">
</div>
<div class="form-group">
    <label>Creado por:</label>
    <select disabled class="form-control" >
        <option selected value="{{ auth()->id(); }}"> {{ auth()->user()->name . " " . auth()->user()->surname; }}</option>
    </select>
</div>
<br>
<input class="btn btn-success" type="submit" value="{{ $modo }} Datos">
<a class="btn btn-primary" href="{{ url('refer/') }}">Regresar</a>
