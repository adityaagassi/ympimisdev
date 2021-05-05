<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($pr_detail) && count($pr_detail) > 0)
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

            @foreach($pr_detail as $pr)

            <tr>
                <td>{{ $num++ }}</td>
                <td>{{ $pr->no_budget }}</td>
                <td>{{ $pr->no_po }}</td>
                <td>{{ $pr->no_pr }}</td>
                <td>{{ $pr->item_desc }}</td>
                <td>{{ $pr->department }}</td>
                <td>{{ $pr->item_currency }}</td>
                <td>{{ $pr->item_qty }}</td>
                <td>{{ $pr->item_price }}</td>
                <td>{{ $pr->item_amount }}</td>
                <td>{{ $pr->submission_date }}</td>
                <td>{{ $pr->periode }}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>