@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150);
		text-align: center;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
		padding:0;
		font-size: 12px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		padding:0;
		vertical-align: middle;
		text-align: center;
	}
	.content{
		color: white;
		font-weight: bold;
	}
	.progress {
		background-color: rgba(0,0,0,0);
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div class="row">
		<div class="col-xs-12" style="padding: 0px; margin-top: 0;">
			<div id="container" style="height: 500px;"></div>
		</div>
	</div>

</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<span id="title_modal" style="font-weight: bold; font-size: 1.5vw;"></span>
				</center>
				<hr>

				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<div class="col-xs-12" style="padding-bottom: 5px;">
						<table class="table table-hover table-bordered" id="tableDetail">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 10%; vertical-align: middle;">Material</th>
									<th style="width: 40%; vertical-align: middle;">Description</th>
									<th style="width: 20%; vertical-align: middle;">Location</th>
									<th style="width: 10%; vertical-align: middle;">In Date</th>
									<th style="width: 10%; vertical-align: middle;">Exp Date</th>
									<th style="width: 10%; vertical-align: middle;">Qty</th>
								</tr>
							</thead>
							<tbody id="tableDetailBody">
							</tbody>
							<tfoot id="tableDetailFoot">
							</tfoot>
						</table>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</div>

@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('.select2').select2();
		fillTable();
		setInterval(fillTable, 30000);
	});

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

	function change() {
		$("#location").val($("#locationSelect").val());
	}

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: "dd-mm-yyyy",
		endDate: '<?php echo $tgl_max ?>'
	});

	function fillTable() {

		$.get('{{ url("fetch/indirect_material_monitoring") }}', function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					
					

					// $('#last_update').html('<b>'+ title_text +'</b>');
					

					var series = [];
					var categories = [];
					

					for (var i = 0; i < result.data.length; i++) {
						categories.push('Expired');
						series.push(parseInt(result.data[i].exp));
						categories.push('< 30 Days');
						series.push(parseInt(result.data[i].one_month));
						categories.push('< 90 Days');
						series.push(parseInt(result.data[i].three_month));
						categories.push('< 180 Days');
						series.push(parseInt(result.data[i].six_month));
						categories.push('< 270 Days');
						series.push(parseInt(result.data[i].nine_month));
						categories.push('< 1 Year');
						series.push(parseInt(result.data[i].twelve_month));
						categories.push('> 1 Year');
						series.push(parseInt(result.data[i].more_year));


					}

					var chart = Highcharts.chart('container', {
						title: {
							text: 'Chemical Expired Monitoring',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						yAxis: {
							title: {
								enabled: true,
								text: "Quantity"
							},
							plotLines: [{
								color: '#FF0000',
								width: 2,
								value: 480,
								label: {
									align:'right',
									text: '480min',
									x:-7,
									style: {
										fontSize: '1vw',
										color: '#FF0000',
										fontWeight: 'bold'
									}
								}
							}],
						},
						xAxis: {
							categories: categories,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '26px'
								}
							},
						},
						credits: {
							enabled:false
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y} Minutes</b> <br/>',
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
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											fetchModal(this.category);
										}
									}
								}
							}
						},
						series: [
						{
							name:'Time',
							type: 'column',
							colorByPoint: true,
							data: series,
							showInLegend: false
						}
						]

					});

				}
			}
		});
	}

	function fetchModal(category) {

		var data = {
			category : category
		}


		$.get('{{ url("fetch/indirect_material_monitoring_detail") }}', data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					$('#tableDetailBody').html("");
					$('#tableDetailFoot').html("");

					$('#title_modal').text('Chemical ' + category);
					var detail = '';
					var detailDataFoot = '';
					var total = 0;


					$.each(result.data, function(key, value){
						detail += '<tr>';
						detail += '<td>'+value.material_number+'</td>';
						detail += '<td>'+(value.material_description || '-')+'</td>';
						detail += '<td>'+(value.storage_location || '-')+'</td>';
						detail += '<td>'+value.in_date+'</td>';
						detail += '<td>'+value.exp_date+'</td>';
						detail += '<td>'+value.qty+'</td>';
						detail += '</tr>';

						total += value.qty;

					});
					$('#tableDetailBody').append(detail);

					detailDataFoot += '<tr>';
					detailDataFoot += '<td style="background-color: rgb(252, 248, 227);" colspan="5">Total</td>';
					detailDataFoot += '<td style="background-color: rgb(252, 248, 227);"><b>'+total+'</b></td>';
					detailDataFoot += '</tr>';
					$('#tableDetailBody').append(detailDataFoot);


					
					$('#modalDetail').modal('show');


				}
			}
		});
		
	}

</script>
@stop