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
        .footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 150px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }
	</style>
</head>

<body>
	<header>
		<table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;" >
			<thead>
				<tr>
					<td colspan="2" rowspan="5" class="centera" style="padding : 0" width="30%">
						<img width="200" src="{{ public_path() . '/waves2.png' }}" alt="" style="padding: 0">
					</td>
					<td colspan="8" style="font-weight: bold;font-size: 13px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA (PT. YMPI)</td>
				</tr>
				<tr>
					<td colspan="8">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="4" style="text-align: left;font-size: 11px">Jl. Rembang Industri I/36</td>
					<td colspan="4" style="text-align: left;font-size: 11px">Phone : (0343) 740290</td>
				</tr>
				<tr>
					<td colspan="4" style="text-align: left;font-size: 11px">Kawasan Industri PIER - Pasuruan</td>
					<td colspan="4" style="text-align: left;font-size: 11px">Fax : (0343) 740291</td>
				</tr>
				<tr>
					<td colspan="8" style="text-align: left;font-size: 11px">Jawa TImur Indonesia</td>
				</tr>

				<tr>
					<td colspan="10"><br></td>
				</tr>
				<tr>
					<td colspan="10" style="text-align:center;font-size: 20px;font-weight: bold;font-style: italic"><div class="line"><span>PURCHASE ORDER</span><div></td>
				</tr>

				<tr>
					<td colspan="10" style="font-size: 12px;font-weight: bold;">Vendor</td>
				</tr>

				<tr>
					<td colspan="4" style="font-size: 14px;font-weight: bold;">{{$po[0]->supplier_name}}</td>
					<td colspan="2"></td>
					<td colspan="1" style="font-size: 12px;">No PO</td>
					<td colspan="3" style="font-size: 12px;">: <b>{{$po[0]->no_po}}</b></td>
				</tr>

				<tr>
					<td colspan="4" style="font-size: 12px">{{$po[0]->supplier_address}}</td>
					<td colspan="2"></td>
					<td colspan="1" style="font-size: 12px;">Date</td>
					<td colspan="3" style="font-size: 12px;">: <?= date('d F Y H:i:s', strtotime($po[0]->tgl_po)) ?></td>
				</tr>

				<tr>
					<td colspan="4" style="font-size: 12px">{{$po[0]->supplier_city}}</td>
					<td colspan="2"></td>
					<td colspan="1" style="font-size: 12px;">No PO SAP</td>
					<td colspan="3" style="font-size: 12px;">: {{$po[0]->no_po_sap}}</td>
				</tr>

				<tr>
					<td colspan="4" style="font-size: 11px">NPWP &nbsp;: {{$po[0]->supplier_npwp}}</td>
					<td colspan="2"></td>
					<td colspan="1" style="font-size: 12px;">Dept/Sect</td>
					<td colspan="3" style="font-size: 12px;">: {{$po[0]->department}}</td>
				</tr>

				<tr>
					<td colspan="4" style="font-size: 11px">Phone &nbsp;&nbsp;: {{$po[0]->supplier_phone}}</td>
					<td colspan="2"></td>
					<td colspan="1" style="font-size: 12px;">Budget</td>
					<td colspan="3" style="font-size: 12px;">: <b><u>{{$po[0]->budget_item}}</u></b></td>
				</tr>

				<tr>
					<td colspan="4" style="font-size: 11px">Fax &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{$po[0]->supplier_fax}}</td>
					<td colspan="2"></td>
					<td colspan="1" style="font-size: 12px;">No PR </td> 
					<td colspan="3" style="font-size: 12px;">: 

						<?php for ($i=0; $i < count($pr) ; $i++) { 

							if(count($pr) > 1){
								$enter = ",";
							}
							else{
								$enter = "";
							}
							if($i+1 == count($pr)){
								$enter = "";
							}

							if(($i+1) % 2 == 0){
								$br = "<br>";
							}else{
								$br = "";
							}

						?>
						{{ $pr[$i]->no_pr }}<?=$enter ?><?=$br ?>

						<?php } ?>
					</td>
				</tr>
				<tr>
					<td colspan="10"><br></td>
				</tr>

				<tr>
					<td colspan="3" style="font-size: 11px"><b>Attn :</b> {{$po[0]->contact_name}}</td>
					<td colspan="3" style="font-size: 11px"><b>Shipped By :</b> {{$po[0]->transportation}}</td>
					<td colspan="4" style="font-size: 11px"><b>Ship To / Invoice To :</b> </td>
				</tr>

				<tr>
					<td colspan="6"></td>
					<td colspan="4" style="font-size: 11px"><b>PT. Yamaha Musical Produtcs Indonesia</b></td>
				</tr>
				<tr>
					<td colspan="6"></td>
					<td colspan="4" style="font-size: 11px">Jl. Rembang Industri I/36-44</td>
				</tr>
				<tr>
					<td colspan="3"></td>
					<td colspan="3" style="font-size: 11px"><b>Delivery Term :</b> {{$po[0]->delivery_term}}</td>
					<td colspan="4">Kawasan Industri PIER - Pasuruan</td>
				</tr>

				<tr>
					<td colspan="3" style="font-size: 11px"><b>Vendor Status :</b> {{$po[0]->supplier_status}}</td>
					<td colspan="3" style="font-size: 11px"><b>Price &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> {{$po[0]->vat}}</td>
					<td colspan="4">Pandean - Rembang</td>
				</tr>

				<tr>
					<td colspan="3" style="font-size: 11px"><b>W/H Tax &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> {{$po[0]->holding_tax}}</td>
					<td colspan="3" style="font-size: 11px"><b>Buyer &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> {{$po[0]->buyer_name}}</td>
					<td colspan="4">Kab. Pasuruan Jawa Timur 67152</td>
				</tr>

				<tr>
					<td colspan="3" style="font-size: 11px"><b>Payment &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> {{$po[0]->supplier_due_payment}}</td>
					<td colspan="3" style="font-size: 11px"><b>Currency :</b> {{$po[0]->currency}}</td>
					<td colspan="4">NPWP : 01.824.283.4-052.000</td>
				</tr>

				<tr>
					<td colspan="10"><br></td>
				</tr>



			</thead>
		</table>
	</header>
	<main>
		<table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; " id="isi">
			<thead>
				<tr style="font-size: 12px">
					<td colspan="1" style="padding:10px;height: 15px; width:1%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">No</td>
					<td colspan="2" style="width:8%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Item Code / Description</td>
					<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Delivery Date</td>
					<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Qty</td>
					<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">UM</td>
					<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Unit Price</td>
					<td colspan="1" style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Amount</td>
				</tr>
			</thead>
			<tbody>
				<?php $no = 1; 
				$total = 0;
				?>
				@foreach($po as $po)
				<tr>
					<td colspan="1" style="height: 26px; border: 1px solid black;text-align: center;padding: 0">{{ $no }}</td>
					<td colspan="2" style="border: 1px solid black;">{{ $po->nama_item }}</td>
					<td colspan="1" style="border: 1px solid black;text-align: center;"><?= date('d F Y', strtotime($po->delivery_date)) ?></td>
					<td colspan="1" style="border: 1px solid black;text-align: center;">{{ $po->qty }}</td>
					<td colspan="1" style="border: 1px solid black;text-align: center;">{{ $po->uom }}</td>
					<td colspan="1" style="border: 1px solid black;text-align: right;padding-right: 5px"><?= number_format($po->goods_price,2,",",".");?></td>
					<?php
						$price = $po->goods_price * $po->qty;
						$total = $total + $price;
					?>
					<td colspan="1" style="border: 1px solid black;text-align: right;padding-right: 5px"><?= number_format($price,2,",","."); ?></td>
				</tr>
				<?php $no++; ?>
				@endforeach


				<tr>
					<td colspan="8"><br></td>
				</tr>


				<tr>
					<td colspan="5">
					<td colspan="2" style="font-weight: bold;font-size: 12px;text-align: right;padding-right: 5px">Sub Total Goods</td>
					<td colspan="1" style="font-weight: bold;font-size: 12px;text-align: right;padding-right: 5px"><?= number_format($total,2,",","."); ?></td>
				</tr>

				<tr>
					<td colspan="5">
					<td colspan="2" style="font-weight: bold;font-size: 12px;text-align: right;padding-right: 5px">VAT 10 % 
						<?php 
							if ($po->material == "Dipungut PPNBM") {
								echo "(Collected)";
							}
							else if ($po->material == "Tidak Dipungut PPNB"){
								echo "(Not Collected)";
							}
						?>
					</td>
					<td colspan="1" style="font-weight: bold;font-size: 12px;text-align: right;padding-right: 5px">
					
					<?php 
						if ($po->supplier_status == "PKP") {
							$pajak = ($total*10)/100;
						}
						else if ($po->supplier_status == "Non PKP"){
							$pajak = 0;
						}
					?> 

					<?= number_format($pajak,2,",","."); ?>

					</td>
				</tr>

				<tr>
					<td colspan="5">
					<td colspan="2" style="font-weight: bold;font-size: 12px;text-align: right;padding-right: 5px">Net Payment </td>
					<td colspan="1" style="font-weight: bold;font-size: 12px;text-align: right;padding-right: 5px">

						<?php 
							$net = 0;
							if($po->supplier_status == "PKP") {
								if ($po->material == "Dipungut PPNBM") {
									$vat = $pajak;
								}
								else if ($po->material == "Tidak Dipungut PPNB"){
									$vat = 0;
								}
								
							}
							else if($po->supplier_status == "Non PKP"){
								$vat = 0;
							}

							$net = $vat + $total;

						?>

						<?= number_format($net,2,",",".");  ?>
					</td>
				</tr>
			</tbody>
		</table>	
	</main>

	<footer>
		<div class="footer">
			<table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse;">
				<thead>
					<tr>
						<td  colspan="9" style="font-size: 12px;font-weight: bold">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
					</tr>
					<tr>
						<td colspan="3" style="height: 70px">
							@if($po->posisi != "staff_pch")
								<?= $po->buyer_name ?>
							@endif
						</td>
						<td colspan="3" style="height: 70px">
							@if($po->approval_authorized2 == "Approved")
								<?= $po->authorized2_name ?>
							@endif
						</td>
						<td colspan="3" style="height: 70px">
							@if($po->approval_authorized3 == "Approved")
								<?= $po->authorized3_name ?>
							@endif
						</td>
					</tr>

				</thead>
				<tbody>

					<tr>
						<td colspan="3" style="height: 26px;padding: 0">{{ $po->buyer_name }}</td>
						<td colspan="3" style="">{{ $po->authorized2_name }}</td>
						<td colspan="3" style="">{{ $po->authorized3_name }}</td>
					</tr>
					<tr>
						<td colspan="3">Procurement Staff</td>
						<td colspan="3">Procurement Manager</td>
						<td colspan="3">GM Production</td>
					</tr>
				</tbody>
			</table>
	        Page <span class="pagenum"></span>
	    </div>
	</footer>
</body>
</html>