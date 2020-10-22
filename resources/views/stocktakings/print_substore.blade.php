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
			margin-bottom: 30px;
		}
		img {
			margin: 0px !important;
		}


		@page { margin: 120px 30px 10px 30px; }


		.cropped {
			width: 100px;
			height: 100px;
			vertical-align: middle;
			text-align: center;
			background-position: center center;
			background-repeat: no-repeat;
		}
	</style>
</head>
<body>
	<?php
	include public_path(). "/qr_generator/qrlib.php"; 
	for ($i=1; $i <= count($lists); $i++) { 

		QRcode::png("ST_".$lists[$i-1]->id, public_path().'/stk-qr.png');


		if ($i % 2 == 0) $margin = 'style="margin-left:10px"'; else $margin = 'style="margin-right:10px"' ?>
		<div class='mstr' <?php echo $margin; ?>>
			<table style="width: 95%;  margin-left: auto; margin-right: auto; margin-bottom: 10px">
				<tr><td style="text-align: center; background-color: black; font-size: 16px; color: white" colspan="2"><b>Summary of Counting</b></td></tr>
				<tr><td style="text-align: center; font-weight: bold;" colspan="2">Store : <?php echo $lists[$i-1]->store ?></td></tr>
				<tr><td style="text-align: center; font-size: 12px" colspan="2">SubStore : <?php echo $lists[$i-1]->sub_store ?></td></tr>
				<tr><td style="text-align: center;" colspan="2"><img src="{{ public_path() . '/stk-qr.png' }}" class="cropped"></td></tr>
				<tr><td style="text-align: center; font-size: 8px" colspan="2"><?php echo "ST_".$lists[$i-1]->id ?></td></tr>
				<tr><td style="text-align: center" colspan="2"><?php echo $lists[$i-1]->category ?></td></tr>
				<tr><td style="text-align: center" colspan="2"><?php echo $lists[$i-1]->material_number."( ".$lists[$i-1]->location." )" ?></td></tr>
				<tr><td colspan="2" style="font-size: 13px; text-align: center"><?php echo $lists[$i-1]->material_description ?></td></tr>
				<tr><td colspan="2"><?php echo "uom : ".$lists[$i-1]->bun; ?></td></tr>

				<tr>
					<th style="background-color: black; text-align: center; color:white">Hitung</th>
					<th style="background-color: black; text-align: center; color:white">Revisi</th>
				</tr>
				<tr>
					<td style="text-align: center; height: 30px">[ _______ ] X [ ______ ]</td>
					<td style="text-align: center; height: 30px">[ _______ ] X [ ______ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 30px">[ _______ ] X [ ______ ]</td>
					<td style="text-align: center; height: 30px">[ _______ ] X [ ______ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 30px">[ _______ ] X [ ______ ]</td>
					<td style="text-align: center; height: 30px">[ _______ ] X [ ______ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 30px">[ _______ ] X [ ______ ]</td>
					<td style="text-align: center; height: 30px">[ _______ ] X [ ______ ]</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 30px">[ _______ ] X [ ______ ]</td>
					<td style="text-align: center; height: 30px">[ _______ ] X [ ______ ]</td>
				</tr>
			</table>
		</div>
		<?php if ($i % 2 == 0) echo '<br><br>'; ?>
	<?php } ?>	
</body>
</html>