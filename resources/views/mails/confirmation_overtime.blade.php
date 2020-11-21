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
			<p style="font-size: 18px;">Total Jumlah Overtime Belum Dikonfirmasi > 3 Hari</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="30%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 6%; border:1px solid black;">Section</th>
						<th style="width: 3%; border:1px solid black;">Unconfirmed</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$total = 0;
					?>
					@foreach($data as $col)
					<tr>
						<td style="border:1px solid black;">{{$col->section}}</td>
						<td style="border:1px solid black; text-align: right;">{{$col->unconfirmed}}</td>
					</tr>
					<?php
					$total += $col->unconfirmed;
					?>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<th>Total:</th>
						<th>{{$total}}</th>
					</tr>
				</tfoot>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('index/overtime_confirmation') }}">Confirm Overtime</a>
		</center>
	</div>
</body>
</html>