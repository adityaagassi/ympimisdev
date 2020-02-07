@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid white;
		color: black
	}
	table.table-bordered > thead > tr > th{
		border:1px solid white;
		color: black
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		margin:0; 
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
</style>
@endsection
@section('header')
<section class="content-header">
	<h1>
		Recorder Picking Result <span class="text-purple">??</span>
	</h1>
	<!-- <ol class="breadcrumb" id="last_update">
	</ol> -->
</section>
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
					<input type="text" class="form-control datepicker" id="date" placeholder="Select Date">
				</div>
			</div>
			<div class="col-xs-2">
				<button class="btn btn-success" onclick="fillChart()">Search</button>
			</div>
			<div class="col-xs-8 pull-right">
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw; color: white"></div>
			</div>
		</div>
		<div class="col-xs-8">

		</div>
		<div class="col-xs-8">
			<div id="container" style="width:100%; height:550px;"></div>
		</div>
		<div class="col-xs-4">
			<table id="tableActual" class="table table-hover table-bordered">				
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 40% !important;color: white;">Model</th>
						<th style="width: 15% !important;color: white;">Plan</th>
						<th style="width: 15% !important;color: white;">Actual</th>
						<th style="width: 15% !important;color: white;">Diff</th>
					</tr>
				</thead>
				<tbody id="tableBody"></tbody>
				<tfoot></tfoot>				
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="progress-group" id="progress_div">
				<div class="progress" style="height: 50px; border-style: solid;border-width: 1px;padding: 1px; border-color: #d3d3d3;margin-bottom: 0.5%">
					<span class="progress-text" id="progress_text_production" style="font-size: 25px; padding-top: 10px;"></span>
					<div class="progress-bar progress-bar-success progress-bar-striped" id="progress_bar_production" style="font-size: 30px; padding-top: 10px;"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-widget">
				<div class="box-footer">
					<div class="row" id="resume"></div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection

@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
{{-- <script src="{{ url("js/highstock.js")}}"></script> --}}
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('.datepicker').datepicker({
			<?php $tgl_max = date('d-m-Y') ?>
			autoclose: true,
			format: "dd-mm-yyyy",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2();
		fillChart();
	});

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

	function fillChart(){
		var now = new Date();
		var date = $('#date').val();
		var data = {
			date:date,
		}
		$.get('{{ url("fetch/recorder/rc_picking_result") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){

					$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

					if(now.getHours() < 7){
						$('#progress_bar_production').append().empty();
						$('#progress_text_production').html("Today's Working Time : 0%");
						$('#progress_bar_production').css('width', '0%');
						$('#progress_bar_production').css('color', 'white');
						$('#progress_bar_production').css('font-weight', 'bold');
					}
					else if((now.getHours() >= 16) && (now.getDay() != 5)){
						$('#progress_text_production').append().empty();
						$('#progress_bar_production').html("Today's Working Time : 100%");
						$('#progress_bar_production').css('width', '100%');
						$('#progress_bar_production').css('color', 'white');
						$('#progress_bar_production').css('font-weight', 'bold');
						$('#progress_bar_production').removeClass('active');
					}
					else if(now.getDay() == 5){
						$('#progress_text_production').append().empty();
						var total = 570;
						var now_menit = ((now.getHours()-7)*60) + now.getMinutes();
						var persen = (now_menit/total) * 100;
						if(now.getHours() >= 7 && now_menit < total){
							if(persen > 24){
								if(persen > 32){
									$('#progress_bar_production').html("Today's Working Time : "+persen.toFixed(2)+"%");
								}
								else{
									$('#progress_bar_production').html("Working Time : "+persen.toFixed(2)+"%");
								}	
							}
							else{
								$('#progress_bar_production').html(persen.toFixed(2)+"%");
							}
							$('#progress_bar_production').css('width', persen+'%');
							$('#progress_bar_production').addClass('active');

						}
						else if(now_menit >= total){
							$('#progress_bar_production').html("Today's Working Time : 100%");
							$('#progress_bar_production').css('width', '100%');
							$('#progress_bar_production').removeClass('active');

						}
						$('#progress_bar_production').css('color', 'white');
						$('#progress_bar_production').css('font-weight', 'bold');
					}
					else{
						$('#progress_text_production').append().empty();
						var total = 540;
						var now_menit = ((now.getHours()-7)*60) + now.getMinutes();
						var persen = (now_menit/total) * 100;
						if(persen > 24){
							if(persen > 32){
								$('#progress_bar_production').html("Today's Working Time : "+persen.toFixed(2)+"%");
							}
							else{
								$('#progress_bar_production').html("Working Time : "+persen.toFixed(2)+"%");
							}	
						}
						else{
							$('#progress_bar_production').html(persen.toFixed(2)+"%");
						}
						$('#progress_bar_production').css('width', persen+'%');
						$('#progress_bar_production').css('color', 'white');
						$('#progress_bar_production').css('font-weight', 'bold');
						$('#progress_bar_production').addClass('active');
					}
					
					var data = result.datas;
					var xAxis = []
					, planCount = []
					, actualCount = []

					for (i = 0; i < data.length; i++) {
						// if(data[i].plan-data[i].debt > 0){
						// 	xAxis.push(data[i].model);
						// 	planCount.push(data[i].plan-data[i].debt);
						// 	actualCount.push(data[i].actual);							
						// }
						xAxis.push(data[i].colorkey);
						planCount.push(data[i].plan);
						actualCount.push(data[i].actual);
					}

					Highcharts.chart('container', {
						colors: ['rgba(248,161,63,1)','rgba(126,86,134,.9)'],
						chart: {
							type: 'column',
							backgroundColor: null
						},
						title: {
							text: 'Recorder Picking Result<br><span style="color:rgba(96,92,168);">???</span>'
						},
						xAxis: {
							tickInterval:  1,
							overflow: true,
							categories: xAxis,
							labels: {
								style: {
									fontSize: '15px',
									fontWeight: 'bold'
								},
								rotation: -45,
							},
							min: 0					
						},
						yAxis: {
							min: 1,
							title: {
								text: 'Set(s)'
							},
							type:'logarithmic'
						},
						credits:{
							enabled: false
						},
						legend: {
							enabled: true,
							itemStyle: {
								fontSize:'16px',
								font: '16pt Trebuchet MS, Verdana, sans-serif',
								color: '#fff'
							}
						},
						tooltip: {
							shared: true
						},
						plotOptions: {
							series:{
								minPointLength: 10,
								pointPadding: 0,
								groupPadding: 0,
								animation:{
									duration:0
								}
							},
							column: {
								grouping: false,
								shadow: false,
								borderWidth: 0,
							}
						},
						series: [{
							name: 'Plan',
							data: planCount,
							pointPadding: 0.05
						}, {
							name: 'Actual',
							data: actualCount,
							pointPadding: 0.2
						}]
					});

					// $('#tableActual').DataTable().destroy();
					$('#tableBody').html("");
					var tableData = '';
					$.each(result.datas, function(key, value) {
						var diff = '';
						diff = value.plan-value.actual;
						tableData += '<tr style="background-color:white">';
						tableData += '<td>'+ value.colorkey +'</td>';
						tableData += '<td>'+ value.plan +'</td>';
						tableData += '<td>'+ value.actual +'</td>';
						tableData += '<td>'+ diff +'</td>';
						tableData += '</tr>';
					});
					$('#tableBody').append(tableData);

					var totalPlan = 0;
					var totalActual = 0;
					$.each(result.datas, function(key, value) {
						totalPlan += value.plan;
						totalActual += value.actual;
					});

					if(totalActual-totalPlan < 0){
						totalCaret = '<span class="text-red"><i class="fa fa-caret-down"></i>';
						persenColor = '<span class="text-red">';
					}
					if(totalActual-totalPlan > 0){
						totalCaret = '<span class="text-yellow"><i class="fa fa-caret-up"></i>';
						persenColor = '<span class="text-yellow">';
					}
					if(totalActual-totalPlan == 0){
						totalCaret = '<span class="text-green">&#9679;';
						persenColor = '<span class="text-green">&#9679;';
					}

					$('#resume').html("");
					var resumeData = '';
					resumeData += '<div class="col-sm-3 col-xs-6">';
					resumeData += '		<div class="description-block border-right">';
					resumeData += '			<h5 class="description-header" style="font-size: 60px;"><span class="description-percentage text-blue">'+ totalPlan.toLocaleString() +'</span></h5>';
					resumeData += '			<span class="description-text" style="font-size: 35px;">Total Plan<br><span class="text-purple">計画の集計</span></span>';
					resumeData += '		</div>';
					resumeData += '	</div>';
					resumeData += '	<div class="col-sm-3 col-xs-6">';
					resumeData += '		<div class="description-block border-right">';
					resumeData += '			<h5 class="description-header" style="font-size: 60px;"><span class="description-percentage text-purple">'+ totalActual.toLocaleString() +'</span></h5>';
					resumeData += '			<span class="description-text" style="font-size: 35px;">Total Actual<br><span class="text-purple">実績の集計</span></span>';
					resumeData += '		</div>';
					resumeData += '	</div>';
					resumeData += '	<div class="col-sm-3 col-xs-6">';
					resumeData += '		<div class="description-block">';
					resumeData += '			<h5 class="description-header" style="font-size: 60px;">'+ totalCaret + '' +Math.abs(totalActual-totalPlan).toLocaleString() +'</span></h5>';
					resumeData += '			<span class="description-text" style="font-size: 35px;">Difference<br><span class="text-purple">差異</span></span>';
					resumeData += '		</div>';
					resumeData += '	</div>';
					// start add percentage
					resumeData += '	<div class="col-sm-3 col-xs-6">';
					resumeData += '		<div class="description-block">';
					resumeData += '			<h5 class="description-header" style="font-size: 60px;">'+ persenColor + ''+ Math.abs((totalActual/totalPlan)*100).toFixed(2) +'%</span></h5>';
					resumeData += '			<span class="description-text" style="font-size: 35px;">Percentage(%)<br><span class="text-purple">差異実績</span></span>';
					resumeData += '		</div>';
					resumeData += '	</div>';
					// end add percentage
					$('#resume').append(resumeData);
					setTimeout(fillChart, 10000);
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}
		});

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
}
</script>
@endsection
