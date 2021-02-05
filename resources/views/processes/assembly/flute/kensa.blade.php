@extends('layouts.display')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
<link href="{{ url('bower_components/roundslider/dist/roundslider.min.css') }}" rel="stylesheet" />
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
	#ngHistory {
		height:200px;
		overflow-y: scroll;
	}
	#historyLocation{
		overflow-x: scroll;
	}
	#ngAll {
		height:480px;
		overflow-y: scroll;
	}
	#loading, #error { display: none; }

</style>
@stop
@section('header')
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">
	<input type="hidden" id="loc" value="{{ $loc }}">
	<input type="hidden" id="loc_spec" value="{{ $loc_spec }}">
	<input type="hidden" id="process" value="{{ $process }}">
	<input type="hidden" id="started_at">
	<div class="row" style="padding-left: 10px;padding-right: 10px">
		<div class="col-xs-7" style="padding-right: 0; padding-left: 0">
			<div class="col-xs-12" style="padding-bottom: 5px;">
				<div class="row">
					<div class="col-xs-8">
						<div class="row">
							<table class="table table-bordered" style="width: 100%; margin-bottom: 0;">
								<thead>
									<tr>
										<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 16px;" colspan="2">Operator Kensa</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:16px; width: 30%;" id="op">-</td>
										<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 16px;" id="op2"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-4">
							<div class="input-group">
								<input type="text" style="text-align: center; border-color: black;" class="form-control input-lg" id="tag" name="tag" placeholder="Scan RFID Card..." required>
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
									<i class="glyphicon glyphicon-credit-card"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div style="padding-top: 5px;">
				<table style="width: 100%; margin-top: 5px;" border="1">
					<tbody>
						<tr>
							<td style="width: 1%; font-weight: bold; font-size: 16px; background-color: rgb(220,220,220);">Model</td>
							<td id="model" style="width: 2%; font-size: 16px; font-weight: bold; background-color: rgb(100,100,100); color: yellow; border: 1px solid black" colspan="2"></td>
							<td style="width: 1%; font-weight: bold; font-size: 16px; background-color: rgb(220,220,220);">SN</td>
							<td id="serial_number" style="width: 2%; font-weight: bold; font-size: 16px; background-color: rgb(100,100,100); color: yellow; border: 1px solid black"></td>
							<td style="width: 1%; font-weight: bold; font-size: 16px; background-color: rgb(220,220,220);">Loc</td>
							<td id="location_now" style="width: 5%; font-weight: bold; font-size: 16px; background-color: rgb(100,100,100); color: yellow; border: 1px solid black"></td>
							<input type="hidden" id="tag2">
							<input type="hidden" id="serial_number2">
							<input type="hidden" id="model2">
							<input type="hidden" id="location_now2">
							<input type="hidden" id="employee_id">
						</tr>
					</tbody>
				</table>
			</div>
			<div style="padding-top: 5px">
				<div id="historyLocation">
					<table class="table table-bordered" style="width: 100%;padding-top: 5px;">
						<tbody id="details">
						</tbody>
					</table>
				</div>
			</div>
			<div style="padding-top: 5px">
				<div id="ngTemp">
					<table id="ngTempTable" class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
						<thead>
							<tr>
								<th style="width: 40%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Nama NG</th>
								<th style="width: 20%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Value / Jumlah</th>
								<th style="width: 20%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Onko</th>
								<th style="width: 20%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Oleh</th>
							</tr>
						</thead>
						<tbody id="ngTempBody">
						</tbody>
					</table>
				</div>
			</div>
			<div style="padding-top: 5px;background-color: rgb(220,220,220);font-weight: bold;padding: 0px; font-size: 20px;border: 1px solid black;width: 100%">
				<center>History NG</center>
			</div>
			<div style="padding-top: 5px">
				<div id="ngHistory">
					<table id="ngHistoryTable" class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
						<thead>
							<tr>
								<th style="width: 3%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Nama NG</th>
								<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Value / Jumlah</th>
								<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Onko</th>
								<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Loc</th>
								<th style="width: 3%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Oleh</th>
							</tr>
						</thead>
						<tbody id="ngHistoryBody">
						</tbody>
					</table>
				</div>
			</div>
			<div style="padding-top: 5px;text-align: center;" id="timer">
				<!-- <button class="btn btn-sm btn-success" id="startkensa" onClick="timerkensa.start(1000)">Start</button>  -->
		        <!-- <button class="btn btn-sm btn-danger" id="stopkensa" onClick="timerkensa.stop()">Stop</button> -->
				<div class="timerkensa" style="color:#000;font-size: 80px;background-color: #85ffa7">
		            <span class="hourkensa">00</span>:<span class="minutekensa">00</span>:<span class="secondkensa">00</span>
		        </div>
		        <div class="timeout" style="color:red;font-size: 80px;display: none">
		        </div>
		        <!-- <input type="text" id="kkensa_time" class="timepicker" style="width: 100%; height: 30px; font-size: 20px; text-align: center;" value="00:00:00" required> -->
			</div>
		</div>
		<div class="col-xs-5" style="padding-right: 0;">
			<div id="ngAll">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 65%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >List NG</th>
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
						<tr <?php echo $color ?>>
							<td onclick="showNgDetail('{{ $ng_list->ng_name }}')" style="font-size: 40px;">{{ $ng_list->ng_name }} </td>
						</tr>
						<?php $no+=1; ?>
						@endforeach
					</tbody>
				</table>
			</div>
			<div>
				<center>
					<button style="width: 100%; margin-top: 10px; font-size: 40px; padding:0; font-weight: bold; border-color: black; color: white; width: 49%" onclick="canc()" class="btn btn-danger">CANCEL</button>
					<button id="conf1" style="width: 100%; margin-top: 10px; font-size: 40px; padding:0; font-weight: bold; border-color: black; color: white; width: 49%" onclick="conf()" class="btn btn-success">CONFIRM</button>
				</center>
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
					<h4 id="judul_ng">NG List</h4>
					<div>
						<div class="col-xs-12" id="ngDetail">
						</div>
						<div class="col-xs-12" id="ngDetailFix" style="display: none;padding-top: 5px">
							<center><button class="btn btn-primary" style="width:100%;font-size: 25px;font-weight: bold;" onclick="getNgChange()" id="ngFix">NG
							</button></center>
							<input type="hidden" id="ngFix2" value="NG">
						</div>
					</div>

					<h4 id="judul_onko" style="padding-top: 10px">Pilih Onko</h4>
					<div>
						<div class="col-xs-12" id="onkoBody">
						</div>
						<div class="col-xs-12" id="onkoBodyFix" style="display: none;padding-top: 5px">
							<center><button class="btn btn-warning" style="width:100%;font-size: 25px;font-weight: bold" onclick="getOnkoChange()" id="onkoFix">ONKO
							</button></center>
							<input type="hidden" id="onkoFix2" value="ONKO">
						</div>
					</div>
					<input type="hidden" id="operator_id_before" value="OPID">

					<div style="padding-top: 10px">
						<button id="confNg" style="width: 100%; margin-top: 10px; font-size: 3vw; padding:0; font-weight: bold; border-color: black; color: white;" onclick="confNgTemp()" class="btn btn-success">CONFIRM</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalNgTanpoAwase">
	<div class="modal-dialog modal-lg" style="width: 1200px">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<h4 id="judul_ng">NG List Tanpo Awase</h4>
					<div id="ngList">
						<div class="col-xs-12" id="onkoBodyFixTanpoAwase" style="display: none;padding-top: 5px">
							<center><button class="btn btn-primary" style="width:100%;font-size: 20px" onclick="getOnkoChangeTanpoAwase()" id="onkoFixTanpoAwase">ONKO
							</button></center>
							<input type="hidden" id="onkoFixTanpoAwase2">
							<input type="hidden" id="idOnkoTanpoAwase">
						</div>
						<div class="col-xs-12" style="padding-top: 5px" id="onkoBodyTanpoAwase">
						</div>
						<div>
							<input type="hidden" id="value1" value="0">
							<input type="hidden" id="value2" value="0">
							<input type="hidden" id="value3" value="0">
							<input type="hidden" id="value4" value="0">
							<input type="hidden" id="value5" value="0">
							<input type="hidden" id="value6" value="0">
							<input type="hidden" id="value7" value="0">
							<input type="hidden" id="value8" value="0">
							<input type="hidden" id="value9" value="0">
							<input type="hidden" id="value10" value="0">
							<input type="hidden" id="value11" value="0">
							<input type="hidden" id="value12" value="0">
							<input type="hidden" id="value13" value="0">
							<input type="hidden" id="value14" value="0">
							<input type="hidden" id="value15" value="0">
							<input type="hidden" id="value16" value="0">
						</div>
						<input type="hidden" id="operator_id_before_tanpoawase" value="OPID">
						<button id="confNgOnkoTanpoAwase" style="width: 100%; margin-top: 10px; font-size: 3vw; padding:0; font-weight: bold; border-color: black; color: white;" onclick="confNgOnkoTanpoAwase()" class="btn btn-success">CONFIRM</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalNgOnko">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<h4 id="judul_ng">Pilih Onko</h4>
					<div>
						<table id="ngOnko" class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
							<thead>
								<tr>
									<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Location</th>
									<th style="width: 65%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >NG Name</th>
									<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Jumlah</th>
								</tr>
							</thead>
							<tbody id="ngOnkoBody">
							</tbody>
						</table>
						<button id="confNgOnko" style="width: 100%; margin-top: 10px; font-size: 3vw; padding:0; font-weight: bold; border-color: black; color: white;" onclick="confNgOnko()" class="btn btn-success">CONFIRM</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('bower_components/roundslider/dist/roundslider.min.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#operator').val('');
		$('#tag').val('');
		if ($('#loc').val() == 'qa-fungsi' || $('#loc').val() == 'qa-visual1' || $('#loc').val() == 'qa-visual2') {
			$('#timer').show();
		}else{
			$('#timer').hide();
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			getHeader(this.value);
		}
	});

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($('#operator').val().length > 9 ){
				var data = {
					employee_id : $("#operator").val()
				}

				$.get('{{ url("scan/assembly/operator_kensa") }}', data, function(result, status, xhr){
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
			}else{
				openErrorGritter('Error', 'Tag Tidak Ditemukan');
				$('#operator').val('');
			}
		}
	});

	function getHeader(tag) {
		var location = $('#loc').val();
		var data = {
			tag : tag,
			location : location,
			employee_id:$('#operator').val()
		}

		var tableData = "";

		fetchNgHistory();

		$.get('{{ url("scan/assembly/kensa") }}', data, function(result, status, xhr){
			if(result.status){
				if ($('#loc').val() == 'qa-fungsi' || $('#loc').val() == 'qa-visual1' || $('#loc').val() == 'qa-visual2') {
					timerkensa.start(1000);
				}
				$("#model").text(result.details.model);
				$("#serial_number").text(result.details.serial_number);
				$("#model2").val(result.details.model);
				$("#serial_number2").val(result.details.serial_number);
				$("#tag2").val(tag);
				$("#location_now").text("{{$loc2}}");
				$("#location_now2").val("{{$loc2}}");

				tableData += '<tr>';
				$.each(result.details2, function(key, value) {
					if (key%2 == 0) {
						var color = 'style="width: 1%; font-weight: bold; font-size: 16px; background-color: #ffff66;"';
					}else{
						var color = 'style="width: 2%; font-weight: bold; font-size: 16px; background-color: #ccffff; border: 1px solid black"';
					}
					tableData += '<td '+color+'>'+ value.location +'</td>';
				});
				tableData += '</tr>';
				tableData += '<tr>';
				$.each(result.details2, function(key2, value2) {
					tableData += '<td style="width: 2%; font-weight: bold; font-size: 16px; background-color: rgb(100,100,100); color: yellow; border: 1px solid black">'+ value2.name +'</td>';
				});
				tableData += '</tr>';

				$('#details').append(tableData);

				$('#started_at').val(result.started_at);				

				$("#tag").prop('disabled', true);
				fetchNgTemp();
				openSuccessGritter('Success', result.message);
			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#model').text("");
				$('#key').text("");
				$("#tag").val("");
				$("#tag").focus();
			}
		});
	}

	function showNgDetail(ng_name) {
		if($('#serial_number').text() == ""){
			audio_error.play();
			openErrorGritter('Error!', 'Scan RFID first.');
			$("#tag").val("");
			$("#tag").focus();
		}
		else{
			$('#operator_id_before').val("OPID");
			$('#operator_id_before_tanpoawase').val("OPID");
			if (ng_name === "Tanpo Awase") {
				$('#modalNgTanpoAwase').modal('show');
				var btn = document.getElementById('confNgOnkoTanpoAwase');
				btn.disabled = false;
				btn.innerText = 'CONFIRM';

				$('#onkoBodyTanpoAwase').show();
				$('#onkoBodyFixTanpoAwase').hide();
				$('#onkoFixTanpoAwase').html("ONKO");
				$('#onkoFixTanpoAwase2').val("ONKO");
				$('#idOnkoTanpoAwase').val("ONKO");

				var bodyNgTemp = "";
				var bodyNgOnko = "";
				$('#onkoBodyTanpoAwase').html("");
				var index = 1;
				var index2 = 1;

				var location = '{{$loc_spec}}';
				var data2 = {
					ng_name:ng_name,
					location:location,
					process:$('#process').val()
				}

				$.get('{{ url("fetch/assembly/ng_detail") }}', data2, function(result, status, xhr){
					$.each(result.ng_detail, function(key, value) {
						var data3 = {
							tag : $('#tag2').val(),
							serial_number : $('#serial_number2').val(),
							model : $('#model2').val(),
							process_before : value.process_before,
						}
						$.get('{{ url("fetch/assembly/get_process_before") }}',data3, function(result, status, xhr){
							if (result.status) {
								$.each(result.details, function(key, value) {
									$('#operator_id_before_tanpoawase').val(value.operator_id);
								});
							}else{
								$('#operator_id_before_tanpoawase').val(result.details);
							}
						});
					});
				});

				var data = {
					process:"tanpoawase"
				}

				$.get('{{ url("fetch/assembly/onko") }}', data, function(result, status, xhr){

					$.each(result.onko, function(key, value) {
						// bodyNgOnko += '<div class="col-xs-3" style="padding-top: 5px">';
						// bodyNgOnko += '<center><button class="btn btn-primary" id="'+value.key+' ('+value.nomor+')" style="width: 180px;font-size: 15px" onclick="getOnkoTanpoAwase(this.id,'+value.id+')">'+value.key+' ('+value.nomor+')';
						// bodyNgOnko += '</button></center></div>';
						bodyNgOnko += '<div class="col-xs-3">'
						bodyNgOnko += '<div style="text-align:center;font-weight:bold;font-size:20px">'+value.keynomor+'</div>';
						bodyNgOnko += '<div id="slider'+index+'"></div>';
						bodyNgOnko += '</div>'
						index++;
					});

					$('#onkoBodyTanpoAwase').append(bodyNgOnko);

					$("#slider1").roundSlider({
					    sliderType: "range",
					    handleShape: "dot",
					    width: 35,
					    radius: 100,
					    value: 0,
					    lineCap: "square",
					    startAngle: 90,
					    handleSize: "+12",
					    max: "12",
					    drag: function (value) {
					        $("#value1").val(value.value);
					    }
					});

					$("#slider2").roundSlider({
					    sliderType: "range",
					    handleShape: "dot",
					    width: 35,
					    radius: 100,
					    value: 0,
					    lineCap: "square",
					    startAngle: 90,
					    handleSize: "+12",
					    max: "12",
					    drag: function (value) {
					        $("#value2").val(value.value);
					    }
					});

					$("#slider3").roundSlider({
					    sliderType: "range",
					    handleShape: "dot",
					    width: 35,
					    radius: 100,
					    value: 0,
					    lineCap: "square",
					    startAngle: 90,
					    handleSize: "+12",
					    max: "12",
					    drag: function (value) {
					        $("#value3").val(value.value);
					    }
					});

					$("#slider4").roundSlider({
					    sliderType: "range",
					    handleShape: "dot",
					    width: 35,
					    radius: 100,
					    value: 0,
					    lineCap: "square",
					    startAngle: 90,
					    handleSize: "+12",
					    max: "12",
					    drag: function (value) {
					        $("#value4").val(value.value);
					    }
					});

					$("#slider5").roundSlider({
					    sliderType: "range",
					    handleShape: "dot",
					    width: 35,
					    radius: 100,
					    value: 0,
					    lineCap: "square",
					    startAngle: 90,
					    handleSize: "+12",
					    max: "12",
					    drag: function (value) {
					        $("#value5").val(value.value);
					    }
					});

					$("#slider6").roundSlider({
					    sliderType: "range",
					    handleShape: "dot",
					    width: 35,
					    radius: 100,
					    value: 0,
					    lineCap: "square",
					    startAngle: 90,
					    handleSize: "+12",
					    max: "12",
					    drag: function (value) {
					        $("#value6").val(value.value);
					    }
					});

					$("#slider7").roundSlider({
					    sliderType: "range",
					    handleShape: "dot",
					    width: 35,
					    radius: 100,
					    value: 0,
					    lineCap: "square",
					    startAngle: 90,
					    handleSize: "+12",
					    max: "12",
					    drag: function (value) {
					        $("#value7").val(value.value);
					    }
					});

					$("#slider8").roundSlider({
					    sliderType: "range",
					    handleShape: "dot",
					    width: 35,
					    radius: 100,
					    value: 0,
					    lineCap: "square",
					    startAngle: 90,
					    handleSize: "+12",
					    max: "12",
					    drag: function (value) {
					        $("#value8").val(value.value);
					    }
					});

					$("#slider9").roundSlider({
					    sliderType: "range",
					    handleShape: "dot",
					    width: 35,
					    radius: 100,
					    value: 0,
					    lineCap: "square",
					    startAngle: 90,
					    handleSize: "+12",
					    max: "12",
					    drag: function (value) {
					        $("#value9").val(value.value);
					    }
					});

					$("#slider10").roundSlider({
					    sliderType: "range",
					    handleShape: "dot",
					    width: 35,
					    radius: 100,
					    value: 0,
					    lineCap: "square",
					    startAngle: 90,
					    handleSize: "+12",
					    max: "12",
					    drag: function (value) {
					        $("#value10").val(value.value);
					    }
					});

					$("#slider11").roundSlider({
					    sliderType: "range",
					    handleShape: "dot",
					    width: 35,
					    radius: 100,
					    value: 0,
					    lineCap: "square",
					    startAngle: 90,
					    handleSize: "+12",
					    max: "12",
					    drag: function (value) {
					        $("#value11").val(value.value);
					    }
					});

					$("#slider12").roundSlider({
					    sliderType: "range",
					    handleShape: "dot",
					    width: 35,
					    radius: 100,
					    value: 0,
					    lineCap: "square",
					    startAngle: 90,
					    handleSize: "+12",
					    max: "12",
					    drag: function (value) {
					        $("#value12").val(value.value);
					    }
					});

					$("#slider13").roundSlider({
					    sliderType: "range",
					    handleShape: "dot",
					    width: 35,
					    radius: 100,
					    value: 0,
					    lineCap: "square",
					    startAngle: 180,
					    handleSize: "+12",
					    max: "12",
					    drag: function (value) {
					        $("#value13").val(value.value);
					    }
					});

					$("#slider14").roundSlider({
					    sliderType: "range",
					    handleShape: "dot",
					    width: 35,
					    radius: 100,
					    value: 0,
					    lineCap: "square",
					    startAngle: 90,
					    handleSize: "+12",
					    max: "12",
					    drag: function (value) {
					        $("#value14").val(value.value);
					    }
					});

					$("#slider15").roundSlider({
					    sliderType: "range",
					    handleShape: "dot",
					    width: 35,
					    radius: 100,
					    value: 0,
					    lineCap: "square",
					    startAngle: 90,
					    handleSize: "+12",
					    max: "12",
					    drag: function (value) {
					        $("#value15").val(value.value);
					    }
					});

					$("#slider16").roundSlider({
					    sliderType: "range",
					    handleShape: "dot",
					    width: 35,
					    radius: 100,
					    value: 0,
					    lineCap: "square",
					    startAngle: 90,
					    handleSize: "+12",
					    max: "12",
					    drag: function (value) {
					        $("#value16").val(value.value);
					    }
					});
				});

			}else{
				$('#modalNg').modal('show');
				var btn = document.getElementById('confNg');
				btn.disabled = false;
				btn.innerText = 'Confirm'

				var location = '{{$loc_spec}}';
				var data = {
					ng_name:ng_name,
					location:location,
					process:$('#process').val()
				}
				var bodyDetail = "";
				$('#ngDetailFix').hide();
				$('#ngDetail').show();
				$('#ngDetail').html("");

				var bodyNgOnko = "";
				$('#onkoBodyFix').hide();
				$('#onkoBody').show();
				$('#onkoBody').html("");

				$.get('{{ url("fetch/assembly/ng_detail") }}', data, function(result, status, xhr){
					$.each(result.ng_detail, function(key, value) {
						bodyDetail += '<div class="col-xs-4" style="padding-top: 10px">';
						bodyDetail += '<center><button class="btn btn-primary" id="'+value.ng_name+' - '+value.ng_detail+'" style="width: 250px;font-size: 25px;" onclick="getNg(this.id,\''+value.process_before+'\')">'+value.ng_name+' - '+value.ng_detail;
						bodyDetail += '</button></center></div>';
						$('#judul_ng').html(value.ng_name);
					});

					$('#ngDetail').append(bodyDetail);

					$.each(result.onko, function(key, value) {
						bodyNgOnko += '<div class="col-xs-3" style="padding-top: 5px">';
						bodyNgOnko += '<center><button class="btn btn-warning" id="'+value.key+' ('+value.nomor+')" style="width: 180px;font-size: 20px" onclick="getOnko(this.id)">'+value.key+' ('+value.nomor+')';
						bodyNgOnko += '</button></center></div>';
					});

					$('#onkoBody').append(bodyNgOnko);
				});
			}
		}
	}

	function fetchNgHistory() {
		var tag = $('#tag2').val();
		var employee_id = $('#employee_id').val();
		var serial_number = $('#serial_number2').val();
		var model = $('#model2').val();

		var data = {
			tag:tag,
			employee_id:employee_id,
			serial_number:serial_number,
			model:model
		}

		var bodyNgTemp = "";
		$('#ngHistoryBody').html("");
		var index = 1;

		$.get('{{ url("fetch/assembly/ng_logs") }}', data, function(result, status, xhr){
			$.each(result.ng_logs, function(key, value) {
				if (index % 2 == 0) {
					var color = 'style="background-color: #fffcb7"';
				}else{
					var color = 'style="background-color: #ffd8b7"'
				}
				index++;
				bodyNgTemp += "<tr "+color+">";
				bodyNgTemp += '<td style="font-size: 15px;">'+value.ng_name+'</td>';
				if (value.value_bawah == null) {
					bodyNgTemp += '<td style="font-size: 15px;">'+value.value_atas+'</td>';
				}else{
					bodyNgTemp += '<td style="font-size: 15px;">'+value.value_atas+' - '+value.value_bawah+'</td>';
				}
				bodyNgTemp += '<td style="font-size: 15px;">'+value.ongko+'</td>';
				bodyNgTemp += '<td style="font-size: 15px;">'+value.location+'</td>';
				bodyNgTemp += '<td style="font-size: 15px;">'+value.name+'</td>';
				bodyNgTemp += "</tr>";
			});

			$('#ngHistoryBody').append(bodyNgTemp);
		});
	}

	function fetchNgTemp() {
		var tag = $('#tag2').val();
		var employee_id = $('#employee_id').val();
		var serial_number = $('#serial_number2').val();
		var model = $('#model2').val();

		var data = {
			tag:tag,
			employee_id:employee_id,
			serial_number:serial_number,
			model:model
		}

		var bodyNgTemp = "";
		$('#ngTempBody').html("");
		var index = 1;

		$.get('{{ url("fetch/assembly/ng_temp") }}', data, function(result, status, xhr){
			$.each(result.ng_temp, function(key, value) {
				if (index % 2 == 0) {
					var color = 'style="background-color: #fffcb7"';
				}else{
					var color = 'style="background-color: #ffd8b7"'
				}
				index++;
				bodyNgTemp += "<tr "+color+">";
				bodyNgTemp += '<td style="font-size: 20px;">'+value.ng_name+'</td>';
				if (value.value_bawah == null) {
					bodyNgTemp += '<td style="font-size: 20px;">'+value.value_atas+'</td>';
				}else{
					bodyNgTemp += '<td style="font-size: 20px;">'+value.value_atas+' - '+value.value_bawah+'</td>';
				}
				bodyNgTemp += '<td style="font-size: 20px;">'+value.ongko+'</td>';
				bodyNgTemp += '<td style="font-size: 20px;">'+value.name+'</td>';
				bodyNgTemp += "</tr>";
			});

			$('#ngTempBody').append(bodyNgTemp);
		});
	}

	function getNg(value,process_before) {
		var data = {
			tag : $('#tag2').val(),
			serial_number : $('#serial_number2').val(),
			model : $('#model2').val(),
			process_before : process_before,
		}
		$.get('{{ url("fetch/assembly/get_process_before") }}',data, function(result, status, xhr){
			if (result.status) {
				$.each(result.details, function(key, value) {
					$('#operator_id_before').val(value.operator_id);
				});
			}else{
				$('#operator_id_before').val(result.details);
			}
		});
		$('#ngDetail').hide();
		$('#ngDetailFix').show();
		$('#ngFix').html(value);
		$('#ngFix2').val(value);
	}

	function getNgChange() {
		$('#ngDetail').show();
		$('#ngDetailFix').hide();
		$('#ngFix').html("NG");
		$('#ngFix2').val("NG");
		$('#operator_id_before').val("OPID");
	}

	function getOnko(value) {
		$('#onkoBody').hide();
		$('#onkoBodyFix').show();
		$('#onkoFix').html(value);
		$('#onkoFix2').val(value);
	}

	function getOnkoChange() {
		$('#onkoBody').show();
		$('#onkoBodyFix').hide();
		$('#onkoFix').html("ONKO");
		$('#onkoFix2').val("ONKO");
	}

	function getOnkoTanpoAwase(value,id) {
		$('#onkoBodyTanpoAwase').hide();
		$('#onkoBodyFixTanpoAwase').show();
		$('#onkoFixTanpoAwase').html(value);
		$('#onkoFixTanpoAwase2').val(value);
		$('#idOnkoTanpoAwase').val(id);
	}

	function getOnkoChangeTanpoAwase() {
		$('#onkoBodyTanpoAwase').show();
		$('#onkoBodyFixTanpoAwase').hide();
		$('#onkoFixTanpoAwase').html("ONKO");
		$('#onkoFix2TanpoAwase').val("ONKO");
		$('#idOnkoTanpoAwase').val("ONKO");
	}

	function confNgOnkoTanpoAwase() {
		var onko = [];
		var value_atas = [];
		var value_bawah = [];
		var onko_ng = [];
		var index = 0;

		var data = {
			process:"tanpoawase"
		}

		var btn = document.getElementById('confNgOnkoTanpoAwase');
		btn.disabled = true;
		btn.innerText = 'Saving...';

		$.get('{{ url("fetch/assembly/onko") }}',data, function(result, status, xhr){
			$.each(result.onko, function(key, value) {
				onko.push(value.keynomor);
				index++;
			});

			for (var i = 0; i < index; i++) {
				var a = i+1;
				var idvalue = '#value'+a;
				if ($(idvalue).val() == "0" || $(idvalue).val() == '0,0') {
					
				}else{
					onko_ng.push(onko[i]);
					var valuesplit = $(idvalue).val().split(",");
					value_atas.push(valuesplit[0]);
					value_bawah.push(valuesplit[1]);
				}
			}

			var data = {
				tag : $('#tag2').val(),
				employee_id : $('#employee_id').val(),
				serial_number : $('#serial_number2').val(),
				model : $('#model2').val(),
				location : $('#location_now2').val(),
				started_at : $('#started_at').val(),
				ng:"Tanpo Awase",
				onko: onko_ng,
				value_atas: value_atas,
				value_bawah:value_bawah,
				origin_group_code : '041',
				operator_id : $('#operator_id_before_tanpoawase').val(),
			}

			$.post('{{ url("input/assembly/ng_temp") }}', data, function(result, status, xhr){
				if(result.status){
					var btn = document.getElementById('confNgOnkoTanpoAwase');
					btn.disabled = true;
					btn.innerText = 'Posting...';
					$('#value1').val('0');
					$('#value2').val('0');
					$('#value3').val('0');
					$('#value4').val('0');
					$('#value5').val('0');
					$('#value6').val('0');
					$('#value7').val('0');
					$('#value8').val('0');
					$('#value9').val('0');
					$('#value10').val('0');
					$('#value11').val('0');
					$('#value12').val('0');
					$('#value13').val('0');
					$('#value14').val('0');
					$('#value15').val('0');
					$('#value16').val('0');
					$('#modalNgTanpoAwase').modal('hide');
					fetchNgTemp();
					openSuccessGritter('Success!', result.message);
				}
				else{
					var btn = document.getElementById('confNgOnkoTanpoAwase');
					btn.disabled = false;
					btn.innerText = 'CONFIRM';
					audio_error.play();
					openErrorGritter('Error!', result.message);
				}
			});
		});
	}

	function confNgTemp() {
		if ($('#ngFix2').val() == "NG" || $('#onkoFix2').val() == "ONKO") {
			audio_error.play();
			openErrorGritter('Error!', "Harus Dipilih Semua!");
		}else{
			var btn = document.getElementById('confNg');
			btn.disabled = true;
			btn.innerText = 'Posting...';

			var data = {
				tag : $('#tag2').val(),
				employee_id : $('#employee_id').val(),
				serial_number : $('#serial_number2').val(),
				model : $('#model2').val(),
				location : $('#location_now2').val(),
				started_at : $('#started_at').val(),
				ng: $('#ngFix2').val(),
				onko: $('#onkoFix2').val(),
				origin_group_code : '041',
				operator_id : $('#operator_id_before').val()
			}

			$.post('{{ url("input/assembly/ng_temp") }}', data, function(result, status, xhr){
				if(result.status){
					var btn = document.getElementById('confNg');
					btn.disabled = true;
					btn.innerText = 'Posting...';
					$('#modalNg').modal('hide');
					fetchNgTemp();
					openSuccessGritter('Success!', result.message);
				}
				else{
					var btn = document.getElementById('confNg');
					btn.disabled = false;
					btn.innerText = 'CONFIRM';
					audio_error.play();
					openErrorGritter('Error!', result.message);
				}
			});
		}
	}

	function disabledButton() {
		if($('#tag').val() != ""){
			var btn = document.getElementById('conf1');
			btn.disabled = true;
			btn.innerText = 'Posting...'
			return false;
		}
	}

	function conf(){
		if($('#tag').val() == ""){
			openErrorGritter('Error!', 'Tag is empty');
			audio_error.play();
			$("#tag").val("");

			return false;
		}

		timerkensa.stop();
		timerkensa.reset();
		$('div.timerkensa').show();
		$('div.timeout').hide();

		var btn = document.getElementById('conf1');
		btn.disabled = true;
		btn.innerText = 'Saving...';

		var data = {
			tag : $('#tag2').val(),
			employee_id : $('#employee_id').val(),
			serial_number : $('#serial_number2').val(),
			model : $('#model2').val(),
			location : $('#location_now2').val(),
			started_at : $('#started_at').val(),
			origin_group_code : '041'
		}

		$.post('{{ url("input/assembly/kensa") }}', data,function(result, status, xhr){
			if(result.status){
				var btn = document.getElementById('conf1');
				btn.disabled = false;
				btn.innerText = 'CONFIRM';
				openSuccessGritter('Success!', result.message);
				$('#model').text("");
				$('#serial_number').text("");
				$('#location_now').text("");
				$('#details').text("");
				$('#tag').val("");
				$('#tag').prop('disabled', false);
				$('#tag').focus();
				deleteNgTemp();
				deleteAssemblies();
				$('#ngHistoryBody').empty();
			}
			else{
				var btn = document.getElementById('conf1');
				btn.disabled = false;
				btn.innerText = 'CONFIRM';
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function canc(){
		$('#model').text("");
		$('#serial_number').text("");
		$('#location_now').text("");
		$('#details').text("");
		$('#tag').val("");
		$('#tag').prop('disabled', false);
		$('#tag').focus();
		deleteNgTemp();
		deleteAssemblies();
		$('#ngHistoryBody').empty();
		timerkensa.stop();
		timerkensa.reset();
		$('div.timerkensa').show();
		$('div.timeout').hide();
		var btn = document.getElementById('conf1');
		btn.disabled = false;
		btn.innerText = 'CONFIRM';
	}

	function deleteAssemblies() {
		var data = {
			employee_id:$('#operator').val()
		}

		$.get('{{ url("destroy/assembly/kensa") }}', data, function(result, status, xhr){
			if(result.status){
				// openSuccessGritter('Success', result.message);
			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
			}
		});
	}

	function deleteNgTemp() {
		var tag = $('#tag2').val();
		var employee_id = $('#employee_id').val();
		var serial_number = $('#serial_number2').val();
		var model = $('#model2').val();

		var data = {
			tag:tag,
			employee_id:employee_id,
			serial_number:serial_number,
			model:model
		}

		$.get('{{ url("delete/assembly/delete_ng_temp") }}', data, function(result, status, xhr){
			if (result.status) {
				fetchNgTemp();
				// openSuccessGritter('Success',result.message);
			}else{
				openErrorGritter('Error!','Temp Not Found');
			}
		});
	}

	function plus(id){
		var count = $('#count'+id).text();
		if($('#serial_number').text() != ""){
			$('#count'+id).text(parseInt(count)+1);
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', 'Scan RFID first.');
			$("#tag").val("");
			$("#tag").focus();
		}
	}

	function minus(id){
		var count = $('#count'+id).text();
		if($('#serial_number').text() != ""){
			if(count > 0)
			{
				$('#count'+id).text(parseInt(count)-1);
			}
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', 'Scan RFID first.');
			$("#tag").val("");
			$("#tag").focus();
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

	function _timerkensa(callback)
	{
	    var time = 0;     //  The default time of the timer
	    var mode = 1;     //    Mode: count up or count down
	    var status = 0;    //    Status: timer is running or stoped
	    var timer_id;
	    var hour;
	    var minute;
	    var second;    //    This is used by setInterval function
	    
	    // this will start the timer ex. start the timer with 1 second interval timer.start(1000) 
	    this.start = function(interval)
	    {
	    	// $('#startpasmod').hide();
			$('#stopkensa').show();
	        interval = (typeof(interval) !== 'undefined') ? interval : 1000;
	 
	        if(status == 0)
	        {
	            status = 1;
	            timer_id = setInterval(function()
	            {
	                switch(1)
	                {
	                    default:
	                    if(time)
	                    {
	                        time--;
	                        generateTime();
	                        if(typeof(callback) === 'function') callback(time);
	                    }
	                    break;
	                    
	                    case 1:
	                    if(time < 86400)
	                    {
	                        time++;
	                        generateTime();
	                        if(typeof(callback) === 'function') callback(time);
	                    }
	                    break;
	                }
	            }, interval);
	        }
	    }
	    
	    //  Same as the name, this will stop or pause the timer ex. timer.stop()
	    this.stop =  function()
	    {
	        if(status == 1)
	        {
	            status = 0;
		        // $('#stopkensa').hide();
	            clearInterval(timer_id);
	        }
	    }
	    
	    // Reset the timer to zero or reset it to your own custom time ex. reset to zero second timer.reset(0)
	    this.reset =  function(sec)
	    {
	        sec = (typeof(sec) !== 'undefined') ? sec : 0;
	        time = sec;
	        generateTime(time);
	    }
	    this.getTime = function()
	    {
	        return time;
	    }
	    this.getMode = function()
	    {
	        return mode;
	    }
	    this.getStatus
	    {
	        return status;
	    }
	    function generateTime()
	    {
	        second = time % 60;
	        minute = Math.floor(time / 60) % 60;
	        hour = Math.floor(time / 3600) % 60;
	        
	        second = (second < 10) ? '0'+second : second;
	        minute = (minute < 10) ? '0'+minute : minute;
	        hour = (hour < 10) ? '0'+hour : hour;
	        
	        $('div.timerkensa span.secondkensa').html(second);
	        $('div.timerkensa span.minutekensa').html(minute);
	        $('div.timerkensa span.hourkensa').html(hour);
	        if ($('#loc').val() == 'qa-fungsi') {
	        	if (minute == 6) {
		        	timerkensa.stop();
		        	$('div.timerkensa').hide();
		        	$('div.timeout').show();
		        	$('div.timeout').html('WAKTU HABIS');
		        	$('div.timeout').css('backgroundColor','red');
		        	$('div.timeout').css('color','white');
		        	audio_error.play();
		        }
	        }else if($('#loc').val() == 'qa-visual1'){
	        	if ($('#loc').val() == 'qa-fungsi') {
	        	if (minute == 4) {
		        	timerkensa.stop();
		        	$('div.timerkensa').hide();
		        	$('div.timeout').show();
		        	$('div.timeout').html('WAKTU HABIS');
		        	$('div.timeout').css('backgroundColor','red');
		        	$('div.timeout').css('color','white');
		        	audio_error.play();
		        }
	        }else if($('#loc').val() == 'qa-visual2'){
	        	if ($('#loc').val() == 'qa-fungsi') {
		        	if (minute == 3) {
			        	timerkensa.stop();
			        	$('div.timerkensa').hide();
			        	$('div.timeout').show();
			        	$('div.timeout').html('WAKTU HABIS');
			        	$('div.timeout').css('backgroundColor','red');
			        	$('div.timeout').css('color','white');
			        	audio_error.play();
			        }
		        }
	        }
	        }
	    }
	}
	 
	var timerkensa;
	$(document).ready(function(e) 
	{
	    timerkensa = new _timerkensa
	    (
	        function(time)
	        {
	            if(time == 0)
	            {
	                timerkensa.stop();
	                alert('time out');
	            }
	        }
	    );
	    timerkensa.reset(0);
	});
</script>
@endsection
