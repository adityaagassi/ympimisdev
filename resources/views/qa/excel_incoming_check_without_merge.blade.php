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
                <th style="font-weight: bold;">No.</th>
                <th style="font-weight: bold;">ID</th>
                <th style="font-weight: bold;">Loc</th>
                <th style="font-weight: bold;">Date</th>
                <th style="font-weight: bold;">Inspector</th>
                <th style="font-weight: bold;">Vendor</th>
                <th style="font-weight: bold;">Invoice</th>
                <th style="font-weight: bold;">Inspection Level</th>
                <th style="font-weight: bold;">Lot Number</th>
                <th style="font-weight: bold;">Material</th>
                <th style="font-weight: bold;">Desc</th>
                <th style="font-weight: bold;">Qty Rec</th>
                <th style="font-weight: bold;">Qty Check</th>
                <th style="font-weight: bold;">Defect</th>
                <th style="font-weight: bold;">Repair</th>
                <th style="font-weight: bold;">Return</th>
                <th style="font-weight: bold;">Scrap</th>
                <th style="font-weight: bold;">Note</th>
                <th style="font-weight: bold;">NG Ratio</th>
                <th style="font-weight: bold;">Lot Status</th>
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
            }else if($datas->location == 'sx'){
                $loc = 'Saxophone Body';
            }else if ($datas->location == 'cs'){
                $loc = 'Case';
            }else if($datas->location == 'ps'){
                $loc = 'Pipe Silver';
            } ?>
            <?php for($i = 0; $i < $jumlah ; $i++){ ?>
                <tr>
                    <td>{{ $index++ }}</td>
                    <td> {{$datas->id_log}}</td>
                    <td> {{$loc}}</td>
                    <td> {{$datas->created}} </td>
                    <td> {{$datas->employee_id}} - {{$datas->name}} </td>
                    <td> {{$datas->vendor}} </td>
                    <td> {{$datas->invoice}} </td>
                    <td> {{$datas->inspection_level}} </td>
                    <td> {{$datas->lot_number}} </td>
                    <td> {{$datas->material_number}} </td>
                    <td> {{$datas->material_description}} </td>
                    <td> {{$datas->qty_rec}} </td>
                    <td> {{$datas->qty_check}} </td>
                    <?php if ($datas->ng_name != null && count($ng_name) > 0) { ?>
                        <td>{{$ng_name[$i]}}</td>
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
                        if (count($note_ng) > 0) {?>
                            <td>{{$note_ng[$i]}}</td>
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
                    <td style="vertical-align:middle">{{round($datas->ng_ratio,2)}}</td>
                    <td style="vertical-align:middle">{{$datas->status_lot}}</td>
                </tr>
            <?php } ?>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>