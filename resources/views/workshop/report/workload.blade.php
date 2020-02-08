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
{{-- 		<div class="col-xs-3">
<div class="small-box" style="background: #52c9ed; height: 175px; margin-bottom: 5px;">
<div class="inner" style="padding-bottom: 0px;">
<h3 style="color: rgb(60, 60, 60); margin-bottom: 0px;font-size: 2vw;"><b>LISTED <span class="text-purple">検査数</span></b></h3>
<h5 style="color: rgb(60, 60, 60); font-size: 4vw; font-weight: bold;" id="total">0</h5>
</div>
<div class="icon" style="padding-top: 40px;">
<i class="fa fa-search"></i>
</div>
</div>
<div class="small-box" style="background: #ff851b; height: 175px; margin-bottom: 5px;">
<div class="inner" style="padding-bottom: 0px;">
<h3 style="color: rgb(60, 60, 60); margin-bottom: 0px;font-size: 2vw;"><b>APPROVED <span class="text-purple">不良品数</span></b></h3>
<h5 style="color: rgb(60, 60, 60); font-size: 4vw; font-weight: bold;" id="ng">0</h5>
</div>
<div class="icon" style="padding-top: 40px;">
<i class="fa fa-hand-o-right"></i>
</div>
</div>
<div class="small-box" style="background: #00a65a; height: 175px; margin-bottom: 5px;">
<div class="inner" style="padding-bottom: 0px;">
<h3 style="color: rgb(60, 60, 60); margin-bottom: 0px;font-size: 2vw;"><b>IN PROGRESS <span class="text-purple">良品数</span></b></h3>
<h5 style="color: rgb(60, 60, 60); font-size: 4vw; font-weight: bold;" id="ok">0</h5>
</div>
<div class="icon" style="padding-top: 40px;">
<i class="fa fa-tasks"></i>
</div>
</div>
<div class="small-box" style="background: rgb(220,220,220); height: 175px; margin-bottom: 5px;">
<div class="inner" style="padding-bottom: 0px;">
<h3 style="color: rgb(60, 60, 60); margin-bottom: 0px;font-size: 2vw;"><b>FINISHED TODAY <span class="text-purple">不良率</span></b></h3>
<h5 style="color: rgb(60, 60, 60); font-size: 4vw; font-weight: bold;" id="pctg">0</h5>
</div>
<div class="icon" style="padding-top: 40px;">
<i class="fa fa-check-square-o"></i>
</div>
</div>
</div> --}}
<div class="col-xs-12">
	<div class="col-xs-6" style="padding: 0px; display: none;">
		<div id="mc-workload-shift-3" style="width:100%;"></div>
	</div>
	<div class="col-xs-6" style="padding: 0px; display: none;">
		<div id="mc-workload-shift-2" style="width:100%;"></div>
	</div>


	<div class="col-xs-12" style="padding: 0px;">
		<div id="container" style="width:100%; margin-top: 1%;"></div>
	</div>

	<div class="col-xs-12" style="padding: 0px;">
		<div id="op-workload" style="width:100%; margin-top: 1%;"></div>
	</div>
</div>
</div>

