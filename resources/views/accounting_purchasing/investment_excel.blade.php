<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($investment_detail) && count($investment_detail) > 0)
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Category</th>
                <th>Code</th>
                <th>Ratio Env</th>
                <th>Purpose</th>
                <th>Role Auth</th>
                <th>YCJ Approval</th>
                <th>Applied Date</th>
                <th>Remark</th>
                <th>Budget No</th>
                <th>Department</th>
                <th>Invesment Number</th>
                <th>Description</th>
                <th>Currency</th>
                <th>Amount Doc</th>
                <th>Vendor</th>
                <th>Status</th>
                <th>Received Plan</th>
                <th>Done?</th>
                <th>PO Number</th>
                <th>Status</th>
                <th>Amount USD</th>
                <th>Month Settle</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $num = 1;
                $amount=0; 
            ?>

            @foreach($investment_detail as $investment)

            <tr>
                <td>{{ $num++ }}</td>
                <td>{{ $investment->category }}</td>
                <td>{{ $investment->type }}</td>
                <td>0</td>
                <td>{{ $investment->objective }}</td>
                <td>
                    @if($investment->category == "Investment")
                        IV Fixed asset: 5
                    @elseif($investment->category == "Expense")
                        2 Manajemen Bisnis: KG21
                    @endif
                </td>
                <td>{{ $investment->ycj_approval }}</td>
                <td>{{ $investment->submission_date }}</td>
                <td>{{ $investment->note }}</td>
                <td>{{ $investment->budget_no }}</td>
                <td>{{ $investment->applicant_department }}</td>
                <td>{{ $investment->reff_number }}</td>
                <td>{{ $investment->detail }}</td>
                <td>{{ $investment->currency }}</td>
                <td>{{ $investment->amount }}</td>
                <td>{{ $investment->supplier_code }} - {{ $investment->supplier_name }}</td>
                <td>{{ $investment->category_budget }}</td>
                <td>{{ $investment->delivery_order }}</td>
                <td></td>
                <td>{{ $investment->no_po }}</td>
                <td>
                    @if($investment->posisi == "finished")
                        Approval Completed
                    @elseif($investment->posisi == "user")
                        Saved
                    @else
                        On Going
                    @endif
                </td>
                <td>{{ $investment->dollar }}</td>
                <td></td>
            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>