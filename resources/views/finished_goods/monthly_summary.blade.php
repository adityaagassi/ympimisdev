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
		Monthly Summary <span class="text-purple">月次まとめ</span>
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
							<table id="monthlySummaryTable" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Period</th>
										<th>Total Sales Order Qty</th>
										<th>Total Back Order Qty</th>
										<th>Achievement %</th>
									</tr>
								</thead>
								<tbody id="tableBody">
								</tbody>
								{{-- <tfoot style="background-color: RGB(252, 248, 227);">
									<tr>
										<th>Total</th>
										<th id="totalQty"></th>
										<th id="totalBO"></th>
										<th id="avgPercentage"></th>
									</tr>
								</tfoot> --}}
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
					var tableData = '';
					$.each(result.tableData, function(key, value) {
						tableData += '<tr>';
						tableData += '<td>'+ value.period +'</td>';
						tableData += '<td>'+ value.total +'</td>';
						if( value.bo > 0 ){
							tableData += '<td><a href="javascript:void(0)" id="'+ value.period +'" onClick="modalBackOrder(id)"> '+ value.bo +'</a></td>';
						}
						else
						{
							tableData += '<td>'+ value.bo +'</td>';
						}
						tableData += '<td>'+ value.percentage +'</td>';
						tableData += '</tr>';
					});
					$('#tableBody').append(tableData);
					$('#monthlySummaryTable').DataTable({
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
						"processing": true,
						// "footerCallback": function (tfoot, data, start, end, display) {
						// 	var intVal = function ( i ) {
						// 		return typeof i === 'string' ?
						// 		i.replace(/[\$%,]/g, '')*1 :
						// 		typeof i === 'number' ?
						// 		i : 0;
						// 	};
						// 	var api = this.api();
						// 	var total = api.column(1).data().reduce(function (a, b) {
						// 		return intVal(a)+intVal(b);
						// 	}, 0)
						// 	$(api.column(1).footer()).html(total.toLocaleString());
						// 	var bo = api.column(2).data().reduce(function (a, b) {
						// 		return intVal(a)+intVal(b);
						// 	}, 0)
						// 	$(api.column(2).footer()).html(bo.toLocaleString());
						// 	var percentage = api.column(3).data().reduce(function (a, b) {
						// 		return intVal(a)+intVal(b);
						// 	}, 0)
						// 	$(api.column(3).footer()).html((percentage/api.column(3).data().filter(function(value,index){return intVal(value)>0?true:false;}).count()).toFixed(2) + '%');
						// },
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
	});
}
</script>
@endsection