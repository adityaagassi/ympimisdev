@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.content{
		color: white;
		font-weight: bold;
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
		border:1px solid white;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		color: black;
		background-color: white;
	}
	table.table-bordered > tbody > tr > th{
		border:1px solid black;
		vertical-align: middle;
		text-align: center;
		padding:2px;
		background-color: white;
		color: black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid white;
		vertical-align: middle;
		padding:2px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid white;
		padding:2px;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	.color {
		-webkit-animation: colors 1s infinite;  /* Safari 4+ */
		-moz-animation: colors 1s infinite;  /* Fx 5+ */
		-o-animation: colors 1s infinite;  /* Opera 12+ */
		animation: colors 1s infinite;  /* IE 10+, Fx 29+ */
	}
	
	@-webkit-keyframes colors {
		0%, 49% {
			background: rgba(0, 0, 0, 0);
			/*opacity: 0;*/
		}
		50%, 100% {
			background-color: #f55656;
		}
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 5px;">
			<div id="chart"></div>

			<!-- <table class="table table-bordered table-stripped" id="logs">
				<thead>
					<tr>
						<th>Material Number</th>
						<th>Model and Key</th>
						<th>Descriptions</th>
						<th>Quantity</th>
						<th>Kanban Quantity</th>
					</tr>
				</thead>
				<tbody id="tbody">
				</tbody>
			</table> -->

			<table id="assyTable" class="table table-bordered" style="padding: 0px; margin-bottom: 0px; font-size: 1vw">
				<tr id="modelAll">
					<!-- <th>#</th> -->
				</tr>
				<tr id="quantity">
					<!-- <th>Total Quantity</th> -->
				</tr>
				<tr id="quantity_kanban">
					<!-- <th>Total Qty Kanban</th> -->
				</tr>
			</table>
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
		drawTable();
		setInterval(drawTable, 2000);
	});

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#8dff69', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
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
					color: '#ddd'
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


	function drawTable() {
		var data = {
			option:"{{$option}}"
		}

		$.get('{{ url("fetch/middle/request/") }}', data, function(result, status, xhr){
			if(result.status){
				$("#modelAll").empty();
				$("#quantity").empty();
				$("#quantity_kanban").empty();

				var material_req = [];
				var cat = [];
				var limit = [];
				var style = "";

				model = "<th style='width:45px'>#</th>";
				quantity = "<th>Qty</th>";
				quantity_kanban = "<th>Qty Kanban</th>";

				$.each(result.datas, function(index, value){
					if ((value.quantity / value.lot_transfer) >= 2) {
						style = 'class="color"';
					} else {
						style = '';
					}

					if (value.quantity > 0) {
						model += "<th>"+value.model+" "+value.key+"</th>";
						quantity += "<td "+style+">"+value.quantity+"</td>";
						quantity_kanban += "<td "+style+">"+(value.quantity / value.lot_transfer)+"</td>";
					}

					if (value.quantity >= (value.lot_transfer*2)) {

					}

					if (value.quantity >= value.lot_transfer) {
						cat.push(value.model+" "+value.key);
						material_req.push((value.quantity / value.lot_transfer));
						limit.push(2);	
					}
				})

				$("#modelAll").append(model);
				$("#quantity").append(quantity);
				$("#quantity_kanban").append(quantity_kanban);


				//CHART
				Highcharts.chart('chart', {
					chart: {
						backgroundColor: null,
						type: 'column',
					},
					exporting: { enabled: false },
					title: {
						text: null
					},
					tooltip: {
						pointFormat: 'Quantity: <b>{point.y} Kanban</b>'
					},
					xAxis: {
						labels: {
							style: {
								color: '#9dff69',
								fontSize: '12px',
								fontWeight: 'bold'
							}
						},
						categories: cat
					},
					yAxis: {
						tickInterval: 1,
						title: {
							text: 'Quantity (Kanban)'
						},
						plotLines: [{
							color: 'red',
							width: 2,
							value: 2,
							zIndex: 5,
							dashStyle: 'Dash'
						}]
					},
					plotOptions: {
						column: {
							allowPointSelect: true,
							borderColor: 'black',
							dataLabels: {
								enabled: true,
								format: '<b>{point.name}<br/>{point.y}</b>',
								distance: -50,
								style:{
									fontSize:'14px',
									textOutline:0,
								},
							},
							zones: [{
								value: 2, 
								color: '#46e83a' 
							},{
								color: '#f55656' 
							}]
						}, 
						series: {
							animation: false
						}
					},
					credits: {
						enabled: false
					},
					series: [{
						name: 'Material',
						data: material_req
					}]
				});
			}
		})
	}


	// function drawTable() {
	// 	var data = {
	// 		option:"{{$option}}"
	// 	}

	// 	$.get('{{ url("fetch/middle/request/") }}', data, function(result, status, xhr){
	// 		if(result.status){
	// 			var tableData = "";
	// 			$('#logs').DataTable().destroy();
	// 			$("#tbody").empty();
	// 			var material_req = [];
	// 			var cat = [];
	// 			var limit = [];

	// 			$.each(result.datas, function(index, value) {

	// 				if (value.quantity >= (value.lot_transfer*2)) {

	// 				}

	// 				tableData += "<tr>";
	// 				tableData += "<td>"+value.material_number+"</td>";
	// 				tableData += "<td>"+value.model+" "+value.key+"</td>";
	// 				tableData += "<td>"+value.material_description+"</td>";
	// 				tableData += "<td>"+value.quantity+"</td>";
	// 				tableData += "<td>"+(value.quantity / value.lot_transfer)+"</td>";
	// 				tableData += "</tr>";

	// 				if (value.quantity >= value.lot_transfer) {
	// 					cat.push(value.model+" "+value.key);
	// 					material_req.push((value.quantity / value.lot_transfer));
	// 					limit.push(2);	
	// 				}

	// 			})

	// 			$("#tbody").append(tableData);
	// 			$('#logs').DataTable({
	// 				"paging": true,
	// 				'searching': false,
	// 				'responsive':true,
	// 				'lengthMenu': [
	// 				[ 10, 25, 50, -1 ],
	// 				[ '10 rows', '25 rows', '50 rows', 'Show all' ]
	// 				],
	// 				'ordering': false,
	// 				'lengthChange': false,
	// 				'info': false,
	// 				'sPaginationType': "full_numbers",
	// 				"columnDefs": [ {
	// 					"targets": 0,
	// 					"orderable": false
	// 				} ]
	// 			});

			// }
	// 	})
	// }

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true
	});


</script>
@endsection