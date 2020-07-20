@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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

	<input type="hidden" id="loc" value="{{ $title }} {{$title_jp}} }">
	
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div class="col-xs-6" style="padding-right: 0; padding-left: 0">
			
			<div id="gauge">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">Total Shot Counter <span style="color: red" id="counter"></span></th>
							
						</tr>
						
					</thead>
					<tbody>
						<tr>
							<td style="width: 10px; background-color: rgb(220,220,220); padding:0;font-size: 20px;" id="gaugechart"></td>
						</tr>
						<tr>
							<td style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(220,220,220);color: black"><b id="statusLog">Running</b> - <b id="statusMesin">Mesin</b></td>
						</tr>
						<tr>							
							<td style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(204,255,255);color: black;"> <b id="colorpart"> - </b> </td>
						</tr>
						<tr>							
							<td style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(204,255,255);color: black;"><b id="modelpart"> - </b> </td>
						</tr>
						<tr>							
							<td style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(204,255,255);color: black;"><b id="moldingpart"> - </b> </td>
						</tr>
						<tr>							
							<td style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(255,255,102);"><div class="timerrunning">
					            <span class="hourrunning" id="hourrunning">00</span> h : <span class="minuterunning" id="minuterunning">00</span> m : <span class="secondrunning" id="secondrunning">00</span> s
					            <input type="hidden" id="running" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" value="0:00:00" required>
					        	</div>
					    	</td>
						</tr>
						
					</tbody>
				</table>
			</div>

			

			<!-- <div style="padding-top: 10px;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
					<thead>
						<tr>
							<th colspan="5" style="background-color: rgb(220,220,220); text-align: center; color: black; font-weight: bold; font-size:2vw;">Target</th>
						</tr>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Color</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Part</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Qty</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Actual</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">Diff</th>
						</tr>
					</thead>
					<tbody id="planTableBody">

					<tr>
					<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 1.5vw;" >IVORY</td>
					<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 1.5vw;" >YRS24BUK MIDDLE INJECTION</td>
					<td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 1.5vw;" >1200</td>
					<td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 1.5vw;" >1000</td>
					<td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 1.5vw;" >20</td>
					</tr>
						
					</tbody>
				</table>
			</div> -->

			<div id="ngList">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 20%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Status</th>
							<th style="width: 50%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Reason</th>
							<th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Start</th>
							<th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >End</th>
						</tr>
					</thead>
					<tbody id="MesinStatus">
												
					</tbody>
				</table>
			</div>

			
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
			<div>
				<center>
					<button id="conf1" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%" onclick="showScan()" class="btn btn-success">RUNNING</button>	
					<button style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%" onclick="showModalStatus('SETUP')" class="btn btn-info">SETUP</button>
					<button id="rework" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%" onclick="showModalStatus('IDDLE')" class="btn btn-warning">IDDLE</button>
					<button style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%" onclick="showModalStatus('TROUBLE')" class="btn btn-danger">TROUBLE</button>
					
				</center>
			</div>

			<div class="input-group" style="padding-top: 10px;">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
					<i class="glyphicon glyphicon-qrcode"></i>
				</div>
				<input type="text" style="text-align: center; border-color: black;" class="form-control" id="tag" name="tag" placeholder="Product RFID ..." required disabled>
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
					<i class="glyphicon glyphicon-qrcode"></i>
				</div>
			</div>
			<div class="input-group" style="padding-top: 10px;">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
					<i class="glyphicon glyphicon-qrcode"></i>
				</div>
				<input type="text" style="text-align: center; border-color: black;" class="form-control" id="tag_molding" name="tag_molding" placeholder="Molding RFID ..." required disabled>
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
					<i class="glyphicon glyphicon-qrcode"></i>
				</div>
			</div>
			<div style="padding-top: 5px;">
				<table style="width: 100%;" border="1">
					<tbody>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 25px; background-color: rgb(220,220,220);">Part</td>
							<td id="model" style="width: 4%; font-size: 25px; font-weight: bold; background-color: rgb(100,100,100); color: yellow;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 25px; background-color: rgb(220,220,220);">Qty</td>
							<td id="key" style="width: 4%; font-weight: bold; font-size: 25px; background-color: rgb(100,100,100); color: yellow;"></td>
							<input type="hidden" id="part">
							<input type="hidden" id="color">
							<input type="hidden" id="start_time">
							<input type="hidden" id="employee_id">
						</tr>
					</tbody>
				</table>
			</div>

			<div style="padding-top: 5px;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
					<thead>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;" colspan="2">Operator</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:2vw; width: 30%;" id="op">-</td>
							<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;" id="op2">-</td>
						</tr>
						<tr>
							<td><p class="center-block" style="color: white;font-size: 2vw;">Perolehan</p></td>
							<td style="">
								<input type="number" class="pull-left" name="total_shot" style="width: 10vw;height: 4.5vw;font-size: 1.5vw;text-align: center;vertical-align: middle;" id="total_shot" placeholder="Total Shot" disabled>
								<input type="number" class="pull-left" name="running_shot" style="width: 10vw;height: 4.5vw;font-size: 1.5vw;text-align: center;vertical-align: middle;" id="running_shot" placeholder="Running Shot">
								<button class="btn btn-success btn-lg pull-right" style='padding-right:10px;padding-left:10px;margin-right: 10px;margin-top: 10px;' onclick="finishProcess()" id="finishButton">Finish</button>
								<button class="btn btn-warning btn-lg pull-right" style='padding-right:10px;padding-left:10px;margin-right: 10px;margin-top: 10px;' onclick="startProcess()" id="startButton">Start</button>
								<button class="btn btn-danger btn-lg pull-right" style='padding-right:10px;padding-left:10px;margin-right: 10px;margin-top: 10px;' onclick="cancelProcess()" id="cancelButton">Cancel</button>
								<button class="btn btn-danger btn-lg pull-right" style='padding-right:10px;padding-left:10px;margin-right: 10px;margin-top: 10px;' onclick="cancelProcess()" id="resetButton">Reset</button>
							</td>
						</tr>
					</tbody>
				</table>
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

