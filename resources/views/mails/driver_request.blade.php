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
	<?php
	$id = $data['driver']['id'];
	?>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Driver Request Approval</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<tbody>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">ID</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['driver']['id'] }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Pemohon</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['requested_by'] }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Keperluan</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['driver']['purpose'] }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Kota Tujuan</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['driver']['destination_city'] }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Mulai</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['driver']['date_from'] }}</td>
					</tr>
					<tr>
						<td style="width: 1%; border:1px solid black; background-color: rgb(126,86,134);">Sampai</td>
						<td style="width: 2%; border:1px solid black;">{{ $data['driver']['date_to'] }}</td>
					</tr>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold;"><i>Apakah anda menyetujui permohonan ini?</i></span><br>
			<a class="btn btn-success" style="background-color: green; width: 50px;" href="{{ url("approve/ga_control/driver/".$id) }}">&nbsp;&nbsp;&nbsp; Ya &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a class="btn btn-danger" style="background-color: orange; width: 50px;" href="{{ url("reject/ga_control/driver/".$id) }}">&nbsp; Tidak &nbsp;</a><br>
		</center>
	</div>
</body>
</html>