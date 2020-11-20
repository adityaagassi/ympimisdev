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
			<p style="font-size: 18px;">Today Stuffing Progress (Last Update: {{ date('d-M-Y H:i:s') }})</p>
			<p style="font-weight: bold;">Stuffing Date: {{ date('l, d F Y') }}</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="90%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 1%; border:1px solid black;">Progress</th>
						<th style="width: 1%; border:1px solid black;">Remark</th>
						<th style="width: 1%; border:1px solid black;">ID</th>
						<th style="width: 1%; border:1px solid black;">Dest</th>
						<th style="width: 1%; border:1px solid black;">Plan</th>
						<th style="width: 1%; border:1px solid black;">Actual</th>
						<th style="width: 1%; border:1px solid black;">Diff</th>
						<th style="width: 1%; border:1px solid black;">Start</th>
						<th style="width: 1%; border:1px solid black;">Finish</th>
						<th style="width: 1%; border:1px solid black;">Duration</th>
						<th style="width: 1%; border:1px solid black;">Departure</th>
						<th style="width: 15%; border:1px solid black;">Remark</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1; ?>
					@foreach($data as $col)
					<?php 
					$color="";
					if($col->stats == 'DEPARTED' ){
						$color = "background-color:RGB(204,255,255);";
					}
					elseif($col->stats == 'LOADING'){
						$color = "background-color:RGB(252,248,227);";
					}
					else{
						$color = "background-color:RGB(255,204,255);";
					}
					?>

					<tr>
						<td style="border:1px solid black; text-align: center;{{$color}}">{{$i}}</td>
						<td style="border:1px solid black; text-align: center;{{$color}}">{{round(($col->total_actual/$col->total_plan)*100,2)}}%</td>
						<td style="border:1px solid black; text-align: center;{{$color}}">{{$col->stats}}</td>
						<td style="border:1px solid black; text-align: center;{{$color}}">{{$col->id_checkSheet}}</td>
						<td style="border:1px solid black; text-align: center;{{$color}}">{{$col->destination}}</td>
						<td style="border:1px solid black; text-align: right;{{$color}}">{{$col->total_plan}}</td>
						<td style="border:1px solid black; text-align: right;{{$color}}">{{$col->total_actual}}</td>
						<td style="border:1px solid black; text-align: right;{{$color}}">{{$col->total_actual-$col->total_plan}}</td>
						<?php if($col->start_stuffing != null ){ ?>
						<td style="border:1px solid black; text-align: right;{{$color}}">{{date('H:i:s', strtotime($col->start_stuffing))}}</td>
						<?php }else{ ?>
						<td style="border:1px solid black; text-align: right;{{$color}}">-</td>
						<?php } ?>
						<?php if($col->stats == 'DEPARTED' ){ ?>
						<td style="border:1px solid black; text-align: right;{{$color}}">{{date('H:i:s', strtotime($col->finish_stuffing))}}</td>
						<td style="border:1px solid black; text-align: right;{{$color}}">{{ $col->duration }} Min(s)</td>
						<?php }else{ ?>
						<td style="border:1px solid black; text-align: right;{{$color}}">-</td>
						<td style="border:1px solid black; text-align: right;{{$color}}">-</td>
						<?php } ?>
						<?php if($col->status != null ){ ?>
						<td style="border:1px solid black; text-align: right;{{$color}}">{{date('H:i:s', strtotime($col->status))}}</td>
						<?php }else{ ?>
						<td style="border:1px solid black; text-align: right;{{$color}}">-</td>
						<?php } ?>
						<?php if($col->reason != null ){ ?>
						<td style="border:1px solid black; text-align: right;{{$color}}">{{$col->reason}}</td>
						<?php }else{ ?>
						<td style="border:1px solid black; text-align: right;{{$color}}">-</td>
						<?php } ?>
					</tr>
					<?php $i++; ?>
					@endforeach
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/index/display/stuffing_monitoring">Realtime Stuffing Monitoring</a><br>
		</center>
	</div>
</body>
</html>