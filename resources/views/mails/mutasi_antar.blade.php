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
			@foreach($data as $datas)
				<?php $id = $datas->id ?>
				<?php $nama = $datas->nama ?>
				<?php $tanggal = $datas->tanggal ?>
				<?php $tanggal_maksimal = $datas->tanggal_maksimal ?>
				<?php $departemen = $datas->departemen ?>
				<?php $ke_departemen = $datas->ke_departemen ?>
			@endforeach
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">
			<br>(Last Update: {{ date('d-M-Y H:i:s') }})
			</p>
			This is an automatic notification. Please do not reply to this address.

			<h1>Mutasi Antar Departemen : </h1>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 2%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black; text-align: center;">Name</td>
						<td style="border:1px solid black; text-align: center;">{{$nama}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black; text-align: center;">Request Date</td>
						<td style="border:1px solid black; text-align: center;">{{$tanggal}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black; text-align: center;">Request Due Date</td>
						<td style="border:1px solid black; text-align: center;">{{$tanggal_maksimal}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black; text-align: center;">Department Of Origin</td>
						<td style="border:1px solid black; text-align: center;">{{$departemen}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black; text-align: center;">Department Of Goals</td>
						<td style="border:1px solid black; text-align: center;">{{$ke_departemen}}</td>
					</tr>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="{{ url('mutasi_ant/verifikasi/'.$id) }}">Mutasi Verification</a><br>
		</center>
	</div>
</body>
</html>