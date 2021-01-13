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
			<p style="font-size: 18px;">Clinic Visit Data</p>

			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="90%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Employee ID</th>
						<th style="width: 4%; border:1px solid black;">Name</th>
						<th style="width: 4%; border:1px solid black;">Departement</th>
						<th style="width: 2%; border:1px solid black;">Purpose</th>
						<th style="width: 2%; border:1px solid black;">Paramedic</th>
						<th style="width: 4%; border:1px solid black;">Diagnose</th>
						<th style="width: 3%; border:1px solid black;">Visited At</th>
					</tr>
				</thead>
				<tbody>					
					@foreach($data['resume'] as $col)
					<tr>
						<td style="border:1px solid black;">{{ $col->employee_id }}</td>
						<td style="border:1px solid black;">{{ $col->name }}</td>
						<td style="border:1px solid black;">{{ $col->department }}</td>
						<td style="border:1px solid black;">{{ $col->purpose }}</td>
						<td style="border:1px solid black;">{{ $col->paramedic }}</td>
						<td style="border:1px solid black;">{{ $col->diagnose }}</td>
						<td style="border:1px solid black;">{{ $col->visited_at }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url("index/clinic_visit_log") }}">Data Kunjungan Klinik</a><br>

		</center>
	</div>
</body>
</html>