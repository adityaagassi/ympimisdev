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
				<form method="GET" action="{{ action('MiddleProcessController@indexOpAnalysis') }}">
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="dateFrom" name="dateFrom" placeholder="Tanggal Mulai">
						</div>
					</div>
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="dateTo" name="dateTo" placeholder="Tanggal Sampai">
						</div>
					</div>
					<div class="col-xs-2">
						<button class="btn" style="background-color: #605ca8; color: white;" type="submit"><i class="fa fa-search"></i> Search</button>
					</div>
				</form>
				<div class="col-xs-6">
					<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;font-size: 20px; color: white;"></div>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<div class="col-xs-9" style="padding-bottom: 5px;">
					<div id="container1" class="container1" style="width: 100%;"></div>
				</div>
				<div class="col-xs-3" style="padding-left: 0;">
					<div class="box box-solid">
						<div class="box-header" style="background-color: #00a65a;">
							<center><span style="font-size: 22px; font-weight: bold; color: black;">Actual Time MP Resume</span></center>
						</div>
						<ul class="nav nav-pills nav-stacked">
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Total MP All Shift
									<span class="pull-right text-green" id="act2">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Acc Time
									<span class="pull-right text-green" id="act3">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Acc MP
									<span class="pull-right text-green" id="act4">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Avg Working Time / OP
									<span class="pull-right text-green" id="act5">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Target Working Time
									<span class="pull-right text-green" id="act6">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Avg Loss Time
									<span class="pull-right text-green" id="act7">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Total Loss Time
									<span class="pull-right text-green" id="act8">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold;">Loss MP Suggestion
									<span class="pull-right text-red" id="act9" style="font-size: 3vw;">0</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<div class="col-xs-9" style="padding-bottom: 5px;">
					<div id="container2" class="container2" style="width: 100%;"></div>
				</div>
				<div class="col-xs-3" style="padding-left: 0;">
					<div class="box box-solid">
						<div class="box-header" style="background-color: #ff851b;">
							<center><span style="font-size: 22px; font-weight: bold; color: black;">Standard Time MP Resume</span></center>
						</div>
						<ul class="nav nav-pills nav-stacked">
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Total MP All Shift
									<span class="pull-right text-green" id="std2">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Acc Time
									<span class="pull-right text-green" id="std3">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Acc MP
									<span class="pull-right text-green" id="std4">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Avg Working Time / OP
									<span class="pull-right text-green" id="std5">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Target Working Time
									<span class="pull-right text-green" id="std6">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Avg Loss Time
									<span class="pull-right text-green" id="std7">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Total Loss Time
									<span class="pull-right text-green" id="std8">0</span>
								</a>
							</li>
							<li>
								<a href="#" style="font-size: 18px; font-weight: bold;">Loss MP Suggestion
									<span class="pull-right text-red" id="std9" style="font-size: 3vw;">0</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
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

	jQuery(document).ready(function(){
		$('#dateFrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#dateTo').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		fetchChart();
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

	function fetchChart(){

		var dateFrom = "{{$_GET['dateFrom']}}";
		var dateTo = "{{$_GET['dateTo']}}";

		var data = {
			dateFrom:dateFrom,
			dateTo:dateTo
		}

		$.get('{{ url("fetch/middle/op_analysis") }}', data, function(result, status, xhr) {
			if(result.status){
				var data = result.datas;
				var categories1 = [];
				var time1 = [];
				var loss1 = [];
				var time2 = [];
				var loss2 = [];
				var target = result.datas[0].target;
				var mp = [];
				var i, cat;

				var maxMP = 0;
				var totalTimeAct = 0;
				var totalTimeStd = 0;
				var totalMP = 0;

				for(i = 0; i < data.length; i++){
					cat = data[i].cat;
					if(categories1.indexOf(cat) === -1){
						categories1[categories1.length] = cat;
					}

					time1.push(parseFloat(data[i].actual/data[i].divider));
					loss1.push(parseFloat(data[i].target-(data[i].actual/data[i].divider)));

					time2.push(parseFloat(data[i].standard/data[i].divider));
					loss2.push(parseFloat(data[i].target-(data[i].standard/data[i].divider)));

					totalTimeAct += parseFloat(data[i].actual);
					totalTimeStd += parseFloat(data[i].standard);
					totalMP += parseFloat(data[i].divider);

					mp.push(parseInt(data[i].divider));
				}

				maxMP = Math.max.apply(Math, mp);


				$('#last_update').html('<b>Period: '+ result.dateFrom +' to '+ result.dateTo +'</b>');

				$('#act2').text(maxMP+' Operator(s)');
				$('#act4').text(totalMP+' Operator(s)');
				$('#act3').text(totalTimeAct.toFixed(0)+' Min(s)');
				$('#act5').text((totalTimeAct/totalMP).toFixed(0)+' Min(s)');
				$('#act6').text(target+' Min(s)');
				$('#act7').text((target-(totalTimeAct/totalMP)).toFixed(2)+' Min(s)');
				$('#act8').text(((target-(totalTimeAct/totalMP))*maxMP).toFixed(0)+' Min(s)');
				$('#act9').text((((target-(totalTimeAct/totalMP))*maxMP)/480).toFixed(0)+' MP(s)');

				$('#std2').text(maxMP+' Operator(s)');
				$('#std4').text(totalMP+' Operator(s)');
				$('#std3').text(totalTimeStd.toFixed(0)+' Min(s)');
				$('#std5').text((totalTimeStd/totalMP).toFixed(0)+' Min(s)');
				$('#std6').text(target+' Min(s)');
				$('#std7').text((target-(totalTimeStd/totalMP)).toFixed(2)+' Min(s)');
				$('#std8').text(((target-(totalTimeStd/totalMP))*maxMP).toFixed(0)+' Min(s)');
				$('#std9').text((((target-(totalTimeStd/totalMP))*maxMP)/480).toFixed(0)+' MP(s)');

				var yAxisLabels = [0,25,50,75,100,110];
				var chart = Highcharts.chart('container1', {

					chart: {
						type: 'column',
						backgroundColor: null
					},

					title: {
						text: '<span style="color: #00a65a;">Average Actual Working Time</span>',
						style: {
							fontSize: '24px',
							fontWeight: 'bold'
						}
					},
					legend:{
						enabled: true,
						itemStyle: {
							fontSize:'16px',
						}
					},
					credits:{
						enabled:false
					},
					xAxis: {
						categories: categories1,
						type: 'category',
						labels: {
							style: {
								fontSize: '16px'
							}
						},
						// min: 0,
						// max:21,
						scrollbar: {
							enabled: false
						}
					},
					yAxis: {
						title: {
							enabled:false,
						},
						tickPositioner: function() {
							return yAxisLabels;
						},
						labels: {
							enabled:false
						},
						plotLines: [{
							color: '#00a65a',
							value: 101,
							dashStyle: 'shortdash',
							width: 2,
							zIndex: 5,
							label: {
								align:'right',
								text: 'Target '+target+'min',
								x:-7,
								style: {
									fontSize: '16px',
									color: '#00a65a',
									fontWeight: 'bold'
								}
							}
						}],
					},
					tooltip: {
						formatter: function () {
							return '<b>' + this.x + '</b><br/>' +
							this.series.name + ': ' + this.y.toFixed(0) + '<br/>' +
							'Total: ' + this.point.stackTotal;
						}
					},
					plotOptions: {
						column: {
							stacking: 'percent',
						},
						series:{
							animation: false,
							// minPointLength: 2,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								formatter: function() {
									return this.y.toFixed(0);
								},
								y:-5,
								style: {
									fontSize:'18px',
									fontWeight: 'bold',
								}
							},
							point: {
								events: {
									click: function () {
										fillModal(this.category, this.series.userOptions.stack);
									}
								}
							}
						}
					},
					series: [{
						name: 'Loss Working Time',
						data: loss1,
						color: 'rgba(255, 0, 0, 0.25)'
					}, {
						name: 'Actual Working Time',
						data: time1,
						color: '#00a65a'
					}]
				});

				var chart = Highcharts.chart('container2', {
					chart: {
						type: 'column',
						backgroundColor: null
					},

					title: {
						text: '<span style="color: #ff851b;">Average Standard Working Time</span>',
						style: {
							fontSize: '24px',
							fontWeight: 'bold'
						}
					},
					legend:{
						enabled: true,
						itemStyle: {
							fontSize:'16px',
						}
					},
					credits:{
						enabled:false
					},
					xAxis: {
						categories: categories1,
						type: 'category',
						labels: {
							style: {
								fontSize: '16px'
							}
						},
						// min: 0,
						// max:3,
						scrollbar: {
							enabled: false
						}
					},
					yAxis: {
						title: {
							enabled:false,
						},
						tickPositioner: function() {
							return yAxisLabels;
						},
						labels: {
							enabled:false
						},
						plotLines: [{
							color: '#ff851b',
							value: 101,
							dashStyle: 'shortdash',
							width: 2,
							zIndex: 5,
							label: {
								align:'right',
								text: 'Target '+target+'min',
								x:-7,
								style: {
									fontSize: '16px',
									color: '#ff851b',
									fontWeight: 'bold'
								}
							}
						}],
					},
					tooltip: {
						formatter: function () {
							return '<b>' + this.x + '</b><br/>' +
							this.series.name + ': ' + this.y.toFixed(0) + '<br/>' +
							'Total: ' + this.point.stackTotal;
						}
					},
					plotOptions: {
						column: {
							stacking: 'percent',
						},
						series:{
							animation: false,
							// minPointLength: 2,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								formatter: function() {
									return this.y.toFixed(0);
								},
								y:-5,
								style: {
									fontSize:'18px',
									fontWeight: 'bold',
								}
							},
							point: {
								events: {
									click: function () {
										fillModal(this.category, this.series.userOptions.stack);
									}
								}
							}
						}
					},
					series: [{
						name: 'Loss Working Time',
						data: loss2,
						color: 'rgba(255, 0, 0, 0.25)'
					}, {
						name: 'Actual Working Time',
						data: time2,
						color: '#ff851b'
					}]
				});
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

function changeLocation(){
	$("#location").val($("#locationSelect").val());
}


</script>
@endsection