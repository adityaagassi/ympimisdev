@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.morecontent span {
		display: none;
	}
	.morelink {
		display: block;
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
	.std {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: rgb(255,116,116);
		display: inline-block;
	}
	.act {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: rgb(144,238,126);
		display: inline-block;
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
				<form method="GET" action="{{ action('MiddleProcessController@indexBuffingOpEff') }}">
					<div class="col-xs-2">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" name="tanggal" id="tanggal" placeholder="Select Date">
						</div>
					</div>
					<div class="col-xs-2" style="color: black;">
						<div class="form-group">
							<select class="form-control select2" multiple="multiple" id='groupSelect' onchange="changeGroup()" data-placeholder="Select Group" style="width: 100%;">
								<option value="A">GROUP A</option>
								<option value="B">GROUP B</option>
								<option value="C">GROUP C</option>
							</select>
							<input type="text" name="group" id="group" hidden>			
						</div>
					</div>
					<div class="col-xs-2" style="color: black;">
						<div class="form-group">
							<select class="form-control select2" id='targetSelect' onchange="changeTarget()" data-placeholder="Select Target" style="width: 100%;">
								<option value="">Select Target</option>
								<option value="80">80%</option>
								<option value="85">85%</option>
								<option value="90">90%</option>
								<option value="95">95%</option>
								<option value="100">100%</option>
							</select>
							<input type="text" name="target" id="target" hidden>			
						</div>
					</div>
					<div class="col-xs-1">
						<button class="btn btn-success" type="submit">Update Chart</button>
					</div>
				</form>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>
			

			{{-- OP Overall Eff --}}
			<div class="col-xs-12" style="margin-top: 1%; padding: 0px;">
				<div id="shifta">
					<div id="container1_shifta" style="width: 100%;"></div>					
				</div>
				<div id="shiftb">
					<div id="container1_shiftb" style="width: 100%;"></div>					
				</div>
				<div id="shiftc">
					<div id="container1_shiftc" style="width: 100%;"></div>					
				</div>
			</div>

			
			{{-- OP Result --}}
			<div class="col-xs-12" style="margin-top: 1%; padding: 0px; display: none;">
				<div id="shifta2">
					<div id="container2_shifta" style="width: 100%;"></div>
				</div>
				<div id="shiftb2">
					<div id="container2_shiftb" style="width: 100%;"></div>
				</div>
				<div id="shiftc2">
					<div id="container2_shiftc" style="width: 100%;"></div>
				</div>
			</div>			


			{{-- OP eff --}}
			<div class="col-xs-12" style="margin-top: 1%; padding: 0px; display: none;">
				<div id="shifta3">
					<div id="container3_shifta" style="width: 100%;"></div>
				</div>
				<div id="shiftb3">
					<div id="container3_shiftb" style="width: 100%;"></div>
				</div>
				<div id="shiftc3">
					<div id="container3_shiftc" style="width: 100%;"></div>
				</div>
			</div>

			{{-- Last NG --}}
			<div class="col-xs-12" style="margin-top: 1%; padding: 0px;">
				<div id="shifta4">
					<div id="container4_shifta" style="width: 100%;"></div>
				</div>
				<div id="shiftb4">
					<div id="container4_shiftb" style="width: 100%;"></div>
				</div>
				<div id="shiftc4">
					<div id="container4_shiftc" style="width: 100%;"></div>
				</div>
			</div>

			{{-- Table Last NG --}}
			<div style="display: none;">
				<div class="col-xs-12" id="target" style="margin-top: 1%; margin-left: 1%; width:98%; padding: 0px; background-color: #2a2a2b">
					<table id="eff_target" class="table table-bordered" style="width:97%; margin-left: 2.5%; margin-top: 1%; margin-bottom: 1%;"> 
						<thead id="eff_target_head">
						</thead>
						<tbody id="eff_target_body">
						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>

	<!-- start modal -->
	<div class="modal fade" id="myModal" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Operator Overall Efficiency Details</b></h4>
					<h5 class="modal-title" style="text-align: center;" id="judul"></h5>
				</div>
				<div class="modal-body">
					<div class="row">
						{{-- <h5 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Resume</b></h5> --}}

						<div class="col-md-12" style="margin-bottom: 20px;">
							<div class="col-md-4">
								<h5 class="modal-title">NG Rate</h5>
								<h5 class="modal-title" id="ng_rate"></h5>
							</div>
							<div class="col-md-4">
								<h5 class="modal-title">Post Rate</h5>
								<h5 class="modal-title" id="posh_rate"></h5>
							</div>
							<div class="col-md-4">
								<h5 class="modal-title">Operator Overall Efficiency</h5>
								<h5 class="modal-title" id="op_eff"></h5>
							</div>
						</div>

						<div class="col-md-6">
							<table id="middle-log" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="middle-log-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th colspan="5" style="text-align: center;">GOOD</th>
									</tr>
									<tr>
										<th style="width: 20%">Finish Buffing</th>
										<th>Model</th>
										<th>Key</th>
										<th>OP Kensa</th>
										<th>Material Qty</th>
									</tr>
								</thead>
								<tbody id="middle-log-body">
								</tbody>
							</table>
						</div>
						<div class="col-md-6">
							<table id="middle-ng-log" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="middle-ng-log-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th colspan="5" style="text-align: center;">NOT GOOD</th>
									</tr>
									<tr>
										<th style="width: 20%">Finish Buffing</th>
										<th>Model</th>
										<th>Key</th>
										<th>OP Kensa</th>
										<th>Material Qty</th>
									</tr>
								</thead>
								<tbody id="middle-ng-log-body">
								</tbody>
							</table>
						</div>
						<div class="col-md-8">
							<table id="middle-cek" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="middle-cek-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th colspan="5" style="text-align: center;">TOTAL CEK</th>
									</tr>
									<tr>
										<th>Finish Buffing</th>
										<th>Model</th>
										<th>Key</th>
										<th>OP Kensa</th>
										<th>Material Qty</th>
									</tr>
								</thead>
								<tbody id="middle-cek-body">
								</tbody>
							</table>
						</div>
						<div class="col-md-12">
							<table id="data-log" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="data-log-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th colspan="8" style="text-align: center;">BUFFING RESULT</th>
									</tr>
									<tr>
										<th>Model</th>
										<th>Key</th>
										<th style="width: 13%">Akan</th>
										<th style="width: 13%">Sedang</th>
										<th style="width: 13%">Selesai</th>
										<th style="width: 10%">Standart time</th>
										<th style="width: 10%">Actual time</th>
										<th style="width: 10%">Material Qty</th>
									</tr>
								</thead>
								<tbody id="data-log-body">
								</tbody>
							</table>
						</div>

					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- end modal -->

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
		setInterval(fillChart, 20000);

	});

	function changeGroup() {
		$("#group").val($("#groupSelect").val());
	}

	function changeTarget() {
		$("#target").val($("#targetSelect").val());
	}

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

	function showDetail(tgl, nama) {
		var data = {
			tgl:tgl,
			nama:nama,
		}

		$('#myModal').modal('show');
		$('#data-log-body').append().empty();
		$('#middle-log-body').append().empty();
		$('#middle-ng-log-body').append().empty();
		$('#middle-cek-body').append().empty();
		$('#ng_rate').append().empty();
		$('#posh_rate').append().empty();
		$('#op_eff').append().empty();
		$('#judul').append().empty();


		$.get('{{ url("fetch/middle/buffing_op_eff_detail") }}', data, function(result, status, xhr) {
			if(result.status){

				$('#judul').append('<b>'+result.nik+' - '+result.nama+' on '+tgl+'</b>');

				//Middle log
				var total_good = 0;
				var body = '';
				for (var i = 0; i < result.good.length; i++) {
					body += '<tr>';
					body += '<td>'+result.good[i].buffing_time+'</td>';
					body += '<td>'+result.good[i].model+'</td>';
					body += '<td>'+result.good[i].key+'</td>';
					body += '<td>'+result.good[i].op_kensa+'</td>';
					body += '<td>'+result.good[i].quantity+'</td>';
					body += '</tr>';

					total_good += parseInt(result.good[i].quantity);
				}
				body += '<tr>';
				body += '<td  colspan="4" style="text-align: center;">Total</td>';
				body += '<td>'+total_good+'</td>';
				body += '</tr>';
				$('#middle-log-body').append(body);


				//Middle log
				var total_ng = 0;
				var body = '';
				for (var i = 0; i < result.ng.length; i++) {
					body += '<tr>';
					body += '<td>'+result.ng[i].buffing_time+'</td>';
					body += '<td>'+result.ng[i].model+'</td>';
					body += '<td>'+result.ng[i].key+'</td>';
					body += '<td>'+result.ng[i].op_kensa+'</td>';
					body += '<td>'+result.ng[i].quantity+'</td>';
					body += '</tr>';

					total_ng += parseInt(result.ng[i].quantity);
				}
				body += '<tr>';
				body += '<td colspan="4" style="text-align: center;">Total</td>';
				body += '<td>'+total_ng+'</td>';
				body += '</tr>';
				$('#middle-ng-log-body').append(body);


				//Middle cek
				var total_cek = 0;
				var body = '';
				for (var i = 0; i < result.cek.length; i++) {
					body += '<tr>';
					body += '<td>'+result.cek[i].buffing_time+'</td>';
					body += '<td>'+result.cek[i].model+'</td>';
					body += '<td>'+result.cek[i].key+'</td>';
					body += '<td>'+result.cek[i].op_kensa+'</td>';
					body += '<td>'+result.cek[i].quantity+'</td>';
					body += '</tr>';

					total_cek += parseInt(result.cek[i].quantity);
				}
				body += '<tr>';
				body += '<td colspan="4" style="text-align: center;">Total</td>';
				body += '<td>'+total_cek+'</td>';
				body += '</tr>';
				$('#middle-cek-body').append(body);



				//Data log
				var total_perolehan = 0;
				var total_std = 0;
				var total_act = 0;

				var body = '';
				for (var i = 0; i < result.data_log.length; i++) {
					body += '<tr>';
					body += '<td>'+result.data_log[i].model+'</td>';
					body += '<td>'+result.data_log[i].key+'</td>';
					body += '<td>'+result.data_log[i].akan+'</td>';
					body += '<td>'+result.data_log[i].sedang+'</td>';
					body += '<td>'+result.data_log[i].selesai+'</td>';
					body += '<td>'+result.data_log[i].std+'</td>';
					body += '<td>'+result.data_log[i].act+'</td>';
					body += '<td>'+result.data_log[i].material_qty+'</td>';
					body += '</tr>';
					total_perolehan += parseInt(result.data_log[i].material_qty);
					total_std += parseFloat(result.data_log[i].std);
					total_act += parseFloat(result.data_log[i].act);
				}
				body += '<tr>';
				body += '<td colspan="5" style="text-align: center;">Total</td>';
				body += '<td>'+total_std.toFixed(2)+'</td>';
				body += '<td>'+total_act.toFixed(2)+'</td>';
				body += '<td>'+total_perolehan+'</td>';
				body += '</tr>';
				$('#data-log-body').append(body);


				//Resume
				var ng_rate = total_ng / total_cek * 100;
				var text_ng_rate = '= <sup>Total NG</sup>/<sub>Total Cek</sub> x 100%';
				text_ng_rate += '<br>= <sup>'+ total_ng +'</sup>/<sub>'+ total_cek +'</sub> x 100%';
				text_ng_rate += '<br>= <b>'+ ng_rate.toFixed(2) +'%</b>';
				$('#ng_rate').append(text_ng_rate);

				var posh_rate = ((total_cek - total_ng) / total_cek) * 100;
				var text_posh_rate = '= <sup>(Total Cek - Total NG)</sup>/<sub>Total Cek</sub> x 100%';
				text_posh_rate += '<br>= <sup>('+ total_cek + ' - ' + total_ng +')</sup>/<sub>'+ total_cek +'</sub> x 100%';
				text_posh_rate += '<br>= <b>'+ posh_rate.toFixed(2) +'%</b>';
				$('#posh_rate').append(text_posh_rate);

				var op_eff = posh_rate * (total_std / total_act);
				var text_op_eff = '= <sup>Total Standart time</sup>/<sub>Total Actual time</sub> x Posh Rate';
				text_op_eff += '<br>= <sup>'+ total_std.toFixed(2) +'</sup>/<sub>'+ total_act.toFixed(2) +'</sub> x '+ posh_rate.toFixed(2) +'%';
				text_op_eff += '<br>= <b>'+ op_eff.toFixed(2) +'%</b>';
				$('#op_eff').append(text_op_eff);

			}

		});

}


