@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        {{-- Columna de Navegación Lateral --}}
        <div class="col-md-3">
            <div class="sticky-top mb-4">
                <div class="card">
                    <div class="card-header">Manual de Usuario</div>
                    <div class="card-body">
                        {{-- Lista de navegación que actúa como tabla de contenidos --}}
                        <ul class="nav flex-column" id="manual-nav">
                            <li class="nav-item"><a class="nav-link" href="#introduccion">1. Introducción</a></li>
                            <li class="nav-item"><a class="nav-link" href="#conceptos-clave">2. Conceptos Clave</a></li>
                            <li class="nav-item"><a class="nav-link" href="#primeros-pasos">3. Primeros Pasos</a></li>
                            <li class="nav-item"><a class="nav-link" href="#gestion-remitos">4. Gestión de Remitos</a></li>
                            <li class="nav-item"><a class="nav-link" href="#ver-stock">5. Consultar el Stock</a></li>
                            <li class="nav-item"><a class="nav-link" href="#administracion">6. Administración</a></li>
                            <li class="nav-item"><a class="nav-link" href="#importacion-csv">7. Importación (Avanzado)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna de Contenido Principal --}}
        <div class="col-md-9">
            {{-- 1. Introducción --}}
            <div class="card mb-4" id="introduccion">
                <div class="card-header"><h3>1. Introducción al Sistema de Stock</h3></div>
                <div class="card-body">
                    <p class="lead">Bienvenido al manual de usuario de <strong>Stock System</strong>.</p>
                    <p>Esta aplicación ha sido creada para simplificar la gestión de inventario en empresas que manejan <strong>múltiples almacenes, depósitos o centros de stock</strong>. El objetivo principal es que puedas rastrear cada artículo y saber exactamente dónde se encuentra en todo momento.</p>
                    <p>La lógica del sistema se centra en los <strong>Remitos</strong>, documentos digitales que registran cada movimiento de mercadería, ya sea una entrada, una salida o una transferencia interna.</p>
                </div>
            </div>

            {{-- 2. Conceptos Clave --}}
            <div class="card mb-4" id="conceptos-clave">
                <div class="card-header"><h3>2. Conceptos Clave: Datos Maestros vs. Operaciones</h3></div>
                <div class="card-body">
                    <p>Para usar el sistema correctamente, es vital entender los dos tipos de información que maneja:</p>
                    <hr>
                    <h5><strong>A. Datos Maestros (o Catálogos Base)</strong></h5>
                    <p>Son la información fundamental que el sistema necesita para operar. Piénsalos como los "sustantivos" de tu negocio. Debes configurarlos antes de poder registrar movimientos.</p>
                    <ul>
                        <li><strong>Artículos:</strong> Tu catálogo de productos o materiales.</li>
                        <li><strong>Centros de Stock:</strong> Tus almacenes, tiendas, depósitos, etc.</li>
                        <li><strong>Personas:</strong> Clientes, proveedores, o personal.</li>
                        <li><strong>Direcciones:</strong> Ubicaciones físicas.</li>
                    </ul>
                    <div class="alert alert-light">Estos datos se gestionan en la sección de <strong>Administración</strong>.</div>

                    <h5><strong>B. Operaciones</strong></h5>
                    <p>Son las acciones que registran cambios en tu inventario. Son los "verbos" que actúan sobre tus datos maestros.</p>
                    <ul>
                        <li><strong>Remitos y Movimientos:</strong> Representan la transferencia de <strong>artículos</strong> entre <strong>centros de stock</strong>. Un remito es el documento (la carátula) y los movimientos son los artículos específicos dentro de ese documento.</li>
                    </ul>
                </div>
            </div>

            {{-- 3. Primeros Pasos --}}
            <div class="card mb-4" id="primeros-pasos">
                <div class="card-header"><h3>3. Primeros Pasos para Empezar a Operar</h3></div>
                <div class="card-body">
                    <p>Para poder crear tu primer movimiento de stock, necesitas tener cargados los datos mínimos necesarios. Sigue esta guía:</p>
                    <ol>
                        <li><strong>Crea tus Centros de Stock:</strong> Ve a <code>Administración > Centros de Stock</code> y añade los almacenes o depósitos que vayas a gestionar. Necesitas al menos uno para empezar.</li>
                        <li><strong>Carga tus Artículos:</strong> Ve a <code>Administración > Artículos</code> y crea los productos que vas a mover. Sin artículos, no hay inventario que gestionar.</li>
                        <li><strong>¡Listo para empezar!</strong> Con al menos un centro de stock y un artículo, ya puedes <a href="#gestion-remitos">crear tu primer Remito</a>.</li>
                    </ol>
                    <div class="alert alert-info"><strong>¿Tienes muchos datos?</strong> Para una carga inicial rápida, te recomendamos usar la <a href="#importacion-csv">herramienta de importación masiva</a>.</div>
                </div>
            </div>

            {{-- 4. Gestión de Remitos --}}
            <div class="card mb-4" id="gestion-remitos">
                <div class="card-header"><h3>4. Gestión de Remitos: El Corazón del Sistema</h3></div>
                <div class="card-body">
                    <p>Casi toda la operatoria diaria se realiza a través de los remitos. Un remito sigue un ciclo de vida claro:</p>
                    <h5 class="mt-3">Paso 1: Crear la Carátula del Remito</h5>
                    <ol>
                        <li>En el menú principal, haz clic en <strong>Remitos</strong> y luego en el botón <strong>"Crear Remito"</strong>.</li>
                        <li>Completa la información principal: Centro de Origen, Centro de Destino y Tipo de Remito.</li>
                        <li>En este punto, el remito se crea con estado <strong>"Pendiente"</strong>. Aún no afecta a tu stock.</li>
                    </ol>

                    <h5 class="mt-3">Paso 2: Añadir Artículos (Movimientos)</h5>
                    <ol>
                        <li>En la lista de remitos, busca tu remito "Pendiente" y haz clic en el icono del ojo (&#128065;) para <strong>Ver Detalles</strong>.</li>
                        <li>Dentro de la página del remito, encontrarás la sección <strong>"Movimientos"</strong>.</li>
                        <li>Haz clic en <strong>"Agregar Movimiento"</strong>, selecciona un artículo de tu catálogo y especifica la cantidad.</li>
                        <li>Puedes añadir tantos artículos como necesites.</li>
                    </ol>

                    <h5 class="mt-3">Paso 3: Finalizar el Remito para Afectar el Stock</h5>
                    <p>Este es el paso más importante. Hasta que no finalices un remito, tu inventario no se modifica.</p>
                    <ol>
                        <li>Dentro de la vista de un remito con movimientos, cambia su estado de "Pendiente" a <strong>"Finalizado"</strong>.</li>
                    </ol>
                    <div class="alert alert-warning">
                        <strong>¿Qué ocurre al finalizar?</strong><br>
                        El sistema <strong>actualiza automáticamente las cantidades de stock</strong>. Por ejemplo, en una transferencia, resta los artículos del Centro de Origen y los suma en el Centro de Destino. <strong>Esta acción no se puede deshacer.</strong>
                    </div>
                </div>
            </div>

            {{-- 5. Consultar el Stock --}}
            <div class="card mb-4" id="ver-stock">
                <div class="card-header"><h3>5. Consultar el Stock</h3></div>
                <div class="card-body">
                    <p>El resultado de toda la gestión de remitos se puede ver en la sección <strong>Stock</strong> del menú principal.</p>
                    <p>Esta pantalla te ofrece una vista consolidada de tu inventario. Podrás ver:</p>
                    <ul>
                        <li>La <strong>cantidad total de cada artículo</strong> que posees.</li>
                        <li>El detalle de <strong>cuántas unidades de ese artículo hay en cada Centro de Stock</strong>.</li>
                    </ul>
                    <p>Esta es tu herramienta principal para la toma de decisiones: saber qué tienes y dónde lo tienes.</p>
                </div>
            </div>

            {{-- 6. Administración --}}
            <div class="card mb-4" id="administracion">
                <div class="card-header"><h3>6. Administración de Datos Maestros</h3></div>
                <div class="card-body">
                    <p>En la sección <strong>Administración</strong> configuras los pilares de tu sistema. Es fundamental que esta información sea correcta y esté actualizada.</p>
                    <ul>
                        <li><strong>Artículos:</strong> Tu catálogo de productos. Define su nombre, código, unidad de medida, etc.</li>
                        <li><strong>Personas:</strong> Un directorio de tus clientes, proveedores o empleados. Útil para asociar a operaciones.</li>
                        <li><strong>Direcciones:</strong> Permite gestionar ubicaciones que pueden ser asignadas a personas o centros de stock.</li>
                        <li><strong>Centros de Stock:</strong> Tus almacenes, tiendas o depósitos. El elemento central para la ubicación del inventario.</li>
                        <li><strong>Importar CSV:</strong> La herramienta para cargar masivamente los datos anteriores.</li>
                    </ul>
                </div>
            </div>

            {{-- 7. Importación CSV --}}
            <div class="card mb-4" id="importacion-csv">
                <div class="card-header"><h3>7. Funcionalidad Avanzada: Importación Masiva (CSV)</h3></div>
                <div class="card-body">
                    <p>Esta herramienta te permite cargar cientos o miles de registros de una sola vez, ideal para la puesta en marcha inicial del sistema.</p>
                    <div class="alert alert-danger"><strong>¡MUY IMPORTANTE!</strong> El archivo debe estar guardado en formato CSV y usar el <strong>punto y coma (;)</strong> como separador de columnas. La primera fila (cabecera) se ignora automáticamente.</div>
                    
                    <h5 class="mt-4">Formato para Artículos</h5>
                    <pre><code>nombre;codigo;unidad;tipo
"Tornillo Phillips 3x15mm, cabeza plana";T001;U;Ferretería</code></pre>

                    <h5 class="mt-4">Formato para Personas</h5>
                    <pre><code>nombre;apellido;email;cuit;telefono
"Juan";"Pérez";"juan.perez@ejemplo.com";20123456789;1155443322</code></pre>

                    <h5 class="mt-4">Formato para Direcciones</h5>
                    <pre><code>pais;provincia;ciudad;localidad;calle;numero;departamento;casa;piso;cp
"Argentina";"Buenos Aires";"CABA";"Palermo";"Av. Santa Fe";1234;"A";"";"5";1425</code></pre>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
