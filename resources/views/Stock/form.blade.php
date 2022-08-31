@if (count($errors) > 0)
    <div class="alert alert-danger" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<input type="hidden" name="id_refer"  value="{{$id_refer}}">
<table class="table table-striped table-hover table-md" id="movement-t">
    <thead>
        <tr>
            <th class="table-light">#</th>
            <th class="table-light">Codigo</th>
            <th class="table-light">Nombre</th>
            <th class="table-light">Unidad</th>
            <th class="table-light">Tipo</th>
            <th class="table-light">Cantidad</th>
            <th class="table-light text-center">Eliminar</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($movements))
        @foreach ($movements as $movement)
        <tr>
            <td>{{ $movement->id_article->id }}</td>
            <td>{{ $movement->id_article->code }}</td>
            <td>{{ $movement->id_article->name }}</td>
            <td>{{ $movement->id_article->unit }}</td>
            <td>{{ $movement->id_article->type }}</td>
            <td><input type="number" name="{{ $movement->id_article->id }}[quantity]" value="{{ $movement->quantity }}"></td>
            <td class="text-center">
                <input type="hidden" name="{{ $movement->id_article->id }}[id]"  value="{{$movement->id}}">
                <input class="id_article" type="hidden" name="{{ $movement->id_article->id }}[id_article]" value="{{ $movement->id_article->id }}">
                <input class="expandCheckbox" type="checkbox" name="{{ $movement->id_article->id }}[delete]">
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
<br>
<button class="btn btn-dark article-btn m-2" type="button" data-bs-toggle="collapse" data-bs-target="#article-t"
    aria-expanded="true" aria-controls="article-t">
    Agregar articulos</button>
<br>
<input class="btn btn-success m-2" type="submit" value="{{ $modo }} Datos">
<a class="btn btn-primary m-2" href="{{ url('refer/') }}">Regresar</a>

<div class="aticle-container collapse" id="article-t">
    <div class="article-store">
        <div class="article-tittle d-grid gap-2 ">

            <button class="btn btn-dark article-btn" type="button" data-bs-toggle="collapse"
                data-bs-target="#article-t" aria-expanded="true" aria-controls="article-t">X</button>
            <h3 class="text-center">Seleccionar Articulos</h3>
            <div class="article-scroll ">
                <table id="article-t" class="table table-striped table-hover table-md" width="100%">
                    <thead>
                        <tr>
                            <th class="table-light">#</th>
                            <th class="table-light">Codigo</th>
                            <th class="table-light">Nombre</th>
                            <th class="table-light">Unidad</th>
                            <th class="table-light">Tipo</th>
                            <th class="table-light text-center">Agregar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($articles as $article)
                            <tr>
                                <td>{{ $article->id }}</td>
                                <td>{{ $article->code }}</td>
                                <td>{{ $article->name }}</td>
                                <td>{{ $article->unit }}</td>
                                <td>{{ $article->type }}</td>
                                <td class="text-center">
                                    <input class="expandCheckbox" type="checkbox" name="article" value="{{ $article->id }}">

                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
            <div class="article-buttom">
                <button class="btn btn-success md-block" type="button" data-bs-toggle="collapse"
                    data-bs-target="#article-t" aria-expanded="true" aria-controls="article-t" id="insert">
                    Ingresar</button>
            </div>
        </div>
    </div>
</div>
@section('js')
    @vite(['resources/js/functions/movement.js'])
@endsection
