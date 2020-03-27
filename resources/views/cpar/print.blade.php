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

		@page { margin: 100px 50px; }
        .header { position: fixed; left: 0px; top: -100px; right: 0px; height: 100px; text-align: center; }
        .footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 50px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }

	</style>

	<div class="footer">
        Page <span class="pagenum"></span>
    </div>
	
	<table class="table table-bordered" style="table-layout: fixed">
		<thead>
			@foreach($qa as $req)
			<tr>
				<td colspan="2" rowspan="2" class="centera" >
					<img width="90" src="{{ public_path() . '/waves.jpg' }}" alt="" style="vertical-align: middle !important">
				</td>
				<td colspan="8" style="text-align: center; vertical-align: middle;font-size: 14px;font-weight: bold">Form Laporan Ketidaksesuaian</td>
			</tr>
			<tr>
				<td colspan="1" style="text-align: center; vertical-align: middle;font-size: 11px;">Subject</td>
				<td colspan="4" style="text-align: center; vertical-align: middle;font-size: 11px;">{{$req->judul}} (<b>{{ $req->kategori }}</b>)</td>

				<td colspan="1" style="text-align: center; vertical-align: middle;font-size: 11px;">Tanggal</td>
				<td colspan="2" style="text-align: center; vertical-align: middle;font-size: 11px;"><?php echo date('d F Y', strtotime($req->tanggal)) ?></td>
			</tr>
		</thead>
		<tbody>
			<?php 
				$secfrom = explode('_',$req->section_from);
				$secto = explode('_',$req->section_to);
			?>

			<tr>
				<td rowspan="2" colspan="5" style="border: none !important; border-left: 1px solid black !important;font-size: 11px">Section Pelapor :<br> <b><?= $secfrom[0]; ?> - <?= $secfrom[1] ?></b></td>
				<td rowspan="2" colspan="5" style="border: none !important; border-right: 1px solid black !important;font-size: 11px">Section Yang Dituju : <br><b><?= $secto[0]; ?> - <?= $secto[1] ?></b></td>
			</tr>
			<tr>
				
			</tr>
			<tr>
				<td colspan="2">Nomor Item</td>
				<td colspan="2">Nama Material</td>
				<td colspan="2">Jumlah Cek</td>
				<td colspan="2">Jumlah NG</td>
				<td colspan="2">% NG</td>
			</tr>
			<?php 
			$jumlahitem = count($items);
			if($jumlahitem != 0) { 

			?>
			@foreach($items as $item)
			<tr>
				<td rowspan="2" colspan="2">{{$item->item}}</td>
				<td rowspan="2" colspan="2">{{$item->item_desc}}</td>
				<td rowspan="2" colspan="2">{{$item->jml_cek}} Pcs</td>
				<td rowspan="2" colspan="2">{{$item->jml_ng}} Pcs</td>
				<td rowspan="2" colspan="2">{{$item->presentase_ng}} Persen</td>
			</tr>
			<tr></tr>
			@endforeach
			<?php }
			else { 
			?>
			<tr>
				<td rowspan="2" colspan="2">-</td>
				<td rowspan="2" colspan="2">-</td>
				<td rowspan="2" colspan="2">-</td>
				<td rowspan="2" colspan="2">-</td>
				<td rowspan="2" colspan="2">-</td>
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
				<td colspan="3">Target Perhari : 
					<b>
					   @if($req->target != "") 
						{{ $req->target }} Pcs/hari
					   @else
					    -
					   @endif
					</b>
				</td>
				<td colspan="4">Jumlah Perkiraan Keterlambatan : 
					<b> 
					   @if($req->jumlah != "") 
						{{ $req->jumlah }} Pcs
					   @else
					    -
					   @endif
					</b>
				</td>
				<td colspan="3">Waktu Penanganan Masalah : 
					<b>
					@if($req->waktu != "") 
						{{ $req->waktu }} Menit
					@else
					    -
					@endif
					</b>
				</td>
			</tr>
			<tr>
				<td colspan="10">Corrective Action Oleh Section Pelapor</td>
			</tr>
			@if($req->aksi != "") 
			<tr>
				<td colspan="10"><?= $req->aksi ?></td>
			</tr>
			@else
			<tr>
				<td colspan="10"> - </td>
			</tr>
			@endif			
			<tr>
				<td>Pelapor</td>
				<td>Mengetahui</td>
				<td>Mengetahui</td>
				<td colspan="7" rowspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td rowspan="2" style="vertical-align: middle;">
					@if($req->posisi == "sl" || $req->posisi == "cf" || $req->posisi == "m" || $req->posisi == "qa")
	                  {{$sl}}
	                @elseif($req->approvalcf == "Approved" || $req->approvalm == "Approved")
	                  {{$sl}}
	                @else

	                @endif
				</td>
				<td rowspan="2" style="vertical-align: middle;">
					@if($req->approvalcf == "Approved" || $req->approvalm == "Approved")
	                  {{$cf}}
	                @else

	                @endif
				</td>
				<td rowspan="2" style="vertical-align: middle;">
					@if($req->approvalm == "Approved")
	                  {{$m}}
	                @else
	                  
	                @endif
				</td>
			</tr>
			<tr></tr>
			<tr>
				<td>Leader / Staff</td>
	            <td>Foreman / Chief</td>
	            <td>Manager</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	@if($cpar->posisi != "sl" && $cpar->posisi != "cf" && $cpar->posisi != "m")
	<div style="page-break-after: always;"></div>
	<table class="table table-bordered" style="table-layout: fixed">
		<thead>
			<tr>

				<td colspan="8" style="text-align: center; vertical-align: middle;font-size: 12px;font-weight: bold">
					Penanganan / Keputusan Oleh Departemen Terkait
				</td>
				<td colspan="2" style="text-align: center; vertical-align: middle;font-size: 12px;font-weight: bold">
					<?= date('d F Y', strtotime($cpar->tanggal_car))?>
				</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="10"><b>Deskripsi Permasalahan</b></td>
			</tr>
			<tr>
				<td colspan="10"><?= $cpar->deskripsi_car ?></td>
			</tr>
			<tr>
				<td colspan="10"><b>Penanganan</b></td>
			</tr>
			<tr>
				<td colspan="10"><?= $cpar->penanganan_car ?></td>
			</tr>
			<tr>
				<td>Pelapor</td>
				<td>Mengetahui</td>
				<td>Mengetahui</td>
				<td colspan="7" rowspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td rowspan="2" style="vertical-align: middle;">
					@if($cpar->posisi == "dept")
	                  {{$pic}}
	                @elseif($cpar->approvalcf_car == "Approved" || $cpar->approvalm_car == "Approved")
	                  {{$pic}}
	                @else

	                @endif
				</td>
				<td rowspan="2" style="vertical-align: middle;">
					@if($cpar->approvalcf_car == "Approved" || $cpar->approvalm_car == "Approved")
					  {{$cfcar}}
	                @else

	                @endif
				</td>
				<td rowspan="2" style="vertical-align: middle;">
					@if($cpar->approvalm_car == "Approved")
					  {{$mcar}}
	                @else
	                  
	                @endif
				</td>
			</tr>
			<tr></tr>
			<tr>
				<td>Leader / Staff</td>
	            <td>Foreman / Chief</td>
	            <td>Manager</td>
			</tr>
		</tbody>
	</table>
	@endif
</body>
</html>