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
				<?php $auditor_name = $datas->auditor_name ?>
				<?php $auditor_date = $datas->auditor_date ?>		
				<?php $auditor_jenis = $datas->auditor_jenis ?>
				<?php $auditor_lokasi = $datas->auditor_lokasi ?>
				<?php $auditee_name = $datas->auditee_name ?>
				<?php $auditee_due_date = $datas->auditee_due_date ?>
				<?php $auditor_permasalahan = $datas->auditor_permasalahan ?>
				<?php $posisi = $datas->posisi ?>
			@endforeach

			@if($posisi == "auditee")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Audit {{ $auditor_jenis }} <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h3> Audit {{$auditor_jenis}} </h3>
			<h4>Permasalahan: <br><?= $auditor_permasalahan ?></h4>

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
						<td style="width: 2%; border:1px solid black;">Tanggal Terbit</td>
						<td style="border:1px solid black; text-align: center;"><?= date('d F Y', strtotime($auditor_date)) ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Lokasi</td>
						<td style="border:1px solid black; text-align: center;">{{$auditor_lokasi}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Auditee</td>
						<td style="border:1px solid black; text-align: center;">{{$auditee_name}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Due Date</td>
						<td style="border:1px solid black; text-align: center;"><?= date('d F Y', strtotime($auditee_due_date)) ?></td>
					</tr>

				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Klik disini untuk</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/index/audit/response/{{ $id }}">Response Audit {{ $auditor_jenis }}</a><br>

			@elseif($posisi == "auditor_final")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Audit {{ $auditor_jenis }} Telah Ditangani<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h3> Audit {{$auditor_jenis}} Telah Ditangani</h3>

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
						<td style="width: 2%; border:1px solid black;">Tanggal Terbit</td>
						<td style="border:1px solid black; text-align: center;"><?= date('d F Y', strtotime($auditor_date)) ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Lokasi</td>
						<td style="border:1px solid black; text-align: center;">{{$auditor_lokasi}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Auditee</td>
						<td style="border:1px solid black; text-align: center;">{{$auditee_name}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Due Date</td>
						<td style="border:1px solid black; text-align: center;"><?= date('d F Y', strtotime($auditee_due_date)) ?></td>
					</tr>

				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Klik disini untuk</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/index/audit/print/{{ $id }}">Lihat Report Perbaikan</a><br>


			@endif
			
		</center>
	</div>
</body>
</html>