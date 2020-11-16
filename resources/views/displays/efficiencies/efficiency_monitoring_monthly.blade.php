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
		<div id="period_title" class="col-xs-5" style="background-color: #ccff90;"><center><span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text"></span></center>
		</div>
		<div class="col-xs-5">
			<button class="btn btn-success pull-right"  data-toggle="modal" data-target="#modalAdd">Tambah Data</button>
		</div>
		<div class="col-xs-2">
			<select class="form-control select2" id="fiscal_year" style="width: 100%;" data-placeholder="Select Fiscal Year" onchange="fetchChart(value)" required>
				<option value=""></option>
				@foreach($weeks as $week)
				<option value="{{ $week->indek }}">{{ $week->fiscal_year }} {{ $week->bulan }}</option>
				@endforeach
			</select>
		</div>
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-6" id="eff_monitoring_year" style="padding: 0;">
				</div>
				<div class="col-xs-6" id="eff_monitoring_month" style="padding: 0;">
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalAdd">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<div class="col-md-12">
						<form class="form-horizontal">
							<div class="form-group" style="margin-bottom: 0;">
								<label for="newDate" class="col-sm-12 control-label">Tanggal<span class="text-red">*</span></label>
								<div class="col-sm-12">
									<input type="text" class="form-control pull-right" id="newDate" name="newDate" value="{{date('Y-m-d')}}">
								</div>
							</div>
							<div class="form-group" style="margin-bottom: 0;">
								<label for="newCost" class="col-sm-12 control-label">Cost Center<span class="text-red">*</span></label>
								<div class="col-sm-12">
									<select class="form-control select2" name="newCost" id="newCost" data-placeholder="Pilih Kehadiran" style="width: 100%;">
										<option value=""></option>
										@foreach($cost_centers as $cost_center)
										<option value="{{ $cost_center->cost_center_eff }}">{{ $cost_center->cost_center_eff }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="form-group" style="margin-bottom: 0;">
								<label for="newInput" class="col-sm-12 control-label">Total Input<span class="text-red">*</span></label>
								<div class="col-sm-12">
									<input type="text" style="width: 100%" class="form-control" id="newInput" name="newInput" placeholder="Total Jam Input">
								</div>
							</div>
							<div class="form-group" style="margin-bottom: 0;">
								<label for="newOutput" class="col-sm-12 control-label">Total Output<span class="text-red">*</span></label>
								<div class="col-sm-12">
									<input type="text" style="width: 100%" class="form-control" id="newOutput" name="newOutput" placeholder="Total Jam Output">
								</div>
							</div>
							<br>
						</form>
						<button class="btn btn-primary" style="width: 100%; font-size: 1.5vw;" onclick="addEfficiency()">Update Data</button>
					</div>
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
		$("#fiscal_year").prop('selectedIndex', 0).change();
		clearData();
		// fetchChart();
		$('#newDate').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		$('.select2').select2();
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

	function clearData(){
		$('#newDate').val("");
		$('#newInput').val("");
		$('#newOutput').val("");
		$("#newCost").prop('selectedIndex', 0).change();
	}

	function addEfficiency(){
		if($('#newDate').val() != "" && $('#newCost').val() != "" && $('#newInput').val() != "" && $('#newOutput').val() != ""){
			var newDate = $('#newDate').val();
			var newCost = $('#newCost').val();
			var newInput = $('#newInput').val();
			var newOutput = $('#newOutput').val();

			var data = {
				newDate:newDate,
				newCost:newCost,
				newInput:newInput,
				newOutput:newOutput			
			}

			$.post('{{ url("input/display/efficiency_monitoring_monthly") }}', data, function(result, status, xhr){
				if(result.status){
					clearData();
					$('#modalAdd').modal('hide');
				}
				else{
					alert(result.message);
					clearData();					
				}
			});
		}
		else{
			alert('Lengkapi Data Terlebih Dahulu');
			clearData();
		}
	}

	function fetchChart(id){
		var data = {
			period:id
		}
		$.get('{{ url("fetch/display/efficiency_monitoring_monthly") }}', data, function(result, status, xhr) {
			if(result.status){
				var title = result.period.split(" ");
				$('#title_text').text('Efficiency Data '+title[0]);
				var h = $('#period_title').height();
				$('#fiscal_year').css('height', h);

				var cost_center_name_month = [];
				cost_center_name_month.push('YMPI');
				var month = {};
				var months = result.months;
				$('#eff_monitoring_month').html("");
				var eff_monitoring_month = "";
				var monthCategories = [];
				month['YMPI'] = [];

				eff_monitoring_month += '<div class="col-xs-12" style="padding:0; height:300px;" id="eff_month_YMPI"></div><hr>';

				$.each(result.months, function(key, value){
					if(jQuery.inArray(value.cost_center_name, cost_center_name_month) === -1){
						month[value.cost_center_name] = [];
						cost_center_name_month.push(value.cost_center_name);
						eff_monitoring_month += '<div class="col-xs-12" style="padding:0; height:250px;" id="eff_month_'+value.cost_center_name+'"></div><hr>';
					}

					var cat = value.week_date.split('-');
					// console.log(cat[2]);

					if(jQuery.inArray(cat[2], monthCategories) === -1){
						monthCategories.push(cat[2]);
					}

					month[value.cost_center_name].push({
						week_date: value.week_date, 
						total_input:value.total_input,
						total_output:value.total_output,
					});
				});


				months.reduce(function (res, value) {
					if (!res[value.week_date]) {
						res[value.week_date] = {
							week_date: value.week_date,
							total_output: 0,
							total_input: 0
						};
						month['YMPI'].push(res[value.week_date]);
					}
					res[value.week_date].total_input += value.total_input;
					res[value.week_date].total_output += value.total_output;
					return res;
				}, {});


				$('#eff_monitoring_month').append(eff_monitoring_month);
				var sum_input = {};
				var sum_output = {};
				var sum_percentage = {};

				for(var i = 0; i < cost_center_name_month.length; i++){
					var id_div_month = 'eff_month_'+cost_center_name_month[i];
					var total_input = 0;
					var total_output = 0;
					sum_input[cost_center_name_month[i]] = [];
					sum_output[cost_center_name_month[i]] = [];
					sum_percentage[cost_center_name_month[i]] = [];

					$.each(month[cost_center_name_month[i]], function(key, value){
						total_input += value.total_input;
						total_output += value.total_output;
						sum_input[cost_center_name_month[i]].push(total_input);
						sum_output[cost_center_name_month[i]].push(total_output);
						sum_percentage[cost_center_name_month[i]].push((total_output/total_input)*100);
					});

					Highcharts.chart(id_div_month, {
						chart: {
							type: 'column',
							backgroundColor	: null
						},
						title: {
							text: cost_center_name_month[i]+' '+title[1]
						},
						credits: {
							enabled: false
						},
						xAxis: {
							tickInterval: 1,
							gridLineWidth: 1,
							categories: monthCategories,
							crosshair: true
						},
						yAxis: [{
							min: 0,
							title: {
								text: null
							}
						}, { 
							min: 0,
							max:120,
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
							data: sum_input[cost_center_name_month[i]]

						}, {
							name: 'Output Hour(s)',
							data: sum_output[cost_center_name_month[i]]
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
							data: sum_percentage[cost_center_name_month[i]]

						}]
					});
				}

				var cost_center_name_year = [];
				cost_center_name_year.push('YMPI');
				var year = {};
				var years = result.years;
				$('#eff_monitoring_year').html("");
				var eff_monitoring_year = "";
				var yearCategories = [];
				year['YMPI'] = [];

				eff_monitoring_year += '<div class="col-xs-12" style="padding:0; height:300px;" id="eff_year_YMPI"></div><hr>';

				$.each(result.years, function(key, value){
					if(jQuery.inArray(value.cost_center_name, cost_center_name_year) === -1){
						year[value.cost_center_name] = [];
						cost_center_name_year.push(value.cost_center_name);
						eff_monitoring_year += '<div class="col-xs-12" style="padding:0; height:250px;" id="eff_year_'+value.cost_center_name+'"></div><hr>';
					}

					if(jQuery.inArray(value.month_date, yearCategories) === -1){
						yearCategories.push(value.month_date);
					}

					var percentage = (value.total_output/value.total_input)*100;

					if(value.total_output == 0){
						percentage = 0;
					}

					year[value.cost_center_name].push({
						week_date: value.month_date, 
						total_input:value.total_input,
						total_output:value.total_output,
						total_percentage:percentage
					});
				});

				years.reduce(function (res, value) {
					if (!res[value.month_date]) {
						res[value.month_date] = {
							month_date: value.month_date,
							total_input: 0,
							total_output: 0
						};
						year['YMPI'].push(res[value.month_date]);
					}
					res[value.month_date].total_input += value.total_input;
					res[value.month_date].total_output += value.total_output;
					var percentage = (res[value.month_date].total_output/res[value.month_date].total_input)*100;
					if(res[value.month_date].total_output == 0){
						percentage = 0;
					}
					res[value.month_date].total_percentage = percentage;
					return res;
				}, {});

				$('#eff_monitoring_year').append(eff_monitoring_year);

				var data_input = {};
				var data_output = {};
				var data_percentage = {};

				for(var i = 0; i < cost_center_name_year.length; i++){
					var id_div_year = 'eff_year_'+cost_center_name_year[i];
					data_input[cost_center_name_year[i]] = [];
					data_output[cost_center_name_year[i]] = [];
					data_percentage[cost_center_name_year[i]] = [];

					$.each(year[cost_center_name_year[i]], function(key, value){
						data_input[cost_center_name_year[i]].push(value.total_input);
						data_output[cost_center_name_year[i]].push(value.total_output);
						data_percentage[cost_center_name_year[i]].push(value.total_percentage);
					});

					Highcharts.chart(id_div_year, {
						chart: {
							type: 'column',
							backgroundColor	: null
						},
						title: {
							text: cost_center_name_year[i]+' '+title[0]
						},
						credits: {
							enabled: false
						},
						xAxis: {
							tickInterval: 1,
							gridLineWidth: 1,
							categories: yearCategories,
							crosshair: true
						},
						yAxis: [{
							min: 0,
							title: {
								text: null
							}
						}, { 
							min: 0,
							max:120,
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
							data: data_input[cost_center_name_year[i]]

						}, {
							name: 'Output Hour(s)',
							data: data_output[cost_center_name_year[i]]
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
							data: data_percentage[cost_center_name_year[i]]

						}]
					});
				}

			}
			else{
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