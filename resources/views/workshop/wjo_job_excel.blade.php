<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($workshop) && count($workshop) > 0)
    <table>
        <thead>
            <tr><th colspan="5" style="text-align: center;">Tunjangan Proses Kerja {{$mon}}</th></tr>
            <tr>
                <th>#</th>
                <th>ID Operator</th>
                <th>Nama Operator</th>
                <th>Jumlah Hari</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php $index = 1; ?>
            @foreach($workshop as $wjo)
            <tr>
                <td>{{ $index++ }}</td>
                <td>{{ $wjo->employee_id }}</td>
                <td>{{ $wjo->name }}</td>
                <td>{{ $wjo->hari }}</td>
                <td>{{ $wjo->ket }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>