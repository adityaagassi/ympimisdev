<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		td{
			padding-right: 5px;
			padding-left: 5px;
			padding-top: 0px;
			padding-bottom: 0px;
		}
		th{
			padding-right: 5px;
			padding-left: 5px;			
		}
	</style>
</head>
<body>
	<div>
		<center>
		@foreach($data as $datas)
			<?php $id = $datas->id ?>
			<?php $request_date = $datas->request_date ?>
			<?php $budget_from = $datas->budget_from ?>
			<?php $budget_to = $datas->budget_to ?>
			<?php $amount = $datas->amount ?>
			<?php $posisi = $datas->posisi ?>
		@endforeach

		@if($posisi == "manager_from")

		<p style="font-size: 18px;">Request Transfer Budget <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Detail Transfer Budget</h2>

		<table style="border:1px solid black; border-collapse: collapse;" width="80%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 2%; border:1px solid black;">Point</th>
					<th style="width: 2%; border:1px solid black;">Content</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="width: 2%; border:1px solid black;">Date</td>
					<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($request_date)) ?></td></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Budget From</td>
					<td style="border:1px solid black; text-align: center;"><b><?= $budget_from ?></b></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Budget To</td>
					<td style="border:1px solid black; text-align: center;"><b><?= $budget_to ?></b></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Amount</td>
					<td style="border:1px solid black; text-align: center;"><b>$ <?= $amount ?></b></td>
				</tr>
			</tbody>
		</table>
		<br>
		<span style="font-weight: bold;font-size: 18px"><i>Do you want to Approve This Transfer Budget?</i></span>
		<br><br>

		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("transfer_budget/approvemanagerfrom/".$id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("transfer_budget/reject/".$id) }}">&nbsp; Reject &nbsp;</a>

		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

		@elseif($posisi == "manager_to")

		<p style="font-size: 18px;">Request Transfer Budget <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Detail Transfer Budget</h2>

		<table style="border:1px solid black; border-collapse: collapse;" width="80%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 2%; border:1px solid black;">Point</th>
					<th style="width: 2%; border:1px solid black;">Content</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="width: 2%; border:1px solid black;">Date</td>
					<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($request_date)) ?></td></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Budget From</td>
					<td style="border:1px solid black; text-align: center;"><b><?= $budget_from ?></b></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Budget To</td>
					<td style="border:1px solid black; text-align: center;"><b><?= $budget_to ?></b></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Amount</td>
					<td style="border:1px solid black; text-align: center;"><b>$ <?= $amount ?></b></td>
				</tr>
			</tbody>
		</table>
		<br>
		<span style="font-weight: bold;font-size: 18px"><i>Do you want to Approve This Transfer Budget?</i></span>
		<br><br>

		<a style="background-color: green; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("transfer_budget/approvemanagerto/".$id) }}">&nbsp;&nbsp;&nbsp; Approve &nbsp;&nbsp;&nbsp;</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a style="background-color: red; width: 50px; text-decoration: none;color: white;font-size: 20px;" href="{{ url("transfer_budget/reject/".$id) }}">&nbsp; Reject &nbsp;</a>

		<br><br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

		@elseif($posisi == "acc")

		<p style="font-size: 18px;">Request Transfer Budget Approved<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
		This is an automatic notification. Please do not reply to this address.

		<h2>Transfer Budget Already Approved By Both Manager</h2>

		<table style="border:1px solid black; border-collapse: collapse;" width="80%">
			<thead style="background-color: rgb(126,86,134);">
				<tr>
					<th style="width: 2%; border:1px solid black;">Point</th>
					<th style="width: 2%; border:1px solid black;">Content</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="width: 2%; border:1px solid black;">Date</td>
					<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($request_date)) ?></td></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Budget From</td>
					<td style="border:1px solid black; text-align: center;"><b><?= $budget_from ?></b></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Budget To</td>
					<td style="border:1px solid black; text-align: center;"><b><?= $budget_to ?></b></td>
				</tr>
				<tr>
					<td style="width: 2%; border:1px solid black;">Amount</td>
					<td style="border:1px solid black; text-align: center;"><b>$ <?= $amount ?></b></td>
				</tr>
			</tbody>
		</table>
		<br>

		<span style="font-size: 20px">Best Regards,</span>
		<br><br>

		<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt="">

		@endif
			
		</center>
	</div>
</body>
</html>