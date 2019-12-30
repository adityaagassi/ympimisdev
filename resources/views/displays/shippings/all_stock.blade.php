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
		margin:0;
		padding:0;
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
		Finished Goods Stock <span class="text-purple">完成品在庫</span>
		<small>By Each Location <span class="text-purple">ロケーション毎</span></small>
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box">
				<div class="box-header with-border" id="boxTitle">
				</div>
				<div class="box-body">
					<div id="container" style="width:100%; height:450px;"></div>
				</div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="box">
				<div class="box-body">
					<table id="tableStock" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width : 5%">Material Number</th>
								<th style="width : 40%">Description</th>
								<th style="width : 10%">Destination</th>
								<th style="width : 10%">Location</th>
								<th style="width : 20%">Quantity</th>
							</tr>
						</thead>
						<tbody id="tableStockBody">
						</tbody>
						<tfoot >
							<tr>
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
</section>

<div class="modal fade" id="modalStock">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title"></h4>
				<div class="modal-body table-responsive no-padding">
					<table class="table table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<th>Material</th>
							<th>Description</th>
							<th>Quantity</th>
							<th>m&sup3;</th>
						</thead>
						<tbody id="tableBody">
						</tbody>
						<tfoot style="background-color: RGB(252, 248, 227);">
							<th>Total</th>
							<th></th>
							<th id="totalQty"></th>
							<th id="totalM3"></th>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
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
		fillChart();
		// setInterval(function(){
		// 	fillChart();
		// }, 10000);
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
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	function fillChart(){
		$.get('{{ url("fetch/display/all_stock") }}', function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					var total_volume = 0;
					var total_stock = 0;

					$.each(result.jsonData, function(key, value) {
						total_volume += value.volume;
						total_stock += value.actual;
					});

					$('#tableStock').DataTable().clear();
					$('#tableStock').DataTable().destroy();
					$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
					$('#boxTitle').html('<i class="fa fa-info-circle"></i><h4 class="box-title">Total Stock: <b>'+ total_stock + ' pc(s)</b> &#8786; <b>'+ total_volume.toFixed(2) +' m&sup3;</b> (<b>' + (total_volume/52).toFixed(2) + ' container(s)</b>)</h4>');
					$('#boxTitle').append('<div class="pull-right"><b>1 Container &#8786; 52 m&sup3</b></div>');
					var data = result.jsonData;

					$('#tableStockBody').html("");
					
					var tableStockData = '';

					$.each(result.stockData, function(key, value) {
						tableStockData += '<tr>';
						tableStockData += '<td style="width: 10%;">'+ value.material_number +'</td>';
						tableStockData += '<td>'+ value.material_description +'</td>';
						tableStockData += '<td style="width: 10%;">'+ value.destination +'</td>';
						tableStockData += '<td style="width: 10%;">'+ value.location +'</td>';
						tableStockData += '<td style="width: 10%;">'+ value.quantity +'</td>';
						tableStockData += '</tr>';
					});

					$('#tableStockBody').append(tableStockData);
					$('#tableStock tfoot th').each( function () {
						var title = $(this).text();
						$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
					});

					var table = $('#tableStock').DataTable({
						'dom': 'Bfrtip',
						'responsive': true,
						"pageLength": 25,
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
						}
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
					// $('#tableStock thead').html("");
					$('#tableStock tfoot tr').appendTo('#tableStock thead');
					// data = data.reverse()
					// var seriesData = [];
					// var xCategories = [];
					// var i, cat;
					// for(i = 0; i < data.length; i++){
					// 	cat = data[i].destination;
					// 	if(xCategories.indexOf(cat) === -1){
					// 		xCategories[xCategories.length] = cat;
					// 	}
					// }
					// for(i = 0; i < data.length; i++){
					// 	if(seriesData){
					// 		var currSeries = seriesData.filter(function(seriesObject){ return seriesObject.name == data[i].location;});
					// 		if(currSeries.length === 0){
					// 			seriesData[seriesData.length] = currSeries = {name: data[i].location, data: []};
					// 		} else {
					// 			currSeries = currSeries[0];
					// 		}
					// 		var index = currSeries.data.length;
					// 		currSeries.data[index] = data[i].actual;
					// 	} else {
					// 		seriesData[0] = {name: data[i].location, data: [data[i].actual]}
					// 	}
					// }

					var xAxis = []
					, productionCount = []
					, inTransitCount = []
					, fstkCount = []

					for (i = 0; i < data.length; i++) {
						xAxis.push(data[i].destination);
						productionCount.push(data[i].production);
						inTransitCount.push(data[i].intransit);
						fstkCount.push(data[i].fstk);
					}

					var chart;
					// $(document).ready(function() {
						chart = new Highcharts.Chart({
							colors: ['rgba(119, 152, 191, 0.80)', 'rgba(144, 238, 126, 0.80)', 'rgba(247, 163, 92, 0.80)'],
							chart: {
								renderTo: 'container',
								type: 'column',
							},
							title: {
								text: 'Finished Goods Stock By Location Chart'
							},
							xAxis: {
								categories: xAxis,
								gridLineWidth: 1,
								scrollbar: {
									enabled: true
								}
							},
							yAxis: {
								min: 1,
								title: {
									text: 'Total Finished Goods'
								},
								stackLabels: {
									enabled: true,
									style: {
										fontWeight: 'bold',
										color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
									}
								},
								type: 'logarithmic'
							},
							credits: {
								enabled: false
							},
							plotOptions: {
								series: {
									borderColor: '#303030',
									cursor: 'pointer',
									stacking: 'normal',
									point: {
										events: {
											click: function () {
												// alert('Destinasi: ' + this.category + ', Location: ' + this.series.name +', qty: ' + this.y);
												modalStock(this.category , this.series.name);
											}
										}
									}
								},
								column: {
									minPointLength: 4
								}
							},
							tooltip: {
								formatter: function() {
									return '<b>'+ this.x +'</b><br/>'+
									this.series.name +': '+ this.y +'<br/>'+
									'Total: '+ this.point.stackTotal;
								}
							},
							series: [{
								name: 'Production',
								data: productionCount,
							}, {
								name: 'InTransit',
								data: inTransitCount,
							}, {
								name: 'FSTK',
								data: fstkCount,
							}]
						});
					// });
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

function modalStock(destination, location){
	if(location == 'Production'){
		var status = ['0', 'M'];
	}
	if(location == 'InTransit'){
		var status = ['1'];
	}
	if(location == 'FSTK'){
		var status = ['2'];
	}
	var data = {
		status:status,
		destination:destination
	}

	$.get('{{ url("fetch/tb_stock") }}', data, function(result, status, xhr){
		console.log(status);
		console.log(result);
		console.log(xhr);
		if(xhr.status == 200){
			if(result.status){
				$('#tableBody').html("");
				$('.modal-title').html("");
				$('.modal-title').html('Location <b>' + result.location+ '</b> for Destination <b>' +result.title+'</b>');
				var tableData = '';
				var totalQty = 0;
				var totalM3 = 0;
				$.each(result.table, function(key, value) {
					totalQty += value.actual;
					totalM3 += (((value.length*value.width*value.height)/value.lot_carton)*value.actual);
					tableData += '<tr>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ value.material_description +'</td>';
					tableData += '<td>'+ value.actual +'</td>';
					tableData += '<td>'+ (((value.length*value.width*value.height)/value.lot_carton)*value.actual).toFixed(2).toLocaleString() +'</td>';
					tableData += '</tr>';
				});
				$('#tableBody').append(tableData);
				$('#modalStock').modal('show');
				$('#totalQty').html('');
				$('#totalQty').append(totalQty.toLocaleString());
				$('#totalM3').html('');
				$('#totalM3').append(totalM3.toFixed(2).toLocaleString());
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