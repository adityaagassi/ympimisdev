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
					<h3 class="box-title">Filters <span class="text-purple"></span></h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-3">
							<div class="form-group">
								<label>Training From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="datefrom" name="datefrom">
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Training To</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="dateto" name="dateto">
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-6">
							<div class="form-group pull-right">
								<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
								<button id="search" onClick="fillTable()" class="btn btn-primary">Search</button>
							</div>
						</div>
					</div>			
					<div class="row">
						<div class="col-md-12">
							<table id="report" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th width="10%">Operator ID</th>
										<th width="15%">Operator Name</th>
										<th width="10%">Material Number</th>
										<th width="5%">Model</th>
										<th width="5%">Key</th>
										<th width="15%">Start Buffing</th>
										<th width="15%">Finish Buffing</th>
										<th width="5%">Efficiency</th>
										<th width="10%">Trainer</th>
									</tr>
								</thead>
								<tbody>
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

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('#datefrom').datepicker({
			autoclose: true
		});
		$('#dateto').datepicker({
			autoclose: true
		});
		$('.select2').select2({
		});
	});

	function clearConfirmation(){
		location.reload(true);		
	}

	function fillTable(){
		$('#report').DataTable().destroy();
		$('#report_qty').DataTable().destroy();

		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();

		var data = {
			datefrom:datefrom,
			dateto:dateto,
		}

		var table = $('#report').DataTable({
			'dom': 'Bfrtip',
			'searching'   : false,
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
				},
				]
			},
			'paging': true,
			'lengthChange': true,
			'searching': false,
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
				"url" : "{{ url("fetch/middle/report_buffing_traing_eff_operator") }}",
				"data" : data
			},
			"columns": [
			{ "data": "operator_id" },
			{ "data": "name" },
			{ "data": "material_number" },
			{ "data": "model" },
			{ "data": "key" },
			{ "data": "sedang_start_time" },
			{ "data": "selesai_start_time" },
			{ "data": "eff" },
			{ "data": "trainer" },
			]
		});

		$('#report tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="3"/>' );
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
		$('#report tfoot tr').appendTo('#report thead');



	}

</script>
@endsection