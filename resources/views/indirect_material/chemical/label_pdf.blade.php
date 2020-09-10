<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="shortcut icon" type="image/x-icon" href="{{ public_path() . '/logo_mirai.png' }}" />
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
	<style type="text/css">
		.cropped {
			width: 220px;
			height: 200px;
			vertical-align: middle;
			text-align: center;
		}

		table tr td{
			border: 3px solid black !important;
			border-collapse: collapse;
			vertical-align: middle;
		}

		@page {
			margin: 50px; 
			vertical-align: middle;
		}

		.page-break {
		    page-break-after: always;
		}


	</style>
</head>

<body style="text-transform: uppercase; color: #000;">


	@php
	include public_path(). "/qr_generator/qrlib.php";

	for ($i=0; $i < count($data); $i++) { 	

		@endphp

		@php
		QRcode::png($data[$i]->qr_code, public_path().'/qr_code.png');
		@endphp

		<table style="width: 100%; margin-top: 5%;">
			<tbody style="font-weight: bold;">
				<tr>
					<td style="padding: 0px 5px 0px 5px; vertical-align: middle; font-size: 25px; width: 20%">GMC</td>
					<td style="padding: 0px 5px 0px 5px; vertical-align: middle; font-size: 50px; width: 50%">{{ $data[$i]->material_number }}</td>
					<td style="vertical-align: middle; font-size: 30px; width: 30%; text-align: center;">
						{{ $data[$i]->month }}
					</td>
				</tr>
				<tr>
					<td style="padding: 0px 5px 0px 5px; vertical-align: middle; font-size: 25px;">Desc.</td>
					<td style="padding: 0px 5px 0px 5px; vertical-align: middle; font-size: 26px;">{{ $data[$i]->material_description }}</td>
					<td rowspan="3" style="text-align: center; vertical-align: middle;">
						<img src="{{ public_path() . '/qr_code.png' }}" class="cropped">
						<span style="font-size: 10px;">{{ $data[$i]->qr_code }}</span>
					</td>
				</tr>
				<tr>
					<td style="padding: 0px 5px 0px 5px; font-size: 25px; vertical-align: middle;">Tgl Masuk</td>
					<td style="padding: 0px 5px 0px 5px; font-size: 50px; vertical-align: middle;">{{ $data[$i]->masuk }}</td>
				</tr>
				<tr>
					<td style="padding: 0px 5px 0px 5px; font-size: 25px; vertical-align: middle;">Tgl Exp</td>
					<td style="padding: 0px 5px 0px 5px; font-size: 50px; vertical-align: middle;">{{ $data[$i]->exp }}</td>
				</tr>
			</tbody>
		</table>

		<br>
		<br>
		<br>
		<br>
		<br>
		<div style="border-bottom: 2px dashed #000;"></div>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>

		<table style="width: 100%;" class="page-break">
			<tbody style="font-weight: bold;">
				<tr>
					<td style="padding: 0px 5px 0px 5px; vertical-align: middle; font-size: 25px; width: 20%">GMC</td>
					<td style="padding: 0px 5px 0px 5px; vertical-align: middle; font-size: 50px; width: 50%">{{ $data[$i]->material_number }}</td>
					<td style="vertical-align: middle; font-size: 30px; width: 30%; text-align: center;">
						{{ $data[$i]->month }}
					</td>
				</tr>
				<tr>
					<td style="padding: 0px 5px 0px 5px; vertical-align: middle; font-size: 25px;">Desc.</td>
					<td style="padding: 0px 5px 0px 5px; vertical-align: middle; font-size: 26px;">{{ $data[$i]->material_description }}</td>
					<td rowspan="3" style="text-align: center; vertical-align: middle;">
						<img src="{{ public_path() . '/qr_code.png' }}" class="cropped">
						<span style="font-size: 10px;">{{ $data[$i]->qr_code }}</span>
					</td>
				</tr>
				<tr>
					<td style="padding: 0px 5px 0px 5px; font-size: 25px; vertical-align: middle;">Tgl Masuk</td>
					<td style="padding: 0px 5px 0px 5px; font-size: 50px; vertical-align: middle;">{{ $data[$i]->masuk }}</td>
				</tr>
				<tr>
					<td style="padding: 0px 5px 0px 5px; font-size: 25px; vertical-align: middle;">Tgl Exp</td>
					<td style="padding: 0px 5px 0px 5px; font-size: 50px; vertical-align: middle;">{{ $data[$i]->exp }}</td>
				</tr>
			</tbody>
		</table>
		
		@php
	}	
	@endphp

</body>
</html>