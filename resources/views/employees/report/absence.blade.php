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
	#error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
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
					<div class="tab-content">
						<div class="tab-pane active" id="tab_1">
							<div id="tidak_ada_data"></div>
							<div id="absence" style="width: 99%;"></div>
							<div id="loading" style="font-size: 25px; text-align: center;"><i class="fa fa-spinner fa-pulse fa-lg"></i>&nbsp;Loading . . .</div>
							<div id ="container" style ="margin: 0 auto"></div>
							<br>
							<table class="table table-striped">
								<thead>
									<tr>
										<th bgcolor="#605ca8" colspan="4" class="text-center" style="color: white"><i class="fa fa-bullhorn"></i> Keterangan</th>
									</tr>
								</thead>
								<tbody>

									<?php 
									for ($i=1; $i <= count($absence_category); $i++) { 
										if ($i % 2 == 0) { ?>
											<td style="border-right: 1px solid #605ca8; border-left: 1px solid #605ca8;"><?= $absence_category[$i-1]['attend_code'] ?></td>
											<td><?= $absence_category[$i-1]['attend_name'] ?></td>
										</tr>
										
									<?php } else { ?>
										<tr>
											<td style="border-right: 1px solid #605ca8; border-left: 1px solid #605ca8;"><?= $absence_category[$i-1]['attend_code'] ?></td>
											<td><?= $absence_category[$i-1]['attend_name'] ?></td>
										<?php } 
									} ?>
								</tbody>
							</table>
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
										<th>Bagian</th>
										<th>Absensi</th>
									</tr>
								</thead>
								<tbody id="body_detail">
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


@endsection

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

	var absences = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('#myModal').on('hidden.bs.modal', function () {
			$('#tabel_detail').DataTable().clear();
		});

		$("#loading").hide();

		drawChart();
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: "dd-mm-yyyy",
		endDate: '<?php echo $tgl_max ?>'
	});

	function drawChart(){
		var tanggal = $('#tgl').val();
		$("#loading").show();
		
		var data = {
			tgl:tanggal
		}

		$.get('{{ url("fetch/report/absence") }}', data, function(result, status, xhr) {  
			$("#loading").hide();

			if(result.absence.length > 0){
				absences = result.absence;

				var counts = {};
				
				for(var i = 0; i < absences.length; i++){
					var key = absences[i].Attend_Code;
					if(!counts[key]) counts[key] = 0;
					counts[key]++;
				}

				shift = Object.keys(counts);
				jml = Object.values(counts);

				$('#tidak_ada_data').append().empty();

				var titleChart = result.titleChart;

				Highcharts.chart('absence', {
					chart: {
						type: 'column'
					},
					title: {
						text: '<span style="font-size: 18pt;">Absence Data</span><br><center><span style="color: rgba(96, 92, 168);">'+ titleChart +'</center></span>',
						useHTML: true
					},
					xAxis: {
						categories: shift
					},
					yAxis: {
						title: {
							text: 'Total Manpower'
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
										showDetail(event.point.category, result.tgl);
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
				$('#tidak_ada_data').append('<br><div class="alert alert-warning alert-dismissible" data-dismiss="alert" aria-hidden="true" style="margin-right: 3.3%;margin-left: 2%"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h4><i class="icon fa fa-warning"></i> Data Hari ini belum diupload!</h4></div>');
			}		

		});

	}

	function showDetail(shift, tgl) {
		$('#tabel_detail').DataTable().clear();
		$('#tabel_detail').DataTable().destroy();
		// console.log(shift, tgl);

		var tanggal = parseInt(tgl.slice(0, 2));
		var bulan = parseInt(tgl.slice(3, 5));
		var tahun = tgl.slice(6, 10);
		var bulanText = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

		$("#body_detail").empty();
		body = "";

		$.each(absences, function(index, value){
			if (value.Attend_Code == shift){
				body += "<tr>";
				body += "<td>"+value.tanggal+"</td>";
				body += "<td>"+value.emp_no+"</td>";
				body += "<td>"+value.official_name+"</td>";
				body += "<td>"+value.bagian+"</td>";
				body += "<td>"+value.Attend_Code+"</td>";
				body += "</tr>";
			}
		})

		$("#body_detail").append(body);
		$('#myModal').modal('show');

		var table = $('#tabel_detail').DataTable({
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
		});

		$('#judul_table').append().empty();
		$('#judul_table').append('<center>Absence '+shift+' in '+tanggal+' '+bulanText[bulan-1]+' '+tahun+'<center>');

	}

</script>


@stop