@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
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
		Shipment Schedule Data <span class="text-purple">出荷スケジュール</span>
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
			<div class="box">
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
					<div class="col-md-12">
						<div class="col-md-4">
							<div class="form-group">
								<select class="form-control select2" data-placeholder="Select Origin Group" name="origin_group" id="origin_group">
									<option></option>
									@foreach($origin_groups as $origin_group)
									<option value="{{ $origin_group->origin_group_code }}">{{ $origin_group->origin_group_code }} - {{ $origin_group->origin_group_name }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<select class="form-control select2" data-placeholder="Select Location" name="hpl" id="hpl" style="width: 100%;">
									<option></option>
									@foreach($hpls as $hpl)
									<option value="{{ $hpl->hpl }}">{{ $hpl->hpl }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<select class="form-control select2" data-placeholder="Select Category" name="category" id="category" style="width: 100%;">
									<option></option>
									@foreach($categories as $category)
									<option value="{{ $category }}">{{ $category }}<option>
									@endforeach
								</select>
							</div>
							<div class="form-group pull-right">
								<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
								<button id="search" onClick="fillTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<table id="shipmentScheduleTable" class="table table-bordered table-striped table-hover" style="width: 100%;">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%;">Period</th>
										<th style="width: 1%;">Cat.</th>
										<th style="width: 1%;">Sales Order</th>
										<th style="width: 1%;">Dest</th>
										<th style="width: 1%;">By</th>
										<th style="width: 5%;">Material</th>
										<th style="width: 30%;">Desc</th>
										<th style="width: 1%;">Plan</th>
										<th style="width: 1%;">Act Prod.</th>
										<th style="width: 1%;">Diff</th>
										<th style="width: 1%;">Act Deliv.</th>
										<th style="width: 1%;">Diff</th>
										<th style="width: 15%;">Ship. Date</th>
										<th style="width: 15%;">BL Date Plan</th>
										{{-- <th>BL Date Actual</th> --}}
										{{-- <th style="width: 5%">Container ID</th> --}}
									</tr>
								</thead>
								<tbody id="tableBody">
								</tbody>
								<tfoot style="background-color: RGB(252, 248, 227);">
									<tr>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th>Total</th>
										<th id="totalPlan"></th>
										<th id="totalActual"></th>
										<th id="totalDiff"></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										{{-- <th></th> --}}
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
		var periodTo = $('#periodTo').val();
		var periodFrom = $('#periodFrom').val();
		var originGroupCode = $('#origin_group').val();
		var hpl = $('#hpl').val();
		var category = $('#category').val();
		var data = {
			periodTo:periodTo,
			periodFrom:periodFrom,
			originGroupCode:originGroupCode,
			hpl:hpl,
			category:category,
		}
		$.get('{{ url("fetch/fg_shipment_schedule") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
					$('#shipmentScheduleTable').DataTable().clear();
					$('#shipmentScheduleTable').DataTable().destroy();
					$('#tableBody').html("");
					var tableData = '';
					$.each(result.tableData, function(key, value) {
						tableData += '<tr>';
						tableData += '<td>'+ value.st_month +'</td>';
						tableData += '<td>'+ value.category +'</td>';
						tableData += '<td>'+ value.sales_order +'</td>';
						tableData += '<td>'+ value.destination_shortname +'</td>';
						tableData += '<td>'+ value.shipment_condition_name +'</td>';
						tableData += '<td>'+ value.material_number +'</td>';
						tableData += '<td>'+ value.material_description +'</td>';
						tableData += '<td>'+ value.quantity +'</td>';
						tableData += '<td>'+ value.quantity_production +'</td>';
						tableData += '<td>'+ (value.quantity_production-value.quantity) +'</td>';
						tableData += '<td>'+ value.quantity_delivery +'</td>';
						tableData += '<td>'+ (value.quantity_delivery-value.quantity) +'</td>';
						tableData += '<td>'+ value.st_date +'</td>';
						tableData += '<td>'+ value.bl_date_plan +'</td>';
						// tableData += '<td>'+ value.bl_date +'</td>';
						// tableData += '<td>'+ value.container_id +'</td>';
						tableData += '</tr>';		
					});
					$('#tableBody').append(tableData);
					$('#shipmentScheduleTable').DataTable({
						'dom': 'Bfrtip',
						'responsive': true,
						'lengthMenu': [
						[ 10, 25, 50, -1 ],
						[ '10 rows', '25 rows', '50 rows', 'Show all' ]
						],
						"pageLength": 25,
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
							var totalPlan = api.column(7).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(7).footer()).html(totalPlan.toLocaleString());
							var totalPlan = api.column(8).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(8).footer()).html(totalPlan.toLocaleString());
							var totalPlan = api.column(9).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(9).footer()).html(totalPlan.toLocaleString());
							var totalPlan = api.column(10).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(10).footer()).html(totalPlan.toLocaleString());
							var totalPlan = api.column(11).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(11).footer()).html(totalPlan.toLocaleString());
						},
						"columnDefs": [ {
							"targets": [9, 11],
							"createdCell": function (td, cellData, rowData, row, col) {
								if ( cellData <  0 ) {
									$(td).css('background-color', 'RGB(255,204,255)')
								}
								else
								{
									$(td).css('background-color', 'RGB(204,255,255)')
								}
							}
						}]
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


</script>
@endsection