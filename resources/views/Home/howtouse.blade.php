@extends('layouts.app')

@section('content')
    <div class="container">
        <p>En desarrollo...</p>
        <ul>
            <li><a href="#introduccion">Introducción</a></li>
            <ul>
                <li><a href="#quees">¿Que es?</a></li>
                <li><a href="#comousarla">Como usarla</a></li>
            </ul>
        </ul>
        <section id="introduccion">
            <h2>Introducción</h2>
            <article id="quees">
                <h3>¿Que es?</h3>
                <p> Esta demo en desarrollo es una aplicacion pensado para empresas con multiples almacenes o centros de stock, donde se lleve un control diario de almacenamiento de materiales o articulos</p>
                <p> Cuenta con la posibilidad de tener varios Centros de stock en diferentes localidades, llevar un control de movimientos por usuario y fecha y una variedad de estados de remitos, donde se producen los principales cambios en el Stock</p>
            </article>
            <article id="comousarla">
                <h3>Como usarla</h3>
                <p> La aplicacion se basa en la creacion y finalizacion de Remitos, para empezar a ver movimientos en su Stock siga estos pasos:</p>
                <ul>
                    <li>Dirijaase a <strong>Remitos</strong> y en la parte inferior dale click a 'Crear Remito'</li>
                    <li>Seleccione su Centro de Stock de origen y su destino (segun el centro de stock que elija puede no verse reflejado ningun moviento o no aparecer en el stock, mas adelante se explican sus diferencias</li>
                    <li></li>
                </ul>
            </article>
            
        </section>
    </div>
@endsection
