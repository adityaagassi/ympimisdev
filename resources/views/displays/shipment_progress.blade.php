@extends('layouts.display')

@section('stylesheets')
<style type="text/css">

</style>
@endsection

@section('content')
<div class="col-xs-12">

</div>
<div id="container" style="min-width: 310px; height:600px; margin: 0 auto"></div>

@endsection

@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});


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
				fontFamily: '\'Unica One\', sans-serif'
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

	jQuery(document).ready(function() {

		fillChart();
	});

	function fillChart(){
		$.get('{{ url("fetch/display/shipment_progress") }}', function(result, status, xhr){
			if(result.status){

				var data = result.shipment_results;
				var xCategories = [];
				var planFL = [];
				var planCL = [];
				var planAS = [];
				var planTS = [];
				var planPN = [];
				var planRC = [];
				var planVN = [];
				var actualFL = [];
				var actualCL = [];
				var actualAS = [];
				var actualTS = [];
				var actualPN = [];
				var actualRC = [];
				var actualVN = [];
				var i, cat;
				var intVal = function ( i ) {
					return typeof i === 'string' ?
					i.replace(/[\$,]/g, '')*1 :
					typeof i === 'number' ?
					i : 0;
				};
				for(i = 0; i < data.length; i++){
					cat = data[i].st_date;
					if(xCategories.indexOf(cat) === -1){
						xCategories[xCategories.length] = cat;
					}
					if(data[i].hpl == 'FLFG'){
						planFL.push(data[i].plan-data[i].act);
						actualFL.push(data[i].act);
					}
					if(data[i].hpl == 'CLFG'){
						planCL.push(data[i].plan-data[i].act);
						actualCL.push(data[i].act);
					}
					if(data[i].hpl == 'ASFG'){
						planAS.push(data[i].plan-data[i].act);
						actualAS.push(data[i].act);
					}
					if(data[i].hpl == 'TSFG'){
						planTS.push(data[i].plan-data[i].act);
						actualTS.push(data[i].act);
					}
					if(data[i].hpl == 'PN'){
						planPN.push(data[i].plan-data[i].act);
						actualPN.push(data[i].act);
					}
					if(data[i].hpl == 'RC'){
						planRC.push(data[i].plan-data[i].act);
						actualRC.push(data[i].act);
					}
					if(data[i].hpl == 'VENOVA'){
						planVN.push(data[i].plan-data[i].act);
						actualVN.push(data[i].act);
					}
				}

				if(xCategories.length <= 5){
					var scrollMax = xCategories.length-1;
				}
				else{
					var scrollMax = 4;
				}


				var yAxisLabels = [0,25,50,75,100,110];
				Highcharts.chart('container', {

					chart: {
						type: 'column'
					},

					title: {
						text: 'Shipment Fulfillment Progress'
					},
					legend:{
						enabled: false
					},
					xAxis: {
						categories: xCategories,
						type: 'category',
						gridLineWidth: 5,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							style: {
								fontSize: '20px'
							}
						},
						min: 0,
						max:scrollMax,
						scrollbar: {
							enabled: true
						}
					},
					yAxis: {
						title: {
							enabled:false,
						},
						tickPositioner: function() {
							return yAxisLabels;
						},
						plotLines: [{
							color: '#FF0000',
							width: 2,
							value: 100,
							label: {
								align:'right',
								text: '100%',
								x:-7,
								style: {
									fontSize: '1vw',
									color: '#FF0000',
									fontWeight: 'bold'
								}
							}
						}],
						labels: {
							enabled:false
						},
						stackLabels: {
							enabled: true,
							rotation: -90,
							verticalAlign: 'middle',
							style: {
								fontSize: '20px',
								color: 'white',
								textOutline: false,
								fontWeight: 'bold',
							},
							formatter:  function() {
								return this.stack;
							}
						}
					},
					tooltip: {
						formatter: function () {
							return '<b>' + this.x + '</b><br/>' +
							this.series.name + ': ' + this.y + '<br/>' +
							'Total: ' + this.point.stackTotal;
						}
					},
					plotOptions: {
						column: {
							stacking: 'percent',
						},
						series:{
							animation: false,
							minPointLength: 2,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										fillModal(this.category, this.series.name);
									}
								}
							}
						}
					},
					series: [{
						name: 'Plan',
						data: planFL,
						stack: 'FLFG',
						color: 'rgba(255, 0, 0, 0.25)'
					}, {
						name: 'Plan',
						data: planCL,
						stack: 'CLFG',
						color: 'rgba(255, 0, 0, 0.25)'
					}, {
						name: 'Plan',
						data: planAS,
						stack: 'ASFG',
						color: 'rgba(255, 0, 0, 0.25)'
					}, {
						name: 'Plan',
						data: planTS,
						stack: 'TSFG',
						color: 'rgba(255, 0, 0, 0.25)'
					}, {
						name: 'Plan',
						data: planPN,
						stack: 'PN',
						color: 'rgba(255, 0, 0, 0.25)'
					}, {
						name: 'Plan',
						data: planRC,
						stack: 'RC',
						color: 'rgba(255, 0, 0, 0.25)'
					}, {
						name: 'Plan',
						data: planVN,
						stack: 'VENOVA',
						color: 'rgba(255, 0, 0, 0.25)'
					}, {
						name: 'Actual',
						data: actualFL,
						stack: 'FLFG',
						color: 'rgba(0, 255, 0, 0.90)'
					}, {
						name: 'Actual',
						data: actualCL,
						stack: 'CLFG',
						color: 'rgba(0, 255, 0, 0.90)'
					}, {
						name: 'Actual',
						data: actualAS,
						stack: 'ASFG',
						color: 'rgba(0, 255, 0, 0.90)'
					}, {
						name: 'Actual',
						data: actualTS,
						stack: 'TSFG',
						color: 'rgba(0, 255, 0, 0.90)'
					}, {
						name: 'Actual',
						data: actualPN,
						stack: 'PN',
						color: 'rgba(0, 255, 0, 0.90)'
					}, {
						name: 'Actual',
						data: actualRC,
						stack: 'RC',
						color: 'rgba(0, 255, 0, 0.90)'
					}, {
						name: 'Actual',
						data: actualVN,
						stack: 'VENOVA',
						color: 'rgba(0, 255, 0, 0.90)'
					}]
				});


			}
			else{
				alert('Attempt to retrieve data failed.')
			}
		});
}

function addZero(i) {
	if (i < 10) {
		i = "0" + i;
	}
	return i;
}

function getActualFullDate(){
	var d = new Date();
	var day = addZero(d.getDate());
	var month = addZero(d.getMonth()+1);
	var year = addZero(d.getFullYear());
	var h = addZero(d.getHours());
	var m = addZero(d.getMinutes());
	var s = addZero(d.getSeconds());
	return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
}
</script>
@endsection