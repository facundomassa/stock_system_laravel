@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Importar Datos desde CSV</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Seleccionar Archivo CSV (separado por punto y coma)</label>
                            <input class="form-control" type="file" id="csv_file" name="csv_file" required accept=".csv">
                        </div>

                        <div class="mb-3">
                            <label for="model_type" class="form-label">Tipo de Datos a Importar</label>
                            <select class="form-select" id="model_type" name="model_type">
                                <option value="articles" selected>Artículos</option>
                                <option value="persons">Personas</option>
                                <option value="directions">Direcciones</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Importar</button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">Instrucciones para la Importación</div>
                <div class="card-body">
                    <p class="text-danger"><strong>¡Importante!</strong> El separador de columnas debe ser un punto y coma (<strong>;</strong>). La primera fila del archivo debe contener las cabeceras, y será ignorada por el sistema.</p>
                    <hr>

                    <h5>Formato para Artículos</h5>
                    <p>El archivo CSV debe tener las siguientes columnas en este orden:</p>
                    <ol>
                        <li><strong>name</strong> (Obligatorio): Nombre del artículo. Máx. 60 caracteres.</li>
                        <li><strong>code</strong> (Opcional): Código del artículo. Máx. 12 caracteres.</li>
                        <li><strong>unit</strong> (Obligatorio): Unidad de medida. 1 caracter (ej: U, L).</li>
                        <li><strong>type</strong> (Opcional): Tipo o categoría. Máx. 30 caracteres.</li>
                    </ol>
                    <p><strong>Ejemplo:</strong> <code>"Tornillo Phillips 3x15mm, cabeza plana";T001;U;Ferretería</code></p>
                    <hr>

                    <h5>Formato para Personas</h5>
                    <p>El archivo CSV debe tener las siguientes columnas en este orden:</p>
                    <ol>
                        <li><strong>name</strong> (Obligatorio): Nombre de la persona. Máx. 60 caracteres.</li>
                        <li><strong>surname</strong> (Opcional): Apellido. Máx. 60 caracteres.</li>
                        <li><strong>email</strong> (Opcional): Correo electrónico. Debe ser un email válido.</li>
                        <li><strong>cuit</strong> (Opcional): Número de CUIT. Solo números.</li>
                        <li><strong>telephone</strong> (Opcional): Número de teléfono. Solo números.</li>
                    </ol>
                    <p><strong>Ejemplo:</strong> <code>"Juan";"Pérez";"juan.perez@ejemplo.com";20123456789;1155443322</code></p>
                    <hr>

                    <h5>Formato para Direcciones</h5>
                    <p>El archivo CSV debe tener las siguientes columnas en este orden:</p>
                    <ol>
                        <li><strong>country</strong> (Obligatorio): País. Máx. 60 caracteres.</li>
                        <li><strong>state</strong> (Obligatorio): Provincia o estado. Máx. 60 caracteres.</li>
                        <li><strong>city</strong> (Obligatorio): Ciudad. Máx. 60 caracteres.</li>
                        <li><strong>locality</strong> (Obligatorio): Localidad o barrio. Máx. 100 caracteres.</li>
                        <li><strong>street</strong> (Obligatorio): Calle. Máx. 100 caracteres.</li>
                        <li><strong>number</strong> (Obligatorio): Altura. Solo números.</li>
                        <li><strong>department</strong> (Opcional): Departamento. Máx. 6 caracteres.</li>
                        <li><strong>house</strong> (Opcional): Casa. Máx. 6 caracteres.</li>
                        <li><strong>floor</strong> (Opcional): Piso. Máx. 4 caracteres.</li>
                        <li><strong>cp</strong> (Obligatorio): Código Postal. Solo números.</li>
                    </ol>
                    <p><strong>Ejemplo:</strong> <code>"Argentina";"Buenos Aires";"CABA";"Palermo";"Av. Santa Fe";1234;"A";"";"5";1425</code></p>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
