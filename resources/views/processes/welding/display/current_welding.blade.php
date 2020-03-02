@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	.content{
		color: white;
		font-weight: bold;
	}

	hr {
		margin: 0px;
	}

	.over {
		-webkit-animation: sedang 1s infinite;  /* Safari 4+ */
		-moz-animation: sedang 1s infinite;  /* Fx 5+ */
		-o-animation: sedang 1s infinite;  /* Opera 12+ */
		animation: sedang 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes sedang {
		0%, 49% {
			background: rgba(0, 0, 0, 0);
		}
		50%, 100% {
			background-color: #f44336;
		}
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">
	<h1>
		<span class="text-yellow">
			{{ $title }}
		</span>
		<small>
			<span style="color: #FFD700;"> {{ $title_jp }}</span>
		</small>
	</h1>
</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding: 0px;">
	<div class="row">
		<div class="col-xs-12">
			<div id="container1"></div>
			<div id="container2"></div>
		</div>
	</div>
</section>
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

	jQuery(document).ready(function() {
		fillChart();
		setInterval(fillChart, 5000);
	});



	function diff_seconds(dt2, dt1){
		var diff = (dt2.getTime() - dt1.getTime()) / 1000;
		return Math.abs(Math.round(diff));
	}

	function fillChart(){

		$.get('{{ url("fetch/welding/current_welding") }}', function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){

					var xAxis = [];
					var act = [];
					var std = [];
					var plotBands = [];
					var loop = 0;

					for(var i = 0; i < 10; i++){
						loop += 1;

						var x = '';
						x += result.current[i].ws_name + '<br>';
						x += (result.current[i].name || 'Not Found') + '<br>';

						if(result.current[i].model != null){
							x += result.current[i].model + ' ' + result.current[i].key;
							xAxis.push(x);	
						}else{
							xAxis.push(x);
						}
						
						if(result.current[i].sedang != null){
							std.push(result.current[i].std);
							act.push(parseInt(diff_seconds(new Date(), new Date(result.current[i].sedang))/60));
						}else{
							std.push(0);
							act.push(0);
						}

						if(std[loop-1] < act[loop-1]){
							plotBands.push({from: (loop - 1.5), to: (loop - 0.5), color: 'rgba(255, 116, 116, .3)'});
						}
					}


					var chart = Highcharts.chart('container1', {
						title: {
							text: '',
							style: {
								fontSize: '23px',
								fontWeight: 'bold'
							}
						},
						yAxis: {
							title: {
								text: 'Minute(s)'
							},
							style: {
								fontSize: '26px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: xAxis,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '12px'
								}
							},
							plotBands: plotBands
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						credits: {
							enabled:false
						},
						legend : {
							enabled:false
						},
						plotOptions: {
							series:{
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										textOutline: false,
										fontSize: '1vw'
									}
								},
								animation: false,
								cursor: 'pointer'
							}
						},
						series: [{
							name:'Standart Time',
							type: 'column',
							color: '#3F51B5',
							data: std,
						},{
							name:'Actual Time',
							type: 'column',
							color: '#FFC107',
							data: act,
						}]
					});




					var xAxis = [];
					var act = [];
					var std = [];
					var plotBands = [];
					var loop = 0;
					for(var i = 10; i < 20; i++){
						loop += 1;

						var x = '';
						x += result.current[i].ws_name + '<br>';
						x += (result.current[i].name || 'Not Found') + '<br>';

						if(result.current[i].model != null){
							x += result.current[i].model + ' ' + result.current[i].key;
							xAxis.push(x);	
						}else{
							xAxis.push(x);
						}
						
						if(result.current[i].sedang != null){
							std.push(result.current[i].std);
							act.push(parseInt(diff_seconds(new Date(), new Date(result.current[i].sedang))/60));
						}else{
							std.push(0);
							act.push(0);
						}

						if(std[loop-1] < act[loop-1]){
							plotBands.push({from: (loop - 1.5), to: (loop - 0.5), color: 'rgba(255, 116, 116, .3)'});
						}
					}

					var chart = Highcharts.chart('container2', {
						title: {
							text: '',
						},
						yAxis: {
							title: {
								text: 'Minute(s)'
							},
							style: {
								fontSize: '26px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: xAxis,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '12px'
								}
							},
							plotBands: plotBands
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						credits: {
							enabled:false
						},
						legend : {
							align: 'center',
							verticalAlign: 'bottom',
							x: 0,
							y: 0,

							backgroundColor: (
								Highcharts.theme && Highcharts.theme.background2) || 'white',
							shadow: false
						},
						plotOptions: {
							series:{
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										textOutline: false,
										fontSize: '1vw'
									}
								},
								animation: false,
								cursor: 'pointer'
							}
						},
						series: [{
							name:'Standart Time',
							type: 'column',
							color: '#3F51B5',
							data: std,
						},{
							name:'Actual Time',
							type: 'column',
							color: '#FFC107',
							data: act,
						}]
					});

				}else{
					alert('Attempt to retrieve data failed.');
				}
			}
		});
}


Highcharts.createElement('link', {
	href: '{{ url("fonts/UnicaOne.css")}}',
	rel: 'stylesheet',
	type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
	colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
	'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
	chart: {
		backgroundColor: null,
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


</script>
@endsection