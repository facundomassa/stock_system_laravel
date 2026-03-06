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
            <div class="card-header"->{{ $modo }} Movimiento</div>

            <div class="card-body">
                <input type="hidden" name="id_refer" value="{{ $refer->id }}">
                <table class="table table-striped table-hover table-md" id="movement-t">
                    <thead>
                        <tr>
                            <th class="table-light">#</th>
                            <th class="table-light">Codigo</th>
                            <th class="table-light">Nombre</th>
                            <th class="table-light">Unidad</th>
                            <th class="table-light">Tipo</th>
                            <th class="table-light">Cantidad</th>
                            <th class="table-light">En Stock</th>
                            <th class="table-light text-center">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($movements))
                            @foreach ($movements as $movement)
                                <tr>
                                    <td>{{ $movement->article->id }}</td>
                                    <td>{{ $movement->article->code }}</td>
                                    <td>{{ $movement->article->name }}</td>
                                    <td>{{ $movement->article->unitName }}</td>
                                    <td>{{ $movement->article->type }}</td>
                                    <td><input type="number" name="{{ $movement->article->id }}[quantity]"
                                            value="{{ $movement->quantity }}"></td>
                                    <td>{{ $movement->stock->quantity ?? '-' }}</td>
                                    <td class="text-center">
                                        <input type="hidden" name="{{ $movement->article->id }}[id]" value="{{ $movement->id }}">
                                        <input class="id_article" type="hidden" name="{{ $movement->article->id }}[id_article]"
                                            value="{{ $movement->article->id }}">
                                        <input class="expandCheckbox" type="checkbox" name="{{ $movement->article->id }}[delete]">
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

                <div class="fixed-top aticle-container collapse" id="article-t">
                    <div class="article-store">
                        <div class="article-title d-flex gap-2 flex-column">

                            <button class="btn btn-dark article-btn" type="button" data-bs-toggle="collapse"
                                data-bs-target="#article-t" aria-expanded="true" aria-controls="article-t">X</button>

                            <div class="filters" style="max-height: 160px">
                                <h3 class="text-center">Seleccionar Articulos</h3>
                                <p class="m-0 filter">Filtros:</p>
                                <div class="row align-items-center border border-secondary p-2 mb-2 rounded">
                                    <div class="col-auto">
                                        <label for="codeTx">Codigo:</label>
                                        <input class="form-control" type="text" maxlength="40"
                                            value="{{ isset($codeTx) ? $codeTx : old('codeTx') }}" id="codeTx">
                                    </div>
                                    <div class="col-auto">
                                        <label for="nameTx">Nombre:</label>
                                        <input class="form-control" type="text" maxlength="40"
                                            value="{{ isset($nameTx) ? $nameTx : old('nameTx') }}" id="nameTx">
                                    </div>
                                    <div class="col-auto">
                                        <label for="typeTx">Tipo:</label>
                                        <input class="form-control" type="text" maxlength="40"
                                            value="{{ isset($typeTx) ? $typeTx : old('typeTx') }}" id="typeTx">
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-primary md-block" id="filter-b">Buscar</button>
                                    </div>
                                </div>
                            </div>

                            <div class="article-scroll flex-grow-1">
                                <table id="article-t" class="table table-striped table-hover table-md" width="100%">
                                    <thead>
                                        <tr>
                                            <th class="table-light">#</th>
                                            <th class="table-light">Codigo</th>
                                            <th class="table-light">Nombre</th>
                                            <th class="table-light">Unidad</th>
                                            <th class="table-light">Tipo</th>
                                            <th class="table-light">En Stock</th>
                                            <th class="table-light text-center">Agregar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($articles as $article)
                                            <tr>
                                                <td>{{ $article->id }}</td>
                                                <td>{{ $article->code }}</td>
                                                <td>{{ $article->name }}</td>
                                                <td>{{ $article->unitName }}</td>
                                                <td>{{ $article->type }}</td>
                                                <td>{{ isset($article->stock_quantity) ? $article->stock_quantity : '-' }}</td>
                                                <td class="text-center">
                                                    <input class="expandCheckbox" type="checkbox" name="article"
                                                        value="{{ $article->id }}"
                                                        data-stock="{{ isset($article->stock_quantity) ? $article->stock_quantity : '-' }}">

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
                    <script>
                        let route = "{{ url('api/article') }}";
                        let refer = {{ $refer->id }};
                    </script>
                @endsection
            </div>
        </div>
    </div>
</div>