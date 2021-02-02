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
			<p style="font-size: 18px;">Abnormal Employee Temperature</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 1%; border:1px solid black;">#</th>
						<th style="width: 2%; border:1px solid black;">NIK</th>
						<th style="width: 2%; border:1px solid black;">Nama</th>
						<th style="width: 2%; border:1px solid black;">Dept</th>
						<th style="width: 2%; border:1px solid black;">Section</th>
						<th style="width: 2%; border:1px solid black;">Group</th>
						<th style="width: 2%; border:1px solid black;">Time</th>
						<th style="width: 2%; border:1px solid black;">Temperature</th>
					</tr>
				</thead>
				<tbody>
					<?php for ($i=0; $i < count($data); $i++) { ?>
						<tr>
							<td style="border:1px solid black; text-align: center;">{{ $i+1 }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data[$i]['employee_id'] }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data[$i]['name'] }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data[$i]['department'] }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data[$i]['section'] }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data[$i]['group'] }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data[$i]['date_in'] }}</td>
							<td style="border:1px solid black; text-align: center;">{{ $data[$i]['temperature'] }}</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<!-- <br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br> -->
		</center>
	</div>
</body>
</html>