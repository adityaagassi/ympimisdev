@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
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
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}

	.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}
	#loading, #error { display: none; }
</style>

@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<span class="text-purple"> ??</span>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">

	<input type="hidden" id="month" value="{{ $month }}">

	<div class="row">
		<div class="col-xs-12">
			<h2>PI VS Book</h2>
			<div class="col-xs-6" style="padding-left: 0px;">
				<h3 style="margin: 0px;">PI</h3>
				<table id="tablePi" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
					<thead style="background-color: rgb(126,86,134); color: #FFD700;">
						<tr>
							<th width="10%">Group</th>
							<th width="9%">Location</th>
							<th width="10%">Material</th>
							<th>Material Description</th>
							<th width="1%">PI</th>
						</tr>
					</thead>
					<tbody id="tablePiBody">
					</tbody>
				</table>		
			</div>
			<div class="col-xs-6" style="padding-right: 0px;">
				<h3 style="margin: 0px;">Book</h3>
				<table id="tableBook" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
					<thead style="background-color: rgb(126,86,134); color: #FFD700;">
						<tr>
							<th width="10%">Group</th>
							<th width="9%">Location</th>
							<th width="10%">Material</th>
							<th>Material Description</th>
							<th width="1%">Book</th>
						</tr>
					</thead>
					<tbody id="tableBookBody">
					</tbody>
				</table>	
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-8">
			<h2>Kitto VS PI</h2>
			<table id="tableKittoPi" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th width="10%">Group</th>
						<th width="9%">Location</th>
						<th width="10%">Material</th>
						<th>Material Description</th>
						<th width="10%">Kitto</th>
						<th width="10%">PI</th>
					</tr>
				</thead>
				<tbody id="tableKittoPiBody">
				</tbody>
			</table>		
		</div>
	</div>

	<div class="row">
		<div class="col-xs-8">
			<h2>Kitto VS Book</h2>
			<table id="tableKittoBook" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th width="10%">Group</th>
						<th width="9%">Location</th>
						<th width="10%">Material</th>
						<th>Material Description</th>
						<th width="10%">Kitto</th>
						<th width="10%">Book</th>
					</tr>
				</thead>
				<tbody id="tableKittoBookBody">
				</tbody>
			</table>		
		</div>
	</div>

	<div class="row">
		<div class="col-xs-8">
			<h2>PI VS Lot</h2>
			<table id="tablePiLot" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th width="10%">Group</th>
						<th width="9%">Location</th>
						<th width="10%">Material</th>
						<th>Material Description</th>
						<th width="10%">Lot</th>
						<th width="10%">PI</th>
					</tr>
				</thead>
				<tbody id="tablePiLotBody">
				</tbody>
			</table>		
		</div>
	</div>



</section>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		
		fillTable();

	});	
	
	function fillTable(){
		var month = $('#month').val();

		var data = {
			month : month
		}

		$('#tablePi').DataTable().destroy();
		var table = $('#tablePi').DataTable({
			'dom': 'Brtip',
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
			"columnDefs": [ {
				"searchable": false,
				"orderable": false,
				"targets": 0
			} ],
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': false,
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/stocktaking/pi_vs_book") }}",
				"data" : data
			},

			"columns": [
			{ "data": "group"},
			{ "data": "location"},
			{ "data": "material_number"},
			{ "data": "material_description" },
			{ "data": "pi"}]
		});

		$('#tableBook').DataTable().destroy();
		var table = $('#tableBook').DataTable({
			'dom': 'Brtip',
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
			"columnDefs": [ {
				"searchable": false,
				"orderable": false,
				"targets": 0
			} ],
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': false,
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/stocktaking/book_vs_pi") }}",
				"data" : data
			},

			"columns": [
			{ "data": "group"},
			{ "data": "location"},
			{ "data": "material_number"},
			{ "data": "material_description" },
			{ "data": "book"}]
		});

		$('#tableKittoPi').DataTable().destroy();
		var table = $('#tableKittoPi').DataTable({
			'dom': 'Brtip',
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
			"columnDefs": [ {
				"searchable": false,
				"orderable": false,
				"targets": 0
			} ],
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': false,
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/stocktaking/kitto_vs_pi") }}",
				"data" : data
			},

			"columns": [
			{ "data": "group"},
			{ "data": "location"},
			{ "data": "material_number"},
			{ "data": "material_description" },
			{ "data": "kitto"},
			{ "data": "pi"}]
		});

		$('#tableKittoBook').DataTable().destroy();
		var table = $('#tableKittoBook').DataTable({
			'dom': 'Brtip',
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
			"columnDefs": [ {
				"searchable": false,
				"orderable": false,
				"targets": 0
			} ],
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': false,
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/stocktaking/kitto_vs_book") }}",
				"data" : data
			},

			"columns": [
			{ "data": "group"},
			{ "data": "location"},
			{ "data": "material_number"},
			{ "data": "material_description" },
			{ "data": "kitto"},
			{ "data": "book"}]
		});



		$('#tablePiLot').DataTable().destroy();
		var table = $('#tablePiLot').DataTable({
			'dom': 'Brtip',
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
			"columnDefs": [ {
				"searchable": false,
				"orderable": false,
				"targets": 0
			} ],
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': false,
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/stocktaking/pi_vs_lot") }}",
				"data" : data
			},

			"columns": [
			{ "data": "group"},
			{ "data": "location"},
			{ "data": "material_number"},
			{ "data": "material_description" },
			{ "data": "kitto"},
			{ "data": "book"}]
		});


	}


</script>
@endsection