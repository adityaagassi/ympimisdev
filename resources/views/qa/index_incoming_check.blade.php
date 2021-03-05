@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<link rel="stylesheet" href="{{ url("css/jqbtk.css")}}">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding: 0px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
		vertical-align: middle;
		background-color: rgb(126,86,134);
		color: #FFD700;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#ngTemp {
		height:200px;
		overflow-y: scroll;
	}

	#ngList2 {
		height:385px;
		overflow-y: scroll;
	}
	#loading, #error { display: none; }
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
</style>
@stop
@section('header')
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="location" value="{{ $location }}">
	<input type="hidden" id="employee_id" value="">
	<input type="hidden" id="start_time" value="">
	<input type="hidden" id="incoming_check_code" value="">
	
	<div class="row" style="padding-left: 10px; padding-right: 10px;">
		<div class="col-xs-6" style="padding-right: 0; padding-left: 0">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
				<tbody>
					<tr>
						<th style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Date</th>
						<th style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Loc</th>
					</tr>
					<tr>
						<td style="background-color: #fca311; color: #14213d; text-align: center; font-size:15px;" id="date">{{date("Y-m-d")}}</td>
						<td style="background-color: #14213d; text-align: center;color: white; font-size:15px;" id="loc">{{$loc}}</td>
					</tr>
					<tr>
						<th colspan="2" style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Inspector QA</th>
					</tr>
					<tr>
						<td style="background-color: #fca311; color: #14213d; text-align: center; font-size:15px; width: 30%;" id="op">-</td>
						<td style="background-color: #14213d; text-align: center; color: white; font-size: 15px;" id="op2">-</td>
					</tr>
					
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
				<tbody>
					<tr>
						<td colspan="2" style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;">
							MATERIAL
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="text" class="pull-right" name="material_number" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="material_number" placeholder="Material Number" onkeyup="checkMaterial(this.value)">
						</td>
					</tr>
					<tr>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">
							Material Description
						</td>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">
							Vendor
						</td>
					</tr>
					<tr>
						<td id="material_description" style="background-color: #fca311; text-align: center; color: #14213d; font-size: 20px;">-
						</td>
						<td id="vendor" style="background-color: #14213d; text-align: center; color: #fff; font-size: 20px;">-
						</td>
					</tr>
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
				<tbody>
					<tr>
						<td colspan="2" style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;">
							QTY
						</td>
					</tr>
					<tr>
						<td>
							<input type="number" class="pull-right numpad2" name="qty_rec" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="qty_rec" placeholder="Qty Rec">
						</td>
						<td>
							<input type="number" class="pull-right numpad" name="qty_check" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="qty_check" placeholder="Qty Check">
						</td>
					</tr>
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
				<tbody>
					<tr>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							INVOICE NUMBER
						</td>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							INSPECTION LEVEL
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" class="pull-right" name="invoice" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="invoice" placeholder="Invoice">
						</td>
						<td>
							<select name="inspection_level" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="inspection_level" data-placeholder="Inspection Level">
								<option value="-">Pilih Inspection Level</option>
								@foreach($inspection_level as $inspection)
									<option value="{{$inspection->inspection_level}}">{{$inspection->inspection_level}}</option>
								@endforeach
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="col-xs-6" style="padding-right: 0;">
			<div id="ngList2">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 65%; background-color: #d1d1d1; padding:0;font-size: 20px;" >Nama NG</th>
						</tr>
					</thead>
					<tbody>
						<?php $no = 1; ?>
						@foreach($ng_lists as $nomor => $ng_list)
						<?php if ($no % 2 === 0 ) {
							$color = 'style="background-color: #fffcb7"';
						} else {
							$color = 'style="background-color: #ffd8b7"';
						}
						?>
						<input type="hidden" id="loop" value="{{$loop->count}}">
						<tr <?php echo $color ?>>
							<td id="{{ $ng_list->ng_name }}" onclick="showModalNg('{{ $ng_list->ng_name }}')" style="font-size: 35px;">{{ $ng_list->ng_name }}</td>
						</tr>
						<?php $no+=1; ?>
						@endforeach
					</tbody>
				</table>
			</div>
			<div id="ngTemp">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 30%; background-color: #d1d1d1; padding:0;font-size: 20px;" >Nama NG</th>
							<th style="width: 10%; background-color: #d1d1d1; padding:0;font-size: 20px;" >Qty</th>
							<th style="width: 10%; background-color: #d1d1d1; padding:0;font-size: 20px;" >Status</th>
							<th style="width: 30%; background-color: #d1d1d1; padding:0;font-size: 20px;" >Note</th>
							<th style="width: 20%; background-color: #d1d1d1; padding:0;font-size: 20px;" >Action</th>
						</tr>
					</thead>
					<tbody id="bodyNgTemp">
					</tbody>
				</table>
			</div>

			<div class="col-xs-6" style="padding: 0px;padding-top: 10px;padding-right: 5px">
				<button class="btn btn-danger" id="btn_cancel" onclick="cancelAll()" style="font-size: 25px;font-weight: bold;width: 100%">
					CANCEL
				</button>
			</div>
			<div class="col-xs-6" style="padding: 0px;padding-top: 10px;padding-left: 5px">
				<button class="btn btn-success" id="btn_confirm" onclick="confirm()" style="font-size: 25px;font-weight: bold;width: 100%">
					CONFIRM
				</button>
			</div>
		</div>
	</div>
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