function fillChart() {
	var group = "{{$_GET['group']}}";
	var tanggal = "{{$_GET['tanggal']}}";
	var target = "{{$_GET['target']}}";

	if(target == ''){
		target = 85;
	}

	var position = $(document).scrollTop();


	$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

	var data = {
		tanggal:tanggal,
		group:group,
	}

		//Show Group				
		group = group.split(',');

		if(group != ''){
			$('#shifta').hide();
			$('#shiftb').hide();
			$('#shiftc').hide();

			$('#shifta4').hide();
			$('#shiftb4').hide();
			$('#shiftc4').hide();


			if(group.length == 1){
				for (var i = 0; i < group.length; i++) {
					$('#shift'+group[i].toLowerCase()).addClass("col-xs-12");
					$('#shift'+group[i].toLowerCase()).show();

					$('#shift'+group[i].toLowerCase()+'4').addClass("col-xs-12");
					$('#shift'+group[i].toLowerCase()+'4').show();
				}
			}else if(group.length == 2){
				for (var i = 0; i < group.length; i++) {
					$('#shift'+group[i].toLowerCase()).addClass("col-xs-6");
					$('#shift'+group[i].toLowerCase()).show();

					$('#shift'+group[i].toLowerCase()+'4').addClass("col-xs-6");
					$('#shift'+group[i].toLowerCase()+'4').show();
				}
			}else if(group.length == 3){
				for (var i = 0; i < group.length; i++) {
					$('#shift'+group[i].toLowerCase()).addClass("col-xs-4");
					$('#shift'+group[i].toLowerCase()).show();

					$('#shift'+group[i].toLowerCase()+'4').addClass("col-xs-4");
					$('#shift'+group[i].toLowerCase()+'4').show();
				}
			}

		}else{
			$('#shifta').addClass("col-xs-4");
			$('#shiftb').addClass("col-xs-4");
			$('#shiftc').addClass("col-xs-4");

			$('#shifta4').addClass("col-xs-4");
			$('#shiftb4').addClass("col-xs-4");
			$('#shiftc4').addClass("col-xs-4");
		}




		$.get('{{ url("fetch/middle/buffing_op_eff") }}', data, function(result, status, xhr) {
			if(result.status){

				var op_name = [];
				var eff_value = [];
				var data = [];
				var loop = 0;

				for(var i = 0; i < result.time_eff.length; i++){
					if(result.time_eff[i].group == 'A'){
						loop += 1;
						for(var j = 0; j < result.emp_name.length; j++){
							if(result.emp_name[j].employee_id == result.time_eff[i].employee_id){
								var name_temp = result.emp_name[j].name.split(" ");
								var xAxis = '';
								xAxis += result.time_eff[i].employee_id + ' - ';

								if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Moch.' || name_temp[0] == 'Mochamad'){
									xAxis += name_temp[0].charAt(0)+'. '+name_temp[1];
								}else{
									xAxis += name_temp[0]+'. '+name_temp[1].charAt(0);
								}
								op_name.push(xAxis);
							}
						}

						var isEmpty = true;
						for(var j = 0; j < result.rate.length; j++){
							if(result.time_eff[i].employee_id == result.rate[j].operator_id){
								eff_value.push(result.rate[j].rate * result.time_eff[i].eff * 100);
								isEmpty = false;
							}
						}
						if(isEmpty){
							eff_value.push(0);
						}


						if(eff_value[loop-1] > parseInt(target)){
							data.push({y: eff_value[loop-1], color: 'rgb(144,238,126)'});
						}else{
							data.push({y: eff_value[loop-1], color: 'rgb(255,116,116)'})
						}
					}

				}

				var chart = Highcharts.chart('container1_shifta', {
					chart: {
						animation: false
					},
					title: {
						text: 'Operators Overall Efficiency',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'Group A on '+ result.date,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							enabled: true,
							text: "Overall Efficiency (%)"
						},
						min: 0,
						plotLines: [{
							color: '#FF0000',
							value: parseInt(target),
							dashStyle: 'shortdash',
							width: 2,
							zIndex: 5,
							label: {
								align:'right',
								text: 'Target '+parseInt(target)+'%',
								x:-7,
								style: {
									fontSize: '12px',
									color: '#FF0000',
									fontWeight: 'bold'
								}
							}
						}],
						labels: {
							enabled: false
						}
					},
					xAxis: {
						categories: op_name,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							rotation: -45,
							style: {
								fontSize: '13px'
							}
						},
					},
					tooltip: {
						headerFormat: '<span>{point.category}</span><br/>',
						pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
					},
					credits: {
						enabled:false
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y:.2f}%',
								rotation: -90,
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
									click: function (event) {
										showDetail(result.date, event.point.category);

									}
								}
							},
						}
					},
					series: [{
						name:'OP Efficiency',
						type: 'column',
						data: data,
						showInLegend: false
					}]

				});


				// Shift B
				var op_name = [];
				var eff_value = [];
				var data = [];
				var loop = 0;

				for(var i = 0; i < result.time_eff.length; i++){
					if(result.time_eff[i].group == 'B'){
						loop += 1;
						for(var j = 0; j < result.emp_name.length; j++){
							if(result.emp_name[j].employee_id == result.time_eff[i].employee_id){
								var name_temp = result.emp_name[j].name.split(" ");
								var xAxis = '';
								xAxis += result.time_eff[i].employee_id + ' - ';

								if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Moch.' || name_temp[0] == 'Mochamad'){
									xAxis += name_temp[0].charAt(0)+'. '+name_temp[1];
								}else{
									xAxis += name_temp[0]+'. '+name_temp[1].charAt(0);
								}
								op_name.push(xAxis);
							}
						}

						var isEmpty = true;
						for(var j = 0; j < result.rate.length; j++){
							if(result.time_eff[i].employee_id == result.rate[j].operator_id){
								eff_value.push(result.rate[j].rate * result.time_eff[i].eff * 100);
								isEmpty = false;
							}
						}
						if(isEmpty){
							eff_value.push(0);
						}


						if(eff_value[loop-1] > parseInt(target)){
							data.push({y: eff_value[loop-1], color: 'rgb(144,238,126)'});
						}else{
							data.push({y: eff_value[loop-1], color: 'rgb(255,116,116)'})
						}
					}

				}

				var chart = Highcharts.chart('container1_shiftb', {
					chart: {
						animation: false
					},
					title: {
						text: 'Operators Overall Efficiency',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'Group B on '+ result.date,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							enabled: true,
							text: "Overall Efficiency (%)"
						},
						min: 0,
						plotLines: [{
							color: '#FF0000',
							value: parseInt(target),
							dashStyle: 'shortdash',
							width: 2,
							zIndex: 5,
							label: {
								align:'right',
								text: 'Target '+parseInt(target)+'%',
								x:-7,
								style: {
									fontSize: '12px',
									color: '#FF0000',
									fontWeight: 'bold'
								}
							}
						}],
						labels: {
							enabled: false
						}
					},
					xAxis: {
						categories: op_name,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							rotation: -45,
							style: {
								fontSize: '13px'
							}
						},
					},
					tooltip: {
						headerFormat: '<span>{point.category}</span><br/>',
						pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
					},
					credits: {
						enabled:false
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y:.2f}%',
								rotation: -90,
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
									click: function (event) {
										showDetail(result.date, event.point.category);

									}
								}
							},
						}
					},
					series: [{
						name:'OP Efficiency',
						type: 'column',
						data: data,
						showInLegend: false
					}]

				});


				// Shift C
				var op_name = [];
				var eff_value = [];
				var data = [];
				var loop = 0;

				for(var i = 0; i < result.time_eff.length; i++){
					if(result.time_eff[i].group == 'C'){
						loop += 1;
						for(var j = 0; j < result.emp_name.length; j++){
							if(result.emp_name[j].employee_id == result.time_eff[i].employee_id){
								var name_temp = result.emp_name[j].name.split(" ");
								var xAxis = '';
								xAxis += result.time_eff[i].employee_id + ' - ';

								if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Moch.' || name_temp[0] == 'Mochamad'){
									xAxis += name_temp[0].charAt(0)+'. '+name_temp[1];
								}else{
									xAxis += name_temp[0]+'. '+name_temp[1].charAt(0);
								}
								op_name.push(xAxis);
							}
						}

						var isEmpty = true;
						for(var j = 0; j < result.rate.length; j++){
							if(result.time_eff[i].employee_id == result.rate[j].operator_id){
								eff_value.push(result.rate[j].rate * result.time_eff[i].eff * 100);
								isEmpty = false;
							}
						}
						if(isEmpty){
							eff_value.push(0);
						}


						if(eff_value[loop-1] > parseInt(target)){
							data.push({y: eff_value[loop-1], color: 'rgb(144,238,126)'});
						}else{
							data.push({y: eff_value[loop-1], color: 'rgb(255,116,116)'})
						}
					}

				}

				var chart = Highcharts.chart('container1_shiftc', {
					chart: {
						animation: false
					},
					title: {
						text: 'Operators Overall Efficiency',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'Group C on '+ result.date,
						style: {
							fontSize: '1vw',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							enabled: true,
							text: "Overall Efficiency (%)"
						},
						min: 0,
						plotLines: [{
							color: '#FF0000',
							value: parseInt(target),
							dashStyle: 'shortdash',
							width: 2,
							zIndex: 5,
							label: {
								align:'right',
								text: 'Target '+parseInt(target)+'%',
								x:-7,
								style: {
									fontSize: '12px',
									color: '#FF0000',
									fontWeight: 'bold'
								}
							}
						}],
						labels: {
							enabled: false
						}
					},
					xAxis: {
						categories: op_name,
						type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						labels: {
							rotation: -45,
							style: {
								fontSize: '13px'
							}
						},
					},
					tooltip: {
						headerFormat: '<span>{point.category}</span><br/>',
						pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
					},
					credits: {
						enabled:false
					},
					plotOptions: {
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y:.2f}%',
								rotation: -90,
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
									click: function (event) {
										showDetail(result.date, event.point.category);

									}
								}
							},
						}
					},
					series: [{
						name:'OP Efficiency',
						type: 'column',
						data: data,
						showInLegend: false
					}]

				});

				$(document).scrollTop(position);

			}


		});


