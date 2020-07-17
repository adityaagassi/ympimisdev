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
				<?php $posisi = $datas->posisi ?>
				<?php $reff_number = $datas->reff_number ?>
				<?php $applicant_id = $datas->applicant_id ?>
				<?php $applicant_name = $datas->applicant_name ?>
				<?php $applicant_department = $datas->applicant_department ?>
				<?php $submission_date = $datas->submission_date ?>
				<?php $category = $datas->category ?>
				<?php $subject = $datas->subject ?>
				<?php $type = $datas->type ?>
				<?php $objective = $datas->objective ?>
				<?php $objective_detail = $datas->objective_detail ?>
				<?php $supplier_code = $datas->supplier_code ?>
				<?php $supplier_name = $datas->supplier_name ?>
				<?php $delivery_order = $datas->delivery_order ?>
				<?php $date_order = $datas->date_order ?>
				<?php $budget_no = $datas->budget_no ?>
			@endforeach

			@if($posisi == "acc_budget")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Investment <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Investment Nomor {{$reff_number}}</h2>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 2%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Applicant</td>
						<td style="border:1px solid black; text-align: center;"><?= $applicant_name ?> - <?= $applicant_department ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Submission Date</td>
						<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($submission_date)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Category</td>
						<td style="border:1px solid black; text-align: center;"><?= $category ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Subject</td>
						<td style="border:1px solid black; text-align: center;"><?= ucfirst($subject) ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Type</td>
						<td style="border:1px solid black; text-align: center;"><?= $type ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Objective</td>
						<td style="border:1px solid black; text-align: center;"><?= $objective ?> - <?= $objective_detail ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Supplier</td>
						<td style="border:1px solid black; text-align: center;"><?= $supplier_code ?> - <?= $supplier_name ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Order Date</td>
						<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($date_order)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Delivery Date</td>
						<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($delivery_order)) ?></td></td>
					</tr>
				</tbody>
			</table>
			<br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Klik disini untuk</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/investment/check/{{ $id }}">Check Investment</a><br>

			<!-- <br>

			<span style="font-weight: bold;"><i>Apakah anda ingin menyetujui Investment ini ?</i></span><br>
			<a style="background-color: green; width: 50px;" href="{{ url("purchase_order/approvemanager/".$id) }}">&nbsp;&nbsp;&nbsp; Ya &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: orange; width: 50px;" href="{{ url("purchase_order/reject/".$id) }}">&nbsp; Tidak &nbsp;</a><br> -->

			

			<!-- Tolak -->

			@elseif($posisi == "user")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Investment Tidak Disetujui<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<h2>Investment Nomor {{$reff_number}} Telah Ditolak</h2>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/investment/detail/{{ $id }}">PO Detail</a>

			@endif
			
		</center>
	</div>
</body>
</html>