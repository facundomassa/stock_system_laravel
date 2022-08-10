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
    <input class="form-control" type="text" name="name" maxlength="60"
        value="{{ isset($article->name) ? $article->name : old('name') }}" id="name">
</div>
<div class="form-group">
    <label for="code">Codigo:</label>
    <input class="form-control" type="text" name="code" maxlength="16"
        value="{{ isset($article->code) ? $article->code : old('code') }}" id="code">
</div>
<div class="form-group">
    <label for="unit">Unidad:</label>
    <input class="form-control" type="text" name="unit" maxlength="1"
        value="{{ isset($article->unit) ? $article->unit : old('unit') }}" id="unit">
</div>
<div class="form-group">
    <label for="type">Tipo:</label>
    <input class="form-control" type="text" name="type" maxlength="30"
        value="{{ isset($article->type) ? $article->type : old('type') }}" id="type">
</div>
<br>
<input class="btn btn-success" type="submit" value="{{ $modo }} Datos">
<a class="btn btn-primary" href="{{ url('article/') }}">Regresar</a>
