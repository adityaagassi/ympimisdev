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
			padding: 3px;
		}
	</style>
</head>
<body>
	<div style="width: 700px;">
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Top 20 Overtime Information (Last Update: {{ date('d-M-Y H:i:s') }})</p>
			<p style="font-weight: bold;">Overtime Period: {{ date('F Y', strtotime($data['first'])) }}</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<table style="border-color: black">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th colspan="6" style="background-color: #9f84a7">Production Overtime</th>
					</tr>
					<tr style="color: white; background-color: #7e5686">
						<th style="width: 2%; border:1px solid black;">Period</th>
						<th style="width: 5%; border:1px solid black;">Dept</th>
						<th style="width: 2%; border:1px solid black;">ID</th>
						<th style="width: 6%; border:1px solid black;">Name</th>
						<th style="width: 3%; border:1px solid black;">Grade</th>
						<th style="width: 2%; border:1px solid black;">Σ Overtime</th>
					</tr>
				</thead>
				<tbody>
					<?php
					for ($i=0; $i < count($data['productions']); $i++) { 
						print_r ('<tr>
							<td style="text-align:right">'.$data['productions'][$i]['period'].'</td>
							<td>'.$data['productions'][$i]['department'].'</td>
							<td>'.$data['productions'][$i]['employee_id'].'</td>
							<td>'.$data['productions'][$i]['name'].'</td>
							<td>'.$data['productions'][$i]['grade'].'</td>
							<td style="text-align:right">'.round($data['productions'][$i]['overtime'],2).'</td>
							</tr>');
					}
					?>
				</tbody>
			</table>

			<br>

			<table style="border-color: black">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th colspan="6" style="background-color: #9f84a7">Office Overtime</th>
					</tr>
					<tr style="color: white; background-color: #7e5686">
						<th style="width: 2%; border:1px solid black;">Period</th>
						<th style="width: 5%; border:1px solid black;">Dept</th>
						<th style="width: 2%; border:1px solid black;">ID</th>
						<th style="width: 6%; border:1px solid black;">Name</th>
						<th style="width: 3%; border:1px solid black;">Grade</th>
						<th style="width: 2%; border:1px solid black;">Σ Overtime</th>
					</tr>
				</thead>
				<tbody>
					<?php
					for ($i=0; $i < count($data['offices']); $i++) { 
						print_r ('<tr>
							<td style="text-align:right">'.$data['offices'][$i]['period'].'</td>
							<td>'.$data['offices'][$i]['department'].'</td>
							<td>'.$data['offices'][$i]['employee_id'].'</td>
							<td>'.$data['offices'][$i]['name'].'</td>
							<td>'.$data['offices'][$i]['grade'].'</td>
							<td style="text-align:right">'.round($data['offices'][$i]['overtime'], 2).'</td>
							</tr>');
					}
					?>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://10.109.52.4/mirai/public/index/report/overtime_monthly_fq">Overtime Monitoring</a><br>
		</center>
	</div>
</body>
</html>