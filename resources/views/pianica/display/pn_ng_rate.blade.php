@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
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
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0px;">
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<div class="row" style="margin:0px;">
					<form method="GET" action="{{ action('Pianica@indexNgRate') }}">
						<div class="col-xs-2">
							<div class="input-group date">
								<div class="input-group-addon bg-green" style="border: none;">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control datepicker" name="tanggal" id="tanggal" placeholder="Select Date">
							</div>
						</div>
						<div class="col-xs-2" style="color: black; text-transform: capitalize;">
							<div class="form-group">
								<select class="form-control select2" id='locationSelect' onchange="change()" data-placeholder="Select Location" style="width: 100%;">
									<option value="">Select Location</option>
									<option value="welding">Welding Spot</option>
									<option value="bentsuki-benage">Bentsuki Benage</option>
									<option value="kensa-awal">Kensa Awal</option>
								</select>
								<input type="text" name="location" id="location" hidden>			
							</div>
						</div>
						<div class="col-xs-1">
							<button class="btn btn-success" type="submit">Update Chart</button>
						</div>
					</form>
					<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12" style="margin-bottom: 1%;">
			<div id="spot-welding">
				<div id="chart1"></div>
			</div>				
		</div>
		<div class="col-xs-12" style="margin-bottom: 1%;">
			<div id="bentsuki-benage">
				<div id="chart2"></div>
			</div>				
		</div>
		<div class="col-xs-12" style="margin-bottom: 1%;">
			<div id="bentsuki-benage">
				<div id="tuning"></div>
			</div>				
		</div>
		<div class="col-xs-12" style="margin-bottom: 1%;">
			<div id="kensa-awal">
				<div id="chart3"></div>
			</div>				
		</div>
	</div>

	<div class="modal fade" id="modalProgress">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="modalProgressTitle" ></h4>
					<h4 class="modal-title" id="modalProgressTitle2"></h4>
					<h4 class="modal-title" id="modalProgressTitle3"></h4>
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<center>
							<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
						</center>
						<table class="table table-hover table-bordered table-striped" id="tableModal">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Reed</th>
									<th>Total NG</th>									              
								</tr>
							</thead>
							<tbody id="modalProgressBody">
							</tbody>
							<tfoot style="background-color: RGB(252, 248, 227);">
								<th >Total</th>
								<th id="totalP"></th>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalProgress2">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="modalProgressTitle22"></h4>
					<h4 class="modal-title" id="modalProgressTitle32"></h4>
					<div class="modal-body table-responsive no-padding" style="min-height: 100px">
						<center>
							<i class="fa fa-spinner fa-spin" id="loading2" style="font-size: 80px;"></i>
						</center>
						<table class="table table-hover table-bordered table-striped" id="tableModal2">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Model</th>
									<th>NG</th>		
									<th>Posisi</th>	
									<th>Mesin</th>								              
								</tr>
							</thead>
							<tbody id="modalProgressBody2">
							</tbody>
						<!-- 	<tfoot style="background-color: RGB(252, 248, 227);">
								<th >Total</th>
								<th id="totalP2"></th>
							</tfoot> -->
						</table>
					</div>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('#date').datepicker({
			autoclose: true
		});
		$('.select2').select2({
		});

		fillChart();
		setInterval(fillChart, 60000);
	});


	function change() {
		$("#location").val($("#locationSelect").val());
	}

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function fillChart(){
		var data = {
			tanggal:"{{$_GET['tanggal']}}"			
		}

		var location = "{{$_GET['location']}}";

		$('#spot-welding').hide();
		$('#bentsuki-benage').hide();
		$('#kensa-awal').hide();


		if(location == ''){
			$('#spot-welding').show();
			$('#bentsuki-benage').show();
			$('#kensa-awal').show();
		}else if(location == 'welding'){
			$('#spot-welding').show();
		}else if(location == 'bentsuki-benage'){
			$('#bentsuki-benage').show();
		}else if(location == 'kensa-awal'){
			$('#kensa-awal').show();
		}

		$.get('{{ url("fetch/pianica/ng_spot_welding") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					var op = [];
					var ng = [];

					for (var i = 0; i < result.ng.length; i++) {
						op.push(result.ng[i].nama);
						ng.push(result.ng[i].qty);
					}

					Highcharts.chart('chart1', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'NG Spot Welding',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'On '+result.date,
							style: {
								fontSize: '18px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: op,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '26px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Total NG'
							},
						},
						legend : {
							enabled: false
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span>{point.category}</span><br/><span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b> <br/>',

						},
						plotOptions: {
							series:{
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										textOutline: false,
										fontSize: '26px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer',
							},
						},credits: {
							enabled: false
						},
						series: [
						{
							name: 'NG Rate',
							data: ng,
							colorByPoint: true,
							point: {
								events: {
									click: function () {
										fillModalSpotWelding(this.category , result.date);
									}
								}
							}

						}
						]
					});

				}
			}
		});


		$.get('{{ url("fetch/pianica/ng_bentsuki_benage") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					var op = [];
					var ng = [];
					var sum = [];
					var data = [];

					var biri = [];
					var oktaf = [];
					var t_rendah = [];
					var t_tinggi = [];

					for (var i = 0; i < result.op.length; i++) {
						op.push(result.op[i].nama);
						biri.push(0);
						oktaf.push(0);
						t_rendah.push(0);
						t_tinggi.push(0);

						for (var j = 0; j < result.ng.length; j++) {
							if(result.op[i].operator == result.ng[j].operator){
								if(result.ng[j].ng_name == 'Biri'){
									biri[i] = parseInt(result.ng[j].jml);
								}else if(result.ng[j].ng_name == 'Oktaf'){
									oktaf[i] = parseInt(result.ng[j].jml);
								}else if(result.ng[j].ng_name == 'T. Rendah'){
									t_rendah[i] = parseInt(result.ng[j].jml);
								}else if(result.ng[j].ng_name == 'T. Tinggi'){
									t_tinggi[i] = parseInt(result.ng[j].jml);
								}
							}
						}

						sum.push(biri[i]  + t_rendah[i] + t_tinggi[i]);
						// sum.push(biri[i] + oktaf[i] + t_rendah[i] + t_tinggi[i]);
						data.push({nama: op[i], biri: biri[i], oktaf: oktaf[i], t_rendah: t_rendah[i], t_tinggi: t_tinggi[i], sum: sum[i]});

					}

					data.sort((a, b) => b.sum - a.sum);


					var op = [];
					var biri = [];
					var oktaf = [];
					var t_rendah = [];
					var t_tinggi = [];
					for (var i = 0; i < data.length; i++) {
						op.push(data[i].nama);
						biri.push(data[i].biri);
						oktaf.push(data[i].oktaf);
						t_rendah.push(data[i].t_rendah);
						t_tinggi.push(data[i].t_tinggi);
					}

					Highcharts.chart('chart2', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'NG Bentsuki - Benage',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'On '+result.date,
							style: {
								fontSize: '18px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: op,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '18px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Total NG'
							},
							stackLabels: {
								enabled: true,
								style: {
									fontWeight: 'bold',
									color: 'white',
									fontSize: '2vw'
								}
							},
						},
						legend : {
							enabled: true
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						plotOptions: {
							column: {
								stacking: 'normal',
							},
							series:{
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
							name: 'Biri',
							data: biri,
							color: '#e88113',
							point: {
								events: {
									click: function () {
										fillModalTuning(this.category , 101 , result.date,"Bentsuki");
									}
								}
							}
						},
						// {
						// 	name: 'Oktaf',
						// 	data: oktaf,
						// 	color: '#90ee7e'
						// },
						{
							name: 'T. Tinggi',
							data: t_tinggi,
							color: '#f45b5b',
							point: {
								events: {
									click: function () {
										fillModalTuning(this.category , 103 , result.date,"Bentsuki");
									}
								}
							}
						},
						{
							name: 'T. Rendah',
							data: t_rendah,
							color: '#7798BF',
							point: {
								events: {
									click: function () {
										fillModalTuning(this.category , 104 , result.date,"Bentsuki");
									}
								}
							}
						}
						]
					});

				}
			}
		});

		$.get('{{ url("fetch/pianica/ng_tuning") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					var op = [];
					var ng = [];
					var sum = [];
					var data = [];

					var biri = [];
					var oktaf = [];
					var t_rendah = [];
					var t_tinggi = [];

					for (var i = 0; i < result.op.length; i++) {
						op.push(result.op[i].nama);
						biri.push(0);
						oktaf.push(0);
						t_rendah.push(0);
						t_tinggi.push(0);

						for (var j = 0; j < result.ng.length; j++) {
							if(result.op[i].nik == result.ng[j].tuning){
								if(result.ng[j].ng_name == 'Biri'){
									biri[i] = parseInt(result.ng[j].total);
								}else if(result.ng[j].ng_name == 'Oktaf'){
									oktaf[i] = parseInt(result.ng[j].total);
								}else if(result.ng[j].ng_name == 'T. Rendah'){
									t_rendah[i] = parseInt(result.ng[j].total);
								}else if(result.ng[j].ng_name == 'T. Tinggi'){
									t_tinggi[i] = parseInt(result.ng[j].total);
								}
							}
						}

						// sum.push(biri[i] + oktaf[i] + t_rendah[i] + t_tinggi[i]);
						sum.push( oktaf[i] + t_rendah[i] + t_tinggi[i]);
						data.push({nama: op[i], biri: biri[i], oktaf: oktaf[i], t_rendah: t_rendah[i], t_tinggi: t_tinggi[i], sum: sum[i]});

					}

					data.sort((a, b) => b.sum - a.sum);


					var op = [];
					var biri = [];
					var oktaf = [];
					var t_rendah = [];
					var t_tinggi = [];
					for (var i = 0; i < data.length; i++) {
						op.push(data[i].nama);
						biri.push(data[i].biri);
						oktaf.push(data[i].oktaf);
						t_rendah.push(data[i].t_rendah);
						t_tinggi.push(data[i].t_tinggi);
					}

					Highcharts.chart('tuning', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'NG Tuning',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'On '+result.date,
							style: {
								fontSize: '18px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: op,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '18px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Total NG'
							},
							stackLabels: {
								enabled: true,
								style: {
									fontWeight: 'bold',
									color: 'white',
									fontSize: '2vw'
								}
							},
						},
						legend : {
							enabled: true
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						plotOptions: {
							column: {
								stacking: 'normal',
							},
							series:{
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
						// {
						// 	name: 'Biri',
						// 	data: biri,
						// 	color: '#e88113'
						// },
						{
							name: 'Oktaf',
							data: oktaf,
							color: '#90ee7e',
							point: {
								events: {
									click: function () {
										fillModalTuning(this.category , 102 , result.date, "Tuning");
									}
								}
							}
						},
						{
							name: 'T. Tinggi',
							data: t_tinggi,
							color: '#f45b5b',
							point: {
								events: {
									click: function () {
										fillModalTuning(this.category , 103 , result.date, "Tuning");
									}
								}
							}
						},
						{
							name: 'T. Rendah',
							data: t_rendah,
							color: '#7798BF',
							point: {
								events: {
									click: function () {
										fillModalTuning(this.category , 104 , result.date, "Tuning");
									}
								}
							}
						}
						]
					});

				}
			}
		});


		$.get('{{ url("fetch/pianica/ng_kensa_awal") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					var op = [];
					var ng = [];
					var sum = [];
					var data = [];

					var biri = [];
					var oktaf = [];
					var t_rendah = [];
					var t_tinggi = [];

					for (var i = 0; i < result.op.length; i++) {
						op.push(result.op[i].nama);
						biri.push(0);
						oktaf.push(0);
						t_rendah.push(0);
						t_tinggi.push(0);

						for (var j = 0; j < result.ng.length; j++) {
							if(result.op[i].operator == result.ng[j].operator){
								if(result.ng[j].ng_name == 'Biri'){
									biri[i] = parseInt(result.ng[j].jml);
								}else if(result.ng[j].ng_name == 'Oktaf'){
									oktaf[i] = parseInt(result.ng[j].jml);
								}else if(result.ng[j].ng_name == 'T. Rendah'){
									t_rendah[i] = parseInt(result.ng[j].jml);
								}else if(result.ng[j].ng_name == 'T. Tinggi'){
									t_tinggi[i] = parseInt(result.ng[j].jml);
								}
							}
						}

						sum.push(biri[i] + oktaf[i] + t_rendah[i] + t_tinggi[i]);
						data.push({nama: op[i], biri: biri[i], oktaf: oktaf[i], t_rendah: t_rendah[i], t_tinggi: t_tinggi[i], sum: sum[i]});

					}

					data.sort((a, b) => b.sum - a.sum);


					var op = [];
					var biri = [];
					var oktaf = [];
					var t_rendah = [];
					var t_tinggi = [];
					for (var i = 0; i < data.length; i++) {
						op.push(data[i].nama);
						biri.push(data[i].biri);
						oktaf.push(data[i].oktaf);
						t_rendah.push(data[i].t_rendah);
						t_tinggi.push(data[i].t_tinggi);
					}

					Highcharts.chart('chart3', {
						chart: {
							type: 'column'
						},
						title: {
							text: 'NG Kensa Awal',
							style: {
								fontSize: '30px',
								fontWeight: 'bold'
							}
						},
						subtitle: {
							text: 'On '+result.date,
							style: {
								fontSize: '18px',
								fontWeight: 'bold'
							}
						},
						xAxis: {
							categories: op,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '26px'
								}
							},
						},
						yAxis: {
							title: {
								text: 'Total NG'
							},
							stackLabels: {
								enabled: true,
								style: {
									fontWeight: 'bold',
									color: 'white',
									fontSize: '2vw'
								}
							},
						},
						legend : {
							enabled: true
						},
						tooltip: {
							headerFormat: '<span>{point.category}</span><br/>',
							pointFormat: '<span　style="color:{point.color};font-weight: bold;">{point.category}</span><br/><span>{series.name} </span>: <b>{point.y}</b> <br/>',
						},
						plotOptions: {
							column: {
								stacking: 'normal',
							},
							series:{
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
							name: 'Biri',
							data: biri,
							color: '#2b908f',
							point: {
								events: {
									click: function () {
										fillModalTuning(this.category , 105 , result.date,"Awal");
									}
								}
							}
						},
						{
							name: 'Oktaf',
							data: oktaf,
							color: '#90ee7e',
							point: {
								events: {
									click: function () {
										fillModalTuning(this.category , 106 , result.date,"Awal");
									}
								}
							}
						},
						{
							name: 'T. Tinggi',
							data: t_tinggi,
							color: '#f45b5b',
							point: {
								events: {
									click: function () {
										fillModalTuning(this.category , 107 , result.date,"Awal");
									}
								}
							}
						},
						{
							name: 'T. Rendah',
							data: t_rendah,
							color: '#7798BF',
							point: {
								events: {
									click: function () {
										fillModalTuning(this.category , 108 , result.date,"Awal");
									}
								}
							}
						}
						]
					});

				}
			}

		});

