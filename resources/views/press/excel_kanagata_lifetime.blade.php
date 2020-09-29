<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($kanagata_lifetime) && count($kanagata_lifetime) > 0)
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Material Number</th>
                <th>Material Name</th>
                <th>Material Description</th>
                <th>Product</th>
                <th>Kanagata Number</th>
                <th>Part</th>
                <th>Last Counter</th>
            </tr>
        </thead>
        <tbody>
            <?php $index = 1; ?>
            @foreach($kanagata_lifetime as $kanagata_lifetime)
            <tr>
                <td>{{ $index++ }}</td>
                <td>{{ $kanagata_lifetime->material_number }}</td>
                <td>{{ $kanagata_lifetime->material_name }}</td>
                <td>{{ $kanagata_lifetime->material_description }}</td>
                <td>{{ $kanagata_lifetime->product }}</td>
                <td>{{ $kanagata_lifetime->punch_die_number }}</td>
                <td>{{ $kanagata_lifetime->part }}</td>
                <td>{{ $kanagata_lifetime->last_data }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>