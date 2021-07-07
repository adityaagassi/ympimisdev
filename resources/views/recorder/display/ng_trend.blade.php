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
	#loading, #error { display: none; }
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>	
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<!-- <form method="GET" action="{{ action('RecorderProcessController@indexNgRateKensa') }}"> -->
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
						</div>
					</div>
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To">
						</div>
					</div>

					<div class="col-xs-2">
						<button class="btn btn-success" onclick="fetchChart()"><i class="fa fa-search"></i> Search</button>
					</div>
					<!-- <div class="pull-right" id="loc" style="margin: 0px;padding-top: 0px;padding-right: 20px;font-size: 2vw;"></div> -->
				<!-- </form> -->
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row">
				<!-- <div class="col-xs-2" style="padding-right: 0;">
					<div class="small-box" style="background: #52c9ed; height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>CHECK <span class="text-purple">検査数</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="total">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-search"></i>
						</div>
					</div>
					<div class="small-box" style="background: #00a65a; height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>OK <span class="text-purple">良品数</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="ok">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-check"></i>
						</div>
					</div>
					<div class="small-box" style="background: #ff851b; height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>NG <span class="text-purple">不良品数</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="ng">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-remove"></i>
						</div>
					</div>
					<div class="small-box" style="background: rgb(220,220,220); height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>% <span class="text-purple">不良率</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="pctg">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-line-chart"></i>
						</div>
					</div>
				</div> -->
				<div class="col-xs-12">
					<div id="container" class="container" style="width: 100%;"></div>
					<!-- <table class="table table-hover table-bordered" id="tableTrend" style="padding-top: 10px">
						<thead style="background-color: rgba(126,86,134,.7);color: white">
							<tr>
								<th style="width: 1%;">Date</th>
								<th style="width: 1%;">Product</th>
								<th style="width: 1%;">Part</th>
								<th style="width: 1%;">Color</th>
								<th style="width: 1%;">Cav</th>
								<th style="width: 1%;">OP Molding</th>
								<th style="width: 1%;">Molding</th>
								<th style="width: 1%;">OP Injeksi</th>
								<th style="width: 1%;">Mesin</th>
								<th style="width: 1%;">OP Resin</th>
								<th style="width: 1%;">Resin</th>
								<th style="width: 1%;">Dryer</th>
								<th style="width: 1%;">OP Kensa</th>
								<th style="width: 1%;">NG Kensa</th>
							</tr>
						</thead>
						<tbody id="tableTrendBody">
						</tbody>
					</table> -->
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg" style="width: 1200px">
		<div class="modal-content">
			<div class="modal-header">
				<div style="background-color: #fcba03;text-align: center;">
					<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitle"></h4>
				</div>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<table class="table table-hover table-bordered" id="tableTrend" style="padding-top: 10px">
						<thead style="background-color: rgba(126,86,134,.7);color: white">
							<tr>
								<th style="width: 1%;">Date</th>
								<th style="width: 1%;">Product</th>
								<th style="width: 1%;">Part</th>
								<th style="width: 1%;">Color</th>
								<th style="width: 1%;">Cav</th>
								<th style="width: 1%;">OP Molding</th>
								<th style="width: 1%;">Molding</th>
								<th style="width: 1%;">OP Injeksi</th>
								<th style="width: 1%;">Mesin</th>
								<th style="width: 1%;">OP Resin</th>
								<th style="width: 1%;">Resin</th>
								<th style="width: 1%;">Dryer</th>
								<th style="width: 1%;">OP Kensa</th>
								<th style="width: 1%;">NG Kensa</th>
							</tr>
						</thead>
						<tbody id="tableTrendBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
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

	function topFunction() {
	  document.body.scrollTop = 0;
	  document.documentElement.scrollTop = 0;
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

	var detail_all = [];
	var date_all = [];

	function fetchChart(){
		var date_from = $('#date_from').val();
		var date_to = $('#date_to').val();

		var data = {
			date_from:date_from,
			date_to:date_to,
		}

		$.get('{{ url("fetch/recorder/display/ng_trend") }}', data, function(result, status, xhr) {
			if(result.status){
				//BLOCK
				var nghead = [];
				var ngmiddle = [];
				var ngfoot = [];
				var ngblock = [];
				var ngheadyrf = [];
				var ngbodyyrf = [];
				var ngstopperyrf = [];
				var category = [];
				var totals = [];
				var totalbyday = [];
				date_all = [];
				for (var i = 0; i < result.week_date.length; i++) {
					date_all.push(result.week_date[i].week_date);
					category.push(result.week_date[i].week_date);

					var ng_head = 0;
					var ng_middle = 0;
					var ng_foot = 0;
					var ng_block = 0;
					var ng_headyrf = 0;
					var ng_bodyyrf = 0;
					var ng_stopperyrf = 0;
					var total = 0;

					for(var j = 0; j< result.resumes.length;j++){
						if (result.week_date[i].week_date == result.resumes[j][0].week_date) {
							for(var k = 0; k< result.resumes[j].length;k++){
								if (result.resumes[j][k].part_code.match(/HJ/gi)) {
									if (result.resumes[j][k].ng_name != null) {
										var ngs = result.resumes[j][k].ng_name.split(',');
										var counts = result.resumes[j][k].ng_count.split(',');
										for (var l = 0; l < ngs.length; l++) {
											ng_head = ng_head + parseInt(counts[l]);
											total = total + parseInt(counts[l]);
										}
									}
								}
							}
							for(var k = 0; k< result.resumes[j].length;k++){
								if (result.resumes[j][k].part_code.match(/MJ/gi)) {
									if (result.resumes[j][k].ng_name != null) {
										var ngs = result.resumes[j][k].ng_name.split(',');
										var counts = result.resumes[j][k].ng_count.split(',');
										for (var l = 0; l < ngs.length; l++) {
											ng_middle = ng_middle + parseInt(counts[l]);
											total = total + parseInt(counts[l]);
										}
									}
								}
							}
							for(var k = 0; k< result.resumes[j].length;k++){
								if (result.resumes[j][k].part_code.match(/BJ/gi)) {
									if (result.resumes[j][k].ng_name != null) {
										var ngs = result.resumes[j][k].ng_name.split(',');
										var counts = result.resumes[j][k].ng_count.split(',');
										for (var l = 0; l < ngs.length; l++) {
											ng_block = ng_block + parseInt(counts[l]);
											total = total + parseInt(counts[l]);
										}
									}
								}
							}

							for(var k = 0; k< result.resumes[j].length;k++){
								if (result.resumes[j][k].part_code.match(/FJ/gi)) {
									if (result.resumes[j][k].ng_name != null) {
										var ngs = result.resumes[j][k].ng_name.split(',');
										var counts = result.resumes[j][k].ng_count.split(',');
										for (var l = 0; l < ngs.length; l++) {
											ng_foot = ng_foot + parseInt(counts[l]);
											total = total + parseInt(counts[l]);
										}
									}
								}
							}

							for(var k = 0; k< result.resumes[j].length;k++){
								if (result.resumes[j][k].part_code.match(/A YRF H/gi)) {
									if (result.resumes[j][k].ng_name != null) {
										var ngs = result.resumes[j][k].ng_name.split(',');
										var counts = result.resumes[j][k].ng_count.split(',');
										for (var l = 0; l < ngs.length; l++) {
											ng_headyrf = ng_headyrf + parseInt(counts[l]);
											total = total + parseInt(counts[l]);
										}
									}
								}
							}

							for(var k = 0; k< result.resumes[j].length;k++){
								if (result.resumes[j][k].part_code.match(/A YRF B/gi)) {
									if (result.resumes[j][k].ng_name != null) {
										var ngs = result.resumes[j][k].ng_name.split(',');
										var counts = result.resumes[j][k].ng_count.split(',');
										for (var l = 0; l < ngs.length; l++) {
											ng_bodyyrf = ng_bodyyrf + parseInt(counts[l]);
											total = total + parseInt(counts[l]);
										}
									}
								}
							}

							for(var k = 0; k< result.resumes[j].length;k++){
								if (result.resumes[j][k].part_code.match(/A YRF S/gi)) {
									if (result.resumes[j][k].ng_name != null) {
										var ngs = result.resumes[j][k].ng_name.split(',');
										var counts = result.resumes[j][k].ng_count.split(',');
										for (var l = 0; l < ngs.length; l++) {
											ng_stopperyrf = ng_stopperyrf + parseInt(counts[l]);
											total = total + parseInt(counts[l]);
										}
									}
								}
							}
						}
					}
					nghead.push(ng_head);
					ngmiddle.push(ng_middle);
					ngfoot.push(ng_foot);
					ngblock.push(ng_block);
					ngheadyrf.push(ng_headyrf);
					ngbodyyrf.push(ng_bodyyrf);
					ngstopperyrf.push(ng_stopperyrf);
					totals.push(total);

					var arrday = [];
					arrday.push({y:ng_head,ng:'head'});
					arrday.push({y:ng_middle,ng:'middle'});
					arrday.push({y:ng_foot,ng:'foot'});
					arrday.push({y:ng_block,ng:'block'});
					arrday.push({y:ng_headyrf,ng:'headyrf'});
					arrday.push({y:ng_bodyyrf,ng:'bodyyrf'});
					arrday.push({y:ng_stopperyrf,ng:'stopperyrf'});

					arrday.sort(dynamicSort('y'));

					var high = "";

					for (var m = 0; m < arrday.length;m++) {
						high = arrday[m].ng;
					}

					totalbyday.push(high);
				}

				var datas = [];

				Highcharts.chart('container', {
					chart: {
						type: 'column',
						height: '500',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: "TREND NG RECORDER",
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: result.dateTitleFirst+' - '+result.dateTitleLast,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories:category,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '20px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Qty NG Pc(s)',
							style: {
								color: '#eee',
								fontSize: '18px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"14px"
							}
						},
						type: 'linear',
						
					}
					],
					tooltip: {
						headerFormat: '<span>NG Name</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						enabled:true
					},	
					credits: {
						enabled: false
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function (e) {
										showHighlight(this.series.name,this.category);
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}',
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
						type: 'column',
						data: nghead,
						name: "NG Head",
						colorByPoint: false,
						color: "#788cff",
						animation: false,
						stacking:true
					},
					{
						type: 'column',
						data: ngmiddle,
						name: "NG Middle",
						colorByPoint: false,
						color: "#cc6eff",
						animation: false,
						stacking:true
					},
					{
						type: 'column',
						data: ngfoot,
						name: "NG Foot",
						colorByPoint: false,
						color: "#93ff87",
						animation: false,
						stacking:true
					},
					{
						type: 'column',
						data: ngblock,
						name: "NG Block",
						colorByPoint: false,
						color: "#ffad4f",
						animation: false,
						stacking:true
					},
					{
						type: 'column',
						data: ngheadyrf,
						name: "NG Head YRF",
						colorByPoint: false,
						color: "#edff4f",
						animation: false,
						stacking:true
					},
					{
						type: 'column',
						data: ngbodyyrf,
						name: "NG Body YRF",
						colorByPoint: false,
						color: "#94eaff",
						animation: false,
						stacking:true
					},
					{
						type: 'column',
						data: ngstopperyrf,
						name: "NG Stopper YRF",
						colorByPoint: false,
						color: "#ff9494",
						animation: false,
						stacking:true
					},
					{
						type: 'spline',
						data: totals,
						name: "Total NG",
						colorByPoint: false,
						color: "#d62d2d",
						animation: false,
						marker: {
			                radius: 4,
			                lineColor: '#ff0000',
			                lineWidth: 2
			            }
					}
					]
				});

				var tableTrend = "";
				$('#tableTrendBody').html('');

				var index = 1;

				for (var i = 0; i < result.week_date.length; i++) {
					var color = "#ffffff";
					for(var j = 0; j < result.resume_trend.length;j++){
						if (result.resume_trend[j].length > 0) {
							if (result.week_date[i].week_date == result.resume_trend[j][0].week_date) {
								for(var k = 0; k < result.resume_trend[j].length;k++){
									if (totalbyday[i] == 'head') {
										if (result.resume_trend[j][k].part_type.match(/HJ/gi)) {

											var product = result.resume_trend[j][k].product.split('_');
											var ng_name_kensa = result.resume_trend[j][k].ng_name_kensa.split('_');
											var ng_count_kensa = result.resume_trend[j][k].ng_count_kensa.split('_');
											var operator_molding = result.resume_trend[j][k].operator_molding.split('_');
											var cavity = result.resume_trend[j][k].cavity.split('_');
											var product = result.resume_trend[j][k].product.split('_');
											var part_name = result.resume_trend[j][k].part_name.split('_');
											var part_type = result.resume_trend[j][k].part_type.split('_');
											var colors = result.resume_trend[j][k].color.split('_');
											var molding = result.resume_trend[j][k].molding.split('_');
											var mesin = result.resume_trend[j][k].mesin.split('_');
											var operator_injeksi = result.resume_trend[j][k].operator_injeksi.split('_');
											var injeksi_name = result.resume_trend[j][k].injeksi_name.split('_');
											var operator_resin = result.resume_trend[j][k].operator_resin.split('_');
											var resin_name = result.resume_trend[j][k].resin_name.split('_');
											var resin = result.resume_trend[j][k].resin.split('_');
											var dryer = result.resume_trend[j][k].dryer.split('_');
											var operator_kensa = result.resume_trend[j][k].operator_kensa.split('_');
											var kensa_name = result.resume_trend[j][k].kensa_name.split('_');

											for(var l = 0; l < product.length;l++){
												tableTrend += '<tr style="background-color:'+color+';" id="'+result.week_date[i].week_date+'">';
												tableTrend += '<td>'+result.week_date[i].week_date+'</td>';
												tableTrend += '<td>'+product[l]+'</td>';
												tableTrend += '<td>'+result.resume_trend[j][k].material_number+'<br>'+part_name[l]+' '+part_type[l]+'</td>';
												tableTrend += '<td>'+colors[l]+'</td>';
												tableTrend += '<td>'+cavity[l]+'</td>';
												tableTrend += '<td>'+operator_molding[l]+'</td>';
												tableTrend += '<td>'+molding[l]+'</td>';
												tableTrend += '<td>'+operator_injeksi[l]+'<br>'+injeksi_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												tableTrend += '<td>'+mesin[l]+'</td>';
												tableTrend += '<td>'+operator_resin[l]+'<br>'+resin_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												tableTrend += '<td>'+resin[l]+'</td>';
												tableTrend += '<td>'+dryer[l]+'</td>';
												tableTrend += '<td>'+operator_kensa[l]+'<br>'+kensa_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												var ng_name_kensas = ng_name_kensa[l].split(',');
												var ng_count_kensas = ng_count_kensa[l].split(',');
												tableTrend += '<td>';
												for(var m = 0; m < ng_name_kensas.length;m++){
													tableTrend += ng_name_kensas[m]+' = '+ng_count_kensas[m]+'<br>';
												}
												tableTrend += '</td>';
												tableTrend += '</tr>';
											}
										}
									}

									if (totalbyday[i] == 'middle') {
										if (result.resume_trend[j][k].part_type.match(/MJ/gi)) {
											var product = result.resume_trend[j][k].product.split('_');
											var ng_name_kensa = result.resume_trend[j][k].ng_name_kensa.split('_');
											var ng_count_kensa = result.resume_trend[j][k].ng_count_kensa.split('_');
											var operator_molding = result.resume_trend[j][k].operator_molding.split('_');
											var cavity = result.resume_trend[j][k].cavity.split('_');
											var product = result.resume_trend[j][k].product.split('_');
											var part_name = result.resume_trend[j][k].part_name.split('_');
											var part_type = result.resume_trend[j][k].part_type.split('_');
											var colors = result.resume_trend[j][k].color.split('_');
											var molding = result.resume_trend[j][k].molding.split('_');
											var mesin = result.resume_trend[j][k].mesin.split('_');
											var operator_injeksi = result.resume_trend[j][k].operator_injeksi.split('_');
											var injeksi_name = result.resume_trend[j][k].injeksi_name.split('_');
											var operator_resin = result.resume_trend[j][k].operator_resin.split('_');
											var resin_name = result.resume_trend[j][k].resin_name.split('_');
											var resin = result.resume_trend[j][k].resin.split('_');
											var dryer = result.resume_trend[j][k].dryer.split('_');
											var operator_kensa = result.resume_trend[j][k].operator_kensa.split('_');
											var kensa_name = result.resume_trend[j][k].kensa_name.split('_');

											for(var l = 0; l < product.length;l++){
												tableTrend += '<tr style="background-color:'+color+';" id="'+result.week_date[i].week_date+'">';
												tableTrend += '<td>'+result.week_date[i].week_date+'</td>';
												tableTrend += '<td>'+product[l]+'</td>';
												tableTrend += '<td>'+result.resume_trend[j][k].material_number+'<br>'+part_name[l]+' '+part_type[l]+'</td>';
												tableTrend += '<td>'+colors[l]+'</td>';
												tableTrend += '<td>'+cavity[l]+'</td>';
												tableTrend += '<td>'+operator_molding[l]+'</td>';
												tableTrend += '<td>'+molding[l]+'</td>';
												tableTrend += '<td>'+operator_injeksi[l]+'<br>'+injeksi_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												tableTrend += '<td>'+mesin[l]+'</td>';
												tableTrend += '<td>'+operator_resin[l]+'<br>'+resin_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												tableTrend += '<td>'+resin[l]+'</td>';
												tableTrend += '<td>'+dryer[l]+'</td>';
												tableTrend += '<td>'+operator_kensa[l]+'<br>'+kensa_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												var ng_name_kensas = ng_name_kensa[l].split(',');
												var ng_count_kensas = ng_count_kensa[l].split(',');
												tableTrend += '<td>';
												for(var m = 0; m < ng_name_kensas.length;m++){
													tableTrend += ng_name_kensas[m]+' = '+ng_count_kensas[m]+'<br>';
												}
												tableTrend += '</td>';
												tableTrend += '</tr>';
											}
										}
									}

									if (totalbyday[i] == 'foot') {
										if (result.resume_trend[j][k].part_type.match(/FJ/gi)) {
											var product = result.resume_trend[j][k].product.split('_');
											var ng_name_kensa = result.resume_trend[j][k].ng_name_kensa.split('_');
											var ng_count_kensa = result.resume_trend[j][k].ng_count_kensa.split('_');
											var operator_molding = result.resume_trend[j][k].operator_molding.split('_');
											var cavity = result.resume_trend[j][k].cavity.split('_');
											var product = result.resume_trend[j][k].product.split('_');
											var part_name = result.resume_trend[j][k].part_name.split('_');
											var part_type = result.resume_trend[j][k].part_type.split('_');
											var colors = result.resume_trend[j][k].color.split('_');
											var molding = result.resume_trend[j][k].molding.split('_');
											var mesin = result.resume_trend[j][k].mesin.split('_');
											var operator_injeksi = result.resume_trend[j][k].operator_injeksi.split('_');
											var injeksi_name = result.resume_trend[j][k].injeksi_name.split('_');
											var operator_resin = result.resume_trend[j][k].operator_resin.split('_');
											var resin_name = result.resume_trend[j][k].resin_name.split('_');
											var resin = result.resume_trend[j][k].resin.split('_');
											var dryer = result.resume_trend[j][k].dryer.split('_');
											var operator_kensa = result.resume_trend[j][k].operator_kensa.split('_');
											var kensa_name = result.resume_trend[j][k].kensa_name.split('_');

											for(var l = 0; l < product.length;l++){
												tableTrend += '<tr style="background-color:'+color+';" id="'+result.week_date[i].week_date+'">';
												tableTrend += '<td>'+result.week_date[i].week_date+'</td>';
												tableTrend += '<td>'+product[l]+'</td>';
												tableTrend += '<td>'+result.resume_trend[j][k].material_number+'<br>'+part_name[l]+' '+part_type[l]+'</td>';
												tableTrend += '<td>'+colors[l]+'</td>';
												tableTrend += '<td>'+cavity[l]+'</td>';
												tableTrend += '<td>'+operator_molding[l]+'</td>';
												tableTrend += '<td>'+molding[l]+'</td>';
												tableTrend += '<td>'+operator_injeksi[l]+'<br>'+injeksi_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												tableTrend += '<td>'+mesin[l]+'</td>';
												tableTrend += '<td>'+operator_resin[l]+'<br>'+resin_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												tableTrend += '<td>'+resin[l]+'</td>';
												tableTrend += '<td>'+dryer[l]+'</td>';
												tableTrend += '<td>'+operator_kensa[l]+'<br>'+kensa_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												var ng_name_kensas = ng_name_kensa[l].split(',');
												var ng_count_kensas = ng_count_kensa[l].split(',');
												tableTrend += '<td>';
												for(var m = 0; m < ng_name_kensas.length;m++){
													tableTrend += ng_name_kensas[m]+' = '+ng_count_kensas[m]+'<br>';
												}
												tableTrend += '</td>';
												tableTrend += '</tr>';
											}
										}
									}

									if (totalbyday[i] == 'block') {
										if (result.resume_trend[j][k].part_type.match(/BJ/gi)) {
											var product = result.resume_trend[j][k].product.split('_');
											var ng_name_kensa = result.resume_trend[j][k].ng_name_kensa.split('_');
											var ng_count_kensa = result.resume_trend[j][k].ng_count_kensa.split('_');
											var operator_molding = result.resume_trend[j][k].operator_molding.split('_');
											var cavity = result.resume_trend[j][k].cavity.split('_');
											var product = result.resume_trend[j][k].product.split('_');
											var part_name = result.resume_trend[j][k].part_name.split('_');
											var part_type = result.resume_trend[j][k].part_type.split('_');
											var colors = result.resume_trend[j][k].color.split('_');
											var molding = result.resume_trend[j][k].molding.split('_');
											var mesin = result.resume_trend[j][k].mesin.split('_');
											var operator_injeksi = result.resume_trend[j][k].operator_injeksi.split('_');
											var injeksi_name = result.resume_trend[j][k].injeksi_name.split('_');
											var operator_resin = result.resume_trend[j][k].operator_resin.split('_');
											var resin_name = result.resume_trend[j][k].resin_name.split('_');
											var resin = result.resume_trend[j][k].resin.split('_');
											var dryer = result.resume_trend[j][k].dryer.split('_');
											var operator_kensa = result.resume_trend[j][k].operator_kensa.split('_');
											var kensa_name = result.resume_trend[j][k].kensa_name.split('_');

											for(var l = 0; l < product.length;l++){
												tableTrend += '<tr style="background-color:'+color+';" id="'+result.week_date[i].week_date+'">';
												tableTrend += '<td>'+result.week_date[i].week_date+'</td>';
												tableTrend += '<td>'+product[l]+'</td>';
												tableTrend += '<td>'+result.resume_trend[j][k].material_number+'<br>'+part_name[l]+' '+part_type[l]+'</td>';
												tableTrend += '<td>'+colors[l]+'</td>';
												tableTrend += '<td>'+cavity[l]+'</td>';
												tableTrend += '<td>'+operator_molding[l]+'</td>';
												tableTrend += '<td>'+molding[l]+'</td>';
												tableTrend += '<td>'+operator_injeksi[l]+'<br>'+injeksi_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												tableTrend += '<td>'+mesin[l]+'</td>';
												tableTrend += '<td>'+operator_resin[l]+'<br>'+resin_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												tableTrend += '<td>'+resin[l]+'</td>';
												tableTrend += '<td>'+dryer[l]+'</td>';
												tableTrend += '<td>'+operator_kensa[l]+'<br>'+kensa_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												var ng_name_kensas = ng_name_kensa[l].split(',');
												var ng_count_kensas = ng_count_kensa[l].split(',');
												tableTrend += '<td>';
												for(var m = 0; m < ng_name_kensas.length;m++){
													tableTrend += ng_name_kensas[m]+' = '+ng_count_kensas[m]+'<br>';
												}
												tableTrend += '</td>';
												tableTrend += '</tr>';
											}
										}
									}

									if (totalbyday[i] == 'headyrf') {
										if (result.resume_trend[j][k].part_type.match(/A YRF H/gi)) {
											var product = result.resume_trend[j][k].product.split('_');
											var ng_name_kensa = result.resume_trend[j][k].ng_name_kensa.split('_');
											var ng_count_kensa = result.resume_trend[j][k].ng_count_kensa.split('_');
											var operator_molding = result.resume_trend[j][k].operator_molding.split('_');
											var cavity = result.resume_trend[j][k].cavity.split('_');
											var product = result.resume_trend[j][k].product.split('_');
											var part_name = result.resume_trend[j][k].part_name.split('_');
											var part_type = result.resume_trend[j][k].part_type.split('_');
											var colors = result.resume_trend[j][k].color.split('_');
											var molding = result.resume_trend[j][k].molding.split('_');
											var mesin = result.resume_trend[j][k].mesin.split('_');
											var operator_injeksi = result.resume_trend[j][k].operator_injeksi.split('_');
											var injeksi_name = result.resume_trend[j][k].injeksi_name.split('_');
											var operator_resin = result.resume_trend[j][k].operator_resin.split('_');
											var resin_name = result.resume_trend[j][k].resin_name.split('_');
											var resin = result.resume_trend[j][k].resin.split('_');
											var dryer = result.resume_trend[j][k].dryer.split('_');
											var operator_kensa = result.resume_trend[j][k].operator_kensa.split('_');
											var kensa_name = result.resume_trend[j][k].kensa_name.split('_');

											for(var l = 0; l < product.length;l++){
												tableTrend += '<tr style="background-color:'+color+';" id="'+result.week_date[i].week_date+'">';
												tableTrend += '<td>'+result.week_date[i].week_date+'</td>';
												tableTrend += '<td>'+product[l]+'</td>';
												tableTrend += '<td>'+result.resume_trend[j][k].material_number+'<br>'+part_name[l]+' '+part_type[l]+'</td>';
												tableTrend += '<td>'+colors[l]+'</td>';
												tableTrend += '<td>'+cavity[l]+'</td>';
												tableTrend += '<td>'+operator_molding[l]+'</td>';
												tableTrend += '<td>'+molding[l]+'</td>';
												tableTrend += '<td>'+operator_injeksi[l]+'<br>'+injeksi_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												tableTrend += '<td>'+mesin[l]+'</td>';
												tableTrend += '<td>'+operator_resin[l]+'<br>'+resin_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												tableTrend += '<td>'+resin[l]+'</td>';
												tableTrend += '<td>'+dryer[l]+'</td>';
												tableTrend += '<td>'+operator_kensa[l]+'<br>'+kensa_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												var ng_name_kensas = ng_name_kensa[l].split(',');
												var ng_count_kensas = ng_count_kensa[l].split(',');
												tableTrend += '<td>';
												for(var m = 0; m < ng_name_kensas.length;m++){
													tableTrend += ng_name_kensas[m]+' = '+ng_count_kensas[m]+'<br>';
												}
												tableTrend += '</td>';
												tableTrend += '</tr>';
											}
										}
									}

									if (totalbyday[i] == 'bodyyrf') {
										if (result.resume_trend[j][k].part_type.match(/A YRF B/gi)) {
											var product = result.resume_trend[j][k].product.split('_');
											var ng_name_kensa = result.resume_trend[j][k].ng_name_kensa.split('_');
											var ng_count_kensa = result.resume_trend[j][k].ng_count_kensa.split('_');
											var operator_molding = result.resume_trend[j][k].operator_molding.split('_');
											var cavity = result.resume_trend[j][k].cavity.split('_');
											var product = result.resume_trend[j][k].product.split('_');
											var part_name = result.resume_trend[j][k].part_name.split('_');
											var part_type = result.resume_trend[j][k].part_type.split('_');
											var colors = result.resume_trend[j][k].color.split('_');
											var molding = result.resume_trend[j][k].molding.split('_');
											var mesin = result.resume_trend[j][k].mesin.split('_');
											var operator_injeksi = result.resume_trend[j][k].operator_injeksi.split('_');
											var injeksi_name = result.resume_trend[j][k].injeksi_name.split('_');
											var operator_resin = result.resume_trend[j][k].operator_resin.split('_');
											var resin_name = result.resume_trend[j][k].resin_name.split('_');
											var resin = result.resume_trend[j][k].resin.split('_');
											var dryer = result.resume_trend[j][k].dryer.split('_');
											var operator_kensa = result.resume_trend[j][k].operator_kensa.split('_');
											var kensa_name = result.resume_trend[j][k].kensa_name.split('_');

											for(var l = 0; l < product.length;l++){
												tableTrend += '<tr style="background-color:'+color+';" id="'+result.week_date[i].week_date+'">';
												tableTrend += '<td>'+result.week_date[i].week_date+'</td>';
												tableTrend += '<td>'+product[l]+'</td>';
												tableTrend += '<td>'+result.resume_trend[j][k].material_number+'<br>'+part_name[l]+' '+part_type[l]+'</td>';
												tableTrend += '<td>'+colors[l]+'</td>';
												tableTrend += '<td>'+cavity[l]+'</td>';
												tableTrend += '<td>'+operator_molding[l]+'</td>';
												tableTrend += '<td>'+molding[l]+'</td>';
												tableTrend += '<td>'+operator_injeksi[l]+'<br>'+injeksi_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												tableTrend += '<td>'+mesin[l]+'</td>';
												tableTrend += '<td>'+operator_resin[l]+'<br>'+resin_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												tableTrend += '<td>'+resin[l]+'</td>';
												tableTrend += '<td>'+dryer[l]+'</td>';
												tableTrend += '<td>'+operator_kensa[l]+'<br>'+kensa_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												var ng_name_kensas = ng_name_kensa[l].split(',');
												var ng_count_kensas = ng_count_kensa[l].split(',');
												tableTrend += '<td>';
												for(var m = 0; m < ng_name_kensas.length;m++){
													tableTrend += ng_name_kensas[m]+' = '+ng_count_kensas[m]+'<br>';
												}
												tableTrend += '</td>';
												tableTrend += '</tr>';
											}
										}
									}

									if (totalbyday[i] == 'stopperyrf') {
										if (result.resume_trend[j][k].part_type.match(/A YRF S/gi)) {
											var product = result.resume_trend[j][k].product.split('_');
											var ng_name_kensa = result.resume_trend[j][k].ng_name_kensa.split('_');
											var ng_count_kensa = result.resume_trend[j][k].ng_count_kensa.split('_');
											var operator_molding = result.resume_trend[j][k].operator_molding.split('_');
											var cavity = result.resume_trend[j][k].cavity.split('_');
											var product = result.resume_trend[j][k].product.split('_');
											var part_name = result.resume_trend[j][k].part_name.split('_');
											var part_type = result.resume_trend[j][k].part_type.split('_');
											var colors = result.resume_trend[j][k].color.split('_');
											var molding = result.resume_trend[j][k].molding.split('_');
											var mesin = result.resume_trend[j][k].mesin.split('_');
											var operator_injeksi = result.resume_trend[j][k].operator_injeksi.split('_');
											var injeksi_name = result.resume_trend[j][k].injeksi_name.split('_');
											var operator_resin = result.resume_trend[j][k].operator_resin.split('_');
											var resin_name = result.resume_trend[j][k].resin_name.split('_');
											var resin = result.resume_trend[j][k].resin.split('_');
											var dryer = result.resume_trend[j][k].dryer.split('_');
											var operator_kensa = result.resume_trend[j][k].operator_kensa.split('_');
											var kensa_name = result.resume_trend[j][k].kensa_name.split('_');

											for(var l = 0; l < product.length;l++){
												tableTrend += '<tr style="background-color:'+color+';" id="'+result.week_date[i].week_date+'">';
												tableTrend += '<td>'+result.week_date[i].week_date+'</td>';
												tableTrend += '<td>'+product[l]+'</td>';
												tableTrend += '<td>'+result.resume_trend[j][k].material_number+'<br>'+part_name[l]+' '+part_type[l]+'</td>';
												tableTrend += '<td>'+colors[l]+'</td>';
												tableTrend += '<td>'+cavity[l]+'</td>';
												tableTrend += '<td>'+operator_molding[l]+'</td>';
												tableTrend += '<td>'+molding[l]+'</td>';
												tableTrend += '<td>'+operator_injeksi[l]+'<br>'+injeksi_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												tableTrend += '<td>'+mesin[l]+'</td>';
												tableTrend += '<td>'+operator_resin[l]+'<br>'+resin_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												tableTrend += '<td>'+resin[l]+'</td>';
												tableTrend += '<td>'+dryer[l]+'</td>';
												tableTrend += '<td>'+operator_kensa[l]+'<br>'+kensa_name[l].replace(/(.{14})..+/, "$1&hellip;")+'</td>';
												var ng_name_kensas = ng_name_kensa[l].split(',');
												var ng_count_kensas = ng_count_kensa[l].split(',');
												tableTrend += '<td>';
												for(var m = 0; m < ng_name_kensas.length;m++){
													tableTrend += ng_name_kensas[m]+' = '+ng_count_kensas[m]+'<br>';
												}
												tableTrend += '</td>';
												tableTrend += '</tr>';
											}
										}
									}
								}
							}
						}
					}
					index++;
				}

				$('#tableTrendBody').append(tableTrend);
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
}
function showHighlight(name,date) {
	for (var i = 0; i < date_all.length; i++) {
		if (document.getElementById(date_all[i]) != null) {
			var elms = document.querySelectorAll("[id='"+date_all[i]+"']");
			for(var j = 0; j < elms.length; j++){
				// elms[j].style.backgroundColor = '#fff';
				elms[j].style.display = 'none';
			}
		}
	}
	var elms = document.querySelectorAll("[id='"+date+"']");
	for(var i = 0; i < elms.length; i++){
		// elms[i].scrollIntoView({
		//   behavior: 'smooth'
		// });
		// elms[i].style.backgroundColor = '#9ccaff';
		elms[i].style.display = '';
	}

	$('#modalDetailTitle').html('DETAIL HIGHEST NG ON '+date);

	$('#modalDetail').modal('show');
  		
	// document.getElementById(date).scrollIntoView({
	//   behavior: 'smooth'
	// });
	// document.getElementById(date).style.backgroundColor = '#ffb3b3';
}

// function ShowModal(ng_name,part_code) {
// 	$('#tableDetailBody').html('');
// 	var bodyDetail = '';
// 	var modalDetailTitle = '';
// 	var total = 0;
// 	if (part_code === 'HJ') {
// 		var index = 1;
// 		for (var i = 0; i < detail_all_hj.length; i++) {
// 			if (ng_name === detail_all_hj[i].ng_name) {
// 				bodyDetail += '<tr>';
// 				bodyDetail += '<td>'+index+'</td>';
// 				bodyDetail += '<td>'+detail_all_hj[i].serial_number+'</td>';
// 				bodyDetail += '<td>'+detail_all_hj[i].product+'</td>';
// 				bodyDetail += '<td>'+detail_all_hj[i].material_number+'<br>'+detail_all_hj[i].part_name+'</td>';
// 				bodyDetail += '<td>'+detail_all_hj[i].cavity+'</td>';
// 				bodyDetail += '<td>'+detail_all_hj[i].operator_kensa+'<br>'+detail_all_hj[i].name+'</td>';
// 				bodyDetail += '<td>'+detail_all_hj[i].created_at+'</td>';
// 				bodyDetail += '<td>'+detail_all_hj[i].ng_name+'</td>';
// 				bodyDetail += '<td>'+detail_all_hj[i].ng_count+'</td>';
// 				bodyDetail += '</tr>';
// 				index++;
// 				total = total + parseInt(detail_all_hj[i].ng_count);
// 			}
// 		}
// 		modalDetailTitle = 'Head YRS / Head YRF NG Resume';
// 	}
// 	if (part_code === 'MJ') {
// 		var index = 1;
// 		for (var i = 0; i < detail_all_mj.length; i++) {
// 			if (ng_name === detail_all_mj[i].ng_name) {
// 				bodyDetail += '<tr>';
// 				bodyDetail += '<td>'+index+'</td>';
// 				bodyDetail += '<td>'+detail_all_mj[i].serial_number+'</td>';
// 				bodyDetail += '<td>'+detail_all_mj[i].product+'</td>';
// 				bodyDetail += '<td>'+detail_all_mj[i].material_number+'<br>'+detail_all_mj[i].part_name+'</td>';
// 				bodyDetail += '<td>'+detail_all_mj[i].cavity+'</td>';
// 				bodyDetail += '<td>'+detail_all_mj[i].operator_kensa+'<br>'+detail_all_mj[i].name+'</td>';
// 				bodyDetail += '<td>'+detail_all_mj[i].created_at+'</td>';
// 				bodyDetail += '<td>'+detail_all_mj[i].ng_name+'</td>';
// 				bodyDetail += '<td>'+detail_all_mj[i].ng_count+'</td>';
// 				bodyDetail += '</tr>';
// 				index++;
// 				total = total + parseInt(detail_all_mj[i].ng_count);
// 			}
// 		}
// 		modalDetailTitle = 'Middle / Body YRF NG Resume';
// 	}
// 	if (part_code === 'FJ') {
// 		var index = 1;
// 		for (var i = 0; i < detail_all_fj.length; i++) {
// 			if (ng_name === detail_all_fj[i].ng_name) {
// 				bodyDetail += '<tr>';
// 				bodyDetail += '<td>'+index+'</td>';
// 				bodyDetail += '<td>'+detail_all_fj[i].serial_number+'</td>';
// 				bodyDetail += '<td>'+detail_all_fj[i].product+'</td>';
// 				bodyDetail += '<td>'+detail_all_fj[i].material_number+'<br>'+detail_all_fj[i].part_name+'</td>';
// 				bodyDetail += '<td>'+detail_all_fj[i].cavity+'</td>';
// 				bodyDetail += '<td>'+detail_all_fj[i].operator_kensa+'<br>'+detail_all_fj[i].name+'</td>';
// 				bodyDetail += '<td>'+detail_all_fj[i].created_at+'</td>';
// 				bodyDetail += '<td>'+detail_all_fj[i].ng_name+'</td>';
// 				bodyDetail += '<td>'+detail_all_fj[i].ng_count+'</td>';				
// 				bodyDetail += '</tr>';
// 				index++;
// 				total = total + parseInt(detail_all_fj[i].ng_count);
// 			}
// 		}
// 		modalDetailTitle = 'Foot NG Resume';
// 	}
// 	if (part_code === 'BJ') {
// 		var index = 1;
// 		for (var i = 0; i < detail_all_bj.length; i++) {
// 			if (ng_name === detail_all_bj[i].ng_name) {
// 				bodyDetail += '<tr>';
// 				bodyDetail += '<td>'+index+'</td>';
// 				bodyDetail += '<td>'+detail_all_bj[i].serial_number+'</td>';
// 				bodyDetail += '<td>'+detail_all_bj[i].product+'</td>';
// 				bodyDetail += '<td>'+detail_all_bj[i].material_number+'<br>'+detail_all_bj[i].part_name+'</td>';
// 				bodyDetail += '<td>'+detail_all_bj[i].cavity+'</td>';
// 				bodyDetail += '<td>'+detail_all_bj[i].operator_kensa+'<br>'+detail_all_bj[i].name+'</td>';
// 				bodyDetail += '<td>'+detail_all_bj[i].created_at+'</td>';
// 				bodyDetail += '<td>'+detail_all_bj[i].ng_name+'</td>';
// 				bodyDetail += '<td>'+detail_all_bj[i].ng_count+'</td>';
// 				bodyDetail += '</tr>';
// 				index++;
// 				total = total + parseInt(detail_all_bj[i].ng_count);
// 			}
// 		}
// 		modalDetailTitle = 'Block / Stopper YRF NG Resume';
// 	}

// 	$('#total_all').html(total);

// 	$('#tableDetailBody').append(bodyDetail);
// 	$('#modalDetailTitle').html(modalDetailTitle);
// 	$('#modalDetail').modal('show');
// }

function dynamicSort(property) {
    var sortOrder = 1;
    if(property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1);
    }
    return function (a,b) {
        /* next line works with strings and numbers, 
         * and you may want to customize it to your needs
         */
        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
        return result * sortOrder;
    }
}

	function perbandingan(a,b){
		return a-b;
	}
	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
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


</script>
@endsection