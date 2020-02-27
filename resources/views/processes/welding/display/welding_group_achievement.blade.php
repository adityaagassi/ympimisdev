@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	thead>tr>th{
		text-align:center;
		vertical-align: middle;
		padding: 0px;
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
		vertical-align: middle;
	}
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
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<form method="GET" action="{{ action('WeldingProcessController@indexWeldingAchievement') }}">
					<div class="col-xs-2">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" name="tanggal" id="tanggal" placeholder="Select Date">
						</div>
					</div>
					<div class="col-xs-3">
						<div class="form-group" style="color: black;">
							<select class="form-control select3" multiple="multiple" id='wsSelect' onchange="changeWs()" data-placeholder="Select Work Station" style="width: 100%;">
								<option value=""></option>
								@foreach($workstations as $workstation) 
								<option value="{{ $workstation->ws_name }}">{{ $workstation->ws_name }}</option>
								@endforeach
							</select>
							<input type="text" name="ws" id="ws" hidden>
						</div>
					</div>
					<div class="col-xs-2">
						<button class="btn btn-success" type="submit">Update Chart</button>
					</div>
				</form>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>

			<div class="col-xs-12" style="margin-top: 5px;">
				<h2 style="text-transform: uppercase; font-weight: bold; text-align: center;">Daily qty of incoming instruction Vs Actual result qty</h2>
			</div>

			<div class="row">
				<div class="col-xs-12" style="margin-top: 5px; margin-left: 0px;">
					<div id="container" style="width: 100%;"></div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-4" style="margin-top: 5px; margin-left: 0px;">
					<div id="1" style="width: 100%;"></div>
				</div>
				<div class="col-xs-4" style="margin-top: 5px;">
					<div id="2" style="width: 100%;"></div>
				</div>
				<div class="col-xs-4" style="margin-top: 5px; margin-right: 0px;">
					<div id="3" style="width: 100%;"></div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-3" style="margin-top: 5px; margin-left: 0px;">
					<div id="4" style="width: 100%;"></div>
				</div>
				<div class="col-xs-3" style="margin-top: 5px;">
					<div id="5" style="width: 100%;"></div>
				</div>
				<div class="col-xs-3" style="margin-top: 5px; margin-right: 0px;">
					<div id="13" style="width: 100%;"></div>
				</div>
				<div class="col-xs-3" style="margin-top: 5px; margin-left: 0px;">
					<div id="14" style="width: 100%;"></div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-3" style="margin-top: 5px;">
					<div id="15" style="width: 100%;"></div>
				</div>
				<div class="col-xs-3" style="margin-top: 5px; margin-right: 0px;">
					<div id="16" style="width: 100%;"></div>
				</div>
				<div class="col-xs-3" style="margin-top: 5px; margin-right: 0px;">
					<div id="19" style="width: 100%;"></div>
				</div>
				<div class="col-xs-3" style="margin-top: 5px;">
					<div id="18" style="width: 100%;"></div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12" style="margin-top: 5px; margin-left: 0px;">
					<div id="17" style="width: 100%;"></div>
				</div>
			</div>

			<div class="col-xs-12" style="margin-top: 5px;">
				<div id="container2" style="width: 100%;"></div>
			</div>			
		</div>
	</div>

	<div class="modal fade" id="modal-detail" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Workstation Achievements</b></h4>
					<h5 class="modal-title" style="text-align: center;" id="judul-detail"></h5>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-6">
							<h3 style="margin-top: 0px;">Buffing Request</h3>
							<table id="request-detail" class="table table-bordered" style="width: 100%;"> 
								<thead id="request-detail-head" style="background-color: rgba(126,86,134,.8);">
									<tr>
										<th rowspan="2">WS Name</th>
										<th rowspan="2">Material Number</th>
										<th rowspan="2">Model</th>
										<th rowspan="2">Key</th>
										<th colspan="3">Quantity</th>
									</tr>
									<tr>
										<th>Kanban(s)</th>
										<th>PC(s)</th>
									</tr>
								</thead>
								<tbody id="request-detail-body">
								</tbody>
							</table>
						</div>
						<div class="col-xs-6">
							<h3 style="margin-top: 0px;">Welding Result</h3>
							<table id="result-detail" class="table table-bordered" style="width: 100%;"> 
								<thead id="result-detail-head" style="background-color: rgba(126,86,134,.8);">
									<tr>
										<th rowspan="2">WS Name</th>
										<th rowspan="2">Material Number</th>
										<th rowspan="2">Model</th>
										<th rowspan="2">Key</th>
										<th colspan="3">Quantity</th>
									</tr>
									<tr>
										<th>Kanban(s)</th>
										<th>PC(s)</th>
									</tr>
								</thead>
								<tbody id="result-detail-body">
								</tbody>
							</table>
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
		$('.select2').select2();
		fillChart();
		setInterval(fillChart, 60000);

	});

	function changeWs() {
		$("#ws").val($("#wsSelect").val());
	}

	$(function () {
		$('.select3').select2();
	})

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: "dd-mm-yyyy",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
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

	function fillChart() {
		var tanggal = "{{$_GET['tanggal']}}";
		var ws = "{{$_GET['ws']}}";

		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');
		var position = $(document).scrollTop();

		var data = {
			tanggal:tanggal,
		}
		var workstations = ws.split(",");


		$.get('{{ url("fetch/welding/group_achievement") }}', data, function(result, status, xhr) {
			if(result.status){

				console.log(workstations);
				console.log(workstations.length);
				
				if(workstations.toString() != ""){
					var key = [];
					var bff = [];
					var wld = [];
					for(var i = 0; i < result.data.length; i++){
						if(workstations.includes(result.data[i].ws_name)){
							key.push(result.data[i].model +" "+ result.data[i].key);
							bff.push(parseInt(result.data[i].bff));
							wld.push(parseInt(result.data[i].wld));
						}
					}
					var chart = Highcharts.chart('container', {
						title: {
							text: workstations.toString() + ' on '+ result.tanggal,
							style: {
								fontSize: '18px',
								fontWeight: 'bold'
							}
						},
						yAxis: {
							title: {
								text: 'Kanban(s)'
							},
							style: {
								fontSize: '26px',
								fontWeight: 'bold'
							},
							labels: {
								enabled: false
							}
						},
						xAxis: {
							categories: key,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								rotation: -45,
								style: {
									fontSize: '1vw'
								}
							},
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						credits: {
							enabled:false
						},
						legend : {
							align: 'center',
							verticalAlign: 'bottom',
							x: 0,
							y: 0,

							backgroundColor: (
								Highcharts.theme && Highcharts.theme.background2) || 'white',
							shadow: false
						},
						plotOptions: {
							series:{
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										textOutline: false,
										fontSize: '1vw'
									}
								},
								animation: false,
								cursor: 'pointer',
								point: {
									events: {
										click: function (event) {
											showDetail(result.tanggal, ws);
										}
									}
								},
							}
						},
						series: [{
							name:'Buffing Request',
							type: 'column',
							color: 'rgb(255,116,116)',
							data: bff,
						},{
							name:'Welding Result',
							type: 'column',
							color: 'rgb(169,255,151)',
							data: wld,
						}]
					});
				}else{
					for(var h = 0; h < result.ws.length; h++){
						var key = [];
						var bff = [];
						var wld = [];
						var ws = result.ws[h].ws_name;
						for(var i = 0; i < result.data.length; i++){
							if(result.data[i].ws_name == result.ws[h].ws_name){
								key.push(result.data[i].model +" "+ result.data[i].key);
								bff.push(parseInt(result.data[i].bff));
								wld.push(parseInt(result.data[i].wld));
							}
						}
						var chart = Highcharts.chart(''+ result.ws[h].ws_id +'', {
							title: {
								text: result.ws[h].ws_name + ' on '+result.tanggal,
								style: {
									fontSize: '18px',
									fontWeight: 'bold'
								}
							},
							yAxis: {
								title: {
									text: 'Kanban(s)'
								},
								style: {
									fontSize: '26px',
									fontWeight: 'bold'
								},
								labels: {
									enabled: false
								}
							},
							xAxis: {
								categories: key,
								type: 'category',
								gridLineWidth: 1,
								gridLineColor: 'RGB(204,255,255)',
								labels: {
									rotation: -45,
									style: {
										fontSize: '1vw'
									}
								},
							},
							tooltip: {
								headerFormat: '<span>{point.category}</span><br/>',
								pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
							},
							credits: {
								enabled:false
							},
							legend : {
								align: 'center',
								verticalAlign: 'bottom',
								x: 0,
								y: 0,

								backgroundColor: (
									Highcharts.theme && Highcharts.theme.background2) || 'white',
								shadow: false
							},
							plotOptions: {
								series:{
									dataLabels: {
										enabled: true,
										format: '{point.y}',
										style:{
											textOutline: false,
											fontSize: '1vw'
										}
									},
									animation: false,
									cursor: 'pointer',
									point: {
										events: {
											click: function (event) {
												showDetail(result.tanggal, ws);
											}
										}
									},
								}
							},
							series: [{
								name:'Buffing Request',
								type: 'column',
								color: 'rgb(255,116,116)',
								data: bff,
							},{
								name:'Welding Result',
								type: 'column',
								color: 'rgb(169,255,151)',
								data: wld,
							}]
						});
					}
				}

				$(document).scrollTop(position);
				
			}

		});


