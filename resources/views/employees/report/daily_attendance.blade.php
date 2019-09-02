@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
	#queueTable.dataTable {
		margin-top: 0px!important;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Attendance Rate <span class="text-purple">  出勤率 </span>
	</h1>
	
	<ol class="breadcrumb">
	</ol>
</section>
@endsection


@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-12">
				<div class="col-md-2 pull-right">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border-color: #00a65a">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tgl" onchange="drawChart()" placeholder="Select Date" style="border-color: #00a65a">
					</div>
					<br>
				</div>
			</div>
			
			<div class="col-md-12">
				<div class="nav-tabs-custom">
				<!-- <ul class="nav nav-tabs">
					<li class="active"><a href="#tab_1" data-toggle="tab">
						By Shift 
						<br><span class="text-purple">シフト別</span>
					</a></li>
				</ul> -->
				<div class="tab-content">
					<div class="tab-pane active" id="tab_1">
						<div id="tidak_ada_data"></div>
						<div id="daily_attendance" style="width: 99%;"></div>
					</div>
					<div class="tab-pane" id="tab_2">
						<div id = "container2" style = "width: 850px; margin: 0 auto"></div>
					</div>
					<!-- /.tab-pane -->
				</div>
				<!-- /.tab-content -->
			</div>
		</div>
	</div>
</div>
<!-- start modal -->
<div class="modal fade" id="myModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="float: right;" id="modal-title"></h4>
				<h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
				<br><h4 class="modal-title" id="judul_table"></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<table id="tabel_detail" class="table table-striped table-bordered" style="width: 100%;"> 
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Tanggal</th>
									<th>NIK</th>
									<th>Nama karyawan</th>
									<th>Section</th>
									<th>Check-in</th>
									<th>Check-out</th>
									<th>Shift</th>
								</tr>
							</thead>
							<tbody>
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
	<!-- end modal -->
</div>


</section>


@stop

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

		$('#myModal').on('hidden.bs.modal', function () {
			$('#tabel_detail').DataTable().clear();
		});

		drawChart();
	});

	$('.datepicker').datepicker({
		// <?php $tgl_max = date('m-Y') ?>
		autoclose: true,
		format: "mm-yyyy",
		startView: "months", 
		minViewMode: "months",
		autoclose: true,
		
		// endDate: '<?php echo $tgl_max ?>'

	});

	function drawChart(){
		var tanggal = $('#tgl').val();
		
		var data = {
			tgl:tanggal
		}

		$.get('{{ url("fetch/report/daily_attendance") }}', data, function(result, status, xhr) {

			if(result.attendanceData.length > 0){
				$('#tidak_ada_data').append().empty();
				var tgl = [];
				var masuk = [];
				var tdk =[];
				var titleChart = result.titleChart;

				for (var i = 0; i < result.attendanceData.length; i++) {
					tgl.push( result.attendanceData[i].tanggal);
					masuk.push( parseInt( result.attendanceData[i].hadir ));
					tdk.push(parseInt( result.attendanceData[i].tdk));
				}
				Highcharts.chart('daily_attendance', {
					chart: {
						type: 'column'
					},
					title: {
						text: '<span style="font-size: 18pt;">Attendance in</span><br><center><span style="color: rgba(96, 92, 168);">'+ titleChart +'</center></span>',
						useHTML: true
					},
					xAxis: {
						categories: tgl
					},
					yAxis: {
						min: 0,
						title: {
							text: 'Employee Total (percentage)'
						},
						stackLabels: {
							enabled: true,
							style: {
								fontWeight: 'bold',
								color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
							}
						}
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
					tooltip: {
						pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
						shared: true
					},
					plotOptions: {
						column: {
							stacking: 'percent',
							dataLabels: {
								enabled: true,
								color: (Highcharts.theme && Highcharts.theme.dataLabelsColor)
								|| 'white',
								style: {
									textShadow: '0 0 3px black'
								}
							},
							cursor: 'pointer',
							point: {
								events: {
									click:function(event) {
										showDetail(event.point.category);
									}
								}
							},
						},
						series : {
							dataLabels:{
								enabled:true,
								formatter:function() {
									var pcnt = this.y;
									return Highcharts.numberFormat(pcnt);
								}
							}
						}
					},
					credits: {
						enabled: false
					},
					series: [
					{
						name: 'Tidak Hadir',
						data: tdk,
					},{
						name: 'Hadir',
						data: masuk,
					}]
				});

			}else{
				$('#daily_attendance').append().empty();
				$('#tidak_ada_data').append().empty();
				$('#tidak_ada_data').append('<br><div class="alert alert-warning alert-dismissible" data-dismiss="alert" aria-hidden="true" style="margin-right: 3.3%;margin-left: 2%"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h4><i class="icon fa fa-warning"></i> Data Bulan ini belum diupload!</h4></div>');
			}		

		});

	}

	function showDetail(tgl) {
		tabel = $('#tabel_detail').DataTable();
		tabel.destroy();

		$('#myModal').modal('show');

		var table = $('#tabel_detail').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'buttons': {
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default',
					// text: '<i class="fa fa-print"></i> Show',
				},
				{
					extend: 'copy',
					className: 'btn btn-success',
					text: '<i class="fa fa-copy"></i> Copy',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				{
					extend: 'excel',
					className: 'btn btn-info',
					text: '<i class="fa fa-file-excel-o"></i> Excel',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				{
					extend: 'print',
					className: 'btn btn-warning',
					text: '<i class="fa fa-print"></i> Print',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				]
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/report/detail_daily_attendance") }}",
				"data" : {
					tgl : tgl
				}
			},
			"columns": [
			{ "data": "tanggal" },
			{ "data": "nik" },
			{ "data": "nama" },
			{ "data": "section" },
			{ "data": "masuk" },
			{ "data": "keluar" },
			{ "data": "shift"}
			]
		});

		$('#judul_table').append().empty();
		$('#judul_table').append('<center>Attendance in '+tgl+'<center>');

	}

</script>


@stop