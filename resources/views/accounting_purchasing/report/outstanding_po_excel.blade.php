<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($po_detail) && count($po_detail) > 0)
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Budget No</th>
                <th>PO Number</th>
                <th>PR/Inv Number</th>
                <th>Detail Item</th>
                <th>Bagian</th>
                <th>Currency</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Amount</th>
                <th>Submission Date</th>
                <th>Periode</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $num = 1;
            ?>

            @foreach($po_detail as $po)

            <?php
                if($po->goods_price != "0" || $po->goods_price != 0){
                    $amount = $po->goods_price * $po->qty;                    
                }else{
                    $amount = $po->service_price * $po->qty; 
                }
            ?>

            <tr>
                <td>{{ $num++ }}</td>
                <td>{{ $po->budget_item }}</td>
                <td>{{ $po->no_po }}</td>
                <td>{{ $po->no_pr }}</td>
                <td>{{ $po->nama_item }}</td>
                @if($po->remark == "PR")
                    <td>{{ $po->department_pr }}</td>
                @else
                    <td>{{ $po->department_investment }}</td>
                @endif
                <td>{{ $po->currency }}</td>
                <td>{{ $po->qty }}</td>
                <td>
                    @if($po->goods_price != "0" || $po->goods_price != 0)
                        {{ $po->goods_price }}
                    @elseif($po->service_price != "0" || $po->service_price != 0)
                        {{ $po->service_price }}
                    @endif
                </td>
                <td><?= $amount ?></td>
                <td>{{ $po->tgl_po }}</td>
                <td>{{ $po->periode }}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>