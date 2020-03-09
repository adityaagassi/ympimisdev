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
		background-color: #ccff90;
	}
	table.table-bordered > tfoot > tr > th{
		font-size: 16px;
		border:1px solid black;
		background-color: #ffffc2;
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
				<form method="GET" action="{{ action('PantryController@indexDisplayPantryVisit') }}">
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
					<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 20px;font-size: 2vw; color: white;"></div>
				</form>
			</div>
		</div>
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<div class="col-xs-4">
					<span style="color: white; font-size: 22px; font-weight: bold;"><i class="fa fa-caret-right"></i> Today's Visitor</span>
					<table class="table table-bordered" id="tableTotal" style="margin-bottom: 5px;">
						<thead>
							<tr>
								<th style="width: 50%; text-align: center;">Total Visitor</th>
								<th style="width: 50%; text-align: center;">Total Duration</th>
							</tr>			
						</thead>
						<tbody id="tableTotalBody">
							<tr>
								<td style="font-size: 40px; font-weight: bold;" id="total_person">99 Person(s)</td>
								<td style="font-size: 40px; font-weight: bold;" id="total_duration">99 Min(s)</td>
							</tr>
						</tbody>
					</table>
					<span style="color: white; font-size: 22px; font-weight: bold;"><i class="fa fa-caret-right"></i> Realtime Visitor</span>
					<table class="table table-bordered" id="tableVisitor" style="margin-bottom: 5px;">
						<thead>
							<tr>
								<th style="width: 5%; text-align: center;">ID</th>
								<th style="width: 30%; text-align: center;">Name</th>
								<th style="width: 10%; text-align: center;">Duration</th>
							</tr>					
						</thead>
						<tbody id="tableVisitorBody">
						</tbody>
					</table>
					<span style="color: white; font-size: 22px; font-weight: bold;"><i class="fa fa-caret-right"></i> Haven't Visited</span>
					<table class="table table-bordered" id="tableNovisit" style="margin-bottom: 5px;">
						<thead>
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 5%; text-align: center;">ID</th>
								<th style="width: 30%; text-align: center;">Name</th>
							</tr>					
						</thead>
						<tbody id="tableNovisitBody">
						</tbody>
					</table>
				</div>
				<div class="col-xs-8">
					<div id="container1" class="container1" style="width: 100%;"></div>
					<div id="container2" class="container2" style="width: 100%;"></div>
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
								<th style="width: 3%;">ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 3%;">Duration(Min)</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
						<tfoot>
							<tr>
								<th colspan="3">Total Duration</th>
								<th id="totalDetail">9</th>
							</tr>
						</tfoot>
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
		$('#tanggal').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		fetchVisitor();
		setInterval(fetchVisitor, 20000);
		fetchRealtimeVisitor();
		setInterval(fetchRealtimeVisitor, 5000);
		setInterval(setTime, 1000);
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

	var in_time = [];
	function setTime() {
		for (var i = 0; i < in_time.length; i++) {
			var duration = diff_seconds(new Date(), in_time[i]);
			document.getElementById("hours"+i).innerHTML = pad(parseInt(duration / 3600));
			document.getElementById("minutes"+i).innerHTML = pad(parseInt((duration % 3600) / 60));
			document.getElementById("seconds"+i).innerHTML = pad(duration % 60);
		}
	}

	function pad(val) {
		var valString = val + "";
		if (valString.length < 2) {
			return "0" + valString;
		} else {
			return valString;
		}
	}

	function diff_seconds(dt2, dt1){
		var diff = (dt2.getTime() - dt1.getTime()) / 1000;
		return Math.abs(Math.round(diff));
	}

	function fetchVisitor(){
		var tanggal = "{{$_GET['tanggal']}}";

		var data = {
			tanggal:tanggal
		}

		$.get('{{ url("fetch/pantry/visitor") }}', data, function(result, status, xhr) {

			$('#tableNovisitBody').html('');

			var index = 1;
			var resultData = "";

			$.each(result.novisit, function(key, value) {
				resultData += '<tr>';
				resultData += '<td>'+ index +'</td>';
				resultData += '<td>'+ value.employee_id +'</td>';
				resultData += '<td>'+ value.NAME +'</td>';
				resultData += '</tr>';
				index += 1;
			});
			$('#tableNovisitBody').append(resultData);

			$('#total_person').text(result.total[0].qty_employee + ' Prsn(s)');
			$('#total_duration').text(result.total[0].qty_duration + ' Min(s)');

			var categories1 = [];
			var series1 = [];

			$.each(result.duration, function(key, value) {
				categories1.push(value.duration);
				series1.push(value.qty_employee);
			});

			var chart = Highcharts.chart('container1', {
				chart: {
					type: 'column',
					backgroundColor: null
				},
				title: {
					text: 'Pantry Visitor By Duration',
					style: {
						fontSize: '30px',
						fontWeight: 'bold'
					}
				},
				subtitle: {
					text: 'Last Update: '+getActualFullDate(),
					style: {
						fontSize: '18px',
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
							fontSize: '26px'
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
								fontSize: '26px'
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
									fetchVisitorDetail(this.category, 'duration');
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

			var categories2 = [];
			var series2 = [];

			$.each(result.hourly, function(key, value) {
				categories2.push(value.jam);
				series2.push(value.qty_visit);
			});

			var chart2 = Highcharts.chart('container2', {
				chart: {
					type: 'column',
					backgroundColor: null
				},
				title: {
					text: 'Pantry Visitor By Hour',
					style: {
						fontSize: '30px',
						fontWeight: 'bold'
					}
				},
				subtitle: {
					text: 'Last Update: '+getActualFullDate(),
					style: {
						fontSize: '18px',
						fontWeight: 'bold'
					}
				},
				yAxis: {
					title: {
						text: 'Count Person(s)'
					}
				},
				xAxis: {
					categories: categories2,
					type: 'category',
					gridLineWidth: 1,
					gridLineColor: 'RGB(204,255,255)',
					labels: {
						style: {
							fontSize: '26px'
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
								fontSize: '26px'
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
									fetchVisitorDetail(this.category, 'hour');
								}
							}
						}
					}
				},
				series: [{
					name:'Person(s)',
					type: 'column',
					data: series2,
					showInLegend: false,
					color: '#ff851b'
				}]

			});
		});		
	}

	function fetchVisitorDetail(cat, typ){
		$('#modalDetail').modal('show');
		$('#loading').show();
		$('#modalDetailTitle').html("");
		$('#tableDetail').hide();

		var tanggal = "{{$_GET['tanggal']}}";

		var data = {
			tanggal:tanggal,
			category:cat,
			type:typ
		}

		$.get('{{ url("fetch/pantry/visitor_detail") }}', data, function(result, status, xhr) {
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
					resultData += '<td>'+ value.duration +'</td>';
					resultData += '</tr>';
					index += 1;
					total += parseFloat(value.duration);
				});
				$('#tableDetailBody').append(resultData);
				$('#modalDetailTitle').html("<center><span style='font-size: 20px; font-weight: bold;'>Stock: "+cat+"</span></center>");
				$('#loading').hide();
				$('#totalDetail').text(total);
				$('#tableDetail').show();
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function fetchRealtimeVisitor(){
		$.get('{{ url("fetch/pantry/realtime_visitor") }}', function(result, status, xhr) {
			if(result.status){
				var tableData = "";
				var count = 0;
				in_time = [];

				$('#tableVisitorBody').html("");

				$.each(result.visitors, function(key, value){
					in_time.push(new Date(value.in_time));

					tableData += '<tr>';
					tableData += '<td>'+value.employee_id+'</td>';
					tableData += '<td>'+value.name+'</td>';
					tableData += '<td>';
					tableData += '<span style="font-weight: bold;" id="hours'+ count +'">'+ pad(parseInt(diff_seconds(new Date(), in_time[count]) / 3600)) +'</span>:';
					tableData += '<span style="font-weight: bold;" id="minutes'+ count +'">'+ pad(parseInt((diff_seconds(new Date(), in_time[count]) % 3600) / 60)) +'</span>:';
					tableData += '<span style="font-weight: bold;" id="seconds'+ count +'">'+ pad(diff_seconds(new Date(), in_time[count]) % 60) +'</span>';
					tableData += '</td>';
					tableData += '</tr>';

					++count;
				});

				$('#tableVisitorBody').append(tableData);

			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}
</script>
@endsection
