@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	#loading { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>
	<div class="row">
		<div id="period_title" class="col-xs-9" style="background-color: #ccff90;"><center><span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text"></span></center></div>
		<div class="col-xs-3">
			<div class="input-group date">
				<div class="input-group-addon" style="background-color: #ccff90;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control pull-right" id="datepicker" name="datepicker" onchange="fetchChart()">
			</div>
		</div>
		<div class="row" id="eff_monitoring">
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 3%;">Material</th>
								<th style="width: 9%;">Description</th>
								<th style="width: 1%;">Quantity</th>
								<th style="width: 1%;">Total Amount</th>
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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fetchChart();
		$('#datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm",
			startView: "months", 
			minViewMode: "months",
			autoclose: true,
		});
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

	function fetchChart(){
		// $('#loading').show();
		var period = $('#datepicker').val();
		var data = {
			period:period
		}
		$.get('{{ url("fetch/display/efficiency_monitoring") }}', data, function(result, status, xhr) {
			if(result.status){

				$('#title_text').text('Periode '+result.first+' - '+result.last);
				var h = $('#period_title').height();
				$('#datepicker').css('height', h);

				var cost_center_name = [];
				cost_center_name.push('ALL');
				var data = {};
				var sum_input = {};
				var sum_output = {};
				var data_percentage = {};
				var datas = result.datas;
				$('#eff_monitoring').html("");
				var eff_monitoring = "";

				eff_monitoring += '<div class="col-xs-12" style="padding-left: 0; padding-right: 0; padding-top: 10px; height: 500px;" id="eff_ALL"></div>';

				$.each(result.datas, function(key, value){

					if(jQuery.inArray(value.cost_center_name, cost_center_name) === -1){
						data[value.cost_center_name] = [];
						eff_monitoring += '<div class="col-xs-4" style="padding:0; height:300px;" id="eff_'+value.cost_center_name+'"></div>';
						cost_center_name.push(value.cost_center_name);
					}

					data[value.cost_center_name].push({
						week_date: value.week_date, 
						total_output:value.total_output/60, 
						total_input:value.total_input/60
					});

				});

				$('#eff_monitoring').append(eff_monitoring);

				data['ALL'] = [];
				var xCategories = [];

				datas.reduce(function (res, value) {
					if (!res[value.week_date]) {
						res[value.week_date] = {
							week_date: value.week_date,
							total_output: 0,
							total_input: 0
						};
						data['ALL'].push(res[value.week_date]);
						xCategories.push(value.week_date);
					}
					res[value.week_date].total_input += value.total_input;
					res[value.week_date].total_output += value.total_output;
					return res;
				}, {});

				for(var i = 0; i < cost_center_name.length; i++){
					var total_input = 0;
					var total_output = 0;
					sum_input[cost_center_name[i]] = [];
					sum_output[cost_center_name[i]] = [];
					data_percentage[cost_center_name[i]] = [];
					$.each(data[cost_center_name[i]], function(key, value){
						total_input += value.total_input;
						total_output += value.total_output;
						sum_input[cost_center_name[i]].push(total_input);
						sum_output[cost_center_name[i]].push(total_output);
						data_percentage[cost_center_name[i]].push((total_output/total_input)*100);
					});

					var id_div = 'eff_'+cost_center_name[i];

					Highcharts.chart(id_div, {
						chart: {
							type: 'column',
							backgroundColor	: null
						},
						title: {
							text: cost_center_name[i]
						},
						credits: {
							enabled: false
						},
						xAxis: {
							tickInterval: 1,
							gridLineWidth: 1,
							categories: xCategories,
							crosshair: true
						},
						yAxis: [{
							min: 0,
							title: {
								text: 'Hour(s)'
							}
						}, { 
							min: 0,
							max:120,
							title: {
								text: 'Percentage',
							},
							labels: {
								format: '{value}%',
							},
							opposite: true
						}],
						tooltip: {
							headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
							pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
							'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
							footerFormat: '</table>',
							shared: true,
							useHTML: true
						},
						plotOptions: {
							column: {
								pointPadding: 0.05,
								groupPadding: 0.1,
								borderWidth: 0
							}
						},
						series: [{
							name: 'Input Hour(s)',
							data: sum_input[cost_center_name[i]]

						}, {
							name: 'Output Hour(s)',
							data: sum_output[cost_center_name[i]]
						},{
							name: 'Efficiency %',
							type: 'spline',
							yAxis: 1,
							dataLabels: {
								enabled: true,
								formatter: function () {
									return Highcharts.numberFormat(this.y,2)+'%';
								}
							},
							data: data_percentage[cost_center_name[i]]

						}]
					});
				}

				return false;

				Highcharts.chart('eff_all', {
					chart: {
						type: 'column',
						backgroundColor	: null
					},
					title: {
						text: 'YMPI'
					},
					credits: {
						enabled: false
					},
					xAxis: {
						tickInterval: 1,
						gridLineWidth: 1,
						categories: xCategories,
						crosshair: true
					},
					yAxis: [{
						min: 0,
						title: {
							text: 'Hour(s)'
						}
					}, { 
						min: 0,
						title: {
							text: 'Percentage',
						},
						labels: {
							format: '{value}%',
						},
						opposite: true
					}],
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						}
					},
					series: [{
						name: 'Input Hour(s)',
						data: series_final_input

					}, {
						name: 'Output Hour(s)',
						data: series_final_output
					},{
						name: 'Efficiency %',
						type: 'spline',
						yAxis: 1,
						dataLabels: {
							enabled: true,
							formatter: function () {
								return Highcharts.numberFormat(this.y,2)+'%';
							}
						},
						data: series_final_percentage

					}]
				});

				Highcharts.chart('eff_final', {
					chart: {
						type: 'column',
						backgroundColor	: null
					},
					title: {
						text: 'FINAL'
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: false
					},
					xAxis: {
						categories: xCategories,
						labels: {
							enabled:false
						},
						crosshair: true
					},
					yAxis: [{
						min: 0,
						title: {
							text: null
						}
					}, { 
						min: 0,
						title: {
							text: null
						},
						labels: {
							format: '{value}%',
						},
						opposite: true
					}],
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						}
					},
					series: [{
						name: 'Input Hour(s)',
						data: series_final_input

					}, {
						name: 'Output Hour(s)',
						data: series_final_output
					},{
						name: 'Efficiency %',
						type: 'spline',
						yAxis: 1,
						dataLabels: {
							enabled: true,
							formatter: function () {
								return Highcharts.numberFormat(this.y,2)+'%';
							}
						},
						data: series_final_percentage

					}]
				});

			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');				
			}
		});		
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

</script>
@endsection