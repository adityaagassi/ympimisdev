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
		Monthly Summary <span class="text-purple">月次まとめ</span>
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
								<label>Period From</label>
								<select class="form-control select2" name="periodFrom" id='periodFrom' data-placeholder="Select Period" style="width: 100%;">
									<option></option>
									@foreach($periods as $period)
									<option value="{{ $period->st_month }}">{{ date('F Y', strtotime($period->st_month)) }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Period To</label>
								<select class="form-control select2" name="periodTo" id='periodTo' data-placeholder="Select Period" style="width: 100%;">
									<option></option>
									@foreach($periods as $period)
									<option value="{{ $period->st_month }}">{{ date('F Y', strtotime($period->st_month)) }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-md-offset-3">
						<div class="col-md-6">
							<div class="form-group pull-right">
								<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
								<button id="search" onClick="fillTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<table id="monthlySummaryTable" class="table table-bordered table-striped">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Period</th>
										<th>Total Sales Order Qty</th>
										<th>Total Back Order Qty</th>
										<th>Achievement %</th>
									</tr>
								</thead>
								<tbody id="tableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
									<tr>
										<th>Total/Average</th>
										<th id="totalSales"></th>
										<th id="totalBO"></th>
										<th id="avgPercentage"></th>
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

<div class="modal fade" id="modalBackOrder">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="modalBackOrderTitle"></h4>
				<div class="modal-body table-responsive table-bordered table-striped no-padding">
					<table class="table table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>SO</th>
								<th>ST Date</th>
								<th>BL Plan</th>
								<th>BL Actual</th>
								<th>Material</th>
								<th>Description</th>
								<th>Quantity</th>
							</tr>
						</thead>
						<tbody id="modalBackOrderBody">
						</tbody>
						<tfoot style="background-color: RGB(252, 248, 227);">
							<th>Total</th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th id="modalBackOrderTotal"></th>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

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

	function fillTable() {
		$('#monthlySummaryTable').DataTable().clear();
		$('#monthlySummaryTable').DataTable().destroy();
		var periodTo = $('#periodTo').val();
		var periodFrom = $('#periodFrom').val();
		var data = {
			periodTo:periodTo,
			periodFrom:periodFrom,
		}
		$.get('{{ url("fetch/fg_monthly_summary") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
					$('#monthlySummaryTable').DataTable().clear();
					$('#monthlySummaryTable').DataTable().destroy();
					$('#tableBody').html("");
					$('#totalSales').html('');
					$('#totalBO').html('');
					$('#avgPercentage').html('');
					var tableData = '';
					var totalSales = 0;
					var totalBO = 0;
					var totalPercentage = 0;
					var divider = 0;
					$.each(result.tableData, function(key, value) {
						totalSales += value.total;
						totalBO += value.bo;
						totalPercentage += value.percentage;
						divider += 1;
						tableData += '<tr>';
						tableData += '<td>'+ value.period +'</td>';
						tableData += '<td>'+ value.total.toLocaleString() +'</td>';
						if( value.bo > 0 ){
							tableData += '<td><a href="javascript:void(0)" id="'+ value.period +'" onClick="modalBackOrder(id)"> '+ value.bo.toLocaleString() +'</a></td>';
						}
						else{
							tableData += '<td>'+ value.bo.toLocaleString() +'</td>';
						}
						tableData += '<td>'+ value.percentage +'%</td>';
						tableData += '</tr>';
					});
					$('#tableBody').append(tableData);
					$('#totalSales').append(totalSales.toLocaleString());
					$('#totalBO').append(totalBO.toLocaleString());
					$('#avgPercentage').append((totalPercentage/divider).toFixed(2)+'%');
					$('#monthlySummaryTable').DataTable({
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
						"processing": true,
						"columnDefs": [ {
							"targets": 2,
							"createdCell": function (td, cellData, rowData, row, col) {
								if ( cellData >  0 ) {
									$(td).css('background-color', 'RGB(255,204,255)');
								}
								else
								{
									$(td).css('background-color', 'RGB(204,255,255)');
								}
							}
						},
						{
							"targets": 3,
							"createdCell": function (td, cellData, rowData, row, col) {
								var intVal = function ( i ) {
									return typeof i === 'string' ?
									i.replace(/[\$%,]/g, '')*1 :
									typeof i === 'number' ?
									i : 0;
								};
								if ( intVal(cellData) <  100 ) {
									$(td).css('background-color', 'RGB(255,204,255)');
								}
								else
								{
									$(td).css('background-color', 'RGB(204,255,255)');
								}
							}
						}
						]
					});
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}
		});
	}

	function modalBackOrder(period){
		var data = {
			period:period,
		}

		$.get('{{ url("fetch/tb_monthly_summary") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#modalBackOrderTitle').html('');
					$('#modalBackOrderTitle').html('Detail Backorder '+ period);
					$('#modalBackOrderBody').html('');
					var resultData = '';
					var resultTotal = 0;
					$.each(result.resultData, function(key, value) {
						resultData += '<tr>';
						resultData += '<td>'+ value.sales_order +'</td>';
						resultData += '<td>'+ value.st_date +'</td>';
						resultData += '<td>'+ value.bl_plan +'</td>';
						resultData += '<td>'+ value.bl_actual +'</td>';
						resultData += '<td>'+ value.material_number +'</td>';
						resultData += '<td>'+ value.material_description +'</td>';
						resultData += '<td>'+ value.actual.toLocaleString() +'</td>';
						resultData += '</tr>';
						resultTotal += value.actual;
					});
					$('#modalBackOrderBody').append(resultData);
					$('#modalBackOrderTotal').html('');
					$('#modalBackOrderTotal').append(resultTotal.toLocaleString());
					$('#modalBackOrder').modal('show');
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}
		});
	}
</script>
@endsection