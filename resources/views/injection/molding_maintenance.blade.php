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

	<input type="hidden" id="loc" value="{{ $title }} {{$title_jp}} }">
	
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
							<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:2vw; width: 30%;" id="op">-</td>
							<td colspan="2" style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;" id="op2">-</td>
						</tr>
						<tr>
							<td colspan="3" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 23%;background-color: rgb(220,220,220);color: black;font-size: 20px;"><b>Molding List</b></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="molding">
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
								Status
							</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Total Shot
							</th>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
								Mesin
							</th>
						</tr>
					</thead>
					<tbody id="moldingMaster">
					</tbody>
				</table>
			</div>
				<button id="change_operator" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="location.reload()" class="btn btn-warning">GANTI OPERATOR</button>
		</div>

		<div class="col-xs-6" style="padding-right: 0; padding-left: 10px">
			<div>
				<table style="width: 100%;" border="1">
					<tbody>
						<tr>
							<td colspan="4" style="width: 1%; font-weight: bold; font-size: 20px; background-color: rgb(220,220,220);">Status</td>
						</tr>
						<tr>
							<td colspan="4" id="status" style="border:1px solid black;width: 4%; font-size: 2vw; font-weight: bold; background-color: rgb(50, 50, 50); color: yellow;">-</td>
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
					            <input type="hidden" id="perbaikan" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" value="0:00:00" required>
					            <input type="hidden" id="start_time_perbaikan" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" required>
					        	</div>
					    	</td>
						</tr>
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
				<button id="start_perbaikan" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="startPerbaikan()" class="btn btn-success">MULAI PERBAIKAN</button>
				<button id="finish_perbaikan" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="finishPerbaikan()" class="btn btn-danger">SELESAI PERBAIKAN</button>
				<button id="reset_perbaikan" style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: white; width: 100%" onclick="resetPerbaikan()" class="btn btn-warning">RESET</button>
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

	jQuery(document).ready(function() {
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});

		$('#operator').val('');

		$('#finish_perbaikan').hide();
		$('#reset_perbaikan').hide();
		$('#perbaikannote').hide();
		$('#perbaikannote2').hide();
		$('#change_operator').hide();

		setInterval(setTime, 1000);
		setInterval(update_maintenance_temp,60000);

		CKEDITOR.replace('noteperbaikan' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
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
						// getMoldingLogPasang();
						get_maintenance_temp(result.employee.name);
						getMoldingMaster();
						$('#change_operator').show();
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

	function getMoldingMaster() {
		$.get('{{ url("get/injeksi/get_molding_master") }}',  function(result, status, xhr){
			if(result.status){

				var moldingMaster = '';
				$('#moldingMaster').html("");
				var no = 1;
				var color ="";
				$.each(result.datas, function(key, value) {
					if (no % 2 === 0 ) {
						color = 'style="background-color: #fffcb7;font-size: 18.5px;"';
					} else {
						color = 'style="background-color: #ffd8b7;font-size: 18.5px;"';
					}
					if (value.last_counter >= 15000) {
						color = 'style="background-color: #ff3030;font-size: 18.5px;color:white"';
					}
					if ($('#status').text() == '-') {
						moldingMaster += '<tr onclick="fetchCount(\''+value.id+'\')">';
					}
					else{
						moldingMaster += '<tr>';
					}
					moldingMaster += '<td '+color+'>'+value.product+'</td>';
					moldingMaster += '<td '+color+'>'+value.part+'</td>';
					moldingMaster += '<td '+color+'>'+value.status+'</td>';
					moldingMaster += '<td '+color+'>'+value.last_counter+'</td>';
					if (value.status_mesin == null) {
						moldingMaster += '<td '+color+'>-</td>';
					}else{
						moldingMaster += '<td '+color+'>'+value.status_mesin+'</td>';
					}
					
					moldingMaster += '</tr>';				
				no++;
				});
				$('#moldingMaster').append(moldingMaster);
				

				openSuccessGritter('Success!', result.message);
				
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
				$('#last_counter').html(result.datas.last_counter);
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
		clearInterval(update_maintenance_temp);
		count = false;
		var detik = $('div.timerperbaikan span.secondperbaikan').text();
        var menit = $('div.timerperbaikan span.minuteperbaikan').text();
        var jam = $('div.timerperbaikan span.hourperbaikan').text();
        var waktu = jam + ':' + menit + ':' + detik;
        $('#perbaikan').val(waktu);

		var pic = $('#op2').text();
		var mesin = $('#mesin').text();
		var part = $('#part').text();
		var product = $('#product').text();
		var status = $('#status').text();
		var last_counter = $('#last_counter').text();
		var start_time = $('#start_time_perbaikan').val();
		var end_time = getActualFullDate();
		var running_time = $('#perbaikan').val();
		var noteperbaikan =  CKEDITOR.instances.noteperbaikan.getData();

		
		var data = {
			mesin : mesin,
			pic : pic,
			product : product,
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
				location.reload();
			} else {
				audio_error.play();
				openErrorGritter('Error','Create Maintenance Molding Temp Failed');
			}
		});
	}

	function resetPerbaikan() {
		window.location.href = "{{ url('index/injection/molding_maintenance') }}";
	}

	function store_maintenance_temp() {
		var start_time = getActualFullDate();

		var pic = $('#op2').text();
		var mesin = $('#mesin').text();
		var part = $('#part').text();
		var product = $('#product').text();
		var last_counter = $('#last_counter').text();
		var status = $('#status').text();
		var note = CKEDITOR.instances.noteperbaikan.getData();

		var data = {
			pic : pic,
			mesin : mesin,
			product : product,
			part : part,
			last_counter : last_counter,
			status : status,
			note : note,
			start_time : start_time
		}

		$.post('{{ url("index/injeksi/store_maintenance_temp") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Maintenance Molding Temp has been created');
				// reset();
				getMoldingMaster();
			} else {
				audio_error.play();
				openErrorGritter('Error','Create Maintenance Molding Temp Failed');
			}
		});
	}

	function get_maintenance_temp(pic) {
		var data = {
			pic : pic
		}
		$.get('{{ url("index/injeksi/get_maintenance_temp") }}',data,  function(result, status, xhr){
			if(result.status){
				if(result.datas.length != 0){
					$.each(result.datas, function(key, value) {
						$('#mesin').html(value.mesin);
						$('#part').html(value.part);
						$('#status').html(value.status);
						$('#last_counter').html(value.last_counter);
						$('#product').html(value.product);
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
		var pic = $('#op2').text();
		var noteperbaikan = CKEDITOR.instances.noteperbaikan.getData();

		var data = {
			pic : pic,
			note : noteperbaikan
		}

		$.post('{{ url("index/injeksi/update_maintenance_temp") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success','Maintenance Molding Temp has been updated');
				// reset();
			} else {
				audio_error.play();
				openErrorGritter('Error','Update Maintenance Molding Temp Failed');
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


