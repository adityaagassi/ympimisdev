<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($location) && count($location) > 0)
    <table>
        <thead>
            <tr>
                <th>Nomor</th>
                <th>Tanggal</th>
                <th>ID Karyawan</th>
                <th>Nama Karyawan</th>
                <th>Departemen</th>
                <th>Kota Absensi</th>
                <th>Kota Domisili</th>
            </tr>
        </thead>
        <tbody>
            <?php $index = 1; ?>
            @foreach($location as $loc)
            <tr>
                <td>{{ $index++ }}</td>
                <td>{{ $loc->answer_date }}</td>
                <td>{{ $loc->employee_id }}</td>
                <td>{{ $loc->name }}</td>
                <td>{{ $loc->department }}</td>
                <td>{{ $loc->city }}</td>
                <td>{{ $loc->kota }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>