<div class="modal fade" id="modalStatus">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><center> <b id="statusa" style="font-size: 2vw"></b> </center>
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label for="exampleInputEmail1">Reason</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="Reason" placeholder="Reason" required><br>
						<button class="btn btn-warning pull-left" data-dismiss="modal">Cancel</button>
						<button class="btn btn-success pull-right" onclick="saveStatus()">Confirm</button>
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

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {

		setInterval(setTime, 1000);
		setInterval(update_ng_temp,60000);
	});

	var duration = 0;
	var count = false;
	var started_at;
	function setTime() {
		if(count){
			$('#secondrunning').html(pad(diff_seconds(new Date(), started_at) % 60));
	        $('#minuterunning').html(pad(parseInt((diff_seconds(new Date(), started_at) % 3600) / 60)));
	        $('#hourrunning').html(pad(parseInt(diff_seconds(new Date(), started_at) / 3600)));
		}
	}

	function pad(val) {
		var valString = val + "";
		if (valString.length < 2) {
			return "0" + valString;
		} else {
			return valString;
		}
	}

	function diff_seconds(dt2, dt1){
		var diff = (dt2.getTime() - dt1.getTime()) / 1000;
		return Math.abs(Math.round(diff));
	}

	jQuery(document).ready(function() {
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#operator').val('');
		$('#tag').val('');
		$('#tag_molding').val('');
		var mesin = "{{substr($name,10)}}";
		getDataMesinStatusLog(mesin);
		// getDataMesinShootLog();
		// chart();
		$('#resetButton').hide();
		$('#finishButton').hide();
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
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#op').html(result.employee.employee_id);
						$('#op2').html(result.employee.name);
						$('#employee_id').val(result.employee.employee_id);
						// fillResult(result.employee.employee_id);
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

	$('#tag').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag").val().length >= 7){
				scanTag($("#tag").val());
			}
			else{
				openErrorGritter('Error!', 'RFID Product Tag Invalid');
				audio_error.play();
				$("#tag").val("");
				$("#tag").focus();
			}			
		}
	});

	$('#tag_molding').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_molding").val().length >= 7){
				scanTagMolding($("#tag_molding").val(),$("#part").val());
			}
			else{
				openErrorGritter('Error!', 'RFID Molding Tag Invalid');
				audio_error.play();
				$("#tag_molding").val("");
				$("#tag_molding").focus();
			}			
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function getDataMesinShootLog(){
		
		$.get('{{ url("get/getDataMesinShootLog") }}',  function(result, status, xhr){
			if(result.status){
				var BodyMESIN = '';
				// $('#planTableBody').html("");
				$.each(result.target, function(key, value) {
					BodyMESIN += '<tr>';
					BodyMESIN += '<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 1vw;" >'+value.color+'</td>';
					BodyMESIN += '<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 1vw;" >'+value.part+'</td>';
					BodyMESIN += '<td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 1vw;" >'+value.target+'</td>';
					BodyMESIN += '<td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 1vw;" >'+value.act+'</td>';
					BodyMESIN += '<td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 1vw;" >'+value.minus+'</td>';
					BodyMESIN += '</tr>';				
				
				});
				$('#planTableBody').append(BodyMESIN);

				openSuccessGritter('Success!', result.message);
				
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function getDataMesinStatusLog(mesin){
		var data = {
			mesin:mesin
		} 
		$.get('{{ url("get/getDataMesinStatusLog") }}',data,  function(result, status, xhr){
			if(result.status){

				var BodyMESIN2 = '';
				$('#MesinStatus').html("");
				var no = 1;
				var color ="";
				$.each(result.log, function(key, value) {
					if (no % 2 === 0 ) {
							color = 'style="background-color: #fffcb7;font-size: 20px;"';
						} else {
							color = 'style="background-color: #ffd8b7;font-size: 20px;"';
						}
					BodyMESIN2 += '<tr>';
					BodyMESIN2 += '<td  '+color+'>'+value.status+'</td>';
					BodyMESIN2 += '<td '+color+'>'+value.reason+'</td>';
					BodyMESIN2 += '<td '+color+'>'+value.start_time+'</td>';
					BodyMESIN2 += '<td '+color+'>'+value.end_time+'</td>';
					
					BodyMESIN2 += '</tr>';				
				no++;
				});
				$('#MesinStatus').append(BodyMESIN2);

				$('#statusLog').text(result.log[0].status);
				$('#statusMesin').text(mesin);
				

				openSuccessGritter('Success!', result.message);
				
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function showModalStatus(status) {
		$("#modalStatus").modal('show');
		$("#statusa").text(status);
	}

	function showScan() {
		if($('#tag').val() == ""){
			$("#tag").removeAttr('disabled');
			$("#tag").val("");
			$("#tag").focus();
		}
		else{
			var model = $('#model').text();
			$("#statusa").html('RUNNING');
			$("#Reason").val('Running Process '+model);
			saveStatus();
		}
	}

	

	function saveStatus() {
		var statusa = $("#statusa").text();
		var Reason = $("#Reason").val();
		// timerrunning.stop();
		var mesin = "{{substr($name,10)}}";

		var data = {
			mesin:mesin,
			statusa:statusa,
			Reason:Reason
		} 

		if (Reason === "") {
			alert('Reason Must be Filled')
		}else{
			$.get('{{ url("input/statusmesin") }}', data, function(result, status, xhr){
			if(result.status){

				$("#statusa").text('');
				$("#Reason").val('');
				$("#modalStatus").modal('hide');
				openSuccessGritter('Success!', result.message);
				getDataMesinStatusLog(mesin);
				getDataMesinShootLog();
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
		}

		
	}

	function getStatusMesin() {
		
			$.get('{{ url("get/statusmesin") }}', data, function(result, status, xhr){
			if(result.status){

				
				openSuccessGritter('Success!', result.message);
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function plus(id){
		var count = $('#count'+id).text();
		if($('#key').text() != ""){
			$('#count'+id).text(parseInt(count)+1);
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', 'Scan material first.');
			$("#tag").val("");
			$("#tag").focus();
		}
	}

	function minus(id){
		var count = $('#count'+id).text();
		if($('#key').text() != ""){
			if(count > 0)
			{
				$('#count'+id).text(parseInt(count)-1);
			}
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', 'Scan material first.');
			$("#tag").val("");
			$("#tag").focus();
			$('#tag').blur();
		}
	}

	var total_running_shot = 0;
	function scanTag(tag){
		$('#tag_molding').focus();
		$('#tag').prop('disabled', true);
		var data = {
			serialNumber:tag,
		}
		// console.log(tag);
		$.post('{{ url("scan/part_injeksi") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				// console.log(result.part);
				$.each(result.part, function(key, value) {
					$('#model').html(value.part_name);
					$('#key').html(value.capacity);
					$('#colorpart').html(value.part_code +' - '+ value.color);
					$('#part').val(value.part_code);
					$('#color').val(value.color);
					$('#modelpart').html(value.part_name);
					$("#statusa").html('RUNNING');
					$("#Reason").val('Running Process '+value.part_name);
					saveStatus();
					get_ng_temp();
					$('#tag_molding').prop('disabled', false);
					$('#tag_molding').focus();
				});
				// $('#material_tag').val(result.middle_inventory.tag);
				// $('#material_number').val(result.middle_inventory.material_number);
				// $('#material_quantity').val(result.middle_inventory.quantity);
				// $('#started_at').val(result.started_at);
			}
			else{
				$('#tag').prop('disabled', false);
				openErrorGritter('Error!', result.message);
				audio_error.play();
				$("#tag").val("");
				$("#tag").focus();
			}
		});
	}

	function scanTagMolding(tag,part){
		// $('#tag_molding').focus();
		$('#tag_molding').prop('disabled', true);
		var data = {
			serialNumber:tag,
			part:part,
		}
		// console.log(tag);
		$.post('{{ url("scan/part_molding") }}', data, function(result, status, xhr){
			if(result.molding.length > 0){
				openSuccessGritter('Success!', result.message);
				// console.log(result.part);
				$.each(result.molding, function(key, value) {
					// $('#model').html(value.part_name);
					// $('#key').html(value.capacity);
					// $('#colorpart').html(value.part_code +' - '+ value.color);
					// $('#part').val(value.part_code);
					// $('#color').val(value.color);
					$('#moldingpart').html(value.part);
					get_molding_log(tag);
					// $('#tag_molding').prop('disabled', false);
					// $('#tag_molding').focus();
				});
				// $('#material_tag').val(result.middle_inventory.tag);
				// $('#material_number').val(result.middle_inventory.material_number);
				// $('#material_quantity').val(result.middle_inventory.quantity);
				// $('#started_at').val(result.started_at);
			}
			else{
				$('#tag_molding').prop('disabled', false);
				openErrorGritter('Error!', 'Molding Tidak Sesuai');
				audio_error.play();
				$("#tag_molding").val("");
				$("#tag_molding").focus();
			}
		});
	}

	function store_ng_temp() {
		var mesin = "{{substr($name,10)}}";
		var pic = $('#op2').text();
		var tag = $('#tag').val();
		var tag_molding = $('#tag_molding').val();
		var part_name = $('#modelpart').text();
		var color = $('#color').val();
		var part_code = $('#part').val();
		var start_time = getActualFullDate();
		var running_shot = $('#running_shot').val();
		var capacity = $('#key').text();
		var ng_name = [];
		var ng_count = [];
		var jumlah_ng = '{{$nomor+1}}';
		for (var i = 1; i <= jumlah_ng; i++ ) {
			if($('#count'+i).text() != 0){
				ng_name.push($('#ng'+i).text());
				ng_count.push($('#count'+i).text());
			}
		}
		$('#start_time').val(start_time);
		// console.log(ng_name.join());
		// console.log(ng_count.join());

		var data = {
			mesin : mesin,
			tag : tag,
			tag_molding : tag_molding,
			pic : pic,
			part_name : part_name,
			part_code : part_code,
			color : color,
			capacity : capacity,
			start_time : start_time,
			running_shot : running_shot,
			ng_name : ng_name.join(),
			ng_count : ng_count.join()
		}

		$.post('{{ url("index/injeksi/store_ng_temp") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Injection Temp has been created');
				// reset();
				duration = 0;
				count = true;
				started_at = new Date(result.start_time);
			} else {
				audio_error.play();
				openErrorGritter('Error','Create Injection Temp Failed');
			}
		});
	}

	function update_ng_temp() {
		var mesin = "{{substr($name,10)}}";
		var ng_name = [];
		var ng_count = [];
		var jumlah_ng = '{{$nomor+1}}';
		for (var i = 1; i <= jumlah_ng; i++ ) {
			if($('#count'+i).text() != 0){
				ng_name.push($('#ng'+i).text());
				ng_count.push($('#count'+i).text());
			}
		}
		var running_shot = $('#running_shot').val();
		var total_shot = $('#total_shot').val();
		var tag = $('#tag').val();
		var tag_molding = $('#tag_molding').val();
		// console.log(ng_name.join());
		// console.log(ng_count.join());

		var data = {
			mesin : mesin,
			ng_name : ng_name.join(),
			ng_count : ng_count.join(),
			running_shot:running_shot,
			total_shot:total_shot,
			tag:tag,
			tag_molding:tag_molding
		}

		$.post('{{ url("index/injeksi/update_ng_temp") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Injection Temp has been updated');
				// reset();
				$('#total_shot').val(result.jumlah_shot);
				$('#running_shot').val('');
			} else {
				audio_error.play();
				openErrorGritter('Error','Update Injection Temp Failed');
			}
		});
	}

	function get_molding_log(tag) {
		var data = {
			tag : tag
		}
		$.get('{{ url("index/injeksi/get_molding_log") }}',data,  function(result, status, xhr){
			if(result.status){
				if(result.datas.length != 0){
					// $.each(result.datas, function(key, value) {				
						
					// });
					// total_running_shot = value.total_running_shot;

				}
				chart(result.last_counter);
				if (parseInt(result.last_counter) >= 15000) {
					$('#counter').html('<br><b><i>Molding Harus di Maintenance!</i></b>');
					$('#startButton').hide();
				}
				openSuccessGritter('Success!', result.message);
				
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function chart(total_running_shot) {
		var total_shot = parseInt(total_running_shot);
				Highcharts.chart('gaugechart', {			        
			    chart: {
			        type: 'gauge',
			        plotBackgroundColor: null,
			        plotBackgroundImage: null,
			        plotBorderWidth: 0,
			        plotShadow: false,
			        height: 250
			    },

			    title: {
			        text: ''
			    },

			    pane: {
			        startAngle: -150,
			        endAngle: 150,
			        background: [{
			            backgroundColor: {
			                linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
			                stops: [
			                    [0, '#FFF'],
			                    [1, '#333']
			                ]
			            },
			            borderWidth: 0,
			            outerRadius: '109%'
			        }, {
			            backgroundColor: {
			                linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
			                stops: [
			                    [0, '#333'],
			                    [1, '#FFF']
			                ]
			            },
			            borderWidth: 1,
			            outerRadius: '107%'
			        }, {
			            // default background
			        }, {
			            backgroundColor: '#DDD',
			            borderWidth: 0,
			            outerRadius: '105%',
			            innerRadius: '103%'
			        }]
			    },

			    // the value axis
			    yAxis: {
			        min: 0,
			        max: 20000,

			        minorTickInterval: 'auto',
			        minorTickWidth: 1,
			        minorTickLength: 10,
			        minorTickPosition: 'inside',
			        minorTickColor: '#666',

			        tickPixelInterval: 30,
			        tickWidth: 2,
			        tickPosition: 'inside',
			        tickLength: 10,
			        tickColor: '#666',
			        labels: {
			            step: 2,
			            rotation: 'auto'
			        },
			        title: {
			            text: 'Shots'
			        },
			        plotBands: [{
			            from: 0,
			            to: 12000,
			            color: '#55BF3B' // green
			        }, {
			            from: 12000,
			            to: 15000,
			            color: '#DDDF0D' // yellow
			        }, {
			            from: 15000,
			            to: 20000,
			            color: '#DF5353' // red
			        }]
			    },
			    tooltip: {
					// headerFormat: '<span>Total Shots</span><br/>',
					pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
				},
			    series: [{
			        name: 'Total Shots',
			        data: [total_shot],
			        tooltip: {
			            valueSuffix: ' shots'
			        }
			    }]

			});
	}

	function get_ng_temp() {
		var mesin = "{{substr($name,10)}}";
		var data = {
			mesin : mesin
		}
		$.get('{{ url("index/injeksi/get_ng_temp") }}',data,  function(result, status, xhr){
			if(result.status){
				if(result.datas.length != 0){
					$.each(result.datas, function(key, value) {				
						$('#model').html(value.part_name);
						$('#tag').val(value.rfid);
						$('#key').html(value.capacity);
						$('#colorpart').html(value.part_code + ' - ' +value.color);
						$('#part').val(value.part_code);
						$('#color').val(value.color);
						$('#modelpart').html(value.part_name);
						$('#total_shot').val(value.running_shot);
						get_molding_log(value.rfid_molding);
						$('#tag_molding').val(value.rfid_molding);
						$('#tag_molding').prop('disabled', true);
						scanTagMolding(value.rfid_molding,value.part_code);
						// var ng_count = [];
						// console.log(ng_count);
						if (value.ng_name != null) {
							var ng_name = value.ng_name.split(',');
							var ng_count = value.ng_count.split(',');
							var jumlah_ng = '{{$nomor+1}}';
							for (var i = 1; i <= jumlah_ng; i++ ) {
								for (var j = 0; j < ng_name.length; j++ ) {
									if($('#ng'+i).text() == ng_name[j]){
										$('#count'+i).html(ng_count[j]);
									}
								}
							}
						}
						duration = 0;
						count = true;
						started_at = new Date(value.start_time);
						// console.log(started_at);
						$('#start_time').val(value.start_time);
					});
					$('#finishButton').show();
					$('#startButton').hide();
				}
				openSuccessGritter('Success!', result.message);
				
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function delete_ng_temp() {
		var mesin = "{{substr($name,10)}}";
		var data = {
			mesin : mesin
		}
		$.post('{{ url("index/injeksi/delete_ng_temp") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Injection Temp has been deleted');
				// reset();
			} else {
				audio_error.play();
				openErrorGritter('Error','Delete Injection Temp Failed');
			}
		});
	}

	function startProcess() {
		$('#finishButton').show();
		$('#startButton').hide();
		// timerrunning.start(1000);
		store_ng_temp();
	}

	function finishProcess() {
		update_ng_temp();
		count = false;
		var detik = $('div.timerrunning span.secondrunning').text();
        var menit = $('div.timerrunning span.minuterunning').text();
        var jam = $('div.timerrunning span.hourrunning').text();
        var waktu = jam + ':' + menit + ':' + detik;
        $('#running').val(waktu);
		var mesin = "{{substr($name,10)}}";
		var pic = $('#op2').text();
		var part = $('#part').val();
		var moldingpart = $('#moldingpart').text();
		var color2 = $('#color').val();
		var tag = $('#tag').val();
		var tag_molding = $('#tag_molding').val();
		var part_name = $('#modelpart').text();
		var color = $('#colorpart').text();
		var running_time = $('#running').val();
		var running_shot = $('#total_shot').val();
		var start_time = $('#start_time').val();
		var end_time = getActualFullDate();
		var ng_name = [];
		var ng_count = [];
		var ng_counting = 0;
		var jumlah_ng = '{{$nomor+1}}';
		for (var i = 1; i <= jumlah_ng; i++ ) {
			if($('#count'+i).text() != 0){
				ng_name.push($('#ng'+i).text());
				ng_count.push($('#count'+i).text());
				ng_counting = ng_counting + parseInt($('#count'+i).text());
			}
		}
		// console.log(ng_name.join());
		// console.log(ng_count.join());

		var data = {
			mesin : mesin,
			pic : pic,
			part_name : part_name,
			color : color,
			running_shot : running_shot,
			start_time : start_time,
			end_time : end_time,
			running_time : running_time,
			ng_name : ng_name.join(),
			ng_count : ng_count.join()
		}

		var data2 = {
			mesin : mesin,
			pic : pic,
			tag_molding : tag_molding,
			part : moldingpart,
			color : color2,
			start_time : start_time,
			end_time : end_time,
			running_shot : running_shot,
			ng_name : ng_name.join(),
			ng_count : ng_count.join(),
			ng_counting:ng_counting
		}

		if(running_shot == ''){
			alert('Semua Data Harus Diisi');
			$('#running_shot').focus();
		}else{
			$.post('{{ url("index/injeksi/store_molding_log") }}', data2, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success','New Molding Log has been created');
				} else {
					audio_error.play();
					openErrorGritter('Error','Create Molding Log Data Failed');
				}
			});

			$.post('{{ url("index/injeksi/store_ng") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success','New Injection Data has been created');
					// reset();
					$("#statusa").html('FINISH');
					$("#Reason").val('Finish Process ' + part_name);
					saveStatus();
					$('#startButton').hide();
					$('#cancelButton').hide();
					$('#finishButton').hide();
					$('#resetButton').show();
					delete_ng_temp();
				} else {
					audio_error.play();
					openErrorGritter('Error','Create Injection Data Failed');
				}
			});
		}
	}

	function cancelProcess() {
		window.location.href = "{{ url('index/opmesin') }}";
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

	Highcharts.theme = {
		colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
		'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: {
				linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
				stops: [
				[0, '#2a2a2b'],
				[1, '#3e3e40']
				]
			},
			style: {
				fontFamily: 'sans-serif'
			},
			plotBorderColor: '#606063'
		},
		title: {
			style: {
				color: '#E0E0E3',
				textTransform: 'uppercase',
				fontSize: '20px'
			}
		},
		subtitle: {
			style: {
				color: '#E0E0E3',
				textTransform: 'uppercase'
			}
		},
		xAxis: {
			gridLineColor: '#707073',
			labels: {
				style: {
					color: '#E0E0E3'
				}
			},
			lineColor: '#707073',
			minorGridLineColor: '#505053',
			tickColor: '#707073',
			title: {
				style: {
					color: '#A0A0A3'

				}
			}
		},
		yAxis: {
			gridLineColor: '#707073',
			labels: {
				style: {
					color: '#0f0f0f'
				}
			},
			lineColor: '#707073',
			minorGridLineColor: '#505053',
			tickColor: '#707073',
			tickWidth: 1,
			title: {
				style: {
					color: '#A0A0A3'
				}
			}
		},
		tooltip: {
			backgroundColor: 'rgba(0, 0, 0, 0.85)',
			style: {
				color: '#F0F0F0'
			}
		},
		plotOptions: {
			series: {
				dataLabels: {
					color: 'white'
				},
				marker: {
					lineColor: '#333'
				}
			},
			boxplot: {
				fillColor: '#505053'
			},
			candlestick: {
				lineColor: 'white'
			},
			errorbar: {
				color: 'white'
			}
		},
		legend: {
			itemStyle: {
				color: '#E0E0E3'
			},
			itemHoverStyle: {
				color: '#FFF'
			},
			itemHiddenStyle: {
				color: '#606063'
			}
		},
		credits: {
			style: {
				color: '#666'
			}
		},
		labels: {
			style: {
				color: '#707073'
			}
		},

		drilldown: {
			activeAxisLabelStyle: {
				color: '#F0F0F3'
			},
			activeDataLabelStyle: {
				color: '#F0F0F3'
			}
		},

		navigation: {
			buttonOptions: {
				symbolStroke: '#DDDDDD',
				theme: {
					fill: '#505053'
				}
			}
		},

		rangeSelector: {
			buttonTheme: {
				fill: '#505053',
				stroke: '#000000',
				style: {
					color: '#CCC'
				},
				states: {
					hover: {
						fill: '#707073',
						stroke: '#000000',
						style: {
							color: 'white'
						}
					},
					select: {
						fill: '#000003',
						stroke: '#000000',
						style: {
							color: 'white'
						}
					}
				}
			},
			inputBoxBorderColor: '#505053',
			inputStyle: {
				backgroundColor: '#333',
				color: 'silver'
			},
			labelStyle: {
				color: 'silver'
			}
		},

		navigator: {
			handles: {
				backgroundColor: '#666',
				borderColor: '#AAA'
			},
			outlineColor: '#CCC',
			maskFill: 'rgba(255,255,255,0.1)',
			series: {
				color: '#7798BF',
				lineColor: '#A6C7ED'
			},
			xAxis: {
				gridLineColor: '#505053'
			}
		},

		scrollbar: {
			barBackgroundColor: '#808083',
			barBorderColor: '#808083',
			buttonArrowColor: '#CCC',
			buttonBackgroundColor: '#606063',
			buttonBorderColor: '#606063',
			rifleColor: '#FFF',
			trackBackgroundColor: '#404043',
			trackBorderColor: '#404043'
		},

		legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
		background2: '#505053',
		dataLabelsColor: '#B0B0B3',
		textColor: '#C0C0C0',
		contrastTextColor: '#F0F0F3',
		maskColor: 'rgba(255,255,255,0.3)'
	};
	Highcharts.setOptions(Highcharts.theme);
</script>
@endsection