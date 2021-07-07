@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	input {
		line-height: 22px;
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
		padding: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	.gambar {
	    width: 100%;
	    background-color: none;
	    border-radius: 5px;
	    margin-left: 0px;
	    margin-top: 10px;
	    display: inline-block;
	    border: 2px solid white;
	  }

	.sedang {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: sedang 1s infinite;  /* Safari 4+ */
		-moz-animation: sedang 1s infinite;  /* Fx 5+ */
		-o-animation: sedang 1s infinite;  /* Opera 12+ */
		animation: sedang 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes sedang {
		0%, 49% {
			background: #ff8080;
		}
		50%, 100% {
			background-color: #ffe8e8;
		}
	}
	#loading, #error { display: none; }
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 50%;">
			<span style="font-size: 60px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<form method="GET" action="{{ action('InjectionsController@indexInjectionNgRate') }}">
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Select Date">
						</div>
					</div>

					<div class="col-xs-2">
						<button class="btn btn-success" type="submit"><i class="fa fa-search"></i> Search</button>
					</div>
					<!-- <div class="pull-right" id="loc" style="margin: 0px;padding-top: 0px;padding-right: 20px;font-size: 2vw;"></div> -->
				</form>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row">
				<!-- <div class="col-xs-2" style="padding-right: 0;">
					<div class="small-box" style="background: #52c9ed; height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>CHECK <span class="text-purple">検査数</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="total">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-search"></i>
						</div>
					</div>
					<div class="small-box" style="background: #00a65a; height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>OK <span class="text-purple">良品数</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="ok">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-check"></i>
						</div>
					</div>
					<div class="small-box" style="background: #ff851b; height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>NG <span class="text-purple">不良品数</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="ng">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-remove"></i>
						</div>
					</div>
					<div class="small-box" style="background: rgb(220,220,220); height: 150px; margin-bottom: 5px;">
						<div class="inner" style="padding-bottom: 0px;">
							<h3 style="margin-bottom: 0px;font-size: 2vw;"><b>% <span class="text-purple">不良率</span></b></h3>
							<h5 style="font-size: 4vw; font-weight: bold;" id="pctg">0</h5>
						</div>
						<div class="icon" style="padding-top: 40px;">
							<i class="fa fa-line-chart"></i>
						</div>
					</div>
				</div> -->
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-8">
							<div id="container1" class="container1" style="width: 100%;"></div>
						</div>
						<div class="col-xs-2" style="padding-left: 0px">
							<div class="gambar">
								<table style="text-align:center;width:100%">
									<tr>
										<td style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 15px;font-weight: bold;">BEST QUALITY EMPLOYEE<br>OF THE DAY</td>
									</tr>
									<tr>
										<td id="lowest_avatar_daily" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 15px;font-weight: bold;"></td>
									</tr>
									<tr>
										<td id="lowest_name_daily" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 15px;font-weight: bold;"></td>
									</tr>
									<tr>
										<td id="lowest_ng_daily" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 15px;font-weight: bold;"></td>
									</tr>
								</table>
							</div>
						</div>
						<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px">
							<div class="gambar">
								<table style="text-align:center;width:100%">
									<tr>
										<td style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 15px;font-weight: bold;">BEST QUALITY EMPLOYEE<br>OF THE WEEK</td>
									</tr>
									<tr>
										<td id="lowest_avatar" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 15px;font-weight: bold;"></td>
									</tr>
									<tr>
										<td id="lowest_name" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 15px;font-weight: bold;"></td>
									</tr>
									<tr>
										<td id="lowest_ng" style="border: 1px solid #fff !important;background-color: #80ff91;color: black;font-size: 15px;font-weight: bold;"></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
				<hr style="border:3px solid white">
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-8">
							<div id="container2" class="container2" style="width: 100%;"></div>
						</div>
						<div class="col-xs-2" style="padding-left: 0px">
							<div class="gambar">
								<table style="text-align:center;width:100%">
									<tr>
										<td style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 15px;font-weight: bold;cursor: pointer;" id="highest_title_daily">BAD QUALITY EMPLOYEE<br>OF THE DAY</td>
									</tr>
									<tr id="not_counceled">
										<td id="not_counceled_td_daily" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 15px;font-weight: bold;cursor: pointer;" ></td>
									</tr>
									<tr>
										<td id="highest_avatar_daily" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 15px;font-weight: bold;cursor: pointer;"></td>
									</tr>
									<tr>
										<td id="highest_name_daily" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 15px;font-weight: bold;cursor: pointer;" ></td>
									</tr>
									<tr>
										<td id="highest_ng_daily" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 15px;font-weight: bold;cursor: pointer;" ></td>
									</tr>
								</table>
							</div>
						</div>
						<div class="col-xs-2" style="padding-left: 0px;padding-right: 5px">
							<div class="gambar">
								<table style="text-align:center;width:100%">
									<tr>
										<td style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 15px;font-weight: bold;cursor: pointer;" onclick="councelingModal()" class="sedang" id="highest_title">BAD QUALITY EMPLOYEE<br>OF THE WEEK</td>
									</tr>
									<tr id="not_counceled">
										<td onclick="councelingModal()" id="not_counceled_td" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 15px;font-weight: bold;cursor: pointer;" class="sedang">BELUM TRAINING & KONSELING</td>
									</tr>
									<tr>
										<td id="highest_avatar" onclick="councelingModal()" style="border: 1px solid #fff !important;background-color:white;color: black;font-size: 15px;font-weight: bold;cursor: pointer;"></td>
									</tr>
									<tr>
										<td id="highest_name" onclick="councelingModal()" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 15px;font-weight: bold;cursor: pointer;" class="sedang"></td>
									</tr>
									<tr>
										<td id="highest_ng" onclick="councelingModal()" style="border: 1px solid #fff !important;background-color: #ff8080;color: black;font-size: 15px;font-weight: bold;cursor: pointer;" class="sedang"></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div style="background-color: #fcba03;text-align: center;">
					<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitle"></h4>
				</div>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px;margin-top: 10px">
					<!-- <center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
					</center> -->
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 3%;">Product</th>
								<th style="width: 4%;">Material</th>
								<th style="width: 2%;">Cav</th>
								<th style="width: 4%;">Employee</th>
								<th style="width: 2%;">NG Name</th>
								<th style="width: 2%;">Qty</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
						<tfoot>
				            <tr style="background-color:rgba(126,86,134,.7);font-size:15px;font-weight:bold">
								<th colspan="6" style="border-top:1px solid black;border-bottom:1px solid black">TOTAL</th>
								<th style="border-top:1px solid black;border-bottom:1px solid black" id="total_ng"></th>
							</tr>
				        </tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalCounceling">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div style="background-color: #03adfc;text-align: center;">
					<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalDetailTitleCounceling"></h4>
				</div>
				<div class="modal-body table-responsive no-padding" style="min-height: 120px;margin-top: 10px">
					<table class="table table-hover table-bordered table-striped">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">ID</th>
								<th style="width: 2%;">Name</th>
								<th style="width: 2%;">NG Qty</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td id="employee_id"></td>
								<td id="name"></td>
								<td id="ng_qty"></td>
							</tr>
						</tbody>
					</table>

					<div class="form-group">
					  <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px">
		              	<label for="">Trainee Employee</label>
		              </div>
					  <div class="col-xs-10" style="padding-left: 0px">
					  	<input type="text" name="tag_employee" id="tag_employee" class="form-control" placeholder="Scan ID Card Employee">
					  </div>
					  <div class="col-xs-2" style="padding-right: 0px">
					  	<button class="btn btn-danger" onclick="cancelScan('tag_employee')">Cancel</button>
					  </div>
					  <input type="hidden" name="firstDate" id="firstDate" class="form-control" placeholder="">
					  <input type="hidden" name="lastDate" id="lastDate" class="form-control" placeholder="">
		            </div>

		            <div class="form-group">
		              <div class="col-xs-12" style="padding-left: 0px">
		              	<label for="">Trained By</label>
		              </div>
					  <div class="col-xs-10" style="padding-left: 0px">
					  	<input type="text" name="tag_leader" id="tag_leader" class="form-control" placeholder="Scan ID Card Sub Leader / Leader">
					  </div>
					  <div class="col-xs-2" style="padding-right: 0px">
					  	<button class="btn btn-danger" onclick="cancelScan('tag_leader')">Cancel</button>
					  </div>
		            </div>

		            <div class="form-group">
		              <div class="col-xs-12" style="padding-left: 0px;">
		              	<label for="">Document Training</label>
		              </div>
					  <div class="col-xs-10" style="padding-left: 0px;">
					  	<!-- <input type="file" name="counceled_image" id="counceled_image" class="form-control" placeholder="Scan ID Card Sub Leader / Leader"> -->
					  	<a href="{{url('input/injection/training_document')}}" target="_blank" class="btn btn-primary">Input Document Training</a>
					  </div>
		            </div>
				</div>
				<div class="modal-footer table-responsive no-padding" style="margin-top: 10px">
					<button class="btn btn-success" onclick="submitCouncel()">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
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
		$('#tanggal').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('.select2').select2();
		fetchChart();
		$('#modalDetail').on('hidden.bs.modal', function () {
			$('#tableDetail').DataTable().clear();
		});
		// setInterval(fetchChart, 20000);
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

	var detail_all_injeksi = [];
	var detail_all_assy = [];

	function fetchChart(){

		var tanggal = "{{$_GET['tanggal']}}";

		var data = {
			tanggal:tanggal,
		}

		$.get('{{ url("fetch/injection/ng_rate") }}', data, function(result, status, xhr) {
			if(result.status){

				//HIGHEST LOWEST TODAY
				var operator = [];
				var ngname = [];
				var ngcount = [];
				var ngnamekensa = [];
				var ngcountkensa = [];
				var ngall = [];
				for (var i = 0; i < result.emp.length; i++) {
					operator.push(result.emp[i].name);
					var countsngkensa = 0;
					for (var j = 0; j < result.resumes.length; j++) {
						if (result.resumes[j].ng_name_kensa != null) {
							if (result.resumes[j].operator_injection == result.emp[i].employee_id) {
								var countskensa = result.resumes[j].ng_count_kensa.split(',');
								var nameskensa = result.resumes[j].ng_name_kensa.split(',');
								for (var k = 0; k < countskensa.length; k++) {
									if (nameskensa[k] == 'NG Gate Cut' || nameskensa[k] == 'NG Top Side' || nameskensa[k] == 'NG Hot Stamp') {

									}else{
										countsngkensa = countsngkensa + parseInt(countskensa[k]);
									}
								}
							}
						}
					}
					ngcountkensa.push({y:parseInt(countsngkensa),key:result.emp[i].employee_id+'_'+result.emp[i].name});
				}
				ngcountkensa.sort(dynamicSort('y'));
				var highest_emp = "";
				var highest_name = "";
				var highest_ng = 0;
				for (var i = 0; i < ngcountkensa.length; i++) {
					var high = ngcountkensa[i].key.split('_');
					highest_emp = high[0];
					highest_name = high[1];
					highest_ng = ngcountkensa[i].y;
				}

				var low = ngcountkensa[0].key.split('_');
				var lowest_emp = low[0];
				var lowest_name = low[1];
				var lowest_ng = ngcountkensa[0].y;

				// $('#highest_name').html(highest_emp+' - '+highest_name.split(' ').slice(0,1).join(' '));
				// $('#highest_ng').html('Jumlah NG = '+highest_ng);

				// $('#lowest_name').html(lowest_emp+' - '+lowest_name.split(' ').slice(0,1).join(' '));
				// $('#lowest_ng').html('Jumlah NG = '+lowest_ng);

				var url_lowest = '{{ url("images/avatar/") }}/'+lowest_emp+'.jpg';
				var url_highest = '{{ url("images/avatar/") }}/'+highest_emp+'.jpg';

				//NG KENSA
				var operator = [];
				var ngname = [];
				var ngcount = [];
				var ngnamekensa = [];
				var ngcountkensa = [];
				var ngall = [];

				detail_all_injeksi = [];
				detail_all_assy = [];

				for (var i = 0; i < result.emp.length; i++) {
					operator.push(result.emp[i].name);
					// var countsng = 0;
					var countsngkensa = 0;
					for (var j = 0; j < result.resumes.length; j++) {
						if (result.resumes[j].ng_name_kensa != null) {
							if (result.resumes[j].operator_injection == result.emp[i].employee_id) {
								// var counts = result.resumes[j].ng_count.split(',');
								var countskensa = result.resumes[j].ng_count_kensa.split(',');
								var namekensa = result.resumes[j].ng_name_kensa.split(',');
								for (var k = 0; k < countskensa.length; k++) {
									// countsng = countsng + parseInt(counts[k]);
									if (namekensa[k] == 'NG Gate Cut' || namekensa[k] == 'NG Top Side' || namekensa[k] == 'NG Hot Stamp') {

									}else{
										countsngkensa = countsngkensa + parseInt(countskensa[k]);
										detail_all_assy.push({
											operator_kensa: result.resumes[j].emp_kensa,
											name_kensa: result.resumes[j].name_kensa,
											operator_injection: result.resumes[j].operator_injection,
											name_injection: result.resumes[j].name,
											product: result.resumes[j].product,
											material_number: result.resumes[j].material_number,
											part_code: result.resumes[j].part_code,
											part_name: result.resumes[j].part_name,
											cavity: result.resumes[j].cavity,
											ng_name: namekensa[k],
											ng_count: countskensa[k],
										});
									}
								}
							}
						}
					}
					// ngcount.push(countsng);
					ngcountkensa.push(countsngkensa);
				}

				// var ngcounts = [];

				// for (var i = 0; i < ngnames.length; i++) {
				// 	ngcounts[i] = 0;
				// 	for (var j = 0; j < ngall.length; j++) {
				// 		var ngalls = ngall[j].split('_');
				// 		if (ngalls[0] == ngnames[i]) {
				// 			ngcounts[i] = ngcounts[i]+parseInt(ngalls[1]);
				// 		}
				// 	}
				// }
				// var datas = [];
				// for (var i = 0; i < ngnames.length; i++) {
				// 	datas.push([ngnames[i], ngcounts[i]]);
				// }

				Highcharts.chart('container1', {
					chart: {
						type: 'column',
						height: '330',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: "TOTAL NG FROM ASSY",
						style: {
							fontSize: '20px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'on '+result.dateTitle,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: operator,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '13px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Qty NG Pc(s)',
							style: {
								color: '#eee',
								fontSize: '15px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"14px"
							}
						},
						type: 'linear',
						max: 400
					}
					],
					tooltip: {
						headerFormat: '<span>Total NG Assy</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{this.category} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						align: 'right',
						verticalAlign: 'top',
						x: -90,
						y: 20,
						floating: true,
						borderWidth: 1,
						backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
						shadow: true,
						itemStyle: {
							fontSize:'13px',
						},
					},	
					credits: {
						enabled: false
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModal(this.category,'assy');
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
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer'
						},
					},
					series: [
					{
						zoneAxis: 'x',
						type: 'column',
						data: ngcountkensa,
						name: "Total NG",
						colorByPoint: false,
						color: "#f0ad4e",
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					}
					]
				});

				var ngname = [];
				var ngcount = [];
				var ngnamekensa = [];
				var ngcountkensa = [];
				for (var i = 0; i < result.emp.length; i++) {
					var countsngkensa = 0;
					var ngall = [];
					var ngall_detail = [];
					for (var j = 0; j < result.resumes.length; j++) {
						if (result.resumes[j].ng_name_kensa != null) {
							if (result.resumes[j].operator_injection == result.emp[i].employee_id) {
								ngall.push(result.resumes[j].operator_injection+'_'+result.resumes[j].name+'_'+result.resumes[j].product+'_'+result.resumes[j].material_number+'_'+result.resumes[j].part_code+'_'+result.resumes[j].part_name+'_'+result.resumes[j].cavity+'_'+result.resumes[j].ng_name+'_'+result.resumes[j].ng_count);
								ngall_detail.push(result.resumes[j].operator_injection+'_'+result.resumes[j].ng_name+'_'+result.resumes[j].ng_count);
							}
						}
					}
					var ng_counts = ngall_detail.filter(onlyUnique);
					var ngalldetailtable = ngall.filter(onlyUnique);
					// for (var l = 0; l < ng_counts.length; l++) {
					// 	var ngcountsss = ngall_detail[l].split('_');
					// 	var ngcountssss = ngcountsss[1].split(',');
					// 	var ng_names = ngcountsss[0].split(',');

					// 	for (var k = 0; k < ngcountssss.length; k++) {
					// 		countsngkensa = countsngkensa + parseInt(ngcountssss[k]);
					// 	}
					// }
					

					for (var z = 0; z < ngalldetailtable.length; z++) {
						var ngalldetailtablesplit = ngalldetailtable[z].split('_');
						var ng_name_split = ngalldetailtablesplit[7].split(',');
						var ng_count_split = ngalldetailtablesplit[8].split(',');
						for(var y = 0; y < ng_name_split.length; y++){
							detail_all_injeksi.push({
								operator_injection: ngalldetailtablesplit[0],
								name_injection: ngalldetailtablesplit[1],
								product: ngalldetailtablesplit[2],
								material_number: ngalldetailtablesplit[3],
								part_code: ngalldetailtablesplit[4],
								part_name: ngalldetailtablesplit[5],
								cavity: ngalldetailtablesplit[6],
								ng_name: ng_name_split[y],
								ng_count: ng_count_split[y],
							});
							countsngkensa = countsngkensa + parseInt(ng_count_split[y]);
						}
					}

					ngcountkensa.push(countsngkensa);
				}

				Highcharts.chart('container2', {
					chart: {
						type: 'column',
						height: '330',
						backgroundColor: "rgba(0,0,0,0)"
					},
					title: {
						text: "TOTAL NG FROM INJECTION",
						style: {
							fontSize: '20px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'on '+result.dateTitle,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: operator,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '13px',
								fontWeight: 'bold'
							}
						},
					},
					yAxis: [{
						title: {
							text: 'Qty NG Pc(s)',
							style: {
								color: '#eee',
								fontSize: '15px',
								fontWeight: 'bold',
								fill: '#6d869f'
							}
						},
						labels:{
							style:{
								fontSize:"14px"
							}
						},
						type: 'linear',
						max: 400
					}
					],
					tooltip: {
						headerFormat: '<span>Total NG Injection</span><br/>',
						pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b><br/>',
					},
					legend: {
						layout: 'horizontal',
						align: 'right',
						verticalAlign: 'top',
						x: -90,
						y: 20,
						floating: true,
						borderWidth: 1,
						backgroundColor:
						Highcharts.defaultOptions.legend.backgroundColor || '#2a2a2b',
						shadow: true,
						itemStyle: {
							fontSize:'13px',
						},
					},	
					credits: {
						enabled: false
					},
					plotOptions: {
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										ShowModal(this.category,'injeksi');
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
							animation: {
								enabled: true,
								duration: 800
							},
							pointPadding: 0.93,
							groupPadding: 0.93,
							borderWidth: 0.93,
							cursor: 'pointer'
						},
					},
					series: [
					{
						zoneAxis: 'x',
						type: 'column',
						data: ngcountkensa,
						name: "Total NG",
						colorByPoint: false,
						color: "#4287f5",
						animation: false,
						dataLabels: {
							enabled: true,
							format: '{point.y}' ,
							style:{
								fontSize: '1vw',
								textShadow: false
							},
						},
					}
					]
				}
				// , function (chartt) { // on complete

				//     chartt.renderer.image(url_highest,80,0,70,90)
				//         .add();
				//     chartt.renderer.text('TODAY BAD',77,90,85,90).css({
				//         fontSize: '12px',
				//         color: '#fff',
				//         fontWeight:'bold',
				//         textShadow: '1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue'
				//       }).add();

				//     chartt.renderer.image(url_lowest,160,0,70,90)
				//         .add();
				//     chartt.renderer.text('TODAY BEST',160,90,85,90).css({
				//         fontSize: '12px',
				//         color: '#fff',
				//         fontWeight:'bold',
				//         textShadow: '1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue'
				//       }).add();
						
				// }
				);
				var emp = [];
				for (var j = 0; j < result.resumeweek.length; j++) {
					// if (result.resumeweek[j].ng_name_kensa != null) {
					// 	var countskensa = result.resumeweek[j].ng_count_kensa.split(',');
					// 	for (var k = 0; k < countskensa.length; k++) {
					// 		countsngkensa = countsngkensa + parseInt(countskensa[k]);
					// 	}
					// }
					// counceling = result.resumeweek[j].counceled_employee;
					emp.push(result.resumeweek[j].operator_injection+'_'+result.resumeweek[j].name);
				}

				var emps = emp.filter(onlyUnique);

				var operator = [];
				var ngname = [];
				var ngcount = [];
				var ngnamekensa = [];
				var ngcountkensa = [];
				var ngall = [];
				var counceling = "";

				for (var i = 0; i < emps.length; i++) {
					operator.push(emps[i].name);
					var countsngkensa = 0;
					for (var j = 0; j < result.resumeweek.length; j++) {
						if (result.resumeweek[j].ng_name_kensa != null) {
							if (result.resumeweek[j].operator_injection == emps[i].split('_')[0]) {
								var nameskensa = result.resumeweek[j].ng_name_kensa.split(',');
								var countskensa = result.resumeweek[j].ng_count_kensa.split(',');
								for (var k = 0; k < countskensa.length; k++) {
									if (nameskensa[k] == 'NG Gate Cut' || nameskensa[k] == 'NG Top Side' || nameskensa[k] == 'NG Hot Stamp') {

									}else{
										countsngkensa = countsngkensa + parseInt(countskensa[k]);
									}
								}
							}
						}
						counceling = result.resumeweek[j].counceled_employee;
					}
					ngcountkensa.push({y:parseInt(countsngkensa),key:emps[i].split('_')[0]+'_'+emps[i].split('_')[1]});
				}
				ngcountkensa.sort(dynamicSort('y'));

				// console.log(ngcountkensa);
				var highest_emp = "";
				var highest_name = "";
				var highest_ng = 0;
				for (var i = 0; i < ngcountkensa.length; i++) {
					var high = ngcountkensa[i].key.split('_');
					highest_emp = high[0];
					highest_name = high[1];
					highest_ng = ngcountkensa[i].y;
				}

				var low = ngcountkensa[0].key.split('_');
				var lowest_emp = low[0];
				var lowest_name = low[1];
				var lowest_ng = ngcountkensa[0].y;

				$('#highest_name').html(highest_emp+' - '+highest_name.split(' ').slice(0,2).join(' '));
				$('#highest_ng').html('Jumlah NG = '+highest_ng);

				$('#lowest_name').html(lowest_emp+' - '+lowest_name.split(' ').slice(0,2).join(' '));
				$('#lowest_ng').html('Jumlah NG = '+lowest_ng);

				var url_lowest = '{{ url("images/avatar/") }}/'+lowest_emp+'.jpg';
				var thumbs_up = '{{ url("data_file/injection/ok.png") }}';
				var url_highest = '{{ url("images/avatar/") }}/'+highest_emp+'.jpg';
				var thumbs_down = '{{ url("data_file/injection/not_ok.png") }}';
				var ganbatte = '{{ url("data_file/injection/ganbatte.png") }}';

				$('#lowest_avatar').html('<img style="width:80px" src="'+url_lowest+'" class="user-image" alt="User image"> <img style="width:80px" src="'+thumbs_up+'" class="user-image" alt="User image">');

				$('#firstDate').val(result.firstdayweek);
				$('#lastDate').val(result.lastdayweek);

				if (counceling == null) {
					$('#not_counceled_td').html('BELUM TRAINING & KONSELING');
					document.getElementById('not_counceled_td').style.backgroundColor = '#ff8080';
					$('#highest_avatar').html('<img style="width:80px" src="'+url_highest+'" class="user-image" alt="User image"> <img style="width:80px" src="'+thumbs_down+'" class="user-image" alt="User image">');
					$('#highest_title').prop('class','sedang');
					$('#not_counceled_td').prop('class','sedang');
					$('#highest_name').prop('class','sedang');
					$('#highest_ng').prop('class','sedang');
				}else{
					// $('#not_counceled').show();
					document.getElementById('not_counceled_td').style.backgroundColor = '#82ff80';
					$('#highest_avatar').html('<img style="width:80px" src="'+url_highest+'" class="user-image" alt="User image"> <img style="width:80px" src="'+ganbatte+'" class="user-image" alt="User image">');
					$('#not_counceled_td').html('SUDAH TRAINING &KONSELING');
					$('#highest_title').removeAttr('class');
					$('#not_counceled_td').removeAttr('class');
					$('#highest_name').removeAttr('class');
					$('#highest_ng').removeAttr('class');
				}


				//RESUME YESTERDAY
				var emp = [];
				var name = [];
				for (var j = 0; j < result.resumeyesterday.length; j++) {
					// if (result.resumeweek[j].ng_name_kensa != null) {
					// 	var countskensa = result.resumeweek[j].ng_count_kensa.split(',');
					// 	for (var k = 0; k < countskensa.length; k++) {
					// 		countsngkensa = countsngkensa + parseInt(countskensa[k]);
					// 	}
					// }
					// counceling = result.resumeweek[j].counceled_employee;
					emp.push(result.resumeyesterday[j].operator_injection+'_'+result.resumeyesterday[j].name);
					if (result.resumeyesterday[j].ng_name_kensa != null) {
						var namekensa = result.resumeyesterday[j].ng_name_kensa.split(',');
						for (var k = 0; k < result.resumeyesterday.length; k++) {
							name.push(result.resumeyesterday[j].ng_name_kensa);
						}
					}
				}
				var emps = emp.filter(onlyUnique);
				var names = name.filter(onlyUnique);

				var operator = [];
				var ngname = [];
				var ngcount = [];
				var ngnamekensa = [];
				var ngcountkensa = [];
				var ngall = [];
				// for (var i = 0; i < emps.length; i++) {
				// 	operator.push(emps[i].split('_')[1]);
				// 	var countsngkensa = 0;
				// 	for (var j = 0; j < result.resumeyesterday.length; j++) {
				// 		if (result.resumeyesterday[j].ng_name_kensa != null) {
				// 			if (result.resumeyesterday[j].operator_injection == emps[i].split('_')[0]) {
				// 				var namekensa = result.resumeyesterday[j].ng_name_kensa.split(',');
				// 				for (var k = 0; k < namekensa.length; k++) {
				// 					ngnamekensa.push(namekensa[k]);
				// 				}
				// 			}
				// 		}
				// 	}
				// }
				var ngnames = ngname.filter(onlyUnique);
				for (var i = 0; i < emps.length; i++) {
					operator.push(emps[i].split('_')[1]);
					var countsngkensa = 0;
					for (var j = 0; j < result.resumeyesterday.length; j++) {
						if (result.resumeyesterday[j].ng_name_kensa != null) {
							var ng_names = [];
							if (result.resumeyesterday[j].operator_injection == emps[i].split('_')[0]) {
								var countskensa = result.resumeyesterday[j].ng_count_kensa.split(',');
								var nameskensa = result.resumeyesterday[j].ng_name_kensa.split(',');
								for (var k = 0; k < countskensa.length; k++) {
									if (nameskensa[k] == 'NG Gate Cut' || nameskensa[k] == 'NG Top Side' || nameskensa[k] == 'NG Hot Stamp') {

									}else{
										countsngkensa = countsngkensa + parseInt(countskensa[k]);
										ngnamekensa.push(result.resumeyesterday[j].part_name+'_'+result.resumeyesterday[j].operator_injection+'_'+nameskensa[k]+'_'+countskensa[k]);
									}
								}
							}
						}
					}
					ngcountkensa.push({y:parseInt(countsngkensa),key:emps[i].split('_')[0]+'_'+emps[i].split('_')[1]});
				}
				ngcountkensa.sort(dynamicSort('y'));

				var highest_emp = "";
				var highest_name = "";
				var highest_ng = 0;
				for (var i = 0; i < ngcountkensa.length; i++) {
					var high = ngcountkensa[i].key.split('_');
					highest_emp = high[0];
					highest_name = high[1];
					highest_ng = ngcountkensa[i].y;
				}

				var resume_ng_name = [];

				for (var i = 0; i <ngnamekensa.length;i++ ) {
					if (ngnamekensa[i].split('_')[1] == highest_emp) {
						resume_ng_name.push({y:parseInt(ngnamekensa[i].split('_')[3]),key:ngnamekensa[i].split('_')[2],product:ngnamekensa[i].split('_')[0]});
						// for (var j = 0; j <names.length;j++ ) {
						// 	if (names[j] == ngnamekensa[i].split('_')[1]) {
						// 	}
						// }
					}
				}

				var newObjectsMerged = resume_ng_name.reduce((r, { key, y,product }) => {
			        var temp = r.find(o => o.key === key);
			        if (temp) {
			            temp.y += y;
			        } else {
			            r.push({ key, y,product });
			        }
			        return r;
			    }, []);

				newObjectsMerged.sort(dynamicSort('y'));

				var ng_name_highest = "";
				var product_highest = "";
				var ng_count_highest = 0;
				for (var i = 0; i < newObjectsMerged.length; i++) {
					ng_name_highest = newObjectsMerged[i].key;
					ng_count_highest = newObjectsMerged[i].y;
					product_highest = newObjectsMerged[i].product.split(' ').slice(0,2).join(' ');
				}

				var low = ngcountkensa[0].key.split('_');
				var lowest_emp = low[0];
				var lowest_name = low[1];
				var lowest_ng = ngcountkensa[0].y;

				$('#highest_name_daily').html(highest_emp+' - '+highest_name.split(' ').slice(0,2).join(' '));
				$('#highest_ng_daily').html('Jumlah NG = '+highest_ng);

				$('#lowest_name_daily').html(lowest_emp+' - '+lowest_name.split(' ').slice(0,2).join(' '));
				$('#lowest_ng_daily').html('Jumlah NG = '+lowest_ng);

				var url_lowest = '{{ url("images/avatar/") }}/'+lowest_emp+'.jpg';
				var thumbs_up = '{{ url("data_file/injection/ok.png") }}';
				var url_highest = '{{ url("images/avatar/") }}/'+highest_emp+'.jpg';
				var thumbs_down = '{{ url("data_file/injection/not_ok.png") }}';
				var ganbatte = '{{ url("data_file/injection/ganbatte.png") }}';

				$('#lowest_avatar_daily').html('<img style="width:80px" src="'+url_lowest+'" class="user-image" alt="User image"> <img style="width:80px" src="'+thumbs_up+'" class="user-image" alt="User image">');

				// $('#not_counceled_td_daily').html('BELUM TRAINING & KONSELING');
				// document.getElementById('not_counceled_td').style.backgroundColor = '#ff8080';
				$('#highest_avatar_daily').html('<img style="width:80px" src="'+url_highest+'" class="user-image" alt="User image"> <img style="width:80px" src="'+ganbatte+'" class="user-image" alt="User image">');
				$('#not_counceled_td_daily').html('NG Terbanyak<br>'+product_highest+' '+ng_name_highest+' = '+ng_count_highest);
				// $('#highest_title_daily').prop('class','sedang');
				// $('#not_counceled_td_daily').prop('class','sedang');
				// $('#highest_name_daily').prop('class','sedang');
				// $('#highest_ng_daily').prop('class','sedang');
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
}

function ShowModal(operator_injection,type) {
	$('#tableDetail').DataTable().clear();
	$('#tableDetail').DataTable().destroy();
	$('#tableDetailBody').html('');
	var bodyDetail = '';
	var total_ng = 0;
	if (type === 'injeksi') {
		var index = 1;
		$('#modalDetailTitle').html('Detail NG From Injection');
		for (var i = 0; i < detail_all_injeksi.length; i++) {
			if (detail_all_injeksi[i].name_injection === operator_injection) {
				bodyDetail += '<tr>';
				bodyDetail += '<td>'+index+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].product+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].material_number+'<br>'+detail_all_injeksi[i].part_name+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].cavity+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].operator_injection+'<br>'+detail_all_injeksi[i].name_injection+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].ng_name+'</td>';
				bodyDetail += '<td>'+detail_all_injeksi[i].ng_count+'</td>';
				total_ng = total_ng + parseInt(detail_all_injeksi[i].ng_count);
				bodyDetail += '</tr>';
				index++;
			}
		}
	}
	if (type === 'assy') {
		var index = 1;
		$('#modalDetailTitle').html('Detail NG From Assy');
		for (var i = 0; i < detail_all_assy.length; i++) {
			if (detail_all_assy[i].name_injection === operator_injection) {
				bodyDetail += '<tr>';
				bodyDetail += '<td>'+index+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].product+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].material_number+'<br>'+detail_all_assy[i].part_name+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].cavity+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].operator_injection+'<br>'+detail_all_assy[i].name_injection+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].ng_name+'</td>';
				bodyDetail += '<td>'+detail_all_assy[i].ng_count+'</td>';
				total_ng = total_ng + parseInt(detail_all_assy[i].ng_count);
				bodyDetail += '</tr>';
				index++;
			}
		}
	}
	
	$('#tableDetailBody').append(bodyDetail);

	$('#total_ng').html(total_ng);

	var table = $('#tableDetail').DataTable({
		'dom': 'Bfrtip',
		'responsive':true,
		'lengthMenu': [
		[ 10, 25, 50, -1 ],
		[ '10 rows', '25 rows', '50 rows', 'Show all' ]
		],
		'buttons': {
			buttons:[
			{
				extend: 'pageLength',
				className: 'btn btn-default',
			},
			]
		},
		'paging': true,
		'lengthChange': true,
		'searching': true,
		'ordering': true,
		'info': true,
		'autoWidth': true,
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
		"processing": true,
		// "footerCallback": function ( row, data, start, end, display ) {
  //           var api = this.api(), data;
 
  //           var intVal = function ( i ) {
  //               return typeof i === 'string' ?
  //                   i.replace(/[\$,]/g, '')*1 :
  //                   typeof i === 'number' ?
  //                       i : 0;
  //           };

  //           pageTotal = api
  //               .column( 7, { page: 'current'} )
  //               .data()
  //               .reduce( function (a, b) {
  //                   return intVal(a) + intVal(b);
  //               }, 0 );
 
  //           $( api.column( 7 ).footer() ).html(
  //               pageTotal
  //           );
  //       }
	});

	$('#modalDetail').modal('show');
}

