@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
			<div class="col-xs-12">
				<div class="col-xs-2 pull-right">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border-color: #00a65a">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="date" onchange="drawChart()" placeholder="Select Date" style="border-color: #00a65a">
					</div>
					<br>
				</div>
			</div>
			<div class="col-xs-12">
				<div id="chart_daily" style="height: 350px"></div><br>
				<div id="chart_overall" style="height: 350px"></div><br>
			</div>
		</div>
	</div>	
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		drawChart();
		setInterval(drawChart, 10000);
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

	function drawChart() {
		var data = {
			date: $("#date").val()
		}
		$.get('{{ url("fetch/middle/muzusumashi") }}', data, function(result, status, xhr){

			var ctg = [], sedang = [], selesai = [], akan = [];

			$.each(result.datas, function(index, value){
				$.each(result.op, function(index2, value2){
					if (value.operator_id == value2.employee_id) {
						ctg.push(value2.name.split(' ').slice(0,2).join(' '));
					}
				})

				sedang.push(parseFloat(value.nganggur_min));
				selesai.push(parseFloat(value.selesai_min));
				akan.push(parseFloat(value.akan_min));
			})

			Highcharts.chart('chart_daily', {
				chart : {
					type: "column"
				},
				title: {
					text: 'Operator Monitoring',
					style: {
						fontSize: '30px',
						fontWeight: 'bold'
					}
				},
				xAxis: {
					type: 'category',
					categories: ctg
				},
				yAxis: {
					title: {
						text: 'Total Time (min)'
					}
				},
				legend: {
					enabled: true
				},
				plotOptions: {
					series: {
						cursor: 'pointer',
						point: {
						},
						borderWidth: 0,
						dataLabels: {
							enabled: true,
							format: '{point.y:.2f} min'
						},
						animation: false,
						minPointLength: 3

					}
				},
				credits: {
					enabled: false
				},

				tooltip: {
					formatter:function(){
						return this.key + ' : ' + this.y;
					}
				},

				series: [{
					name: 'Idle Operator',
					color: '#2598db',
					data: sedang
				}, {
					name: 'Finish in Rack',
					color: '#f78a1d',
					data: selesai
				},
				{
					name: 'Next Material Blanks',
					color: '#f90031',
					data: akan
				}
				]
			})

			var ctg3 = [], sedang_all = [], selesai_all = [], akan_all = [];

			$.each(result.overall, function(index, value){
				ctg3.push(value.tanggal);
				sedang_all.push(parseFloat(value.nganggur_min));
				selesai_all.push(parseFloat(value.selesai_min));
				akan_all.push(parseFloat(value.akan_min));
			})


			Highcharts.chart('chart_overall', {
				chart: {
					type: 'spline'			
				},
				title: {
					text: 'Overall Operator Monitoring',
					style: {
						fontSize: '30px',
						fontWeight: 'bold'
					}
				},

				yAxis: {
					title: {
						text: 'Total Time (min)'
					}
				},
				xAxis: {
					type: 'category',
					categories: ctg3
				},
				plotOptions: {
					series: {
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y:.2f} min'
						}
					}
				},

				tooltip: {
					headerFormat: '<b>{series.name}</b><br />',
					pointFormat: '{point.y:.2f}'
				},

				series: [{
					data: sedang_all,
					name: "Idle Operator",
					color: '#2598db',
				},
				{
					data: selesai_all,
					name: "Finish in Rack",
					color: '#f78a1d',
				},
				{
					data: akan_all,
					name: "Next Material Blanks",
					color: '#f90031',
				}]
			});
		})


	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	$('#date').datepicker({
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,
	});

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '3000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}
</script>
@endsection