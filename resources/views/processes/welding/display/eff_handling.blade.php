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
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<form method="GET" action="{{ action('WeldingProcessController@indexEffHandling') }}">
					<div class="col-xs-2">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" name="tanggal" id="tanggal" placeholder="Select Date">
						</div>
					</div>
					<div class="col-xs-2" style="color: black;">
						<div class="form-group">
							<select class="form-control select2" multiple="multiple" id="locationSelect" data-placeholder="Select Location" onchange="change()">
								@foreach($locations as $location)
								<option value="{{ $location }}">{{ $location }}</option>
								@endforeach
							</select>
							<input type="text" name="location" id="location" hidden>
						</div>
					</div>
					<div class="col-xs-2">
						<button class="btn btn-success" type="submit">Update Chart</button>
					</div>
				</form>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>
			<div class="col-xs-12" style="margin-top: 5px;">
				<div id="container1" style="width: 100%;"></div>
			</div>
			<div class="col-xs-12" style="margin-top: 5px;">
				<div id="container2" style="width: 100%;"></div>
			</div>	
			<div class="col-xs-12" style="margin-top: 5px;">
				<div id="container3" style="width: 100%;"></div>
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
		$('.select2').select2();
		fillChart();
		setInterval(fillChart, 10000);

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
			backgroundColor: null,
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

	function fillChart() {
		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');
		var position = $(document).scrollTop();		

		var data = {
			tanggal:"{{$_GET['tanggal']}}",
			location:"{{$_GET['location']}}"
		}

		$.get('{{ url("fetch/welding/eff_handling") }}', data, function(result, status, xhr) {
			if(result.status){

				var first = ['C','E'];
				var second = ['D','F','G','H','J'];


				var key = [];
				var act = [];
				var std = [];
				var eff = [];
				var plotBands = [];
				var loop = 0;

				for(var i = 0; i < result.time.length; i++){
					if(result.time[i].hpl == 'ASKEY' && first.includes(result.time[i].key.split("-")[0])){
						loop += 1;

						var name = '';
						var name_temp = result.time[i].name.split(" ");
						if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Moch.' || name_temp[0] == 'Mochamad' || name_temp[0] == 'Rr.'){
							name += name_temp[0].charAt(0)+'. '+name_temp[1];
						}else{
							name += name_temp[0]+'. '+name_temp[1].charAt(0);
						}

						key.push(result.time[i].model + " - " + result.time[i].key + "<br>" + name);
						act.push(Math.ceil(parseInt(result.time[i].actual)/60));
						std.push(Math.ceil(parseInt(result.time[i].std)/60));
						eff.push(Math.ceil(parseInt(result.time[i].std)/60) / Math.ceil(parseInt(result.time[i].actual)/60) * 100);

						if(eff[loop-1] < 85){
							plotBands.push({from: (loop - 1.5), to: (loop - 0.5), color: 'rgba(255, 116, 116, .3)'});		
						}
					}
				}
				
				var chart = Highcharts.chart('container1', {
					title: {
						text: result.location + ' AVG Actual Working Time VS AVG Standart Time',
						style: {
							fontSize: '23px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'ASKEY (C,E) on '+result.date,
						style: {
							fontSize: '18px',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: 'Minute(s)'
						},
						style: {
							fontSize: '26px',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: key,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							style: {
								fontSize: '12px'
							}
						},
						plotBands: plotBands
					},
					tooltip: {
						headerFormat: '<span>{point.category}</span><br/>',
						pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
					},
					credits: {
						enabled:false
					},
					legend : {
						align: 'center',
						verticalAlign: 'bottom',
						x: 0,
						y: 0,

						backgroundColor: (
							Highcharts.theme && Highcharts.theme.background2) || 'white',
						shadow: false
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									textOutline: false,
									fontSize: '1vw'
								}
							},
							animation: false,
							cursor: 'pointer'
						}
					},
					series: [{
						name:'Standart Time',
						type: 'column',
						color: '#3F51B5',
						data: std,
					},{
						name:'Actual Time',
						type: 'column',
						color: '#FFC107',
						data: act,
					}]
				});





				var key = [];
				var act = [];
				var std = [];
				var eff = [];
				var plotBands = [];
				var loop = 0;

				for(var i = 0; i < result.time.length; i++){
					if(result.time[i].hpl == 'ASKEY' && second.includes(result.time[i].key.split("-")[0])){
						loop += 1;

						var name = '';
						var name_temp = result.time[i].name.split(" ");
						if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Moch.' || name_temp[0] == 'Mochamad' || name_temp[0] == 'Rr.'){
							name += name_temp[0].charAt(0)+'. '+name_temp[1];
						}else{
							name += name_temp[0]+'. '+name_temp[1].charAt(0);
						}

						key.push(result.time[i].model + " - " + result.time[i].key + "<br>" + name);
						act.push(Math.ceil(parseInt(result.time[i].actual)/60));
						std.push(Math.ceil(parseInt(result.time[i].std)/60));
						eff.push(Math.ceil(parseInt(result.time[i].std)/60) / Math.ceil(parseInt(result.time[i].actual)/60) * 100);

						if(eff[loop-1] < 85){
							plotBands.push({from: (loop - 1.5), to: (loop - 0.5), color: 'rgba(255, 116, 116, .3)'});		
						}
					}
				}
				
				var chart = Highcharts.chart('container2', {
					title: {
						text: result.location + ' AVG Actual Working Time VS AVG Standart Time',
						style: {
							fontSize: '23px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'ASKEY (D,F,G,H,J) on '+result.date,
						style: {
							fontSize: '18px',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: 'Minute(s)'
						},
						style: {
							fontSize: '26px',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: key,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							style: {
								fontSize: '12px'
							}
						},
						plotBands: plotBands
					},
					tooltip: {
						headerFormat: '<span>{point.category}</span><br/>',
						pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
					},
					credits: {
						enabled:false
					},
					legend : {
						align: 'center',
						verticalAlign: 'bottom',
						x: 0,
						y: 0,

						backgroundColor: (
							Highcharts.theme && Highcharts.theme.background2) || 'white',
						shadow: false
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									textOutline: false,
									fontSize: '1vw'
								}
							},
							animation: false,
							cursor: 'pointer'
						}
					},
					series: [{
						name:'Standart Time',
						type: 'column',
						color: '#3F51B5',
						data: std,
					},{
						name:'Actual Time',
						type: 'column',
						color: '#FFC107',
						data: act,
					}]
				});




				var key = [];
				var act = [];
				var std = [];
				var eff = [];
				var plotBands = [];
				var loop = 0;

				for(var i = 0; i < result.time.length; i++){
					if(result.time[i].hpl == 'TSKEY' && result.time[i].model != 'A82'){
						loop += 1;

						var name = '';
						var name_temp = result.time[i].name.split(" ");
						if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Moch.' || name_temp[0] == 'Mochamad' || name_temp[0] == 'Rr.'){
							name += name_temp[0].charAt(0)+'. '+name_temp[1];
						}else{
							name += name_temp[0]+'. '+name_temp[1].charAt(0);
						}

						key.push(result.time[i].model + " - " + result.time[i].key + "<br>" + name);
						act.push(Math.ceil(parseInt(result.time[i].actual)/60));
						std.push(Math.ceil(parseInt(result.time[i].std)/60));
						eff.push(Math.ceil(parseInt(result.time[i].std)/60) / Math.ceil(parseInt(result.time[i].actual)/60) * 100);

						if(eff[loop-1] < 85){
							plotBands.push({from: (loop - 1.5), to: (loop - 0.5), color: 'rgba(255, 116, 116, .3)'});		
						}
					}
				}

				var chart = Highcharts.chart('container3', {
					title: {
						text: result.location + ' AVG Actual Working Time VS AVG Standart Time',
						style: {
							fontSize: '23px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'TSKEY on '+result.date,
						style: {
							fontSize: '18px',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: 'Minute(s)'
						},
						style: {
							fontSize: '26px',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: key,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							style: {
								fontSize: '12px'
							}
						},
						plotBands: plotBands
					},
					tooltip: {
						headerFormat: '<span>{point.category}</span><br/>',
						pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
					},
					credits: {
						enabled:false
					},
					legend : {
						align: 'center',
						verticalAlign: 'bottom',
						x: 0,
						y: 0,

						backgroundColor: (
							Highcharts.theme && Highcharts.theme.background2) || 'white',
						shadow: false
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									textOutline: false,
									fontSize: '1vw'
								}
							},
							animation: false,
							cursor: 'pointer'
						}
					},
					series: [{
						name:'Standart Time',
						type: 'column',
						color: '#3F51B5',
						data: std,
					},{
						name:'Actual Time',
						type: 'column',
						color: '#FFC107',
						data: act,
					}]
				});


				$(document).scrollTop(position);
				
			}

		});

}



</script>
@endsection