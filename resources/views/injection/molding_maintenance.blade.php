@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
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
	#moldingMaster > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#molding {
		height:410px;
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="loc" value="{{ $title }} {{$title_jp}} }">
	<input type="hidden" id="maintenance_code" value="{{ $title }} {{$title_jp}} }">
	
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div class="col-xs-6" style="padding-right: 10px; padding-left: 0">
			
			<div id="op_molding">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th colspan="3" style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">PIC Molding <span style="color: red" id="counter"></span></th>
							
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size:16px; width: 1%;" id="op_0">-</td>
							<td colspan="" style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 16px;width: 1%" id="op_1">-</td>
							<td colspan="" style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 16px;width: 1%" id="op_2">-</td>
						</tr>
						<!-- <tr>
							<td colspan="3" style="width: 100%; margin-top: 10px; font-size: 15px; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(220,220,220);color: black;font-size: 15px;"><b>Molding List</b></td>
						</tr> -->
					</tbody>
				</table>
			</div>
			<table style="width: 100%;border: '1'">
				<tbody>
						<tr>
							<td colspan="4" style="width: 1%; font-weight: bold; font-size: 15px; background-color: rgb(220,220,220);display: none">Status</td>
						</tr>
						<tr>
							<td colspan="4" id="status" style="border:1px solid black;width: 4%; font-size: 2vw; font-weight: bold; background-color: rgb(50, 50, 50); color: yellow;display: none">-</td>
						</tr>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Product</td>
							<td id="product" style="border:1px solid black;width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Part</td>
							<td id="part" style="border:1px solid black; width: 4%; font-size: 20px; font-weight: bold; background-color: rgb(100,100,100); color: white;"></td>
						</tr>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Mesin</td>
							<td id="mesin" style="border:1px solid black; width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;"></td>
							<td style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Last Counter</td>
							<td id="last_counter" style="border:1px solid black; width: 4%; font-weight: bold; font-size: 20px; background-color: rgb(100,100,100); color: white;">-</td>
						</tr>
						<tr id="perbaikantime">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: black; width: 23%;background-color: rgb(204,255,255);"><div class="timerperbaikan">
					            <span class="hourperbaikan" id="hourperbaikan">00</span> h : <span class="minuteperbaikan" id="minuteperbaikan">00</span> m : <span class="secondperbaikan" id="secondperbaikan">00</span> s
					            <input type="hidden" id="perbaikan" class="timepicker" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" value="0:00:00" required>
					            <input type="hidden" id="start_time_perbaikan" style="width: 100%; height: 30px; font-size: 15px; text-align: center;" required>
					        	</div>
					    	</td>
						</tr>
				</tbody>
			</table>
			<button id="start_perbaikan" style="width: 100%; margin-top: 5px; font-size: 30px; padding-top:5px;padding-bottom: 5px; font-weight: bold; border-color: black; color: white; width: 100%" onclick="startPerbaikan()" class="btn btn-success">MULAI PERBAIKAN</button>
				<button id="pause" style="width: 100%; margin-top: 10px; font-size: 30px; padding-top: 5px;padding-bottom: 5px; font-weight: bold; border-color: black; color: white; width: 100%" onclick="pause()" class="btn btn-warning">PAUSE</button>
				<button id="change_operator" style="width: 100%; margin-top: 10px; font-size: 30px; padding-top: 5px;padding-bottom: 5px; font-weight: bold; border-color: black; color: white; width: 100%" onclick="location.reload()" class="btn btn-info">GANTI OPERATOR</button>
		</div>

		<div class="col-xs-6" style="padding-right: 0; padding-left: 10px">
			<div>
				<table style="width: 100%;" border="1">
					<tbody>
						<tr id="perbaikannote">
							<td colspan="4" style="width: 100%; margin-top: 10px; font-size: 1.5vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
								<span class="hourperbaikan" id="hourperbaikan">Note</span>
					    	</td>
						</tr>
						<tr id="perbaikannote2">
							<td colspan="4" style="width: 100%; margin-top: 10px; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;color: black;background-color: rgb(220,220,220);">
								<textarea name="noteperbaikan" id="noteperbaikan" style="width:100%;height:230px;font-size: 1.2vw;text-align: center;vertical-align: middle;" placeholder="TULISKAN CATATAN DI SINI"></textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div style="padding-top: 5px;">
				<button id="finish_perbaikan" style="width: 100%; margin-top: 5px; font-size: 30px;padding-top: 5px padding-bottom: 5px;font-weight: bold; border-color: black; color: white; width: 100%" onclick="finishPerbaikan()" class="btn btn-danger">SELESAI PERBAIKAN</button>
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
						<label for="exampleInputEmail1">Employee ID 1</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator_0" placeholder="Scan ID Card">
						<input class="form-control" style="width: 100%; text-align: center;" type="hidden" id="employee_id_0" placeholder="Scan ID Card">

						<label for="exampleInputEmail1">Employee ID 2</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator_1" placeholder="Scan ID Card">
						<input class="form-control" style="width: 100%; text-align: center;" type="hidden" id="employee_id_1" placeholder="Scan ID Card">

						<label for="exampleInputEmail1">Employee ID 3</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator_2" placeholder="Scan ID Card">
						<input class="form-control" style="width: 100%; text-align: center;" type="hidden" id="employee_id_2" placeholder="Scan ID Card">

					</div>
					<div class="col-xs-12">
						<div class="row">
							<button id="btn_operator" onclick="saveOperator()" class="btn btn-success btn-block" style="font-weight: bold;font-size: 20px">
								CONFIRM
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalMolding" style="overflow-y: auto;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header"><center> <b id="statusa" style="font-size: 2vw"></b> </center>
				<div class="modal-body table-responsive no-padding">
					<div class="col-xs-12" id="molding_choice" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12">
								<center><span style="font-weight: bold; font-size: 18px;">Pilih Molding</span></center>
							</div>
							<div class="col-xs-12" id="molding_btn">
							</div>
						</div>
					</div>
					<div class="col-xs-12" id="molding_fix" style="padding-top: 20px">
						<div class="row">
							<div class="col-xs-12">
								<center><span style="font-weight: bold; font-size: 18px;">Pilih Molding</span></center>
							</div>
							<div class="col-xs-12" style="padding-top: 10px">
								<button id="molding_fix2" style="width: 100%;font-size: 20px;font-weight: bold;" onclick="changeMolding2()">
									MOLDING
								</button>
							</div>
						</div>
					</div>
					<input type="hidden" id="id_molding">
					<div class="col-xs-12" style="padding-top: 20px">
						<div class="modal-footer row">
							<button onclick="saveMolding()" class="btn btn-success btn-block pull-right" style="font-size: 30px;font-weight: bold;">
								CONFIRM
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalStatus">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center> <b style="font-size: 2vw">PAUSE</b> </center>
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<center><label for="">Reason</label></center>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="reasonPause" placeholder="Reason" required><br>
					</div>
					<div class="col-xs-6" style="padding-left: 0px">
						<button class="btn btn-danger btn-block" style="font-weight: bold;font-size: 20px" data-dismiss="modal">Cancel</button>
					</div>
					<div class="col-xs-6" style="padding-right: 0px">
						<button class="btn btn-success btn-block" style="font-weight: bold;font-size: 20px" onclick="saveStatus()">Confirm</button>
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

	jQuery(document).ready(function() {
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('#operator_0').val('');
		$('#operator_1').val('');
		$('#operator_2').val('');

		$('#finish_perbaikan').hide();
		$('#perbaikannote').hide();
		$('#perbaikannote2').hide();

		$('#molding_fix').hide();

		setInterval(setTime, 1000);

		CKEDITOR.replace('noteperbaikan' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	});

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator_0').focus();
	});

	function cancelAll() {
		$('#finish_perbaikan').hide();
		$('#start_perbaikan').show();
		$('#perbaikannote').hide();
		$('#perbaikannote2').hide();

		$('#molding_fix').hide();
		$('#molding_choice').show();

		$('#status').html('-');
		$('#product').html('');
		$('#part').html('');
		$('#mesin').html('');
		$('#last_counter').html('');

		count = false;

		$('div.timerperbaikan span.secondperbaikan').html('00');
        $('div.timerperbaikan span.minuteperbaikan').html('00');
        $('div.timerperbaikan span.hourperbaikan').html('00');
	}

	$('#operator_0').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator_0").val().length >= 8){
				var data = {
					employee_id : $("#operator_0").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#operator_0').val(result.employee.name);
						$('#op_0').html(result.employee.name.split(' ').slice(0,2).join(' '));
						$('#employee_id_0').val(result.employee.employee_id);
						$('#operator_0').prop('disabled',true);
						$('#operator_1').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator_0').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator_0").val("");
			}			
		}
	});

	$('#operator_1').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator_1").val().length >= 8){
				var data = {
					employee_id : $("#operator_1").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#operator_1').val(result.employee.name);
						$('#op_1').html(result.employee.name.split(' ').slice(0,2).join(' '));
						$('#employee_id_1').val(result.employee.employee_id);
						$('#operator_1').prop('disabled',true);
						$('#operator_2').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator_1').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator_1").val("");
			}			
		}
	});

	$('#operator_2').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator_2").val().length >= 8){
				var data = {
					employee_id : $("#operator_2").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#operator_2').val(result.employee.name);
						$('#op_2').html(result.employee.name.split(' ').slice(0,2).join(' '));
						$('#employee_id_2').val(result.employee.employee_id);
						$('#operator_2').prop('disabled',true);
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator_2').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator_2").val("");
			}			
		}
	});

	function getMoldingMaster() {
		$.get('{{ url("get/injeksi/get_molding_master") }}',  function(result, status, xhr){
			if(result.status){

				$('#molding_fix').hide();

				var moldingMaster = '';
				$('#molding_btn').html("");
				var no = 1;
				var color ="";
				// $('#modalMolding').modal('show');
				$('#modalMolding').modal({
					backdrop: 'static',
					keyboard: false
				});
				$.each(result.datas, function(key, value) {
					if (value.status == 'HARUS MAINTENANCE') {
						moldingMaster += '<div class="col-xs-3" style="padding-top: 5px">';
						moldingMaster += '<center>';
						moldingMaster += '<button class="btn btn-danger" id="'+value.part+'" style="width: 200px;font-size: 15px;font-weight: bold;" onclick="getMolding(this.id,\''+value.status+'\',\''+value.id_molding+'\')">'+value.product+' - '+value.part+'<br>'+(value.last_counter/value.qty_shot).toFixed(0)+' Shot<br>'+value.status+'</button>';
						moldingMaster += '</center>';
						moldingMaster += '</div>';
					}else if(value.status == 'DIPERBAIKI'){
						moldingMaster += '<div class="col-xs-3" style="padding-top: 5px">';
						moldingMaster += '<center>';
						moldingMaster += '<button class="btn btn-warning" id="'+value.part+'" style="width: 200px;font-size: 15px;font-weight: bold;" onclick="getMolding(this.id,\''+value.status+'\',\''+value.id_molding+'\')">'+value.product+' - '+value.part+'<br>'+(value.last_counter/value.qty_shot).toFixed(0)+' Shot<br>PERIODIK</button>';
						moldingMaster += '</center>';
						moldingMaster += '</div>';
					}else{
						moldingMaster += '<div class="col-xs-3" style="padding-top: 5px">';
						moldingMaster += '<center>';
						moldingMaster += '<button class="btn btn-primary" id="'+value.part+'" style="width: 200px;font-size: 15px;font-weight: bold;" onclick="getMolding(this.id,\''+value.status+'\',\''+value.id_molding+'\')">'+value.product+' - '+value.part+'<br>'+(value.last_counter/value.qty_shot).toFixed(0)+' Shot<br>READY</button>';
						moldingMaster += '</center>';
						moldingMaster += '</div>';
					}
				});
				$('#molding_btn').append(moldingMaster);
				openSuccessGritter('Success!', result.message);
				
			}
			else{
				openErrorGritter('Error!', result.message);
				audio_error.play();
				
			}
		});
	}

	function saveOperator() {
		$('#modalOperator').modal('hide');
		getMoldingMaster();
	}

	function getMolding(value,status,id) {
		$('#molding_fix').show();
		$('#molding_choice').hide();
		$('#molding_fix2').html(value);
		$('#id_molding').val(id);
		if (status == 'DIPERBAIKI') {
			$('#molding_fix2').prop('class','btn btn-warning');
		}else if(status === 'HARUS MAINTENANCE'){
			$('#molding_fix2').prop('class','btn btn-danger');
		}else{
			$('#molding_fix2').prop('class','btn btn-primary');
		}
	}

	function changeMolding2() {
		$('#molding_fix').hide();
		$('#molding_choice').show();
		$('#molding_fix2').html("MOLDING");
	}

	function change_molding() {
		getMoldingMaster();
		cancelAll();
	}

	function saveMolding() {
		get_maintenance_temp($('#molding_fix2').text());
		$('#modalMolding').modal('hide');
		fetchCount($('#id_molding').val());
	}

	function fetchCount(id){
		var data = {
			id : id,
		}
		$.get('{{ url("fetch/injeksi/fetch_molding_master") }}', data, function(result, status, xhr){
			if(result.status){
				$('#status').html(result.datas.status);
				$('#product').html(result.datas.product);
				$('#part').html(result.datas.part);
				if (result.datas.status_mesin == null) {
					$('#mesin').html('-');
				}else{
					$('#mesin').html(result.datas.status_mesin);
				}
				$('#last_counter').html((parseInt(result.datas.last_counter)/parseInt(result.datas.qty_shot)).toFixed(0));
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function startPerbaikan() {
		if ($('#status').text() == '-') {
			alert('Pilih Molding yang Akan Di Perbaiki');
		}
		else{
			setInterval(update_maintenance_temp,60000);
			duration = 0;
			count = true;
			started_at = new Date(getActualFullDate());
			$('#start_time_perbaikan').val(getActualFullDate());
			$('#start_perbaikan').hide();
			$('#finish_perbaikan').show();
			$('#perbaikannote').show();
			$('#perbaikannote2').show();
			store_maintenance_temp();
		}
	}

	function finishPerbaikan() {
		$('#loading').show();
		clearInterval(update_maintenance_temp);
		count = false;
		var detik = $('div.timerperbaikan span.secondperbaikan').text();
        var menit = $('div.timerperbaikan span.minuteperbaikan').text();
        var jam = $('div.timerperbaikan span.hourperbaikan').text();
        var waktu = jam + ':' + menit + ':' + detik;
        $('#perbaikan').val(waktu);

		var pic = [];
		var pic_1 = $('#op_0').text();
		var pic_2 = $('#op_1').text();
		var pic_3 = $('#op_2').text();

		if (pic_1 != "-") {
			pic.push(pic_1);
		}
		if (pic_2 != "-") {
			pic.push(pic_2);
		}
		if (pic_3 != "-") {
			pic.push(pic_3);
		}
		var mesin = $('#mesin').text();
		var part = $('#part').text();
		var product = $('#product').text();
		var maintenance_code = $('#maintenance_code').val();
		var status = $('#status').text();
		var last_counter = $('#last_counter').text();
		var start_time = $('#start_time_perbaikan').val();
		var end_time = getActualFullDate();
		var running_time = $('#perbaikan').val();
		var noteperbaikan =  CKEDITOR.instances.noteperbaikan.getData();

		
		var data = {
			mesin : mesin,
			pic : pic.join(', '),
			product : product,
			maintenance_code : maintenance_code,
			part : part,
			status : status,
			last_counter : last_counter,
			start_time : start_time,
			end_time : end_time,
			running_time : running_time,
			note : noteperbaikan,
		}

		$.post('{{ url("index/injeksi/store_maintenance_molding") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Maintenance Molding has been created');
				// reset();
				$('#finish_perbaikan').hide();
				$('#perbaikannote').hide();
				$('#perbaikannote2').hide();
				$('#loading').hide();
				location.reload();
			} else {
				audio_error.play();
				$('#loading').hide();
				openErrorGritter('Error','Gagal Simpan Data');
			}
		});
	}

	function resetPerbaikan() {
		window.location.href = "{{ url('index/injection/molding_maintenance') }}";
	}

	function store_maintenance_temp() {
		var start_time = getActualFullDate();
		var pic = [];
		var pic_1 = $('#op_0').text();
		var pic_2 = $('#op_1').text();
		var pic_3 = $('#op_2').text();

		if (pic_1 != "-") {
			pic.push(pic_1);
		}
		if (pic_2 != "-") {
			pic.push(pic_2);
		}
		if (pic_3 != "-") {
			pic.push(pic_3);
		}
		var mesin = $('#mesin').text();
		var part = $('#part').text();
		var product = $('#product').text();
		var last_counter = $('#last_counter').text();
		var status = $('#status').text();
		var note = CKEDITOR.instances.noteperbaikan.getData();

		var maintenance_code = pic.join(', ')+'_'+product+'_'+part+'_'+last_counter+'_'+status+'_'+getActualFullDate();

		var data = {
			pic : pic.join(', '),
			mesin : mesin,
			maintenance_code : maintenance_code,
			product : product,
			part : part,
			last_counter : last_counter,
			status : status,
			note : note,
			start_time : start_time
		}

		$.post('{{ url("index/injeksi/store_maintenance_temp") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Mulai Perbaikan');
				$('#maintenance_code').val(maintenance_code);
			} else {
				audio_error.play();
				openErrorGritter('Error','Gagal Memulai Perbaikan');
			}
		});
	}

	function pause() {
		$('#modalStatus').modal('show');
		$('#reasonPause').val('');
	}

	function saveStatus() {
		var reason = $('#reasonPause').val();

		var pic_1 = $('#op_0').text();
		var pic_2 = $('#op_1').text();
		var pic_3 = $('#op_2').text();

		var pic = [];

		if (pic_1 != "-") {
			pic.push(pic_1);
		}

		if (pic_2 != "-") {
			pic.push(pic_2);
		}

		if (pic_3 != "-") {
			pic.push(pic_3);
		}


		var mesin = $('#mesin').text();
		var part = $('#part').text();
		var product = $('#product').text();
		var maintenance_code = $('#maintenance_code').val();
		var status = $('#status').text();
		var last_counter = $('#last_counter').text();
		var start_time = getActualFullDate();
		var end_time = getActualFullDate();
		var running_time = $('#perbaikan').val();
		var noteperbaikan =  CKEDITOR.instances.noteperbaikan.getData();

		
		var data = {
			pic : pic.join(', '),
			product : product,
			maintenance_code : maintenance_code,
			part : part,
			status : 'PAUSE',
			last_counter : last_counter,
			start_time : start_time,
			reason:reason,
		}

		$.post('{{ url("input/injeksi/input_pause") }}', data, function(result, status, xhr){
			if(result.status){
				alert('Periodik Molding Dihentikan Sementara.');
				location.reload();
				$('#reasonPause').val('');
			}else{
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function changeStatus(maintenance_code) {
		var data = {
			maintenance_code:maintenance_code
		}
		$.post('{{ url("change/injeksi/change_pause") }}', data, function(result, status, xhr){
			if(result.status){
				alert('Periodik Molding Dilanjutkan.');
			}else{
				openErrorGritter('Error!',result.message);
			}
		});
	}

	function get_maintenance_temp(part) {
		var data = {
			part : part
		}
		$.get('{{ url("index/injeksi/get_maintenance_temp") }}',data,  function(result, status, xhr){
			if(result.status){
				if(result.datas.length != 0){
					$.each(result.datas, function(key, value) {
						if (value.remark == 'PAUSE') {
							if (confirm('Periodik dalam kondisi PAUSE. Apakah Anda ingin melanjutkan?')) {
								$('#mesin').html(value.mesin);
								$('#part').html(value.part);
								$('#status').html(value.status);
								$('#last_counter').html(value.last_counter);
								$('#product').html(value.product);
								$('#maintenance_code').val(value.maintenance_code);
								$("#noteperbaikan").html(CKEDITOR.instances.noteperbaikan.setData(value.note));
								// $('#noteperbaikan').val(value.note);
								$('#start_time_perbaikan').val(value.start_time);
								duration = 0;
								count = true;
								started_at = new Date(value.start_time);
								$('#start_perbaikan').hide();
								$('#finish_perbaikan').show();
								$('#perbaikannote').show();
								$('#perbaikannote2').show();
								setInterval(update_maintenance_temp,60000);
								changeStatus(value.maintenance_code);
							}else{
								change_molding();
							}
						}else{
							$('#mesin').html(value.mesin);
							$('#part').html(value.part);
							$('#status').html(value.status);
							$('#last_counter').html(value.last_counter);
							$('#product').html(value.product);
							$('#maintenance_code').val(value.maintenance_code);
							$("#noteperbaikan").html(CKEDITOR.instances.noteperbaikan.setData(value.note));
							// $('#noteperbaikan').val(value.note);
							$('#start_time_perbaikan').val(value.start_time);
							duration = 0;
							count = true;
							started_at = new Date(value.start_time);
							$('#start_perbaikan').hide();
							$('#finish_perbaikan').show();
							$('#perbaikannote').show();
							$('#perbaikannote2').show();
							setInterval(update_maintenance_temp,60000);
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

	function update_maintenance_temp() {
		var part = $('#part').text();
		var maintenance_code = $('#maintenance_code').val();
		var noteperbaikan = CKEDITOR.instances.noteperbaikan.getData();

		var data = {
			part : part,
			maintenance_code : maintenance_code,
			note : noteperbaikan
		}

		$.post('{{ url("index/injeksi/update_maintenance_temp") }}', data, function(result, status, xhr){
			if(result.status){
				// openSuccessGritter('Success','Maintenance Molding Temp has been updated');
				// reset();
			} else {
				// audio_error.play();
				// openErrorGritter('Error','Update Maintenance Molding Temp Failed');
			}
		});
	}

	var duration = 0;
	var count = false;
	// var count_pasang = false;
	var started_at;
	function setTime() {
		if(count){
			$('#secondperbaikan').html(pad(diff_seconds(new Date(), started_at) % 60));
	        $('#minuteperbaikan').html(pad(parseInt((diff_seconds(new Date(), started_at) % 3600) / 60)));
	        $('#hourperbaikan').html(pad(parseInt(diff_seconds(new Date(), started_at) / 3600)));
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


