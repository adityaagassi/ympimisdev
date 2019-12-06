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
					@foreach($data as $col2)
						<?php $activity_name = $col2->activity_name ?>
						<?php $department_name = $col2->department_name ?>
						<?php $activity_list_id = $col2->activity_list_id ?>
						<?php $id_first_product_audit = $col2->id_first_product_audit?>
						<?php $subsection = $col2->subsection ?>
						<?php $proses = $col2->proses ?>
						<?php $jenis = $col2->jenis ?>
						<?php $date = $col2->date ?>
						<?php $monthitle = date("F Y", strtotime(substr($date,0,7))); ?>
						<?php $month = substr($date,0,7); ?>
						<?php $leader = $col2->leader_dept ?>
					@endforeach
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">{{ $activity_name }} of {{ $leader }} ({{ $department_name }}) <br>on {{ $monthitle }}<br><br>Sub Section {{ $subsection }} <br> (Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 2%; border:1px solid black;">Date</th>
						<th style="width: 2%; border:1px solid black;">Month</th>
						<th style="width: 2%; border:1px solid black;">Proses</th>
						<th style="width: 2%; border:1px solid black;">Jenis</th>
						<th style="width: 2%; border:1px solid black;">Auditor</th>
						<th style="width: 2%; border:1px solid black;">Note</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1; ?>
					@foreach($data as $col)
					<tr>
						<td style="border:1px solid black; text-align: center;">{{$i}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->date}}</td>
						<td style="border:1px solid black; text-align: center;">{{date("F Y", strtotime(substr($date,0,7)))}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->proses}}</td>
						<td style="border:1px solid black; text-align: center;"><?php echo $col->jenis ?></td>
						<td style="border:1px solid black; text-align: center;">{{$col->leader}}</td>
						<td style="border:1px solid black; text-align: center;"><?php echo $col->note ?></td>
					</tr>
					<?php $i++; ?>
					@endforeach
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/index/first_product_audit/print_first_product_audit_email/{{ $activity_list_id }}/{{ $id_first_product_audit }}/{{ substr($date,0,7) }}">See Audit Cek Produk Pertama / Approval Data</a><br>
			{{-- <a href="http://172.17.128.4/mirai/public/index/audit_report_activity/report_audit_activity/8">Audit Report Activity Monitoring</a> --}}
		</center>
	</div>
</body>
</html>