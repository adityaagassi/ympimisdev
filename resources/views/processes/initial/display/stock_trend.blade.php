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
		padding: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<input type="hidden" id="location" value="{{ $location }}">
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<div class="col-xs-2" style="padding-right: 0;">
					<select class="form-control select2" multiple="multiple" id='locs' data-placeholder="Select Products" style="width: 100%;">
						@foreach($locs as $loc)
						<option value="{{$loc}}">{{ trim($loc, "'")}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-xs-2">
					<button class="btn btn-info" onclick="fillChart()">Update Chart</button>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div id="container1" style="height: 550px;"></div>
		</div>
		{{-- <div class="col-xs-12">
			<div id="container2" style="height: 400px;"></div>			
		</div> --}}
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
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('#locs').val('');
		$('.select2').select2();

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
					fontFamily: '\'Unica One\', sans-serif'
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

		fillChart();
		// setInterval(fillChart, 10000);
	});

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

	function fillChart(){
		var location = $('#location').val();
		if($('#locs').val() != ""){
			location = $('#locs').val().toString();
		}
		var data = {
			location : location,
		}
		$.get('{{ url("fetch/initial/stock_trend") }}', data, function(result, status, xhr){
			if(result.status){

				var names = [];
				var dataCount = [];
				// var dataPercentage = [];
				var cat;

				$.each(result.stocks, function(key, value) {
					cat = value.category;
					if(names.indexOf(cat) === -1){
						names[names.length] = cat;
					}
				});

				$.each(names, function(key, name){
					var series = [];
					// var series2 = [];
					$.each(result.stocks, function(i, value) {
						if(value.category == name){
							series.push([Date.parse(value.date_stock), parseFloat(value.material)]);
							// series2.push([Date.parse(value.date_stock), parseFloat(value.percentage)]);
						}
					});

					dataCount[key] = {
						name:name,
						data:series
					};

					// dataPercentage[key] = {
					// 	name:name,
					// 	data:series2
					// };
				});

				window.chart = Highcharts.stockChart('container1', {
					// colors: ['rgb(255,0,0)','rgb(255,128,0)','rgb(255,255,0)','rgb(127,0,255)','rgb(0,255,255)','rgb(0,255,0)'],
					rangeSelector: {
						selected: 0
					},
					navigator:{
						enabled:false
					},
					yAxis: {
						title: {
							text: 'Quantity (pcs)'
						}
					},
					title: {
						text: 'M-PRO Daily Stock By Quantity',
						style: {
							fontSize: '20px',
							fontWeight: 'bold'
						}
					},
					xAxis:{
						type: 'datetime',
						tickInterval: 24 * 3600 * 1000
					},
					plotOptions: {
						series: {
							lineWidth: 2,
							label: {
								connectorAllowed: false
							},
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										fetchModal($.date(this.category), this.series.name);
									}
								}
							}
						},
						line: {
							dataLabels: {
								enabled: true
							}
						}
					},
					legend: {
						enabled: true,
						itemStyle: {
							fontSize:'20px'
						}
					},
					credits:{
						enabled:false
					},
					tooltip: {
						pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> pc(s)<br/>',
						split: true
					},
					series: dataCount
				});

				// window.chart = Highcharts.stockChart('container2', {
				// 	// colors: ['rgb(255,0,0)','rgb(255,128,0)','rgb(255,255,0)','rgb(127,0,255)','rgb(0,255,255)','rgb(0,255,0)'],
				// 	rangeSelector: {
				// 		selected: 0
				// 	},
				// 	navigator:{
				// 		enabled:false
				// 	},
				// 	yAxis: {
				// 		title: {
				// 			text: 'Percentage (%)'
				// 		}
				// 	},
				// 	title: {
				// 		text: 'M-PRO Daily Stock By Percentage',
				// 		style: {
				// 			fontSize: '20px',
				// 			fontWeight: 'bold'
				// 		}
				// 	},
				// 	xAxis:{
				// 		type: 'datetime',
				// 		tickInterval: 24 * 3600 * 1000
				// 	},
				// 	plotOptions: {
				// 		series: {
				// 			lineWidth: 2,
				// 			label: {
				// 				connectorAllowed: false
				// 			}
				// 		},
				// 		line: {
				// 			dataLabels: {
				// 				enabled: true
				// 			}
				// 		}
				// 	},
				// 	legend: {
				// 		enabled: true,
				// 		itemStyle: {
				// 			fontSize:'20px'
				// 		}
				// 	},
				// 	credits:{
				// 		enabled:false
				// 	},
				// 	tooltip: {
				// 		pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}%</b> <br/>',
				// 		valueDecimals: 2,
				// 		split: true
				// 	},
				// 	series: dataPercentage
				// });
			}
			else{
				alert('Attempt to retrieve data failed.');
			}
		});

	}

	function fetchModal(date_stock, category){
		$('#modalDetail').modal('show');
		$('#loading').show();
		$('#modalDetailTitle').html("");
		$('#tableDetail').hide();
		var location = $('#location').val();
		if($('#locs').val() != ""){
			location = $('#locs').val().toString();
		}
		var data = {
			date_stock : date_stock,
			location : location,
			category : category
		}
		$.get('{{ url("fetch/initial/stock_trend_detail") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableDetailBody').html('');

				var index = 1;
				var resultData = "";

				$.each(result.stocks, function(key, value) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.material_number +'</td>';
					resultData += '<td>'+ value.description +'</td>';
					resultData += '<td>'+ value.safety_stock.toLocaleString() +'</td>';
					resultData += '<td>'+ value.act_stock.toLocaleString() +'</td>';
					resultData += '<td>'+ value.stock.toFixed(2) +' Day(s)</td>';
					resultData += '</tr>';
					index += 1;
				});
				$('#tableDetailBody').append(resultData);
				$('#modalDetailTitle').html("<center><span style='font-size: 20px; font-weight: bold;'>Date : "+date_stock+"Stock: "+category+"</span></center>");
				$('#loading').hide();
				$('#tableDetail').show();
			}
			else{
				alert('Attempt to retrieve data failed.');
			}
		});
	}
</script>
@endsection