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
						<th style="width: 500px;border:1px solid black;">Department</th>
						<th style="width: 100px;border:1px solid black;">Unconfirmed Visitor</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$total = 0;
					for ($i=0; $i < count($data); $i++) { 
						print_r ('<tr>
							<td style="text-align:center">'.$data[$i]['department'].'</td>
							<td style="text-align:center">'.$data[$i]['jumlah_visitor'].'</td>
							</tr>');
						$total = $total + $data[$i]['jumlah_visitor'];
					}
					print_r ('<tr style="color: white; background-color: #7e5686">
							<td style="text-align:center"><b>TOTAL</b></td>
							<td style="text-align:center"><b>'.$total.'</b></td>
							</tr>');
					?>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For Confirm Your Visitor</i> &#8650;</span><br>
			<a href="http://10.109.52.4/mirai/public/visitor_confirmation_manager">Visitor Confirmation</a><br>
		</center>
	</div>
</body>
</html>