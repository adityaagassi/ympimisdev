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
				<form method="GET" action="{{ action('MiddleProcessController@indexBuffingOpNg') }}">

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
					<div class="col-xs-2">
						<button class="btn btn-success" type="submit">Update Chart</button>
					</div>
				</form>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
			</div>
			<div class="col-xs-12" style="margin-top: 5px;">
				<div id="shifta">
					<div id="container1_shifta" style="width: 100%;"></div>					
				</div>
				<div id="shiftb">
					<div id="container1_shiftb" style="width: 100%;"></div>					
				</div>
				<div id="shiftc">
					<div id="container1_shiftc" style="width: 100%;"></div>					
				</div>			</div>
				<div class="col-xs-12" style="margin-top: 5px;">
					<div id="container2" style="width: 100%;"></div>
				</div>
			</div>
		</div>

		<!-- start modal -->
		<div class="modal fade" id="myModal" style="color: black;">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>NG Rate Operator Details</b></h4>
						<h5 class="modal-title" style="text-align: center;" id="judul"></h5>
					</div>
					<div class="modal-body">
						<div class="row">
							{{-- <h5 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Resume</b></h5> --}}

							<div class="col-md-12" style="margin-bottom: 20px;">
								<div class="col-md-6">
									<h5 class="modal-title">NG Rate</h5><br>
									<h5 class="modal-title" id="ng_rate"></h5>
								</div>
								<div class="col-md-6">
									<div id="modal_ng" style="height: 200px"></div>
								</div>
							</div>

							<div class="col-md-5">
								<h5 class="modal-title" style="text-transform: uppercase;"><b>Good</b></h5>
								<table id="middle-log" class="table table-striped table-bordered" style="width: 100%;"> 
									<thead id="middle-log-head" style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th>Finish Buffing</th>
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
							<div class="col-md-7">
								<h5 class="modal-title" style="text-transform: uppercase;"><b>Not Good</b></h5>
								<table id="middle-ng-log" class="table table-striped table-bordered" style="width: 100%;"> 
									<thead id="middle-ng-log-head" style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 15%;">Finish Buffing</th>
											<th>Model</th>
											<th>Key</th>
											<th>OP Kensa</th>
											<th>NG Name</th>
											<th style="width: 5%;">Material Qty</th>
										</tr>
									</thead>
									<tbody id="middle-ng-log-body">
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
			return year + "-" + month + "-" + day + " (" + h + ":" + m + ":" + s +")";
		}


		function showDetail(tgl, nama) {
			var data = {
				tgl:tgl,
				nama:nama,
			}

			$('#myModal').modal('show');
			$('#middle-log-body').append().empty();
			$('#middle-ng-log-body').append().empty();
			$('#ng_rate').append().empty();
			$('#posh_rate').append().empty();
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
				for (var i = 0; i < result.ng_ng.length; i++) {
					body += '<tr>';
					body += '<td>'+result.ng_ng[i].buffing_time+'</td>';
					body += '<td>'+result.ng_ng[i].model+'</td>';
					body += '<td>'+result.ng_ng[i].key+'</td>';
					body += '<td>'+result.ng_ng[i].op_kensa+'</td>';
					body += '<td>'+result.ng_ng[i].ng_name+'</td>';
					body += '<td>'+result.ng_ng[i].quantity+'</td>';
					body += '</tr>';

					total_ng += parseInt(result.ng_ng[i].quantity);
				}
				body += '<tr>';
				body += '<td colspan="5" style="text-align: center;">Total</td>';
				body += '<td>'+total_ng+'</td>';
				body += '</tr>';
				$('#middle-ng-log-body').append(body);


				//Resume
				var ng_rate = total_ng / total_good * 100;
				var text_ng_rate = '= <sup>Total NG</sup>/<sub>Total Good</sub> x 100%';
				text_ng_rate += '<br>= <sup>'+ total_ng +'</sup>/<sub>'+ total_good +'</sub> x 100%';
				text_ng_rate += '<br>= <b>'+ ng_rate.toFixed(2) +'%</b>';
				$('#ng_rate').append(text_ng_rate);


				//Chart NG
				var data = [];
				var ng_name = [];
				var qty = [];
				for (var i = 0; i < result.ng_qty.length; i++) {
					
					ng_name.push(result.ng_qty[i].ng_name);
					qty.push(result.ng_qty[i].qty);
					
					if(i == 0){
						data.push([ng_name[i], qty[i], true, false]);
					}else{
						data.push([ng_name[i], qty[i], false, false]);
					}

				}

				Highcharts.chart('modal_ng', {
					chart: {
						styledMode: true,
						backgroundColor: null,
						borderWidth: null,
						plotBackgroundColor: null,
						plotShadow: null,
						plotBorderWidth: null,
						plotBackgroundImage: null
					},

					title: {
						text: '',
						style: {
							display: 'none'
						}
					},
					exporting: {
						enabled: false 
					},
					tooltip: {
						enabled: false
					},
					plotOptions: {
						pie: {
							animation: false,
							dataLabels: {
								useHTML: true,
								enabled: true,
								format: '<span style="color:#121212"><b>{point.name}</b>:</span><br><span style="color:#121212">total = {point.y} PC(s)</span>',
								style:{
									textOutline: true,
								}
							}
						}
					},
					credits: {
						enabled:false
					},
					series: [{
						type: 'pie',
						allowPointSelect: true,
						keys: ['name', 'y', 'selected', 'sliced'],
						data: data,
					}]
				});

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

			if(group.length == 1){
				for (var i = 0; i < group.length; i++) {
					$('#shift'+group[i].toLowerCase()).addClass("col-xs-12");
					$('#shift'+group[i].toLowerCase()).show();
				}
			}else if(group.length == 2){
				for (var i = 0; i < group.length; i++) {
					$('#shift'+group[i].toLowerCase()).addClass("col-xs-6");
					$('#shift'+group[i].toLowerCase()).show();
				}
			}else if(group.length == 3){
				for (var i = 0; i < group.length; i++) {
					$('#shift'+group[i].toLowerCase()).addClass("col-xs-4");
					$('#shift'+group[i].toLowerCase()).show();
				}
			}

		}else{
			$('#shifta').addClass("col-xs-4");
			$('#shiftb').addClass("col-xs-4");
			$('#shiftc').addClass("col-xs-4");
		}

		$.get('{{ url("fetch/middle/buffing_op_ng") }}', data, function(result, status, xhr) {
			if(result.status){

				var date = result.date;

				// GROUP A
				var op_name = [];
				var rate = [];
				for(var i = 0; i < result.ng_rate.length; i++){
					if(result.ng_rate[i].shift == 'A'){
						var name_temp = result.ng_rate[i].name.split(" ");
						var xAxis = '';
						xAxis += result.ng_rate[i].operator_id + ' - ';

						if(name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Moch.' || name_temp[0] == 'Mochamad'){
							xAxis += name_temp[0].charAt(0)+'. '+name_temp[1];
						}else{
							xAxis += name_temp[0]+' '+name_temp[1].charAt(0);
						}

						op_name.push(xAxis);
						rate.push(result.ng_rate[i].rate);						
					}
				}

				var chart = Highcharts.chart('container1_shifta', {
					chart: {
						animation: false
					},
					title: {
						text: 'NG Rate By Operators',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'Shift A on '+date,
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
										showDetail(date, event.point.category);
									}
								}
							},
						}
					},
					series: [{
						name:'NG Rate',
						type: 'column',
						color: 'rgb(68,169,168)',
						data: rate,
						showInLegend: false
					}]
				});

				

				// GROUP B
				var op_name = [];
				var rate = [];
				for(var i = 0; i < result.ng_rate.length; i++){
					if(result.ng_rate[i].shift == 'B'){
						var name_temp = result.ng_rate[i].name.split(" ");
						var xAxis = '';
						xAxis += result.ng_rate[i].operator_id + ' - ';

						if(name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Moch.' || name_temp[0] == 'Mochamad'){
							xAxis += name_temp[0].charAt(0)+'. '+name_temp[1];
						}else{
							xAxis += name_temp[0]+'. '+name_temp[1].charAt(0);
						}

						op_name.push(xAxis);
						rate.push(result.ng_rate[i].rate);				
					}
				}

				var chart = Highcharts.chart('container1_shiftb', {
					chart: {
						animation: false
					},
					title: {
						text: 'NG Rate By Operators',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'Group B on '+date,
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
										showDetail(date, event.point.category);
									}
								}
							},
						}
					},
					series: [{
						name:'NG Rate',
						type: 'column',
						color: 'rgb(169,255,151)',
						data: rate,
						showInLegend: false
					}]
				});


				// GROUP C
				var op_name = [];
				var rate = [];
				for(var i = 0; i < result.ng_rate.length; i++){
					if(result.ng_rate[i].shift == 'C'){
						var name_temp = result.ng_rate[i].name.split(" ");
						var xAxis = '';
						xAxis += result.ng_rate[i].operator_id + ' - ';

						if(name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Moch.' || name_temp[0] == 'Mochamad'){
							xAxis += name_temp[0].charAt(0)+'. '+name_temp[1];
						}else{
							xAxis += name_temp[0]+'. '+name_temp[1].charAt(0);
						}

						op_name.push(xAxis);
						rate.push(result.ng_rate[i].rate);	
					}
				}

				var chart = Highcharts.chart('container1_shiftc', {
					chart: {
						animation: false
					},
					title: {
						text: 'NG Rate By Operators',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'Group C on '+date,
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
										showDetail(date, event.point.category);
									}
								}
							},
						}
					},
					series: [{
						name:'NG Rate',
						type: 'column',
						color: 'rgb(255,116,116)',
						data: rate,
						showInLegend: false
					}]
				});
			}
		});

