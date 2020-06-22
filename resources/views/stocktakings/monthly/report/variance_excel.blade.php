<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($variances) && count($variances) > 0)
    <table>
        <thead>
            <tr>
                <th>Group</th>
                <th>plnt</th>
                <th>valcl</th>
                <th>GMC</th>
                <th>Description</th>
                <th>Loc</th>
                <th>Loc Name</th>
                <th>Uom</th>
                <th>Std</th>
                <th>PI</th>
                <th>Book</th>
                <th>diff_qty</th>
                <th>pi_amt</th>
                <th>book_amt</th>
                <th>diff_amt</th>
                <th>var_amt(-)</th>
                <th>var_amt(+)</th>
                <th>var_amt_abs</th>
                <th>note</th>
            </tr>
        </thead>
        <tbody>
            @foreach($variances as $tr)
            <tr>
                <td>{{ $tr->group }}</td>
                <td>{{ $tr->plnt }}</td>
                <td>{{ $tr->valcl }}</td>
                <td>{{ $tr->material_number }}</td>
                <td>{{ $tr->material_description }}</td>
                <td>{{ $tr->location }}</td>
                <td>{{ $tr->location_name }}</td>
                <td>{{ $tr->uom }}</td>
                <td>{{ $tr->std }}</td>
                <td>{{ $tr->pi }}</td>
                <td>{{ $tr->book }}</td>
                <td>{{ $tr->diff_qty }}</td>
                <td>{{ $tr->pi_amt }}</td>
                <td>{{ $tr->book_amt }}</td>
                <td>{{ $tr->diff_amt }}</td>
                <td>{{ $tr->var_amt_min }}</td>
                <td>{{ $tr->var_amt_plus }}</td>
                <td>{{ $tr->var_amt_abs }}</td>
                <td>{{ $tr->note }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>