function councelingModal() {
	if ($('#not_counceled_td').text() == 'BELUM TRAINING & KONSELING') {
		$('#modalCounceling').modal('show');
		$('#employee_id').html($('#highest_name').text().split(' - ')[0]);
		$('#name').html($('#highest_name').text().split(' - ')[1]);
		$('#ng_qty').html($('#highest_ng').text().split(' = ')[1]);

		$('#modalDetailTitleCounceling').html('TRAINING DAN KONSELING');

		$('#tag_employee').val('');
		$('#tag_leader').val('');
		// document.getElementById("counceled_image").value = "";
		$('#tag_employee').removeAttr('disabled');
		$('#tag_leader').removeAttr('disabled');
		$('#tag_employee').focus();
	}
}

function submitCouncel() {
	$('#loading').show();
	if ($('#tag_employee').val() == "" || $('#tag_leader').val() == "") {
		$('#loading').hide();
		openErrorGritter('Error!','Semua Data Harus Diisi');
		return false;
	}
	var counceled_employee = $("#tag_employee").val();
	var counceled_by = $("#tag_leader").val();
	var first_date = $("#firstDate").val();
	var last_date = $("#lastDate").val();
	// var fileData  = $('#counceled_image').prop('files')[0];

	// var file=$('#counceled_image').val().replace(/C:\\fakepath\\/i, '').split(".");

	var formData = new FormData();
	// formData.append('fileData', fileData);
	formData.append('counceled_employee', counceled_employee);
	formData.append('counceled_by', counceled_by);
	formData.append('first_date', first_date);
	formData.append('last_date', last_date);
	// formData.append('extension', file[1]);
	// formData.append('foto_name', file[0]);

	$.ajax({		
		url:"{{ url('input/injection/counceling') }}",
		method:"POST",
		data:formData,
		dataType:'JSON',
		contentType: false,
		cache: false,
		processData: false,
		success:function(data)
		{
			$('#loading').hide();
			fetchChart();
			$('#modalCounceling').modal('hide');
			openSuccessGritter('Success','Input Konseling Berhasil');
		},
		error: function (err) {
	        openErrorGritter('Error!',err);
	    }
	})
}