<div class="modal fade" id="modalNg">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<h4 id="ng_name" style="width: 100%;background-color: #fca311;font-size: 25px;font-weight: bold;padding: 5px;text-align: center;color: #14213d"></h4>
					<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
						<tbody>
							<tr>
								<td style="background-color: #14213d; text-align: center; color: #fff; padding:0;font-size: 20px;font-weight: bold;">
									QTY
								</td>
								<td style="background-color: #14213d; text-align: center; color: #fff; padding:0;font-size: 20px;font-weight: bold;">
									Status
								</td>
							</tr>
							<tr>
								<td>
									<input type="number" class="pull-right numpad" name="qty_ng" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="qty_ng" placeholder="Qty NG">
								</td>
								<td>
									<select name="status_ng" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="status_ng" data-placeholder="Status NG">
										<option value="-">Pilih Status NG</option>
										<option value="Repair">Repair</option>
										<option value="Scrap">Scrap</option>
										<option value="Return">Return</option>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2" style="background-color: #14213d; text-align: center; color: #fff; padding:0;font-size: 20px;font-weight: bold;">
									Note
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<textarea type="text" class="pull-right" name="note_ng" style="height: 50px;font-size: 20px;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="note_ng" placeholder="Note"></textarea>
								</td>
							</tr>
						</tbody>
					</table>

					<div style="padding-top: 10px">
						<button id="confNg" style="width: 100%; margin-top: 10px; font-size: 3vw; padding:0; font-weight: bold; border-color: black; color: white;" onclick="confNgTemp()" class="btn btn-success">CONFIRM</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jqbtk.js") }}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	var hour;
    var minute;
    var second;
    var intervalTime;
    var intervalUpdate;

    $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$("#operator").val('');
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		$('.numpad2').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		cancelAll();
		$('#invoice').keyboard();
		$('#material_number').keyboard();
		$('#note_ng').keyboard();
	});

	function cancelAll() {
		$('#material_number').val('');
		$('#invoice').val('');
		$('#qty_check').val('');
		$('#qty_rec').val('');
		$('#material_description').html('-');
		$('#vendor').html('-');
		$('#inspection_level').val('-').trigger('change');
		$('#note_ng').val('');
		$('#qty_ng').val('');
		$('#status_ng').val('-').trigger('change');
		$('#start_time').val('');
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val(),
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#modalMesin').modal('show');
						$('#op').html(result.employee.employee_id);
						$('#op2').html(result.employee.name);
						$('#employee_id').val(result.employee.employee_id);
						$('#material_number').focus();
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

	function checkMaterial(material_number) {
		if (material_number.length === 7) {
			var data = {
				material_number:material_number
			}
			$.get('{{ url("fetch/qa/check_material") }}', data, function(result, status, xhr){
				if(result.status){
					$('#material_description').html(result.material.material_description);
					$('#vendor').html(result.material.vendor);
					$('#material_number').focus();
					$('#start_time').val(getActualFullDate());
				}
				else{
					$('#material_description').html("-");
					$('#vendor').html("-");
				}
			});
		}else{
			$('#material_description').html("-");
			$('#vendor').html("-");
		}
	}

	function showModalNg(ng_name) {
		if ($('#material_number').val() == "" || $('#qty_rec').val() == "" || $('#qty_check').val() == "" || $('#invoice').val() == "" || $('#inspection_level').val() == "-") {
			openErrorGritter('Error!','Masukkan Semua Data');
		}else{
			$('#note_ng').val('');
			$('#qty_ng').val('');
			$('#status_ng').val('-').trigger('change');
			$('#ng_name').html(ng_name);
			$('#modalNg').modal('show');
		}
	}

	function confNgTemp() {
		if ($('#qty_ng').val() == "" || $('#status_ng').val() == "") {
			alert('Isi Semua Data');
		}else{
			var material_number = $('#material_number').val();
			var material_description = $('#material_description').text();
			var vendor = $('#vendor').text();
			var qty_rec = $('#qty_rec').val();
			var qty_check = $('#qty_check').val();
			var invoice = $('#invoice').val();
			var inspection_level = $('#inspection_level').val();
			var ng_name = $('#ng_name').text();
			var qty_ng = $('#qty_ng').val();
			var status_ng = $('#status_ng').val();
			var note_ng = $('#note_ng').val();
			var inspector = $('#employee_id').val();
			var location = $('#location').val();

			var data = {
				material_number:material_number,
				material_description:material_description,
				vendor:vendor,
				qty_rec:qty_rec,
				qty_check:qty_check,
				invoice:invoice,
				inspection_level:inspection_level,
				ng_name:ng_name,
				qty_ng:qty_ng,
				status_ng:status_ng,
				note_ng:note_ng,
				location:location,
				inspector:inspector,
			}

			$.post('{{ url("input/qa/ng_temp") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					$('#note_ng').val('');
					$('#qty_ng').val('');
					$('#status_ng').val('-').trigger('change');
					$('#modalNg').modal('hide');
					$('#incoming_check_code').val(result.incoming_check_code);
					fetchNgTemp();
				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
				}
			});
		}
	}

	function fetchNgTemp() {
		data = {
			incoming_check_code:$('#incoming_check_code').val()
		}
		$.get('{{ url("fetch/qa/ng_temp") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				var ngTemp = "";
				$('#bodyNgTemp').html("");
				var index = 1;
				$.each(result.ng_temp, function(key,value){
					if (index % 2 === 0) {
						var color = 'style="background-color: #e1e5f2"';
					}else{
						var color = 'style="background-color: #bfdbf7"';
					}
					ngTemp += '<tr '+color+'>';
					ngTemp += '<td>'+value.ng_name+'</td>';
					ngTemp += '<td>'+value.qty_ng+'</td>';
					ngTemp += '<td>'+value.status_ng+'</td>';
					ngTemp += '<td>'+(value.note_ng || "")+'</td>';
					ngTemp += '<td><button onclick="deleteNgTemp(\''+value.id+'\')" class="btn btn-danger btn-sm">Delete</button></td>';
					ngTemp += '</tr>';
					index++;
				});
				$('#bodyNgTemp').append(ngTemp);
			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
			}
		});
	}

	function deleteNgTemp(id) {
		if (confirm('Are you sure want to delete this data?')) {
			var data = {
				id:id
			}
			$.get('{{ url("delete/qa/ng_temp") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					fetchNgTemp();
				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
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

	$.date = function(dateObject) {
		var d = new Date(dateObject);
		var day = d.getDate();
		var month = d.getMonth() + 1;
		var year = d.getFullYear();
		if (day < 10) {
			day = "0" + day;
		}
		if (month < 10) {
			month = "0" + month;
		}
		var date = day + "/" + month + "/" + year;

		return date;
	};

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

</script>
@endsection