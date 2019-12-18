@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<style type="text/css">
	thead>tr>th{
		font-size: 16px;
	}
	/*#tableMachine> tbody > tr > td :hover {
		cursor: pointer;
		background-color: #7dfa8c;
		}*/
	/*#tableShift> tbody > tr > td :hover {
		cursor: pointer;
		background-color: #7dfa8c;
		}*/
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
			{{ $page }} - {{ $head }}
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
				<div class="box" style="padding: 0;">
					<div class="box-body">
						
					</div>
				</div>
			</div>
			<div class="col-xs-7">
				<div class="row">
					<div class="col-xs-6">
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
					</div>
					<div class="col-xs-6">
						<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
							<thead>
								<tr>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Product</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="background-color: #ffd8b7; text-align: center; color: black; font-size:1vw; padding:0;width: 30%;" id="product">{{ $head }}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-xs-1">
								<span style="font-weight: bold; font-size: 15px;">Shift:</span>
							</div>
							<div class="col-xs-3">
								<input type="text" id="shift" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" disabled>
							</div>
							<div class="col-xs-1">
								<span style="font-weight: bold; font-size: 15px;">Machine:</span>
							</div>
							<div class="col-xs-3">
								<input type="text" id="machine" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" disabled>
							</div>
							<div class="col-xs-1">
								<span style="font-weight: bold; font-size: 15px;">Material:</span>
							</div>
							<div class="col-xs-3">
								<input type="text" id="material_number" style="width: 100%; height: 30px; font-size:15px; text-align: center;" disabled>
							</div>
						</div>
					</div>
					<div class="col-md-12" style="padding-top: 5px;">
						<div class="row">
							<div class="col-xs-6">
								<span style="font-weight: bold; font-size: 15px;">Part Name:</span>
								<input type="text" id="part_name" style="width: 70%; height: 30px; font-size: 15px; text-align: center;" disabled>
							</div>
							<div class="col-xs-6">
								<span style="font-weight: bold; font-size: 15px;">Description:</span>
								<input type="text" id="material_description" style="width: 70%; height: 30px; font-size: 15px; text-align: center;" disabled>
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
									<td style=" text-align: center; color: black; font-size12vw; width: 30%;">
										<select class="form-control" style="width: 100%; height: 40px; font-size: 15px; text-align: center;" id="punch" name="punch" data-placeholder="Choose Punch" required>
											<option></option>
										</select>
									</td>
									<td style=" text-align: center; color: black; font-size:1vw; width: 30%;">
										<select class="form-control" style="width: 100%; height: 40px; font-size: 15px; text-align: center;" id="dies" name="dies" data-placeholder="Choose Dies" required>
											<option></option>
										</select>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<button class="btn btn-success" onclick="start()" id="start_button" style="font-size:40px; width: 100%; font-weight: bold; padding: 0;">
					MULAI PROSES
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
					<div class="col-xs-12" style="padding-top: 5px;">
						<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
							<thead>
								<tr>

								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
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
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Lepas Molding</th>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Pasang Molding</th>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Process Time</th>
									<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Electric Supply Time</th>
								</tr>
								<tr>
									<td style=" text-align: center; color: black; font-size:2vw; "><input type="text" id="lepas_molding" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" placeholder="0:00:00"></td>
									<td style="text-align: center; color: black; font-size:2vw; "><input type="text" id="pasang_molding" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" placeholder="0:00:00"></td>
									<td style=" text-align: center; color: black; font-size:2vw; "><input type="text" id="process_time" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" placeholder="0:00:00"></td>
									<td style="text-align: center; color: black; font-size:2vw; "><input type="text" id="electric_time" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" placeholder="0:00:00"></td>
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
					SELESAI PROSES
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
				product : $("#product").text()
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
			var date = $("#date").text();
			var pic = $("#op2").text();
			var product = $("#product").text();
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
			var date = $("#date").text();
			var pic = $("#op2").text();
			var product = $("#product").text();
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
					$('#punch').html(result.punch_data);
					$('#dies').html(result.dies_data);
					$('#countMaterial').val(result.count.quantity_check);
					$('#addCount').val("0");
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
			window.location = "{{ url('index/press/create/'.$head) }}";
		}

		function end(){
			$("#end_time").html(getActualFullDate());

			var date = $("#date").text();
			var pic = $("#op2").text();
			var product = $("#product").text();
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

			if(process == '' || machine == '' || data_ok == ''){
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
					machine : machine,
					shift : shift,
					material_number : material_number,
					process : process,
					punch_number : punch_number,
					die_number : die_number,
					start_time : start_time,
					end_time : end_time,
					punch_value : punch_value,
					die_value : die_value,
				}
				console.log(data2);
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