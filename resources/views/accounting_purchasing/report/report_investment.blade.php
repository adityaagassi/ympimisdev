<!DOCTYPE html>
<html>
<head>
	<title>YMPI 情報システム</title>
	<!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
			padding: 5px;
		}

		.centera{
			text-align: center;
			vertical-align: middle !important;
		}

		@font-face {
	      font-family: Calibri;
	      font-style: normal;
	      font-weight: 400;
	    }

	    * {
	      font-family: Calibri;
	    }

	    input[type=radio] { display: inline; }
		input[type=radio]:before { font-family: DejaVu Sans; }


	    /*@font-face {
		  font-family:"ヒラギノ角ゴ Pro W3", "Hiragino Kaku Gothic Pro",Osaka, "メイリオ", Meiryo, "ＭＳ Ｐゴシック", "MS PGothic", sans-serif;
	      font-style: normal;
	      font-weight: 400;
	    }*/


	    .droid {
	        font-family: ipag;
	    }


		/*@import url('https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin-ext');*/


		@page { }
        .footer { position: fixed; left: 0px; bottom: -30px; right: 0px; height: 170px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }
	</style>
</head>

<body>
	<header>
		<?php

		$ket_harga = "";

		if($inv[0]->currency == "USD"){
			$ket_harga = "$";
		}else if($inv[0]->currency == "JPY"){
			$ket_harga = "¥";
		}else if($inv[0]->currency == "IDR"){
			$ket_harga = "Rp.";
		}

		?>
		<table style="width: 100%; border-collapse: collapse; text-align: left;">
			<thead>
				<tr>
					<td colspan="10" style="font-size: 14px">PT. Yamaha Musical Products Indonesia</td>
				</tr>
				<tr>
					<td colspan="10"><br></td>
				</tr>				
				<tr>
					<td colspan="10" style="text-align: center;font-size: 16px;font-weight: bold">INVESTMENT-EXPENSE APPLICATION<br><span class="droid">資産・経費申請書</span></td>
				</tr>
				<tr>
					<td colspan="10"><br></td>
				</tr>
				<tr>
					<td colspan="1" style="font-size: 13px;width: 22%">Date Of Submission <span class="droid">申請日</span></td>
					<td colspan="3" style="font-size: 13px;color: blue;font-weight: bold">: <?= date('d-M-Y', strtotime($inv[0]->submission_date)) ?></td>
					
					<td colspan="1" style="font-size: 13px;"></td>
					<td colspan="1" style="font-size: 11px;border: 1px solid black;text-align: center;">President</td>
					<td colspan="1" style="font-size: 11px;border: 1px solid black;text-align: center;">Dir Finance</td>
					<td colspan="1" style="font-size: 11px;border: 1px solid black;text-align: center;">GM Division</td>
					<td colspan="1" style="font-size: 11px;border: 1px solid black;text-align: center;">DGM</td>
					<td colspan="1" style="font-size: 11px;border: 1px solid black;text-align: center;">Applicant</td>
				</tr>

				<tr>
					<td colspan="1" style="font-size: 13px;width: 22%">Reff. Number <span class="droid">関連番号</span></td>
					<td colspan="3" style="font-size: 13px;color: blue;font-weight: bold">: {{ $inv[0]->reff_number }}</td>
					<td colspan="1" style="font-size: 13px;"></td>
					<td colspan="1" rowspan="2" style="font-size: 11px;border: 1px solid black;text-align: center;">
						<?php 

						$limit = explode("/", $inv[0]->approval_presdir);

						if(count($limit) > 2 ) {
							echo $limit[1];
						} 

						?>
					</td>
					<td colspan="1" rowspan="2" style="font-size: 11px;border: 1px solid black;text-align: center;">
						<?php 

						$limit = explode("/", $inv[0]->approval_dir_acc);

						if(count($limit) > 2 ) {
							echo $limit[1];
						} 

						?>
					</td>
					<td colspan="1" rowspan="2" style="font-size: 11px;border: 1px solid black;text-align: center;">
						<?php 

						$limit = explode("/", $inv[0]->approval_gm);

						if(count($limit) > 2 ) {
							echo $limit[1];
						} 

						?>
					</td>
					<td colspan="1" rowspan="2" style="font-size: 11px;border: 1px solid black;text-align: center;">
						<?php 

						$limit = explode("/", $inv[0]->approval_dgm);

						if(count($limit) > 2 ) {
							echo $limit[1];
						} 

						?>
					</td>
					<td colspan="1" rowspan="2" style="font-size: 11px;border: 1px solid black;text-align: center;">
						<?php 

						$limit = explode("/", $inv[0]->approval_manager);

						if(count($limit) > 2 ) {
							echo $limit[1];
						} 

						?>
					</td>
				</tr>
				<tr>
					<td colspan="10"><br></td>
					
				</tr>
				<tr>
					<td colspan="4">
						<td colspan="1" style="font-size: 13px;"></td>
						<td colspan="1" style="font-size: 11px;border: 1px solid black;text-align: center;">
							<?php 

							$limit = explode("/", $inv[0]->approval_presdir);

							if(count($limit) > 2 ) {
								echo date('d-m-Y', strtotime($limit[3]));
							}
							else{
								echo "<br>";
							}

							?>
						</td>
						<td colspan="1" style="font-size: 11px;border: 1px solid black;text-align: center;">
							<?php 

							$limit = explode("/", $inv[0]->approval_dir_acc);

							if(count($limit) > 2 ) {
								echo date('d-m-Y', strtotime($limit[3]));
							} 
							else{
								echo "<br>";
							}

							?>
						</td>
						<td colspan="1" style="font-size: 11px;border: 1px solid black;text-align: center;">
							<?php 

							$limit = explode("/", $inv[0]->approval_gm);

							if(count($limit) > 2 ) {
								echo date('d-m-Y', strtotime($limit[3]));
							}
							else{
								echo "<br>";
							} 

							?>
						</td>
						<td colspan="1" style="font-size: 11px;border: 1px solid black;text-align: center;">
							<?php 

							$limit = explode("/", $inv[0]->approval_dgm);

							if(count($limit) > 2 ) {
								echo date('d-m-Y', strtotime($limit[3]));
							}
							else{
								echo "<br>";
							} 

							?>
						</td>
						<td colspan="1" style="font-size: 11px;border: 1px solid black;text-align: center;">
							<?php 

							$limit = explode("/", $inv[0]->approval_manager);

							if(count($limit) > 2 ) {
								echo date('d-m-Y', strtotime($limit[3]));
							}
							else{
								echo "<br>";
							} 

							?>
						</td>
					</td>
				</tr>
				<tr>
					<td colspan="10"><br></td>
				</tr>

			</thead>
		</table>
	</header>

	<main>
		<table style="table-layout: fixed; width: 100%; border-collapse: collapse;font-size: 11px" id="isi">
			<thead>
				
			</thead>
			<tbody>
				<tr>
					<td colspan="2" style="border: 1px solid black;">Kind Of Application {{$inv[0]->category}} <span class="droid">申請種類</span></td>
					<td colspan="4" style="border: 1px solid black;<?php if ($inv[0]->category == "Investment") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">1. Investment (Role: IV Fixed Asset: 5) <span class="droid">資産（役割：IV 固定資産：5）</span></td>
					<td colspan="4" style="border: 1px solid black;<?php if ($inv[0]->category == "Expense") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">2. Expense (2 Manajemen Bisnis: KG21) <span class="droid"> 経費　（2．経営管理：KG21)</span></td>
				</tr>
				<tr>
					<td colspan="2" style="border: 1px solid black;">Subject <span class="droid">件名</span></td>
					<td colspan="8" style="border: 1px solid black;text-transform: uppercase;color: blue;font-weight: bold">{{ $inv[0]->subject }} <br> <span class="droid">{{ $inv[0]->subject_jpy }}</span></td>
				</tr>
				<tr>
					<td colspan="2" style="border: 1px solid black;">Class Of Assets / Kind Of Expense (Account) <span class="droid">資産・経費種類</span></td>
					<td colspan="4" style="border: 1px solid black;">
						<rio style="<?php if($inv[0]->type == "Building") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">1. Building <span class="droid">建物</span> </rio><rio style="<?php if($inv[0]->type == "Machine and Equipment") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">2. Machine & Equipment <span class="droid">機械・道具</span></rio>
						<rio style="<?php if($inv[0]->type == "Vehicle") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">3. Vehicle <span class="droid">車両</span> </rio> 
						<rio style="<?php if($inv[0]->type == "Tools, Jigs and Furniture") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">4. Tools, Jigs & Furniture <span class="droid">工具、治具、家具</span> </rio> 
						<rio style="<?php if($inv[0]->type == "Moulding") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">5. Moulding <span class="droid">金型</span> </rio><br>
						<rio style="<?php if($inv[0]->type == "PC and Printer") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">6. PC & Printer <span class="droid">パソコン・プリント機</span> </rio><br>
						<rio style="<?php if($inv[0]->type == "Land Acquisition") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">7. Land Acquisition <span class="droid"> </span> </rio>
					</td>
					

					<td colspan="4" style="border: 1px solid black;">
						<rio style="<?php if($inv[0]->type == "Office Supplies") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">1. Office Supplies <span class="droid">事務用品</span> </rio> 
						<rio style="<?php if($inv[0]->type == "Repair and Maintenance") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">2. Repair & Maintenance <span class="droid">修理・メンテナンス</span> </rio> 
						<rio style="<?php if($inv[0]->type == "Constool") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">3. Constool <span class="droid">消耗費</span> </rio>
						<br>
						<rio style="<?php if($inv[0]->type == "Professional Fee") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">4. Professional Fee <span class="droid">専門家鑑定料</span> </rio> 
						<rio style="<?php if($inv[0]->type == "Miscellaneous") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">5. Miscellaneous <span class="droid">諸経費</span> </rio>
						6. Others <span class="droid">その他</span>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="border: 1px solid black;">Department <span class="droid">部門</span></td>
					<td colspan="8" style="border: 1px solid black;">
						<rio style="<?php if($inv[0]->applicant_department == "Human Resources" || $inv[0]->applicant_department == "General Affairs") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">1. Administration <span class="droid">人事総</span></rio>
						<rio style="<?php if($inv[0]->applicant_department == "Procurement" || $inv[0]->applicant_department == "Accounting") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 2. Finance & Accounting  <span class="droid">財務・経理</span></rio>
						<rio style="<?php if($inv[0]->applicant_department == "Logistic") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 3. Logistic <span class="droid">物流</span> </rio>
						<rio style="<?php if($inv[0]->applicant_department == "Quality Assurance") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 4. Standarization <span class="droid">標準課</span> </rio>
						<rio style="<?php if($inv[0]->applicant_department == "Purchasing Control") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 5. Purchasing <span class="droid">購買</span> </rio>
						<rio style="<?php if($inv[0]->applicant_department == "Production Control") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 6. Production Control <span class="droid">生産管理</span> </rio>
						<rio style="<?php if($inv[0]->applicant_department == "Assembly (WI-A)" || $inv[0]->applicant_department == "Welding-Surface Treatment (WI-WST)" || $inv[0]->applicant_department == "Educational Instrument (EI)" || $inv[0]->applicant_department == "Parts Process (WI-PP)") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 7. Production <span class="droid">生産</span> (<?php if($inv[0]->applicant_department == "Assembly (WI-A)" || $inv[0]->applicant_department == "Welding-Surface Treatment (WI-WST)" || $inv[0]->applicant_department == "Educational Instrument (EI)" || $inv[0]->applicant_department == "Parts Process (WI-PP)") { echo $inv[0]->applicant_department; } ?>)  <span class="droid"></span> </rio>
						<rio style="<?php if($inv[0]->applicant_department == "Maintenance") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 8. Maintenance <span class="droid">保全</span> </rio>
						<rio style="<?php if($inv[0]->applicant_department == "Production Engineering") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 9. Prod Engineering <span class="droid">生産技術</span> </rio>
						<rio style="<?php if($inv[0]->applicant_department == "Management Information System") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 10. Management Information System <span class="droid">情報システム管理</span> </rio>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="border: 1px solid black;">Main Objective <span class="droid">目的</span></td>
					<td colspan="8" style="border: 1px solid black;">
						<rio style="<?php if($inv[0]->objective == "Safety & Prevention of Pollution & Disaster") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">1. Safety & Prevention of Pollution & Disaster <span class="droid">汚染と災害の安全と防止、</span></rio>
						<rio style="<?php if($inv[0]->objective == "R & D") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 2. R&D <span class="droid">研究開発,</span> 
						 <br></rio>
						<rio style="<?php if($inv[0]->objective == "Production of New Model") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 3. Production of New Model <span class="droid">新製品生産, </span></rio>
						<rio style="<?php if($inv[0]->objective == "Rationalization") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 4. Rationalization <span class="droid">合理化</span></rio>
						<rio style="<?php if($inv[0]->objective == "Production Increase") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 5. Production Increase <span class="droid">新製品生産,</span></rio>
						<rio style="<?php if($inv[0]->objective == "Repair & Modification") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 6. Repair & Modification <span class="droid">修理・改造</span></rio></td>
				</tr>
				<tr>
					<td colspan="2" style="border: 1px solid black;">Objective Explanation <span class="droid">目的説明</span></td>
					<td colspan="8" style="border: 1px solid black;font-weight: bold;color: blue"><?= ucfirst($inv[0]->objective_detail) ?> <br> <span class="droid">{{ $inv[0]->objective_detail_jpy }}</span></td>
				</tr>
				<?php
					$jumlahitem = count($inv);

					if($jumlahitem < 2)
						$jumlah = 1;
					else if($jumlahitem == 2)
						$jumlah = 3;
					else if($jumlahitem == 3)
						$jumlah = 5;
					else if($jumlahitem == 4)
						$jumlah = 7;
					else if($jumlahitem == 5)
						$jumlah = 9;
					else if($jumlahitem == 6)
						$jumlah = 11;
					else if($jumlahitem == 7)
						$jumlah = 13;
					else if($jumlahitem == 8)
						$jumlah = 15;
					?>

				?>
				<tr>
					<td colspan="2" rowspan="{{ 10 + $jumlah }}" style="border: 1px solid black;">Description<br><br>For Taxation Purpose, Please break down good / material cost & Service expense (if possible) <br><br> <span class="droid">課税目的のため、可能<br>であれば材料費とサ><br>ービス費用の内訳をご<br>記入ください</span></td>
					<td colspan="8" style="border-right: 1px solid black;font-weight: bold;"><u>Supplier <span class="droid">サプライヤー</span></u></td>
				</tr>
				<tr>
					<td colspan="2" style="">Company Name <span class="droid">会社名</span></td>
					<td colspan="6" style="border-right: 1px solid black;color: blue;font-weight: bold">: <b> <?= $inv[0]->supplier_code ?> - <?= $inv[0]->supplier_name ?> </b></td>
				</tr>
				<tr>
					<td colspan="2">PKP Status <span class="droid">課税事業者</span></td>

			        <td colspan="2" style="border: none">
			        	: <rio style="<?php if($inv[0]->pkp == "Yes") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> Yes <span class="droid">はい</span></rio>
			        </td>
			        
			        <td colspan="4" style="border-right: 1px solid black;">
			        	<rio style="<?php if($inv[0]->pkp == "No") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> No <span class="droid">いいえ</span></rio>
			        </td>

				</tr>
				<tr>
					<td colspan="2" style="">NPWP <span class="droid">納税者登録番号</span></td>
					 <td colspan="2" style="border: none">
			        	: <rio style="<?php if($inv[0]->npwp == "Yes") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> Yes <span class="droid">はい</span></rio>
			        </td>
			        
			        <td colspan="4" style="border-right: 1px solid black;">
			        	<rio style="<?php if($inv[0]->npwp == "No") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> No <span class="droid">いいえ</span></rio>
			        </td>
				</tr>
				<tr>
					<td colspan="2" style="">Constructor Certificate <span class="droid"></span></td>
					 <td colspan="2" style="border: none">
			        	: <rio style="<?php if($inv[0]->certificate == "Yes") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> Yes <span class="droid">はい</span></rio>
			        </td>
			        
			        <td colspan="4" style="border-right: 1px solid black;">
			        	<rio style="<?php if($inv[0]->certificate == "No") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> No <span class="droid">いいえ</span></rio>
			        </td>
				</tr>
				<tr>
					<td colspan="3" style="border: 1px solid black;">Specification <span class="droid">仕様</span></td>
					<td colspan="1" style="border: 1px solid black;">Qty <span class="droid">数量</span></td>
					<td colspan="2" style="border: 1px solid black;">Price <span class="droid">価格</span></td>
					<td colspan="2" style="border: 1px solid black;">Amount <span class="droid">金額</span></td>
				</tr>
				<?php 

				$total = 0;
				$vat = 0;
				$total_all = 0;
				$investmentitem = count($inv);

				if($investmentitem != 0) { 

				?>
				@foreach($inv as $item)
				<tr>
					<td colspan="3" rowspan="2" style="border: 1px solid black;color: blue;font-weight: bold;vertical-align: top;">{{$item->detail}}</td>
					<td colspan="1" rowspan="2" style="border: 1px solid black;color: blue;font-weight: bold;vertical-align: top;">{{$item->qty}} {{$item->uom}}</td>
					<td colspan="2" rowspan="2" style="border: 1px solid black;color: blue;font-weight: bold;vertical-align: top;"><?= $ket_harga ?> <?= number_format($item->price,2,",",".");?></td>
					<td colspan="2" rowspan="2" style="border: 1px solid black;color: blue;font-weight: bold;vertical-align: top;"><?= $ket_harga ?> <?= number_format($item->amount,2,",",".");?></td>

					<?php 
					if($item->vat_status == "Yes"){
						$vat = $vat + (0.1*$item->amount);						
					} 
					?>
				</tr>
				<tr></tr>
				<?php
					$total = $total + $item->amount;
				?>

				@endforeach
				<?php 
				} else { 
				?>
				<tr>
					<td colspan="3" rowspan="2" style="border: 1px solid black;"></td>
					<td colspan="1" rowspan="2" style="border: 1px solid black;"></td>
					<td colspan="2" rowspan="2" style="border: 1px solid black;"></td>
					<td colspan="2" rowspan="2" style="border: 1px solid black;"></td>
				</tr>
				<tr></tr>
				<?php } ?>
				<tr>
					<td colspan="2" rowspan="3" style="border: 1px solid black;">Currency<br><span class="droid">通貨</span></td>
					<td colspan="2" rowspan="3" style="border: 1px solid black;color: blue;font-weight: bold">{{ $inv[0]->currency }}</td>
					<td colspan="2" style="border: 1px solid black;">Sub Total <span class="droid">小計</span></td>
					<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold"><?= $ket_harga ?> <?= number_format($total,2,",",".");?></td>
				</tr>
				<tr>
					<td colspan="2" style="border: 1px solid black;">VAT <span class="droid">付加価値税</span></td>
					<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold"><?= $ket_harga ?> <?= number_format($vat,2,",",".");?></td>
				</tr>
				<tr>
					<td colspan="2" style="border: 1px solid black;">Total <span class="droid">合計</span></td>
					<?php $total_all = $total - $vat; ?>
					<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold"><?= $ket_harga ?> <?= number_format($total_all,2,",",".");?></td>
				</tr>
				<tr>
					<td colspan="2" style="border: 1px solid black;">Delivery <span class="droid">納期</span></td>
					<td colspan="4" style="border: 1px solid black;">Delivery Order <span class="droid">送付日付</span><br>&nbsp;&nbsp;<span style="color: blue;font-weight: bold"><?= date('d-M-Y', strtotime($inv[0]->delivery_order)) ?> </span></td>
					<td colspan="4" style="border: 1px solid black;">Date Order <span class="droid">発注日</span><br>&nbsp;&nbsp;<span style="color: blue;font-weight: bold"><?= date('d-M-Y', strtotime($inv[0]->date_order)) ?> </span></td>
				</tr>
				<tr>	
					<td colspan="2" style="border: 1px solid black;">Payment Term <span class="droid">支払い条件</span></td>
					<td colspan="4" style="border: 1px solid black;color: blue;font-weight: bold">{{ $inv[0]->payment_term }}</td>
					<td colspan="2" style="border: 1px solid black;">Fill By Acc Dept W/H Tax (%) <span class="droid">経理課が記入W/H税（％)</span></td>
					<td colspan="1" style="border: 1px solid black;">Total <br><span class="droid">合計</span><br> <span style="color: blue;font-weight: bold"> @if($inv[0]->total != null) <?= $inv[0]->total.'%' ?> @endif </span></td>
					<td colspan="1" style="border: 1px solid black;">Service <span class="droid">サービス</span><br> <span style="color: blue;font-weight: bold"> @if($inv[0]->service != null) <?= $inv[0]->service.'%' ?> @endif </span></td>
				</tr>
				<tr>
					<td colspan="2" style="border: 1px solid black;">Quotation <span class="droid">見積書</span> <br><b>*Other Quotation Must Be Attached</b><span class="droid">他の見積書も添付すること</span></td>
					<td colspan="8" style="border: 1px solid black;color: blue;font-weight: bold"><?= $inv[0]->quotation_supplier ?></td>
				</tr>

				<?php 
				$investmentbudget = count($inv_budget);

				if($investmentbudget != 0) { ?>

				@foreach($inv_budget as $bud)
					<tr>
						<td colspan="2" rowspan="4" style="border: 1px solid black;">Budget No, Name & Balance<br><span class="droid">予算番号、名前、残高</span></td>
						<td colspan="2" style="border: 1px solid black;">Budget</td>
						<td colspan="2" style="border: 1px solid black;">Budget No & Name</td>
						<td colspan="1" style="border: 1px solid black;">Beg Bal <span class="droid">残高</span> <br> <span style="color: blue;font-weight: bold"></span></td>
						<td colspan="1" style="border: 1px solid black;">Amount <span class="droid">金額</span><br> <span style="color: blue;font-weight: bold"></span></td>
						<td colspan="2" style="border: 1px solid black;">End Bal (US$)<span class="droid">最終残高</span>  <span style="color: blue;font-weight: bold"></span></td>
					</tr>

					@if($bud->category_budget == "On Budget")
					<tr>
						<td colspan="2" style="border: 1px solid black;">Budget No. <span class="droid">予算番号</span> <br> Budget Name <span class="droid">予算名前</span></td>
						<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold">{{ $bud->budget_no }} <br> {{ $bud->budget_name }}</td>
						<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold">$ <?= number_format($bud->sisa,2,",",".");?></span></td>
						<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"> $ <?= number_format($bud->total,2,",",".");?></span></td>
						<td colspan="2" style="border: 1px solid black;"> <span style="color: blue;font-weight: bold">$ <?= number_format($bud->sisa - $bud->total, 2, ',', '.'); ?></span></td>
					</tr>
					<tr>
						<td colspan="2" style="border: 1px solid black;">Shifting Budget No. <span class="droid">予算シフト</span> <br> Budget Name <span class="droid">予算名前</span></td>
						<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold"> </td>
						<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"> </span></td>
						<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
						<td colspan="2" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
					</tr>
					<tr>
						<td colspan="8" style="border: 1px solid black;">Out Of Budget <span class="droid">予算不足</span> ($)</td>
					</tr>

					@elseif($bud->category_budget == "Shifting")
					<tr>
						<td colspan="2" style="border: 1px solid black;">Budget No. <span class="droid">予算番号</span> <br> Budget Name <span class="droid">予算名前</span></td>
						<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold"> </td>
						<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
						<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"> </span></td>
						<td colspan="2" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
					</tr>

					<tr>
						<td colspan="2" style="border: 1px solid black;">Shifting Budget No. <span class="droid">予算シフト</span> <br> Budget Name <span class="droid">予算名前</span></td>
						<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold">{{ $bud->budget_no }} <br> {{ $bud->budget_name }}</td>
						<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"> $ <?= number_format($bud->sisa,2,",",".");?></span></td>
						<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold">$ <?= number_format($bud->total,2,",",".");?></span></td>
						<td colspan="2" style="border: 1px solid black;"><span style="color: blue;font-weight: bold">$ <?= number_format($bud->sisa - $bud->total,2,",",".");?></span></td>
					</tr>
					<tr>
						<td colspan="8" style="border: 1px solid black;">Out Of Budget <span class="droid">予算不足</span> ($)</td>
					</tr>

					@elseif($bud->category_budget == "Out Of Budget")
					<tr>
						<td colspan="2" style="border: 1px solid black;">Budget No. <span class="droid">予算番号</span> <br> Budget Name <span class="droid">予算名前</span></td>
						<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold"> </td>
						<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
						<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"> </span></td>
						<td colspan="2" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
					</tr>
					<tr>
						<td colspan="2" style="border: 1px solid black;">Shifting Budget No. <span class="droid">予算シフト</span> <br> Budget Name <span class="droid">予算名前</span></td>
						<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold"> </td>
						<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"> </span></td>
						<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
						<td colspan="2" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
					</tr>
					<tr>
						<td colspan="2" style="border: 1px solid black;"><span style="color: blue;font-weight: bold">Out Of Budget <span class="droid">予算不足</span> ($)</span></td>
						<td colspan="2" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
						<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
						<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold">$ <?= number_format($bud->total,2,",",".");?></span></td>
						<td colspan="2" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>						
					</tr>
					
					@endif
				</tr>
				@endforeach

				<?php } else { ?>
				<tr>
					<td colspan="2" rowspan="4" style="border: 1px solid black;">Budget No, Name & Balance<br><span class="droid">予算番号、名前、残高</span></td>
					<td colspan="2" style="border: 1px solid black;">Budget</td>
					<td colspan="2" style="border: 1px solid black;">Budget No & Name</td>
					<td colspan="1" style="border: 1px solid black;">Beg Bal <span class="droid">残高</span> <br> <span style="color: blue;font-weight: bold"></span></td>
					<td colspan="1" style="border: 1px solid black;">Amount <span class="droid">金額</span><br> <span style="color: blue;font-weight: bold"></span></td>
					<td colspan="2" style="border: 1px solid black;">End Bal (US$)<span class="droid">最終残高</span>  <span style="color: blue;font-weight: bold"></span></td>
				</tr>
				<tr>
					<td colspan="2" style="border: 1px solid black;">Budget No. <span class="droid">予算番号</span> <br> Budget Name <span class="droid">予算名前</span></td>
					<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold"> </td>
					<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
					<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"> </span></td>
					<td colspan="2" style="border: 1px solid black;"> <span style="color: blue;font-weight: bold"></span></td>
				</tr>
				<tr>
					<td colspan="2" style="border: 1px solid black;">Shifting Budget No. <span class="droid">予算シフト</span> <br> Budget Name <span class="droid">予算名前</span></td>
					<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold"> </td>
					<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"> </span></td>
					<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
					<td colspan="2" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
				</tr>
				<tr>
					<td colspan="8" style="border: 1px solid black;">Out Of Budget <span class="droid">予算不足</span> ($)</td>
				</tr>
				<?php } ?>
			</tbody>

		</table>
	</main>
	<footer>
		<div class="footer">
			<table style="table-layout: fixed;width: 100%; font-family: arial; border-collapse: collapse; text-align: center;font-size: 12px;" border="1">
				<thead>
					<tr>
						<td colspan="4" rowspan="3" style="text-align: left;margin-left: 10px">Note <span class="droid">備考</span>: <span style="color:blue;font-weight: bold"><br><?= $inv[0]->note ?> </span>
						</td>
						<td colspan="2" rowspan="2">Approval By Acc Manager </td>
						<td colspan="4">Checked By Acc Staff <span class="droid">経理担当が確認</span></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2">Tax Effect <span class="droid">税効果</span></td>
						<td colspan="2">Budget Balance <span class="droid">予算残高</span></td>
					</tr>
					<tr>
						<td colspan="2" style="height: 40px">
							<?php 

							$limit = explode("/", $inv[0]->approval_manager_acc);

							if(count($limit) > 2 ) {
								echo $limit[1]."<br>".date('d-m-Y', strtotime($limit[3]));
							}
							else{
								echo "<br>";
							} 

							?>
						</td>
						<td colspan="2" style="height: 40px">
							@if($inv[0]->posisi != "acc_pajak" && $inv[0]->posisi != "acc_budget" && $inv[0]->posisi != "user")
								Yeny Arisanty
							@endif
						</td>
						<td colspan="2" style="height: 40px">
							@if($inv[0]->posisi != "acc_budget" && $inv[0]->posisi != "user")
								Lailatul Chusnah
							@endif
						</td>
					</tr>
					<tr>
						<td colspan="6" rowspan="2" style="text-align: left"><u>The Application Used For</u><br>1. Purchase goods/service more than Rp 10,000,000 or equivalent.<br>2. All entertainment expense & donation<br>3. All advance payment</td>
						<td colspan="4">YCJ Approval Requirement?</td>
					</tr>
					<tr>
						<td colspan="4"><?= $inv[0]->ycj_approval ?></td>
					</tr>
				</tbody>
			</table>
			<span style="float: left">*Indicated Amount is valuated to Quarterly(Budget/Forecast) Exchanged Rate</span>
	    </div>
	</footer>
</body>
</html>