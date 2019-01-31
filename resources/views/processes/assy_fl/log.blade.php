@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
thead>tr>th{
	text-align:center;
}
tbody>tr>td{
	text-align:center;
}
thead input {
	width: 100%;
	padding: 3px;
	box-sizing: border-box;
}
table.table-bordered{
	border:1px solid black;
	/*margin-top:20px;*/
}
table.table-bordered > thead > tr > th{
	border:1px solid black;
	font-size: 20px;
}
table.table-bordered > tbody > tr > td{
	border:1px solid rgb(211,211,211);
	padding: 0; 
	margin: 0;
	font-size: 18px;
	font-family: sans-serif;
}
table.table-bordered > tfoot > tr > th{
	border:1px solid black;
}
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Log Process Flute <span class="text-purple">ログプロセスフルート</span>
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
			<table id="logTable" cellspacing="0" cellpadding="0" style="width: 100%;" class="table table-bordered table-hover table-striped">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 10%;">Serial Number</th>
						<th style="width: 10%;">Model</th>
						<th style="width: 20%;">Stamp-Kariawase</th>
						<th style="width: 20%;">Tanpoawase</th>
						<th style="width: 20%;">Yuge</th>
						<th style="width: 20%;">Chousei</th>
						<th style="width: 20%;">Status</th>
					</tr>
				</thead>
				<tbody id="logTableBody">
				</tbody>
				<tfoot>
					<tr>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fetchTableLog();
	});

	function fetchTableLog(){
		$('#logTable tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
		});
		// $.get('{{ url("fetch/logTableFl") }}', function(result, status, xhr){
		// 	console.log(status);
		// 	console.log(result);
		// 	console.log(xhr);
		// 	if(xhr.status == 200){
		// 		if(result.status){
		// 			$('#logTableBody').html("");
		// 			var tableBody = '';
		// 			$.each(result.logs, function(key, value) {
		// 				tableBody += '<tr>';
		// 				tableBody += '<td>'+value.serial_number+'</td>';
		// 				tableBody += '<td>'+value.model+'</td>';
		// 				tableBody += '<td>'+value.kariawase+'</td>';
		// 				tableBody += '<td>'+value.tanpoawase+'</td>';
		// 				tableBody += '<td>'+value.yuge+'</td>';
		// 				tableBody += '<td>'+value.chousei+'</td>';
		// 				tableBody += '<td>'+value.status+'</td>';
		// 				tableBody += '</tr>';
		// 			});
		// 			$('#logTableBody').append(tableBody);
		// 		}
		// 		else{
		// 			alert('Attempt to retrieve data failed');
		// 		}
		// 	}
		// 	else{
		// 		alert('Disconnected from server');
		// 	}
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
			"columnDefs": [{
				"targets": 2,
				"createdCell": function (td, cellData, rowData, row, col) {
					if ( cellData ==  null ) {
						$(td).css('background-color', null)
					}
					else
					{
						$(td).css('background-color', 'RGBA(144, 238, 144, 0.7)')
					}
				}
			},
			{
				"targets": 3,
				"createdCell": function (td, cellData, rowData, row, col) {
					if ( cellData ==  null ) {
						$(td).css('background-color', null)
					}
					else
					{
						$(td).css('background-color', 'RGBA(255, 215, 0, 0.7)')
					}
				}
			},
			{
				"targets": 4,
				"createdCell": function (td, cellData, rowData, row, col) {
					if ( cellData ==  null ) {
						$(td).css('background-color', null)
					}
					else
					{
						$(td).css('background-color', 'RGBA(135, 206, 235, 0.7)')
					}
				}
			},
			{
				"targets": 5,
				"createdCell": function (td, cellData, rowData, row, col) {
					if ( cellData ==  null ) {
						$(td).css('background-color', null)
					}
					else
					{
						$(td).css('background-color', 'RGBA(255, 69, 0, 0.7)')
					}
				}
			}],
			'paging'        : true,
			'lengthChange'  : true,
			'searching'     : true,
			'ordering'      : true,
			'info'        : true,
			'order'       : [],
			'autoWidth'   : true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/logTableFl") }}",
			},
			"columns": [
			{ "data": "serial_number" },
			{ "data": "model" },
			{ "data": "kariawase" },
			{ "data": "tanpoawase" },
			{ "data": "yuge" },
			{ "data": "chousei" },
			{ "data": "status" },
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
			});
		});

		$('#logTable tfoot tr').appendTo('#logTable thead');
	}
</script>
@endsection