@extends('layouts.master')
@section('stylesheets')
{{-- <link href="{{ url("css/jquery-ui.css") }}" rel="stylesheet"> --}}
<style type="text/css">
	.picker {
		text-align: center;
	}
	.button {
		position: absolute;
		top: 50%;
	}
	.nav-tabs-custom > ul.nav.nav-tabs {
		display: table;
		width: 100%;
		table-layout: fixed;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li {
		float: none;
		display: table-cell;
	}
	.nav-tabs-custom > ul.nav.nav-tabs > li > a {
		text-align: center;
	}
	.vendor-tab{
		width:100%;
	}
	.btn-active {
		border: 5px solid rgb(255,77,77) !important;
	}
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
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Production Result <span class="text-purple">生産実績</span>
		{{-- <small>By Shipment Schedule <span class="text-purple">??????</span></small> --}}
	</h1>
	<ol class="breadcrumb" id="last_update">
	</ol>
</section>
@stop
@section('content')
<section class="content" style="padding-top: 0">
	<div class="col-xs-10">
		<div class="col-md-12 picker" id="weekResult">
		</div>
		<div class="col-md-12">
		</div>
		<div class="col-md-12 picker" id="dateResult" style="margin: 1;">
		</div>
		<div class="col-md-12">
			<br>
		</div>
	</div>
	<div class="col-xs-2">
		<div class="row">
			<span class="text-red"><i class="fa fa-info-circle"></i> Select Other Date</span>
		</div>
		<div class="row">
			<div class="col-xs-7">
				<div class="row">
					<div class="input-group date">
						<div class="input-group-addon bg-olive">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control pull-right" id="datepicker" name="datepicker">
					</div>
				</div>
			</div>
			<div class="col-xs-5">
				<button id="search" onClick="searchDate()" class="btn bg-olive">Search</button>
			</div>
		</div>
	</div>
	{{-- <div class="col-xs-2">
		<span class="text-red"><i class="fa fa-info-circle"></i> Select Other Date</span>
	</div> --}}
	<div class="row">
		<input type="hidden" name="dateHidden" value="{{ date('Y-m-d') }}" id="dateHidden">
		<div class="col-md-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Production Result<br><span class="text-purple">生産実績</span></a></li>
					<li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">BI Production Accuracy<br><span class="text-purple">BI週次出荷</span></a></li>
					<li class="vendor-tab"><a href="#tab_3" data-toggle="tab" id="tab_header_3">BI Weekly Shipment<br><span class="text-purple">BI週次出荷</span></a></li>
					{{-- <li class="vendor-tab"><a href="#tab_4" data-toggle="tab" id="tab_header_4">EI Production Result<br><span class="text-purple">EI生産実績</span></a></li> --}}
					<li class="vendor-tab"><a href="#tab_5" data-toggle="tab" id="tab_header_5">EI Production Accuracy<br><span class="text-purple">EI週次出荷</span></a></li>
					<li class="vendor-tab"><a href="#tab_6" data-toggle="tab" id="tab_header_6">EI Weekly Shipment<br><span class="text-purple">EI週次出荷</span></a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="tab_1">
						<div id="container1" style="width:100%; height:530px;"></div>
					</div>
					<div class="tab-pane" id="tab_2">
						<div id="container2" style="width:100%; height:530px;"></div>
					</div>
					<div class="tab-pane" id="tab_3">
						<div id="container3" style="width:100%; height:530px;"></div>
					</div>
					{{-- <div class="tab-pane" id="tab_4">
						<div id="container4" style="width:100%; height:520px;"></div>
					</div> --}}
					<div class="tab-pane" id="tab_5">
						<div id="container5" style="width:100%; height:530px;"></div>
					</div>
					<div class="tab-pane" id="tab_6">
						<div id="container6" style="width:100%; height:530px;"></div>
					</div>
				</div>
			</div>			
		</div>
	</div>
</section>

<div class="modal fade" id="modalResult">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="modalResultTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i></center>
						<table class="table table-hover table-bordered table-striped" id="tableResult">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Material</th>
									<th>Description</th>
									<th>Quantity</th>
								</tr>
							</thead>
							<tbody id="modalResultBody">
							</tbody>
							<tfoot style="background-color: RGB(252, 248, 227);">
								<th>Total</th>
								<th></th>
								<th id="modalResultTotal"></th>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>



	@endsection
	@section('scripts')
	<script src="{{ url("js/highcharts.js")}}"></script>
	<script src="{{ url("js/exporting.js")}}"></script>
	<script src="{{ url("js/export-data.js")}}"></script>
	{{-- <script src="{{ url("js/jquery-ui.js")}}"></script> --}}
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		jQuery(document).ready(function() {
			$('#datepicker').datepicker({
				autoclose: true,
				todayHighlight: true
			});
			$('body').toggleClass("sidebar-collapse");
			fillWeek();
			fillDate();
			fillChart($('#dateHidden').val());
			setInterval(function(){
				fillChart($('#dateHidden').val());
			}, 10000);
		});

		$(function() {
			$(document).keydown(function(e) {
				switch(e.which) {
					case 48:
					location.reload(true);
					break;
					case 49:
					$("#tab_header_1").click()
					break;
					case 50:
					$("#tab_header_2").click()
					break;
					case 51:
					$("#tab_header_3").click()
					break;
				// case 52:
				// $("#tab_header_4").click()
				// break;
				case 52:
				$("#tab_header_5").click()
				break;
				case 53:
				$("#tab_header_6").click()
				break;
			}
		});
		});

		function searchDate(){
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


			var date = $.date($('#datepicker').val());

			if($('#datepicker').val() != 0){
				fillChart(date);
			}
		}

		function fillWeek(){
			$.get('{{ url("fetch/daily_production_result_week") }}', function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){
						$('#weekResult').html('');
						var weekData = '';
						$.each(result.weekData, function(key, value) {
							weekData += '<button type="button" class="btn bg-purple btn-lg" id="' + value.week_name + '" onClick="fillDate(id)">' + value.week + '</button>&nbsp;';
						});
						$('#weekResult').append(weekData);
					}
					else{
						alert('Attempt to retrieve data failed');
					}
				}
				else{
					alert('Disconnected from server');
				}
			});
		}

		function fillDate(id){
			$("#weekResult .btn").removeClass("btn-active");
			$("#"+id+"").addClass("btn-active");
			var data = {
				week:id,
			}
			$.get('{{ url("fetch/daily_production_result_date") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){
						$('#dateResult').html('');
						var dateData = '';
						$.each(result.dateData, function(key, value) {
							dateData += '<button type="button" class="btn bg-olive" id="' + value.week_date + '" onClick="fillChart(id)">' + value.week_date_name + '</button>&nbsp;';
						});
						$('#dateResult').append(dateData);
					}
					else{
						alert('Attempt to retrieve data failed');
					}
				}
				else{
					alert('Disconnected from server');
				}			
			});		
		}

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

		function fillChart(id){
			if(id != 0){
				$('#dateHidden').val(id);
			}
			var date = id;
			var data = {
				date:date,
			};
			$.get('{{ url("fetch/daily_production_result") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){
						$('#last_update').html('<b>Last Updated: '+ getActualFullDate() +'</b>');
						var data = result.chartResult1;
						var xAxis = []
						, planCount = []
						, actualCount = []
						, xAxisEI = []
						, planCountEI = []
						, actualCountEI = []

						$("#dateResult .btn").removeClass("btn-active");
						$("#"+date+"").addClass("btn-active");

						for (i = 0; i < data.length; i++) {
						// if(jQuery.inArray(data[i].hpl, ['CLFG', 'ASFG', 'TSFG', 'FLFG']) !== -1){
							xAxis.push(data[i].hpl);
							planCount.push(data[i].plan);
							actualCount.push(data[i].actual);							
						// }
						// if(jQuery.inArray(data[i].hpl, ['RC', 'VENOVA', 'PN']) !== -1){
						// 	xAxisEI.push(data[i].hpl);
						// 	planCountEI.push(data[i].plan);
						// 	actualCountEI.push(data[i].actual);
						// }
					}

					var yAxisLabels = [0,25,50,75,110];
					Highcharts.chart('container1', {
						colors: ['rgba(255, 0, 0, 0.25)','rgba(75, 30, 120, 0.70)'],
						chart: {
							type: 'column',
							backgroundColor: null
						},
						legend: {
							enabled:true,
							itemStyle: {
								fontSize:'20px',
								font: '20pt Trebuchet MS, Verdana, sans-serif',
								color: '#000000'
							}
						},
						credits: {
							enabled: false
						},
						title: {
							text: '<span style="font-size: 3vw;">Production Result</span><br><span style="color: rgba(96, 92, 168);">'+ result.week +'</span> (<span style="color: rgba(61, 153, 112);">'+ result.dateTitle +'</span>)',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: xAxis,
							labels: {
								style: {
									color: 'rgba(75, 30, 120)',
									fontSize: '30px',
									fontWeight: 'bold'
								}
							}
						},
						yAxis: {
							tickPositioner: function() {
								return yAxisLabels;
							},
							labels: {
								enabled:false
							},
							min: 0,
							title: {
								text: ''
							},
							stackLabels: {
								format: 'Total: {total:,.0f}set(s)',
								enabled: true,
								style: {
									fontWeight: 'bold',
									color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
								}
							}
						},
						tooltip: {
							headerFormat: '<b>{point.x}</b><br/>',
							pointFormat: '{series.name}: {point.y}set(s) {point.percentage:.0f}%'
						},
						plotOptions: {
							column: {
								minPointLength: 1,
								pointPadding: 0.2,
								size: '95%',
								borderWidth: 0,
								events: {
									legendItemClick: function () {
										return false; 
									}
								},
								animation:{
									duration:0
								}
							},
							series: {
								pointPadding: 0.95,
								groupPadding: 0.95,
								borderWidth: 0.95,
								shadow: false,
								borderColor: '#303030',
								cursor: 'pointer',
								stacking: 'percent',
								point: {
									events: {
										click: function () {
											modalResult(this.category, this.series.name, result.now, result.first, result.last);
										}
									}
								},
								dataLabels: {
									format: '{point.percentage:.0f}%',
									enabled: true,
									color: '#000000',
									style: {
										textOutline: false,
										fontWeight: 'bold',
										fontSize: '3vw'
									}
								}
							}
						},
						series: [{
							name: 'Plan',
							data: planCount
						}, {
							name: 'Actual',
							data: actualCount
						}]
					});

					// Highcharts.chart('container4', {
					// 	colors: ['rgba(255, 0, 0, 0.25)','rgba(75, 30, 120, 0.70)'],
					// 	chart: {
					// 		type: 'column',
					// 		backgroundColor: null
					// 	},
					// 	legend: {
					// 		enabled:true,
					// 		itemStyle: {
					// 			fontSize:'20px',
					// 			font: '20pt Trebuchet MS, Verdana, sans-serif',
					// 			color: '#000000'
					// 		}
					// 	},
					// 	credits: {
					// 		enabled: false
					// 	},
					// 	title: {
					// 		text: '<span style="font-size: 30px;">Production Result</span><br><span style="color: rgba(96, 92, 168);">'+ result.week +'</span> (<span style="color: rgba(61, 153, 112);">'+ result.dateTitle +'</span>)'
					// 		// style: {
					// 		// 	fontSize: '30px',
					// 		// 	fontWeight: 'bold'
					// 		// }
					// 	},
					// 	xAxis: {
					// 		categories: xAxisEI,
					// 		labels: {
					// 			style: {
					// 				color: 'rgba(75, 30, 120)',
					// 				fontSize: '20px',
					// 				fontWeight: 'bold'
					// 			}
					// 		}
					// 	},
					// 	yAxis: {
					// 		tickPositioner: function() {
					// 			return yAxisLabels;
					// 		},
					// 		labels: {
					// 			enabled:false
					// 		},
					// 		min: 0,
					// 		title: {
					// 			text: ''
					// 		},
					// 		stackLabels: {
					// 			format: 'Total: {total:,.0f}set(s)',
					// 			enabled: true,
					// 			style: {
					// 				fontWeight: 'bold',
					// 				color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
					// 			}
					// 		}
					// 	},
					// 	tooltip: {
					// 		headerFormat: '<b>{point.x}</b><br/>',
					// 		pointFormat: '{series.name}: {point.y}set(s) {point.percentage:.0f}%'
					// 	},
					// 	plotOptions: {
					// 		column: {
					// 			minPointLength: 1,
					// 			pointPadding: 0.2,
					// 			size: '95%',
					// 			borderWidth: 0,
					// 			events: {
					// 				legendItemClick: function () {
					// 					return false; 
					// 				}
					// 			},
					// 			animation:{
					// 				duration:0
					// 			}
					// 		},
					// 		series: {
					// 			pointPadding: 0.95,
					// 			groupPadding: 0.95,
					// 			borderWidth: 0.95,
					// 			shadow: false,
					// 			borderColor: '#303030',
					// 			cursor: 'pointer',
					// 			stacking: 'percent',
					// 			point: {
					// 				events: {
					// 					click: function () {
					// 						modalResult(this.category, this.series.name, result.now, result.first, result.last);
					// 					}
					// 				}
					// 			},
					// 			dataLabels: {
					// 				format: '{point.percentage:.0f}%',
					// 				enabled: true,
					// 				color: '#000000',
					// 				style: {
					// 					textOutline: false,
					// 					fontWeight: 'bold',
					// 					fontSize: '30px'
					// 				}
					// 			}
					// 		}
					// 	},
					// 	series: [{
					// 		name: 'Plan',
					// 		data: planCountEI
					// 	}, {
					// 		name: 'Actual',
					// 		data: actualCountEI
					// 	}]
					// });

					var data2 = result.chartResult2;
					var xAxis2 = []
					, plusCount = []
					, minusCount = []
					, xAxis2EI = []
					, plusCountEI = []
					, minusCountEI = []

					for (i = 0; i < data2.length; i++) {
						if(jQuery.inArray(data2[i].hpl, ['CLFG', 'ASFG', 'TSFG', 'FLFG']) !== -1){
							xAxis2.push(data2[i].hpl);
							plusCount.push(data2[i].plus);
							minusCount.push(data2[i].minus);
						}
						if(jQuery.inArray(data2[i].hpl, ['VENOVA', 'RC', 'PN']) !== -1){
							xAxis2EI.push(data2[i].hpl);
							plusCountEI.push(data2[i].plus);
							minusCountEI.push(data2[i].minus);
						}
					}

					Highcharts.chart('container2', {
						colors: ['rgba(75, 30, 120, 0.60)', 'rgba(255, 0, 0, 0.60)'],
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 3vw;">Production Accuracy</span><br><span style="color: rgba(96, 92, 168);">'+ result.week +'</span> (<span style="color: rgba(61, 153, 112);">'+ result.dateTitle +'</span>)',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: xAxis2,
							labels: {
								style: {
									color: 'rgba(75, 30, 120)',
									fontSize: '30px',
									fontWeight: 'bold'
								}
							}
						},
						yAxis: {
							title: {
								text: 'Set(s)'
							}
						},
						legend: {
							enabled:true,
							itemStyle: {
								fontSize:'20px',
								font: '20pt Trebuchet MS, Verdana, sans-serif',
								color: '#000000'
							}
						},
						credits: {
							enabled: false
						},
						plotOptions: {
							column: {
								// minPointLength: 2,
								pointPadding: 0,
								size: '100%',
								borderWidth: 1
							},
							series: {
								groupPadding: 0.1,
								borderColor: '#303030',
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									format: '{point.y:,.0f}',
									style:{
										fontSize:'3vw',
										color:'black',
										textOutline: false
									}
								},
								animation:{
									duration:0
								},
								point: {
									events: {
										click: function () {
											modalAccuracy(this.category, this.series.name, result.now, result.first, result.last);
										}
									}
								},
							}
						},
						series: [{
							name: 'Plus',
							data: plusCount
						}, {
							name: 'Minus',
							data: minusCount
						}]
					});

					Highcharts.chart('container5', {
						colors: ['rgba(75, 30, 120, 0.60)', 'rgba(255, 0, 0, 0.60)'],
						chart: {
							type: 'column'
						},
						title: {
							text: '<span style="font-size: 3vw;">Production Accuracy</span><br><span style="color: rgba(96, 92, 168);">'+ result.week +'</span> (<span style="color: rgba(61, 153, 112);">'+ result.dateTitle +'</span>)',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: xAxis2EI,
							labels: {
								style: {
									color: 'rgba(75, 30, 120)',
									fontSize: '30px',
									fontWeight: 'bold'
								}
							}
						},
						yAxis: {
							title: {
								text: 'Set(s)'
							}
						},
						legend: {
							enabled:true,
							itemStyle: {
								fontSize:'20px',
								font: '20pt Trebuchet MS, Verdana, sans-serif',
								color: '#000000'
							}
						},
						credits: {
							enabled: false
						},
						plotOptions: {
							column: {
								// minPointLength: 2,
								pointPadding: 0,
								size: '100%',
								borderWidth: 1
							},
							series: {
								groupPadding: 0.1,
								borderColor: '#303030',
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									format: '{point.y:,.0f}',
									style:{
										fontSize:'3vw',
										color:'black',
										textOutline: false
									}
								},
								animation:{
									duration:0
								},
								point: {
									events: {
										click: function () {
											modalAccuracy(this.category, this.series.name, result.now, result.first, result.last);
										}
									}
								},
							}
						},
						series: [{
							name: 'Plus',
							data: plusCountEI
						}, {
							name: 'Minus',
							data: minusCountEI
						}]
					});

					var data3 = result.chartResult3;
					var xAxis3 = []
					, planBLCount = []
					, actualBLCount = []
					, xAxis3EI = []
					, planBLCountEI = []
					, actualBLCountEI = []

					for (i = 0; i < data3.length; i++) {
						if(jQuery.inArray(data3[i].hpl, ['CLFG', 'ASFG', 'TSFG', 'FLFG']) !== -1){
							xAxis3.push(data3[i].hpl);
							planBLCount.push(data3[i].prc_plan);
							actualBLCount.push(data3[i].prc_actual);
						}
						if(jQuery.inArray(data3[i].hpl, ['VENOVA', 'RC', 'PN']) !== -1){
							xAxis3EI.push(data3[i].hpl);
							planBLCountEI.push(data3[i].prc_plan);
							actualBLCountEI.push(data3[i].prc_actual);
						}
					}

					Highcharts.chart('container3', {
						colors: ['rgba(255, 0, 0, 0.15)','rgba(255, 69, 0, 0.70)'],
						chart: {
							type: 'column',
							backgroundColor: null
						},
						legend: {
							enabled:true,
							itemStyle: {
								fontSize:'20px',
								font: '20pt Trebuchet MS, Verdana, sans-serif',
								color: '#000000'
							}
						},
						credits: {
							enabled: false
						},
						title: {
							text: '<span style="font-size: 3vw;">Weekly Shipment ETD SUB</span><br><span style="color: rgba(96, 92, 168);">'+ result.weekTitle +'</span>',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: xAxis3,
							labels: {
								style: {
									color: 'rgba(75, 30, 120)',
									fontSize: '30px',
									fontWeight: 'bold'
								}
							}
						},
						yAxis: {
							tickPositioner: function() {
								return yAxisLabels;
							},
							labels: {
								enabled:false
							},
							min: 0,
							title: {
								text: ''
							}
							// stackLabels: {
							// 	format: 'Total: {total:,.0f}set(s)',
							// 	enabled: true,
							// 	style: {
							// 		fontWeight: 'bold',
							// 		color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
							// 	}
							// }
						},
						tooltip: {
							headerFormat: '<b>{point.x}</b><br/>',
							pointFormat: '{series.name}: {point.percentage:.0f}%'
						},
						plotOptions: {
							column: {
								minPointLength: 1,
								pointPadding: 0.2,
								size: '95%',
								borderWidth: 0,
								events: {
									legendItemClick: function () {
										return false; 
									}
								}
							},
							series: {
								animation:{
									duration:0
								},
								pointPadding: 0.95,
								groupPadding: 0.95,
								borderWidth: 0.95,
								shadow: false,
								borderColor: '#303030',
								cursor: 'pointer',
								stacking: 'percent',
								point: {
									events: {
										click: function () {
											modalBL(this.category , this.series.name, result.weekTitle, result.now);
										}
									}
								},
								dataLabels: {
									format: '{point.percentage:.0f}%',
									enabled: true,
									color: '#000000',
									style: {
										textOutline: false,
										fontWeight: 'bold',
										fontSize: '3vw'
									}
								}
							}
						},
						series: [{
							name: 'Plan',
							data: planBLCount
						}, {
							name: 'Actual',
							data: actualBLCount
						}]
					});

					Highcharts.chart('container6', {
						colors: ['rgba(255, 0, 0, 0.15)','rgba(255, 69, 0, 0.70)'],
						chart: {
							type: 'column',
							backgroundColor: null
						},
						legend: {
							enabled:true,
							itemStyle: {
								fontSize:'20px',
								font: '20pt Trebuchet MS, Verdana, sans-serif',
								color: '#000000'
							}
						},
						credits: {
							enabled: false
						},
						title: {
							text: '<span style="font-size: 3vw;">Weekly Shipment ETD SUB</span><br><span style="color: rgba(96, 92, 168);">'+ result.weekTitle +'</span>',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: xAxis3EI,
							labels: {
								style: {
									color: 'rgba(75, 30, 120)',
									fontSize: '30px',
									fontWeight: 'bold'
								}
							}
						},
						yAxis: {
							tickPositioner: function() {
								return yAxisLabels;
							},
							labels: {
								enabled:false
							},
							min: 0,
							title: {
								text: ''
							}
							// stackLabels: {
							// 	format: 'Total: {total:,.0f}set(s)',
							// 	enabled: true,
							// 	style: {
							// 		fontWeight: 'bold',
							// 		color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
							// 	}
							// }
						},
						tooltip: {
							headerFormat: '<b>{point.x}</b><br/>',
							pointFormat: '{series.name}: {point.percentage:.0f}%'
						},
						plotOptions: {
							column: {
								minPointLength: 1,
								pointPadding: 0.2,
								size: '95%',
								borderWidth: 0,
								events: {
									legendItemClick: function () {
										return false; 
									}
								}
							},
							series: {
								animation:{
									duration:0
								},
								pointPadding: 0.95,
								groupPadding: 0.95,
								borderWidth: 0.95,
								shadow: false,
								borderColor: '#303030',
								cursor: 'pointer',
								stacking: 'percent',
								point: {
									events: {
										click: function () {
											modalBL(this.category , this.series.name, result.weekTitle, result.now);
										}
									}
								},
								dataLabels: {
									format: '{point.percentage:.0f}%',
									enabled: true,
									color: '#000000',
									style: {
										textOutline: false,
										fontWeight: 'bold',
										fontSize: '3vw'
									}
								}
							}
						},
						series: [{
							name: 'Plan',
							data: planBLCountEI
						}, {
							name: 'Actual',
							data: actualBLCountEI
						}]
					});
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
			}			
		});	
}

