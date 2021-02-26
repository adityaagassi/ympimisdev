<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($maintenance_job_orders) && count($maintenance_job_orders) > 0)
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nomor SPK</th>
                <th>Tanggal SPK</th>
                <th>Prioritas</th>
                <th>Bagian</th>
                <th>Jenis Pekerjaan</th>
                <th>Kategori</th>
                <th>Uraian SPK</th>
                <th>Target Selesai</th>
                <th>Kondisi Mesin</th>
                <th>Nama Mesin</th>
                <th>Potensi Bahaya</th>
                <th>Catatan Keamanan</th>
                <th>PIC</th>
                <th>Actual Start</th>
                <th>Actual Selesai</th>
                <th>Penyebab</th>
                <th>Penanganan</th>
                <th>Pencegahan</th>
                <th>Foto</th>
            </tr>
        </thead>
        <tbody>
            <?php $index = 1; ?>
            @foreach($maintenance_job_orders as $mjo)
            <tr>
                <td>{{ $index++ }}</td>
                <td>{{ $mjo->order_no }}</td>
                <td>{{ $mjo->date }}</td>
                <td>{{ $mjo->priority }}</td>
                <td>{{ $mjo->section }}</td>
                <td>{{ $mjo->type }}</td>
                <td>{{ $mjo->category }}</td>
                <td>{{ $mjo->description }}</td>
                <td>{{ $mjo->target_date }}</td>
                <td>{{ $mjo->machine_condition }}</td>
                <td>{{ $mjo->machine_desc }} - {{ $mjo->location }}</td>
                <td>{{ $mjo->danger }}</td>
                <td>{{ $mjo->safety_note }}</td>
                <td>{{ $mjo->name_op }}</td>
                <td>{{ $mjo->start_actual }}</td>
                <td>{{ $mjo->finish_actual }}</td>
                <td>{{ $mjo->cause }}</td>
                <td>{{ $mjo->handling }}</td>
                <td>{{ $mjo->prevention }}</td>

                <?php 
                echo "<td>";

                $poto = explode(', ', $mjo->photo);

                if ($mjo->photo) {
                    foreach ($poto as $ph) {
                        echo "<img src='".public_path("maintenance/spk_report").'/'.$ph."' width='200px'>";
                    }
                } else {
                    echo "tidak ada foto";
                }

                echo "</td>";

                ?>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>