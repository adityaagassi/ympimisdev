<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
</head>
<body >
	<style type="text/css">
		.table tr td,
		.table tr th{
			border: 2px solid black !important;
			border-collapse: collapse;
			vertical-align: middle;
			text-align: center;
		}

		table.table > tbody > tr > td{
			padding-top: 0px;
			padding-bottom: 0px;
		}

		@page {
			margin-top: 5%; 
			margin-bottom: 0px; 
			margin-left: 5%;
			margin-right: 5%;
			vertical-align: middle;
		}

		.header {
			font-size: 12pt;
			font-weight: bold;
		}

		.text {
			font-size: 11pt;
		}

		.desc {
			font-size: 10pt;
			text-align: left;
		}

		#barcode {
			width: 70px;
			height: 70px;
		}

		#kd_number {
			font-size: 8pt;
		}


	</style>

	@php
	include public_path(). "/qr_generator/qrlib.php";

	QRcode::png($kd_number, public_path().'/qr_code'.$kd_number.'.png');

	@endphp

	<table class="table table-bordered" style="width: 300px; margin-bottom: 10px;">
		<tbody>

			<tr>
				<td width="10px" class="header">No</td>
				<td width="30px" class="header">GMC</td>
				<td width="170px" class="header">Desc</td>
				<td width="20px" class="header">Qty</td>
				<td width="20px" class="header">Uom</td>
				<td width="30px" class="header">XY</td>
			</tr>

			@php $i = 0; @endphp
			@foreach($knock_down_details as $tr)
			<tr>
				<td class="text">{{ ++$i }}</td>
				<td class="text">{{ $tr->material_number }}</td>
				<td class="desc">{{ $tr->material_description }}</td>
				<td class="text">{{ $tr->quantity }}</td>
				<td class="text">{{ $tr->base_unit }}</td>
				<td class="text">{{ $tr->material_number }}</td>
			</tr>
			@endforeach

		</tbody>
	</table>

	<table style="border: none; margin-bottom: 0px; width: 100%">
		<tbody>
			<tr>
				<td height="110px" style="text-align: left;">
					Stuffing Date :
					<br>
					{{ date('d F Y', strtotime($knock_down_details[0]->st_date)) }}
				</td>
				<td height="110px" style="vertical-align: middle; text-align: right;">
					<img id="barcode" src="{{ public_path() . '/qr_code'.$kd_number.'.png' }}">
					<p id="kd_number">{{ $kd_number }}&nbsp;&nbsp;</p>
				</td>
			</tr>
		</tbody>
	</table>

</body>
</html>