$.get('{{ url("fetch/middle/buffing_daily_op_ng_rate") }}', function(result, status, xhr) {
	if(result.status){

		var seriesData = [];
		var data = [];


		for (var i = 0; i < result.op.length; i++) {
			data = [];
			for (var j = 0; j < result.ng_rate.length; j++) {
				if(result.op[i].operator_id == result.ng_rate[j].operator_id){
					if(Date.parse(result.ng_rate[j].week_date) > Date.parse('2019-10-01')){
						if(result.ng_rate[j].ng_rate == 0){
							data.push([Date.parse(result.ng_rate[j].week_date), null]);
						}else{
							data.push([Date.parse(result.ng_rate[j].week_date), result.ng_rate[j].ng_rate]);
						}
					}else{
						data.push([Date.parse(result.ng_rate[j].week_date), null]);

					}
				}
			}
			seriesData.push({name : result.op[i].name, data: data});
		}

		var chart = Highcharts.stockChart('container2', {
			chart:{
				type:'spline',
			},
			rangeSelector: {
				selected: 0
			},
			scrollbar:{
				enabled:false
			},
			navigator:{
				enabled:false
			},
			title: {
				text: 'Daily NG Rate By Operators',
				style: {
					fontSize: '30px',
					fontWeight: 'bold'
				}
			},
			subtitle: {
				text: 'Last Update: '+getActualFullDate(),
				style: {
					fontSize: '18px',
					fontWeight: 'bold'
				}
			},
			yAxis: {
				title: {
					text: 'NG Rate (%)'
				},
				plotLines: [{
					color: '#FFFFFF',
					width: 2,
					value: 0,
					dashStyles: 'longdashdot'
				}],
			},
			xAxis: {
				categories: 'datetime',
				tickInterval: 24 * 3600 * 1000 
			},
			tooltip: {
				pointFormat: '<span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y:.2f}%</b>',
				split: false,
			},
			legend : {
				enabled:false
			},
			credits: {
				enabled:false
			},
			plotOptions: {
				series: {
					animation: false,
					dataLabels: {
						enabled: true,
						format: '{point.y:,.1f}%',
					},
					connectNulls: true,
					shadow: {
						width: 3,
						opacity: 0.4
					},
					label: {
						connectorAllowed: false
					},
					cursor: 'pointer',
				}
			},
			series: seriesData,
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
});
}

</script>
@endsection