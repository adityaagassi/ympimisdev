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
			<p style="font-size: 18px;">Minimum Kanban Queue Reached</p>
			<p style="font-weight: bold;">Total Kanban di Antrian: {{ $data['barrel_count'] }}</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="70%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 1%; border:1px solid black;">Tag</th>
						<th style="width: 2%; border:1px solid black;">Material</th>
						<th style="width: 2%; border:1px solid black;">Model</th>
						<th style="width: 2%; border:1px solid black;">Key</th>
						<th style="width: 2%; border:1px solid black;">Surface</th>
						<th style="width: 3%; border:1px solid black;">Qty</th>
						<th style="width: 4%; border:1px solid black;">Created At</th>
						<th style="width: 3%; border:1px solid black;">Remark</th>
					</tr>
				</thead>
				<tbody>
					<?php
					for ($i=0; $i < count($data['barrel_queues']); $i++) { 
						print_r ('<tr>
							<td style="text-align:right">'.$i.'</td>
							<td>'.$data['barrel_queues'][$i]['tag'].'</td>
							<td>'.$data['barrel_queues'][$i]['material_number'].'</td>
							<td>'.$data['barrel_queues'][$i]['model'].'</td>
							<td>'.$data['barrel_queues'][$i]['key'].'</td>
							<td>'.$data['barrel_queues'][$i]['surface'].'</td>
							<td style="text-align:right">'.$data['barrel_queues'][$i]['quantity'].'</td>
							<td style="text-align:right">'.$data['barrel_queues'][$i]['created_at'].'</td>
							<td>'.$data['barrel_queues'][$i]['remark'].'</td>
							</tr>');
					}
					?>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://localhost/ympimisdev/public/index/middle/barrel_board/barrel-sx">SX Key Barrel Board</a>
		</center>
	</div>
</body>
</html>