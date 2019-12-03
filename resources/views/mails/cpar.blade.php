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
				<?php $cpar_no = $datas->cpar_no ?>
				<?php $kategori = $datas->kategori ?>
				<?php $name = $datas->name ?>
				<?php $department = $datas->department_name ?>
				<?php $lokasi = $datas->lokasi ?>
				<?php $tgl_permintaan = $datas->tgl_permintaan ?>
				<?php $tgl_balas = $datas->tgl_balas ?>
				<?php $sumber_komplain = $datas->sumber_komplain ?>
				<?php $posisi = $datas->posisi ?>
				<?php if ($posisi == "bagian"){ 
						$id_car = $datas->id_car;
					}
				?>
			@endforeach

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Penerbitan CPAR {{ $cpar_no }}<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is an automatic notification. Please do not reply to this address.
			<table style="border:1px solid black; border-collapse: collapse;" width="80%">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="width: 2%; border:1px solid black;">Point</th>
						<th style="width: 2%; border:1px solid black;">Content</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 2%; border:1px solid black;">Request Date</td>
						<td style="border:1px solid black; text-align: center;">{{$tgl_permintaan}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Request Due Date</td>
						<td style="border:1px solid black; text-align: center;">{{$tgl_balas}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Location</td>
						<td style="border:1px solid black; text-align: center;">{{$lokasi}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Department</td>
						<td style="border:1px solid black; text-align: center;">{{$department}}</td>
					</tr>
					<tr>
						<td style="width: 2%; border:1px solid black;">Source Of Complaint</td>
						<td style="border:1px solid black; text-align: center;">{{$sumber_komplain}}</td>
					</tr>
				</tbody>
			</table>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<!-- <a href="http://172.17.128.4/mirai/public/index/qc_report/print_cpar/{{ $id }}">See Report Data</a><br> -->
			@if($posisi == "bagian")
				<a href="http://172.17.128.4/mirai/public/index/qc_car/detail/{{$id_car}}">See CAR Data</a><br>
			@else
				<a href="http://172.17.128.4/mirai/public/index/qc_report/verifikasicpar/{{ $id }}">CPAR Verification</a><br>
			@endif
		</center>
	</div>
</body>
</html>