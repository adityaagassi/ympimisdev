@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	#main tbody>tr>td {
		text-align:center;
	}

	table {
		border:2px solid #333;
	}

	thead>tr>th {
		border:2px solid #333;
		background-color: white;
		text-align: center;
		font-size: 2vw;
	}

	tbody>tr>td {
		border:2px solid #333;
		font-size: 1.5vw;
		color: #333;
		font-weight: bold;
		text-align:center;
	}
	#loading { display: none; }

</style>
@stop
@section('header')
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-md-12" style="padding-bottom:10px;">
			<div class="input-group">
				<span class="input-group-addon"><i class="fa fa-barcode"></i></span>
				<input type="text" class="form-control input-lg" placeholder="Scan Kanban Solder . . ." style="text-align: center" id="tag">
				<span class="input-group-addon"><i class="fa fa-barcode"></i></span>
			</div>
		</div>
		<div class="col-md-12">
			<table class="table">
				<thead>
					<th>Material Number</th>
					<th>Model</th>
					<th>Key</th>
					<th>Kanban</th>
					<th>Quantity</th>
				</thead>
				<tbody id="tableBody">
				</tbody>
			</table>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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

	jQuery(document).ready(function() {
		$("#tag").focus();

		drawTable();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag").val().length == 10){
				var str = $("#tag").val();
				scanTag(str);
				$("#tag").focus();
			}
			else{
				audio_error.play();
				openErrorGritter('Error', 'Barcode tidak valid. Silahkan scan ulang.');
				$("#tag").val("");
				$('#tag').focus();
			}
		}
	});

	function scanTag(tag) {
		$("#loading").show();
		var data = {
			tag:$("#tag").val()
		}

		$.get('{{ url("scan/middle/request") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				openSuccessGritter('Success!', result.message);
				drawTable();
				$("#tag").val("");
				$('#tag').focus();
			}
			else{
				$("#loading").hide();
				audio_error.play();
				openErrorGritter('Error', result.message);
				$("#tag").val("");
				$('#tag').focus();
			}
		});
	}

	function drawTable() {
		var data = {
			origin_group_code:"{{$option}}"
		}

		$.get('{{ url("fetch/middle/request") }}', data, function(result, status, xhr){
			if(result.status){
				var material_req = [];
				var cat = [];
				var limit = [];

				var tableData = "";

				$('#tableBody').html("");
				$.each(result.datas, function(index, value) {

					if(value.model[0] == 'A'){
						tableData += "<tr>";
						tableData += "<td style='padding-top: 0px; padding-bottom: 0px; background-color: #ffff66;'>"+value.material_number+"</td>";
						tableData += "<td style='padding-top: 0px; padding-bottom: 0px; background-color: #ffff66;'>"+value.model+"</td>";
						tableData += "<td style='padding-top: 0px; padding-bottom: 0px; background-color: #ffff66;'>"+value.key+"</td>";
						tableData += "<td style='padding-top: 0px; padding-bottom: 0px; background-color: #ffff66;'>"+value.kanban+"</td>";
						tableData += "<td style='padding-top: 0px; padding-bottom: 0px; background-color: #ffff66; text-align:right;'>"+value.quantity+"</td>";
						tableData += "</tr>";
					}else if(value.model[0] == 'T'){
						tableData += "<tr>";
						tableData += "<td style='padding-top: 0px; padding-bottom: 0px; background-color: rgb(157,255,105);'>"+value.material_number+"</td>";
						tableData += "<td style='padding-top: 0px; padding-bottom: 0px; background-color: rgb(157,255,105);'>"+value.model+"</td>";
						tableData += "<td style='padding-top: 0px; padding-bottom: 0px; background-color: rgb(157,255,105);'>"+value.key+"</td>";
						tableData += "<td style='padding-top: 0px; padding-bottom: 0px; background-color: rgb(157,255,105);'>"+value.kanban+"</td>";
						tableData += "<td style='padding-top: 0px; padding-bottom: 0px; background-color: rgb(157,255,105); text-align:right;'>"+value.quantity+"</td>";
						tableData += "</tr>";
					}


					
				});

				$('#tableBody').append(tableData);





				// Highcharts.chart('chart', {
				// 	chart: {
				// 		backgroundColor: null,
				// 		type: 'column',
				// 	},
				// 	exporting: { enabled: false },
				// 	title: {
				// 		text: null
				// 	},
				// 	tooltip: {
				// 		pointFormat: 'Quantity: <b>{point.y} Kanban</b>'
				// 	},
				// 	xAxis: {
				// 		labels: {
				// 			style: {
				// 				color: '#9dff69',
				// 				fontSize: '12px',
				// 				fontWeight: 'bold'
				// 			}
				// 		},
				// 		categories: cat
				// 	},
				// 	yAxis: {
				// 		tickInterval: 1,
				// 		title: {
				// 			text: 'Quantity (Kanban)'
				// 		},
				// 		plotLines: [{
				// 			color: 'red',
				// 			width: 2,
				// 			value: 2,
				// 			zIndex: 5,
				// 			dashStyle: 'Dash'
				// 		}]
				// 	},
				// 	plotOptions: {
				// 		column: {
				// 			allowPointSelect: true,
				// 			borderColor: 'black',
				// 			dataLabels: {
				// 				enabled: true,
				// 				format: '<b>{point.name}<br/>{point.y}</b>',
				// 				distance: -50,
				// 				style:{
				// 					fontSize:'14px',
				// 					textOutline:0,
				// 				},
				// 			},
				// 			zones: [{
				// 				value: 2, 
				// 				color: '#46e83a' 
				// 			},{
				// 				color: '#f55656' 
				// 			}]
				// 		}, 
				// 		series: {
				// 			animation: false
				// 		}
				// 	},
				// 	credits: {
				// 		enabled: false
				// 	},
				// 	series: [{
				// 		name: 'Material',
				// 		data: material_req
				// 	}]
				// });
			}
		});
	}

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#8dff69', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
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
					color: '#ddd'
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

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '3000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '3000'
		});
	}
</script>
@endsection