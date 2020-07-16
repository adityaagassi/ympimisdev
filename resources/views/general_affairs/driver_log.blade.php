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
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-md-2 col-md-offset-4">
							<div class="form-group">
								<label>From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="datefrom">
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>To</label>
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
						<div class="col-md-4 col-md-offset-4">
							<div class="form-group">
								<label>Driver</label>
								<select class="form-control select2" multiple="multiple" name="driver_id" id='driver_id' data-placeholder="Pilih Driver" style="width: 100%;">
									<option value=""></option>
									@foreach($driver_lists as $driver_list)
									<option value="{{ $driver_list->driver_id }}">{{ $driver_list->driver_id }} - {{ $driver_list->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4 col-md-offset-4">
							<div class="form-group">
								<label>Status</label>
								<select class="form-control select2" multiple="multiple" name="status" id='status' data-placeholder="Pilih Status" style="width: 100%;">
									<option value=""></option>
									<option value="Requested">Requested</option>
									<option value="Approved">Approved</option>
									<option value="Received">Received</option>
									<option value="Rejected">Rejected</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-md-offset-4">
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
										<th style="width: 1%">ID</th>
										<th style="width: 1%">Driver ID</th>
										<th style="width: 2%">Nama</th>
										<th style="width: 3%">Keperluan</th>
										<th style="width: 1%">Kota Destinasi</th>
										<th style="width: 2%">Dari</th>
										<th style="width: 2%">Sampai</th>
										<th style="width: 1%">Requested By</th>
										<th style="width: 1%">Approved By</th>
										<th style="width: 1%">Received By</th>
										<th style="width: 1%">Status</th>
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
			todayHighlight : true,
			autoclose: true,
			format: "yyyy-mm-dd"
		});
		$('#dateto').datepicker({
			todayHighlight : true,
			autoclose: true,
			format: "yyyy-mm-dd"
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
		var driver_id = $('#driver_id').val();
		var status = $('#status').val();
		
		var data = {
			datefrom:datefrom,
			dateto:dateto,
			driver_id:driver_id,
			status:status
		}	
		$.get('{{ url("fetch/ga_control/driver_log") }}', data, function(result, status, xhr) {
			if(result.status){
				var tableData = "";
				$('#resumeTable').DataTable().clear();
				$('#resumeTable').DataTable().destroy();
				$('#resumeTableBody').html('');

				$.each(result.logs, function(key, value){
					var ot = parseFloat(value.overtime);
					tableData += '<tr>';
					tableData += '<td>'+value.id+'</td>';
					tableData += '<td>'+value.driver_id+'</td>';
					tableData += '<td>'+value.name+'</td>';
					tableData += '<td>'+value.purpose+'</td>';
					tableData += '<td>'+value.destination_city+'</td>';
					tableData += '<td>'+value.date_from+'</td>';
					tableData += '<td>'+value.date_to+'</td>';
					tableData += '<td>'+value.request_employee_id+'<br>'+value.request_name+'</td>';
					tableData += '<td>'+value.approve_employee_id+'<br>'+value.approve_name+'</td>';
					tableData += '<td>'+value.receive_employee_id+'<br>'+value.receive_name+'</td>';
					tableData += '<td>'+value.status+'</td>';
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