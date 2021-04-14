@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
  .content-header {
    background-color: #2e7d32 !important;
    padding: 10px;
    color: white;
  }

  .content-header > h1 {
    margin: 0;
    font-size: 50px;
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
				<div class="col-md-6" style="padding: 0;padding-left: 10px">
					<div class="col-md-12 content-header" style="border: 1px solid white">
	          			<h1>MIRAI DB</h1>
					</div>
					<div class="col-md-4" style="padding:0">
						<div id="container_memory_mirai_db" style="width: 100%;height: 250px;"></div>
					</div>
					<div class="col-md-8" style="padding:0">
						<div id="chart_internet_mirai_db" style="width: 100%;height: 250px"></div>
					</div>
				</div>

				<div class="col-xs-6" style="padding-left: 10px">
					<div class="col-md-12 content-header" style="border: 1px solid white">
	          			<h1>YMPISERVER</h1>
					</div>
					<div class="col-md-4" style="padding:0">
						<div id="container_memory_ympiserver" style="width: 100%;height: 250px;"></div>
					</div>
					<div class="col-md-8" style="padding:0">
						<div id="chart_internet_ympiserver" style="width: 100%;height: 250px;"></div>
					</div>
				</div>

				<div class="col-xs-6" style="padding: 0;padding-left: 10px">
					<div class="col-md-12 content-header" style="border: 1px solid white">
	          			<h1>SUNFISH DB</h1>
					</div>
					<div class="col-md-4" style="padding:0">
						<div id="container_memory_sunfish_db" style="width: 100%;height: 250px;"></div>
					</div>
					<div class="col-md-8" style="padding:0">
						<div id="chart_internet_sunfish_db" style="width: 100%;height: 250px;"></div>
					</div>
				</div>

				<div class="col-xs-6" style="padding-left: 10px">
					<div class="col-md-12 content-header" style="border: 1px solid white">
	          			<h1>REPORTMAN SERVER</h1>
					</div>
					<div class="col-md-4" style="padding:0">
						<div id="container_memory_reportman" style="width: 100%;height: 250px;"></div>
					</div>
					<div class="col-md-8" style="padding:0">
						<div id="chart_internet_reportman" style="width: 100%;height: 250px"></div>
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

		setInterval(postData, 120000);
	});

	function postData() {

		var ip = ['10.109.52.2','10.109.52.1','10.109.52.9','10.109.48.3'];
		var remark = ['mirai db','ympiserver','sunfish db','reportman'];

		for (var i = 0; i < ip.length; i++) {
			var url = '{{ url("fetch/display/fetch_hit") }}'+'/'+ip[i];

			var no = 0;
			$.get(url, function(result, status, xhr){
				var time;
				if (result.sta == 0) {
					if (result.output.length == 8) {
						timearray = /time\=(.*)?ms|time\<(.*)?ms /g.exec(result.output[2]);
						if(timearray[1] != undefined){
							time = timearray[1];
						}else if(timearray[2] != undefined){
							time = timearray[2];
						}
						status = "Alive";
					}
					else{
						time = 0;
						status = "Host Unreachable";
					}
				}
				else{
					time = 0;
					status = "Timed Out";
				}

				var data = {	
					ip : ip[no],
					remark : remark[no],
					hasil_hit : time,
					status : status
				}

				no++;

				$.post('{{ url("post/display/ip_log") }}', data, function(result, status, xhr){
					if(result.status){
						// openSuccessGritter("Success","IP Log Created");
					} else {
						openErrorGritter('Error',result.message);
					}
				});
			});
		}

		

		$.get('{{ url("post/server_room/all_app_status") }}', function(result, status, xhr){
			if (result.status) {

				ping_mirai_db = [];
				time_ping_mirai_db = [];
				var hardisk_used_mirai_db = 0;
				var hardisk_free_mirai_db = 0;

				ping_ympiserver = [];
				time_ping_ympiserver = [];
				var hardisk_used_ympiserver = 0;
				var hardisk_free_ympiserver = 0;

				ping_sunfish_db = [];
				time_ping_sunfish_db = [];
				var hardisk_used_sunfish_db = 0;
				var hardisk_free_sunfish_db = 0;

				ping_reportman = [];
				time_ping_reportman = [];
				var hardisk_used_reportman = 0;
				var hardisk_free_reportman = 0;

				hardisk_used_mirai_db = parseFloat(result.hardisk_used_mirai_db.toFixed(2));
				hardisk_free_mirai_db = parseFloat(result.hardisk_free_mirai_db.toFixed(2));

				hardisk_used_ympiserver = parseFloat(result.hardisk_used_ympiserver.toFixed(2));
				hardisk_free_ympiserver = parseFloat(result.hardisk_free_ympiserver.toFixed(2));

				hardisk_used_sunfish_db = parseFloat(result.hardisk_used_sunfish_db.toFixed(2));
				hardisk_free_sunfish_db = parseFloat(result.hardisk_free_sunfish_db.toFixed(2));

				hardisk_used_reportman = parseFloat(result.hardisk_used_reportman.toFixed(2));
				hardisk_free_reportman = parseFloat(result.hardisk_free_reportman.toFixed(2));

				$.each(result.data_ping, function(index, value){
					if (value.remark == "mirai db") {
						ping_mirai_db.push(value.data_time);
						time_ping_mirai_db.push(value.time);						
					}
					else if (value.remark == "ympiserver") {
						ping_ympiserver.push(value.data_time);
						time_ping_ympiserver.push(value.time);						
					}
					else if (value.remark == "sunfish db") {
						ping_sunfish_db.push(value.data_time);
						time_ping_sunfish_db.push(value.time);						
					}
					else if (value.remark == "reportman") {
						ping_reportman.push(value.data_time);
						time_ping_reportman.push(value.time);						
					}
				})

				Highcharts.chart('container_memory_mirai_db', {
				    chart: {
				        plotBackgroundColor: null,
				        plotBorderWidth: null,
				        plotShadow: false,
				        type: 'pie'
				    },
				    title: {
				        text: 'Hardisk<br>Capacity',
				        align: 'center',
				        verticalAlign: 'middle',
				        y: 20,
				        style: {
		                    fontWeight: 'bold',
		                    fontSize: '14px'
		                }
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
  								padding: 0,
          						allowOverlap: true,
				                enabled: true,
				                distance: -30,
				                format: '{point.y} Gib',
				                style: {
				                    fontWeight: 'bold',
				                    color: 'white'
				                }
				            },
				            startAngle: -90,
				            endAngle: 90,
				            center: ['50%', '75%'],
				            size: '110%'
				        }
				    },
				    credits: {
						enabled: false
					},
					exporting: {
						enabled: false
					},
				    series: [{
        				type: 'pie',
				        name: 'Hardisk',
       					innerSize: '50%',
				        data: [{
				            name: 'Usage',
				            y: hardisk_used_mirai_db,
							color: "#a83232"
				        }, {
				            name: 'Free',
				            y: hardisk_free_mirai_db,
							color:'#32a852'
				        }]
				    }]
				});

				Highcharts.chart('chart_internet_mirai_db', {
					chart: {
						type: 'spline',
						height: '250px'
					},

					title: {
						text: 'REPLY ROUND TRIP TIME (ms)'
					},

					yAxis: {
						title: {
							text: 'Time (ms)'
						},
						gridLineWidth: 1,
					},

					xAxis: {
						categories: ping_mirai_db,
						tickInterval: 1
					},

					legend: {
						enabled: false
					},

					credits:{
						enabled:false
					},

					exporting: {
						enabled: false
					},

					plotOptions: {
						series: {
							label: {
								connectorAllowed: false
							},
							marker: {
								enabled: false
							},
							animation: false,
						},
						spline: {
							dataLabels: {
								enabled: true,
								formatter: function(){
									var isLast = false;
									if(this.point.x === this.series.data[this.series.data.length -1].x && this.point.y === this.series.data[this.series.data.length -1].y) isLast = true;
									if (isLast) {
										return this.x;
									} else {
										return '';
									}
								},
								allowOverlap: true
							},
						}
					},

					series: [
					{
						name: 'Time',
						data: time_ping_mirai_db,
						color: '#901aeb',
						lineWidth: 3
					}],

					responsive: {
						rules: [{
							condition: {
								maxWidth: 500
							},
						}]
					}

				});


				Highcharts.chart('container_memory_ympiserver', {
				    chart: {
				        plotBackgroundColor: null,
				        plotBorderWidth: null,
				        plotShadow: false,
				        type: 'pie'
				    },
				    title: {
				        text: 'Hardisk<br>Capacity',
				        align: 'center',
				        verticalAlign: 'middle',
				        y: 20,
				        style: {
		                    fontWeight: 'bold',
		                    fontSize: '14px'
		                }
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
  								padding: 0,
          						allowOverlap: true,
				                enabled: true,
				                format: '{point.y} Tib',
				                distance: -30,
				            },
				            startAngle: -90,
				            endAngle: 90,
				            center: ['50%', '75%'],
				            size: '110%',
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
        				type: 'pie',
				        name: 'Hardisk',
       					innerSize: '50%',
				        data: [{
				            name: 'Usage',
				            y: hardisk_used_ympiserver,
							color: "#a83232"
				        }, {
				            name: 'Free',
				            y: hardisk_free_ympiserver,
							color:'#32a852'
				        }]
				    }]
				});

				Highcharts.chart('chart_internet_ympiserver', {
					chart: {
						type: 'spline',
						height: '250px'
					},

					title: {
						text: 'REPLY ROUND TRIP TIME (ms)'
					},

					yAxis: {
						title: {
							text: 'Time (ms)'
						},
						gridLineWidth: 1,
					},

					xAxis: {
						categories: ping_ympiserver,
						tickInterval: 1
					},

					legend: {
						enabled: false
					},

					credits:{
						enabled:false
					},

					exporting: {
						enabled: false
					},

					plotOptions: {
						series: {
							label: {
								connectorAllowed: false
							},
							marker: {
								enabled: false
							},
							animation: false,
						},
						spline: {
							dataLabels: {
								enabled: true,
								formatter: function(){
									var isLast = false;
									if(this.point.x === this.series.data[this.series.data.length -1].x && this.point.y === this.series.data[this.series.data.length -1].y) isLast = true;
									if (isLast) {
										return this.x;
									} else {
										return '';
									}
								},
								allowOverlap: true
							},
						}
					},

					series: [
					{
						name: 'Time',
						data: time_ping_ympiserver,
						color: '#901aeb',
						lineWidth: 3
					}],

					responsive: {
						rules: [{
							condition: {
								maxWidth: 500
							},
						}]
					}

				});

				Highcharts.chart('container_memory_sunfish_db', {
				    chart: {
				        plotBackgroundColor: null,
				        plotBorderWidth: null,
				        plotShadow: false,
				        type: 'pie'
				    },
				    title: {
				        text: 'Hardisk<br>Capacity',
				        align: 'center',
				        verticalAlign: 'middle',
				        y: 20,
				        style: {
		                    fontWeight: 'bold',
		                    fontSize: '14px'
		                }
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
  								padding: 0,
          						allowOverlap: true,
				                enabled: true,
				                format: '{point.y} Gib',
				                distance: -30,
				            },
				            startAngle: -90,
				            endAngle: 90,
				            center: ['50%', '75%'],
				            size: '110%',
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
        				type: 'pie',
				        name: 'Hardisk',
       					innerSize: '50%',
				        data: [{
				            name: 'Usage',
				            y: hardisk_used_sunfish_db,
							color: "#a83232"
				        }, {
				            name: 'Free',
				            y: hardisk_free_sunfish_db,
							color:'#32a852'
				        }]
				    }]
				});

				Highcharts.chart('chart_internet_sunfish_db', {
					chart: {
						type: 'spline',
						height: '250px'
					},

					title: {
						text: 'REPLY ROUND TRIP TIME (ms)'
					},

					yAxis: {
						title: {
							text: 'Time (ms)'
						},
						gridLineWidth: 1,
					},

					xAxis: {
						categories: ping_sunfish_db,
						tickInterval: 1
					},

					legend: {
						enabled: false
					},

					credits:{
						enabled:false
					},

					exporting: {
						enabled: false
					},

					plotOptions: {
						series: {
							label: {
								connectorAllowed: false
							},
							marker: {
								enabled: false
							},
							animation: false,
						},
						spline: {
							dataLabels: {
								enabled: true,
								formatter: function(){
									var isLast = false;
									if(this.point.x === this.series.data[this.series.data.length -1].x && this.point.y === this.series.data[this.series.data.length -1].y) isLast = true;
									if (isLast) {
										return this.x;
									} else {
										return '';
									}
								},
								allowOverlap: true
							},
						}
					},

					series: [
					{
						name: 'Time',
						data: time_ping_sunfish_db,
						color: '#901aeb',
						lineWidth: 3
					}],

					responsive: {
						rules: [{
							condition: {
								maxWidth: 500
							},
						}]
					}

				});

				Highcharts.chart('container_memory_reportman', {
				    chart: {
				        plotBackgroundColor: null,
				        plotBorderWidth: null,
				        plotShadow: false,
				        type: 'pie'
				    },
				    title: {
				        text: 'Hardisk<br>Capacity',
				        align: 'center',
				        verticalAlign: 'middle',
				        y: 20,
				        style: {
		                    fontWeight: 'bold',
		                    fontSize: '14px'
		                }
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
  								padding: 0,
          						allowOverlap: true,
				                enabled: true,
				                format: '{point.y} Gib',
				                distance: -30,
				            },
				            startAngle: -90,
				            endAngle: 90,
				            center: ['50%', '75%'],
				            size: '110%',
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
        				type: 'pie',
				        name: 'Hardisk',
       					innerSize: '50%',
				        data: [{
				            name: 'Usage',
				            y: hardisk_used_reportman,
							color: "#a83232"
				        }, {
				            name: 'Free',
				            y: hardisk_free_reportman,
							color:'#32a852'
				        }]
				    }]
				});

				Highcharts.chart('chart_internet_reportman', {
					chart: {
						type: 'spline',
						height: '250px'
					},

					title: {
						text: 'REPLY ROUND TRIP TIME (ms)'
					},

					yAxis: {
						title: {
							text: 'Time (ms)'
						},
						gridLineWidth: 1,
					},

					xAxis: {
						categories: ping_reportman,
						tickInterval: 1
					},

					legend: {
						enabled: false
					},

					credits:{
						enabled:false
					},

					exporting: {
						enabled: false
					},

					plotOptions: {
						series: {
							label: {
								connectorAllowed: false
							},
							marker: {
								enabled: false
							},
							animation: false,
						},
						spline: {
							dataLabels: {
								enabled: true,
								formatter: function(){
									var isLast = false;
									if(this.point.x === this.series.data[this.series.data.length -1].x && this.point.y === this.series.data[this.series.data.length -1].y) isLast = true;
									if (isLast) {
										return this.x;
									} else {
										return '';
									}
								},
								allowOverlap: true
							},
						}
					},

					series: [
					{
						name: 'Time',
						data: time_ping_reportman,
						color: '#901aeb',
						lineWidth: 3
					}],

					responsive: {
						rules: [{
							condition: {
								maxWidth: 500
							},
						}]
					}

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