$.get('{{ url("fetch/pianica/totalNgReed") }}',data, function(result, status, xhr) {
	if(xhr.status == 200){
		if(result.status){


			var Biri = [];
			var Oktaf = [];
			var Tinggi = [];
			var Rendah = [];


			Biri.push(result.ngTotal[0].s1);
			Biri.push(result.ngTotal[0].s2);
			Biri.push(result.ngTotal[0].s3);
			Biri.push(result.ngTotal[0].s4);
			Biri.push(result.ngTotal[0].s5);
			Biri.push(result.ngTotal[0].s6);
			Biri.push(result.ngTotal[0].s7);
			Biri.push(result.ngTotal[0].s8);
			Biri.push(result.ngTotal[0].s9);
			Biri.push(result.ngTotal[0].s10);
			Biri.push(result.ngTotal[0].s11);
			Biri.push(result.ngTotal[0].s12);


			console.log(Biri)

					// Highcharts.chart('chart22', {
					// 	chart: {
					// 		type: 'column'
					// 	},
					// 	title: {
					// 		text: 'NG Spot Welding',
					// 		style: {
					// 			fontSize: '30px',
					// 			fontWeight: 'bold'
					// 		}
					// 	},
					// 	subtitle: {
					// 		text: 'On '+result.date,
					// 		style: {
					// 			fontSize: '18px',
					// 			fontWeight: 'bold'
					// 		}
					// 	},
					// 	xAxis: {
					// 		categories: op,
					// 		type: 'category',
					// 		gridLineWidth: 1,
					// 		gridLineColor: 'RGB(204,255,255)',
					// 		labels: {
					// 			style: {
					// 				fontSize: '26px'
					// 			}
					// 		},
					// 	},
					// 	yAxis: {
					// 		title: {
					// 			text: 'Total NG'
					// 		},
					// 	},
					// 	legend : {
					// 		enabled: false
					// 	},
					// 	tooltip: {
					// 		headerFormat: '<span>{point.category}</span><br/>',
					// 		pointFormat: '<span>{point.category}</span><br/><span style="color:{point.color};font-weight: bold;">{series.name} </span>: <b>{point.y}</b> <br/>',

					// 	},
					// 	plotOptions: {
					// 		series:{
					// 			dataLabels: {
					// 				enabled: true,
					// 				format: '{point.y}',
					// 				style:{
					// 					textOutline: false,
					// 					fontSize: '26px'
					// 				}
					// 			},
					// 			animation: false,
					// 			pointPadding: 0.93,
					// 			groupPadding: 0.93,
					// 			borderWidth: 0.93,
					// 			cursor: 'pointer',
					// 		},
					// 	},credits: {
					// 		enabled: false
					// 	},
					// 	series: [
					// 	{
					// 		name: 'NG Rate',
					// 		data: ng,
					// 		colorByPoint: true,
					// 	}
					// 	]
					// });

				}
			}
		});



}

