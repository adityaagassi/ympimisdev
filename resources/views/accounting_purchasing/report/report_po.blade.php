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

		@if($po[0]->revised == "true")
			<img width="150" src="{{ public_path() . '/files/ttd_pr_po/revised.jpg' }}" alt="" style="padding: 0;position: absolute;top: 150px;left: 250px">
			<span style="position: absolute;left: 11px;width: 75px;font-size: 12px;font-weight: bold;font-family: arial-narrow;top:194px;left: 294px;color: #e797ab"><?= date('d-M-y', strtotime($po[0]->revised_date)) ?></span>
		@endif
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
					<td colspan="8" style="text-align: left;font-size: 11px">Jawa Timur Indonesia</td>
				</tr>

				<tr>
					<td colspan="10"><br></td>
				</tr>
				<tr>
					<td colspan="10" style="text-align:center;font-size: 20px;font-weight: bold;font-style: italic">
						<div class="line">
							<span>
								<?php 

									$status = 0;

									foreach($po as $pos){
	 									if($pos->service_price != "0"){
											$status = 1;	 										
	 									}						
									}

									if($status == "0")
										{
											echo "PURCHASE";
										}
									elseif($status == "1")
										{
											echo "JOB";
										}
								?>

								ORDER
							</span>
						<div>
					</td>
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
					<td colspan="3" style="font-size: 12px;">: <?= date('d-M-y', strtotime($po[0]->tgl_po)) ?></td>
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
					<?php if($po[0]->remark == "PR"){ ?>
						<td colspan="3" style="font-size: 12px;">: {{$po[0]->department}}</td>
					<?php } else if($po[0]->remark == "Investment") { ?>
						<td colspan="3" style="font-size: 12px;">: {{$po[0]->applicant_department}}</td>
					<?php } ?> 
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
					<?php if($po[0]->remark == "PR"){ ?>
						<td colspan="1" style="font-size: 12px;">No PR</td>
					<?php } else if($po[0]->remark == "Investment") { ?>
						<td colspan="1" style="font-size: 12px;">No Investment</td>
					<?php } ?>
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
					<td colspan="4" style="font-size: 11px"><b>PT. Yamaha Musical Products Indonesia</b></td>
				</tr>
				<tr>
					<td colspan="6"></td>
					<td colspan="4" style="font-size: 11px">Jl. Rembang Industri I/36-44</td>
				</tr>
				<tr>
					<td colspan="1" style="font-size: 11px;width: 10%"><b>Delivery Term</b></td>
					<td colspan="2" style="font-size: 11px;width: 20%"><b>:</b> {{$po[0]->delivery_term}}</td>
					<td colspan="3"></td>
					<td colspan="4">Kawasan Industri PIER - Pasuruan</td>
				</tr>

				<tr>
					<td colspan="1" style="font-size: 11px;"><b>Vendor Status</b></td>
					<td colspan="2" style="font-size: 11px;"><b>:</b> {{$po[0]->supplier_status}}</td>
					<td colspan="1" style="font-size: 11px"><b>Price</b></td>
					<td colspan="2" style="font-size: 11px"><b>:</b> {{$po[0]->vat}}</td>
					<td colspan="4">Pandean - Rembang</td>
				</tr>

				<tr>
					<td colspan="1" style="font-size: 11px;"><b>Payment</b></td>
					<td colspan="2" style="font-size: 11px;"><b>:</b> {{$po[0]->supplier_due_payment}}</td>
					<td colspan="1" style="font-size: 11px;"><b>W/H Tax</b></td>
					<td colspan="2" style="font-size: 11px;"><b>:</b> {{$po[0]->holding_tax}} %</td>
					<td colspan="4">Kab. Pasuruan Jawa Timur 67152</td>
				</tr>

				<tr>
					<td colspan="1" style="font-size: 11px;"><b>Buyer</b></td>
					<td colspan="2" style="font-size: 11px;"><b>:</b> {{$po[0]->buyer_name}}</td>

					<td colspan="1" style="font-size: 11px;"><b>Currency</b></td>
					<td colspan="2" style="font-size: 11px;"><b>:</b> {{$po[0]->currency}}</td>
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
					<td colspan="2" style="width:7%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Item Code / Description<br><span style="font-size: 10px;font-style: italic">(Kode / Deskripsi Item)</span></td>
					<td colspan="1" style="width:4%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Delivery Date<br><span style="font-size: 10px;font-style: italic">(Tanggal Pengiriman)</span></td>
					<td colspan="1" style="width:2%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Qty<br><span style="font-size: 10px;font-style: italic">(Banyaknya)</span></td>
					<td colspan="1" style="width:2%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">UM<br><span style="font-size: 10px;font-style: italic">(Satuan)</span></td>
					<?php 

						$status = 0;

						foreach($po as $pos){
							if($pos->service_price != "0"){
								$status = 1;	 										
							}						
						}

						if($status == "0")
						{
							echo '<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Unit Price<br><span style="font-size: 10px;font-style: italic">(Harga Satuan)</span></td>';
						}
						elseif($status == "1")
						{
							echo '
								<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Price<br><span style="font-size: 10px;font-style: italic">(Harga)</span></td>';
						}
					?>

					<td colspan="1" style="width:3%; background-color: #eceff1; font-weight: bold; border: 1px solid black;">Amount<br><span style="font-size: 10px;font-style: italic">(Jumlah)</span></td>
				</tr>
			</thead>
			<tbody>
				<?php $no = 1; 
				$total = 0;
				?>
				@foreach($po as $pos)
				<tr>
					<td colspan="1" style="height: 26px; border: 1px solid black;text-align: center;padding: 0">{{ $no }}</td>
					<td colspan="2" style="border: 1px solid black;">{{ $pos->nama_item }}</td>
					<td colspan="1" style="border: 1px solid black;text-align: center;"><?= date('d-M-y', strtotime($pos->delivery_date)) ?></td>
					<td colspan="1" style="border: 1px solid black;text-align: center;">{{ $pos->qty }}</td>
					<td colspan="1" style="border: 1px solid black;text-align: center;">{{ $pos->uom }}</td>
					@if($pos->goods_price != "0") 
					<td colspan="1" style="border: 1px solid black;text-align: right;padding-right: 5px"><?= number_format($pos->goods_price,2,",",".");?></td>
					<?php
						$price = $pos->goods_price * $pos->qty;
						$total = $total + $price;
					?>

					@elseif($pos->service_price != "0")
					<td colspan="1" style="border: 1px solid black;text-align: right;padding-right: 5px"><?= number_format($pos->service_price,2,",",".");?></td>
					<?php
						$price = $pos->service_price * $pos->qty;
						$total = $total + $price;
					?>

					@endif

					<td colspan="1" style="border: 1px solid black;text-align: right;padding-right: 5px"><?= number_format($price,2,",","."); ?></td>
				</tr>
				<?php $no++; ?>
				@endforeach

				@if($po[0]->note != null)

				<tr>
					<td colspan="8"><span style="color: red;font-size: 12px"><?= $po[0]->note ?> </span></td>
				</tr>

				@endif


				<tr>
					<td colspan="8"><br></td>
				</tr>


				<tr>
					<td colspan="5">
					
					<?php 

					$status = 0;

						foreach($po as $pos){
							if($pos->service_price != "0"){
								$status = 1;	 										
							}						
						}

						if($status == "0")
						{
							echo '<td colspan="2" style="font-weight: bold;font-size: 12px;text-align: right;padding-right: 5px">Sub Total Goods</td>';
						}
						elseif($status == "1")
						{
							echo '
								<td colspan="2" style="font-weight: bold;font-size: 12px;text-align: right;padding-right: 5px">Sub Total</td>';
						}

					?>

					<td colspan="1" style="font-weight: bold;font-size: 12px;text-align: right;padding-right: 5px"><?= number_format($total,2,",","."); ?></td>
				</tr>

				<tr>
					<td colspan="5">
					<td colspan="2" style="font-weight: bold;font-size: 12px;text-align: right;padding-right: 5px">VAT 10 % 
						<?php 
							if ($po[0]->material == "Dipungut PPNBM") {
								echo "(Collected)";
							}
							else if ($po[0]->material == "Tidak Dipungut PPNB"){
								echo "(Not Collected)";
							}
						?>
					</td>
					<td colspan="1" style="font-weight: bold;font-size: 12px;text-align: right;padding-right: 5px">
					
					<?php 
						//Jika ini barang

					$status = 0;

						foreach($po as $pos){
							if($pos->service_price != "0"){
								$status = 1;	 										
							}						
						}

						if($status == "0")
						{
							
							if ($po[0]->supplier_status == "PKP") {
								$pajak = ($total*10)/100;
							}
							else if ($po[0]->supplier_status == "Non PKP" || $po[0]->supplier_status == "Import"){
								$pajak = 0;
							}

						}
						
						elseif($status == "1")
						{
							if ($po[0]->supplier_status == "PKP") {
								$pajak = ($total*10)/100;
							}
							else if ($po[0]->supplier_status == "Non PKP" || $po[0]->supplier_status == "Import"){
								$pajak = 0;
							}
							// $pajak = ($total*10)/100;
						}

					?> 

					<?= number_format($pajak,2,",","."); ?>

					</td>
				</tr>

				<?php 
					$wh = 0;
					if ($po[0]->holding_tax != 0) {
				?>

				<tr>
					<td colspan="5">
					<td colspan="2" style="font-weight: bold;font-size: 12px;text-align: right;padding-right: 5px">W/H Tax <?= $po[0]->holding_tax ?> %</td>
					<td colspan="1" style="font-weight: bold;font-size: 12px;text-align: right;padding-right: 5px">
					
					<?php 
						if ($po[0]->holding_tax != 0) {
							$wh = (($total+$pajak) * $po[0]->holding_tax)/100;
						}
					?> 

					<?= number_format($wh,2,",","."); ?>

					</td>
				</tr>

				<?php } ?>

				<tr>
					<td colspan="5">
					<td colspan="2" style="font-weight: bold;font-size: 12px;text-align: right;padding-right: 5px">Net Payment </td>
					<td colspan="1" style="font-weight: bold;font-size: 12px;text-align: right;padding-right: 5px">

						<?php 
							$net = 0;
							$status = 0;

							foreach($po as $pos){
								if($pos->service_price != "0"){
									$status = 1;	 										
								}						
							}

							if($status == "0")
							{
								
								if($po[0]->supplier_status == "PKP") {
									if ($po[0]->material == "Dipungut PPNBM") {
										$vat = $pajak;
									}
									else if ($po[0]->material == "Tidak Dipungut PPNB"){
										$vat = 0;
									}
									
								}
								else if($po[0]->supplier_status == "Non PKP" || $po[0]->supplier_status == "Import"){
									$vat = 0;
								}

							}
							
							elseif($status == "1")
							{
								$vat = $pajak;
							}


							$net = ($vat + $total) - $wh;

						?> 

						<?= number_format($net,2,",",".");  ?>
					</td>
				</tr>
			</tbody>
		</table>	
	</main>

	<footer>

		@if($po[0]->approval_authorized4 == "Approved")
		<img width="100" src="{{ public_path() . '/files/ttd_pr_po/stempel_ympi.png' }}" alt="" style="padding: 0;position: absolute;top: 830px;left: 600px;z-index: 200">
		@endif
		<div class="footer">
			<table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse;">
				<thead>
					<tr>
						<td colspan="12" style="font-size: 12px;font-weight: bold">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
					</tr>
					<tr>
						<td colspan="3" style="width: 40%">
							@if($po[0]->buyer_id == "PI1908032")
								<img width="100" src="{{ public_path() . '/files/ttd_pr_po/ttd_erlangga.png' }}" alt="" style="padding: 0">
								<span style="position: absolute;left: 11px;width: 75px;font-size: 10px;font-weight: bold;top: 81px;font-family: arial-narrow"><?= date('d-M-y', strtotime($po[0]->tgl_po)) ?></span>
							@elseif($po[0]->buyer_id == "PI1810020")
								<img width="75" src="{{ public_path() . '/files/ttd_pr_po/ttd_shega.png' }}" alt="" style="padding: 0">
								<span style="position: absolute;left: 25px;width: 75px;font-size: 10px;font-weight: bold;top: 80px;font-family: arial-narrow"><?= date('d-M-y', strtotime($po[0]->tgl_po)) ?></span>
							@else
								<?= $po[0]->buyer_name ?>
							@endif
						</td>
						<td colspan="3" style="width: 30%">
							@if($po[0]->approval_authorized2 == "Approved")
								<img width="70" src="{{ public_path() . '/files/ttd_pr_po/ttd_pak_imron.jpg' }}" alt="" style="padding: 0">
								<span style="position: absolute;left: 220px;width: 75px;font-size: 10px;font-weight: bold;top: 81px;font-family: arial-narrow"><?= date('d-M-y', strtotime($po[0]->date_approval_authorized2)) ?></span>
							@endif
						</td>
						<td colspan="3" style="width: 30%">
							@if($po[0]->approval_authorized3 == "Approved")
								<img width="90" src="{{ public_path() . '/files/ttd_pr_po/ttd_pak_budhi.jpg' }}" alt="" style="padding: 0">
								<span style="position: absolute;left: 380px;width: 75px;font-size: 10px;font-weight: bold;top: 81px;font-family: arial-narrow"><?= date('d-M-y', strtotime($po[0]->date_approval_authorized3)) ?></span>
							@endif
						</td>

						<td colspan="3" style="width: 30%">
							@if($po[0]->approval_authorized4 == "Approved")
								<img width="90" src="{{ public_path() . '/files/ttd_pr_po/ttd_pak_hayakawa.png' }}" alt="" style="padding: 0">
								<span style="position: absolute;left: 540px;width: 75px;font-size: 10px;font-weight: bold;top: 81px;font-family: arial-narrow"><?= date('d-M-y', strtotime($po[0]->date_approval_authorized4)) ?></span>
							@endif
						</td>
					</tr>

				</thead>
				<tbody>

					<tr>
						<td colspan="3" style="height: 26px;padding: 0;font-weight: bold;text-decoration: underline;">{{ $po[0]->buyer_name }}</td>
						<td colspan="3" style="font-weight: bold;text-decoration: underline;">{{ $po[0]->authorized2_name }}</td>
						<td colspan="3" style="font-weight: bold;text-decoration: underline;">{{ $po[0]->authorized3_name }}</td>
						<td colspan="3" style="font-weight: bold;text-decoration: underline;">{{ $po[0]->authorized4_name }}</td>
					</tr>
					<tr>
						<td colspan="3">Procurement Staff</td>
						<td colspan="3">Procurement Manager</td>
						<td colspan="3">DGM Production</td>
						<td colspan="3">GM Production</td>
					</tr>
				</tbody>
			</table>
	        <!-- Page <span class="pagenum"></span> -->
	    </div>
	</footer>
</body>
</html>