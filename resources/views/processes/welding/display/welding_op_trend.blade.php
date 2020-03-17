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

				<div class="col-xs-2" style="padding-right: 0px;">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="datefrom" placeholder="Select Date From">
					</div>
				</div>

				<div class="col-xs-2" style="padding-right: 0px;">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="dateto" placeholder="Select Date To">
					</div>
				</div>

				<div class="col-xs-2" style="padding-right: 0; color:#212121;">
					<select class="form-control select2" id='condition' data-placeholder="Select Condition" style="width: 100%;">
						<option value="">Select Condition</option>
						<option value="date">Trend by Date</option>
						<option value="month">Trend by Month</option>
					</select>
				</div>

				<div class="col-xs-3" style="padding-right: 0; color:#212121;">
					<select class="form-control select2" multiple="multiple" id='operator' data-placeholder="Select Operators" style="width: 100%;">
						@foreach($emps as $emp)
						<option value="{{ $emp->employee_id }}">{{ $emp->employee_id }} - {{ $emp->name }}</option>
						@endforeach
					</select>
				</div>
				
				<div class="col-xs-2">
					<div class="col-xs-4" style="padding: 0px;">
						<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
					</div>
					<div class="col-xs-8" style="padding: 0px;">
						<button class="btn btn-success" onclick="fillChart()">Update Chart</button>
					</div>			
				</div>

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
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		todayHighlight : true,
		autoclose: true,
		format: "yyyy-mm-dd",
		endDate: '<?php echo $tgl_max ?>'
	});

	function clearConfirmation(){
		location.reload(true);		
	}

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

	function fillChart() {

		var position = $(document).scrollTop();

		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var condition = $('#condition').val();
		var operator = $('#operator').val();

		if(datefrom == "" || dateto == ""){
			alert("Select Date From and Date To");
			return false;
		}

		var data = {
			datefrom:datefrom,
			dateto:dateto,
			condition:condition,
			operator:operator,
		}

		$.get('{{ url("fetch/welding/op_trend") }}',data, function(result, status, xhr) {
			if(result.status){

				var seriesData = [];
				var data = [];
				var xAxis = [];

				for (var i = 0; i < result.op.length; i++) {
					data = [];
					xAxis = [];

					for (var j = 0; j < result.ng.length; j++) {
						if(result.op[i].employee_id == result.ng[j].employee_id){
							data.push(result.ng[j].ng_rate);
							xAxis.push(result.ng[j].series);
						}
					}
					seriesData.push({name : result.op[i].employee_id + ' - ' +result.op[i].name, data: data});
				}

				console.log(seriesData);
				console.log(xAxis);

				var chart = Highcharts.chart('container1', {
					chart: {
						type: 'spline',
					},
					title: {
						text: 'Trend NG Rate By Operator',
						align: 'left'
					},
					subtitle: {
						text: 'From '+ datefrom +' To ' + dateto,
						align: 'left'
					},
					xAxis: {
						categories: xAxis,
						type: 'category',
						labels: {
							overflow: 'justify'
						}
					},
					yAxis: {
						title: {
							text: 'NG Rate (%) '
						},
						minorGridLineWidth: 0,
						gridLineWidth: 0,
						alternateGridColor: null
					},
					legend : {
						enabled:false
					},
					credits: {
						enabled:false
					},
					tooltip: {
						valueSuffix: ' %'
					},
					plotOptions: {
						spline: {
							animation: false,
							connectNulls: true,
							lineWidth: 0.5,
							shadow: {
								width: 1,
								opacity: 0.4
							},
							label: {
								connectorAllowed: false
							},
							cursor: 'pointer',
							marker: {
								enabled: false
							}
						}
					},
					series: seriesData,
					navigation: {
						menuItemStyle: {
							fontSize: '10px'
						}
					}
				});



				var seriesData = [];
				var data = [];
				var xAxis = [];

				for (var i = 0; i < result.op.length; i++) {
					data = [];
					xAxis = [];

					for (var j = 0; j < result.eff.length; j++) {
						if(result.op[i].employee_id == result.eff[j].employee_id){
							if(result.eff[j].eff != null){
								data.push(parseFloat(result.eff[j].eff));
							}else{
								data.push(result.eff[j].eff);
							}
							xAxis.push(result.eff[j].series);
						}
					}
					seriesData.push({name : result.op[i].employee_id + ' - ' +result.op[i].name, data: data});
				}

				console.log(seriesData);
				console.log(xAxis);

				var chart = Highcharts.chart('container2', {
					chart: {
						type: 'spline',
					},
					title: {
						text: 'Trend Operator Efficiency',
						align: 'left'
					},
					subtitle: {
						text: 'From '+ datefrom +' To ' + dateto,
						align: 'left'
					},
					xAxis: {
						categories: xAxis,
						type: 'category',
						labels: {
							overflow: 'justify'
						}
					},
					yAxis: {
						title: {
							text: 'Efficiency (%) '
						},
						minorGridLineWidth: 0,
						gridLineWidth: 0,
						alternateGridColor: null
					},
					legend : {
						enabled:false
					},
					credits: {
						enabled:false
					},
					tooltip: {
						valueSuffix: ' %'
					},
					plotOptions: {
						spline: {
							animation: false,
							connectNulls: true,
							lineWidth: 0.5,
							shadow: {
								width: 1,
								opacity: 0.4
							},
							label: {
								connectorAllowed: false
							},
							cursor: 'pointer',
							marker: {
								enabled: false
							}
						}
					},
					series: seriesData,
					navigation: {
						menuItemStyle: {
							fontSize: '10px'
						}
					}
				});

				$(document).scrollTop(position);
			}
		});

	}





</script>
@endsection