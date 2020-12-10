@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
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
		padding-top: 0;
		padding-bottom: 0;
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
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-4">
					<div id="chartStatus" style="width: 100%;"></div>
				</div>
				<div class="col-xs-4">
					<div id="chartUnion" style="width: 100%;"></div>
				</div>
				<div class="col-xs-4">
					<div id="chartGender" style="width: 100%;"></div>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-top: 10px;">
			<div id="chartDepartment" style="width: 100%;">/div>
			</div>
		{{-- <div class="col-xs-12" style="padding-top: 10px;">
			<div id="chartGrade" style="width: 100%;">By Grade (等級別)</div>
		</div> --}}
		<div id="chartPosition" style="width: 100%; padding-top: 10px;"></div>
	</div>
</section>

<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<div class="row">
					<div class="col-xs-12">
						<table id="tableDetail" class="table table-bordered table-striped table-hover">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%">#</th>
									<th style="width: 1%">ID</th>
									<th style="width: 5%">Name</th>
									<th style="width: 5%">Dept</th>
									<th style="width: 5%">Sect</th>
									<th style="width: 5%">Group</th>
									<th style="width: 5%">Sub</th>
									<th style="width: 1%">Join</th>
									<th style="width: 1%">Pos</th>
									<th style="width: 1%">Status</th>
								</tr>
							</thead>
							<tbody id="tableDetailBody">
							</tbody>
							<tfoot>
							</tfoot>
						</table>
					</div>
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

	jQuery(document).ready(function(){
		fetchChart()
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


	function fetchModal(filter, cat){
		$("#loading").show();
		var data = {
			filter:filter,
			category:cat
		};
		$.get('{{ url("fetch/report/manpower_detail") }}', data, function(result, status, xhr) {
			if(result.status){
				var tableData = "";
				$('#tableDetailBody').html("");
				var count = 1;
				$('#tableDetail').DataTable().clear();
				$('#tableDetail').DataTable().destroy();

				$.each(result.details, function(key, value) {
					tableData += "<tr>";
					tableData += "<td>"+count+"</td>";
					tableData += "<td>"+value.Emp_no+"</td>";
					tableData += "<td>"+value.Full_name+"</td>";
					tableData += "<td>"+value.Department+"</td>";
					tableData += "<td>"+value.Section+"</td>";
					tableData += "<td>"+value.Groups+"</td>";
					tableData += "<td>"+value.Sub_Groups+"</td>";
					tableData += "<td>"+value.start_date+"</td>";
					tableData += "<td>"+value.pos_name_en+"</td>";
					tableData += "<td>"+value.employ_code+"</td>";
					tableData += "</tr>";
					count += 1;
				});
				$('#tableDetailBody').append(tableData);
				$('#tableDetail').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					"pageLength": 30,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
					// {
					// 	extend: 'pageLength',
					// 	className: 'btn btn-default',
					// },
					{
						extend: 'copy',
						className: 'btn btn-success',
						text: '<i class="fa fa-copy"></i> Copy',
						exportOptions: {
							columns: ':not(.notexport)'
						}
					},
					{
						extend: 'excel',
						className: 'btn btn-info',
						text: '<i class="fa fa-file-excel-o"></i> Excel',
						exportOptions: {
							columns: ':not(.notexport)'
						}
					},
					{
						extend: 'print',
						className: 'btn btn-warning',
						text: '<i class="fa fa-print"></i> Print',
						exportOptions: {
							columns: ':not(.notexport)'
						}
					},
					]
				},
				'paging': true,
				'lengthChange': true,
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
				$("#loading").hide();
				$('#modalDetail').modal('show');
			}
			else{
				alert(result.message);
			}
		});
	}

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
		autoclose: true,
		format: "yyyy-mm",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
	});

	function fetchChart(){
		$('#loading').show();
		$.get('{{ url("fetch/report/manpower") }}', function(result, status, xhr) {
			if(result.status){
				var xStatus = [];
				var xGrade = [];
				var xGender = [];
				var xUnion = [];
				var countStatus = [];
				var countGrade = [];
				var countGender = [];
				var countUnion = [];
				var seriesStatus = [];
				var seriesGrade = [];
				var seriesGender = [];
				var seriesUnion = [];

				var xDepartment = [];
				var seriesDepartment = [];

				$.each(result.by_departments, function(key, value){
					if(xDepartment.indexOf(value.Department) === -1){
						xDepartment[xDepartment.length] = value.Department;
					}
					seriesDepartment.push(parseInt(value.total));
				});


				var xPosition = [];
				var seriesPosition = [];

				$.each(result.by_positions, function(key, value){
					if(xPosition.indexOf(value.pos_name_en) === -1){
						xPosition[xPosition.length] = value.pos_name_en;
					}
					seriesPosition.push(parseInt(value.total));
				});


				$.each(result.manpowers, function(key, value) {
					var catStatus = value.employ_code;
					var catGrade = value.grade_code;
					var catGender = value.gender;
					var catUnion = value.union;

					if(!countStatus[catStatus]) {
						countStatus[catStatus] = 0;
					}
					countStatus[catStatus]++;

					if(!countGrade[catGrade]) {
						countGrade[catGrade] = 0;
					}
					countGrade[catGrade]++;

					if(!countGender[catGender]) {
						countGender[catGender] = 0;
					}
					countGender[catGender]++;

					if(!countUnion[catUnion]) {
						countUnion[catUnion] = 0;
					}
					countUnion[catUnion]++;

					if(xStatus.indexOf(catStatus) === -1){
						xStatus[xStatus.length] = catStatus;
					}
					if(xGrade.indexOf(catGrade) === -1){
						xGrade[xGrade.length] = catGrade;
					}
					if(xGender.indexOf(catGender) === -1){
						xGender[xGender.length] = catGender;
					}
					if(xUnion.indexOf(catUnion) === -1){
						xUnion[xUnion.length] = catUnion;
					}
				});

				for(var prop in countStatus){
					seriesStatus.push(countStatus[prop]);
				}
				for(var prop in countGrade){
					seriesGrade.push(countGrade[prop]);
				}
				for(var prop in countGender){
					var objGender = {};
					objGender.name = prop;
					objGender.y = countGender[prop];
					seriesGender.push(objGender);
				}
				for(var prop in countUnion){
					seriesUnion.push(countUnion[prop]);
				}

				Highcharts.chart('chartStatus', {
					title: {
						text: 'By Status (雇用形態別)',
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: null
						}
					},
					xAxis: {
						categories: xStatus,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							style: {
								fontSize: '18px'
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
										fetchModal('employ_code', this.category);
									}
								}
							}
						}
					},
					series: [{
						name:'Employees',
						type: 'column',
						colorByPoint: true,
						data: seriesStatus,
						showInLegend: false
					}]
				});

				Highcharts.chart('chartUnion', {
					title: {
						text: 'By Union (労働組合別)',
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: null
						}
					},
					xAxis: {
						categories: xUnion,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							style: {
								fontSize: '18px'
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
										fetchModal('Labour_Union', this.category);
									}
								}
							}
						}
					},
					series: [{
						name:'Person(s)',
						type: 'column',
						colorByPoint: true,
						data: seriesUnion,
						showInLegend: false
					}]
				});

				Highcharts.chart('chartGender', {
					chart: {
						type: 'pie',
					},
					title: {
						text: 'By Gender (性別)',
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: null
						}
					},
					legend:{
						enabled:false
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							borderColor: 'rgb(126,86,134)',
							dataLabels: {
								enabled: true,
								format: '<b style="font-size: 24px;">{point.name}: {point.y} </b>',
								distance: -50,
								style:{
									fontSize:'16px',
									textOutline:0
								},
								color:'black',
							},
							showInLegend: true,
							point: {
								events: {
									click: function () {
										fetchModal('gender', this.name);
									}
								}
							}
						},
						series:{
							animation:{
								duration:false
							}
						}
					},
					credits: {
						enabled:false
					},
					series: [{
						name:'Employees',
						type: 'pie',
						colorByPoint: true,
						data: seriesGender,
						showInLegend: false
					}]
				});

				Highcharts.chart('chartDepartment', {
					title: {
						text: 'By Department (部門別)',
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: null
						}
					},
					xAxis: {
						categories: xDepartment,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							style: {
								fontSize: '18px'
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
										fetchModal('Department', this.category);
									}
								}
							}
						}
					},
					series: [{
						name:'Person(s)',
						type: 'column',
						colorByPoint: true,
						data: seriesDepartment,
						showInLegend: false
					}]
				});

				// Highcharts.chart('chartGrade', {
				// 	title: {
				// 		text: 'By Grade (等級別)',
				// 		style: {
				// 			fontSize: '30px',
				// 			fontWeight: 'bold'
				// 		}
				// 	},
				// 	yAxis: {
				// 		title: {
				// 			text: null
				// 		}
				// 	},
				// 	xAxis: {
				// 		categories: xGrade,
				// 		type: 'category',
				// 		gridLineWidth: 1,
				// 		gridLineColor: 'RGB(204,255,255)',
				// 		labels: {
				// 			style: {
				// 				fontSize: '18px'
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
				// 						fetchModal('grade_code', this.category);
				// 					}
				// 				}
				// 			}
				// 		}
				// 	},
				// 	series: [{
				// 		name:'Person(s)',
				// 		type: 'column',
				// 		colorByPoint: true,
				// 		data: seriesGrade,
				// 		showInLegend: false
				// 	}]
				// });

				Highcharts.chart('chartPosition', {
					title: {
						text: 'By Position (役職別)',
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: null
						}
					},
					xAxis: {
						categories: xPosition,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							style: {
								fontSize: '18px'
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
										fetchModal('pos_name_en', this.category);
									}
								}
							}
						}
					},
					series: [{
						name:'Person(s)',
						type: 'column',
						colorByPoint: true,
						data: seriesPosition,
						showInLegend: false
					}]
				});

				$('#loading').hide();
			}
			else{

				$('#loading').hide();
				alert('Error!', result.message);
			}
		});
}
</script>
@endsection