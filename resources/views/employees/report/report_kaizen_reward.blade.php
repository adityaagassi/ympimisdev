@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	@font-face {
		font-family: JTM;
		src: url("{{ url("fonts/JTM.otf") }}") format("opentype");
	}
	
	td, th {
		color: white;
		text-align: center;
	}

	thead > tr > th {
		text-align: center;
	}

	.dataTable > thead > tr > th[class*="sort"]:after{
		content: "" !important;
	}

	.judul {
		font-family: 'JTM';
		color: white;
		font-size: 35pt;
	}

	.total {
		background-color: white;
		color: black;
		border-color: black !important;
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-left: 0px; padding-right: 0px; padding-top: 0px; overflow-y:hidden; overflow-x:scroll;">
	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-12">
				<div class="col-xs-4 pull-left">
					<p class="judul"><i style="color: #c290d1">e </i> - Kaizen Reward</p>
				</div>
			</div>
			<div class="col-xs-12">
				<table class="table table-bordered">
					<thead>
						<tr id="mon">
						</tr>
					</thead>
					<tbody id="body">
					</tbody>
				</table>
			</div>
			<div class="col-xs-12">
				<div id="chart"></div>
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
<script src="{{ url("js/drilldown.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$("#navbar-collapse").text('');

		getData();
	});

	function getData() {
		$.get('{{ url("fetch/kaizen/value") }}', function(result) {

			var xCategories = [];
			var xValue = [];
			var mainChart = [];

			var ctg;
			var ctg_isi = "";
			var val;
			var val_isi = "";
			var sub_tot = "";

			for(var i = 0; i < result.datas.length; i++){
				val = result.datas[i].doit;

				xCategories.push(result.datas[i].mons);

				if(xValue.indexOf(val) === -1){
					xValue[xValue.length] = val;
				}
			}

			xCategories = unique(xCategories);

			var ctg1 = "<tr>";
			ctg_isi = '<th rowspan="2" style="vertical-align: middle;">Reward (IDR)</th>';

			var total_tmp = [{ qty: 0, doit: 0}];


			$.each(xCategories, function(index, value){
				ctg_isi += "<th colspan=2 style='border-right: 2px solid red'>"+value+"</th>";
				ctg1 += "<th>Quantity</th>";
				ctg1 += "<th style='border-right: 2px solid red'>Sub Total Reward (IDR)</th>";
				total_tmp.push({ qty: 0, doit: 0});
			})

			console.log(total_tmp);

			ctg_isi += '<th colspan=2 style="vertical-align: middle;" class="total">TOTAL</th>';
			ctg1 += "<th class='total'>Quantity</th>";
			ctg1 += "<th class='total'>Sub Total Reward (IDR)</th>";

			ctg1 += "</tr>";

			$("#mon").after(ctg1);


			$.each(xValue, function(index, value){
				var num = 0;
				var qty_tot = 0;
				var curr_tot = 0;

				val_isi += "<tr>";
				doit = new Intl.NumberFormat().format(value);
				val_isi += "<th>"+doit+"</th>";
				var tmp = [];

				$.each(xCategories, function(index2, value2){
					var stat = 0;
					$.each(result.datas, function(index3, value3){
						if (value3.doit == value && value3.mons == value2) {
							val_isi += "<td>"+new Intl.NumberFormat().format(value3.tot)+"</td>";
							sub_doit = new Intl.NumberFormat().format(value3.tot * value3.doit);
							val_isi += "<td style='border-right: 2px solid red'>"+sub_doit+"</td>";

							curr_tot += value3.tot * value3.doit;
							qty_tot += value3.tot;
							tmp.push(value3.tot * value3.doit);
							stat = 1;

							total_tmp[index2].qty += value3.tot;
							total_tmp[index2].doit += value3.tot * value3.doit;
						}

					})

					if (stat == 0) {
						val_isi += "<td>0</td>";
						val_isi += "<td style='border-right: 2px solid red'>0</td>";
						curr_tot += 0;
						qty_tot += 0;
						tmp.push(0);
					}

				})

				// console.log(tot);
				val_isi += "<td class='total'>"+new Intl.NumberFormat().format(qty_tot)+"</td>";
				tot_doit = new Intl.NumberFormat().format(curr_tot);
				val_isi += "<td class='total'>"+tot_doit+"</td>";

				total_tmp[xCategories.length].qty += qty_tot;
				total_tmp[xCategories.length].doit += curr_tot;


				// $.each(xValue, function(index, value){
				// 	$.each(result.datas, function(index3, value3){
				// 		if (value3.doit == value) {

				// 		}

				// 	})
				// })

				mainChart.push({name:value, data: tmp});
				val_isi += "</tr>";
			})

			console.log(total_tmp);

			val_isi += "<tr>";
			val_isi += "<th class='total'>TOTAL</th>";

			$.each(total_tmp, function(index, value){
				val_isi += "<th class='total'>"+new Intl.NumberFormat().format(value.qty)+"</th>";
				val_isi += "<th class='total' style='border-right: 2px solid red !important'>"+new Intl.NumberFormat().format(value.doit)+"</th>";
			})

			// val_isi += "<th>"+value.qty+"</th>";
			// val_isi += "<th>"+new Intl.NumberFormat().format(value.doit)+"</th>";

			val_isi += "</tr>";

			$("#mon").append(ctg_isi);
			$("#body").append(val_isi);

			// console.log(mainChart);

			drawChart(mainChart, xCategories);

			// console.log(xCategories);
			// console.log(xValue);
		})
	}


	function drawChart(data, ctg) {
		Highcharts.chart('chart', {

			title: {
				text: 'Kaizen Reward'
			},

			yAxis: {
				title: {
					text: 'Kaizen Reward (IDR)'
				}
			},

			xAxis: {
				categories: ctg
			},

			legend: {
				
			},

			plotOptions: {
				series: {
					label: {
						connectorAllowed: false
					}
				},
			},
			credits : {
				enabled:false
			},
			series: data,

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

	function unique(list) {
		var result = [];
		$.each(list, function(i, e) {
			if ($.inArray(e, result) == -1) result.push(e);
		});
		return result;
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '2000'
		});
	}
</script>
@endsection
