<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($error) && count($error) > 0)
    @php
    $id = 0;
    @endphp
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Loc</th>
                <th>Store</th>
                <th>SubStore</th>
                <th>GMC</th>
                <th>Description</th>
                <th>Jenis Slip</th>
                <th>Error Message</th>
            </tr>
        </thead>
        <tbody>
            @foreach($error as $tr)
            <tr>
                <td>{{ ++$id }}</td>
                <td>{{ $tr->location }}</td>
                <td>{{ $tr->store }}</td>
                <td>{{ $tr->sub_store }}</td>
                <td>{{ $tr->material_number }}</td>
                <td>{{ $tr->material_description }}</td>
                <td>{{ $tr->category }}</td>
                <td>{{ $tr->error_message }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>