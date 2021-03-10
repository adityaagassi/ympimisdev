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

		#head > thead > tr > th,#head > thead > tr {
			/*border: 2px solid black;*/
			text-align: center;
			vertical-align: middle;
		}

		#head > tbody > tr > td,#head > tbody > tr{
			border: 1px solid black;
			text-align: center;
			vertical-align: middle;
			padding: 0px;
		}

		#middle > thead > tr > th,#middle > thead > tr {
			/*border: 1px solid black;*/
			text-align: center;
			vertical-align: middle;
		}

		#middle > tbody > tr > td,#middle > tbody > tr{
			border: 1px solid black;
			text-align: center;
			vertical-align: middle;
			padding: 0px;
		}

		#foot > thead > tr > th,#foot > thead > tr {
			/*border: 1px solid black;*/
			text-align: center;
			vertical-align: middle;
		}

		#foot > tbody > tr > td,#foot > tbody > tr{
			border: 1px solid black;
			text-align: center;
			vertical-align: middle;
			padding: 0px;
		}

		#head_yrf > thead > tr > th,#head_yrf > thead > tr {
			/*border: 1px solid black;*/
			text-align: center;
			vertical-align: middle;
		}

		#head_yrf > tbody > tr > td,#head_yrf > tbody > tr{
			border: 1px solid black;
			text-align: center;
			vertical-align: middle;
			padding: 0px;
		}

		#body_yrf > thead > tr > th,#body_yrf > thead > tr {
			/*border: 1px solid black;*/
			text-align: center;
			vertical-align: middle;
		}

		#body_yrf > tbody > tr > td,#body_yrf > tbody > tr{
			border: 1px solid black;
			text-align: center;
			vertical-align: middle;
			padding: 0px;
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
		<a class="btn btn-danger pull-right" target="_blank" href="{{ url('index/recorder/cdm_report') }}" style="margin-right: 10px"><i class="fa fa-file-pdf-o"></i> &nbsp;Report</a>
	</h1>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="location">
	<div class="row">
		<div class="col-xs-3" style="padding-right: 5px">
			<div class="box box-solid">
				<div class="box-body">
					<span style="font-weight: bold; font-size: 16px;">Scan ID Card:</span>
					<div class="input-group" id="scan_tag" style="padding-bottom: 10px">
						<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
							<i class="glyphicon glyphicon-qrcode"></i>
						</div>
						<input type="text" style="text-align: center; border-color: black;font-size: 20px;height: 35px" class="form-control" id="tag" name="tag" placeholder="Scan ID Card" required>
						<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
							<i class="glyphicon glyphicon-qrcode"></i>
						</div>
					</div>
					<div class="input-group" id="scan_tag_success" style="padding-bottom: 10px">
						<div class="col-xs-4">
							<div class="row">
								<input type="text" id="op" style="width: 100%; height: 35px; font-size: 15px; text-align: center;border: 1px solid black" disabled placeholder="Employee ID">
							</div>
						</div>
						<div class="col-xs-5">
							<div class="row">
								<input type="text" id="op2" style="width: 100%; height: 35px; font-size: 15px; text-align: center;border: 1px solid black" disabled placeholder="Name">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="row" style="padding-left: 5px">
								<button class="btn btn-danger" onclick="cancelEmp()" style="width: 100%;height: 35px;font-size: 15px;vertical-align: middle;">
									<b>CLEAR</b>
								</button>
							</div>
						</div>
					</div>
					<span style="font-size: 20px; font-weight: bold;">DAFTAR ITEM:</span>
					<table class="table table-hover table-striped" id="tableList" style="width: 100%;">
						<thead>
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 1%;">Product</th>
							</tr>					
						</thead>
						<tbody id="tableBodyList">
						</tbody>
						<tfoot>
							<tr>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
		<div class="col-xs-9" style="padding-left: 5px">
				<div class="box box-solid">
					<div class="box-body">
						<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px">
							<div class="row">
								<div class="col-xs-3">
									<span style="font-weight: bold; font-size: 16px;">Product:</span>
									<input type="text" id="product" style="width: 100%; height: 40px; font-size: 20px; text-align: center;" readonly>
									<input type="hidden" id="save_type" style="width: 100%; height: 40px; font-size: 20px; text-align: center;">
									<input type="hidden" id="cdm_code" style="width: 100%; height: 40px; font-size: 20px; text-align: center;">
									<input type="hidden" id="id_cdm_1">
									<input type="hidden" id="id_cdm_2">
									<input type="hidden" id="id_cdm_3">
									<input type="hidden" id="id_cdm_4">
									<input type="hidden" id="id_cdm_5">
									<input type="hidden" id="id_cdm_6">
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
						<div class="col-xs-12" style="padding-left: 0px;padding-right: 0px">
							<div class="row">
								<div class="col-xs-3">
									<span style="font-weight: bold; font-size: 16px;">Injection Date:</span>
									<input type="text" id="injection_date" style="width: 100%; height: 40px; font-size: 20px; text-align: center;" placeholder="Injection Date" readonly>
								</div>
								<div class="col-xs-3">
									<span style="font-weight: bold; font-size: 16px;">Machine:</span>
									<select name="machine" id="machine" class="form-group" style="width: 100%; height: 40px; font-size: 20px; text-align: center;" data-placeholder="Select Machine">
											
									</select>
								</div>
								<div class="col-xs-3">
									<span style="font-weight: bold; font-size: 16px;">Machine Injection:</span>
									<select name="machine_injection" id="machine_injection" class="form-group" style="width: 100%; height: 40px; font-size: 20px; text-align: center;" data-placeholder="Select Machine Injection">
										<option value=""></option>
										@foreach($machine as $machines)
											<option value="{{$machines}}">{{$machines}}</option>
										@endforeach
									</select>
								</div>
								<div class="col-xs-3">
									<span style="font-weight: bold; font-size: 16px;">Cavity:</span>
									<select name="cavity" id="cavity" class="form-group" style="width: 100%; height: 40px; font-size: 20px; text-align: center;" data-placeholder="Select Cavity" onchange="fetchCavity(this.value)">
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="box box-solid">
					<div class="box-body">
						<div class="col-xs-12" style="overflow-x: scroll;padding-left: 0px;padding-right: 0px">
							<!-- TABLE HEAD -->
							<table class="table table-striped table-bordered" id="head">
								<thead>
									<tr>
										<th rowspan="2" style="background-color: #a5adff;border-bottom: 3px solid #ffa5a5">Cav</th>
										<th colspan="4" style="background-color: #ffd6a5;">Awal Proses</th>
										<th colspan="4" style="background-color: #9bf6ff;">Istirahat 1</th>
										<th colspan="4" style="background-color: #ffc6ff;">Istirahat 2</th>
										<th colspan="4" style="background-color: #caffbf;">Istirahat 3</th>
									</tr>
									<tr>
										<th style="background-color: #ffd6a5;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #ffd6a5;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #ffd6a5;border-bottom: 3px solid #ffa5a5">C</th>
										<th style="background-color: #ffd6a5;border-bottom: 3px solid #ffa5a5">Jdg</th>
										<th style="background-color: #9bf6ff;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #9bf6ff;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #9bf6ff;border-bottom: 3px solid #ffa5a5">C</th>
										<th style="background-color: #9bf6ff;border-bottom: 3px solid #ffa5a5">Jdg</th>
										<th style="background-color: #ffc6ff;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #ffc6ff;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #ffc6ff;border-bottom: 3px solid #ffa5a5">C</th>
										<th style="background-color: #ffc6ff;border-bottom: 3px solid #ffa5a5">Jdg</th>
										<th style="background-color: #caffbf;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #caffbf;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #caffbf;border-bottom: 3px solid #ffa5a5">C</th>
										<th style="background-color: #caffbf;border-bottom: 3px solid #ffa5a5">Jdg</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td id="cav_head_1">1</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('a_1',this.value,'awal')" placeholder="A" class="form-control" id="awal_head_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('b_1',this.value,'awal')" placeholder="B" class="form-control" id="awal_head_b_1">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="awal_head_c_1" name="awal_head_c_1" value="OK" onclick="headvalue('c_1',this.value,'awal')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="awal_head_c_1" name="awal_head_c_1" onclick="headvalue('c_1',this.value,'awal')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_head_status_1" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('a_1',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_head_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('b_1',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_head_b_1">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist1_head_c_1" name="ist1_head_c_1" value="OK" onclick="headvalue('c_1',this.value,'ist1')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist1_head_c_1" name="ist1_head_c_1" onclick="headvalue('c_1',this.value,'ist1')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_head_status_1" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('a_1',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_head_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('b_1',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_head_b_1">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist2_head_c_1" name="ist2_head_c_1" value="OK" onclick="headvalue('c_1',this.value,'ist2')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist2_head_c_1" name="ist2_head_c_1" onclick="headvalue('c_1',this.value,'ist2')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_head_status_1" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('a_1',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_head_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('b_1',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_head_b_1">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist3_head_c_1" name="ist3_head_c_1" value="OK" onclick="headvalue('c_1',this.value,'ist3')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist3_head_c_1" name="ist3_head_c_1" onclick="headvalue('c_1',this.value,'ist3')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_head_status_1" readonly>
										</td>

									</tr>
									<tr>
										<td id="cav_head_2">2</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('a_2',this.value,'awal')" placeholder="A" class="form-control" id="awal_head_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('b_2',this.value,'awal')" placeholder="B" class="form-control" id="awal_head_b_2">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="awal_head_c_2" name="awal_head_c_2" value="OK" onclick="headvalue('c_2',this.value,'awal')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="awal_head_c_2" name="awal_head_c_2" onclick="headvalue('c_2',this.value,'awal')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_head_status_2" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('a_2',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_head_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('b_2',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_head_b_2">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist1_head_c_2" name="ist1_head_c_2" value="OK" onclick="headvalue('c_2',this.value,'ist1')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist1_head_c_2" name="ist1_head_c_2" onclick="headvalue('c_2',this.value,'ist1')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_head_status_2" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('a_2',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_head_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('b_2',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_head_b_2">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist2_head_c_2" name="ist2_head_c_2" value="OK" onclick="headvalue('c_2',this.value,'ist2')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist2_head_c_2" name="ist2_head_c_2" onclick="headvalue('c_2',this.value,'ist2')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_head_status_2" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('a_2',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_head_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('b_2',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_head_b_2">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist3_head_c_2" name="ist3_head_c_2" value="OK" onclick="headvalue('c_2',this.value,'ist3')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist3_head_c_2" name="ist3_head_c_2" onclick="headvalue('c_2',this.value,'ist3')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_head_status_2" readonly>
										</td>

									</tr>
									<tr>
										<td id="cav_head_3">3</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('a_3',this.value,'awal')" placeholder="A" class="form-control" id="awal_head_a_3">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('b_3',this.value,'awal')" placeholder="B" class="form-control" id="awal_head_b_3">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="awal_head_c_3" name="awal_head_c_3" value="OK" onclick="headvalue('c_3',this.value,'awal')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="awal_head_c_3" name="awal_head_c_3" onclick="headvalue('c_3',this.value,'awal')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_head_status_3" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('a_3',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_head_a_3">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('b_3',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_head_b_3">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist1_head_c_3" name="ist1_head_c_3" value="OK" onclick="headvalue('c_3',this.value,'ist1')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist1_head_c_3" name="ist1_head_c_3" onclick="headvalue('c_3',this.value,'ist1')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_head_status_3" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('a_3',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_head_a_3">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('b_3',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_head_b_3">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist2_head_c_3" name="ist2_head_c_3" value="OK" onclick="headvalue('c_3',this.value,'ist2')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist2_head_c_3" name="ist2_head_c_3" onclick="headvalue('c_3',this.value,'ist2')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_head_status_3" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('a_3',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_head_a_3">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('b_3',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_head_b_3">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist3_head_c_3" name="ist3_head_c_3" value="OK" onclick="headvalue('c_3',this.value,'ist3')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist3_head_c_3" name="ist3_head_c_3" onclick="headvalue('c_3',this.value,'ist3')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_head_status_3" readonly>
										</td>

									</tr>
									<tr>
										<td id="cav_head_4">4</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('a_4',this.value,'awal')" placeholder="A" class="form-control" id="awal_head_a_4">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('b_4',this.value,'awal')" placeholder="B" class="form-control" id="awal_head_b_4">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="awal_head_c_4" name="awal_head_c_4" value="OK" onclick="headvalue('c_4',this.value,'awal')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="awal_head_c_4" name="awal_head_c_4" onclick="headvalue('c_4',this.value,'awal')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_head_status_4" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('a_4',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_head_a_4">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('b_4',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_head_b_4">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist1_head_c_4" name="ist1_head_c_4" value="OK" onclick="headvalue('c_4',this.value,'ist1')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist1_head_c_4" name="ist1_head_c_4" onclick="headvalue('c_4',this.value,'ist1')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_head_status_4" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('a_4',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_head_a_4">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('b_4',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_head_b_4">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist2_head_c_4" name="ist2_head_c_4" value="OK" onclick="headvalue('c_4',this.value,'ist2')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist2_head_c_4" name="ist2_head_c_4" onclick="headvalue('c_4',this.value,'ist2')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_head_status_4" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('a_4',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_head_a_4">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="headvalue('b_4',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_head_b_4">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist3_head_c_4" name="ist3_head_c_4" value="OK" onclick="headvalue('c_4',this.value,'ist3')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist3_head_c_4" name="ist3_head_c_4" onclick="headvalue('c_4',this.value,'ist3')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_head_status_4" readonly>
										</td>

									</tr>
								</tbody>
							</table>

							<!-- TABLE MIDDLE -->
							<table class="table table-striped table-bordered" id="middle">
								<thead>
									<tr>
										<th rowspan="2" style="background-color: #a5adff;border-bottom: 3px solid #ffa5a5">Cav</th>
										<th colspan="3" style="background-color: #ffd6a5;">Awal Proses</th>
										<th colspan="3" style="background-color: #9bf6ff;">Istirahat 1</th>
										<th colspan="3" style="background-color: #ffc6ff;">Istirahat 2</th>
										<th colspan="3" style="background-color: #caffbf;">Istirahat 3</th>
									</tr>
									<tr>
										<th style="background-color: #ffd6a5;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #ffd6a5;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #ffd6a5;border-bottom: 3px solid #ffa5a5">Jdg</th>
										<th style="background-color: #9bf6ff;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #9bf6ff;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #9bf6ff;border-bottom: 3px solid #ffa5a5">Jdg</th>
										<th style="background-color: #ffc6ff;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #ffc6ff;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #ffc6ff;border-bottom: 3px solid #ffa5a5">Jdg</th>
										<th style="background-color: #caffbf;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #caffbf;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #caffbf;border-bottom: 3px solid #ffa5a5">Jdg</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td id="cav_middle_1">1</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('a_1',this.value,'awal')" placeholder="A" class="form-control" id="awal_middle_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('b_1',this.value,'awal')" placeholder="B" class="form-control" id="awal_middle_b_1">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_middle_status_1" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('a_1',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_middle_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('b_1',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_middle_b_1">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_middle_status_1" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('a_1',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_middle_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('b_1',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_middle_b_1">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_middle_status_1" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('a_1',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_middle_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('b_1',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_middle_b_1">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_middle_status_1" readonly>
										</td>

									</tr>
									<tr>
										<td id="cav_middle_2">2</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('a_2',this.value,'awal')" placeholder="A" class="form-control" id="awal_middle_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('b_2',this.value,'awal')" placeholder="B" class="form-control" id="awal_middle_b_2">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_middle_status_2" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('a_2',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_middle_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('b_2',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_middle_b_2">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_middle_status_2" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('a_2',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_middle_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('b_2',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_middle_b_2">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_middle_status_2" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('a_2',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_middle_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('b_2',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_middle_b_2">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_middle_status_2" readonly>
										</td>

									</tr>
									<tr>
										<td id="cav_middle_3">3</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('a_3',this.value,'awal')" placeholder="A" class="form-control" id="awal_middle_a_3">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('b_3',this.value,'awal')" placeholder="B" class="form-control" id="awal_middle_b_3">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_middle_status_3" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('a_3',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_middle_a_3">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('b_3',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_middle_b_3">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_middle_status_3" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('a_3',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_middle_a_3">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('b_3',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_middle_b_3">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_middle_status_3" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('a_3',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_middle_a_3">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('b_3',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_middle_b_3">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_middle_status_3" readonly>
										</td>

									</tr>
									<tr>
										<td id="cav_middle_4">4</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('a_4',this.value,'awal')" placeholder="A" class="form-control" id="awal_middle_a_4">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('b_4',this.value,'awal')" placeholder="B" class="form-control" id="awal_middle_b_4">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_middle_status_4" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('a_4',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_middle_a_4">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('b_4',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_middle_b_4">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_middle_status_4" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('a_4',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_middle_a_4">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('b_4',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_middle_b_4">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_middle_status_4" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('a_4',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_middle_a_4">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="middlevalue('b_4',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_middle_b_4">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_middle_status_4" readonly>
										</td>

									</tr>
								</tbody>
							</table>

							<!-- TABLE FOOT -->
							<table class="table table-striped table-bordered" id="foot">
								<thead>
									<tr>
										<th rowspan="2" style="background-color: #a5adff;border-bottom: 3px solid #ffa5a5">Cav</th>
										<th colspan="4" style="background-color: #ffd6a5;">Awal Proses</th>
										<th colspan="4" style="background-color: #9bf6ff;">Istirahat 1</th>
										<th colspan="4" style="background-color: #ffc6ff;">Istirahat 2</th>
										<th colspan="4" style="background-color: #caffbf;">Istirahat 3</th>
									</tr>
									<tr>
										<th style="background-color: #ffd6a5;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #ffd6a5;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #ffd6a5;border-bottom: 3px solid #ffa5a5">C</th>
										<th style="background-color: #ffd6a5;border-bottom: 3px solid #ffa5a5">Jdg</th>
										<th style="background-color: #9bf6ff;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #9bf6ff;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #9bf6ff;border-bottom: 3px solid #ffa5a5">C</th>
										<th style="background-color: #9bf6ff;border-bottom: 3px solid #ffa5a5">Jdg</th>
										<th style="background-color: #ffc6ff;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #ffc6ff;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #ffc6ff;border-bottom: 3px solid #ffa5a5">C</th>
										<th style="background-color: #ffc6ff;border-bottom: 3px solid #ffa5a5">Jdg</th>
										<th style="background-color: #caffbf;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #caffbf;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #caffbf;border-bottom: 3px solid #ffa5a5">C</th>
										<th style="background-color: #caffbf;border-bottom: 3px solid #ffa5a5">Jdg</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td id="cav_foot_1">1</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_1',this.value,'awal')" placeholder="A" class="form-control" id="awal_foot_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_1',this.value,'awal')" placeholder="B" class="form-control" id="awal_foot_b_1">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="awal_foot_c_1" name="awal_foot_c_1" value="OK" onclick="footvalue('c_1',this.value,'awal')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="awal_foot_c_1" name="awal_foot_c_1" onclick="footvalue('c_1',this.value,'awal')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_foot_status_1" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_1',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_foot_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_1',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_foot_b_1">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist1_foot_c_1" name="ist1_foot_c_1" value="OK" onclick="footvalue('c_1',this.value,'ist1')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist1_foot_c_1" name="ist1_foot_c_1" onclick="footvalue('c_1',this.value,'ist1')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_foot_status_1" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_1',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_foot_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_1',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_foot_b_1">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist2_foot_c_1" name="ist2_foot_c_1" value="OK" onclick="footvalue('c_1',this.value,'ist2')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist2_foot_c_1" name="ist2_foot_c_1" onclick="footvalue('c_1',this.value,'ist2')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_foot_status_1" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_1',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_foot_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_1',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_foot_b_1">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist3_foot_c_1" name="ist3_foot_c_1" value="OK" onclick="footvalue('c_1',this.value,'ist3')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist3_foot_c_1" name="ist3_foot_c_1" onclick="footvalue('c_1',this.value,'ist3')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_foot_status_1" readonly>
										</td>

									</tr>
									<tr>
										<td id="cav_foot_2">2</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_2',this.value,'awal')" placeholder="A" class="form-control" id="awal_foot_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_2',this.value,'awal')" placeholder="B" class="form-control" id="awal_foot_b_2">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="awal_foot_c_2" name="awal_foot_c_2" value="OK" onclick="footvalue('c_2',this.value,'awal')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="awal_foot_c_2" name="awal_foot_c_2" onclick="footvalue('c_2',this.value,'awal')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_foot_status_2" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_2',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_foot_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_2',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_foot_b_2">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist1_foot_c_2" name="ist1_foot_c_2" value="OK" onclick="footvalue('c_2',this.value,'ist1')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist1_foot_c_2" name="ist1_foot_c_2" onclick="footvalue('c_2',this.value,'ist1')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_foot_status_2" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_2',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_foot_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_2',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_foot_b_2">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist2_foot_c_2" name="ist2_foot_c_2" value="OK" onclick="footvalue('c_2',this.value,'ist2')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist2_foot_c_2" name="ist2_foot_c_2" onclick="footvalue('c_2',this.value,'ist2')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_foot_status_2" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_2',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_foot_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_2',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_foot_b_2">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist3_foot_c_2" name="ist3_foot_c_2" value="OK" onclick="footvalue('c_2',this.value,'ist3')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist3_foot_c_2" name="ist3_foot_c_2" onclick="footvalue('c_2',this.value,'ist3')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_foot_status_2" readonly>
										</td>

									</tr>
									<tr>
										<td id="cav_foot_3">3</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_3',this.value,'awal')" placeholder="A" class="form-control" id="awal_foot_a_3">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_3',this.value,'awal')" placeholder="B" class="form-control" id="awal_foot_b_3">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="awal_foot_c_3" name="awal_foot_c_3" value="OK" onclick="footvalue('c_3',this.value,'awal')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="awal_foot_c_3" name="awal_foot_c_3" onclick="footvalue('c_3',this.value,'awal')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_foot_status_3" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_3',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_foot_a_3">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_3',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_foot_b_3">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist1_foot_c_3" name="ist1_foot_c_3" value="OK" onclick="footvalue('c_3',this.value,'ist1')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist1_foot_c_3" name="ist1_foot_c_3" onclick="footvalue('c_3',this.value,'ist1')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_foot_status_3" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_3',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_foot_a_3">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_3',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_foot_b_3">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist2_foot_c_3" name="ist2_foot_c_3" value="OK" onclick="footvalue('c_3',this.value,'ist2')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist2_foot_c_3" name="ist2_foot_c_3" onclick="footvalue('c_3',this.value,'ist2')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_foot_status_3" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_3',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_foot_a_3">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_3',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_foot_b_3">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist3_foot_c_3" name="ist3_foot_c_3" value="OK" onclick="footvalue('c_3',this.value,'ist3')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist3_foot_c_3" name="ist3_foot_c_3" onclick="footvalue('c_3',this.value,'ist3')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_foot_status_3" readonly>
										</td>

									</tr>
									<tr>
										<td id="cav_foot_4">4</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_4',this.value,'awal')" placeholder="A" class="form-control" id="awal_foot_a_4">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_4',this.value,'awal')" placeholder="B" class="form-control" id="awal_foot_b_4">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="awal_foot_c_4" name="awal_foot_c_4" value="OK" onclick="footvalue('c_4',this.value,'awal')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="awal_foot_c_4" name="awal_foot_c_4" onclick="footvalue('c_4',this.value,'awal')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_foot_status_4" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_4',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_foot_a_4">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_4',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_foot_b_4">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist1_foot_c_4" name="ist1_foot_c_4" value="OK" onclick="footvalue('c_4',this.value,'ist1')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist1_foot_c_4" name="ist1_foot_c_4" onclick="footvalue('c_4',this.value,'ist1')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_foot_status_4" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_4',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_foot_a_4">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_4',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_foot_b_4">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist2_foot_c_4" name="ist2_foot_c_4" value="OK" onclick="footvalue('c_4',this.value,'ist2')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist2_foot_c_4" name="ist2_foot_c_4" onclick="footvalue('c_4',this.value,'ist2')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_foot_status_4" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_4',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_foot_a_4">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_4',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_foot_b_4">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist3_foot_c_4" name="ist3_foot_c_4" value="OK" onclick="footvalue('c_4',this.value,'ist3')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist3_foot_c_4" name="ist3_foot_c_4" onclick="footvalue('c_4',this.value,'ist3')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_foot_status_4" readonly>
										</td>

									</tr>

									<tr>
										<td id="cav_foot_5">5</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_5',this.value,'awal')" placeholder="A" class="form-control" id="awal_foot_a_5">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_5',this.value,'awal')" placeholder="B" class="form-control" id="awal_foot_b_5">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="awal_foot_c_5" name="awal_foot_c_5" value="OK" onclick="footvalue('c_5',this.value,'awal')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="awal_foot_c_5" name="awal_foot_c_5" onclick="footvalue('c_5',this.value,'awal')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_foot_status_5" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_5',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_foot_a_5">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_5',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_foot_b_5">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist1_foot_c_5" name="ist1_foot_c_5" value="OK" onclick="footvalue('c_5',this.value,'ist1')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist1_foot_c_5" name="ist1_foot_c_5" onclick="footvalue('c_5',this.value,'ist1')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_foot_status_5" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_5',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_foot_a_5">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_5',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_foot_b_5">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist2_foot_c_5" name="ist2_foot_c_5" value="OK" onclick="footvalue('c_5',this.value,'ist2')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist2_foot_c_5" name="ist2_foot_c_5" onclick="footvalue('c_5',this.value,'ist2')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_foot_status_5" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_5',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_foot_a_5">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_5',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_foot_b_5">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist3_foot_c_5" name="ist3_foot_c_5" value="OK" onclick="footvalue('c_5',this.value,'ist3')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist3_foot_c_5" name="ist3_foot_c_5" onclick="footvalue('c_5',this.value,'ist3')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_foot_status_5" readonly>
										</td>

									</tr>

									<tr>
										<td id="cav_foot_6">6</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_6',this.value,'awal')" placeholder="A" class="form-control" id="awal_foot_a_6">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_6',this.value,'awal')" placeholder="B" class="form-control" id="awal_foot_b_6">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="awal_foot_c_6" name="awal_foot_c_6" value="OK" onclick="footvalue('c_6',this.value,'awal')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="awal_foot_c_6" name="awal_foot_c_6" onclick="footvalue('c_6',this.value,'awal')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_foot_status_6" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_6',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_foot_a_6">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_6',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_foot_b_6">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist1_foot_c_6" name="ist1_foot_c_6" value="OK" onclick="footvalue('c_6',this.value,'ist1')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist1_foot_c_6" name="ist1_foot_c_6" onclick="footvalue('c_6',this.value,'ist1')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_foot_status_6" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_6',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_foot_a_6">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_6',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_foot_b_6">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist2_foot_c_6" name="ist2_foot_c_6" value="OK" onclick="footvalue('c_6',this.value,'ist2')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist2_foot_c_6" name="ist2_foot_c_6" onclick="footvalue('c_6',this.value,'ist2')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_foot_status_6" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('a_6',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_foot_a_6">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="footvalue('b_6',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_foot_b_6">
										</td>
										<td>
											<div class="radio">
										    	<label class="radio" style="margin-top: 0px;margin-left: 0px;font-size: 15px">
													<input type="radio" id="ist3_foot_c_6" name="ist3_foot_c_6" value="OK" onclick="footvalue('c_6',this.value,'ist3')">&nbsp;&nbsp;&nbsp;OK
													<span class="checkmark"></span>
												</label>
												<br>
												<br>
												<label class="radio" style="margin-top: 0px;font-size: 15px">
													<input type="radio" id="ist3_foot_c_6" name="ist3_foot_c_6" onclick="footvalue('c_6',this.value,'ist3')" value="NG">&nbsp;&nbsp;&nbsp;NG
													<span class="checkmark"></span>
												</label>
											</div>
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_foot_status_6" readonly>
										</td>

									</tr>
								</tbody>
							</table>

							<!-- TABLE HEAD YRF -->
							<table class="table table-striped table-bordered" id="head_yrf">
								<thead>
									<tr>
										<th rowspan="2" style="background-color: #a5adff;border-bottom: 3px solid #ffa5a5">Cav</th>
										<th colspan="3" style="background-color: #ffd6a5;">Awal Proses</th>
										<th colspan="3" style="background-color: #9bf6ff;">Istirahat 1</th>
										<th colspan="3" style="background-color: #ffc6ff;">Istirahat 2</th>
										<th colspan="3" style="background-color: #caffbf;">Istirahat 3</th>
									</tr>
									<tr>
										<th style="background-color: #ffd6a5;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #ffd6a5;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #ffd6a5;border-bottom: 3px solid #ffa5a5">Jdg</th>
										<th style="background-color: #9bf6ff;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #9bf6ff;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #9bf6ff;border-bottom: 3px solid #ffa5a5">Jdg</th>
										<th style="background-color: #ffc6ff;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #ffc6ff;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #ffc6ff;border-bottom: 3px solid #ffa5a5">Jdg</th>
										<th style="background-color: #caffbf;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #caffbf;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #caffbf;border-bottom: 3px solid #ffa5a5">Jdg</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td id="cav_head_yrf_1">1</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="head_yrfvalue('a_1',this.value,'awal')" placeholder="A" class="form-control" id="awal_head_yrf_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="head_yrfvalue('b_1',this.value,'awal')" placeholder="B" class="form-control" id="awal_head_yrf_b_1">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_head_yrf_status_1" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="head_yrfvalue('a_1',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_head_yrf_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="head_yrfvalue('b_1',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_head_yrf_b_1">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_head_yrf_status_1" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="head_yrfvalue('a_1',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_head_yrf_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="head_yrfvalue('b_1',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_head_yrf_b_1">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_head_yrf_status_1" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="head_yrfvalue('a_1',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_head_yrf_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="head_yrfvalue('b_1',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_head_yrf_b_1">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_head_yrf_status_1" readonly>
										</td>

									</tr>
									<tr>
										<td id="cav_head_yrf_2">2</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="head_yrfvalue('a_2',this.value,'awal')" placeholder="A" class="form-control" id="awal_head_yrf_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="head_yrfvalue('b_2',this.value,'awal')" placeholder="B" class="form-control" id="awal_head_yrf_b_2">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_head_yrf_status_2" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="head_yrfvalue('a_2',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_head_yrf_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="head_yrfvalue('b_2',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_head_yrf_b_2">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_head_yrf_status_2" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="head_yrfvalue('a_2',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_head_yrf_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="head_yrfvalue('b_2',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_head_yrf_b_2">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_head_yrf_status_2" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="head_yrfvalue('a_2',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_head_yrf_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="head_yrfvalue('b_2',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_head_yrf_b_2">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_head_yrf_status_2" readonly>
										</td>

									</tr>
								</tbody>
							</table>

							<!-- TABLE BODY YRF -->
							<table class="table table-striped table-bordered" id="body_yrf">
								<thead>
									<tr>
										<th rowspan="2" style="background-color: #a5adff;border-bottom: 3px solid #ffa5a5">Cav</th>
										<th colspan="3" style="background-color: #ffd6a5;">Awal Proses</th>
										<th colspan="3" style="background-color: #9bf6ff;">Istirahat 1</th>
										<th colspan="3" style="background-color: #ffc6ff;">Istirahat 2</th>
										<th colspan="3" style="background-color: #caffbf;">Istirahat 3</th>
									</tr>
									<tr>
										<th style="background-color: #ffd6a5;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #ffd6a5;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #ffd6a5;border-bottom: 3px solid #ffa5a5">Jdg</th>
										<th style="background-color: #9bf6ff;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #9bf6ff;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #9bf6ff;border-bottom: 3px solid #ffa5a5">Jdg</th>
										<th style="background-color: #ffc6ff;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #ffc6ff;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #ffc6ff;border-bottom: 3px solid #ffa5a5">Jdg</th>
										<th style="background-color: #caffbf;border-bottom: 3px solid #ffa5a5">A</th>
										<th style="background-color: #caffbf;border-bottom: 3px solid #ffa5a5">B</th>
										<th style="background-color: #caffbf;border-bottom: 3px solid #ffa5a5">Jdg</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td id="cav_body_yrf_1">1</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="body_yrfvalue('a_1',this.value,'awal')" placeholder="A" class="form-control" id="awal_body_yrf_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="body_yrfvalue('b_1',this.value,'awal')" placeholder="B" class="form-control" id="awal_body_yrf_b_1">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_body_yrf_status_1" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="body_yrfvalue('a_1',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_body_yrf_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="body_yrfvalue('b_1',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_body_yrf_b_1">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_body_yrf_status_1" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="body_yrfvalue('a_1',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_body_yrf_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="body_yrfvalue('b_1',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_body_yrf_b_1">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_body_yrf_status_1" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="body_yrfvalue('a_1',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_body_yrf_a_1">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="body_yrfvalue('b_1',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_body_yrf_b_1">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_body_yrf_status_1" readonly>
										</td>

									</tr>
									<tr>
										<td id="cav_body_yrf_2">2</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="body_yrfvalue('a_2',this.value,'awal')" placeholder="A" class="form-control" id="awal_body_yrf_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="body_yrfvalue('b_2',this.value,'awal')" placeholder="B" class="form-control" id="awal_body_yrf_b_2">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="awal_body_yrf_status_2" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="body_yrfvalue('a_2',this.value,'ist1')" placeholder="A" class="form-control" id="ist1_body_yrf_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="body_yrfvalue('b_2',this.value,'ist1')" placeholder="B" class="form-control" id="ist1_body_yrf_b_2">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist1_body_yrf_status_2" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="body_yrfvalue('a_2',this.value,'ist2')" placeholder="A" class="form-control" id="ist2_body_yrf_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="body_yrfvalue('b_2',this.value,'ist2')" placeholder="B" class="form-control" id="ist2_body_yrf_b_2">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist2_body_yrf_status_2" readonly>
										</td>

										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="body_yrfvalue('a_2',this.value,'ist3')" placeholder="A" class="form-control" id="ist3_body_yrf_a_2">
										</td>
										<td>
											<input type="number" style="font-size: 15px; height: 100%;width: 100%; text-align: center;"onkeyup="body_yrfvalue('b_2',this.value,'ist3')" placeholder="B" class="form-control" id="ist3_body_yrf_b_2">
										</td>
										<td>
											<input type="text" style="font-size: 15px; height: 100%;width: 100%; text-align: center;" placeholder="Status" class="form-control" id="ist3_body_yrf_status_2" readonly>
										</td>

									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				
				<div class="col-xs-12">
					<div class="row">
						<button class="btn btn-success btn-block" style="height: 50px;font-size: 30px;font-weight: bold;" onclick="inputCdm()">
							<i class="fa fa-save"></i> SAVE
						</button>
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
										<th style="width: 1%;text-align:center;">No.</th>
										<th style="width: 2%;text-align:center;">Product</th>
										<th style="width: 1%;text-align:center;">Cavity</th>
										<th style="width: 2%;text-align:center;">Mesin</th>
										<th style="width: 2%;text-align:center;">Injection</th>
										<th style="width: 2%;text-align:center;background-color: #ffd6a5">Awal</th>
										<th style="width: 2%;text-align:center;background-color: #9bf6ff">Istirahat 1</th>
										<th style="width: 2%;text-align:center;background-color: #ffc6ff">Istirahat 2</th>
										<th style="width: 2%;text-align:center;background-color: #caffbf">Istirahat 3</th>
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
							<span style="font-size: 1.4vw">A. Panjang Head = 124 - 124.7 mm (Nogisu)</span><br>
							<span style="font-size: 1.4vw">B. Kedalaman middle joint shaft = 22.5 - 22.8 mm (Depht gauge)</span><br>
							<span style="font-size: 1.4vw">C. Cek celah <= 0.20 mm (Thickness gauge)</span><br>
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
							<span style="font-size: 1.4vw">A. 173.1 mm - 174.1 mm</span><br>
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
		$('#cdm_code').val('');
		$('#product').val("");
		$('#part').val("");
		$('#color').val("");
		$('#type').val("");
		$('#injection_date').val("");
		$('#machine').val("").trigger('change');
		$('#machine_injection').val("").trigger('change');
		$('#cavity').val("").trigger('change');

		$('#injection_date').prop('disabled',true);
		$('#machine').prop('disabled',true);
		$('#machine_injection').prop('disabled',true);
		$('#cavity').prop('disabled',true);

		$('#head').hide();
		$('#middle').hide();
		$('#foot').hide();

		$('#head_yrf').hide();
		$('#body_yrf').hide();

		$('#id_cdm_1').val('');
		$('#id_cdm_2').val('');
		$('#id_cdm_3').val('');
		$('#id_cdm_4').val('');
		$('#id_cdm_5').val('');
		$('#id_cdm_6').val('');

		for (var i = 1; i <=4; i++) {
			$('#awal_head_a_'+i).val("");
			$('#awal_head_b_'+i).val("");
			$("input[name=awal_head_c_"+i+"]").prop('checked', false);
			$('#awal_head_status_'+i).val("");
			document.getElementById('awal_head_status_'+i).style.backgroundColor = "#fff";

			$('#ist1_head_a_'+i).val("");
			$('#ist1_head_b_'+i).val("");
			$("input[name=ist1_head_c_"+i+"]").prop('checked', false);
			$('#ist1_head_status_'+i).val("");
			document.getElementById('ist1_head_status_'+i).style.backgroundColor = "#fff";

			$('#ist2_head_a_'+i).val("");
			$('#ist2_head_b_'+i).val("");
			$("input[name=ist2_head_c_"+i+"]").prop('checked', false);
			$('#ist2_head_status_'+i).val("");
			document.getElementById('ist2_head_status_'+i).style.backgroundColor = "#fff";

			$('#ist3_head_a_'+i).val("");
			$('#ist3_head_b_'+i).val("");
			$("input[name=ist3_head_c_"+i+"]").prop('checked', false);
			$('#ist3_head_status_'+i).val("");
			document.getElementById('ist3_head_status_'+i).style.backgroundColor = "#fff";

			$('#cav_head_'+i).html(i);
		}

		for (var i = 1; i <=4; i++) {
			$('#awal_middle_a_'+i).val("");
			$('#awal_middle_b_'+i).val("");
			$('#awal_middle_status_'+i).val("");
			document.getElementById('awal_middle_status_'+i).style.backgroundColor = "#fff";

			$('#ist1_middle_a_'+i).val("");
			$('#ist1_middle_b_'+i).val("");
			$('#ist1_middle_status_'+i).val("");
			document.getElementById('ist1_middle_status_'+i).style.backgroundColor = "#fff";

			$('#ist2_middle_a_'+i).val("");
			$('#ist2_middle_b_'+i).val("");
			$('#ist2_middle_status_'+i).val("");
			document.getElementById('ist2_middle_status_'+i).style.backgroundColor = "#fff";

			$('#ist3_middle_a_'+i).val("");
			$('#ist3_middle_b_'+i).val("");
			$('#ist3_middle_status_'+i).val("");
			document.getElementById('ist3_middle_status_'+i).style.backgroundColor = "#fff";

			$('#cav_middle_'+i).html(i);
		}

		for (var i = 1; i <=6; i++) {
			$('#awal_foot_a_'+i).val("");
			$('#awal_foot_b_'+i).val("");
			$("input[name=awal_foot_c_"+i+"]").prop('checked', false);
			$('#awal_foot_status_'+i).val("");
			document.getElementById('awal_foot_status_'+i).style.backgroundColor = "#fff";

			$('#ist1_foot_a_'+i).val("");
			$('#ist1_foot_b_'+i).val("");
			$("input[name=ist1_foot_c_"+i+"]").prop('checked', false);
			$('#ist1_foot_status_'+i).val("");
			document.getElementById('ist1_foot_status_'+i).style.backgroundColor = "#fff";

			$('#ist2_foot_a_'+i).val("");
			$('#ist2_foot_b_'+i).val("");
			$("input[name=ist2_foot_c_"+i+"]").prop('checked', false);
			$('#ist2_foot_status_'+i).val("");
			document.getElementById('ist2_foot_status_'+i).style.backgroundColor = "#fff";

			$('#ist3_foot_a_'+i).val("");
			$('#ist3_foot_b_'+i).val("");
			$("input[name=ist3_foot_c_"+i+"]").prop('checked', false);
			$('#ist3_foot_status_'+i).val("");
			document.getElementById('ist3_foot_status_'+i).style.backgroundColor = "#fff";

			$('#cav_foot_'+i).html(i);
		}

		for (var i = 1; i <=2; i++) {
			$('#awal_head_yrf_a_'+i).val("");
			$('#awal_head_yrf_b_'+i).val("");
			$("input[name=awal_head_yrf_c_"+i+"]").prop('checked', false);
			$('#awal_head_yrf_status_'+i).val("");
			document.getElementById('awal_head_yrf_status_'+i).style.backgroundColor = "#fff";

			$('#ist1_head_yrf_a_'+i).val("");
			$('#ist1_head_yrf_b_'+i).val("");
			$("input[name=ist1_head_yrf_c_"+i+"]").prop('checked', false);
			$('#ist1_head_yrf_status_'+i).val("");
			document.getElementById('ist1_head_yrf_status_'+i).style.backgroundColor = "#fff";

			$('#ist2_head_yrf_a_'+i).val("");
			$('#ist2_head_yrf_b_'+i).val("");
			$("input[name=ist2_head_yrf_c_"+i+"]").prop('checked', false);
			$('#ist2_head_yrf_status_'+i).val("");
			document.getElementById('ist2_head_yrf_status_'+i).style.backgroundColor = "#fff";

			$('#ist3_head_yrf_a_'+i).val("");
			$('#ist3_head_yrf_b_'+i).val("");
			$("input[name=ist3_head_yrf_c_"+i+"]").prop('checked', false);
			$('#ist3_head_yrf_status_'+i).val("");
			document.getElementById('ist3_head_yrf_status_'+i).style.backgroundColor = "#fff";

			$('#cav_head_yrf_'+i).html(i);
		}

		for (var i = 1; i <=2; i++) {
			$('#awal_body_yrf_a_'+i).val("");
			$('#awal_body_yrf_b_'+i).val("");
			$("input[name=awal_body_yrf_c_"+i+"]").prop('checked', false);
			$('#awal_body_yrf_status_'+i).val("");
			document.getElementById('awal_body_yrf_status_'+i).style.backgroundColor = "#fff";

			$('#ist1_body_yrf_a_'+i).val("");
			$('#ist1_body_yrf_b_'+i).val("");
			$("input[name=ist1_body_yrf_c_"+i+"]").prop('checked', false);
			$('#ist1_body_yrf_status_'+i).val("");
			document.getElementById('ist1_body_yrf_status_'+i).style.backgroundColor = "#fff";

			$('#ist2_body_yrf_a_'+i).val("");
			$('#ist2_body_yrf_b_'+i).val("");
			$("input[name=ist2_body_yrf_c_"+i+"]").prop('checked', false);
			$('#ist2_body_yrf_status_'+i).val("");
			document.getElementById('ist2_body_yrf_status_'+i).style.backgroundColor = "#fff";

			$('#ist3_body_yrf_a_'+i).val("");
			$('#ist3_body_yrf_b_'+i).val("");
			$("input[name=ist3_body_yrf_c_"+i+"]").prop('checked', false);
			$('#ist3_body_yrf_status_'+i).val("");
			document.getElementById('ist3_body_yrf_status_'+i).style.backgroundColor = "#fff";

			$('#cav_body_yrf_'+i).html(i);
		}
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

		var cav = type.split('_');


		if ($('#'+check+'_head_a_'+cav[1]).val() != "" && $('#'+check+'_head_b_'+cav[1]).val() != "") {
			if (parseFloat($('#'+check+'_head_a_'+cav[1]).val()) < parseFloat(bawah_a) || parseFloat($('#'+check+'_head_a_'+cav[1]).val()) > parseFloat(atas_a)) {
				status++;
			}

			if (parseFloat($('#'+check+'_head_b_'+cav[1]).val()) < parseFloat(bawah_b) || parseFloat($('#'+check+'_head_b_'+cav[1]).val()) > parseFloat(atas_b)) {
				status++;
			}

			if (type.match(/c/gi)) {
				if (value === 'NG') {
					status++;
				}
			}

			if (status > 0) {
				$('#'+check+'_head_status_'+cav[1]).val('NG');
				document.getElementById(''+check+'_head_status_'+cav[1]).style.backgroundColor = "#ff4f4f";
			}else{
				$('#'+check+'_head_status_'+cav[1]).val('OK');
				document.getElementById(''+check+'_head_status_'+cav[1]).style.backgroundColor = "#7fff6e";
			}
		}else{
			$('#'+check+'_head_status_'+cav[1]).val('');
			document.getElementById(''+check+'_head_status_'+cav[1]).style.backgroundColor = "#fff";
		}
	}

	function head_yrfvalue(type,value,check) {
		var bawah_a = '{{$head_yrf_a_bawah}}';
		var atas_a = '{{$head_yrf_a_atas}}';

		var bawah_b = '{{$head_yrf_b_bawah}}';
		var atas_b = '{{$head_yrf_b_atas}}';
		var status = 0;

		var cav = type.split('_');

		if ($('#'+check+'_head_yrf_a_'+cav[1]).val() != "" && $('#'+check+'_head_yrf_b_'+cav[1]).val() != "") {
			if (parseFloat($('#'+check+'_head_yrf_a_'+cav[1]).val()) < parseFloat(bawah_a) || parseFloat($('#'+check+'_head_yrf_a_'+cav[1]).val()) > parseFloat(atas_a)) {
				status++;
			}

			if (parseFloat($('#'+check+'_head_yrf_b_'+cav[1]).val()) < parseFloat(bawah_b) || parseFloat($('#'+check+'_head_yrf_b_'+cav[1]).val()) > parseFloat(atas_b)) {
				status++;
			}

			if (type.match(/c/gi)) {
				if (value === 'NG') {
					status++;
				}
			}

			if (status > 0) {
				$('#'+check+'_head_yrf_status_'+cav[1]).val('NG');
				document.getElementById(''+check+'_head_yrf_status_'+cav[1]).style.backgroundColor = "#ff4f4f";
			}else{
				$('#'+check+'_head_yrf_status_'+cav[1]).val('OK');
				document.getElementById(''+check+'_head_yrf_status_'+cav[1]).style.backgroundColor = "#7fff6e";
			}
		}else{
			$('#'+check+'_head_yrf_status_'+cav[1]).val('');
			document.getElementById(''+check+'_head_yrf_status_'+cav[1]).style.backgroundColor = "#fff";
		}
	}

	function body_yrfvalue(type,value,check) {
		var bawah_a = '{{$body_yrf_a_bawah}}';
		var atas_a = '{{$body_yrf_a_atas}}';

		var bawah_b = '{{$body_yrf_b_bawah}}';
		var atas_b = '{{$body_yrf_b_atas}}';
		var status = 0;

		var cav = type.split('_');

		if ($('#'+check+'_body_yrf_a_'+cav[1]).val() != "" && $('#'+check+'_body_yrf_b_'+cav[1]).val() != "") {
			if (parseFloat($('#'+check+'_body_yrf_a_'+cav[1]).val()) < parseFloat(bawah_a) || parseFloat($('#'+check+'_body_yrf_a_'+cav[1]).val()) > parseFloat(atas_a)) {
				status++;
			}

			if (parseFloat($('#'+check+'_body_yrf_b_'+cav[1]).val()) < parseFloat(bawah_b) || parseFloat($('#'+check+'_body_yrf_b_'+cav[1]).val()) > parseFloat(atas_b)) {
				status++;
			}

			if (status > 0) {
				$('#'+check+'_body_yrf_status_'+cav[1]).val('NG');
				document.getElementById(''+check+'_body_yrf_status_'+cav[1]).style.backgroundColor = "#ff4f4f";
			}else{
				$('#'+check+'_body_yrf_status_'+cav[1]).val('OK');
				document.getElementById(''+check+'_body_yrf_status_'+cav[1]).style.backgroundColor = "#7fff6e";
			}
		}else{
			$('#'+check+'_body_yrf_status_'+cav[1]).val('');
			document.getElementById(''+check+'_body_yrf_status_'+cav[1]).style.backgroundColor = "#fff";
		}
	}

	function middlevalue(type,value,check) {
		var bawah_a = '{{$middle_a_bawah}}';
		var atas_a = '{{$middle_a_atas}}';

		var bawah_b = '{{$middle_b_bawah}}';
		var atas_b = '{{$middle_b_atas}}';
		var status = 0;

		var cav = type.split('_');


		if ($('#'+check+'_middle_a_'+cav[1]).val() != "" && $('#'+check+'_middle_b_'+cav[1]).val() != "") {
			if (parseFloat($('#'+check+'_middle_a_'+cav[1]).val()) < parseFloat(bawah_a) || parseFloat($('#'+check+'_middle_a_'+cav[1]).val()) > parseFloat(atas_a)) {
				status++;
			}

			if (parseFloat($('#'+check+'_middle_b_'+cav[1]).val()) < parseFloat(bawah_b) || parseFloat($('#'+check+'_middle_b_'+cav[1]).val()) > parseFloat(atas_b)) {
				status++;
			}

			if (status > 0) {
				$('#'+check+'_middle_status_'+cav[1]).val('NG');
				document.getElementById(''+check+'_middle_status_'+cav[1]).style.backgroundColor = "#ff4f4f";
			}else{
				$('#'+check+'_middle_status_'+cav[1]).val('OK');
				document.getElementById(''+check+'_middle_status_'+cav[1]).style.backgroundColor = "#7fff6e";
			}
		}else{
			$('#'+check+'_middle_status_'+cav[1]).val('');
			document.getElementById(''+check+'_middle_status_'+cav[1]).style.backgroundColor = "#fff";
		}
	}

	function footvalue(type,value,check) {
		var bawah_a = '{{$foot_a_bawah}}';
		var atas_a = '{{$foot_a_atas}}';

		var bawah_b = '{{$foot_b_bawah}}';
		var atas_b = '{{$foot_b_atas}}';
		var status = 0;

		var cav = type.split('_');

		if ($('#'+check+'_foot_a_'+cav[1]).val() != "" && $('#'+check+'_foot_b_'+cav[1]).val() != "") {
			if (parseFloat($('#'+check+'_foot_a_'+cav[1]).val()) < parseFloat(bawah_a) || parseFloat($('#'+check+'_foot_a_'+cav[1]).val()) > parseFloat(atas_a)) {
				status++;
			}

			if (parseFloat($('#'+check+'_foot_b_'+cav[1]).val()) < parseFloat(bawah_b) || parseFloat($('#'+check+'_foot_b_'+cav[1]).val()) > parseFloat(atas_b)) {
				status++;
			}

			if (type.match(/c/gi)) {
				if (value === 'NG') {
					status++;
				}
			}

			if (status > 0) {
				$('#'+check+'_foot_status_'+cav[1]).val('NG');
				document.getElementById(''+check+'_foot_status_'+cav[1]).style.backgroundColor = "#ff4f4f";
			}else{
				$('#'+check+'_foot_status_'+cav[1]).val('OK');
				document.getElementById(''+check+'_foot_status_'+cav[1]).style.backgroundColor = "#7fff6e";
			}
		}else{
			$('#'+check+'_foot_status_'+cav[1]).val('');
			document.getElementById(''+check+'_foot_status_'+cav[1]).style.backgroundColor = "#fff";
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
			$('#injection_date').focus();
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
					cavity += '<option value=""></option>';
					$.each(result.datas, function(key, value) {
						cavity += '<option value="'+value.no_cavity+'">'+value.no_cavity+'</option>';
					});
				}
				$('#cavity').append(cavity);
			});

			machine = "";
			$('#machine').html('');

			machine += '<option value=""></option>';

			if (product.match(/YRS/gi)) {
				if (type === 'HEAD') {
					// $('#awal_head').show();
					// $('#awal_head_yrf').hide();
					// $('#awal_body_yrf').hide();
					// $('#awal_middle').hide();
					// $('#awal_foot').hide();

					// $('#ist1_head').show();
					// $('#ist1_head_yrf').hide();
					// $('#ist1_body_yrf').hide();
					// $('#ist1_middle').hide();
					// $('#ist1_foot').hide();

					// $('#ist2_head').show();
					// $('#ist2_head_yrf').hide();
					// $('#ist2_body_yrf').hide();
					// $('#ist2_middle').hide();
					// $('#ist2_foot').hide();

					// $('#ist3_head').show();
					// $('#ist3_head_yrf').hide();
					// $('#ist3_body_yrf').hide();
					// $('#ist3_middle').hide();
					// $('#ist3_foot').hide();
					$('#head').show();
					$('#middle').hide();
					$('#foot').hide();
					$('#head_yrf').hide();
					$('#body_yrf').hide();

					machine += '<option value="HJ 1">HJ 1</option>'
					machine += '<option value="HJ 2">HJ 2</option>';
					machine += '<option value="HJ 3">HJ 3</option>';

				}else if(type === 'MIDDLE'){
					// $('#awal_middle').show();
					// $('#awal_head_yrf').hide();
					// $('#awal_body_yrf').hide();
					// $('#awal_head').hide();
					// $('#awal_foot').hide();

					// $('#ist1_middle').show();
					// $('#ist1_head_yrf').hide();
					// $('#ist1_body_yrf').hide();
					// $('#ist1_head').hide();
					// $('#ist1_foot').hide();

					// $('#ist2_middle').show();
					// $('#ist2_head_yrf').hide();
					// $('#ist2_body_yrf').hide();
					// $('#ist2_head').hide();
					// $('#ist2_foot').hide();

					// $('#ist3_middle').show();
					// $('#ist3_head_yrf').hide();
					// $('#ist3_body_yrf').hide();
					// $('#ist3_head').hide();
					// $('#ist3_foot').hide();
					$('#head').hide();
					$('#middle').show();
					$('#foot').hide();
					$('#head_yrf').hide();
					$('#body_yrf').hide();

					machine += '<option value="MJ 1">MJ 1</option>';
					machine += '<option value="MJ 2">MJ 2</option>';
				}else if(type === 'FOOT'){
					// $('#awal_middle').hide();
					// $('#awal_head_yrf').hide();
					// $('#awal_body_yrf').hide();
					// $('#awal_head').hide();
					// $('#awal_foot').show();

					// $('#ist1_middle').hide();
					// $('#ist1_head_yrf').hide();
					// $('#ist1_body_yrf').hide();
					// $('#ist1_head').hide();
					// $('#ist1_foot').show();

					// $('#ist2_middle').hide();
					// $('#ist2_head_yrf').hide();
					// $('#ist2_body_yrf').hide();
					// $('#ist2_head').hide();
					// $('#ist2_foot').show();

					// $('#ist3_middle').hide();
					// $('#ist3_head_yrf').hide();
					// $('#ist3_body_yrf').hide();
					// $('#ist3_head').hide();
					// $('#ist3_foot').show();
					$('#head').hide();
					$('#middle').hide();
					$('#foot').show();
					$('#head_yrf').hide();
					$('#body_yrf').hide();

					machine += '<option value="FJ 1">FJ 1</option>';
					machine += '<option value="FJ 2">FJ 2</option>';
					machine += '<option value="FJ S 1">FJ S 1</option>';
					machine += '<option value="FJ S 2">FJ S 2</option>';
				}
			}else{
				if (type === 'HEAD') {
					// $('#awal_head').hide();
					// $('#awal_head_yrf').show();
					// $('#awal_body_yrf').hide();
					// $('#awal_middle').hide();
					// $('#awal_foot').hide();

					// $('#ist1_head').hide();
					// $('#ist1_head_yrf').show();
					// $('#ist1_body_yrf').hide();
					// $('#ist1_middle').hide();
					// $('#ist1_foot').hide();

					// $('#ist2_head').hide();
					// $('#ist2_head_yrf').show();
					// $('#ist2_body_yrf').hide();
					// $('#ist2_middle').hide();
					// $('#ist2_foot').hide();

					// $('#ist3_head').hide();
					// $('#ist3_head_yrf').show();
					// $('#ist3_body_yrf').hide();
					// $('#ist3_middle').hide();
					// $('#ist3_foot').hide();
					$('#head').hide();
					$('#middle').hide();
					$('#foot').hide();
					$('#head_yrf').show();
					$('#body_yrf').hide();
				}else if(type === 'BODY'){
					// $('#awal_middle').hide();
					// $('#awal_head_yrf').hide();
					// $('#awal_body_yrf').show();
					// $('#awal_head').hide();
					// $('#awal_foot').hide();

					// $('#ist1_middle').hide();
					// $('#ist1_head_yrf').hide();
					// $('#ist1_body_yrf').show();
					// $('#ist1_head').hide();
					// $('#ist1_foot').hide();

					// $('#ist2_middle').hide();
					// $('#ist2_head_yrf').hide();
					// $('#ist2_body_yrf').show();
					// $('#ist2_head').hide();
					// $('#ist2_foot').hide();

					// $('#ist3_middle').hide();
					// $('#ist3_head_yrf').hide();
					// $('#ist3_body_yrf').show();
					// $('#ist3_head').hide();
					// $('#ist3_foot').hide();
					$('#head').hide();
					$('#middle').hide();
					$('#foot').hide();
					$('#head_yrf').hide();
					$('#body_yrf').show();
				}
				machine += '<option value="YRF">YRF</option>';
			}

			$('#injection_date').removeAttr('disabled');
			$('#machine').removeAttr('disabled');
			$('#machine_injection').removeAttr('disabled');
			$('#cavity').removeAttr('disabled');

			$('#machine').append(machine);
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
					tableData += '<td>'+ part[0] +'<br>'+value.part_type.toUpperCase()+'<br>'+value.part_code+'<br>'+value.color+'</td>';
					// tableData += '<td>'+ value.part_type.toUpperCase() +'</td>';
					// tableData += '<td>'+ value.part_code +'</td>';
					// tableData += '<td>'+ value.color +'</td>';
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
					tableData += '<tr onclick="fetchCdm(\''+value.cdm_code+'\')">';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td style="text-align:center">'+ value.product +'<br>'+value.part+' - '+value.color+'</td>';
					tableData += '<td style="text-align:center">'+ value.cavity +'</td>';
					tableData += '<td style="text-align:center">'+ value.machine +'</td>';
					tableData += '<td style="text-align:center">'+ value.injection_date +'<br>Mesin '+(value.machine_injection || "??")+'</td>';
					tableData += '<td style="text-align:center;background-color: #ffd6a5">'+ value.awal_name+'</td>';
					tableData += '<td style="text-align:center;background-color: #9bf6ff">'+ value.ist_1_name+'</td>';
					tableData += '<td style="text-align:center;background-color: #ffc6ff">'+ value.ist_2_name+'</td>';
					tableData += '<td style="text-align:center;background-color: #caffbf">'+ value.ist_3_name+'</td>';
					// tableData += '<td style="background-color: #ffd6a5">'+ value.awal_a +'</td>';
					// tableData += '<td style="background-color: #ffd6a5">'+ value.awal_b +'</td>';
					// tableData += '<td style="background-color: #ffd6a5">'+ value.awal_c +'</td>';
					// tableData += '<td style="background-color: #ffd6a5">'+ value.awal_status +'</td>';
					// tableData += '<td style="background-color: #9bf6ff">'+ value.ist_1_a +'</td>';
					// tableData += '<td style="background-color: #9bf6ff">'+ value.ist_1_b +'</td>';
					// tableData += '<td style="background-color: #9bf6ff">'+ value.ist_1_c +'</td>';
					// tableData += '<td style="background-color: #9bf6ff">'+ value.ist_1_status +'</td>';
					// tableData += '<td style="background-color: #ffc6ff">'+ value.ist_2_a +'</td>';
					// tableData += '<td style="background-color: #ffc6ff">'+ value.ist_2_b +'</td>';
					// tableData += '<td style="background-color: #ffc6ff">'+ value.ist_2_c +'</td>';
					// tableData += '<td style="background-color: #ffc6ff">'+ value.ist_2_status +'</td>';
					// tableData += '<td style="background-color: #caffbf">'+ value.ist_3_a +'</td>';
					// tableData += '<td style="background-color: #caffbf">'+ value.ist_3_b +'</td>';
					// tableData += '<td style="background-color: #caffbf">'+ value.ist_3_c +'</td>';
					// tableData += '<td style="background-color: #caffbf">'+ value.ist_3_status +'</td>';
					// tableData += '<td style="text-align:center">'+ value.employee_id +'<br>'+ value.name +'</td>';
					// tableData += '<td style="text-align:center">'+ exp[4] +'</td>';
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
		$('#loading').show();
		if ($('#op').val() == '') {
			$('#loading').hide();
			openErrorGritter('Error!', "Scan ID Card First!");
			$('#tag').focus();
		}else{
			emptyAll();

			var data = {
				cdm_code:id
			}

			$.get('{{ url("fetch/recorder/cdm") }}',data, function(result, status, xhr){
				if(result.status){
					$('#save_type').val('UPDATE');
					machine = "";
					$('#machine').html('');

					machine += '<option value=""></option>';

					if (result.datas[0].product.match(/YRS/gi)) {
						if (result.datas[0].type === 'HEAD') {

							$('#head').show();
							$('#middle').hide();
							$('#foot').hide();
							$('#head_yrf').hide();
							$('#body_yrf').hide();

							var index = 1;

							machine += '<option value="HJ 1">HJ 1</option>';
							machine += '<option value="HJ 2">HJ 2</option>';
							machine += '<option value="HJ 3">HJ 3</option>';

							for (var i = 0; i < result.datas.length; i++) {
								$('#id_cdm_'+index).val(result.datas[i].id_cdm);
								$('#cav_head_'+index).html(result.datas[i].cav);
								$('#awal_head_a_'+index).val(result.datas[i].awal_a);
								$('#awal_head_b_'+index).val(result.datas[i].awal_b);
								$("input[name=awal_head_c_"+index+"][value=" + result.datas[i].awal_c + "]").prop('checked', true);
								$('#awal_head_status_'+index).val(result.datas[i].awal_status);

								if (result.datas[i].awal_status == 'NG') {
									document.getElementById('awal_head_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].awal_status == 'OK'){
									document.getElementById('awal_head_status_'+index).style.backgroundColor = "#7fff6e";
								}

								$('#ist1_head_a_'+index).val(result.datas[i].ist_1_a);
								$('#ist1_head_b_'+index).val(result.datas[i].ist_1_b);
								$("input[name=ist1_head_c_"+index+"][value=" + result.datas[i].ist_1_c + "]").prop('checked', true);
								$('#ist1_head_status_'+index).val(result.datas[i].ist_1_status);

								if (result.datas[i].ist_1_status == 'NG') {
									document.getElementById('ist1_head_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].ist_1_status == 'OK'){
									document.getElementById('ist1_head_status_'+index).style.backgroundColor = "#7fff6e";
								}

								$('#ist2_head_a_'+index).val(result.datas[i].ist_2_a);
								$('#ist2_head_b_'+index).val(result.datas[i].ist_2_b);
								$("input[name=ist2_head_c_"+index+"][value=" + result.datas[i].ist_2_c + "]").prop('checked', true);
								$('#ist2_head_status_'+index).val(result.datas[i].ist_2_status);

								if (result.datas[i].ist_2_status == 'NG') {
									document.getElementById('ist2_head_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].ist_2_status == 'OK'){
									document.getElementById('ist2_head_status_'+index).style.backgroundColor = "#7fff6e";
								}

								$('#ist3_head_a_'+index).val(result.datas[i].ist_3_a);
								$('#ist3_head_b_'+index).val(result.datas[i].ist_3_b);
								$("input[name=ist3_head_c_"+index+"][value=" + result.datas[i].ist_3_c + "]").prop('checked', true);
								$('#ist3_head_status_'+index).val(result.datas[i].ist_3_status);

								if (result.datas[i].ist_3_status == 'NG') {
									document.getElementById('ist3_head_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].ist_3_status == 'OK'){
									document.getElementById('ist3_head_status_'+index).style.backgroundColor = "#7fff6e";
								}

								index++;
							}

						}else if(result.datas[0].type === 'MIDDLE'){
							$('#head').hide();
							$('#middle').show();
							$('#foot').hide();
							$('#head_yrf').hide();
							$('#body_yrf').hide();

							var index = 1;

							machine += '<option value="MJ 1">MJ 1</option>';
							machine += '<option value="MJ 2">MJ 2</option>';

							for (var i = 0; i < result.datas.length; i++) {
								$('#id_cdm_'+index).val(result.datas[i].id_cdm);
								$('#cav_middle_'+index).html(result.datas[i].cav);
								$('#awal_middle_a_'+index).val(result.datas[i].awal_a);
								$('#awal_middle_b_'+index).val(result.datas[i].awal_b);
								$('#awal_middle_status_'+index).val(result.datas[i].awal_status);

								if (result.datas[i].awal_status == 'NG') {
									document.getElementById('awal_middle_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].awal_status == 'OK'){
									document.getElementById('awal_middle_status_'+index).style.backgroundColor = "#7fff6e";
								}

								$('#ist1_middle_a_'+index).val(result.datas[i].ist_1_a);
								$('#ist1_middle_b_'+index).val(result.datas[i].ist_1_b);
								$('#ist1_middle_status_'+index).val(result.datas[i].ist_1_status);

								if (result.datas[i].ist_1_status == 'NG') {
									document.getElementById('ist1_middle_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].ist_1_status == 'OK'){
									document.getElementById('ist1_middle_status_'+index).style.backgroundColor = "#7fff6e";
								}

								$('#ist2_middle_a_'+index).val(result.datas[i].ist_2_a);
								$('#ist2_middle_b_'+index).val(result.datas[i].ist_2_b);
								$('#ist2_middle_status_'+index).val(result.datas[i].ist_2_status);

								if (result.datas[i].ist_2_status == 'NG') {
									document.getElementById('ist2_middle_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].ist_2_status == 'OK'){
									document.getElementById('ist2_middle_status_'+index).style.backgroundColor = "#7fff6e";
								}

								$('#ist3_middle_a_'+index).val(result.datas[i].ist_3_a);
								$('#ist3_middle_b_'+index).val(result.datas[i].ist_3_b);
								$('#ist3_middle_status_'+index).val(result.datas[i].ist_3_status);

								if (result.datas[i].ist_3_status == 'NG') {
									document.getElementById('ist3_middle_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].ist_3_status == 'OK'){
									document.getElementById('ist3_middle_status_'+index).style.backgroundColor = "#7fff6e";
								}

								index++;
							}

						}else if(result.datas[0].type === 'FOOT'){
							$('#head').hide();
							$('#middle').hide();
							$('#foot').show();
							$('#head_yrf').hide();
							$('#body_yrf').hide();

							var index = 1;

							machine += '<option value="FJ 1">FJ 1</option>';
							machine += '<option value="FJ 2">FJ 2</option>';
							machine += '<option value="FJ S 1">FJ S 1</option>';
							machine += '<option value="FJ S 2">FJ S 2</option>';

							for (var i = 0; i < result.datas.length; i++) {
								$('#id_cdm_'+index).val(result.datas[i].id_cdm);
								$('#cav_foot_'+index).html(result.datas[i].cav);
								$('#awal_foot_a_'+index).val(result.datas[i].awal_a);
								$('#awal_foot_b_'+index).val(result.datas[i].awal_b);
								$("input[name=awal_foot_c_"+index+"][value=" + result.datas[i].awal_c + "]").prop('checked', true);
								$('#awal_foot_status_'+index).val(result.datas[i].awal_status);

								if (result.datas[i].awal_status == 'NG') {
									document.getElementById('awal_foot_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].awal_status == 'OK'){
									document.getElementById('awal_foot_status_'+index).style.backgroundColor = "#7fff6e";
								}

								$('#ist1_foot_a_'+index).val(result.datas[i].ist_1_a);
								$('#ist1_foot_b_'+index).val(result.datas[i].ist_1_b);
								$("input[name=ist1_foot_c_"+index+"][value=" + result.datas[i].ist_1_c + "]").prop('checked', true);
								$('#ist1_foot_status_'+index).val(result.datas[i].ist_1_status);

								if (result.datas[i].ist_1_status == 'NG') {
									document.getElementById('ist1_foot_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].ist_1_status == 'OK'){
									document.getElementById('ist1_foot_status_'+index).style.backgroundColor = "#7fff6e";
								}

								$('#ist2_foot_a_'+index).val(result.datas[i].ist_2_a);
								$('#ist2_foot_b_'+index).val(result.datas[i].ist_2_b);
								$("input[name=ist2_foot_c_"+index+"][value=" + result.datas[i].ist_2_c + "]").prop('checked', true);
								$('#ist2_foot_status_'+index).val(result.datas[i].ist_2_status);

								if (result.datas[i].ist_2_status == 'NG') {
									document.getElementById('ist2_foot_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].ist_2_status == 'OK'){
									document.getElementById('ist2_foot_status_'+index).style.backgroundColor = "#7fff6e";
								}

								$('#ist3_foot_a_'+index).val(result.datas[i].ist_3_a);
								$('#ist3_foot_b_'+index).val(result.datas[i].ist_3_b);
								$("input[name=ist3_foot_c_"+index+"][value=" + result.datas[i].ist_3_c + "]").prop('checked', true);
								$('#ist3_foot_status_'+index).val(result.datas[i].ist_3_status);

								if (result.datas[i].ist_3_status == 'NG') {
									document.getElementById('ist3_foot_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].ist_3_status == 'OK'){
									document.getElementById('ist3_foot_status_'+index).style.backgroundColor = "#7fff6e";
								}

								index++;
							}
						}
					}else{
						if (result.datas[0].type === 'HEAD') {

							$('#head').hide();
							$('#middle').hide();
							$('#foot').hide();
							$('#head_yrf').show();
							$('#body_yrf').hide();

							var index = 1;

							machine += '<option value="YRF">YRF</option>';

							for (var i = 0; i < result.datas.length; i++) {
								$('#id_cdm_'+index).val(result.datas[i].id_cdm);
								$('#cav_head_yrf_'+index).html(result.datas[i].cav);
								$('#awal_head_yrf_a_'+index).val(result.datas[i].awal_a);
								$('#awal_head_yrf_b_'+index).val(result.datas[i].awal_b);
								$('#awal_head_yrf_status_'+index).val(result.datas[i].awal_status);

								if (result.datas[i].awal_status == 'NG') {
									document.getElementById('awal_head_yrf_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].awal_status == 'OK'){
									document.getElementById('awal_head_yrf_status_'+index).style.backgroundColor = "#7fff6e";
								}

								$('#ist1_head_yrf_a_'+index).val(result.datas[i].ist_1_a);
								$('#ist1_head_yrf_b_'+index).val(result.datas[i].ist_1_b);
								$('#ist1_head_yrf_status_'+index).val(result.datas[i].ist_1_status);

								if (result.datas[i].ist_1_status == 'NG') {
									document.getElementById('ist1_head_yrf_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].ist_1_status == 'OK'){
									document.getElementById('ist1_head_yrf_status_'+index).style.backgroundColor = "#7fff6e";
								}

								$('#ist2_head_yrf_a_'+index).val(result.datas[i].ist_2_a);
								$('#ist2_head_yrf_b_'+index).val(result.datas[i].ist_2_b);
								$('#ist2_head_yrf_status_'+index).val(result.datas[i].ist_2_status);

								if (result.datas[i].ist_2_status == 'NG') {
									document.getElementById('ist2_head_yrf_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].ist_2_status == 'OK'){
									document.getElementById('ist2_head_yrf_status_'+index).style.backgroundColor = "#7fff6e";
								}

								$('#ist3_head_yrf_a_'+index).val(result.datas[i].ist_3_a);
								$('#ist3_head_yrf_b_'+index).val(result.datas[i].ist_3_b);
								$('#ist3_head_yrf_status_'+index).val(result.datas[i].ist_3_status);

								if (result.datas[i].ist_3_status == 'NG') {
									document.getElementById('ist3_head_yrf_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].ist_3_status == 'OK'){
									document.getElementById('ist3_head_yrf_status_'+index).style.backgroundColor = "#7fff6e";
								}

								index++;
							}

						}else if(result.datas[0].type === 'BODY'){
							$('#head').hide();
							$('#middle').hide();
							$('#foot').hide();
							$('#head_yrf').hide();
							$('#body_yrf').show();

							var index = 1;

							machine += '<option value="YRF">YRF</option>';

							for (var i = 0; i < result.datas.length; i++) {
								$('#id_cdm_'+index).val(result.datas[i].id_cdm);
								$('#cav_body_yrf_'+index).html(result.datas[i].cav);
								$('#awal_body_yrf_a_'+index).val(result.datas[i].awal_a);
								$('#awal_body_yrf_b_'+index).val(result.datas[i].awal_b);
								$('#awal_body_yrf_status_'+index).val(result.datas[i].awal_status);

								if (result.datas[i].awal_status == 'NG') {
									document.getElementById('awal_body_yrf_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].awal_status == 'OK'){
									document.getElementById('awal_body_yrf_status_'+index).style.backgroundColor = "#7fff6e";
								}

								$('#ist1_body_yrf_a_'+index).val(result.datas[i].ist_1_a);
								$('#ist1_body_yrf_b_'+index).val(result.datas[i].ist_1_b);
								$('#ist1_body_yrf_status_'+index).val(result.datas[i].ist_1_status);

								if (result.datas[i].ist_1_status == 'NG') {
									document.getElementById('ist1_body_yrf_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].ist_1_status == 'OK'){
									document.getElementById('ist1_body_yrf_status_'+index).style.backgroundColor = "#7fff6e";
								}

								$('#ist2_body_yrf_a_'+index).val(result.datas[i].ist_2_a);
								$('#ist2_body_yrf_b_'+index).val(result.datas[i].ist_2_b);
								$('#ist2_body_yrf_status_'+index).val(result.datas[i].ist_2_status);

								if (result.datas[i].ist_2_status == 'NG') {
									document.getElementById('ist2_body_yrf_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].ist_2_status == 'OK'){
									document.getElementById('ist2_body_yrf_status_'+index).style.backgroundColor = "#7fff6e";
								}

								$('#ist3_body_yrf_a_'+index).val(result.datas[i].ist_3_a);
								$('#ist3_body_yrf_b_'+index).val(result.datas[i].ist_3_b);
								$('#ist3_body_yrf_status_'+index).val(result.datas[i].ist_3_status);

								if (result.datas[i].ist_3_status == 'NG') {
									document.getElementById('ist3_body_yrf_status_'+index).style.backgroundColor = "#ff4f4f";
								}else if(result.datas[i].ist_3_status == 'OK'){
									document.getElementById('ist3_body_yrf_status_'+index).style.backgroundColor = "#7fff6e";
								}

								index++;
							}

						}
					}

					$('#injection_date').removeAttr('disabled');
					$('#machine').removeAttr('disabled');
					$('#machine_injection').removeAttr('disabled');
					$('#cavity').removeAttr('disabled');

					$('#machine').append(machine);

					var data2 = {
						type:result.datas[0].type.toLowerCase()
					}

					$('#cavity').empty();

					$.get('{{ url("fetch/cavity") }}',data2, function(result2, status, xhr){
						if(result2.status){
							var cavity = "";
							cavity += '<option value=""></option>';
							$.each(result2.datas, function(key, value) {
								cavity += '<option value="'+value.no_cavity+'">'+value.no_cavity+'</option>';
							});
						}
						$('#cavity').append(cavity);
						$('#cavity').val(result.datas[0].cavity).trigger('change');
					})

					$('#cdm_code').val(result.datas[0].cdm_code);
					$('#product').val(result.datas[0].product);
					$('#cavity').val(result.datas[0].cavity);
					$('#type').val(result.datas[0].type);
					$('#part').val(result.datas[0].part);
					$('#color').val(result.datas[0].color);
					$('#injection_date').val(result.datas[0].injection_date);
					$('#machine').val(result.datas[0].machine).trigger('change');
					$('#machine_injection').val(result.datas[0].machine_injection).trigger('change');

					$('#product').focus();
					$('#loading').hide();
					openSuccessGritter('Success','Success Get Data');
				}else{
					audio_error.play();
					openErrorGritter('Error!','Get Data Failed');
					$('#loading').hide();
				}
			})
		}
	}

	function inputCdm() {
		$('#loading').show();
		if ($('#product').val() == "" || $('#type').val() == ""|| $('#part').val() == "" || $('#color').val() == "" || $('#injection_date').val() == "" || $('#machine').val() == ""|| $('#machine_injection').val() == "" || $('#cavity').val() == "") {
			openErrorGritter('Error!', 'Semua Data Harus Diisi.');
			$('#loading').hide();
		}else{
			// $('#loading').show();
			var head = [];
			var middle = [];
			var foot = [];
			var head_yrf = [];
			var body_yrf = [];

			var datas = [];

			if ($('#product').val().match(/YRS/gi)) {
				if ($('#type').val() == 'HEAD') {
					for (var i = 1; i <= 4; i++) {
						if ($('input[id="awal_head_c_'+i+'"]:checked').val() == 'OK') {
							$awal_head_c = 'OK';
						}else if($('input[id="awal_head_c_'+i+'"]:checked').val() == 'NG'){
							$awal_head_c = 'NG';
						}else{
							$awal_head_c = null;
						}

						if ($('input[id="ist1_head_c_'+i+'"]:checked').val() == 'OK') {
							$ist1_head_c = 'OK';
						}else if($('input[id="ist1_head_c_'+i+'"]:checked').val() == 'NG'){
							$ist1_head_c = 'NG';
						}else{
							$ist1_head_c = null;
						}

						if ($('input[id="ist2_head_c_'+i+'"]:checked').val() == 'OK') {
							$ist2_head_c = 'OK';
						}else if($('input[id="ist2_head_c_'+i+'"]:checked').val() == 'NG'){
							$ist2_head_c = 'NG';
						}else{
							$ist2_head_c = null;
						}

						if ($('input[id="ist3_head_c_'+i+'"]:checked').val() == 'OK') {
							$ist3_head_c = 'OK';
						}else if($('input[id="ist3_head_c_'+i+'"]:checked').val() == 'NG'){
							$ist3_head_c = 'NG';
						}else{
							$ist3_head_c = null;
						}

						head.push(
						{
							'cav': $('#cav_head_'+i).text(),
							'awal_a': $('#awal_head_a_'+i).val(),
							'awal_b': $('#awal_head_b_'+i).val(),
							'awal_c': $awal_head_c,
							'awal_status': $('#awal_head_status_'+i).val(),
							'ist1_a': $('#ist1_head_a_'+i).val(),
							'ist1_b': $('#ist1_head_b_'+i).val(),
							'ist1_c': $ist1_head_c,
							'ist1_status': $('#ist1_head_status_'+i).val(),
							'ist2_a': $('#ist2_head_a_'+i).val(),
							'ist2_b': $('#ist2_head_b_'+i).val(),
							'ist2_c': $ist2_head_c,
							'ist2_status': $('#ist2_head_status_'+i).val(),
							'ist3_a': $('#ist3_head_a_'+i).val(),
							'ist3_b': $('#ist3_head_b_'+i).val(),
							'ist3_c': $ist3_head_c,
							'ist3_status': $('#ist3_head_status_'+i).val(),
							'id_cdm':$('#id_cdm_'+i).val()
						});

						var data = {
							product:$('#product').val(),
							type:$('#type').val(),
							part:$('#part').val(),
							color:$('#color').val(),
							injection_date:$('#injection_date').val(),
							machine:$('#machine').val(),
							machine_injection:$('#machine_injection').val(),
							cavity:$('#cavity').val(),
							employee_id:$('#op').val(),
							head:head,
							save_type:$('#save_type').val(),
						}

						datas.push(data);
					}
				}

				if ($('#type').val() == 'MIDDLE') {
					for (var j = 1; j <= 4; j++) {
						middle.push(
						{
							'cav': $('#cav_middle_'+j).text(),
							'awal_a': $('#awal_middle_a_'+j).val(),
							'awal_b': $('#awal_middle_b_'+j).val(),
							'awal_c': '-',
							'awal_status': $('#awal_middle_status_'+j).val(),
							'ist1_a': $('#ist1_middle_a_'+j).val(),
							'ist1_b': $('#ist1_middle_b_'+j).val(),
							'ist1_c': '-',
							'ist1_status': $('#ist1_middle_status_'+j).val(),
							'ist2_a': $('#ist2_middle_a_'+j).val(),
							'ist2_b': $('#ist2_middle_b_'+j).val(),
							'ist2_c': '-',
							'ist2_status': $('#ist2_middle_status_'+j).val(),
							'ist3_a': $('#ist3_middle_a_'+j).val(),
							'ist3_b': $('#ist3_middle_b_'+j).val(),
							'ist3_c': '-',
							'ist3_status': $('#ist3_middle_status_'+j).val(),
							'id_cdm':$('#id_cdm_'+j).val()
						});

						var data = {
							product:$('#product').val(),
							type:$('#type').val(),
							part:$('#part').val(),
							color:$('#color').val(),
							injection_date:$('#injection_date').val(),
							machine:$('#machine').val(),
							machine_injection:$('#machine_injection').val(),
							cavity:$('#cavity').val(),
							employee_id:$('#op').val(),
							middle:middle,
							save_type:$('#save_type').val()
						}

						datas.push(data);
					}
				}

				if ($('#type').val() == 'FOOT') {
					if ($('#cav_foot_5').text() == "") {
						for (var k = 1; k <= 4; k++) {
							if ($('input[id="awal_foot_c_'+k+'"]:checked').val() == 'OK') {
								$awal_foot_c = 'OK';
							}else if($('input[id="awal_foot_c_'+k+'"]:checked').val() == 'NG'){
								$awal_foot_c = 'NG';
							}else{
								$awal_foot_c = null;
							}

							if ($('input[id="ist1_foot_c_'+k+'"]:checked').val() == 'OK') {
								$ist1_foot_c = 'OK';
							}else if($('input[id="ist1_foot_c_'+k+'"]:checked').val() == 'NG'){
								$ist1_foot_c = 'NG';
							}else{
								$ist1_foot_c = null;
							}

							if ($('input[id="ist2_foot_c_'+k+'"]:checked').val() == 'OK') {
								$ist2_foot_c = 'OK';
							}else if($('input[id="ist2_foot_c_'+k+'"]:checked').val() == 'NG'){
								$ist2_foot_c = 'NG';
							}else{
								$ist2_foot_c = null;
							}

							if ($('input[id="ist3_foot_c_'+k+'"]:checked').val() == 'OK') {
								$ist3_foot_c = 'OK';
							}else if($('input[id="ist3_foot_c_'+k+'"]:checked').val() == 'NG'){
								$ist3_foot_c = 'NG';
							}else{
								$ist3_foot_c = null;
							}

							foot.push(
							{
								'cav': $('#cav_foot_'+k).text(),
								'awal_a': $('#awal_foot_a_'+k).val(),
								'awal_b': $('#awal_foot_b_'+k).val(),
								'awal_c': $awal_foot_c,
								'awal_status': $('#awal_foot_status_'+k).val(),
								'ist1_a': $('#ist1_foot_a_'+k).val(),
								'ist1_b': $('#ist1_foot_b_'+k).val(),
								'ist1_c': $ist1_foot_c,
								'ist1_status': $('#ist1_foot_status_'+k).val(),
								'ist2_a': $('#ist2_foot_a_'+k).val(),
								'ist2_b': $('#ist2_foot_b_'+k).val(),
								'ist2_c': $ist2_foot_c,
								'ist2_status': $('#ist2_foot_status_'+k).val(),
								'ist3_a': $('#ist3_foot_a_'+k).val(),
								'ist3_b': $('#ist3_foot_b_'+k).val(),
								'ist3_c': $ist3_foot_c,
								'ist3_status': $('#ist3_foot_status_'+k).val(),
								'id_cdm':$('#id_cdm_'+k).val()
							});

							var data = {
								product:$('#product').val(),
								type:$('#type').val(),
								part:$('#part').val(),
								color:$('#color').val(),
								injection_date:$('#injection_date').val(),
								machine:$('#machine').val(),
								machine_injection:$('#machine_injection').val(),
								cavity:$('#cavity').val(),
								employee_id:$('#op').val(),
								foot:foot,
								save_type:$('#save_type').val()
							}

							datas.push(data);
						}
					}else{
						for (var k = 1; k <= 6; k++) {
							if ($('input[id="awal_foot_c_'+k+'"]:checked').val() == 'OK') {
								$awal_foot_c = 'OK';
							}else if($('input[id="awal_foot_c_'+k+'"]:checked').val() == 'NG'){
								$awal_foot_c = 'NG';
							}else{
								$awal_foot_c = null;
							}

							if ($('input[id="ist1_foot_c_'+k+'"]:checked').val() == 'OK') {
								$ist1_foot_c = 'OK';
							}else if($('input[id="ist1_foot_c_'+k+'"]:checked').val() == 'NG'){
								$ist1_foot_c = 'NG';
							}else{
								$ist1_foot_c = null;
							}

							if ($('input[id="ist2_foot_c_'+k+'"]:checked').val() == 'OK') {
								$ist2_foot_c = 'OK';
							}else if($('input[id="ist2_foot_c_'+k+'"]:checked').val() == 'NG'){
								$ist2_foot_c = 'NG';
							}else{
								$ist2_foot_c = null;
							}

							if ($('input[id="ist3_foot_c_'+k+'"]:checked').val() == 'OK') {
								$ist3_foot_c = 'OK';
							}else if($('input[id="ist3_foot_c_'+k+'"]:checked').val() == 'NG'){
								$ist3_foot_c = 'NG';
							}else{
								$ist3_foot_c = null;
							}

							foot.push(
							{
								'cav': $('#cav_foot_'+k).text(),
								'awal_a': $('#awal_foot_a_'+k).val(),
								'awal_b': $('#awal_foot_b_'+k).val(),
								'awal_c': $awal_foot_c,
								'awal_status': $('#awal_foot_status_'+k).val(),
								'ist1_a': $('#ist1_foot_a_'+k).val(),
								'ist1_b': $('#ist1_foot_b_'+k).val(),
								'ist1_c': $ist1_foot_c,
								'ist1_status': $('#ist1_foot_status_'+k).val(),
								'ist2_a': $('#ist2_foot_a_'+k).val(),
								'ist2_b': $('#ist2_foot_b_'+k).val(),
								'ist2_c': $ist2_foot_c,
								'ist2_status': $('#ist2_foot_status_'+k).val(),
								'ist3_a': $('#ist3_foot_a_'+k).val(),
								'ist3_b': $('#ist3_foot_b_'+k).val(),
								'ist3_c': $ist3_foot_c,
								'ist3_status': $('#ist3_foot_status_'+k).val(),
								'id_cdm':$('#id_cdm_'+k).val()
							});

							var data = {
								product:$('#product').val(),
								type:$('#type').val(),
								part:$('#part').val(),
								color:$('#color').val(),
								injection_date:$('#injection_date').val(),
								machine:$('#machine').val(),
								machine_injection:$('#machine_injection').val(),
								cavity:$('#cavity').val(),
								employee_id:$('#op').val(),
								foot:foot,
								save_type:$('#save_type').val(),
							}

							datas.push(data);
						}
					}
				}
			}else{
				if ($('#type').val() == 'HEAD') {
					for (var l = 1; l <= 2; l++) {
						head_yrf.push(
						{
							'cav': $('#cav_head_yrf_'+l).text(),
							'awal_a': $('#awal_head_yrf_a_'+l).val(),
							'awal_b': $('#awal_head_yrf_b_'+l).val(),
							'awal_c': '-',
							'awal_status': $('#awal_head_yrf_status_'+l).val(),
							'ist1_a': $('#ist1_head_yrf_a_'+l).val(),
							'ist1_b': $('#ist1_head_yrf_b_'+l).val(),
							'ist1_c': '-',
							'ist1_status': $('#ist1_head_yrf_status_'+l).val(),
							'ist2_a': $('#ist2_head_yrf_a_'+l).val(),
							'ist2_b': $('#ist2_head_yrf_b_'+l).val(),
							'ist2_c': '-',
							'ist2_status': $('#ist2_head_yrf_status_'+l).val(),
							'ist3_a': $('#ist3_head_yrf_a_'+l).val(),
							'ist3_b': $('#ist3_head_yrf_b_'+l).val(),
							'ist3_c': '-',
							'ist3_status': $('#ist3_head_yrf_status_'+l).val(),
							'id_cdm':$('#id_cdm_'+l).val()
						});

						var data = {
							product:$('#product').val(),
							type:$('#type').val(),
							part:$('#part').val(),
							color:$('#color').val(),
							injection_date:$('#injection_date').val(),
							machine:$('#machine').val(),
							machine_injection:$('#machine_injection').val(),
							cavity:$('#cavity').val(),
							employee_id:$('#op').val(),
							head_yrf:head_yrf,
							save_type:$('#save_type').val(),
						}

						datas.push(data);
					}
				}

				if ($('#type').val() == 'BODY') {
					for (var l = 1; l <= 2; l++) {
						body_yrf.push(
						{
							'cav': $('#cav_body_yrf_'+l).text(),
							'awal_a': $('#awal_body_yrf_a_'+l).val(),
							'awal_b': $('#awal_body_yrf_b_'+l).val(),
							'awal_c': '-',
							'awal_status': $('#awal_body_yrf_status_'+l).val(),
							'ist1_a': $('#ist1_body_yrf_a_'+l).val(),
							'ist1_b': $('#ist1_body_yrf_b_'+l).val(),
							'ist1_c': '-',
							'ist1_status': $('#ist1_body_yrf_status_'+l).val(),
							'ist2_a': $('#ist2_body_yrf_a_'+l).val(),
							'ist2_b': $('#ist2_body_yrf_b_'+l).val(),
							'ist2_c': '-',
							'ist2_status': $('#ist2_body_yrf_status_'+l).val(),
							'ist3_a': $('#ist3_body_yrf_a_'+l).val(),
							'ist3_b': $('#ist3_body_yrf_b_'+l).val(),
							'ist3_c': '-',
							'ist3_status': $('#ist3_body_yrf_status_'+l).val(),
							'id_cdm':$('#id_cdm_'+l).val()
						});

						var data = {
							product:$('#product').val(),
							type:$('#type').val(),
							part:$('#part').val(),
							color:$('#color').val(),
							injection_date:$('#injection_date').val(),
							machine:$('#machine').val(),
							machine_injection:$('#machine_injection').val(),
							cavity:$('#cavity').val(),
							employee_id:$('#op').val(),
							body_yrf:body_yrf,
							save_type:$('#save_type').val(),
						}

						datas.push(data);
					}
				}
			}

			var datass = {
				data:data
			}

			// console.log(datas);

			$.post('{{ url("input/recorder/cdm") }}',datass, function(result, status, xhr){
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

	function fetchCavity(no_cavity) {
		if (no_cavity !== "") {
			var product = $('#product').val();
			var data = {
				type:$('#type').val(),
				no_cavity:no_cavity,
			}
			$.get('{{ url("fetch/cavity_detail") }}', data, function(result, status, xhr){
				if(result.status){
					if (product.match(/YRS/gi)) {
						if ($('#type').val() == 'HEAD') {
							$('#cav_head_1').html(result.cavity_1);
							$('#cav_head_2').html(result.cavity_2);
							$('#cav_head_3').html(result.cavity_3);
							$('#cav_head_4').html(result.cavity_4);
						}else if ($('#type').val() == 'MIDDLE') {
							$('#cav_middle_1').html(result.cavity_1);
							$('#cav_middle_2').html(result.cavity_2);
							$('#cav_middle_3').html(result.cavity_3);
							$('#cav_middle_4').html(result.cavity_4);
						}else if ($('#type').val() == 'FOOT') {
							$('#cav_foot_1').html(result.cavity_1);
							$('#cav_foot_2').html(result.cavity_2);
							$('#cav_foot_3').html(result.cavity_3);
							$('#cav_foot_4').html(result.cavity_4);
							$('#cav_foot_5').html(result.cavity_5);
							$('#cav_foot_6').html(result.cavity_6);
						}
					}else{
						if ($('#type').val() == 'HEAD') {
							$('#cav_head_yrf_1').html(result.cavity_1);
							$('#cav_head_yrf_2').html(result.cavity_2);
						}else if ($('#type').val() == 'BODY') {
							$('#cav_body_yrf_1').html(result.cavity_1);
							$('#cav_body_yrf_2').html(result.cavity_2);
						}
					}
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			});
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