$.get('{{ url("fetch/welding/accumulated_achievement") }}', data, function(result, status, xhr) {
	if(result.status){

		var tgl= [];
		var bff = [];
		var wld = [];

		var week_name = '';
		var diff = 0;

		for (var i = 0; i < result.akumulasi.length; i++) {
			week_name = result.akumulasi[i].week_name;
			tgl.push(result.akumulasi[i].tgl);
			bff.push(parseInt(result.akumulasi[i].bff) + diff);
			wld.push(parseInt(result.akumulasi[i].wld));

			diff = bff[i] - wld[i];
		}

		var chart = Highcharts.chart('container2', {
			chart: {
				type: 'areaspline'
			},
			title: {
				text: 'Weekly Group Achievements Accumulation',
				style: {
					fontSize: '30px',
					fontWeight: 'bold'
				}
			},
			subtitle: {
				text: 'on WEEK '+week_name.substr(1),
				style: {
					fontSize: '18px',
					fontWeight: 'bold'
				}
			},
			yAxis: {
				title: {
					text: 'Kanban(s)'
				},
				style: {
					fontSize: '26px',
					fontWeight: 'bold'
				},
				gridLineWidth: 0,
				startOnTick: false,
				endOnTick: false
			},
			xAxis: {
				categories: tgl,
				gridLineWidth: 0,
				gridLineColor: 'RGB(204,255,255)',
				labels: {
					style: {
						fontSize: '26px'
					}
				}
			},
			tooltip: {
				headerFormat: '<span>{point.category}</span><br/>',
				pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
			},
			credits: {
				enabled:false
			},
			legend : {
				align: 'center',
				verticalAlign: 'bottom',
				x: 0,
				y: 0,

				backgroundColor: (
					Highcharts.theme && Highcharts.theme.background2) || 'white',
				shadow: false
			},
			plotOptions: {
				series:{
					dataLabels: {
						enabled: true,
						format: '{point.y}',
						style:{
							textOutline: false,
							fontSize: '26px'
						}
					},
					animation: false,
					cursor: 'pointer'
				},
				areaspline: {
					fillOpacity: 0.5
				}
			},
			series: [{
				name:'Incoming Instruction',
				color: 'rgb(255,116,116)',
				data: bff,
			},{
				name:'Actual Result',
				color: 'rgb(169,255,151)',
				data: wld,
			}]

		});				
		$(document).scrollTop(position);


	}
});

}

