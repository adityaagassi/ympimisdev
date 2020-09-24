<!DOCTYPE html>
<html>
<head>
	<title>YMPI 情報システム</title>
	<!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> -->
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, user-scalable=yes, initial-scale=1.0" name="viewport">
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

		/*@font-face {
	      font-family: 'Firefly Sung';
	      font-style: normal;
	      font-weight: 400;
	    }
	    * {
	      font-family: Firefly Sung, DejaVu Sans, sans-serif;
	    }*/

	    * {
	      font-family: arial;
	    }

		@page { }
        .footer { position: fixed; left: 0px; bottom: 50px; right: 0px; height: 150px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }
	</style>
</head>

<body>
	<header>
		<table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: left;">
			<thead>
				<tr>
					<td colspan="10" style="font-weight: bold;font-size: 13px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td colspan="10"><br></td>
				</tr>				
				<tr>
					<td colspan="3">&nbsp;</td>
					<td colspan="4" style="text-align: center;font-weight: bold;font-size: 16px">PURCHASE REQUISITION FORM</td>
					<td colspan="2" style="text-align: right;font-size: 12px">No:</td>
					<td colspan="1" style="text-align: right;font-size: 14px;font-weight: bold">{{ $pr[0]->no_pr }}</td>
				</tr>
				<tr>
					<td colspan="10"><br></td>
				</tr>	
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Department</td>
					<td colspan="8" style="font-size: 12px;">: {{ $pr[0]->department }}</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Section</td>
					@if($pr[0]->section != null)
					<td colspan="8" style="font-size: 12px;">: {{ $pr[0]->section }}</td>
					@else
					<td colspan="8" style="font-size: 12px;">: {{ $pr[0]->department }}</td>
					@endif
				</tr>
				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Date Of Submission</td>
					<td colspan="8" style="font-size: 12px;">: <?= date('d-M-Y', strtotime($pr[0]->submission_date)) ?></td>
				</tr>

				<tr>
					<td colspan="2" style="font-size: 12px;width: 22%">Budget</td>
					<td colspan="8" style="font-size: 12px;">: {{ $pr[0]->no_budget }}</td>
				</tr>

				<tr>
					<td colspan="10"><br></td>
				</tr>

			</thead>
		</table>
	</header>

	@if($pr[0]->receive_date != null)
		<img width="120" src="{{ public_path() . '/files/ttd_pr_po/received.jpg' }}" alt="" style="padding: 0;position: absolute;top: 55px;left: 540px">
		<span style="position: absolute;width: 100px;font-size: 12px;font-weight: bold;font-family: arial-narrow;top:88px;left: 575px;color:#c34354"><?= date('d-M-y', strtotime($pr[0]->receive_date)) ?></span>
	@endif
	<main>
		<table style="width: 100%; font-family: arial; border-collapse: collapse; " id="isi">
			<thead>
				<tr style="font-size: 12px">
					<td colspan="1" style="padding:10px;height: 15px; width:1%; background-color: yellow; font-weight: bold; border: 1px solid black;">No</td>
					<td colspan="1" style="width:3%; background-color: yellow; font-weight: bold; border: 1px solid black;">Item Code</td>
					<td colspan="1" style="width:8%; background-color: yellow; font-weight: bold; border: 1px solid black;">Description & Specification</td>
					<td colspan="1" style="width:2%; background-color: yellow; font-weight: bold; border: 1px solid black;">Stock WIP</td>
					<td colspan="1" style="width:4%; background-color: yellow; font-weight: bold; border: 1px solid black;">Request Date</td>
					<td colspan="1" style="width:2%; background-color: yellow; font-weight: bold; border: 1px solid black;">Qty</td>
					<td colspan="1" style="width:3%; background-color: yellow; font-weight: bold; border: 1px solid black;">Currency</td>
					<td colspan="1" style="width:3%; background-color: yellow; font-weight: bold; border: 1px solid black;">Unit Price</td>
					<td colspan="1" style="width:3%; background-color: yellow; font-weight: bold; border: 1px solid black;">Amount</td>
					<td colspan="1" style="width:4%; background-color: yellow; font-weight: bold; border: 1px solid black;">Last Order</td>
				</tr>
			</thead>
			<tbody>
				<?php $no = 1; 
				
				$totalidr = 0;
				$totaljpy = 0;
				$totalusd = 0;

				foreach($pr as $purchase_r) {

				if($purchase_r->item_currency == "IDR"){
					$totalidr += $purchase_r->item_amount;
				}
				else if($purchase_r->item_currency == "JPY"){
					$totaljpy += $purchase_r->item_amount;
				}
				else if($purchase_r->item_currency == "USD"){
					$totalusd += $purchase_r->item_amount;
				}

				?>

				<tr>
					<td colspan="1" style="height: 26px; border: 1px solid black;text-align: center;padding: 0">{{ $no }}</td>
					<td colspan="1" style="border: 1px solid black;">{{ $purchase_r->item_code }}</td>
					<td colspan="1" style="border: 1px solid black;">
						{{ $purchase_r->item_desc }} 
						@if($purchase_r->item_spec != null)
						- {{ $purchase_r->item_spec }}
						@endif
					</td>
					<td colspan="1" style="border: 1px solid black;">{{ $purchase_r->item_stock }}</td>
					<td colspan="1" style="border: 1px solid black;"><?= date('d-M-y', strtotime($purchase_r->item_request_date)) ?></td>
					<td colspan="1" style="border: 1px solid black;">{{ $purchase_r->item_qty }} {{ $purchase_r->item_uom }}</td>
					<td colspan="1" style="border: 1px solid black;">{{ $purchase_r->item_currency }}</td>
					@if($purchase_r->item_currency == "IDR")
					<td colspan="1" style="border: 1px solid black;"><?= number_format($purchase_r->item_price,0,"",".") ?></td>
					@else
					<td colspan="1" style="border: 1px solid black;"><?= $purchase_r->item_price ?></td>
					@endif

					@if($purchase_r->item_currency == "IDR")
					<td colspan="1" style="border: 1px solid black;"><?= number_format($purchase_r->item_amount,0,"",".") ?></td>
					@else
					<td colspan="1" style="border: 1px solid black;"><?= $purchase_r->item_amount ?></td>
					@endif

					<td colspan="1" style="border: 1px solid black;">
						@if($purchase_r->last_order != null) 
						<?= date('d-M-Y', strtotime($purchase_r->last_order)) ?>
						@else
						-
						@endif
					</td>
				</tr>
				<?php $no++; } ?>
				<tr>
					<td colspan="10">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="5" rowspan="2" style="font-size: 12px;font-weight: bold">Exchange Rate :</td>

					<th colspan="1" style="background-color: yellow; border: 1px solid black;font-size: 12px;text-align: center;">Cur</th>
					<th colspan="2" style="background-color: yellow; border: 1px solid black;font-size: 12px;text-align: center;">Original Amount</th>
					<th colspan="2" style="background-color: yellow; border: 1px solid black;font-size: 12px;text-align: center;">Amount US$</th>
				</tr>
				<tr>

					<td colspan="1" style="border: 1px solid black;font-size: 12px;text-align: center;">IDR</td>
					<td colspan="2" style="border: 1px solid black;font-size: 12px;text-align: center;">Rp. <?= number_format($totalidr,0,"",".") ?></td>
					<td colspan="2" style="border: 1px solid black;font-size: 12px;text-align: center;">
						<?php 
							$totalkonversiidr = $totalidr / $rate[0]->rate;
						?>		
						$ <?= number_format($totalkonversiidr,0,".","") ?>
					</td>
				</tr>
				<tr>

					<td colspan="5" style="font-size: 12px;">{{ $rate[0]->currency }} = {{ $rate[0]->rate }} / USD</td>
					<td colspan="1" style="border: 1px solid black;font-size: 12px;text-align: center;">USD</td>
					<td colspan="2" style="border: 1px solid black;font-size: 12px;text-align: center;">$ <?= number_format($totalusd,0,".","") ?></td>
					<td colspan="2" style="border: 1px solid black;font-size: 12px;text-align: center;">$ <?= number_format($totalusd,0,".","") ?></td>
				</tr>		
				<tr>
					<td colspan="5" style="font-size: 12px;">{{ $rate[1]->currency }} = {{ $rate[1]->rate }} / USD</td>

					<td colspan="1" style="border: 1px solid black;font-size: 12px;text-align: center;">JPY</td>
					<td colspan="2" style="border: 1px solid black;font-size: 12px;text-align: center;">¥ <?= number_format($totaljpy,0,"",".") ?></td>
					<td colspan="2" style="border: 1px solid black;font-size: 12px;text-align: center;">
						<?php 
							$totalkonversijpy = $totaljpy / $rate[1]->rate;
						?>		
						$ <?= number_format($totalkonversijpy,0,".","") ?>
							
					</td>
				</tr>
				<tr>
					<td colspan="10">
						&nbsp;
					</td>
				</tr>
				
			</tbody>
		</table>
	</main>
	<footer>
		<div class="footer">
			<table style="width: 50%; font-family: arial; border-collapse: collapse;">
				<thead>
					<tr style="font-size: 12px">
						<th colspan="2" style="background-color:yellow; text-align:center; padding:10px; height: 15px; font-weight: bold; border: 1px solid black;">Beg Balance</th>
						<th colspan="2" style="background-color:yellow; text-align:center; padding:10px; height: 15px; font-weight: bold; border: 1px solid black;">Amount</th>
						<th colspan="2" style="background-color:yellow; text-align:center; padding:10px; height: 15px; font-weight: bold; border: 1px solid black;">End Balance</th>
					</tr>
				</thead>
				<tbody>

					<?php

					$totalamount = 0;

					foreach ($pr as $pr_budget) {
						$totalamount += $pr_budget->amount;
					}

					?>
					<tr>
						<td colspan="2" style="text-align:center; font-weight: bold; padding:5px; height: 15px; border: 1px solid black;">$ <?= number_format($pr[0]->beg_bal,0,".","") ?></td>
						<td colspan="2" style="text-align:center; font-weight: bold; padding:5px; height: 15px; border: 1px solid black;">$ <?= number_format($totalamount,0,".","") ?></td>
						<td colspan="2" style="text-align:center; font-weight: bold; padding:5px; height: 15px; border: 1px solid black;">$ <?= number_format($pr[0]->beg_bal - $totalamount,0,".","") ?></td>
					</tr>
				</tbody>
			</table>
			<br>

			<table style="width: 100%; font-family: arial; border-collapse: collapse; text-align: center;" border="1">
				<thead>
					<tr>
						<td colspan="1" style="width:15%;height: 26px; border: 1px solid black;text-align: center;padding: 0">Applied By</td>
						<td colspan="1" style="width:15%;">Acknowledge By</td>
						<td colspan="1" style="width:15%;">Acknowledge By</td>
						<td colspan="1" style="width:15%;">Approve By</td>
						<td colspan="6" rowspan="3" style="text-align: left;padding: 5px;vertical-align: top;font-size: 11px">
							Note :
							@if($pr[0]->note == null)
							-
							@else
							<br><?= $pr[0]->note ?>
							@endif
						</td>
					</tr>

				</thead>
				<tbody>
					<tr>
						<td colspan="1" style="height: 40px">
							@if($pr[0]->posisi != "staff")
								<?= $pr[0]->emp_name ?>
							@endif
						</td>
						<td colspan="1" style="height: 40px">
							@if($pr[0]->approvalm == "Approved")
								<?= $pr[0]->manager_name ?>
							@endif
						</td>
						<td colspan="1" style="height: 40px">
							@if($pr[0]->approvaldgm == "Approved")
								<img width="70" src="{{ public_path() . '/files/ttd_pr_po/stempel_pak_budhi.jpg' }}" alt="" style="padding: 0">
								<span style="position: absolute;left: 227px;width: 75px;font-size: 8px;color: #f84c32;top: 133px;font-family: arial-narrow"><?= date('d F Y', strtotime($pr[0]->dateapprovaldgm)) ?></span>
							@endif
						</td>
						<td colspan="1" style="height: 40px">
							@if($pr[0]->approvalgm == "Approved")
								<img width="70" src="{{ public_path() . '/files/ttd_pr_po/stempel_pak_hayakawa.jpg' }}" alt="" style="padding: 0">
								<span style="position: absolute;left: 332px;width: 75px;font-size: 8px;color: #f84c32;top: 133px;font-family: arial-narrow"><?= date('d F Y', strtotime($pr[0]->dateapprovalgm)) ?></span>
							@endif
						</td>
					</tr>
					<tr>
						<td colspan="1">User</td>
						<td colspan="1">Manager</td>
						<td colspan="1">Deputy GM</td>
						<td colspan="1">GM</td>
					</tr>
				</tbody>
			</table>
	        Page <span class="pagenum"></span>
	    </div>
	</footer>
</body>
</html>