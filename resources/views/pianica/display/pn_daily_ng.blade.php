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
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
		padding: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0px;">
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<div class="row" style="margin:0px;">
					<form method="GET" action="{{ action('Pianica@indexDailyNg') }}">
						<div class="col-xs-2" style="color: black; text-transform: capitalize;">
							<div class="form-group">
								<select class="form-control select2" id='locationSelect' onchange="change()" data-placeholder="Select Location" style="width: 100%;">
									<option value="">Select Location</option>
									<option value="welding">Welding Spot</option>
									<option value="kensa-awal">Kensa Awal</option>
									<option value="kensa-akhir">Kensa Akhir</option>
									<option value="kakunin-visual">Kakunin Visual</option>
								</select>
								<input type="text" name="location" id="location" hidden>			
							</div>
						</div>
						<div class="col-xs-1">
							<button class="btn btn-success" type="submit">Update Chart</button>
						</div>
					</form>
					<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0.75%;padding-right: 1%;font-size: 1vw; color: white;"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-6" style="margin-bottom: 1%;">
			<div id="spot-welding">
				<div id="chart1"></div>
			</div>				
		</div>
		<div class="col-xs-6" style="margin-bottom: 1%;">
			<div id="kensa-awal">
				<div id="chart2"></div>
			</div>				
		</div>
		<div class="col-xs-6" style="margin-bottom: 1%;">
			<div id="kensa-akhir">
				<div id="chart3"></div>
			</div>				
		</div>
		<div class="col-xs-6" style="margin-bottom: 1%;">
			<div id="kakunin-visual">
				<div id="chart4"></div>
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
		$('#date').datepicker({
			autoclose: true
		});
		$('.select2').select2({
		});

		fillChart();
		setInterval(fillChart, 30000);
	});


	function change() {
		$("#location").val($("#locationSelect").val());
	}

	function fillChart(){
		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

		var location = "{{$_GET['location']}}";

		// $('#spot-welding').hide();
		// $('#kensa-awal').hide();
		// $('#kensa-akhir').hide();
		// $('#kakunin-visual').hide();

		// if(location == ''){
		// 	$('#spot-welding').show();
		// 	$('#kensa-awal').show();
		// 	$('#kensa-akhir').show();
		// 	$('#kakunin-visual').show();
		// }else if(location == 'welding'){
		// 	$('#spot-welding').show();
		// }else if(location == 'kensa-awal'){
		// 	$('#kensa-awal').show();
		// }else if(location == 'kensa-akhir'){
		// 	$('#kensa-akhir').show();
		// }else if(location == 'kakunin-visual'){
		// 	$('#kakunin-visual').show();
		// }


		var location = "{{$_GET['location']}}";


		$.get('{{ url("fetch/reportSpotWeldingData") }}', function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					
					var tgl_all = [];
					var mesin1 = [];
					var mesin2 = [];
					var mesin3 = [];
					var mesin4 = [];
					var mesin5 = [];
					var mesin6 = [];



					for(i = 0; i < result.tgl.length; i++){
						tgl_all.push(result.tgl[i].date_a);		
						
					}


					for(i = 0; i < result.ng.length; i++){
						if ( result.ng[i].ng == "H1") {
							mesin1.push(parseInt(result.ng[i].ng_all));
						}

						if ( result.ng[i].ng == "H2") {
							mesin2.push(parseInt(result.ng[i].ng_all));
						}

						if ( result.ng[i].ng == "H3") {
							mesin3.push(parseInt(result.ng[i].ng_all));
						}

						if ( result.ng[i].ng == "M1") {
							mesin4.push(parseInt(result.ng[i].ng_all));
						}

						if ( result.ng[i].ng == "M2") {
							mesin5.push(parseInt(result.ng[i].ng_all));
						}

						if ( result.ng[i].ng == "M3") {
							mesin6.push(parseInt(result.ng[i].ng_all));
						}
						
					}

					
					Highcharts.chart('chart1', {
						chart: {
							type: 'spline'
						},
						title: {
							text: 'Daily Total NG Spot Welding',
							style: {
								fontSize: '25px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: tgl_all
						},
						yAxis: {
							title: {
								text: 'Total'
							}
						},
						plotOptions: {
							series: {
								// lineWidth: 1,
								marker: {
									enabled: false
								}
							},
							line: {
								dataLabels: {
									enabled: true
								},
								enableMouseTracking: false,
							}
						},
						credits: {
							enabled: false
						},
						series: [{

							animation: false,
							name: 'Mesin 1',
							data: mesin1,
							point: {
								events: {
									click: function () {
										fillModal(this.category, this.series.name);
									}
								}
							}
						}, {

							animation: false,
							name: 'Mesin 2',
							data: mesin2,
							point: {
								events: {
									click: function () {
										fillModal(this.category, this.series.name);
									}
								}
							}
						}, {

							animation: false,
							name: 'Mesin 3',
							data: mesin3,
							point: {
								events: {
									click: function () {
										fillModal(this.category, this.series.name);
									}
								}
							}
						}
						, {

							animation: false,
							name: 'Mesin 4',
							data: mesin4,
							point: {
								events: {
									click: function () {
										fillModal(this.category, this.series.name);
									}
								}
							}
						}
						, {

							animation: false,
							name: 'Mesin 5',
							data: mesin5,
							point: {
								events: {
									click: function () {
										fillModal(this.category, this.series.name);
									}
								}
							}
						}, {

							animation: false,
							name: 'Mesin 6',
							data: mesin6,
							point: {
								events: {
									click: function () {
										fillModal(this.category, this.series.name);
									}
								}
							}
						}
						]
					});
				}
			}
		});

		$.get('{{ url("fetch/getReportKensaAwalDaily") }}', function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					
					var tgl_all = [];
					var biri = [];
					var oktaf = [];
					var tinggi = [];
					var rendah = [];

					for(i = 0; i < result.tgl.length; i++){
						tgl_all.push(result.tgl[i].date_a);		
						
					}

					for(i = 0; i < result.ng.length; i++){						
						biri.push(parseInt(result.ng[i].biri));
						oktaf.push(parseInt(result.ng[i].oktaf));
						tinggi.push(parseInt(result.ng[i].tinggi));
						rendah.push(parseInt(result.ng[i].rendah));	
						
					}

					Highcharts.chart('chart2', {
						chart: {
							type: 'spline'
						},
						title: {
							text: 'Daily Total NG Kensa Awal',
							style: {
								fontSize: '25px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: tgl_all
						},
						yAxis: {
							title: {
								text: 'Total'
							}
						},
						credits:{
							enabled: false
						},
						plotOptions: {
							series: {
								// lineWidth: 1,
								marker: {
									enabled: false
								}
							},
							line: {
								dataLabels: {
									enabled: true
								},
								enableMouseTracking: false,
							}
						},
						series: [{

							animation: false,
							name: 'Biri',
							data: biri
						}, {

							animation: false,
							name: 'Oktaf',
							data: oktaf
						}, {

							animation: false,
							name: 'Terlalu Tinggi',
							data: tinggi
						}
						, {

							animation: false,
							name: 'Terlalu Rendah',
							data: rendah
						}

						]
					});
				}
			}
		});

		$.get('{{ url("fetch/getReportKensaAkhirDaily") }}', function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					
					var tgl_all = [];
					var biri = [];
					var oktaf = [];
					var tinggi = [];
					var rendah = [];



					for(i = 0; i < result.tgl.length; i++){
						tgl_all.push(result.tgl[i].date_a);		
						
					}


					for(i = 0; i < result.ng.length; i++){						
						biri.push(parseInt(result.ng[i].biri));
						oktaf.push(parseInt(result.ng[i].oktaf));
						tinggi.push(parseInt(result.ng[i].tinggi));
						rendah.push(parseInt(result.ng[i].rendah));	
						
					}

					
					Highcharts.chart('chart3', {
						chart: {
							type: 'spline'
						},
						title: {
							text: 'Daily Total NG Kensa Akhir',
							style: {
								fontSize: '25px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: tgl_all
						},
						yAxis: {
							title: {
								text: 'Total'
							}
						},
						credits:{
							enabled: false
						},
						plotOptions: {
							series: {
								// lineWidth: 1,
								marker: {
									enabled: false
								}
							},
							line: {
								dataLabels: {
									enabled: true
								},
								enableMouseTracking: false,
							}
						},
						series: [{

							animation: false,
							name: 'Biri',
							data: biri
						}, {

							animation: false,
							name: 'Oktaf',
							data: oktaf
						}, {

							animation: false,
							name: 'Terlalu Tinggi',
							data: tinggi
						}
						, {

							animation: false,
							name: 'Terlalu Rendah',
							data: rendah
						}

						]
					});
				}
			}
		});

		$.get('{{ url("fetch/getReportVisualDaily") }}', function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					
					var tgl_all = [];
					var frame = [];
					var r_l = [];
					var lower = [];
					var handle = [];
					var button = [];
					var pianica = [];
					

					for(i = 0; i < result.tgl.length; i++){
						tgl_all.push(result.tgl[i].date_a);		
						
					}


					for(i = 0; i < result.ng.length; i++){						
						frame.push(parseInt(result.ng[i].frame));
						r_l.push(parseInt(result.ng[i].r_l));
						lower.push(parseInt(result.ng[i].lower));
						handle.push(parseInt(result.ng[i].handle));
						button.push(parseInt(result.ng[i].button));
						pianica.push(parseInt(result.ng[i].pianica));	
						
					}

					
					Highcharts.chart('chart4', {
						chart: {
							type: 'spline'
						},
						title: {
							text: 'Daily Total NG Kakunin Visual',
							style: {
								fontSize: '25px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: tgl_all
						},
						yAxis: {
							title: {
								text: 'Total'
							}
						},
						credits:{
							enabled: false
						},
						plotOptions: {
							series: {
								// lineWidth: 1,
								marker: {
									enabled: false
								}
							},
							line: {
								dataLabels: {
									enabled: true
								},
								enableMouseTracking: false,
							}
						},
						series: [{

							animation: false,
							name: 'Frame Assy',
							data: frame
						}, {

							animation: false,
							name: 'Cover R/L',
							data: r_l
						}, {

							animation: false,
							name: 'Cover Lower',
							data: lower
						}
						, {

							animation: false,
							name: 'Handle',
							data: handle
						}, {

							animation: false,
							name: 'Button',
							data: button
						}, {

							animation: false,
							name: 'Pianica',
							data: pianica
						}

						]
					});
				}
			}
		});


	}

	function fillModal(cat, name){

	}

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function getActualFullDate(){
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
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

</script>
@endsection