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
</style>
@endsection
@section('header')
<section class="content-header">
	<h1>
		Finished Goods Accuracy <span class="text-purple"> ????</span>
		{{-- <small>By Shipment Schedule <span class="text-purple">??????</span></small> --}}
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section>
@endsection
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div id="container1" style="width: 100%; height: 370px; padding: 0;" border="1px"></div>
			<div id="container2" style="width: 100%; height: 370px; padding: 0; margin-top: 15px;" border="1px"></div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		fillChart();
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

		$.get('{{ url("fetch/dp_fg_accuracy") }}', function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					var data = result.accuracyBI;
					var dataMinus = [];
					var dataPlus = [];

					for (var i = 0; i < data.length; i++) {
						dataMinus.push([Date.parse(data[i].week_date), data[i].minus]);
						dataPlus.push([Date.parse(data[i].week_date), data[i].plus]);
					}

					window.chart = Highcharts.stockChart('container1', {
						chart:{
							type: 'spline',
							backgroundColor: null,
							borderColor: 'rgb(100,100,100)',
							borderRadius: 10,
							borderWidth: 2,
							type: 'line'
						},
						rangeSelector: {
							selected: 0
						},
						scrollbar:{
							enabled:false
						},
						title: {
							text: 'Band Instruments',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						yAxis: {
							tickPositioner: function () {

								var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));
								var halfMaxDeviation = Math.ceil(maxDeviation / 2);

								return [-maxDeviation, -halfMaxDeviation, 0, halfMaxDeviation, maxDeviation];
							},
							title: {
								text: 'Band Instruments'
							},
							plotLines: [{
								color: '#00FF00',
								width: 2,
								value: 0
							}]
						},
						legend: {
							layout: 'vertical',
							align: 'right',
							verticalAlign: 'middle'
						},
						xAxis:{
							type: 'datetime',
						},
						plotOptions: {
							series: {
								label: {
									connectorAllowed: false
								}
							}
						},
						credits: {
							enabled: false
						},
						series: [{
							name: 'Plus',
							data: dataPlus,
							color: '#0000FF',
							width: 1
						},{
							name: 'Minus',
							data: dataMinus,
							color: '#FF0000',
							width: 1
						}],
						responsive: {
							rules: [{
								condition: {
									maxWidth: 500
								},
								chartOptions: {
									legend: {
										layout: 'horizontal',
										align: 'center',
										verticalAlign: 'bottom'
									}
								}
							}]
						}
					});

					window.chart.addSeries({
						xAxis: 1,
						yAxis: 1,
						type: "line",
						color: "#FF0000",
						enableMouseTracking: false,
						isInternal: true,
						data : dataMinus,
						showInLegend:false
					});

					var data2 = result.accuracyEI;
					var dataMinus2 = [];
					var dataPlus2 = [];

					for (var i = 0; i < data2.length; i++) {
						dataMinus2.push([Date.parse(data2[i].week_date), data2[i].minus]);
						dataPlus2.push([Date.parse(data2[i].week_date), data2[i].plus]);
					}

					window.chart2 = Highcharts.stockChart('container2', {
						chart:{
							type: 'spline',
							backgroundColor: null,
							borderColor: 'rgb(100,100,100)',
							borderRadius: 10,
							borderWidth: 2,
							type: 'line'
						},
						rangeSelector: {
							selected: 0
						},
						scrollbar:{
							enabled:false
						},
						title: {
							text: 'Educational Instruments',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						yAxis: {
							tickPositioner: function () {

								var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));
								var halfMaxDeviation = Math.ceil(maxDeviation / 2);

								return [-maxDeviation, -halfMaxDeviation, 0, halfMaxDeviation, maxDeviation];
							},
							title: {
								text: 'Educational Instruments'
							},
							plotLines: [{
								color: '#00FF00',
								width: 2,
								value: 0
							}]
						},
						legend: {
							layout: 'vertical',
							align: 'right',
							verticalAlign: 'middle'
						},
						xAxis:{
							type: 'datetime',
						},
						plotOptions: {
							series: {
								label: {
									connectorAllowed: false
								}
							}
						},
						credits: {
							enabled: false
						},
						series: [{
							name: 'Plus',
							data: dataPlus2,
							color: '#0000FF',
							width: 1
						},{
							name: 'Minus',
							data: dataMinus2,
							color: '#FF0000',
							width: 1
						}],
						responsive: {
							rules: [{
								condition: {
									maxWidth: 500
								},
								chartOptions: {
									legend: {
										layout: 'horizontal',
										align: 'center',
										verticalAlign: 'bottom'
									}
								}
							}]
						}
					});

					window.chart2.addSeries({
						xAxis: 1,
						yAxis: 1,
						type: "line",
						color: "#FF0000",
						enableMouseTracking: false,
						isInternal: true,
						data : dataMinus2,
						showInLegend:false
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
