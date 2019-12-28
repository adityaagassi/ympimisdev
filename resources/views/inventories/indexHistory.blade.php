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
	#loading, #error { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Upload History <span class="text-purple">????</span>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="col-md-4">
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
							<div class="col-md-4">
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
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<div class="col-md-4">
								<div class="form-group">
									<label>Movement Type</label>
									<select class="form-control select2" multiple="multiple" name="mvt" id="mvt" data-placeholder="Select Movement Type" style="width: 100%;">
										<option></option>
										@foreach($mvts as $mvt)
										<option value="{{ $mvt }}">{{ $mvt }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Origin Group</label>
									<select class="form-control select2" multiple="multiple" name="originGroup" id='originGroup' data-placeholder="Select Origin Group" style="width: 100%;">
										<option></option>
										@foreach($origin_groups as $origin_group)
										<option value="{{ $origin_group->origin_group_code }}">{{ $origin_group->origin_group_code }} - {{ $origin_group->origin_group_name }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12">
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label">Issue Storage Location</label>
									<textarea id="issueStorageLocationArea" class="form-control" rows="3" placecholder="Paste location from excel here"></textarea>
									<input id="issueStorageLocationTags" type="text" placeholder="Material Number" class="form-control tags" name="issueStorageLocationTags" />
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label">Receive Storage Location</label>
									<textarea id="receiveStorageLocationArea" class="form-control" rows="3" placecholder="Paste location from excel here"></textarea>
									<input id="receiveStorageLocationTags" type="text" placeholder="Material Number" class="form-control tags" name="receiveStorageLocationTags" />
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label">Material Number</label>
									<textarea id="materialNumberArea" class="form-control" rows="3" placecholder="Paste location from excel here"></textarea>
									<input id="materialNumberTags" type="text" placeholder="Material Number" class="form-control tags" name="materialNumberTags" />
								</div>	
							</div>
						</div>
					</div>		
					<div class="col-xs-12">
						<div class="form-group pull-right">
							<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
							<button id="search" onClick="fetchTable()" class="btn btn-primary">Search</button>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<table id="transactionTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:5%;">Material</th>
										<th style="width:35%;">Description</th>
										<th style="width:5%;">SLoc</th>
										<th style="width:5%;">ToLoc</th>
										<th style="width:5%;">MvT</th>
										<th style="width:8%;">Quantity</th>
										<th style="width:15%;">Upload</th>
										<th style="width:15%;">Created At</th>
										<th style="width:15%;">File</th>
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('#dateFrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#dateTo').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		jQuery('.tags').tagsInput({ width: 'auto' });
		$('.select2').select2();
		$('#issueStorageLocationTags').hide();
		$('#issueStorageLocationTags_tagsinput').hide();
		$('#issueStorageLocationTags').hide();
		$('#receiveStorageLocationTags_tagsinput').hide();
		$('#materialNumberTags').hide();
		$('#materialNumberTags_tagsinput').hide();
		initKeyDown();
	});

	function initKeyDown() {
		$('#issueStorageLocationArea').keydown(function(event) {
			if (event.keyCode == 13) {
				convertIssueStorageLocationToTags();
				return false;
			}
		});
		$('#receiveStorageLocationArea').keydown(function(event) {
			if (event.keyCode == 13) {
				convertReceiveStorageLocationToTags();
				return false;
			}
		});
		$('#materialNumberArea').keydown(function(event) {
			if (event.keyCode == 13) {
				convertMaterialNumberToTags();
				return false;
			}
		});
	}

	function convertIssueStorageLocationToTags() {
		var data = $('#issueStorageLocationArea').val();
		if (data.length > 0) {
			var rows = data.split('\n');
			if (rows.length > 0) {
				for (var i = 0; i < rows.length; i++) {
					var barcode = rows[i].trim();
					if (barcode.length > 0) {
						$('#issueStorageLocationTags').addTag(barcode);
					}
				}
				$('#issueStorageLocationTags').hide();
				$('#issueStorageLocationTags_tagsinput').show();
				$('#issueStorageLocationArea').hide();
			}
		}
	}

	function convertReceiveStorageLocationToTags() {
		var data = $('#receiveStorageLocationArea').val();
		if (data.length > 0) {
			var rows = data.split('\n');
			if (rows.length > 0) {
				for (var i = 0; i < rows.length; i++) {
					var barcode = rows[i].trim();
					if (barcode.length > 0) {
						$('#receiveStorageLocationTags').addTag(barcode);
					}
				}
				$('#receiveStorageLocationTags').hide();
				$('#receiveStorageLocationTags_tagsinput').show();
				$('#receiveStorageLocationArea').hide();
			}
		}
	}

	function convertMaterialNumberToTags() {
		var data = $('#materialNumberArea').val();
		if (data.length > 0) {
			var rows = data.split('\n');
			if (rows.length > 0) {
				for (var i = 0; i < rows.length; i++) {
					var barcode = rows[i].trim();
					if (barcode.length > 0) {
						$('#materialNumberTags').addTag(barcode);
					}
				}
				$('#materialNumberTags').hide();
				$('#materialNumberTags_tagsinput').show();
				$('#materialNumberArea').hide();
			}
		}
	}

	function clearConfirmation(){
		location.reload(true);
	}

	function fetchTable(){
		$('#transactionTable').DataTable().destroy();
		var dateFrom = $('#dateFrom').val();
		var dateTo = $('#dateTo').val();
		var originGroup = $('#originGroup').val();
		var mvt = $('#mvt').val();
		var materialNumber = $('#materialNumberTags').val();
		var receiveStorageLocation = $('#receiveStorageLocationTags').val();
		var issueStorageLocation = $('#issueStorageLocationTags').val();
		var data = {
			dateFrom:dateFrom,
			dateTo:dateTo,
			originGroup:originGroup,
			mvt:mvt,
			materialNumber:materialNumber,
			receiveStorageLocation:receiveStorageLocation,
			issueStorageLocation:issueStorageLocation,
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
				"url" : "{{ url("fetch/tr_history") }}",
				"data" : data
			},
			"columns": [
			{ "data": "material_number"},
			{ "data": "material_description"},
			{ "data": "issue_storage_location"},
			{ "data": "receive_storage_location"},
			{ "data": "mvt"},
			{ "data": "qty"},
			{ "data": "transaction_date"},
			{ "data": "created_at"},
			{ "data": "reference_file"}
			]
		});
	}

	function downloadFile(id){
		var data = {
			referenceFile : id
		}
		$.get('{{ url("download/tr_history") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				var file_path = result.file_path;
				var a = document.createElement('A');
				a.href = file_path;
				a.download = file_path.substr(file_path.lastIndexOf('/') + 1);
				document.body.appendChild(a);
				a.click();
				document.body.removeChild(a);
			}
			else{
				alert('Disconnected from server');
			}
		});
	}

</script>
@endsection

