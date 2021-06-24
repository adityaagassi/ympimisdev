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
            height: 30;
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
                        <br>
                        デイリーパトロール　1直・2直
                    @elseif($detail[0]->kategori == "Patrol Covid")
                        Patrol Covid
                        <br>
                        コロナ対策パトロール
                    @else
                        {{$detail[0]->kategori}}
                    @endif
                </th>
            </tr>

            <tr></tr>
            
            <tr style="vertical-align: middle;">
                <th colspan="2" style="text-align:left">Nama Petugas<br>パトロール担当者</th> 
                <th>: {{ $detail[0]->auditor_name }}</th>
            </tr>

            <tr style="vertical-align: middle;">
                <th colspan="2">Tanggal<br>日付</th> 
                <th>: <?php echo date('d-m-Y', strtotime($detail[0]->tanggal)) ?></th>
            </tr>

            <tr></tr>
        </thead>
        <tbody>

            <tr>
                <th style="border: 1px solid black;">No</th>
                <th style="border: 1px solid black;">Lokasi<br>場所</th>
                <th style="border: 1px solid black;">PIC<br>場所</th>
                <th style="border: 1px solid black;">Poin Kategori<br>指摘カテゴリー</th>
                <th style="border: 1px solid black;">Note<br>備考</th>
                <th style="border: 1px solid black;">Foto<br>写真</th>
            </tr>

            <?php 
                $num = 1;
                $amount=0; 
            ?>

            @foreach($detail as $audit)

            <tr>
                <td style="vertical-align: middle;text-align: left;width: 10">{{ $num++ }}</td>
                <td style="vertical-align: middle;text-align: left;width: 20">{{ $audit->lokasi }}</td>
                <td style="vertical-align: middle;text-align: left;width: 20">{{ $audit->auditee_name }}</td>
                <td style="vertical-align: middle;text-align: left;width: 20">{{ $audit->point_judul }}</td>
                <td style="vertical-align: middle;text-align: left;width: 20"><b>{{ $audit->note }}</b></td>
                <td style="vertical-align: middle;text-align: right;align-items: right;align-content: right">&nbsp;&nbsp;<img src="files/patrol/{{ $audit->foto }}" width="190"></td>
            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>