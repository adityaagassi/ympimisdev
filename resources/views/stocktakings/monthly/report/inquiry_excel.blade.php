<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($inquiries) && count($inquiries) > 0)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Loc</th>
                <th>Kode Slip</th>
                <th>Store</th>
                <th>GMC</th>
                <th>Description</th>
                <th>Jenis Slip</th>
                <th>UOM</th>
                <th>Qty</th>
                <th>Last Update</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inquiries as $tr)
            <tr>
                <td>{{ $tr->id }}</td>
                <td>{{ $tr->location }}</td>
                <td>{{ $tr->group }}</td>
                <td>{{ $tr->store }}</td>
                <td>{{ $tr->material_number }}</td>
                <td>{{ $tr->material_description }}</td>
                <td>{{ $tr->category }}</td>
                <td>{{ $tr->bun }}</td>
                <td>{{ $tr->final_count }}</td>
                <td>{{ $tr->updated_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>