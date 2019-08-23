<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
	</style>
</head>
<body>
	<div style="width: 700px;">
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 30px; font-weight: bold;">Informasi Double Serial Number (Last Update: {{ date('d-M-Y H:i:s') }})</p>
			
			This is an automatic notification. Please do not reply to this address.
			@foreach($data as $col2)
			@if($col2->log=="2")
			<p style="font-size: 25px;">{{$col2->serial_number}} - SAX - ASSY</p>
			@else
			<p style="font-size: 25px;">{{$col2->serial_number}} - SAX - HANDATSUKE</p>
			@endif
			@endforeach
			<table style="border:1px solid black; border-collapse: collapse;">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 3%; border:1px solid black;">Old Model</th>
						<th style="width: 7%; border:1px solid black;">Old Created At</th>
						<th style="width: 7%; border:1px solid black;">Old Created By</th>
						<th style="width: 3%; border:1px solid black;">New Model</th>
						<th style="width: 7%; border:1px solid black;">New Created At</th>
						<th style="width: 7%; border:1px solid black;">New Created By</th>
					</tr>
				</thead>
				<tbody>
					@foreach($data as $col)
					
					<tr>
						<td style="border:1px solid black;">{{$col->model}}</td>
						<td style="border:1px solid black;">{{$col->updated_at}}</td>
						<td style="border:1px solid black;">{{$col->user1}}</td>
						<td style="border:1px solid black;">{{$col->model}}</td>
						<td style="border:1px solid black;">{{$col->input}}</td>
						<td style="border:1px solid black;">{{$col->user3}}</td>					
					</tr>
					
					@endforeach
				</tbody>
			</table>
			<br>
			<!-- <span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/index/display/shipment_progress">Realtime Hasil Produksi Terhadap Kebutuhan Ekspor</a><br>
			<a href="{{ url("/index/display/stuffing_progress") }}">Realtime Today Loading Progress</a> -->
		</center>
	</div>
</body>
</html>