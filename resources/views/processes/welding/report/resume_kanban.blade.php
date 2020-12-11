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
		overflow:hidden;
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
					<tr style="border: 1px solid black">
						<th width="10%">Material Number</th>
						<th width="20%">Material Description</th>
						<th width="10%">Model</th>
						<th width="10%">Key</th>
						<th width="10%">Surface</th>
						<th width="8%">Queue</th>
						<th width="8%">Welding</th>
						<th width="8%">Cuci Asam</th>
						<th width="8%">Kensa</th>
						<th width="8%">Store After</th>
						<th width="8%">Total</th>				
						<th width="8%">WIP > 3Hari</th>			
					</tr>
				</thead>
				<tbody id="tableResumeBody">
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
		$.get('{{ url("fetch/welding/resume_kanban") }}', function(result, status, xhr){
			if(result.status){
				$('#tableResume').DataTable().clear();
				$('#tableResume').DataTable().destroy();
				var bodyResume = "";
				$('#tableResumeBody').html("");

				for (var i = 0; i < result.datas.length; i++) {
					bodyResume += '<tr style="cursor:pointer;">';
					bodyResume += '<td>'+result.datas[i]['material_number']+'</td>';
					bodyResume += '<td>'+result.datas[i]['material_description']+'</td>';
					bodyResume += '<td>'+result.datas[i]['model']+'</td>';
					bodyResume += '<td>'+result.datas[i]['key']+'</td>';
					bodyResume += '<td>'+result.datas[i]['surface']+'</td>';
					bodyResume += '<td>'+result.datas[i]['qty_queue']+'</td>';
					bodyResume += '<td>'+result.datas[i]['qty_solder']+'</td>';
					bodyResume += '<td>'+result.datas[i]['qty_cuci']+'</td>';
					bodyResume += '<td>'+result.datas[i]['qty_kensa']+'</td>';
					bodyResume += '<td>'+result.datas[i]['qty_after']+'</td>';
					var total_edar = parseInt(result.datas[i]['qty_solder']) + parseInt(result.datas[i]['qty_after']) + parseInt(result.datas[i]['qty_kensa']) + parseInt(result.datas[i]['qty_cuci']) +  parseInt(result.datas[i]['qty_queue']);
					bodyResume += '<td>'+total_edar+'</td>';
					bodyResume += '<td>'+result.datas[i]['wip_tiga']+'</td>';
					bodyResume += '</tr>';
				}

				$('#tableResumeBody').append(bodyResume);

				$('#tableResume tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;color:black" type="text" placeholder="Search '+title+'" size="8"/>' );
				});
				
				var table = $('#tableResume').DataTable({
					'dom': 'Bfrtip',
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
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching'   	: true,
					'ordering'		: true,
					'order': [],
					'info'       	: true,
					'autoWidth'		: false,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
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
			else{
				openErrorGritter('Error!', 'Upload Failed.');
				audio_error.play();
			}
		});
	}


</script>
@endsection