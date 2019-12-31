<html>
<head>
	<title>YMPI 情報システム</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="shortcut icon" type="image/x-icon" href="{{ public_path() . '/logo_mirai.png' }}" />
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 7pt;
			border: 1px solid black !important;
			border-collapse: collapse;
		}

		.centera{
			text-align: center;
			vertical-align: middle !important;
		}

	</style>
	
	<table class="table table-bordered" style="table-layout: fixed">
		<thead>
			@foreach($qa as $req)
			<tr>
				<td colspan="2" rowspan="2" class="centera" >
					<img width="90" src="{{ public_path() . '/waves.jpg' }}" alt="" style="vertical-align: middle !important">
				</td>
				<td colspan="8" style="text-align: center; vertical-align: middle;font-size: 14px;font-weight: bold">Form Laporan Ketidaksesuaian Material</td>
			</tr>
			<tr>
				<td colspan="1" style="text-align: center; vertical-align: middle;font-size: 11px;">Subject</td>
				<td colspan="4" style="text-align: center; vertical-align: middle;font-size: 11px;">{{ $req->subject }}</td>

				<td colspan="1" style="text-align: center; vertical-align: middle;font-size: 11px;">Tanggal</td>
				<td colspan="2" style="text-align: center; vertical-align: middle;font-size: 11px;"><?php echo date('d F Y', strtotime($req->tanggal)) ?></td>
			</tr>
		</thead>
		<tbody>
			
			<tr>
				<td rowspan="2" colspan="5" style="border: none !important; border-left: 1px solid black !important;font-size: 12px">Section Pelapor : <b>{{ $req->section_from }}</b></td>
				<td rowspan="2" colspan="5" style="border: none !important; border-right: 1px solid black !important;font-size: 12px">Section Yang Dituju : <b>{{ $req->section_to }}</b></td>
			</tr>
			<tr>
				
			</tr>
			<tr>
				<td>Nomor Item</td>
				<td colspan="2">Nama Material</td>
				<td colspan="2">Supplier</td>
				<td colspan="2">Jumlah Cek</td>
				<td colspan="2">Jumlah NG</td>
				<td>% NG</td>
			</tr>
			<?php 
			$jumlahitem = count($items);
			if($jumlahitem != 0) { 

			?>
			@foreach($items as $item)
			<tr>
				<td rowspan="2">{{$item->item}}</td>
				<td rowspan="2" colspan="2">{{$item->item_desc}}</td>
				<td rowspan="2" colspan="2">{{$item->supplier}}</td>
				<td rowspan="2" colspan="2">{{$item->jml_cek}}</td>
				<td rowspan="2" colspan="2">{{$item->jml_ng}}</td>
				<td rowspan="2">{{$item->presentase_ng}}</td>
			</tr>
			<tr></tr>
			@endforeach
			<?php }
			else { 
			?>
			<tr>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2" colspan="2"></td>
				<td rowspan="2" colspan="2"></td>
				<td rowspan="2" colspan="2"></td>
				<td rowspan="2" colspan="2"></td>
				<td rowspan="2"></td>
			</tr>
			<tr></tr>
			<?php } ?>
			<?php if($jumlahitem != 0) { ?> 
			<tr>
				<td colspan="10"><p style="font-size: 12px">Detail Ketidaksesuaian : </p><?= $item->detail ?>  </td>
			</tr>
			<?php } else { ?>
			<tr>
				<td colspan="10">&nbsp;</td>
			</tr>	
			<?php } ?>
			

			<tr>
				<td colspan="3">Target Perhari : <b>{{ $req->target }} Pcs/hari</b></td>
				<td colspan="4">Jumlah Perkiraan Keterlambatan : <b> {{ $req->jumlah }} Pcs</b></td>
				<td colspan="3">Waktu Penanganan Masalah : <b> {{ $req->waktu }} Menit</b></td>
			</tr>
			<tr>
				<td colspan="10">Corrective Action Oleh Section Pelapor</td>
			</tr>
			<tr>
				<td colspan="10"><?= $req->aksi ?></td>
			</tr>
			<tr>
				<td>Pelapor</td>
				<td>Mengetahui</td>
				<td>Mengetahui</td>
				<td>Mengetahui</td>
				<td colspan="6" rowspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td rowspan="2" style="vertical-align: middle;">
				</td>
				<td rowspan="2" style="vertical-align: middle;">
				</td>
				<td rowspan="2" style="vertical-align: middle;">
				</td>
				<td rowspan="2" style="vertical-align: middle;">
				</td>
				<!-- <td colspan="2" rowspan="2" style="vertical-align: middle;">&nbsp;</td> -->
					
				</td>
			</tr>
			<tr></tr>
			<tr>
				<td>Leader</td>
				<td>Foreman</td>
				<td>Chief</td>	
				<td>Manager</td>
				<!-- <td colspan="2"></td> -->
			</tr>
			@endforeach
		</tbody>
	</table>
</body>
</html>