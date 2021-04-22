@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
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
		border:2px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:2px solid black;
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:2px solid black;
	}
	#loading, #error { display: none; }
	.bar {
		height:100px;
		display:inline-block;
		float:left;
		border: 1px solid black;
	}
	.text-rotasi {
		-ms-transform: rotate(-90deg); /* IE 9 */
		-webkit-transform: rotate(-90deg); /* Safari 3-8 */
		transform: rotate(-90deg);
		white-space: nowrap;
		font-size: 12px;
		vertical-align: middle;
		line-height: 100px;
	}
	#mc_head2 > th{
		padding: 0px;
		border-top: 0px;
		border-left: 1px solid black;
		border-right: 1px solid black;
		width: 10px;
		font-size: 1vw;
	}
	#mc_head > th{
		padding: 0px;
		border-bottom: 0px;
	}
</style>
@endsection

@section('content')
<section class="content" style="overflow-y:hidden; overflow-x:scroll; padding-top: 0px">
	<div class="row">
		<div class="col-xs-12">
			
			<!-- <table id="example1" class="table table-bordered">
				<thead style="background-color: #b89cff;">
					<tr id="mc_head">
					</tr>
					<tr id="mc_head2">
					</tr>
				</thead>
				<tbody id="mc_body" style="color: white">
				</tbody>
			</table> -->
			<div class="row">
				<div class="container" id="container" style="width: 100%"></div>
			</div>
		</div>
	</div>
</section>

</div>

