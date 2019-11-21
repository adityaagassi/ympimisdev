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
	.taranai {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: #2b908f;
		display: inline-block;
	}
	.solder {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: #90ee7e;
		display: inline-block;
	}
	.kizu {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: #f45b5b;
		display: inline-block;
	}
	.others {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: #7798BF;
		display: inline-block;
	}
	.nagare {
		margin-left:20px;
		width: 12px;
		height: 12px;
		border-radius: 50%;
		background: #aaeeee;
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
								<option value="15">15%</option>
								<option value="20">20%</option>
								<option value="25">25%</option>
								<option value="30">30%</option>
								<option value="35">35%</option>
							</select>
							<input type="text" name="target" id="target" hidden>			
						</div>
					</div>

					<div class="col-xs-2">
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

			<div class="col-xs-12" style="margin-top: 1%; display: none;">
				<table class="table table-striped table-bordered" style="width: 100%; margin: 0px;"> 
					<thead id="" style="background-color: rgba(126,86,134,.7);">
						<tr>
							<th colspan="12" style="text-align: center;">Hourly NG Rate</th>
						</tr>
						<tr>
							<th style="width: 16.67%;">00.00 - 01.00</th>
							<th style="width: 16.67%;">01.00 - 03.00</th>
							<th style="width: 16.67%;">03.00 - 05.00</th>
							<th style="width: 16.67%;">05.00 - 07.00</th>
							<th style="width: 16.67%;">07.00 - 09.00</th>
							<th style="width: 16.67%;">09.00 - 11.00</th>
						</tr>
					</thead>
					<tbody id="">
						<tr>
							<td style="background-color: #333;"><div id="time0" style="width: 100%;"></div></td>
							<td style="background-color: #333;"><div id="time1" style="width: 100%;"></div></td>
							<td style="background-color: #333;"><div id="time2" style="width: 100%;"></div></td>
							<td style="background-color: #333;"><div id="time3" style="width: 100%;"></div></td>
							<td style="background-color: #333;"><div id="time4" style="width: 100%;"></div></td>
							<td style="background-color: #333;"><div id="time5" style="width: 100%;"></div></td>
						</tr>
					</tbody>
				</table>
				<table class="table table-striped table-bordered" style="width: 100%; margin: 0px;"> 
					<thead id="" style="background-color: rgba(126,86,134,.7);">
						<tr>
							<th style="width: 16.67%;">11.00 - 14.00</th>
							<th style="width: 16.67%;">14.00 - 16.00</th>
							<th style="width: 16.67%;">16.00 - 18.00</th>
							<th style="width: 16.67%;">18.00 - 20.00</th>
							<th style="width: 16.67%;">20.00 - 22.00</th>
							<th style="width: 16.67%;">22.00 - 24.00</th>
						</tr>
					</thead>
					<tbody id="">
						<tr>
							<td style="background-color: #333;"><div id="time6" style="width: 100%;"></div></td>
							<td style="background-color: #333;"><div id="time7" style="width: 100%;"></div></td>
							<td style="background-color: #333;"><div id="time8" style="width: 100%;"></div></td>
							<td style="background-color: #333;"><div id="time9" style="width: 100%;"></div></td>
							<td style="background-color: #333;"><div id="time10" style="width: 100%;"></div></td>
							<td style="background-color: #333;"><div id="time11" style="width: 100%;"></div></td>
						</tr>
						
					</tbody>
				</table>
				<table class="table table-striped table-bordered" style="width: 100%; margin: 0px;"> 
					<thead id="">
						<tr>
							<th colspan="6">
								<div class="taranai"></div> Buff Tarinai
								<div class="solder"></div> NG Soldering
								<div class="kizu"></div> Kizu
								<div class="others"></div> Buff Others (Aus, Nami, dll)
								<div class="nagare"></div> Buff Nagare
							</th>
						</tr>
					</thead>
				</table>
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
							<table id="middle-log" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="middle-log-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th colspan="5" style="text-align: center;">GOOD</th>
									</tr>
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
						<div class="col-md-7" style="padding-left: 0px;">
							<table id="middle-ng-log" class="table table-striped table-bordered" style="width: 100%;"> 
								<thead id="middle-ng-log-head" style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th colspan="6" style="text-align: center;">NOT GOOD</th>
									</tr>
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


	function showDetail(tgl, nama) {
		var data = {
			tgl:tgl,
			nama:nama,
		}

		$('#myModal').modal('show');
		$('#middle-log-body').append().empty();
		$('#middle-ng-log-body').append().empty();
		$('#middle-cek-body').append().empty();
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


				//Resume
				var ng_rate = total_ng / total_cek * 100;
				var text_ng_rate = '= <sup>Total NG</sup>/<sub>Total Cek</sub> x 100%';
				text_ng_rate += '<br>= <sup>'+ total_ng +'</sup>/<sub>'+ total_cek +'</sub> x 100%';
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
		var target = "{{$_GET['target']}}";

		if(target == ''){
			target = 15;
		}

		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

		var position = $(document).scrollTop();

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


			if(group.length == 1){
				for (var i = 0; i < group.length; i++) {
					$('#shift'+group[i].toLowerCase()).addClass("col-xs-12");
					$('#shift'+group[i].toLowerCase()).show();


					$('#shift'+group[i].toLowerCase()+'2').addClass("col-xs-12");
					$('#shift'+group[i].toLowerCase()+'2').show();
				}
			}else if(group.length == 2){
				for (var i = 0; i < group.length; i++) {
					$('#shift'+group[i].toLowerCase()).addClass("col-xs-6");
					$('#shift'+group[i].toLowerCase()).show();


					$('#shift'+group[i].toLowerCase()+'2').addClass("col-xs-6");
					$('#shift'+group[i].toLowerCase()+'2').show();
				}
			}else if(group.length == 3){
				for (var i = 0; i < group.length; i++) {
					$('#shift'+group[i].toLowerCase()).addClass("col-xs-4");
					$('#shift'+group[i].toLowerCase()).show();


					$('#shift'+group[i].toLowerCase()+'2').addClass("col-xs-4");
					$('#shift'+group[i].toLowerCase()+'2').show();
				}
			}

		}else{
			$('#shifta').addClass("col-xs-4");
			$('#shiftb').addClass("col-xs-4");
			$('#shiftc').addClass("col-xs-4");

			$('#shifta2').addClass("col-xs-4");
			$('#shiftb2').addClass("col-xs-4");
			$('#shiftc2').addClass("col-xs-4");
		}

		$.get('{{ url("fetch/middle/buffing_op_ng") }}', data, function(result, status, xhr){
			if(result.status){

				var date = result.date;

				// GROUP A
				var op_name = [];
				var rate = [];
				var data = [];
				var loop = 0;

				for(var i = 0; i < result.ng_rate.length; i++){
					if(result.ng_rate[i].shift == 'A'){
						loop += 1;
						var name_temp = result.ng_rate[i].name.split(" ");
						var xAxis = '';
						xAxis += result.ng_rate[i].operator_id + ' - ';

						if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Moch.' || name_temp[0] == 'Mochamad'){
							xAxis += name_temp[0].charAt(0)+'. '+name_temp[1];
						}else{
							xAxis += name_temp[0]+' '+name_temp[1].charAt(0);
						}

						op_name.push(xAxis);

						if(result.ng_rate[i].rate > 100){
							rate.push(100);						
						}else{
							rate.push(result.ng_rate[i].rate);						
						}

						if(rate[loop-1] > parseInt(target)){
							data.push({y: rate[loop-1], color: 'rgb(255,116,116)'})
						}else{
							data.push({y: rate[loop-1], color: 'rgb(144,238,126)'});
						}
					}
				}

				var chart = Highcharts.chart('container1_shifta', {
					chart: {
						animation: false
					},
					title: {
						text: 'Movement Average',
						style: {
							fontSize: '25px',
							fontWeight: 'bold'
						}
					},
					subtitle: {
						text: 'Group A on '+date,
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
					legend: {
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
						data: data
					}]
				});

				

				// GROUP B
				var op_name = [];
				var rate = [];
				var data = [];
				var loop = 0;

				for(var i = 0; i < result.ng_rate.length; i++){
					if(result.ng_rate[i].shift == 'B'){
						loop += 1;
						var name_temp = result.ng_rate[i].name.split(" ");
						var xAxis = '';
						xAxis += result.ng_rate[i].operator_id + ' - ';

						if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Moch.' || name_temp[0] == 'Mochamad'){
							xAxis += name_temp[0].charAt(0)+'. '+name_temp[1];
						}else{
							xAxis += name_temp[0]+' '+name_temp[1].charAt(0);
						}

						op_name.push(xAxis);

						if(result.ng_rate[i].rate > 100){
							rate.push(100);						
						}else{
							rate.push(result.ng_rate[i].rate);						
						}

						if(rate[loop-1] > parseInt(target)){
							data.push({y: rate[loop-1], color: 'rgb(255,116,116)'})
						}else{
							data.push({y: rate[loop-1], color: 'rgb(144,238,126)'});
						}
					}
				}

				var chart = Highcharts.chart('container1_shiftb', {
					chart: {
						animation: false
					},
					title: {
						text: 'Movement Average',
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
						title: {
							enabled: true,
							text: "Minutes"
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
					legend: {
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
						data: data
					}]
				});


				// GROUP C
				var op_name = [];
				var rate = [];
				var data = [];
				var loop = 0;

				for(var i = 0; i < result.ng_rate.length; i++){
					if(result.ng_rate[i].shift == 'C'){
						loop += 1;
						var name_temp = result.ng_rate[i].name.split(" ");
						var xAxis = '';
						xAxis += result.ng_rate[i].operator_id + ' - ';

						if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Moch.' || name_temp[0] == 'Mochamad'){
							xAxis += name_temp[0].charAt(0)+'. '+name_temp[1];
						}else{
							xAxis += name_temp[0]+' '+name_temp[1].charAt(0);
						}

						op_name.push(xAxis);

						if(result.ng_rate[i].rate > 100){
							rate.push(100);						
						}else{
							rate.push(result.ng_rate[i].rate);						
						}

						if(rate[loop-1] > parseInt(target)){
							data.push({y: rate[loop-1], color: 'rgb(255,116,116)'})
						}else{
							data.push({y: rate[loop-1], color: 'rgb(144,238,126)'});
						}
					}
				}

				var chart = Highcharts.chart('container1_shiftc', {
					chart: {
						animation: false
					},
					title: {
						text: 'Movement Average',
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
						title: {
							enabled: true,
							text: "Minutes"
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
					legend: {
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
						data: data
					}]
				});

				$(document).scrollTop(position);
			}
		});



