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
			<tr>
				<td colspan="3" class="centera" >
					<img width="100" src="{{ public_path() . '/waves.jpg' }}" alt="" style="vertical-align: middle !important">
				</td>
				<td colspan="5" style="text-align: center; vertical-align: middle;font-size: 14px;font-weight: bold">Laporan Masalah</td>
				<td colspan="2" style="font-size: 9px;">
					Document No : {{$ymmj->nomor}} <br>
					Date : <?php echo date('F d, Y', strtotime($ymmj->tgl_form)) ?><br>
				</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="2" style="border-right: 1px solid black !important;">Perihal : <b></b></td>
				<td colspan="8" style=""><b>{{$ymmj->judul}}</b></td>
			</tr>
			<tr>
				<td colspan="2" style="border-right: 1px solid black !important;">Tanggal Kejadian : <b></b></td>
				<td colspan="3" style="border-right: 1px solid black !important;"><?php echo date('d F Y', strtotime($ymmj->tgl_kejadian)) ?><b></b></td>
				<td colspan="2" style="">Kuantitas : <b></b></td>
				<td colspan="3" style="border-right: 1px solid black !important;">{{$ymmj->qty_cek}}<b></b></td>
			</tr>
			<tr>
				<td colspan="2" style="">Invoice No : <b></b></td>
				<td colspan="3" style=" border-right: 1px solid black !important;">{{$ymmj->no_invoice}}<b></b></td>
				<td colspan="2" style="">NG : <b></b></td>
				<td colspan="3" style=" border-right: 1px solid black !important;">{{$ymmj->qty_ng}}<b></b></td>
			</tr>
			<tr>
				<td colspan="2" style="">Nama Material : <b></b></td>
				<td colspan="3" style=" border-right: 1px solid black !important;">{{$ymmj->material_description}}<b></b></td>
				<td colspan="2" style="">GMC : <b></b></td>
				<td colspan="3" style=" border-right: 1px solid black !important;">{{$ymmj->material_number}}<b></b></td>
			</tr>
			<tr>
				<td colspan="2" style="">Ditemukan di : <b></b></td>
				<td colspan="3" style=" border-right: 1px solid black !important;">{{$ymmj->lokasi}}<b></b></td>
				<td colspan="2" style="">Terjadi di : <b></b></td>
				<td colspan="3" style=" border-right: 1px solid black !important;">YMMJ<b></b></td>
			</tr>
			<tr>
				<td colspan="10">
				Uraian Penyebab :
				<br>
				<?= $ymmj->detail ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">Penanganan</td>
				<td colspan="8">{{$ymmj->penanganan}}</td>
			</tr>
		</tbody>
	</table>
</body>
</html>