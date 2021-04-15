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
	/*tbody>tr>td{
		text-align:center;
		padding: 0px !important;
		}*/
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

		td {
			/*border-bottom: 1px solid white;*/
			padding: 0px 5px 0px 5px;
			text-align:center;
		}


		.alarm {
			width: 50px;
			height: 50px;
			-webkit-animation: alarm_ani 1s infinite;  /* Safari 4+ */
			-moz-animation: alarm_ani 1s infinite;  /* Fx 5+ */
			-o-animation: alarm_ani 1s infinite;  /* Opera 12+ */
			animation: alarm_ani 1s infinite;  /* IE 10+, Fx 29+ */
		}

		@-webkit-keyframes alarm_ani {
			0%, 49% {
				background-color: rgba(153, 0, 204, 0.85);
			}
			50%, 100% {
				background-color: #ed2f2f;
			}
		}

		.contain {
			position: relative; 
			width: 550px; 
			height: 1105px; 
			margin: 0 auto;
		}

		.judul {
			color: white; 
			float: left; 
			position: absolute; 
			background-color: rgba(153, 0, 204, 0.85);
			z-index: 1000;
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
			<div class="col-xs-12">
				<div class="box box-solid" style="background-color:#3c3c3c !important; color: white; box-shadow: 0 0 0 0 !important;">
					<div class="box-body" style="padding: 0px">
						<div style="border: 1px solid white; padding: 5px; border-radius: 5px;">
							<table width="100%" id="table_container">
								<!-- <tr>
									<td width="50%" rowspan="5" style="border-bottom: 0px; border-right: 1px solid white; color: #f39c12;">
										
										<div class="contain">
											<div class="judul" style="margin-top: 100px; margin-left: 35px; width: 240px; height: 100px">
												<b style="font-size: 40px">YMPI 1</b><br>
												<b style="font-size: 25px">600 ppm</b>
											</div>
											<div class="judul" style="margin-top: 40px; margin-left: 280px; width: 120px; height: 100px">
												<b style="font-size: 30px">YMPI 2</b><br>
												<b style="font-size: 20px">800 ppm</b>
											</div>
											<div class="judul alarm" style="margin-top: 40px; margin-left: 400px; width: 120px; height: 100px">
												<b style="font-size: 30px">YMPI 3</b><br>
												<b style="font-size: 20px">650 ppm</b>
											</div>
											<div class="judul alarm" style="margin-top: 900px; margin-left: 100px; width: 220px; height: 100px">
												<b style="font-size: 40px">YMPI 4</b><br>
												<b style="font-size: 25px">7250 ppm</b>
											</div>
											<img src="{{ url("images/office_map.png") }}" >
										</div>
									</td>
									<td width="50%">
										<div style="background-color: #f39c12; font-size: 25px; font-weight: bold;"> YMPI 01</div>
										<div id="chart_co1" style="height: 200px; border: 1px solid blue"></div>
									</td>
								</tr>
								<tr>
									<td width="50%">
										<div style="background-color: #f39c12; font-size: 25px; font-weight: bold;"> YMPI 02</div>
										<div id="chart_co1" style="height: 200px; border: 1px solid blue"></div>
									</td>
								</tr>
								<tr>
									<td width="50%"><div id="chart_co1" style="height: 200px"></div></td>
								</tr>
								<tr>
									<td width="50%"><div id="chart_co1" style="height: 200px"></div></td>
								</tr>
								<tr>
									<td width="50%"><div id="chart_co1" style="height: 200px"></div></td>
								</tr> -->
							</table>
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

		var audio_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');

		jQuery(document).ready(function() {
			$('body').toggleClass("sidebar-collapse");

			postData();

			// setInterval(postData, 600000);

			setInterval(postData, 120000);

		});

		function postData() {
			$.get('{{ url("post/general/airvisual/data") }}', function(result, status, xhr){
				if (result.status) {
					$("#table_container").empty();

					var count_alarm = 0;

					$.each(result.last_data, function(index, value){
						categories = [];
						co = [];
						max = [];
						bottom = [];
						mid = [];
						upper = [];

						var table_content = "";
						var node_name = "";
						var current_co = "";
						var current_temp = "";
						var current_humd = "";

						$.each(result.datas, function(index2, value2){
							if (value.location == value2.location) {
								max.push(1000);
								categories.push(value2.data_time2);
								co.push(value2.co);
								node_name = value2.location;
								current_co = value2.co;
								current_temp = value2.temperature;
								current_humd = value2.humidity;
							}
						})


						table_content += '<tr>';
						if (index == 0) {
							table_content += '<td width="50%" rowspan="5" style="border-bottom: 0px; border-right: 1px solid white; color: #f39c12;">';
							table_content += '<div class="contain">';

							table_content += '<div class="judul" style="margin-top: 0px; margin-left: 5px; width: 98%; height: 28px; background-color: #605ca8; color: white">';
							table_content += '<b style="font-size: 20px">YMPI OFFICE</b><br>';
							table_content += '</div>';

							cls = "";
							if (result.last_data[1].co >= 1000) {
								cls = "alarm";
								count_alarm++;
							}

							table_content += '<div class="judul '+cls+'" style="margin-top: 100px; margin-left: 35px; width: 240px; height: 130px">';

							table_content += '<b style="font-size: 30px; color: #f39c12">'+result.last_data[1].location+'</b><br>';
							table_content += '<b style="font-size: 20px">CO2 : '+result.last_data[1].co+' ppm</b><br>';
							table_content += '<b style="font-size: 20px">Temp. : '+result.last_data[1].temperature+' &#8451;</b><br>';
							table_content += '<b style="font-size: 20px">Humd. : '+result.last_data[1].humidity+' %</b>';
							table_content += '</div>';

							cls = "";
							if (result.last_data[2].co >= 1000) {
								cls = "alarm";
								count_alarm++;
							}

							table_content += '<div class="judul '+cls+'" style="margin-top: 40px; margin-left: 280px; width: 120px; height: 130px">';
							table_content += '<b style="font-size: 30px; color: #f39c12">'+result.last_data[2].location+'</b><br>';
							table_content += '<b style="font-size: 18px">CO2 : '+result.last_data[2].co+' ppm</b><br>';
							table_content += '<b style="font-size: 18px">Temp. : '+result.last_data[2].temperature+' &#8451;</b><br>';
							table_content += '<b style="font-size: 18px">Humd. : '+result.last_data[2].humidity+' %</b>';
							table_content += '</div>';

							cls = "";
							if (result.last_data[3].co >= 1000) {
								cls = "alarm";
								count_alarm++;
							}

							table_content += '<div class="judul '+cls+'" style="margin-top: 40px; margin-left: 405px; width: 120px; height: 130px">';
							table_content += '<b style="font-size: 30px; color: #f39c12">'+result.last_data[3].location+'</b><br>';
							table_content += '<b style="font-size: 18px">CO2 : '+result.last_data[3].co+' ppm</b><br>';
							table_content += '<b style="font-size: 18px">Temp. : '+result.last_data[3].temperature+' &#8451;</b><br>';
							table_content += '<b style="font-size: 18px">Humd. : '+result.last_data[3].humidity+' %</b>';
							table_content += '</div>';

							cls = "";
							if (result.last_data[4].co >= 1000) {
								cls = "alarm";
								count_alarm++;
							}

							table_content += '<div class="judul '+cls+'" style="margin-top: 400px; margin-left: 100px; width: 220px; height: 130px">';
							table_content += '<b style="font-size: 30px; color: #f39c12">'+result.last_data[4].location+'</b><br>';
							table_content += '<b style="font-size: 18px">CO2 : '+result.last_data[4].co+' ppm</b><br>';
							table_content += '<b style="font-size: 18px">Temp. : '+result.last_data[4].temperature+' &#8451;</b><br>';
							table_content += '<b style="font-size: 18px">Humd. : '+result.last_data[4].humidity+' %</b>';
							table_content += '</div>';

							cls = "";
							if (result.last_data[0].co >= 1000) {
								cls = "alarm";
								count_alarm++;
							}

							table_content += '<div class="judul '+cls+'" style="margin-top: 600px; margin-left: 100px; width: 220px; height: 130px">';
							table_content += '<b style="font-size: 30px; color: #f39c12">'+result.last_data[0].location+'</b><br>';
							table_content += '<b style="font-size: 18px">CO2 : '+result.last_data[0].co+' ppm</b><br>';
							table_content += '<b style="font-size: 18px">Temp. : '+result.last_data[0].temperature+' &#8451;</b><br>';
							table_content += '<b style="font-size: 18px">Humd. : '+result.last_data[0].humidity+' %</b>';
							table_content += '</div>';
							table_content += '<img src="{{ url("images/office_map.png") }}">';
							table_content += '</div>';
							table_content += '</td>';


							table_content += '<td width="50%">';
							table_content += '<div style="background-color: #605ca8; font-size: 25px; font-weight: bold;">'+node_name+'</div>';
							table_content += '<div id="chart_co'+index+'" style="height: 200px; border: 1px solid #605ca8"></div>';
							table_content += '</td>';
						} else {
							table_content += '<td width="50%">';
							table_content += '<div style="background-color: #605ca8; font-size: 25px; font-weight: bold;">'+node_name+'</div>';
							table_content += '<div id="chart_co'+index+'" style="height: 200px; border: 1px solid #605ca8"></div>';
							table_content += '</td>';
						}

						table_content += '</tr>';

						$("#table_container").append(table_content);

						Highcharts.chart('chart_co'+index, {
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
								tickInterval: 15
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
											if (isLast) {
												return this.x;
											} else {
												return '';
											}
										},
										allowOverlap: true
									},
								}
							},

							series: [
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
							},

							exporting: {
								enabled: false
							}

						});
					});


if (count_alarm > 0) {

	var times = 5;
	var loop = setInterval(repeat, 2000);

	function repeat() {
		times--;
		if (times === 0) {
			clearInterval(loop);
		}

		audio_error.play();
	}

	repeat(); 
}
}
})
}


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