$.get('{{ url("fetch/middle/buffing_op_eff_target") }}', data, function(result, status, xhr) {
	if(result.status){

		$('#eff_target_head').html("");		
		$('#eff_target_body').html("");		

		var head = "<tr>";
		var body1 = "<tr>";
		var body2 = "<tr>";
		var body3 = "<tr>";
		var foot = "<tr>";

		$('#eff_target').css('font-size', '10px');
		// $('#eff_target').css('border-color', '#2a2a2b');
		$('#eff_target_head').css('background-color', '#2a2a2b');
		$('#eff_target_head').css('text-transform', 'uppercase');
		$('#eff_target_head').css('font-size', '25px');
		$('#eff_target_head').css('font-weight', 'bold');
		$('#eff_target_head').css('font-family', 'sans-serif');
		$('#eff_target_body').css('color', '#2a2a2b');


		for(var i = 0; i < result.target.length; i++){
			if(result.target[i].group == group){
				for(var j = 0; j < result.emp_name.length; j++){
					if(result.emp_name[j].employee_id == result.target[i].employee_id){

						if((result.target[i].eff * 100) > 85){
							body1 += "<td style='background-color: rgb(144,238,126); height: 30px; width:4%;'>"+result.target[i].finish+"</td>";
							body2 += "<td style='background-color: rgb(144,238,126); height: 60px; width:4%; font-size: 18px; font-weight:bold;'>"+(result.target[i].eff * 100).toFixed(0)+"%</td>";
							body3 += "<td style='background-color: rgb(144,238,126); height: 30px; width:4%;'>"+result.target[i].key+"</td>";
							foot += "<td style='background-color: rgba(85,139,47,.85); height: 100px;'><div style='transform: rotate(-90deg);'>"+result.target[i].employee_id+"<br>";
							foot += result.emp_name[j].name+"</div></td>";	
						}else{
							body1 += "<td style='background-color: rgb(255,116,116); height: 30px; width:4%;'>"+result.target[i].finish+"</td>";
							body2 += "<td style='background-color: rgb(255,116,116); height: 60px; width:4%; font-size: 18px; font-weight:bold;'>"+(result.target[i].eff * 100).toFixed(0)+"%</td>";
							body3 += "<td style='background-color: rgb(255,116,116); height: 30px; width:4%;'>"+result.target[i].key+"</td>";
							foot += "<td style='background-color: rgba(220,105,0,.75); height: 100px;'><div style='transform: rotate(-90deg);'>"+result.target[i].employee_id+"<br>";
							foot += result.emp_name[j].name+"</div></td>";
						}

					}
				}

			}			
		}
		head += "<th colspan='25'>Last Operators Efficiency</th>";
		head += "</tr>";
		body1 += "</tr>";
		body2 += "</tr>";
		body3 += "</tr>";
		foot += "</tr>";

		$('#eff_target_head').append(head);
		$('#eff_target_body').append(body1 + body2 + body3);

		if(group == ''){
			$('#eff_target').css('display', 'none');
		}




		var op = [];
		var key = [];
		var eff = [];
		var data = [];

		var loop = 0;


		for(var i = 0; i < result.target.length; i++){
			if(result.target[i].group == 'A'){
				loop += 1;
				for(var j = 0; j < result.emp_name.length; j++){
					if(result.emp_name[j].employee_id == result.target[i].employee_id){
						op.push(result.emp_name[j].name);
						key.push(result.target[i].key || 'Not Found');
						eff.push(result.target[i].eff * 100);
					}
				}

				if(eff[loop-1] > parseInt(target)){
					data.push({y: Math.ceil(eff[loop-1]), color: 'rgb(144,238,126)'});
				}else{
					data.push({y: Math.ceil(eff[loop-1]), color: 'rgb(255,116,116)'})
				}
			}			
		}

		var chart = Highcharts.chart('container4_shifta', {
			chart: {
				animation: false
			},
			title: {
				text: 'Last Operators Efficiency Less '+target+'%',
				style: {
					fontSize: '25px',
					fontWeight: 'bold'
				}
			},
			subtitle: {
				text: 'Group A on '+ result.date,
				style: {
					fontSize: '1vw',
					fontWeight: 'bold'
				}
			},
			yAxis: {
				title: {
					enabled: true,
					text: "Efficiency"
				},
				labels: {
					enabled: false
				},
			},
			xAxis:  {
				categories: key,
				gridLineWidth: 1,
				gridLineColor: 'RGB(204,255,255)',
				labels: {
					rotation: -45,
					style: {
						fontSize: '13px'
					}
				},
			},
			tooltip: {
				headerFormat: '<span>{point.category}</span><br/>',
				pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
			},
			credits: {
				enabled:false
			},
			legend : {
				enabled:false
			},
			plotOptions: {
				series:{				
					dataLabels: {
						enabled: true,
						format: '{point.y:.2f}%',
						rotation: -90,
						style:{
							fontSize: '15px'
						}
					},
					animation: false,
					pointPadding: 0.93,
					groupPadding: 0.93,
					borderWidth: 0.93,
					cursor: 'pointer',
				},
			},
			series: [{
				name:'OP Efficiency',
				type: 'column',
				data: data,
				showInLegend: false
			}]

		});


		var op = [];
		var key = [];
		var eff = [];
		var data = [];

		var loop = 0;


		for(var i = 0; i < result.target.length; i++){
			if(result.target[i].group == 'B'){
				loop += 1;
				for(var j = 0; j < result.emp_name.length; j++){
					if(result.emp_name[j].employee_id == result.target[i].employee_id){
						op.push(result.emp_name[j].name);
						key.push(result.target[i].key || 'Not Found');
						eff.push(result.target[i].eff * 100);

					}
				}

				if(eff[loop-1] > parseInt(target)){
					data.push({y: Math.ceil(eff[loop-1]), color: 'rgb(144,238,126)'});
				}else{
					data.push({y: Math.ceil(eff[loop-1]), color: 'rgb(255,116,116)'})
				}
			}			
		}

		var chart = Highcharts.chart('container4_shiftb', {
			chart: {
				animation: false
			},
			title: {
				text: 'Last Operators Efficiency Less '+target+'%',
				style: {
					fontSize: '25px',
					fontWeight: 'bold'
				}
			},
			subtitle: {
				text: 'Group B on '+ result.date,
				style: {
					fontSize: '1vw',
					fontWeight: 'bold'
				}
			},
			yAxis: {
				title: {
					enabled: true,
					text: "Efficiency"
				},
				labels: {
					enabled: false
				},
			},
			xAxis:  {
				categories: key,
				gridLineWidth: 1,
				gridLineColor: 'RGB(204,255,255)',
				labels: {
					rotation: -45,
					style: {
						fontSize: '13px'
					}
				},
			},
			tooltip: {
				headerFormat: '<span>{point.category}</span><br/>',
				pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
			},
			credits: {
				enabled:false
			},
			legend : {
				enabled:false
			},
			plotOptions: {
				series:{				
					dataLabels: {
						enabled: true,
						format: '{point.y:.2f}%',
						rotation: -90,
						style:{
							fontSize: '15px'
						}
					},
					animation: false,
					pointPadding: 0.93,
					groupPadding: 0.93,
					borderWidth: 0.93,
					cursor: 'pointer',
				},
			},
			series: [{
				name:'OP Efficiency',
				type: 'column',
				data: data,
				showInLegend: false
			}]

		});




		var op = [];
		var key = [];
		var eff = [];
		var data = [];

		var loop = 0;


		for(var i = 0; i < result.target.length; i++){
			if(result.target[i].group == 'C'){
				loop += 1;
				for(var j = 0; j < result.emp_name.length; j++){
					if(result.emp_name[j].employee_id == result.target[i].employee_id){
						op.push(result.emp_name[j].name);
						key.push(result.target[i].key || 'Not Found');
						eff.push(result.target[i].eff * 100);

					}
				}

				if(eff[loop-1] > parseInt(target)){
					data.push({y: Math.ceil(eff[loop-1]), color: 'rgb(144,238,126)'});
				}else{
					data.push({y: Math.ceil(eff[loop-1]), color: 'rgb(255,116,116)'})
				}
			}			
		}

		var chart = Highcharts.chart('container4_shiftc', {
			chart: {
				animation: false
			},
			title: {
				text: 'Last Operators Efficiency Less '+target+'%',
				style: {
					fontSize: '25px',
					fontWeight: 'bold'
				}
			},
			subtitle: {
				text: 'Group C on '+ result.date,
				style: {
					fontSize: '1vw',
					fontWeight: 'bold'
				}
			},
			yAxis: {
				title: {
					enabled: true,
					text: "Efficiency"
				},
				labels: {
					enabled: false
				},
			},
			xAxis:  {
				categories: key,
				gridLineWidth: 1,
				gridLineColor: 'RGB(204,255,255)',
				labels: {
					rotation: -45,
					style: {
						fontSize: '13px'
					}
				},
			},
			tooltip: {
				headerFormat: '<span>{point.category}</span><br/>',
				pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
			},
			credits: {
				enabled:false
			},
			legend : {
				enabled:false
			},
			plotOptions: {
				series:{				
					dataLabels: {
						enabled: true,
						format: '{point.y:.2f}%',
						rotation: -90,
						style:{
							fontSize: '15px'
						}
					},
					animation: false,
					pointPadding: 0.93,
					groupPadding: 0.93,
					borderWidth: 0.93,
					cursor: 'pointer',
				},
			},
			series: [{
				name:'OP Efficiency',
				type: 'column',
				data: data,
				showInLegend: false
			}]

		});

		

		$(document).scrollTop(position);

	}
});



