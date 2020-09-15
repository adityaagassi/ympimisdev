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
		<small>WIP Control</small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">

	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<table id="tableResume" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th style="border: 1px solid black" width="10%">Material Number</th>
						<th width="20%">Material Description</th>
						<th width="10%">Model</th>
						<th width="10%">Key</th>
						<th width="10%">Surface</th>
						<th width="8%">Queue</th>
						<th width="8%">WIP</th>
						<th width="8%">Stockroom</th>				
						<th width="8%">Total</th>				
						<th style="background-color: #ecf0f5; border-top-style: none; border-bottom-style: none;"></th>				
						<th width="8%">WIP > 3Hari</th>				
						<th width="8%">Inactive</th>				
					</tr>
				</thead>
				<tbody id="tableResumeBody">
				</tbody>
				<tfoot>
					<tr style="color: black">
						<th style="border: 1px solid black;" ></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th style="background-color: #ecf0f5; border-top-style: none; border-bottom-style: none; visibility: hidden;"></th>
						<th style="border: 1px solid black;"></th>
						<th></th>
					</tr>
				</tfoot>
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
		$('#tableResume').DataTable().destroy();

		$('#tableResume tfoot th').each(function(){
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
		});


		var table = $('#tableResume').DataTable({
			'dom': 'Brtip',
			'responsive': true,
			'lengthMenu': [
			[ 20, 50, 100, -1 ],
			[ '20 rows', '50 rows', '100 rows', 'Show all' ]
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
				]},
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
					"url" : "{{ url("fetch/resume_kanban") }}"
				},
				"columnDefs": [ {
					"targets": [9],
					"createdCell": function (td, cellData, rowData, row, col) {
						$(td).css('background-color', '#ecf0f5');
						$(td).css('color', '#ecf0f5');
						$(td).css('border-top-style', 'none');
						$(td).css('border-bottom-style', 'none');
					}
				},{
					"targets": [0],
					"createdCell": function (td, cellData, rowData, row, col) {
						$(td).css('border-left', '1px solid black');
					}
				},{
					"targets": [11],
					"createdCell": function (td, cellData, rowData, row, col) {
						$(td).css('border-right', '1px solid black');
					}
				}],

				"columns": [
				{ "data": "material_number"},
				{ "data": "material_description"},
				{ "data": "model"},
				{ "data": "key"},
				{ "data": "surface" },
				{ "data": "queue"},
				{ "data": "wip"},
				{ "data": "stockroom" },
				{ "data": "total_edar" },
				{ "data": "total_edar" },
				{ "data": "wip_tiga" },
				{ "data": "inactive" }
				]
			});

		table.columns().every( function () {
			var that = this;

			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			} );
		} );

		$('#tableResume tfoot tr').appendTo('#tableResume thead');

	}


</script>
@endsection