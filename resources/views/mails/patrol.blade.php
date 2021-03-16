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
				<?php $tanggal = $datas->tanggal ?>
				<?php $kategori = $datas->kategori ?>		
				<?php $auditor_name = $datas->auditor_name ?>
				<?php $lokasi = $datas->lokasi ?>
				<?php $auditee_name = $datas->auditee_name ?>
				<?php $point_judul = $datas->point_judul ?>
				<?php $note = $datas->note ?>
			@endforeach

			@if($kategori == "S-Up And EHS Patrol Presdir" || $kategori == "5S Patrol GM")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Audit & Patrol YMPI <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Patrol Presdir & General Manager</h2>
			<h3>Permasalahan : <br><?= $note ?></h3>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 2%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Auditor</td>
						<td style="border:1px solid black; text-align: center;">{{$auditor_name}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Tanggal</td>
						<td style="border:1px solid black; text-align: center;"><?= date('d F Y', strtotime($tanggal)) ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Lokasi</td>
						<td style="border:1px solid black; text-align: center;">{{$lokasi}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Auditee</td>
						<td style="border:1px solid black; text-align: center;">{{$auditee_name}}</td>
					</tr>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Klik disini untuk Melihat Monitoring</i> &#8650;</span><br>
			<a href="{{ url('index/audit_patrol/monitoring') }}">Response atau Penanganan Patrol</a><br>

			@elseif($kategori == "EHS & 5S Patrol")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Audit & Patrol YMPI <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>EHS & 5S Patrol Bulanan</h2>
			<h3>Permasalahan : <br><?= $note ?></h3>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 2%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Auditor</td>
						<td style="border:1px solid black; text-align: center;">{{$auditor_name}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Tanggal</td>
						<td style="border:1px solid black; text-align: center;"><?= date('d F Y', strtotime($tanggal)) ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Lokasi</td>
						<td style="border:1px solid black; text-align: center;">{{$lokasi}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Auditee</td>
						<td style="border:1px solid black; text-align: center;">{{$auditee_name}}</td>
					</tr>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Klik disini untuk Melihat Monitoring</i> &#8650;</span><br>
			<a href="{{ url('index/audit_patrol_monthly/monitoring') }}">Response atau Penanganan Patrol Bulanan</a><br>

			@endif

		</center>
	</div>
</body>
</html>