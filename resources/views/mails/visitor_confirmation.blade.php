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
			<p style="font-size: 18px;">Visitor Confirmation (来客の確認)
				<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p><br>
			This is an automatic notification. Please do not reply to this address.
			<br><br>
			<table style="border-color: black">
				<thead style="background-color: rgb(126,86,134);">
					<!-- <tr>
						<th colspan="6" style="background-color: #9f84a7">Production Overtime</th>
					</tr> -->
					<tr style="color: white; background-color: #7e5686">
						<th style="width: 2%;border:1px solid black;">Manager</th>
						<th style="width: 1%;border:1px solid black;">Unconfirmed Visitor</th>
					</tr>
				</thead>
				<tbody>
					<?php
					for ($i=0; $i < count($data); $i++) { 
						print_r ('<tr>
							<td>'.$data[$i]['manager_name'].'</td>
							<td>'.$data[$i]['jumlah_visitor'].'</td>
							</tr>');
					}
					?>
				</tbody>
			</table>
			<br>
			<a href="http://10.109.52.4/mirai/public/visitor_confirmation_manager">Visitor Confirmation</a><br>
		</center>
	</div>
</body>
</html>