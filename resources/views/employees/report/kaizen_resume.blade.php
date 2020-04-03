@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	@font-face {
		font-family: JTM;
		src: url("{{ url("fonts/JTM.otf") }}") format("opentype");
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

	.judul {
		font-family: 'JTM';
		color: white;
		font-size: 35pt;
	}
	#kz_top_sum, #kz_top_count {
		font-size: 15pt;
	}

	#kz_top_count > tr {
		color: white;
	}
	#kz_top_sum > tr:first-child, #kz_top_count > tr:first-child {
		color: #363836 !important;
		background-color: #ffbf00 !important;
	}
	#kz_top_sum > tr:nth-child(2), #kz_top_count > tr:nth-child(2) {
		color: #363836 !important;
		background-color: #a9aba9 !important;
	}
	#kz_top_sum > tr:nth-child(3), #kz_top_count > tr:nth-child(3) {
		color: #363836 !important;
		background-color: #cc952f !important;
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-left: 0px; padding-right: 0px; padding-top: 0px">
	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-12">
				<div class="col-xs-2 pull-left">
					<p class="judul"><i style="color: #c290d1">e </i> - Kaizen</p>
				</div>
				<div class="col-xs-2 pull-right">
					<div class="input-group date">
						<div class="input-group-addon bg-green" style="border-color: #00a65a">
							<i class="fa fa-calendar"></i>
						</div>
						<input type="text" class="form-control datepicker" id="tgl" onchange="drawChart()" placeholder="Select Date" style="border-color: #00a65a">
					</div>
					<br>
				</div>
			</div>
			<div class="col-xs-12">
				<div id="kz_total" style="width: 100%; height: 500px;"></div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 3%;">ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 3%;">Grade</th>
								<th style="width: 3%;">Section</th>
								<th style="width: 3%;">Group</th>
								<th style="width: 3%;">Total Kaizen</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
						<tfoot id="tableDetailFoot">
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th>Total</th>
								<th id="totalKaizen"></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

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

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$("#navbar-collapse").text('');
		drawChart();

		// setInterval(drawChart, 3000);
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function drawChart() {

		var tanggal = $('#tgl').val();

		var data = {
			tanggal:tanggal
		}

		$.get('{{ url("fetch/kaizen/resume") }}', data, function(result) {
			if (result.status) {

				var kumpul = [];
				var belum = [];
				var total_kz = [];
				var ctg = [];

				$.each(result.datas, function(index, value){
					kumpul.push(parseInt(value.total_sudah));
					belum.push(parseInt(value.total_belum));
					total_kz.push(parseInt(value.total_kaizen));
					ctg.push(value.leader+"-"+value.name);
				});

				Highcharts.chart('kz_total', {
					chart: {
						type: 'column'
					},

					title: {
						text: 'Grafik Kaizen Teian '+result.fiscal
					},

					xAxis: {
						categories: ctg
					},

					yAxis: {
						allowDecimals: false,
						min: 0,
						title: {
							text: 'Number of Kaizen Teian'
						},
					},

					tooltip: {
						formatter: function () {
							return '<b>' + this.x + '</b><br/>' +
							this.series.name + ': ' + this.y;
						},
					},

					plotOptions: {
						column: {
							stacking: 'normal'
						},
						line: {
							marker: {
								enabled: false,
								radius: 0.1
							},

						},
						series:{
							cursor: 'pointer',
							point: {
								events: {
									click: function () {
										fetchModal(this.category);
									}
								}
							},
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '0.7vw'
								}
							},
						},
					},

					credits: {
						enabled: false
					},

					series: [{
						name: 'Belum Mengumpulkan',
						data: belum,
						color: '#db3223'
					}, {
						name: 'Mengumpulkan',
						data: kumpul,
						color: '#2caddb'
					}
					]
				});
			} else {
				alert(result.message);
			}
		})
	}

	function fetchModal(cat){
		$('#modalDetail').modal('show');
		$('#loading').show();
		$('#modalDetailTitle').html("");
		$('#modalDetailTitle').html("<center><span style='font-size: 20px; font-weight: bold;'>Stock: "+cat+"</span></center>");
		$('#tableDetail').hide();
		var tanggal = $('#tgl').val();

		var data = {
			tanggal:tanggal,
			leader:cat
		}
		$.get('{{ url("fetch/kaizen/resume_detail") }}', data, function(result) {
			if(result.status){
				$('#tableDetailBody').html('');

				var index = 1;
				var resultData = "";
				var total = 0;

				$.each(result.details, function(key, value) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.employee_id +'</td>';
					resultData += '<td>'+ value.name +'</td>';
					resultData += '<td>'+ value.grade +'</td>';
					resultData += '<td>'+ value.section +'</td>';
					resultData += '<td>'+ value.group +'</td>';
					if(value.kz == 0){
						resultData += '<td style="background-color: RGB(255,204,255)">'+ value.kz +'</td>';						
					}
					else{
						resultData += '<td style="background-color: RGB(204,255,255)">'+ value.kz +'</td>';
					}
					resultData += '</tr>';
					index += 1;
					total += parseInt(value.kz);
				});
				$('#tableDetailBody').append(resultData);
				$('#loading').hide();
				$('#tableDetail').show();

				$('#totalKaizen').text(total);

			}
			else{
				alert(result.message);
			}
		});

	}

	$('#tgl').datepicker({
		autoclose: true,
		format: "yyyy-mm",
		viewMode: "months", 
		minViewMode: "months"
	});

</script>
@endsection
