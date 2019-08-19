<!DOCTYPE html>
<html>
<head>
	<title>Overtime</title>
</head>

<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>

<style type="text/css">
	@page {
		size: A4 portrait;
		margin: 1;
	}
	@media print {
		#tb-collapse {
			background-color: #dddddd !important;
			-webkit-print-color-adjust: exact;
		}

		body {
			font-size: 12pt;
		}
		#tb-collapse tr td{
			padding: 2px;
		}

		.head {
			background-color: #dddddd !important;
		}

		.main2 {
			font-size : 8pt;
		}

		.foot {
			background-color: #e6e8e1 !important;
		}
	}

	body {
		font-family: sans-serif;
		padding: 5px;
	}
	.main {
		width: 100%;
		margin: 5px 0px 20px 0px;
	}
	.main tr th {
		text-align: center;
	}
	.main tr th, .main tr td {
		padding: 8px;
		vertical-align: top;
	}
	.head {
		background-color: #dddddd
	}

	.main2 {
		width: 50%;
		margin: 5px 0px 20px 0px;
	}
	.main2 tr th {
		text-align: center;
	}
	.main2 tr th, .main2 tr td {
		padding: 8px;
		vertical-align: top;
	}

	.foot {
		text-align: right; 
		font-weight: bold; 
		background-color: #e6e8e1;
	}

	table,
	table tr td,
	table tr th {
		page-break-inside: avoid;
	}
</style>
<body>
	<div class="row">
		<div class="col-md-12">
			<b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b>
			<center><h3>LEMBAR PERSETUJUAN LEMBUR LIBUR</h3></center>
			<table class="main" border="1px">
				<tr class="head">
					<th>No</th>
					<th>Tanggal</th>
					<th>ID SPL</th>
					<th>Bagian</th>
					<th>Jumlah <br> (orang)</th>
					<th>Jumlah <br> (jam)</th>
					<th>Keterangan</th>
				</tr>
				<?php $no = 1; $jml = 0; $jam = 0;
				foreach ($anggota as $key) { ?>
					<tr>
						<td><?php echo $no ?></td>
						<td><?php echo date('d-m-Y',strtotime($key->overtime_date)) ?></td>
						<td style="text-align: right;"><?php echo $key->overtime_id ?></td>
						<td><?php echo $key->bagian ?></td>
						<td style="text-align: right;"><?php echo $key->count_member ?></td>
						<td style="text-align: right;"><?php echo $key->total_hour ?></td>
						<td><?php echo $key->reason ?></td>
					</tr>
					<?php 
					$jml += $key->count_member;
					$jam += $key->total_hour;
					$no++; 
				} ?>
				<tr class="foot">
					<td colspan="4">Total</td>
					<td><?php echo $jml ?></td>
					<td><?php echo $jam ?></td>
					<td></td>
				</tr>
			</table>

			<div style="width: 100%">
				<table class="main2" border="1">
					<tr class="head">
						<th width="20%">ID Cost Center</th>
						<th>Cost Center</th>
						<th>Budget</th>
						<th>Aktual</th>
						<th>Diff</th>
					</tr>
					<?php foreach ($cc as $key2) { ?>
						<tr>
							<td style="text-align: right"><?php echo $key2->cost_center ?></td>
							<td><?php echo $key2->cost_center_name ?></td>
							<td style="text-align: right"><?php echo $key2->bdg ?></td>
							<td style="text-align: right"><?php echo $key2->act ?></td>
							<?php $diff = $key2->bdg - $key2->act; ?>
							<td style="text-align: right"><?php echo $diff ?></td>
						</tr>
					<?php } ?>
				</table>
			</div>

			<div style="width: 50%" class="pull-right">
				<table width="100%" border="1px" id="tb-collapse" style="background-color: #dddddd">
					<tr>
						<td width="25%">Diketahui,</td>
						<td width="25%">Disetujui,</td>
						<td width="25%">Disetujui,</td>
						<td width="25%">Diketahui,</td>
					</tr>
					<tr>
						<td>Dept. Manager</td>
						<td>DGM Division</td>
						<td>GM Division</td>
						<td>HR Director</td>
					</tr>
					<tr>
						<td height="92px"></td><td></td><td></td>
					</tr>
					<tr><td style="text-align: left;">tgl. </td><td style="text-align: left;">tgl. </td><td style="text-align: left;">tgl. </td><td style="text-align: left;">tgl. </td></tr>
				</table>
			</div>
		</div>
	</div>

	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		$(document).ready(function() {
			window.print();
		})

	</script>

</body>
</html>