$('#tag_employee').keydown(function(event) {
	if (event.keyCode == 13 || event.keyCode == 9) {
		if($('#tag_employee').val().length > 9 ){
			var data = {
				employee_id : $("#tag_employee").val()
			}

			$.get('{{ url("scan/injection/counceled_employee") }}', data, function(result, status, xhr){
				if(result.status){
					if (result.employee.employee_id != $('#employee_id').text()) {
						audio_error.play();
						openErrorGritter('Error!', 'Operator Tidak Sama');
						$('#tag_employee').val('');
					}else{
						$('#tag_employee').val(result.employee.employee_id+'-'+result.employee.name);
						$('#tag_employee').prop('disabled',true);
						openSuccessGritter('Success!', result.message);
					}
				}
				else{
					audio_error.play();
					openErrorGritter('Error!', result.message);
					$('#tag_employee').val('');
				}
			});
		}else{
			openErrorGritter('Error!', 'Tag Tidak Ditemukan');
			$('#tag_employee').val('');
		}
	}
});

$('#tag_leader').keydown(function(event) {
	if (event.keyCode == 13 || event.keyCode == 9) {
		if($('#tag_leader').val().length > 9 ){
			var data = {
				employee_id : $("#tag_leader").val()
			}

			$.get('{{ url("scan/injection/counceled_by") }}', data, function(result, status, xhr){
				if(result.status){
					$('#tag_leader').val(result.employee.employee_id+'-'+result.employee.name);
					$('#tag_leader').prop('disabled',true);
					openSuccessGritter('Success!', result.message);
				}
				else{
					audio_error.play();
					openErrorGritter('Error', result.message);
					$('#tag_leader').val('');
				}
			});
		}else{
			openErrorGritter('Error', 'Tag Tidak Ditemukan');
			$('#tag_leader').val('');
		}
	}
});

function cancelScan(btn) {
	$('#'+btn).val('');
	$('#'+btn).removeAttr('disabled');
	$('#'+btn).focus();
}

function dynamicSort(property) {
    var sortOrder = 1;
    if(property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1);
    }
    return function (a,b) {
        /* next line works with strings and numbers, 
         * and you may want to customize it to your needs
         */
        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
        return result * sortOrder;
    }
}

	function perbandingan(a,b){
		return a-b;
	}

	function onlyUnique(value, index, self) {
	  return self.indexOf(value) === index;
	}

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

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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


</script>
@endsection