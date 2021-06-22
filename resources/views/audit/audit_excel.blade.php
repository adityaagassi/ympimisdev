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
    @if(isset($detail) && count($detail) > 0)
    <table>
        <thead>
            <tr style="background-color: #ddebf7; vertical-align: middle; ">
                <th colspan="6" style="text-align: left;">PT. Yamaha Musical Products Indonesia</th>
            </tr>

            <tr style="vertical-align: middle; ">
                <th colspan="6" style="text-align: center;">
                    @if($detail[0]->kategori == "Patrol Daily")
                        Patrol Daily Shift 1 & Shift
                    @elseif($detail[0]->kategori == "Patrol Covid")
                        Patrol Covid
                    @endif
                </th>
            </tr>

            <tr></tr>
            
            <tr style="vertical-align: middle;">
                <th colspan="2" style="text-align:left">Nama Petugas</th> 
                <th>: {{ $detail[0]->auditor_name }}</th>
            </tr>

            <tr style="vertical-align: middle;">
                <th colspan="2">Tanggal</th> 
                <th>: <?php echo date('d-m-Y', strtotime($detail[0]->tanggal)) ?></th>
            </tr>

            <tr></tr>
        </thead>
        <tbody>

            <tr>
                <th style="border: 1px solid black">No</th>
                <th style="border: 1px solid black">Lokasi</th>
                <th style="border: 1px solid black">PIC</th>
                <th style="border: 1px solid black">Poin Kategori</th>
                <th style="border: 1px solid black">Note</th>
                <th style="border: 1px solid black">Foto</th>
            </tr>

            <?php 
                $num = 1;
                $amount=0; 
            ?>

            @foreach($detail as $audit)

            <tr>
                <td style="width: 200px;vertical-align: middle;text-align: left">{{ $num++ }}</td>
                <td style="width: 200px;vertical-align: middle;text-align: left">{{ $audit->lokasi }}</td>
                <td style="width: 200px;vertical-align: middle;text-align: left">{{ $audit->auditee_name }}</td>
                <td style="width: 200px;vertical-align: middle;text-align: left">{{ $audit->point_judul }}</td>
                <td style="width: 200px;vertical-align: middle;text-align: left">{{ $audit->note }}</td>
                <td style="width: 200px;vertical-align: middle;text-align: left"><img src="files/patrol/{{ $audit->foto }}" width="200"></td>
            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>