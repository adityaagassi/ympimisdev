@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	body{
		padding-right: 0px !important;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	td{
		text-overflow: ellipsis;
	}
	table {
		/*table-layout:fixed;*/
		text-align: center;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
		/*margin-top:20px;*/
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px">
	<input type="hidden" id="data" value="data">
	<div class="row" style="padding-top: 0px">
		<div class="col-md-6" style="padding-right: 20px;border-right: 2px solid red">
			<div class="box box-solid" style="background-color: #f0f0f0">
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-12">
									<table class="table table-striped">
										<tr>
											<td colspan="3" style="font-size: 1.5vw; font-weight: bold;background-color: #8bc34a;border: 1px solid black;color: black">
												CAMERA MIDDLE CHECK
											</td>
										</tr>
										<tr>
											<td style="width:2%;background-color: #7ecb20;font-size: 1.5vw;text-align: center;color: #000;font-weight:bold;padding-top: 0px;padding-bottom: 0px;border-top: #9ccc65;border: 1px solid black" id="op">
												-
											</td>
											<td style="width:30%;background-color: #9ccc65;font-size: 1.5vw;text-align: center;color: #000;font-weight:bold;padding-top: 0px;padding-bottom: 0px;border-top: #9ccc65;border: 1px solid black" id="op2">
												-
											</td>
										</tr>
									</table>
								</div>
								<div class="col-xs-6">
									<!-- <table class="table table-striped">
										<tr>
											<td style="font-size: 1.5vw; font-weight: bold;background-color: #8bc34a;border: 1px solid black;color: black">
												PEROLEHAN
											</td>
											<td style="width:50%;background-color: #7ecb20;font-size: 1.5vw;text-align: center;color: #000;font-weight:bold;padding-top: 0px;padding-bottom: 0px;border-top: #9ccc65;border: 1px solid black;vertical-align: middle;" id="perolehan">
											</td>
										</tr>
									</table> -->
									<span style="font-size: 20px; font-weight: bold;"><center>Judgement Middle:</center></span>
									<input type="text" name="judgement_middle" id="judgement_middle" class="form-control" value="OK" required="required" pattern="" title="" style="width: 100%;height: 200px;font-size: 10vw;text-align: center;font-weight:bold;background-color: #57ff86;color: #163756;border: 1px solid black" disabled>
								</div>
								<div class="col-xs-6">
									<span style="font-size: 20px; font-weight: bold;"><center>Perolehan:</center></span>
									<input type="text" name="perolehan" id="perolehan" class="form-control" value="0" required="required" pattern="" title="" style="width: 100%;height: 200px;font-size: 7vw;text-align: center;font-weight:bold;background-color: #57ff86;color: #163756;border: 1px solid black" disabled>
								</div>
								<!-- <div class="col-xs-6">
									<span style="font-size: 20px; font-weight: bold;"><center>Judgement Stamp:</center></span>
									<input type="text" name="judgement_stamp" id="judgement_stamp" class="form-control" value="OK" required="required" pattern="" title="" style="width: 100%;height: 200px;font-size: 10vw;text-align: center;font-weight:bold;background-color: #57ff86;color: #163756;border: 1px solid black" disabled>
								</div> -->
							</div>
							<span style="font-size: 20px; font-weight: bold;"><center>COLOR :</center></span>
							<input type="text" name="color_camera" id="color_camera" class="form-control" value="YRS" required="required" pattern="" title="" style="width: 100%;height: 60px;background-color: #dde875;font-size: 3vw;text-align: center;font-weight:bold;color: #163756;border: 1px solid black" disabled>
							<span style="font-size: 20px; font-weight: bold;"><center>Jenis Middle:</center></span>
							<input type="text" name="middle_type" id="middle_type" class="form-control" value="-" required="required" pattern="" title="" style="width: 100%;height: 80px;background-color: #e7ff8c;font-size: 5vw;text-align: center;color: #0d2443;font-weight:bold;border: 1px solid black" readonly>
							<input type="text" name="middle_type_desc" id="middle_type_desc" class="form-control" value="-" required="required" pattern="" title="" style="width: 100%;height: 40px;background-color: #e7ff8c;font-size: 2vw;text-align: center;color: #0d2443;font-weight:bold;border: 1px solid black" readonly>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6" style="padding-left: 20px;border-left: 2px solid red">
			<div class="box box-solid" style="background-color: #f0f0f0">
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-12">
									<table class="table table-striped">
										<tr>
											<td colspan="3" style="font-size: 1.5vw; font-weight: bold;background-color: #4db6ac;border: 1px solid black;color: black">
												PUSH PULL CHECK
											</td>
										</tr>
										<tr>
											<td style="width:2%;background-color: #26a69a;font-size: 1.5vw;text-align: center;color: #000;font-weight:bold;padding-top: 0px;padding-bottom: 0px;border-top: #26a69a;border: 1px solid black" id="op3">
												-
											</td>
											<td style="width:30%;background-color: #80cbc4;font-size: 1.5vw;text-align: center;color: #000;font-weight:bold;padding-top: 0px;padding-bottom: 0px;border-top: #26a69a;border: 1px solid black" id="op4">
												-
											</td>
										</tr>
									</table>
								</div>
								<div class="col-xs-6">
									<span style="font-size: 20px; font-weight: bold;"><center>Last Check (Kgf):</center></span>
									<input type="text" name="last_check" id="last_check" class="form-control" value="3.2" required="required" pattern="" title="" style="width: 100%;height: 200px;background-color: #ffdd71;font-size: 10vw;text-align: center;color: #0d2443;font-weight:bold;border: 1px solid black" disabled>
								</div>
								<div class="col-xs-6">
									<span style="font-size: 20px; font-weight: bold;"><center>Judgement :</center></span>
									<input type="text" name="judgement_push_pull" id="judgement_push_pull" class="form-control" value="OK" required="required" pattern="" title="" style="width: 100%;height: 200px;font-size: 10vw;text-align: center;font-weight:bold;background-color: #57ff86;color: #163756;border: 1px solid black" disabled>
								</div>
							</div>
							<span style="font-size: 20px; font-weight: bold;"><center>COLOR :</center></span>
							<input type="text" name="color_push_pull" id="color_push_pull" class="form-control" value="YRS" required="required" pattern="" title="" style="width: 100%;height: 60px;background-color: #ffd0b0;font-size: 3vw;text-align: center;font-weight:bold;color: #163756;border: 1px solid black" disabled>
							<span style="font-size: 20px; font-weight: bold;"><center>Jenis Middle:</center></span>
							<input type="text" name="middle_type3" id="middle_type3" class="form-control" value="-" required="required" pattern="" title="" style="width: 100%;height: 80px;background-color: #e7ff8c;font-size: 5vw;text-align: center;color: #0d2443;font-weight:bold;border: 1px solid black" readonly>
							<input type="text" name="middle_type_desc3" id="middle_type_desc3" class="form-control" value="-" required="required" pattern="" title="" style="width: 100%;height: 40px;background-color: #e7ff8c;font-size: 2vw;text-align: center;color: #0d2443;font-weight:bold;border: 1px solid black" readonly>
							<!-- <div class="row" style="padding-top: 20px">
								<div class="col-xs-12">
									<span style="font-size: 20px; font-weight: bold;"><center>Result:</center></span>
									<table id="resultTable" name="resultTable" class="table table-bordered table-striped table-hover">
										<thead style="background-color: rgba(126,86,134,.7);">
											<th style="width: 15%">Model</th>
											<th style="width: 15%">Checked At</th>
											<th style="width: 15%">Value Check</th>
											<th style="width: 15%">Judgement</th>
											<th style="width: 15%">Checked By</th>
										</thead>
										<tbody id="resultTableBody">
										</tbody>
										<tfoot style="background-color: RGB(252, 248, 227);">
										</tfoot>
									</table>
								</div>
							</div> -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- <div class="row">
		<div class="col-xs-12">
			<button class="btn btn-danger" onclick="konfirmasi()" id="selesai_button" style="font-size:35px; width: 100%; font-weight: bold; padding: 0;">
				SELESAI
			</button>
			<button class="btn btn-warning" onclick="reset()" id="reset_button" style="font-size:35px; width: 100%; font-weight: bold; padding: 0;">
				RESET
			</button>
		</div>
	</div> -->

	<!-- <div class="modal fade" id="modalOperator2">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<center><label for="exampleInputEmail1">ID Card Operator Push Pull</label></center>
							<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator2" placeholder="Scan ID Card" required>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div> -->
	<div class="modal fade" id="modalOperator">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="col-xs-12">
							<center><label for="exampleInputEmail1">ID Card Operator Kango</label></center>
							<div class="input-group">
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border:1px solid black">
									<i class="glyphicon glyphicon-qrcode"></i>
								</div>
								<input class="form-control" style="text-align: center;width: 100%;border:1px solid black" type="text" id="operator" placeholder="Scan ID Card" required>
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border:1px solid black">
									<i class="glyphicon glyphicon-qrcode"></i>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<center><label for="exampleInputEmail1">ID Card Operator Push Pull</label></center>
							<div class="input-group">
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border:1px solid black">
									<i class="glyphicon glyphicon-qrcode"></i>
								</div>
								<input class="form-control" style="text-align: center;width: 100%;border:1px solid black" type="text" id="operator2" placeholder="Scan ID Card" required>
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border:1px solid black">
									<i class="glyphicon glyphicon-qrcode"></i>
								</div>
							</div>
						</div>
						<center><label for="exampleInputEmail1">Color</label></center>
						<div class="col-xs-12" id="color_choice">
							<div class="col-xs-4" style="margin-top: 5px"><button style="width: 100%;font-size: 20px;background-color: #fffac4" class="btn btn-default" id="IVORY" onclick="getModel(this.id)">IVORY</button>
							</div>
							<div class="col-xs-4" style="margin-top: 5px"><button style="width: 100%;font-size: 20px" class="btn btn-success" id="GREEN" onclick="getModel(this.id)">GREEN</button>
							</div>
							<div class="col-xs-4" style="margin-top: 5px"><button style="width: 100%;font-size: 20px" class="btn btn-primary" id="BLUE" onclick="getModel(this.id)">BLUE</button>
							</div>
							<div class="col-xs-4" style="margin-top: 5px"><button style="width: 100%;font-size: 20px;background-color: #ff40cf;color: white" class="btn btn-default" id="PINK" onclick="getModel(this.id)">PINK</button>
							</div>
							<div class="col-xs-4" style="margin-top: 5px"><button style="width: 100%;font-size: 20px" class="btn btn-danger" id="RED" onclick="getModel(this.id)">RED</button>
							</div>
							<div class="col-xs-4" style="margin-top: 5px"><button style="width: 100%;font-size: 20px;background-color: #96631b;color: white" class="btn btn-default" id="BROWN" onclick="getModel(this.id)">BROWN</button>
							</div>
						</div>
						<div class="col-xs-12" id="choice_color"><button style="width: 100%;font-size: 20px;color: black;font-weight: bold;background-color: #ffdd71" class="btn btn-default" id="color_fix" onclick="changeColor()">-</button>
						</div>
						<center><label  style="padding-top: 20px" for="exampleInputEmail1">Jenis Middle</label></center>
						<div class="col-xs-12" id="middle_choice">
							<div class="col-xs-6">
								<button style="width: 100%;font-size: 20px;background-color: #ffdd71" class="btn btn-default" id="B" onclick="getMiddle(this.id,this.value)" value="Baroque">Baroque
								</button>
							</div>
							<div class="col-xs-6">
								<button style="width: 100%;font-size: 20px;background-color: #ffab40" class="btn btn-default" id="G" onclick="getMiddle(this.id,this.value)" value="German">German
								</button>
							</div>
						</div>
						<div class="col-xs-12" id="middle">
							<button style="width: 100%;font-size: 20px;background-color: #ffdd71" class="btn btn-default" id="middle_type_desc2" onclick="changeMiddle()">
							</button>
						</div>
						<div class="col-xs-12" style="padding-top: 60px">
							<button class="btn btn-success" style="width: 100%;font-size: 40px;font-weight: bold;" onclick="submit()">MULAI PROSES</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	var audio_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');
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
		// $('body').toggleClass("sidebar-collapse");
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#operator').val('');
		$('#operator2').val('');
		$('#middle').hide();
		$('#choice_color').hide();
	});

	function getMiddle(id,value) {
		$('#middle').show();
		$('#middle_choice').hide();
		$('#middle_type_desc2').html(value);
		$('#middle_type').val(id);
		$('#middle_type_desc').val(value);
		$('#middle_type3').val(id);
		$('#middle_type_desc3').val(value);
	}

	function changeMiddle() {
		$('#middle').hide();
		$('#middle_choice').show();
	}

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val()
				}

				$.get('{{ url("scan/push_pull/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						// $('#modalOperator').modal('hide');
						$('#op').html(result.employee.employee_id);
						$('#op2').html(result.employee.name);
						// $('#employee_id').val(result.employee.employee_id);
						// $('#tag').focus();
						// itemList();
						// fillResult();
						// fillResultCamera();
						// push_pull();
						// $('#modalOperator2').modal({
						// 	backdrop: 'static',
						// 	keyboard: false
						// });
						$('#operator2').focus();
						$('#operator').prop('disabled', true);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', 'Employee ID Invalid.');
						$('#operator2').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator2").val("");
			}
		}
	});

	$('#modalOperator2').on('shown.bs.modal', function () {
		$('#operator2').focus();
	});

	$('#operator2').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator2").val().length >= 8){
				var data = {
					employee_id : $("#operator2").val()
				}

				$.get('{{ url("scan/push_pull/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						// $('#modalOperator2').modal('hide');
						$('#op3').html(result.employee.employee_id);
						$('#op4').html(result.employee.name);
						// $('#employee_id').val(result.employee.employee_id);
						// $('#tag').focus();
						// itemList();
						// fillResult();
						// fillResultCamera();
						// push_pull();
						$('#operator2').prop('disabled', true);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', 'Employee ID Invalid.');
						$('#operator2').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator2").val("");
			}
		}
	});

	function getModel(model) {
		$('#color_camera').val(model);
		$('#color_push_pull').val(model);
		$('#color_choice').hide();
		$('#color_fix').html(model);
		$('#choice_color').show();
	}

	function changeColor(){
		$('#choice_color').hide();
		$('#color_choice').show();
	}

	function submit() {
		if ($('#operator').val() == '' || $('#operator2').val() == '') {
			alert('Isi Semua Data!');
		}
		else if($('#color_camera').val() == 'YRS'){
			alert('Pilih Warna!');
		}
		else if($('#middle_type').val() == '-'){
			alert('Pilih Jenis Middle!');
		}
		else{
			setInterval(push_pull,1000);
			setInterval(camera_kango,1000);
			// setInterval(camera_kango2,1000);
			$('#modalOperator').modal('hide');
		}
	}

	function fillResult(){
		$.get('{{ url("push_pull/fetchResult") }}', function(result, status, xhr){
			// $('#resultTable').DataTable().destroy();
			if(xhr.status == 200){
				if(result.status){
					// $('#resultTableBody').html("");
					var resultData = '';
					var last_check = 0;
					var judgement_push_pull = '';
					// $.each(result.data, function(key, value){
					// 	resultData += '<tr>';
					// 	resultData += '<td>'+ value.model +'</td>';
					// 	resultData += '<td>'+ value.check_date +'</td>';
					// 	resultData += '<td>'+ value.value_check +'</td>';
					// 	if (value.judgement == 'NG') {
					// 		var color = '#ff6363';
					// 	}
					// 	else{
					// 		var color = '#57ff86';
					// 	}
					// 	resultData += '<td style="background-color:'+color+'">'+ value.judgement +'</td>';
					// 	resultData += '<td>'+ value.pic_check +'</td>'
					// 	resultData += '</tr>';
					// });

					$.each(result.data2, function(key, value2){
						last_check = value2.value_check;
						judgement_push_pull = value2.judgement;
					});
					$('#last_check').val(last_check);
					$('#judgement_push_pull').val(judgement_push_pull);
					// $('#resultTableBody').append(resultData);
					// $('#resultTable').DataTable({
					// 	"sDom": '<"top"i>rt<"bottom"flp><"clear">',
					// 	'paging'      	: true,
					// 	'lengthChange'	: false,
					// 	'searching'   	: true,
					// 	'ordering'		: false,
					// 	'info'       	: true,
					// 	'autoWidth'		: false,
					// 	"sPaginationType": "full_numbers",
					// 	"bJQueryUI": true,
					// 	"bAutoWidth": false,
					// 	"infoCallback": function( settings, start, end, max, total, pre ) {
					// 		return "<b>Total "+ total +" pc(s)</b>";
					// 	}
					// });
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
			}
		});
	}

	function fillResultCamera(){
		$.get('{{ url("push_pull/fetchResultCamera") }}', function(result, status, xhr){
			// $('#resultCamera').DataTable().destroy();
			if(xhr.status == 200){
				if(result.status){
					// $('#resultCameraBody').html("");
					var resultData = '';
					var judgement_middle = '';
					var judgement_stamp = '';
					// $.each(result.data, function(key, value){
					// 	resultData += '<tr>';
					// 	resultData += '<td>'+ value.model +'</td>';
					// 	resultData += '<td>'+ value.check_date +'</td>';
					// 	resultData += '<td>'+ value.value_check +'</td>';
					// 	if (value.judgement == 'NG') {
					// 		var color = '#ff6363';
					// 	}
					// 	else{
					// 		var color = '#57ff86';
					// 	}
					// 	resultData += '<td style="background-color:'+color+'">'+ value.judgement +'</td>';
					// 	resultData += '<td>'+ value.remark +'</td>';
					// 	resultData += '<td>'+ value.pic_check +'</td>';
					// 	resultData += '</tr>';
					// });
					$.each(result.data_middle, function(key, value2){
						judgement_middle = value2.judgement;
						// judgement_stamp = value2.judgement_stamp;
					});
					$.each(result.data_stamp, function(key, value3){
						// judgement_middle = value2.judgement_middle;
						judgement_stamp = value3.judgement;
					});
					$('#judgement_middle').val(judgement_middle);
					if (judgement_middle == 'NG') {
						document.getElementById('judgement_middle').style.backgroundColor = "#ff6363";
						document.getElementById('judgement_middle').style.color = "#fff";
					}else{
						document.getElementById('judgement_middle').style.backgroundColor = "#57ff86";
						document.getElementById('judgement_middle').style.color = "#163756";
					}
					$('#judgement_stamp').val(judgement_stamp);
					if (judgement_stamp == 'NG') {
						document.getElementById('judgement_stamp').style.backgroundColor = "#ff6363";
						document.getElementById('judgement_stamp').style.color = "#fff";
					}else{
						document.getElementById('judgement_stamp').style.backgroundColor = "#57ff86";
						document.getElementById('judgement_stamp').style.color = "#163756";
					}
					// $('#resultCameraBody').append(resultData);
					// $('#resultCamera').DataTable({
					// 	"sDom": '<"top"i>rt<"bottom"flp><"clear">',
					// 	'paging'      	: true,
					// 	'lengthChange'	: false,
					// 	'searching'   	: true,
					// 	'ordering'		: false,
					// 	'info'       	: true,
					// 	'autoWidth'		: false,
					// 	"sPaginationType": "full_numbers",
					// 	"bJQueryUI": true,
					// 	"bAutoWidth": false,
					// 	"infoCallback": function( settings, start, end, max, total, pre ) {
					// 		return "<b>Total "+ total +" pc(s)</b>";
					// 	}
					// });
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
			}
		});
	}

	function push_pull() {
		// if($('#color_push_pull').val() == 'YRS'){
		// 	alert('PILIH WARNA');
		// }else{

			var data = {
				model : $('#color_push_pull').val(),
				check_date : getActualFullDate(),
				// value_check : $('#last_check').val(),
				pic_check : $('#op4').text(),
			}
			// console.log(data);
			// console.log(data);

			$.post('{{ url("push_pull/store_push_pull") }}', data, function(result, status, xhr){
				if(result.status){
					// openSuccessGritter('Success', result.message);
					// fillResult();
					if (result.judgement.length > 0) {
						$('#last_check').val(result.value);
						$('#judgement_push_pull').val(result.judgement);
						// console.log(result.value);
						if (result.judgement == 'OK') {
							document.getElementById('judgement_push_pull').style.backgroundColor = "#57ff86";
							document.getElementById('judgement_push_pull').style.color = "#163756";
						}
						else if(result.judgement == 'NG'){
							audio_error.play();
							document.getElementById('judgement_push_pull').style.backgroundColor = "#ff6363";
							document.getElementById('judgement_push_pull').style.color = "#163756";
						}
					}
				}
				else{
					// openErrorGritter('Error!', result.message);
				}
			});
		// }
	}

	function camera_kango() {
			var data = {
				model : $('#color_camera').val(),
				check_date : getActualFullDate(),
				value_check : $('#middle_type').val(),
				pic_check : $('#op2').text(),
			}
			// console.log(data);
			// console.log(data);

			$.post('{{ url("camera_kango/store_camera_kango") }}', data, function(result, status, xhr){
				if(result.status){
					// openSuccessGritter('Success', result.message);
					// fillResultCamera();
					if (result.jumlah_perolehan >= 1000) {
						$('#perolehan').val(convertToK(result.jumlah_perolehan));
					}else{
						$('#perolehan').val(result.jumlah_perolehan);
					}
					$('#judgement_middle').val(result.judgement);
					// console.log(result.value);
					if (result.judgement == 'OK') {
						document.getElementById('judgement_middle').style.backgroundColor = "#57ff86";
						document.getElementById('judgement_middle').style.color = "#163756";
					}
					else if(result.judgement == 'NG'){
						audio_error.play();
						document.getElementById('judgement_middle').style.backgroundColor = "#ff6363";
						document.getElementById('judgement_middle').style.color = "#163756";
					}
				}
				else{
					// openErrorGritter('Error!', result.message);
					if (result.jumlah_perolehan >= 1000) {
						$('#perolehan').val(convertToK(result.jumlah_perolehan));
					}else{
						$('#perolehan').val(result.jumlah_perolehan);
					}
				}
			});
		// }
	}

	function camera_kango2() {
			var data = {
				model : $('#color_camera').val(),
				check_date : getActualFullDate(),
				value_check : $('#middle_type').val(),
				pic_check : $('#op2').text(),
			}
			// console.log(data);
			// console.log(data);

			$.post('{{ url("camera_kango/store_camera_kango2") }}', data, function(result, status, xhr){
				if(result.status){
					// openSuccessGritter('Success', result.message);
					// fillResultCamera();
					$('#judgement_stamp').val(result.judgement);
					// console.log(result.value);
					if (result.judgement == 'OK') {
						document.getElementById('judgement_stamp').style.backgroundColor = "#57ff86";
						document.getElementById('judgement_stamp').style.color = "#163756";
					}
					else if(result.judgement == 'NG'){
						audio_error.play();
						document.getElementById('judgement_stamp').style.backgroundColor = "#ff6363";
						document.getElementById('judgement_stamp').style.color = "#163756";
					}
				}
				else{
					// openErrorGritter('Error!', result.message);
				}
			});
		// }
	}

	function convertToK(value)
    {
        var number = value / 1000;
    //if you want 2 decimal digits
        return newVal = number.toFixed(2) + 'K';
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
