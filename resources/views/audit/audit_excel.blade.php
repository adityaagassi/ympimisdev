<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($detail) && count($detail) > 0)
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Auditor</th>
                <th>Lokasi</th>
                <th>Auditee</th>
                <th>Poin Kategori</th>
                <th>Note</th>
                <th>Foto</th>
                <th>Status Penanganan</th>
                <th>Tanggal Penanganan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $num = 1;
                $amount=0; 
            ?>

            @foreach($detail as $audit)

            <tr>
                <td>{{ $num++ }}</td>
                <td>{{ $audit->tanggal }}</td>
                <td>{{ $audit->kategori }}</td>
                <td>{{ $audit->auditor_name }}</td>
                <td>{{ $audit->lokasi }}</td>
                <td>{{ $audit->auditee_name }}</td>
                <td>{{ $audit->point_judul }}</td>
                <td>{{ $audit->note }}</td>
                <td><img src="files/patrol/{{ $audit->foto }}" width="50"></td>

                @if($audit->penanganan != "")
                    <td>Close</td>
                @else
                    <td></td>
                @endif
                <td>{{ $audit->tanggal_penanganan }}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>