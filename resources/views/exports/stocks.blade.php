<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Centro de Stock</th>
            <th>Codigo</th>
            <th>Articulo</th>
            <th>Unidad</th>
            <th>Tipo</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stocks as $stock)
            <tr>
                <td>{{ $stock->id }}</td>
                <td>{{ $stock->StockCenter->name }}</td>
                <td>{{ $stock->Article->code }}</td>
                <td>{{ $stock->Article->name }}</td>
                <td>{{ $stock->Article->UnitName }}</td>
                <td>{{ $stock->Article->type }}</td>
                <td>{{ $stock->quantity }}</td>
            </tr>
        @endforeach
    </tbody>

</table>