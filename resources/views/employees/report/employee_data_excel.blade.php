<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        table{
            border: 2px solid black;
            vertical-align: middle;
        }
        table > thead > tr > th{
            border: 2px solid black;
        }
        table > tbody > tr > td{
            border: 1px solid rgb(211,211,211);
        }
        table > tfoot > tr > th{
            border: 1px solid rgb(211,211,211);
        }
    </style>
</head>
<body>
    @if(isset($emp) && count($emp) > 0)
    <table>
        <thead>
            <tr style="background-color: #ddebf7; vertical-align: middle; text-align: center;">
                <th style="vertical-align: middle; text-align: center;" rowspan="2">NIK</th>
                <th style="vertical-align: middle; text-align: center;" rowspan="2">Nama</th>
                <th style="vertical-align: middle; text-align: center;" rowspan="2">No. KTP</th>
                <th style="vertical-align: middle; text-align: center;" rowspan="2">NPWP</th>
                <th style="vertical-align: middle; text-align: center;" rowspan="2">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;" rowspan="2">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;" rowspan="2">Agama</th>
                <th style="vertical-align: middle; text-align: center;" rowspan="2">Status Perkawinan</th>
                <th style="vertical-align: middle; text-align: center;" rowspan="2">Alamat Asal</th>
                <th style="vertical-align: middle; text-align: center;" rowspan="2">Alamat Domisili</th>
                <th style="vertical-align: middle; text-align: center;" rowspan="2">Telepon Rumah</th>
                <th style="vertical-align: middle; text-align: center;" rowspan="2">Handphone</th>
                <th style="vertical-align: middle; text-align: center;" rowspan="2">Email</th>
                <th style="vertical-align: middle; text-align: center;" rowspan="2">No. BPJSKES</th>
                <th style="vertical-align: middle; text-align: center;" rowspan="2">FASKES</th>
                <th style="vertical-align: middle; text-align: center;" rowspan="2">No. BPJSKTK</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Ayah</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Ibu</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Saudara 1</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Saudara 2</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Saudara 3</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Saudara 4</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Saudara 5</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Saudara 6</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Saudara 7</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Saudara 8</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Saudara 9</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Saudara 10</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Saudara 11</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Saudara 12</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Suami/Istri</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Anak 1</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Anak 2</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Anak 3</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Anak 4</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Anak 5</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Anak 6</th>
                <th style="vertical-align: middle; text-align: center;" colspan="5">Anak 7</th>
                <th style="vertical-align: middle; text-align: center;" colspan="4">SD</th>
                <th style="vertical-align: middle; text-align: center;" colspan="4">SMP</th>
                <th style="vertical-align: middle; text-align: center;" colspan="4">SMA</th>
                <th style="vertical-align: middle; text-align: center;" colspan="4">S1</th>
                <th style="vertical-align: middle; text-align: center;" colspan="4">S2</th>
                <th style="vertical-align: middle; text-align: center;" colspan="4">S3</th>
                <th style="vertical-align: middle; text-align: center;" colspan="4">Kontak Emergency 1</th>
                <th style="vertical-align: middle; text-align: center;" colspan="4">Kontak Emergency 2</th>
                <th style="vertical-align: middle; text-align: center;" colspan="4">Kontak Emergency 3</th>
            </tr>
            <tr style="background-color: #ddebf7;">
                <th style="vertical-align: middle; text-align: center;">NIK</th>
                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">No. KTP</th>
                <th style="vertical-align: middle; text-align: center;">NPWP</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Agama</th>
                <th style="vertical-align: middle; text-align: center;">Status Perkawinan</th>
                <th style="vertical-align: middle; text-align: center;">Alamat Asal</th>
                <th style="vertical-align: middle; text-align: center;">Alamat Domisili</th>
                <th style="vertical-align: middle; text-align: center;">Telepon Rumah</th>
                <th style="vertical-align: middle; text-align: center;">Handphone</th>
                <th style="vertical-align: middle; text-align: center;">Email</th>
                <th style="vertical-align: middle; text-align: center;">No. BPJSKES</th>
                <th style="vertical-align: middle; text-align: center;">FASKES</th>
                <th style="vertical-align: middle; text-align: center;">No. BPJSKTK</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">L/P</th>
                <th style="vertical-align: middle; text-align: center;">Tempat Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Tanggal Lahir</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>

                <th style="vertical-align: middle; text-align: center;">Nama Lembaga Pendidikan</th>
                <th style="vertical-align: middle; text-align: center;">Jurusan</th>
                <th style="vertical-align: middle; text-align: center;">Tahun Masuk</th>
                <th style="vertical-align: middle; text-align: center;">Tahun Lulus</th>

                <th style="vertical-align: middle; text-align: center;">Nama Lembaga Pendidikan</th>
                <th style="vertical-align: middle; text-align: center;">Jurusan</th>
                <th style="vertical-align: middle; text-align: center;">Tahun Masuk</th>
                <th style="vertical-align: middle; text-align: center;">Tahun Lulus</th>

                <th style="vertical-align: middle; text-align: center;">Nama Lembaga Pendidikan</th>
                <th style="vertical-align: middle; text-align: center;">Jurusan</th>
                <th style="vertical-align: middle; text-align: center;">Tahun Masuk</th>
                <th style="vertical-align: middle; text-align: center;">Tahun Lulus</th>

                <th style="vertical-align: middle; text-align: center;">Nama Lembaga Pendidikan</th>
                <th style="vertical-align: middle; text-align: center;">Jurusan</th>
                <th style="vertical-align: middle; text-align: center;">Tahun Masuk</th>
                <th style="vertical-align: middle; text-align: center;">Tahun Lulus</th>

                <th style="vertical-align: middle; text-align: center;">Nama Lembaga Pendidikan</th>
                <th style="vertical-align: middle; text-align: center;">Jurusan</th>
                <th style="vertical-align: middle; text-align: center;">Tahun Masuk</th>
                <th style="vertical-align: middle; text-align: center;">Tahun Lulus</th>

                <th style="vertical-align: middle; text-align: center;">Nama Lembaga Pendidikan</th>
                <th style="vertical-align: middle; text-align: center;">Jurusan</th>
                <th style="vertical-align: middle; text-align: center;">Tahun Masuk</th>
                <th style="vertical-align: middle; text-align: center;">Tahun Lulus</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">No. Telp</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>
                <th style="vertical-align: middle; text-align: center;">Hubungan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">No. Telp</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>
                <th style="vertical-align: middle; text-align: center;">Hubungan</th>

                <th style="vertical-align: middle; text-align: center;">Nama</th>
                <th style="vertical-align: middle; text-align: center;">No. Telp</th>
                <th style="vertical-align: middle; text-align: center;">Pekerjaan</th>
                <th style="vertical-align: middle; text-align: center;">Hubungan</th>
            </tr>
        </thead>
        <tbody>
            @php $last = ''; @endphp
            @foreach($emp as $tr)
            <tr>
                <td>{{ $tr->employee_id }}</td>
                <td>{{ $tr->name }}</td>
                <td>{{ $tr->nik }}</td>
                <td>{{ $tr->npwp }}</td>
                <td>{{ $tr->birth_place }}</td>
                <td>{{ $tr->birth_date }}</td>
                <td>{{ $tr->religion }}</td>
                <td>{{ $tr->mariage_status }}</td>
                <td>{{ $tr->address }}</td>
                <td>{{ $tr->current_address }}</td>
                <td>{{ $tr->telephone }}</td>
                <td>{{ $tr->handphone }}</td>
                <td>{{ $tr->email }}</td>
                <td>{{ $tr->bpjskes }}</td>
                <td>{{ $tr->faskes }}</td>
                <td>{{ $tr->bpjstk }}</td>

                @php $data = explode('_', $tr->f_ayah) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->f_ibu) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->f_saudara1) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->f_saudara2) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->f_saudara3) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->f_saudara4) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->f_saudara5) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->f_saudara6) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->f_saudara7) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->f_saudara8) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->f_saudara9) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->f_saudara10) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->f_saudara11) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->f_saudara12) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->m_pasangan) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->m_anak1) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->m_anak2) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->m_anak3) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->m_anak4) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->m_anak5) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->m_anak6) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->m_anak7) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
                <td>{{ $data[4] }}</td>

                @php $data = explode('_', $tr->sd) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>

                @php $data = explode('_', $tr->smp) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>

                @php $data = explode('_', $tr->sma) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>

                @php $data = explode('_', $tr->s1) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>

                @php $data = explode('_', $tr->s2) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>

                @php $data = explode('_', $tr->s3) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>

                @php $data = explode('_', $tr->emergency1) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>

                @php $data = explode('_', $tr->emergency2) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>

                @php $data = explode('_', $tr->emergency3) @endphp
                <td>{{ $data[0] }}</td>
                <td>{{ $data[1] }}</td>
                <td>{{ $data[2] }}</td>
                <td>{{ $data[3] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>