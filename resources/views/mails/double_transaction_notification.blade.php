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
			<p style="font-size: 20px;">Double Transaction Notification<br>{{ $data['date_text'] }} : {{ count($data['resume']) }} Double Transaction</p>

			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="90%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 10%; border:1px solid black;">Category</th>
						<th style="width: 10%; border:1px solid black;">Barcode</th>
						<th style="width: 10%; border:1px solid black;">Material</th>
						<th style="width: 30%; border:1px solid black;">Description</th>
						<th style="width: 5%; border:1px solid black;">SLoc</th>
						<th style="width: 5%; border:1px solid black;">ToLoc</th>
						<th style="width: 5%; border:1px solid black;">Quantity</th>
						<th style="width: 20%; border:1px solid black;">Created At</th>
						<th style="width: 5%; border:1px solid black;">Duplicate</th>
					</tr>
				</thead>
				<tbody>					
					@foreach($data['resume'] as $col)
					<tr>
						<td style="border:1px solid black;">{{ $col->category }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $col->barcode }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $col->material_number }}</td>
						<td style="border:1px solid black;">{{ $col->description }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $col->issue }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $col->receive }}</td>
						<td style="border:1px solid black; text-align: right;">{{ $col->lot }}</td>
						<td style="border:1px solid black; text-align: center;">{{ $col->created_at }}</td>
						<td style="border:1px solid black; text-align: right;">{{ $col->duplicates }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://10.109.52.4/kitto/public/">Kitto きっと</a><br>

		</center>
	</div>
</body>
</html>