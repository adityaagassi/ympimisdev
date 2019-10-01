@extends('layouts.visitor')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
	h2{
		font-size: 70px;
		font-weight: bold;
	}
	
</style>
@stop
@section('header')
<section class="content-header" style="text-align: center;">

</section>
@stop
@section('content')
<section class="content" style="padding-top: 0px;">
	

	<div class="row" style="margin-bottom: 1%;">
		<div class="col-xs-3">
			<div class="input-group date">
				<div class="input-group-addon bg-olive" style="border: none;">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control pull-right" id="datepicker" name="datepicker" placeholder="Select date">
			</div>
		</div>
		<div class="col-xs-2">
			<button id="search" onClick="drawChart()" class="btn bg-olive">Search</button>
		</div>
		{{-- <div class="col-xs-3 pull-right">
			<p class="pull-right" id="last_update"></p>
		</div> --}}
	</div>
	

	<div class="row">
		<div class="col-lg-3 col-xs-12" style="margin-left: 0px;">
			<div class="col-lg-12 col-xs-12" style="margin-left: 0px; padding: 0px;">
				<!-- small box -->
				<div class="small-box bg-yellow" style="font-size: 30px;font-weight: bold;height: 188px;">
					<div class="inner" style="padding-bottom: 0px;">
						<h3 style="margin-bottom: 0px;"><b>TARIK</b></h3>
						<h2 style="margin: 0px;font-size: 4vw;" id='tarik'>0<sup style="font-size: 2vw">set</sup></h2>
					</div>
					<div class="icon">
						<i class="ion ion-stats-bars"></i>
					</div>
				</div>
			</div>
			<div class="col-lg-12 col-xs-12" style="margin-left: 0px; padding: 0px;">
				<!-- small box -->
				<div class="small-box bg-red" style="font-size: 30px;font-weight: bold;height: 188px;">
					<div class="inner" style="padding-bottom: 0px;">
						<h3 style="margin-bottom: 0px;"><b>SEDANG REPAIR</b></h3>
						<h2 style="margin: 0px;font-size: 4vw;" id='sedang'>0<sup style="font-size: 2vw">set</sup></h2>
					</div>
					<div class="icon">
						<i class="ion ion-stats-bars"></i>
					</div>
					<a href="{{ url("index/flute_repair/tarik") }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
				</div>
			</div>
			<div class="col-lg-12 col-xs-12" style="margin-left: 0px; padding: 0px;">
				<!-- small box -->
				<div class="small-box bg-blue" style="font-size: 30px;font-weight: bold;height: 188px;">
					<div class="inner" style="padding-bottom: 0px;">
						<h3 style="margin-bottom: 0px;"><b>SELESAI</b></h3>
						<h2 style="margin: 0px; font-size: 4vw;" id='selesai'>0<sup style="font-size: 2vw">set</sup></h2>
					</div>
					<div class="icon">
						<i class="ion ion-stats-bars"></i>
					</div>
					<a href="{{ url("index/flute_repair/selesai") }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
				</div>
			</div>
			<div class="col-lg-12 col-xs-12" style="margin-left: 0px; padding: 0px;">
				<!-- small box -->
				<div class="small-box bg-green" style="font-size: 30px;font-weight: bold;height: 188px;">
					<div class="inner" style="padding-bottom: 0px;">
						<h3 style="margin-bottom: 0px;"><b>KEMBALI</b></h3>
						<h2 style="margin: 0px; font-size: 4vw;" id='kembali'>0<sup style="font-size: 2vw">set</sup></h2>
					</div>
					<div class="icon">
						<i class="ion ion-stats-bars"></i>
					</div>
					<a href="{{ url("index/flute_repair/kembali") }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
				</div>
			</div>
		</div>
		<div class="col-lg-9" style="margin-left: 0px; padding: 0px;">
			<div class="col-lg-12" style="margin-bottom: 1%;">
				<div id="container1" style="width: 100%;"></div>
			</div>
			<div class="col-lg-12" style="margin-bottom: 1%;">
				<div id="container2" style="width: 100%;"></div>
			</div>  
		</div>
	</div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
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
		$('#datepicker').datepicker({
			autoclose: true,
			todayHighlight: true,
			format: "dd-mm-yyyy"
		});
		$('#last_update').html('<i class="fa fa-clock-o"></i> Last Updated: '+ getActualFullDate());
		$('#last_update').css('color','white');
		$('#last_update').css('font-weight','bold');

		drawSmallBox();
		drawChart();
		setInterval(drawSmallBox, 30000);
		setInterval(drawChart, 30000);


	});

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

	function drawSmallBox(){
		$.get('{{ url("fetch/flute_repair/by_status") }}', function(result, status, xhr){
			if(result.status){
				var tarik = 0;
				for(var i = 0; i < result.status.length; i++){
					if(result.status[i].status == 'repair'){
						$('#sedang').append().empty();
						$('#sedang').html(result.status[i].jml + '<sup style="font-size: 30px">set</sup>');
						tarik += result.status[i].jml;
					}
					if(result.status[i].status == 'selesai repair'){
						$('#selesai').append().empty();
						$('#selesai').html(result.status[i].jml + '<sup style="font-size: 30px">set</sup>');
						tarik += result.status[i].jml;
					}
					if(result.status[i].status == 'kembali ke warehouse'){
						$('#kembali').append().empty();
						$('#kembali').html(result.status[i].jml + '<sup style="font-size: 30px">set</sup>');
					}
				}
				$('#tarik').append().empty();
				$('#tarik').html(tarik + '<sup style="font-size: 30px">set</sup>');
			}

		});

	}

	function drawChart(){
		var tanggal = $('#datepicker').val();

		var data = {
			tanggal:tanggal		
		}

		$.get('{{ url("fetch/flute_repair/by_model") }}', data, function(result, status, xhr) {
			if(result.status){

				var tarik = [];
				var selesai = [];
				var kembali = [];

				var model = [];
				for (var i = 0; i < result.model.length; i++) {
					model.push(result.model[i].model);
				}

				for (var i = 0; i < result.datas.length; i++) {
					if(result.datas[i].status == 'repair'){
						tarik.push(result.datas[i].jml);
					}

					if(result.datas[i].status == 'selesai repair'){
						selesai.push(result.datas[i].jml);
					}					

					if(result.datas[i].status == 'kembali ke warehouse'){
						kembali.push(result.datas[i].jml);
					}
				}


				Highcharts.chart('container1', {
					chart: {
						type: 'column'
					},
					title: {
						text: 'Flute Repair by Model on '+result.date,
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: model,
						gridLineWidth: 3,
						gridLineColor: 'RGB(204,255,255)',
						crosshair: true
					},
					yAxis: {
						title: {
							text: 'Total'
						},
						type: 'logarithmic'
					},
					legend : {
						enabled: false
					},
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y}</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						series: {
							shadow: false,
							cursor: 'pointer',
							borderWidth: 0
						},
						column: {
							events: {
								legendItemClick: function () {
									return false; 
								}
							},
							animation:{
								duration:0
							},
							dataLabels: {	
								enabled: true,
								formatter: function () {
									return Highcharts.numberFormat(this.y,0);
								},
								style: {
									fontSize: '30px',
									fontWeight: 'bold'
								}

							}
						}
					},
					credits: {
						enabled: false
					},
					legend : {
						align: 'center',
						verticalAlign: 'bottom',
						x: 0,
						y: 0,

						backgroundColor: (
							Highcharts.theme && Highcharts.theme.background2) || 'white',
						borderColor: '#CCC',
						borderWidth: 1,
						shadow: false
					},
					series: [
					{
						name: 'Repair',
						data: tarik,
						color: '#dd4b39'
					},
					{
						name: 'Selesai Repair',
						data: selesai,
						color: '#0073b7'
					},
					{
						name: 'Kembali',
						data: kembali,
						color: '#00a65a'
					}
					]
					
				});

			}


		});


		$.get('{{ url("fetch/flute_repair/by_date") }}', function(result, status, xhr) {
			if(result.status){

				var tarik = [];
				var selesai = [];
				var kembali = [];

				var tgl = [];
				for (var i = 0; i < result.tgl.length; i++) {
					tgl.push(result.tgl[i].tgl);
				}

				for (var i = 0; i < result.datas.length; i++) {
					if(result.datas[i].status == 'repair'){
						tarik.push(result.datas[i].jml);
					}

					if(result.datas[i].status == 'selesai repair'){
						selesai.push(result.datas[i].jml);
					}					

					if(result.datas[i].status == 'kembali ke warehouse'){
						kembali.push(result.datas[i].jml);
					}
				}

				
				Highcharts.chart('container2', {
					chart: {
						type: 'column'
					},
					title: {
						text: 'Flute Repair by Date',
						style: {
							fontSize: '30px',
							fontWeight: 'bold'
						}
					},
					xAxis: {
						categories: tgl,
						gridLineWidth: 3,
						gridLineColor: 'RGB(204,255,255)',
						crosshair: true
					},
					yAxis: {
						title: {
							text: 'Total'
						},
						type: 'logarithmic'
					},
					legend : {
						enabled: false
					},
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y}</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					plotOptions: {
						series: {
							shadow: false,
							cursor: 'pointer',
							borderWidth: 0
						},
						column: {
							events: {
								legendItemClick: function () {
									return false; 
								}
							},
							animation:{
								duration:0
							},
							dataLabels: {	
								enabled: true,
								formatter: function () {
									return Highcharts.numberFormat(this.y,0);
								},
								style: {
									fontSize: '30px',
									fontWeight: 'bold'
								}
							}
						}
					},
					credits: {
						enabled: false
					},
					legend : {
						align: 'center',
						verticalAlign: 'bottom',
						x: 0,
						y: 0,

						backgroundColor: (
							Highcharts.theme && Highcharts.theme.background2) || 'white',
						borderColor: '#CCC',
						borderWidth: 1,
						shadow: false
					},
					series: [
					{
						name: 'Repair',
						data: tarik,
						color: '#dd4b39'
					},
					{
						name: 'Selesai Repair',
						data: selesai,
						color: '#0073b7'
					},
					{
						name: 'Kembali',
						data: kembali,
						color: '#00a65a'
					}
					]

				});

			}


		});

	}



</script>
@endsection