@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	@font-face {
		font-family: JTM;
		src: url("{{ url("fonts/JTM.otf") }}") format("opentype");
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
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	.dataTable > thead > tr > th[class*="sort"]:after{
		content: "" !important;
	}

	.judul {
		font-family: 'JTM';
		color: white;
		font-size: 35pt;
	}
	#kz_top_sum, #kz_top_count {
		font-size: 15pt;
	}

	#kz_top_count > tr {
		color: white;
	}
	#kz_top_sum > tr:first-child, #kz_top_count > tr:first-child {
		color: #363836 !important;
		background-color: #ffbf00 !important;
	}
	#kz_top_sum > tr:nth-child(2), #kz_top_count > tr:nth-child(2) {
		color: #363836 !important;
		background-color: #a9aba9 !important;
	}
	#kz_top_sum > tr:nth-child(3), #kz_top_count > tr:nth-child(3) {
		color: #363836 !important;
		background-color: #cc952f !important;
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-left: 0px; padding-right: 0px; padding-top: 0px">
	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-12">
				<div class="col-xs-2 pull-left">
					<p class="judul"><i style="color: #c290d1">e </i> - Kaizen</p>
				</div>
				<div class="col-xs-2 pull-right">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border-color: #00a65a">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tgl" onchange="drawChart()" placeholder="Select Date" style="border-color: #00a65a">
					</div>
					<br>
				</div>
			</div>
			<div class="col-xs-12">
				<div id="kz_total" style="width: 100%; height: 500px;"></div>
			</div>

			<div class="col-xs-12" style="padding-top: 10px;">
				<div class="box box-solid" style="background-color: rgb(240,240,240);">
					<table style="width: 100%;">
						<tr>
							<td width="1%">
								<div class="description-block border-right" style="color: #000">
									<h5 class="description-header" style="font-size: 48px;">
										<span class="description-percentage" id="tot_budget">-</span>
									</h5>      
									<span class="description-text" style="font-size: 32px;">Total Operator<br><span>作業者人数</span></span>   
								</div>
							</td>
							<td width="1%">
								<div class="description-block border-right text-green" style="color: #7300ab" >
									<h5 class="description-header" style="font-size: 48px; ">
										<span class="description-percentage" id="tot_kumpul">- (-)</span>
									</h5>      
									<span class="description-text" style="font-size: 32px;">Total Mengumpulkan<br><span >提出者</span></span>   
								</div>
							</td>
							<td width="1%">
								<div class="description-block border-right text-red" id="diff_text">
									<h5 class="description-header" style="font-size: 48px;">
										<span class="description-percentage" id="tot_belum">- (-)</span>
									</h5>      
									<span class="description-text" style="font-size: 32px;">Total Belum Mengumpulkan</span>
									<br><span class="description-text" style="font-size: 32px;">未提出者</span>   
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 3%;">ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 3%;">Grade</th>
								<th style="width: 3%;">Section</th>
								<th style="width: 3%;">Group</th>
								<th style="width: 3%;">Total Kaizen</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
						<tfoot id="tableDetailFoot">
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th>Total</th>
								<th id="totalKaizen"></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
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
		$('body').toggleClass("sidebar-collapse");
		$("#navbar-collapse").text('');
		drawChart();

		// setInterval(drawChart, 3000);
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function drawChart() {

		var tanggal = $('#tgl').val();

		var data = {
			tanggal:tanggal
		}

		$.get('{{ url("fetch/kaizen/resume") }}', data, function(result) {
			if (result.status) {

				var kumpul = [];
				var belum = [];
				var total_kz = [];
				var ctg = [];

				var tot_kz = 0;
				var tot_kumpul = 0;
				var tot_belum = 0;

				$.each(result.datas, function(index, value){
					kumpul.push(parseInt(value.total_sudah));
					belum.push(parseInt(value.total_belum));
					total_kz.push(parseInt(value.total_kaizen));
					ctg.push(value.leader+"-"+value.name);

					tot_kz += parseInt(value.total_kaizen);
					tot_kumpul += parseInt(value.total_sudah);
					tot_belum += parseInt(value.total_belum);
				});

				var prctg_kumpul = tot_kumpul / (tot_kumpul + tot_belum) * 100;
				var prctg_belum = tot_belum / (tot_kumpul + tot_belum) * 100;

				$("#tot_budget").html(tot_kumpul + tot_belum);
				$("#tot_kumpul").html(tot_kumpul+" ( "+prctg_kumpul+" %)");
				$("#tot_belum").html(tot_belum+" ( "+prctg_belum+" %)");

				Highcharts.chart('kz_total', {
					chart: {
						type: 'column'
					},

					title: {
						text: 'Grafik Pengumpulan Kaizen Teian '+result.fiscal+'<br> 改善提案提出実績グラフ '+result.fiscal
					},

					xAxis: {
						categories: ctg
					},

					yAxis: {
						allowDecimals: false,
						min: 0,
						title: {
							text: 'Number of Kaizen Teian'
						},
					},

					tooltip: {
						formatter: function () {
							return '<b>' + this.x + '</b><br/>' +
							this.series.name + ': ' + this.y;
						},
					},

					plotOptions: {
						column: {
							stacking: 'normal'
						},
						line: {
							marker: {
								enabled: false,
								radius: 0.1
							},

						},
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										fetchModal(this.category);
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
						},
					},

					credits: {
						enabled: false
					},

					series: [{
						name: 'Belum Mengumpulkan',
						data: belum,
						color: '#db3223'
					}, {
						name: 'Mengumpulkan',
						data: kumpul,
						color: '#00a65a'
					}
					]
				});
			} else {
				alert(result.message);
			}
		})
	}

	function fetchModal(cat){
		$('#modalDetail').modal('show');
		$('#loading').show();
		$('#modalDetailTitle').html("");
		$('#modalDetailTitle').html("<center><span style='font-size: 20px; font-weight: bold;'>Leader : "+cat+"</span></center>");
		$('#tableDetail').hide();
		var tanggal = $('#tgl').val();

		var data = {
			tanggal:tanggal,
			leader:cat
		}
		$.get('{{ url("fetch/kaizen/resume_detail") }}', data, function(result) {
			if(result.status){
				$('#tableDetailBody').html('');

				var index = 1;
				var resultData = "";
				var total = 0;

				$.each(result.details, function(key, value) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.employee_id +'</td>';
					resultData += '<td>'+ value.name +'</td>';
					resultData += '<td>'+ value.grade +'</td>';
					resultData += '<td>'+ value.section +'</td>';
					resultData += '<td>'+ value.group +'</td>';
					if(value.kz == 0){
						resultData += '<td style="background-color: RGB(255,204,255)">'+ value.kz +'</td>';						
					}
					else{
						resultData += '<td style="background-color: RGB(204,255,255)">'+ value.kz +'</td>';
					}
					resultData += '</tr>';
					index += 1;
					total += parseInt(value.kz);
				});
				$('#tableDetailBody').append(resultData);
				$('#loading').hide();
				$('#tableDetail').show();

				$('#totalKaizen').text(total);

			}
			else{
				alert(result.message);
			}
		});

	}

	$('#tgl').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		viewMode: "months", 
		minViewMode: "months"
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

</script>
@endsection