$.get('{{ url("fetch/middle/buffing_op_result") }}', data, function(result, status, xhr) {
	if(result.status){


		//Group A
		var op = [];
		var qty = [];

		for(var i = 0; i < result.op_result.length; i++){
			if(result.op_result[i].group == 'A'){
				for(var j = 0; j < result.emp_name.length; j++){
					if(result.op_result[i].operator_id == result.emp_name[j].employee_id){
						var name_temp = result.emp_name[j].name.split(" ");
						if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Mochamad'){
							op.push(name_temp[0].charAt(0)+'. '+name_temp[1]);
						}else{
							if(name_temp[1].length > 7){
								op.push(name_temp[0]+'. '+name_temp[1].charAt(0));
							}else{
								op.push(result.emp_name[j].name);
							}
						}
					}
				}
				qty.push(Math.ceil(result.op_result[i].qty));
			}
		}

		var chart = Highcharts.chart('container2_shifta', {
			chart: {
				animation: false
			},
			title: {
				text: 'Operators Result',
				style: {
					fontSize: '25px',
					fontWeight: 'bold'
				}
			},
			subtitle: {
				text: 'Group A on '+ result.date,
				style: {
					fontSize: '1vw',
					fontWeight: 'bold'
				}
			},
			yAxis: {
				title: {
					enabled: true,
					text: "PC(s)"
				},
				labels: {
					enabled: false
				}
			},
			xAxis: {
				categories: op,
				type: 'category',
				gridLineWidth: 1,
				gridLineColor: 'RGB(204,255,255)',
				labels: {
					rotation: -45,
					style: {
						fontSize: '13px'
					}
				},
			},
			tooltip: {
				headerFormat: '<span>{point.category}</span><br/>',
				pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
			},
			credits: {
				enabled:false
			},
			legend : {
				enabled:false
			},
			plotOptions: {
				series:{
					dataLabels: {
						enabled: true,
						format: '{point.y}',
						style:{
							textOutline: false,
							fontSize: '1vw'
						}
					},
					animation: false,
					cursor: 'pointer'
				},
			},
			series: [
			{
				name:'Result',
				type: 'column',
				color: 'rgb(93,194,193)',
				data: qty,
			}
			]

		});

		//Group B
		var op = [];
		var qty = [];

		for(var i = 0; i < result.op_result.length; i++){
			if(result.op_result[i].group == 'B'){
				for(var j = 0; j < result.emp_name.length; j++){
					if(result.op_result[i].operator_id == result.emp_name[j].employee_id){
						var name_temp = result.emp_name[j].name.split(" ");
						if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Mochamad'){
							op.push(name_temp[0].charAt(0)+'. '+name_temp[1]);
						}else{
							if(name_temp[1].length > 7){
								op.push(name_temp[0]+'. '+name_temp[1].charAt(0));
							}else{
								op.push(result.emp_name[j].name);
							}
						}
					}
				}
				qty.push(Math.ceil(result.op_result[i].qty));
			}
		}


		var chart = Highcharts.chart('container2_shiftb', {
			chart: {
				animation: false
			},
			title: {
				text: 'Operators Result',
				style: {
					fontSize: '25px',
					fontWeight: 'bold'
				}
			},
			subtitle: {
				text: 'Group B on '+ result.date,
				style: {
					fontSize: '1vw',
					fontWeight: 'bold'
				}
			},
			yAxis: {
				title: {
					enabled: true,
					text: "PC(s)"
				},
				labels: {
					enabled: false
				}
			},
			xAxis: {
				categories: op,
				type: 'category',
				gridLineWidth: 1,
				gridLineColor: 'RGB(204,255,255)',
				labels: {
					rotation: -45,
					style: {
						fontSize: '13px'
					}
				},
			},
			tooltip: {
				headerFormat: '<span>{point.category}</span><br/>',
				pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
			},
			credits: {
				enabled:false
			},
			legend : {
				enabled:false
			},
			plotOptions: {
				series:{
					dataLabels: {
						enabled: true,
						format: '{point.y}',
						style:{
							textOutline: false,
							fontSize: '1vw'
						}
					},
					animation: false,
					cursor: 'pointer'
				},
			},
			series: [
			{
				name:'Result',
				type: 'column',
				color: 'rgb(93,194,193)',
				data: qty,
			}
			]

		});

		//Group C
		var op = [];
		var qty = [];

		for(var i = 0; i < result.op_result.length; i++){
			if(result.op_result[i].group == 'C'){
				for(var j = 0; j < result.emp_name.length; j++){
					if(result.op_result[i].operator_id == result.emp_name[j].employee_id){
						var name_temp = result.emp_name[j].name.split(" ");
						if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Mochamad'){
							op.push(name_temp[0].charAt(0)+'. '+name_temp[1]);
						}else{
							if(name_temp[1].length > 7){
								op.push(name_temp[0]+'. '+name_temp[1].charAt(0));
							}else{
								op.push(result.emp_name[j].name);
							}
						}
					}
				}
				qty.push(Math.ceil(result.op_result[i].qty));
			}
		}


		var chart = Highcharts.chart('container2_shiftc', {
			chart: {
				animation: false
			},
			title: {
				text: 'Operators Result',
				style: {
					fontSize: '25px',
					fontWeight: 'bold'
				}
			},
			subtitle: {
				text: 'Group C on '+ result.date,
				style: {
					fontSize: '1vw',
					fontWeight: 'bold'
				}
			},
			yAxis: {
				title: {
					enabled: true,
					text: "PC(s)"
				},
				labels: {
					enabled: false
				}
			},
			xAxis: {
				categories: op,
				type: 'category',
				gridLineWidth: 1,
				gridLineColor: 'RGB(204,255,255)',
				labels: {
					rotation: -45,
					style: {
						fontSize: '13px'
					}
				},
			},
			tooltip: {
				headerFormat: '<span>{point.category}</span><br/>',
				pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
			},
			credits: {
				enabled:false
			},
			legend : {
				enabled:false
			},
			plotOptions: {
				series:{
					dataLabels: {
						enabled: true,
						format: '{point.y}',
						style:{
							textOutline: false,
							fontSize: '1vw'
						}
					},
					animation: false,
					cursor: 'pointer'
				},
			},
			series: [
			{
				name:'Result',
				type: 'column',
				color: 'rgb(93,194,193)',
				data: qty,
			}
			]

		});

		$(document).scrollTop(position);

	}

});

