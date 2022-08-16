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
    <label for="country">Pais:</label>
    <select required class="form-control" name="country" maxlength="60" id="country">
        <option disabled selected value="">-Seleccionar una opcion-</option>
        @foreach ($countrys as $country)
            @if ((isset($direction->country) ? $direction->country : old('country')) == $country['country_name'])
                <option selected value="{{ $country['country_name'] }}"> {{ $country['country_name'] }}</option>
            @else
                <option value="{{ $country['country_name'] }}"> {{ $country['country_name'] }}</option>
            @endif
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="state">Provincia:</label>
    <select required class="form-control" name="state" maxlength="60" id="state">
        <option disabled selected value="">-Seleccionar una opcion-</option>
        @if (isset($direction->country) )
                <option selected value="{{ isset($direction->state) ? $direction->state : old('state') }}"> {{isset($direction->state) ? $direction->state : old('state')}}</option>
        @endif
    </select>
</div>
<div class="form-group">
    <label for="city">Ciudad:</label>
    <input required class="form-control" type="text" name="city" maxlength="60"
        value="{{ isset($direction->city) ? $direction->city : old('city') }}" id="city">
</div>
<div class="form-group">
    <label for="locality">Localidad:</label>
    <input required class="form-control" type="text" name="locality" maxlength="100"
        value="{{ isset($direction->locality) ? $direction->locality : old('locality') }}" id="locality">
</div>
<div class="form-group">
    <label for="street">Calle:</label>
    <input required class="form-control" type="text" name="street" maxlength="100"
        value="{{ isset($direction->street) ? $direction->street : old('street') }}" id="street">
</div>
<div class="form-group">
    <label for="number">Numero:</label>
    <input required class="form-control" type="number" name="number" maxlength="11"
        value="{{ isset($direction->number) ? $direction->number : old('number') }}" id="number">
</div>
<div class="form-group">
    <label for="department">Departamento:</label>
    <input class="form-control" type="text" name="department" maxlength="6"
        value="{{ isset($direction->department) ? $direction->department : old('department') }}" id="department">
</div>
<div class="form-group">
    <label for="house">Casa:</label>
    <input class="form-control" type="text" name="house" maxlength="6"
        value="{{ isset($direction->house) ? $direction->house : old('house') }}" id="house">
</div>
<div class="form-group">
    <label for="floor">Piso:</label>
    <input class="form-control" type="text" name="floor" maxlength="4"
        value="{{ isset($direction->floor) ? $direction->floor : old('floor') }}" id="floor">
</div>
<div class="form-group">
    <label for="cp">Codigo Postal:</label>
    <input required class="form-control" type="number" name="cp" maxlength="11"
        value="{{ isset($direction->cp) ? $direction->cp : old('cp') }}" id="cp">
</div>
<br>
<input class="btn btn-success" type="submit" value="{{ $modo }} Datos">
<a class="btn btn-primary" href="{{ url('direction/') }}">Regresar</a>

@vite(['resources/js/ajax/state.js'])
