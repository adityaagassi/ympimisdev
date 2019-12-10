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
				<?php $alasan = $datas->alasan ?>				
			@endforeach

			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">CPAR {{ $cpar_no }} Tidak Disetujui<br>(Last Update: {{ date('d-M-Y H:i:s') }})</p>
			This is 
			an automatic notification. Please do not reply to this address.
			<br>
			<h3>Alasan CPAR Tidak Disetujui :<h3>
			<h3>
				{{ $alasan }}	
			</h3>
			<br>
			<span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
			<a href="http://172.17.128.4/mirai/public/index/qc_report/update/{{ $id }}">Detail CPAR</a>
		</center>
	</div>
</body>
</html>