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
	      	/*font-family: ipag;*/
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
						<td colspan="10" style="text-align: center;font-size: 16px;font-weight: bold">INVESTMENT-EXPENSE APPLICATION<br>
							<img src="{{ public_path() . '/files/jepang/Screenshot_42.jpg' }}" alt="" width="150">
						</td>
					</tr>
					<tr>
						<td colspan="10"><br></td>
					</tr>
					<tr>
						<td colspan="1" style="font-size: 13px;width: 22%">Date Of Submission 
							<img src="{{ public_path() . '/files/jepang/Screenshot_43.jpg' }}" width="35">
						</td>
						<td colspan="3" style="font-size: 13px;color: blue;font-weight: bold">: <?= date('d-M-Y', strtotime($inv[0]->submission_date)) ?></td>

						<td colspan="1" style="font-size: 13px;"></td>
						<td colspan="1" style="font-size: 11px;border: 1px solid black;text-align: center;">President</td>
						<td colspan="1" style="font-size: 11px;border: 1px solid black;text-align: center;">Dir Finance</td>
						<td colspan="1" style="font-size: 11px;border: 1px solid black;text-align: center;">GM Division</td>
						<td colspan="1" style="font-size: 11px;border: 1px solid black;text-align: center;">DGM</td>
						<td colspan="1" style="font-size: 11px;border: 1px solid black;text-align: center;">Applicant</td>
					</tr>

					<tr>
						<td colspan="1" style="font-size: 13px;width: 22%">Reff. Number 
							<img src="{{ public_path() . '/files/jepang/Screenshot_44.jpg' }}" width="40">
						</td>
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
						<td colspan="2" style="border: 1px solid black;">Kind Of Application {{$inv[0]->category}} 							
							<br><img src="{{ public_path() . '/files/jepang/Screenshot_45.jpg' }}" width="40">
						</td>
						<td colspan="4" style="border: 1px solid black;<?php if ($inv[0]->category == "Investment") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">1. Investment (Role: IV Fixed Asset: 5) 						
							<br><img src="{{ public_path() . '/files/jepang/Screenshot_46.jpg' }}" width="140">
						</td>
						<td colspan="4" style="border: 1px solid black;<?php if ($inv[0]->category == "Expense") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">2. Expense (2 Manajemen Bisnis: KG21) 					
							<br><img src="{{ public_path() . '/files/jepang/Screenshot_47.jpg' }}" width="140">
						</td>
					</tr>
					<tr>
						<td colspan="2" style="border: 1px solid black;">Subject 
							<img src="{{ public_path() . '/files/jepang/Screenshot_52.jpg' }}" width="20">
						</td>
						<td colspan="8" style="border: 1px solid black;text-transform: uppercase;color: blue;font-weight: bold">{{ $inv[0]->subject }} 
						<!-- <br> <span class="droid">{{ $inv[0]->subject_jpy }}</span></td> -->
					</tr>
					<tr>
						<td colspan="2" style="border: 1px solid black;">Class Of Assets / Kind Of Expense (Account) 
							<br><img src="{{ public_path() . '/files/jepang/Screenshot_53.jpg' }}" width="70">
						</td>
						<td colspan="4" style="border: 1px solid black;">
							<rio style="<?php if($inv[0]->type == "Building") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">1. Building <img src="{{ public_path() . '/files/jepang/Screenshot_54.jpg' }}" width="20"> 
							</rio><rio style="<?php if($inv[0]->type == "Machine and Equipment") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 2. Machine & Equipment <img src="{{ public_path() . '/files/jepang/Screenshot_55.jpg' }}" width="50"></rio>
							<rio style="<?php if($inv[0]->type == "Vehicle") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 3. Vehicle <img src="{{ public_path() . '/files/jepang/Screenshot_56.jpg' }}" width="20"> </rio> 
							<rio style="<?php if($inv[0]->type == "Tools, Jigs and Furniture") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 4. Tools, Jigs & Furniture <img src="{{ public_path() . '/files/jepang/Screenshot_57.jpg' }}" width="60"></rio> 
							<rio style="<?php if($inv[0]->type == "Moulding") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 5. Moulding <img src="{{ public_path() . '/files/jepang/Screenshot_58.jpg' }}" width="20"> </rio>
							<rio style="<?php if($inv[0]->type == "PC and Printer") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 6. PC & Printer <img src="{{ public_path() . '/files/jepang/Screenshot_59.jpg' }}" width="80"> </rio><br>
							<rio style="<?php if($inv[0]->type == "Land Acquisition") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 7. Land Acquisition </rio>
						</td>


						<td colspan="4" style="border: 1px solid black;">
							<rio style="<?php if($inv[0]->type == "Office Supplies") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 1. Office Supplies <img src="{{ public_path() . '/files/jepang/Screenshot_60.jpg' }}" width="40"> </rio> 
							<rio style="<?php if($inv[0]->type == "Repair and Maintenance") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 2. Repair & Maintenance <br> <img src="{{ public_path() . '/files/jepang/Screenshot_62.jpg' }}" width="80"></rio> 
							<rio style="<?php if($inv[0]->type == "Constool") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 3. Constool <img src="{{ public_path() . '/files/jepang/Screenshot_63.jpg' }}" width="30"> </rio>
							<br>
							<rio style="<?php if($inv[0]->type == "Professional Fee") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 4. Professional Fee <img src="{{ public_path() . '/files/jepang/Screenshot_64.jpg' }}" width="50"> </rio> 
							<rio style="<?php if($inv[0]->type == "Miscellaneous") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 5. Miscellaneous <img src="{{ public_path() . '/files/jepang/Screenshot_66.jpg' }}" width="30"> </rio>
							<?php 
							if($inv[0]->type == "Miscellaneous" ||
								$inv[0]->type == "Professional Fee" || 
								$inv[0]->type == "Constool"  || 
								$inv[0]->type == "Repair and Maintenance" || 
								$inv[0]->type == "Office Supplies" || 
								$inv[0]->type == "Building" || 
								$inv[0]->type == "Machine and Equipment" || 
								$inv[0]->type == "Vehicle" || 
								$inv[0]->type == "Tools, Jigs and Furniture" ||
								$inv[0]->type == "Moulding" || 
								$inv[0]->type == "PC and Printer" || 
								$inv[0]->type == "Land Acquisition") { 
							?>
								6. Others
							<img src="{{ public_path() . '/files/jepang/Screenshot_66.jpg' }}" width="30">
							<?php } else { ?>

							<rio style="color:blue;font-weight: bold;text-decoration: underline;"> 	
								6. Others (<?= $inv[0]->type ?>) 
							</rio> 
								<img src="{{ public_path() . '/files/jepang/Screenshot_66.jpg' }}" width="30">
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="border: 1px solid black;">Department 
							<img src="{{ public_path() . '/files/jepang/Screenshot_67.jpg' }}" width="20">
						</td>
						<td colspan="8" style="border: 1px solid black;">
							<rio style="<?php if($inv[0]->applicant_department == "Human Resources" || $inv[0]->applicant_department == "General Affairs") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">1. Administration <img src="{{ public_path() . '/files/jepang/Screenshot_70.jpg' }}" width="20"></rio>
							<rio style="<?php if($inv[0]->applicant_department == "Procurement" || $inv[0]->applicant_department == "Accounting") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 2. Finance & Accounting  <img src="{{ public_path() . '/files/jepang/Screenshot_71.jpg' }}" width="40"></rio>
							<rio style="<?php if($inv[0]->applicant_department == "Logistic") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 3. Logistic <img src="{{ public_path() . '/files/jepang/Screenshot_72.jpg' }}" width="20"> </rio>
							<rio style="<?php if($inv[0]->applicant_department == "Quality Assurance") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 4. Quality Assurance <img src="{{ public_path() . '/files/jepang/Screenshot_73.jpg' }}" width="20"> </rio>
							<rio style="<?php if($inv[0]->applicant_department == "Purchasing Control") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 5. Purchasing <img src="{{ public_path() . '/files/jepang/Screenshot_74.jpg' }}" width="20"> </rio>
							<rio style="<?php if($inv[0]->applicant_department == "Production Control") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 6. Production Control <img src="{{ public_path() . '/files/jepang/Screenshot_75.jpg' }}" width="40"> </rio>
							<rio style="<?php if($inv[0]->applicant_department == "Assembly (WI-A)" || $inv[0]->applicant_department == "Welding-Surface Treatment (WI-WST)" || $inv[0]->applicant_department == "Educational Instrument (EI)" || $inv[0]->applicant_department == "Parts Process (WI-PP)") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 7. Production <img src="{{ public_path() . '/files/jepang/Screenshot_76.jpg' }}" width="20"> (<?php if($inv[0]->applicant_department == "Assembly (WI-A)" || $inv[0]->applicant_department == "Welding-Surface Treatment (WI-WST)" || $inv[0]->applicant_department == "Educational Instrument (EI)" || $inv[0]->applicant_department == "Parts Process (WI-PP)") { echo $inv[0]->applicant_department; } ?>)  </rio>
							<rio style="<?php if($inv[0]->applicant_department == "Maintenance") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 8. Maintenance <img src="{{ public_path() . '/files/jepang/Screenshot_77.jpg' }}" width="20"> </rio>
							<rio style="<?php if($inv[0]->applicant_department == "Production Engineering") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 9. Prod Engineering <img src="{{ public_path() . '/files/jepang/Screenshot_78.jpg' }}" width="30"> </rio>
							<rio style="<?php if($inv[0]->applicant_department == "Management Information System") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 10. Management Information System <img src="{{ public_path() . '/files/jepang/Screenshot_79.jpg' }}" width="60"> </rio>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="border: 1px solid black;">Main Objective <img src="{{ public_path() . '/files/jepang/Screenshot_80_2.jpg' }}" width="20"> </td>
						<td colspan="8" style="border: 1px solid black;">
							<rio style="<?php if($inv[0]->objective == "Safety & Prevention of Pollution & Disaster") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>">1. Safety & Prevention of Pollution & Disaster <img src="{{ public_path() . '/files/jepang/Screenshot_80.jpg' }}" width="80"></rio>
							<rio style="<?php if($inv[0]->objective == "R & D") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 2. R&D <img src="{{ public_path() . '/files/jepang/Screenshot_81.jpg' }}" width="40"> 
								<br></rio>
								<rio style="<?php if($inv[0]->objective == "Production of New Model") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 3. Production of New Model <img src="{{ public_path() . '/files/jepang/Screenshot_82.jpg' }}" width="40"></rio>
								<rio style="<?php if($inv[0]->objective == "Rationalization") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 4. Rationalization <img src="{{ public_path() . '/files/jepang/Screenshot_83.jpg' }}" width="20"></rio>
								<rio style="<?php if($inv[0]->objective == "Production Increase") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 5. Production Increase <img src="{{ public_path() . '/files/jepang/Screenshot_84.jpg' }}" width="40"></rio>
								<rio style="<?php if($inv[0]->objective == "Repair & Modification") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> 6. Repair & Modification <img src="{{ public_path() . '/files/jepang/Screenshot_85.jpg' }}" width="40"></rio></td>
							</tr>
							<tr>
								<td colspan="2" style="border: 1px solid black;">Objective Explanation <br><img src="{{ public_path() . '/files/jepang/Screenshot_86_2.jpg' }}" width="40"></td>
								<td colspan="8" style="border: 1px solid black;font-weight: bold;color: blue"><?= ucfirst($inv[0]->objective_detail) ?> 
								<!-- <br> <span class="droid">{{ $inv[0]->objective_detail_jpy }}</span> -->
								</td>
							</tr>
							<?php
							$jumlahitem = count($inv);

							if($jumlahitem < 2)
								$jumlah = 1;
							else if($jumlahitem == 2)
								$jumlah = 4;
							else if($jumlahitem == 3)
								$jumlah = 7;
							else if($jumlahitem == 4)
								$jumlah = 10;
							else if($jumlahitem == 5)
								$jumlah = 13;
							else if($jumlahitem == 6)
								$jumlah = 16;
							else if($jumlahitem == 7)
								$jumlah = 19;
							else if($jumlahitem == 8)
								$jumlah = 22;
							else if($jumlahitem == 9)
								$jumlah = 25;
							else if($jumlahitem == 10)
								$jumlah = 28;
							else if($jumlahitem == 11)
								$jumlah = 31;
							else if($jumlahitem == 12)
								$jumlah = 34;
							else if($jumlahitem == 13)
								$jumlah = 37;
							else if($jumlahitem == 14)
								$jumlah = 40;
							else if($jumlahitem == 15)
								$jumlah = 43;
							else if($jumlahitem == 16)
								$jumlah = 46;
							else if($jumlahitem == 17)
								$jumlah = 49;
							else if($jumlahitem == 18)
								$jumlah = 52;
							else if($jumlahitem == 19)
								$jumlah = 55;
							else if($jumlahitem == 20)
								$jumlah = 58;
							else if($jumlahitem == 21)
								$jumlah = 61;
							else if($jumlahitem == 22)
								$jumlah = 64;
							?>

							?>
							<tr>
								<td colspan="2" rowspan="{{ 11 + $jumlah }}" style="border: 1px solid black;">Description<br><br>For Taxation Purpose, Please break down good / material cost & Service expense (if possible) <br><br> 
									<img src="{{ public_path() . '/files/jepang/Screenshot_97.jpg' }}" width="100">
								</td>
								<td colspan="8" style="border-right: 1px solid black;font-weight: bold;"><u>Supplier 
									<img src="{{ public_path() . '/files/jepang/Screenshot_88.jpg' }}" width="50"></u>
								</td>
							</tr>
							<tr>
								<td colspan="2" style="">Company Name <img src="{{ public_path() . '/files/jepang/Screenshot_89.jpg' }}" width="30"></td>
								<td colspan="6" style="border-right: 1px solid black;color: blue;font-weight: bold">: <b> <?= $inv[0]->supplier_code ?> - <?= $inv[0]->supplier_name ?> </b></td>
							</tr>
							<tr>
								<td colspan="2">PKP Status <img src="{{ public_path() . '/files/jepang/Screenshot_90.jpg' }}" width="50"></td>

								<td colspan="2" style="border: none">
									: <rio style="<?php if($inv[0]->pkp == "Yes") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> Yes <img src="{{ public_path() . '/files/jepang/Screenshot_92.jpg' }}" width="20"></rio>
								</td>

								<td colspan="4" style="border-right: 1px solid black;">
									<rio style="<?php if($inv[0]->pkp == "No") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> No <img src="{{ public_path() . '/files/jepang/Screenshot_93.jpg' }}" width="20"></rio>
								</td>

							</tr>
							<tr>
								<td colspan="2" style="">NPWP <img src="{{ public_path() . '/files/jepang/Screenshot_91.jpg' }}" width="60"></td>
								<td colspan="2" style="border: none">
									: <rio style="<?php if($inv[0]->npwp == "Yes") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> Yes <img src="{{ public_path() . '/files/jepang/Screenshot_92.jpg' }}" width="20"></rio>
								</td>

								<td colspan="4" style="border-right: 1px solid black;">
									<rio style="<?php if($inv[0]->npwp == "No") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> No <img src="{{ public_path() . '/files/jepang/Screenshot_93.jpg' }}" width="20"></rio>
								</td>
							</tr>
							<tr>
								<td colspan="2" style="">Constructor Certificate</td>
								<td colspan="2" style="border: none">
									: <rio style="<?php if($inv[0]->certificate == "Yes") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> Yes <img src="{{ public_path() . '/files/jepang/Screenshot_92.jpg' }}" width="20"></rio>
								</td>

								<td colspan="4" style="border-right: 1px solid black;">
									<rio style="<?php if($inv[0]->certificate == "No") { echo 'color:blue;font-weight: bold;text-decoration: underline;'; } ?>"> No <img src="{{ public_path() . '/files/jepang/Screenshot_93.jpg' }}" width="20"></rio>
								</td>
							</tr>

							<tr>
								<td colspan="3" style="border: 1px solid black;">Specification <img src="{{ public_path() . '/files/jepang/Screenshot_96.jpg' }}" width="20"></td>
								<td colspan="1" style="border: 1px solid black;">Qty <img src="{{ public_path() . '/files/jepang/Screenshot_98.jpg' }}" width="20"></td>
								<td colspan="2" style="border: 1px solid black;">Price <img src="{{ public_path() . '/files/jepang/Screenshot_99.jpg' }}" width="20"></td>
								<td colspan="2" style="border: 1px solid black;">Amount <img src="{{ public_path() . '/files/jepang/Screenshot_100.jpg' }}" width="20"></td>
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
									<td colspan="3" rowspan="3" style="border: 1px solid black;color: blue;font-weight: bold;vertical-align: top;">{{$item->detail}}</td>
									<td colspan="1" rowspan="3" style="border: 1px solid black;color: blue;font-weight: bold;vertical-align: top;">{{$item->qty}} {{$item->uom}}</td>
									<td colspan="2" rowspan="3" style="border: 1px solid black;color: blue;font-weight: bold;vertical-align: top;text-align: right;"><?= $ket_harga ?> <?= number_format($item->price,2,",",".");?></td>
									<td colspan="2" rowspan="3" style="border: 1px solid black;color: blue;font-weight: bold;vertical-align: top;text-align: right;"><?= $ket_harga ?> <?= number_format($item->amount,2,",",".");?></td>

									<?php 
									if($item->vat_status == "Yes"){
										$vat = $vat + (0.1*$item->amount);						
									} 
									?>
								</tr>
								<tr></tr>
								<tr></tr>
								<?php
								$total = $total + $item->amount;
								?>

								@endforeach
								<?php 
							} else { 
								?>
								<tr>
									<td colspan="3" rowspan="3" style="border: 1px solid black;"></td>
									<td colspan="1" rowspan="3" style="border: 1px solid black;"></td>
									<td colspan="2" rowspan="3" style="border: 1px solid black;"></td>
									<td colspan="2" rowspan="3" style="border: 1px solid black;"></td>
								</tr>
								<tr></tr>
								<tr></tr>
							<?php } ?>
							<tr>
								<td colspan="2" rowspan="3" style="border: 1px solid black;">Currency<br><img src="{{ public_path() . '/files/jepang/Screenshot_101.jpg' }}" width="20"></td>
								<td colspan="2" rowspan="3" style="border: 1px solid black;color: blue;font-weight: bold;">{{ $inv[0]->currency }}</td>
								<td colspan="2" style="border: 1px solid black;">Sub Total <img src="{{ public_path() . '/files/jepang/Screenshot_102.jpg' }}" width="25"></td>
								<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold;text-align: right;"><?= $ket_harga ?> <?= number_format($total,2,",",".");?></td>
							</tr>
							<tr>
								<td colspan="2" style="border: 1px solid black;">VAT <img src="{{ public_path() . '/files/jepang/Screenshot_103.jpg' }}" width="50"></td>
								<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold;text-align: right;"><?= $ket_harga ?> <?= number_format($vat,2,",",".");?></td>
							</tr>
							<tr>
								<td colspan="2" style="border: 1px solid black;">Total <img src="{{ public_path() . '/files/jepang/Screenshot_104.jpg' }}" width="20"></td>
								<?php $total_all = $total + $vat; ?>
								<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold;text-align: right;"><?= $ket_harga ?> <?= number_format($total_all,2,",",".");?></td>
							</tr>
							<tr>
								<td colspan="2" style="border: 1px solid black;">Delivery <img src="{{ public_path() . '/files/jepang/Screenshot_105.jpg' }}" width="30"></td>
								<td colspan="4" style="border: 1px solid black;">Delivery Order <img src="{{ public_path() . '/files/jepang/Screenshot_106.jpg' }}" width="40"><br>&nbsp;&nbsp;<span style="color: blue;font-weight: bold"><?= date('d-M-Y', strtotime($inv[0]->delivery_order)) ?> </span></td>
								<td colspan="4" style="border: 1px solid black;">Date Order <img src="{{ public_path() . '/files/jepang/Screenshot_107.jpg' }}" width="30"><br>&nbsp;&nbsp;<span style="color: blue;font-weight: bold"><?= date('d-M-Y', strtotime($inv[0]->date_order)) ?> </span></td>
							</tr>
							<tr>	
								<td colspan="2" style="border: 1px solid black;">Payment Term <img src="{{ public_path() . '/files/jepang/Screenshot_108.jpg' }}" width="40"></td>
								<td colspan="4" style="border: 1px solid black;color: blue;font-weight: bold">{{ $inv[0]->payment_term }}</td>
								<td colspan="2" style="border: 1px solid black;">Fill By Acc Dept W/H Tax (%) <img src="{{ public_path() . '/files/jepang/Screenshot_109.jpg' }}" width="80"></td>
								<td colspan="1" style="border: 1px solid black;">Total <br><img src="{{ public_path() . '/files/jepang/Screenshot_110.jpg' }}" width="20"><br> <span style="color: blue;font-weight: bold"> @if($inv[0]->total != null) <?= $inv[0]->total.'%' ?> @endif </span></td>
								<td colspan="1" style="border: 1px solid black;">Service <br><img src="{{ public_path() . '/files/jepang/Screenshot_111.jpg' }}" width="30"><br> <span style="color: blue;font-weight: bold"> @if($inv[0]->service != null) <?= $inv[0]->service.'%' ?> @endif </span></td>
							</tr>
							<tr>
								<td colspan="2" style="border: 1px solid black;">Quotation <img src="{{ public_path() . '/files/jepang/Screenshot_113.jpg' }}" width="30"> <br><b>*Other Quotation Must Be Attached</b><img src="{{ public_path() . '/files/jepang/Screenshot_112.jpg' }}" width="90"></td>
								<td colspan="8" style="border: 1px solid black;color: blue;font-weight: bold"><?= $inv[0]->quotation_supplier ?></td>
							</tr>

							<?php 
							$investmentbudget = count($inv_budget);

							if($investmentbudget != 0) { ?>

								@foreach($inv_budget as $bud)
								<tr>
									<td colspan="2" rowspan="4" style="border: 1px solid black;">Budget No, Name & Balance<br><img src="{{ public_path() . '/files/jepang/Screenshot_11.jpg' }}" width="100"></td>
									<td colspan="2" style="border: 1px solid black;">Budget</td>
									<td colspan="2" style="border: 1px solid black;">Budget No & Name</td>
									<td colspan="1" style="border: 1px solid black;">Beg Bal <img src="{{ public_path() . '/files/jepang/Screenshot_8.jpg' }}" width="20"> <br> <span style="color: blue;font-weight: bold"></span></td>
									<td colspan="1" style="border: 1px solid black;">Amount <img src="{{ public_path() . '/files/jepang/Screenshot_9.jpg' }}" width="20"><br> <span style="color: blue;font-weight: bold"></span></td>
									<td colspan="2" style="border: 1px solid black;">End Bal (US$) <img src="{{ public_path() . '/files/jepang/Screenshot_10.jpg' }}" width="30">  <span style="color: blue;font-weight: bold"></span></td>
								</tr>

								@if($bud->category_budget == "On Budget")
								<tr>
									<td colspan="2" style="border: 1px solid black;">Budget No. <img src="{{ public_path() . '/files/jepang/Screenshot_12.jpg' }}" width="30"> <br> Budget Name <img src="{{ public_path() . '/files/jepang/Screenshot_13.jpg' }}" width="30"></td>
									<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold">{{ $bud->budget_no }} <br> {{ $bud->budget_name }}</td>
									<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold">$ <?= number_format($bud->sisa,2,",",".");?></span></td>
									<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"> $ <?= number_format($bud->total,2,",",".");?></span></td>
									<td colspan="2" style="border: 1px solid black;"> <span style="color: blue;font-weight: bold">$ <?= number_format($bud->sisa - $bud->total, 2, ',', '.'); ?></span></td>
								</tr>
								<tr>
									<td colspan="2" style="border: 1px solid black;">Shifting Budget No. <img src="{{ public_path() . '/files/jepang/Screenshot_14.jpg' }}" width="40"> <br> Budget Name <img src="{{ public_path() . '/files/jepang/Screenshot_13.jpg' }}" width="30"></td>
									<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold"> </td>
									<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"> </span></td>
									<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
									<td colspan="2" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
								</tr>
								<tr>
									<td colspan="8" style="border: 1px solid black;">Out Of Budget <img src="{{ public_path() . '/files/jepang/Screenshot_16.jpg' }}" width="40"></td>
								</tr>

								@elseif($bud->category_budget == "Shifting")
								<tr>
									<td colspan="2" style="border: 1px solid black;">Budget No. <img src="{{ public_path() . '/files/jepang/Screenshot_12.jpg' }}" width="30"> <br> Budget Name <img src="{{ public_path() . '/files/jepang/Screenshot_13.jpg' }}" width="30"></td>
									<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold"> </td>
									<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
									<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"> </span></td>
									<td colspan="2" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
								</tr>

								<tr>
									<td colspan="2" style="border: 1px solid black;">Shifting Budget No. <img src="{{ public_path() . '/files/jepang/Screenshot_14.jpg' }}" width="40"> <br> Budget Name <img src="{{ public_path() . '/files/jepang/Screenshot_13.jpg' }}" width="30"></td>
									<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold">{{ $bud->budget_no }} <br> {{ $bud->budget_name }}</td>
									<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"> $ <?= number_format($bud->sisa,2,",",".");?></span></td>
									<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold">$ <?= number_format($bud->total,2,",",".");?></span></td>
									<td colspan="2" style="border: 1px solid black;"><span style="color: blue;font-weight: bold">$ <?= number_format($bud->sisa - $bud->total,2,",",".");?></span></td>
								</tr>
								<tr>
									<td colspan="8" style="border: 1px solid black;">Out Of Budget <img src="{{ public_path() . '/files/jepang/Screenshot_16.jpg' }}" width="40"></td>
								</tr>

								@elseif($bud->category_budget == "Out Of Budget")
								<tr>
									<td colspan="2" style="border: 1px solid black;">Budget No. <img src="{{ public_path() . '/files/jepang/Screenshot_12.jpg' }}" width="30"> <br> Budget Name <img src="{{ public_path() . '/files/jepang/Screenshot_13.jpg' }}" width="30"></td>
									<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold"> </td>
									<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
									<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"> </span></td>
									<td colspan="2" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
								</tr>
								<tr>
									<td colspan="2" style="border: 1px solid black;">Shifting Budget No. <img src="{{ public_path() . '/files/jepang/Screenshot_14.jpg' }}" width="40"> <br> Budget Name <img src="{{ public_path() . '/files/jepang/Screenshot_13.jpg' }}" width="30"></td>
									<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold"> </td>
									<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"> </span></td>
									<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
									<td colspan="2" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
								</tr>
								<tr>
									<td colspan="2" style="border: 1px solid black;"><span style="color: blue;font-weight: bold">Out Of Budget <img src="{{ public_path() . '/files/jepang/Screenshot_16.jpg' }}" width="40"></span></td>
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
								<td colspan="2" rowspan="4" style="border: 1px solid black;">Budget No, Name & Balance<br><img src="{{ public_path() . '/files/jepang/Screenshot_11.jpg' }}" width="100"></td>
								<td colspan="2" style="border: 1px solid black;">Budget</td>
								<td colspan="2" style="border: 1px solid black;">Budget No & Name</td>
								<td colspan="1" style="border: 1px solid black;">Beg Bal <img src="{{ public_path() . '/files/jepang/Screenshot_8.jpg' }}" width="20"> <br> <span style="color: blue;font-weight: bold"></span></td>
								<td colspan="1" style="border: 1px solid black;">Amount <img src="{{ public_path() . '/files/jepang/Screenshot_9.jpg' }}" width="20"><br> <span style="color: blue;font-weight: bold"></span></td>
								<td colspan="2" style="border: 1px solid black;">End Bal (US$) <img src="{{ public_path() . '/files/jepang/Screenshot_10.jpg' }}" width="30">  <span style="color: blue;font-weight: bold"></span></td>
							</tr>
							<tr>
								<td colspan="2" style="border: 1px solid black;">Budget No. <img src="{{ public_path() . '/files/jepang/Screenshot_12.jpg' }}" width="30"> <br> Budget Name <img src="{{ public_path() . '/files/jepang/Screenshot_13.jpg' }}" width="30"></td>
								<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold"> </td>
								<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
								<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"> </span></td>
								<td colspan="2" style="border: 1px solid black;"> <span style="color: blue;font-weight: bold"></span></td>
							</tr>
							<tr>
								<td colspan="2" style="border: 1px solid black;">Shifting Budget No. <img src="{{ public_path() . '/files/jepang/Screenshot_14.jpg' }}" width="40"> <br> Budget Name <img src="{{ public_path() . '/files/jepang/Screenshot_13.jpg' }}" width="30"></td>
								<td colspan="2" style="border: 1px solid black;color: blue;font-weight: bold"> </td>
								<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"> </span></td>
								<td colspan="1" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
								<td colspan="2" style="border: 1px solid black;"><span style="color: blue;font-weight: bold"></span></td>
							</tr>
							<tr>
								<td colspan="8" style="border: 1px solid black;">Out Of Budget <img src="{{ public_path() . '/files/jepang/Screenshot_16.jpg' }}" width="40"></td>
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
								<td colspan="4" rowspan="3" style="text-align: left;margin-left: 10px">Note <img src="{{ public_path() . '/files/jepang/Screenshot_17.jpg' }}" width="20"> <span style="color:blue;font-weight: bold"><br><?= $inv[0]->note ?> </span>
								</td>
								<td colspan="2" rowspan="2">Approval By Acc Manager </td>
								<td colspan="4">Checked By Acc Staff <img src="{{ public_path() . '/files/jepang/Screenshot_18.jpg' }}" width="60"></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="2">Tax Effect <img src="{{ public_path() . '/files/jepang/Screenshot_19.jpg' }}" width="25"></td>
								<td colspan="2">Budget Balance <img src="{{ public_path() . '/files/jepang/Screenshot_20.jpg' }}" width="30"></td>
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