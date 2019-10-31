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
	<div class="row">
		<form method="GET" action="{{ action('MiddleProcessController@indexDisplayMonitoring') }}">
			<div class="col-xs-12" style="padding-bottom: 5px;">
				<div class="row">
					<div class="col-xs-2" style="padding-right: 0;">
						<select class="form-control select2" multiple="multiple" id="locationSelect" data-placeholder="Select Location" style="width: 100%;" onchange="change()">
							@foreach($locs as  $loc)
							<option value="{{$loc->location}}">{{$loc->location}}</option>
							@endforeach
						</select>
						<input type="text" name="location" id="location" hidden>
					</div>
					<div class="col-xs-2">
						<button class="btn btn-success" onclick="fillChart()">Search</button>
					</div>
				</div>
			</div>
		</form>
		<div class="col-xs-12">
			<div id="container" style="width: 100%;"></div>
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
								<th style="width: 3%;">Tag</th>
								<th style="width: 3%;">Material</th>
								<th style="width: 9%;">Description</th>
								<th style="width: 3%;">Location</th>
								<th style="width: 3%;">Quantity</th>
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
		$('#locs').val('');
		$('.select2').select2();
		fillChart();
		setInterval(fillChart, 10000);
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

	function change() {
		$("#location").val($("#locationSelect").val());
	}

	function fillChart(){
		var data = {
			location:"{{$_GET['location']}}"
		}
		
		$.get('{{ url("fetch/middle/display_monitoring") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					var categories = [];
					var series = [];
					var value =  [];
					var over = 0;

					for (var j = 0; j < result.diff.length; j++) {
						if(result.diff[j].diff == 0){
							categories.push(result.diff[j].diff+'Days');
						}else if(result.diff[j].diff <= 5){
							categories.push('<'+result.diff[j].diff+'Days');
						}else{
							categories.push('>5Days');
							break;
						}
					}
					
					for (var i = 0; i < result.loc.length; i++) {
						for (var j = 0; j < result.diff.length; j++) {
							for (var k = 0; k < result.stock.length; k++) {	
								if((result.diff[j].diff == result.stock[k].diff) && (result.loc[i].location == result.stock[k].location)){
									value.push(result.stock[k].jml);	
								}							
							}
						}
						series.push(value);
						value = [];
						over = 0;
					}

					console.log(series);
					console.log(categories);

					var chart = Highcharts.chart('container', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'Realtime Middle Process Stock Monitoring',
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
								enabled: false
							},
							type: 'logarithmic',
							stackLabels: {
								enabled: true,
								style: {
									fontWeight: 'bold',
									color: 'white',
									fontSize: '26px'
								}
							},
						},
						xAxis: {
							categories: categories,
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
							column: {
								stacking: 'normal',
								dataLabels: {
									enabled: true,
								}
							},
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
											fetchModal(this.series.name, this.category);
										}
									}
								}
							}
						},
						series: [{
							name:'Barrel',
							data: series[0],
						},
						{
							name:'LCQ-Incoming',
							data: series[1],
						},
						{
							name:'LCQ-Kensa',
							data: series[2],
						}
						]

					});
				}
			}
		});
	}

	function fetchModal(loc,diff){
		$('#modalDetail').modal('show');
		$('#loading').show();
		$('#modalDetailTitle').html("");
		$('#tableDetail').hide();

		$('#tableDetail').DataTable().destroy();

		var data = {
			loc:loc,
			diff:diff
		}

		$.get('{{ url("fetch/middle/detail_monitoring") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#tableDetailBody').html('');

					var index = 1;
					var resultData = "";

					$.each(result.detail, function(key, value) {
						resultData += '<tr>';
						resultData += '<td>'+ index +'</td>';
						resultData += '<td>'+ value.tag +'</td>';
						resultData += '<td>'+ value.material_number +'</td>';
						resultData += '<td>'+ value.material_description +'</td>';
						resultData += '<td>'+ value.location +'</td>';
						resultData += '<td>'+ value.quantity +'</td>';
						resultData += '</tr>';
						index += 1;
					});
					$('#tableDetailBody').append(resultData);
					$('#modalDetailTitle').html("<center><span style='font-size: 20px; font-weight: bold;'>"+loc+" : "+diff+"</span></center>");
					
					$('#tableDetail').DataTable();
					
					$('#loading').hide();
					$('#tableDetail').show();

				}
			}
		});




	}

</script>
@endsection