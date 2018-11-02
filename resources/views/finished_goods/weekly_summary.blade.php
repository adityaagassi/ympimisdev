@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
input {
	line-height: 24px;
}
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
					<div class="col-md-12 col-md-offset-2">
						<div class="col-md-2">
							<div class="form-group">
								<label>Week From</label>
								<select class="form-control select2" name="weekFrom" id='weekFrom' data-placeholder="Select Week" style="width: 100%;">
									<option></option>
									@foreach($weeks as $week)
									<option value="{{ $week->week_name }}">{{ $week->week_name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Week To</label>
								<select class="form-control select2" name="weekTo" id='weekTo' data-placeholder="Select Week" style="width: 100%;">
									<option></option>
									@foreach($weeks as $week)
									<option value="{{ $week->week_name }}">{{ $week->week_name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Year</label>
								<select class="form-control select2" name="year" id='year' data-placeholder="Select Year" style="width: 100%;">
									<option></option>
									@foreach($years as $year)
									<option value="{{ $year->year }}">{{ $year->year }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Fiscal Year</label>
								<select class="form-control select2" name="fiscalYear" id='fiscalYear' data-placeholder="Select Year" style="width: 100%;">
									<option></option>
									@foreach($fiscalYears as $fiscalYear)
									<option value="{{ $fiscalYear->fiscal_year }}">{{ $fiscalYear->fiscal_year }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-7">
							<div class="form-group pull-right">
								<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
								<button id="search" onClick="fillTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<table id="weeklySummaryTable" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>FY</th>
										<th>Year</th>
										<th>Week</th>
										<th>ETD (SUB)</th>
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
										<th></th>
										<th></th>
										<th></th>
										<th>Total</th>
										<th id="totalPlan"></th>
										<th id="totalActual"></th>
										<th id="totalDiff"></th>
										<th id="avgDiffPercentage"></th>
										<th id="totalActualShipment"></th>
										<th id="totalDiffShipment"></th>
										<th id="totalDiffShipmentPercentage"></th>
										<th id="totalDelay"></th>
										<th id="avgDelayPercentage"></th>
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
		var weekFrom = $('#weekFrom').val();
		var weekTo = $('#weekTo').val();
		var year = $('#year').val();
		var fiscalYear = $('#fiscalYear').val();
		var data = {
			weekFrom:weekFrom,
			weekTo:weekTo,
			year:year,
			fiscalYear:fiscalYear,
		}

		// $.get('{{ url("fetch/fg_weekly_summary") }}', data, function(result, status, xhr){
		// 	console.log(status);
		// 	console.log(result);
		// 	console.log(xhr);
		// });

		$('#weeklySummaryTable').DataTable({
			"scrollX": true,
			'dom': 'Bfrtip',
			'buttons': {
				dom: {
					button: {
						tag:'button',
						className:''
					}
				},
				buttons:[
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
			{ "data": "fiscal_year" },
			{ "data": "year" },
			{ "data": "week_name" },
			{ "data": "etd" },
			{ "data": "plan" },
			{ "data": "actual" },
			{ "data": "diff" },
			{ "data": "diff_percentage", },
			{ "data": "actual_shipment" },
			{ "data": "diff_shipment" },
			{ "data": "diff_shipment_percentage" },
			{ "data": "delay" },
			{ "data": "delay_percentage" },
			]
		});
}
</script>
@endsection