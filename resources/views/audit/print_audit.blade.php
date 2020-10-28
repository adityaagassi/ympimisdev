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
			<tr>
				<td colspan="10" style="text-align: center; vertical-align: middle;font-size: 14px;font-weight: bold">AUDIT <?= strtoupper($audit->auditor_jenis) ?></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: center; vertical-align: middle;font-size: 11px;">Lokasi</td>
				<td colspan="4" style="text-align: center; vertical-align: middle;font-size: 11px;">{{$audit->auditor_lokasi}}</td>

				<td colspan="1" style="text-align: center; vertical-align: middle;font-size: 11px;">Tanggal</td>
				<td colspan="3" style="text-align: center; vertical-align: middle;font-size: 11px;"><?php echo date('d F Y', strtotime($audit->auditor_date)) ?></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: center; vertical-align: middle;font-size: 11px;">Auditor</td>
				<td colspan="8" style="text-align: center; vertical-align: middle;font-size: 11px;">{{$audit->auditor_name}}</td>
			</tr>
		</thead>
		<tbody>

			<tr>
				<td colspan="10"><b>Uraian Permasalahan</b></td>
			</tr>
			
			@if($audit->auditor_permasalahan != "")
			<tr>
				<td colspan="10"><?= $audit->auditor_permasalahan ?></td>
			</tr>
			@endif

			@if($audit->auditor_kategori != "") 
			<tr>
				<td colspan="10"><?= $audit->auditor_kategori ?></td>
			</tr>
			@endif
			<tr>
				<td colspan="10"><b>Bukti Temuan (Yang Mendukung Uraian Permasalahan)</b></td>
			</tr>
			
			@if($audit->auditor_bukti != "")
			<tr>
				<td colspan="10"><?= $audit->auditor_bukti ?></td>
			</tr>
			@endif

			<tr>
				<td colspan="10"><b>Penyebab Permasalahan</b></td>
			</tr>
			
			@if($audit->auditor_penyebab != "")
			<tr>
				<td colspan="10"><?= $audit->auditor_penyebab ?></td>
			</tr>
			@endif

		</tbody>
	</table>
	
	<div style="page-break-after: always;"></div>
	<table class="table table-bordered" style="table-layout: fixed">
		<thead>
			<tr>
				<td colspan="10" style="text-align: center; vertical-align: middle;font-size: 14px;font-weight: bold">AUDIT <?= strtoupper($audit->auditor_jenis) ?> - PENANGANAN AUDITEE</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: center; vertical-align: middle;font-size: 11px;">Auditee</td>
				<td colspan="4" style="text-align: center; vertical-align: middle;font-size: 11px;">{{$audit->auditee_name}}</td>

				<td colspan="1" style="text-align: center; vertical-align: middle;font-size: 11px;">Target Penyelesaian</td>
				<td colspan="3" style="text-align: center; vertical-align: middle;font-size: 11px;"><?php echo date('d F Y', strtotime($audit->auditee_due_date)) ?></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="10"><b>Tindakan Perbaikan</b></td>
			</tr>
			
			@if($audit->auditee_perbaikan != "")
			<tr>
				<td colspan="10"><?= $audit->auditee_perbaikan ?></td>
			</tr>
			@endif

			<tr>
				<td colspan="10"><b>Tindakan Pencegahan</b></td>
			</tr>
			
			@if($audit->auditee_pencegahan != "")
			<tr>
				<td colspan="10"><?= $audit->auditee_pencegahan ?></td>
			</tr>
			@endif


			<tr>
				<td colspan="10"><b>Biaya Yang Dikeluarkan Unuk Perbaikan</b></td>
			</tr>
			
			@if($audit->auditee_biaya != "")
			<tr>
				<td colspan="10"><?= $audit->auditee_biaya ?></td>
			</tr>
			@endif

		</tbody>
	</table>
</body>
</html>