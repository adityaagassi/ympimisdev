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
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		text-align: center;
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
							<select class="form-control select2" multiple="multiple" id='groupSelect' onchange="change()" data-placeholder="Select Group" style="width: 100%;">
								<option value="A">GROUP A</option>
								<option value="B">GROUP B</option>
								<option value="C">GROUP C</option>
							</select>
							<input type="text" name="group" id="group" hidden>			
						</div>
					</div>
					<div class="col-xs-1">
						<button class="btn btn-success" type="submit">Update Chart</button>
					</div>
				</form>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>

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

			<div class="col-xs-12" style="margin-top: 1%; padding: 0px;">
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

			<div class="col-xs-12" style="margin-top: 1%; padding: 0px;">
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
							<h5 class="modal-title" style="text-transform: uppercase;"><b>Good</b></h5>
							<table id="middle-log" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="middle-log-head" style="background-color: rgba(126,86,134,.7);">
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
							<h5 class="modal-title" style="text-transform: uppercase;"><b>Not Good</b></h5>
							<table id="middle-ng-log" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="middle-ng-log-head" style="background-color: rgba(126,86,134,.7);">
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
						<div class="col-md-12">
							<h5 class="modal-title" style="text-transform: uppercase;"><b>Buffing Result</b></h5>
							<table id="data-log" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="data-log-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th>Model</th>
										<th>Key</th>
										<th style="width: 13%">Akan</th>
										<th style="width: 13%">Sedang</th>
										<th style="width: 13%">Selesai</th>
										<th>Standart time</th>
										<th>Actual time</th>
										<th>Material Qty</th>
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
		setInterval(fillChart, 10000);

	});

	function change() {
		$("#group").val($("#groupSelect").val());
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
				var ng_rate = total_ng / total_good * 100;
				var text_ng_rate = '= <sup>Total NG</sup>/<sub>Total Good</sub> x 100%';
				text_ng_rate += '<br>= <sup>'+ total_ng +'</sup>/<sub>'+ total_good +'</sub> x 100%';
				text_ng_rate += '<br>= <b>'+ ng_rate.toFixed(2) +'%</b>';
				$('#ng_rate').append(text_ng_rate);

				var posh_rate = ((total_good - total_ng) / total_good) * 100;
				var text_posh_rate = '= <sup>(Total Good - Total NG)</sup>/<sub>Total Good</sub> x 100%';
				text_posh_rate += '<br>= <sup>('+ total_good + ' - ' + total_ng +')</sup>/<sub>'+ total_good +'</sub> x 100%';
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

			$('#shifta2').hide();
			$('#shiftb2').hide();
			$('#shiftc2').hide();

			$('#shifta3').hide();
			$('#shiftb3').hide();
			$('#shiftc3').hide();


			if(group.length == 1){
				for (var i = 0; i < group.length; i++) {
					$('#shift'+group[i].toLowerCase()).addClass("col-xs-12");
					$('#shift'+group[i].toLowerCase()).show();

					$('#shift'+group[i].toLowerCase()+'2').addClass("col-xs-12");
					$('#shift'+group[i].toLowerCase()+'2').show();

					$('#shift'+group[i].toLowerCase()+'3').addClass("col-xs-12");
					$('#shift'+group[i].toLowerCase()+'3').show();
				}
			}else if(group.length == 2){
				for (var i = 0; i < group.length; i++) {
					$('#shift'+group[i].toLowerCase()).addClass("col-xs-6");
					$('#shift'+group[i].toLowerCase()).show();

					$('#shift'+group[i].toLowerCase()+'2').addClass("col-xs-6");
					$('#shift'+group[i].toLowerCase()+'2').show();

					$('#shift'+group[i].toLowerCase()+'3').addClass("col-xs-6");
					$('#shift'+group[i].toLowerCase()+'3').show();
				}
			}else if(group.length == 3){
				for (var i = 0; i < group.length; i++) {
					$('#shift'+group[i].toLowerCase()).addClass("col-xs-4");
					$('#shift'+group[i].toLowerCase()).show();

					$('#shift'+group[i].toLowerCase()+'2').addClass("col-xs-4");
					$('#shift'+group[i].toLowerCase()+'2').show();

					$('#shift'+group[i].toLowerCase()+'3').addClass("col-xs-4");
					$('#shift'+group[i].toLowerCase()+'3').show();
				}
			}

		}else{
			$('#shifta').addClass("col-xs-4");
			$('#shiftb').addClass("col-xs-4");
			$('#shiftc').addClass("col-xs-4");

			$('#shifta2').addClass("col-xs-4");
			$('#shiftb2').addClass("col-xs-4");
			$('#shiftc2').addClass("col-xs-4");

			$('#shifta3').addClass("col-xs-4");
			$('#shiftb3').addClass("col-xs-4");
			$('#shiftc3').addClass("col-xs-4");
		}




		$.get('{{ url("fetch/middle/buffing_op_eff") }}', data, function(result, status, xhr) {
			if(result.status){
				
				// Shift A
				var eff = [];
				for(var i = 0; i < result.rate.length; i++){
					if(result.rate[i].shift == 'A'){
						for(var j = 0; j < result.time_eff.length; j++){
							if(result.rate[i].operator_id == result.time_eff[j].operator_id){

								var name_temp = result.rate[i].name.split(" ");
								var xAxis = '';
								var eff_value = 0;
								xAxis += result.rate[i].operator_id + ' - ';

								if(name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Moch.' || name_temp[0] == 'Mochamad'){
									xAxis += name_temp[0].charAt(0)+'. '+name_temp[1];
								}else{
									xAxis += name_temp[0]+'. '+name_temp[1].charAt(0);

								}

								eff_value = (result.rate[i].rate * result.time_eff[j].eff * 100);
								if(eff_value < 0){
									eff_value = 0;
								}

								eff.push([xAxis, eff_value]);
							}
						}
					}					
				}

				eff.sort(function(a, b){return b[1] - a[1]});
				var op_name = [];
				var eff_value = [];
				for (var i = 0; i < eff.length; i++) {
					op_name.push(eff[i][0]);
					eff_value.push(eff[i][1]);
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
						visible: false,
						min: 0
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
							minPointLength: 5,
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
						color: 'rgb(68,169,168)',
						data: eff_value,
						showInLegend: false
					}]

				});


				// Shift B
				var eff = [];
				for(var i = 0; i < result.rate.length; i++){
					if(result.rate[i].shift == 'B'){
						for(var j = 0; j < result.time_eff.length; j++){
							if(result.rate[i].operator_id == result.time_eff[j].operator_id){
								var name_temp = result.rate[i].name.split(" ");
								var xAxis = '';
								var eff_value = 0;
								xAxis += result.rate[i].operator_id + ' - ';

								if(name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Moch.' || name_temp[0] == 'Mochamad'){
									xAxis += name_temp[0].charAt(0)+'. '+name_temp[1];
								}else{
									xAxis += name_temp[0]+'. '+name_temp[1].charAt(0);

								}

								eff_value = (result.rate[i].rate * result.time_eff[j].eff * 100);
								if(eff_value < 0){
									eff_value = 0;
								}

								eff.push([xAxis, eff_value]);
							}
						}
					}					
				}

				eff.sort(function(a, b){return b[1] - a[1]});
				var op_name = [];
				var eff_value = [];
				for (var i = 0; i < eff.length; i++) {
					op_name.push(eff[i][0]);
					eff_value.push(eff[i][1]);
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
						visible: false,
						min: 0
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
							minPointLength: 5,
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
						color: 'rgb(169,255,151)',
						data: eff_value,
						showInLegend: false
					}]

				});


				// Shift C
				var eff = [];
				for(var i = 0; i < result.rate.length; i++){
					if(result.rate[i].shift == 'C'){
						for(var j = 0; j < result.time_eff.length; j++){
							if(result.rate[i].operator_id == result.time_eff[j].operator_id){
								var name_temp = result.rate[i].name.split(" ");
								var xAxis = '';
								var eff_value = 0;
								xAxis += result.rate[i].operator_id + ' - ';

								if(name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Moch.' || name_temp[0] == 'Mochamad'){
									xAxis += name_temp[0].charAt(0)+'. '+name_temp[1];
								}else{
									xAxis += name_temp[0]+'. '+name_temp[1].charAt(0);

								}

								eff_value = (result.rate[i].rate * result.time_eff[j].eff * 100);
								if(eff_value < 0){
									eff_value = 0;
								}

								eff.push([xAxis, eff_value]);
							}
						}
					}					
				}

				eff.sort(function(a, b){return b[1] - a[1]});
				var op_name = [];
				var eff_value = [];
				for (var i = 0; i < eff.length; i++) {
					op_name.push(eff[i][0]);
					eff_value.push(eff[i][1]);
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
						visible: false
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
							minPointLength: 5,
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
						color: 'rgb(255,116,116)',
						data: eff_value,
						showInLegend: false
					}]

				});

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
						if(name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Mochamad'){
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
					fontSize: '30px',
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
						if(name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Mochamad'){
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
					fontSize: '30px',
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
						if(name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Mochamad'){
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
					fontSize: '30px',
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

	}

});

