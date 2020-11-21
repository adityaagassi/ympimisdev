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
				<?php $deskripsi = $datas->deskripsi ?>
				<?php $cpar_no = $datas->cpar_no ?>
				<?php $lokasi = $datas->lokasi ?>
				<?php $kategori = $datas->kategori ?>
				<?php $sumber_komplain = $datas->sumber_komplain ?>
				<?php $judul_komplain = $datas->judul_komplain ?>
				<?php $pic_name = $datas->pic_name ?>
				<?php $posisi = $datas->posisi ?>
				<?php 
					if($posisi == "qa"){ 
						$id_cpar = $datas->id_cpar;
					}
				?>
			@endforeach

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			@if($posisi == "qa")
				<p style="font-size: 18px;">Verifikasi CAR Oleh QA<br>
			@endif
			<p style="font-size: 18px;">Pembuatan CAR dari Penerbitan CPAR {{ $cpar_no }}<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.<br><br>

			<h1>Komplain : {{$judul_komplain}}</h1>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 2%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">PIC</td>
						<td style="border:1px solid black; text-align: center;"><?= $pic_name ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Location</td>
						<td style="border:1px solid black; text-align: center;">{{$lokasi}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Kategori</td>
						<td style="border:1px solid black; text-align: center;">{{$kategori}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Sumber Komplain</td>
						<td style="border:1px solid black; text-align: center;">{{$sumber_komplain}}</td>
					</tr>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>

			@if($posisi == "qa")
				<a href="{{ url('index/qc_report/verifikasiqa/'.$id_cpar) }}">QA Verification</a><br>
			@else
				<a href="{{ url('index/qc_car/verifikasicar/'.$id) }}">CAR Verification</a><br>

			@endif


			
			<!-- <a href="http://172.17.128.87/miraidev/public/index/qc_car/verifikasicar/{{ $id }}">See CAR Detail and Verification</a><br> -->
		</center>
	</div>
</body>
</html>