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
				<?php $auditor_lokasi = $datas->auditor_lokasi ?>
				<?php $auditor_kategori = $datas->auditor_kategori ?>
				<?php $auditee_name = $datas->auditee_name ?>
				<?php $auditee_due_date = $datas->auditee_due_date ?>
				<?php $status = $datas->status ?>
				<?php $alasan = $datas->alasan ?>
			@endforeach

			@if($status == "cpar")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">CPAR Audit Internal Standarisasi<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h3> Standart {{$auditor_kategori}} </h3>

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
			<a href="http://172.17.128.4/mirai/public/index/audit_iso/verifikasistd/{{ $id }}">Detail dan Verifikasi</a><br>

			@elseif($status == "car")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">CPAR Audit Internal Standarisasi<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h3> Standart {{$auditor_kategori}} </h3>

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
			<a href="http://172.17.128.4/mirai/public/index/audit_iso/response/{{ $id }}">Response Audit Internal ISO</a><br>


			@elseif($status == "commended")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Revisi CPAR Audit Internal Standarisasi<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h3>Standart {{$auditor_kategori}} </h3>

			<h3>Revisi Audit ISO Oleh Standarisasi Dengan Catatan :<h3>
			<h3>
				{{ $alasan }}	
			</h3>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/index/audit_iso/detail/{{ $id }}">Edit Form Audit ISO</a>


			@elseif($status == "rejected")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">CPAR Audit Internal Standarisasi Di Tolak<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h3>Standart {{$auditor_kategori}} </h3>
			<h3>Audit ISO Tidak Ditolak Dengan Catatan :<h3>
			<h3>
				{{ $alasan }}	
			</h3>

			
			@elseif($status == "verif")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Penanganan Audit Internal Standarisasi<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h3> Standart {{$auditor_kategori}} </h3>

			<h3> Penanganan Telah Dilakukan Oleh <?= $auditee_name ?></h3>

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
						<td style="width: 2%; border:1px solid black;">Due Date</td>
						<td style="border:1px solid black; text-align: center;"><?= date('d F Y', strtotime($auditee_due_date)) ?></td>
					</tr>

				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Klik disini untuk</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/index/audit_iso/print/{{ $id }}">Cek Penanganan</a><br>
			<a href="http://172.17.128.4/mirai/public/index/audit_iso/detail/{{ $id }}">Tambahkan Catatan Audit</a><br>

			@endif
			
		</center>
	</div>
</body>
</html>