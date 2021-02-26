<?php $__env->startSection('stylesheets'); ?>
<link href="<?php echo e(url("css/jquery.gritter.css")); ?>" rel="stylesheet">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		font-size: 16px;
	}
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#tableBodyResume > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}

	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}
	
	#loading { display: none; }

	.radio {
			display: inline-block;
			position: relative;
			margin-bottom: 12px;
			cursor: pointer;
			font-size: 16px;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}

		/* Hide the browser's default radio button */
		.radio input {
			position: absolute;
			opacity: 0;
			cursor: pointer;
		}

		/* Create a custom radio button */
		.checkmark {
			position: absolute;
			top: 0;
			left: 0;
			height: 25px;
			width: 25px;
			background-color: #ccc;
			border-radius: 50%;
		}

		/* On mouse-over, add a grey background color */
		.radio:hover input ~ .checkmark {
			background-color: #ccc;
		}

		/* When the radio button is checked, add a blue background */
		.radio input:checked ~ .checkmark {
			background-color: #2196F3;
		}

		/* Create the indicator (the dot/circle - hidden when not checked) */
		.checkmark:after {
			content: "";
			position: absolute;
			display: none;
		}

		/* Show the indicator (dot/circle) when checked */
		.radio input:checked ~ .checkmark:after {
			display: block;
		}

		/* Style the indicator (dot/circle) */
		.radio .checkmark:after {
			top: 9px;
			left: 9px;
			width: 8px;
			height: 8px;
			border-radius: 50%;
			background: white;
		}
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('header'); ?>
<section class="content-header">
	<h1>
		<?php echo e($title); ?>
		<small><span class="text-purple"> <?php echo e($title_jp); ?></span></small>
		<button class="btn btn-primary pull-right" data-toggle="modal" data-target="#modalGuidance">
			<i class="fa fa-book"></i> &nbsp;<b>Petunjuk</b>
		</button>
		<a class="btn btn-danger pull-right" href="{{ url('index/recorder/cdm_report') }}" style="margin-right: 10px"><i class="fa fa-file-pdf-o"></i> &nbsp;Report</a>
	</h1>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Sedang memproses, tunggu sebentar <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="location">
	<div class="row">
		<div class="col-xs-5">
				<span style="font-weight: bold; font-size: 16px;">Scan ID Card:</span>
				<div class="input-group" id="scan_tag" style="padding-bottom: 10px">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
						<i class="glyphicon glyphicon-qrcode"></i>
					</div>
					<input type="text" style="text-align: center; border-color: black;font-size: 23px;height: 40px" class="form-control" id="tag" name="tag" placeholder="Scan ID Card" required>
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
						<i class="glyphicon glyphicon-qrcode"></i>
					</div>
				</div>
				<div class="input-group" id="scan_tag_success" style="padding-bottom: 10px">
					<div class="col-xs-4">
						<div class="row">
							<input type="text" id="op" style="width: 100%; height: 40px; font-size: 20px; text-align: center;border: 1px solid black" disabled placeholder="Employee ID">
						</div>
					</div>
					<div class="col-xs-5">
						<div class="row">
							<input type="text" id="op2" style="width: 100%; height: 40px; font-size: 20px; text-align: center;border: 1px solid black" disabled placeholder="Name">
						</div>
					</div>
					<div class="col-xs-3">
						<div class="row" style="padding-left: 5px">
							<button class="btn btn-danger" onclick="cancelEmp()" style="width: 100%;height: 40px;font-size: 20px;vertical-align: middle;">
								<b>CLEAR</b>
							</button>
						</div>
					</div>
				</div>
			<div class="box box-solid">
				<div class="box-body">
					<span style="font-size: 20px; font-weight: bold;">DAFTAR ITEM:</span>
					<table class="table table-hover table-striped" id="tableList" style="width: 100%;">
						<thead>
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 1%;">Product</th>
								<th style="width: 7%;">Type</th>
								<th style="width: 7%;">Part</th>
								<th style="width: 1%;">Color</th>
							</tr>					
						</thead>
						<tbody id="tableBodyList">
						</tbody>
						<tfoot>
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
		<div class="col-xs-7">
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-3">
							<span style="font-weight: bold; font-size: 16px;">Product:</span>
							<input type="text" id="product" style="width: 100%; height: 40px; font-size: 20px; text-align: center;" readonly>
							<input type="hidden" id="save_type" style="width: 100%; height: 40px; font-size: 20px; text-align: center;">
							<input type="hidden" id="id_cdm" style="width: 100%; height: 40px; font-size: 20px; text-align: center;">
						</div>
						<div class="col-xs-3">
							<span style="font-weight: bold; font-size: 16px;">Type:</span>
							<input type="text" id="type" style="width: 100%; height: 40px; font-size: 20px; text-align: center;" readonly>
						</div>
						<div class="col-xs-3">
							<span style="font-weight: bold; font-size: 16px;">Part:</span>
							<input type="text" id="part" style="width: 100%; height: 40px; font-size: 20px; text-align: center;" readonly>
						</div>
						<div class="col-xs-3">
							<span style="font-weight: bold; font-size: 16px;">Color:</span>
							<input type="text" id="color" style="width: 100%; height: 40px; font-size: 20px; text-align: center;" readonly>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-4">
							<span style="font-weight: bold; font-size: 16px;">Injection Date:</span>
							<input type="text" id="injection_date" style="width: 100%; height: 40px; font-size: 20px; text-align: center;" placeholder="Injection Date" readonly>
						</div>
						<div class="col-xs-4">
							<span style="font-weight: bold; font-size: 16px;">Machine:</span>
							<select name="machine" id="machine" class="form-group" style="width: 100%; height: 40px; font-size: 20px; text-align: center;" data-placeholder="Select Machine">
								@foreach($machine as $machine)
									<option value="{{$machine}}">{{$machine}}</option>
								@endforeach
							</select>
						</div>
						<div class="col-xs-4">
							<span style="font-weight: bold; font-size: 16px;">Cavity:</span>
							<select name="cavity" id="cavity" class="form-group" style="width: 100%; height: 40px; font-size: 20px; text-align: center;" data-placeholder="Select Cavity">
							</select>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="awal_head" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Awal Proses</span>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #ffd6a5;height: 100px"><span style="font-weight: bold; font-size: 16px;">A<br>(Panjang Head = 124 - 124.5 mm (Nogisu))</span></center>
								<input id="awal_head_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="headvalue('a',this.value,'awal')">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #ffd6a5;height: 100px"><span style="font-weight: bold; font-size: 16px;">B<br>(Kedalaman Middle Joint Shaft = 22.5 - 22.8 mm (Depht Gauge))</span></center>
								<input id="awal_head_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="headvalue('b',this.value,'awal')">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #ffd6a5;height: 100px"><span style="font-weight: bold; font-size: 16px;">C<br>(Cek Visual Hasil Potong Standard, Tidak Bari)</span></center>
								<div class="radio">
							    	<label class="radio" style="margin-top: 0px;margin-left: 0px">
										<input type="radio" id="awal_head_c" name="awal_head_c" value="OK" onclick="headvalue('c',this.value,'awal')">&nbsp;&nbsp;&nbsp;OK
										<span class="checkmark"></span>
									</label>
									<label class="radio" style="margin-top: 0px">
										<input type="radio" id="awal_head_c" name="awal_head_c" onclick="headvalue('c',this.value,'awal')" value="NG">&nbsp;&nbsp;&nbsp;NG
										<span class="checkmark"></span>
									</label>
								</div>
					        </div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #ffd6a5;height: 100px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="awal_head_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="awal_head_yrf" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Awal Proses</span>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffd6a5;height: 70px"><span style="font-weight: bold; font-size: 16px;vertical-align: middle;">A<br>(139.8 - 140.2 mm (Nogisu))</span></center>
								<input id="awal_head_yrf_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="headyrfvalue('a',this.value,'awal')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffd6a5;height: 70px"><span style="font-weight: bold; font-size: 16px;">B<br>(16.5 - 17.5 mm (Go no go))</span></center>
								<input id="awal_head_yrf_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="headyrfvalue('b',this.value,'awal')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffd6a5;height: 70px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="awal_head_yrf_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="awal_body_yrf" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Awal Proses</span>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffd6a5;height: 70px"><span style="font-weight: bold; font-size: 16px;vertical-align: middle;">A<br>(216.3 - 216.7 mm (Nogisu))</span></center>
								<input id="awal_body_yrf_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="bodyyrfvalue('a',this.value,'awal')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffd6a5;height: 70px"><span style="font-weight: bold; font-size: 16px;">B<br>(10.5 - 11.5 mm (Go no go))</span></center>
								<input id="awal_body_yrf_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="bodyyrfvalue('b',this.value,'awal')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffd6a5;height: 70px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="awal_body_yrf_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="awal_middle" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Awal Proses</span>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffd6a5;height: 100px"><span style="font-weight: bold; font-size: 16px;">A<br>(173.5 mm - 173.7 mm)</span></center>
								<input id="awal_middle_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="middlevalue('a',this.value,'awal')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffd6a5;height: 100px"><span style="font-weight: bold; font-size: 16px;">B<br>(11.8 mm - 11.9 mm)</span></center>
								<input id="awal_middle_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="middlevalue('a',this.value,'awal')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffd6a5;height: 100px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="awal_middle_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="awal_foot" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Awal Proses</span>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #ffd6a5;height: 100px"><span style="font-weight: bold; font-size: 16px;">A<br>(13.3 - 14.7 mm)</span></center>
								<input id="awal_foot_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="footvalue('a',this.value,'awal')">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #ffd6a5;height: 100px"><span style="font-weight: bold; font-size: 16px;">B<br>(62.8 - 63.1 mm)</span></center>
								<input id="awal_foot_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="footvalue('a',this.value,'awal')">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #ffd6a5;height: 100px"><span style="font-weight: bold; font-size: 16px;">C<br>(Cek Visual Tidak Kizu / Kake dan Hasil Tidak Bari)</span></center>
								<div class="radio">
							    	<label class="radio" style="margin-top: 0px;margin-left: 0px">
										<input type="radio" id="awal_foot_c" name="awal_foot_c" value="OK" onclick="footvalue('c',this.value,'awal')">&nbsp;&nbsp;&nbsp;OK
										<span class="checkmark"></span>
									</label>
									<label class="radio" style="margin-top: 0px">
										<input type="radio" id="awal_foot_c" name="awal_foot_c" onclick="footvalue('c',this.value,'awal')" value="NG">&nbsp;&nbsp;&nbsp;NG
										<span class="checkmark"></span>
									</label>
								</div>
					        </div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #ffd6a5;height: 100px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="awal_foot_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>



				<div class="col-xs-12" id="ist1_head" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Setelah Istirahat 1</span>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #9bf6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">A<br>(Panjang Head = 124 - 124.5 mm (Nogisu))</span></center>
								<input id="ist1_head_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="headvalue('a',this.value,'ist1')">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #9bf6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">B<br>(Kedalaman Middle Joint Shaft = 22.5 - 22.8 mm (Depht Gauge))</span></center>
								<input id="ist1_head_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="headvalue('b',this.value,'ist1')">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #9bf6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">C<br>(Cek Visual Hasil Potong Standard, Tidak Bari)</span></center>
								<div class="radio">
							    	<label class="radio" style="margin-top: 0px;margin-left: 0px">
										<input type="radio" id="ist1_head_c" name="ist1_head_c" value="OK" onclick="headvalue('c',this.value,'ist1')">&nbsp;&nbsp;&nbsp;OK
										<span class="checkmark"></span>
									</label>
									<label class="radio" style="margin-top: 0px">
										<input type="radio" id="ist1_head_c" name="ist1_head_c" onclick="headvalue('c',this.value,'ist1')" value="NG">&nbsp;&nbsp;&nbsp;NG
										<span class="checkmark"></span>
									</label>
								</div>
					        </div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #9bf6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="ist1_head_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="ist1_head_yrf" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Setelah Istirahat 1</span>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #9bf6ff;height: 70px"><span style="font-weight: bold; font-size: 16px;">A<br>(139.8 - 140.2 mm (Nogisu))</span></center>
								<input id="ist1_head_yrf_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="headyrfvalue('a',this.value,'ist1')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #9bf6ff;height: 70px"><span style="font-weight: bold; font-size: 16px;">B<br>(16.5 - 17.5 mm (Go no go))</span></center>
								<input id="ist1_head_yrf_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="headyrfvalue('b',this.value,'ist1')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #9bf6ff;height: 70px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="ist1_head_yrf_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="ist1_body_yrf" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Setelah Istirahat 1</span>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #9bf6ff;height: 70px"><span style="font-weight: bold; font-size: 16px;vertical-align: middle;">A<br>(216.3 - 216.7 mm (Nogisu))</span></center>
								<input id="ist1_body_yrf_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="bodyyrfvalue('a',this.value,'ist1')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #9bf6ff;height: 70px"><span style="font-weight: bold; font-size: 16px;">B<br>(10.5 - 11.5 mm (Go no go))</span></center>
								<input id="ist1_body_yrf_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="bodyyrfvalue('b',this.value,'ist1')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #9bf6ff;height: 70px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="ist1_body_yrf_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="ist1_middle" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Setelah Istirahat 1</span>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #9bf6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">A<br>(173.5 mm - 173.7 mm)</span></center>
								<input id="ist1_middle_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="middlevalue('a',this.value,'ist1')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #9bf6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">B<br>(11.8 mm - 11.9 mm)</span></center>
								<input id="ist1_middle_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="middlevalue('a',this.value,'ist1')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #9bf6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="ist1_middle_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="ist1_foot" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Setelah Istirahat 1</span>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #9bf6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">A<br>(13.3 - 14.7 mm)</span></center>
								<input id="ist1_foot_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="footvalue('a',this.value,'ist1')">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #9bf6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">B<br>(62.8 - 63.1 mm)</span></center>
								<input id="ist1_foot_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="footvalue('a',this.value,'ist1')">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #9bf6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">C<br>(Cek Visual Tidak Kizu / Kake dan Hasil Tidak Bari)</span></center>
								<div class="radio">
							    	<label class="radio" style="margin-top: 0px;margin-left: 0px">
										<input type="radio" id="ist1_foot_c" name="ist1_foot_c" value="OK" onclick="footvalue('c',this.value,'ist1')">&nbsp;&nbsp;&nbsp;OK
										<span class="checkmark"></span>
									</label>
									<label class="radio" style="margin-top: 0px">
										<input type="radio" id="ist1_foot_c" name="ist1_foot_c" onclick="footvalue('c',this.value,'ist1')" value="NG">&nbsp;&nbsp;&nbsp;NG
										<span class="checkmark"></span>
									</label>
								</div>
					        </div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #9bf6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="ist1_foot_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>


				<div class="col-xs-12" id="ist2_head" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Setelah Istirahat 2</span>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #ffc6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">A<br>(Panjang Head = 124 - 124.5 mm (Nogisu))</span></center>
								<input id="ist2_head_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="headvalue('a',this.value,'ist2')">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #ffc6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">B<br>(Kedalaman Middle Joint Shaft = 22.5 - 22.8 mm (Depht Gauge))</span></center>
								<input id="ist2_head_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="headvalue('b',this.value,'ist2')">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #ffc6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">C<br>(Cek Visual Hasil Potong Standard, Tidak Bari)</span></center>
								<div class="radio">
							    	<label class="radio" style="margin-top: 0px;margin-left: 0px">
										<input type="radio" id="ist2_head_c" name="ist2_head_c" value="OK" onclick="headvalue('c',this.value,'ist2')">&nbsp;&nbsp;&nbsp;OK
										<span class="checkmark"></span>
									</label>
									<label class="radio" style="margin-top: 0px">
										<input type="radio" id="ist2_head_c" name="ist2_head_c" onclick="headvalue('c',this.value,'ist2')" value="NG">&nbsp;&nbsp;&nbsp;NG
										<span class="checkmark"></span>
									</label>
								</div>
					        </div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #ffc6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="ist2_head_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="ist2_head_yrf" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Setelah Istirahat 2</span>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffc6ff;height: 70px"><span style="font-weight: bold; font-size: 16px;">A<br>(139.8 - 140.2 mm (Nogisu))</span></center>
								<input id="ist2_head_yrf_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="headyrfvalue('a',this.value,'ist2')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffc6ff;height: 70px"><span style="font-weight: bold; font-size: 16px;">B<br>(16.5 - 17.5 mm (Go no go))</span></center>
								<input id="ist2_head_yrf_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="headyrfvalue('b',this.value,'ist2')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffc6ff;height: 70px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="ist2_head_yrf_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="ist2_body_yrf" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Setelah Istirahat 2</span>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffc6ff;height: 70px"><span style="font-weight: bold; font-size: 16px;vertical-align: middle;">A<br>(216.3 - 216.7 mm (Nogisu))</span></center>
								<input id="ist2_body_yrf_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="bodyyrfvalue('a',this.value,'ist2')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffc6ff;height: 70px"><span style="font-weight: bold; font-size: 16px;">B<br>(10.5 - 11.5 mm (Go no go))</span></center>
								<input id="ist2_body_yrf_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="bodyyrfvalue('b',this.value,'ist2')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffc6ff;height: 70px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="ist2_body_yrf_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="ist2_middle" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Setelah Istirahat 2</span>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffc6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">A<br>(173.5 mm - 173.7 mm)</span></center>
								<input id="ist2_middle_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="middlevalue('a',this.value,'ist2')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffc6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">B<br>(11.8 mm - 11.9 mm)</span></center>
								<input id="ist2_middle_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="middlevalue('a',this.value,'ist2')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #ffc6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="ist2_middle_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="ist2_foot" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Setelah Istirahat 2</span>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #ffc6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">A<br>(13.3 - 14.7 mm)</span></center>
								<input id="ist2_foot_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="footvalue('a',this.value,'ist2')">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #ffc6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">B<br>(62.8 - 63.1 mm)</span></center>
								<input id="ist2_foot_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="footvalue('a',this.value,'ist2')">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #ffc6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">C<br>(Cek Visual Tidak Kizu / Kake dan Hasil Tidak Bari)</span></center>
								<div class="radio">
							    	<label class="radio" style="margin-top: 0px;margin-left: 0px">
										<input type="radio" id="ist2_foot_c" name="ist2_foot_c" value="OK" onclick="footvalue('c',this.value,'ist2')">&nbsp;&nbsp;&nbsp;OK
										<span class="checkmark"></span>
									</label>
									<label class="radio" style="margin-top: 0px">
										<input type="radio" id="ist2_foot_c" name="ist2_foot_c" onclick="footvalue('c',this.value,'ist2')" value="NG">&nbsp;&nbsp;&nbsp;NG
										<span class="checkmark"></span>
									</label>
								</div>
					        </div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #ffc6ff;height: 100px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="ist2_foot_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>


				<div class="col-xs-12" id="ist3_head" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Setelah Istirahat 3</span>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #caffbf;height: 100px"><span style="font-weight: bold; font-size: 16px;">A<br>(Panjang Head = 124 - 124.5 mm (Nogisu))</span></center>
								<input id="ist3_head_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="headvalue('a',this.value,'ist3')">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #caffbf;height: 100px"><span style="font-weight: bold; font-size: 16px;">B<br>(Kedalaman Middle Joint Shaft = 22.5 - 22.8 mm (Depht Gauge))</span></center>
								<input id="ist3_head_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="headvalue('b',this.value,'ist3')">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #caffbf;height: 100px"><span style="font-weight: bold; font-size: 16px;">C<br>(Cek Visual Hasil Potong Standard, Tidak Bari)</span></center>
								<div class="radio">
							    	<label class="radio" style="margin-top: 0px;margin-left: 0px">
										<input type="radio" id="ist3_head_c" name="ist3_head_c" value="OK" onclick="headvalue('c',this.value,'ist3')">&nbsp;&nbsp;&nbsp;OK
										<span class="checkmark"></span>
									</label>
									<label class="radio" style="margin-top: 0px">
										<input type="radio" id="ist3_head_c" name="ist3_head_c" onclick="headvalue('c',this.value,'ist3')" value="NG">&nbsp;&nbsp;&nbsp;NG
										<span class="checkmark"></span>
									</label>
								</div>
					        </div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #caffbf;height: 100px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="ist3_head_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="ist3_head_yrf" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Setelah Istirahat 3</span>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #caffbf;height: 70px"><span style="font-weight: bold; font-size: 16px;">A<br>(139.8 - 140.2 mm (Nogisu))</span></center>
								<input id="ist3_head_yrf_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="headyrfvalue('a',this.value,'ist3')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #caffbf;height: 70px"><span style="font-weight: bold; font-size: 16px;">B<br>(16.5 - 17.5 mm (Go no go))</span></center>
								<input id="ist3_head_yrf_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="headyrfvalue('b',this.value,'ist3')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #caffbf;height: 70px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="ist3_head_yrf_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="ist3_body_yrf" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Setelah Istirahat 3</span>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #caffbf;height: 70px"><span style="font-weight: bold; font-size: 16px;vertical-align: middle;">A<br>(216.3 - 216.7 mm (Nogisu))</span></center>
								<input id="ist3_body_yrf_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="bodyyrfvalue('a',this.value,'ist3')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #caffbf;height: 70px"><span style="font-weight: bold; font-size: 16px;">B<br>(10.5 - 11.5 mm (Go no go))</span></center>
								<input id="ist3_body_yrf_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="bodyyrfvalue('b',this.value,'ist3')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #caffbf;height: 70px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="ist3_body_yrf_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="ist3_middle" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Setelah Istirahat 3</span>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #caffbf;height: 100px"><span style="font-weight: bold; font-size: 16px;">A<br>(173.5 mm - 173.7 mm)</span></center>
								<input id="ist3_middle_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="middlevalue('a',this.value,'ist3')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #caffbf;height: 100px"><span style="font-weight: bold; font-size: 16px;">B<br>(11.8 mm - 11.9 mm)</span></center>
								<input id="ist3_middle_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="middlevalue('a',this.value,'ist3')">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="input-group">
								<center style="background-color: #caffbf;height: 100px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="ist3_middle_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" id="ist3_foot" style="padding-bottom: 10px">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Setelah Istirahat 3</span>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #caffbf;height: 100px"><span style="font-weight: bold; font-size: 16px;">A<br>(13.3 - 14.7 mm)</span></center>
								<input id="ist3_foot_a" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="A" onkeyup="footvalue('a',this.value,'ist3')">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #caffbf;height: 100px"><span style="font-weight: bold; font-size: 16px;">B<br>(62.8 - 63.1 mm)</span></center>
								<input id="ist3_foot_b" style="font-size: 20px; height: 40px; text-align: center;" type="number" class="form-control" value="0" placeholder="B" onkeyup="footvalue('a',this.value,'ist3')">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #caffbf;height: 100px"><span style="font-weight: bold; font-size: 16px;">C<br>(Cek Visual Tidak Kizu / Kake dan Hasil Tidak Bari)</span></center>
								<div class="radio">
							    	<label class="radio" style="margin-top: 0px;margin-left: 0px">
										<input type="radio" id="ist3_foot_c" name="ist3_foot_c" value="OK" onclick="footvalue('c',this.value,'ist3')">&nbsp;&nbsp;&nbsp;OK
										<span class="checkmark"></span>
									</label>
									<label class="radio" style="margin-top: 0px">
										<input type="radio" id="ist3_foot_c" name="ist3_foot_c" onclick="footvalue('c',this.value,'ist3')" value="NG">&nbsp;&nbsp;&nbsp;NG
										<span class="checkmark"></span>
									</label>
								</div>
					        </div>
						</div>
						<div class="col-xs-3">
							<div class="input-group">
								<center style="background-color: #caffbf;height: 100px"><span style="font-weight: bold; font-size: 16px;">Status</span></center>
								<input id="ist3_foot_status" style="font-size: 20px; height: 40px; text-align: center;" type="text" class="form-control" value="" readonly placeholder="Status">
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<!-- <div class="row"> -->
						<button class="btn btn-success btn-block" style="height: 50px;font-size: 30px;font-weight: bold;" onclick="inputCdm()">
							<i class="fa fa-save"></i> SAVE
						</button>
					<!-- </div> -->
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-top: 20px">
			<div class="row">
				<div class="col-xs-12">
					<div class="box box-solid" style="overflow-x: scroll;">
						<div class="box-body">
							<span style="font-size: 20px; font-weight: bold;" id="">HASIL CEK PRODUK PERTAMA RECORDER (<?php echo date('d-M-Y', strtotime('-1 month')); ?> - <?php echo date('d-M-Y'); ?>)</span>
							<table class="table table-hover table-striped table-bordered" id="tableResume">
								<thead>
									<tr style="text-align:center">
										<th style="width: 1%;" rowspan="2">No.</th>
										<th style="width: 1%;" rowspan="2">Product</th>
										<th style="width: 1%;" rowspan="2">Injection</th>
										<th style="width: 1%;background-color: #ffd6a5" colspan="4">Awal</th>
										<th style="width: 1%;background-color: #9bf6ff" colspan="4">Istirahat 1</th>
										<th style="width: 1%;background-color: #ffc6ff" colspan="4">Istirahat 2</th>
										<th style="width: 1%;background-color: #caffbf" colspan="4">Istirahat 3</th>
										<th style="width: 1%;" rowspan="2">By</th>
										<th style="width: 1%;" rowspan="2">At</th>
									</tr>
									<tr>
										<th style="width: 1%;background-color: #ffd6a5">A</th>
										<th style="width: 1%;background-color: #ffd6a5">B</th>
										<th style="width: 1%;background-color: #ffd6a5">C</th>
										<th style="width: 1%;background-color: #ffd6a5">Stts</th>
										<th style="width: 1%;background-color: #9bf6ff">A</th>
										<th style="width: 1%;background-color: #9bf6ff">B</th>
										<th style="width: 1%;background-color: #9bf6ff">C</th>
										<th style="width: 1%;background-color: #9bf6ff">Stts</th>
										<th style="width: 1%;background-color: #ffc6ff">A</th>
										<th style="width: 1%;background-color: #ffc6ff">B</th>
										<th style="width: 1%;background-color: #ffc6ff">C</th>
										<th style="width: 1%;background-color: #ffc6ff">Stts</th>
										<th style="width: 1%;background-color: #caffbf">A</th>
										<th style="width: 1%;background-color: #caffbf">B</th>
										<th style="width: 1%;background-color: #caffbf">C</th>
										<th style="width: 1%;background-color: #caffbf">Stts</th>
									</tr>
								</thead>
								<tbody id="tableBodyResume">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<div class="modal fade" id="modalGuidance">
	<div class="modal-dialog modal-lg" style="width: 1200px">
		<div class="modal-content">
			<div class="modal-header">
				<center style="background-color: #ffac26;color: white">
					<span style="font-weight: bold; font-size: 3vw;">Petunjuk Pengukuran</span>
				</center>
				<hr>
				<div class="modal-body" style="min-height: 950px; padding-bottom: 5px;">
					<div class="col-xs-12">
						<div class="row">
							<center style="background-color: #00cf45"><span style="font-size: 1.5vw;font-weight: bold;">HEAD</span></center>
						</div>
					</div>
					<div class="col-xs-6" style="padding-top: 10px;padding-bottom: 20px">
						<div class="row">
							<span style="font-size: 1.7vw">Standard:</span><br>
							<span style="font-size: 1.4vw">A. Panjang Head = 124 - 124,5 mm (Nogisu)</span><br>
							<span style="font-size: 1.4vw">B. Kedalaman middle joint shaft = 22,5 - 22,8 mm (Depht gauge)</span><br>
							<span style="font-size: 1.4vw">C. Cek celah <= 0,20 mm (Thickness gauge)</span><br>
							<span style="font-size: 1.4vw">D. Cek Visual hasil hasil potong standart tidak bari</span><br>
						</div>
					</div>
					<div class="col-xs-6" style="padding-top: 10px;padding-bottom: 20px">
						<div class="row">
							<img width="200px" src="{{ url('/data_file/recorder/cdm/head_a.png') }}">
							<img width="200px" src="{{ url('/data_file/recorder/cdm/head_c.png') }}">
							<img width="200px" src="{{ url('/data_file/recorder/cdm/head_d.png') }}">
						</div>
					</div>

					<div class="col-xs-12">
						<div class="row">
							<center style="background-color: #00cf45"><span style="font-size: 1.5vw;font-weight: bold;">MIDDLE</span></center>
						</div>
					</div>
					<div class="col-xs-6" style="padding-top: 10px;padding-bottom: 20px">
						<div class="row">
							<span style="font-size: 1.7vw">Standard:</span><br>
							<span style="font-size: 1.4vw">A. 173.5 mm - 173.7 mm</span><br>
							<span style="font-size: 1.4vw">B. 11.8 mm - 11.9 mm</span><br>
							<span style="font-size: 1.4vw">C. Hasil pot. / injeksi tidak bari dan aus</span><br>
						</div>
					</div>
					<div class="col-xs-6" style="padding-top: 10px;padding-bottom: 20px">
						<div class="row">
							<img width="250px" src="{{ url('/data_file/recorder/cdm/middle_a.png') }}">
							<img width="250px" src="{{ url('/data_file/recorder/cdm/middle_b.png') }}">
							<img width="300px" src="{{ url('/data_file/recorder/cdm/middle_c.png') }}">
						</div>
					</div>

					<div class="col-xs-12">
						<div class="row">
							<center style="background-color: #00cf45"><span style="font-size: 1.5vw;font-weight: bold;">FOOT</span></center>
						</div>
					</div>
					<div class="col-xs-6" style="padding-top: 10px;padding-bottom: 20px">
						<div class="row">
							<span style="font-size: 1.7vw">Standard:</span><br>
							<span style="font-size: 1.4vw">A. 13.3 - 14.7 mm</span><br>
							<span style="font-size: 1.4vw">B. 62.8 - 63.1 mm</span><br>
							<span style="font-size: 1.4vw">C. Cek visual tidak kizu/kake dan hasil tidak bari</span><br>
						</div>
					</div>
					<div class="col-xs-6" style="padding-top: 10px;padding-bottom: 20px">
						<div class="row">
							<img width="250px" src="{{ url('/data_file/recorder/cdm/foot_a_b.png') }}">
							<img width="250px" src="{{ url('/data_file/recorder/cdm/foot_c.png') }}">
						</div>
					</div>

					<div class="col-xs-12">
						<div class="row">
							<center style="background-color: #00cf45"><span style="font-size: 1.5vw;font-weight: bold;">HEAD PIECE YRF</span></center>
						</div>
					</div>
					<div class="col-xs-6" style="padding-top: 10px;padding-bottom: 20px">
						<div class="row">
							<span style="font-size: 1.7vw">Standard:</span><br>
							<span style="font-size: 1.4vw">A. 139.8 - 140.2 mm</span><br>
							<span style="font-size: 1.4vw">B. 16.5 - 17.5 mm</span><br>
							<br>
						</div>
					</div>
					<div class="col-xs-6" style="padding-top: 10px;padding-bottom: 20px">
						<div class="row">
							<img width="400px" src="{{ url('/data_file/recorder/cdm/head_yrf.png') }}">
						</div>
					</div>

					<div class="col-xs-12">
						<div class="row">
							<center style="background-color: #00cf45"><span style="font-size: 1.5vw;font-weight: bold;">BODY PIECE YRF</span></center>
						</div>
					</div>
					<div class="col-xs-6" style="padding-top: 10px;padding-bottom: 20px">
						<div class="row">
							<span style="font-size: 1.7vw">Standard:</span><br>
							<span style="font-size: 1.4vw">A. 216.3 - 216.7 mm</span><br>
							<span style="font-size: 1.4vw">B. 10.5 - 11.5 mm</span><br>
							<br>
						</div>
					</div>
					<div class="col-xs-6" style="padding-top: 10px;padding-bottom: 20px">
						<div class="row">
							<img width="400px" src="{{ url('/data_file/recorder/cdm/body_yrf.png') }}">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="col-xs-12">
						<div class="row" id="skillFooter">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</section>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(url("js/jquery.gritter.min.js")); ?>"></script>
