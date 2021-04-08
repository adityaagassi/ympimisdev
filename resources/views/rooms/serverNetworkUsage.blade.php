@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
  .content-header {
    background-color: #61258e !important;
    padding: 10px;
    color: white;
  }

  .content-header > h1 {
    margin: 0;
    font-size: 65px;
    text-align: center;
    font-weight: bold;
  }

  .content-header > h2 {
    margin: 0;
    font-size: 30px;
    text-align: center;
    font-weight: bold;
  }

  .content-header .isi {
    margin: 0;
    font-size: 60px;
    text-align: center;
    vertical-align: middle;
    font-weight: bold;
  }

  .content-wrapper{
  	padding: 0 !important;
  }

  .box-header{
  	text-transform: uppercase;
  }


</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding: 0">
	<div class="row" style="padding: 0">
		<div class="col-xs-12">
			<div class="row">
				<div class="col-md-12 content-header">
	          		<h1>MIRAI SERVER STATUS</h1>
				</div>
				<div class="col-xs-12" style="padding-top: 0px;margin-top: 10px">
					<div class="col-xs-3" style="padding: 0;padding-left: 10px">
						<div class="box box-solid">
							<div class="box-header" style="background-color: #ff851b;">
								<center>
									<span style="font-size: 22px; font-weight: bold; color: black;">SYSTEM INFORMATION</span>
								</center>
							</div>
							<table class="table table-responsive" style="height: 250px;border: 0;background:#2a2a2b">
								<tr>
									<th style="vertical-align: top;font-weight: bold;font-size: 1.2vw;color: white;border-top: 1px solid #111">Hostname</th>
									<th style="vertical-align: bottom;font-weight: bold;font-size: 1.6vw;border-top: 1px solid #111"><span class="pull-right text-green" id="hostname"></span></th>
								</tr>
								<tr>
									<th style="vertical-align: top;font-weight: bold;font-size: 1.2vw;color: white;border-top: 1px solid #111">IP Address</th>
									<th style="vertical-align: bottom;font-weight: bold;font-size: 1.6vw;border-top: 1px solid #111"><span class="pull-right text-green" id="ip_address"></span></th>
								</tr>
								<tr>
									<th style="vertical-align: top;font-weight: bold;font-size: 1.2vw;color: white;border-top: 1px solid #111">Alive Time</th>
									<th style="vertical-align: bottom;font-weight: bold;font-size: 1.2vw;border-top: 1px solid #111"><span class="pull-right text-red" id="uptime"></span></th>
								</tr>
								<tr>
									<th style="vertical-align: top;font-weight: bold;font-size: 1.2vw;color: white;border-top: 1px solid #111">Last Boot</th>
									<th style="vertical-align: bottom;font-weight: bold;font-size: 1.4vw;border-top: 1px solid #111"><span class="pull-right text-red" id="last_boot"></span></th>
								</tr>
							</table>
						</div>
					</div>

					<div class="col-xs-3" style="padding-left: 10px;padding-right: 0">
						<div class="box box-solid">
							<div class="box-header" style="background-color: #ff851b;">
								<center><span style="font-size: 22px; font-weight: bold; color: black;">Memory Usage</span></center>
							</div>
							<div id="container_memory" style="width: 100%;height: 250px;"></div>
						</div>
					</div>

					<div class="col-xs-6" style="padding-left: 10px">
						<div class="box box-solid">
							<div class="box-header" style="background-color: #ff851b;">
								<center><span style="font-size: 22px; font-weight: bold; color: black;">Hardisk Status</span></center>
							</div>
							<div id="container_hardisk" style="width: 100%;height: 250px;"></div>
						</div>
					</div>
				</div>
				<div class="col-xs-12" style="padding-left: 25px;padding-right: 30px;">
					<div class="box box-solid">
						<div class="box-header" style="background-color: #ff851b;">
							<center>
								<span style="font-size: 22px; font-weight: bold; color: black;">Network Usage</span>
							</center>
						</div>
						<table class="table table-responsive" style="height: 200px;background-color: #2a2a2b">
							<tr style="border: 1px solid white">
								<td width="33%" style="text-align: center;vertical-align: middle;">
									<span style="font-weight: bold; color: #d1a8ff; font-size: 50px;">Received</span><br>
									<span id="time_receive" style="font-size: 40px;color:#fff"></span>
								</td>
								<td width="33%" style="text-align: center;vertical-align: middle;">
									<span style="font-weight: bold; color: #d1a8ff; font-size: 50px;">Sent</span><br>
									<span id="time_sent" style="font-size: 40px;color:#fff"></span>
								</td>
								<td width="33%" style="text-align: center;vertical-align: middle;;">
									<span style="font-weight: bold; color: #d1a8ff; font-size: 50px;">Error</span><br>
									<span id="time_error" style="font-size: 40px;color:#fff"></span>
								</td>
								<td width="55%"><div id="chart_co1" style="height: 200px"></div></td>
							</tr>
						</table>
					</div>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		postData();

		setInterval(postData, 60000);
	
	});

	function postData() {
		$.get('{{ url("post/server_room/network_usage") }}', function(result, status, xhr){
			if (result.status) {

				$('#hostname').append().empty();
				$('#hostname').html(result.last_data[0].hostname);

				$('#ip_address').append().empty();
				$('#ip_address').html(result.last_data[0].ip);

				$('#uptime').append().empty();
				$('#uptime').html(result.last_data[0].uptime);

				$('#last_boot').append().empty();
				$('#last_boot').html(result.last_data[0].last_boot);


				$('#time_receive').append().empty();
				$('#time_receive').html(result.last_data[0].received+" Gib");

				$('#time_sent').append().empty();
				$('#time_sent').html(result.last_data[0].sent+" Gib");

				$('#time_error').append().empty();
				$('#time_error').html(result.last_data[0].err);

				var memory_used = 0;
				var memory_free = 0;
				
				var hardisk_used = 0;
				var hardisk_free = 0;

				memory_used = parseFloat(result.memory_used.toFixed(2));
				memory_free = parseFloat(result.memory_free.toFixed(2));

				hardisk_used = parseFloat(result.hardisk_used.toFixed(2));
				hardisk_free = parseFloat(result.hardisk_free.toFixed(2));

				Highcharts.chart('container_memory', {
				    chart: {
				        plotBackgroundColor: null,
				        plotBorderWidth: null,
				        plotShadow: false,
				        type: 'pie'
				    },
				    title: {
				        text: null
				    },
				    tooltip: {
				        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
				    },
				    accessibility: {
				        point: {
				            valueSuffix: '%'
				        }
				    },
				    plotOptions: {
				        pie: {
				            allowPointSelect: true,
				            cursor: 'pointer',
				            dataLabels: {
				                enabled: true,
				                format: '<b>{point.name}</b>: {point.y} Gib'
				            },
				            animation: false,
				        }
				    },
				    credits: {
						enabled: false
					},
					exporting: {
						enabled: false
					},
				    series: [{
				        name: 'Memory',
				        data: [{
				            name: 'Usage',
				            y: memory_used,
							color: "#a83232"
				        }, {
				            name: 'Free',
				            y: memory_free,
							color:'#32a852'
				        }]
				    }]
				});

				Highcharts.chart('container_hardisk', {
				    chart: {
				        plotBackgroundColor: null,
				        plotBorderWidth: null,
				        plotShadow: false,
				        type: 'pie'
				    },
				    title: {
				        text: null
				    },
				    tooltip: {
				        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
				    },
				    accessibility: {
				        point: {
				            valueSuffix: '%'
				        }
				    },
				    plotOptions: {
				        pie: {
				            allowPointSelect: true,
				            cursor: 'pointer',
				            dataLabels: {
				                enabled: true,
				                format: '<b>{point.name}</b>: {point.y} Gib'
				            },
				            animation: false,
				        }
				    },
				    credits: {
						enabled: false
					},
					exporting: {
						enabled: false
					},
				    series: [{
				        name: 'Hardisk',
				        data: [{
				            name: 'Usage',
				            y: hardisk_used,
							color: "#a83232"
				        }, {
				            name: 'Free',
				            y: hardisk_free,
							color:'#32a852'
				        }]
				    }]
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
		colors: ['#90ee7e', '#2b908f', '#eeaaee', '#ec407a', '#7798BF', '#f45b5b',
		'#ff9800', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: {
				linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
				stops: [
				[0, '#2a2a2b'],
				[1, '#2a2a2b']
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