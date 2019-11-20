@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.content{
		color: white;
		font-weight: bold;
	}
	#loading, #error { display: none; }

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
		</div>

		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="col-xs-12" style="margin-top: 5px;">
				<div id="container2" style="width: 100%;height: 20%; margin-bottom: 1%;"></div>
				<div id="container1" style="width: 100%;height: 20%; margin-bottom: 1%;"></div>
			</div>
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

	jQuery(document).ready(function(){
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
						color: 'white'
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

			legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
			background2: '#505053',
			dataLabelsColor: '#B0B0B3',
			textColor: '#C0C0C0',
			contrastTextColor: '#F0F0F3',
			maskColor: 'rgba(255,255,255,0.3)'
		};

		Highcharts.setOptions(Highcharts.theme);
		fillChart();
		setInterval(fillChart, 30000);

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

	function fillChart() {
		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

		var position = $(document).scrollTop();

		
		$.get('{{ url("fetch/middle/buffing_daily_ng_rate") }}', function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					var tgl = [];
					var ng_rate_alto = [];
					var ng_rate_tenor = [];

					for(var i = 0; i < result.alto.length; i++){
						tgl.push(result.alto[i].week_date);
						ng_rate_alto.push([Date.parse(tgl[i]), result.alto[i].rate]);
						ng_rate_tenor.push([Date.parse(tgl[i]), result.tenor[i].rate]);
					}

					var chart = Highcharts.stockChart('container1', {
						chart:{
							type:'spline',
						},
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
							text: 'SX Buffing NG Rate By HPL',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						yAxis: {
							title: {
								text: 'NG Rate (%)'
							},
							plotLines: [{
								color: '#FFFFFF',
								width: 2,
								value: 0,
								dashStyles: 'longdashdot'
							}]
						},
						xAxis: {
							categories: 'datetime',
							tickInterval: 24 * 3600 * 1000 
						},
						tooltip: {
							pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y:.2f}%</b>',
						},
						legend : {
							enabled:true
						},
						credits: {
							enabled:false
						},
						plotOptions: {
							series: {
								animation: false,
								dataLabels: {
									enabled: true,
									format: '{point.y:,.1f}%',
								},
								connectNulls: true,
								shadow: {
									width: 3,
									opacity: 0.4
								},
								label: {
									connectorAllowed: false
								},
								cursor: 'pointer',
							}
						},
						series: [
						{
							name:'Alto Key',
							color: '#f5ff0d',
							data: ng_rate_alto,
							lineWidth: 3
						},
						{
							name:'Tenor Key',
							color: '#00FF00',
							data: ng_rate_tenor,
							lineWidth: 3
						}
						],
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

					var names = [];
					var dataCount = [];
					var cat;

					$.each(result.daily_by_ng, function(key, value) {
						cat = value.ng_name;
						if(names.indexOf(cat) === -1){
							names[names.length] = cat;
						}
					});

					$.each(names, function(key, name){
						var series = [];
						$.each(result.daily_by_ng, function(i, value) {
							if(value.ng_name == name){
								series.push([Date.parse(value.created_at), parseFloat(value.percentage)]);
							}
						});

						dataCount[key] = {
							name:name,
							data:series
						};
					});

					window.chart2 = Highcharts.stockChart('container2', {
						chart:{
							type:'spline',
						},
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
							text: 'SX Buffing NG Rate By NG Name',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						yAxis: {
							title: {
								text: 'NG Rate (%)'
							},
							plotLines: [{
								color: '#FFFFFF',
								width: 2,
								value: 0,
								dashStyles: 'longdashdot'
							}]
						},
						xAxis: {
							categories: 'datetime',
							tickInterval: 24 * 3600 * 1000 
						},
						tooltip: {
							pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y:.2f}%</b>',
						},
						legend : {
							enabled:true
						},
						credits: {
							enabled:false
						},
						plotOptions: {
							series: {
								animation: false,
								dataLabels: {
									enabled: true,
									format: '{point.y:,.1f}%',
								},
								connectNulls: true,
								shadow: {
									width: 3,
									opacity: 0.4
								},
								label: {
									connectorAllowed: false
								},
								cursor: 'pointer',
							}
						},
						series: dataCount,
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
				
				$(document).scrollTop(position);
				
				}
			}
		});
}



</script>
@endsection