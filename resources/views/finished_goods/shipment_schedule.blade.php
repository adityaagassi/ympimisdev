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
							<table id="shipmentScheduleTable" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Period</th>
										<th>Sales Order</th>
										<th>Destination</th>
										<th>Material</th>
										<th>Description</th>
										<th>Plan</th>
										<th>Actual</th>
										<th>Diff</th>
										<th>Ship. Date</th>
										<th>BL Date Plan</th>
										{{-- <th>BL Date Actual</th> --}}
										<th>Container ID</th>
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
										<th>Total</th>
										<th id="totalPlan"></th>
										<th id="totalActual"></th>
										<th id="totalDiff"></th>
										<th></th>
										<th></th>
										{{-- <th></th> --}}
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
		var data = {
			periodTo:periodTo,
			periodFrom:periodFrom,
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
						tableData += '<td>'+ value.sales_order +'</td>';
						tableData += '<td>'+ value.destination_shortname +'</td>';
						tableData += '<td>'+ value.material_number +'</td>';
						tableData += '<td>'+ value.material_description +'</td>';
						tableData += '<td>'+ value.quantity +'</td>';
						tableData += '<td>'+ value.actual +'</td>';
						tableData += '<td>'+ value.diff +'</td>';
						tableData += '<td>'+ value.st_date +'</td>';
						tableData += '<td>'+ value.bl_date_plan +'</td>';
						// tableData += '<td>'+ value.bl_date +'</td>';
						tableData += '<td>'+ value.container_id +'</td>';
						tableData += '</tr>';		
					});
					$('#tableBody').append(tableData);
					$('#shipmentScheduleTable').DataTable({
						'dom': 'Bfrtip',
						"scrollX": true,
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
						"footerCallback": function (tfoot, data, start, end, display) {
							var intVal = function ( i ) {
								return typeof i === 'string' ?
								i.replace(/[\$%,]/g, '')*1 :
								typeof i === 'number' ?
								i : 0;
							};
							var api = this.api();
							var totalPlan = api.column(5).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(5).footer()).html(totalPlan.toLocaleString());
							var api = this.api();
							var totalPlan = api.column(6).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(6).footer()).html(totalPlan.toLocaleString());
							var api = this.api();
							var totalPlan = api.column(7).data().reduce(function (a, b) {
								return intVal(a)+intVal(b);
							}, 0)
							$(api.column(7).footer()).html(totalPlan.toLocaleString());
						},
						"columnDefs": [ {
							"targets": 7,
							"createdCell": function (td, cellData, rowData, row, col) {
								if ( cellData <  0 ) {
									$(td).css('background-color', 'RGB(255,204,255)')
								}
								else
								{
									$(td).css('background-color', 'RGB(204,255,255)')
								}
							}
						} ],
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
						"processing": true
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