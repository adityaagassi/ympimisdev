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
		Location Stock <span class="text-purple">在庫の位置</span>
		<small>Material stock details <span class="text-purple">材料の在庫の詳細</span></small>
	</h1>
	<ol class="breadcrumb">
		{{-- <li>
			<button href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reprintModal">
				<i class="fa fa-print"></i>&nbsp;&nbsp;Reprint FLO
			</button>
		</li> --}}
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
					<div class="col-md-4 col-md-offset-2">
						<div class="form-group">
							<label>Plant</label>
							<select class="form-control select2" multiple="multiple" name="plant" id='plant' data-placeholder="Select Plant" style="width: 100%;">
								<option></option>
								@foreach($plants as $plant)
								<option value="{{ $plant }}">{{ $plant }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label>Origin Group</label>
							<select class="form-control select2" multiple="multiple" name="origin_group" id='origin_group' data-placeholder="Select Origin Group" style="width: 100%;">
								<option></option>
								@foreach($origin_groups as $origin_group)
								<option value="{{ $origin_group->origin_group_code }}">{{ $origin_group->origin_group_code }} - {{ $origin_group->origin_group_name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-4 col-md-offset-2">
						<div class="form-group">
							<label class="control-label">Material Number</label>
							<textarea id="materialArea" class="form-control" rows="3" placecholder="Paste barcode number from excel here"></textarea>
							<input id="materialTags" type="text" placeholder="Material Number" class="form-control tags" name="material_numbers" />
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Storage Location</label>
							<textarea id="locationArea" class="form-control" rows="3" placecholder="Paste location from excel here"></textarea>
							<input id="locationTags" type="text" placeholder="Material Number" class="form-control tags" name="locations" />
						</div>
					</div>			
					<div class="col-md-4 col-md-offset-6">
						<div class="form-group pull-right">
							<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
							<button id="search" onClick="fillInventoryTable()" class="btn btn-primary">Search</button>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<table id="inventoryTable" class="table table-bordered table-striped">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Plant</th>
										<th>Group</th>
										<th>Material</th>
										<th>Description</th>
										<th>SLoc</th>
										<th>Quantity</th>
										<th>Last Updated</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
									<tr>
										<th>Total</th>
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.tags').tagsInput({ width: 'auto' });
		$('.select2').select2();
		$('#materialTags').hide();
		$('#materialTags_tagsinput').hide();
		$('#locationTags').hide();
		$('#locationTags_tagsinput').hide();
		initKeyDown();
	});

	function clearConfirmation(){
		location.reload(true);
	}

	function initKeyDown() {
		$('#materialArea').keydown(function(event) {
			if (event.keyCode == 13) {
				convertMaterialToTags();
				return false;
			}
		});
		$('#locationArea').keydown(function(event) {
			if (event.keyCode == 13) {
				convertLocationToTags();
				return false;
			}
		});
	}

	function convertMaterialToTags() {
		var data = $('#materialArea').val();
		if (data.length > 0) {
			var rows = data.split('\n');
			if (rows.length > 0) {
				for (var i = 0; i < rows.length; i++) {
					var barcode = rows[i].trim();
					if (barcode.length > 0) {
						$('#materialTags').addTag(barcode);
					}
				}
				$('#materialTags').hide();
				$('#materialTags_tagsinput').show();
				$('#materialArea').hide();
			}
		}
	}

	function convertLocationToTags() {
		var data = $('#locationArea').val();
		if (data.length > 0) {
			var rows = data.split('\n');
			if (rows.length > 0) {
				for (var i = 0; i < rows.length; i++) {
					var barcode = rows[i].trim();
					if (barcode.length > 0) {
						$('#locationTags').addTag(barcode);
					}
				}
				$('#locationTags').hide();
				$('#locationTags_tagsinput').show();
				$('#locationArea').hide();
			}
		}
	}

	function fillInventoryTable(){
		$('#inventoryTable').DataTable().destroy();
		var plant = $('#plant').val();
		var origin_group = $('#origin_group').val();
		var material_number = $('#materialTags').val();
		var storage_location = $('#locationTags').val();
		var data = {
			plant:plant,
			origin_group:origin_group,
			material_number:material_number,
			storage_location:storage_location,
		}
		$('#inventoryTable').DataTable({
			'dom': 'Bfrtip',
			"scrollX": true,
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
			"footerCallback": function (tfoot, data, start, end, display) {
				var intVal = function ( i ) {
					return typeof i === 'string' ?
					i.replace(/[\$,]/g, '')*1 :
					typeof i === 'number' ?
					i : 0;
				};
				var api = this.api();
				var total_diff = api.column(5).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0)
				$(api.column(5).footer()).html(total_diff.toLocaleString());
			},
			"processing": true,
				// "serverSide": true,
				"ajax": {
					"type" : "post",
					"url" : "{{ url("fetch/inventory") }}",
					"data" : data,
				},
				"columns": [
				{ "data": "plant" },
				{ "data": "origin_group_name" },
				{ "data": "material_number" },
				{ "data": "material_description" },
				{ "data": "storage_location" },
				{ "data": "quantity" },
				{ "data": "updated_at" }
				]
			});
	}

</script>
@endsection

