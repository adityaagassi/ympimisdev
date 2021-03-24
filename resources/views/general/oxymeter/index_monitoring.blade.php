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

	.dataTables_info,
	.dataTables_length {
		color: white;
	}

	div.dataTables_filter label, 
	div.dataTables_wrapper div.dataTables_info {
		color: white;
	}

	div#tableDetail_info.dataTables_info,
	div#tableDetail_filter.dataTables_filter label,
	div#tableDetail_wrapper.dataTables_wrapper{
		color: black;
	}

	#tableDetail_info.dataTables_info,
	#tableDetail_info.dataTables_length {
		color: black;
	}

	div#tableDetailCheck_info.dataTables_info,
	div#tableDetailCheck_filter.dataTables_filter label,
	div#tableDetailCheck_wrapper.dataTables_wrapper{
		color: black;
	}

	#tableDetailCheck_info.dataTables_info,
	#tableDetailCheck_info.dataTables_length {
		color: black;
	}

	#tableTotalOfc tr td {
		cursor: pointer;
	}

	#tableTotalPrd tr td {
		cursor: pointer;
	}

	.alert {
		-webkit-animation: fade 1s infinite;  /* Safari 4+ */
		-moz-animation: fade 1s infinite;  /* Fx 5+ */
		-o-animation: fade 1s infinite;  /* Opera 12+ */
		animation: fade 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes fade {
		0%, 49% {
			background-color: #8cd790;
		}
		50%, 100% {
			background-color: #e50000;
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
					<div class="col-md-3" style="padding-left: 3px;padding-right: 0px">
						<div class="input-group">
							<div class="input-group-addon bg-blue">
								<i class="fa fa-search"></i>
							</div>
							<select class="form-control select2" multiple="multiple" id="group" data-placeholder="Select Group" style="border-color: #605ca8" onchange="fetchTemperature()">
								
							</select>
						</div>
					</div>
					<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 1vw	;font-size: 1vw;color: white"></div>
				</form>
			</div>
		</div>
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<div class="col-xs-5">
					<span style="color: white; font-size: 1.7vw; font-weight: bold;"><i class="fa fa-caret-right"></i> Office</span>
					<table class="table table-bordered" id="tableTotalOfc" style="margin-bottom: 5px;">
						<thead>
							<tr>
								<th style="width:2%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Shift Schedule</th>
								<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Checked</th>
								<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Unchecked</th>
								<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Total</th>
							</tr>
						</thead>
						<tbody id="tableTotalBodyOfc">
							<tr>
								<td style="font-size: 1.7vw; font-weight: bold;color: black;" id="">Shift 1</td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black;" id="total_check_ofc_1"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black;" id="total_uncheck_ofc_1"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black;" id="total_person_ofc_1"></td>
							</tr>
							<tr>
								<td style="font-size: 1.7vw; font-weight: bold;color: black;" id="">Shift 2</td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black;" id="total_check_ofc_2"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black;" id="total_uncheck_ofc_2"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black;" id="total_person_ofc_2"></td>
							</tr>
						</tbody>
					</table>
				<!-- 	<span style="color: white; font-size: 1.7vw; font-weight: bold;"><i class="fa fa-caret-right"></i> Production</span>
					<table class="table table-bordered" id="tableTotalPrd" style="margin-bottom: 5px;">
						<thead>
							<tr>
								<th style="width:2%; text-align: center;color: white; font-size: 1.2vw;">Shift Schedule</th>
								<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw;">Hadir</th>
								<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw">Belum Hadir</th>
								<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw">Total</th>
							</tr>
						</thead>
						<tbody id="tableTotalBodyPrd">
							<tr>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="">Shift 1</td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_check_prd_1"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_uncheck_prd_1"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_person_prd_1"></td>
							</tr>
							<tr>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="">Shift 2</td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_check_prd_2"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_uncheck_prd_2"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_person_prd_2"></td>
							</tr>
							<tr>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="">Shift 3</td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_check_prd_3"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_uncheck_prd_3"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_person_prd_3"></td>
							</tr>
						</tbody>
					</table> -->
					<span style="color: white; font-size: 1.7vw; font-weight: bold;"><i class="fa fa-caret-right"></i> Oxygen Rate Below Standard</span>
					<table class="table table-bordered" id="tableAbnormal" style="margin-bottom: 5px;">
						<thead style="color: white">
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 3%;">ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 3%;">Dept</th>
								<th style="width: 3%;">Shift</th>
								<th style="width: 2%;">Time</th>
								<th style="width: 2%;">Oxy</th>
							</tr>			
						</thead>
						<tbody id="bodyAbnormal">
						</tbody>
					</table>
				</div>
				<div class="col-xs-7">
					<div id="chart" style="width: 100%;height: 600px"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="padding-bottom: 15px" class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loadingDetail" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr style="color: white">
								<th style="width: 1%;">#</th>
								<th style="width: 3%;">ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 9%;">Dept</th>
								<th style="width: 9%;">Sect</th>
								<th style="width: 9%;">Group</th>
								<th style="width: 9%;">Point</th>
								<th style="width: 3%;">Time</th>
								<th style="width: 2%;">Temp</th>
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

<div class="modal fade" id="modalDetailCheck">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="padding-bottom: 15px" class="modal-title" id="modalDetailTitleCheck"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loadingDetailCheck" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableDetailCheck">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr style="color: white">
								<th style="color:white;width: 1%; font-size: 1.2vw;">#</th>
								<th style="color:white;width: 5%; font-size: 1.2vw; text-align: center;">ID</th>
								<th style="color:white;width: 30%; font-size: 1.2vw; text-align: center;">Name</th>
								<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Dept</th>
								<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Sect</th>
								<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Group</th>
								<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Shift</th>
								<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Attendance</th>
								<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Time In</th>
							</tr>
						</thead>
						<tbody id="tableDetailCheckBody">
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
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: new Date()
		});
		$('.select2').select2();
		draw_data();
	});

	function draw_data() {
		var data = {
			dt : '{{ date("Y-m-d") }}'
		}

		$.get('{{ url("fetch/general/oxymeter/data") }}', data, function(result, status, xhr){
			var xCategories = [];
			var xSeries = [];
			var xColor = [];


			// total_check_ofc_1
			// total_uncheck_ofc_1
			// total_person_ofc_1

			for (var i = 80; i <= 100; i++) {
				xCategories.push(i);
				var stat = 0;
				$.each(result.datas, function(index, value){
					if (parseInt(value.remark) == i) {
						stat = 1;
						xSeries.push(value.qty);
						if (parseInt(value.remark) >= 95) {
							xColor.push("#8cd790");
						} else {
							xColor.push("#ed5c64");
						}
					}
				})

				if (stat == 0) {
					xSeries.push(0);
					xColor.push("#8cd790");
				}
			}

			// --------------   CHECKED TABLE   -------------------

			$("#total_check_ofc_1").empty();
			$("#total_uncheck_ofc_1").empty();
			$("#total_person_ofc_1").empty();

			$("#total_check_ofc_2").empty();
			$("#total_uncheck_ofc_2").empty();
			$("#total_person_ofc_2").empty();

			var ofc_cek_1 = 0;
			var ofc_uncek_1 = 0;
			var ofc_total_1 = 0;

			var ofc_cek_2 = 0;
			var ofc_uncek_2 = 0;
			var ofc_total_2 = 0;

			var below_rate = [];


			$.each(result.shift, function(index, value){
				if (~value.shiftdaily_code.indexOf("1")) {
					ofc_total_1++;
					if (value.oxy != "") {
						ofc_cek_1++;
					} else {
						ofc_uncek_1++;
					}
				} else if (~value.shiftdaily_code.indexOf("2")) {
					ofc_total_2++;
					if (value.oxy != "") {
						ofc_cek_2++;
					} else {
						ofc_uncek_2++;
					}
				}

				if (parseInt(value.oxy) < 95) {
					below_rate.push({'emp_id': value.employee_id, 'name': value.name, 'shift': value.shiftdaily_code, 'oxy': value.oxy});
				}
			})


			$("#total_check_ofc_1").text(ofc_cek_1);
			$("#total_uncheck_ofc_1").text(ofc_uncek_1);
			$("#total_person_ofc_1").text(ofc_total_1);

			$("#total_check_ofc_2").text(ofc_cek_2);
			$("#total_uncheck_ofc_2").text(ofc_uncek_2);
			$("#total_person_ofc_2").text(ofc_total_2);


			// ------------------  TABLE BELOW RATE ------------------------
			$("#bodyAbnormal").empty();
			var body = "";

			$.each(below_rate, function(index, value){
				body += "<tr>";
				body += "<td class='alert'>"+(index + 1)+"</td>";
				body += "<td class='alert'>"+value.emp_id+"</td>";
				body += "<td class='alert'>"+value.name+"</td>";
				body += "<td class='alert'>-</td>";
				body += "<td class='alert'>"+value.shift+"</td>";
				body += "<td class='alert'>-</td>";
				body += "<td class='alert'>"+value.oxy+"</td>";
				body += "</tr>";
			})

			$("#bodyAbnormal").append(body);


			// ------------------   GRAFIK   ----------------------------
			Highcharts.chart('chart', {
				chart: {
					type: 'column'
				},
				title: {
					text: 'OXYGEN METER MONITORING',
					style: {
						fontSize: '20px',
						fontWeight: 'bold'
					}
				},
				xAxis: {
					categories: xCategories,
					gridLineWidth: 1,
					gridLineColor: 'RGB(204,255,255)',
					label: {
						style: {
							fontSize: '20px',
							fontWeight: 'bold'
						},
					},
					title: {
						text: 'Oxygen Meter',
					}
				},
				yAxis: {
					min: 0,
					title: {
						text: 'Count Person(s)'
					},
					tickInterval: 1,
				},
				tooltip: {
					headerFormat: '<span style="font-size:10px">Oxygen Rate <b>{point.key}</b></span><table>',
					pointFormat: '<tr><td style="padding:0"><b>{point.y} Person</b></td></tr>',
					footerFormat: '</table>',
					shared: true,
					useHTML: true
				},
				plotOptions: {
					column: {
						pointPadding: 0.2,
						borderWidth: 0,
						dataLabels: {
							enabled: true
						}
					}, 
					series: {
						colorByPoint: true,
						colors: xColor
					}
				},
				legend: {
					enabled: false
				},
				credits: {
					enabled: false
				},
				series: [{
					name: 'Oxygen',
					data: xSeries
				}]
			});
		})
	}


	// STYLE CHART
	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#8cd790', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
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
