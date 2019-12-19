@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
	<style type="text/css">
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
		input[type="radio"] {
		}

		#loading { display: none; }


		.radio {
			display: inline-block;
			position: relative;
			padding-left: 35px;
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
			background-color: #eee;
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
	@stop
	@section('header')
	<section class="content-header">
		<h1>
			{{ $page }}
			<span class="text-purple">{{ $title_jp }}</span>
			<button type="button" class="btn btn-danger pull-right" data-toggle="modal" data-target="#trouble-modal" onclick="troubleMaker()">
				<b>TROUBLE</b>
			</button>
		</h1>
	</section>
	@stop
	@section('content')
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<section class="content">
		<input type="hidden" id="data" value="data">
		<div class="row">
			<div class="col-xs-5">
				<div style="padding: 0;">
					<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
						<tbody>
							<tr>
								<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;" colspan="10">Operator</th>
							</tr>
							<tr>
								<td style="background-color: #6e81ff; text-align: center; color: black; font-size:1vw; padding:0;width: 30%;" colspan="3" id="op">-</td>
								<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; padding:0;font-size: 1vw;" colspan="7" id="op2">-</td>
							</tr>
							<tr>
								<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;" colspan="3" width="50%">Shift List</th>
								<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;" colspan="7" width="50%">Machine List (Amada)</th>
							</tr>
							<tr>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getDataShift('Shift 1')">
									<center>
										<p class="btn btn-success" style="font-size: 1vw;">1</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getDataShift('Shift 2')">
									<center>
										<p class="btn btn-success" style="font-size: 1vw;">2</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getDataShift('Shift 3')">
									<center>
										<p class="btn btn-success" style="font-size: 1vw;">3</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Amada 1')">
									<center>
										<p class="btn btn-primary" style="font-size: 1vw;">#1</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Amada 2')">
									<center>
										<p class="btn btn-primary" style="font-size: 1vw;">#2</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Amada 3')">
									<center>
										<p class="btn btn-primary" style="font-size: 1vw;">#3</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Amada 4')">
									<center>
										<p class="btn btn-primary" style="font-size: 1vw;">#4</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Amada 5')">
									<center>
										<p class="btn btn-primary" style="font-size: 1vw;">#5</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Amada 6')">
									<center>
										<p class="btn btn-primary" style="font-size: 1vw;">#6</p>
									</center>
								</td>
								<td style="padding-left: 0;padding-right: 0;padding-bottom: 0;padding-top: 0;" onclick="getData('Amada 7')">
									<center>
										<p class="btn btn-primary" style="font-size: 1vw;">#7</p>
									</center>
								</td>
							</tr>
							<tr>
								<td style="text-align: center; color: yellow; font-size:1vw; width: 30%;" id="process_desc_select" colspan="10">
									<select class="form-control" style="width: 100%; height: 40px; font-size: 15px; text-align: center;" id="process_desc" name="process_desc" data-placeholder="Choose Process Desc" required>
										<option></option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="box" style="padding: 0;">
					<div class="box-body">
						<span style="font-size: 15px; font-weight: bold;">ITEM LIST:</span>
						<table class="table table-hover table-striped" id="tableList">
							<thead>
								<tr>
									<th style="width: 1%;">#</th>
									<th style="width: 2%;">Material Number</th>
									<th style="width: 5%;">Part Name</th>
									<th style="width: 5%;">Description</th>
								</tr>					
							</thead>
							<tbody id="tableBodyList">
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-xs-7">
				<div class="row">
					{{-- <div class="col-xs-12">
						<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
							<thead>
								<tr>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Date</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="background-color: #fffcb7; text-align: center; color: black; font-size:1vw; padding:0; width: 30%;" id="date">{{ date('Y-m-d') }}</td>
								</tr>
							</tbody>
						</table>
					</div> --}}
					{{-- <div class="col-xs-6">
						<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
							<thead>
								<tr>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Product</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="background-color: #ffd8b7; text-align: center; color: black; font-size:1vw; padding:0;width: 30%;" id="product"></td>
								</tr>
							</tbody>
						</table>
					</div> --}}
				</div>
				<div class="row">
					<div class="col-md-12" style="padding-top: 5px;">
						<div class="row">
							<div class="col-xs-2">
								<span style="font-weight: bold; font-size: 15px;">Date:</span>
							</div>
							<div class="col-xs-4">
								<input type="text" id="date" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" disabled value="{{ date('Y-m-d') }}">
							</div>
							<div class="col-xs-2">
								<span style="font-weight: bold; font-size: 15px;">Product:</span>
							</div>
							<div class="col-xs-4">
								<input type="text" id="product" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" disabled>
							</div>
						</div>						
					</div>
					<div class="col-md-12" style="padding-top: 5px;">
						<div class="row">
							<div class="col-xs-2">
								<span style="font-weight: bold; font-size: 15px;">Shift:</span>
							</div>
							<div class="col-xs-4">
								<input type="text" id="shift" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" disabled>
							</div>
							<div class="col-xs-2">
								<span style="font-weight: bold; font-size: 15px;">Material:</span>
							</div>
							<div class="col-xs-4">
								<input type="text" id="material_number" style="width: 100%; height: 30px; font-size:15px; text-align: center;" disabled>
							</div>
						</div>
					</div>
					<div class="col-md-12" style="padding-top: 5px;">
						<div class="row">
							<div class="col-xs-2">
								<span style="font-weight: bold; font-size: 15px;">Machine:</span>
							</div>
							<div class="col-xs-4">
								<input type="text" id="machine" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" disabled>
							</div>
							<div class="col-xs-2">
								<span style="font-weight: bold; font-size: 15px;">Part:</span>
							</div>
							<div class="col-xs-4">
								<input type="text" id="part_name" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" disabled>
							</div>
						</div>						
					</div>
					
					<div class="col-md-12" style="padding-top: 5px;">
						<div class="row">
							<div class="col-xs-2">
								{{-- <span style="font-weight: bold; font-size: 15px;">Part:</span> --}}
							</div>
							<div class="col-xs-4">
								{{-- <input type="text" id="part_name" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" disabled> --}}
							</div>
							<div class="col-xs-2">
								<span style="font-weight: bold; font-size: 15px;">Desc:</span>
							</div>
							<div class="col-xs-4">
								<input type="text" id="material_description" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" disabled>
							</div>
						</div>						
					</div>
					<div class="col-xs-12" style="padding-top: 5px;">
						<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
							<thead>
								<tr>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">PUNCH</th>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">DIES</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style=" text-align: center; color: black; font-size:1vw; width: 30%;">
										<select class="form-control" style="width: 100%; height: 40px; font-size: 15px; text-align: center;" id="punch" name="punch" data-placeholder="Choose Punch" required onchange="fetchTotalPunch(this.value)">
											<option></option>
										</select>
									</td>
									<td style=" text-align: center; color: black; font-size:1vw; width: 30%;">
										<select class="form-control" style="width: 100%; height: 40px; font-size: 15px; text-align: center;" id="dies" name="dies" data-placeholder="Choose Dies" required onchange="fetchTotalDie(this.value)">
											<option></option>
										</select>
									</td>
								</tr>
								<tr>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">RUNNING PUNCH</th>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">RUNNING DIES</th>
								</tr>
								<tr>
									<td style=" text-align: center; color: black; font-size:1vw; width: 30%;">
										<input type="text" id="punch_total" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" disabled>
									</td>
									<td style=" text-align: center; color: black; font-size:1vw; width: 30%;">
										<input type="text" id="die_total" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" disabled>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<button class="btn btn-success" onclick="start()" id="start_button" style="font-size:35px; width: 100%; font-weight: bold; padding: 0;">
					TEKAN UNTUK MULAI PROSES
				</button>			
				<div class="row" id="processtime_picker">
					<div class="col-xs-6">
						<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
							<thead>
								<tr>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Start Time</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="background-color: #fffcb7; text-align: center; color: black; font-size:1vw; width: 30%;" id="start_time"></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="col-xs-6">
						<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
							<thead>
								<tr>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">End Time</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="background-color: #ffd8b7; text-align: center; color: black; font-size:1vw; width: 30%;" id="end_time"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row" id="downtime_picker">
					<div class="col-xs-12">
						<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
							<thead>
								<tr>
									<th style="width:15%; background-color: #6e81ff; text-align: center; color: black; padding:0;font-size: 15px;" colspan="2">SETUP MOLDING</th>
									<th style="width:15%; background-color: #6e81ff; text-align: center; color: black; padding:0;font-size: 15px;" colspan="2">PRODUCTION TIME</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Pasang Molding</th>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Lepas Molding</th>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Process Time</th>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Electric Supply Time</th>
								</tr>
								<tr>
									<td style="text-align: center; color: black; font-size:2vw; ">
							        <button class="btn btn-sm btn-success" id="startpasmod" onClick="timerpasmod.start(1000)">Start</button> 
							        <button class="btn btn-sm btn-danger" id="stoppasmod" onClick="timerpasmod.stop()">Stop</button>
									<div class="timerpasmod">
							            <span class="hourpasmod">00</span>:<span class="minutepasmod">00</span>:<span class="secondpasmod">10</span>
							        </div>
							        <input type="hidden" id="pasang_molding" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" placeholder="0:00:00" required>
							    	</td>
									<td style=" text-align: center; color: black; font-size:2vw; ">
									<button class="btn btn-sm btn-success" id="startlepmod" onClick="timerlepmod.start(1000)">Start</button> 
							        <button class="btn btn-sm btn-danger" id="stoplepmod" onClick="timerlepmod.stop()">Stop</button>
									<div class="timerlepmod">
							            <span class="hourlepmod">00</span>:<span class="minutelepmod">00</span>:<span class="secondlepmod">10</span>
							        </div>
									<input type="hidden" id="lepas_molding" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" placeholder="0:00:00" required>
									</td>
									<td style=" text-align: center; color: black; font-size:2vw; ">
									<input type="text" id="process_time" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" placeholder="0:00:00" required></td>
									<td style="text-align: center; color: black; font-size:2vw; ">
									{{-- <button class="btn btn-sm btn-success" id="startelectime" onClick="timerelectime.start(1000)">Start</button>  --}}
							        <button class="btn btn-sm btn-danger" id="stopelectime" onClick="timerelectime.stop()">Stop</button>
									<div class="timerelectime">
							            <span class="hourelectime">00</span>:<span class="minuteelectime">00</span>:<span class="secondelectime">10</span>
							        </div>
									<input type="hidden" id="electric_time" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" placeholder="0:00:00" required></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row" id="production_data">
					<div class="col-xs-12">
						<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
							<thead>
								<tr>
									<th style="width:15%; background-color: #6e81ff; text-align: center; color: black; padding:0;font-size: 15px;" colspan="3">PRODUCTION DATA</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Actual Shot</th>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Punch</th>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Dies</th>
								</tr>
								<tr>
									<td style=" text-align: center; color: black; font-size:2vw; "><input type="number" id="data_ok" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" placeholder="Actual Shot" onkeyup="dataOkKeyUp()"></td>
									<td style="text-align: center; color: black; font-size:2vw; "><input type="number" id="jumlah_punch" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" placeholder="Jumlah Punch" disabled></td>
									<td style="text-align: center; color: black; font-size:2vw; "><input type="number" id="jumlah_dies" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" placeholder="Jumlah Dies" disabled></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<button class="btn btn-danger" onclick="end()" id="end_button" style="font-size:40px; width: 100%; font-weight: bold; padding: 0;">
					TEKAN UNTUK SELESAI PROSES
				</button>
			</div>
		</div>
		<div class="modal fade" id="modalOperator">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-header">
						<div class="modal-body table-responsive no-padding">
							<div class="form-group">
								<label for="exampleInputEmail1">Employee ID</label>
								<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal fade modal-danget" id="trouble-modal">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title" align="center"><b>Create Trouble Data</b></h4>
					</div>
					<div class="modal-body">
						<div class="box-body">
							<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
								<div class="form-group">
									<label for="">Trouble Start Time</label>
									<input type="text" class="form-control" name="trouble_start" id="trouble_start" placeholder="Enter Leader" readonly>
								</div>
							</div>
							<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
								<div class="form-group">
									<label for="">Reason</label>
									<textarea name="reason" id="reason" class="form-control" rows="2" required="required"></textarea>
								</div>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<span style="font-size: 15px; font-weight: bold;">TROUBLE HISTORY:</span>
								<table class="table table-hover table-striped" id="tableTrouble">
									<thead>
										<tr>
											<th style="width: 1%;">#</th>
											<th style="width: 2%;">Start Time</th>
											<th style="width: 5%;">Reason</th>
											<th style="width: 2%;">End Time</th>
											<th style="width: 2%;">Action</th>
										</tr>					
									</thead>
									<tbody id="tableBodyTrouble">
									</tbody>
								</table>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
								<div class="modal-footer">
									<button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
									<input type="submit" value="Create" onclick="createTrouble()" class="btn btn-primary">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	@endsection
	@section('scripts')
	<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
	<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
	<script src="{{ url("js/buttons.flash.min.js")}}"></script>
	<script src="{{ url("js/jszip.min.js")}}"></script>
	<script src="{{ url("js/vfs_fonts.js")}}"></script>
	<script src="{{ url("js/buttons.html5.min.js")}}"></script>
	<script src="{{ url("js/buttons.print.min.js")}}"></script>
	<script>
		$('#injection_date').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		jQuery(document).ready(function() {
			$('body').toggleClass("sidebar-collapse");
			$('.select2').select2({
				language : {
					noResults : function(params) {
						return "There is no data";
					}
				}
			});
		});

		function dataOkKeyUp() {
			var x = document.getElementById("data_ok").value;
			document.getElementById("jumlah_punch").value = x;
			document.getElementById("jumlah_dies").value = x;
		}

		function getData(nama_mesin){
			$("#machine").val(nama_mesin);
		}

		function getDataShift(shift){
			$("#shift").val(shift);
		}

		function troubleMaker(){
			$("#trouble_start").val(getActualFullDate());
			troubleList();
		}

		jQuery(document).ready(function() {
			$('#modalOperator').modal({
				backdrop: 'static',
				keyboard: false
			});
			$('#operator').val('');
			$('#tag').val('');

			$("#downtime_picker").hide();
			$("#processtime_picker").hide();
			$("#productiontime_picker").hide();
			$("#production_data").hide();
			$("#end_button").hide();
			$("#reset_button").hide();
			$("#process_desc_select").hide();

			$('#stoppasmod').hide();
			$('#stoplepmod').hide();
			// $('#stopproctime').hide();
			$('#stopelectime').hide();

			$('.timepicker').timepicker({
				minuteStep: 1,
				template: 'modal',
				appendWidgetTo: 'body',
				showSeconds: true,
				showMeridian: false,
				defaultTime: false
			});
			itemList();
		});

		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').focus();
		});

		$('#operator').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#operator").val().length >= 8){
					var data = {
						employee_id : $("#operator").val()
					}

					$.get('{{ url("scan/press/operator") }}', data, function(result, status, xhr){
						if(result.status){
							openSuccessGritter('Success!', result.message);
							$('#modalOperator').modal('hide');
							$('#op').html(result.employee.employee_id);
							$('#op2').html(result.employee.name);
							$('#employee_id').val(result.employee.employee_id);
							$('#tag').focus();
						}
						else{
							audio_error.play();
							openErrorGritter('Error', result.message);
							$('#operator').val('');
						}
					});
				}
				else{
					openErrorGritter('Error!', 'Employee ID Invalid.');
					audio_error.play();
					$("#operator").val("");
				}
			}
		});

		function itemList(){
			var data = {
				process : 'Forging',
				product : $("#product").val()
			}

			$.get('{{ url("fetch/press/fetchProcess") }}', data, function(result, status, xhr){
				if(result.status){
					$("#process_desc_select").show();
					$('#process_desc').html(result.process_desc);
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			});

			$.get('{{ url("fetch/press/press_list") }}', data, function(result, status, xhr){
				if(result.status){
					$('#tableList').DataTable().clear();
					$('#tableList').DataTable().destroy();
					$('#tableBodyList').html("");
					var tableData = "";
					var count = 1;
					$.each(result.lists, function(key, value) {
						tableData += '<tr onclick="fetchCount(\''+value.id+'\')">';
						tableData += '<td>'+ count +'</td>';
						tableData += '<td>'+ value.material_number +'</td>';
						tableData += '<td>'+ value.material_name +'</td>';
						tableData += '<td>'+ value.material_description +'</td>';
						tableData += '</tr>';
						count += 1;
					});
					$('#tableBodyList').append(tableData);
					$('#tableList').DataTable({
						'dom': 'Bfrtip',
						'responsive':true,
						'lengthMenu': [
						[ 5, 10, 25, -1 ],
						[ '5 rows', '10 rows', '25 rows', 'Show all' ]
						],
						'buttons': {
							buttons:[
							{
								extend: 'pageLength',
								className: 'btn btn-default',
							},
							
							]
						},
						'paging': true,
						'lengthChange': true,
						'pageLength': 5,
						'searching': true,
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
					alert('Attempt to retrieve data failed');
				}
			});
		}

		function createTrouble(){
			var date = $("#date").val();
			var pic = $("#op").text();
			var product = $("#product").val();
			var machine = $("#machine").val();
			var shift = $("#shift").val();
			var material_number = $("#material_number").val();
			var process = $("#process_desc").val();
			var trouble_start = $("#trouble_start").val();
			var reason = $("#reason").val();

			var data = {
				date : date,
				pic : pic,
				product : product,
				machine : machine,
				shift : shift,
				material_number : material_number,
				process : process,
				start_time : trouble_start,
				reason : reason,
			}

			$.post('{{ url("index/press/store_trouble") }}', data, function(result, status, xhr){
				if(result.status){
					// $("#trouble-modal").modal('hide');
					openSuccessGritter('Success','New Trouble Data has been created');
					$("#reason").val('');
					troubleList();
				} else {
					audio_error.play();
					openErrorGritter('Error','Create Trouble Data Failed');
				}
			});
		}

		function troubleList(){
			var date = $("#date").val();
			var pic = $("#op").text();
			var product = $("#product").val();
			var machine = $("#machine").val();
			var material_number = $("#material_number").val();
			var process = $("#process_desc").val();
			var data = {
				date : date,
				pic : pic,
				product : product,
				machine : machine,
				material_number : material_number,
				process : process
			}
			$.get('{{ url("fetch/press/trouble_list") }}', data, function(result, status, xhr){
				if(result.status){
					var tableData = "";
					$('#tableBodyTrouble').html("");
					var count = 1;
					$.each(result.lists, function(key, value) {
						tableData += '<tr>';
						tableData += '<td>'+ count +'</td>';
						tableData += '<td>'+ value.start_time +'</td>';
						tableData += '<td>'+ value.reason +'</td>';
						tableData += '<td>'+ value.end_time +'</td>';
						if(value.end_time == null){
							tableData += '<td><button type="button" class="btn btn-danger pull-right" onclick="finishTrouble('+ value.id +')"><b>FINISH</b></button></td>';
						}else{
							tableData += '<td></td>';
						}
						tableData += '</tr>';

						count += 1;
					});
					$('#tableBodyTrouble').append(tableData);
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			});
		}

		function finishTrouble(id){

			var data = {
				id : id
			}

			$.post('{{ url("index/press/finish_trouble") }}', data, function(result, status, xhr){
				if(result.status){
					// $("#trouble-modal").modal('hide');
					openSuccessGritter('Success','The Trouble has been finished');
					troubleList();
				} else {
					audio_error.play();
					openErrorGritter('Error','Create Trouble Data Failed');
				}
			});
		}

		function fetchCount(id){
			var data = {
				id : id,
			}
			$.get('{{ url("fetch/press/fetchMaterialList") }}', data, function(result, status, xhr){
				if(result.status){
					$('#id_silver').val(result.count.id);
					$('#material_number').val(result.count.material_number);
					$('#material_description').val(result.count.material_description);
					$('#part_name').val(result.count.material_name);
					$('#product').val(result.count.product);
					$('#punch').html(result.punch_data);
					$('#dies').html(result.dies_data);
					$('#countMaterial').val(result.count.quantity_check);
					fetchTotalPunch(result.punch_first.punch_die_number);
					fetchTotalDie(result.dies_first.punch_die_number);
					$('#addCount').val("0");
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			});
		}

		function fetchTotalPunch(punch_number){
			var material_number = $("#material_number").val();
			var process = $("#process_desc").val();
			var data = {
				material_number : material_number,
				process : process,
				punch_number : punch_number,
			}
			$.get('{{ url("fetch/press/fetchPunch") }}', data, function(result, status, xhr){
				if(result.status){
					$('#punch_total').val(result.total_punch);
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			});
		}

		function fetchTotalDie(die_number){
			var material_number = $("#material_number").val();
			var process = $("#process_desc").val();
			var data = {
				material_number : material_number,
				process : process,
				die_number : die_number,
			}
			$.get('{{ url("fetch/press/fetchDie") }}', data, function(result, status, xhr){
				if(result.status){
					$('#die_total').val(result.total_die);
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			});
		}

		function addZero(i) {
			if (i < 10) {
				i = "0" + i;
			}
			return i;
		}

		function getActualFullDate() {
			var d = new Date();
			var day = addZero(d.getDate());
			var month = addZero(d.getMonth()+1);
			var year = addZero(d.getFullYear());
			var h = addZero(d.getHours());
			var m = addZero(d.getMinutes());
			var s = addZero(d.getSeconds());
			return year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
		}

		function start(){
			$("#start_time").html(getActualFullDate());
			$("#start_button").hide();
			$("#end_button").show();
			$("#downtime_picker").show();
			$("#processtime_picker").show();
			$("#productiontime_picker").show();
			$("#production_data").show();
		}

		function reset(){
			window.location = "{{ url('index/press/create') }}";
		}

		function end(){
			$("#end_time").html(getActualFullDate());

			var date = $("#date").val();
			var pic = $("#op").text();
			var product = $("#product").val();
			var machine = $("#machine").val();
			var shift = $("#shift").val();
			var material_number = $("#material_number").val();
			var process = $("#process_desc").val();
			var punch_number = $("#punch").val();
			var die_number = $("#dies").val();
			var start_time = $("#start_time").text();
			var end_time = $("#end_time").text();
			var lepas_molding = $("#lepas_molding").val();
			var pasang_molding = $("#pasang_molding").val();
			var process_time = $("#process_time").val();
			var electric_supply_time = $("#electric_time").val();
			var data_ok = $("#data_ok").val();
			var punch_value = $("#jumlah_punch").val();
			var die_value = $("#jumlah_dies").val();

			if(process == '' || machine == '' || data_ok == '' || lepas_molding == '' || pasang_molding == '' || process_time == '' || electric_supply_time == ''){
				alert("Semua Data Harus Diisi.");
			}
			else{
				var data = {
					date : date,
					pic : pic,
					product : product,
					machine : machine,
					shift : shift,
					material_number : material_number,
					process : process,
					punch_number : punch_number,
					die_number : die_number,
					start_time : start_time,
					end_time : end_time,
					lepas_molding : lepas_molding,
					pasang_molding : pasang_molding,
					process_time : process_time,
					electric_supply_time : electric_supply_time,
					data_ok : data_ok,
					punch_value : punch_value,
					die_value : die_value,
				}
				var data2 = {
					date : date,
					pic : pic,
					shift : shift,
					product : product,
					machine : machine,
					material_number : material_number,
					process : process,
					punch_number : punch_number,
					die_number : die_number,
					start_time : start_time,
					end_time : end_time,
					punch_value : punch_value,
					die_value : die_value,
				}
				console.log(data);
				// $("#end_button").hide();
				// $("#reset_button").show();

				$.post('{{ url("index/press/store") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success','New Production Record has been created');
						reset();
					} else {
						audio_error.play();
						openErrorGritter('Error','Create Production Record Failed');
					}
				});
			}
		}

		function _timerpasmod(callback)
		{
		    var time = 0;     //  The default time of the timer
		    var mode = 1;     //    Mode: count up or count down
		    var status = 0;    //    Status: timer is running or stoped
		    var timer_id;
		    var hour;
		    var minute;
		    var second;    //    This is used by setInterval function
		    
		    // this will start the timer ex. start the timer with 1 second interval timer.start(1000) 
		    this.start = function(interval)
		    {
		    	$('#startpasmod').hide();
				$('#stoppasmod').show();
				timerelectime.start(1000);
		        interval = (typeof(interval) !== 'undefined') ? interval : 1000;
		 
		        if(status == 0)
		        {
		            status = 1;
		            timer_id = setInterval(function()
		            {
		                switch(1)
		                {
		                    default:
		                    if(time)
		                    {
		                        time--;
		                        generateTime();
		                        if(typeof(callback) === 'function') callback(time);
		                    }
		                    break;
		                    
		                    case 1:
		                    if(time < 86400)
		                    {
		                        time++;
		                        generateTime();
		                        if(typeof(callback) === 'function') callback(time);
		                    }
		                    break;
		                }
		            }, interval);
		        }
		    }
		    
		    //  Same as the name, this will stop or pause the timer ex. timer.stop()
		    this.stop =  function()
		    {
		        if(status == 1)
		        {
		            status = 0;
		            var detik = $('div.timerpasmod span.secondpasmod').text();
			        var menit = $('div.timerpasmod span.minutepasmod').text();
			        var jam = $('div.timerpasmod span.hourpasmod').text();
			        var waktu = jam + ':' + menit + ':' + detik;
			        $('#pasang_molding').val(waktu);
			        $('#stoppasmod').hide();
		            clearInterval(timer_id);
		        }
		    }
		    
		    // Reset the timer to zero or reset it to your own custom time ex. reset to zero second timer.reset(0)
		    this.reset =  function(sec)
		    {
		        sec = (typeof(sec) !== 'undefined') ? sec : 0;
		        time = sec;
		        generateTime(time);
		    }
		    this.getTime = function()
		    {
		        return time;
		    }
		    this.getMode = function()
		    {
		        return mode;
		    }
		    this.getStatus
		    {
		        return status;
		    }
		    function generateTime()
		    {
		        second = time % 60;
		        minute = Math.floor(time / 60) % 60;
		        hour = Math.floor(time / 3600) % 60;
		        
		        second = (second < 10) ? '0'+second : second;
		        minute = (minute < 10) ? '0'+minute : minute;
		        hour = (hour < 10) ? '0'+hour : hour;
		        
		        $('div.timerpasmod span.secondpasmod').html(second);
		        $('div.timerpasmod span.minutepasmod').html(minute);
		        $('div.timerpasmod span.hourpasmod').html(hour);
		    }
		}
		 
		var timerpasmod;
		$(document).ready(function(e) 
		{
		    timerpasmod = new _timerpasmod
		    (
		        function(time)
		        {
		            if(time == 0)
		            {
		                timerpasmod.stop();
		                alert('time out');
		            }
		        }
		    );
		    timerpasmod.reset(0);
		});

		function _timerlepmod(callback)
		{
		    var time = 0;     //  The default time of the timer
		    var mode = 1;     //    Mode: count up or count down
		    var status = 0;    //    Status: timer is running or stoped
		    var timer_id;
		    var hour;
		    var minute;
		    var second;    //    This is used by setInterval function
		    
		    // this will start the timer ex. start the timer with 1 second interval timer.start(1000) 
		    this.start = function(interval)
		    {
		    	$('#startlepmod').hide();
		    	$('#stoplepmod').show();
		        interval = (typeof(interval) !== 'undefined') ? interval : 1000;
		 
		        if(status == 0)
		        {
		            status = 1;
		            timer_id = setInterval(function()
		            {
		                switch(1)
		                {
		                    default:
		                    if(time)
		                    {
		                        time--;
		                        generateTime();
		                        if(typeof(callback) === 'function') callback(time);
		                    }
		                    break;
		                    
		                    case 1:
		                    if(time < 86400)
		                    {
		                        time++;
		                        generateTime();
		                        if(typeof(callback) === 'function') callback(time);
		                    }
		                    break;
		                }
		            }, interval);
		        }
		    }
		    
		    //  Same as the name, this will stop or pause the timer ex. timer.stop()
		    this.stop =  function()
		    {
		        if(status == 1)
		        {
		            status = 0;
		            var detik = $('div.timerlepmod span.secondlepmod').text();
			        var menit = $('div.timerlepmod span.minutelepmod').text();
			        var jam = $('div.timerlepmod span.hourlepmod').text();
			        var waktu = jam + ':' + menit + ':' + detik;
			        $('#stoplepmod').hide();
			        $('#lepas_molding').val(waktu);
		            clearInterval(timer_id);
		        }
		    }
		    
		    // Reset the timer to zero or reset it to your own custom time ex. reset to zero second timer.reset(0)
		    this.reset =  function(sec)
		    {
		        sec = (typeof(sec) !== 'undefined') ? sec : 0;
		        time = sec;
		        generateTime(time);
		    }
		    this.getTime = function()
		    {
		        return time;
		    }
		    this.getMode = function()
		    {
		        return mode;
		    }
		    this.getStatus
		    {
		        return status;
		    }
		    function generateTime()
		    {
		        second = time % 60;
		        minute = Math.floor(time / 60) % 60;
		        hour = Math.floor(time / 3600) % 60;
		        
		        second = (second < 10) ? '0'+second : second;
		        minute = (minute < 10) ? '0'+minute : minute;
		        hour = (hour < 10) ? '0'+hour : hour;
		        
		        $('div.timerlepmod span.secondlepmod').html(second);
		        $('div.timerlepmod span.minutelepmod').html(minute);
		        $('div.timerlepmod span.hourlepmod').html(hour);
		    }
		}
		 
		var timerlepmod;
		$(document).ready(function(e) 
		{
		    timerlepmod = new _timerlepmod
		    (
		        function(time)
		        {
		            if(time == 0)
		            {
		                timerlepmod.stop();
		                alert('time out');
		            }
		        }
		    );
		    timerlepmod.reset(0);
		});

		function _timerelectime(callback)
		{
		    var time = 0;     //  The default time of the timer
		    var mode = 1;     //    Mode: count up or count down
		    var status = 0;    //    Status: timer is running or stoped
		    var timer_id;
		    var hour;
		    var minute;
		    var second;    //    This is used by setInterval function
		    
		    // this will start the timer ex. start the timer with 1 second interval timer.start(1000) 
		    this.start = function(interval)
		    {
		    	$('#startelectime').hide();
				$('#stopelectime').show();
		        interval = (typeof(interval) !== 'undefined') ? interval : 1000;
		 
		        if(status == 0)
		        {
		            status = 1;
		            timer_id = setInterval(function()
		            {
		                switch(1)
		                {
		                    default:
		                    if(time)
		                    {
		                        time--;
		                        generateTime();
		                        if(typeof(callback) === 'function') callback(time);
		                    }
		                    break;
		                    
		                    case 1:
		                    if(time < 86400)
		                    {
		                        time++;
		                        generateTime();
		                        if(typeof(callback) === 'function') callback(time);
		                    }
		                    break;
		                }
		            }, interval);
		        }
		    }
		    
		    //  Same as the name, this will stop or pause the timer ex. timer.stop()
		    this.stop =  function()
		    {
		        if(status == 1)
		        {
		            status = 0;
		            var detik = $('div.timerelectime span.secondelectime').text();
			        var menit = $('div.timerelectime span.minuteelectime').text();
			        var jam = $('div.timerelectime span.hourelectime').text();
			        var waktu = jam + ':' + menit + ':' + detik;
			        $('#electric_time').val(waktu);
			        $('#stopelectime').hide();
		            clearInterval(timer_id);
		        }
		    }
		    
		    // Reset the timer to zero or reset it to your own custom time ex. reset to zero second timer.reset(0)
		    this.reset =  function(sec)
		    {
		        sec = (typeof(sec) !== 'undefined') ? sec : 0;
		        time = sec;
		        generateTime(time);
		    }
		    this.getTime = function()
		    {
		        return time;
		    }
		    this.getMode = function()
		    {
		        return mode;
		    }
		    this.getStatus
		    {
		        return status;
		    }
		    function generateTime()
		    {
		        second = time % 60;
		        minute = Math.floor(time / 60) % 60;
		        hour = Math.floor(time / 3600) % 60;
		        
		        second = (second < 10) ? '0'+second : second;
		        minute = (minute < 10) ? '0'+minute : minute;
		        hour = (hour < 10) ? '0'+hour : hour;
		        
		        $('div.timerelectime span.secondelectime').html(second);
		        $('div.timerelectime span.minuteelectime').html(minute);
		        $('div.timerelectime span.hourelectime').html(hour);
		    }
		}
		 
		var timerelectime;
		$(document).ready(function(e) 
		{
		    timerelectime = new _timerelectime
		    (
		        function(time)
		        {
		            if(time == 0)
		            {
		                timerelectime.stop();
		                alert('time out');
		            }
		        }
		    );
		    timerelectime.reset(0);
		});

		function openSuccessGritter(title, message){
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-success',
				image: '{{ url("images/image-screen.png") }}',
				sticky: false,
				time: '2000'
			});
		}

		function openErrorGritter(title, message) {
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-danger',
				image: '{{ url("images/image-stop.png") }}',
				sticky: false,
				time: '2000'
			});
		}

	</script>
	@endsection