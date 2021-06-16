<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($download) && count($download) > 0)
    <table>
        <thead>
            <tr>
                <th>id</th>
                <th>store</th>
                <th>category</th>
                <th>material_number</th>
                <th>material_description</th>
                <th>ideal</th>
                <th>actual</th>
                <th>location</th>
                <th>remark</th>
                <th>print</th>
                <th>status</th>
                <th>quantity</th>
                <th>created_by</th>
                <th>created_at</th>
                <th>deleted_at</th>
                <th>updated_at</th>
            </tr>
        </thead>
        <tbody>
            <!-- <?php 
                $num = 1;
            ?> -->

            @foreach($download as $dw)

            <tr>
                <td></td>
                <td>{{ $dw->store }}</td>
                <td>{{ $dw->category }}</td>
                <td>{{ $dw->material_number }}</td>
                <td>{{ $dw->material_description }}</td>
                <td>{{ $dw->ideal }}</td>
                <td></td>
                <td>{{ $dw->location }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>