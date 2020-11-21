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
						<?php $month = $col2->month;
						$monthTitle = date("F Y", strtotime($month)); ?>
						<?php $activity_list_id = $col2->activity_list_id ?>
						<?php $subsection = $col2->subsection ?>
						<?php $leader = $col2->leader_dept ?>
					@endforeach
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Cek Alat Pelindung Diri (APD)<br>{{ $leader }} ({{ strtoupper($department_name) }})</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 2%; border:1px solid black;">Date</th>
						<th style="width: 2%; border:1px solid black;">PIC</th>
						<th style="width: 2%; border:1px solid black;">Jenis APD</th>
						<th style="width: 2%; border:1px solid black;">Proses</th>
						<th style="width: 2%; border:1px solid black;">Kondisi</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1; ?>
					@foreach($data as $col)
					<tr>
						<td style="border:1px solid black; text-align: center;">{{$i}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->date}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->pic}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->jenis_apd}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->proses}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->kondisi}}</td>
					</tr>
					<?php $i++; ?>
					@endforeach
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('index/apd_check/print_apd_check_email/'.$activity_list_id.
			'/'.$month) }}">See APD Check Data / Approval Data</a><br>
		</center>
	</div>
</body>
</html>