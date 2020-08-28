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
                <th>PR No</th>
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
                
                <th style="background-color: #ffeb3b;color: #f44336">Item</th>
                <th style="background-color: #ffeb3b;color: #f44336">A</th>
                <th style="background-color: #ffeb3b;color: #f44336">I</th>
                <th style="background-color: #ffeb3b;color: #f44336">Material</th>
                <th style="background-color: #ffeb3b;color: #f44336">Short Text</th>
                <th style="background-color: #ffeb3b;color: #f44336">PO Qty</th>
                <th style="background-color: #ffeb3b;color: #f44336">Oun</th>
                <th style="background-color: #ffeb3b;color: #f44336">C</th>
                <th style="background-color: #ffeb3b;color: #f44336">Delivery Date</th>
                <th style="background-color: #ffeb3b;color: #f44336">Net Payment</th>
                <th style="background-color: #ffeb3b;color: #f44336">Cur</th>
                <th style="background-color: #ffeb3b;color: #f44336">Per</th>
                <th style="background-color: #ffeb3b;color: #f44336">OPU</th>
                <th style="background-color: #ffeb3b;color: #f44336">Mtrl group</th>
                <th style="background-color: #ffeb3b;color: #f44336">Plnt</th>
                <th style="background-color: #ffeb3b;color: #f44336">Stor Loc</th>
                <th style="background-color: #ffeb3b;color: #f44336">Batch</th>
                <th style="background-color: #ffeb3b;color: #f44336">Reqmnt No</th>
                <th style="background-color: #ffeb3b;color: #f44336">Requisitioner</th>
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
                <td>
                    @if($po->goods_price != null)
                        {{ $po->goods_price }}
                    @elseif ($po->service_price != null)
                        {{ $po->service_price }}
                    @endif
                </td>
                <td><?= $amount ?> </td>
                <td>{{ $po->gl_number }}</td>
                <td></td>
                <td>
                    @if($po->gl_number == "15100000" || $po->gl_number == "15300000" || $po->gl_number == "15400000" || $po->gl_number == "15500000" || $po->gl_number == "15600000" || $po->gl_number == "15700000")
                        A
                    @else
                        K
                    @endif
                </td>
                <td></td>
                <td></td>
                <td>{{ $po->nama_item }}</td>
                <td>{{ $po->qty }}</td>
                <td>{{ $po->uom }}</td>
                <td></td>
                <td><?php echo date('d.m.Y', strtotime($po->delivery_date)) ?></td>
                <td>
                    @if($po->goods_price != null)
                        {{ $po->goods_price }}
                    @elseif ($po->service_price != null)
                        {{ $po->service_price }}
                    @endif
                </td>
                <td>{{$po->currency}}</td>
                <td>
                    @if($po->currency == "JPY" && $po->supplier_code == "G504")
                    1000
                    @else
                    1
                    @endif
                </td>
                <td></td>
                <td>9990</td>
                <td>8190</td>
                <td></td>
                <td></td>
                <td>
                    <?= substr($po->no_po,1,8); ?><?= substr($po->no_po,10,2); ?>
                </td>
                <td>{{$po->no_po}}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>