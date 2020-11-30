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
			<div class="col-xs-4" style="padding: 0 0 0 0;">
				<center>
					<div style="font-weight: bold; font-size: 2.5vw; color: black; text-align: center; color: #3d9970;">
						<i class="fa fa-arrow-down"></i> ➀ PILIH MODEL <i class="fa fa-arrow-down"></i>
					</div>
					<div>
						@foreach($models as $model)
						<button id="{{ $model->model }}" onclick="fetchModel(id)" type="button" class="btn bg-olive btn-lg" style="padding: 5px 1px 5px 1px; margin-top: 6px; margin-left: 2px; margin-right: 2px; width: 30%; font-size: 1.3vw">{{ $model->model }}</button>
						@endforeach
					</div>
				</center>
			</div>
			<div class="col-xs-4" style="padding: 0 0 0 0;">
				<center>
					<div style="font-weight: bold; font-size: 2.5vw; color: black; text-align: center; color: #ffa500;">
						<i class="fa fa-arrow-down"></i> STAMP <i class="fa fa-arrow-down"></i>
					</div>
					<table style="width: 100%; text-align: center; background-color: orange; font-weight: bold; font-size: 1.5vw;" border="1">
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
			<div class="col-xs-4" style="padding: 0 10px 0 20px;">
				<center>
					<div style="font-weight: bold; font-size: 2.5vw; color: black; text-align: center; color: white;">
						<i class="fa fa-arrow-down"></i> STAMP LOG <i class="fa fa-arrow-down"></i>
					</div>
					<div>
						<table id="logTable" class="table table-bordered table-striped table-hover">
							<thead style="background-color: rgb(240,240,240);">
								<tr>
									<th style="width: 1%">Serial</th>
									<th style="width: 1%">Model</th>
									<th style="width: 1%">Cat</th>
									<th style="width: 2%">By</th>
									<th style="width: 1%">At</th>
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
						setInterval(focusTag, 1000);
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
					tableData += '<td>'+value.serial_number+'</td>';
					tableData += '<td>'+value.model+'</td>';
					tableData += '<td>'+value.category+'</td>';
					tableData += '<td>'+value.name+'</td>';
					tableData += '<td>'+value.created_at+'</td>';
					tableData += '</tr>';
					no += 1;
				});
				$('#logTableBody').append(tableData);

				$('#logTable').DataTable({
					"bInfo" : false,
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
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
				audio_error.play();	
				openErrorGritter('Error!', 'Attempt to retrieve data failed');			
			}
		});
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

