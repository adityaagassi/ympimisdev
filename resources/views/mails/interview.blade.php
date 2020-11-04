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
						<?php $date_interview = $col2->date ?>
						<?php $interview_id = $col2->interview_id ?>
						<?php $activity_list_id = $col2->activity_list_id ?>
						<?php $periode = $col2->periode ?>
						<?php $section = $col2->section ?>
						<?php $subsection = $col2->subsection ?>
						<?php $leader = $col2->leader_dept ?>
					@endforeach
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">{{ $activity_name }}<br>{{ $leader }} ({{ strtoupper($department_name) }}) <br>Tanggal {{ $date_interview }}</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 2%; border:1px solid black;">Section</th>
						<th style="width: 2%; border:1px solid black;">Sub Section</th>
						<th style="width: 2%; border:1px solid black;">Periode</th>
						<th style="width: 2%; border:1px solid black;">Date</th>
						<th style="width: 2%; border:1px solid black;">Leader</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1; ?>
					@foreach($data as $col)
					<tr>
						<td style="border:1px solid black; text-align: center;">{{$i}}</td>
						<td style="border:1px solid black; text-align: center;">{{strtoupper($col->section)}}</td>
						<td style="border:1px solid black; text-align: center;">strtoupper({{$col->subsection}})</td>
						<td style="border:1px solid black; text-align: center;">{{$col->periode}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->date}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->leader}}</td>
					</tr>
					<?php $i++; ?>
					@endforeach
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/index/interview/print_email/{{ $interview_id }}">See Interview Data / Approval Data</a><br>
		</center>
	</div>
</body>
</html>