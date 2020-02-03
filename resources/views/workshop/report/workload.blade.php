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

	thead>tr>th{
		text-align:center;
		overflow:hidden;
		padding: 3px;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid #2a2a2b;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid #2a2a2b;
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid #2a2a2b;
		text-align: center;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid #2a2a2b;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
</style>
@endsection
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12">
			<div class="row" style="margin:0px;">
				<div class="pull-right" id="last_update" style="color: white; margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>
			<div id="mc-workload" style="width:100%;"></div>
			<div id="op-workload" style="width:100%; margin-top: 1%;"></div>
		</div>
	</div>

	<div class="modal fade" id="modal-operator" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Operator Workload Details</b></h4>
					<h5 class="modal-title" style="text-align: center;" id="judul-operator"></h5>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<table id="operator" class="table table-striped table-bordered" style="width: 100%; margin-bottom: 2%;"> 
								<thead id="operator-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Name</th>
										<th>Order No.</th>
										<th>Tag No.</th>
										<th>Item Name</th>
										<th>Workload<sup>*</sup></th>
									</tr>
								</thead>
								<tbody id="operator-body">
								</tbody>
							</table>
							<span class="pull-left" style="font-weight: bold; background-color: yellow; color: rgb(255,0,0);">&nbsp;(*) Workload in minute(s)&nbsp;</span>

						</div>

					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
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
<script type="text/javascript">
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		fillChart();
		setInterval(fillChart, 10000);
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

	function mode(array){
		var function_name = 'Untuk menghitung jumlah index yg sering muncul';

		if(array.length == 0)
			return null;
		var modeMap = {};
		var maxEl = array[0], maxCount = 1;
		for(var i = 0; i < array.length; i++){
			var el = array[i];
			if(modeMap[el] == null)
				modeMap[el] = 1;
			else
				modeMap[el]++;  
			if(modeMap[el] > maxCount){
				maxEl = el;
				maxCount = modeMap[el];
			}
		}
		return maxCount;
	}

	function fillChart(){
		$.get('{{ url("fetch/workshop/workload") }}', function(result, status, xhr){
			if(result.status){
				$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

				var machine = [];
				var data = [];

				for (var j = 0; j < result.machine.length; j++) {
					machine.push(result.machine[j].shortname);

					var fill = true;
					for (var k = 0; k < result.mc_workload.length; k++) {
						if(result.machine[j].machine_name == result.mc_workload[k].machine_name){
							data.push(parseInt(result.mc_workload[k].workload));
							fill = false;
						}
					}
					if(fill){
						data.push(0);
					}	
				}

				$('#mc-workload').highcharts({
					chart: {
						type: 'column'
					},
					title: {
						text: 'Workshop Machine Workload',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						type: 'category',
						categories: machine,
						lineWidth:2,
						lineColor:'#9e9e9e',
						gridLineWidth: 1
					},
					yAxis: {
						title: {
							text: 'Minute(s)'
						},
						type: 'logarithmic',
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '15px'
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
										alert(this.category);
									}
								}
							},
						},
						column: {
							color:  Highcharts.ColorString,
							stacking: 'normal',
							borderRadius: 1,
							dataLabels: {
								enabled: true
							}
						}
					},
					credits: {
						enabled: false
					},
					legend: {
						enabled: false
					},
					tooltip: {
						formatter:function(){
							return this.series.name+' : ' + this.y;
						}
					},
					series: [{
						name: 'Machine Workload',
						data: data,
						color: 'rgb(144,238,126)'
					}]
				});



				var op = [];
				var data = [];
				var series = [];

				for (var j = 0; j < result.op.length; j++) {
					op.push(result.op[j].name);

					var fill = true;
					for (var k = 0; k < result.op_workload.length; k++) {
						if(result.op[j].operator_id == result.op_workload[k].operator){
							data.push(parseInt(result.op_workload[k].workload));
							fill = false;
						}
					}
					if(fill){
						data.push(0);
					}

					
				}

				for (var j = 0; j < data.length; j++) {
					if(data[j] > 460){
						series.push({y: data[j], color: 'rgb(255,116,116)'})
					}else{
						series.push({y: data[j], color: 'rgb(144,238,126)'});
					}
				}


				$('#op-workload').highcharts({
					chart: {
						type: 'column'
					},
					title: {
						text: 'Workshop Operator Workload',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						type: 'category',
						categories: op,
						lineWidth:2,
						lineColor:'#9e9e9e',
						gridLineWidth: 1
					},
					yAxis: {
						title: {
							text: 'Minute(s)'
						},
						type: 'logarithmic',
						plotLines: [{
							color: '#FF0000',
							value: 460,
							dashStyle: 'shortdash',
							width: 2,
							zIndex: 5,
							label: {
								align:'right',
								text: '460 Minutes',
								x:-7,
								style: {
									fontSize: '12px',
									color: '#FF0000',
									fontWeight: 'bold'
								}
							}
						}]
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '15px'
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
										showOperatorDetail(this.category);
									}
								}
							},
						},
						column: {
							color:  Highcharts.ColorString,
							stacking: 'normal',
							borderRadius: 1,
							dataLabels: {
								enabled: true
							}
						}
					},
					credits: {
						enabled: false
					},
					legend: {
						enabled: false
					},
					tooltip: {
						formatter:function(){
							return this.series.name+' : ' + this.y;
						}
					},
					series:  [{
						name: 'Operator Workload',
						data: series,
					}]
				});

			}
		});
}

function showOperatorDetail(name) {
	var data = {
		name : name
	}

	$.get('{{ url("fetch/workshop/workload_operator_detail") }}', data, function(result, status, xhr){
		if(result.status){
			$('#modal-operator').modal('show');
			$('#operator-body').append().empty();
			$('#judul-operator').append().empty();
			$('#judul-operator').append('<b>'+ result.detail[0].name +' on '+ result.date +'</b>');

			var body = '';
			for (var i = 0; i < result.detail.length; i++) {
				body += '<tr>';
				body += '<td>'+ result.detail[i].name +'</td>';
				body += '<td>'+ result.detail[i].order_no +'</td>';
				body += '<td>'+ result.detail[i].tag_number +'</td>';
				body += '<td style="text-transform: capitalize;">'+ result.detail[i].item_name +'</td>';
				body += '<td>'+ result.detail[i].workload +'</td>';
				body += '</tr>';
			}

			$('#operator-body').append(body);
		}
	});
}


</script>
@endsection