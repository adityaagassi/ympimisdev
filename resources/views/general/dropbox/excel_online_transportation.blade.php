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
				<th style="width: 1%">EMPNO</th>
				<th style="width: 3%">EMPNAME</th>
				<th style="width: 1%">ALTRANSPORTYMPI</th>
			</tr>
        </thead>
        <tbody>
            <?php $index = 1; ?>
            <?php for ($i=0; $i < count($datas); $i++) { ?>
            <tr>
            	<td style="width: 1%;">{{$datas[$i]['employee_id']}}</td>
            	<td style="width: 1%;">{{$datas[$i]['name']}}</td>
            	<td style="width: 1%;">{{round($datas[$i]['total_amount'])}}</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    @endif
</body>
</html>