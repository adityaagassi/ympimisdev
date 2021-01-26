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
			margin-top: 5%; 
			margin-bottom: 0px; 
			vertical-align: middle;
		}

		.txt {
			font-size: 20pt;
			text-align: left;
			vertical-align: middle;
			/*font-weight: bold;*/
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

	QRcode::png($data[0]->kd_number, public_path().'/qr_code'.$data[0]->kd_number.'.png');

	@endphp

	<table class="table table-bordered">
		<tbody>

			<tr> 
				<td width="25%" class="txt">GMC</td>
				<td colspan="2" class="txt">{{ $data[0]->material_number }}</td>	
			</tr>

			<tr> 
				<td width="25%" style="text-align: left; vertical-align: middle; height: 80px;" class="txt">Part</td>
				<td colspan="2" style="text-align: left; vertical-align: middle; height: 80px;" class="txt">{{ $data[0]->material_description }}</td>
			</tr>

			<tr>
				<td width="25%" class="txt">Q'ty</td>
				<td width="35%" class="txt">{{ $data[0]->quantity }} PC(s)</td>

				<td rowspan="5" style="vertical-align: middle; text-align: center;">
					<img id="barcode" src="{{ public_path() . '/qr_code'.$data[0]->kd_number.'.png' }}">
					<p id="kd_number">{{ $data[0]->kd_number }}</p>
				</td>
			</tr>

			<tr>
				<td width="25%" class="txt">Box No.</td>
				<td width="35%" class="txt"></td>
			</tr>

			<tr>
				<td width="25%" class="txt">Date</td>
				<td width="35%" class="txt">{{ $data[0]->date_code }}</td>
			</tr>

			<tr>
				<td width="25%" class="txt">YMMJ</td>
				<td width="35%" class="txt">{{ $data[0]->mj }}</td>
			</tr>

			<tr>
				<td width="25%" class="txt">XY</td>
				<td width="35%" class="txt">{{ $data[0]->xy }}</td>
			</tr>

		</tbody>
	</table>

</body>
</html>