<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		td{
			padding-right: 5px;
			padding-left: 5px;
			padding-top: 10px;
			padding-bottom: 10px;
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
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 3%; border:1px solid black;">Progress</th>
						<th style="width: 3%; border:1px solid black;">Remark</th>
						<th style="width: 3%; border:1px solid black;">Container No</th>
						<th style="width: 4%; border:1px solid black;">Container Type</th>
						<th style="width: 3%; border:1px solid black;">Dest</th>
						<th style="width: 5%; border:1px solid black;">Plan</th>
						<th style="width: 5%; border:1px solid black;">Actual</th>
						<th style="width: 4%; border:1px solid black;">Diff</th>
						<th style="width: 4%; border:1px solid black;">Finished At</th>
					</tr>
				</thead>
				<tbody>
					@foreach($data as $col)
					<?php 
					$color="";
					$remark=""; 
					if($col->container_number != "" && $col->actual-$col->plan == 0 ){
						$remark = 'Departed';
						$color = "background-color:RGB(204,255,255);";
					}
					elseif($col->container_number == "" && $col->actual > 0 ){
						$remark = 'Loading';
						$color = "background-color:RGB(252,248,227);";
					}
					else{
						$color = "background-color:RGB(255,204,255);";
					}
					?>

					<tr>
						<td style="border:1px solid black; text-align: center;{{$color}}">{{($col->actual/$col->plan)*100}}%</td>
						<td style="border:1px solid black; text-align: center;{{$color}}">{{$remark}}</td>
						<td style="border:1px solid black; text-align: center;{{$color}}">{{$col->container_number}}</td>
						<td style="border:1px solid black; text-align: center;{{$color}}">{{$col->container_name}}</td>
						<td style="border:1px solid black; text-align: center;{{$color}}">{{$col->destination_shortname}}</td>
						<td style="border:1px solid black; text-align: right;{{$color}}">{{$col->plan}}</td>
						<td style="border:1px solid black; text-align: right;{{$color}}">{{$col->actual}}</td>
						<td style="border:1px solid black; text-align: right;{{$color}}">{{$col->actual-$col->plan}}</td>
						<td style="border:1px solid black; text-align: right;{{$color}}">{{$col->finished_at}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<br>
			{{-- <span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br> --}}
			{{-- <a href="http://172.17.128.4/mirai/public/index/fg_shipment_result">Realtime Shipment Progress</a> --}}
		</center>
	</div>
</body>
</html>