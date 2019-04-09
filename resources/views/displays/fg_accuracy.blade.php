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
		Finished Goods Accuracy <span class="text-purple"> FG週次出荷</span>
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
			<div id="container1" style="width: 100%; height: 440px; padding: 0;"></div>
			<div id="container2" style="width: 100%; height: 440px; padding: 0; margin-top: 15px;"></div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="modalDetailTitle" style="text-align: center;"></h4>
				<div class="modal-body table-responsive no-padding">
					<div id="container3" style="width: 100%; height: 100%; position: abosulte;"></div>
				</div>
			</div>
		</div>
	</div>
</div>

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

	var interval;
	var statusx = "idle";

	$(document).on('mousemove keyup keypress',function(){
		clearTimeout(interval);
		settimeout();
		statusx = "active";
	})

	function settimeout(){
		interval=setTimeout(function(){
			statusx = "idle";
			fillChart()
		},30000)
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

	function modalDetail(date, category){
		var data = {
			date:date,
			category:category
		}
		$.get('{{ url("fetch/dp_fg_accuracy_detail") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#modalDetailTitle').html('<span style="font-weight: bold;">' + result.title + '</span>');
					var data = result.accuracyDetail;
					var xCategories = [];
					var xSeries = [];

					for (var i = 0; i < data.length; i++) {
						xCategories.push(data[i].material_description);
						xSeries.push(data[i].qty);
					}

					Highcharts.chart('container3', {
						chart: {
							type: 'bar',
							height: 75+'%'
						},
						title: {
							text: null
						},
						xAxis: {
							categories: xCategories,
							type: 'category',
							labels: {
								// rotation: -45,
								style: {
									width:130,
									fontSize: '13px',
									fontFamily: 'Verdana, sans-serif',
									textOverflow: 'ellipsis'
								}
							}
						},
						plotOptions: {
							column: {
								minPointLength: 2,
								pointPadding: 0,
								size: '100%',
								borderWidth: 0.5
							},
							series: {
								groupPadding: 0.1,
								negativeColor: 'RGB(255,204,255)',
								borderColor: '#303030',
								cursor: 'pointer',
								dataLabels: {
									// rotation: -90,
									enabled: true,
									format: '{point.y:,.0f}',
									style:{
										// fontSize:'vw',
										color:'black',
										textOutline: false
									}
								}
							}
						},
						yAxis: {
							title: {
								text: 'Quantity (sets)'
							},
							tickPositioner: function () {

								var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));
								var halfMaxDeviation = Math.ceil(maxDeviation / 2);

								return [-maxDeviation, -halfMaxDeviation, 0, halfMaxDeviation, maxDeviation];
							}
						},
						legend: {
							enabled: false
						},
						credits:{
							enabled:false
						},
						series: [{
							name: 'Quantity',
							data: xSeries
						}]
					});

					$('#modalDetail').modal('show');
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

	$.date = function(dateObject) {
		var d = new Date(dateObject);
		var day = d.getDate();
		var month = d.getMonth() + 1;
		var year = d.getFullYear();
		if (day < 10) {
			day = "0" + day;
		}
		if (month < 10) {
			month = "0" + month;
		}
		var date = year + "-" + month + "-" + day;

		return date;
	};


	function fillChart(){

		$.get('{{ url("fetch/dp_fg_accuracy") }}', function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					
					$('#last_update').html('<b><span style="font-size: 14px;">Last Updated: '+ getActualFullDate() +'</span></b>');
					var data = result.accuracyBI;
					var dataMinus = [];
					var dataPlus = [];

					for (var i = 0; i < data.length; i++) {
						dataMinus.push([Date.parse(data[i].week_date), data[i].minus]);
						dataPlus.push([Date.parse(data[i].week_date), data[i].plus]);
					}

					window.chart = Highcharts.stockChart('container1', {
						chart:{
							borderColor: 'rgb(200,200,200)',
							borderRadius: 5,
							borderWidth: 2,
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
								value: 0,
								dashStyles: 'longdashdot'
							}]
						},
						legend: {
							layout: 'vertical',
							align: 'right',
							verticalAlign: 'middle'
						},
						xAxis:{
							type: 'datetime',
							tickInterval: 24 * 3600 * 1000
						},
						plotOptions: {
							series: {
								label: {
									connectorAllowed: false
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											modalDetail($.date(this.category), this.series.name);
										}
									}
								}
							}
						},
						credits: {
							enabled: false
						},
						series: [{
							name: 'BI Plus',
							data: dataPlus,
							color: '#0000FF',
							lineWidth: 1
						},{
							name: 'BI Minus',
							data: dataMinus,
							color: '#FF0000',
							lineWidth: 1
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
						lineWidth: 1,
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
							borderColor: 'rgb(200,200,200)',
							borderRadius: 5,
							borderWidth: 2,
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
							tickInterval: 24 * 3600 * 1000
						},
						plotOptions: {
							series: {
								label: {
									connectorAllowed: false
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											modalDetail($.date(this.category), this.series.name);
										}
									}
								}
							}
						},
						credits: {
							enabled: false
						},
						series: [{
							name: 'EI Plus',
							data: dataPlus2,
							color: '#0000FF',
							lineWidth: 1
						},{
							name: 'EI Minus',
							data: dataMinus2,
							color: '#FF0000',
							lineWidth: 1
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
						lineWidth: 1,
						enableMouseTracking: false,
						isInternal: true,
						data : dataMinus2,
						showInLegend:false
					});

					if(statusx == "idle"){
						setTimeout(fillChart(), 1000);
					}
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
