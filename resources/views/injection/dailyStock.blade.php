@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150);
		text-align: center;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
		padding:0;
		font-size: 12px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		padding:0;
		vertical-align: middle;
		text-align: center;
	}
	.content{
		color: white;
		font-weight: bold;
	}
	.progress {
		background-color: rgba(0,0,0,0);
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<!-- <form method="GET" action="{{ action('InjectionsController@getDailyStock') }}"> -->
					<div class="col-xs-2">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Select Date">
						</div>
					</div>
					<div class="col-xs-3">
						<div class="form-group">
							<select class="form-control select2"  id="locationSelect" data-placeholder="Select Location" onchange="change()">
								<option value="Blue">YRS Blue</option>
								<option value="Green">YRS Green</option>
								<option value="Pink">YRS Pink</option>
								<option value="Red">YRS Red</option>
								<option value="Brown">YRS Brown</option>
								<option value="Ivory">YRS Ivory</option>
								<option value="Yrf">YRF</option>
							</select>
							<input type="text" name="location" id="location" hidden>
						</div>
					</div>
					<div class="col-xs-1">
						<div class="form-group">
							<button class="btn btn-success" type="button" onclick="fillTable()">Update Chart</button>
						</div>
					</div>
				<!-- </form> -->
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 2vw;"></div>
			</div>
			<div class="col-xs-12" style="padding: 0px; margin-top: 0;">
				<div id="container" style="height: 690px;"></div>
			</div>
		</div>
	</div>

</section>
@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('.select2').select2();
		fillTable();
		setInterval(fillTable, 30000);
	});

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

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		viewMode: "months", 
 		minViewMode: "months",
		autoclose: true,
		format: "yyyy-mm",
		endDate: '<?php echo $tgl_max ?>'
	});

	function fillTable() {
		var tgl1 = $('#tanggal').val();
		var location = $('#location').val();
		var data = {
			tgl:tgl1,
			location:location
		}

		$.get('{{ url("fetch/dailyStock") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					
					var categories = [];
					var series0 = [];
					var series1 = [];
					var series2 = [];
					var series3 = [];
					var series4 = [];

					var seriesP0 = "";
					var seriesP1 = "";
					var seriesP2 = "";
					var seriesP3 = "";
					var seriesP4 = "";

					for(i = 0; i < result.part.length; i++){
					if ( location == "Yrf" || location == "Brown" ) {
						if ( result.part[i].part == result.model[0].part) {
						categories.push(result.part[i].week_date);
						series0.push(parseInt(result.part[i].stock));
						seriesP0 = result.model[0].part;
						}

						if ( result.part[i].part == result.model[1].part) {
						series1.push(parseInt(result.part[i].stock));
						seriesP1 = result.model[1].part;
						}

						if ( result.part[i].part == result.model[2].part) {
						series2.push(parseInt(result.part[i].stock));
						seriesP2 = result.model[2].part;
						}
					}else if ( location == "Red" ) {
						if ( result.part[i].part == result.model[0].part) {
						categories.push(result.part[i].week_date);
						series0.push(parseInt(result.part[i].stock));
						seriesP0 = result.model[0].part;
						}

						if ( result.part[i].part == result.model[1].part) {
						series1.push(parseInt(result.part[i].stock));
						seriesP1 = result.model[1].part;
						}

						if ( result.part[i].part == result.model[2].part) {
						series2.push(parseInt(result.part[i].stock));
						seriesP2 = result.model[2].part;
						}
						if ( result.part[i].part == result.model[3].part) {
						series3.push(parseInt(result.part[i].stock));
						seriesP3 = result.model[3].part;
						}
					}else{
						if ( result.part[i].part == result.model[0].part) {
						categories.push(result.part[i].week_date);
						series0.push(parseInt(result.part[i].stock));
						seriesP0 = result.model[0].part;
						}

						if ( result.part[i].part == result.model[1].part) {
						series1.push(parseInt(result.part[i].stock));
						seriesP1 = result.model[1].part;
						}

						if ( result.part[i].part == result.model[2].part) {
						series2.push(parseInt(result.part[i].stock));
						seriesP2 = result.model[2].part;
						}

						if ( result.part[i].part == result.model[3].part) {
						series3.push(parseInt(result.part[i].stock));
						seriesP3 = result.model[3].part;
						}

						if ( result.part[i].part == result.model[4].part) {
						series4.push(parseInt(result.part[i].stock));
						seriesP4 = result.model[4].part;
						}
					}

					}

					


					Highcharts.chart('container', {
					    chart: {
					        type: 'column'
					    },
					    title: {
							text: 'Daily Stock After Injection',
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
					    xAxis: {
					        categories: categories,
					        crosshair: true
					    },
					    yAxis: {
					        min: 0,
					        title: {
					            text: 'Pc'
					        }
					    },
					    tooltip: {
					        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
					        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					            '<td style="padding:0"><b>{point.y:.1f} pc</b></td></tr>',
					        footerFormat: '</table>',
					        shared: true,
					        useHTML: true
					    },
					    plotOptions: {
					        column: {
					            pointPadding: 0.2,
					            borderWidth: 0
					        }
					    },
					    series: [{

      						animation: false,
					        name: seriesP0,
					        data: series0

					    }, {

      						animation: false,
					        name: seriesP1,
					        data: series1

					    }, {

      						animation: false,
					        name: seriesP2,
					        data: series2

					    }, 

					    {

      						animation: false,
					        name: seriesP3,
					        data: series3

					    },{

      						animation: false,
					        name: seriesP4,
					        data: series4,
					        // if (location == "Red") {
					        // 	visible: false
					        // }
					    }]
					});
				}
			}
		});
	}

</script>
@stop