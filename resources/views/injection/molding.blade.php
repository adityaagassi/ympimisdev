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
	#moldingLog > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#moldingLogPasang > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#molding {
		height:150px;
		overflow-y: scroll;
	}

	#molding_pasang {
		height:150px;
		overflow-y: scroll;
	}

	#ngList2 {
		height:480px;
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
		<div class="col-xs-6" style="padding-right: 5px; padding-left: 0">
			
			<div id="op_molding">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th colspan="3" style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">Maintenance Molding (LEPAS) <span style="color: red" id="counter"></span></th>
							
						</tr>
						
					</thead>
					<tbody>
						<tr>
							<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:2vw; width: 30%;" id="op">-</td>
							<td colspan="2" style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;" id="op2">-</td>
						</tr>
						<tr>
							<td colspan="3" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(220,220,220);color: black;font-size: 20px;"><b>Molding List (LEPAS)</b></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="molding">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Mesin
							</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Part
							</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Color
							</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Total Shot
							</th>
						</tr>
					</thead>
					<tbody id="moldingLog">
					</tbody>
				</table>
			</div>

			<div style="padding-top: 5px;">
				<table style="width: 100%;" border="1">
					<tbody>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Mesin</td>
							<td id="mesin_lepas" style="width: 4%; font-size: 20px; font-weight: bold; background-color: rgb(100,100,100); color: white;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Part</td>
							<td id="part_lepas" style="width: 4%; font-size: 20px; font-weight: bold; background-color: rgb(100,100,100); color: white;"></td>
						</tr>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Color</td>
							<td id="color_lepas" style="width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Total Shot</td>
							<td id="total_shot_lepas" style="width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;"></td>
						</tr>
						<tr id="lepastime">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);"><div class="timerlepas">
					            <span class="hourlepas" id="hourlepas">00</span> h : <span class="minutelepas" id="minutelepas">00</span> m : <span class="secondlepas" id="secondlepas">00</span> s
					            <input type="hidden" id="lepas" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" value="0:00:00" required>
					            <input type="hidden" id="start_time_lepas" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" required>
					        	</div>
					    	</td>
						</tr>
						<tr id="reasonlepas">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 1.5vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);" id="reason">-
					    	</td>
						</tr>
						<tr id="lepasnote">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 1.5vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
								<span class="hourlepas" id="hourlepas">Note</span>
					    	</td>
						</tr>
						<tr id="lepasnote2">
							<td colspan="4" style="width: 100%; margin-top: 10px; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
								<textarea name="notelepas" id="notelepas" cols="35" rows="2" style="font-size: 1.2vw;"></textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div style="padding-top: 5px;">
				<button id="start_lepas" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="startLepas()" class="btn btn-success">MULAI LEPAS</button>
			</div>
			<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
				<button id="batal_lepas" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="cancelLepas()" class="btn btn-danger">BATAL</button>	
			</div>
			<div class="col-xs-6" style="padding-right: 0px;padding-left: 0px">
				<button id="finish_lepas" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="finishLepas()" class="btn btn-success">SELESAI LEPAS</button>
			</div>
		</div>

		<div class="col-xs-6" style="padding-right: 0; padding-left: 5px">
			
			<div id="op_molding">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th colspan="3" style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">Maintenance Molding (PASANG) <span style="color: red" id="counter"></span></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td id="mesin_pasang_pilihan" style="padding:0;background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:2vw; width: 30%;">
								<button class="btn btn-danger" onclick="getDataMesin(1);" id="#1">#1</button>
								<button class="btn btn-danger" onclick="getDataMesin(2);" id="#2">#2</button>
								<button class="btn btn-danger" onclick="getDataMesin(3);" id="#3">#3</button>
								<button class="btn btn-danger" onclick="getDataMesin(4);" id="#4">#4</button>
								<button class="btn btn-danger" onclick="getDataMesin(5);" id="#5">#5</button>
								<button class="btn btn-danger" onclick="getDataMesin(6);" id="#6">#6</button>
								<button class="btn btn-danger" onclick="getDataMesin(7);" id="#7">#7</button>
								<button class="btn btn-danger" onclick="getDataMesin(8);" id="#8">#8</button>
								<button class="btn btn-danger" onclick="getDataMesin(9);" id="#9">#9</button>
								<button class="btn btn-danger" onclick="getDataMesin(11);" id="#11">#11</button>
							</td>
							<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:2vw; width: 30%;" id="mesin_pasang" onclick="changeMesin()">-</td>
						</tr>
						<tr>
							<td colspan="3" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(220,220,220);color: black;font-size: 20px;"><b>Molding List (PASANG)</b><br>
							<span style="color: red"><i id="pesan_pasang" ></i></span></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="molding_pasang">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Product
							</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Part
							</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Mesin
							</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Last Counter
							</th>
						</tr>
					</thead>
					<tbody id="moldingLogPasang">
					</tbody>
				</table>
			</div>

			<div style="padding-top: 5px;">
				<table style="width: 100%;" border="1">
					<tbody>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Product</td>
							<td id="product_pasang" style="width: 4%; font-size: 20px; font-weight: bold; background-color: rgb(100,100,100); color: white;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Part</td>
							<td id="part_pasang" style="width: 4%; font-size: 20px; font-weight: bold; background-color: rgb(100,100,100); color: white;"></td>
						</tr>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Mesin</td>
							<td id="mesin_pasang_list" style="width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Last Counter</td>
							<td id="last_counter_pasang" style="width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;"></td>
						</tr>
						<tr id="pasangtime">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);"><div class="timerpasang">
					            <span class="hourpasang" id="hourpasang">00</span> h : <span class="minutepasang" id="minutepasang">00</span> m : <span class="secondpasang" id="secondpasang">00</span> s
					            <input type="hidden" id="pasang" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" value="0:00:00" required>
					            <input type="hidden" id="start_time_pasang" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" required>
					        	</div>
					    	</td>
						</tr>
						<tr id="pasangnote">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 1.5vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
								<span class="hourpasang" id="hourpasang">Note</span>
					    	</td>
						</tr>
						<tr id="pasangnote2">
							<td colspan="4" style="width: 100%; margin-top: 10px; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
								<textarea name="notepasang" id="notepasang" cols="35" rows="2" style="font-size: 1.2vw;"></textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div style="padding-top: 5px;">
				<button id="start_pasang" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="startPasang()" class="btn btn-success">MULAI PASANG</button>
				<!-- <input type="hidden" id="start_time_pasang"> -->
			</div>
			<div class="col-xs-6" style="padding-left: 0px;padding-right: 5px">
				<button id="batal_pasang" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="cancelPasang()" class="btn btn-danger">BATAL</button>
			</div>
			<div class="col-xs-6" style="padding-right: 0px;padding-left: 5px">
				<button id="finish_pasang" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="finishPasang()" class="btn btn-success">SELESAI PASANG</button>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row">
				<button id="change_operator" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="changeOperator()" class="btn btn-warning">GANTI OPERATOR</button>
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

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var intervalUpdate;

	jQuery(document).ready(function() {
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#reasonlepas').hide();
		$('#lepasnote').hide();
		$('#lepasnote2').hide();
		$('#lepastime').hide();
		$('#finish_lepas').hide();

		$('#mesin_pasang').hide();
		$('#pasangnote').hide();
		$('#pasangnote2').hide();
		$('#pasangtime').hide();
		$('#finish_pasang').hide();
		$('#batal_pasang').hide();
		$('#batal_lepas').hide();

		$('#operator').val('');
		// $('#tag').val('');
		// getDataMesinStatusLog(mesin);
		// getDataMesinShootLog();
		// chart();
		// $('#resetButton').hide();
		// $('#finishButton').hide();
		setInterval(setTime, 1000);
		intervalUpdate = setInterval(update_history_temp,60000);
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
						// $('#tag').focus();
						getMoldingLog();
						// getMoldingLogPasang();
						get_history_temp(result.employee.name);
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

	function getDataMesin(nomor_mesin) {
		$('#mesin_pasang').html('Mesin ' + nomor_mesin);
		$('#mesin_pasang_pilihan').hide();
		$('#mesin_pasang').show();
		getMoldingLogPasang('Mesin ' + nomor_mesin);
	}

	function changeMesin() {
		$('#mesin_pasang_pilihan').show();
		$('#mesin_pasang').hide();
		$('#moldingLogPasang').html("");
	}

	function changeOperator() {
		location.reload();
	}

	function getMoldingLog(){
		$.get('{{ url("get/injeksi/get_molding") }}',  function(result, status, xhr){
			if(result.status){

				var moldingLog = '';
				$('#moldingLog').html("");
				var no = 1;
				var color ="";
				$.each(result.datas, function(key, value) {
					if (no % 2 === 0 ) {
							color = 'style="background-color: #fffcb7;font-size: 20px;"';
						} else {
							color = 'style="background-color: #ffd8b7;font-size: 20px;"';
						}
					if (value.total_running_shot >= 15000) {
						color = 'style="background-color: #ff3030;font-size: 20px;color:white"';
					}
					moldingLog += '<tr onclick="fetchCount(\''+value.id+'\')">';
					moldingLog += '<td '+color+'>'+value.mesin+'</td>';
					moldingLog += '<td '+color+'>'+value.part+'</td>';
					moldingLog += '<td '+color+'>'+value.color+'</td>';
					moldingLog += '<td '+color+'>'+value.total_running_shot+'</td>';
					// moldingLog += '<td '+color+'>'+value.end_time+'</td>';
					
					moldingLog += '</tr>';				
				no++;
				});
				$('#moldingLog').append(moldingLog);

				// $('#statusLog').text(result.log[0].status);
				

				openSuccessGritter('Success!', result.message);
				
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function getMoldingLogPasang(mesin){
		var data = {
			mesin : mesin,
		}
		$.get('{{ url("get/injeksi/get_molding_pasang") }}', data, function(result, status, xhr){
			if(result.status){
				$('#pesan_pasang').html(result.pesan);

				var moldingLogPasang = '';
				$('#moldingLogPasang').html("");
				var no = 1;
				var color ="";
				$.each(result.datas, function(key, value) {
					if (no % 2 === 0 ) {
							color = 'style="background-color: #fffcb7;font-size: 25px;"';
						} else {
							color = 'style="background-color: #ffd8b7;font-size: 25px;"';
						}
					moldingLogPasang += '<tr onclick="fetchCountPasang(\''+value.id+'\')">';
					moldingLogPasang += '<td '+color+'>'+value.product+'</td>';
					moldingLogPasang += '<td '+color+'>'+value.part+'</td>';
					moldingLogPasang += '<td '+color+'>'+value.mesin+'</td>';
					moldingLogPasang += '<td '+color+'>'+value.last_counter+'</td>';
					// moldingLogPasang += '<td '+color+'>'+value.end_time+'</td>';
					
					moldingLogPasang += '</tr>';				
				no++;
				});
				$('#moldingLogPasang').append(moldingLogPasang);

				// $('#statusLog').text(result.log[0].status);
				

				// openSuccessGritter('Success!', result.message);
				
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function fetchCount(id){
		var data = {
			id : id,
		}
		$.get('{{ url("fetch/injeksi/fetch_molding") }}', data, function(result, status, xhr){
			if(result.status){
				$('#mesin_lepas').html(result.datas.mesin);
				$('#part_lepas').html(result.datas.part);
				$('#color_lepas').html(result.datas.color);
				$('#total_shot_lepas').html(result.datas.total_running_shot);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function fetchCountPasang(id){
		var data = {
			id : id,
		}
		$.get('{{ url("fetch/injeksi/fetch_molding_pasang") }}', data, function(result, status, xhr){
			if(result.status){
				$('#product_pasang').html(result.datas.product);
				$('#part_pasang').html(result.datas.part);
				$('#mesin_pasang_list').html(result.datas.mesin);
				$('#last_counter_pasang').html(result.datas.last_counter);

			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function startLepas() {
		if ($('#mesin_lepas').text() == '') {
			alert('Pilih Data Molding Yang Akan Dilepas.');
		}else{
			$('#reasonlepas').show();
			$('#lepasnote').show();
			$('#lepasnote2').show();
			$('#lepastime').show();
			$('#finish_lepas').show();
			$('#batal_lepas').show();
			$('#start_lepas').hide();
			store_history_temp('LEPAS');
		}
	}

	function cancelLepas() {
		var pic = $('#op2').text();
		var mesin = $('#mesin_lepas').text();
		var part = $('#part_lepas').text();
		var data = {
			pic : pic,
			mesin : mesin,
			part : part,
			type : 'LEPAS',
		}

		$.post('{{ url("index/injeksi/cancel_history_molding") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Setup Molding Canceled');
				getMoldingLog();
				$('#finish_lepas').hide();
				$('#lepasnote').hide();
				$('#lepasnote2').hide();
				$('#batal_lepas').hide();
				$('#lepastime').hide();
				$('#start_lepas').show();
				$('#secondlepas').html("00");
		        $('#minutelepas').html("00");
		        $('#hourlepas').html("00");
		        $('#part_lepas').html("");
		        $('#total_shot_lepas').html("");
		        $('#color_lepas').html("");
		        $('#mesin_lepas').html("");
		        $('#reasonlepas').hide();
			} else {
				audio_error.play();
				openErrorGritter('Error','Cancel Failed');
			}
		});
	}

	function finishLepas() {
		clearInterval(intervalUpdate);
		count = false;
		var detik = $('div.timerlepas span.secondlepas').text();
        var menit = $('div.timerlepas span.minutelepas').text();
        var jam = $('div.timerlepas span.hourlepas').text();
        var waktu = jam + ':' + menit + ':' + detik;
        $('#lepas').val(waktu);

		var pic = $('#op2').text();
		var mesin = $('#mesin_lepas').text();
		var part = $('#part_lepas').text();
		var color = $('#color_lepas').text();
		var total_shot = $('#total_shot_lepas').text();
		var start_time = $('#start_time_lepas').val();
		var end_time = getActualFullDate();
		var running_time = $('#lepas').val();
		var notelepas = $('#notelepas').val();
		var reason = $('#reason').text();
		// console.log(ng_name.join());
		// console.log(ng_count.join());

		if (reason == '-') {
			alert('Semua Data Harus Diisi');
		}else{
			var data = {
				mesin : mesin,
				type : 'LEPAS',
				pic : pic,
				reason : reason,
				part : part,
				color : color,
				total_shot : total_shot,
				start_time : start_time,
				end_time : end_time,
				running_time : running_time,
				notelepas : notelepas,
			}

			$.post('{{ url("index/injeksi/store_history_molding") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success','History Molding has been created');
					// reset();
					$('#finish_lepas').hide();
					$('#lepastime').hide();
					$('#reasonlepas').hide();
					$('#lepasnote').hide();
					$('#lepasnote2').hide();
					$('#batal_lepas').hide();
					$('#start_lepas').show();
					$('#secondlepas').html("00");
			        $('#minutelepas').html("00");
			        $('#hourlepas').html("00");
			        $('#part_lepas').html("");
			        $('#total_shot_lepas').html("");
			        $('#color_lepas').html("");
			        $('#mesin_lepas').html("");
			        $('#reasonlepas').hide();
					getMoldingLog();
				} else {
					audio_error.play();
					openErrorGritter('Error','Create History Molding Temp Failed');
				}
			});
		}
	}

	function startPasang() {
		if ($('#mesin_pasang_list').text() == '' || $('#mesin_pasang').text() == '-') {
			alert('Pilih Data Molding Yang Akan Dipasang.');
		}else if ($('#pesan_pasang').text() != '') {
			alert('Mesin Sudah Terpasang Molding. Silahkan Pilih Mesin Lain.');
		}
		else{
			$('#pasangnote').show();
			$('#pasangnote2').show();
			$('#pasangtime').show();
			$('#finish_pasang').show();
			$('#start_pasang').hide();
			$('#batal_pasang').show();
			store_history_temp('PASANG');
		}
	}

	function cancelPasang() {
		var pic = $('#op2').text();
		var mesin = $('#mesin_pasang').text();
		var data = {
			pic : pic,
			mesin : mesin,
			type : 'PASANG',
		}

		$.post('{{ url("index/injeksi/cancel_history_molding") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Setup Molding Canceled');
				$('#finish_pasang').hide();
				$('#pasangnote').hide();
				$('#pasangnote2').hide();
				$('#batal_pasang').hide();
				$('#pasangtime').hide();
				$('#start_pasang').show();
				$('#secondpasang').html("00");
		        $('#minutepasang').html("00");
		        $('#hourpasang').html("00");
		        $('#product_pasang').html("");
		        $('#part_pasang').html("");
		        $('#mesin_pasang_list').html("");
		        $('#last_counter_pasang').html("");
		        $('#moldingLogPasang').html("");
		        $('#mesin_pasang_pilihan').show();
		        $('#mesin_pasang').hide();
			} else {
				audio_error.play();
				openErrorGritter('Error','Cancel Failed');
			}
		});
	}

	function finishPasang() {
		clearInterval(intervalUpdate);
		count_pasang = false;
		var detik = $('div.timerpasang span.secondpasang').text();
        var menit = $('div.timerpasang span.minutepasang').text();
        var jam = $('div.timerpasang span.hourpasang').text();
        var waktu = jam + ':' + menit + ':' + detik;
        $('#pasang').val(waktu);

		var pic = $('#op2').text();
		var mesin = $('#mesin_pasang').text();
		var part = $('#part_pasang').text();
		var color = $('#product_pasang').text();
		var total_shot = $('#last_counter_pasang').text();
		var start_time = $('#start_time_pasang').val();
		var end_time = getActualFullDate();
		var running_time = $('#pasang').val();
		var notepasang = $('#notepasang').val();
		// console.log(ng_name.join());
		// console.log(ng_count.join());

		var data = {
			mesin : mesin,
			type : 'PASANG',
			pic : pic,
			part : part,
			color : color,
			total_shot : total_shot,
			start_time : start_time,
			end_time : end_time,
			running_time : running_time,
			notelepas : notepasang,
		}

		$.post('{{ url("index/injeksi/store_history_molding") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','History Molding has been created');
				// reset();
				$('#finish_pasang').hide();
				$('#pasangnote').hide();
				$('#pasangnote2').hide();
				$('#batal_pasang').hide();
				$('#pasangtime').hide();
				$('#start_pasang').show();
				$('#secondpasang').html("00");
		        $('#minutepasang').html("00");
		        $('#hourpasang').html("00");
		        $('#product_pasang').html("");
		        $('#part_pasang').html("");
		        $('#mesin_pasang_list').html("");
		        $('#last_counter_pasang').html("");
		        $('#moldingLogPasang').html("");
		        $('#mesin_pasang_pilihan').show();
		        $('#mesin_pasang').hide();
		        getMoldingLog();
			} else {
				audio_error.play();
				openErrorGritter('Error','Create History Molding Failed');
			}
		});
	}

	function store_history_temp(type) {
		var pic = $('#op2').text();
		var start_time = getActualFullDate();
		if (type === 'LEPAS') {
			var mesin = $('#mesin_lepas').text();
			var part = $('#part_lepas').text();
			var color = $('#color_lepas').text();
			var total_shot = $('#total_shot_lepas').text();
			if (parseInt(total_shot) < 15000) {
				$('#reason').html('LEPAS');
			}else if (parseInt(total_shot) >= 15000){
				$('#reason').html('MAINTENANCE');
			}
			$('#start_time_lepas').val(start_time);
			duration = 0;
			count = true;
			started_at = new Date(start_time);
			getMoldingLog();
		}
		else if (type === 'PASANG') {
			var mesin = $('#mesin_pasang').text();
			var color = $('#part_pasang').text();
			var part = $('#product_pasang').text();
			var total_shot = $('#last_counter_pasang').text();
			$('#start_time_pasang').val(start_time);
			duration = 0;
			count_pasang = true;
			started_at = new Date(start_time);
			getMoldingLogPasang(mesin);
		}
		// console.log(ng_name.join());
		// console.log(ng_count.join());

		if (mesin == '-' || mesin == null) {
			alert('Semua Data Harus Diisi');
		}else{
			var data = {
				mesin : mesin,
				type : type,
				pic : pic,
				part : part,
				color : color,
				total_shot : total_shot,
				start_time : start_time
			}

			$.post('{{ url("index/injeksi/store_history_temp") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success','History Molding Temp has been created');
					// reset();
				} else {
					audio_error.play();
					openErrorGritter('Error','Create History Molding Temp Failed');
				}
			});
		}
	}

	function get_history_temp(pic) {
		var data = {
			pic : pic
		}
		$.get('{{ url("index/injeksi/get_history_temp") }}',data,  function(result, status, xhr){
			if(result.status){
				if(result.datas.length != 0){
					$.each(result.datas, function(key, value) {				
						if (value.type == "LEPAS") {
							$('#mesin_lepas').html(value.mesin);
							$('#part_lepas').html(value.part);
							$('#color_lepas').html(value.color);
							$('#total_shot_lepas').html(value.total_shot);
							$('#notelepas').val(value.note);
							$('#start_time_lepas').val(value.start_time);
							if (parseInt(value.total_shot) < 15000) {
								$('#reason').html('LEPAS');
							}else if (parseInt(value.total_shot) >= 15000){
								$('#reason').html('MAINTENANCE');
							}
							duration = 0;
							count = true;
							started_at = new Date(value.start_time);
							$('#start_lepas').hide();
							$('#finish_lepas').show();
							$('#lepastime').show();
							$('#lepasnote').show();
							$('#reasonlepas').show();
							$('#lepasnote2').show();
							$('#batal_lepas').show();
						}
						else if(value.type == 'PASANG'){
							$('#mesin_pasang_pilihan').hide();
							$('#mesin_pasang').show();
							$('#mesin_pasang').html(value.mesin);
							$('#mesin_pasang_list').html(value.mesin);
							$('#part_pasang').html(value.color);
							$('#product_pasang').html(value.part);
							$('#last_counter_pasang').html(value.total_shot);
							$('#notepasang').val(value.note);
							$('#start_time_pasang').val(value.start_time);
							duration = 0;
							count_pasang = true;
							started_at = new Date(value.start_time);
							$('#start_pasang').hide();
							$('#finish_pasang').show();
							$('#pasangtime').show();
							$('#pasangnote').show();
							$('#pasangnote2').show();
							$('#batal_pasang').show();
						}
					});
				}
				openSuccessGritter('Success!', result.message);
				
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function update_history_temp() {
		var pic = $('#op2').text();
		var notelepas = $('#notelepas').val();
		var notepasang = $('#notepasang').val();

		var data = {
			pic : pic,
			note : notelepas,
			type : 'LEPAS'
		}

		$.post('{{ url("index/injeksi/update_history_temp") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','History Molding Temp has been updated');
				// reset();
			} else {
				audio_error.play();
				openErrorGritter('Error','Update History Molding Temp Failed');
			}
		});

		var data2 = {
			pic : pic,
			note : notepasang,
			type : 'PASANG'
		}

		$.post('{{ url("index/injeksi/update_history_temp") }}', data2, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','History Molding Temp has been updated');
				// reset();
			} else {
				audio_error.play();
				openErrorGritter('Error','Update History Molding Temp Failed');
			}
		});
	}

	var duration = 0;
	var count = false;
	var count_pasang = false;
	var started_at;
	function setTime() {
		if(count){
			$('#secondlepas').html(pad(diff_seconds(new Date(), started_at) % 60));
	        $('#minutelepas').html(pad(parseInt((diff_seconds(new Date(), started_at) % 3600) / 60)));
	        $('#hourlepas').html(pad(parseInt(diff_seconds(new Date(), started_at) / 3600)));
		}
		if(count_pasang){
			$('#secondpasang').html(pad(diff_seconds(new Date(), started_at) % 60));
	        $('#minutepasang').html(pad(parseInt((diff_seconds(new Date(), started_at) % 3600) / 60)));
	        $('#hourpasang').html(pad(parseInt(diff_seconds(new Date(), started_at) / 3600)));
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
