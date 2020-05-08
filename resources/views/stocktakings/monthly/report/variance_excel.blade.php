<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($variances) && count($variances) > 0)
    <table>
        <thead>
            <tr>
                <th>Group</th>
                <th>Valcl</th>
                <th>GMC</th>
                <th>Description</th>
                <th>Loc</th>
                <th>Loc Name</th>
                <th>Uom</th>
                <th>Std</th>
                <th>PI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($variances as $tr)
            <tr>
                <td>{{ $tr->group }}</td>
                <td>{{ $tr->valcl }}</td>
                <td>{{ $tr->material_number }}</td>
                <td>{{ $tr->material_description }}</td>
                <td>{{ $tr->location }}</td>
                <td>{{ $tr->location_name }}</td>
                <td>{{ $tr->uom }}</td>
                <td>{{ $tr->std }}</td>
                <td>{{ $tr->pi }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>