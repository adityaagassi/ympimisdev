@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.morecontent span {
		display: none;
	}
	.morelink {
		display: block;
	}
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
				<div class="col-xs-3" style="padding-right: 0; color:#212121;">
					<select class="form-control select2" multiple="multiple" id='operator' data-placeholder="Select Operators" style="width: 100%;">
						@foreach($emps as $emp)
						<option value="{{ $emp->employee_id }}">{{ $emp->employee_id }} - {{ $emp->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-xs-2">
					<button class="btn btn-success" onclick="fillChart()">Update Chart</button>
				</div>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>

			<div class="col-xs-12">
				<div id="content1">
					<div id="container1" style="width: 100%; margin-top: 1%;"></div>
				</div>
			</div>
			<div class="col-xs-12">
				<div id="content2">
					<div id="container2" style="width: 100%; margin-top: 1%;"></div>					
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
				[0, '#2a2a2b']
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
		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');


		var operator = $('#operator').val();;

		var data = {
			operator:operator,
		}

		$.get('{{ url("fetch/middle/buffing_daily_op_eff") }}',data, function(result, status, xhr) {
			if(result.status){

				var seriesData = [];
				var data = [];


				for (var i = 0; i < result.op.length; i++) {
					data = [];

					for (var j = 0; j < result.rate.length; j++) {

						if(result.op[i].operator_id == result.rate[j].operator_id){
							var isEmpty = true;
							for (var k = 0; k < result.time_eff.length; k++) {
								if((result.rate[j].week_date == result.time_eff[k].tgl) && (result.rate[j].operator_id == result.time_eff[k].operator_id)){

									if(Date.parse(result.rate[j].week_date) > Date.parse('2019-10-01')){
										if(result.rate[j].rate == 0){
											data.push([Date.parse(result.rate[j].week_date), null]);
										}else{
											data.push([Date.parse(result.rate[j].week_date), (result.rate[j].rate * result.time_eff[k].eff * 100)]);
										}

									}else{
										data.push([Date.parse(result.rate[j].week_date), null]);

									}
									isEmpty = false;						
								}
							}
							if(isEmpty){
								data.push([Date.parse(result.rate[j].week_date), null]);
							}				
						}
					}
					seriesData.push({name : result.op[i].name, data: data});
				}


				var chart = Highcharts.stockChart('container1', {
					chart:{
						type:'spline',
						animation: false,
					},
					rangeSelector: {
						selected: 0
					},
					scrollbar:{
						enabled:false
					},
					navigator:{
						enabled:false
					},
					title: {
						text: 'Daily Operators Overall Efficiency',
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: 'NG Rate (%)'
						},
						plotLines: [{
							color: '#FFFFFF',
							width: 2,
							value: 0,
							dashStyles: 'longdashdot'
						}]
					},
					xAxis: {
						categories: 'datetime',
						tickInterval: 24 * 3600 * 1000 
					},
					tooltip: {
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y:.2f}%</b>',
						split: false,
					},
					legend : {
						enabled:false
					},
					credits: {
						enabled:false
					},
					plotOptions: {
						series: {
							dataLabels: {
								enabled: true,
								format: '{point.y:,.2f}%',
							},

							animation: false,
							connectNulls: true,
							lineWidth: 1,
							shadow: {
								width: 1,
								opacity: 0.4
							},
							label: {
								connectorAllowed: false
							},
							cursor: 'pointer',

						}
					},
					series: seriesData,
					responsive: {
						rules: [{
							condition: {
								maxWidth: 500
							},
							chartOptions: {
								legend: {
									layout: 'horizontal',
									align: 'center',
									verticalAlign: 'bottom'
								}
							}
						}]
					}
				});


			}
		});



		$.get('{{ url("fetch/middle/buffing_daily_op_ng_rate") }}',data, function(result, status, xhr) {
			if(result.status){

				var seriesData = [];
				var data = [];


				for (var i = 0; i < result.op.length; i++) {
					data = [];
					for (var j = 0; j < result.ng_rate.length; j++) {
						if(result.op[i].operator_id == result.ng_rate[j].operator_id){
							if(Date.parse(result.ng_rate[j].week_date) > Date.parse('2019-10-01')){
								if(result.ng_rate[j].ng_rate == 0){
									data.push([Date.parse(result.ng_rate[j].week_date), null]);
								}else{
									if(result.ng_rate[j].ng_rate > 100){
										data.push([Date.parse(result.ng_rate[j].week_date), 100]);

									}else{
										data.push([Date.parse(result.ng_rate[j].week_date), result.ng_rate[j].ng_rate]);
									}
								}
							}else{
								data.push([Date.parse(result.ng_rate[j].week_date), null]);

							}
						}
					}
					seriesData.push({name : result.op[i].name, data: data});
				}

				var chart = Highcharts.stockChart('container2', {
					chart:{
						type:'spline',
						animation: false,
					},
					rangeSelector: {
						selected: 0
					},
					scrollbar:{
						enabled:false
					},
					navigator:{
						enabled:false
					},
					title: {
						text: 'Daily NG Rate By Operators',
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: 'NG Rate (%)'
						},
						plotLines: [{
							color: '#FFFFFF',
							width: 2,
							value: 0,
							dashStyles: 'longdashdot'
						}],
					},
					xAxis: {
						categories: 'datetime',
						tickInterval: 24 * 3600 * 1000 
					},
					tooltip: {
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y:.2f}%</b>',
						split: false,
					},
					legend : {
						enabled:false
					},
					credits: {
						enabled:false
					},
					plotOptions: {
						series: {
							animation: false,
							connectNulls: true,
							lineWidth: 1,
							shadow: {
								width: 1,
								opacity: 0.4
							},
							label: {
								connectorAllowed: false
							},
							cursor: 'pointer',
						}
					},
					series: seriesData,
					responsive: {
						rules: [{
							condition: {
								maxWidth: 500
							},
							chartOptions: {
								legend: {
									layout: 'horizontal',
									align: 'center',
									verticalAlign: 'bottom'
								}
							}
						}]
					}
				});
			}
		});







	}



</script>
@endsection