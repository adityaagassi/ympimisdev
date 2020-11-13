@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	input {
		line-height: 22px;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
		vertical-align: middle;
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
		font-size: 18px;
		padding-top: 1px;
		padding-bottom: 1px;
		border:1px solid black;
		background-color: rgba(126,86,134);
	}
	table.table-bordered > tbody > tr > td{
		font-size: 16px;
		border:1px solid black;
		padding-top: 3px;
		padding-bottom: 3px;
		background-color: #8CD790;
		color: #000;
	}
	table.table-bordered > tfoot > tr > th{
		font-size: 16px;
		border:1px solid black;
		background-color: #ffffc2;
	}

	.sedang {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: sedang 1s infinite;  /* Safari 4+ */
		-moz-animation: sedang 1s infinite;  /* Fx 5+ */
		-o-animation: sedang 1s infinite;  /* Opera 12+ */
		animation: sedang 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes sedang {
		0%, 49% {
			background: #ff0033;
			color: white;
		}
		50%, 100% {
			background-color: #ffccff;
		}
	}
</style>
@stop
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
							<input type="text" class="form-control datepicker" id="tanggal_from" name="tanggal_from" placeholder="Select Date" onchange="fetchTemperature()">
						</div>
					</div>
					<!-- <div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal_to" name="tanggal_to" placeholder="Select Date To" onchange="fetchTemperature()">
						</div>
					</div> -->
					<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 1vw	;font-size: 1vw;color: white"></div>
				</form>
			</div>
		</div>
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<div class="col-xs-4">
					<span style="color: white; font-size: 1.7vw; font-weight: bold;"><i class="fa fa-caret-right"></i> Cek Hari Ini</span>
					<table class="table table-bordered" id="tableTotal" style="margin-bottom: 5px;">
						<thead>
							<tr>
								<th style="width: 50%; text-align: center;color: white; font-size: 1.2vw;">Sudah Cek</th>
								<th style="width: 50%; text-align: center;color: white; font-size: 1.2vw">Belum Cek</th>;
							</tr>			
						</thead>
						<tbody id="tableTotalBody">
							<tr>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_check"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_uncheck"></td>
							</tr>
						</tbody>
					</table>
					<span style="color: white; font-size: 1.7vw; font-weight: bold;"><i class="fa fa-caret-right"></i> Detail Cek Suhu >= 37.5 °C</span>
					<table class="table table-bordered" id="tableAbnormal" style="margin-bottom: 5px;">
						<thead>
							<tr>
								<th style="color:white;width: 30%; font-size: 1.2vw; text-align: center;">Karyawan</th>
								<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Temp</th>
							</tr>			
						</thead>
						<tbody id="tableAbnormalBody">
						</tbody>
					</table>
					<span style="color: white; font-size: 1.7vw; font-weight: bold;"><i class="fa fa-caret-right"></i> Detail Belum Cek</span>
					<table class="table table-bordered" id="tableNoCheck" style="margin-bottom: 5px;">
						<thead>
							<tr>
								<th style="color:white;width: 1%; font-size: 1.2vw;">#</th>
								<th style="color:white;width: 5%; font-size: 1.2vw; text-align: center;">ID</th>
								<th style="color:white;width: 30%; font-size: 1.2vw; text-align: center;">Name</th>
								<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Attendance</th>
							</tr>					
						</thead>
						<tbody id="tableNoCheckBody">
						</tbody>
					</table>
				</div>
				<div class="col-xs-8">
					<div id="container1" class="container1" style="width: 100%;height: 600px"></div>
					<!-- <div id="container2" class="container2" style="width: 100%;"></div> -->
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
							<tr style="color: white">
								<th style="width: 1%;">#</th>
								<th style="width: 3%;">Employee ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 3%;">Check Time</th>
								<th style="width: 3%;">Check Point</th>
								<th style="width: 2%;">Temperature</th>
								<th style="width: 2%;">Abnormal Status</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
						<!-- <tfoot>
							<tr>
								<th colspan="5">Total Duration</th>
								<th id="totalDetail">9</th>
							</tr>
						</tfoot> -->
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

	jQuery(document).ready(function() {
		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		fetchTemperature();
		setInterval(fetchTemperature, 20000);
	});


	function fetchTemperature(){
		var tanggal_from = $('#tanggal_from').val();
		// var tanggal_to = $('#tanggal_to').val();

		var data = {
			tanggal_from:tanggal_from,
			// tanggal_to:tanggal_to,
			location:'{{$loc}}'
		}

		$.get('{{ url("fetch/temperature/minmoe_monitoring") }}', data, function(result, status, xhr) {

			$('#tableNoCheckBody').html('');

			var index = 1;
			var check = 0;
			var uncheck = 0;
			var resultData = "";

			$.each(result.datacheck, function(key, value) {
				if (value.checks == null) {
					if (result.attendance[key][0] != undefined) {
						var attnd = result.attendance[key][0].attend_code;
					}else{
						var attnd = '-';
					}
					resultData += '<tr>';
					resultData += '<td style="font-size: 1vw;">'+ index +'</td>';
					resultData += '<td style="font-size: 1vw;">'+ value.employee_id +'</td>';
					resultData += '<td style="font-size: 1vw;">'+ value.name +'</td>';
					resultData += '<td style="font-size: 1vw;">'+ attnd +'</td>';
					resultData += '</tr>';
					index++;
					uncheck++;
				}else{
					check++;
				}
			});

			$('#tableNoCheckBody').append(resultData);

			$('#tableAbnormalBody').html('');

			var index = 1;
			var resultDataAbnormal = "";

			$.each(result.dataAbnormal, function(key, value) {
				resultDataAbnormal += '<tr>';
				resultDataAbnormal += '<td class="sedang" style="font-size: 1.5vw;vertical-align:middle; font-weight: bold; background-color: #ffccff">'+value.employee_id+'<br>'+ value.name +'</td>';
				resultDataAbnormal += '<td class="sedang" style="font-size: 1.7vw;vertical-align:middle; font-weight: bold; background-color: #ffccff">'+ value.temperature +'</td>';
				resultDataAbnormal += '</tr>';
				index++;
			});

			$('#tableAbnormalBody').append(resultDataAbnormal);

			$('#total_check').html(check+' Person(s)');
			$('#total_uncheck').html(uncheck+' Person(s)');

			var categories1 = [];
			var series1 = [];

			$.each(result.datatoday, function(key, value) {
				categories1.push(value.temperature+' °C');
				series1.push({y:parseFloat(value.jumlah),key:value.temperature});
			});

			$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Update: '+ getActualFullDate() +'</p>');

			var chart = Highcharts.chart('container1', {
				chart: {
					type: 'column',
					backgroundColor: null
				},
				title: {
					text: 'Employees Temperature Monitoring <br>On '+result.dateTitle,
					style: {
						fontSize: '25px',
						fontWeight: 'bold'
					}
				},
				yAxis: {
					title: {
						text: 'Count Person(s)'
					}
				},
				xAxis: {
					categories: categories1,
					type: 'category',
					gridLineWidth: 1,
					gridLineColor: 'RGB(204,255,255)',
					labels: {
						style: {
							fontSize: '20px'
						}
					},
				},
				credits: {
					enabled:false
				},
				plotOptions: {
					series:{
						dataLabels: {
							enabled: true,
							format: '{point.y}',
							style:{
								textOutline: false,
								fontSize: '20px'
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
									fetchTemperatureDetail(this.key);
								}
							}
						}
					}
				},
				series: [{
					name:'Person(s)',
					type: 'column',
					data: series1,
					showInLegend: false,
					color: '#00a65a'
				}]

			});

			// var categories2 = [];
			// var series2 = [];

			// $.each(result.hourly, function(key, value) {
			// 	categories2.push(value.jam);
			// 	series2.push(value.qty_visit);
			// });

			// var chart2 = Highcharts.chart('container2', {
			// 	chart: {
			// 		type: 'column',
			// 		backgroundColor: null
			// 	},
			// 	title: {
			// 		text: 'Pantry Visitor By Hour (07:00 - 16:00 exclude break)',
			// 		style: {
			// 			fontSize: '30px',
			// 			fontWeight: 'bold'
			// 		}
			// 	},
			// 	subtitle: {
			// 		text: 'Last Update: '+getActualFullDate(),
			// 		style: {
			// 			fontSize: '18px',
			// 			fontWeight: 'bold'
			// 		}
			// 	},
			// 	yAxis: {
			// 		title: {
			// 			text: 'Count Person(s)'
			// 		}
			// 	},
			// 	xAxis: {
			// 		categories: categories2,
			// 		type: 'category',
			// 		gridLineWidth: 1,
			// 		gridLineColor: 'RGB(204,255,255)',
			// 		labels: {
			// 			style: {
			// 				fontSize: '26px'
			// 			}
			// 		},
			// 	},
			// 	credits: {
			// 		enabled:false
			// 	},
			// 	plotOptions: {
			// 		series:{
			// 			dataLabels: {
			// 				enabled: true,
			// 				format: '{point.y}',
			// 				style:{
			// 					textOutline: false,
			// 					fontSize: '26px'
			// 				}
			// 			},
			// 			animation: false,
			// 			pointPadding: 0.93,
			// 			groupPadding: 0.93,
			// 			borderWidth: 0.93,
			// 			cursor: 'pointer',
			// 			point: {
			// 				events: {
			// 					click: function () {
			// 						fetchVisitorDetail(this.category, 'hour');
			// 					}
			// 				}
			// 			}
			// 		}
			// 	},
			// 	series: [{
			// 		name:'Person(s)',
			// 		type: 'column',
			// 		data: series2,
			// 		showInLegend: false,
			// 		color: '#ff851b'
			// 	}]

			// });
		});		
	}

	function fetchTemperatureDetail(temperature){
		$('#modalDetail').modal('show');
		$('#loading').show();
		$('#modalDetailTitle').html("");
		$('#tableDetail').hide();

		var tanggal_from = $('#tanggal_from').val();
		// var tanggal_to = $('#tanggal_to').val();

		var data = {
			tanggal_from:tanggal_from,
			// tanggal_to:tanggal_to,
			temperature:temperature,
			location:'{{$loc}}'
		}

		$.get('{{ url("fetch/temperature/detail_minmoe_monitoring") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#tableDetailBody').html('');

				var index = 1;
				var resultData = "";
				var total = 0;

				$.each(result.details, function(key, value) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.employee_id +'</td>';
					resultData += '<td>'+ value.name +'</td>';
					resultData += '<td>'+ value.date_in +'</td>';
					resultData += '<td>'+ value.point +'</td>';
					resultData += '<td>'+ value.temperature +' °C</td>';
					resultData += '<td>'+ value.abnormal_status +'</td>';
					resultData += '</tr>';
					index += 1;
				});
				$('#tableDetailBody').append(resultData);
				$('#modalDetailTitle').html("<center><span style='font-size: 20px; font-weight: bold;'>Detail Employees on "+temperature+" °C</span></center>");
				$('#loading').hide();
				$('#tableDetail').show();
			}
			else{
				alert('Attempt to retrieve data failed');
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
</script>
@endsection
