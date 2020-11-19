<html>
<head>
	<title>YMPI 情報システム</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="shortcut icon" type="image/x-icon" href="{{ public_path() . '/logo_mirai.png' }}" />
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">

	<style type="text/css">
		.mstr {
			width: 350px; 
			display: inline-block;
		}

		.text {
			width: 100px; 
			display: inline-block;
		}

		img {
			margin: 0px !important;
		}

		@page { margin: 35px 20px 10px 30px; }


		.cropped {
			width: 100px;
			height: 100px;
			vertical-align: middle;
			text-align: center;
			background-position: center center;
			background-repeat: no-repeat;
		}

		.page-break {
			page-break-after: always;
		}
	</style>
</head>
<body>
	<?php
	include public_path(). "/qr_generator/qrlib.php"; 
	for ($i=1; $i <= count($lists); $i++) { 

		QRcode::png("ST_".$lists[$i-1]->id, public_path().'/stocktaking_qr/ST_'.$lists[$i-1]->id.'.png');

		?>
		<div class='mstr page-break'>
			<table style="width: 95%;  margin-left: auto; margin-right: auto; margin-bottom: 10px">
				<tr>
					<td style="text-align: center; background-color: black; font-size: 16px; color: white" colspan="4"><b>Summary of Counting</b></td>
				</tr>
				
				<tr>
					<td style="text-align: center; font-weight: bold;" colspan="2">Store : <?php echo $lists[$i-1]->store ?></td>
					<td style="text-align: center; font-size: 12px" colspan="2">SubStore : <?php echo $lists[$i-1]->sub_store ?></td>
				</tr>
				<tr><td style="text-align: center;" colspan="4"><img src="{{ public_path() . '/stocktaking_qr/ST_'.$lists[$i-1]->id.'.png' }}" class="cropped"></td></tr>
				<tr><td style="text-align: center; font-size: 8px" colspan="4"><?php echo "ST_".$lists[$i-1]->id ?></td></tr>
				
				<tr>
					<td style="text-align: center; font-weight: bold;" colspan="2"><?php echo $lists[$i-1]->category ?></td>
					<td style="text-align: center; font-size: 13px; font-weight: bold;" colspan="2"><?php echo $lists[$i-1]->material_number."( ".$lists[$i-1]->location." )" ?></td>
				</tr>
				
				<tr>
					<td colspan="4" style="font-size: 13px; text-align: center"><?php echo $lists[$i-1]->material_description ?></td>
				</tr>
				<tr>
					<th style="background-color: black; text-align: center; color:white">PLATE</th>
					<th style="background-color: black; text-align: center; color:white">(GR)</th>
					<th style="background-color: black; text-align: center; color:white"></th>
					<th style="background-color: black; text-align: center; color:white">(GR)</th>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px" rowspan="8">GLOSSY A</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>

				<tr>
					<td style="text-align: center; height: 2px" colspan="4"><br></td>
				</tr>

				<tr>
					<td style="text-align: center; height: 20px" rowspan="4">GLOSSY B</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px" colspan="4">______________________________________________</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px" colspan="3">TOTAL</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>

				<tr>
					<th style="font-size: 12px; background-color: black; text-align: center; color:white">IN SOLUTION</th>
					<th style="background-color: black; text-align: center; color:white">(GR)</th>
					<th style="background-color: black; text-align: center; color:white"></th>
					<th style="background-color: black; text-align: center; color:white">(GR)</th>
				</tr>
				<tr>
					<td style="font-size: 12px; text-align: center; height: 20px">STRIKE SILVER</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">GLOSSY A</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">GLOSSY B</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 2px" colspan="4">______________________________________________</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px" colspan="3">TOTAL</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>

				<tr>
					<th style="background-color: black; text-align: center; color:white">IN WASTE</th>
					<th style="background-color: black; text-align: center; color:white">(GR)</th>
					<th style="background-color: black; text-align: center; color:white"></th>
					<th style="background-color: black; text-align: center; color:white">(GR)</th>
				</tr>
				<tr>
					<td style="font-size: 12px; text-align: center; height: 20px">Ag IN RINSE</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="font-size: 10px; text-align: center; height: 20px">Ag IN HAKURI JIG SILVER</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 2px" colspan="4"><br></td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px" rowspan="13">POWDER + FLAKE</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
					<td style="text-align: center; height: 20px">X1</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 2px" colspan="4">______________________________________________</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 20px" colspan="3">TOTAL</td>
					<td style="text-align: center; height: 20px">[ ___________ ]</td>
				</tr>
			</table>
		</div>
		{{-- <div class="note">
			<p>
				Note : Isi dalam satuan GRAM dengan tulisan yang jelas.
				Gunakan tanda koma (,) untuk desimal dan titik (.) untuk ribuan
			</p>
		</div> --}}
		<?php } ?>	
		{{-- <div class="page-break"></div> --}}

	</body>
</html>

