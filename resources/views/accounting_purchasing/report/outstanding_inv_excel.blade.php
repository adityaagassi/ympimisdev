<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($inv_detail) && count($inv_detail) > 0)
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Budget No</th>
                <th>PO Number</th>
                <th>PR Number</th>
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

            @foreach($inv_detail as $inv)

            <tr>
                <td>{{ $num++ }}</td>
                <td>{{ $inv->budget_no }}</td>
                <td>{{ $inv->no_po }}</td>
                <td>{{ $inv->reff_number }}</td>
                <td>{{ $inv->detail }}</td>
                <td>{{ $inv->applicant_department }}</td>
                <td>{{ $inv->currency }}</td>
                <td>{{ $inv->qty }}</td>
                <td>{{ $inv->price }}</td>
                <td>{{ $inv->amount }}</td>
                <td>{{ $inv->submission_date }}</td>
                <td>{{ $inv->periode }}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>