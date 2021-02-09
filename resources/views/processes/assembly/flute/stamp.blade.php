@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
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
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		font-size: 1.2vw;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		font-size: 1vw;
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }

	.dataTables_info,
	.dataTables_length {
		color: white;
	}

	div.dataTables_filter label, 
     div.dataTables_wrapper div.dataTables_info {
	     color: white;
	}
</style>
@stop
@section('header')
@endsection
@section('content')
<div id="error" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(255,102,102); z-index: 30001; opacity: 0.8;">
	<p id="pError" style="position: absolute; color: White; top: 35%; left: 30%; font-weight: bold; font-size: 1.5vw;">
		<i class="fa fa-unlink fa-spin"></i> <span>Error!!<br>
			Lakukan refresh pada browser dan lakukan proses dari awal.<br>
			Apabila masih terjadi "ERROR" silahkan menghubungi MIS.
		</p>
	</div>
	<section class="content" style="padding-top: 0;">
		<div class="row">
			<div class="col-xs-4" style="padding: 0 0 0 10px;">
				<center>
					<div style="font-weight: bold; font-size: 2.5vw; color: black; text-align: center; color: #3d9970;background-color: white">
						<i class="fa fa-arrow-down"></i> ➀ PILIH MODEL <i class="fa fa-arrow-down"></i>
					</div>
					<div>
						<div class="row" style="padding-right: 10px">
							@foreach($models as $model)
							<button id="{{ $model->model }}" onclick="fetchModel(id)" type="button" class="btn bg-olive btn-lg" style="padding: 5px 1px 5px 1px; margin-top: 6px; margin-left: 2px; margin-right: 2px; width: 30%; font-size: 1.3vw">{{ $model->model }}</button>
							@endforeach
						</div>
					</div>
				</center>
			</div>
			<div class="col-xs-4" style="padding: 0 0 0 0;">
				<center>
					<div style="font-weight: bold; font-size: 2.5vw; color: black; text-align: center; color: #ffa500;background-color: white;">
						<i class="fa fa-arrow-down"></i> STAMP <i class="fa fa-arrow-down"></i>
					</div>
					<table style="width: 100%; text-align: center; background-color: orange; font-weight: bold; font-size: 1.5vw;margin-top: 5px" border="1">
						<tbody>
							<tr>
								<td style="width: 2%;" id="op_id">-</td>
								<td style="width: 8%;" id="op_name">-</td>
							</tr>
						</tbody>
					</table>
					<span style="font-size: 2vw; font-weight: bold; color: rgb(255,255,150);">Last Counter:</span><br>
					<input id="lastCounter" type="text" style="border:0; font-weight: bold; background-color: rgb(255,255,204); width: 100%; text-align: center; font-size: 4vw" disabled>
					<button class="btn btn-danger" id="minus" onclick="adjustSerial(id)" style="width: 49%; margin-top: 5px; font-weight: bold; font-size: 1.5vw; padding: 0;">MINUS <i class="fa fa-minus-square"></i></button>
					<button class="btn btn-danger" id="plus" onclick="adjustSerial(id)" style="width: 49%; margin-top: 5px; font-weight: bold; font-size: 1.5vw; padding: 0;">PLUS <i class="fa fa-plus-square"></i></button>
					<span style="font-size: 2vw; font-weight: bold; color: rgb(255,127,80);;">Model:</i></span><br>
					<input id="model" type="text" style="border:0; font-weight: bold; background-color: rgb(255,127,80); width: 100%; text-align: center; font-size: 4vw" value="YFL" disabled>
				</center>
				<div class="col-xs-12" style="margin-top: 10px;">
					<div class="row">
						<div class="col-xs-7" style="padding-left: 0;">
							<button class="btn btn-primary" style="width: 100%; font-weight: bold; margin-bottom: 5px; font-size: 1.82vw;" onclick="fetchCategory('FG')">Finished Goods</button>
							<button class="btn btn-success" style="width: 100%; font-weight: bold; font-size: 1.82vw;" onclick="fetchCategory('KD')">KD Parts</button>
						</div>
						<div class="col-xs-5" style="padding: 0;">
							<input id="category" type="text" style="margin-bottom: 0; border:0; font-weight: bold; background-color: #ffee58; width: 100%; height: 100%; text-align: center; font-size: 5vw; color: black;" disabled>				
						</div>
					</div>
				</div>
				<span style="font-size: 2vw; font-weight: bold; color: white;">Tag RFID:</span><br>
				<input id="tagName" type="text" style="border:0; font-weight: bold; background-color: white; width: 100%; text-align: center; font-size: 4vw" disabled>
				<input id="tagBody" type="text" style="border:0; background-color: #3c3c3c; width: 100%; text-align: center; font-size: 1vw">
			</div>
			<div class="col-xs-4" style="padding: 0 10px 0 0;">
				<center>
					<div style="font-weight: bold; font-size: 2.5vw; text-align: center; color: #3c3c3c;background-color: white">
						<i class="fa fa-arrow-down"></i> STAMP LOG <i class="fa fa-arrow-down"></i>
					</div>
					<div style="padding-left: 10px">
						<button class="btn bg-primary btn-lg" style="padding: 5px 1px 5px 1px; margin-top: 5px; margin-left: 2px; margin-right: 2px; width: 30%; font-size: 1.3vw" onclick="adjust()">
							<i class="fa fa-calendar-check-o "></i>&nbsp;&nbsp;Adjust Serial
						</button>
						<button class="btn btn-danger btn-lg" style="padding: 5px 1px 5px 1px; margin-top: 5px; margin-left: 2px; margin-right: 2px; width: 30%; font-size: 1.3vw" data-toggle="modal" data-target="#reprintModal" onclick="clearInterval(intervalTag)">
							<i class="fa fa-print"></i>&nbsp;&nbsp;Reprint
						</button>
						<table id="logTable" class="table table-bordered table-striped table-hover">
							<thead style="background-color: rgb(240,240,240);">
								<tr>
									<th style="width: 1%">Serial</th>
									<th style="width: 1%">Model</th>
									<th style="width: 1%">Cat</th>
									<th style="width: 2%">By</th>
									<th style="width: 1%">At</th>
									<th style="width: 1%">Action</th>
								</tr>
							</thead>
							<tbody id="logTableBody">
							</tbody>
							<tfoot>
							</tfoot>						
						</table>
					</div>
				</center>
			</div>
		</div>
	</div>
	<input type="hidden" id="employee_id">
	<input type="hidden" id="nextCounter">
	<input type="hidden" id="started_at">
