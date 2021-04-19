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
								<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Oleh</th>
								<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Repair</th>
								<?php if ($loc2 == 'repair-process'): ?>
									<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 15px;" >Action</th>
								<?php endif ?>
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
					<h4 id="judul_ng" style="font-weight: bold;text-align:center;background-color: #61d2ff;padding: 5px">NG List</h4>
					<div class="row">
						<div class="col-xs-12" id="ngDetail">
						</div>
						<div class="col-xs-12" id="ngDetailFix" style="display: none;padding-top: 5px">
							<center><button class="btn btn-primary" style="width:100%;font-size: 25px;font-weight: bold;" onclick="getNgChange()" id="ngFix">NG
							</button></center>
							<input type="hidden" id="ngFix2" value="NG">
						</div>
					</div>

					<h4 id="judul_onko" style="padding-top: 10px;font-weight: bold;text-align:center;background-color: #ffd375;padding: 5px">Pilih Onko</h4>
					<div class="row">
						<div class="col-xs-12" id="onkoBody">
						</div>
						<div class="col-xs-12" id="onkoBodyFix" style="display: none;padding-top: 5px">
							<center><button class="btn btn-warning" style="width:100%;font-size: 25px;font-weight: bold" onclick="getOnkoChange()" id="onkoFix">ONKO
							</button></center>
							<input type="hidden" id="onkoFix2" value="ONKO">
						</div>
					</div>
					<h4 id="judul_onko" style="padding-top: 10px;font-weight: bold;text-align:center;background-color: #75ff9f;padding: 5px">Pilih Operator Asal NG</h4>
						<select class="form-control" style="width: 100%;font-size:20px;padding:5px;text-align:center" id="operator_id_before_select" data-placeholder="Pilih Operator" onchange="changeOperator(this.id)"></select>
						<input type="hidden" id="operator_id_before" value="OPID">

					<div style="padding-top: 10px">
						<button id="confNg" style="width: 100%; margin-top: 10px; font-size: 3vw; padding:0; font-weight: bold; border-color: black; color: white;" onclick="confNgTemp()" class="btn btn-success">CONFIRM</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalGantiKunci">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<h4 id="judul_ngGantiKunci" style="font-weight: bold;text-align:center;background-color: #61d2ff;padding: 5px">Action</h4>
					<div class="row">
						<div class="col-xs-12" id="ngDetailGantiKunci">
						</div>
						<div class="col-xs-12" id="ngDetailFixGantiKunci" style="display: none;padding-top: 5px">
							<center><button class="btn btn-primary" style="width:100%;font-size: 25px;font-weight: bold;" onclick="getNgChangeGantiKunci()" id="ngFixGantiKunci">NG
							</button></center>
							<input type="hidden" id="ngFix2GantiKunci" value="NG">
						</div>
					</div>

					<h4 id="judul_onkoGantiKunci" style="padding-top: 10px;font-weight: bold;text-align:center;background-color: #ffd375;padding: 5px">Pilih Onko Yang Diganti</h4>
					<div class="row">
						<div class="col-xs-12" id="onkoBodyGantiKunci">
						</div>
						<div class="col-xs-12" id="onkoBodyFixGantiKunci" style="display: none;padding-top: 5px">
							<center><button class="btn btn-warning" style="width:100%;font-size: 25px;font-weight: bold" onclick="getOnkoChangeGantiKunci()" id="onkoFixGantiKunci">ONKO
							</button></center>
							<input type="hidden" id="onkoFix2GantiKunci" value="ONKO">
						</div>
					</div>

					<h4 id="judul_onkoGantiKunci2" style="padding-top: 10px;font-weight: bold;text-align:center;background-color: #ffd375;padding: 5px">Pilih Onko Pengganti</h4>
					<div class="row">
						<div class="col-xs-12" id="onkoBodyGantiKunci2">
						</div>
						<div class="col-xs-12" id="onkoBodyFixGantiKunci2" style="display: none;padding-top: 5px">
							<center><button class="btn btn-warning" style="width:100%;font-size: 25px;font-weight: bold" onclick="getOnkoChangeGantiKunci2()" id="onkoFixGantiKunci2">ONKO
							</button></center>
							<input type="hidden" id="onkoFix2GantiKunci2" value="ONKO">
						</div>
					</div>
					<div style="padding-top: 10px">
						<button id="confGantiKunci" style="width: 100%; margin-top: 10px; font-size: 3vw; padding:0; font-weight: bold; border-color: black; color: white;" onclick="confNgTempGantiKunci()" class="btn btn-success">CONFIRM</button>
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
					<div class="col-xs-12">
						<h4 style="font-weight: bold;text-align:center;background-color: #61d2ff;padding: 5px">NG List Tanpo Awase</h4>
						<div class="row">
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
								
							<input type="hidden" id="operator_id_before_tanpoawase1" value="OPID">
							<input type="hidden" id="operator_id_before_tanpoawase2" value="OPID">
							<input type="hidden" id="operator_id_before_tanpoawase3" value="OPID">
							<input type="hidden" id="operator_id_before_tanpoawase4" value="OPID">
							<input type="hidden" id="operator_id_before_tanpoawase5" value="OPID">
							<input type="hidden" id="operator_id_before_tanpoawase6" value="OPID">
							<input type="hidden" id="operator_id_before_tanpoawase7" value="OPID">
							<input type="hidden" id="operator_id_before_tanpoawase8" value="OPID">
							<input type="hidden" id="operator_id_before_tanpoawase9" value="OPID">
							<input type="hidden" id="operator_id_before_tanpoawase10" value="OPID">
							<input type="hidden" id="operator_id_before_tanpoawase11" value="OPID">
							<input type="hidden" id="operator_id_before_tanpoawase12" value="OPID">
							<input type="hidden" id="operator_id_before_tanpoawase13" value="OPID">
							<input type="hidden" id="operator_id_before_tanpoawase14" value="OPID">
							<input type="hidden" id="operator_id_before_tanpoawase15" value="OPID">
							<input type="hidden" id="operator_id_before_tanpoawase16" value="OPID">
							<button id="confNgOnkoTanpoAwase" style="width: 100%; margin-top: 10px; font-size: 3vw; padding:0; font-weight: bold; border-color: black; color: white;" onclick="confNgOnkoTanpoAwase()" class="btn btn-success">CONFIRM</button>
						</div>
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
	var timerkensa;
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

		$('#pilihan1_1').val('0').trigger('change');
		$('#pilihan1_2').val('0').trigger('change');
		$('#pilihan1_3').val('0').trigger('change');
		$('#pilihan1_4').val('0').trigger('change');
		$('#pilihan1_5').val('0').trigger('change');
		$('#pilihan1_6').val('0').trigger('change');
		$('#pilihan1_7').val('0').trigger('change');
		$('#pilihan1_8').val('0').trigger('change');
		$('#pilihan1_9').val('0').trigger('change');
		$('#pilihan1_10').val('0').trigger('change');
		$('#pilihan1_11').val('0').trigger('change');
		$('#pilihan1_12').val('0').trigger('change');
		$('#pilihan1_13').val('0').trigger('change');
		$('#pilihan1_14').val('0').trigger('change');
		$('#pilihan1_15').val('0').trigger('change');
		$('#pilihan1_16').val('0').trigger('change');

		$('#pilihan2_1').val('0').trigger('change');
		$('#pilihan2_2').val('0').trigger('change');
		$('#pilihan2_3').val('0').trigger('change');
		$('#pilihan2_4').val('0').trigger('change');
		$('#pilihan2_5').val('0').trigger('change');
		$('#pilihan2_6').val('0').trigger('change');
		$('#pilihan2_7').val('0').trigger('change');
		$('#pilihan2_8').val('0').trigger('change');
		$('#pilihan2_9').val('0').trigger('change');
		$('#pilihan2_10').val('0').trigger('change');
		$('#pilihan2_11').val('0').trigger('change');
		$('#pilihan2_12').val('0').trigger('change');
		$('#pilihan2_13').val('0').trigger('change');
		$('#pilihan2_14').val('0').trigger('change');
		$('#pilihan2_15').val('0').trigger('change');
		$('#pilihan2_16').val('0').trigger('change');
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
				fetchNgHistory();
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
			$('#operator_id_before_tanpoawase1').val("OPID");
			$('#operator_id_before_tanpoawase2').val("OPID");
			$('#operator_id_before_tanpoawase3').val("OPID");
			$('#operator_id_before_tanpoawase4').val("OPID");
			$('#operator_id_before_tanpoawase5').val("OPID");
			$('#operator_id_before_tanpoawase6').val("OPID");
			$('#operator_id_before_tanpoawase7').val("OPID");
			$('#operator_id_before_tanpoawase8').val("OPID");
			$('#operator_id_before_tanpoawase9').val("OPID");
			$('#operator_id_before_tanpoawase10').val("OPID");
			$('#operator_id_before_tanpoawase11').val("OPID");
			$('#operator_id_before_tanpoawase12').val("OPID");
			$('#operator_id_before_tanpoawase13').val("OPID");
			$('#operator_id_before_tanpoawase14').val("OPID");
			$('#operator_id_before_tanpoawase15').val("OPID");
			$('#operator_id_before_tanpoawase16').val("OPID");

			$('#operator_id_before_tanpoawase_select1').html("");
			$('#operator_id_before_tanpoawase_select2').html("");
			$('#operator_id_before_tanpoawase_select3').html("");
			$('#operator_id_before_tanpoawase_select4').html("");
			$('#operator_id_before_tanpoawase_select5').html("");
			$('#operator_id_before_tanpoawase_select6').html("");
			$('#operator_id_before_tanpoawase_select7').html("");
			$('#operator_id_before_tanpoawase_select8').html("");
			$('#operator_id_before_tanpoawase_select9').html("");
			$('#operator_id_before_tanpoawase_select10').html("");
			$('#operator_id_before_tanpoawase_select11').html("");
			$('#operator_id_before_tanpoawase_select12').html("");
			$('#operator_id_before_tanpoawase_select13').html("");
			$('#operator_id_before_tanpoawase_select14').html("");
			$('#operator_id_before_tanpoawase_select15').html("");
			$('#operator_id_before_tanpoawase_select16').html("");
			if (ng_name === "Tanpo Awase") {
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
								var opbeforetanposelect = "";
								opbeforetanposelect += '<option value="">Pilih Operator</option>';
								$.each(result.details, function(key, value) {
									opbeforetanposelect += '<option value="'+value.operator_id+'">'+value.operator_id+' - '+value.name+'</option>';
									// $('#operator_id_before_tanpoawase').val(value.operator_id);
								});
								$('#operator_id_before_tanpoawase_select1').append(opbeforetanposelect);
								$('#operator_id_before_tanpoawase_select2').append(opbeforetanposelect);
								$('#operator_id_before_tanpoawase_select3').append(opbeforetanposelect);
								$('#operator_id_before_tanpoawase_select4').append(opbeforetanposelect);
								$('#operator_id_before_tanpoawase_select5').append(opbeforetanposelect);
								$('#operator_id_before_tanpoawase_select6').append(opbeforetanposelect);
								$('#operator_id_before_tanpoawase_select7').append(opbeforetanposelect);
								$('#operator_id_before_tanpoawase_select8').append(opbeforetanposelect);
								$('#operator_id_before_tanpoawase_select9').append(opbeforetanposelect);
								$('#operator_id_before_tanpoawase_select10').append(opbeforetanposelect);
								$('#operator_id_before_tanpoawase_select11').append(opbeforetanposelect);
								$('#operator_id_before_tanpoawase_select12').append(opbeforetanposelect);
								$('#operator_id_before_tanpoawase_select13').append(opbeforetanposelect);
								$('#operator_id_before_tanpoawase_select14').append(opbeforetanposelect);
								$('#operator_id_before_tanpoawase_select15').append(opbeforetanposelect);
								$('#operator_id_before_tanpoawase_select16').append(opbeforetanposelect);
							}else{
								// $('#divOperatorTanpo').hide();
								$('#operator_id_before_tanpoawase_select1').append(value.process_before);
								$('#operator_id_before_tanpoawase_select2').append(value.process_before);
								$('#operator_id_before_tanpoawase_select3').append(value.process_before);
								$('#operator_id_before_tanpoawase_select4').append(value.process_before);
								$('#operator_id_before_tanpoawase_select5').append(value.process_before);
								$('#operator_id_before_tanpoawase_select6').append(value.process_before);
								$('#operator_id_before_tanpoawase_select7').append(value.process_before);
								$('#operator_id_before_tanpoawase_select8').append(value.process_before);
								$('#operator_id_before_tanpoawase_select9').append(value.process_before);
								$('#operator_id_before_tanpoawase_select10').append(value.process_before);
								$('#operator_id_before_tanpoawase_select11').append(value.process_before);
								$('#operator_id_before_tanpoawase_select12').append(value.process_before);
								$('#operator_id_before_tanpoawase_select13').append(value.process_before);
								$('#operator_id_before_tanpoawase_select14').append(value.process_before);
								$('#operator_id_before_tanpoawase_select15').append(value.process_before);
								$('#operator_id_before_tanpoawase_select16').append(value.process_before);
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
						// bodyNgOnko += '<div id="slider'+index+'"></div>';
						bodyNgOnko += '<div>';
						bodyNgOnko += '<select style="width:47%;font-size:20px;padding:5px;text-align:center" id="pilihan1_'+index+'" onchange="changeTanpoAwase(this.id)">';
						bodyNgOnko += '<option value="0">0</option>';
						bodyNgOnko += '<option value="1">1</option>';
						bodyNgOnko += '<option value="2">2</option>';
						bodyNgOnko += '<option value="3">3</option>';
						bodyNgOnko += '<option value="4">4</option>';
						bodyNgOnko += '<option value="5">5</option>';
						bodyNgOnko += '<option value="6">6</option>';
						bodyNgOnko += '<option value="7">7</option>';
						bodyNgOnko += '<option value="8">8</option>';
						bodyNgOnko += '<option value="9">9</option>';
						bodyNgOnko += '<option value="10">10</option>';
						bodyNgOnko += '<option value="11">11</option>';
						bodyNgOnko += '<option value="12">12</option>';
						bodyNgOnko += '</select>';
						bodyNgOnko += '<span style="width:20%;font-weight:bold"> - </span>';
						bodyNgOnko += '<select style="width:47%;font-size:20px;padding:5px;text-align:center" id="pilihan2_'+index+'" onchange="changeTanpoAwase(this.id)">';
						bodyNgOnko += '<option value="0">0</option>';
						bodyNgOnko += '<option value="1">1</option>';
						bodyNgOnko += '<option value="2">2</option>';
						bodyNgOnko += '<option value="3">3</option>';
						bodyNgOnko += '<option value="4">4</option>';
						bodyNgOnko += '<option value="5">5</option>';
						bodyNgOnko += '<option value="6">6</option>';
						bodyNgOnko += '<option value="7">7</option>';
						bodyNgOnko += '<option value="8">8</option>';
						bodyNgOnko += '<option value="9">9</option>';
						bodyNgOnko += '<option value="10">10</option>';
						bodyNgOnko += '<option value="11">11</option>';
						bodyNgOnko += '<option value="12">12</option>';
						bodyNgOnko += '</select>';
						bodyNgOnko += '</div>';
						bodyNgOnko += '<div style="text-align:middle;padding-top:10px" id="lokasi_choice'+index+'"><button class="btn btn-warning" style="width:24%" onclick="changeLokasi(\''+index+'\',\'A\')">A</button><button onclick="changeLokasi(\''+index+'\',\'B\')" style="margin-left:2px;width:24%" class="btn btn-warning">B</button><button onclick="changeLokasi(\''+index+'\',\'C\')" style="margin-left:2px;width:24%" class="btn btn-warning">C</button><button onclick="changeLokasi(\''+index+'\',\'D\')" style="margin-left:2px;width:24%" class="btn btn-warning">D</button></div>';
						bodyNgOnko += '<div style="display:none;padding-top:10px" id="lokasi_fix'+index+'"><button style="width:100%;font-weight:bold" onclick="changeLokasi2(\''+index+'\',\'E\')" class="btn btn-warning" id="lokasi_fix2'+index+'">E</button></div>';
						bodyNgOnko += '<div style="padding-top:10px">';
						bodyNgOnko += '<select class="form-control" style="width: 100%;font-size:20px;padding:5px;text-align:center" id="operator_id_before_tanpoawase_select'+index+'" data-placeholder="Pilih Operator" onchange="changeOperatorTanpo(\''+index+'\')">';
						bodyNgOnko += '</select>';
						bodyNgOnko += '</div>';
						bodyNgOnko += '</div>';
						index++;
					});

					$('#onkoBodyTanpoAwase').append(bodyNgOnko);

					// $("#slider1").roundSlider({
					//     sliderType: "range",
					//     handleShape: "dot",
					//     width: 35,
					//     radius: 100,
					//     value: 0,
					//     lineCap: "square",
					//     startAngle: 90,
					//     handleSize: "+12",
					//     max: "12",
					//     drag: function (value) {
					//         $("#value1").val(value.value);
					//     }
					// });

					// $("#slider2").roundSlider({
					//     sliderType: "range",
					//     handleShape: "dot",
					//     width: 35,
					//     radius: 100,
					//     value: 0,
					//     lineCap: "square",
					//     startAngle: 90,
					//     handleSize: "+12",
					//     max: "12",
					//     drag: function (value) {
					//         $("#value2").val(value.value);
					//     }
					// });

					// $("#slider3").roundSlider({
					//     sliderType: "range",
					//     handleShape: "dot",
					//     width: 35,
					//     radius: 100,
					//     value: 0,
					//     lineCap: "square",
					//     startAngle: 90,
					//     handleSize: "+12",
					//     max: "12",
					//     drag: function (value) {
					//         $("#value3").val(value.value);
					//     }
					// });

					// $("#slider4").roundSlider({
					//     sliderType: "range",
					//     handleShape: "dot",
					//     width: 35,
					//     radius: 100,
					//     value: 0,
					//     lineCap: "square",
					//     startAngle: 90,
					//     handleSize: "+12",
					//     max: "12",
					//     drag: function (value) {
					//         $("#value4").val(value.value);
					//     }
					// });

					// $("#slider5").roundSlider({
					//     sliderType: "range",
					//     handleShape: "dot",
					//     width: 35,
					//     radius: 100,
					//     value: 0,
					//     lineCap: "square",
					//     startAngle: 90,
					//     handleSize: "+12",
					//     max: "12",
					//     drag: function (value) {
					//         $("#value5").val(value.value);
					//     }
					// });

					// $("#slider6").roundSlider({
					//     sliderType: "range",
					//     handleShape: "dot",
					//     width: 35,
					//     radius: 100,
					//     value: 0,
					//     lineCap: "square",
					//     startAngle: 90,
					//     handleSize: "+12",
					//     max: "12",
					//     drag: function (value) {
					//         $("#value6").val(value.value);
					//     }
					// });

					// $("#slider7").roundSlider({
					//     sliderType: "range",
					//     handleShape: "dot",
					//     width: 35,
					//     radius: 100,
					//     value: 0,
					//     lineCap: "square",
					//     startAngle: 90,
					//     handleSize: "+12",
					//     max: "12",
					//     drag: function (value) {
					//         $("#value7").val(value.value);
					//     }
					// });

					// $("#slider8").roundSlider({
					//     sliderType: "range",
					//     handleShape: "dot",
					//     width: 35,
					//     radius: 100,
					//     value: 0,
					//     lineCap: "square",
					//     startAngle: 90,
					//     handleSize: "+12",
					//     max: "12",
					//     drag: function (value) {
					//         $("#value8").val(value.value);
					//     }
					// });

					// $("#slider9").roundSlider({
					//     sliderType: "range",
					//     handleShape: "dot",
					//     width: 35,
					//     radius: 100,
					//     value: 0,
					//     lineCap: "square",
					//     startAngle: 90,
					//     handleSize: "+12",
					//     max: "12",
					//     drag: function (value) {
					//         $("#value9").val(value.value);
					//     }
					// });

					// $("#slider10").roundSlider({
					//     sliderType: "range",
					//     handleShape: "dot",
					//     width: 35,
					//     radius: 100,
					//     value: 0,
					//     lineCap: "square",
					//     startAngle: 90,
					//     handleSize: "+12",
					//     max: "12",
					//     drag: function (value) {
					//         $("#value10").val(value.value);
					//     }
					// });

					// $("#slider11").roundSlider({
					//     sliderType: "range",
					//     handleShape: "dot",
					//     width: 35,
					//     radius: 100,
					//     value: 0,
					//     lineCap: "square",
					//     startAngle: 90,
					//     handleSize: "+12",
					//     max: "12",
					//     drag: function (value) {
					//         $("#value11").val(value.value);
					//     }
					// });

					// $("#slider12").roundSlider({
					//     sliderType: "range",
					//     handleShape: "dot",
					//     width: 35,
					//     radius: 100,
					//     value: 0,
					//     lineCap: "square",
					//     startAngle: 90,
					//     handleSize: "+12",
					//     max: "12",
					//     drag: function (value) {
					//         $("#value12").val(value.value);
					//     }
					// });

					// $("#slider13").roundSlider({
					//     sliderType: "range",
					//     handleShape: "dot",
					//     width: 35,
					//     radius: 100,
					//     value: 0,
					//     lineCap: "square",
					//     startAngle: 180,
					//     handleSize: "+12",
					//     max: "12",
					//     drag: function (value) {
					//         $("#value13").val(value.value);
					//     }
					// });

					// $("#slider14").roundSlider({
					//     sliderType: "range",
					//     handleShape: "dot",
					//     width: 35,
					//     radius: 100,
					//     value: 0,
					//     lineCap: "square",
					//     startAngle: 90,
					//     handleSize: "+12",
					//     max: "12",
					//     drag: function (value) {
					//         $("#value14").val(value.value);
					//     }
					// });

					// $("#slider15").roundSlider({
					//     sliderType: "range",
					//     handleShape: "dot",
					//     width: 35,
					//     radius: 100,
					//     value: 0,
					//     lineCap: "square",
					//     startAngle: 90,
					//     handleSize: "+12",
					//     max: "12",
					//     drag: function (value) {
					//         $("#value15").val(value.value);
					//     }
					// });

					// $("#slider16").roundSlider({
					//     sliderType: "range",
					//     handleShape: "dot",
					//     width: 35,
					//     radius: 100,
					//     value: 0,
					//     lineCap: "square",
					//     startAngle: 90,
					//     handleSize: "+12",
					//     max: "12",
					//     drag: function (value) {
					//         $("#value16").val(value.value);
					//     }
					// });
					$('#modalNgTanpoAwase').modal('show');
				});
			}else if (ng_name === 'Ganti Kunci') {
				var btn = document.getElementById('confGantiKunci');
				btn.disabled = false;
				btn.innerText = 'Confirm'

				var location = '{{$loc_spec}}';
				var data = {
					ng_name:ng_name,
					location:location,
					process:$('#process').val()
				}
				var bodyDetail = "";
				$('#ngDetailFixGantiKunci').hide();
				$('#ngDetailGantiKunci').show();
				$('#ngDetailGantiKunci').html("");

				var bodyNgOnko = "";
				$('#onkoBodyFixGantiKunci').hide();
				$('#onkoBodyGantiKunci').show();
				$('#onkoBodyGantiKunci').html("");

				var bodyNgOnko2 = "";
				$('#onkoBodyFixGantiKunci2').hide();
				$('#onkoBodyGantiKunci2').show();
				$('#onkoBodyGantiKunci2').html("");

				$.get('{{ url("fetch/assembly/ng_detail") }}', data, function(result, status, xhr){
					bodyDetail += '<div class="row">';
					$.each(result.ng_detail, function(key, value) {
						bodyDetail += '<div class="col-xs-4" style="padding-top: 10px">';
						bodyDetail += '<center><button class="btn btn-primary" id="'+value.ng_name+' - '+value.ng_detail+'" style="width: 250px;font-size: 25px;" onclick="getNgGantiKunci(this.id,\''+value.process_before+'\')">'+value.ng_name+' - '+value.ng_detail;
						bodyDetail += '</button></center></div>';
						$('#judul_ng').html(value.ng_name);
					});
					bodyDetail += '</div>';

					$('#ngDetailGantiKunci').append(bodyDetail);

					bodyNgOnko += '<div class="row">';
					$.each(result.onko, function(key, value) {
						bodyNgOnko += '<div class="col-xs-3" style="padding-top: 5px">';
						bodyNgOnko += '<center><button class="btn btn-warning" id="'+value.key+' ('+value.nomor+')" style="width: 180px;font-size: 20px" onclick="getOnkoGantiKunci(this.id)">'+value.key+' ('+value.nomor+')';
						bodyNgOnko += '</button></center></div>';
					});
					bodyNgOnko += '</div>';

					$('#onkoBodyGantiKunci').append(bodyNgOnko);

					bodyNgOnko2 += '<div class="row">';
					$.each(result.onko, function(key, value) {
						bodyNgOnko2 += '<div class="col-xs-3" style="padding-top: 5px">';
						bodyNgOnko2 += '<center><button class="btn btn-warning" id="'+value.key+' ('+value.nomor+')" style="width: 180px;font-size: 20px" onclick="getOnkoGantiKunci2(this.id)">'+value.key+' ('+value.nomor+')';
						bodyNgOnko2 += '</button></center></div>';
					});
					bodyNgOnko2 += '</div>';

					$('#onkoBodyGantiKunci2').append(bodyNgOnko2);
					$('#modalGantiKunci').modal('show');
				});
			}
			else{
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
					bodyDetail += '<div class="row">';
					$.each(result.ng_detail, function(key, value) {
						bodyDetail += '<div class="col-xs-4" style="padding-top: 10px">';
						bodyDetail += '<center><button class="btn btn-primary" id="'+value.ng_name+' - '+value.ng_detail+'" style="width: 250px;font-size: 25px;" onclick="getNg(this.id,\''+value.process_before+'\')">'+value.ng_name+' - '+value.ng_detail;
						bodyDetail += '</button></center></div>';
						$('#judul_ng').html(value.ng_name);
					});
					bodyDetail += '</div>';

					$('#ngDetail').append(bodyDetail);

					bodyNgOnko += '<div class="row">';
					$.each(result.onko, function(key, value) {
						bodyNgOnko += '<div class="col-xs-3" style="padding-top: 5px">';
						bodyNgOnko += '<center><button class="btn btn-warning" id="'+value.key+' ('+value.nomor+')" style="width: 180px;font-size: 20px" onclick="getOnko(this.id)">'+value.key+' ('+value.nomor+')';
						bodyNgOnko += '</button></center></div>';
					});
					bodyNgOnko += '</div>';

					$('#onkoBody').append(bodyNgOnko);
					$('#modalNg').modal('show');
				});
			}
		}
	}

	function changeOperatorTanpo(id) {
		$('#operator_id_before_tanpoawase'+id).val($('#operator_id_before_tanpoawase_select'+id).val());
	}

	function changeOperator(id) {
		$('#operator_id_before').val($('#operator_id_before_select').val());
	}

	function changeTanpoAwase(id) {
		var ids = id.split('_');
		$('#value'+ids[1]).val($('#pilihan1_'+ids[1]).val()+','+$('#pilihan2_'+ids[1]).val());
	}

	function changeLokasi(index,lokasi) {
		$('#lokasi_fix2'+index).html(lokasi);
		$('#lokasi_fix'+index).show();
		$('#lokasi_choice'+index).hide();
	}

	function changeLokasi2(index,lokasi) {
		$('#lokasi_fix2'+index).html(lokasi);
		$('#lokasi_fix'+index).hide();
		$('#lokasi_choice'+index).show();
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
				if (value.ng_name == 'Tanpo Awase') {
					bodyNgTemp += '<td style="font-size: 15px;">'+value.ongko+' - '+value.value_lokasi+'</td>';
				}else{
					bodyNgTemp += '<td style="font-size: 15px;">'+value.ongko+'</td>';
				}
				bodyNgTemp += '<td style="font-size: 15px;">'+value.location+'</td>';
				bodyNgTemp += '<td style="font-size: 15px;">'+value.name+'</td>';
				if ($('#location_now2').val() == 'repair-process') {
					if (value.repair_status == null) {
						bodyNgTemp += '<td style="font-size: 15px;"></td>';
						bodyNgTemp += '<td style="font-size: 15px;"><button class="btn btn-warning btn-sm" onclick="repairProcess(\''+value.id+'\')">Repairing</button></td>';
					}else{
						bodyNgTemp += '<td style="font-size: 15px;">'+value.repair_status+'</td>';
						bodyNgTemp += '<td style="font-size: 15px;"></td>';
					}
				}else{
					if (value.repair_status == null) {
						bodyNgTemp += '<td style="font-size: 15px;"></td>';
					}else{
						bodyNgTemp += '<td style="font-size: 15px;">'+value.repair_status+'</td>';
					}
				}
				bodyNgTemp += "</tr>";
			});

			$('#ngHistoryBody').append(bodyNgTemp);
		});
	}

	function repairProcess(id) {
		if (confirm("Apakah Anda selesai melakukan Repair?")) {
			var employee_id = $('#employee_id').val();
			var data = {
				id:id,
				employee_id:employee_id,
			}

			$.post('{{ url("input/assembly/repair_process") }}', data, function(result, status, xhr){
				if (result.status) {
					fetchNgHistory();
					openSuccessGritter('Success',result.message);
				}else{
					openErrorGritter('Error!',result.message);
				}
			});
		}
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
				if (value.ng_name == 'Tanpo Awase') {
					bodyNgTemp += '<td style="font-size: 20px;">'+value.ongko+' - '+value.value_lokasi+'</td>';
				}else{
					bodyNgTemp += '<td style="font-size: 20px;">'+value.ongko+'</td>';
				}
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
				$('#operator_id_before_select').html("");
				var opbfsel = "";
				opbfsel += '<option value="">Pilih Operator</option>';
				$.each(result.details, function(key, value) {
					opbfsel += '<option value="'+value.operator_id+'">'+value.operator_id+' - '+value.name+'</option>';
				});
				$('#operator_id_before_select').append(opbfsel);
			}else{
				$('#operator_id_before').val(process_before);
			}
		});
		$('#ngDetail').hide();
		$('#ngDetailFix').show();
		$('#ngFix').html(value);
		$('#ngFix2').val(value);
	}

	function getNgGantiKunci(value,process_before) {
		$('#ngDetailGantiKunci').hide();
		$('#ngDetailFixGantiKunci').show();
		$('#ngFixGantiKunci').html(value);
		$('#ngFix2GantiKunci').val(value);
	}

	function getNgChange() {
		$('#ngDetail').show();
		$('#ngDetailFix').hide();
		$('#ngFix').html("NG");
		$('#ngFix2').val("NG");
		$('#operator_id_before').val("OPID");
	}

	function getNgChangeGantiKunci() {
		$('#ngDetailGantiKunci').show();
		$('#ngDetailFixGantiKunci').hide();
		$('#ngFixGantiKunci').html("NG");
		$('#ngFix2GantiKunci').val("NG");
	}

	function getOnko(value) {
		$('#onkoBody').hide();
		$('#onkoBodyFix').show();
		$('#onkoFix').html(value);
		$('#onkoFix2').val(value);
	}

	function getOnkoGantiKunci(value) {
		$('#onkoBodyGantiKunci').hide();
		$('#onkoBodyFixGantiKunci').show();
		$('#onkoFixGantiKunci').html(value);
		$('#onkoFix2GantiKunci').val(value);
	}
	function getOnkoGantiKunci2(value) {
		$('#onkoBodyGantiKunci2').hide();
		$('#onkoBodyFixGantiKunci2').show();
		$('#onkoFixGantiKunci2').html(value);
		$('#onkoFix2GantiKunci2').val(value);
	}

	function getOnkoChange() {
		$('#onkoBody').show();
		$('#onkoBodyFix').hide();
		$('#onkoFix').html("ONKO");
		$('#onkoFix2').val("ONKO");
	}

	function getOnkoChangeGantiKunci() {
		$('#onkoBodyGantiKunci').show();
		$('#onkoBodyFixGantiKunci').hide();
		$('#onkoFixGantiKunci').html("ONKO");
		$('#onkoFix2GantiKunci').val("ONKO");
	}

	function getOnkoChangeGantiKunci2() {
		$('#onkoBodyGantiKunci2').show();
		$('#onkoBodyFixGantiKunci2').hide();
		$('#onkoFixGantiKunci2').html("ONKO");
		$('#onkoFix2GantiKunci2').val("ONKO");
	}

	function getOnkoDetail(value) {
		$('#onkoBodyDetail').hide();
		$('#onkoBodyDetailFix').show();
		$('#onkoDetailFix').html(value);
		$('#onkoDetailFix2').val(value);
	}

	function getOnkoDetailChange() {
		$('#onkoBodyDetail').show();
		$('#onkoBodyDetailFix').hide();
		$('#onkoDetailFix').html("ONKO");
		$('#onkoDetailFix2').val("ONKO");
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
		var lokasi = [];
		var onko_ng = [];
		var operator = [];
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
				var idlokasi = '#lokasi_fix2'+a;
				if ($(idvalue).val() == "0" || $(idvalue).val() == '0,0' || $(idlokasi).text() == 'E') {
					
				}else{
					onko_ng.push(onko[i]);
					var valuesplit = $(idvalue).val().split(",");
					value_atas.push(valuesplit[0]);
					value_bawah.push(valuesplit[1]);
					lokasi.push($(idlokasi).text());
					operator.push($('#operator_id_before_tanpoawase'+a).val());
					// if ($(idvalue).text() != 'E') {

					// }
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
				lokasi:lokasi,
				origin_group_code : '041',
				operator_id : operator,
			}

			$.post('{{ url("input/assembly/ng_temp") }}', data, function(result, status, xhr){
				if(result.status){
					var btn = document.getElementById('confNgOnkoTanpoAwase');
					btn.disabled = true;
					btn.innerText = 'Posting...';
					
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

	function confNgTempGantiKunci() {
		if ($('#ngFix2GantiKunci').val() == "NG" || $('#onkoFix2GantiKunci').val() == "ONKO" || $('#onkoFix2GantiKunci2').val() == "ONKO") {
			audio_error.play();
			openErrorGritter('Error!', "Harus Dipilih Semua!");
		}else{
			var btn = document.getElementById('confGantiKunci');
			btn.disabled = true;
			btn.innerText = 'Posting...';

			var data = {
				tag : $('#tag2').val(),
				employee_id : $('#employee_id').val(),
				serial_number : $('#serial_number2').val(),
				model : $('#model2').val(),
				location : $('#location_now2').val(),
				started_at : $('#started_at').val(),
				ng: $('#ngFix2GantiKunci').val(),
				onko: $('#onkoFix2GantiKunci').val(),
				onko2: $('#onkoFix2GantiKunci2').val(),
				origin_group_code : '041',
			}

			$.post('{{ url("input/assembly/ganti_kunci") }}', data, function(result, status, xhr){
				if(result.status){
					var btn = document.getElementById('confGantiKunci');
					btn.disabled = false;
					btn.innerText = 'CONFIRM';
					$('#modalGantiKunci').modal('hide');
					fetchNgTemp();
					openSuccessGritter('Success!', result.message);
				}
				else{
					var btn = document.getElementById('confGantiKunci');
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

				$('#pilihan1_1').val('0').trigger('change');
				$('#pilihan1_2').val('0').trigger('change');
				$('#pilihan1_3').val('0').trigger('change');
				$('#pilihan1_4').val('0').trigger('change');
				$('#pilihan1_5').val('0').trigger('change');
				$('#pilihan1_6').val('0').trigger('change');
				$('#pilihan1_7').val('0').trigger('change');
				$('#pilihan1_8').val('0').trigger('change');
				$('#pilihan1_9').val('0').trigger('change');
				$('#pilihan1_10').val('0').trigger('change');
				$('#pilihan1_11').val('0').trigger('change');
				$('#pilihan1_12').val('0').trigger('change');
				$('#pilihan1_13').val('0').trigger('change');
				$('#pilihan1_14').val('0').trigger('change');
				$('#pilihan1_15').val('0').trigger('change');
				$('#pilihan1_16').val('0').trigger('change');

				$('#pilihan2_1').val('0').trigger('change');
				$('#pilihan2_2').val('0').trigger('change');
				$('#pilihan2_3').val('0').trigger('change');
				$('#pilihan2_4').val('0').trigger('change');
				$('#pilihan2_5').val('0').trigger('change');
				$('#pilihan2_6').val('0').trigger('change');
				$('#pilihan2_7').val('0').trigger('change');
				$('#pilihan2_8').val('0').trigger('change');
				$('#pilihan2_9').val('0').trigger('change');
				$('#pilihan2_10').val('0').trigger('change');
				$('#pilihan2_11').val('0').trigger('change');
				$('#pilihan2_12').val('0').trigger('change');
				$('#pilihan2_13').val('0').trigger('change');
				$('#pilihan2_14').val('0').trigger('change');
				$('#pilihan2_15').val('0').trigger('change');
				$('#pilihan2_16').val('0').trigger('change');
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

		$('#pilihan1_1').val('0').trigger('change');
		$('#pilihan1_2').val('0').trigger('change');
		$('#pilihan1_3').val('0').trigger('change');
		$('#pilihan1_4').val('0').trigger('change');
		$('#pilihan1_5').val('0').trigger('change');
		$('#pilihan1_6').val('0').trigger('change');
		$('#pilihan1_7').val('0').trigger('change');
		$('#pilihan1_8').val('0').trigger('change');
		$('#pilihan1_9').val('0').trigger('change');
		$('#pilihan1_10').val('0').trigger('change');
		$('#pilihan1_11').val('0').trigger('change');
		$('#pilihan1_12').val('0').trigger('change');
		$('#pilihan1_13').val('0').trigger('change');
		$('#pilihan1_14').val('0').trigger('change');
		$('#pilihan1_15').val('0').trigger('change');
		$('#pilihan1_16').val('0').trigger('change');

		$('#pilihan2_1').val('0').trigger('change');
		$('#pilihan2_2').val('0').trigger('change');
		$('#pilihan2_3').val('0').trigger('change');
		$('#pilihan2_4').val('0').trigger('change');
		$('#pilihan2_5').val('0').trigger('change');
		$('#pilihan2_6').val('0').trigger('change');
		$('#pilihan2_7').val('0').trigger('change');
		$('#pilihan2_8').val('0').trigger('change');
		$('#pilihan2_9').val('0').trigger('change');
		$('#pilihan2_10').val('0').trigger('change');
		$('#pilihan2_11').val('0').trigger('change');
		$('#pilihan2_12').val('0').trigger('change');
		$('#pilihan2_13').val('0').trigger('change');
		$('#pilihan2_14').val('0').trigger('change');
		$('#pilihan2_15').val('0').trigger('change');
		$('#pilihan2_16').val('0').trigger('change');
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
