@extends('layouts.master')
@section('stylesheets')
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
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
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Overtime Data Filters</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<div class="form-group">
								<label>Overtime. Date From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="datefrom">
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Overtime. Date To</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="dateto">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<div class="form-group">
								<label>Cost Center</label>
								<select class="form-control select2" multiple="multiple" name="cost_center_code" id='cost_center_code' data-placeholder="Select Cost Center" style="width: 100%;">
									<option value=""></option>
									@php
									$cost_center_code = array();
									@endphp
									@foreach($datas as $data)
									@if(!in_array($data->cost_center, $cost_center_code))
									<option value="{{ $data->cost_center }}">{{ $data->cost_center }} - {{ $data->cost_center_name }}</option>
									@php
									array_push($cost_center_code, $data->cost_center);
									@endphp
									@endif
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Department</label>
								<select class="form-control select2" multiple="multiple" name="department" id='department' data-placeholder="Select Department" style="width: 100%;">
									<option value=""></option>
									@php
									$department = array();
									@endphp
									@foreach($datas as $data)
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
								<select class="form-control select2" multiple="multiple" name="section" id='section' data-placeholder="Select Section" style="width: 100%;">
									<option value=""></option>
									@php
									$section = array();
									@endphp
									@foreach($datas as $data)
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
								<select class="form-control select2" multiple="multiple" name="group" id='group' data-placeholder="Select Group" style="width: 100%;">
									<option value=""></option>
									@php
									$group = array();
									@endphp
									@foreach($datas as $data)
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
						<div class="col-md-8 col-md-offset-2">
							<div class="form-group">
								<label>Employee ID</label>
								<select class="form-control select2" multiple="multiple" name="employee_id" id='employee_id' data-placeholder="Select Employee ID" style="width: 100%;">
									<option value=""></option>
									@php
									$employee_id = array();
									@endphp
									@foreach($datas as $data)
									@if(!in_array($data->employee_id, $employee_id))
									<option value="{{ $data->employee_id }}">{{ $data->employee_id }} - {{ strtoupper($data->name) }}</option>
									@php
									array_push($employee_id, $data->employee_id);
									@endphp
									@endif
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
						<div class="col-md-12">
							<table id="overtimeDataTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 8%">Date</th>
										<th style="width: 8%">ID Overtime</th>
										<th style="width: 7%">Employee ID</th>
										<th style="width: 10%">Name</th>
										<th style="width: 15%">Department</th>
										<th style="width: 15%">Section</th>
										<th style="width: 15%">Group</th>
										<th style="width: 8%">Cost Center</th>
										<th style="width: 3%">Overtime</th>
										<th style="width: 15%">Reason</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
									<tr>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
									</tr>
								</tfoot>
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
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
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
		$('#datefrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#dateto').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('.select2').select2();
	});

	function clearConfirmation(){
		location.reload(true);		
	}

	function fillTable(){
		$('#overtimeDataTable').DataTable().clear();
		$('#overtimeDataTable').DataTable().destroy();

		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var cost_center_code = $('#cost_center_code').val();
		var section = $('#section').val();
		var department = $('#department').val();
		var group = $('#group').val();
		var employee_id = $('#employee_id').val();
		
		var data = {
			datefrom:datefrom,
			dateto:dateto,
			cost_center_code:cost_center_code,
			section:section,
			department:department,
			group:group,
			employee_id:employee_id,
		}

		var table = $('#overtimeDataTable').DataTable({
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
					className: 'btn btn-default',
					// text: '<i class="fa fa-print"></i> Show',
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
				"url" : "{{ url("fetch/report/overtime_data") }}",
				"data" : data
			},
			"columns": [
			{ "data": "tanggal" },
			{ "data": "id_overtime" },
			{ "data": "nik" },
			{ "data": "name" },
			{ "data": "department" },
			{ "data": "section" },
			{ "data": "group" },
			{ "data": "cost_center" },
			{ "data": "ot" },
			{ "data": "keperluan" }
			],
		});

		$('#overtimeDataTable tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="3"/>' );
		});

		table.columns().every( function () {
			var that = this;
			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			});
		});
		$('#overtimeDataTable tfoot tr').appendTo('#overtimeDataTable thead');	

	}

</script>

@endsection