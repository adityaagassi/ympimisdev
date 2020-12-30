<!DOCTYPE html>
<html>
<head>
	<title>YMPI 情報システム</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
		body{
			font-size: 10px;
		}

		#isi > thead > tr > td {
			text-align: center;
		}

		#isi > tbody > tr > td {
			text-align: left;
			padding-left: 5px;
		}

		.centera{
			text-align: center;
			vertical-align: middle !important;
		}

		.line{
		   width: 100%; 
		   text-align: center; 
		   border-bottom: 1px solid #000; 
		   line-height: 0.1em;
		   margin: 10px 0 20px;  
		}

		.line span{
		   background:#fff; 
		   padding:0 10px;
		}

		@page { }
        .footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 200px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }
	</style>
</head>

<body>
	<header>

		<table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;" >
			<thead>
				<tr>
					<td colspan="10" style="font-weight: bold;font-size: 13px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td colspan="10">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="6" style="text-align: left;font-size: 11px">Jl. Rembang Industri I/36 Kawasan Industri PIER - Pasuruan</td>
					<td colspan="1" style="text-align: left;font-size: 11px;width: 16%"><b>Nomor Tanda Terima</b></td>
					<td colspan="3" style="text-align: left;font-size: 11px"><b>: {{$invoice->id}}</b></td>
				</tr>
				<tr>
					<td colspan="6" style="text-align: left;font-size: 11px">Phone : (0343) 740290 Fax : (0343) 740291</td>
					<td colspan="1" style="text-align: left;font-size: 11px;width: 16%"><b>Tanggal</b></td>
					<td colspan="3" style="text-align: left;font-size: 11px"><b>: <?= date('d-M-y', strtotime($invoice->invoice_date)) ?></b></td>
				</tr>
				<tr>
					<td colspan="" style="text-align: left;font-size: 11px">Jawa Timur Indonesia</td>
				</tr>

				<tr>
					<td colspan="10"><br></td>
				</tr>

				<tr>
					<td colspan="10" style="text-align: center;font-size: 30px"><b><u>Tanda Terima</u></b></td>
				</tr>
			</thead>
		</table>
	</header>
	
</body>
</html>