$.get('{{ url("fetch/middle/buffing_op_working") }}', data, function(result, status, xhr) {
	if(result.status){


		//Group A
		var op = [];
		var act = [];
		var std = [];
		var target = [];

		for(var i = 0; i < result.working_time.length; i++){
			if(result.working_time[i].group == 'A'){
				for(var j = 0; j < result.emp_name.length; j++){
					if(result.working_time[i].operator_id == result.emp_name[j].employee_id){
						var name_temp = result.emp_name[j].name.split(" ");
						if(name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Mochamad'){
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
				act.push(Math.ceil(result.working_time[i].act));
				std.push(Math.ceil(result.working_time[i].std));
				target.push(parseInt(480));
			}			
		}


		var chart = Highcharts.chart('container3_shifta', {
			chart: {
				animation: false
			},
			title: {
				text: 'Operators Working Time',
				style: {
					fontSize: '30px',
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
				max: 500,
				plotLines: [{
					color: '#FF0000',
					width: 2,
					value: 480,
					label: {
						align:'right',
						text: '480 Minutes',
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
				pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
			},
			credits: {
				enabled:false
			},
			legend : {
				align: 'center',
				verticalAlign: 'bottom',
				x: 0,
				y: 0,

				backgroundColor: (
					Highcharts.theme && Highcharts.theme.background2) || 'white',
				shadow: false
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
				name:'Standart time',
				type: 'column',
				color: 'rgb(255,116,116)',
				data: std
			},
			{
				name:'Actual Time',
				type: 'column',
				color: 'rgb(144,238,126)',
				data: act,
			}
			]

		});

		//Group B
		var op = [];
		var act = [];
		var std = [];
		var target = [];

		for(var i = 0; i < result.working_time.length; i++){
			if(result.working_time[i].group == 'B'){
				for(var j = 0; j < result.emp_name.length; j++){
					if(result.working_time[i].operator_id == result.emp_name[j].employee_id){
						var name_temp = result.emp_name[j].name.split(" ");
						if(name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Mochamad'){
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
				act.push(Math.ceil(result.working_time[i].act));
				std.push(Math.ceil(result.working_time[i].std));
				target.push(parseInt(480));
			}			
		}


		var chart = Highcharts.chart('container3_shiftb', {
			chart: {
				animation: false
			},
			title: {
				text: 'Operators Working Time',
				style: {
					fontSize: '30px',
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
				max: 500,
				plotLines: [{
					color: '#FF0000',
					width: 2,
					value: 480,
					label: {
						align:'right',
						text: '480 Minutes',
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
				pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
			},
			credits: {
				enabled:false
			},
			legend : {
				align: 'center',
				verticalAlign: 'bottom',
				x: 0,
				y: 0,

				backgroundColor: (
					Highcharts.theme && Highcharts.theme.background2) || 'white',
				shadow: false
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
				name:'Standart time',
				type: 'column',
				color: 'rgb(255,116,116)',
				data: std
			},
			{
				name:'Actual Time',
				type: 'column',
				color: 'rgb(144,238,126)',
				data: act,
			}
			]

		});

		//Group C
		var op = [];
		var act = [];
		var std = [];
		var target = [];

		for(var i = 0; i < result.working_time.length; i++){
			if(result.working_time[i].group == 'C'){
				for(var j = 0; j < result.emp_name.length; j++){
					if(result.working_time[i].operator_id == result.emp_name[j].employee_id){
						var name_temp = result.emp_name[j].name.split(" ");
						if(name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Mochamad'){
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
				act.push(Math.ceil(result.working_time[i].act));
				std.push(Math.ceil(result.working_time[i].std));
				target.push(parseInt(480));
			}			
		}


		var chart = Highcharts.chart('container3_shiftc', {
			chart: {
				animation: false
			},
			title: {
				text: 'Operators Working Time',
				style: {
					fontSize: '30px',
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
				max: 500,
				plotLines: [{
					color: '#FF0000',
					width: 2,
					value: 480,
					label: {
						align:'right',
						text: '480 Minutes',
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
				pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
			},
			credits: {
				enabled:false
			},
			legend : {
				align: 'center',
				verticalAlign: 'bottom',
				x: 0,
				y: 0,

				backgroundColor: (
					Highcharts.theme && Highcharts.theme.background2) || 'white',
				shadow: false
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
				name:'Standart time',
				type: 'column',
				color: 'rgb(255,116,116)',
				data: std
			},
			{
				name:'Actual Time',
				type: 'column',
				color: 'rgb(144,238,126)',
				data: act,
			}
			]

		});

	}

});


}



</script>
@endsection