@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	input {
		line-height: 22px;
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
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<form method="GET" action="{{ action('TemperatureController@indexBodyTempMonitoring') }}">
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal_from" name="tanggal_from" placeholder="Select Date From" onchange="fetchChart()">
						</div>
					</div>
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal_to" name="tanggal_to" placeholder="Select Date To" onchange="fetchChart()">
						</div>
					</div>

					<!-- <div class="col-xs-2">
						<button class="btn btn-success" type="submit"><i class="fa fa-search"></i> Search</button>
					</div> -->
					<div class="pull-right" id="loc" style="margin: 0px;padding-top: 0px;padding-right: 20px;font-size: 2vw;"></div>
				</form>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-10">
					<div id="container1" class="container1" style="width: 100%;"></div>
				</div>
				<div class="col-xs-2" style="padding-right: 0;">
					<div class="small-box" style="background: #cfcfcf; height: 120px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 1.5vw;text-align: right;"><b>TOTAL <span class="text-purple">トータル</span></b></h3>
							<h5 style="font-size: 3.3vw; font-weight: bold;text-align: right;" id="total">0</h5>
						</div>
						<div class="icon" style="padding-top: 55px; font-size: 60px;margin-right: 9vw">
							<i class="fa fa-users"></i>
						</div>
					</div>
					<div class="small-box" style="background: #668eff; height: 120px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 1.5vw;text-align: right;"><b>AVG TEMP <span class="text-purple">体温平均</span></b></h3>
							<h5 style="font-size: 3.3vw; font-weight: bold;text-align: right;" id="avg">0</h5>
						</div>
						<div class="icon" style="padding-top: 55px; font-size: 60px;margin-right: 11vw">
							<i class="fa fa-thermometer-half"></i>
						</div>
					</div>
					<div class="small-box" style="background: #eb5050; height: 120px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 1.5vw;text-align: right;"><b>HIGHEST TEMP <span class="text-purple">最高体温</span></b></h3>
							<h5 style="font-size: 3.3vw; font-weight: bold;text-align: right;" id="highest">0</h5>
						</div>
						<div class="icon" style="padding-top: 55px; font-size: 60px;margin-right: 11vw">
							<i class="fa fa-thermometer-full"></i>
						</div>
					</div>
					<div class="small-box" style="background: #00d941; height: 120px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 1.5vw;text-align: right;"><b>LIMIT <span class="text-purple">リミット</span></b></h3>
							<h5 style="font-size: 3.3vw; font-weight: bold;text-align: right;">37.5 <sup>o</sup>C</h5>
						</div>
						<div class="icon" style="padding-top: 60px; font-size: 50px;margin-right: 10.5vw">
							<i class="fa fa-window-close"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 3%;">Material</th>
								<th style="width: 9%;">Description</th>
								<th style="width: 3%;">Stock/Day</th>
								<th style="width: 3%;">Act. Stock</th>
								<th style="width: 3%;">Stock</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

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

	jQuery(document).ready(function(){
		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('.select2').select2();
		fetchChart();
		setInterval(fetchChart, 20000);
	});

	function fetchChart(){

		var tanggal_from = $('#tanggal_from').val();
		var tanggal_to = $('#tanggal_to').val();

		var data = {
			tanggal_from:tanggal_from,
			tanggal_to:tanggal_to
		}

		$.get('{{ url("fetch/temperature/fetch_body_temp_monitoring") }}', data, function(result, status, xhr) {
			if(result.status){

				for(var i = 0; i < result.datas_now.length; i++){
					// var Rate = result.data[i].ng_rate;

					$('#total').append().empty();
					$('#total').html(result.datas_now[i].total+ '');

					$('#avg').append().empty();
					$('#avg').html(result.datas_now[i].avg + ' <sup>o</sup>C');

					$('#highest').append().empty();
					$('#highest').html(result.datas_now[i].highest + ' <sup>o</sup>C');
				}


				var categories1 = [];
				var seriesCount1 = [];

				var week_date = [],week_date2 = [], avg = [], highest = [], date = [], series = [], series2 = [];

				$.each(result.datas, function(key, value){
					ctg1 = value.week_date;
					if(categories1.indexOf(ctg1) === -1){
						categories1[categories1.length] = ctg1;
					}
					
					week_date.push(value.week_date);
					avg.push(parseFloat(value.avg));
					series.push([week_date[key],avg[key]]);

					week_date2.push(value.week_date);
					highest.push(parseFloat(value.highest));
					series2.push([week_date2[key],highest[key]]);
				});

				Highcharts.chart('container1', {
				    chart: {
						type: 'spline',
						height: '500',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: 'Visitor Body Temperature',
						style: {
							fontSize: '20px',
							fontWeight: 'bold'
						}
					},
					// subtitle: {
				// 	// 	text: 'on '+result.dateTitle,
				// 	// 	style: {
				// 	// 		fontSize: '1vw',
				// 	// 		fontWeight: 'bold'
				// 	// 	}
				// 	// },
				    xAxis: {
				        categories: categories1,
				        type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '12px',
								fontWeight: 'bold'
							}
						},
				    },
				    yAxis: {
				        title: {
							text: 'Visitor Body Temperature',
							style: {
								color: '#eee',
								fontSize: '12px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							formatter: function () {
				                return this.value + ' °C';
				            },
							style:{
								fontSize:"12px"
							}
						},
						type: 'linear'
				    },
				    tooltip: {
						headerFormat: '<span>{point.series.name}</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y} °C</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						align: 'right',
						verticalAlign: 'top',
						x: -110,
						y: 30,
						floating: true,
						borderWidth: 1,
						backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
						shadow: true,
						itemStyle: {
							fontSize:'14px',
						},
					},	
					credits: {
						enabled: false
					},
				    	plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										// ShowModal(this.category,result.date);
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}'+ ' °C',
								style:{
									fontSize: '1vw'
								}
							},
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer'
						},
					},
				    series: [
				    {
				        type: 'spline',
						data: series,
						name: 'Avg Temp',
						colorByPoint: false,
						color:'#668eff',
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}'+ ' °C' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
				    }, {
				        type: 'spline',
						data: series2,
						name: 'Highest Temp',
						colorByPoint: false,
						color: "#eb5050",
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}'+ ' °C' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
				    }
				    ]
				});

				// Highcharts.chart('container1', {
				// 	chart: {
				// 		type: 'spline',
				// 		height: '500',
				// 		backgroundColor: "rgba(0,0,0,0)"
				// 	},
				// 	title: {
				// 		text: 'VISITOR BODY TEMP',
				// 		style: {
				// 			fontSize: '30px',
				// 			fontWeight: 'bold'
				// 		}
				// 	},
				// 	// subtitle: {
				// 	// 	text: 'on '+result.dateTitle,
				// 	// 	style: {
				// 	// 		fontSize: '1vw',
				// 	// 		fontWeight: 'bold'
				// 	// 	}
				// 	// },
				// 	xAxis: {
				// 		categories: categories1,
				// 		type: 'category',
				// 		gridLineWidth: 1,
				// 		gridLineColor: 'RGB(204,255,255)',
				// 		lineWidth:2,
				// 		lineColor:'#9e9e9e',
				// 		labels: {
				// 			style: {
				// 				fontSize: '20px',
				// 				fontWeight: 'bold'
				// 			}
				// 		},
				// 	},
				// 	yAxis: [

				// 	{
				// 		title: {
				// 			text: 'Highest Temperature',
				// 			style: {
				// 				color: '#eee',
				// 				fontSize: '20px',
				// 				fontWeight: 'bold',
				// 				fill: '#6d869f'
				// 			}
				// 		},
				// 		labels:{
				// 			formatter: function () {
				//                 return this.value + ' °C';
				//             },
				// 			style:{
				// 				fontSize:"16px"
				// 			}
				// 		},
				// 		type: 'linear',
				// 		opposite: true

				// 	},
				// 	{
				// 		title: {
				// 			text: 'Average Temperature',
				// 			style: {
				// 				color: '#eee',
				// 				fontSize: '20px',
				// 				fontWeight: 'bold',
				// 				fill: '#6d869f'
				// 			}
				// 		},
				// 		labels:{
				// 			formatter: function () {
				//                 return this.value + ' °C';
				//             },
				// 			style:{
				// 				fontSize:"16px"
				// 			}
				// 		},
				// 		type: 'linear',
						
				// 	}
				// 	],
				// 	tooltip: {
				// 		headerFormat: '<span>{point.series.name}</span><br/>',
				// 		pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y} °C</b><br/>',
				// 	},
				// 	legend: {
				// 		layout: 'horizontal',
				// 		align: 'right',
				// 		verticalAlign: 'top',
				// 		x: -110,
				// 		y: 30,
				// 		floating: true,
				// 		borderWidth: 1,
				// 		backgroundColor:
				// 		Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
				// 		shadow: true,
				// 		itemStyle: {
				// 			fontSize:'14px',
				// 		},
				// 	},	
				// 	credits: {
				// 		enabled: false
				// 	},
				// 	plotOptions: {
				// 		series:{
				// 			cursor: 'pointer',
				// 			point: {
				// 				events: {
				// 					click: function () {
				// 						// ShowModal(this.category,result.date);
				// 					}
				// 				}
				// 			},
				// 			dataLabels: {
				// 				enabled: true,
				// 				format: '{point.y}'+ ' °C',
				// 				style:{
				// 					fontSize: '1vw'
				// 				}
				// 			},
				// 			animation: {
				// 				enabled: true,
				// 				duration: 800
				// 			},
				// 			pointPadding: 0.93,
				// 			groupPadding: 0.93,
				// 			borderWidth: 0.93,
				// 			cursor: 'pointer'
				// 		},
				// 	},
				// 	series: [{
				// 		type: 'spline',
				// 		data: series,
				// 		name: 'Avg Temp',
				// 		yAxis:1,
				// 		colorByPoint: false,
				// 		color:'#668eff',
				// 		animation: false,
				// 		dataLabels: {
				// 			enabled: true,
				// 			format: '{point.y}'+ ' °C' ,
				// 			style:{
				// 				fontSize: '1vw',
				// 				textShadow: false
				// 			},
				// 		},
						
				// 	},
				// 	{
				// 		type: 'spline',
				// 		data: series2,
				// 		name: 'Highest Temp',
				// 		colorByPoint: false,
				// 		color: "#eb5050",
				// 		animation: false,
				// 		dataLabels: {
				// 			enabled: true,
				// 			format: '{point.y}'+ ' °C' ,
				// 			style:{
				// 				fontSize: '1vw',
				// 				textShadow: false
				// 			},
				// 		},
				// 	}
				// 	]
				// });
			}
			else{
				alert('Attempt to retrieve data failed');
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