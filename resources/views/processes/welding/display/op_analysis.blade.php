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
		<div class="col-xs-12" style="padding-bottom: 0px;">
			<div class="row">
				<!-- <form method="GET" action="{{ action('WeldingProcessController@indexOpAnalysis') }}"> -->
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
						</div>
					</div>
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To">
						</div>
					</div>
					<div class="col-xs-2">
						<button class="btn" style="background-color: #605ca8; color: white;" onclick="fetchChart()"><i class="fa fa-search"></i> Search</button>
					</div>
				<!-- </form> -->
					<div class="pull-right" id="period" style="margin: 0px;padding-top: 0px;padding-right: 20px;font-size: 1.5vw;color: white;font-weight: bold;"></div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-top: 0px">
			<div class="col-xs-9" style="padding-left: 0;">
				<div id="container1" class="container1" style="width: 100%;height: 400px"></div>
			</div>
			<div class="col-xs-3">
				<div class="box box-solid">
					<div class="box-header" style="background-color: #00a65a;">
						<center><span style="font-size: 22px; font-weight: bold; color: black;">Actual Time MP Resume</span></center>
					</div>
					<table class="table table-responsive" style="height: 330px">
						<tr>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px">Total MP All Shift</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right text-green" id="op_all">0</span></th>
						</tr>
						<tr>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px">Acc Time</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right text-green" id="wt">0</span></th>
						</tr>
						<tr>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px">Acc MP</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right text-green" id="acc_mp">0</span></th>
						</tr>
						<tr>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px">Avg Working Time / OP</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right text-green" id="avg_wt">0</span></th>
						</tr>
						<tr>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px">Target Working Time</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right text-green" id="twt">0</span></th>
						</tr>
						<tr>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px">Avg Loss Time</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right text-green" id="avg_lt">0</span></th>
						</tr>
						<tr>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px">Total Loss Time</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right text-green" id="tlt">0</span></th>
						</tr>
						<tr>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px">Loss MP Suggestion</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 2vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right text-red" id="lm">0</span></th>
						</tr>
					</table>
				</div>
			</div>
		</div>

		<div class="col-xs-12" style="padding-top: 0px">
			<div class="col-xs-9" style="padding-left: 0;">
				<div id="container2" class="container2" style="width: 100%;height: 400px"></div>
			</div>
			<div class="col-xs-3">
				<div class="box box-solid">
					<div class="box-header" style="background-color: #ff851b;">
						<center><span style="font-size: 22px; font-weight: bold; color: black;">Actual Time MP Resume</span></center>
					</div>
					<table class="table table-responsive" style="height: 330px">
						<tr>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px">Total MP All Shift</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right text-green" id="op_all_std">0</span></th>
						</tr>
						<tr>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px">Acc Time</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right text-green" id="wt_std">0</span></th>
						</tr>
						<tr>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px">Acc MP</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right text-green" id="acc_mp_std">0</span></th>
						</tr>
						<tr>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px">Avg Working Time / OP</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right text-green" id="avg_wt_std">0</span></th>
						</tr>
						<tr>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px">Target Working Time</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right text-green" id="twt_std">0</span></th>
						</tr>
						<tr>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px">Avg Loss Time</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right text-green" id="avg_lt_std">0</span></th>
						</tr>
						<tr>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px">Total Loss Time</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right text-green" id="tlt_std">0</span></th>
						</tr>
						<tr>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.2vw;padding-top: 0px;padding-bottom: 0px">Loss MP Suggestion</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 2vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right text-red" id="lm_std">0</span></th>
						</tr>
					</table>
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
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 3%;">ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 3%;">Perolehan (PCs)</th>
								<th style="width: 3%;">Act Time (Mins)</th>
								<th style="width: 3%;">Std Time (Mins)</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
						<tfoot id="tableDetailFoot">
							<tr>
								<th></th>
								<th></th>
								<th>Total</th>
								<th id="tot1"></th>
								<th id="tot2"></th>
								<th id="tot3"></th>
							</tr>
							<tr>
								<th></th>
								<th></th>
								<th>Average</th>
								<th id="avg1"></th>
								<th id="avg2"></th>
								<th id="avg3"></th>
							</tr>
						</tfoot>
					</table>
				</div>
				<center>
					<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
				</center>
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
		var now = '{{date("Y-m-d")}}';
		$('.datepicker').datepicker({
			autoclose: true,
			todayHighlight: true,
			format: "yyyy-mm-dd",
			endDate: now
		});
		$('.select2').select2();
		// fetchChart();
		// setInterval(fetchChart, 300000);
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

		var date_from = $('#date_from').val();
		var date_to = $('#date_to').val();

		var data = {
			date_from:date_from,
			date_to:date_to,
			// location:location,
			// group:group
		}

		$.get('{{ url("fetch/welding/op_analysis") }}', data, function(result, status, xhr) {
			if(result.status){

				var date = [];
				var date2 = [];

				var act_time = [];
				var std_time = [];
				var loss_time = [];
				var loss_time2 = [];
				var opall = [];

				var op = 0;
				var wt = 0;
				var lt = 0;
				var lm = 0;
				var twt = 0;

				var opall_std = [];

				var op_std = 0;
				var wt_std = 0;
				var lt_std = 0;
				var lm_std = 0;
				var twt_std = 0;
				// var avg_time = [];

				// var series = [];
				// var series2 = [];

				$.each(result.actual, function(key, value) {
					date2.push(value.tgl);
		            date.push(value.tgl);
		            opall.push(value.op);
		            act_time.push(parseFloat(value.act_time));
		            loss_time.push(value.loss_time);
		            var loss_time_std = 0;
		            if(value.loss_time_std > 0){
		            	loss_time_std = value.loss_time_std
		            }
		            loss_time2.push(loss_time_std);
		            op = op + value.op;
		            wt = wt + parseFloat(value.all_time);
		            lt = lt + value.loss_time;
		            twt = value.normal_time;
		            std_time.push(parseFloat(value.std_time));

		            opall_std.push(value.op);
		            op_std = op_std + value.op;
		            wt_std = wt_std + parseFloat(value.all_time_std);
		            lt_std = lt_std + value.loss_time_std;
		            twt_std = value.normal_time;
		        })
		        lm = lt / 480;

		        $('#period').html('Period: '+result.dateTitleFrom+' to '+result.dateTitleNow);
		        $('#op_all').html(Math.max.apply(Math, opall)+' Operator(s)');
		        $('#acc_mp').html(op+' Operator(s)');
		        $('#wt').html(wt+' Min(s)');
		        $('#avg_wt').html((wt/op).toFixed(2)+' Min(s)');
		        $('#twt').html(twt+' Min(s)');
		        $('#avg_lt').html((twt-(wt/op)).toFixed(2)+' Min(s)');
		        $('#tlt').html(((twt-(wt/op).toFixed(2))*Math.max.apply(Math, opall)).toFixed(2)+' Min(s)');
		        $('#lm').html(((twt-(wt/op).toFixed(2))*Math.max.apply(Math, opall)/480).toFixed(0)+' MP(s)');

		        $('#op_all_std').html(Math.max.apply(Math, opall_std)+' Operator(s)');
		        $('#acc_mp_std').html(op+' Operator(s)');
		        $('#wt_std').html((wt_std).toFixed(2)+' Min(s)');
		        $('#avg_wt_std').html((wt_std/op).toFixed(2)+' Min(s)');
		        $('#twt_std').html(twt_std+' Min(s)');
		        $('#avg_lt_std').html((twt_std-(wt_std/op)).toFixed(2)+' Min(s)');
		        $('#tlt_std').html(((twt_std-(wt_std/op).toFixed(2))*Math.max.apply(Math, opall_std)).toFixed(2)+' Min(s)');
		        $('#lm_std').html(((twt_std-(wt_std/op).toFixed(2))*Math.max.apply(Math, opall_std)/480).toFixed(0)+' MP(s)');


				Highcharts.chart('container1', {
					chart: {
						type: 'column',
						backgroundColor:null
					},
					title: {
						text: 'AVERAGE ACTUAL WORKING TIME',
						style: {
							fontSize: '24px',
							fontWeight: 'bold',
							color:'#00a65a'
						}
					},
					xAxis: {
						categories: date,
						type: 'category',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '15px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: {
						title: {
							enabled:false
						},
						labels: {
							enabled:false
						},
						plotLines: [{
							color: '#00a65a',
							value: 460,
							dashStyle: 'shortdash',
							width: 2,
							zIndex: 5,
							label: {
								align:'right',
								text: 'Target 460 Minutes',
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
					legend:{
						enabled: true,
						itemStyle: {
							fontSize:'16px',
						}
					},
					credits: {
						enabled: false
					},
					plotOptions: {
						column: {
							stacking: 'normal',
						},
						series:{
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '18px',
										fontWeight:'bold'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											fetchModal(this.category);
										}
									}
								}
							}
					},
					series: [{
						name: 'Loss Working Time',
						color:'rgba(255, 0, 0, 0.25)',
						stacking:'normal',
						data: loss_time
					},{
						name: 'Actual Working Time',
						color:'#00a65a',
						stacking:'normal',
						data: act_time
					}
					]
				});

				Highcharts.chart('container2', {
					chart: {
						type: 'column',
						backgroundColor:null
					},
					title: {
						text: 'AVERAGE ACTUAL STANDARD TIME',
						style: {
							fontSize: '24px',
							fontWeight: 'bold',
							color:'#ff851b'
						}
					},
					xAxis: {
						categories: date2,
						type: 'category',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '15px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: {
						title: {
							enabled:false
						},
						labels: {
							enabled:false
						},
						plotLines: [{
							color: '#ff851b',
							value: 460,
							dashStyle: 'shortdash',
							width: 2,
							zIndex: 5,
							label: {
								align:'right',
								text: 'Target 460 Minutes',
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
					legend:{
						enabled: true,
						itemStyle: {
							fontSize:'16px',
						}
					},
					credits: {
						enabled: false
					},
					plotOptions: {
						column: {
							stacking: 'normal',
						},
						series:{
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										fontSize: '18px',
										fontWeight:'bold'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											fetchModal(this.category);
										}
									}
								}
							},
					},
					series: [{
						name: 'Loss Working Time',
						color:'rgba(255, 0, 0, 0.25)',
						stacking:'normal',
						data: loss_time2
					},{
						name: 'Standard Working Time',
						color:'#ff851b',
						stacking:'normal',
						data: std_time
					}
					]
				});
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
}

function fetchModal(date){
	$('#modalDetailTitle').text(date);
	$('#modalDetail').modal('show');
	$('#loading').show();
	var data = {
		date:date
	}
	$.get('{{ url("fetch/welding/op_analysis_detail") }}', data, function(result, status, xhr) {
		if(result.status){
			var tableData = "";
			$('#tableDetailBody').html("");

			var count = 1;
			var tot_result = 0;
			var tot_act = 0;
			var tot_standard = 0;

			$.each(result.details, function(key, value){
				tableData += "<tr>";
				tableData += "<td>"+count+"</td>";
				tableData += "<td>"+value.operator_id+"</td>";
				tableData += "<td>"+value.name+"</td>";
				tableData += "<td>"+value.result+"</td>";
				tableData += "<td>"+Math.round(value.actual)+"</td>";
				tableData += "<td>"+Math.round(value.standard)+"</td>";
				tableData += "</tr>";
				count += 1;
				tot_result += Math.round(parseFloat(value.result));
				tot_act += Math.round(parseFloat(value.actual));
				tot_standard += Math.round(parseFloat(value.standard));
			});

			$('#tableDetailBody').append(tableData);
			$('#tot1').text(tot_result);
			$('#tot2').text(tot_act);
			$('#tot3').text(tot_standard);
			$('#avg1').text(Math.round(tot_result/count));
			$('#avg2').text(Math.round(tot_act/count));
			$('#avg3').text(Math.round(tot_standard/count));
			$('#loading').hide();

		}
		else{
			alert('Attempt to retrieve data failed.')
			$('#loading').hide();
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

// function changeLocation(){
// 	$("#location").val($("#locationSelect").val());
// }

// function changeGroup() {
// 	$("#group").val($("#groupSelect").val());
// }


</script>
@endsection