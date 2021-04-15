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
    font-size: 80px;
    text-align: center;
    font-weight: bold;
  }

  .content-header > h2 {
    margin: 0;
    font-size: 3vw;
    text-align: center;
    font-weight: bold;
  }

  .content-header .isi {
    margin: 0;
    font-size: 150px;
    text-align: center;
    vertical-align: middle;
    font-weight: bold;
  }

  .content-header .keterangan {
    margin: 0;
    font-size: 3vw;
    text-align: center;
    vertical-align: middle;
  }

  .content-wrapper{
  	padding: 0 !important;
  }

  .text-yellow{
  	font-size: 35px !important;
    font-weight: bold;
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
				<div class="col-xs-12" style="padding-top: 0px;margin-top: 10px">
					<div class="col-xs-3" style="padding: 0;padding-left: 10px">
						<div class="box box-solid">
							<div class="box-header" style="background-color: #a31545;">
								<center>
									<span style="font-size: 22px; font-weight: bold; color: white;"><b>INFORMATION</b></span>
								</center>
							</div>
							<table class="table table-responsive" style="height: 250px;border: 0;background:#2a2a2b">
								<tr>
									<th style="vertical-align: top;font-weight: bold;font-size: 1.2vw;color: white;border-top: 1px solid #111">ISP</th>
									<th style="vertical-align: bottom;font-weight: bold;font-size: 1.6vw;border-top: 1px solid #111"><span class="pull-right" id="isp" style="color: orange;"></span>
									</th>
								</tr>
								<tr>
									<th style="vertical-align: top;font-weight: bold;font-size: 1.2vw;color: white;border-top: 1px solid #111">Place</th>
									<th style="vertical-align: bottom;font-weight: bold;font-size: 1.6vw;border-top: 1px solid #111"><span class="pull-right" id="place" style="color: orange;"></span></th>
								</tr>
								<tr>
									<th style="vertical-align: top;font-weight: bold;font-size: 1.2vw;color: white;border-top: 1px solid #111">Address</th>
									<th style="vertical-align: bottom;font-weight: bold;font-size: 1.6vw;border-top: 1px solid #111"><span class="pull-right" id="address" style="color: orange;"></span></th>
								</tr>
							</table>
						</div>
					</div>

					<div class="col-xs-3" style="padding-left: 10px">
						<div class="box box-solid">
							<div class="box-header" style="background-color: #a31545;">
								<center><span style="font-size: 22px; font-weight: bold; color: white;"><b>Ping</b></span></center>
							</div>
							<div id="ping" style="width: 100%;height: 250px;margin: 0;font-size: 100px;text-align: center;vertical-align: middle;font-weight: bold;color: black"></div>
						</div>
					</div>

					<div class="col-xs-3" style="padding-left: 10px;padding-right: 0">
						<div class="box box-solid">
							<div class="box-header" style="background-color: #a31545;">
								<center><span style="font-size: 22px; font-weight: bold; color: white;"><b>Upload</b></span></center>
							</div>
							<div id="upload" style="width: 100%;height: 250px;margin: 0;font-size: 100px;text-align: center;vertical-align: middle;font-weight: bold;"></div>
						</div>
					</div>

					<div class="col-xs-3" style="padding-left: 10px">
						<div class="box box-solid">
							<div class="box-header" style="background-color: #a31545;">
								<center><span style="font-size: 22px; font-weight: bold; color: white;"><b>Download</b></span></center>
							</div>
							<div id="download" style="width: 100%;height: 250px;margin: 0;font-size: 100px;text-align: center;vertical-align: middle;font-weight: bold;"></div>
						</div>
					</div>

				</div>


				<div class="col-md-12" style="padding: 0">
					<div class="box box-solid" style="background-color:#3c3c3c !important; color: white; box-shadow: 0 0 0 0 !important;">
						<div class="box-body" style="padding: 0px">
							<div class="div_name" style="border: 1px solid white; padding-left: 10px; padding-right: 10px; border-radius: 5px;text-align: center;">
								<div style="font-weight: bold; font-size: 30px; display: inline-block; border-radius: 5px;">SpeedTest Today</div>
								<div id="chart_speedtest" style="margin-top: 5px;margin-left: 10px"></div>
							</div>
						</div>
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
		setInterval(postData, 600000);
	});

	function postData() {
		$.get('{{ url("post/server_room/speedtest") }}', function(result, status, xhr){
			if (result.status) {

				categories = [];
				download = [];
				upload = [];
				ping = [];

				$.each(result.speedtest, function(index, value){
					$('#isp').append().empty();
					$('#isp').html(value.service_provider);

					$('#place').append().empty();
					$('#place').html(value.city);

					$('#address').append().empty();
					$('#address').html(value.address);

					$('#download').append().empty();
					$('#download').html(value.download+'<br><span style="font-size:80px;vertical-align: top;line-height: 40px;">Mbps</span>');

					$('#upload').append().empty();
					$('#upload').html(value.upload+'<br><span style="font-size:80px;vertical-align: top;line-height: 40px;">Mbps</span>');

					$('#ping').append().empty();
					$('#ping').html(value.ping+'<br><span style="font-size:80px;vertical-align: top;line-height: 40px;">ms</span>');

					if(parseFloat(value.download) >= 50) {
						$("#download").addClass("bg-green");	
						$("#download").removeClass('bg-orange');	
						$("#download").removeClass('bg-red');

					}
					else if(parseFloat(value.download) >= 25 && parseFloat(value.download) < 50){
						$("#download").addClass("bg-orange");
						$("#download").removeClass('bg-green');	
						$("#download").removeClass('bg-red');
					}
					else if(parseFloat(value.download) < 25){
						$("#download").addClass("bg-red");
						$("#download").removeClass('bg-green');	
						$("#download").removeClass('bg-orange');
					}

					if(parseFloat(value.upload) >= 20) {
						$("#upload").addClass("bg-green");	
						$("#upload").removeClass('bg-orange');	
						$("#upload").removeClass('bg-red');

					}
					else if(parseFloat(value.upload) >= 15 && parseFloat(value.upload) < 20){
						$("#upload").addClass("bg-orange");
						$("#upload").removeClass('bg-green');	
						$("#upload").removeClass('bg-red');
					}
					else if(parseFloat(value.upload) < 15){
						$("#upload").addClass("bg-red");
						$("#upload").removeClass('bg-green');	
						$("#upload").removeClass('bg-orange');
					}

					if(parseFloat(value.ping) > 0 && parseFloat(value.ping) < 10) {
						$("#ping").addClass("bg-green");	
						$("#ping").removeClass('bg-orange');	
						$("#ping").removeClass('bg-red');

					}
					else if(parseFloat(value.ping) > 10){
						$("#ping").addClass("bg-orange");
						$("#ping").removeClass('bg-green');	
						$("#ping").removeClass('bg-red');
					}
					else if(parseFloat(value.ping) == 0){
						$("#ping").addClass("bg-red");
						$("#ping").removeClass('bg-green');	
						$("#ping").removeClass('bg-orange');
					}

					categories.push(value.data_time);
					ping.push(parseFloat(value.ping));
					download.push(parseFloat(value.download));
					upload.push(parseFloat(value.upload));


				})
				Highcharts.chart('chart_speedtest', {
					chart: {
						type: 'spline',
						height: '250px'
					},

					title: {
						text: ''
					},

					yAxis: {
						title: {
							text: 'Speed'
						},
						gridLineWidth: 1,
					},

					xAxis: {
						categories: categories,
						tickInterval: 30
					},

					legend: {
						enabled: true
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
						name: 'Download',
						color: 'blue',
						data: download,
						lineWidth: 3
					},
					{
						name: 'Upload',
						color: 'green',
						data: upload,
						lineWidth: 3
					},
					{
						name: 'Ping',
						data: ping,
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
		})
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#90ee7e', '#f45b5b', '#7798BF', '#e3311e', '#aaeeee', '#ff0066',
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

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '3000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}	



</script>
@endsection