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

	thead>tr>th{
		text-align:center;
		overflow:hidden;
		padding: 3px;
	}
	tbody>tr>td{
		text-align:center;
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
		border:1px solid #2a2a2b;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid #2a2a2b;
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid #2a2a2b;
		text-align: center;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid #2a2a2b;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}

</style>
@endsection
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin:0px;">
			<div class="pull-right" id="last_update" style="color: white; margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
		</div>
		<div class="col-xs-12" style="padding: 0px;">
			<div id="container" style="width:100%; margin-top: 1%;"></div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highcharts-gantt.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script type="text/javascript">
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		fillChart();
		setInterval(fillChart, 60000);

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

	Highcharts.setOptions({
		global: {
			useUTC: true,
			timezoneOffset: -420

		}
	});

	function mode(array){
		var function_name = 'Untuk menghitung jumlah index yg sering muncul';

		if(array.length == 0)
			return null;
		var modeMap = {};
		var maxEl = array[0], maxCount = 1;
		for(var i = 0; i < array.length; i++){
			var el = array[i];
			if(modeMap[el] == null)
				modeMap[el] = 1;
			else
				modeMap[el]++;  
			if(modeMap[el] > maxCount){
				maxEl = el;
				maxCount = modeMap[el];
			}
		}
		return maxCount;
	}

	function fillChart(){
		var position = $(document).scrollTop();


		$.get('{{ url("fetch/workshop/operatorload") }}', function(result, status, xhr){
			if(result.status){

				var today = new Date();
				var day = 1000 * 60 * 60 * 24;
				var map = Highcharts.map;
				var dateFormat = Highcharts.dateFormat;
				var series = [];
				var operators = [];

				today.setUTCHours(0);
				today.setUTCMinutes(0);
				today.setUTCSeconds(0);
				today.setUTCMilliseconds(0);
				today = today.getTime();


				for (var i = 0; i < result.operators.length; i++) {

					var deal = [];
					var unfilled = true;
					for (var j = 0; j < result.op_workloads.length; j++) {
						if(result.operators[i].operator_id == result.op_workloads[j].operator){
							unfilled = false;
							deal.push({
								mc_name: result.op_workloads[j].shortname,
								wjo : result.op_workloads[j].order_no,
								from : Date.parse(result.op_workloads[j].start_plan),
								to : Date.parse(result.op_workloads[j].finish_plan)
							});
						}
					}
					if(unfilled){
						deal.push({
							mc_name: 0,
							wjo : 0,
							from : 0,
							to : 0
						});
					}

					operators.push({
						name: result.operators[i].name,
						current: 0,
						deals: deal
					});
				}

				series = operators.map(function(value, i) {
					var data = value.deals.map(function(value2) {
						return {
							id: 'deal-' + i,
							wjo: value2.wjo,
							mc_name: value2.mc_name,
							start: value2.from,
							end: value2.to,
							y: i
						};
					});
					return {
						name: value.name,
						data: data,
						current: value.deals[value.current]
					};
				});

				// console.log(deal);
				// console.log(series);

				Highcharts.ganttChart('container', {
					series: series,
					title: {
						text: null,
					},
					tooltip: {
						pointFormat: '<span>Order No: {point.wjo}</span><br/> <span style="color:#25f55c">Proccess: {point.mc_name}</span><br/> <span>From: {point.start:%e %b %Y, %H:%M}</span><br/> <span>To: {point.end:%e %b %Y, %H:%M}</span>'
					},
					xAxis: {
						min: today,
						max: today + 3 * day,
						currentDateIndicator:{
							enabled: true,
							color : '#fff',
							label: {
								style: {
									fontSize: '14px',
									color: '#FFB300',
									fontWeight: 'bold'
								}
							}
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
						},
						tickLength: 0
					},
					yAxis: {
						type: 'category',
						grid: {
							columns: [{
								title: {
									text: 'OPERATORS',
									style: {
										fontSize: '18px',
										fontWeight: 'bold'
									}
								},
								categories: map(series, function(s) {
									return s.name;
								}),
							}]
						}
					},
					plotOptions: {
						gantt: {
							animation: false,
						},
						// series: {
						// 	dataLabels: {
						// 		enabled: true,
						// 		format: '<span>{point.mc_name}</span>',
						// 		useHTML: true,
						// 		align: 'left'
						// 	}
						// }
					},
					credits: {
						enabled: false
					},
					exporting: {
						enabled: false
					}
				});

				// var sumPlotLines = Math.ceil(Math.max(...data) / 1280);
				// var plotLines = [];
				// for (var i = 1; i <= sumPlotLines; i++){
				// 	plotLines.push({
				// 		color: '#FFB300',
				// 		value: (i * 1280),
				// 		dashStyle: 'shortdash',
				// 		width: 2,
				// 		zIndex: 5,
				// 		label: {
				// 			rotation: 0,
				// 			align:'right',
				// 			text: i + ' day(s)',
				// 			x:-7,
				// 			style: {
				// 				fontSize: '12px',
				// 				color: '#FFB300',
				// 				fontWeight: 'bold'
				// 			}
				// 		}
				// 	});
				// }


				// var sumPlotLines = Math.ceil(Math.max(...data) / 880);
				// var plotLines = [];
				// for (var i = 1; i <= sumPlotLines; i++){
				// 	plotLines.push({
				// 		color: '#FFB300',
				// 		value: (i * 880),
				// 		dashStyle: 'shortdash',
				// 		width: 2,
				// 		zIndex: 5,
				// 		label: {
				// 			rotation: 0,
				// 			align:'right',
				// 			text: i + ' day(s)',
				// 			x:-7,
				// 			style: {
				// 				fontSize: '12px',
				// 				color: '#FFB300',
				// 				fontWeight: 'bold'
				// 			}
				// 		}
				// 	});
				// }


				// var op = [];
				// var data = [];
				// var series = [];

				// for (var j = 0; j < result.op.length; j++) {
				// 	op.push(result.op[j].name);

				// 	var fill = true;
				// 	for (var k = 0; k < result.op_workload.length; k++) {
				// 		if(result.op[j].operator_id == result.op_workload[k].operator){
				// 			data.push(parseInt(result.op_workload[k].workload));
				// 			fill = false;
				// 		}
				// 	}
				// 	if(fill){
				// 		data.push(0);
				// 	}	
				// }

				// var sumPlotLines = Math.ceil(Math.max(...data) / 400);
				// var plotLines = [];


				// for (var i = 1; i <= sumPlotLines; i++){
				// 	plotLines.push({
				// 		color: '#FFB300',
				// 		value: (i * 400),
				// 		dashStyle: 'shortdash',
				// 		width: 2,
				// 		zIndex: 5,
				// 		label: {
				// 			align:'right',
				// 			text: i + ' day(s)',
				// 			x:-7,
				// 			style: {
				// 				fontSize: '12px',
				// 				color: '#FFB300',
				// 				fontWeight: 'bold'
				// 			}
				// 		}
				// 	});

				// }

			}

		});
}

</script>
@endsection