function showDetail(date, ws) {
	var data = {
		date : date,
		ws : ws
	}

	$.get('{{ url("fetch/welding/group_achievement_detail") }}', data, function(result, status, xhr){
		if(result.status){
			$('#modal-detail').modal('show');

			$('#judul-detail').append().empty();
			$('#judul-detail').append('<b>'+ ws +' on '+ date +'</b>');	

			$('#result-detail-body').html("");			
			var body = '';
			var sum_kanban = 0;
			var sum_pc = 0;
			for (var i = 0; i < result.wld.length; i++) {
				body += '<tr>';
				body += '<td>'+ result.wld[i].ws_name +'</td>';
				body += '<td>'+ result.wld[i].material_number +'</td>';
				body += '<td>'+ result.wld[i].model +'</td>';
				body += '<td>'+ result.wld[i].key +'</td>';
				body += '<td>'+ result.wld[i].kanban +'</td>';
				body += '<td>'+ result.wld[i].jml +'</td>';
				body += '</tr>';

				sum_kanban += parseInt(result.wld[i].kanban);
				sum_pc += parseInt(result.wld[i].jml);
			}
			body += '<tr>';
			body += '<td colspan="4">Total</td>';
			body += '<td>'+ sum_kanban +'</td>';
			body += '<td>'+ sum_pc +'</td>';
			body += '</tr>';
			$('#result-detail-body').append(body);


			$('#request-detail-body').html("");			
			var body = '';
			var sum_kanban = 0;
			var sum_pc = 0;
			for (var i = 0; i < result.bff.length; i++) {
				body += '<tr>';
				body += '<td>'+ result.bff[i].ws_name +'</td>';
				body += '<td>'+ result.bff[i].material_number +'</td>';
				body += '<td>'+ result.bff[i].model +'</td>';
				body += '<td>'+ result.bff[i].key +'</td>';
				body += '<td>'+ result.bff[i].kanban +'</td>';
				body += '<td>'+ result.bff[i].jml +'</td>';
				body += '</tr>';

				sum_kanban += parseInt(result.bff[i].kanban);
				sum_pc += parseInt(result.bff[i].jml);
			}
			body += '<tr>';
			body += '<td colspan="4">Total</td>';
			body += '<td>'+ sum_kanban +'</td>';
			body += '<td>'+ sum_pc +'</td>';
			body += '</tr>';
			$('#request-detail-body').append(body);

		}
	});
}



</script>
@endsection