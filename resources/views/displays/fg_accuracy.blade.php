@extends('layouts.display')
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
<section class="content-header" style="padding-top: 0;">
	<h1>
		<span class="text-yellow">Finished Goods Accuracy</span> <span style="color: yellow;"> FG週次出荷</span>
		{{-- <small>By Shipment Schedule <span class="text-purple">??????</span></small> --}}
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section>
@endsection
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-6">
			<div id="container1" style="width: 100%; height: 350px; padding: 0;"></div>			
		</div>
		<div class="col-xs-6">
			<div id="container2" style="width: 100%; height: 350px; padding: 0;"></div>			
		</div>
		<div class="col-xs-6">
			<div id="container3" style="width: 100%; height: 350px; padding: 0;"></div>			
		</div>
		<div class="col-xs-6">
			<div id="container4" style="width: 100%; height: 350px; padding: 0;"></div>			
		</div>
		<div class="col-xs-6">
			<div id="container5" style="width: 100%; height: 350px; padding: 0;"></div>			
		</div>
		<div class="col-xs-6">
			<div id="container6" style="width: 100%; height: 350px; padding: 0;"></div>			
		</div>
		<div class="col-xs-6">
			<div id="container7" style="width: 100%; height: 350px; padding: 0;"></div>			
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
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
						<div id="container8" style="width: 100%; height: 100%; position: abosulte;"></div>
					</center>
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
		Highcharts.createElement('link', {
			href: '{{ url("fonts/UnicaOne.css")}}',
			rel: 'stylesheet',
			type: 'text/css'
		}, null, document.getElementsByTagName('head')[0]);

		Highcharts.theme = {
			colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
			'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
			chart: {
				backgroundColor: {
					linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
					stops: [
					[0, '#2a2a2b'],
					[1, '#3e3e40']
					]
				},
				style: {
					fontFamily: 'sans-serif'
				},
				plotBorderColor: '#606063'
			},
			title: {
				style: {
					color: '#E0E0E3',
					textTransform: 'uppercase',
					fontSize: '20px'
				}
			},
			subtitle: {
				style: {
					color: '#E0E0E3',
					textTransform: 'uppercase'
				}
			},
			xAxis: {
				gridLineColor: '#707073',
				labels: {
					style: {
						color: '#E0E0E3'
					}
				},
				lineColor: '#707073',
				minorGridLineColor: '#505053',
				tickColor: '#707073',
				title: {
					style: {
						color: '#A0A0A3'

					}
				}
			},
			yAxis: {
				gridLineColor: '#707073',
				labels: {
					style: {
						color: '#E0E0E3'
					}
				},
				lineColor: '#707073',
				minorGridLineColor: '#505053',
				tickColor: '#707073',
				tickWidth: 1,
				title: {
					style: {
						color: '#A0A0A3'
					}
				}
			},
			tooltip: {
				backgroundColor: 'rgba(0, 0, 0, 0.85)',
				style: {
					color: '#F0F0F0'
				}
			},
			plotOptions: {
				series: {
					dataLabels: {
						color: '#B0B0B3'
					},
					marker: {
						lineColor: '#333'
					}
				},
				boxplot: {
					fillColor: '#505053'
				},
				candlestick: {
					lineColor: 'white'
				},
				errorbar: {
					color: 'white'
				}
			},
			legend: {
				itemStyle: {
					color: '#E0E0E3'
				},
				itemHoverStyle: {
					color: '#FFF'
				},
				itemHiddenStyle: {
					color: '#606063'
				}
			},
			credits: {
				style: {
					color: '#666'
				}
			},
			labels: {
				style: {
					color: '#707073'
				}
			},

			drilldown: {
				activeAxisLabelStyle: {
					color: '#F0F0F3'
				},
				activeDataLabelStyle: {
					color: '#F0F0F3'
				}
			},

			navigation: {
				buttonOptions: {
					symbolStroke: '#DDDDDD',
					theme: {
						fill: '#505053'
					}
				}
			},

    // scroll charts
    rangeSelector: {
    	buttonTheme: {
    		fill: '#505053',
    		stroke: '#000000',
    		style: {
    			color: '#CCC'
    		},
    		states: {
    			hover: {
    				fill: '#707073',
    				stroke: '#000000',
    				style: {
    					color: 'white'
    				}
    			},
    			select: {
    				fill: '#000003',
    				stroke: '#000000',
    				style: {
    					color: 'white'
    				}
    			}
    		}
    	},
    	inputBoxBorderColor: '#505053',
    	inputStyle: {
    		backgroundColor: '#333',
    		color: 'silver'
    	},
    	labelStyle: {
    		color: 'silver'
    	}
    },

    navigator: {
    	handles: {
    		backgroundColor: '#666',
    		borderColor: '#AAA'
    	},
    	outlineColor: '#CCC',
    	maskFill: 'rgba(255,255,255,0.1)',
    	series: {
    		color: '#7798BF',
    		lineColor: '#A6C7ED'
    	},
    	xAxis: {
    		gridLineColor: '#505053'
    	}
    },

    scrollbar: {
    	barBackgroundColor: '#808083',
    	barBorderColor: '#808083',
    	buttonArrowColor: '#CCC',
    	buttonBackgroundColor: '#606063',
    	buttonBorderColor: '#606063',
    	rifleColor: '#FFF',
    	trackBackgroundColor: '#404043',
    	trackBorderColor: '#404043'
    },

    // special colors for some of the
    legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
    background2: '#505053',
    dataLabelsColor: '#B0B0B3',
    textColor: '#C0C0C0',
    contrastTextColor: '#F0F0F3',
    maskColor: 'rgba(255,255,255,0.3)'
};

