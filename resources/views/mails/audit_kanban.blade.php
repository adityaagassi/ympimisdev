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
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 25px;"><b>Audit Kanban (かんばん監査)</b></p>
			<p style="font-size: 20px;">Leader {{$data[0]->leader}}<br>{{$data[0]->area}}<br>Tanggal {{date('d F Y',strtotime($data[0]->check_date))}}</p>
			<?php $activity_list_id = $data[0]->activity_list_id; ?>
			<?php $month = date('Y-m',strtotime($data[0]->check_date)); ?>
			This is an automatic notification. Please do not reply to this address.<br>
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);color: white">
					<tr>
						<th style="width: 1%; border:1px solid black;">No.<br>番</th>
						<th style="width: 4%; border:1px solid black;">Point Check<br>監査箇所</th>
						<th style="width: 1%; border:1px solid black;">Condition<br>調子</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1; ?>
					@foreach($data as $data)
					<?php if ($data->condition == 'OK'){ ?>
						<?php $color = '#c5ffb8' ?>
					<?php }else{
						$color = '#c5ffb8';
					} ?>
						<tr>
							<td style="border:1px solid black; text-align: center;">{{ $i++ }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data->point_check_name }}<br>{{ $data->point_check_jp }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data->condition }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('approval/audit_kanban/'.$activity_list_id.
			'/'.$month) }}">Approval Data</a>
		</center>
	</div>
</body>
</html>