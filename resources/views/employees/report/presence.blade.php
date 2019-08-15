@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
@endsection
@section('header')
<section class="content-header">
	<h1>
		Presence Data <span class="text-purple">  出勤データ </span>
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
			<div id="tidak_ada_data">
			</div>
		</div>
		<div class="col-md-12">
			<div id="presence" style="width: 850pt;"></div>
		</div>
	</div>

	<!-- start modal -->
	<div class="modal fade" id="myModal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 style="float: right;" id="modal-title"></h4>
					<h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
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

		$.get('{{ url("fetch/report/presence") }}', data, function(result, status, xhr) {
			console.log(result);
			console.log(status);
			console.log(xhr);

			var shift = [];
			var hadir = [];
			var titleChart = result.tgl;

			for (var i = 0; i < result.presence.length; i++) {
				shift.push(parseInt(result.presence[i].shift));
				hadir.push(parseInt(result.presence[i].jml));
			}

			Highcharts.chart('presence', {
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
						text: 'Total Absent'
					}
				},
				legend : {
					enabled: false
				},
				tooltip: {
					headerFormat: '',
					pointFormat: '<span style="color:{point.color}">Shift {point.name}</span>: <b>{point.y}</b> <br/>'
				},
				plotOptions: {
					series: {
						cursor: 'pointer',
						point: {
							events: {
								click: function (event) {
									alert(event.point.category);
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
				series: [
				{
					"name": "By Shift",
					"colorByPoint": true,
					name: 'By Absent',
					data: hadir,
				}
				]
			});

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

	}

</script>


@stop