function fillModal(cat, name){

}

function addZero(i) {
	if (i < 10) {
		i = "0" + i;
	}
	return i;
}

function getActualFullDate(){
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

function fillModalTuning(nama ,ng, tgl, procescode){
	$('#modalProgress').modal('show');
	$('#loading').show();
	// $('#modalProgressTitle').hide();
	$('#tableModal').hide();

	var data = {
		procescode:procescode,
		nama:nama,
		ng:ng,
		tgl:tgl
	}
	$.get('{{ url("fetch/pianica/detailReedTuning") }}', data, function(result, status, xhr){
		if(result.status){

			$('#modalProgressBody').html('');
			var resultData = '';
			var total = 0;

			var	allng = [];


			for (var i = 0; i < result.ngTotal.length; i++) {
				var m = "";
				var a = result.ngTotal[i].reed;
				if (result.ngTotal[i].reed.match(/,.*/)) {
					m = a.split(',');

					for (var y = 0; y < m.length; y++) {
						allng.push(m[y])
					}

				} else{

					allng.push(result.ngTotal[i].reed)
				}
			}

			var  count = {};
			allng.forEach(function(i) { count[i] = (count[i]||0) + 1;});
			console.log(count);
			var resultReed = Object.keys(count).map(function(key) {
				return [Number(key), count[key]];
			});

			console.log( resultReed);
			resultReed.sort(function(a, b){return b[1] - a[1]});
			// console.log( resultReed);
			for (var i = 0; i < resultReed.length; i++) {					

				resultData += '<tr >';
				resultData += '<td style="width: 40%; font-size:16px">'+ resultReed[i][0] +'</td>';
				resultData += '<td style="width: 40%; font-size:16px">'+ resultReed[i][1] +'</td>';
				resultData += '</tr>';  
				total += resultReed[i][1];
			}   


			$('#loading').hide();
			$('#modalProgressBody').append(resultData);
			$('#totalP').text(total);

			if (ng =="101") {
				$('#modalProgressTitle2').text("Detail NG Bentsuki - Biri");
			} 

			if (ng =="102") {
				$('#modalProgressTitle2').text("Detail NG Tuning - Oktaf");
			} 

			if (ng =="103" && procescode=="Tuning") {
				$('#modalProgressTitle2').text("Detail NG Tuning - T. Tinggi");
			}

			if (ng =="104" && procescode=="Tuning") {
				$('#modalProgressTitle2').text("Detail NG Tuning - T. Rendah");
			}

			if (ng =="103" && procescode=="Bentsuki") {
				$('#modalProgressTitle2').text("Detail NG Bentsuki - T. Tinggi");
			}

			if (ng =="104" && procescode=="Bentsuki") {
				$('#modalProgressTitle2').text("Detail NG Bentsuki - T. Rendah");
			}

			if (ng =="105") {
				$('#modalProgressTitle2').text("Detail NG Kensa Awal - Biri");
			}

			if (ng =="106") {
				$('#modalProgressTitle2').text("Detail NG Kensa Awal - Oktaf");
			}

			if (ng =="107") {
				$('#modalProgressTitle2').text("Detail NG Kensa Awal - T. Tinggi");
			}

			if (ng =="108") {
				$('#modalProgressTitle2').text("Detail NG Kensa Awal - T. Rendah");
			}



			$('#modalProgressTitle3').text(nama+' On '+tgl);


        // $('#modalProgressTitle').show();
        $('#tableModal').show();
    }
    else{
    	alert('Attempt to retrieve data failed');
    }
});
}

function fillModalSpotWelding(nama , tgl){
	$('#modalProgress2').modal('show');
	$('#loading2').show();
	// $('#modalProgressTitle22').hide();
	$('#tableModal2').hide();

	var data = {
		nama:nama,
		tgl:tgl
	}
	$.get('{{ url("fetch/pianica/totalNgReedSpotWelding") }}', data, function(result, status, xhr){
		if(result.status){

			$('#modalProgressBody2').html('');
			var resultData = '';
			var total = 0;

			var	allng = [];		


			for (var i = 0; i < result.ngTotal.length; i++) {					

				resultData += '<tr >';

				resultData += '<td style="width: 40%; font-size:16px">'+ result.ngTotal[i].model  +'</td>';
				resultData += '<td style="width: 40%; font-size:16px">'+ result.ngTotal[i].ng +'</td>';				
				resultData += '<td style="width: 40%; font-size:16px">'+ result.ngTotal[i].posisi  +'</td>';
				resultData += '<td style="width: 40%; font-size:16px">'+ result.ngTotal[i].mesin  +'</td>';
				resultData += '</tr>';  
			}   


			$('#loading2').hide();
			$('#modalProgressBody2').append(resultData);
			$('#totalP2').text(total);

			$('#modalProgressTitle22').text("Detail NG Spot Welding");
			
			$('#modalProgressTitle32').text(nama+' On '+tgl);


        // $('#modalProgressTitle').show();
        $('#tableModal2').show();
    }
    else{
    	alert('Attempt to retrieve data failed');
    }
});
}

</script>
@endsection