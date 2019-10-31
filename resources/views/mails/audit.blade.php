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
						<?php $date_audit = $col2->date ?>
						<?php $activity_list_id = $col2->activity_list_id ?>
						<?php $product = $col2->product ?>
						<?php $proses = $col2->proses ?>
					@endforeach
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Production Report ({{ $department_name }}) {{ $activity_name }} <br>on {{ $date_audit }} <br> (Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 2%; border:1px solid black;">Departments</th>
						<th style="width: 2%; border:1px solid black;">Product</th>
						<th style="width: 2%; border:1px solid black;">Proses</th>
						<th style="width: 2%; border:1px solid black;">Date</th>
						<th style="width: 2%; border:1px solid black;">Kondisi</th>
						<th style="width: 2%; border:1px solid black;">PIC</th>
						<th style="width: 2%; border:1px solid black;">Auditor</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1; ?>
					@foreach($data as $col)
					<tr>
						<td style="border:1px solid black; text-align: center;">{{$i}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->department_name}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->product}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->proses}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->date}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->kondisi}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->pic_name}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->auditor_name}}</td>
					</tr>
					<?php $i++; ?>
					@endforeach
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://172.17.128.87/miraidev/public/index/production_audit/print_audit_email/{{ $activity_list_id }}/{{ $date_audit }}/{{ $product }}/{{ $proses }}">See Audit Data</a><br>
			<a href="http://172.17.128.4/mirai/public/index/production_audit/report_audit/8">Audit Monitoring</a>
		</center>
	</div>
</body>
</html>