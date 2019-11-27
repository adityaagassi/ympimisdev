<html>
<head>
	<title>YMPI 情報システム</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
	<link rel="shortcut icon" type="image/x-icon" href="{{ public_path() . '/logo_mirai.png' }}" />
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 6pt;
			border: 1px solid black !important;
			border-collapse: collapse;
		}
		.centera{
			text-align: center;
			vertical-align: middle !important;
		}
		.square {
			height: 5px;
			width: 5px;
			border: 1px solid black;
			background-color: transparent;
		}
	</style>
	
 	
	<table class="table table-bordered">
		@foreach($cars as $car)
		<thead>
			<tr>
				<td colspan="2" rowspan="5" class="centera">
					<img width="80px" src="{{ public_path() . '/waves.jpg' }}" alt="">
				</td>
				<td>Departemen</td>
				<td>{{ $car->department_name }}</td>
				<td colspan="3" rowspan="5" class="centera" style="font-size: 14px;font-weight: bold">CORRECTIVE ACTION REPORT</td>
				<td class="centera">Approved By</td>
				<td class="centera">Approved By</td>
				<td class="centera">Approved By</td>
			</tr>
			<tr>
				<td>Section</td>
				<td>{{ $car->section }}</td>
				<td rowspan="3">&nbsp;</td>
				<td rowspan="3">&nbsp;</td>
				<td rowspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>Location</td>
				<td>{{ $car->lokasi }}</td>
			</tr>
			<tr>
				<td>Date</td>
				<td>{{ $car->tgl_permintaan }}</td>
			</tr>
			<tr>
				<td>No Report</td>
				<td>&nbsp;</td>
				<td class="centera">GM</td>
				<td class="centera">DGM</td>
				<td class="centera">Manager</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="10">Kategori Komplain : {{ $car->kategori }}</td>
			</tr>
			<tr>
				<td rowspan="6" colspan="7">Deskripsi : <?= $car->deskripsi ?></td>
				<td rowspan="6" colspan="3" class="centera" style="font-weight: bold;font-size: 12px">Tinjauan 4M </td>
			</tr>
			<tr></tr>
			<tr></tr><tr></tr><tr></tr><tr></tr>
			<tr>
				<td colspan="10">A. Immediately Action </td>
			</tr>
			<tr>
				<td rowspan="4" colspan="10"><?= $car->tindakan ?></td>
			</tr>
			<tr></tr><tr></tr><tr></tr>
			<tr>
				<td colspan="10">B. Possibility Cause </td>
			</tr>
			<tr>
				<td rowspan="4" colspan="10"><?= $car->penyebab ?></td>
			</tr>
			<tr></tr><tr></tr><tr></tr>
			<tr>
				<td colspan="10">C. Corrective Action </td>
			</tr>
			<tr>
				<td rowspan="4" colspan="10"><?= $car->perbaikan ?></td>
			</tr>
			<tr></tr><tr></tr><tr></tr>
			<tr>
				<td class="centera">Prepared</td>
				<td class="centera">Prepared</td>
				<td class="centera">Checked</td>
				<td class="centera">Checked</td>
				<td colspan="6"></td>
			</tr>
			<tr>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2">&nbsp;</td>
				<td rowspan="2" colspan="6"></td>
			</tr>
			<tr></tr>
			<tr>
				<td>Staff</td>
				<td>Leader</td>
				<td>Foreman</td>
				<td>Chief</td>
				<td colspan="6"></td>
			</tr>
			<tr>
				<td colspan="10"></td>
			</tr>
			<tr>
				<td rowspan="2" colspan="6" class="centera" style="font-weight: bold;font-size: 20px">Verification Result</td>
				<td rowspan="2" class="centera">Dept In Charge</td>
				<td colspan="3" class="centera">QA</td>
			</tr>
			<tr>
				<td class="centera">Verified</td>
				<td class="centera">Checked</td>
				<td class="centera">Approved</td>
			</tr>
			<tr>
				<td colspan="2">Date Of Verification:</td>
				<td>Tanggal</td>
				<td colspan="3">Comment</td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
				<td rowspan="2"></td>
			</tr>
			<tr>
				<td colspan="2">Status</td>
				<td>Open</td>
				<td colspan="3"></td>
			</tr>
			<tr>
				<td colspan="6"></td>
				<td class="centera">Manager</td>
				<td class="centera">QA Staff</td>
				<td class="centera">QA Chief</td>
				<td class="centera">QA Manager</td>
			</tr>
		</tbody>
		@endforeach
	</table>
	<span style="font-size: 8pt">No FM : YMPI/QA/FM/899</span>
</body>
</html>