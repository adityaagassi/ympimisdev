<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="shortcut icon" type="image/x-icon" href="{{ public_path() . '/logo_mirai.png' }}" />
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
	<style type="text/css">
		.cropped {
			width: 300px;
			height: 300px;
			vertical-align: middle;
			text-align: center;
		}

		table tr td{
			border: none;
			border-collapse: collapse;
			vertical-align: middle;
		}

		table {

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


		<table style="width: 100%; text-align: center;" class="page-break">
			<tbody style="font-weight: bold;">
				@php
				QRcode::png($data[$i]->store, public_path().'/qr_code'.$data[$i]->store.'.png');
				@endphp
				<tr>
					<td style="padding: 0px; vertical-align: middle; font-size: 50px;">
						{{ $data[$i]->area }}
					</td>
				</tr>				
				<tr>
					<td style="padding: 0px; vertical-align: middle; font-size: 85px;">
						{{ $data[$i]->store }}
					</td>
				</tr>
				<tr>
					<td style="padding-top: 5%; text-align: center; vertical-align: middle;">
						<img src="<?php echo public_path() . '/qr_code'.$data[$i]->store.'.png'; ?>" class="cropped">
					</td>
				</tr>
			</tbody>
		</table>

		@php
	}	
	@endphp



</body>
</html>