@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts-gantt.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		drawTable();
		setInterval(drawTable, 50000);
	});

	

	function drawTable() {
		$.get('{{ url("fetch/injection_schedule") }}',  function(result, status, xhr){
			if (result.status) {
				var today = new Date();
				var day = 1000 * 60 * 60 * 24;
				var map = Highcharts.map;
				var dateFormat = Highcharts.dateFormat;
				var mesin = [];
				var series = [];
				var schedules = [];

				today.setUTCHours(0);
				today.setUTCMinutes(0);
				today.setUTCSeconds(0);
				today.setUTCMilliseconds(0);
				today = today.getTime();

				for (var i = 0; i < result.mesin.length; i++) {
					var deal = [];
					// var colors_skeleton = [];
					var unfilled = true;
						for(var j = 0; j < result.schedule.length;j++){
							if (result.schedule[j].machine == result.mesin[i]) {
								var colors_skeleton = "";
								if (result.schedule[j].type == 'molding') {
									unfilled = false;
									deal.push({
										machine : result.schedule[j].machine,
										materials : "",
										material : result.schedule[j].material_number+' - '+result.schedule[j].material_description,
										part : result.schedule[j].part+' - '+result.schedule[j].color,
										qty : result.schedule[j].qty,
										start_time : Date.parse(result.schedule[j].start_time),
										end_time : Date.parse(result.schedule[j].end_time),
										colors : '#8729c2'
									});
								}else{
									unfilled = false;
									if (result.schedule[j].color == 'BLUE') {
										var colors_skeleton = '#1432b8';
									}else if(result.schedule[j].color == 'PINK'){
										var colors_skeleton = '#b8149f';
									}else if(result.schedule[j].color == 'GREEN'){
										var colors_skeleton = '#14b833';
									}else if(result.schedule[j].color == 'RED'){
										var colors_skeleton = '#ff4a4a';
									}else if(result.schedule[j].color == 'IVORY'){
										var colors_skeleton = '#fff5a6';
									}else if(result.schedule[j].color == 'BROWN'){
										var colors_skeleton = '#b85e14';
									}else if(result.schedule[j].color == 'BEIGE'){
										var colors_skeleton = '#b87f14';
									}else{
										var colors_skeleton = '#000';
									}
									deal.push({
										machine : result.schedule[j].machine,
										materials : result.schedule[j].material_number+' - '+result.schedule[j].material_description,
										material : result.schedule[j].material_number+' - '+result.schedule[j].material_description,
										part : result.schedule[j].part+' - '+result.schedule[j].color,
										qty : result.schedule[j].qty,
										start_time : Date.parse(result.schedule[j].start_time),
										end_time : Date.parse(result.schedule[j].end_time),
										colors : colors_skeleton
									});
								}
							}
						}
						if (unfilled) {
							deal.push({
								machine : result.mesin[i],
								material : "",
								part : "",
								qty : 0,
								start_time : 0,
								end_time : 0,
								colors : ""
							});
						}


					schedules.push(
						{name: result.mesin[i],
						current: 0,
						deals: deal}
					);
				}

				series = schedules.map(function (car, i) {
				    var data = car.deals.map(function (deal) {
				        return {
				            id: 'deal-' + i,
				            machine: deal.machine,
				            material: deal.material,
				            materials: deal.materials,
				            part: deal.part,
				            qty: deal.qty,
				            start: deal.start_time,
				            end: deal.end_time,
				            color: deal.colors,
				            y: i
				        };
				    });
				    return {
				        name: car.name,
				        data: data,
				        current: car.deals[car.current]
				    };
				});

				var chart = Highcharts.ganttChart('container', {
				    series: series,
					chart: {
						backgroundColor: null
					},
					title: {
						text: null,
					},
					tooltip: {
						pointFormat: '<span>Mesin: <b>{point.machine}</b></span><br/><span>Material:<b> {point.material}</b></span><br/><span>Part: <b>{point.part}</b></span><br/><span>From: <b>{point.start:%e %b %Y, %H:%M}</b></span><br/><span>To: <b>{point.end:%e %b %Y, %H:%M}</b></span><br/><span>Qty: <b>{point.qty}</b></span>'
					},
					xAxis:
					[{
						tickInterval: 1000 * 60 * 60,
						min: today,
						max: today + 1 * day,
						currentDateIndicator:{
							enabled: true,
							width: 3,
				            dashStyle: 'dot',
				            color: 'red',
							label: {
								style: {
									fontSize: '14px',
									color: '#fff',
									fontWeight: 'bold'
								},
								x: -90,
								y: -4,
							},
						},
						scrollbar: {
							enabled: true,
							barBackgroundColor: 'gray',
							barBorderRadius: 7,
							barBorderWidth: 0,
							buttonBackgroundColor: 'gray',
							buttonBorderWidth: 0,
							buttonArrowColor: 'white',
							buttonBorderRadius: 7,
							rifleColor: 'white',
							trackBackgroundColor: '#3C3C3C',
							trackBorderWidth: 1,
							trackBorderColor: 'silver',
							trackBorderRadius: 7
						}
					},{
						tickInterval: 1000 * 60 * 60 * 24
					}],
					yAxis: {
						type: 'category',
						grid: {
							columns: [{
								title: {
									text: null
								},
								categories: map(series, function(s) {
									return s.name;
								})
							}]
						},
					},
					plotOptions: {
						gantt: {
							animation: false,
						},
						series:{
							cursor: 'pointer',
							dataLabels: {
						        enabled: true,
						        format: '{point.materials}',
						        style: {
						          cursor: 'default',
						          pointerEvents: 'none',
						          fontSize:'13px'
						        }
						    },
						    pointPadding: -0.31,
						    point: {
								events: {
									click: function () {
										alert('Edit Schedule');
									}
								}
							},
							borderWidth: 0
						}
					},
					credits: {
						enabled: false
					},
					exporting: {
						enabled: false
					}
				});

				$.each(chart.yAxis[0].ticks, function(i, tick) {
					$('.highcharts-yaxis-labels text').hover(function () {
						$(this).css('fill', '#33c570');
						$(this).css('cursor', 'pointer');
					},
					function () {
						$(this).css('cursor', 'pointer');
						$(this).css('fill', 'white');
					});
				});
			}else{
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		})
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

	Highcharts.setOptions({
		global: {
			useUTC: true,
			timezoneOffset: -420
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

@stop