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
@stop
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-1">
					<label style="color: white;">Stuffing Date From:</label>
				</div>
				<div class="col-xs-2">
					<div class="form-group">
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control pull-right" id="datefrom" nama="datefrom">
						</div>
					</div>
				</div>
				<div class="col-xs-1">
					<label style="color: white;">Stuffing Date To:</label>
				</div>
				<div class="col-xs-2">
					<div class="form-group">
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control pull-right" id="dateto" nama="dateto">
						</div>
					</div>
				</div>
				<div class="col-xs-2">
					<button id="search" onClick="fillChart()" class="btn btn-primary bg-purple">Update Chart</button>
				</div>
			</div>
			<div id="container" style="min-width: 310px; height:600px; margin: 0 auto"></div>
		</div>
	</div>
</section>
<div class="modal fade" id="modalProgress">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="modalProgressTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableModal">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>Material</th>
								<th>Description</th>
								<th>Dest.</th>
								<th>Plan</th>
								<th>Actual</th>
								<th>Diff</th>
							</tr>
						</thead>
						<tbody id="modalProgressBody">
						</tbody>
						<tfoot style="background-color: RGB(252, 248, 227);">
							<th>Total</th>
							<th></th>
							<th></th>
							<th id="modalProgressTotal1"></th>
							<th id="modalProgressTotal2"></th>
							<th id="modalProgressTotal3"></th>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

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

	jQuery(document).ready(function() {
		$('#datefrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#dateto').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#datefrom').val("");
		$('#dateto').val("");
		fillChart();
		setInterval(function(){
			fillChart();
		}, 10000);
	});

	function fillModal(cat, name){
		$('#modalProgress').modal('show');
		$('#loading').show();
		$('#modalProgressTitle').hide();
		$('#tableModal').hide();
		var hpl = name;
		if(name == 'VN'){
			var hpl = 'VENOVA';
		}
		if(name == 'FL'){
			var hpl = 'FLFG';
		}
		if(name == 'AS'){
			var hpl = 'ASFG';
		}
		if(name == 'TS'){
			var hpl = 'TSFG';
		}
		if(name == 'CL'){
			var hpl = 'CLFG';
		}
		var data = {
			date:cat,
			hpl:hpl
		}
		$.get('{{ url("fetch/display/modal_shipment_progress") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableModal').DataTable().destroy();
				// $('#modalProgressTitle').html('');
				// $('#modalProgressTitle').html(hpl +' Export Date: '+ date);
				$('#modalProgressBody').html('');
				var resultData = '';
				var resultTotal1 = 0;
				var resultTotal2 = 0;
				var resultTotal3 = 0;
				$.each(result.shipment_progress, function(key, value) {
					resultData += '<tr>';
					resultData += '<td style="width: 10%">'+ value.material_number +'</td>';
					resultData += '<td style="width: 40%">'+ value.material_description +'</td>';
					resultData += '<td style="width: 5%">'+ value.destination_shortname +'</td>';
					resultData += '<td style="width: 15%">'+ value.plan.toLocaleString() +'</td>';
					resultData += '<td style="width: 15%">'+ value.actual.toLocaleString() +'</td>';
					resultData += '<td style="width: 15%; font-weight: bold;">'+ value.diff.toLocaleString() +'</td>';
					resultData += '</tr>';
					resultTotal1 += value.plan;
					resultTotal2 += value.actual;
					resultTotal3 += value.diff;
				});
				$('#modalProgressBody').append(resultData);
				$('#modalProgressTotal1').html('');
				$('#modalProgressTotal1').append(resultTotal1.toLocaleString());
				$('#modalProgressTotal2').html('');
				$('#modalProgressTotal2').append(resultTotal2.toLocaleString());
				$('#modalProgressTotal3').html('');
				$('#modalProgressTotal3').append(resultTotal3.toLocaleString());
				$('#tableModal').DataTable({
					"paging": false,
					'searching': false,
					'order':[],
					'responsive': true,
					'info': false,
					"columnDefs": [{
						"targets": 5,
						"createdCell": function (td, cellData, rowData, row, col) {
							if ( cellData.substring(0,1) ==  "-" ) {
								$(td).css('background-color', 'RGB(255,204,255)')
							}
							else
							{
								$(td).css('background-color', 'RGB(204,255,255)')
							}
						}
					}]
				});
				$('#loading').hide();
				// $('#modalProgressTitle').show();
				$('#tableModal').show();
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function fillChart(){
		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var data = {
			datefrom:datefrom,
			dateto:dateto
		};
		$.get('{{ url("fetch/display/shipment_progress") }}', data, function(result, status, xhr){
			if(result.status){

				var data = result.shipment_results;
				var xCategories = [];
				var planFL = [];
				var planCL = [];
				var planAS = [];
				var planTS = [];
				var planPN = [];
				var planRC = [];
				var planVN = [];
				var actualFL = [];
				var actualCL = [];
				var actualAS = [];
				var actualTS = [];
				var actualPN = [];
				var actualRC = [];
				var actualVN = [];
				var i, cat;
				var intVal = function ( i ) {
					return typeof i === 'string' ?
					i.replace(/[\$,]/g, '')*1 :
					typeof i === 'number' ?
					i : 0;
				};
				for(i = 0; i < data.length; i++){
					cat = data[i].st_date;
					if(xCategories.indexOf(cat) === -1){
						xCategories[xCategories.length] = cat;
					}
					if(data[i].hpl == 'FLFG'){
						planFL.push(data[i].plan-data[i].act);
						actualFL.push(data[i].act);
					}
					if(data[i].hpl == 'CLFG'){
						planCL.push(data[i].plan-data[i].act);
						actualCL.push(data[i].act);
					}
					if(data[i].hpl == 'ASFG'){
						planAS.push(data[i].plan-data[i].act);
						actualAS.push(data[i].act);
					}
					if(data[i].hpl == 'TSFG'){
						planTS.push(data[i].plan-data[i].act);
						actualTS.push(data[i].act);
					}
					if(data[i].hpl == 'PN'){
						planPN.push(data[i].plan-data[i].act);
						actualPN.push(data[i].act);
					}
					if(data[i].hpl == 'RC'){
						planRC.push(data[i].plan-data[i].act);
						actualRC.push(data[i].act);
					}
					if(data[i].hpl == 'VENOVA'){
						planVN.push(data[i].plan-data[i].act);
						actualVN.push(data[i].act);
					}
				}

				if(xCategories.length <= 5){
					var scrollMax = xCategories.length-1;
				}
				else{
					var scrollMax = 4;
				}


				var yAxisLabels = [0,25,50,75,100,110];
				var chart = Highcharts.chart('container', {

					chart: {
						type: 'column',
						backgroundColor: null
					},

					title: {
						text: 'Finished Goods Achievement For Shipment Progress',
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					legend:{
						enabled: true,
						itemStyle: {
							fontSize:'20px'
						}
					},
					credits:{
						enabled:false
					},
					xAxis: {
						categories: xCategories,
						type: 'category',
						gridLineWidth: 5,
						gridLineColor: 'RGB(204,255,255)',
						labels: {	
							style: {
								fontSize: '20px'
							}
						},
						min: 0,
						max:scrollMax,
						scrollbar: {
							enabled: true
						}
					},
					yAxis: {
						title: {
							enabled:false,
						},
						tickPositioner: function() {
							return yAxisLabels;
						},
						// plotLines: [{
						// 	color: '#FF0000',
						// 	width: 2,
						// 	value: 100,
						// 	label: {
						// 		align:'right',
						// 		text: '100%',
						// 		x:-7,
						// 		style: {
						// 			fontSize: '1vw',
						// 			color: '#FF0000',
						// 			fontWeight: 'bold'
						// 		}
						// 	}
						// }],
						labels: {
							enabled:false
						}
						,stackLabels: {
							enabled: true,
							verticalAlign: 'left',
							align:'center',
							style: {
								fontSize: '20px',
								color: 'white',
								textOutline: false,
								fontWeight: 'bold',
							},
							formatter:  function() {
								return this.stack;
							}
						}
					},
					tooltip: {
						formatter: function () {
							return '<b>' + this.x + '</b><br/>' +
							this.series.name + ': ' + this.y + '<br/>' +
							'Total: ' + this.point.stackTotal;
						}
					},
					plotOptions: {
						column: {
							stacking: 'percent',
						},
						series:{
							animation: false,
							// minPointLength: 2,
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								formatter: function() {
									return this.y;
								},
								y:-5,
								style: {
									fontSize:'18px',
									fontWeight: 'bold',
								}
							},
							point: {
								events: {
									click: function () {
										fillModal(this.category, this.series.userOptions.stack);
									}
								}
							}
						}
					},
					series: [{
						name: 'Plan',
						data: planFL,
						stack: 'FL',
						color: 'rgba(255, 0, 0, 0.25)',
						showInLegend: false
					}, {
						name: 'Plan',
						data: planCL,
						stack: 'CL',
						color: 'rgba(255, 0, 0, 0.25)',
						showInLegend: false
					}, {
						name: 'Plan',
						data: planAS,
						stack: 'AS',
						color: 'rgba(255, 0, 0, 0.25)',
						showInLegend: false
					}, {
						name: 'Plan',
						data: planTS,
						stack: 'TS',
						color: 'rgba(255, 0, 0, 0.25)',
						showInLegend: false
					}, {
						name: 'Plan',
						data: planPN,
						stack: 'PN',
						color: 'rgba(255, 0, 0, 0.25)',
						showInLegend: false
					}, {
						name: 'Plan',
						data: planRC,
						stack: 'RC',
						color: 'rgba(255, 0, 0, 0.25)',
						showInLegend: false
					}, {
						name: 'Plan',
						data: planVN,
						stack: 'VN',
						color: 'rgba(255, 0, 0, 0.25)'
					}, {
						name: 'Actual',
						data: actualFL,
						stack: 'FL',
						color: 'rgba(0, 255, 0, 0.90)'
					}, {
						name: 'Actual',
						data: actualCL,
						stack: 'CL',
						color: 'rgba(0, 255, 0, 0.90)',
						showInLegend: false
					}, {
						name: 'Actual',
						data: actualAS,
						stack: 'AS',
						color: 'rgba(0, 255, 0, 0.90)',
						showInLegend: false
					}, {
						name: 'Actual',
						data: actualTS,
						stack: 'TS',
						color: 'rgba(0, 255, 0, 0.90)',
						showInLegend: false
					}, {
						name: 'Actual',
						data: actualPN,
						stack: 'PN',
						color: 'rgba(0, 255, 0, 0.90)',
						showInLegend: false
					}, {
						name: 'Actual',
						data: actualRC,
						stack: 'RC',
						color: 'rgba(0, 255, 0, 0.90)',
						showInLegend: false
					}, {
						name: 'Actual',
						data: actualVN,
						stack: 'VN',
						color: 'rgba(0, 255, 0, 0.90)',
						showInLegend: false
					}]
				});

$('.highcharts-xaxis-labels text').on('click', function () {
	fillModal(this.textContent, 'all');
});

$.each(chart.xAxis[0].ticks, function(i, tick) {
	$('.highcharts-xaxis-labels text').hover(function () {
		$(this).css('fill', '#33c570');
		$(this).css('cursor', 'pointer');

	},
	function () {
		$(this).css('cursor', 'pointer');
		$(this).css('fill', 'white');
	});
});


}
else{
	alert('Attempt to retrieve data failed.')
}
});
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
</script>
@endsection