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
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<div class="col-xs-2">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="bulan" placeholder="Select Month">
					</div>
				</div>
				<div class="col-xs-2">
					<button class="btn btn-success" onclick="fillChart()">Update Chart</button>
				</div>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>
			<div class="col-xs-12" style="margin-top: 5px;">
				<div id="container1" style="width: 100%; margin-top: 1%;"></div>
				<div id="container2" style="width: 100%; margin-top: 1%;"></div>
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
		// setInterval(fillChart, 10000);
		
	});

	function fillChart() {
		var bulan = $('#bulan').val();

		var data = {
			bulan: bulan
		}


		$.get('{{ url("fetch/middle/bff_op_ng_monthly") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					var name = [];
					var ng_rate = [];
					var data = [];

					for (var i = 0; i < result.op_ng.length; i++) {
						var name_temp = result.op_ng[i].name.split(" ");
						var xAxis = '';
						xAxis += result.op_ng[i].operator_id + ' - ';

						if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Moch.' || name_temp[0] == 'Mochamad'){
							xAxis += name_temp[0].charAt(0)+'. '+name_temp[1];
						}else{
							xAxis += name_temp[0]+'. '+name_temp[1].charAt(0);
						}

						name.push(xAxis);
						ng_rate.push(result.op_ng[i].ng_rate * 100);

						if(ng_rate[i] < 15){
							data.push({y: ng_rate[i], color: 'rgb(144,238,126)'});
						}else{
							data.push({y: ng_rate[i], color: 'rgb(255,116,116)'})
						}
					}

					var body = "";
					for (var i = 0; i < result.op_ng.length; i++) {
						body += "<tr>";
						body += "<td>"+name[i]+"</td>";
						body += "<td>"+ng_rate[i].toFixed(2)+"%</td>";
						body += "</tr>";
					}
					$('#body_op_ng').append(body);

					
					Highcharts.chart('container1', {
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 18pt;">Highest NG Rate by OP on '+ bulanText(result.bulan) +'</span>',
							useHTML: true
						},
						xAxis: {
							categories: name,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								rotation: -45,
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'NG Rate (%)'
							},
							plotLines: [{
								color: '#FF0000',
								value: 15,
								dashStyle: 'shortdash',
								width: 2,
								zIndex: 5,
								label: {
									align:'right',
									text: 'Target 15%',
									x:-7,
									style: {
										fontSize: '12px',
										color: '#FF0000',
										fontWeight: 'bold'
									}
								}
							}],
						},
						legend : {
							enabled: false
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span>{point.category}</span><br/><span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',

						},
						plotOptions: {
							series:{
								dataLabels: {
									enabled: true,
									format: '{point.y:.2f}%',
									rotation: -90,
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
										click: function (event) {
											showDetail(result.date, event.point.category);

										}
									}
								},
							}
						},credits: {
							enabled: false
						},
						series: [
						{
							name: 'Working Time',
							data: data
						}
						]
					});
				}
			}
		});

		$.get('{{ url("fetch/middle/bff_op_work_monthly") }}', data, function(result, status, xhr) {

			if(xhr.status == 200){
				if(result.status){

					var name = [];
					var count_time = [];
					var sum_time = [];
					var avg_time = [];

					var series = [];

					for (var i = 0; i < result.emp.length; i++) {
						var name_temp = result.emp[i].name.split(" ");
						var xAxis = '';
						xAxis += result.emp[i].employee_id + ' - ';

						if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Moch.' || name_temp[0] == 'Mochamad'){
							xAxis += name_temp[0].charAt(0)+'. '+name_temp[1];
						}else{
							xAxis += name_temp[0]+'. '+name_temp[1].charAt(0);
						}

						name.push(xAxis);


						var sum = 0;
						var temp = 0;
						for (var j = 0; j < result.act.length; j++) {
							if(result.emp[i].employee_id == result.act[j].operator_id){
								sum += parseInt(result.act[i].act);
								temp += 1;
							}
						}

						count_time.push(temp);
						sum_time.push(sum);
						avg_time.push(Math.ceil(sum/temp));

						series.push([xAxis, Math.ceil(sum/temp)]);

					}


					series.sort(function(a, b){return b[1] - a[1]});
					var categories = [];
					var y = [];
					var data = [];
					for (var i = 0; i < series.length; i++) {
						categories.push(series[i][0]);
						y.push(series[i][1]);

						if(y[i] > 400){
							data.push({y: y[i], color: 'rgb(144,238,126)'});
						}else{
							data.push({y: y[i], color: 'rgb(255,116,116)'})
						}
					}

					Highcharts.chart('container2', {
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 18pt;">Highest Working Time Average on '+ bulanText(result.bulan) +'</span>',
							useHTML: true
						},
						xAxis: {
							categories: categories,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								rotation: -45,
								style: {
									fontSize: '13px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Minutes'
							},
							plotLines: [{
								color: '#FF0000',
								value: 400,
								dashStyle: 'shortdash',
								width: 2,
								zIndex: 5,
								label: {
									align:'right',
									text: 'Target 400 Minutes',
									x:-7,
									style: {
										fontSize: '12px',
										color: '#FF0000',
										fontWeight: 'bold'
									}
								}
							}],
						},
						legend : {
							enabled: false
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span>{point.category}</span><br/><span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b> <br/>',

						},
						plotOptions: {
							series:{
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									rotation: -90,
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
										click: function (event) {
											showDetail(result.date, event.point.category);

										}
									}
								},
							}
						},credits: {
							enabled: false
						},
						series: [
						{
							name: 'Working Time',
							data: data
						}
						]
					});
				}
			}

		});


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

	$('.datepicker').datepicker({
		<?php $tgl_max = date('m-Y') ?>
		format: "mm-yyyy",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
		endDate: '<?php echo $tgl_max ?>'

	});

	function bulanText(param){
		var bulan = parseInt(param.slice(0, 2));
		var tahun = param.slice(3, 8);
		var bulanText = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

		return bulanText[bulan-1]+" "+tahun;
	}





</script>
@endsection