<script src="<?php echo e(url("js/dataTables.buttons.min.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.flash.min.js")); ?>"></script>
<script src="<?php echo e(url("js/jszip.min.js")); ?>"></script>
<script src="<?php echo e(url("js/vfs_fonts.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.html5.min.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.print.min.js")); ?>"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script src="<?php echo e(url("js/jsQR.js")); ?>"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2();
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		fetchProductList();
		fetchResumeCdm();
		$('#tag').focus();
		$('#tag').val("");
		$('#op').val("");
		$('#op2').val("");
		$('#scan_tag_success').hide();

		emptyAll();

		$('#injection_date').datepicker({
	      autoclose: true,
	      format: 'yyyy-mm-dd',
	      todayHighlight: true
	    });
	});

	function emptyAll() {
		$('#save_type').val('INPUT');
		$('#id_cdm').val('');
		$('#product').val("");
		$('#part').val("");
		$('#color').val("");
		$('#type').val("");
		$('#injection_date').val("");
		$('#machine').val("").trigger('change');
		$('#cavity').val("").trigger('change');

		$('#injection_date').prop('disabled',true);
		$('#machine').prop('disabled',true);
		$('#cavity').prop('disabled',true);

		$('#awal_head').hide();
		$('#awal_head_yrf').hide();
		$('#awal_body_yrf').hide();
		$('#awal_middle').hide();
		$('#awal_foot').hide();

		$('#ist1_head').hide();
		$('#ist1_head_yrf').hide();
		$('#ist1_body_yrf').hide();
		$('#ist1_middle').hide();
		$('#ist1_foot').hide();

		$('#ist2_head').hide();
		$('#ist2_head_yrf').hide();
		$('#ist2_body_yrf').hide();
		$('#ist2_middle').hide();
		$('#ist2_foot').hide();

		$('#ist3_head').hide();
		$('#ist3_head_yrf').hide();
		$('#ist3_body_yrf').hide();
		$('#ist3_middle').hide();
		$('#ist3_foot').hide();

		$('#awal_head_a').val("");
		$('#awal_head_b').val("");
		$("input[name=awal_head_c]").prop('checked', false);
		$('#awal_head_status').val("");
		document.getElementById('awal_head_status').style.backgroundColor = "#fff";

		$('#awal_head_yrf_a').val("");
		$('#awal_head_yrf_b').val("");
		$('#awal_head_yrf_status').val("");
		document.getElementById('awal_head_yrf_status').style.backgroundColor = "#fff";

		$('#awal_body_yrf_a').val("");
		$('#awal_body_yrf_b').val("");
		$('#awal_body_yrf_status').val("");
		document.getElementById('awal_body_yrf_status').style.backgroundColor = "#fff";

		$('#awal_middle_a').val("");
		$('#awal_middle_b').val("");
		$('#awal_middle_status').val("");
		document.getElementById('awal_middle_status').style.backgroundColor = "#fff";

		$('#awal_foot_a').val("");
		$('#awal_foot_b').val("");
		$("input[name=awal_foot_c]").prop('checked', false);
		$('#awal_foot_status').val("");
		document.getElementById('awal_foot_status').style.backgroundColor = "#fff";

		$('#ist1_head_a').val("");
		$('#ist1_head_b').val("");
		$("input[name=ist1_head_c]").prop('checked', false);
		$('#ist1_head_status').val("");
		document.getElementById('ist1_head_status').style.backgroundColor = "#fff";

		$('#ist1_head_yrf_a').val("");
		$('#ist1_head_yrf_b').val("");
		$('#ist1_head_yrf_status').val("");
		document.getElementById('ist1_head_yrf_status').style.backgroundColor = "#fff";

		$('#ist1_body_yrf_a').val("");
		$('#ist1_body_yrf_b').val("");
		$('#ist1_body_yrf_status').val("");
		document.getElementById('ist1_body_yrf_status').style.backgroundColor = "#fff";

		$('#ist1_middle_a').val("");
		$('#ist1_middle_b').val("");
		$('#ist1_middle_status').val("");
		document.getElementById('ist1_middle_status').style.backgroundColor = "#fff";

		$('#ist1_foot_a').val("");
		$('#ist1_foot_b').val("");
		$("input[name=ist1_foot_c]").prop('checked', false);
		$('#ist1_foot_status').val("");
		document.getElementById('ist1_foot_status').style.backgroundColor = "#fff";

		$('#ist2_head_a').val("");
		$('#ist2_head_b').val("");
		$("input[name=ist2_head_c]").prop('checked', false);
		$('#ist2_head_status').val("");
		document.getElementById('ist2_head_status').style.backgroundColor = "#fff";

		$('#ist2_head_yrf_a').val("");
		$('#ist2_head_yrf_b').val("");
		$('#ist2_head_yrf_status').val("");
		document.getElementById('ist2_head_yrf_status').style.backgroundColor = "#fff";

		$('#ist2_body_yrf_a').val("");
		$('#ist2_body_yrf_b').val("");
		$('#ist2_body_yrf_status').val("");
		document.getElementById('ist2_body_yrf_status').style.backgroundColor = "#fff";

		$('#ist2_middle_a').val("");
		$('#ist2_middle_b').val("");
		$('#ist2_middle_status').val("");
		document.getElementById('ist2_middle_status').style.backgroundColor = "#fff";

		$('#ist2_foot_a').val("");
		$('#ist2_foot_b').val("");
		$("input[name=ist2_foot_c]").prop('checked', false);
		$('#ist2_foot_status').val("");
		document.getElementById('ist2_foot_status').style.backgroundColor = "#fff";

		$('#ist3_head_a').val("");
		$('#ist3_head_b').val("");
		$("input[name=ist3_head_c]").prop('checked', false);
		$('#ist3_head_status').val("");
		document.getElementById('ist3_head_status').style.backgroundColor = "#fff";

		$('#ist3_head_yrf_a').val("");
		$('#ist3_head_yrf_b').val("");
		$('#ist3_head_yrf_status').val("");
		document.getElementById('ist3_head_yrf_status').style.backgroundColor = "#fff";

		$('#ist3_body_yrf_a').val("");
		$('#ist3_body_yrf_b').val("");
		$('#ist3_body_yrf_status').val("");
		document.getElementById('ist3_body_yrf_status').style.backgroundColor = "#fff";

		$('#ist3_middle_a').val("");
		$('#ist3_middle_b').val("");
		$('#ist3_middle_status').val("");
		document.getElementById('ist3_middle_status').style.backgroundColor = "#fff";

		$('#ist3_foot_a').val("");
		$('#ist3_foot_b').val("");
		$("input[name=ist3_foot_c]").prop('checked', false);
		$('#ist3_foot_status').val("");
		document.getElementById('ist3_foot_status').style.backgroundColor = "#fff";
	}

	function cancelEmp() {
		$('#op').val("");
		$('#op2').val("");
		$('#scan_tag').show();
		$('#scan_tag_success').hide();
		$('#tag').focus();
		$('#tag').val("");
		emptyAll();
	}

	function headvalue(type,value,check) {
		var bawah_a = '{{$head_a_bawah}}';
		var atas_a = '{{$head_a_atas}}';

		var bawah_b = '{{$head_b_bawah}}';
		var atas_b = '{{$head_b_atas}}';
		var status = 0;


		if ($('#'+check+'_head_a').val() != "" && $('#'+check+'_head_b').val() != "") {
			if (parseFloat($('#'+check+'_head_a').val()) < parseFloat(bawah_a) || parseFloat($('#'+check+'_head_a').val()) > parseFloat(atas_a)) {
				status++;
			}

			if (parseFloat($('#'+check+'_head_b').val()) < parseFloat(bawah_b) || parseFloat($('#'+check+'_head_b').val()) > parseFloat(atas_b)) {
				status++;
			}

			if (type === 'c') {
				if (value === 'NG') {
					status++;
				}
			}

			if (status > 0) {
				$('#'+check+'_head_status').val('NG');
				document.getElementById(''+check+'_head_status').style.backgroundColor = "#ff4f4f";
			}else{
				$('#'+check+'_head_status').val('OK');
				document.getElementById(''+check+'_head_status').style.backgroundColor = "#7fff6e";
			}
		}else{
			$('#'+check+'_head_status').val('');
			document.getElementById(''+check+'_head_status').style.backgroundColor = "#fff";
		}
	}

	function headyrfvalue(type,value,check) {
		var bawah_a = '{{$head_yrf_a_bawah}}';
		var atas_a = '{{$head_yrf_a_atas}}';

		var bawah_b = '{{$head_yrf_b_bawah}}';
		var atas_b = '{{$head_yrf_b_atas}}';
		var status = 0;


		if ($('#'+check+'_head_yrf_a').val() != "" && $('#'+check+'_head_yrf_b').val() != "") {
			if (parseFloat($('#'+check+'_head_yrf_a').val()) < parseFloat(bawah_a) || parseFloat($('#'+check+'_head_yrf_a').val()) > parseFloat(atas_a)) {
				status++;
			}

			if (parseFloat($('#'+check+'_head_yrf_b').val()) < parseFloat(bawah_b) || parseFloat($('#'+check+'_head_yrf_b').val()) > parseFloat(atas_b)) {
				status++;
			}

			if (type === 'c') {
				if (value === 'NG') {
					status++;
				}
			}

			if (status > 0) {
				$('#'+check+'_head_yrf_status').val('NG');
				document.getElementById(''+check+'_head_yrf_status').style.backgroundColor = "#ff4f4f";
			}else{
				$('#'+check+'_head_yrf_status').val('OK');
				document.getElementById(''+check+'_head_yrf_status').style.backgroundColor = "#7fff6e";
			}
		}else{
			$('#'+check+'_head_yrf_status').val('');
			document.getElementById(''+check+'_head_yrf_status').style.backgroundColor = "#fff";
		}
	}

	function bodyyrfvalue(type,value,check) {
		var bawah_a = '{{$body_yrf_a_bawah}}';
		var atas_a = '{{$body_yrf_a_atas}}';

		var bawah_b = '{{$body_yrf_b_bawah}}';
		var atas_b = '{{$body_yrf_b_atas}}';
		var status = 0;


		if ($('#'+check+'_body_yrf_a').val() != "" && $('#'+check+'_body_yrf_b').val() != "") {
			if (parseFloat($('#'+check+'_body_yrf_a').val()) < parseFloat(bawah_a) || parseFloat($('#'+check+'_body_yrf_a').val()) > parseFloat(atas_a)) {
				status++;
			}

			if (parseFloat($('#'+check+'_body_yrf_b').val()) < parseFloat(bawah_b) || parseFloat($('#'+check+'_body_yrf_b').val()) > parseFloat(atas_b)) {
				status++;
			}

			if (type === 'c') {
				if (value === 'NG') {
					status++;
				}
			}

			if (status > 0) {
				$('#'+check+'_body_yrf_status').val('NG');
				document.getElementById(''+check+'_body_yrf_status').style.backgroundColor = "#ff4f4f";
			}else{
				$('#'+check+'_body_yrf_status').val('OK');
				document.getElementById(''+check+'_body_yrf_status').style.backgroundColor = "#7fff6e";
			}
		}else{
			$('#'+check+'_body_yrf_status').val('');
			document.getElementById(''+check+'_body_yrf_status').style.backgroundColor = "#fff";
		}
	}

	function middlevalue(type,value,check) {
		var bawah_a = '{{$middle_a_bawah}}';
		var atas_a = '{{$middle_a_atas}}';

		var bawah_b = '{{$middle_b_bawah}}';
		var atas_b = '{{$middle_b_atas}}';
		var status = 0;


		if ($('#'+check+'_middle_a').val() != "" && $('#'+check+'_middle_b').val() != "") {
			if (parseFloat($('#'+check+'_middle_a').val()) < parseFloat(bawah_a) || parseFloat($('#'+check+'_middle_a').val()) > parseFloat(atas_a)) {
				status++;
			}

			if (parseFloat($('#'+check+'_middle_b').val()) < parseFloat(bawah_b) || parseFloat($('#'+check+'_middle_b').val()) > parseFloat(atas_b)) {
				status++;
			}

			if (status > 0) {
				$('#'+check+'_middle_status').val('NG');
				document.getElementById(''+check+'_middle_status').style.backgroundColor = "#ff4f4f";
			}else{
				$('#'+check+'_middle_status').val('OK');
				document.getElementById(''+check+'_middle_status').style.backgroundColor = "#7fff6e";
			}
		}else{
			$('#'+check+'_middle_status').val('');
			document.getElementById(''+check+'_middle_status').style.backgroundColor = "#fff";
		}
	}

	function footvalue(type,value,check) {
		var bawah_a = '{{$foot_a_bawah}}';
		var atas_a = '{{$foot_a_atas}}';

		var bawah_b = '{{$foot_b_bawah}}';
		var atas_b = '{{$foot_b_atas}}';
		var status = 0;


		if ($('#'+check+'_foot_a').val() != "" && $('#'+check+'_foot_b').val() != "") {
			if (parseFloat($('#'+check+'_foot_a').val()) < parseFloat(bawah_a) || parseFloat($('#'+check+'_foot_a').val()) > parseFloat(atas_a)) {
				status++;
			}

			if (parseFloat($('#'+check+'_foot_b').val()) < parseFloat(bawah_b) || parseFloat($('#'+check+'_foot_b').val()) > parseFloat(atas_b)) {
				status++;
			}

			if (type === 'c') {
				if (value === 'NG') {
					status++;
				}
			}

			if (status > 0) {
				$('#'+check+'_foot_status').val('NG');
				document.getElementById(''+check+'_foot_status').style.backgroundColor = "#ff4f4f";
			}else{
				$('#'+check+'_foot_status').val('OK');
				document.getElementById(''+check+'_foot_status').style.backgroundColor = "#7fff6e";
			}
		}else{
			$('#'+check+'_foot_status').val('');
			document.getElementById(''+check+'_foot_status').style.backgroundColor = "#fff";
		}
	}

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag").val().length >= 8){
				var data = {
					employee_id : $("#tag").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#scan_tag').hide();
						$('#scan_tag_success').show();
						$('#op').val(result.employee.employee_id);
						$('#op2').val(result.employee.name);
						$('#lot_number_choice').removeAttr('disabled');
						$('#dryer').removeAttr('disabled');
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#tag').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#tag").val("");
			}			
		}
	});

	function fetchProduct(product,type,part,color) {
		if ($('#op').val() == '') {
			openErrorGritter('Error!', "Scan ID Card First!");
			$('#tag').focus();
		}else{
			emptyAll();
			$('#product').val(product);
			$('#part').val(part);
			$('#type').val(type);
			$('#color').val(color);

			var data = {
				type:type.toLowerCase()
			}

			$('#cavity').empty();

			$.get('{{ url("fetch/cavity") }}',data, function(result, status, xhr){
				if(result.status){
					var cavity = "";
					$.each(result.datas, function(key, value) {
						cavity += '<option value="'+value.no_cavity+'">'+value.no_cavity+'</option>';
					});
				}
				$('#cavity').append(cavity);
			});

			

			if (product.match(/YRS/gi)) {
				if (type === 'HEAD') {
					$('#awal_head').show();
					$('#awal_head_yrf').hide();
					$('#awal_body_yrf').hide();
					$('#awal_middle').hide();
					$('#awal_foot').hide();

					$('#ist1_head').show();
					$('#ist1_head_yrf').hide();
					$('#ist1_body_yrf').hide();
					$('#ist1_middle').hide();
					$('#ist1_foot').hide();

					$('#ist2_head').show();
					$('#ist2_head_yrf').hide();
					$('#ist2_body_yrf').hide();
					$('#ist2_middle').hide();
					$('#ist2_foot').hide();

					$('#ist3_head').show();
					$('#ist3_head_yrf').hide();
					$('#ist3_body_yrf').hide();
					$('#ist3_middle').hide();
					$('#ist3_foot').hide();
				}else if(type === 'MIDDLE'){
					$('#awal_middle').show();
					$('#awal_head_yrf').hide();
					$('#awal_body_yrf').hide();
					$('#awal_head').hide();
					$('#awal_foot').hide();

					$('#ist1_middle').show();
					$('#ist1_head_yrf').hide();
					$('#ist1_body_yrf').hide();
					$('#ist1_head').hide();
					$('#ist1_foot').hide();

					$('#ist2_middle').show();
					$('#ist2_head_yrf').hide();
					$('#ist2_body_yrf').hide();
					$('#ist2_head').hide();
					$('#ist2_foot').hide();

					$('#ist3_middle').show();
					$('#ist3_head_yrf').hide();
					$('#ist3_body_yrf').hide();
					$('#ist3_head').hide();
					$('#ist3_foot').hide();
				}else if(type === 'FOOT'){
					$('#awal_middle').hide();
					$('#awal_head_yrf').hide();
					$('#awal_body_yrf').hide();
					$('#awal_head').hide();
					$('#awal_foot').show();

					$('#ist1_middle').hide();
					$('#ist1_head_yrf').hide();
					$('#ist1_body_yrf').hide();
					$('#ist1_head').hide();
					$('#ist1_foot').show();

					$('#ist2_middle').hide();
					$('#ist2_head_yrf').hide();
					$('#ist2_body_yrf').hide();
					$('#ist2_head').hide();
					$('#ist2_foot').show();

					$('#ist3_middle').hide();
					$('#ist3_head_yrf').hide();
					$('#ist3_body_yrf').hide();
					$('#ist3_head').hide();
					$('#ist3_foot').show();
				}
			}else{
				if (type === 'HEAD') {
					$('#awal_head').hide();
					$('#awal_head_yrf').show();
					$('#awal_body_yrf').hide();
					$('#awal_middle').hide();
					$('#awal_foot').hide();

					$('#ist1_head').hide();
					$('#ist1_head_yrf').show();
					$('#ist1_body_yrf').hide();
					$('#ist1_middle').hide();
					$('#ist1_foot').hide();

					$('#ist2_head').hide();
					$('#ist2_head_yrf').show();
					$('#ist2_body_yrf').hide();
					$('#ist2_middle').hide();
					$('#ist2_foot').hide();

					$('#ist3_head').hide();
					$('#ist3_head_yrf').show();
					$('#ist3_body_yrf').hide();
					$('#ist3_middle').hide();
					$('#ist3_foot').hide();
				}else if(type === 'BODY'){
					$('#awal_middle').hide();
					$('#awal_head_yrf').hide();
					$('#awal_body_yrf').show();
					$('#awal_head').hide();
					$('#awal_foot').hide();

					$('#ist1_middle').hide();
					$('#ist1_head_yrf').hide();
					$('#ist1_body_yrf').show();
					$('#ist1_head').hide();
					$('#ist1_foot').hide();

					$('#ist2_middle').hide();
					$('#ist2_head_yrf').hide();
					$('#ist2_body_yrf').show();
					$('#ist2_head').hide();
					$('#ist2_foot').hide();

					$('#ist3_middle').hide();
					$('#ist3_head_yrf').hide();
					$('#ist3_body_yrf').show();
					$('#ist3_head').hide();
					$('#ist3_foot').hide();
				}
			}

			$('#injection_date').removeAttr('disabled');
			$('#machine').removeAttr('disabled');
			$('#cavity').removeAttr('disabled');
		}
	}

	function fetchProductList(){
		$.get('{{ url("fetch/recorder/product") }}',function(result, status, xhr){
			if(result.status){
				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$('#tableBodyList').html("");
				var tableData = "";
				var count = 1;
				$.each(result.datas, function(key, value) {
					var part = value.part_name.split(' ');
					tableData += '<tr onclick="fetchProduct(\''+part[0]+'\''+','+'\''+value.part_type.toUpperCase()+'\''+','+'\''+value.part_code+'\''+','+'\''+value.color+'\')">';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ part[0] +'</td>';
					tableData += '<td>'+ value.part_type.toUpperCase() +'</td>';
					tableData += '<td>'+ value.part_code +'</td>';
					tableData += '<td>'+ value.color +'</td>';
					tableData += '</tr>';

					count += 1;
				});
				$('#tableBodyList').append(tableData);

				var table = $('#tableList').DataTable({
					'dom': 'Bfrtip',
						'responsive':true,
						'lengthMenu': [
						[ 10, 25, 50, -1 ],
						[ '10 rows', '25 rows', '50 rows', 'Show all' ]
						],
						'buttons': {
							buttons:[
							{
								extend: 'pageLength',
								className: 'btn btn-default',
							},
							{
								extend: 'copy',
								className: 'btn btn-success',
								text: '<i class="fa fa-copy"></i> Copy',
								exportOptions: {
									columns: ':not(.notexport)'
								}
							},
							{
								extend: 'excel',
								className: 'btn btn-info',
								text: '<i class="fa fa-file-excel-o"></i> Excel',
								exportOptions: {
									columns: ':not(.notexport)'
								}
							},
							{
								extend: 'print',
								className: 'btn btn-warning',
								text: '<i class="fa fa-print"></i> Print',
								exportOptions: {
									columns: ':not(.notexport)'
								}
							}
							]
						},
						'paging': true,
						'lengthChange': true,
						'pageLength': 15,
						'searching': true	,
						'ordering': true,
						'order': [],
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
				});
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function fetchResumeCdm(){
		$.get('{{ url("index/recorder/fetch_resume_cdm") }}', function(result, status, xhr){
			if(result.status){
				$('#tableResume').DataTable().clear();
				$('#tableResume').DataTable().destroy();
				$('#tableBodyResume').html("");
				var tableData = "";
				var count = 1;
				$.each(result.datas, function(key, value) {
					tableData += '<tr onclick="fetchCdm('+value.id_cdm+')">';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td style="text-align:center">'+ value.product +'<br>'+value.part+' - '+value.color+'<br>'+value.cavity+'</td>';
					tableData += '<td style="text-align:center">'+ value.injection_date +'<br>Mesin '+value.machine+'</td>';
					tableData += '<td style="background-color: #ffd6a5">'+ value.awal_a +'</td>';
					tableData += '<td style="background-color: #ffd6a5">'+ value.awal_b +'</td>';
					tableData += '<td style="background-color: #ffd6a5">'+ value.awal_c +'</td>';
					tableData += '<td style="background-color: #ffd6a5">'+ value.awal_status +'</td>';
					tableData += '<td style="background-color: #9bf6ff">'+ value.ist_1_a +'</td>';
					tableData += '<td style="background-color: #9bf6ff">'+ value.ist_1_b +'</td>';
					tableData += '<td style="background-color: #9bf6ff">'+ value.ist_1_c +'</td>';
					tableData += '<td style="background-color: #9bf6ff">'+ value.ist_1_status +'</td>';
					tableData += '<td style="background-color: #ffc6ff">'+ value.ist_2_a +'</td>';
					tableData += '<td style="background-color: #ffc6ff">'+ value.ist_2_b +'</td>';
					tableData += '<td style="background-color: #ffc6ff">'+ value.ist_2_c +'</td>';
					tableData += '<td style="background-color: #ffc6ff">'+ value.ist_2_status +'</td>';
					tableData += '<td style="background-color: #caffbf">'+ value.ist_3_a +'</td>';
					tableData += '<td style="background-color: #caffbf">'+ value.ist_3_b +'</td>';
					tableData += '<td style="background-color: #caffbf">'+ value.ist_3_c +'</td>';
					tableData += '<td style="background-color: #caffbf">'+ value.ist_3_status +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.created +'</td>';
					tableData += '</tr>';

					count += 1;
				});
				$('#tableBodyResume').append(tableData);

				var table = $('#tableResume').DataTable({
					'dom': 'Bfrtip',
						'responsive':true,
						'lengthMenu': [
						[ 10, 25, 50, -1 ],
						[ '10 rows', '25 rows', '50 rows', 'Show all' ]
						],
						'buttons': {
							buttons:[
							{
								extend: 'pageLength',
								className: 'btn btn-default',
							},
							{
								extend: 'copy',
								className: 'btn btn-success',
								text: '<i class="fa fa-copy"></i> Copy',
								exportOptions: {
									columns: ':not(.notexport)'
								}
							},
							{
								extend: 'excel',
								className: 'btn btn-info',
								text: '<i class="fa fa-file-excel-o"></i> Excel',
								exportOptions: {
									columns: ':not(.notexport)'
								}
							},
							{
								extend: 'print',
								className: 'btn btn-warning',
								text: '<i class="fa fa-print"></i> Print',
								exportOptions: {
									columns: ':not(.notexport)'
								}
							}
							]
						},
						'paging': true,
						'lengthChange': true,
						'pageLength': 5,
						'searching': true	,
						'ordering': true,
						'order': [],
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
				});

				// openSuccessGritter('Success!', "Success get Resume");
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function fetchCdm(id) {
		if ($('#op').val() == '') {
			openErrorGritter('Error!', "Scan ID Card First!");
			$('#tag').focus();
		}else{

			emptyAll();

			var data = {
				id:id
			}

			$.get('{{ url("fetch/recorder/cdm") }}',data, function(result, status, xhr){
				if(result.status){
					$('#save_type').val('UPDATE');
					if (result.datas.product.match(/YRS/gi)) {
						if (result.datas.type === 'HEAD') {

							$('#awal_head').show();
							$('#awal_head_yrf').hide();
							$('#awal_body_yrf').hide();
							$('#awal_middle').hide();
							$('#awal_foot').hide();

							$('#ist1_head').show();
							$('#ist1_head_yrf').hide();
							$('#ist1_body_yrf').hide();
							$('#ist1_middle').hide();
							$('#ist1_foot').hide();

							$('#ist2_head').show();
							$('#ist2_head_yrf').hide();
							$('#ist2_body_yrf').hide();
							$('#ist2_middle').hide();
							$('#ist2_foot').hide();

							$('#ist3_head').show();
							$('#ist3_head_yrf').hide();
							$('#ist3_body_yrf').hide();
							$('#ist3_middle').hide();
							$('#ist3_foot').hide();

							$('#awal_head_a').val(result.datas.awal_a);
							$('#awal_head_b').val(result.datas.awal_b);
							$("input[name=awal_head_c][value=" + result.datas.awal_c + "]").prop('checked', true);
							$('#awal_head_status').val(result.datas.awal_status);

							if (result.datas.awal_status == 'NG') {
								document.getElementById('awal_head_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.awal_status == 'OK'){
								document.getElementById('awal_head_status').style.backgroundColor = "#7fff6e";
							}

							$('#ist1_head_a').val(result.datas.ist_1_a);
							$('#ist1_head_b').val(result.datas.ist_1_b);
							$("input[name=ist1_head_c][value=" + result.datas.ist_1_c + "]").prop('checked', true);
							$('#ist1_head_status').val(result.datas.ist_1_status);

							if (result.datas.ist_1_status == 'NG') {
								document.getElementById('ist1_head_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.ist_1_status == 'OK'){
								document.getElementById('ist1_head_status').style.backgroundColor = "#7fff6e";
							}

							$('#ist2_head_a').val(result.datas.ist_2_a);
							$('#ist2_head_b').val(result.datas.ist_2_b);
							$("input[name=ist2_head_c][value=" + result.datas.ist_2_c + "]").prop('checked', true);
							$('#ist2_head_status').val(result.datas.ist_2_status);

							if (result.datas.ist_2_status == 'NG') {
								document.getElementById('ist2_head_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.ist_2_status == 'OK'){
								document.getElementById('ist2_head_status').style.backgroundColor = "#7fff6e";
							}

							$('#ist3_head_a').val(result.datas.ist_3_a);
							$('#ist3_head_b').val(result.datas.ist_3_b);
							$("input[name=ist3_head_c][value=" + result.datas.ist_3_c + "]").prop('checked', true);
							$('#ist3_head_status').val(result.datas.ist_3_status);

							if (result.datas.ist_3_status == 'NG') {
								document.getElementById('ist3_head_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.ist_3_status == 'OK'){
								document.getElementById('ist3_head_status').style.backgroundColor = "#7fff6e";
							}

						}else if(result.datas.type === 'MIDDLE'){
							$('#awal_middle').show();
							$('#awal_head').hide();
							$('#awal_head_yrf').hide();
							$('#awal_body_yrf').hide();
							$('#awal_foot').hide();

							$('#ist1_middle').show();
							$('#ist1_head').hide();
							$('#ist1_head_yrf').hide();
							$('#ist1_body_yrf').hide();
							$('#ist1_foot').hide();

							$('#ist2_middle').show();
							$('#ist2_head').hide();
							$('#ist2_head_yrf').hide();
							$('#ist2_body_yrf').hide();
							$('#ist2_foot').hide();

							$('#ist3_middle').show();
							$('#ist3_head').hide();
							$('#ist3_head_yrf').hide();
							$('#ist3_body_yrf').hide();
							$('#ist3_foot').hide();

							$('#awal_middle_a').val(result.datas.awal_a);
							$('#awal_middle_b').val(result.datas.awal_b);
							$('#awal_middle_status').val(result.datas.awal_status);

							if (result.datas.awal_status == 'NG') {
								document.getElementById('awal_middle_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.awal_status == 'OK'){
								document.getElementById('awal_middle_status').style.backgroundColor = "#7fff6e";
							}

							$('#ist1_middle_a').val(result.datas.ist_1_a);
							$('#ist1_middle_b').val(result.datas.ist_1_b);
							$('#ist1_middle_status').val(result.datas.ist_1_status);

							if (result.datas.ist_1_status == 'NG') {
								document.getElementById('ist1_middle_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.ist_1_status == 'OK'){
								document.getElementById('ist1_middle_status').style.backgroundColor = "#7fff6e";
							}

							$('#ist2_middle_a').val(result.datas.ist_2_a);
							$('#ist2_middle_b').val(result.datas.ist_2_b);
							$('#ist2_middle_status').val(result.datas.ist_2_status);

							if (result.datas.ist_2_status == 'NG') {
								document.getElementById('ist2_middle_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.ist_2_status == 'OK'){
								document.getElementById('ist2_middle_status').style.backgroundColor = "#7fff6e";
							}

							$('#ist3_middle_a').val(result.datas.ist_3_a);
							$('#ist3_middle_b').val(result.datas.ist_3_b);
							$('#ist3_middle_status').val(result.datas.ist_3_status);

							if (result.datas.ist_3_status == 'NG') {
								document.getElementById('ist3_middle_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.ist_3_status == 'OK'){
								document.getElementById('ist3_middle_status').style.backgroundColor = "#7fff6e";
							}

						}else if(result.datas.type === 'FOOT'){
							$('#awal_middle').hide();
							$('#awal_head_yrf').hide();
							$('#awal_body_yrf').hide();
							$('#awal_head').hide();
							$('#awal_foot').show();

							$('#ist1_middle').hide();
							$('#ist1_head_yrf').hide();
							$('#ist1_body_yrf').hide();
							$('#ist1_head').hide();
							$('#ist1_foot').show();

							$('#ist2_middle').hide();
							$('#ist2_head_yrf').hide();
							$('#ist2_body_yrf').hide();
							$('#ist2_head').hide();
							$('#ist2_foot').show();

							$('#ist3_middle').hide();
							$('#ist3_head_yrf').hide();
							$('#ist3_body_yrf').hide();
							$('#ist3_head').hide();
							$('#ist3_foot').show();

							$('#awal_foot_a').val(result.datas.awal_a);
							$('#awal_foot_b').val(result.datas.awal_b);
							$("input[name=awal_foot_c][value=" + result.datas.awal_c + "]").prop('checked', true);
							$('#awal_foot_status').val(result.datas.awal_status);

							if (result.datas.awal_status == 'NG') {
								document.getElementById('awal_foot_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.awal_status == 'OK'){
								document.getElementById('awal_foot_status').style.backgroundColor = "#7fff6e";
							}

							$('#ist1_foot_a').val(result.datas.ist_1_a);
							$('#ist1_foot_b').val(result.datas.ist_1_b);
							$("input[name=ist1_foot_c][value=" + result.datas.ist_1_c + "]").prop('checked', true);
							$('#ist1_foot_status').val(result.datas.ist_1_status);

							if (result.datas.ist_1_status == 'NG') {
								document.getElementById('ist1_foot_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.ist_1_status == 'OK'){
								document.getElementById('ist1_foot_status').style.backgroundColor = "#7fff6e";
							}

							$('#ist2_foot_a').val(result.datas.ist_2_a);
							$('#ist2_foot_b').val(result.datas.ist_2_b);
							$("input[name=ist2_foot_c][value=" + result.datas.ist_2_c + "]").prop('checked', true);
							$('#ist2_foot_status').val(result.datas.ist_2_status);

							if (result.datas.ist_2_status == 'NG') {
								document.getElementById('ist2_foot_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.ist_2_status == 'OK'){
								document.getElementById('ist2_foot_status').style.backgroundColor = "#7fff6e";
							}

							$('#ist3_foot_a').val(result.datas.ist_3_a);
							$('#ist3_foot_b').val(result.datas.ist_3_b);
							$("input[name=ist3_foot_c][value=" + result.datas.ist_3_c + "]").prop('checked', true);
							$('#ist3_foot_status').val(result.datas.ist_3_status);

							if (result.datas.ist_3_status == 'NG') {
								document.getElementById('ist3_foot_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.ist_3_status == 'OK'){
								document.getElementById('ist3_foot_status').style.backgroundColor = "#7fff6e";
							}
						}
					}else{
						if (result.datas.type === 'HEAD') {

							$('#awal_head').hide();
							$('#awal_head_yrf').show();
							$('#awal_body_yrf').hide();
							$('#awal_middle').hide();
							$('#awal_foot').hide();

							$('#ist1_head').hide();
							$('#ist1_head_yrf').show();
							$('#ist1_body_yrf').hide();
							$('#ist1_middle').hide();
							$('#ist1_foot').hide();

							$('#ist2_head').hide();
							$('#ist2_head_yrf').show();
							$('#ist2_body_yrf').hide();
							$('#ist2_middle').hide();
							$('#ist2_foot').hide();

							$('#ist3_head').hide();
							$('#ist3_head_yrf').show();
							$('#ist3_body_yrf').hide();
							$('#ist3_middle').hide();
							$('#ist3_foot').hide();

							$('#awal_head_yrf_a').val(result.datas.awal_a);
							$('#awal_head_yrf_b').val(result.datas.awal_b);
							$('#awal_head_yrf_status').val(result.datas.awal_status);

							if (result.datas.awal_status == 'NG') {
								document.getElementById('awal_head_yrf_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.awal_status == 'OK'){
								document.getElementById('awal_head_yrf_status').style.backgroundColor = "#7fff6e";
							}

							$('#ist1_head_yrf_a').val(result.datas.ist_1_a);
							$('#ist1_head_yrf_b').val(result.datas.ist_1_b);
							$('#ist1_head_yrf_status').val(result.datas.ist_1_status);

							if (result.datas.ist_1_status == 'NG') {
								document.getElementById('ist1_head_yrf_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.ist_1_status == 'OK'){
								document.getElementById('ist1_head_yrf_status').style.backgroundColor = "#7fff6e";
							}

							$('#ist2_head_yrf_a').val(result.datas.ist_2_a);
							$('#ist2_head_yrf_b').val(result.datas.ist_2_b);
							$('#ist2_head_yrf_status').val(result.datas.ist_2_status);

							if (result.datas.ist_2_status == 'NG') {
								document.getElementById('ist2_head_yrf_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.ist_2_status == 'OK'){
								document.getElementById('ist2_head_yrf_status').style.backgroundColor = "#7fff6e";
							}

							$('#ist3_head_yrf_a').val(result.datas.ist_3_a);
							$('#ist3_head_yrf_b').val(result.datas.ist_3_b);
							$('#ist3_head_yrf_status').val(result.datas.ist_3_status);

							if (result.datas.ist_3_status == 'NG') {
								document.getElementById('ist3_head_yrf_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.ist_3_status == 'OK'){
								document.getElementById('ist3_head_yrf_status').style.backgroundColor = "#7fff6e";
							}

						}else if(result.datas.type === 'BODY'){
							$('#awal_middle').hide();
							$('#awal_head_yrf').hide();
							$('#awal_body_yrf').show();
							$('#awal_head').hide();
							$('#awal_foot').hide();

							$('#ist1_middle').hide();
							$('#ist1_head_yrf').hide();
							$('#ist1_body_yrf').show();
							$('#ist1_head').hide();
							$('#ist1_foot').hide();

							$('#ist2_middle').hide();
							$('#ist2_head_yrf').hide();
							$('#ist2_body_yrf').show();
							$('#ist2_head').hide();
							$('#ist2_foot').hide();

							$('#ist3_middle').hide();
							$('#ist3_head_yrf').hide();
							$('#ist3_body_yrf').show();
							$('#ist3_head').hide();
							$('#ist3_foot').hide();

							$('#awal_body_yrf_a').val(result.datas.awal_a);
							$('#awal_body_yrf_b').val(result.datas.awal_b);
							$('#awal_body_yrf_status').val(result.datas.awal_status);

							if (result.datas.awal_status == 'NG') {
								document.getElementById('awal_body_yrf_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.awal_status == 'OK'){
								document.getElementById('awal_body_yrf_status').style.backgroundColor = "#7fff6e";
							}

							$('#ist1_body_yrf_a').val(result.datas.ist_1_a);
							$('#ist1_body_yrf_b').val(result.datas.ist_1_b);
							$('#ist1_body_yrf_status').val(result.datas.ist_1_status);

							if (result.datas.ist_1_status == 'NG') {
								document.getElementById('ist1_body_yrf_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.ist_1_status == 'OK'){
								document.getElementById('ist1_body_yrf_status').style.backgroundColor = "#7fff6e";
							}

							$('#ist2_body_yrf_a').val(result.datas.ist_2_a);
							$('#ist2_body_yrf_b').val(result.datas.ist_2_b);
							$('#ist2_body_yrf_status').val(result.datas.ist_2_status);

							if (result.datas.ist_2_status == 'NG') {
								document.getElementById('ist2_body_yrf_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.ist_2_status == 'OK'){
								document.getElementById('ist2_body_yrf_status').style.backgroundColor = "#7fff6e";
							}

							$('#ist3_body_yrf_a').val(result.datas.ist_3_a);
							$('#ist3_body_yrf_b').val(result.datas.ist_3_b);
							$('#ist3_body_yrf_status').val(result.datas.ist_3_status);

							if (result.datas.ist_3_status == 'NG') {
								document.getElementById('ist3_body_yrf_status').style.backgroundColor = "#ff4f4f";
							}else if(result.datas.ist_3_status == 'OK'){
								document.getElementById('ist3_body_yrf_status').style.backgroundColor = "#7fff6e";
							}

						}
					}

					$('#injection_date').removeAttr('disabled');
					$('#machine').removeAttr('disabled');
					$('#cavity').removeAttr('disabled');

					var data2 = {
						type:result.datas.type.toLowerCase()
					}

					$('#cavity').empty();

					$.get('{{ url("fetch/cavity") }}',data2, function(result2, status, xhr){
						if(result2.status){
							var cavity = "";
							$.each(result2.datas, function(key, value) {
								cavity += '<option value="'+value.no_cavity+'">'+value.no_cavity+'</option>';
							});
						}
						$('#cavity').append(cavity);
						$('#cavity').val(result.datas.cavity).trigger('change');
					})

					$('#id_cdm').val(result.datas.id_cdm);
					$('#product').val(result.datas.product);
					$('#type').val(result.datas.type);
					$('#part').val(result.datas.part);
					$('#color').val(result.datas.color);
					$('#injection_date').val(result.datas.injection_date);
					$('#machine').val(result.datas.machine).trigger('change');

					$('#product').focus();
					openSuccessGritter('Success','Success Get Data');
				}else{
					audio_error.play();
					openErrorGritter('Error!','Get Data Failed');
				}
			})
		}
	}

	function inputCdm() {
		if ($('#product').val() == "" || $('#type').val() == ""|| $('#part').val() == "" || $('#color').val() == "" || $('#injection_date').val() == "" || $('#machine').val() == ""|| $('#cavity').val() == "") {
			openErrorGritter('Error!', 'Semua Data Harus Diisi.');
		}else{
			$('#loading').show();
			var head = [];
			var middle = [];
			var foot = [];
			var head_yrf = [];
			var body_yrf = [];
			if ($('#product').val().match(/YRS/gi)) {
				if ($('#type').val() == 'HEAD') {
					if ($('input[id="awal_head_c"]:checked').val() == 'OK') {
						$awal_head_c = 'OK';
					}else if($('input[id="awal_head_c"]:checked').val() == 'NG'){
						$awal_head_c = 'NG';
					}else{
						$awal_head_c = null;
					}

					if ($('input[id="ist1_head_c"]:checked').val() == 'OK') {
						$ist1_head_c = 'OK';
					}else if($('input[id="ist1_head_c"]:checked').val() == 'NG'){
						$ist1_head_c = 'NG';
					}else{
						$ist1_head_c = null;
					}

					if ($('input[id="ist2_head_c"]:checked').val() == 'OK') {
						$ist2_head_c = 'OK';
					}else if($('input[id="ist2_head_c"]:checked').val() == 'NG'){
						$ist2_head_c = 'NG';
					}else{
						$ist2_head_c = null;
					}

					if ($('input[id="ist3_head_c"]:checked').val() == 'OK') {
						$ist3_head_c = 'OK';
					}else if($('input[id="ist3_head_c"]:checked').val() == 'NG'){
						$ist3_head_c = 'NG';
					}else{
						$ist3_head_c = null;
					}

					head.push(
					{
						'awal_a': $('#awal_head_a').val(),
						'awal_b': $('#awal_head_b').val(),
						'awal_c': $awal_head_c,
						'awal_status': $('#awal_head_status').val(),
						'ist1_a': $('#ist1_head_a').val(),
						'ist1_b': $('#ist1_head_b').val(),
						'ist1_c': $ist1_head_c,
						'ist1_status': $('#ist1_head_status').val(),
						'ist2_a': $('#ist2_head_a').val(),
						'ist2_b': $('#ist2_head_b').val(),
						'ist2_c': $ist2_head_c,
						'ist2_status': $('#ist2_head_status').val(),
						'ist3_a': $('#ist3_head_a').val(),
						'ist3_b': $('#ist3_head_b').val(),
						'ist3_c': $ist3_head_c,
						'ist3_status': $('#ist3_head_status').val(),
					});

					var data = {
						product:$('#product').val(),
						type:$('#type').val(),
						part:$('#part').val(),
						color:$('#color').val(),
						injection_date:$('#injection_date').val(),
						machine:$('#machine').val(),
						cavity:$('#cavity').val(),
						employee_id:$('#op').val(),
						head:head,
						save_type:$('#save_type').val(),
						id_cdm:$('#id_cdm').val()
					}
				}

				if ($('#type').val() == 'MIDDLE') {
					middle.push(
					{
						'awal_a': $('#awal_middle_a').val(),
						'awal_b': $('#awal_middle_b').val(),
						'awal_c': '-',
						'awal_status': $('#awal_middle_status').val(),
						'ist1_a': $('#ist1_middle_a').val(),
						'ist1_b': $('#ist1_middle_b').val(),
						'ist1_c': '-',
						'ist1_status': $('#ist1_middle_status').val(),
						'ist2_a': $('#ist2_middle_a').val(),
						'ist2_b': $('#ist2_middle_b').val(),
						'ist2_c': '-',
						'ist2_status': $('#ist2_middle_status').val(),
						'ist3_a': $('#ist3_middle_a').val(),
						'ist3_b': $('#ist3_middle_b').val(),
						'ist3_c': '-',
						'ist3_status': $('#ist3_middle_status').val(),
					});

					var data = {
						product:$('#product').val(),
						type:$('#type').val(),
						part:$('#part').val(),
						color:$('#color').val(),
						injection_date:$('#injection_date').val(),
						machine:$('#machine').val(),
						cavity:$('#cavity').val(),
						employee_id:$('#op').val(),
						middle:middle,
						save_type:$('#save_type').val(),
						id_cdm:$('#id_cdm').val()
					}
				}

				if ($('#type').val() == 'FOOT') {
					if ($('input[id="awal_foot_c"]:checked').val() == 'OK') {
						$awal_foot_c = 'OK';
					}else if($('input[id="awal_foot_c"]:checked').val() == 'NG'){
						$awal_foot_c = 'NG';
					}else{
						$awal_foot_c = null;
					}

					if ($('input[id="ist1_foot_c"]:checked').val() == 'OK') {
						$ist1_foot_c = 'OK';
					}else if($('input[id="ist1_foot_c"]:checked').val() == 'NG'){
						$ist1_foot_c = 'NG';
					}else{
						$ist1_foot_c = null;
					}

					if ($('input[id="ist2_foot_c"]:checked').val() == 'OK') {
						$ist2_foot_c = 'OK';
					}else if($('input[id="ist2_foot_c"]:checked').val() == 'NG'){
						$ist2_foot_c = 'NG';
					}else{
						$ist2_foot_c = null;
					}

					if ($('input[id="ist3_foot_c"]:checked').val() == 'OK') {
						$ist3_foot_c = 'OK';
					}else if($('input[id="ist3_foot_c"]:checked').val() == 'NG'){
						$ist3_foot_c = 'NG';
					}else{
						$ist3_foot_c = null;
					}

					foot.push(
					{
						'awal_a': $('#awal_foot_a').val(),
						'awal_b': $('#awal_foot_b').val(),
						'awal_c': $awal_foot_c,
						'awal_status': $('#awal_foot_status').val(),
						'ist1_a': $('#ist1_foot_a').val(),
						'ist1_b': $('#ist1_foot_b').val(),
						'ist1_c': $ist1_foot_c,
						'ist1_status': $('#ist1_foot_status').val(),
						'ist2_a': $('#ist2_foot_a').val(),
						'ist2_b': $('#ist2_foot_b').val(),
						'ist2_c': $ist2_foot_c,
						'ist2_status': $('#ist2_foot_status').val(),
						'ist3_a': $('#ist3_foot_a').val(),
						'ist3_b': $('#ist3_foot_b').val(),
						'ist3_c': $ist3_foot_c,
						'ist3_status': $('#ist3_foot_status').val(),
					});

					var data = {
						product:$('#product').val(),
						type:$('#type').val(),
						part:$('#part').val(),
						color:$('#color').val(),
						injection_date:$('#injection_date').val(),
						machine:$('#machine').val(),
						cavity:$('#cavity').val(),
						employee_id:$('#op').val(),
						foot:foot,
						save_type:$('#save_type').val(),
						id_cdm:$('#id_cdm').val()
					}
				}
			}else{
				if ($('#type').val() == 'HEAD') {					
					head_yrf.push(
					{
						'awal_a': $('#awal_head_yrf_a').val(),
						'awal_b': $('#awal_head_yrf_b').val(),
						'awal_c': '-',
						'awal_status': $('#awal_head_yrf_status').val(),
						'ist1_a': $('#ist1_head_yrf_a').val(),
						'ist1_b': $('#ist1_head_yrf_b').val(),
						'ist1_c': '-',
						'ist1_status': $('#ist1_head_yrf_status').val(),
						'ist2_a': $('#ist2_head_yrf_a').val(),
						'ist2_b': $('#ist2_head_yrf_b').val(),
						'ist2_c': '-',
						'ist2_status': $('#ist2_head_yrf_status').val(),
						'ist3_a': $('#ist3_head_yrf_a').val(),
						'ist3_b': $('#ist3_head_yrf_b').val(),
						'ist3_c': '-',
						'ist3_status': $('#ist3_head_yrf_status').val(),
					});

					var data = {
						product:$('#product').val(),
						type:$('#type').val(),
						part:$('#part').val(),
						color:$('#color').val(),
						injection_date:$('#injection_date').val(),
						machine:$('#machine').val(),
						cavity:$('#cavity').val(),
						employee_id:$('#op').val(),
						head_yrf:head_yrf,
						save_type:$('#save_type').val(),
						id_cdm:$('#id_cdm').val()
					}
				}

				if ($('#type').val() == 'BODY') {
					body_yrf.push(
					{
						'awal_a': $('#awal_body_yrf_a').val(),
						'awal_b': $('#awal_body_yrf_b').val(),
						'awal_c': '-',
						'awal_status': $('#awal_body_yrf_status').val(),
						'ist1_a': $('#ist1_body_yrf_a').val(),
						'ist1_b': $('#ist1_body_yrf_b').val(),
						'ist1_c': '-',
						'ist1_status': $('#ist1_body_yrf_status').val(),
						'ist2_a': $('#ist2_body_yrf_a').val(),
						'ist2_b': $('#ist2_body_yrf_b').val(),
						'ist2_c': '-',
						'ist2_status': $('#ist2_body_yrf_status').val(),
						'ist3_a': $('#ist3_body_yrf_a').val(),
						'ist3_b': $('#ist3_body_yrf_b').val(),
						'ist3_c': '-',
						'ist3_status': $('#ist3_body_yrf_status').val(),
					});

					var data = {
						product:$('#product').val(),
						type:$('#type').val(),
						part:$('#part').val(),
						color:$('#color').val(),
						injection_date:$('#injection_date').val(),
						machine:$('#machine').val(),
						cavity:$('#cavity').val(),
						employee_id:$('#op').val(),
						body_yrf:body_yrf,
						save_type:$('#save_type').val(),
						id_cdm:$('#id_cdm').val()
					}
				}
			}

			// console.log(data);

			$.post('{{ url("input/recorder/cdm") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					emptyAll();
					fetchProductList();
					fetchResumeCdm();
					$('#loading').hide();
				}
				else{
					audio_error.play();
					openErrorGritter('Error!', result.message);
					$('#loading').hide();
				}
			})
		}
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '<?php echo e(url("images/image-screen.png")); ?>',
			sticky: false,
			time: '2000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '<?php echo e(url("images/image-stop.png")); ?>',
			sticky: false,
			time: '2000'
		});
	}

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>