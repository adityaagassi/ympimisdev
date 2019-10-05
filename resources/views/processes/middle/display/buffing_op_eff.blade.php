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
				<div class="col-xs-2">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tanggal" placeholder="Select Date">
					</div>
				</div>
				<div class="col-xs-1">
					<button class="btn btn-success" onclick="fillChart()">Update Chart</button>
				</div>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>
			<div class="col-xs-12" style="margin-top: 5px;">
				<div class="col-xs-4" style="padding-left: 0px;">
					<div id="container1_shift3" style="width: 100%;"></div>
				</div>
				<div class="col-xs-4">
					<div id="container1_shift1" style="width: 100%;"></div>
				</div>
				<div class="col-xs-4" style="padding-right: 0px;">
					<div id="container1_shift2" style="width: 100%;"></div>
				</div>
			</div>
			<div class="col-xs-12" style="margin-top: 1%;">
				<div id="container2" style="width: 100%;"></div>
			</div>
			<div class="col-xs-12" style="margin-top: 1%;">
				<div id="container3" style="width: 100%;"></div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	

	jQuery(document).ready(function(){
		fillChart();
		setInterval(fillChart, 60000);
		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

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
		var tanggal = $('#tanggal').val();

		var data = {
			tanggal:tanggal,
		}

		$.get('{{ url("fetch/middle/buffing_op_eff") }}', data, function(result, status, xhr) {
			if(result.status){
				
				// Shift 3
				var eff = [];
				for(var i = 0; i < result.rate.length; i++){
					if(result.rate[i].shift == 's3'){
						for(var j = 0; j < result.time_eff.length; j++){
							if(result.rate[i].operator_id == result.time_eff[j].operator_id){
								eff.push([result.rate[i].name, (result.rate[i].rate * result.time_eff[j].eff * 100)]);
							}
						}
					}					
				}

				eff.sort(function(a, b){return b[1] - a[1]});
				var op_name = [];
				var eff_value = [];
				for (var i = 0; i < eff.length; i++) {
					op_name.push(eff[i][0]);
					eff_value.push(eff[i][1]);
				}

				var chart = Highcharts.chart('container1_shift3', {
					title: {
						text: 'Operators Overall Efficiency',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'Shift 3 on '+ result.date,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: 'OP Efficiency (%)'
						},
					},
					xAxis: {
						categories: op_name,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							style: {
								fontSize: '1vw'
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
									fontSize: '20px'
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
						name:'OP Efficiency',
						type: 'column',
						color: 'rgb(68,169,168)',
						data: eff_value,
						showInLegend: false
					}]

				});


				// Shift 1
				var eff = [];
				for(var i = 0; i < result.rate.length; i++){
					if(result.rate[i].shift == 's1'){
						for(var j = 0; j < result.time_eff.length; j++){
							if(result.rate[i].operator_id == result.time_eff[j].operator_id){
								eff.push([result.rate[i].name, (result.rate[i].rate * result.time_eff[j].eff * 100)]);
							}
						}
					}					
				}

				eff.sort(function(a, b){return b[1] - a[1]});
				var op_name = [];
				var eff_value = [];
				for (var i = 0; i < eff.length; i++) {
					op_name.push(eff[i][0]);
					eff_value.push(eff[i][1]);
				}

				var chart = Highcharts.chart('container1_shift1', {
					title: {
						text: 'Operators Overall Efficiency',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'Shift 1 on '+ result.date,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: 'OP Efficiency (%)'
						},
					},
					xAxis: {
						categories: op_name,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							style: {
								fontSize: '1vw'
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
									fontSize: '20px'
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
						name:'OP Efficiency',
						type: 'column',
						color: 'rgb(169,255,151)',
						data: eff_value,
						showInLegend: false
					}]

				});


				// Shift 2
				var eff = [];
				for(var i = 0; i < result.rate.length; i++){
					if(result.rate[i].shift == 's2'){
						for(var j = 0; j < result.time_eff.length; j++){
							if(result.rate[i].operator_id == result.time_eff[j].operator_id){
								eff.push([result.rate[i].name, (result.rate[i].rate * result.time_eff[j].eff * 100)]);
							}
						}
					}					
				}

				eff.sort(function(a, b){return b[1] - a[1]});
				var op_name = [];
				var eff_value = [];
				for (var i = 0; i < eff.length; i++) {
					op_name.push(eff[i][0]);
					eff_value.push(eff[i][1]);
				}

				var chart = Highcharts.chart('container1_shift2', {
					title: {
						text: 'Operators Overall Efficiency',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'Shift 2 on '+ result.date,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: 'OP Efficiency (%)'
						},
					},
					xAxis: {
						categories: op_name,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							style: {
								fontSize: '1vw'
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
									fontSize: '20px'
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
						name:'OP Efficiency',
						type: 'column',
						color: 'rgb(255,116,116)',
						data: eff_value,
						showInLegend: false
					}]

				});


			}


		});

$.get('{{ url("fetch/middle/buffing_op_working") }}', data, function(result, status, xhr) {
	if(result.status){

		var op = [];
		var act = [];
		var std = [];
		var target = [];

		for(var i = 0; i < result.working_time.length; i++){
			for(var j = 0; j < result.emp_name.length; j++){
				if(result.working_time[i].operator_id == result.emp_name[j].employee_id){
					op.push(result.emp_name[j].name);
				}
			}
			act.push(Math.ceil(result.working_time[i].act));
			std.push(Math.ceil(result.working_time[i].std));
			target.push(parseInt(480));
		}


		var chart = Highcharts.chart('container3', {
			title: {
				text: 'Operators Working time on '+ result.date,
				style: {
					fontSize: '30px',
					fontWeight: 'bold'
				}
			},
			yAxis: {
				title: {
					enabled: true,
					text: "Minutes"
				},
				max: 500,
				plotLines: [{
					color: '#FF0000',
					width: 2,
					value: 480,
					label: {
						align:'right',
						text: '480 Minutes',
						x:-7,
						style: {
							fontSize: '1vw',
							color: '#FF0000',
							fontWeight: 'bold'
						}
					}
				}]
			},
			xAxis: {
				categories: op,
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
							fontSize: '26px'
						}
					},
					animation: false,
					cursor: 'pointer'
				},
			},
			series: [
			{
				name:'Standart time',
				type: 'column',
				color: 'rgb(255,116,116)',
				data: std
			},
			{
				name:'Actual Time',
				type: 'column',
				color: 'rgb(144,238,126)',
				data: act,
			}
			]

		});

	}

});


$.get('{{ url("fetch/middle/buffing_daily_op_eff") }}', function(result, status, xhr) {
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

							if(result.rate[j].rate == 0){
								data.push([Date.parse(result.rate[j].week_date), null]);
							}else{
								data.push([Date.parse(result.rate[j].week_date), (result.rate[j].rate * result.time_eff[k].eff * 100)]);
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


		var chart = Highcharts.stockChart('container2', {
			chart:{
				type:'spline',
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
					connectNulls: true,
					shadow: {
						width: 3,
						opacity: 0.4
					},
					label: {
						connectorAllowed: false
					},

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