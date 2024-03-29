<html>

<head>
    <title>Remito N°{{ $refer->id}}</title>
    <style>
        html {
            font-size: 14px;
        }

        p{
            margin: 0;
        }

        @page {
            margin: .8cm;
        }

        footer {
            position: fixed;
            left: 0px;
            bottom: 0px;
            right: 0px;
            height: 120px;
        }

        footer .page:after {
            content: counter(page);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .container {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between!important;
        }

        .color{
            background-color: rgb(250, 250, 250);
        }

        thead {
            background-color: rgb(200, 200, 200);
        }

        tbody {
            font-size: 12px;
        }

        th {
            padding: 5px 10px;
        }

        td{
            padding: 5px 10px;
            letter-spacing: 1px;
            word-spacing: 4px;
        }

        .color tr {
            border-bottom: 1px solid rgb(200, 200, 200);
        }

        .text-right {
            text-align: right;
        }

	    .text-left {
            text-align: left;
        }

        .footer td {
            padding: 0px 15px 60px 15px
        }

        .col-auto {
            flex: 0 0 auto;
            width: auto;
        }

        .left-box {
            border-left: 1px solid #000;
        }
        .border-box {
            border: 1px solid #000;
            /* border-collapse: separate; */
            /* padding: 5px 20px 5px 20px; */
        }

        .border-box td {
            padding-left: 20px;
        }

        .m-width {
            width: 20%;
        }

        hr {
            position: relative;
            height: 1px;
            bottom: 10px;
            width: 80%;
            background-color: rgb(90, 90, 90);
        }

        .firma{
            text-align: center;
            font-size: 16px;
        }
        
        .bold{
            font-weight: bold;
        }
        
        .observation{
            padding: 5px;
            color: rgb(50, 50, 50);
        }
    </style>
</head>
<body>
    <footer>
        <table class="footer">
            <tr>
                <td width="30%">
                    <hr>
                    <p class="firma">Entrega</p>
                </td>
                <td width="30%">
                    <hr>
                    <p class="firma">Transporte</p>
                </td>
                <td width="30%">
                    <hr>
                    <p class="firma">Recibe</p>
                </td>
            </tr>
            <tr class="p-t">
                <td class="p-t">
                    <p class="izq">
                        Sistema de Stock
                    </p>
                </td>
                <td class="p-t"></td>
                <td class="p-t">
                    <p class="page text-right">
                        Página
                    </p>
                </td>
            </tr>
        </table>
    </footer>
    <main>
        <div class="container">
            <table class="border-box">
                <tr>
                    <td class="m-width bold">Desde:</td>
                    <td class="left-box m-width bold">Remito:   {{ $refer->id}}</td>
                </tr>
                <tr>
                    <td class="m-width">Nombre:   {{ $refer->nameOrigin}}</td>
                    <td class="left-box m-width">Fecha emitido:   {{ $refer->DateUpFormatted}}</td>
                </tr>
                <tr>
                    <td class="m-width">Direccion:   {{ $refer->Origin->Direction->FullDirection}}</td>
                    <td class="left-box m-width">Fecha finalizado:   {{ $refer->DateEndedFormatted}}</td>
                </tr>
                <tr>
                    <td class="m-width">Encargado:   {{ $refer->FullNameUser}}</td>
                    <td class="left-box m-width"></td>
                </tr>
            </table>
            <table class="border-box">
	            <tbody>
                    <tr>
                        <td class="bold">A: {{ $refer->NameDestiny}}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Nombre: {{ $refer->Destiny->FullNamePerson}}</td>
			            <td>Telefono: {{ $refer->Destiny->Person->telephone}}</td>
                    </tr>
		            <tr>
                        <td>Domicilio: {{ $refer->Destiny->Direction->FullDirection}}</td>
			            <td>Provincia: {{ $refer->Destiny->Direction->state}}</td>
                    </tr>
		            <tr>
                        <td>CP: {{ $refer->Destiny->Direction->cp}}</td>
			            <td>Localidad: {{ $refer->Destiny->Direction->city}}</td>
                    </tr>
		            <tr>
                        <td>Cuit: {{ $refer->Destiny->Person->cuit}}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <table class="color">
                <thead>
                    <tr>
                        <th class="text-left">Codigo</th>
                        <th class="text-left">Articulo</th>
                        <th class="text-left">Unidad</th>
                        <th class="text-right">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($movements as $movement)
                        <tr class="bg-light">
                            <td>{{ $movement->Article->code }}</td>
                            <td>{{ $movement->Article->name }}</td>
                            <td>{{ $movement->Article->UnitName }}</td>
                            <td class="text-right">{{ $movement->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="observation">Observaciones: {{ $refer->observation}}</p>
        </div>
    </main>
</body>

</html>