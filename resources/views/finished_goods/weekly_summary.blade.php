@extends('layouts.master')
@section('stylesheets')
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
		Weekly Summary <span class="text-purple">週次まとめ</span>
		{{-- <small>Material stock details <span class="text-purple">??????</span></small> --}}
	</h1>
	<ol class="breadcrumb" id="last_update"></ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-3">
							<div class="form-group">
								<label>ETD From</label>
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
								<label>ETD To</label>
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
								<button id="search" onClick="fillTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<table id="weeklySummaryTable" class="table table-bordered table-striped">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Year</th>
										<th>Week</th>
										<th>ETD (SUB) from</th>
										<th>ETD (SUB) to</th>
										<th>Plan</th>
										<th>Actual</th>
										<th>Diff</th>
										<th>%</th>
										<th>Actual Ship.</th>
										<th>Diff</th>
										<th>%</th>
										<th>Delay Qty</th>
										<th>%</th>
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
		$('.select2').select2();
		fillTable();
		$('#datefrom').datepicker({
			autoclose: true
		});
		$('#dateto').datepicker({
			autoclose: true
		});
	});

	function clearConfirmation(){
		location.reload(true);
	}

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function getActualFullDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	function fillTable(){
		$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
		$('#weeklySummaryTable').DataTable().clear();
		$('#weeklySummaryTable').DataTable().destroy();
		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var data = {
			datefrom:datefrom,
			dateto:dateto,
		}
		
		$('#weeklySummaryTable').DataTable({
			"scrollX": true,
			'dom': 'Bfrtip',
			'responsive':true,
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
					i.replace(/[\$%,]/g, '')*1 :
					typeof i === 'number' ?
					i : 0;
				};
				var api = this.api();
				var totalPlan = api.column(4).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0)
				$(api.column(4).footer()).html(totalPlan.toLocaleString());
				var totalActual = api.column(5).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0)
				$(api.column(5).footer()).html(totalActual.toLocaleString());
				var totalDiff = api.column(6).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0)
				$(api.column(6).footer()).html(totalDiff.toLocaleString());
				var avgDiffPercentage = api.column(7).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0)
				$(api.column(7).footer()).html((avgDiffPercentage/api.column(7).data().filter(function(value,index){return intVal(value)>0?true:false;}).count()).toFixed(2) + '%');
				var totalActualShipment = api.column(8).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0)
				$(api.column(8).footer()).html(totalActualShipment.toLocaleString());
				var totalDiffShipment = api.column(9).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0)
				$(api.column(9).footer()).html(totalDiffShipment.toLocaleString());
				var avgDiffPercentage = api.column(10).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0)
				$(api.column(10).footer()).html((avgDiffPercentage/api.column(10).data().filter(function(value,index){return intVal(value)>0?true:false;}).count()).toFixed(2) + '%');
				var totalDelay = api.column(11).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0)
				$(api.column(11).footer()).html(totalDelay.toLocaleString());
				var avgDiffPercentage = api.column(12).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0)
				$(api.column(12).footer()).html((avgDiffPercentage/api.column(12).data().filter(function(value,index){return intVal(value)>0?true:false;}).count()).toFixed(2) + '%');
			},
			"processing": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/fg_weekly_summary") }}",
				"data" : data,
			},
			"columnDefs": [ {
				"targets": [5, 6, 7],
				"createdCell": function (td, cellData, rowData, row, col) {
					$(td).css('background-color', 'RGB(204,255,255,0.50)')
				}
			},
			{
				"targets": [8, 9, 10],
				"createdCell": function (td, cellData, rowData, row, col) {
					$(td).css('background-color', 'RGB(255,255,204,0.50)')
				}
			},
			{
				"targets": [11, 12],
				"createdCell": function (td, cellData, rowData, row, col) {
					$(td).css('background-color', 'RGB(255,204,255,0.50)')
				}
			}],
			"columns": [
			{ "data": "year" },
			{ "data": "week_name" },
			{ "data": "week_start" },
			{ "data": "week_end" },
			{ "data": "plan" },
			{ "data": "actual_production" },
			{ "data": "diff_actual" },
			{ "data": "prctg_actual", },
			{ "data": "actual_shipment" },
			{ "data": "diff_shipment" },
			{ "data": "prctg_shipment" },
			{ "data": "delay" },
			{ "data": "prctg_delay" }
			]
		});
}
</script>
@endsection