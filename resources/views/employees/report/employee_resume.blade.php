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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Employee Resume Data Filters</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<div class="form-group">
								<label>Month From</label>
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
								<label>Month To</label>
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
							<table id="resumeTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">Periode</th>
										<th style="width: 1%">ID</th>
										<th style="width: 5%">Name</th>
										<th style="width: 4%">Department</th>
										<th style="width: 1%">Mangkir</th>
										<th style="width: 1%">Izin</th>
										<th style="width: 1%">Sakit</th>
										<th style="width: 1%">Terlambat</th>
										<th style="width: 1%">Pulang Cepat</th>
										<th style="width: 1%">Cuti</th>
										<th style="width: 1%">Tunjangan Disiplin</th>
										<th style="width: 1%">Lembur (Jam)</th>
									</tr>
								</thead>
								<tbody id="resumeTableBody">
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
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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
			<?php $tgl_max = date('m-Y') ?>
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
			endDate: '<?php echo $tgl_max ?>'

		});
		$('#dateto').datepicker({
			<?php $tgl_max = date('m-Y') ?>
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
			endDate: '<?php echo $tgl_max ?>'
		});
		$('.select2').select2();
	});

	function clearConfirmation(){
		location.reload(true);		
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

	function fillTable(){
		$('#loading').show();
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
		$.get('{{ url("fetch/report/employee_resume") }}', data, function(result, status, xhr) {
			if(result.status){
				var tableData = "";
				$('#resumeTable').DataTable().clear();
				$('#resumeTable').DataTable().destroy();
				$('#resumeTableBody').html('');

				$.each(result.presences, function(key, value){
					var ot = parseFloat(value.overtime);
					tableData += '<tr>';
					tableData += '<td>'+value.periode+'</td>';
					tableData += '<td>'+value.Emp_no+'</td>';
					tableData += '<td>'+value.Full_name+'</td>';
					tableData += '<td>'+value.Department+'</td>';
					tableData += '<td>'+value.mangkir+'</td>';
					tableData += '<td>'+value.cuti+'</td>';
					tableData += '<td>'+value.izin+'</td>';
					tableData += '<td>'+value.sakit+'</td>';
					tableData += '<td>'+value.terlambat+'</td>';
					tableData += '<td>'+value.pulang_cepat+'</td>';
					tableData += '<td>'+value.tunjangan+'</td>';
					tableData += '<td>'+ot.toFixed(2)+'</td>';
					tableData += '</tr>';
				});

				$('#resumeTableBody').append(tableData);

				$('#resumeTable').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
							// dom: {
							// 	button: {
							// 		tag:'button',
							// 		className:''
							// 	}
							// },
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
						"processing": true
					});

				$('#loading').hide();
				openSuccessGritter('Success!', result.message);
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
			}
		});
	}

</script>

@endsection