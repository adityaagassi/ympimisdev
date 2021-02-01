<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($npwp_detail) && count($npwp_detail) > 0)
    <table>
        <thead>
            <tr>
                <th>Nomor</th>
                <th>Timestamp</th>
                <th>Nomor Induk Karyawan</th>
                <th>Nama lengkap</th>
                <th>Nomor Induk Kependudukan</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>Jenis Kelamin</th>
                <th>Alamat (Nama Jalan / Desa)</th>
                <th>RT / RW</th>
                <th>Kelurahan</th>
                <th>Kecamatan</th>
                <th>Kabupaten / Kota</th>
                <th>Status Pernikahan</th>
                <th>Tanggal Pernikahan</th>
                <th>Nama Istri</th>
                <th>Tanggal Lahir Istri</th>
                <th>Pekerjaan Istri</th>
                <th>Nama Anak 1</th>
                <th>Tempat Lahir Anak 1</th>
                <th>Tanggal Lahir Anak 1</th>
                <th>Status Anak 1</th>
                <th>Nama Anak 2</th>
                <th>Tempat Lahir Anak 2</th>
                <th>Tanggal Lahir Anak 2</th>
                <th>Status Anak 2</th>
                <th>Nama Anak 3</th>
                <th>Tempat Lahir Anak 3</th>
                <th>Tanggal Lahir Anak 3</th>
                <th>Status Anak 3</th>
                <th>Apakah sudah memiliki NPWP?</th>
                <th>Atas Nama Sendiri / ikut Suami?</th>
                <th>Nama Sesuai NPWP Sendiri / Suami</th>
                <th>Nomor NPWP</th>
                <th>Alamat Sesuai NPWP</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $num = 1;
            ?>

            @foreach($npwp_detail as $npwp)

            <tr>
                <td>{{ $num++ }}</td>
                <td>{{ $npwp->updated_at }}</td>
                <td>{{ $npwp->employee_id }}</td>
                <td>{{ $npwp->nama }}</td>
                <td>{{ $npwp->nik }}</td>
                <td>{{ $npwp->tempat_lahir }}</td>
                <td>{{ $npwp->tanggal_lahir }}</td>
                <td>{{ $npwp->jenis_kelamin }}</td>
                <td>{{ $npwp->jalan }}</td>
                <td>{{ $npwp->rtrw }}</td>
                <td>{{ $npwp->kelurahan }}</td>
                <td>{{ $npwp->kecamatan }}</td>
                <td>{{ $npwp->kota }}</td>
                <td>{{ $npwp->status_perkawinan }}</td>

                <?php 
                    $istri = explode("_", $npwp->istri);
                ?>
                <td>{{ $istri[0] }}</td>
                <td>{{ $istri[1] }}</td>
                <td>{{ $istri[2] }}</td>
                <td>{{ $istri[3] }}</td>

                <?php 
                    $anak1 = explode("_", $npwp->anak1);
                ?>
                <td>{{ $anak1[0] }}</td>
                <td>{{ $anak1[2] }}</td>
                <td>{{ $anak1[3] }}</td>
                <td>{{ $anak1[4] }}</td>

                <?php 
                    $anak2 = explode("_", $npwp->anak2);
                ?>
                <td>{{ $anak2[0] }}</td>
                <td>{{ $anak2[2] }}</td>
                <td>{{ $anak2[3] }}</td>
                <td>{{ $anak2[4] }}</td>

                <?php 
                    $anak3 = explode("_", $npwp->anak3);
                ?>
                <td>{{ $anak3[0] }}</td>
                <td>{{ $anak3[2] }}</td>
                <td>{{ $anak3[3] }}</td>
                <td>{{ $anak3[4] }}</td>

                <td>{{ $npwp->npwp_kepemilikan }}</td>
                <td>{{ $npwp->npwp_status }}</td>
                <td>{{ $npwp->npwp_nama }}</td>
                <td>{{ $npwp->npwp_nomor }}</td>
                <td>{{ $npwp->npwp_alamat }}</td>

            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>