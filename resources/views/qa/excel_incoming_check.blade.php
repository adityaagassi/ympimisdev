<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        thead>tr>th{
            border:1px solid black;
        }
        tbody>tr>td{
            border:1px solid black;
        }
        tfoot>tr>th{
            border:1px solid black;
        }
        
    </style>
</head>
<body>
    @if(isset($datas) && count($datas) > 0)
    <table>
        <thead>
            <tr>
                <th style="font-weight: bold;" rowspan="2">No.</th>
                <th style="font-weight: bold;" rowspan="2">Loc</th>
                <th style="font-weight: bold;" rowspan="2">Date</th>
                <th style="font-weight: bold;" rowspan="2">Inspector</th>
                <th style="font-weight: bold;" rowspan="2">Vendor</th>
                <th style="font-weight: bold;" rowspan="2">Invoice</th>
                <th style="font-weight: bold;" rowspan="2">Inspection Level</th>
                <th style="font-weight: bold;" rowspan="2">Lot Number</th>
                <th style="font-weight: bold;" rowspan="2">Material</th>
                <th style="font-weight: bold;" rowspan="2">Desc</th>
                <th style="font-weight: bold;" rowspan="2">Qty Rec</th>
                <th style="font-weight: bold;" rowspan="2">Qty Check</th>
                <th style="font-weight: bold;" rowspan="2">Defect</th>
                <th style="font-weight: bold;" colspan="3">Jumlah NG</th>
                <th style="font-weight: bold;" rowspan="2">Note</th>
                <th style="font-weight: bold;" rowspan="2">NG Ratio</th>
                <th style="font-weight: bold;" rowspan="2">Lot Status</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th style="font-weight: bold;">Repair</th>
                <th style="font-weight: bold;">Return</th>
                <th style="font-weight: bold;">Scrap</th>
            </tr>
        </thead>
        <tbody>
            <?php $index = 1; ?>
            @foreach($datas as $datas)
            <?php
            $jumlah = 0;
            if ($datas->ng_name != null) {
                $ng_name = explode("_", $datas->ng_name);
                $ng_qty = explode("_", $datas->ng_qty);
                $status_ng = explode("_", $datas->status_ng);
                if ($datas->ng_qty != null) {
                    $note_ng = explode("_", $datas->note_ng);
                }else{
                    $note_ng = "";
                }
                $jumlah = count($ng_name);
            }else{
                $jumlah = 1;
            }

            if ($datas->location == 'wi1') {
                $loc = 'Woodwind Instrument (WI) 1';
            }else if ($datas->location == 'wi2') {
                $loc = 'Woodwind Instrument (WI) 2';
            }else if($datas->location == 'ei'){
                $loc = 'Educational Instrument (EI)';
            }else if ($datas->location == 'cs'){
                $loc = 'Case';
            }else if($datas->location == 'ps'){
                $loc = 'Pipe Silver';
            } ?>
            <tr>
                <td rowspan="{{$jumlah}}">{{ $index++ }}</td>
                <td rowspan="{{$jumlah}}"> {{$loc}}</td>
                <td rowspan="{{$jumlah}}"> {{$datas->created}} </td>
                <td rowspan="{{$jumlah}}"> {{$datas->employee_id}} - {{$datas->name}} </td>
                <td rowspan="{{$jumlah}}"> {{$datas->vendor}} </td>
                <td rowspan="{{$jumlah}}"> {{$datas->invoice}} </td>
                <td rowspan="{{$jumlah}}"> {{$datas->inspection_level}} </td>
                <td rowspan="{{$jumlah}}"> {{$datas->lot_number}} </td>
                <td rowspan="{{$jumlah}}"> {{$datas->material_number}} </td>
                <td rowspan="{{$jumlah}}"> {{$datas->material_description}} </td>
                <td rowspan="{{$jumlah}}"> {{$datas->qty_rec}} </td>
                <td rowspan="{{$jumlah}}"> {{$datas->qty_check}} </td>
                <?php if ($datas->ng_name != null) { ?>
                    <td>{{$ng_name[0]}}</td>
                    <?php if ($status_ng[0] == 'Repair') {?>
                        <td>{{$ng_qty[0]}}</td>
                        <td></td>
                        <td></td>
                    <?php }else if ($status_ng[0] == 'Return') {?>
                        <td></td>
                        <td>{{$ng_qty[0]}}</td>
                        <td></td>
                    <?php }else if ($status_ng[0] == 'Scrap') {?>
                        <td></td>
                        <td></td>
                        <td>{{$ng_qty[0]}}</td>
                    <?php }
                    if (count($note_ng) > 0) {?>
                        <td>{{$note_ng[0]}}</td>
                    <?php }else{ ?>
                        <td></td>
                    <?php } ?>
                <?php }else{ ?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php } ?>
                <td style="vertical-align:middle" rowspan="{{$jumlah}}">{{round($datas->ng_ratio,2)}}</td>
                <td style="vertical-align:middle" rowspan="{{$jumlah}}">{{$datas->status_lot}}</td>
            </tr>
            <?php if ($datas->ng_name != null) { 
                for($i = 1 ;$i < $jumlah; $i++){ ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{$ng_name[1]}}</td>
                        <?php if ($status_ng[$i] == 'Repair') {?>
                            <td>{{$ng_qty[$i]}}</td>
                            <td></td>
                            <td></td>
                        <?php }else if ($status_ng[$i] == 'Return') {?>
                            <td></td>
                            <td>{{$ng_qty[$i]}}</td>
                            <td></td>
                        <?php }else if ($status_ng[$i] == 'Scrap') {?>
                            <td></td>
                            <td></td>
                            <td>{{$ng_qty[$i]}}</td>
                        <?php }
                        if ($datas->note_ng != null) {?>
                            <td>{{$note_ng[$i]}}</td>
                        <?php }else{ ?>
                            <td></td>
                        <?php } ?>
                    </tr>
                <?php } } ?>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>