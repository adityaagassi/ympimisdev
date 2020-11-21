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
						<?php $date_training = $col2->date ?>
						<?php $training_id = $col2->training_id ?>
						<?php $activity_list_id = $col2->activity_list_id ?>
						<?php $product = $col2->product ?>
						<?php $leader = $col2->leader_dept ?>
					@endforeach
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Training Report<br>{{ $leader }} ({{ strtoupper($department_name) }})</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 2%; border:1px solid black;">Section</th>
						<th style="width: 2%; border:1px solid black;">Periode</th>
						<th style="width: 2%; border:1px solid black;">Tanggal</th>
						<th style="width: 2%; border:1px solid black;">Waktu</th>
						<th style="width: 2%; border:1px solid black;">Trainer</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1; ?>
					@foreach($data as $col)
					<tr>
						<td style="border:1px solid black; text-align: center;">{{$i}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->section}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->periode}}</td>
						<td style="border:1px solid black; text-align: center;">{{$col->date}}</td>
						<td style="border:1px solid black; text-align: center;"><?php 
			                $timesplit=explode(':',$col->time);
			                $min=($timesplit[0]*60)+($timesplit[1])+($timesplit[2]>30?1:0); ?>
			              {{$min.' Min'}}
			          	</td>
						<td style="border:1px solid black; text-align: center;">{{$col->trainer}}</td>
					</tr>
					<?php $i++; ?>
					@endforeach
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('index/training_report/print_training_email/'.$training_id) }} ">See Training Data / Approval Data Approval Data</a><br>
		</center>
	</div>
</body>
</html>