$.get('{{ url("fetch/middle/buffing_op_ng_target") }}', data, function(result, status, xhr){

	if(result.status){
		var key = [];
		var buff_tarinai = [];
		var ng_soldering = [];
		var kizu = [];
		var others = [];
		var buff_nagare = [];

		var ng_rate = [];
		var ng = [];
		var qty = [];

		var plotBands = [];

		var loop = 0;
		for (var i = 0; i < result.operator.length; i++) {

			if(result.operator[i].group == 'A'){
				loop = loop + 1;

				buff_tarinai.push(0);
				ng_soldering.push(0);
				kizu.push(0);
				others.push(0);
				buff_nagare.push(0);

				for (var j = 0; j < result.target.length; j++) {
					if(result.operator[i].employee_id == result.target[j].employee_id){

						if(result.target[j].ng_name == 'Buff Tarinai'){
							buff_tarinai[loop-1] = result.target[j].quantity;
						}else if(result.target[j].ng_name == 'NG Soldering'){
							ng_soldering[loop-1] = result.target[j].quantity;
						}else if(result.target[j].ng_name == 'Kizu'){
							kizu[loop-1] = result.target[j].quantity;
						}else if(result.target[j].ng_name == 'Buff Others (Aus, Nami, dll)'){
							others[loop-1] = result.target[j].quantity;
						}else if(result.target[j].ng_name == 'Buff Nagare'){
							buff_nagare[loop-1] = result.target[j].quantity;
						}

						if(j == 0){
							key.push(result.target[j].key || 'Not Found');
						}else if(result.target[j].employee_id != result.target[j-1].employee_id){
							key.push(result.target[j].key || 'Not Found');
						}

					}

				}

				ng.push(buff_tarinai[loop-1] + ng_soldering[loop-1] + kizu[loop-1] + others[loop-1] + buff_nagare[loop-1]);

				if(key[loop-1] != 'Not Found'){
					if(key[loop-1] != 'A82Z'){
						if(key[loop-1][0] == 'A'){
							qty.push(15);
						}else if(key[loop-1][0] == 'T'){
							qty.push(8);
						}
					}else{
						qty.push(10);
					}
				}else{
					qty.push(0);
				}


				ng_rate.push(ng[loop-1] / qty[loop-1] * 100);

				if(ng_rate[loop-1] > parseInt(target)){
					plotBands.push({from: (loop - 1.5), to: (loop - 0.5), color: 'rgba(255, 116, 116, .5)'});
				}			

			}

		}

		var chart = Highcharts.chart('container2_shifta', {
			chart: {
				type: 'column',
			},
			title: {
				text: 'Last NG Rate By Operator Over '+target+'%',
				style: {
					fontSize: '25px',
					fontWeight: 'bold'
				}
			},
			subtitle: {
				text: 'Group A on '+result.date,
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
					enabled:false
				},
				stackLabels: {
					enabled: true,
					style: {
						fontWeight: 'bold',
						color: 'white',
						fontSize: '1vw'
					}
				},
			},
			xAxis: {
				categories: key,
				type: 'category',
				gridLineWidth: 1,
				gridLineColor: 'RGB(204,255,255)',
				labels: {
					rotation: -45,
					style: {
						fontSize: '13px'
					}
				},
				plotBands: plotBands
			},
			tooltip: {
				headerFormat: '<span>{point.category}</span><br/>',
				pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
			},
			credits: {
				enabled:false
			},
			plotOptions: {
				column: {
					stacking: 'normal',
				},
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
				}
			},
			series: [
			{
				name: 'Buff Tarinai',
				data: buff_tarinai,
				color: '#2b908f'
			},
			{
				name: 'NG Soldering',
				data: ng_soldering,
				color: '#90ee7e'
			},
			{
				name: 'Kizu',
				data: kizu,
				color: '#f45b5b'
			},
			{
				name: 'Buff Others (Aus, Nami, dll)',
				data: others,
				color: '#7798BF'
			},
			{
				name: 'BUff Nagare',
				data: buff_nagare,
				color: '#aaeeee'
			}
			]
		});






		var key = [];
		var buff_tarinai = [];
		var ng_soldering = [];
		var kizu = [];
		var others = [];
		var buff_nagare = [];

		var ng_rate = [];
		var ng = [];
		var qty = [];

		var plotBands = [];

		var loop = 0;
		for (var i = 0; i < result.operator.length; i++) {

			if(result.operator[i].group == 'B'){
				loop = loop + 1;

				buff_tarinai.push(0);
				ng_soldering.push(0);
				kizu.push(0);
				others.push(0);
				buff_nagare.push(0);

				for (var j = 0; j < result.target.length; j++) {
					if(result.operator[i].employee_id == result.target[j].employee_id){

						if(result.target[j].ng_name == 'Buff Tarinai'){
							buff_tarinai[loop-1] = result.target[j].quantity;
						}else if(result.target[j].ng_name == 'NG Soldering'){
							ng_soldering[loop-1] = result.target[j].quantity;
						}else if(result.target[j].ng_name == 'Kizu'){
							kizu[loop-1] = result.target[j].quantity;
						}else if(result.target[j].ng_name == 'Buff Others (Aus, Nami, dll)'){
							others[loop-1] = result.target[j].quantity;
						}else if(result.target[j].ng_name == 'Buff Nagare'){
							buff_nagare[loop-1] = result.target[j].quantity;
						}

						if(j == 0){
							key.push(result.target[j].key || 'Not Found');
						}else if(result.target[j].employee_id != result.target[j-1].employee_id){
							key.push(result.target[j].key || 'Not Found');
						}

					}

				}

				ng.push(buff_tarinai[loop-1] + ng_soldering[loop-1] + kizu[loop-1] + others[loop-1] + buff_nagare[loop-1]);

				if(key[loop-1] != 'Not Found'){
					if(key[loop-1] != 'A82Z'){
						if(key[loop-1][0] == 'A'){
							qty.push(15);
						}else if(key[loop-1][0] == 'T'){
							qty.push(8);
						}
					}else{
						qty.push(10);
					}
				}else{
					qty.push(0);
				}


				ng_rate.push(ng[loop-1] / qty[loop-1] * 100);

				if(ng_rate[loop-1] > parseInt(target)){
					plotBands.push({from: (loop - 1.5), to: (loop - 0.5), color: 'rgba(255, 116, 116, .5)'});
				}			

			}

		}


		var chart = Highcharts.chart('container2_shiftb', {
			chart: {
				type: 'column',
			},
			title: {
				text: 'Last NG Rate By Operator Over '+target+'%',
				style: {
					fontSize: '25px',
					fontWeight: 'bold'
				}
			},
			subtitle: {
				text: 'Group B on '+result.date,
				style: {
					fontSize: '1vw',
					fontWeight: 'bold'
				},
			},
			yAxis: {
				title: {
					enabled: true,
					text: "PC(s)"
				},
				labels: {
					enabled:false
				},
				stackLabels: {
					enabled: true,
					style: {
						fontWeight: 'bold',
						color: 'white',
						fontSize: '1vw'
					}
				},
			},
			xAxis: {
				categories: key,
				type: 'category',
				gridLineWidth: 1,
				gridLineColor: 'RGB(204,255,255)',
				labels: {
					rotation: -45,
					style: {
						fontSize: '13px'
					}
				},
				plotBands: plotBands
			},
			tooltip: {
				headerFormat: '<span>{point.category}</span><br/>',
				pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
			},
			credits: {
				enabled:false
			},
			plotOptions: {
				column: {
					stacking: 'normal',
				},
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
				}
			},
			series: [
			{
				name: 'Buff Tarinai',
				data: buff_tarinai,
				color: '#2b908f'
			},
			{
				name: 'NG Soldering',
				data: ng_soldering,
				color: '#90ee7e'
			},
			{
				name: 'Kizu',
				data: kizu,
				color: '#f45b5b'
			},
			{
				name: 'Buff Others (Aus, Nami, dll)',
				data: others,
				color: '#7798BF'
			},
			{
				name: 'BUff Nagare',
				data: buff_nagare,
				color: '#aaeeee'
			}
			]
		});





		var key = [];
		var buff_tarinai = [];
		var ng_soldering = [];
		var kizu = [];
		var others = [];
		var buff_nagare = [];

		var ng_rate = [];
		var ng = [];
		var qty = [];

		var plotBands = [];

		var loop = 0;
		for (var i = 0; i < result.operator.length; i++) {

			if(result.operator[i].group == 'C'){
				loop = loop + 1;

				buff_tarinai.push(0);
				ng_soldering.push(0);
				kizu.push(0);
				others.push(0);
				buff_nagare.push(0);

				for (var j = 0; j < result.target.length; j++) {
					if(result.operator[i].employee_id == result.target[j].employee_id){

						if(result.target[j].ng_name == 'Buff Tarinai'){
							buff_tarinai[loop-1] = result.target[j].quantity;
						}else if(result.target[j].ng_name == 'NG Soldering'){
							ng_soldering[loop-1] = result.target[j].quantity;
						}else if(result.target[j].ng_name == 'Kizu'){
							kizu[loop-1] = result.target[j].quantity;
						}else if(result.target[j].ng_name == 'Buff Others (Aus, Nami, dll)'){
							others[loop-1] = result.target[j].quantity;
						}else if(result.target[j].ng_name == 'Buff Nagare'){
							buff_nagare[loop-1] = result.target[j].quantity;
						}

						if(j == 0){
							key.push(result.target[j].key || 'Not Found');
						}else if(result.target[j].employee_id != result.target[j-1].employee_id){
							key.push(result.target[j].key || 'Not Found');
						}

					}

				}

				ng.push(buff_tarinai[loop-1] + ng_soldering[loop-1] + kizu[loop-1] + others[loop-1] + buff_nagare[loop-1]);

				if(key[loop-1] != 'Not Found'){
					if(key[loop-1] != 'A82Z'){
						if(key[loop-1][0] == 'A'){
							qty.push(15);
						}else if(key[loop-1][0] == 'T'){
							qty.push(8);
						}
					}else{
						qty.push(10);
					}
				}else{
					qty.push(0);
				}


				ng_rate.push(ng[loop-1] / qty[loop-1] * 100);

				if(ng_rate[loop-1] > parseInt(target)){
					plotBands.push({from: (loop - 1.5), to: (loop - 0.5), color: 'rgba(255, 116, 116, .5)'});
				}			

			}

		}


		var chart = Highcharts.chart('container2_shiftc', {
			chart: {
				type: 'column',
			},
			title: {
				text: 'Last NG Rate By Operator Over '+target+'%',
				style: {
					fontSize: '25px',
					fontWeight: 'bold'
				}
			},
			subtitle: {
				text: 'Group C on '+result.date,
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
					enabled:false
				},
				stackLabels: {
					enabled: true,
					style: {
						fontWeight: 'bold',
						color: 'white',
						fontSize: '1vw'
					}
				},
			},
			xAxis: {
				categories: key,
				type: 'category',
				gridLineWidth: 1,
				gridLineColor: 'RGB(204,255,255)',
				labels: {
					rotation: -45,
					style: {
						fontSize: '13px'
					}
				},
				plotBands: plotBands
			},
			tooltip: {
				headerFormat: '<span>{point.category}</span><br/>',
				pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
			},
			credits: {
				enabled:false
			},
			plotOptions: {
				column: {
					stacking: 'normal',
				},
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
				}
			},
			series: [
			{
				name: 'Buff Tarinai',
				data: buff_tarinai,
				color: '#2b908f'
			},
			{
				name: 'NG Soldering',
				data: ng_soldering,
				color: '#90ee7e'
			},
			{
				name: 'Kizu',
				data: kizu,
				color: '#f45b5b'
			},
			{
				name: 'Buff Others (Aus, Nami, dll)',
				data: others,
				color: '#7798BF'
			},
			{
				name: 'BUff Nagare',
				data: buff_nagare,
				color: '#aaeeee'
			}
			]
		});

		$(document).scrollTop(position);	
	}

});



