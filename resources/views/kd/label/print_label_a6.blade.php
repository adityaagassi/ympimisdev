<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
</head>
<body >
	<style type="text/css">
		table tr td,
		table tr th{
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
			margin-top: 13%; 
			margin-bottom: 0px; 
			vertical-align: middle;
		}

		.material_description, .material_number, .xy, .mj, .quantity, .date {
			font-size: 20pt;
			font-weight: bold;
		}

		#barcode {
			width: 120px;
			height: 120px;
		}

		#kd_number {
			font-weight: bold;
		}


	</style>

	@php
	include public_path(). "/qr_generator/qrlib.php";

	QRcode::png($data->kd_number, public_path().'/qr_code'.$data->kd_number.'.png');

	@endphp

	<table class="table table-bordered">
		<tbody>
			<tr> 
				<td colspan="3" style="vertical-align: middle; text-align: center; height: 80px;" class="material_description">{{ $data->material_description }}</td>
			</tr>

			<tr>
				<td colspan="2" class="material_number">{{ $data->material_number }}</td>
				<td rowspan="5" style="vertical-align: middle; text-align: center;">
					<img id="barcode" src="{{ public_path() . '/qr_code'.$data->kd_number.'.png' }}">
					<p id="kd_number">{{ $data->kd_number }}</p>
				</td>
			</tr>

			<tr>
				<td width="25%" class="xy">XY</td>
				<td width="70%" class="xy">{{ $data->xy }}</td>
			</tr>

			<tr>
				<td width="25%" class="mj">MJ</td>
				<td width="70%" class="mj">{{ $data->mj }}</td>
			</tr>

			<tr>
				<td width="25%" class="quantity">QTY</td>
				<td width="70%" class="quantity">{{ $data->quantity }} PC(s)</td>
			</tr>

			<tr>
				<td width="25%" class="date">DATE</td>
				<td width="70%" class="date">{{ $data->date }}</td>
			</tr>
		</tbody>
	</table>

</body>
</html>