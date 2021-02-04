@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
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
	#ngList {
		height:120px;
		overflow-y: scroll;
	}

	#ngList2 {
		height:420px;
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
	
	<div class="row" style="padding-left: 10px; padding-right: 10px;">
		<div class="col-xs-6" style="padding-right: 0; padding-left: 0">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
				<tbody>
					<tr>
						<th style=" background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Date</th>
						<th style=" background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Loc</th>
					</tr>
					<tr>
						<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:15px;" id="date">{{date("Y-m-d")}}</td>
						<td style="background-color: rgb(204,255,255); text-align: center;  font-size:15px;" id="loc">{{$loc}}</td>
					</tr>
					<tr>
						<th colspan="2" style="background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">Inspector QA</th>
					</tr>
					<tr>
						<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:15px; width: 30%;" id="op">-</td>
						<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 15px;" id="op2">-</td>
					</tr>
					
				</tbody>
			</table>
			<div class="col-xs-12" style="padding: 0px;padding-bottom: 15.6px">
				<div class="input-group" style="padding-top: 10px;">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
						<!-- <i class="glyphicon glyphicon-qrcode"></i> -->
					</div>
					<input type="text" style="text-align: center; border-color: black;font-size: 20px" class="form-control" id="material_number" name="material_number" placeholder="Material Number" required onkeyup="checkMaterial(this.value)">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
						<!-- <i class="glyphicon glyphicon-qrcode"></i> -->
					</div>
				</div>
			</div>
			<table class="table table-bordered" style="padding-top: 20px;padding-bottom: 0px">
				<tr>
					<td style="background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">
						Material Description
					</td>
					<td style="background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 15px;">
						Vendor
					</td>
				</tr>
				<tr>
					<td id="material_description" style="background-color: #6e81ff; text-align: center; color: #fff; font-size: 20px;">-
					</td>
					<td id="vendor" style="background-color: #6e81ff; text-align: center; color: #fff; font-size: 20px;">-
					</td>
				</tr>
			</table>
		</div>

		<div class="col-xs-6" style="padding-right: 0;">
			

			<div id="ngList2">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >#</th>
							<th style="width: 65%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >NG Name</th>
							<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >#</th>
							<th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Count</th>
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
							<td id="minus" onclick="minus({{$nomor+1}})" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">-</td>
							<td id="ng{{$nomor+1}}" style="font-size: 20px;">{{ $ng_list->ng_name }}</td>
							<td id="plus" onclick="plus({{$nomor+1}})" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">+</td>
							<td style="font-weight: bold; font-size: 45px; background-color: rgb(100,100,100); color: yellow;"><span id="count{{$nomor+1}}">0</span></td>
						</tr>
						<?php $no+=1; ?>
						@endforeach
					</tbody>
				</table>
			</div>

			<div class="col-xs-6" style="padding: 0px;padding-top: 10px;padding-right: 5px">
				<button class="btn btn-danger" id="btn_cancel" onclick="cancel()" style="font-size: 25px;font-weight: bold;width: 100%">
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


@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>

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
	});

	function cancelAll() {
		$('#material_number').val('');
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

	function plus(id){
		var count = $('#count'+id).text();
		if($('#material_number').val() == ""){
			audio_error.play();
			openErrorGritter('Error!', 'Masukkan Material Number.');
		}else{
			$('#count'+id).text(parseInt(count)+1);
		}
	}

	function minus(id){
		var count = $('#count'+id).text();
		if($('#material_number').val() == ""){
			audio_error.play();
			openErrorGritter('Error!', 'Masukkan Material Number.');
		}else{
			if(count > 0)
			{
				$('#count'+id).text(parseInt(count)-1);
			}
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