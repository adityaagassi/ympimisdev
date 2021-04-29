<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($tanda_terima) && count($tanda_terima) > 0)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier Name</th>
                <th>Invoice No</th>
                <th>Currency</th>
                <th>Amount</th>
                <th>PPN</th>
                <th>PPH</th>
                <th>Hidden</th>
                <th>Net Payment</th>
                <th>Payment Term Name</th>
                <th>Due Date</th>
                <th>Surat Jalan</th>
                <th>BAP</th>
                <th>Faktur Pajak</th>
                <th>PO Number</th>
                <th>Date</th>
                <th>DO Date</th>
                <th>Distribution Date</th>
            </tr>
        </thead>
        <tbody>
            <!-- <?php 
                $num = 1;
            ?> -->

            @foreach($tanda_terima as $tt)

            <tr>
                <td>{{ $tt->id }}</td>
                <td>{{ $tt->supplier_name }}</td>
                <td>{{ $tt->invoice_no }}</td>
                <td>{{ $tt->currency }}</td>
                <td>{{ $tt->amount }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $tt->payment_term }}</td>
                <td><?php echo date('d-m-Y', strtotime($tt->due_date)) ?></td>
                <td>{{ $tt->surat_jalan }}</td>
                <td>{{ $tt->bap }}</td>
                <td>{{ $tt->faktur_pajak }}</td>
                <td>{{ $tt->po_number }}</td>
                <td><?php echo date('d-m-Y', strtotime($tt->invoice_date)) ?></td>
                <td><?php echo date('d-m-Y', strtotime($tt->do_date)) ?></td>
                <td><?php echo date('d-m-Y', strtotime($tt->distribution_date)) ?></td>
            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>