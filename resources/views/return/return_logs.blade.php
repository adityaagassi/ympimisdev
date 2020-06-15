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
		padding-top: 0;
		padding-bottom: 0;
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
					<h3 class="box-title">Return Logs Filters</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<div class="form-group">
								<label>Slip Printed From</label>
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
								<label>Slip Printed To</label>
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
								<label>Issue Location</label>
								<select class="form-control select2" multiple="multiple" name="issue" id='issue' data-placeholder="Select Location" style="width: 100%;">
									<option value=""></option>
									@foreach($storage_locations as $storage_location)
									<option value="{{ $storage_location->storage_location }}">{{ $storage_location->storage_location }} - {{ $storage_location->location }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Receive Location</label>
								<select class="form-control select2" multiple="multiple" name="receive" id='receive' data-placeholder="Select Location" style="width: 100%;">
									<option value=""></option>
									@foreach($storage_locations as $storage_location)
									<option value="{{ $storage_location->storage_location }}">{{ $storage_location->storage_location }} - {{ $storage_location->location }}</option>
									@endforeach
								</select>
							</div>
						</div>	
					</div>
					<div class="row">
						<div class="col-md-4 col-md-offset-2">
							<div class="form-group">
								<label>Material</label>
								<select class="form-control select2" multiple="multiple" name="material" id='material' data-placeholder="Select Material" style="width: 100%; height: 100px;">
									<option value=""></option>
									@foreach($materials as $material)
									<option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->description }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Status</label>
								<select class="form-control select2" multiple="multiple" name="remark" id='remark' data-placeholder="Select Status" style="width: 100%; height: 100px;">
									<option value=""></option>
									<option value="received">Received</option>
									<option value="deleted">Deleted</option>
									<option value="rejected">Rejected</option>
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
							<table id="logTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">Printed at</th>
										<th style="width: 1%">Material</th>
										<th style="width: 5%">Material Desc.</th>
										<th style="width: 2%">Issue Location</th>
										<th style="width: 1%">Receive Location</th>
										<th style="width: 1%">Return By</th>
										<th style="width: 1%">Action By</th>
										<th style="width: 1%">Status</th>
										<th style="width: 1%">Qty</th>
										<th style="width: 1%">Received at</th>
										<th style="width: 1%">Rejected at</th>
										<th style="width: 1%">Deleted at</th>
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
		$('#logTable').DataTable().clear();
		$('#logTable').DataTable().destroy();

		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var issue = $('#issue').val();
		var receive = $('#receive').val();
		var material = $('#material').val();
		var remark = $('#remark').val();

		
		var data = {
			datefrom:datefrom,
			dateto:dateto,
			issue:issue,
			receive:receive,
			material:material,
			remark:remark
		}

		// $.get('{{ url("fetch/return_logs") }}', data, function(result, status, xhr){

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
				"url" : "{{ url("fetch/return_logs") }}",
				"data" : data
			},
			"columns": [
			{ "data": "slip_created" },
			{ "data": "material_number" },
			{ "data": "material_description" },
			{ "data": "issue_location" },
			{ "data": "receive_location" },
			{ "data": "returner" },
			{ "data": "creator" },
			{ "data": "remark" },
			{ "data": "quantity" },
			{ "data": "receive_at" },
			{ "data": "reject_at" },
			{ "data": "delete_at" }
			]
		});


		
	}

</script>

@endsection