<html>

<head>
    <style>
        html {
            font-size: 14px;
        }

        p{
            margin: 0;
        }

        @page {
            margin: 130px 50px 100px;
        }

        header {
            position: fixed;
            left: 0px;
            top: -100px;
            right: 0px;
            background-color: rgb(250, 250, 250);
            text-align: center;
            border: 1px solid black;
            margin: 0;
        }

        footer {
            position: fixed;
            left: 0px;
            bottom: -100px;
            right: 0px;
            height: 100px;
        }

        footer .page:after {
            content: counter(page);
        }

        table {
            width: 100%;
            background-color: rgb(250, 250, 250);
            border-collapse: collapse;
        }

        thead {
            background-color: rgb(200, 200, 200);
        }

        tbody {
            font-size: 12px;
        }

        td,
        th {
            padding: 5px;
        }

        tr {
            border-bottom: 1px solid rgb(200, 200, 200);
        }

        .text-right {
            text-align: right;
        }

        .footer td {
            padding: 0 15px;
        }

        .filter th{
            text-align: left;
        }

        .filter{
            width: 100%;
            background-color: transparent;
            margin-bottom: 10px;
            border-radius: 15px;
        }
        .filter thead tr th{
            border-top-right-radius: 15px;
            border-top-left-radius: 15PX; 
        }
        

        .col-auto {
            flex: 0 0 auto;
            width: auto;
        }
    </style>

<body>
    <header>
        <h1>Sistema de Stock</h1>
    </header>
    <footer>
        <hr>
        <table class="footer">
            <tr>
                <td>
                    <p class="izq">
                        Sistema de Stock
                    </p>
                </td>
                <td>
                    <p class="page text-right">
                        Página
                    </p>
                </td>
            </tr>
        </table>
    </footer>
    <main>
        <div class="container">
            <table class="table table-striped table-hover table-md">
                <thead>
                    <tr>
                        <th class="table-light">Codigo</th>
                        <th class="table-light">Articulo</th>
                        <th class="taable-light">Unidad</th>
                        <th class="table-light">Tipo</th>
                        <th class="text-right">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($movements as $movement)
                        <tr>
                            <td>{{ $movement->Article->code }}</td>
                            <td>{{ $movement->Article->name }}</td>
                            <td>{{ $movement->Article->UnitName }}</td>
                            <td>{{ $movement->Article->type }}</td>
                            <td class="text-right">{{ $movement->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </main>


</body>

</html>
