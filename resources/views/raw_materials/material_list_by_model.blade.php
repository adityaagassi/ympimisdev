@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
	input {
		line-height: 24px;
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
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Material List By Model <span class="text-purple">????</span>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('success'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('success') }}
	</div>   
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box no-border">
				<div class="box-header">
					<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Material Parent</label>
								<textarea id="materialParentArea" class="form-control" rows="3" placecholder="Paste location from excel here"></textarea>
								<input id="materialParentTags" type="text" placeholder="Material Number" class="form-control tags" name="materialParentTags" />
							</div>	
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Material Child</label>
								<textarea id="materialChildArea" class="form-control" rows="3" placecholder="Paste location from excel here"></textarea>
								<input id="materialChildTags" type="text" placeholder="Material Number" class="form-control tags" name="materialChildTags" />
							</div>	
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Vendor Code</label>
								<textarea id="vendorArea" class="form-control" rows="3" placecholder="Paste location from excel here"></textarea>
								<input id="vendorTags" type="text" placeholder="Vendor Code" class="form-control tags" name="vendorTags" />
							</div>	
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<div class="col-md-6" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-primary form-control" onclick="fetchTable()">Search</button>
								</div>
								<div class="col-md-6" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-danger form-control" onclick="clearSearch()">Clear</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="box no-border">
						<div class="box-header">
							<button class="btn btn-info pull-right" data-toggle="modal" data-target="#importModal">Import</button>
						</div>
						<div class="box-body" style="padding-top: 0;">
							<table id="transactionTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:5%;">Material Parent</th>
										<th style="width:20%;">Description</th>
										<th style="width:5%;">Material Child</th>
										<th style="width:20%;">Description</th>
										<th style="width:6%;">Uom</th>
										<th style="width:6%;">Purg</th>
										<th style="width:6%;">Usage</th>
										<th style="width:6%;">Vendor</th>
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

<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id ="importForm" method="post" action="{{ url('import/material/smbmr') }}" enctype="multipart/form-data">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Import Confirmation</h4>
					Format: -<br>
					Sample: <a href="{{ url('download/manual/import_storage_location_stock.txt') }}">import_material_list_by_model.txt</a> Code: #Truncate
				</div>
				<div class="modal-body">
					<input type="file" name="smbmr" id="smbmr" accept="text/plain">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button id="modalImportButton" type="submit" class="btn btn-success" onclick="loadingPage()">Import</button>
				</div>
			</form>
		</div>
	</div>
</div>

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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('#validMonth').datepicker({
			autoclose: true,
			todayHighlight: true,
			startView: "months", 
			minViewMode: "months",
			format:"mm-yyyy"
		});
		$('#date_stock').datepicker({
			autoclose: true,
			todayHighlight: true,
			startView: "months", 
			minViewMode: "months",
			format:"mm-yyyy"
		});
		$('.select2').select2();
		jQuery('.tags').tagsInput({ width: 'auto' });
		$('#materialParentTags').hide();
		$('#materialParentTags_tagsinput').hide();
		$('#materialChildTags').hide();
		$('#materialChildTags_tagsinput').hide();
		$('#vendorTags').hide();
		$('#vendorTags_tagsinput').hide();
		initKeyDown();
		// fetchTable();
	});

	function initKeyDown() {
		$('#materialParentArea').keydown(function(event) {
			if (event.keyCode == 13) {
				convertMaterialParentToTags();
				return false;
			}
		});
		$('#materialNChildArea').keydown(function(event) {
			if (event.keyCode == 13) {
				convertMaterialChildToTags();
				return false;
			}
		});
		$('#vendorArea').keydown(function(event) {
			if (event.keyCode == 13) {
				convertVendorToTags();
				return false;
			}
		});
	}

	function convertMaterialParentToTags() {
		var data = $('#materialParentArea').val();
		if (data.length > 0) {
			var rows = data.split('\n');
			if (rows.length > 0) {
				for (var i = 0; i < rows.length; i++) {
					var barcode = rows[i].trim();
					if (barcode.length > 0) {
						$('#materialParentTags').addTag(barcode);
					}
				}
				$('#materialParentTags').hide();
				$('#materialParentTags_tagsinput').show();
				$('#materialParentArea').hide();
			}
		}
	}

	function convertMaterialChildToTags() {
		var data = $('#materialChildArea').val();
		if (data.length > 0) {
			var rows = data.split('\n');
			if (rows.length > 0) {
				for (var i = 0; i < rows.length; i++) {
					var barcode = rows[i].trim();
					if (barcode.length > 0) {
						$('#materialChildTags').addTag(barcode);
					}
				}
				$('#materialChildTags').hide();
				$('#materialChildTags_tagsinput').show();
				$('#materialChildArea').hide();
			}
		}
	}

	function convertVendorToTags() {
		var data = $('#vendorArea').val();
		if (data.length > 0) {
			var rows = data.split('\n');
			if (rows.length > 0) {
				for (var i = 0; i < rows.length; i++) {
					var barcode = rows[i].trim();
					if (barcode.length > 0) {
						$('#vendorTags').addTag(barcode);
					}
				}
				$('#vendorTags').hide();
				$('#vendorTags_tagsinput').show();
				$('#vendorArea').hide();
			}
		}
	}

	function clearSearch(){
		location.reload(true);
	}

	function loadingPage(){
		$("#loading").show();
	}

	function fetchTable(){
		$('#transactionTable').DataTable().destroy();
		var material_parent = $('#materialParentArea').val();
		var material_child = $('#materialChildArea').val();
		var vendor = $('#vendorArea').val();
		var data = {
			material_parent:material_parent,
			material_child:material_child,
			vendor:vendor,
		}
		$('#transactionTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 25,
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
				}
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
				"url" : "{{ url("fetch/material/smbmr") }}",
				"data" : data
			},
			"columns": [
			{ "data": "material_parent"},
			{ "data": "material_parent_description"},
			{ "data": "material_child"},
			{ "data": "material_child_description"},
			{ "data": "uom"},
			{ "data": "purg"},
			{ "data": "usage"},
			{ "data": "vendor"},
			]
		});
	}
</script>
@endsection

