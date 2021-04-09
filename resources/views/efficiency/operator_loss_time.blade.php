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
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		font-size: 1.2vw;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		font-size: 1.5vw;
		padding-top: 5px;
		padding-bottom: 5px;
		vertical-align: middle;
		background-color: RGB(252, 248, 227);
		font-weight: bold;
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
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-11">
			<div class="input-group">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
					<i class="fa fa-credit-card" style="background-color: orange;"></i>
				</div>
				<input type="text" style="text-align: center;" class="form-control" id="employee_tag" name="employee_tag" placeholder="Scan ID Card...">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
					<i class="fa fa-credit-card" style="background-color: orange;"></i>
				</div>
			</div>
		</div>
		<div class="col-xs-1" style="padding-left: 0px;">
			<button class="btn btn-info" style="width: 100%;" onclick="modalRecord()"><i class="fa fa-list"></i> Record</button>
		</div>
		<div class="col-xs-12" style="margin-top: 15px;">
			<div class="row">
				<div class="col-xs-2">
					<table class="table table-bordered table-striped table-hover">
						<thead style="background-color: orange;">
							<tr>
								<th colspan="2">WI-FA</th>
							</tr>
						</thead>
						<tbody id="wifaBody">
						</tbody>
					</table>
				</div>
				<div class="col-xs-2">
					<table class="table table-bordered table-striped table-hover">
						<thead style="background-color: orange;">
							<tr>
								<th colspan="2">WI-ST</th>
							</tr>
						</thead>
						<tbody id="wistBody">
						</tbody>
					</table>
				</div>
				<div class="col-xs-2">
					<table class="table table-bordered table-striped table-hover">
						<thead style="background-color: orange;">
							<tr>
								<th colspan="2">WI-WP</th>
							</tr>
						</thead>
						<tbody id="wiwpBody">
						</tbody>
					</table>
				</div>
				<div class="col-xs-2">
					<table class="table table-bordered table-striped table-hover">
						<thead style="background-color: orange;">
							<tr>
								<th colspan="2">WI-BPP</th>
							</tr>
						</thead>
						<tbody id="wibppBody">
						</tbody>
					</table>
				</div>
				<div class="col-xs-2">
					<table class="table table-bordered table-striped table-hover">
						<thead style="background-color: orange;">
							<tr>
								<th colspan="2">WI-KPP</th>
							</tr>
						</thead>
						<tbody id="wikppBody">
						</tbody>
					</table>
				</div>
				<div class="col-xs-2">
					<table class="table table-bordered table-striped table-hover">
						<thead style="background-color: orange;">
							<tr>
								<th colspan="2">EI</th>
							</tr>
						</thead>
						<tbody id="eiBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalRecord">
		<div class="modal-dialog modal-lg" style="width: 70%;">
			<div class="modal-content">
				<div class="modal-header">
					<h3 style="padding:10px; margin:0; font-weight: bold; background-color: orange; text-align: center">Operator Lost Time Record</h3>
					<div class="modal-body no-padding">
						<div class="row" style="padding-top:10px;">
							<div class="col-md-4 col-md-offset-2">
								<div class="form-group">
									<label>Date From</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="record_from">
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Date To</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="record_to">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4 col-md-offset-2">
								<div class="form-group">
									<label>Employee ID</label>
									<select class="form-control select2" multiple="multiple" name="employee_id" id='record_employee_id' data-placeholder="Select Employee ID" style="width: 100%;">
										<option value=""></option>
										@php
										$employee_id = array();
										@endphp
										@foreach($employees as $data)
										@if(!in_array($data->employee_id, $employee_id))
										<option value="{{ $data->employee_id }}">{{ $data->employee_id }} - {{ $data->NAME }}</option>
										@php
										array_push($employee_id, $data->employee_id);
										@endphp
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Department</label>
									<select class="form-control select2" multiple="multiple" name="department" id='record_department' data-placeholder="Select Department" style="width: 100%;">
										<option value=""></option>
										@php
										$department = array();
										@endphp
										@foreach($employees as $data)
										@if(!in_array($data->department, $department))
										<option value="{{ $data->department }}">{{ $data->department }}</option>
										@php
										array_push($department, $data->department);
										@endphp
										@endif
										@endforeach
									</select>
								</div>
							</div>	
						</div>
						<div class="row">
							<div class="col-md-4 col-md-offset-2">
								<div class="form-group">
									<label>Section</label>
									<select class="form-control select2" multiple="multiple" name="section" id='record_section' data-placeholder="Select Section" style="width: 100%;">
										<option value=""></option>
										@php
										$section = array();
										@endphp
										@foreach($employees as $data)
										@if(!in_array($data->section, $section))
										<option value="{{ $data->section }}">{{ $data->section }}</option>
										@php
										array_push($section, $data->section);
										@endphp
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Sub Group</label>
									<select class="form-control select2" multiple="multiple" name="group" id='record_group' data-placeholder="Select Group" style="width: 100%;">
										<option value=""></option>
										@php
										$group = array();
										@endphp
										@foreach($employees as $data)
										@if(!in_array($data->group, $group))
										<option value="{{ $data->group }}">{{ $data->group }}</option>
										@php
										array_push($group, $data->group);
										@endphp
										@endif
										@endforeach
									</select>
								</div>
							</div>			
						</div>
						<div class="row">
							<div class="col-md-4 col-md-offset-6">
								<div class="form-group pull-right">
									<a href="javascript:void(0)" onClick="clearAll()" class="btn btn-danger">Clear</a>
									<button id="search" onClick="fetchRecord()" class="btn btn-primary">Search</button>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<table id="recordTable" class="table table-bordered table-striped table-hover">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="padding-top: 1px; padding-bottom: 1px; font-size: 1vw; width: 1%;">ID</th>
											<th style="padding-top: 1px; padding-bottom: 1px; font-size: 1vw; width: 6%;">Name</th>
											<th style="padding-top: 1px; padding-bottom: 1px; font-size: 1vw; width: 8%;">Dept</th>
											<th style="padding-top: 1px; padding-bottom: 1px; font-size: 1vw; width: 3%;">Sect</th>
											<th style="padding-top: 1px; padding-bottom: 1px; font-size: 1vw; width: 3%;">Group</th>
											<th style="padding-top: 1px; padding-bottom: 1px; font-size: 1vw; width: 3%;">Sub</th>
											<th style="padding-top: 1px; padding-bottom: 1px; font-size: 1vw; width: 1%;">Reason</th>
											<th style="padding-top: 1px; padding-bottom: 1px; font-size: 1vw; width: 1%;">From</th>
											<th style="padding-top: 1px; padding-bottom: 1px; font-size: 1vw; width: 1%;">To</th>
											<th style="padding-top: 1px; padding-bottom: 1px; font-size: 1vw; width: 1%;">Duration<br>(Minute)</th>
										</tr>
									</thead>
									<tbody id="recordTableBody">
									</tbody>
									<tfoot>
									</tfoot>
								</table>
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="modalReason">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<table class="table table-bordered table-striped table-hover">
							<thead>
								<tr>
									<th colspan="2" style="font-weight: bold; font-size: 1.5vw;">DATA KARYAWAN</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>NIK:</td>
									<td><input style="width: 100%;" type="text" id="employee_id" readonly></td>
								</tr>
								<tr>
									<td>Nama:</td>
									<td><input style="width: 100%;" type="text" id="employee_name" readonly></td>
								</tr>
							</tbody>
						</table>
						<input style="width: 100%;" type="hidden" id="position">
						<input style="width: 100%;" type="hidden" id="division">
						<input style="width: 100%;" type="hidden" id="department">
						<input style="width: 100%;" type="hidden" id="section">
						<input style="width: 100%;" type="hidden" id="group">
						<input style="width: 100%;" type="hidden" id="sub_group">
						<input style="width: 100%;" type="hidden" id="cost_center">
						<center><h2 style="font-weight: bold;">Pilih Keperluan:</h2></center>
						<button style="width: 100%; font-weight: bold; margin-bottom: 5px; font-size: 2vw;" class="btn btn-success" onclick="confr('toilet')">TOILET</button>
						<button style="width: 100%; font-weight: bold; margin-bottom: 5px; font-size: 2vw;" class="btn btn-success" onclick="confr('minum')">MINUM</button>
						<button style="width: 100%; font-weight: bold; margin-bottom: 5px; font-size: 2vw;" class="btn btn-success" onclick="confr('serikat')">SERIKAT</button>
						<button style="width: 100%; font-weight: bold; margin-bottom: 5px; font-size: 2vw;" class="btn btn-success" onclick="confr('office')">OFFICE</button>
						<button style="width: 100%; font-weight: bold; margin-bottom: 5px; font-size: 2vw;" class="btn btn-success" onclick="confr('klinik')">KLINIK</button>
						<button style="width: 100%; font-weight: bold; margin-bottom: 5px; font-size: 2vw;" class="btn btn-success" onclick="confr('yewo')">YEWO</button>
						<button style="width: 100%; font-weight: bold; margin-top: 25px; font-size: 2vw;" class="btn btn-danger" onclick="clearAll()">BATAL</button>
					</div>
				</div>
			</div>
		</div>
	</div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {

		$('#record_from').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#record_to').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('.select2').select2();

		clearAll();
		setTime();
		fetchOperatorLossTime();
		setInterval(setTime, 1000);
		setInterval(fetchOperatorLossTime, 10000);
	});

	var in_time = [];
	function setTime() {
		for (var i = 0; i < in_time.length; i++) {
			var duration = diff_seconds(new Date(), in_time[i]);
			document.getElementById("hours"+i).innerHTML = pad(parseInt(duration / 3600));
			document.getElementById("minutes"+i).innerHTML = pad(parseInt((duration % 3600) / 60));
			document.getElementById("seconds"+i).innerHTML = pad(duration % 60);
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

	function focusTag(){
		$('#employee_tag').focus();
	}

	function clearAll(){
		$('#modalRecord').modal('hide');
		$('#record_from').val("");
		$('#record_to').val("");
		$('#record_employee_id').val([]).change();
		$('#record_department').val([]).change();
		$('#record_section').val([]).change();
		$('#record_group').val([]).change();
		$('#employee_tag').val("");
		$('#employee_id').val("");
		$('#employee_name').val("");
		$('#position').val("");
		$('#division').val("");
		$('#department').val("");
		$('#section').val("");
		$('#group').val("");
		$('#sub_group').val("");
		$('#cost_center').val("");
		$('#modalReason').modal('hide');
		$('#loading').hide();
		$('#employee_tag').focus();
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	$('#employee_tag').keydown(function(event) {
		// $('#loading').show();
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#employee_tag").val().length == 10){
				var data = {
					tag : $("#employee_tag").val()
				}
				
				$.get('{{ url("scan/efficiency/employee") }}', data, function(result, status, xhr){
					if(result.status){
						if(result.code == 'pergi'){
							clearAll();
							$('#employee_id').val(result.employee.employee_id);
							$('#employee_name').val(result.employee.name);
							$('#position').val(result.employee.position);
							$('#division').val(result.employee.division);
							$('#department').val(result.employee.department);
							$('#section').val(result.employee.section);
							$('#group').val(result.employee.group);
							$('#sub_group').val(result.employee.sub_group);
							$('#cost_center').val(result.employee.cost_center);
							$('#modalReason').modal('show');
						}
						else{
							audio_ok.play();
							openSuccessGritter('Success', result.message);
							clearAll();
							setInterval(focusTag, 1000);
						}
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						clearAll();
						setInterval(focusTag, 1000);
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				clearAll();
				setInterval(focusTag, 1000);
			}			
		}
	});

	function modalRecord(){
		clearAll();
		$('#modalRecord').modal('show');
	}

	function fetchRecord(){
		$('#loading').show();
		var record_from = $('#record_from').val();
		var record_to = $('#record_to').val();
		var record_section = $('#record_section').val();
		var record_department = $('#record_department').val();
		var record_group = $('#record_group').val();
		var record_employee_id = $('#record_employee_id').val();
		
		var data = {
			record_from:record_from,
			record_to:record_to,
			record_section:record_section,
			record_department:record_department,
			record_group:record_group,
			record_employee_id:record_employee_id
		}	
		$.get('{{ url("fetch/efficiency/operator_loss_time_log") }}', data, function(result, status, xhr){
			if(result.status){
				$('#recordTable').DataTable().clear();
				$('#recordTable').DataTable().destroy();
				$('#recordTableBody').html("");
				var recordTableBody = "";

				$.each(result.operator_loss_time_logs, function(key, value){
					recordTableBody += '<tr>';
					recordTableBody += '<td style="font-size: 1vw; font-weight: normal;">'+value.employee_id+'</td>';
					recordTableBody += '<td style="font-size: 1vw; font-weight: normal;">'+value.employee_name+'</td>';
					recordTableBody += '<td style="font-size: 1vw; font-weight: normal;">'+value.department+'</td>';
					recordTableBody += '<td style="font-size: 1vw; font-weight: normal;">'+value.section+'</td>';
					recordTableBody += '<td style="font-size: 1vw; font-weight: normal;">'+value.group+'</td>';
					recordTableBody += '<td style="font-size: 1vw; font-weight: normal;">'+value.sub_group+'</td>';
					recordTableBody += '<td style="font-size: 1vw; font-weight: normal;">'+value.reason+'</td>';
					recordTableBody += '<td style="font-size: 1vw; font-weight: normal;">'+value.started_at+'</td>';
					recordTableBody += '<td style="font-size: 1vw; font-weight: normal;">'+value.created_at+'</td>';
					recordTableBody += '<td style="font-size: 1vw; font-weight: normal;">'+value.duration+'</td>';
					recordTableBody += '</tr>';
				});

				$('#recordTableBody').append(recordTableBody);

				$('#recordTable').DataTable({
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
						},
						{
							extend: 'print',
							className: 'btn btn-warning',
							text: '<i class="fa fa-print"></i> Print',
							exportOptions: {
								columns: ':not(.notexport)'
							}
						},
						]
					},
					'paging': false,
					'lengthChange': true,
					'searching': true,
					'ordering': true,
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});

				$('#loading').hide();
			}
			else{
				alert('Unidentified ERROR!');
				audio_error.play();
				clearAll();
				setInterval(focusTag, 1000);				
			}
		});
	}

	function fetchOperatorLossTime(){
		$.get('{{ url("fetch/efficiency/operator_loss_time") }}', function(result, status, xhr){
			if(result.status){

				var wifaBody = "";
				var wistBody = "";
				var wiwpBody = "";
				var wibppBody = "";
				var wikppBody = "";
				var eiBody = "";

				$('#wifaBody').html('');
				$('#wistBody').html('');
				$('#wiwpBody').html('');
				$('#wibppBody').html('');
				$('#wikppBody').html('');
				$('#eiBody').html('');

				var operator = 0;
				in_time = [];

				$.each(result.operator_loss_times, function(key, value){
					if(value.department == 'Woodwind Instrument - Final Assembly (WI-FA) Department'){
						wifaBody += '<tr>';
						wifaBody += '<td style="font-size: 1vw; padding:0; width:70%;">'+value.employee_id+'</td>';
						wifaBody += '<td style="font-size: 1vw; padding:0; background-color: yellow;">'+value.reason.toUpperCase()+'</td>';
						wifaBody += '</tr>';
						wifaBody += '<tr>';
						wifaBody += '<td style="font-size: 1vw; padding:0;">'+value.employee_name.replace(/(.{17})..+/, "$1&hellip;")+'</td>';
						wifaBody += '<td style="font-size: 1vw; padding:0;">';
						in_time.push(new Date(value.created_at));
						wifaBody += '<label id="hours'+ operator +'">'+ pad(parseInt(diff_seconds(new Date(), in_time[operator]) / 3600)) +'</label>:';
						wifaBody += '<label id="minutes'+ operator +'">'+ pad(parseInt((diff_seconds(new Date(), in_time[operator]) % 3600) / 60)) +'</label>:';
						wifaBody += '<label id="seconds'+ operator +'">'+ pad(diff_seconds(new Date(), in_time[operator]) % 60) +'</label>';
						wifaBody += '</td>';
						wifaBody += '</tr>';
						operator += 1;
					}
					if(value.department == 'Woodwind Instrument - Surface Treatment (WI-ST) Department'){
						wistBody += '<tr>';
						wistBody += '<td style="font-size: 1vw; padding:0; width:70%;">'+value.employee_id+'</td>';
						wistBody += '<td style="font-size: 1vw; padding:0; background-color: yellow;">'+value.reason.toUpperCase()+'</td>';
						wistBody += '</tr>';
						wistBody += '<tr>';
						wistBody += '<td style="font-size: 1vw; padding:0;">'+value.employee_name.replace(/(.{17})..+/, "$1&hellip;")+'</td>';
						wistBody += '<td style="font-size: 1vw; padding:0;">';
						in_time.push(new Date(value.created_at));
						wistBody += '<label id="hours'+ operator +'">'+ pad(parseInt(diff_seconds(new Date(), in_time[operator]) / 3600)) +'</label>:';
						wistBody += '<label id="minutes'+ operator +'">'+ pad(parseInt((diff_seconds(new Date(), in_time[operator]) % 3600) / 60)) +'</label>:';
						wistBody += '<label id="seconds'+ operator +'">'+ pad(diff_seconds(new Date(), in_time[operator]) % 60) +'</label>';
						wistBody += '</td>';
						wistBody += '</tr>';
						operator += 1;
					}
					if(value.department == 'Woodwind Instrument - Welding Process (WI-WP) Department'){
						wiwpBody += '<tr>';
						wiwpBody += '<td style="font-size: 1vw; padding:0; width:70%;">'+value.employee_id+'</td>';
						wiwpBody += '<td style="font-size: 1vw; padding:0; background-color: yellow;">'+value.reason.toUpperCase()+'</td>';
						wiwpBody += '</tr>';
						wiwpBody += '<tr>';
						wiwpBody += '<td style="font-size: 1vw; padding:0;">'+value.employee_name.replace(/(.{17})..+/, "$1&hellip;")+'</td>';
						wiwpBody += '<td style="font-size: 1vw; padding:0;">';
						in_time.push(new Date(value.created_at));
						wiwpBody += '<label id="hours'+ operator +'">'+ pad(parseInt(diff_seconds(new Date(), in_time[operator]) / 3600)) +'</label>:';
						wiwpBody += '<label id="minutes'+ operator +'">'+ pad(parseInt((diff_seconds(new Date(), in_time[operator]) % 3600) / 60)) +'</label>:';
						wiwpBody += '<label id="seconds'+ operator +'">'+ pad(diff_seconds(new Date(), in_time[operator]) % 60) +'</label>';
						wiwpBody += '</td>';
						wiwpBody += '</tr>';
						operator += 1;
					}
					if(value.department == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department'){
						wibppBody += '<tr>';
						wibppBody += '<td style="font-size: 1vw; padding:0; width:70%;">'+value.employee_id+'</td>';
						wibppBody += '<td style="font-size: 1vw; padding:0; background-color: yellow;">'+value.reason.toUpperCase()+'</td>';
						wibppBody += '</tr>';
						wibppBody += '<tr>';
						wibppBody += '<td style="font-size: 1vw; padding:0;">'+value.employee_name.replace(/(.{17})..+/, "$1&hellip;")+'</td>';
						wibppBody += '<td style="font-size: 1vw; padding:0;">';
						in_time.push(new Date(value.created_at));
						wibppBody += '<label id="hours'+ operator +'">'+ pad(parseInt(diff_seconds(new Date(), in_time[operator]) / 3600)) +'</label>:';
						wibppBody += '<label id="minutes'+ operator +'">'+ pad(parseInt((diff_seconds(new Date(), in_time[operator]) % 3600) / 60)) +'</label>:';
						wibppBody += '<label id="seconds'+ operator +'">'+ pad(diff_seconds(new Date(), in_time[operator]) % 60) +'</label>';
						wibppBody += '</td>';
						wibppBody += '</tr>';
						operator += 1;
					}
					if(value.department == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department'){
						wikppBody += '<tr>';
						wikppBody += '<td style="font-size: 1vw; padding:0; width:70%;">'+value.employee_id+'</td>';
						wikppBody += '<td style="font-size: 1vw; padding:0; background-color: yellow;">'+value.reason.toUpperCase()+'</td>';
						wikppBody += '</tr>';
						wikppBody += '<tr>';
						wikppBody += '<td style="font-size: 1vw; padding:0;">'+value.employee_name.replace(/(.{17})..+/, "$1&hellip;")+'</td>';
						wikppBody += '<td style="font-size: 1vw; padding:0;">';
						in_time.push(new Date(value.created_at));
						wikppBody += '<label id="hours'+ operator +'">'+ pad(parseInt(diff_seconds(new Date(), in_time[operator]) / 3600)) +'</label>:';
						wikppBody += '<label id="minutes'+ operator +'">'+ pad(parseInt((diff_seconds(new Date(), in_time[operator]) % 3600) / 60)) +'</label>:';
						wikppBody += '<label id="seconds'+ operator +'">'+ pad(diff_seconds(new Date(), in_time[operator]) % 60) +'</label>';
						wikppBody += '</td>';
						wikppBody += '</tr>';
						operator += 1;				
					}
					if(value.department == 'Educational Instrument (EI) Department'){
						eiBody += '<tr>';
						eiBody += '<td style="font-size: 1vw; padding:0; width:70%;">'+value.employee_id+'</td>';
						eiBody += '<td style="font-size: 1vw; padding:0; background-color: yellow;">'+value.reason.toUpperCase()+'</td>';
						eiBody += '</tr>';
						eiBody += '<tr>';
						eiBody += '<td style="font-size: 1vw; padding:0;">'+value.employee_name.replace(/(.{17})..+/, "$1&hellip;")+'</td>';
						eiBody += '<td style="font-size: 1vw; padding:0;">';
						in_time.push(new Date(value.created_at));
						eiBody += '<label id="hours'+ operator +'">'+ pad(parseInt(diff_seconds(new Date(), in_time[operator]) / 3600)) +'</label>:';
						eiBody += '<label id="minutes'+ operator +'">'+ pad(parseInt((diff_seconds(new Date(), in_time[operator]) % 3600) / 60)) +'</label>:';
						eiBody += '<label id="seconds'+ operator +'">'+ pad(diff_seconds(new Date(), in_time[operator]) % 60) +'</label>';
						eiBody += '</td>';
						eiBody += '</tr>';
						operator += 1;			
					}
				});

$('#wifaBody').append(wifaBody);
$('#wistBody').append(wistBody);
$('#wiwpBody').append(wiwpBody);
$('#wibppBody').append(wibppBody);
$('#wikppBody').append(wikppBody);
$('#eiBody').append(eiBody);
setInterval(focusTag, 1000);

}
else{
	alert('Unidentified ERROR!');
	audio_error.play();
	clearAll();
	setInterval(focusTag, 1000);
}
});
}

function confr(id){
		// $('#loading').show();
		var employee_id = $('#employee_id').val();
		var employee_name = $('#employee_name').val();
		var position = $('#position').val();
		var division = $('#division').val();
		var department = $('#department').val();
		var section = $('#section').val();
		var group = $('#group').val();
		var sub_group = $('#sub_group').val();
		var cost_center = $('#cost_center').val();

		var data = {
			employee_id:employee_id,
			employee_name:employee_name,
			position:position,
			division:division,
			department:department,
			section:section,
			group:group,
			sub_group:sub_group,
			cost_center:cost_center,
			reason:id
		}

		$.post('{{ url("input/efficiency/operator_loss_time") }}', data, function(result, status, xhr){
			if(result.status){
				audio_ok.play();
				openSuccessGritter('Success', result.message);				
				clearAll();
			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
				clearAll();
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