function modalResult(hpl, name, now, first, last){
	$('#modalResult').modal('show');
	$('#loading').show();
	$('#modalResultTitle').hide();
	$('#tableResult').hide();

	var data = {
		hpl:hpl,
		name:name,
		now:now,
		first:first,
		last:last,
	}
	$.get('{{ url("fetch/production_result_modal") }}', data, function(result, status, xhr){
		console.log(status);
		console.log(result);
		console.log(xhr);
		if(xhr.status == 200){
			if(result.status){
				$('#modalResultTitle').html('');
				$('#modalResultTitle').html('Detail of '+ hpl +' '+ name);
				$('#modalResultBody').html('');
				var resultData = '';
				var resultTotal = 0;
				$.each(result.resultData, function(key, value) {
					resultData += '<tr>';
					resultData += '<td>'+ value.material_number +'</td>';
					resultData += '<td>'+ value.material_description +'</td>';
					resultData += '<td>'+ value.quantity.toLocaleString() +'</td>';
					resultData += '</tr>';
					resultTotal += value.quantity;
				});
				$('#modalResultBody').append(resultData);
				$('#modalResultTotal').html('');
				$('#modalResultTotal').append(resultTotal.toLocaleString());

				$('#loading').hide();
				$('#modalResultTitle').show();
				$('#tableResult').show();
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		}
		else{
			alert('Disconnected from server');
		}
	});
}

function modalAccuracy(hpl, name, now, first, last){
	$('#modalResult').modal('show');
	$('#loading').show();
	$('#modalResultTitle').hide();
	$('#tableResult').hide();
	var data = {
		hpl:hpl,
		name:name,
		now:now,
		first:first,
		last:last,
	}
	$.get('{{ url("fetch/production_accuracy_modal") }}', data, function(result, status, xhr){
		console.log(status);
		console.log(result);
		console.log(xhr);
		if(xhr.status == 200){
			if(result.status){
				$('#modalResultTitle').html('');
				$('#modalResultTitle').html('Detail of '+ hpl +' '+ name);
				$('#modalResultBody').html('');
				var accuracyData = '';
				var accuracyTotal = 0;
				$.each(result.accuracyData, function(key, value) {
					if(name == 'Minus' && value.minus < 0){
						accuracyData += '<tr>';
						accuracyData += '<td>'+ value.material_number +'</td>';
						accuracyData += '<td>'+ value.material_description +'</td>';
						accuracyData += '<td>'+ value.minus.toLocaleString() +'</td>';
						accuracyData += '</tr>';
						accuracyTotal += value.minus;
					}
					if(name == 'Plus' && value.plus > 0){
						accuracyData += '<tr>';
						accuracyData += '<td>'+ value.material_number +'</td>';
						accuracyData += '<td>'+ value.material_description +'</td>';
						accuracyData += '<td>'+ value.plus.toLocaleString() +'</td>';
						accuracyData += '</tr>';
						accuracyTotal += value.plus;
					}
				});
				$('#modalResultBody').append(accuracyData);
				$('#modalResultTotal').html('');
				$('#modalResultTotal').append(accuracyTotal.toLocaleString());

				$('#loading').hide();
				$('#modalResultTitle').show();
				$('#tableResult').show();
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		}
		else{
			alert('Disconnected from server');
		}
	});
}

function modalBL(hpl, name, week, date){
	$('#modalResult').modal('show');
	$('#loading').show();
	$('#modalResultTitle').hide();
	$('#tableResult').hide();
	var data = {
		hpl:hpl,
		name:name,
		week:'W'+week.substring(5),
		date:date,
	}
	$.get('{{ url("fetch/production_bl_modal") }}', data, function(result, status, xhr){
		console.log(status);
		console.log(result);
		console.log(xhr);
		if(xhr.status == 200){
			if(result.status){
				$('#modalResultTitle').html('');
				$('#modalResultTitle').html('Detail of '+ hpl +' '+ name);
				$('#modalResultBody').html('');
				var blData = '';
				var blTotal = 0;
				$.each(result.blData, function(key, value) {
					blData += '<tr>';
					blData += '<td>'+ value.material_number +'</td>';
					blData += '<td>'+ value.material_description +'</td>';
					blData += '<td>'+ value.quantity.toLocaleString() +'</td>';
					blData += '</tr>';
					blTotal += value.quantity;
				});
				$('#modalResultBody').append(blData);
				$('#modalResultTotal').html('');
				$('#modalResultTotal').append(blTotal.toLocaleString());
				
				$('#loading').hide();
				$('#modalResultTitle').show();
				$('#tableResult').show();
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		}
		else{
			alert('Disconnected from server');
		}
	});
}

</script>
@endsection