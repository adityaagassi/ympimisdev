@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link type='text/css' rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
		padding: 3px;
	}
	tbody>tr>td{
		text-align:center;
		padding: 0px !important;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}

	.content-wrapper {
		padding-top: 0px !important;
	}

	.alert {

	}

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-8">
			<div class="box box-solid" style="background-color:#3c3c3c !important; color: white; box-shadow: 0 0 0 0 !important;">
				<div class="box-body" style="padding: 0px">
					<div class="div_name" style="border: 1px solid white; padding: 10px; border-radius: 5px;">
						<div style="font-weight: bold; font-size: 30px; display: inline-block; padding: 5px; border-radius: 5px;" id='body_name'></div>
						<div id="chart_co" style="margin-top: 5px"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-4">
			<div class="box box-solid" style="background-color:#3c3c3c !important; color: white; box-shadow: 0 0 0 0 !important;">
				<div class="box-body" style="padding: 0px">
					<div class="div_co" style="border: 1px solid white; padding: 10px; border-radius: 5px;">
						<div style="font-weight: bold; color: white; font-size: 20px">CO2</div>
						<div style="text-align: right; font-weight: bold; font-size: 50px" id="co_isi"> - ppm</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-4">
			<div class="box box-solid" style="background-color:#3c3c3c !important; color: white; box-shadow: 0 0 0 0 !important;">
				<div class="box-body" style="padding: 0px">
					<div class="div_tmp" style="border: 1px solid white; padding: 10px; border-radius: 5px;">
						<div style="font-weight: bold; color: white; font-size: 20px">Temperature</div>
						<div style="text-align: right; font-weight: bold; font-size: 50px" id="temp_isi"> &#x2103;</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-4">
			<div class="box box-solid" style="background-color:#3c3c3c !important; color: white; box-shadow: 0 0 0 0 !important;">
				<div class="box-body" style="padding: 0px">
					<div class="div_hum" style="border: 1px solid white; padding: 10px; border-radius: 5px;">
						<div style="font-weight: bold; color: white; font-size: 20px">Humidity</div>
						<div style="text-align: right; font-weight: bold; font-size: 50px" id="hum_isi">- %</div>
					</div>
				</div>
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
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
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
		$('body').toggleClass("sidebar-collapse");

		postData();

		// setInterval(postData, 600000);
		// setInterval(postData, 120000);

	});

	function postData() {
		$.get('{{ url("post/general/airvisual/data") }}', function(result, status, xhr){
			if (result.status) {
				console.log(result.status);

				categories = [];
				co = [];
				max = [];
				bottom = [];
				mid = [];
				upper = [];

				$.each(result.datas, function(index, value){
					categories.push(value.data_time2);
					co.push(value.co);
					max.push(1000);
					bottom.push(800);
					mid.push(999);
					upper.push(1500);

					if ((index + 1) == result.datas.length) {
						
						$("#body_name").html(value.location);
						$("#co_isi").html(value.co+" ppm");
						$("#temp_isi").html(value.temperature+" &#x2103;");
						$("#hum_isi").html(value.humidity+" %");

						// div_co
					}
				})

				Highcharts.chart('chart_co', {
					chart: {
						type: 'spline'
					},

					title: {
						text: ''
					},

					yAxis: {
						title: {
							text: 'CO2 rate'
						},
						gridLineWidth: 0,
						minorGridLineWidth: 0,
						plotBands: [{
							from: 0,
							to: 799,
							color: '#57ff5c'
						}, {
							from: 800,
							to: 999,
							color: '#fcba03'
						}, {
							from: 1000,
							to: 5000,
							color: '#ed4545'
						}]
					},

					xAxis: {
						categories: categories,
						tickInterval: 30
					},

					legend: {
						enabled: false
					},

					credits:{
						enabled:false
					},

					plotOptions: {
						series: {
							label: {
								connectorAllowed: false
							},
							marker: {
								enabled: false
							},
							animation: false,
						},
						spline: {
							dataLabels: {
								enabled: true,
								formatter: function(){
									var isLast = false;
									if(this.point.x === this.series.data[this.series.data.length -1].x && this.point.y === this.series.data[this.series.data.length -1].y) isLast = true;

									return isLast ? this.x : '';
								}
							}
						}
					},

					series: [
					// {
					// 	type: 'area',
					// 	name: 'safe',
					// 	data: bottom,
					// 	fillColor: {
					// 		linearGradient: [0, 0, 0, 300],
					// 		stops: [
					// 		[0, Highcharts.getOptions().colors[0]],
					// 		[1, Highcharts.color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
					// 		]
					// 	}
					// },
					// {
					// 	type: 'area',
					// 	name: 'warning',
					// 	data: mid,
					// 	fillColor: {
					// 		linearGradient: [0, 0, 0, 300],
					// 		stops: [
					// 		[0, Highcharts.getOptions().colors[1]],
					// 		[1, Highcharts.color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
					// 		]
					// 	}
					// },
					// {
					// 	type: 'area',
					// 	name: 'danger',
					// 	data: upper,
					// 	fillColor: {
					// 		linearGradient: [0, 0, 0, 300],
					// 		stops: [
					// 		[0, Highcharts.getOptions().colors[2]],
					// 		[1, Highcharts.color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
					// 		]
					// 	}
					// },
					{
						type: 'line',
						name: 'Max',
						color: 'red',
						data: max
					},
					{
						name: 'CO2',
						data: co,
						color: '#901aeb',
						lineWidth: 3
					}],

					responsive: {
						rules: [{
							condition: {
								maxWidth: 500
							},
						}]
					}

				});
			}
		})
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#90ee7e', '#f45b5b', '#7798BF', '#e3311e', '#aaeeee', '#ff0066',
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