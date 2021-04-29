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
					<h3 class="box-title">Scrap Logs Filters</h3>
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
									<option value=""></option>
									@foreach($storage_locations as $stor_loc)
									<option value="{{ $stor_loc }}">{{ $stor_loc }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Receive Location</label>
								<select class="form-control select2" multiple="multiple" name="receive" id='receive' data-placeholder="Select Location" style="width: 100%;">
									<option value=""></option>
									@foreach($reicives as $reicive)
									<option value="{{ $reicive }}">{{ $reicive }}</option>
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
									@php
									$material_number = array();
									@endphp
									@foreach($materials as $material)
									@if(!in_array($material->material_number, $material_number))
									<option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->description }}</option>
									@php
									array_push($material_number, $material->material_number);
									@endphp
									@endif
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Status</label>
								<select class="form-control select2" name="remark" id='remark' data-placeholder="Select Status" style="width: 100%; height: 100px;">
									<option value=""></option>
									<option value="pending">Pending</option>
									<option value="received">Received</option>
									<option value="deleted">Deleted</option>
									<!-- <option value="canceled">Canceled</option> -->
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
										<th style="width: 0.1%">No Slip</th>
										<th style="width: 0.1%">Material</th>
										<th style="width: 15%">Material Desc.</th>
										<th style="width: 0.1%">Issue Loc</th>
										<th style="width: 0.1%">Receive Loc</th>
										<th style="width: 1%">Qty</th>
										<th style="width: 0.1%">Status</th>
										<th style="width: 1%">Printed at</th>
										<th style="width: 1%">Printed by</th>
										<th style="width: 1%">Received at</th>
										<th style="width: 1%">Received by</th>
										<th style="width: 1%">Deleted at</th>
										<th style="width: 1%">Deleted by</th>
										<th style="width: 1%">Canceled at</th>
										<th style="width: 1%">Canceled by</th>
										<th style="width: 1%">Cancel</th>
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
				"url" : "{{ url("fetch/scrap/logs") }}",
				"data" : data
			},
			"columns": [
			{ "data": "slip" },
			{ "data": "material_number" },
			{ "data": "material_description" },
			{ "data": "issue_location" },
			{ "data": "receive_location" },
			{ "data": "quantity" },	
			{ "data": "remark" },
			{ "data": "printed_at" },
			{ "data": "printed_by" },
			{ "data": "received_at" },
			{ "data": "received_by" },
			{ "data": "deleted_at" },
			{ "data": "deleted_by" },
			{ "data": "canceled_at" },
			{ "data": "canceled_by" },
			{ "data": "cancel" }
			]
		});
	}

	function cancelScrap(id) {
		
		$("#loading").show();

		var data = {
			id : id
		}

		if(confirm("Scrap akan di batalkan. Apakah anda yakin melanjutkan proses ini ?\nData yang telah disimpan tidak dapat dikembalikan.")){
			$.post('{{ url("cancel/scrap") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success', result.message);
					$("#loading").hide();
					$('#logTable').DataTable().ajax.reload();
				}else{
					openErrorGritter('Error', result.message);
				}
			});
		}else{
			$("#loading").hide();
		}

	}

	function deleteScrap(id){
		$("#loading").show();

		var data = {
			id:id
		}

		if(confirm("Apa anda yakin anda akan mendelete slip scrap?")){
			$.post('{{ url("delete/scrap") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					$("#loading").hide();
					$('#logTable').DataTable().ajax.reload();
				}
				else{
					openErrorGritter('Error!', result.message);
				}
			});
		}
		else{
			$("#loading").hide();
		}
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