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
				<?php $kategori = $datas->kategori ?>
				<?php $judul = $datas->judul ?>
				<?php $tanggal = $datas->tanggal ?>
				<?php $tanggal_car = $datas->tanggal_car ?>
				<?php $secfrom = $datas->section_from ?>
				<?php $secto = $datas->section_to ?>
				<?php $posisi = $datas->posisi ?>
			@endforeach

			<?php 
				$sec = explode("_",$secfrom);
				$sect = explode("_",$secto);
			 ?>

			@if($posisi == "qa")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Form Laporan Ketidaksesuaian<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Kasus : {{$judul}}
			<br>
			Berpotensi Diterbitkan CPAR oleh QA
			</h2>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 2%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$katt = explode("_",$kategori);
					  	
					  	if ($katt[1] == "Spec"){
					  		$a = "Defect yang berhubungan dengan Spec Produk";
					  	}
					  	else if ($katt[1] == "Part") {
					  		$a = "Defect yang berhubungan dengan kelengkapan Part atau Aksesoris Produk";
					  	}
					  	else if ($katt[1] == "Fungsi") {
					  		$a = "Defect yang dapat mengganggu Fungsi Utama Produk";
					  	}
					  	else if ($katt[1] == "Luka") {
					  		$a = "Temuan NG Berpotensi Melukai atau Mencederai Customer";
					  	}
					  	else if ($katt[1] == "Recheck") {
					  		$a = "Temuan NG Memerlukan Recheck FSTK atau Repair Off line";
					  	}
					?>

					<tr>
						<td style="width: 2%; border:1px solid black;">Kategori</td>
						<td style="border:1px solid black; text-align: center;"><?= $a ?></td>
					</tr>

					<tr>
						<td style="width: 2%; border:1px solid black;">Tanggal</td>
						<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($tanggal)) ?></td></td>
					</tr>

				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Klik disini untuk</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/index/form_ketidaksesuaian/print/{{ $id }}">Cek Report Form Ketidaksesuaian Material</a><br>

			<br>
			<span style="font-weight: bold;"><i>Apakah anda ingin menerbitkan CPAR Berdasarkan kasus ini ?</i></span><br>
			<a style="background-color: green; width: 50px;" href="{{ url("index/form_ketidaksesuaian/approveqa/".$id) }}">&nbsp;&nbsp;&nbsp; Ya &nbsp;&nbsp;&nbsp;</a>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a style="background-color: orange; width: 50px;" href="{{ url("index/form_ketidaksesuaian/rejectqa/".$id) }}">&nbsp; Tidak &nbsp;</a><br>

			@elseif($posisi == "sl" || $posisi == "cf" || $posisi == "m")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Penerbitan Form Laporan Ketidaksesuaian<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h1>Komplain : {{$judul}}</h1>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 2%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Kategori</td>
						<td style="border:1px solid black; text-align: center;">{{$kategori}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Tanggal</td>
						<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($tanggal)) ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Section From</td>
						<td style="border:1px solid black; text-align: center;"><?= $sec[0] ?> - <?= $sec[1] ?></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Section To</td>
						<td style="border:1px solid black; text-align: center;"><?= $sect[0] ?> - <?= $sect[1] ?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			
			<?php if($posisi == "m") { ?>
			<a href="http://172.17.128.4/mirai/public/index/form_ketidaksesuaian/response/{{ $id }}">Response Form</a><br>
			<?php } else { ?>
			<a href="http://172.17.128.4/mirai/public/index/form_ketidaksesuaian/verifikasicpar/{{ $id }}">Verifikasi Form</a><br>
			<?php } ?>

			@elseif($posisi == "dept" || $posisi == "deptcf")

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Penanganan Form Laporan Ketidaksesuaian<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Penanganan Form Ketidaksesuaian {{$judul}}</h2>

			<h3>To <?= $sect[0] ?> - <?= $sect[1] ?></h3>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 2%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Kategori</td>
						<td style="border:1px solid black; text-align: center;">{{$kategori}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Tanggal Penanganan</td>
						<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($tanggal_car)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Section From</td>
						<td style="border:1px solid black; text-align: center;"><?= $sec[0] ?> - <?= $sec[1] ?></td>
					</tr>
				</tbody>
			</table>

			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			
			<a href="http://172.17.128.4/mirai/public/index/form_ketidaksesuaian/verifikasicar/{{ $id }}">Form Verification</a><br>

			@elseif ($posisi == "deptm") 

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Penanganan Form Laporan Ketidaksesuaian<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.

			<h2>Penanganan Form Ketidaksesuaian {{$judul}}</h2>

			<h3>Penanganan Telah Dilakukan Oleh Bagian <?= $sect[0] ?> - <?= $sect[1] ?></h3>

			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 2%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Kategori</td>
						<td style="border:1px solid black; text-align: center;">{{$kategori}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Tanggal Form Dibuat</td>
						<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($tanggal)) ?></td></td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Tanggal Penanganan Dibuat</td>
						<td style="border:1px solid black; text-align: center;"><?php echo date('d F Y', strtotime($tanggal_car)) ?></td></td>
					</tr>
				</tbody>
			</table>

			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			
			<a href="http://172.17.128.4/mirai/public/index/form_ketidaksesuaian/verifikasibagian/{{ $id }}">Hasil Penanganan</a><br>

			@endif
			
		</center>
	</div>
</body>
</html>