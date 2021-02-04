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
						<input type="text" class="form-control datepicker" id="datefrom" placeholder="Select Date From">
					</div>
				</div>

				<div class="col-xs-2">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border: none;">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="dateto" placeholder="Select Date To">
					</div>
				</div>
				
				<div class="col-xs-2">
					<button class="btn btn-success" onclick="fillChart()">Update Chart</button>
				</div>


			</div>
			<div class="col-xs-12" style="margin-top: 5px;">
				<div id="container1" style="width: 100%;"></div>
				<div id="container2" style="width: 100%;"></div>
			</div>
		</div>
	</div>

	{{-- Modal Detail --}}
	<div class="modal modal-default fade" id="detail_modal">
		<div class="modal-dialog modal-lg" style="width: 85%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h2 class="modal-title" id="detail_title"></h2>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<table id="detailTable" class="table table-bordered table-striped">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="">Employee ID</th>
										<th style="width: 20%">Name</th>
										<th style="">Posisi</th>
										<th style="">Category</th>
										<th style="">No. Document</th>
										<th style="">Valid From</th>
										<th style="">Valid To</th>
										<th style="">Status</th>
										<th style="">Condition</th>
										<th style="">Reminder</th>
										<th style="">Active</th>
									</tr>
								</thead>
								<tbody id="detailBody">
								</tbody>
							</table>
						</div>
					</div>
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
		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();

		var data = {
			datefrom:datefrom,
			dateto:dateto
		}

		$.get('{{ url("fetch/middle/buffing_resume_konseling") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					var date = [];
					var target = [];
					var cek = [];

					for (var i = 0; i < result.ng.length; i++) {
						date.push(result.ng[i].week_date);
						target.push(parseInt(result.ng[i].target) - parseInt(result.ng[i].cek));
						cek.push(parseInt(result.ng[i].cek));
					}

					Highcharts.chart('container1', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'Conseuling NG Rate Progress',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'on '+result.datefrom+' ~ '+result.dateto,
							style: {
								fontSize: '1vw',
								fontWeight: 'bold'
							}
						},	
						legend:{
							align: 'right',
							x: -30,
							verticalAlign: 'top',
							y: 0,
							itemStyle:{
								color: "white",
								fontSize: "12px",
								fontWeight: "bold",

							},
							floating: true,
							shadow: false
						},
						credits:{	
							enabled:false
						},
						xAxis: {
							categories: date,
							type: 'category'
						},
						yAxis: {
							title: {
								enabled:false,
							},
							labels: {
								enabled:false
							}
						},
						tooltip: {
							formatter: function () {
								return '<b>' + this.x + '</b><br/>' +
								this.series.name + ': ' + this.y + '<br/>' +
								'Target: ' + this.point.stackTotal;
							}
						},
						plotOptions: {
							column: {
								stacking: 'percent',
							},
							series:{
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer',
								stacking: 'percent',
								dataLabels: {
									enabled: true,
									formatter: function() {
										return this.y;
									},
									style: {
										fontWeight: 'bold',
									}
								},
								point: {
									events: {
										click: function () {
											showModalNg(this.category);
										}
									}
								}
							}
						},
						series: [{
							name: 'Not yet',
							data: target,
							color: 'rgba(255, 0, 0, 0.25)'
						}, {
							name: 'Conseuling',
							data: cek,
							color: '#00a65a'
						}]
					});

				}
			}
		});

	}

	function showModalNg(argument) {
		
	}



</script>
@endsection