<div class="modal fade" id="modal-operator" style="color: black;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Operator Workload Details</b></h4>
				<h5 class="modal-title" style="text-align: center;" id="judul-operator"></h5>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<table id="operator" class="table table-striped table-bordered" style="width: 100%; margin-bottom: 2%;"> 
							<thead id="operator-head" style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Name</th>
									<th>Order No.</th>
									<th>Target Date</th>
									<th>Tag No.</th>
									<th>Item Name</th>
									<th>Workload<sup>*</sup></th>
								</tr>
							</thead>
							<tbody id="operator-body">
							</tbody>
						</table>
						<span class="pull-left" style="font-weight: bold; background-color: yellow; color: rgb(255,0,0);">&nbsp;(*) Workload in minute(s)&nbsp;</span>

					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
			</div>
		</div>
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


		$.get('{{ url("fetch/workshop/workload") }}', function(result, status, xhr){
			if(result.status){

				var today = new Date();
				var day = 1000 * 60 * 60 * 24;
				var map = Highcharts.map;
				var dateFormat = Highcharts.dateFormat;
				var series = [];
				var machines = [];

				today.setUTCHours(0);
				today.setUTCMinutes(0);
				today.setUTCSeconds(0);
				today.setUTCMilliseconds(0);
				today = today.getTime();


				for (var i = 0; i < result.machine.length; i++) {

					var deal = [];
					var unfilled = true;
					for (var j = 0; j < result.mc_workload.length; j++) {
						if(result.machine[i].machine_code == result.mc_workload[j].machine_code){
							unfilled = false;
							deal.push({
								wjo : result.mc_workload[j].order_no,
								from : Date.parse(result.mc_workload[j].start_plan),
								to : Date.parse(result.mc_workload[j].finish_plan)
							});
						}
					}
					if(unfilled){
						deal.push({
							wjo : 0,
							from : 0,
							to : 0
						});
					}

					machines.push({
						name: result.machine[i].shortname,
						current: 0,
						deals: deal
					});
				}

				series = machines.map(function(machine, i) {
					var data = machine.deals.map(function(deal) {
						return {
							id: 'deal-' + i,
							wjo: deal.wjo,
							start: deal.from,
							end: deal.to,
							y: i
						};
					});
					return {
						name: machine.name,
						data: data,
						current: machine.deals[machine.current]
					};
				});

				console.log(series);


				Highcharts.ganttChart('container', {
					series: series,
					title: {
						text: 'Workshop Machine Workload',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					tooltip: {
						pointFormat: '<span>Order No.: {point.wjo}</span><br/><span>From: {point.start:%e %b %Y, %H:%M}</span><br/><span>To: {point.end:%e %b %Y, %H:%M}</span>'
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
									text: 'Machine'
								},
								categories: map(series, function(s) {
									return s.name;
								})
							}]
						}
					},
					credits: {
						enabled: false
					}
				});


				var machine = [];
				var data = [];
				for (var j = 0; j < result.machine.length; j++) {
					if(result.machine[j].shift == 3){
						machine.push(result.machine[j].shortname);

						var fill = true;
						for (var k = 0; k < result.mc_workload.length; k++) {
							if(result.machine[j].machine_name == result.mc_workload[k].machine_name){
								data.push(parseInt(result.mc_workload[k].workload));
								fill = false;
							}
						}
						if(fill){
							data.push(0);
						}	
					}
				}

				var sumPlotLines = Math.ceil(Math.max(...data) / 1280);
				var plotLines = [];
				for (var i = 1; i <= sumPlotLines; i++){
					plotLines.push({
						color: '#FFB300',
						value: (i * 1280),
						dashStyle: 'shortdash',
						width: 2,
						zIndex: 5,
						label: {
							rotation: 0,
							align:'right',
							text: i + ' day(s)',
							x:-7,
							style: {
								fontSize: '12px',
								color: '#FFB300',
								fontWeight: 'bold'
							}
						}
					});
				}


				$('#mc-workload-shift-3').highcharts({
					chart: {
						type: 'bar'
					},
					title: {
						text: 'Workshop Machine Workload',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: '3 Shifts',
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						type: 'category',
						categories: machine,
						lineWidth:2,
						lineColor:'#9e9e9e',
						gridLineWidth: 1
					},
					yAxis: {
						opposite: true,
						gridLineWidth: 0,
						title: {
							text: 'Minute(s)'
						},
						plotLines : plotLines
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '15px'
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
										alert(this.category);
									}
								}
							},
						}
					},
					credits: {
						enabled: false
					},
					legend: {
						enabled: false
					},
					series: [{
						name: 'Machine Workload',
						data: data,
						color: 'rgb(186,104,200)'
					}]
				});


				var machine = [];
				var data = [];
				for (var j = 0; j < result.machine.length; j++) {
					if(result.machine[j].shift == 2){
						machine.push(result.machine[j].shortname);

						var fill = true;
						for (var k = 0; k < result.mc_workload.length; k++) {
							if(result.machine[j].machine_name == result.mc_workload[k].machine_name){
								data.push(parseInt(result.mc_workload[k].workload));
								fill = false;
							}
						}
						if(fill){
							data.push(0);
						}	
					}
				}

				var sumPlotLines = Math.ceil(Math.max(...data) / 880);
				var plotLines = [];
				for (var i = 1; i <= sumPlotLines; i++){
					plotLines.push({
						color: '#FFB300',
						value: (i * 880),
						dashStyle: 'shortdash',
						width: 2,
						zIndex: 5,
						label: {
							rotation: 0,
							align:'right',
							text: i + ' day(s)',
							x:-7,
							style: {
								fontSize: '12px',
								color: '#FFB300',
								fontWeight: 'bold'
							}
						}
					});
				}


				$('#mc-workload-shift-2').highcharts({
					chart: {
						type: 'bar'
					},
					title: {
						text: 'Workshop Machine Workload',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: '2 Shifts',
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						},
					},
					xAxis: {
						type: 'category',
						categories: machine,
						lineWidth:2,
						lineColor:'#9e9e9e',
						gridLineWidth: 1
					},
					yAxis: {
						opposite: true,
						gridLineWidth: 0,
						title: {
							text: 'Minute(s)'
						},
						plotLines : plotLines
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '15px'
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
										alert(this.category);
									}
								}
							},
						}
					},
					credits: {
						enabled: false
					},
					legend: {
						enabled: false
					},
					series: [{
						name: 'Machine Workload',
						data: data,
						color: 'rgb(186,104,200)'
					}]
				});



				var op = [];
				var data = [];
				var series = [];

				for (var j = 0; j < result.op.length; j++) {
					op.push(result.op[j].name);

					var fill = true;
					for (var k = 0; k < result.op_workload.length; k++) {
						if(result.op[j].operator_id == result.op_workload[k].operator){
							data.push(parseInt(result.op_workload[k].workload));
							fill = false;
						}
					}
					if(fill){
						data.push(0);
					}	
				}

				var sumPlotLines = Math.ceil(Math.max(...data) / 400);
				var plotLines = [];


				for (var i = 1; i <= sumPlotLines; i++){
					plotLines.push({
						color: '#FFB300',
						value: (i * 400),
						dashStyle: 'shortdash',
						width: 2,
						zIndex: 5,
						label: {
							align:'right',
							text: i + ' day(s)',
							x:-7,
							style: {
								fontSize: '12px',
								color: '#FFB300',
								fontWeight: 'bold'
							}
						}
					});

				}


				$('#op-workload').highcharts({
					title: {
						text: 'Workshop Operator Workload',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						type: 'category',
						categories: op,
						lineWidth:2,
						lineColor:'#9e9e9e',
						gridLineWidth: 1
					},
					yAxis: {
						gridLineWidth: 0,
						title: {
							text: 'Minute(s)'
						},
						plotLines : plotLines
					},
					tooltip: {
						headerFormat: '<span>Operator Workload</span><br/>',
						pointFormat: '<span style="font-weight: bold;">{point.category} </span>: <b>{point.y} Minute(s)</b><br/>',
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '15px'
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
										showOperatorDetail(this.category);
									}
								}
							},
						}
					},
					credits: {
						enabled: false
					},
					legend: {
						enabled: false
					},
					series:  [{
						name: 'Operator Workload',
						data: data,
						type: 'column'
					}]
				});
				$(document).scrollTop(position);

			}

		});
}

function showOperatorDetail(name) {
	var data = {
		name : name
	}

	$.get('{{ url("fetch/workshop/workload_operator_detail") }}', data, function(result, status, xhr){
		if(result.status){
			$('#modal-operator').modal('show');
			$('#operator-body').append().empty();
			$('#judul-operator').append().empty();

			var body = '';
			for (var i = 0; i < result.detail.length; i++) {
				body += '<tr>';
				body += '<td>'+ result.detail[i].name +'</td>';
				body += '<td>'+ result.detail[i].order_no +'</td>';
				body += '<td>'+ result.detail[i].target_date +'</td>';
				body += '<td>'+ result.detail[i].tag_number +'</td>';
				body += '<td style="text-transform: capitalize;">'+ result.detail[i].item_name +'</td>';
				body += '<td>'+ result.detail[i].workload +'</td>';
				body += '</tr>';
			}

			$('#operator-body').append(body);
		}
	});
}


</script>
@endsection