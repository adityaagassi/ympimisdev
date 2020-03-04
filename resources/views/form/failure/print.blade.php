<html>
<head>
	<title>YMPI 情報システム</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="shortcut icon" type="image/x-icon" href="{{ public_path() . '/mirai.jpg' }}" />
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
		<!-- <thead>

		</thead> -->

		<?php $lokasi = explode('_',$form_failures->lokasi_kejadian) ?>
		<tbody>
			<tr>
				<td colspan="6" style="vertical-align: middle;font-size: 16px;font-weight: bold">Form Kegagalan & Permasalahan</td>
			</tr>
			<tr>
				<td>Tanggal Kejadian : </td>
				<td colspan="5"><b><?php echo date('d F Y', strtotime($form_failures->tanggal_kejadian)) ?></b></td>
			</tr>
			<tr>
				<td>Lokasi Kejadian : </td>
				<td colspan="5"><b><?= $lokasi[0]; ?> - <?= $lokasi[1] ?></b></td>
			</tr>
			<tr>
				<td colspan="3">Equipment : <b>{{$form_failures->equipment}}</b></td>
				<td colspan="3">Grup Kejadian : <b>{{$form_failures->grup_kejadian}}</b></td>
			</tr>
			<tr>
				<td colspan="6" rowspan="2" style="vertical-align: middle;text-align: center;font-size: 12px"><b><?= strtoupper($form_failures->judul)?></b></td>
			</tr>
			<tr></tr>
			<tr>
				<td colspan="6"><b style="font-size: 9px">Deskripsi Kegagalan / Permasalahan</b> : <?= $form_failures->deskripsi ?></td>
			</tr>
			<tr>
				<td colspan="6"><b style="font-size: 9px">Penanganan / Perbaikan Yang Dilakukan</b> : <?= $form_failures->penanganan ?></td>
			</tr>
			<tr>
				<td colspan="6"><b style="font-size: 9px">Tindakan Supaya Tidak Terjadi Lagi</b> : <?= $form_failures->tindakan ?></td>
			</tr>
		</tbody>
	</table>
</body>
</html>