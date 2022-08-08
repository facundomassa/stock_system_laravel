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
    <label for="name">Nombre:</label>
    <input class="form-control" type="text" name="name"
        value="{{ isset($person->name) ? $person->name : old('name') }}" id="name">
</div>
<div class="form-group">
    <label for="surname">Apellido:</label>
    <input class="form-control" type="text" name="surname"
        value="{{ isset($person->surname) ? $person->surname : old('surname') }}" id="surname">
</div>
<div class="form-group">
    <label for="email">Correo:</label>
    <input class="form-control" type="email" name="email"
        value="{{ isset($person->email) ? $person->email : old('email') }}" id="email">
</div>
<div class="form-group">
    <label for="cuit">CUIT:</label>
    <input class="form-control" type="number" name="cuit"
        value="{{ isset($person->cuit) ? $person->cuit : old('cuit') }}" id="cuit">
</div>
<div class="form-group">
    <label for="telephone">Numero de Telefono:</label>
    <input class="form-control" type="number" name="telephone"
        value="{{ isset($person->telephone) ? $person->telephone : old('telephone') }}" id="telephone">
</div>
<br>
<input class="btn btn-success" type="submit" value="{{ $modo }} Datos">
<a class="btn btn-primary" href="{{ url('person/') }}">Regresar</a>
