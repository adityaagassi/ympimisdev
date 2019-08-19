@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
@endsection
@section('header')
<section class="content-header">
	<h1>
		Absence Data <span class="text-purple">  欠勤データ </span>
	</h1>
	
	<ol class="breadcrumb">
	</ol>
</section>
@endsection


@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="col-md-12">
		<div class="col-md-12">
			
		</div>
		<div class="col-md-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<div class="col-md-2 pull-right">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border-color: #00a65a">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tgl" onchange="drawChart()" placeholder="Select Date" style="border-color: #00a65a">
						</div>
						<br>
					</div>
					<li class="pull-right"><label>Date : </label></li>
				</ul>
				
				<div class="tab-content">
					<div class="tab-pane active" id="tab_1">
						<div id="tidak_ada_data"></div>
						<div id="absence" style="width: 850pt;"></div>
						<div class="col-md-12">
							<div id ="container" style ="margin: 0 auto"></div>
							<br>
							<table class="table table-striped">
								<tr>
									<th bgcolor="#605ca8" colspan="4" class="text-center" style="color: white"><i class="fa fa-bullhorn"></i> Keterangan</th>
								</tr>
								<tr>
									<td>CT</td>
									<td>: Cuti Tahunan</td>
									<td>Sn</td>
									<td>: Cuti Khusus Saudara Kandung Nikah</td>
								</tr>
								<tr>
									<td>CK</td>
									<td>: Cuti Khusus Lainnya</td>
									<td>N</td>
									<td>: Cuti Khusus Menikah</td>
								</tr>
								<tr>
									<td>Im</td>
									<td>: Istri Melahirkan</td>
									<td>SD</td>
									<td>: Sakit dengan Surat Dokter</td>
								</tr>
								<tr>
									<td>W</td>
									<td>: W apa</td>
									<td>S</td>
									<td>: Sakit belum ada Surat Dokter</td>
								</tr>
								<tr>
									<td>Km</td>
									<td>: Cuti Khusus Kematian</td>
									<td>I</td>
									<td>: Ijin</td>
								</tr>
								<tr>
									<td>K</td>
									<td>: Cuti Pra-Lahir</td>
									<td>A</td>
									<td>: Alpha</td>
								</tr>
								<tr>
									<td>M</td>
									<td>: Cuti Pasca-Lahir</td>
									<td>DL</td>
									<td>: Dinas Luar</td>
								</tr>
								<tr bgcolor="#6cadc9">
									<td>T</td>
									<td>: Terlambat</td>
									<td>PC</td>
									<td>: Pulang Cepat</td>
								</tr>
							</table>
						</div>
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
								<thead>
									<tr>
										<th>Tanggal</th>
										<th>NIK</th>
										<th>Nama karyawan</th>
										<th>Section</th>
										<th>Absensi</th>
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

		drawChart();
	});

	$('.datepicker').datepicker({
		autoclose: true,
		format: "dd-mm-yyyy"
	});

	function drawChart(){
		var tanggal = $('#tgl').val();
		
		var data = {
			tgl:tanggal
		}

		$.get('{{ url("fetch/report/absence") }}', data, function(result, status, xhr) {  
			
			if(result.absence.length > 0){
				$('#tidak_ada_data').append().empty();
				var shift = [];
				var jml = [];
				var titleChart = result.titleChart;

				for (var i = 0; i < result.absence.length; i++) {
					shift.push(result.absence[i].shift);
					jml.push(parseInt(result.absence[i].jml));
				}

				Highcharts.chart('absence', {
					chart: {
						type: 'column'
					},
					title: {
						text: titleChart
					},
					xAxis: {
						categories: shift
					},
					yAxis: {
						title: {
							text: 'Total Absence'
						}
					},
					legend : {
						enabled: false
					},
					tooltip: {
						headerFormat: '',
						pointFormat: '<span style="color:{point.color}">{point.category}</span>: <b>{point.y}</b> <br/>'
					},
					plotOptions: {
						series: {
							cursor: 'pointer',
							point: {
								events: {
									click: function (event) {
										showDetail(event.point.category,result.tgl);
									}
								}
							},
							borderWidth: 0,
							dataLabels: {
								enabled: true,
								format: '{point.y}'
							}
						}
					},
					credits: {
						enabled: false
					},
					series: [
					{
						"colorByPoint": true,
						name: shift,
						data: jml,
					}
					]
				});

			}else{
				$('#absence').append().empty();
				$('#tidak_ada_data').append().empty();
				$('#tidak_ada_data').append('<div class="alert alert-warning alert-dismissible" data-dismiss="alert" aria-hidden="true" style="margin-right: 3.3%;margin-left: 2%"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h4><i class="icon fa fa-warning"></i> Data Hari ini belum diupload!</h4></div>');
			}		

		});

	}

	function showDetail(shift,tgl) {
		tabel = $('#tabel_detail').DataTable();
		tabel.destroy();

		var tanggal = parseInt(tgl.slice(0, 2));
		var bulan = parseInt(tgl.slice(3, 5));
		var tahun = tgl.slice(6, 10);
		var bulanText = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

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
				"url" : "{{ url("fetch/report/detail_absence") }}",
				"data" : {
					shift : shift,
					tgl : tgl
				}
			},
			"columns": [
			{ "data": "tanggal" },
			{ "data": "nik" },
			{ "data": "nama" },
			{ "data": "section" },
			{ "data": "absensi"}
			]
		});

		$('#judul_table').append().empty();
		$('#judul_table').append('<center>Absence '+shift+' in '+tanggal+' '+bulanText[bulan-1]+' '+tahun+'<center>');

	}

</script>


@stop