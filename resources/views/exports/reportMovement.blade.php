<table>
    <thead>
        <tr>
            <th>Numero de remiro</th>
            <th>Origen</th>
            <th>Destino</th>
            <th>Fecha emitido</th>
            <th>Fecha finalizado</th>
            <th>Usuario</th>
            <th>Estado</th>
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
                <td>{{ $movement->id_refer }}</td>
                <td>{{ $movement->Refer->NameOrigin }}</td>
                <td>{{ $movement->Refer->NameDestiny }}</td>
                <td>{{ $movement->Refer->DateUpFormatted }}</td>
                <td>{{ $movement->Refer->DateEndedFormatted }}</td>
                <td>{{ $movement->Refer->FullNameUser }}</td>
                <td>{{ $movement->Refer->StatusName }}</td>
                <td>{{ $movement->Article->code }}</td>
                <td>{{ $movement->Article->name }}</td>
                <td>{{ $movement->Article->UnitName }}</td>
                <td>{{ $movement->Article->type }}</td>
                <td>{{ $movement->quantity }}</td>
            </tr>
        @endforeach
    </tbody>

</table>