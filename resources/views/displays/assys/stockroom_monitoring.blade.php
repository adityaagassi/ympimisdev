@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	#loading { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<br><br><br>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div id="period_title" class="col-xs-9" style="background-color: #2196f3;"><center><span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text"></span></center></div>
			<div class="col-xs-3" style="padding-right: 0;">
				<div class="input-group date">
					<div class="input-group-addon" style="background-color: #2196f3;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control pull-right" id="datepicker" name="datepicker" onchange="fetchChart()">
				</div>
			</div>
		</div>
	</div>
	<div class="row" id="monitoring" style="margin-top: 10px;">
		
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
		fetchChart();
		setInterval(fetchChart, 1000*60*5);
	});

	var key_details = "";

	function fetchChart(){
		// $('#loading').show();
		var period = $('#datepicker').val();
		var data = {
			period:period
		}
		$.get('{{ url("fetch/display/stockroom_monitoring") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#title_text').text('Stockroom Condition On '+result.now);
				var h = $('#period_title').height();
				$('#datepicker').css('height', h);

				key_details = result.stockroom_keys;
				var hpl = [];
				$('#monitoring').html("");
				var monitoring = "";
				var new_group = [];

				key_details.reduce(function (res, value) {
					if (!res[value.hpl]) {
						res[value.hpl] = {
							safe: 0,
							unsafe: 0,
							zero: 0,
							hpl: value.hpl
						};
						new_group.push(res[value.hpl])
					}
					res[value.hpl].safe += value.safe
					res[value.hpl].unsafe += value.unsafe
					res[value.hpl].zero += value.zero
					return res;
				}, {});

				$.each(result.stockroom_keys, function(key, value){
					if(hpl.indexOf(value.hpl) === -1){
						hpl.push(value.hpl);
						monitoring = '<div style="" class="col-xs-4" id="'+value.hpl+'"></div>';
						$('#monitoring').append(monitoring);
					}
				});

				console.log(new_group);

				for (var i = 0; i < new_group.length; i++) {
					Highcharts.chart(new_group[i].hpl, {
						chart: {
							backgroundColor: 'rgb(80,80,80)',
							type: 'pie'
						},
						title: {
							text: 'STOCK AVAILABILITY FOR - '+new_group[i].hpl+' '
						},
						tooltip: {
							pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
						},
						accessibility: {
							point: {
								valueSuffix: '%'
							}
						},
						legend: {
							align: 'right',
							verticalAlign: 'top',
							layout: 'vertical',
							x: 0,
							y: 100,
							symbolRadius: 1,
							borderWidth: 1
						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								borderColor: 'rgb(126,86,134)',
								dataLabels: {
									enabled: true,
									format: '<b>{point.y} item(s)</b><br>{point.percentage:.1f} %',
									distance: -50,
									style:{
										fontSize:'0.8vw',
										textOutline:0
									},
									color:'black'
								},
								showInLegend: true
							}
						},
						credits:{
							enabled:false
						},						
						series: [{
							name: 'Percentage',
							data: [{
								name: 'Stock > 1 Day',
								y: new_group[i].safe,
								color: '#90ee7e'
							}, {
								name: 'Stock < 1 Day',
								y: new_group[i].unsafe,
								color: '#f7a35c'
							}, {
								name: 'Stock Zero',
								y: new_group[i].zero,
								color: '#f45b5b'
							}]
						}]
					});
				}

			}
			else{
				alert('Unidentified ERROR!');
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