<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		table, th, td {
			border: 1px solid black;
		}
		td {
			padding-top: 0px;
			padding-bottom: 0px;
			padding-left: 3px;
			padding-right: 3px;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 20px;">Raw Material Reminder</p>
			<p style="font-size: 20px;">PIC : {{ $data['user']->name }}</p>

			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 15%; border:1px solid black;">Material</th>
						<th style="width: 45%; border:1px solid black;">Description</th>
						<th style="width: 10%; border:1px solid black;">Stock Date</th>
						<th style="width: 10%; border:1px solid black;">Stock</th>
						<th style="width: 10%; border:1px solid black;">Polcy</th>
						<th style="width: 10%; border:1px solid black;">Percentage</th>
					</tr>
				</thead>
				<tbody>					
					@foreach($data['material'] as $col)
					<tr>
						<td style="border:1px solid black; text-align: center;">{{ $col->material_number }}</td>
						<td style="border:1px solid black;">{{ $col->material_description }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $col->stock_date }}</td>
						<td style="border:1px solid black; text-align: right;">{{ $col->stock }}</td>
						<td style="border:1px solid black; text-align: right;">{{ $col->policy }}</td>
						<td style="border:1px solid black; text-align: right;">{{ $col->percentage }}%</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url("index/material/material_monitoring/direct") }}">Raw Material Monitoring (素材監視)</a><br>

		</center>
	</div>
</body>
</html>