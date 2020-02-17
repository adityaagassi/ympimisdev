@extends('layouts.visitor')
@section('stylesheets')
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
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
		border:none;
		background-color: rgba(126,86,134);
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
	.content{
		color: white;
		font-weight: bold;
	}
	.patient-duration{
		margin: 0px;
		padding: 0px;
	}
	#ada{
		background-color: rgba(118,255,3,.65);
	}
	#tidak-ada{
		background-color: rgba(255,0,0,.85);
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">
	<div class="row">
		<form method="GET" action="{{ action('ClinicController@indexClinicVisit') }}">
			<div class="col-xs-2">
				<div class="input-group date">
					<div class="input-group-addon bg-green" style="border: none;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" name="month" id="month" placeholder="Select Month">
				</div>
			</div>
			<div class="col-xs-1">
				<button class="btn btn-success" type="submit">Update Chart</button>
			</div>
		</form>
		<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>

		<div class="col-xs-12">
			<div id="container1" style="min-width: 300px; height: 200px; margin: 0 auto"></div>
		</div>
		<div class="col-xs-12">
			<div id="container2" style="min-width: 300px; margin: 0 auto"></div>
		</div>
	</div>

	<div class="modal fade" id="modal-detail" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Clinic Visit Detail</b></h4>
					<h5 class="modal-title" style="text-align: center;" id="judul-detail"></h5>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<table id="detail" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="detail-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Employee ID</th>
										<th>Name</th>
										<th>Paramedic</th>
										<th>Visited at</th>
										<th>Purpose</th>
									</tr>
								</thead>
								<tbody id="detail-body">
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
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fillChart();
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m') ?>
		format: "yyyy-mm",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
		endDate: '<?php echo $tgl_max ?>'
	});

	function bulanText(param){
		var date = param.split('-');
		var bulan = parseInt(date[1]);
		var tahun = date[0];
		var bulanText = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

		return bulanText[bulan-1]+" "+tahun;
	}

	function fillChart() {
		var month = "{{$_GET['month']}}";

		var data = {
			month:month,
		}

		$.get('{{ url("fetch/daily_clinic_visit") }}', data, function(result, status, xhr) {
			if(result.status){
				var date = [];
				var visit = [];
				for (i = 0; i < result.clinic_visit.length; i++) {
					date.push(result.clinic_visit[i].week_date);
					visit.push(parseInt(result.clinic_visit[i].visit));
				}

				Highcharts.chart('container1', {
					chart: {
						type: 'spline'
					},
					title: {
						text: 'Daily Clinic Visit'
					},
					subtitle: {
						text: 'on '+ bulanText(result.month),
						style: {
							fontSize: '1vw',
						}
					},						
					xAxis: {
						categories: date,
					},
					yAxis: {
						title: {
							text: 'Clinic Visit'
						}
					},
					tooltip: {
						shared: true,
					},
					credits: {
						enabled: false
					},
					legend: {
						enabled: false
					},
					plotOptions: {
						areaspline: {
							fillOpacity: 0.5,
							dataLabels: {
								enabled: true
							},
							enableMouseTracking: true
						}
					},
					series: 
					[{
						name: 'Clinic Visit',
						data: visit,
						color: '#90ee7e'
					}]
				});		
			}

		});

		$.get('{{ url("fetch/clinic_visit") }}', data, function(result, status, xhr) {
			if(result.status){
				var department = [];
				var visit = [];
				var percentage = [];
				for (i = 0; i < result.clinic_visit.length; i++) {
					department.push(result.clinic_visit[i].department);
					visit.push(parseInt(result.clinic_visit[i].qty));
					for (j = 0; j < result.department.length; j++) {
						if(result.clinic_visit[i].department == result.department[j].department){
							percentage.push((result.clinic_visit[i].qty / result.department[j].qty) * 100);
						}
					}
				}

				Highcharts.SVGRenderer.prototype.symbols['c-rect'] = function (x, y, w, h) {
					return ['M', x, y + h / 2, 'L', x + w, y + h / 2];
				};


				Highcharts.chart('container2', {
					chart: {
						zoomType: 'xy'
					},
					title: {
						text: 'Clinic Visit VS Number of Employees'
					},
					subtitle: {
						text: 'on '+ bulanText(result.month),
						style: {
							fontSize: '1vw',
						}
					},						
					xAxis: {
						categories: department,
					},
					yAxis: [{
						title: {
							text: 'Patient(s)'
						}
					},{
						title: {
							text: '(%) Employees'
						},
						labels: {
							format: '{value} %',

						},
						opposite: true

					}],
					tooltip: {
						shared: false,
					},
					credits: {
						enabled: false
					},
					legend: {
						align: 'right',
						x: -50,
						verticalAlign: 'top',
						y: 30,
						itemStyle:{
							color: "white",
							fontSize: "12px",
							fontWeight: "bold",

						},
						floating: true,
						shadow: false
					},
					plotOptions: {
						column:{
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
										showDetail(this.category, result.month);
									}
								}
							},
						},
						scatter:{
							dataLabels: {
								enabled: true,
								format: '{point.y: .0f}%',
								style:{
									fontSize: '15px'
								}
							},
						}
					},
					series: [
					{
						name: 'Clinic Visit',
						data: visit,
						type: 'column',
						color: 'rgb(144,238,126)',
						tooltip: {
							valueSuffix: ' Person(s)'
						}
					},
					{
						name: 'Percent Employees',
						data: percentage,
						yAxis: 1,
						marker: {
							symbol: 'c-rect',
							lineWidth:5,
							lineColor: 'rgb(255,0,0)',
							radius: 20,
						},
						type: 'scatter',
						tooltip: {
							headerFormat: '<span></span>',
							pointFormat: '<span>{series.name}: <b>{point.y:.0f}% </b></span>',
						},
					}
					]
				});		
			}
		});

	}

	function showDetail(department, month) {
		var data = {
			department : department,
			month : month
		}

		$.get('{{ url("fetch/clinic_visit_detail") }}', data, function(result, status, xhr){
			if(result.status){
				$('#modal-detail').modal('show');

				$('#detail').DataTable().clear();
				$('#detail').DataTable().destroy();
				$('#detail-body').html("");

				$('#judul-detail').append().empty();
				$('#judul-detail').append('<b>'+ department +' on '+ bulanText(month) +'</b>');

				var body = '';
				for (var i = 0; i < result.detail.length; i++) {
					body += '<tr>';
					body += '<td>'+ result.detail[i].employee_id +'</td>';
					body += '<td>'+ result.detail[i].name +'</td>';
					body += '<td>'+ result.detail[i].paramedic +'</td>';
					body += '<td>'+ result.detail[i].visited_at +'</td>';
					body += '<td>'+ result.detail[i].purpose +'</td>';
					body += '</tr>';
				}

				$('#detail-body').append(body);
				$('#detail').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						},
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
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
				[0, '#3c3c3c']
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

	
</script>
@endsection