<!DOCTYPE html>
<html>
<head>
	<title>YMPI 情報システム</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
		body{
			font-size: 12px;
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
					<td colspan="6" style="text-align: left;font-size: 11px">Jl. Rembang Industri I/36 Kawasan Industri PIER - Pasuruan</td>
				</tr>
				<tr>
					<td colspan="6" style="text-align: left;font-size: 11px">Phone : (0343) 740290 Fax : (0343) 740291</td>
				</tr>
				<tr>
					<td colspan="10" style="text-align: left;font-size: 11px">Jawa Timur Indonesia</td>
				</tr>

				<tr>
					<td colspan="10" style="text-align: center;font-size: 20px"><b>Detail Of Suspense Payment</b></td>
				</tr>

				<tr>
					<td colspan="4"><br></td>
					<td colspan="6" style="text-align: left;font-size: 12px"><b>Date : <?= date('d-M-y', strtotime($suspend->submission_date)) ?></b>
				</tr>

				<tr>
					<td colspan="9" style="text-align: right;font-size: 11px;"><b>Nomor</b> <b>: {{$id}}</b></td>
				</tr>

				<tr>
					<td colspan="10" style="text-align: right;font-size: 11px;"></td>
				</tr>

				<tr>
					<td colspan="1">&nbsp;</td>
					<td colspan="4" style="border:1px solid black;padding:5px">&nbsp;<b>Remark</b></td>
					<td colspan="4" style="border:1px solid black">&nbsp;&nbsp;&nbsp;<b>Amount</b></td>
					<td colspan="1">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="1">&nbsp;</td>
					<td colspan="4" style="border:1px solid black;height: 50px">&nbsp;&nbsp;&nbsp;<b>{{$suspend->remark}}</b></td>
					<td colspan="4" style="border:1px solid black;height: 50px">&nbsp;&nbsp;&nbsp;<b><?= $suspend->currency ?> &nbsp; <?= number_format($suspend->amount,2,",",".") ?></b></td>
					<td colspan="1">&nbsp;</td>
				</tr>

				<tr>
					<td colspan="10"><br></td>
				</tr>

				<tr>
					<td colspan="10"><br></td>
				</tr>

				<tr>
					<td colspan="1" style="text-align: center;"></td>
					<td colspan="2" style="border: 1px solid black;text-align: center;">Staff</td>
					<td colspan="2" style="border: 1px solid black;text-align: center;">Manager</td>
					<td colspan="2" style="border: 1px solid black;text-align: center;">Director</td>
					<td colspan="2" style="border: 1px solid black;text-align: center;">President Director</td>
					<td colspan="1" style="text-align: center;"></td>
				</tr>
				<tr>
					<td colspan="1" style="text-align: center;"></td>
					<td colspan="2" style="border: 1px solid black;height: 40px;text-align: center;">
							<?= $suspend->created_name ?>
					</td>
					<?php 
						$manager_stat = explode("/",$suspend->status_manager);
						$direktur_stat = explode("/",$suspend->status_direktur);
						$presdir_stat = explode("/",$suspend->status_presdir);
					?>
					<td colspan="2" style="height: 40px;text-align: center">
						@if($manager_stat[0] == "Approved")
							<?= $suspend->manager_name ?>
						@endif
					</td>
					<td colspan="2" rowspan="2" style="border: 1px solid black;height: 40px">
						@if($direktur_stat[0] == "Approved")
							<center><img width="70" src="{{ public_path() . '/files/ttd_pr_po/stempel_pak_arief.jpg' }}" alt="" style="padding: 0"></center>
							<span style="position: absolute;left: 386px;top: 557px;width: 75px;font-size: 8px;color: #f84c32;font-family: arial-narrow"><?= date('d F Y', strtotime($direktur_stat[1])) ?></span>
						@endif
					</td>
					<td colspan="2" rowspan="2" style="border: 1px solid black;height: 40px">
						@if($presdir_stat[0] == "Approved")
						<center><img width="70" src="{{ public_path() . '/files/ttd_pr_po/stempel_pak_hayakawa.jpg' }}" alt="" style="padding: 0"></center>
						<span style="position: absolute;left: 553px;top: 557px;width: 75px;font-size: 8px;color: #f84c32;font-family: arial-narrow"><?= date('d F Y', strtotime($presdir_stat[1])) ?></span>
						@endif
					</td>
					<td colspan="1" style="text-align:center"></td>
				</tr>
				<tr>
					<td colspan="1" style="text-align: center;"></td>
					<td colspan="2" style="border: 1px solid black;text-align: center;"><?= date('d-M-y', strtotime($suspend->submission_date)) ?></td>
					<td colspan="2" style="border: 1px solid black;text-align: center;">
						@if($manager_stat[0] != null)
							<?= date('d-M-y', strtotime($manager_stat[1])) ?>		
						@endif
					</td>
					<td colspan="1" style="text-align: center;"></td>
				</tr>
			</thead>
		</table>
	</header>
	
</body>
</html>