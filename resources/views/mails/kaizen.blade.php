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
			<p style="font-size: 18px;">Unverified Kaizen Teian (Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<table style="border-color: black">
				<thead style="background-color: rgb(126,86,134);">
					<tr style="color: white; background-color: #7e5686">
						<th style="width: 3%; border:1px solid black;">Department</th>
						<th style="width: 3%; border:1px solid black;">Section</th>
						<th style="width: 2%; border:1px solid black;">Unverified Chief / Foreman</th>
						<th style="width: 2%; border:1px solid black;">Unverified Manager</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($data['kaizens'] as $kzn) {
						print_r ('<tr>
							<td>'.$kzn->child_code.'</td>
							<td>'.$kzn->area.'</td>
							<td>'.$kzn->frm.'</td>
							<td>'.$kzn->mngr.'</td>
							</tr>');
					}
					?>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/index/kaizen">Varify Kaizen Teian</a><br>
		</center>
	</div>
</body>
</html>