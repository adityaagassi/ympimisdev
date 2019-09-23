@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.content{
		color: white;
		font-weight: bold;
	}
	#loading, #error { display: none; }

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-2">
				<div class="input-group date">
					<div class="input-group-addon bg-green" style="border: none;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="tanggal" placeholder="Select Date">
				</div>
			</div>
			<div class="col-xs-2" style="padding-right: 0;">
				<select class="form-control select2" multiple="multiple" id='origin_group' data-placeholder="Select Products" style="width: 100%;">
					@foreach($origin_groups as $origin_group)
					<option value="{{ $origin_group->origin_group_code }}-{{ $origin_group->origin_group_name }}">{{ $origin_group->origin_group_name }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-xs-2">
				<button class="btn btn-success" onclick="fillChart()">Update Chart</button>
			</div>
			<div class="pull-right" id="location_title" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 2vw;"></div>
		</div>
		<div class="col-xs-12">
			<!-- <div id="wait" class="loading">
				<div>
					<center>
						<i class="fa fa-spinner fa-5x fa-spin" style="color: white;"></i><br>
						<h2 style="margin: 0px; color: white;">Loading . . .</h2>
					</center>
				</div>
			</div> -->
			<div id="container" style="style: 100%;"></div>
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
		$('.select2').select2();
		fillChart();
		setInterval(fillChart, 30000);
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: "dd-mm-yyyy",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
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

	function fillChart() {
		// $("#wait").show();
		// $("#container").hide();
		var hpl = $('#origin_group').val();
		var tanggal = $('#tanggal').val();

		console.log(hpl);

		var location_title = "";
		if(hpl.length > 1){
			for(var i = 0; i < hpl.length; i++){
				location_title += hpl[i].replace('-',' ');
				if(i == hpl.length-2){
					location_title += " & ";
				}else if(i != hpl.length-1){
					location_title += ", ";
				}
			}
		}else if(hpl.length == 1){ 
			location_title += hpl[0].replace('-',' ');
		}


		var data = {
			tanggal:tanggal,
			code:hpl
		}

		$.get('{{ url("fetch/middle/buffing_performance") }}', data, function(result, status, xhr) {
			// $("#wait").hide();
			if(result.status){
				// $("#container").show();
				$('#location_title').html('<b>'+ location_title +'</b>');

				var id = [];
				var op_name = [];
				var ng = [];

				for(var i = 0; i < result.ng_logs.length; i++){
					id.push(result.ng_logs[i].operator_id);
					op_name.push(result.ng_logs[i].name);
					ng.push(result.ng_logs[i].jml_ng);
				}

				var total = [];

				for(var i = 0; i < id.length; i++){
					var sum = 0;
					for(var j = 0; j < result.rfid.length; j++){
						if(id[i] == result.rfid[j].operator_id){
							sum += result.rfid[j].jml_buff;
						}
						if(j == result.rfid.length-1){
							total.push(sum);
						}
					}
				}

				var ng_rate = [];
				for(var i = 0; i < id.length; i++){
					ng_rate.push([(ng[i]/total[i])*100,op_name[i]]);
				}

				data = ng_rate.sort(function(a, b){return b[0]-a[0]});

				data_x = [];
				data_series = [];
				for (var i = 0; i < data.length; i++) {
					data_series.push(data[i][0]);
					data_x.push(data[i][1]);
				}

				var chart = Highcharts.chart('container', {
					title: {
						text: 'Operators Performance',
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
							text: 'NG Rate (%)'
						},
					},
					xAxis: {
						categories: data_x,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							style: {
								fontSize: '26px'
							}
						},
					},
					tooltip: {
						headerFormat: '<span>{point.category}</span><br/>',
						pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
					},
					credits: {
						enabled:false
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y:.2f}%',
								style:{
									textOutline: false,
									fontSize: '26px'
								}
							},
							animation: false,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer'
						}
					},
					series: [{
						name:'NG Rate',
						type: 'column',
						colorByPoint: true,
						data: data_series,
						showInLegend: false
					}]

				});

			}

		});
	}



</script>
@endsection