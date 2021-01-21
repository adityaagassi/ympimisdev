@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">
	input {
		line-height: 22px;
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
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loadingscreen { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Meeting Lists <span class="text-purple">会議リスト</span>
		<small>Filters <span class="text-purple">フィルター</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<a href="{{ url('index/meeting/attendance?id=') }}" class="btn btn-primary btn-md" style="color:white"><i class="fa fa-users"></i>Meeting Attendance</a>
		</li>
		<li>
			<a data-toggle="modal" data-target="#modalCreate" class="btn btn-success btn-md" style="color:white"><i class="fa fa-plus"></i>Create Meeting</a>
		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loadingscreen" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-spinner"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-9">
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>Date From</label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control pull-right" id="dateFrom" name="dateFrom">
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Date To</label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control pull-right" id="dateTo" name="dateTo">
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label>Location</label>
						<select class="form-control select2" multiple="multiple" name="location" id="location" data-placeholder="Select Location" style="width: 100%;">
							<option></option>
							@foreach($locations as $location)
							<option value="{{ $location }}">{{ $location }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label>Status</label>
						<select class="form-control select2" name="status" id="status" data-placeholder="Select Status" style="width: 100%;">
							<option></option>
							<option value="all">All</option>
							<option value="open">Open</option>
							<option value="close">Close</option>
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="form-group pull-right">
						<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
						<button id="search" onClick="fetchTable()" class="btn btn-primary">Search</button>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<table id="table" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%">ID</th>
								<th style="width: 2%">Date</th>
								<th style="width: 7%">Title</th>
								<th style="width: 3%">Location</th>
								<th style="width: 3%">Organizer</th>
								<th style="width: 2%">Duration</th>
								<th style="width: 1%">Status</th>
								<th style="width: 4%">Action</th>
							</tr>
						</thead>
						<tbody id="tableBody">
						</tbody>
						<tfoot>
						</tfoot>
					</table>
					<center>
						<i class="fa fa-spinner fa-spin" id="loading2" style="font-size: 80px;"></i>
					</center>
				</div>
			</div>
		</div>
		<div class="col-xs-3">
			<div class="small-box" style="background: #ff851b; height: 150px; margin-bottom: 5px; margin-top: 10px;">
				<div class="inner" style="padding-bottom: 0px;">
					<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>Meeting Chart <span class="text-purple"></span></b></h3>
				</div>
				<div class="icon" style="padding-top: 40px;">
					<i class="fa fa-line-chart"></i>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
					</center>
					<form class="form-horizontal">
						<div class="col-xs-12">
							<input type="hidden" id="meetingId">
							<div class="form-group">
								<label for="editSubject" class="col-sm-2 control-label">Title<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<input type="text" style="width: 100%" class="form-control" id="editSubject" placeholder="Enter Title">
								</div>
							</div>
							<div class="form-group">
								<label for="editDescription" class="col-sm-2 control-label">Description</label>
								<div class="col-sm-10">
									<textarea type="text" style="width: 100%" class="form-control" id="editDescription" placeholder="Enter Description"></textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="editLocation" class="col-sm-2 control-label">Location<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<select class="form-control select2" name="editLocation" id="editLocation" data-placeholder="Select Location" style="width: 100%;">
										<option></option>
										@foreach($locations as $location)
										<option value="{{ $location }}">{{ $location }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="editStart" class="col-sm-2 control-label">Start Time<span class="text-red">*</span></label>
								<div class="col-sm-3">
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="editStart" placeholder="Select Date" >
									</div>
								</div>
								<div class="col-sm-3">								
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-clock-o"></i>
										</div>
										<input type="text" class="form-control timepicker" id="editStartTime" placeholder="select Time">
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="editEnd" class="col-sm-2 control-label">End Time<span class="text-red">*</span></label>
								<div class="col-sm-3">
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="editEnd" placeholder="Select Date" >
									</div>
								</div>
								<div class="col-sm-3">								
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-clock-o"></i>
										</div>
										<input type="text" class="form-control timepicker" id="editEndTime" placeholder="select Time">
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="editStatus" class="col-sm-2 control-label">Status<span class="text-red">*</span></label>
								<div class="col-sm-10">
									<select class="form-control select3" name="editStatus" id="editStatus" data-placeholder="Select Status" style="width: 100%;">
										<option value="open">Open</option>
										<option value="close">Close</option>
									</select>
								</div>
							</div>
						</div>
					</form>
					<div class="col-xs-12">
						<a class="btn btn-danger pull-left" onclick="deleteMeeting()">Delete Meeting</a>
						<a class="btn btn-primary pull-right" onclick="editMeeting()">Confirm</a>
					</div>
					<div class="col-xs-12" style="padding-top: 10px;">
						<table class="table table-hover table-bordered table-striped" id="tableDetail">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%;">#</th>
									<th style="width: 2%;">ID</th>
									<th style="width: 5%;">Name</th>
									<th style="width: 4%;">Dept</th>
									<th style="width: 4%;">Status</th>
									<th style="width: 1%;">Action</th>
								</tr>
							</thead>
							<tbody id="tableDetailBody">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalCreate">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<span style="font-weight: bold; font-size: 24px">Create Meeting</span>
				</center>
				<hr>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-xs-12" style="padding-bottom: 5px;">
							<div class="form-group">
								<label for="createSubject" class="col-sm-2 control-label">Title<span class="text-red">*</span></label>
								<div class="col-sm-8">
									<div class="input-group">
										<div class="input-group-btn">
											<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">Group<span class="fa fa-caret-down"></span></button>
											<ul class="dropdown-menu">
												@php
												$groups = array();
												@endphp
												@foreach($meeting_groups as $meeting_group)
												@if(!in_array($meeting_group->subject, $groups))
												<li><a href="javascript:void(0)" id="{{ $meeting_group->subject }}" onclick="addGroup(id)">{{ $meeting_group->subject }}</a></li>
												@php
												array_push($groups, $meeting_group->subject);
												@endphp
												@endif
												@endforeach
											</ul>
										</div>
										<input type="text" style="width: 100%" class="form-control" id="createSubject" placeholder="Enter Title">
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="createDescription" class="col-sm-2 control-label">Description</label>
								<div class="col-sm-10">
									<textarea type="text" style="width: 100%" class="form-control" id="createDescription" placeholder="Enter Description"></textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="createLocation" class="col-sm-2 control-label">Location<span class="text-red">*</span></label>
								<div class="col-sm-8">
									<select class="form-control select4" name="createLocation" id="createLocation" data-placeholder="Select Location" style="width: 100%;">
										<option></option>
										@foreach($locations as $location)
										<option value="{{ $location }}">{{ $location }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="createStart" class="col-sm-2 control-label">Start Time<span class="text-red">*</span></label>
								<div class="col-sm-3">
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="createStart" placeholder="Select Date" >
									</div>
								</div>
								<div class="col-sm-3">		
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-clock-o"></i>
										</div>
										<input type="text" class="form-control timepicker" id="createStartTime" placeholder="select Time">
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="createEnd" class="col-sm-2 control-label">End Time<span class="text-red">*</span></label>
								<div class="col-sm-3">
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control datepicker" id="createEnd" placeholder="Select Date" >
									</div>
								</div>
								<div class="col-sm-3">								
									<div class="input-group date">
										<div class="input-group-addon bg-purple" style="border: none;">
											<i class="fa fa-clock-o"></i>
										</div>
										<input type="text" class="form-control timepicker" id="createEndTime" placeholder="select Time">
									</div>
								</div>
							</div>
							<div class="col-xs-12">
								<center>
									<span style="font-weight: bold; font-size: 24px">Add Participants</span>
								</center>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Assignment</label>
									<select class="form-control select4" name="addAssignment" id="addAssignment" data-placeholder="Select Assignment" style="width: 100%;">
										<option></option>
										@php
										$assignment = array();
										@endphp
										@foreach($employees as $employee)
										@if(!in_array($employee->assignment, $assignment))
										<option value="{{ $employee->assignment }}">{{ $employee->assignment }}</option>
										@php
										array_push($assignment, $employee->assignment);
										@endphp
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Position</label>
									<select class="form-control select4" name="addPosition" id="addPosition" data-placeholder="Select Position" style="width: 100%;">
										<option></option>
										@php
										$position = array();
										@endphp
										@foreach($employees as $employee)
										@if(!in_array($employee->position, $position))
										<option value="{{ $employee->position }}">{{ $employee->position }}</option>
										@php
										array_push($position, $employee->position);
										@endphp
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Department</label>
									<select class="form-control select4" name="addDepartment" id="addDepartment" data-placeholder="Select Department" style="width: 100%;">
										<option></option>
										@php
										$department = array();
										@endphp
										@foreach($employees as $employee)
										@if(!in_array($employee->department, $department))
										<option value="{{ $employee->department }}">{{ $employee->department }}</option>
										@php
										array_push($department, $employee->department);
										@endphp
										@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Empolyee ID</label>
									<select class="form-control select4" name="addEmployee" id="addEmployee" data-placeholder="Select Employee" style="width: 100%;">
										<option></option>
										@foreach($employees as $employee)
										<option value="{{ $employee->employee_id }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-xs-12" style="padding-bottom: 5px;">
								<div class="row">
									<a class="btn btn-success pull-right" onclick="addParticipant('param')"><i class="fa fa-plus"></i> Add Participant</a>
									<a class="btn btn-danger pull-right" onclick="remParticipant('param')" style="margin-right: 5px;"><i class="fa fa-minus"></i> Remove Participant</a>
								</div>
							</div>
							<div class="col-xs-12">
								<div class="row">
									<table class="table table-hover table-bordered table-striped" id="tableParticipant">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th style="width: 1%;">ID</th>
												<th style="width: 5%;">Name</th>
												<th style="width: 1%;">Assignment</th>
												<th style="width: 1%;">Position</th>
												<th style="width: 1%;">Dept</th>
												<th style="width: 1%;">Action</th>
											</tr>
										</thead>
										<tbody id="tableParticipantBody">
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="col-xs-12">
						<a class="btn btn-primary pull-right" onclick="createMeeting()" style="font-size: 18px; font-weight: bold;"><i class="fa fa-check"></i> CREATE</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalChart">
	<div class="modal-dialog modal-lg" style="width: 1200px">
		<div class="modal-content">
			<div class="modal-header">
				<center><h4 style="padding-bottom: 15px;color: black;font-weight: bold;font-size: 25px" class="modal-title" id="modalChartTitle"></h4></center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loadingChart" style="font-size: 80px;"></i>
					</center>
					<div class="col-xs-8" style="margin-top: 5px;padding-right: 5px">
						<div id="container1" style="width: 100%;"></div>
						<!-- <div id="container2" style="width: 100%;"></div> -->
					</div>
					<div class="col-xs-4" style="margin-top: 5px;padding-left: 0px;padding-bottom: 10px">
						<div id="container2" style="width: 100%;"></div>
						<!-- <div id="container2" style="width: 100%;"></div> -->
					</div>
					<h4 class="modal-title" id="modalDetailTitleChart"></h4>
					<table class="table table-hover table-bordered table-striped" id="tableDetailChart">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr style="color: white">
								<th style="width: 1%;">#</th>
								<th style="width: 3%;">Employee ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 9%;">Dept</th>
								<th style="width: 3%;">Sect</th>
								<th style="width: 3%;">Status</th>
								<th style="width: 3%;">At</th>
							</tr>
						</thead>
						<tbody id="tableDetailChartBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('#loading2').hide();
		$('body').toggleClass("sidebar-collapse");
		$('#dateFrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#dateTo').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('.select2').select2();
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});
		$('.timepicker').timepicker({
			use24hours: true,
			showInputs: false,
			showMeridian: false,
			minuteStep: 30,
			defaultTime: '00:00',
			timeFormat: 'h:mm'
		});
		$('[data-toggle="tooltip"]').tooltip(); 
	});

	$(function () {
		$('.select3').select2({
			dropdownParent: $('#modalDetail')
		});
	})

	$(function () {
		$('.select4').select2({
			dropdownParent: $('#modalCreate')
		});
	})

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	var participants = [];

	function addGroup(id){
		var data = {
			id:id
		}
		$.get('{{ url("fetch/meeting/group") }}', data, function(result, status, xhr){
			if(result.status){
				$('#createSubject').val(result.groups[0].subject);
				$('#createDescription').val(result.groups[0].description);
				tableData = "";
				$.each(result.groups, function(key, value){
					tableData += "<tr>";
					tableData += "<tr id='rowParticipant"+value.employee_id+"'>";
					tableData += "<td>"+value.employee_id+"</td>";
					tableData += "<td>"+value.name+"</td>";
					tableData += "<td>"+value.assignment+"</td>";
					tableData += "<td>"+value.position+"</td>";
					tableData += "<td>"+value.department+"</td>";
					tableData += "<td>";
					tableData += "<a href='javascript:void(0)' onclick='remParticipant(id)' id='"+value.employee_id+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a>";
					tableData += "</td>";
					tableData += "</tr>";
					participants.push(value.employee_id);	
				});

				$('#tableParticipantBody').append(tableData);
				openSuccessGritter('Success!', result.message);
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function addParticipant(id){
		$('#loadingscreen').show();
		var assignment = $('#addAssignment').val();
		var position = $('#addPosition').val();
		var department = $('#addDepartment').val();
		var employee_id = $('#addEmployee').val();
		var data = {
			id:id,
			assignment:assignment,
			position:position,
			department:department,
			employee_id:employee_id
		}
		$.get('{{ url("fetch/meeting/add_participant") }}', data, function(result, status, xhr){
			if(result.status){
				$("#addAssignment").prop('selectedIndex', 0).change();
				$("#addPosition").prop('selectedIndex', 0).change();
				$("#addDepartment").prop('selectedIndex', 0).change();
				$("#addEmployee").prop('selectedIndex', 0).change();

				tableData = "";
				$.each(result.participants, function(key, value) {
					tableData += "<tr id='rowParticipant"+value.employee_id+"'>";
					tableData += "<td>"+value.employee_id+"</td>";
					tableData += "<td>"+value.name+"</td>";
					tableData += "<td>"+value.assignment+"</td>";
					tableData += "<td>"+value.position+"</td>";
					tableData += "<td>"+value.department+"</td>";
					tableData += "<td>";
					tableData += "<a href='javascript:void(0)' onclick='remParticipant(id)' id='"+value.employee_id+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a>";
					tableData += "</td>";
					tableData += "</tr>";
					participants.push(value.employee_id);
				});

				$('#tableParticipantBody').append(tableData);
				$('#loadingscreen').hide();

				openSuccessGritter('Success!', 'Participant added');
			}
			else{
				$('#loadingscreen').hide();
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function remParticipant(id){
		$('#loadingscreen').show();
		var assignment = $('#addAssignment').val();
		var position = $('#addPosition').val();
		var department = $('#addDepartment').val();
		var employee_id = $('#addEmployee').val();
		var data = {
			id:id,
			assignment:assignment,
			position:position,
			department:department,
			employee_id:employee_id
		}
		$.get('{{ url("fetch/meeting/add_participant") }}', data, function(result, status, xhr){
			if(result.status){
				$.each(result.participants, function(key, value) {
					$('#rowParticipant'+value.employee_id).remove();
					participants.splice( $.inArray(value.employee_id, participants), 1 );
				});
				$('#loadingscreen').hide();
				openSuccessGritter('Success!', 'Participant removed');
			}
			else{
				$('#loadingscreen').hide();
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function deleteMeeting(){
		$('#loadingscreen').show();
		var id = $('#meetingId').val();
		var data = {
			id:id,
			cat:'meeting'
		}
		if(confirm("Are you sure you want to delete this meeting?")){
			$.post('{{ url("delete/meeting") }}', data, function(result, status, xhr) {
				if(result.status){
					$('#loadingscreen').hide();
					$('#rowMeeting'+id).remove();
					$('#modalDetail').modal('hide');
					openSuccessGritter('Success!', result.message);
				}
				else{
					$('#loadingscreen').hide();
					audio_error.play();
					openErrorGritter('Error!', result.message);
				}
			});
		}
		else{
			$('#loadingscreen').hide();
			return false;
		}
	}

	function createMeeting(){
		$('#loadingscreen').show();
		var attendances = participants;
		var subject = $('#createSubject').val();
		var description = $('#createDescription').val();
		var location = $('#createLocation').val();
		var start_time = $('#createStart').val()+' '+$('#createStartTime').val();
		var end_time = $('#createEnd').val()+' '+$('#createEndTime').val();
		// alert(subject+' '+location+' '+start_time+' '+end_time);
		if(subject == "" || location == "" || start_time == "" || end_time == ""){
			openErrorGritter('Error!', 'All field must be filled')
			return false;
		}
		var data = {
			subject:subject,
			description:description,
			location:location,
			start_time:start_time,
			end_time:end_time,
			status:status,
			attendances:attendances
		}
		$.post('{{ url("create/meeting") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loadingscreen').hide();
				$('#modalCreate').modal('hide');
				$('#createSubject').val("");
				$('#createDescription').val("");
				$('#createStart').val("");
				$('#createEnd').val("");
				$('#tableParticipant').html("");
				participants = [];
				fetchTable();
				openSuccessGritter('Success!', result.message)
			}
			else{
				$('#loadingscreen').hide();
				audio_error.play();
				openErrorGritter('Error!', result.message)
			}
		});
	}

	function editMeeting(){
		$('#loadingscreen').show();
		var id = $('#meetingId').val();
		var subject = $('#editSubject').val();
		var description = $('#editDescription').val();
		var location = $('#editLocation').val();
		var start_time = $('#editStart').val()+' '+$('#editStartTime').val();
		var end_time = $('#editEnd').val()+' '+$('#editEndTime').val();
		var status = $('#editStatus').val();
		if(subject == "" || location == "" || start_time == "" || end_time == "" || status == ""){
			audio_error.play();
			openErrorGritter('Error!', 'All field must be filled')
			return false;
		}
		var data = {
			id:id,
			subject:subject,
			description:description,
			location:location,
			start_time:start_time,
			end_time:end_time,
			status:status
		}
		$.post('{{ url("edit/meeting") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#loadingscreen').hide();
				$('#modalDetail').modal('hide');
				fetchTable();
				openSuccessGritter('Success!', result.message);
			}
			else{
				$('#loadingscreen').hide();
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		})
	}
	
	function clearConfirmation(){
		location.reload(true);
	}

	function deleteAudience(id, cat){
		$('#loadingscreen').show();
		var data = {
			id:id,
			cat:'audience'
		}
		if(confirm("Are you sure you want to delete this audience?")){
			$.post('{{ url("delete/meeting") }}', data, function(result, status, xhr) {
				if(result.status){
					$('#loadingscreen').hide();
					openSuccessGritter('Success!', result.message);
					$('#rowAudience'+id).remove();
				}
				else{
					$('#loadingscreen').hide();
					audio_error.play();
					openErrorGritter('Error!', result.message);
				}
			});
		}
		else{
			$('#loadingscreen').hide();
			return false;
		}
	}

	function download_files(files) {
		function download_next(i) {
			if (i >= files.length) {
				return;
			}
			var a = document.createElement('a');
			a.href = files[i].download;
			a.target = '_parent';
			if ('download' in a) {
				a.download = files[i].filename;
			}
			(document.body || document.documentElement).appendChild(a);
			if (a.click) {
				a.click();
			} else {
				$(a).click();
			}
			a.parentNode.removeChild(a);
			setTimeout(function() {
				download_next(i + 1);
			}, 500);
		}
		download_next(0);
	}

	function downloadPDF(id){
		$('#loadingscreen').show();
		var data = {
			id:id,
			cat:'pdf'
		}
		$.get('{{ url("download/meeting") }}', data, function(result, status, xhr) {
			if(result.status){
				openSuccessGritter('Success!', result.message);
				download_files(result.paths);
				$('#loadingscreen').hide();
			}
			else{
				openErrorGritter('Error!', result.message);
				$('#loadingscreen').hide();
			}
		});
	}

	function downloadExcel(id){
		$('#loadingscreen').show();
		var data = {
			id:id,
			cat:'xls'
		}
		$.get('{{ url("download/meeting") }}', data, function(result, status, xhr) {
			if(result.status){
				openSuccessGritter('Success!', result.message);
				download_files(result.paths);
				$('#loadingscreen').hide();
			}
			else{
				openErrorGritter('Error!', result.message);
				$('#loadingscreen').hide();
			}
		});		
	}

	function listAttendance(id){
		var url = "{{ url('index/meeting/attendance?id=') }}";
		window.open(url+id);
	}

	function fetchTable(){
		$('#loading2').show();
		var dateFrom = $('#dateFrom').val();
		var dateTo = $('#dateTo').val();
		var location = $('#location').val();
		var status = $('#status').val();

		var data = {
			dateFrom:dateFrom,
			dateTo:dateTo,
			location:location,
			status:status
		}

		$.get('{{ url("fetch/meeting") }}', data, function(result, status, xhr) {
			if(result.status){
				var tableData = "";
				$('#tableBody').html("");

				if(result.meetings.length == 0){
					audio_error.play();
					openErrorGritter('Error!', 'No meeting found');
					$('#loading2').hide();
					return false;
				}

				$.each(result.meetings, function(key, value) {
					tableData += "<tr id='rowMeeting"+value.id+"'>";
					tableData += "<td>"+value.id+"</td>";
					tableData += "<td>"+value.date+"</td>";
					tableData += "<td>"+value.subject+"</td>";
					tableData += "<td>"+value.location+"</td>";
					tableData += "<td>"+value.name+"</td>";
					tableData += "<td>"+value.duration+"</td>";
					if(value.status == 'open'){
						tableData += "<td style='background-color: RGB(204,255,255);'>"+value.status+"</td>";
					}
					else{
						tableData += "<td style='background-color: RGB(255,204,255);'>"+value.status+"</td>";						
					}
					tableData += "<td>";
					tableData += "<button onclick='fetchDetail(id)' id='"+value.id+"' class='btn btn-warning btn-sm' style='margin-right:5px;'><i class='fa fa-pencil'></i></buton>";
					tableData += "<button onclick='downloadPDF(id)' id='"+value.id+"' class='btn btn-danger btn-sm' style='margin-right:5px;' data-toggle='tooltip' title='Download PDF'><i class='fa fa-file-pdf-o'></i></buton>";
					tableData += "<button onclick='downloadExcel(id)' id='"+value.id+"' class='btn btn-success btn-sm' style='margin-right:5px;' data-toggle='tooltip' title='Download Excel'><i class='fa fa-file-excel-o'></i></buton>";
					tableData += "<button onclick='listAttendance(id)' id='"+value.id+"' class='btn btn-info btn-sm' style='margin-right:5px;'><i class='fa fa-users'></i></buton>";
					tableData += "<button onclick='chartAttencance(id)' id='"+value.id+"' class='btn btn-primary btn-sm' style='margin-right:5px;'><i class='fa fa-bar-chart'></i></buton>";
					tableData += "</td>";
					tableData += "</tr>";
				});

				$('#loading2').hide();
				$('#tableBody').append(tableData);				
			}
			else{
				$('#loading2').hide();
				audio_error.play();
				openErrorGritter('Error!', 'Attempt to retrieve data failed');
			}	
		});
	}

	function listAudience(id){

	}

	function fetchDetail(id){
		var data = {
			id:id
		}

		$.get('{{ url("fetch/meeting/detail") }}', data, function(result, status, xhr) {
			$('#modalDetail').modal('show');
			$('#loading').show();
			if(result.status){
				$('#loading').hide();
				$('#editSubject').val(result.meeting.subject);
				$('#meetingId').val(result.meeting.id);
				$('#editDescription').val(result.meeting.description);
				
				var start_time = result.meeting.start_time.split(" ");
				var end_time = result.meeting.end_time.split(" ");

				$("#editStart").datepicker('setDate', start_time[0]);
				$('#editStartTime').val(start_time[1]);

				$("#editEnd").datepicker('setDate', end_time[0]);
				$('#editEndTime').val(end_time[1]);

				$('#editLocation').val(result.meeting.location).trigger('change.select2');
				$('#editStatus').val(result.meeting.status).trigger('change.select2');

				var tableData = "";
				var count = 1;
				$('#tableDetailBody').html("");

				$.each(result.meeting_details, function(key, value) {
					tableData += "<tr id='rowAudience"+value.id+"''>";
					tableData += "<td>"+count+"</td>";
					tableData += "<td>"+value.employee_id+"</td>";
					tableData += "<td>"+value.name+"</td>";
					tableData += "<td>"+value.department+"</td>";
					if(value.status == 0){
						tableData += "<td style='background-color: RGB(255,204,255);'>"+value.status+" - Belum Hadir</td>";
					}
					if(value.status == 1){
						tableData += "<td style='background-color: RGB(204,255,255);'>"+value.status+" - Hadir</td>";
					}
					if(value.status == 2){
						tableData += "<td style='background-color: RGB(204,255,255);'>"+value.status+" - Hadir</td>";
					}
					tableData += "<td><button class='btn btn-danger btn-sm' id='"+value.id+"' onclick='deleteAudience(id)'><i class='fa fa-trash'></i></button></td>";
					tableData += "</tr>";
					count += 1;
				});
				$('#tableDetailBody').append(tableData);
			}
			else{
				audio_error.play();
				$('#loading').hide();
				$('#modalDetail').modal('hide');
				openErrorGritter('Error!', 'Attempt to retrieve data failed');
			}

		});
	}

	function chartAttencance(id) {
		$('#modalChartTitle').html('');
		$('#container1').html('');
		$('#container2').html('');
		$('#loadingChart').show();
		$('#modalDetailTitleChart').html('');
		$('#tableDetailChart').hide();
		$('#tableDetailChart').DataTable().clear();
		$('#tableDetailChart').DataTable().destroy();
		$('#tableDetailChartBody').html('');
		var data = {
			id:id
		}

		$.get('{{ url("fetch/meeting/chart") }}', data,function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					var dept = [];
					var jml_hadir = [];
					var jml_tidak = [];
					var jml_tanpa_undangan = [];
					var hadir = 0;
					var tidak = 0;
					var tanpa_undangan = 0;
					var series = []
					var series2 = [];
					var series3 = [];

					for (var i = 0; i < result.chart.length; i++) {
						dept.push(result.chart[i].department_shortname);
						jml_hadir.push(parseInt(result.chart[i].hadir));
						hadir = hadir+parseInt(result.chart[i].hadir);
						tidak = tidak+parseInt(result.chart[i].tidak);
						tanpa_undangan = tanpa_undangan+parseInt(result.chart[i].tanpa_undangan);
						jml_tidak.push(parseInt(result.chart[i].tidak));
						jml_tanpa_undangan.push(parseInt(result.chart[i].tanpa_undangan));
						series.push([dept[i], jml_hadir[i]]);
						series2.push([dept[i], jml_tidak[i]]);
						series3.push([dept[i], jml_tanpa_undangan[i]]);
					}

					$('#modalChartTitle').html('Meeting Resume<br>'+result.meeting.subject+' || '+result.meeting.date+' ('+result.meeting.start+'-'+result.meeting.end+')');


					Highcharts.chart('container1', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'TOTAL AUDIENCE BY DEPT',
							style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: dept,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							lineWidth:2,
							lineColor:'#9e9e9e',
							labels: {
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: [{
							title: {
								text: 'Total Audience',
								style: {
			                        color: '#eee',
			                        fontSize: '15px',
			                        fontWeight: 'bold',
			                        fill: '#6d869f'
			                    }
							},
							labels:{
					        	style:{
									fontSize:"15px"
								}
					        },
							type: 'linear',
						},
					    ],
						tooltip: {
							headerFormat: '<span>{series.name}</span><br/>',
							pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
						},
						legend: {
							layout: 'horizontal',
							backgroundColor:
							Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
							itemStyle: {
				                fontSize:'12px',
				            },
						},	
						plotOptions: {
							series:{
								cursor: 'pointer',
				                point: {
				                  events: {
				                    click: function () {
				                      ShowModalDetailChart(this.category,this.series.name,id);
				                    }
				                  }
				                },
				                animation: false,
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '1vw'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer'
							},
						},credits: {
							enabled: false
						},
						series: [{
							type: 'column',
							data: series,
							name: 'Hadir',
							colorByPoint: false,
							color: "#78c718"
						},{
							type: 'column',
							data: series2,
							name: 'Tidak Hadir',
							colorByPoint: false,
							color:'#c71818'
						}
						,{
							type: 'column',
							data: series3,
							name: 'Tanpa Undangan',
							colorByPoint: false,
							color:'#fff954'
						},
						]
					});

					Highcharts.chart('container2', {
					    chart: {
					        plotBackgroundColor: null,
					        plotBorderWidth: null,
					        plotShadow: false,
					        type: 'pie'
					    },
					    title: {
					        text: 'Total Audience',
					        style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
					    },
					    tooltip: {
					        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
					    },
					    accessibility: {
					        point: {
					            valueSuffix: '%'
					        }
					    },
					    plotOptions: {
					        pie: {
					            allowPointSelect: true,
					            cursor: 'pointer',
					            dataLabels: {
					                enabled: true,
					                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
					            },
					            animation: false,
					        }
					    },credits: {
							enabled: false
						},
					    series: [{
					        name: 'Audience',
					        colorByPoint: true,
					        data: [{
					            name: 'Hadir',
					            y: hadir,
					            sliced: true,
					            selected: true,
					            colorByPoint: false,
								color: "#78c718"
					        }, {
					            name: 'Tidak Hadir',
					            y: tidak,
					            colorByPoint: false,
								color:'#c71818'
					        }, {
					            name: 'Tanpa Undangan',
					            y: tanpa_undangan,
					            colorByPoint: false,
								color:'#fff954'
					        }, ]
					    }]
					});
					$('#loadingChart').hide();
				}
			}
		});
		$('#modalChart').modal('show');
	}

	function ShowModalDetailChart(dept,attendance,id) {
		$('#tableDetailChart').hide();
		var data = {
			dept:dept,
			attendance:attendance,
			id:id,
		}

		$.get('{{ url("fetch/meeting/chart_detail") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#tableDetailChartBody').html('');

				$('#tableDetailChart').DataTable().clear();
				$('#tableDetailChart').DataTable().destroy();

				var index = 1;
				var resultData = "";
				var total = 0;

				$.each(result.details, function(key, value) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.employee_id +'</td>';
					resultData += '<td>'+ value.name +'</td>';
					resultData += '<td>'+ value.department +'</td>';
					resultData += '<td>'+ value.section +'</td>';
					resultData += '<td>'+ attendance +'</td>';
					resultData += '<td>'+ value.attend_time +'</td>';
					resultData += '</tr>';
					index += 1;
				});
				$('#tableDetailChartBody').append(resultData);
				$('#modalDetailTitleChart').html("<center><span style='font-size: 20px; font-weight: bold;'>Detail Employees With Attendance '"+attendance+"'</span></center>");

				$('#tableDetailChart').show();
				var table = $('#tableDetailChart').DataTable({
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
							}
							]
						},
						'paging': true,
						'lengthChange': true,
						'pageLength': 10,
						'searching': true	,
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
				alert('Attempt to retrieve data failed');
			}
		});
  	}

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#90ee7e', '#2b908f', '#eeaaee', '#ec407a', '#7798BF', '#f45b5b',
		'#ff9800', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: {
				linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
				stops: [
				[0, '#2a2a2b'],
				[1, '#2a2a2b']
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
					color: '#E0E0E3'
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