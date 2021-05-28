<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		table, td, th {
			border: 1px solid black;
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}

	</style>
</head>
<body style="font-family: calibri;">
	<div>
		<!-- <center>
			<span style="font-weight: bold; font-size: 20px;">
				
			</span>
		</center> -->
		<table style="width: 100%;margin-top: 20px;">
			<thead>
				<tr>
					<th colspan="2" style="font-weight: bold; font-size: 20px;padding: 10px">MENU LIVE COOKING BULAN {{strtoupper($monthTitle)}}</th>
				</tr>
				<tr>
					<th style="width: 1%;">Tanggal</th>
					<th style="width: 2%;">Menu</th>
				</tr>
				@foreach($live_cookings as $live_cooking)
				<tr>
					<td style="height: 40px;text-align: center;">{{ $live_cooking->due_date }}</td>
					<td style="height: 40px;text-align: center;">{{ $live_cooking->menu_name }}</td>
				</tr>
				@endforeach
			</thead>
		</table>
		<span style="float: right;font-weight: bold;">General Affairs Department</span>
	</div>
</body>
</html>