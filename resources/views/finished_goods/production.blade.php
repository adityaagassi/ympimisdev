@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	table {
		table-layout:fixed;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
		/*margin-top:20px;*/
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
		Production Result <span class="text-purple">生産実績</span>
		<small>By Shipment Schedule <span class="text-purple">出荷スケジュールによる</span></small>
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<input type="hidden" value="{{csrf_token()}}" name="_token" />
			<div class="col-md-6">
				<div class="progress-group" id="progress_div">
					<span class="progress-text" id="progress_text_production"></span>
					<span class="progress-number" id="progress_number_production" style="font-weight:bold;"></span>
					<div class="progress">
						<div class="progress-bar progress-bar-aqua progress-bar-striped active" id="progress_bar_production"></div>
					</div>
					<span class="progress-text" id="progress_text_delivery"></span>
					<span class="progress-number" id="progress_number_delivery" style="font-weight:bold;"></span>
					<div class="progress">
						<div class="progress-bar progress-bar-green progress-bar-striped active" id="progress_bar_delivery"></div>
					</div>
					<span class="progress-text" id="progress_text_shipment"></span>
					<span class="progress-number" id="progress_number_shipment" style="font-weight:bold;"></span>
					<div class="progress">
						<div class="progress-bar progress-bar-yellow progress-bar-striped active" id="progress_bar_shipment"></div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div id="info"></div>
			</div>
		</div>
		<div class="col-xs-12">
			<table id="productionTable" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th>Period</th>
						<th>Sales Order</th>
						<th>Material</th>
						<th>Description</th>
						<th>Dest</th>
						<th>Plan</th>
						<th>Actual</th>
						<th>Diff</th>
						<th>Ship. Date</th>
						<th>B/L Date</th>
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
						<th>Total:</th>
						<th id="total_plan"></th>
						<th id="total_actual"></th>
						<th id="total_diff"></th>
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
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('RGB(255,204,255)')
		}
	});

	jQuery(document).ready(function() {
		fillBar();
		setInterval(function(){
			fillBar();
		}, 10000);
	});

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
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ")";
	}

	function fillBar(){
		$.get('{{ url("fetch/fg_production") }}', function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
					$('#progress_text_production').html('Total Production vs Ship. Plan of '+result.st_month+'<br>生産数 対　出荷計画');
					$('#progress_number_production').html(result.total_production.toLocaleString() + '/' + result.total_plan.toLocaleString() + ' set(s) <a class="label label-info" onClick="fillTable(id)" id="production"><i class="fa fa-info-circle"></i> Info</a>');
					$('#progress_bar_production').html(((result.total_production/result.total_plan)*100).toFixed(2) + '%');
					$('#progress_bar_production').css('width', (result.total_production/result.total_plan)*100 + '%');
					$('#progress_bar_production').css('color', 'black');
					$('#progress_bar_production').css('font-weight', 'bold');

					$('#progress_text_delivery').html('Total Delivery FSTK vs Ship. Plan of '+result.st_month+'<br>倉庫送り数　対　出荷計画');
					$('#progress_number_delivery').html(result.total_delivery.toLocaleString() + '/' + result.total_plan.toLocaleString() + ' set(s) <a class="label label-success" onClick="fillTable(id)" id="delivery"><i class="fa fa-info-circle"></i> Info</a>');
					$('#progress_bar_delivery').html(((result.total_delivery/result.total_plan)*100).toFixed(2) + '%');
					$('#progress_bar_delivery').css('width', (result.total_delivery/result.total_plan)*100 + '%');
					$('#progress_bar_delivery').css('color', 'black');
					$('#progress_bar_delivery').css('font-weight', 'bold');

					$('#progress_text_shipment').html('Total Shipment vs Ship. Plan of '+result.st_month+'<br>ETD YMPI 出荷数　対　出荷計画');
					$('#progress_number_shipment').html(result.total_shipment.toLocaleString() + '/' + result.total_plan.toLocaleString() + ' set(s) <a class="label label-warning" onClick="fillTable(id)" id="shipment"><i class="fa fa-info-circle"></i> Info</a>');
					$('#progress_bar_shipment').html(((result.total_shipment/result.total_plan)*100).toFixed(2) + '%');
					$('#progress_bar_shipment').css('width', (result.total_shipment/result.total_plan)*100 + '%');
					$('#progress_bar_shipment').css('color', 'black');
					$('#progress_bar_shipment').css('font-weight', 'bold');
				}
				else{
					alert('Attempt to receive data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}
		});
	}

	function fillTable(id){
		$('#productionTable').DataTable().destroy();
		var data = {
			id:id,
		}
		$('#productionTable').DataTable({
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
			"processing": true,
			"serverSide": true,
			"footerCallback": function (tfoot, data, start, end, display) {
				var intVal = function ( i ) {
					return typeof i === 'string' ?
					i.replace(/[\$,]/g, '')*1 :
					typeof i === 'number' ?
					i : 0;
				};
				var api = this.api();
				var total_diff = api.column(7).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0)
				$(api.column(7).footer()).html(total_diff.toLocaleString());
				// $("#total_diff").val(total_diff.toLocaleString());

				var total_actual = api.column(6).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0)
				$(api.column(6).footer()).html(total_actual.toLocaleString());

				var total_plan = api.column(5).data().reduce(function (a, b) {
					return intVal(a)+intVal(b);
				}, 0)
				$(api.column(5).footer()).html(total_plan.toLocaleString());
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
			"processing": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/tb_production") }}",
				"data" : data,
			},
			"columns": [
			{ "data": "st_month" },
			{ "data": "sales_order" },
			{ "data": "material_number" },
			{ "data": "material_description" },
			{ "data": "destination_shortname" },
			{ "data": "quantity" },
			{ "data": "actual" },
			{ "data": "diff" },
			{ "data": "st_date" },
			{ "data": "bl_date" },
			]
		});

		$('#info').html('');
		if(id == 'production'){
			$('#info').html('<div class="callout callout-info"><h4>Production!</h4>Total hasil produksi <i>finished goods</i> yang telah diinput serial number ke dalam sistem.<br>製番記入済みの完成品数</div>');
		}
		if(id == 'delivery'){
			$('#info').html('<div class="callout callout-success"><h4>Delivery!</h4>Total hasil produksi <i>finished goods</i> yang telah dikirim ke gudang (FSTK).<br>倉庫にある完成品数</div>');
		}
		if(id == 'shipment'){
			$('#info').html('<div class="callout callout-warning"><h4>Shipment!</h4>Total hasil produksi <i>finished goods</i> yang telah melewati proses <i>stuffing</i> atau sudah diberangkatkan.<br>コンテナ積込・出荷済みの完成品数</div>');
		}
	}

</script>
@endsection