// Apply the theme
Highcharts.setOptions(Highcharts.theme);
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
		$('#loading').show();
		$('#container8').hide();
		$('#modalDetailTitle').hide();
		$('#modalDetail').modal('show');
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

					Highcharts.chart('container8', {
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
								if(this.dataMax > 0){
									var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));									
								}
								else{
									var maxDeviation = 100;
								}
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


					$('#loading').hide();
					$('#container8').show();
					$('#modalDetailTitle').show();

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
					var data = result.accuracy;
					var dataMinusFL = [];
					var dataPlusFL = [];
					var dataMinusCL = [];
					var dataPlusCL = [];
					var dataMinusAS = [];
					var dataPlusAS = [];
					var dataMinusTS = [];
					var dataPlusTS = [];
					var dataMinusPN = [];
					var dataPlusPN = [];
					var dataMinusRC = [];
					var dataPlusRC = [];
					var dataMinusVN = [];
					var dataPlusVN = [];

					for (var i = 0; i < data.length; i++) {
						if(data[i].hpl == 'FLFG'){
							dataMinusFL.push([Date.parse(data[i].week_date), data[i].minus]);
							dataPlusFL.push([Date.parse(data[i].week_date), data[i].plus]);
						}
						if(data[i].hpl == 'CLFG'){
							dataMinusCL.push([Date.parse(data[i].week_date), data[i].minus]);
							dataPlusCL.push([Date.parse(data[i].week_date), data[i].plus]);
						}
						if(data[i].hpl == 'ASFG'){
							dataMinusAS.push([Date.parse(data[i].week_date), data[i].minus]);
							dataPlusAS.push([Date.parse(data[i].week_date), data[i].plus]);
						}
						if(data[i].hpl == 'TSFG'){
							dataMinusTS.push([Date.parse(data[i].week_date), data[i].minus]);
							dataPlusTS.push([Date.parse(data[i].week_date), data[i].plus]);
						}
						if(data[i].hpl == 'PN'){
							dataMinusPN.push([Date.parse(data[i].week_date), data[i].minus]);
							dataPlusPN.push([Date.parse(data[i].week_date), data[i].plus]);
						}
						if(data[i].hpl == 'RC'){
							dataMinusRC.push([Date.parse(data[i].week_date), data[i].minus]);
							dataPlusRC.push([Date.parse(data[i].week_date), data[i].plus]);
						}
						if(data[i].hpl == 'VENOVA'){
							dataMinusVN.push([Date.parse(data[i].week_date), data[i].minus]);
							dataPlusVN.push([Date.parse(data[i].week_date), data[i].plus]);
						}
					}

					window.chart = Highcharts.stockChart('container1', {
						rangeSelector: {
							selected: 0
						},
						scrollbar:{
							enabled:false
						},
						navigator:{
							enabled:false
						},
						title: {
							text: 'Flute Finished Goods',
							style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
						},
						yAxis: {
							tickPositioner: function () {

								// if(this.dataMax > 0){
								// 	var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));							
								// }
								// else{
								// 	var maxDeviation = 100;
								// }

								var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));						
								if(maxDeviation <= 0){
									maxDeviation = 1;
								}


								var halfMaxDeviation = Math.ceil(maxDeviation / 2);

								return [-maxDeviation, -halfMaxDeviation, 0, halfMaxDeviation, maxDeviation];
							},
							title: {
								text: 'Flute Finished Goods'
							},
							plotLines: [{
								color: '#FFFFFF',
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
								shadow: {
									width: 3,
									opacity: 0.4
								},
								label: {
									connectorAllowed: false
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											modalDetail($.date(this.category), 'FLFG');
										}
									}
								}
							}
						},
						credits: {
							enabled: false
						},
						series: [{
							name: 'FLFG Plus',
							data: dataPlusFL,
							color: 'RGB(255,211,0)',
							lineWidth: 2
						},{
							name: 'FLFG Minus',
							data: dataMinusFL,
							color: '#FF0000',
							lineWidth: 2
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

					// window.chart.addSeries({
					// 	xAxis: 1,
					// 	yAxis: 1,
					// 	type: "line",
					// 	lineWidth: 1,
					// 	color: "#FF0000",
					// 	enableMouseTracking: false,
					// 	isInternal: true,
					// 	data : dataMinusFL,
					// 	showInLegend:false
					// });

					window.chart2 = Highcharts.stockChart('container2', {
						rangeSelector: {
							selected: 0
						},
						scrollbar:{
							enabled:false
						},
						navigator:{
							enabled:false
						},
						title: {
							text: 'Clarinet Finished Goods',
							style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
						},
						yAxis: {
							tickPositioner: function () {
								// if(this.dataMax > 0){
								// 	var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));							
								// }
								// else{
								// 	var maxDeviation = 100;
								// }

								var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));						
								if(maxDeviation <= 0){
									maxDeviation = 1;
								}


								var halfMaxDeviation = Math.ceil(maxDeviation / 2);

								return [-maxDeviation, -halfMaxDeviation, 0, halfMaxDeviation, maxDeviation];
							},
							title: {
								text: 'Clarinet Finished Goods'
							},
							plotLines: [{
								color: '#FFFFFF',
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
								shadow: {
									width: 3,
									opacity: 0.4
								},
								label: {
									connectorAllowed: false
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											modalDetail($.date(this.category), 'CLFG');
										}
									}
								}
							}
						},
						credits: {
							enabled: false
						},
						series: [{
							name: 'CLFG Plus',
							data: dataPlusCL,
							color: 'RGB(255,211,0)',
							lineWidth: 2
						},{
							name: 'CLFG Minus',
							data: dataMinusCL,
							color: '#FF0000',
							lineWidth: 2
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

					// window.chart2.addSeries({
					// 	xAxis: 1,
					// 	yAxis: 1,
					// 	type: "line",
					// 	lineWidth: 1,
					// 	color: "#FF0000",
					// 	enableMouseTracking: false,
					// 	isInternal: true,
					// 	data : dataMinusCL,
					// 	showInLegend:false
					// });

					window.chart3 = Highcharts.stockChart('container3', {
						rangeSelector: {
							selected: 0
						},
						scrollbar:{
							enabled:false
						},
						navigator:{
							enabled:false
						},
						title: {
							text: 'Alto Saxophone Finished Goods',
							style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
						},
						yAxis: {
							tickPositioner: function () {
								// if(this.dataMax > 0){
								// 	var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));							
								// }
								// else{
								// 	var maxDeviation = 100;
								// }

								var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));						
								if(maxDeviation <= 0){
									maxDeviation = 1;
								}


								var halfMaxDeviation = Math.ceil(maxDeviation / 2);

								return [-maxDeviation, -halfMaxDeviation, 0, halfMaxDeviation, maxDeviation];
							},
							title: {
								text: 'Alto Saxophone Finished Goods'
							},
							plotLines: [{
								color: '#FFFFFF',
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
								shadow: {
									width: 3,
									opacity: 0.4
								},
								label: {
									connectorAllowed: false
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											modalDetail($.date(this.category), 'ASFG');
										}
									}
								}
							}
						},
						credits: {
							enabled: false
						},
						series: [{
							name: 'ASFG Plus',
							data: dataPlusAS,
							color: 'RGB(255,211,0)',
							lineWidth: 2
						},{
							name: 'ASFG Minus',
							data: dataMinusAS,
							color: '#FF0000',
							lineWidth: 2
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

					// window.chart3.addSeries({
					// 	xAxis: 1,
					// 	yAxis: 1,
					// 	type: "line",
					// 	lineWidth: 1,
					// 	color: "#FF0000",
					// 	enableMouseTracking: false,
					// 	isInternal: true,
					// 	data : dataMinusAS,
					// 	showInLegend:false
					// });

					window.chart4 = Highcharts.stockChart('container4', {
						rangeSelector: {
							selected: 0
						},
						scrollbar:{
							enabled:false
						},
						navigator:{
							enabled:false
						},
						title: {
							text: 'Tenor Saxophone Finished Goods',
							style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
						},
						yAxis: {
							tickPositioner: function () {
								// if(this.dataMax > 0){
								// 	var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));							
								// }
								// else{
								// 	var maxDeviation = 100;
								// }

								var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));	

								if(maxDeviation <= 0){
									maxDeviation = 1;
								}

								var halfMaxDeviation = Math.ceil(maxDeviation / 2);


								return [-maxDeviation, -halfMaxDeviation, 0, halfMaxDeviation, maxDeviation];
							},
							title: {
								text: 'Tenor Saxophone Finished Goods'
							},
							plotLines: [{
								color: '#FFFFFF',
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
								shadow: {
									width: 3,
									opacity: 0.4
								},
								label: {
									connectorAllowed: false
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											modalDetail($.date(this.category), 'TSFG');
										}
									}
								}
							}
						},
						credits: {
							enabled: false
						},
						series: [{
							name: 'TSFG Plus',
							data: dataPlusTS,
							color: 'RGB(255,211,0)',
							lineWidth: 2
						},{
							name: 'TSFG Minus',
							data: dataMinusTS,
							color: '#FF0000',
							lineWidth: 2
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

					// window.chart4.addSeries({
					// 	xAxis: 1,
					// 	yAxis: 1,
					// 	type: "line",
					// 	lineWidth: 1,
					// 	color: "#FF0000",
					// 	enableMouseTracking: false,
					// 	isInternal: true,
					// 	data : dataMinusTS,
					// 	showInLegend:false
					// });

					window.chart5 = Highcharts.stockChart('container5', {
						rangeSelector: {
							selected: 0
						},
						scrollbar:{
							enabled:false
						},
						navigator:{
							enabled:false
						},
						title: {
							text: 'Pianica Finished Goods',
							style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
						},
						yAxis: {
							tickPositioner: function () {
							// if(this.dataMax > 0){
								// 	var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));							
								// }
								// else{
								// 	var maxDeviation = 100;
								// }

								var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));						
								if(maxDeviation <= 0){
									maxDeviation = 1;
								}


								var halfMaxDeviation = Math.ceil(maxDeviation / 2);

								return [-maxDeviation, -halfMaxDeviation, 0, halfMaxDeviation, maxDeviation];
							},
							title: {
								text: 'Pianica Finished Goods'
							},
							plotLines: [{
								color: '#FFFFFF',
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
								shadow: {
									width: 3,
									opacity: 0.4
								},
								label: {
									connectorAllowed: false
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											modalDetail($.date(this.category), 'PN');
										}
									}
								}
							}
						},
						credits: {
							enabled: false
						},
						series: [{
							name: 'PN Plus',
							data: dataPlusPN,
							color: 'RGB(255,211,0)',
							lineWidth: 2
						},{
							name: 'PN Minus',
							data: dataMinusPN,
							color: '#FF0000',
							lineWidth: 2
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

					// window.chart5.addSeries({
					// 	xAxis: 1,
					// 	yAxis: 1,
					// 	type: "line",
					// 	lineWidth: 1,
					// 	color: "#FF0000",
					// 	enableMouseTracking: false,
					// 	isInternal: true,
					// 	data : dataMinusPN,
					// 	showInLegend:false
					// });

					window.chart6 = Highcharts.stockChart('container6', {
						rangeSelector: {
							selected: 0
						},
						scrollbar:{
							enabled:false
						},
						navigator:{
							enabled:false
						},
						title: {
							text: 'Recorder Finished Goods',
							style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
						},
						yAxis: {
							tickPositioner: function () {
							// if(this.dataMax > 0){
								// 	var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));							
								// }
								// else{
								// 	var maxDeviation = 100;
								// }

								var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));						
								if(maxDeviation <= 0){
									maxDeviation = 1;
								}


								var halfMaxDeviation = Math.ceil(maxDeviation / 2);

								return [-maxDeviation, -halfMaxDeviation, 0, halfMaxDeviation, maxDeviation];
							},
							title: {
								text: 'Recorder Finished Goods'
							},
							plotLines: [{
								color: '#FFFFFF',
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
								shadow: {
									width: 3,
									opacity: 0.4
								},
								label: {
									connectorAllowed: false
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											modalDetail($.date(this.category), 'RC');
										}
									}
								}
							}
						},
						credits: {
							enabled: false
						},
						series: [{
							name: 'RC Plus',
							data: dataPlusRC,
							color: 'RGB(255,211,0)',
							lineWidth: 2
						},{
							name: 'RC Minus',
							data: dataMinusRC,
							color: '#FF0000',
							lineWidth: 2
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

					// window.chart6.addSeries({
					// 	xAxis: 1,
					// 	yAxis: 1,
					// 	type: "line",
					// 	lineWidth: 1,
					// 	color: "#FF0000",
					// 	enableMouseTracking: false,
					// 	isInternal: true,
					// 	data : dataMinusRC,
					// 	showInLegend:false
					// });
					
					window.chart7 = Highcharts.stockChart('container7', {
						rangeSelector: {
							selected: 0
						},
						scrollbar:{
							enabled:false
						},
						navigator:{
							enabled:false
						},
						title: {
							text: 'Venova Finished Goods',
							style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
						},
						yAxis: {
							tickPositioner: function () {
								// if(this.dataMax > 0){
								// 	var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));							
								// }
								// else{
								// 	var maxDeviation = 100;
								// }

								var maxDeviation = Math.ceil(Math.max(Math.abs(this.dataMax), Math.abs(this.dataMin)));						
								if(maxDeviation <= 0){
									maxDeviation = 1;
								}


								var halfMaxDeviation = Math.ceil(maxDeviation / 2);

								return [-maxDeviation, -halfMaxDeviation, 0, halfMaxDeviation, maxDeviation];
							},
							title: {
								text: 'Venova Finished Goods'
							},
							plotLines: [{
								color: '#FFFFFF',
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
								shadow: {
									width: 3,
									opacity: 0.4
								},
								label: {
									connectorAllowed: false
								},
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											modalDetail($.date(this.category), 'Venova');
										}
									}
								}
							}
						},
						credits: {
							enabled: false
						},
						series: [{
							name: 'VN Plus',
							data: dataPlusVN,
							color: 'RGB(255,211,0)',
							lineWidth: 2
						},{
							name: 'VN Minus',
							data: dataMinusVN,
							color: '#FF0000',
							lineWidth: 2
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

					// window.chart7.addSeries({
					// 	xAxis: 1,
					// 	yAxis: 1,
					// 	type: "line",
					// 	lineWidth: 1,
					// 	color: "#FF0000",
					// 	enableMouseTracking: false,
					// 	isInternal: true,
					// 	data : dataMinusVN,
					// 	showInLegend:false
					// });


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
