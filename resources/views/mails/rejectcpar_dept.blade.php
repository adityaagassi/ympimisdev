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
				<?php $alasan = $datas->alasan ?>
				<?php $alasan_car = $datas->alasan_car ?>
				<?php $reject_all = $datas->reject_all ?>
				<?php $judul = $datas->judul ?>
				<?php $posisi = $datas->posisi ?>
				<?php $secfrom = $datas->section_from ?>
				<?php $secto = $datas->section_to ?>
			@endforeach

			<?php 
				$sec = explode("_", $secfrom);
				$sect = explode("_", $secto);
			 ?>

			@if($posisi == "sl" || $posisi == "cf" || $posisi == "m")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Form Laporan Ketidaksesuaian Tidak Disetujui<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<br>
			<h2>Judul : {{$judul}}</h2>
			<h3>Form Tidak Disetujui Dengan Catatan :<h3>
			<h3>
				{{ $alasan }}	
			</h3>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/index/form_ketidaksesuaian/detail/{{ $id }}">Detail Form Ketidaksesuaian</a>

			@elseif($posisi == "dept" || $posisi == "deptcf" || $posisi == "deptm")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Penanganan Form Laporan Ketidaksesuaian<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Penanganan Form Ketidaksesuaian {{$judul}} Tidak Disetujui</h2>

			<h3>Catatan :<h3>
			<h3>
				{{ $alasan_car }}	
			</h3>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/index/form_ketidaksesuaian/response/{{ $id }}">Detail Penanganan</a>

			@elseif($posisi == "verif")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Form Laporan Ketidaksesuaian<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Penanganan Laporan Ketidaksesuaian {{$judul}} Tidak Disetujui</h2>

			<h3>From <?= $sec[0] ?> - <?= $sec[1] ?></h3>
			<h3>To <?= $sect[0] ?> - <?= $sect[1] ?></h3>

			<h3>Dengan Catatan :<h3>
			<h3>
				{{ $reject_all }}	
			</h3>
			<br>

			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/index/form_ketidaksesuaian/response/{{ $id }}">Perbaikan Detail Penanganan</a>

			@endif

		</center>
	</div>
</body>
</html>