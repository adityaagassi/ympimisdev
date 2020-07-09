<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($po_detail) && count($po_detail) > 0)
    <table>
        <thead>
            <tr>
                <th>Nomor</th>
                <th>PO No</th>
                <th>PR No </th>
                <th>PO Date</th>
                <th>Supplier Code</th>
                <th>Supplier</th>
                <th>Currency</th>
                <th>Cost Center</th>
                <th>Budget</th>
                <th>Item Number</th>
                <th>Item Name</th>
                <th>Qty</th>
                <th>UOM</th>
                <th>Goods Price</th>
                <th>Amount</th>
                <th>GL Number</th>
            </tr>
        </thead>
        <tbody>
            <?php $num = 1;$amount=0; ?>
            @foreach($po_detail as $po)

            <?php
                $amount = $po->goods_price * $po->qty;
            ?>

            <tr>
                <td>{{ $num++ }}</td>
                <td>{{ $po->no_po }}</td>
                <td>{{ $po->no_pr }}</td>
                <td>{{ $po->tgl_po }}</td>
                <td>{{ $po->supplier_code }}</td>
                <td>{{ $po->supplier_name }}</td>
                <td>{{ $po->currency }}</td>
                <td>{{ $po->cost_center }}</td>
                <td>{{ $po->budget_item }}</td>
                <td>{{ $po->no_item }}</td>
                <td>{{ $po->nama_item }}</td>
                <td>{{ $po->qty }}</td>
                <td>{{ $po->uom }}</td>
                <td>{{ $po->goods_price }}</td>
                <td><?= $amount ?> </td>
                <td>{{ $po->gl_number }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>