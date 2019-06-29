<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
	</style>
</head>
<body>
	<div style="width: 700px;">
		<center>
			{{-- <img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br> --}}
			{{-- <p style="font-size: 18px;">Informasi Kekurangan Ekspor ETD YMPI (Last Update: {{ date('d-M-Y H:i:s') }})</p> --}}
			{{-- <p style="font-weight: bold;">Shipment Date: {{ date('l, d F Y', strtotime($data[0]->st_date)) }}</p> --}}
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th colspan="7">Production Overtime</th>
					</tr>
					<tr>
						<th style="width: 1%; border:1px solid black;">Period</th>
						<th style="width: 1%; border:1px solid black;">Department</th>
						<th style="width: 1%; border:1px solid black;">Section</th>
						<th style="width: 1%; border:1px solid black;">Employee ID</th>
						<th style="width: 8%; border:1px solid black;">Name</th>
						<th style="width: 1%; border:1px solid black;">ΣOvertime</th>
					</tr>
				</thead>
				<tbody>
					{{-- @foreach($data as $col)
					@if($col->diff < 0)
					<tr>
						<td style="border:1px solid black;">{{$col->hpl}}</td>
						<td style="border:1px solid black;">{{$col->material_number}}</td>
						<td style="border:1px solid black;">{{$col->material_description}}</td>
						<td style="border:1px solid black;">{{$col->destination_shortname}}</td>
						<td style="border:1px solid black; text-align: right;">{{$col->plan}}</td>
						<td style="border:1px solid black; text-align: right;">{{$col->actual}}</td>
						<td style="border:1px solid black; text-align: right; padding-left: 1px; padding-right: 1px;">{{$col->diff}}</td>
					</tr>
					@endif
					@endforeach --}}
				</tbody>
			</table>
			<br>
			<table style="border:1px solid black; border-collapse: collapse;">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th colspan="7">Office Overtime</th>
					</tr>
					<tr>
						<th style="width: 1%; border:1px solid black;">Period</th>
						<th style="width: 1%; border:1px solid black;">Department</th>
						<th style="width: 1%; border:1px solid black;">Section</th>
						<th style="width: 1%; border:1px solid black;">Employee ID</th>
						<th style="width: 8%; border:1px solid black;">Name</th>
						<th style="width: 1%; border:1px solid black;">ΣOvertime</th>
					</tr>
				</thead>
				<tbody>
					{{-- @foreach($data as $col)
					@if($col->diff < 0)
					<tr>
						<td style="border:1px solid black;">{{$col->hpl}}</td>
						<td style="border:1px solid black;">{{$col->material_number}}</td>
						<td style="border:1px solid black;">{{$col->material_description}}</td>
						<td style="border:1px solid black;">{{$col->destination_shortname}}</td>
						<td style="border:1px solid black; text-align: right;">{{$col->plan}}</td>
						<td style="border:1px solid black; text-align: right;">{{$col->actual}}</td>
						<td style="border:1px solid black; text-align: right; padding-left: 1px; padding-right: 1px;">{{$col->diff}}</td>
					</tr>
					@endif
					@endforeach --}}
				</tbody>
			</table>
		</center>
	</div>
</body>
</html>