</section>

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

<div class="modal fade" id="editModal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="newInterval()"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Edit / Delete Stamp</h4>
			</div>
			<div class="modal-body">
				<div class="input-group">
					<input type="text" style="text-align: center;" class="form-control" name="serialNumberText" id="serialNumberText" disabled>
					<input type="text" style="text-align: center;" class="form-control" name="modelTextAsli" id="modelTextAsli" disabled="">
					<input type="text" style="text-align: center;" class="form-control" name="modelText" id="modelText">
					<input type="hidden" class="form-control" name="idStamp" id="idStamp">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" onclick="destroyStamp()" class="btn btn-danger pull-left">Delete</button>
				<button type="button" onclick="updateStamp()" class="btn btn-success">Update</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="adjustModal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="newInterval()"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Edit Serial Number</h4>
			</div>
			<div class="modal-body">
				<div class="input-group">
					<div class="row">
						<div class="col-md-12">
							<label>Prefix</label>
							<input type="text" style="text-align: center;" class="form-control" name="prefix" id="prefix">
						</div>
						<div class="col-md-12">
							<label>Last Index</label>
							<input type="text" style="text-align: center;" class="form-control" name="lastIndex" id="lastIndex">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" onclick="updateSerial()" style="width: 100%" class="btn btn-primary">Confirm</button>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-default fade" id="reprintModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="newInterval()">&times;</button>
				<h4 class="modal-title" id="titleModal">Reprint Stamp</h4>
			</div>
			<!-- <form class="form-horizontal" role="form" method="post" action="{{url('reprint/stamp')}}"> -->
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="modal-body" id="messageModal">
					<label>Serial Number</label>
					<select class="form-control select2" name="serial_number_reprint" style="width: 100%;" data-placeholder="Pilih Serial Number ..." id="serial_number_reprint" required>
						<option value=""></option>
					</select>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left" data-dismiss="modal" oncancel="newInterval()">Close</button>
					<button onclick="reprintStamp()" class="btn btn-danger"><i class="fa fa-print"></i>&nbsp; Reprint</button>
				</div>
			<!-- </form> -->
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		clear();
		$(function () {
			$('.select2').select2()
		});
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

		fetchResult();
		fetchSerial();

		$('#modalOperator').on('shown.bs.modal', function () {
			$('#operator').focus();
		});

	});

	function focusTag(){
		$('#tagBody').focus();
	}

	var intervalTag;

	function clear(){
		$('#operator').val('');
		$('#started_at').val('');
		$('#nextCounter').val('');
		$('#employee_id').val('');
		$('#model').val('YFL');
		$('#tagName').val('');
		$('#tagBody').val('');

		$('#id_op').text('-');
		$('#op_name').text('-');

	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');


	$('#tagBody').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($('#model').val() != "YFL"){
				if($('#category').val() != ""){
					if($("#tagBody").val().length == 10){
						var data = {
							tag : $("#tagBody").val(),
							origin_group_code : '041'
						}
						$.get('{{ url("scan/assembly/tag_stamp") }}', data, function(result, status, xhr){
							if(result.status){
								$('#tagName').val(result.tag.remark);
								$('#started_at').val(result.started_at);
								// $('#tagBody').val('');
								stamp();
							}
							else{
								audio_error.play();
								openErrorGritter('Error', result.message);
								$('#tagBody').val('');
								$('#tagBody').focus();
							}
						});
					}
					else{
						audio_error.play();
						openErrorGritter('Error', 'RFID tidak valid periksa kembali RFID anda');
						$('#tagBody').val('');
						$('#tagBody').focus();				
					}				
				}
				else{
					audio_error.play();
					openErrorGritter('Error', 'Pilih category terlebih dahulu FG / KD');
					$('#tagBody').val('');
					$('#tagBody').focus();				
				}
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'Pilih model terlebih dahulu');
				$('#tagBody').val('');
				$('#tagBody').focus();	
			}
		}
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val()
				}

				$.get('{{ url("scan/assembly/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#op_id').html(result.employee.employee_id);
						$('#op_name').html(result.employee.name);
						$('#employee_id').val(result.employee.employee_id);
						intervalTag = setInterval(focusTag, 1000);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', 'Employee ID Invalid.');
				$("#operator").val("");
			}			
		}
	});

	function newInterval() {
		intervalTag = setInterval(focusTag, 1000);
	}

	function stamp(){
		var model = $('#model').val();
		var serial = $('#nextCounter').val();
		var tagName = $('#tagName').val();
		var tagBody = $('#tagBody').val();
		var op_id = $('#employee_id').val();
		var started_at = $('#started_at').val();
		if($('#category').val() == 'FG'){
			var location = 'stamp-process'; 
		}
		else{
			var location = 'stampkd-process'; 
		}

		var data = {
			origin_group_code: '041',
			model:model,
			serial:serial,
			tagName:tagName,
			tagBody:tagBody,
			op_id:op_id,
			started_at:started_at,
			location:location
		}
		$.post('{{ url("stamp/assembly/flute") }}', data, function(result, status, xhr){
			if(result.status){
				if(result.status_code == 'no_stamp'){
					stamp();
					return false;
				}
				if(result.status_code == 'stamp'){
					openSuccessGritter('Success!', result.message);
					$('#tagName').val('');
					$('#tagBody').val('');
					fetchResult();
					fetchSerial();
					$('#tagBody').focus();
				}
				else{
					$('#pError').append('<br><br>'+result.message);
					$('#error').show();
				}
			}
			else{
				$('#pError').append('<br><br>'+result.message);
				$('#error').show();
			}
		});
	}

	function adjustSerial(id){
		var data ={
			adjust:id,
			origin_group_code:'041'
		}
		$.post('{{ url("stamp/assembly/adjust_serial") }}', data, function(result, status, xhr){
			if(result.status){
				fetchSerial();
				openSuccessGritter('Success!', result.message);
			}
			else{
				audio_error.play();
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function fetchCategory(id){
		if(id == 'FG'){
			$('#category').val(id);
			$('#category').css('color', '#3c8dbc');
		}
		else{
			$('#category').val(id);
			$('#category').css('color', '#00a65a');
		}
	}

	function fetchModel(id){
		$('#model').val(id);
	}

	function fetchSerial(){
		var data = {
			origin_group_code: '041'
		}
		$.get('{{ url("fetch/assembly/serial") }}', data, function(result, status, xhr){
			if(result.status){
				$('#lastCounter').val(result.lastCounter);
				$('#nextCounter').val(result.nextCounter);
			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
			}
		});
	}

	function fetchResult(){
		var data = {
			origin_group_code : '041'
		}
		$.get('{{ url("fetch/assembly/stamp_result") }}', data, function(result, status, xhr){
			if(result.status){
				$('#logTable').DataTable().clear();
				$('#logTable').DataTable().destroy();
				$('#logTableBody').html('');

				var tableData = '';
				var no = 1

				$.each(result.logs, function(key, value){
					if (no % 2 === 0 ) {
						color = 'style="background-color: #fffcb7"';
					} else {
						color = 'style="background-color: #ffd8b7"';
					}
					tableData += '<tr '+color+'>';
					tableData += '<td style="vertical-align:middle">'+value.serial_number+'</td>';
					tableData += '<td style="vertical-align:middle">'+value.model+'</td>';
					tableData += '<td style="vertical-align:middle">'+value.category+'</td>';
					tableData += '<td style="vertical-align:middle">'+value.name+'</td>';
					tableData += '<td style="vertical-align:middle">'+value.created_at+'</td>';
					tableData += '<td style="vertical-align:middle"><button class="btn btn-xs btn-danger" id="'+value.id_details+'" onclick="editStamp(id)"><span class="fa fa-edit"></span></button></td>';
					tableData += '</tr>';
					no += 1;
				});
				$('#logTableBody').append(tableData);

				$('#logTable').DataTable({
					"sDom": '<"top"i>rt<"bottom"flp><"clear">',
						'paging'      	: true,
						'lengthChange'	: false,
						'searching'   	: true,
						'ordering'		: false,
						'info'       	: true,
						'autoWidth'		: false,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"infoCallback": function( settings, start, end, max, total, pre ) {
							return "<b>Total "+ total +" pc(s)</b>";
						}
				});

				$('#serial_number_reprint').html("");
				var reprint = "";
				reprint += '<option value=""></option>';
				$.each(result.logsall, function(key, value){
					reprint += '<option value="'+value.serial_number+'">'+value.serial_number+'</option>';
				});
				$('#serial_number_reprint').append(reprint);

			}
			else{
				audio_error.play();	
				openErrorGritter('Error!', 'Attempt to retrieve data failed');			
			}
		});
	}

	function editStamp(id){
		var data = {
			id:id
		}
		$.get('{{ url("edit/assembly/stamp") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#modelText').val(result.details.model);
					$('#modelTextAsli').val(result.details.model);
					$('#serialNumberText').val(result.details.serial_number);
					$('#idStamp').val(result.details.id);
					$('#editModal').modal('show');
					clearInterval(intervalTag);
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from sever');
			}
		});
	}

	function destroyStamp(){
		var id = $('#idStamp').val();
		var model = $('#modelTextAsli').val();
		var serial_number = $('#serialNumberText').val();
		var data = {
			id:id,
			model:model,
			serial_number:serial_number,
			origin_group_code:'041',
		}
		if(confirm("Are you sure you want to delete this data?")){
			$.post('{{ url("destroy/assembly/stamp") }}', data, function(result, status, xhr){
				if(xhr.status == 200){
					if(result.status){
						$('#idStamp').val('');
						$('#modelTextAsli').val('');
						$('#modelText').val('');
						$('#serialNumberText').val('');
						$('#editModal').modal('hide');
						openSuccessGritter('Success!', result.message);					
						fetchResult();
						fetchSerial();
						intervalTag = setInterval(focusTag, 1000);
						// clear();
					}
					else{
						audio_error.play();
						alert('Attempt to retrieve data failed');
						intervalTag = setInterval(focusTag, 1000);
					}
				}
				else{
					audio_error.play();
					alert('Disconnected from sever');
					intervalTag = setInterval(focusTag, 1000);
				}
			});
		}
	}

	function updateStamp(){
		var id = $('#idStamp').val();
		var model = $('#modelText').val();
		var model_asli = $('#modelTextAsli').val();
		var serial_number = $('#serialNumberText').val();
		var data = {
			id:id,
			model:model,
			model_asli:model_asli,
			serial_number:serial_number,
			origin_group_code:'041',
		}
		if(confirm("Are you sure you want to update this data?")){
			$.post('{{ url("update/assembly/stamp") }}', data, function(result, status, xhr){
				if(xhr.status == 200){
					if(result.status){
						$('#idStamp').val('');
						$('#modelText').val('');
						$('#modelTextAsli').val('');
						$('#serialNumberText').val('');
						$('#editModal').modal('hide');
						openSuccessGritter('Success!', result.message);					
						fetchResult();
						fetchSerial();
						intervalTag = setInterval(focusTag, 1000);
						// clear();
					}
					else{
						audio_error.play();
						alert('Attempt to retrieve data failed');
						intervalTag = setInterval(focusTag, 1000);
					}
				}
				else{
					audio_error.play();
					alert('Disconnected from sever');
					intervalTag = setInterval(focusTag, 1000);
				}
			});
		}
	}

	function adjust(){
		var data = {
			originGroupCode:'041'
		}
		$.get('{{ url("adjust/assembly/stamp") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#prefix').val(result.prefix);
					$('#lastIndex').val(result.lastIndex);
					$('#adjustModal').modal('show');
					clearInterval(intervalTag);
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
			}
		});
	}

	function updateSerial(){
		var prefix = $('#prefix').val();
		var lastIndex = $('#lastIndex').val();
		var data = {
			prefix:prefix,
			lastIndex:lastIndex,
			originGroupCode:'041'
		}
		$.post('{{ url("adjust/assembly/stamp_update") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#prefix').val("");
					$('#lastIndex').val("");
					$('#adjustModal').modal('hide');
					openSuccessGritter('Success!', result.message);
					fetchSerial();
					intervalTag = setInterval(focusTag, 1000);
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
					intervalTag = setInterval(focusTag, 1000);
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
				intervalTag = setInterval(focusTag, 1000);
			}
		});
	}

	function reprintStamp() {
		var serial_number = $('#serial_number_reprint').val();
		if (serial_number == '') {
			alert('Pilih Serial Number');
		}else{
			var data = {
				serial_number:serial_number,
				origin_group_code:'041'
			}

			$.get('{{ url("reprint/assembly/stamp") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#serial_number_reprint').val("").trigger('change');
					$('#reprintModal').modal('hide');
					openSuccessGritter('Success!', result.message);
					fetchSerial();
					fetchResult();
					intervalTag = setInterval(focusTag, 1000);
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
					intervalTag = setInterval(focusTag, 1000);
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
				intervalTag = setInterval(focusTag, 1000);
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
			time: '3000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}

</script>
@endsection

