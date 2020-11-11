@extends('layouts.master')
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
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
	
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Filters</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<div class="form-group">
								<label>Date From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="datefrom" data-placeholder="Select Date">
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
									<input type="text" class="form-control pull-right" id="dateto" data-placeholder="Select Date">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<div class="form-group">
								<label>Department</label>
								<select class="form-control select2" multiple="multiple" name="department" id='department' data-placeholder="Select Department" style="width: 100%;">
									<option value=""></option>
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
						<div class="col-md-4">
							<div class="form-group">
								<label>Section</label>
								<select class="form-control select2" multiple="multiple" name="section" id='section' data-placeholder="Select Section" style="width: 100%;">
									<option value=""></option>
									@php
									$section = array();
									@endphp
									@foreach($employees as $employee)
									@if(!in_array($employee->section, $section))
									<option value="{{ $employee->section }}">{{ $employee->section }}</option>
									@php
									array_push($section, $employee->section);
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
								<label>Group</label>
								<select class="form-control select2" multiple="multiple" name="group" id='group' data-placeholder="Select Group" style="width: 100%; height: 100px;">
									<option value=""></option>
									@php
									$group = array();
									@endphp
									@foreach($employees as $employee)
									@if(!in_array($employee->group, $group))
									<option value="{{ $employee->group }}">{{ $employee->group }}</option>
									@php
									array_push($group, $employee->group);
									@endphp
									@endif
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Status</label>
								<select class="form-control select2" multiple="multiple" name="status" id='status' data-placeholder="Select Status" style="width: 100%; height: 100px;">
									<option value=""></option>
									<option value="Simpan">Simpan</option>
									<option value="Buang">Buang</option>
									<option value="Pinjam">Pinjam</option>
								</select>
							</div>
						</div>				
					</div>
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<div class="form-group">
								<label>Requested By</label>
								<select class="form-control select2" multiple="multiple" name="requester" id='requester' data-placeholder="Select Employees" style="width: 100%; height: 100px;">
									<option value=""></option>
									@foreach($users as $user)
									<option value="{{ $user->id }}">{{ $user->username }} - {{ $user->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Created By</label>
								<select class="form-control select2" multiple="multiple" name="creator" id='creator' data-placeholder="Select Employees" style="width: 100%; height: 100px;">
									<option value=""></option>
									@foreach($users as $user)
									<option value="{{ $user->id }}">{{ $user->username }} - {{ $user->name }}</option>
									@endforeach
								</select>
							</div>
						</div>				
					</div>
					<div class="col-md-4 col-md-offset-6">
						<div class="form-group pull-right">
							<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
							<button id="search" onClick="fillTable()" class="btn btn-primary">Search</button>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12" style="overflow-x: auto;">
							<table id="logTable" class="table table-bordered table-striped table-hover" style="width: 100%;">
								<thead style="background-color: rgba(126,86,134,.7);" >
									<tr>
										<th style="width: 1%">Employee ID</th>
										<th style="width: 5%">Name</th>
										<th style="width: 1%">Gender</th>
										<th style="width: 10%">Department</th>
										<th style="width: 3%">Section</th>
										<th style="width: 3%">Group</th>
										<th style="width: 3%">Sub Group</th>
										<th style="width: 1%">Merk</th>
										<th style="width: 1%">Size</th>
										<th style="width: 1%">Status</th>
										<th style="width: 1%">Qty</th>
										<th style="width: 1%">Requested by</th>										
										<th style="width: 1%">Created by</th>
										<th style="width: 1%">Created at</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('#datefrom').datepicker({
			autoclose: true,
			todayHighlight: true,
			format: "yyyy-mm-dd"
		});
		$('#dateto').datepicker({
			autoclose: true,
			todayHighlight: true,
			format: "yyyy-mm-dd"
		});
		$('.select2').select2();
	});

	function clearConfirmation(){
		location.reload(true);		
	}

	function fillTable(){
		$('#logTable').DataTable().clear();
		$('#logTable').DataTable().destroy();

		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var department = $('#department').val();
		var section = $('#section').val();
		var group = $('#group').val();
		var status = $('#status').val();
		var requester = $('#requester').val();
		var creator = $('#creator').val();

		
		var data = {
			datefrom:datefrom,
			dateto:dateto,
			department:department,
			section:section,
			group:group,
			status:status,
			requested_by:requester,
			created_by:creator,
		}

		// $.get('{{ url("fetch/std_control/safety_shoes_log") }}', data, function(result, status, xhr){

		// });
		

		var table = $('#logTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'buttons': {
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default'
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
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/std_control/safety_shoes_log") }}",
				"data" : data
			},
			"columns": [
			{ "data": "employee_id" },
			{ "data": "name" },	
			{ "data": "gender" },
			{ "data": "department" },
			{ "data": "section" },
			{ "data": "group" },
			{ "data": "sub_group" },
			{ "data": "merk" },
			{ "data": "size" },
			{ "data": "status" },
			{ "data": "quantity" },
			{ "data": "requester" },
			{ "data": "creator" },
			{ "data": "created_at" }
			]
		});
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '4000'
		});
	}

</script>

@endsection