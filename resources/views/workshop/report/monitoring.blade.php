@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
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
	table > thead > tr > th{
		border:1px solid #2a2a2b;
		vertical-align: middle;
		text-align: center;
	}
	table > tbody > tr > td{
		border:1px solid #2a2a2b;
		text-align: center;
		vertical-align: middle;
		font-size: 12px;
	}
	table > tfoot > tr > th{
		border:1px solid #2a2a2b;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
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
		<div class="col-xs-12" style="margin-top: 0px; padding: 0%;">
			<div class="row" style="margin:0px;">
				<form method="GET" action="{{ action('MiddleProcessController@indexBuffingOpEff') }}">
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
					<div class="col-xs-1">
						<button class="btn btn-success" type="submit">Update Chart</button>
					</div>
				</form>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>	
			
			<div id="container1" style="padding: 1%; padding-bottom: 0%; height: 250px;"></div>
			<div id="container2" style="padding-right: 1%; padding-left: 1%;">
				<table id="table-monitor" class="table" style="width: 100%; background-color: #212121;">
					<thead>
						<tr>
							<th style="border: 1px solid #333; width: 7%; padding: 0;vertical-align: middle;;font-size: 16px; border-top: 0px !important;" rowspan="2">Order No.</th>
							<th style="border: 1px solid #333; width: 5%; padding: 0;vertical-align: middle;border-left:3px solid #f44336 !important;font-size: 16px; border-top: 0px !important;" rowspan="2">Priority</th>
							<th style="border: 1px solid #333; width: 15%; padding: 0;vertical-align: middle;border-left:3px solid #f44336 !important;font-size: 16px; border-top: 0px !important;" rowspan="2">Item Name</th>
							<th style="border: 1px solid #333; width: 8%; padding: 0;vertical-align: middle;border-left:3px solid #f44336 !important;font-size: 16px; border-top: 0px !important;" rowspan="2">PIC</th>
							<th style="border: 1px solid #333; padding-top: 0.25%; padding-bottom: 0.25%; width: 25%;vertical-align: middle;border-left:3px solid #f44336 !important;font-size: 16px; border-top: 0px !important;" colspan="4">Status</th>
							<th style="border: 1px solid #333; width: 25%; padding: 0;vertical-align: middle;border-left:3px solid #f44336 !important;font-size: 16px; border-top: 0px !important;" rowspan="2">Progress</th>
						</tr>
						<tr>
							<th style="border: 1px solid #333; width: 5%; padding: 0; padding-top: 0.25%; padding-bottom: 0.25%; border-left:3px solid #f44336 !important;vertical-align: middle;">Requested</th>
							<th style="border: 1px solid #333; width: 5%; padding: 0;vertical-align: middle;">Listed</th>
							<th style="border: 1px solid #333; width: 5%; padding: 0;vertical-align: middle;">Approved</th>
							<th style="border: 1px solid #333; width: 5%; padding: 0;vertical-align: middle;">In Progress</th>
						</tr>
					</thead>
					<tbody id="table-body-monitor">

					</tbody>
					<tfoot>
					</tfoot>
				</table>
			</div>
			
			

		</div>
	</div>


</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('.select2').select2();
		fillChart();
		setInterval(fillChart, 300000);
		setInterval(setTime, 1000);
	});

	$('.datepicker').datepicker({
		autoclose: true,
		format: "dd-mm-yyyy",
		todayHighlight: true,
	});

	var count_process_bar = false;
	var approved_time = [];
	var target_date = [];
	function setTime() {
		
		for (var i = 0; i < target_date.length; i++) {
			if(approved_time[i] == 0){
				$('#progress_bar_'+i).append().empty();
				$('#progress_bar_'+i).addClass('progress-bar-success');
				$('#progress_bar_'+i).html('0%');
				$('#progress_bar_'+i).css('width', '0%');
				$('#progress_bar_'+i).css('color', 'white');
				$('#progress_bar_'+i).css('margin-left', '2%');
				$('#progress_bar_'+i).css('font-weight', 'bold');
				
			}else{
				var actual = diff_seconds(new Date(), approved_time[i]);
				var target = diff_seconds(target_date[i], approved_time[i]);
				var percent = (actual / target) * 100;

				$('#progress_bar_'+i).append().empty();
				if(percent <= 100){
					$('#progress_bar_'+i).addClass('active');
					$('#progress_bar_'+i).addClass('progress-bar-success');
					$('#progress_bar_'+i).html(Math.round(percent)+'%');
					$('#progress_bar_'+i).css('width', percent+'%');
					$('#progress_bar_'+i).css('color', 'white');
					$('#progress_bar_'+i).css('font-weight', 'bold');
				}else{
					$('#progress_bar_'+i).addClass('active');
					$('#progress_bar_'+i).addClass('progress-bar-danger');
					$('#progress_bar_'+i).html('100%');
					$('#progress_bar_'+i).css('width', '100%');
					$('#progress_bar_'+i).css('color', 'white');
					$('#progress_bar_'+i).css('font-weight', 'bold');
				}
			}

		}

	}

	function diff_seconds(dt2, dt1){
		var diff = (dt2.getTime() - dt1.getTime()) / 1000;
		return Math.abs(Math.round(diff));
	}


	function fillChart() {
		var datefrom = $("#datefrom").val();
		var dateto = $("#dateto").val();

		var data = {
			datefrom : datefrom,
			dateto : dateto
		}

		$.get('{{ url("fetch/workshop/wjo_monitoring") }}', data, function(result, status, xhr){
			if(result.status){
				var date = [];
				var progress = [];
				var finish = [];
				var reject = [];
				for (var i = 0; i < result.wjo.length; i++) {
					date.push(result.wjo[i].week_date);
					progress.push(result.wjo[i].progress);
					finish.push(result.wjo[i].finish);
					reject.push(result.wjo[i].reject);
				}
				$('#container1').highcharts({
					chart: {
						type: 'column'
					},
					title: {
						text: 'Workshop Job Orders Monitoring',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'On '+ date[0] + ' ~ ' + date[(date.length-1)],
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						type: 'category',
						categories: date,
						lineWidth:2,
						lineColor:'#9e9e9e',
						gridLineWidth: 1
					},
					yAxis: {
						lineWidth:2,
						lineColor:'#9e9e9e',
						type: 'linear',
						title: {
							text: 'Total WJO'
						},
						tickInterval: 1,  
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
								color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
							}
						}
					},
					legend: {
						align: 'right',
						x: -30,
						verticalAlign: 'top',
						y: 30,
						itemStyle:{
							color: "white",
							fontSize: "12px",
							fontWeight: "bold",

						},
						floating: true,
						shadow: false
					},
					plotOptions: {
						series: {
							cursor: 'pointer',
							// point: {
							// 	events: {
							// 		click: function () {
							// 			ShowModal(this.category,this.series.name,result.tglfrom,result.tglto,result.kategori,result.departemen);
							// 		}
							// 	}
							// },
							borderWidth: 0,
							dataLabels: {
								enabled: false,
								format: '{point.y}'
							}
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

					tooltip: {
						formatter:function(){
							return this.series.name+' : ' + this.y;
						}
					},
					series: [{
						name: 'Rejected',
						color: '#ff6666',
						data: reject
					},{
						name: 'In Progress',
						data: progress,
						color : '#f0ad4e'
					},{
						name: 'Finished',
						data: finish,
						color : '#5cb85c'
					}]
				});


				$('#table-body-monitor').html("");

				approved_time = [];
				target_date = [];

				var body = '';
				for (var i = 0; i < result.progress.length; i++) {
					body += '<tr>';
					body += '<td style="text-align: center; vertical-align: middle; border: 1px solid #333; padding: 0.25%;">';
					body += result.progress[i].order_no;
					body += '</td>';


					body += '<td style="text-align: center; vertical-align: middle; border: 1px solid #333; border-left: 3px solid #f44336 !important; padding: 0.25%; font-size: 13px;">';
					if(result.progress[i].priority == 'Urgent'){
						body += '<span class="label label-danger">Urgent</span>';
					}else{
						body += '<span class="label label-default">Normal</span>';
					}
					body += '</td>';


					body += '<td style="text-align: center; vertical-align: middle; border: 1px solid #333; border-left: 3px solid #f44336 !important; padding: 0.25%;">';
					body += result.progress[i].item_name;
					body += '</td>';


					body += '<td style="text-align: center; vertical-align: middle; border: 1px solid #333; border-left: 3px solid #f44336 !important; padding: 0.25%; font-size: 13px;">';
					if(result.progress[i].name){
						body += '<span class="label label-success">'+ result.progress[i].name +'</span>';				
					}else{
						body += '<span class="label label-danger">None</span>';
					}
					body += '</td>';


					body += '<td style="text-align: center; vertical-align: middle; border: 1px solid #333; border-left: 3px solid #f44336 !important; padding: 0.25%; font-size: 13px;">';
					if(result.progress[i].requested){
						body += '<span class="label label-success">'+ result.progress[i].requested +'</span>';
					}
					body += '</td>';


					body += '<td style="text-align: center; vertical-align: middle; border: 1px solid #333; padding: 0.25%; font-size: 13px;">';
					if(result.progress[i].listed){
						body += '<span class="label label-success">'+ result.progress[i].listed +'</span>';
					}
					body += '</td>';


					body += '<td style="text-align: center; vertical-align: middle; border: 1px solid #333; padding: 0.25%; font-size: 13px;">';
					if(result.progress[i].approved){
						body += '<span class="label label-success">'+ result.progress[i].approved +'</span>';
					}
					body += '</td>';


					body += '<td style="text-align: center; vertical-align: middle; border: 1px solid #333; padding: 0.25%; font-size: 13px;">';
					if(result.progress[i].progress){
						body += '<span class="label label-success">'+ result.progress[i].progress +'</span>';
					}
					body += '</td>';


					body += '<td style="text-align: center; vertical-align: middle; border: 1px solid #333; border-left: 3px solid #f44336 !important; padding: 0.25%;">';

					if(result.progress[i].target_date){
						if(result.progress[i].approved){
							approved_time.push(new Date(result.progress[i].approved));
						}else{
							approved_time.push(0);
						}
						var date = result.progress[i].target_date.split("-");
						target_date.push(new Date(date[0], (parseInt(date[1]) - 1), date[2], 23, 59, 59, 0));

						body += '<div class="progress-group">';
						body += '<div class="progress" style="background-color: #212121; height: 25px; border: 1px solid; padding: 0px; margin: 0px;">';
						body += '<div class="progress-bar progress-bar-success progress-bar-striped" id="progress_bar_'+i+'" style="font-size: 12px; padding-top: 0.5%;"></div>';
						body += '</div>';
						body += '</div>';
					}
					body += '</td>';				
					body += '</tr>';

				}

				$('#table-body-monitor').append(body);




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
			[0, '#212121'],
			[1, '#212121']
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

</script>
@endsection