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
	.gambar {
	    width: 100%;
	    background-color: none;
	    border-radius: 5px;
	    margin-left: 15px;
	    margin-top: 15px;
	    display: inline-block;
	    border: 2px solid white;
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
				<form method="GET" action="{{ action('RecorderProcessController@indexNgRateKensa') }}">
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Select Date">
						</div>
					</div>

					<div class="col-xs-2">
						<button class="btn btn-success" type="submit"><i class="fa fa-search"></i> Search</button>
					</div>
					<!-- <div class="pull-right" id="loc" style="margin: 0px;padding-top: 0px;padding-right: 20px;font-size: 2vw;"></div> -->
				</form>
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
					<div class="row">
						<div class="col-xs-9">
							<div id="container1" class="container1" style="width: 100%;"></div>
						</div>
						<div class="col-xs-3" style="padding-left: 10px">
							<div class="gambar">
								<table style="text-align:center;width:100%">
									<tr>
										<td style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 25px;font-weight: bold;">BEST QUALITY EMPLOYEE<br>OF THE WEEK</td>
									</tr>
									<tr>
										<td id="lowest_avatar" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 24px;font-weight: bold;"></td>
									</tr>
									<tr>
										<td id="lowest_name" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 24px;font-weight: bold;"></td>
									</tr>
									<tr>
										<td id="lowest_ng" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 24px;font-weight: bold;"></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
				<hr style="border:3px solid white">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-9">
							<div id="container2" class="container2" style="width: 100%;"></div>
						</div>
						<div class="col-xs-3" style="padding-left: 10px">
							<div class="gambar">
								<table style="text-align:center;width:100%">
									<tr>
										<td style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 25px;font-weight: bold;">WORST QUALITY EMPLOYEE<br>OF THE WEEK</td>
									</tr>
									<tr>
										<td id="highest_avatar" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 24px;font-weight: bold;"></td>
									</tr>
									<tr>
										<td id="highest_name" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 24px;font-weight: bold;"></td>
									</tr>
									<tr>
										<td id="highest_ng" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 24px;font-weight: bold;"></td>
									</tr>
								</table>
							</div>
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
		$('#tanggal').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('.select2').select2();
		fetchChart();
		// setInterval(fetchChart, 20000);
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

	

	function fetchChart(){

		var tanggal = "{{$_GET['tanggal']}}";

		var data = {
			tanggal:tanggal,
		}

		$.get('{{ url("fetch/injection/ng_rate") }}', data, function(result, status, xhr) {
			if(result.status){

				//NG KENSA
				var operator = [];
				var ngname = [];
				var ngcount = [];
				var ngnamekensa = [];
				var ngcountkensa = [];
				var ngall = [];
				for (var i = 0; i < result.emp.length; i++) {
					operator.push(result.emp[i].name.replace(/(.{14})..+/, "$1&hellip;"));
					// var countsng = 0;
					var countsngkensa = 0;
					for (var j = 0; j < result.resumes.length; j++) {
						if (result.resumes[j].ng_name_kensa != null) {
							if (result.resumes[j].operator_injection == result.emp[i].employee_id) {
								// var counts = result.resumes[j].ng_count.split(',');
								var countskensa = result.resumes[j].ng_count_kensa.split(',');
								for (var k = 0; k < countskensa.length; k++) {
									// countsng = countsng + parseInt(counts[k]);
									countsngkensa = countsngkensa + parseInt(countskensa[k]);
								}
							}
						}
					}
					// ngcount.push(countsng);
					ngcountkensa.push(countsngkensa);
				}

				// var ngcounts = [];

				// for (var i = 0; i < ngnames.length; i++) {
				// 	ngcounts[i] = 0;
				// 	for (var j = 0; j < ngall.length; j++) {
				// 		var ngalls = ngall[j].split('_');
				// 		if (ngalls[0] == ngnames[i]) {
				// 			ngcounts[i] = ngcounts[i]+parseInt(ngalls[1]);
				// 		}
				// 	}
				// }
				// var datas = [];
				// for (var i = 0; i < ngnames.length; i++) {
				// 	datas.push([ngnames[i], ngcounts[i]]);
				// }

				Highcharts.chart('container1', {
					chart: {
						type: 'column',
						height: '330',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: "TOTAL NG FROM ASSY",
						style: {
							fontSize: '20px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'on '+result.dateTitle,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: operator,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '13px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Qty NG Pc(s)',
							style: {
								color: '#eee',
								fontSize: '15px',
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
						headerFormat: '<span>Total NG Assy</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						align: 'right',
						verticalAlign: 'top',
						x: -90,
						y: 20,
						floating: true,
						borderWidth: 1,
						backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
						shadow: true,
						itemStyle: {
							fontSize:'13px',
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
						zoneAxis: 'x',
						type: 'column',
						data: ngcountkensa,
						name: "Total NG",
						colorByPoint: false,
						color: "#f0ad4e",
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					}
					]
				});

				var ngname = [];
				var ngcount = [];
				var ngnamekensa = [];
				var ngcountkensa = [];
				for (var i = 0; i < result.emp.length; i++) {
					var countsngkensa = 0;
					var ngall = [];
					for (var j = 0; j < result.resumes.length; j++) {
						if (result.resumes[j].ng_name_kensa != null) {
							if (result.resumes[j].operator_injection == result.emp[i].employee_id) {
								ngall.push(result.resumes[j].ng_name+'_'+result.resumes[j].ng_count);
							}
						}
					}
					var ng_counts = ngall.filter(onlyUnique);
					for (var l = 0; l < ng_counts.length; l++) {
						var ngcountsss = ng_counts[l].split('_');
						var ngcountssss = ngcountsss[1].split(',');
						for (var k = 0; k < ngcountssss.length; k++) {
							countsngkensa = countsngkensa + parseInt(ngcountssss[k]);
						}
					}
					ngcountkensa.push(countsngkensa);
				}

				// //MIDDLE BODY
				// var ngname = [];
				// var ngcount = [];
				// var ngall = [];
				// for (var i = 0; i < result.resumes.length; i++) {
				// 	if (result.resumes[i].part_code.match(/MJ/gi) || result.resumes[i].part_code == 'A YRF B') {
				// 		var ngs = result.resumes[i].ng_name.split(',');
				// 		var counts = result.resumes[i].ng_count.split(',');
				// 		for (var j = 0; j < ngs.length; j++) {
				// 			ngname.push(ngs[j]);
				// 			ngcount.push(counts[j]);
				// 			ngall.push(ngs[j]+'_'+counts[j]);
				// 		}
				// 	}
				// }

				// function onlyUnique(value, index, self) {
				//   return self.indexOf(value) === index;
				// }

				// var ngnames = ngname.filter(onlyUnique);

				// var ngcounts = [];

				// for (var i = 0; i < ngnames.length; i++) {
				// 	ngcounts[i] = 0;
				// 	for (var j = 0; j < ngall.length; j++) {
				// 		var ngalls = ngall[j].split('_');
				// 		if (ngalls[0] == ngnames[i]) {
				// 			ngcounts[i] = ngcounts[i]+parseInt(ngalls[1]);
				// 		}
				// 	}
				// }
				// var datas = [];
				// for (var i = 0; i < ngnames.length; i++) {
				// 	datas.push([ngnames[i], ngcounts[i]]);
				// }

				Highcharts.chart('container2', {
					chart: {
						type: 'column',
						height: '330',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: "TOTAL NG FROM INJECTION",
						style: {
							fontSize: '20px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'on '+result.dateTitle,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: operator,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '13px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Qty NG Pc(s)',
							style: {
								color: '#eee',
								fontSize: '15px',
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
						headerFormat: '<span>Total NG Assy</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						align: 'right',
						verticalAlign: 'top',
						x: -90,
						y: 20,
						floating: true,
						borderWidth: 1,
						backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
						shadow: true,
						itemStyle: {
							fontSize:'13px',
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
						zoneAxis: 'x',
						type: 'column',
						data: ngcountkensa,
						name: "Total NG",
						colorByPoint: false,
						color: "#4287f5",
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					}
					]
				});

				var operator = [];
				var ngname = [];
				var ngcount = [];
				var ngnamekensa = [];
				var ngcountkensa = [];
				var ngall = [];
				for (var i = 0; i < result.emp.length; i++) {
					operator.push(result.emp[i].name.replace(/(.{14})..+/, "$1&hellip;"));
					// var countsng = 0;
					var countsngkensa = 0;
					for (var j = 0; j < result.resumeweek.length; j++) {
						if (result.resumeweek[j].ng_name_kensa != null) {
							if (result.resumeweek[j].operator_injection == result.emp[i].employee_id) {
								// var counts = result.resumes[j].ng_count.split(',');
								var countskensa = result.resumeweek[j].ng_count_kensa.split(',');
								for (var k = 0; k < countskensa.length; k++) {
									// countsng = countsng + parseInt(counts[k]);
									countsngkensa = countsngkensa + parseInt(countskensa[k]);
								}
							}
						}
					}
					// ngcount.push(countsng);
					ngcountkensa.push({y:parseInt(countsngkensa),key:result.emp[i].employee_id+'_'+result.emp[i].name});
				}
				ngcountkensa.sort(dynamicSort('y'));
				var highest_emp = "";
				var highest_name = "";
				var highest_ng = 0;
				for (var i = 0; i < ngcountkensa.length; i++) {
					var high = ngcountkensa[i].key.split('_');
					highest_emp = high[0];
					highest_name = high[1];
					highest_ng = ngcountkensa[i].y;
				}

				var low = ngcountkensa[0].key.split('_');
				var lowest_emp = low[0];
				var lowest_name = low[1];
				var lowest_ng = ngcountkensa[0].y;

				$('#highest_name').html(highest_emp+' - '+highest_name.split(' ').slice(0,1).join(' '));
				$('#highest_ng').html('Jumlah NG = '+highest_ng);

				$('#lowest_name').html(lowest_emp+' - '+lowest_name.split(' ').slice(0,1).join(' '));
				$('#lowest_ng').html('Jumlah NG = '+lowest_ng);

				var url_lowest = '{{ url("images/avatar/") }}/'+lowest_emp+'.jpg';
				var url_highest = '{{ url("images/avatar/") }}/'+highest_emp+'.JPG';

				$('#lowest_avatar').html('<img style="width:120px" src="'+url_lowest+'" class="user-image" alt="User image">');
				$('#highest_avatar').html('<img style="width:120px" src="'+url_highest+'" class="user-image" alt="User image">');

			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
}

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