$.get('{{ url("fetch/middle/buffing_hourly_ng") }}', data, function(result, status, xhr) {
	if(result.status){

		for (var h = 0; h < 12; h++) {
			var operator = [];

			var buff_tarinai = [];
			var ng_soldering = [];
			var kizu = [];
			var others = [];
			var buff_nagare = [];

			for (var i = 0; i < result.ng[h].length; i++) {
				var name_temp = result.ng[h][i].name.split(" ");

				if(name_temp[0] == 'M.' || name_temp[0] == 'Muhammad' || name_temp[0] == 'Muhamad' || name_temp[0] == 'Mokhammad' || name_temp[0] == 'Mokhamad' || name_temp[0] == 'Mukhammad' || name_temp[0] == 'Mochammad' || name_temp[0] == 'Akhmad' || name_temp[0] == 'Achmad' || name_temp[0] == 'Moh.' || name_temp[0] == 'Mochamad'){
					operator.push(name_temp[0].charAt(0)+'. '+name_temp[1]);
				}else{
					if(name_temp[1].length > 7){
						operator.push(name_temp[0]+'. '+name_temp[1].charAt(0));
					}else{
						operator.push(result.ng[h][i].name);
					}
				}

				buff_tarinai.push(0);
				ng_soldering.push(0);
				kizu.push(0);
				others.push(0);
				buff_nagare.push(0);

				for (var j = 0; j < result.detail[h].length; j++) {
					if(result.ng[h][i].operator_id == result.detail[h][j].operator_id){
						if(result.detail[h][j].ng_name == 'Buff Tarinai'){
							buff_tarinai[i] = result.detail[h][j].qty / result.ng[h][i].tot * 100;
						}else if(result.detail[h][j].ng_name == 'NG Soldering'){
							ng_soldering[i] = result.detail[h][j].qty / result.ng[h][i].tot * 100;
						}else if(result.detail[h][j].ng_name == 'Kizu'){
							kizu[i] = result.detail[h][j].qty / result.ng[h][i].tot * 100;
						}else if(result.detail[h][j].ng_name == 'Buff Others (Aus, Nami, dll)'){
							others[i] = result.detail[h][j].qty / result.ng[h][i].tot * 100;
						}else if(result.detail[h][j].ng_name == 'Buff Nagare'){
							buff_nagare[i] = result.detail[h][j].qty / result.ng[h][i].tot * 100;
						}
					}
				}
			}

			Highcharts.chart('time'+h, {
				chart: {
					type: 'column',
					backgroundColor: null,
				},
				title: {
					text: '',
					style: {
						display: 'none'
					}
				},
				xAxis: {
					categories: operator,
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
				yAxis: {
					title: {
						text: ''
					},
					labels: {
						enabled:false
					},
					stackLabels: {
						enabled: true,
						formatter: function () {
							return this.total.toFixed(2) + "%";
						},
						// rotation: -90,
						style: {
							fontWeight: 'bold',
							color: 'white',
							fontSize: '12px'
						}
					},
				},
				exporting: {
					enabled: false 
				},
				legend: {
					enabled:false
				},
				tooltip: {
					headerFormat: '<span>{point.category}</span><br/>',
					pointFormat: '<span　style="font-weight: bold;">{point.category}</span><br/><span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y:.2f}%</b> <br/>',
				},
				plotOptions: {
					column: {
						stacking: 'normal',
					},
					series:{
						dataLabels: {
							enabled: true,
							format: '{point.y:.2f}%',
							rotation: -90,
							color: '#212121',
							style:{
								fontSize: '12px'
							}
						},
						animation: false,
						pointPadding: 0.93,
						groupPadding: 0.93,
						borderWidth: 0.93,
						cursor: 'pointer'
					}
				},credits: {
					enabled: false
				},
				series: [
				{
					name: 'Buff Tarinai',
					data: buff_tarinai,
					color: '#2b908f'
				},
				{
					name: 'NG Soldering',
					data: ng_soldering,
					color: '#90ee7e'
				},
				{
					name: 'Kizu',
					data: kizu,
					color: '#f45b5b'
				},
				{
					name: 'Buff Others (Aus, Nami, dll)',
					data: others,
					color: '#7798BF'
				},
				{
					name: 'BUff Nagare',
					data: buff_nagare,
					color: '#aaeeee'
				}
				]
			});

		}

		$(document).scrollTop(position);

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
	return year + "-" + month + "-" + day + " (" + h + ":" + m + ":" + s +")";
}


}

</script>
@endsection