$.get('{{ url("fetch/middle/buffing_op_working") }}', data, function(result, status, xhr) {
	if(result.status){


		//Group A
		var op = [];
		var eff = [];
		var data = [];

		var loop = 0;
		for(var i = 0; i < result.working_time.length; i++){
			if(result.working_time[i].group == 'A'){
				loop += 1;
				for(var j = 0; j < result.emp_name.length; j++){
					if(result.working_time[i].employee_id == result.emp_name[j].employee_id){
						var name_temp = result.emp_name[j].name.split(" ");
						if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Mochamad'){
							op.push(name_temp[0].charAt(0)+'. '+name_temp[1]);
						}else{
							if(name_temp[1].length > 7){
								op.push(name_temp[0]+'. '+name_temp[1].charAt(0));
							}else{
								op.push(result.emp_name[j].name);
							}
						}
					}
				}

				eff.push(result.working_time[i].std / result.working_time[i].act * 100);

				if(eff[loop-1] > 85){
					data.push({y: eff[loop-1], color: 'rgb(144,238,126)'});
				}else{
					data.push({y: eff[loop-1], color: 'rgb(255,116,116)'})
				}
			}			
		}

		var chart = Highcharts.chart('container3_shifta', {
			chart: {
				animation: false
			},
			title: {
				text: 'Operators Efficiency',
				style: {
					fontSize: '25px',
					fontWeight: 'bold'
				}
			},
			subtitle: {
				text: 'Group A on '+ result.date,
				style: {
					fontSize: '1vw',
					fontWeight: 'bold'
				}
			},
			yAxis: {
				title: {
					enabled: true,
					text: "Minutes"
				},
				plotLines: [{
					color: '#FF0000',
					width: 2,
					zIndex: 5,
					dashStyle: 'shortdash',
					value: 85,
					label: {
						align:'right',
						text: 'Target 85%',
						x:-7,
						style: {
							fontSize: '1vw',
							color: '#FF0000',
							fontWeight: 'bold'
						}
					}
				}],
				labels: {
					enabled: false
				}
			},
			xAxis: {
				categories: op,
				type: 'category',
				gridLineWidth: 1,
				gridLineColor: 'RGB(204,255,255)',
				labels: {
					rotation: -45,
					style: {
						fontSize: '13px'
					}
				},
			},
			tooltip: {
				headerFormat: '<span>{point.category}</span><br/>',
				pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
			},
			credits: {
				enabled:false
			},
			legend : {
				enabled:false
			},
			plotOptions: {
				series:{
					dataLabels: {
						enabled: true,
						format: '{point.y:.2f}%',
						rotation: -90,
						style:{
							fontSize: '15px'
						}
					},
					animation: false,
					pointPadding: 0.93,
					groupPadding: 0.93,
					borderWidth: 0.93,
					cursor: 'pointer',
				},
			},
			series: [
			{
				name:'Operator Efficiency',
				type: 'column',
				data: data,
			}
			]

		});


		//Group B
		var op = [];
		var eff = [];
		var data = [];

		var loop = 0;
		for(var i = 0; i < result.working_time.length; i++){
			if(result.working_time[i].group == 'B'){
				loop += 1;
				for(var j = 0; j < result.emp_name.length; j++){
					if(result.working_time[i].employee_id == result.emp_name[j].employee_id){
						var name_temp = result.emp_name[j].name.split(" ");
						if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Mochamad'){
							op.push(name_temp[0].charAt(0)+'. '+name_temp[1]);
						}else{
							if(name_temp[1].length > 7){
								op.push(name_temp[0]+'. '+name_temp[1].charAt(0));
							}else{
								op.push(result.emp_name[j].name);
							}
						}
					}
				}

				eff.push(result.working_time[i].std / result.working_time[i].act * 100);

				if(eff[loop-1] > 85){
					data.push({y: eff[loop-1], color: 'rgb(144,238,126)'});
				}else{
					data.push({y: eff[loop-1], color: 'rgb(255,116,116)'})
				}
			}			
		}

		var chart = Highcharts.chart('container3_shiftb', {
			chart: {
				animation: false
			},
			title: {
				text: 'Operators Efficiency',
				style: {
					fontSize: '25px',
					fontWeight: 'bold'
				}
			},
			subtitle: {
				text: 'Group B on '+ result.date,
				style: {
					fontSize: '1vw',
					fontWeight: 'bold'
				}
			},
			yAxis: {
				title: {
					enabled: true,
					text: "Minutes"
				},
				plotLines: [{
					color: '#FF0000',
					width: 2,
					zIndex: 5,
					dashStyle: 'shortdash',
					value: 85,
					label: {
						align:'right',
						text: 'Target 85%',
						x:-7,
						style: {
							fontSize: '1vw',
							color: '#FF0000',
							fontWeight: 'bold'
						}
					}
				}],
				labels: {
					enabled: false
				}
			},
			xAxis: {
				categories: op,
				type: 'category',
				gridLineWidth: 1,
				gridLineColor: 'RGB(204,255,255)',
				labels: {
					rotation: -45,
					style: {
						fontSize: '13px'
					}
				},
			},
			tooltip: {
				headerFormat: '<span>{point.category}</span><br/>',
				pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
			},
			credits: {
				enabled:false
			},
			legend : {
				enabled:false
			},
			plotOptions: {
				series:{
					dataLabels: {
						enabled: true,
						format: '{point.y:.2f}%',
						rotation: -90,
						style:{
							fontSize: '15px'
						}
					},
					animation: false,
					pointPadding: 0.93,
					groupPadding: 0.93,
					borderWidth: 0.93,
					cursor: 'pointer',
				},
			},
			series: [
			{
				name:'Operator Efficiency',
				type: 'column',
				data: data,
			}
			]

		});


		//Group C
		var op = [];
		var eff = [];
		var data = [];

		var loop = 0;
		for(var i = 0; i < result.working_time.length; i++){
			if(result.working_time[i].group == 'C'){
				loop += 1;
				for(var j = 0; j < result.emp_name.length; j++){
					if(result.working_time[i].employee_id == result.emp_name[j].employee_id){
						var name_temp = result.emp_name[j].name.split(" ");
						if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Mochamad'){
							op.push(name_temp[0].charAt(0)+'. '+name_temp[1]);
						}else{
							if(name_temp[1].length > 7){
								op.push(name_temp[0]+'. '+name_temp[1].charAt(0));
							}else{
								op.push(result.emp_name[j].name);
							}
						}
					}
				}

				eff.push(result.working_time[i].std / result.working_time[i].act * 100);

				if(eff[loop-1] > 85){
					data.push({y: eff[loop-1], color: 'rgb(144,238,126)'});
				}else{
					data.push({y: eff[loop-1], color: 'rgb(255,116,116)'})
				}
			}			
		}

		var chart = Highcharts.chart('container3_shiftc', {
			chart: {
				animation: false
			},
			title: {
				text: 'Operators Efficiency',
				style: {
					fontSize: '25px',
					fontWeight: 'bold'
				}
			},
			subtitle: {
				text: 'Group C on '+ result.date,
				style: {
					fontSize: '1vw',
					fontWeight: 'bold'
				}
			},
			yAxis: {
				title: {
					enabled: true,
					text: "Minutes"
				},
				plotLines: [{
					color: '#FF0000',
					width: 2,
					zIndex: 5,
					dashStyle: 'shortdash',
					value: 85,
					label: {
						align:'right',
						text: 'Target 85%',
						x:-7,
						style: {
							fontSize: '1vw',
							color: '#FF0000',
							fontWeight: 'bold'
						}
					}
				}],
				labels: {
					enabled: false
				}
			},
			xAxis: {
				categories: op,
				type: 'category',
				gridLineWidth: 1,
				gridLineColor: 'RGB(204,255,255)',
				labels: {
					rotation: -45,
					style: {
						fontSize: '13px'
					}
				},
			},
			tooltip: {
				headerFormat: '<span>{point.category}</span><br/>',
				pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
			},
			credits: {
				enabled:false
			},
			legend : {
				enabled:false
			},
			plotOptions: {
				series:{
					dataLabels: {
						enabled: true,
						format: '{point.y:.2f}%',
						rotation: -90,
						style:{
							fontSize: '15px'
						}
					},
					animation: false,
					pointPadding: 0.93,
					groupPadding: 0.93,
					borderWidth: 0.93,
					cursor: 'pointer',
				},
			},
			series: [
			{
				name:'Operator Efficiency',
				type: 'column',
				data: data,
			}
			]

		});

		$(document).scrollTop(position);
	}

});


}



</script>
@endsection