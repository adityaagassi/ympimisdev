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
				<?php $no_pr = $datas->no_pr ?>
				<?php $emp_name = $datas->emp_name ?>
				<?php $department = $datas->department ?>
				<?php $note = $datas->note ?>
				<?php $no_budget = $datas->no_budget ?>
				<?php $submission_date = $datas->submission_date ?>
				<?php $posisi = $datas->posisi ?>
				<?php $alasan = $datas->alasan ?>
			@endforeach

			@if($posisi == "manager" || $posisi == "dgm")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Purchase Requisition <br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Telah Dibuat Purchase Requisition (PR) Nomor {{$no_pr}}</h2>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 2%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Tanggal</td>
						<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($submission_date)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">User</td>
						<td style="border:1px solid black; text-align: center;"><?= $emp_name ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Departemen</td>
						<td style="border:1px solid black; text-align: center;"><?= $department ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">No Budget</td>
						<td style="border:1px solid black; text-align: center;"><?= $no_budget ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Catatan</td>
						<td style="border:1px solid black; text-align: center;"><?= $note ?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Klik disini untuk</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/purchase_requisition/verifikasi/{{ $id }}">Approval</a><br>

			<!-- <br>
			<span style="font-weight: bold;"><i>Apakah anda ingin menerbitkan CPAR Berdasarkan kasus ini ?</i></span><br>
			<a style="background-color: green; width: 50px;" href="{{ url("index/form_ketidaksesuaian/approveqa/".$id) }}">&nbsp;&nbsp;&nbsp; Ya &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: orange; width: 50px;" href="{{ url("index/form_ketidaksesuaian/rejectqa/".$id) }}">&nbsp; Tidak &nbsp;</a><br> -->


			<!-- Tolak -->
			@elseif($posisi == "user")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Form Laporan Ketidaksesuaian Tidak Disetujui<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<h2>Purchase Requisition (PR) Nomor {{$no_pr}} Telah Ditolak</h2>
			<h3>PR Tidak Disetujui Dengan Catatan :<h3>
			<h3>
				{{ $alasan }}	
			</h3>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/purchase_requisition/detail/{{ $id }}">PR Detail</a>

			@endif
			
		</center>
	</div>
</body>
</html>