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
			<p style="font-size: 18px;">Highest Survey Covid Report</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 2%; border:1px solid black;">NIK</th>
						<th style="width: 2%; border:1px solid black;">Nama</th>
						<th style="width: 2%; border:1px solid black;">Dept</th>
						<th style="width: 2%; border:1px solid black;">Sect</th>
						<th style="width: 2%; border:1px solid black;">Tanggal Isi</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1; ?>
					@foreach($data as $data)
						<tr>
							<td style="border:1px solid black; text-align: center;">{{ $i++ }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data->employee_id }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data->name }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data->department }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data->section }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data->tanggal }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			<!-- <br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br> -->
		</center>
	</div>
</body>
</html>