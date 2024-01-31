<table>
    <thead>
        <tr>
            <th>Codigo</th>
            <th>Articulo</th>
            <th>Unidad</th>
            <th>Tipo</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($movements as $movement)
            <tr>
                <td>{{ $movement->Article->code }}</td>
                <td>{{ $movement->Article->name }}</td>
                <td>{{ $movement->Article->UnitName }}</td>
                <td>{{ $movement->Article->type }}</td>
                <td>{{ $movement->total_quantity }}</td>
            </tr>
        @endforeach
    </tbody>

</table>