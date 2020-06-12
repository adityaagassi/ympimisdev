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
	#kz_top_sum, #kz_top_count, #kz_top_each_score {
		font-size: 15pt;
	}

	#kz_top_count > tr, #kz_top_each_score > tr {
		color: white;
	}
	#kz_top_sum > tr:first-child, #kz_top_count > tr:first-child, #kz_top_each_score > tr:first-child {
		color: #363836 !important;
		background-color: #ffbf00 !important;
	}
	#kz_top_sum > tr:nth-child(2), #kz_top_count > tr:nth-child(2), #kz_top_each_score > tr:nth-child(2) {
		color: #363836 !important;
		background-color: #a9aba9 !important;
	}
	#kz_top_sum > tr:nth-child(3), #kz_top_count > tr:nth-child(3), #kz_top_each_score > tr:nth-child(3) {
		color: #363836 !important;
		background-color: #cc952f !important;
	}

	#kz_top_each_score > tr:hover {
		background-color: #fff !important;
		cursor: pointer;
	}

	#tabel_nilai_all tbody > tr > th {
		text-align: center;
		background-color: #7e5686;
		color: white;
	}

	#tabel_nilai > tbody > tr > td {
		text-align: left;
	}
	#tabel_assess > tbody > tr > td, #tabel_assess > tbody > tr > th {
		text-align: center;
	}
	#tabel_assess > tbody > tr > th {
		background-color: #7e5686;
		color: white;
	}
	#tabelDetail > tbody > tr > td {
		text-align: left;
	}
	#tabel_Kz > tbody > tr > td {
		text-align: left;
		vertical-align: top;
		padding: 2px;
	}
	#tabel_Kz > tbody > tr > th {
		padding: 2px;
		background-color: #7e5686;
		color: white;
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
				<!-- <h1 style="color: white">TOTAL KAIZEN : 50</h1> -->
				<h3 style="color: white;">TOP Kaizen Score :</h3>
				<table class="table" style="color: white">
					<thead>
						<tr>
							<th><i class="fa fa-trophy fa-2x"></i></th>
							<th>EMPLOYEE ID</th>
							<th>EMPLOYEE NAME</th>
							<th>DEPARTMENT - SECTION - GROUP</th>
							<th>TOTAL POINT</th>
						</tr>
					</thead>
					<tbody id="kz_top_sum"></tbody>
				</table>
				<h3 style="color: white;">TOP Number of Kaizen :</h3>
				<table class="table" style="color: white">
					<thead>
						<tr>
							<th><i class="fa fa-trophy fa-2x"></i></th>
							<th>EMPLOYEE ID</th>
							<th>EMPLOYEE NAME</th>
							<th>DEPARTMENT - SECTION - GROUP</th>
							<th>TOTAL KAIZEN</th>
						</tr>
					</thead>
					<tbody id="kz_top_count"></tbody>
				</table>
				<h3 style="color: white;">TOP 10 each Kaizen Teian Score (POTENTIAL EXCELLENT) :</h3>
				<table class="table" style="color: white">
					<thead>
						<tr>
							<th><i class="fa fa-trophy fa-2x"></i></th>
							<th>EMPLOYEE ID</th>
							<th>EMPLOYEE NAME</th>
							<th>DEPARTMENT - SECTION - GROUP</th>
							<th>SCORE</th>
						</tr>
					</thead>
					<tbody id="kz_top_each_score"></tbody>
				</table>
				<h3 style="color: white;">KAIZEN EXCELLENT :</h3>
				<table class="table" style="color: white">
					<thead>
						<tr>
							<th><i class="fa fa-trophy fa-2x"></i></th>
							<th>EMPLOYEE ID</th>
							<th>EMPLOYEE NAME</th>
							<th>DEPARTMENT - SECTION - GROUP</th>
							<th>SCORE</th>
						</tr>
					</thead>
					<tbody id="kz_excellent"></tbody>
				</table>

				<div class="modal fade" id="modal_excellent">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-body">
								<div class="row">
									<div class="col-xs-12">
										<p style="font-size: 25px; font-weight: bold; text-align: center" id="kz_title"></p>
										<table id="tabelDetail" width="100%">
											<tr>
												<th>NIK/Name </th>
												<td> : </td>
												<td id="kz_nik"></td>
												<th>Date</th>
												<td> : </td>
												<td id="kz_tanggal"></td>
											</tr>
											<tr>
												<th>Section</th>
												<td> : </td>
												<td id="kz_section"></td>
												<th>Area Kaizen</th>
												<td> : </td>
												<td id="kz_area"></td>
											</tr>
											<tr>
												<th>Leader</th>
												<td> : </td>
												<td id="kz_leader"></td>
											</tr>
											<tr>
												<td colspan="6"><hr style="margin: 5px 0px 5px 0px; border-color: black"></td>
											</tr>
										</table>
										<table width="100%" border="1" id="tabel_Kz">
											<tr>
												<th style="border-bottom: 1px solid black" width="50%">BEFORE :</th>
												<th style="border-bottom: 1px solid black; border-left: 1px" width="50%">AFTER :</th>
											</tr>
											<tr>
												<td id="kz_before"></td>
												<td id="kz_after"></td>
											</tr>
										</table>
										<table width="100%" id="tabel_nilai" style="border: 1px solid black">
										</table>
										<br>
										<table width="100%" border="1" id="tabel_assess">
											<tr>
												<th colspan="4">TABEL NILAI KAIZEN</th>
											</tr>
											<tr>
												<th width="5%">No</th>
												<th>Kategori</th>
												<th>Foreman / Chief</th>
												<th>Manager</th>
											</tr>
											<tr>
												<th>1</th>
												<th>Estimasi Hasil</th>
												<td id="foreman_point1"></td>
												<td id="manager_point1"></td>
											</tr>
											<tr>
												<th>2</th>
												<th>Ide</th>
												<td id="foreman_point2"></td>
												<td id="manager_point2"></td>
											</tr>
											<tr>
												<th>3</th>
												<th>Implementasi</th>
												<td id="foreman_point3"></td>
												<td id="manager_point3"></td>
											</tr>
											<tr>
												<th colspan="2"> TOTAL</th>
												<td id="foreman_total" style="font-weight: bold;"></td>
												<td id="manager_total" style="font-weight: bold;"></td>
											</tr>
										</table>
										<br>
										<table width="100%" id="tabel_nilai_all" border="1">
											<tr>
												<th>No</th>
												<th>Total Nilai</th>
												<th>Point</th>
												<th>Keterangan</th>
												<th>Reward Aplikasi</th>
											</tr>
											<tr>
												<td>1</td>
												<td><300</td>
												<td>2</td>
												<td>Kurang</td>
												<td>Rp 2.000,-</td>
											</tr>

											<tr>
												<td>2</td>
												<td>300 - 350</td>
												<td>4</td>
												<td>Cukup</td>
												<td>Rp 5.000,-</td>
											</tr>

											<tr>
												<td>3</td>
												<td>351 - 400</td>
												<td>6</td>
												<td>Baik</td>
												<td>Rp 10.000,-</td>
											</tr>

											<tr>
												<td>4</td>
												<td>401 - 450</td>
												<td>8</td>
												<td>Sangat Baik</td>
												<td>Rp 25,000,-</td>
											</tr>

											<tr>
												<td>5</td>
												<td>> 450</td>
												<td>10</td>
												<td>Potensi Excellent</td>
												<td>Rp 50,000,-</td>
											</tr>
										</table>
										<br>
										<div id="eksekusi">
											<button class="btn btn-success pull-left" onclick="eksekusi_kaizen(true)"><i class="fa fa-check"></i>&nbsp;EXCELLENT</button>
											<button class="btn btn-danger pull-right" onclick="eksekusi_kaizen(false)"><i class="fa fa-close"></i>&nbsp;NOT EXCELLENT</button>
										</div>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
							</div>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
<script src="{{ url("js/drilldown.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var id_kz = 0;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$("#navbar-collapse").text('');
		$("#eksekusi").hide();
		drawChart();

		// setInterval(drawChart, 3000);
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function drawChart() {

		var tanggal = $('#tgl').val();

		var data = {
			tanggal:tanggal
		}

		$.get('{{ url("fetch/kaizen/report") }}', data, function(result) {

			$("#kz_top_sum").empty();
			$("#kz_top_count").empty();
			$("#kz_top_each_score").empty();
			$("#kz_excellent").empty();
			var data1 = [];
			var data_total = [];
			var total_tmp = 0;
			var data_tmp = [];

			for (var i = 0; i < result.charts.length; i++) {
				if (typeof result.charts[i+1] === 'undefined') {
					total_tmp += parseInt(result.charts[i].kaizen);
					data_total.push({name:result.charts[i].department, y:total_tmp, drilldown:result.charts[i].department});
				} else {
					if (result.charts[i].department != result.charts[i+1].department) {
						total_tmp += parseInt(result.charts[i].kaizen);
						data_total.push({name:result.charts[i].department, y:total_tmp, drilldown:result.charts[i].department});
						total_tmp = 0;
					} else {
						total_tmp += parseInt(result.charts[i].kaizen);
					}
				}
			}

			// console.table(data_total);

			for (var z = 0; z < data_total.length; z++) {
				for (var x = 0; x < result.charts.length; x++) {
					if (data_total[z].name == result.charts[x].department) {
						data_tmp.push([result.charts[x].section, parseInt(result.charts[x].kaizen)]);
					}
				}
				data1.push({name:data_total[z].name, id:data_total[z].name, data: data_tmp});
				data_tmp = [];
			}

			// console.table(data1);

			Highcharts.chart('kz_total', {
				chart: {
					type: 'column'
				},
				title: {
					text: 'Kaizen Teian Data Assessed in '+result.date
				},
				subtitle: {
					text: 'Click the columns to view detail each Section'
				},
				accessibility: {
					announceNewData: {
						enabled: true
					}
				},
				xAxis: {
					type: 'category'
				},
				yAxis: {
					title: {
						text: 'Total Kaizen'
					}

				},
				legend: {
					enabled: false
				},
				plotOptions: {
					series: {
						borderWidth: 0,
						dataLabels: {
							enabled: true,
							format: '{point.y}'
						}
					},
					column: {
						animation: false
					}
				},

				credits:{
					enabled:false
				},
				tooltip: {
					headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
					pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> of total<br/>'
				},

				series: [
				{
					name: "Department",
					colorByPoint: true,
					data: data_total
				}
				],
				drilldown: {
					series: data1
				}
			});

			body_sum = "";
			no = 1;
			$.each(result.rank1, function(index, value){
				body_sum += "<tr>";
				body_sum += "<td>"+no+"</td>";
				body_sum += "<td>"+value.employee_id+"</td>";
				body_sum += "<td>"+value.employee_name+"</td>";
				body_sum += "<td>"+value.bagian+"</td>";
				body_sum += "<td>"+value.nilai+"</td>";
				body_sum += "</tr>";
				no++;
			})
			$("#kz_top_sum").append(body_sum);

			body_count = "";
			no = 1;

			$.each(result.rank2, function(index, value){
				body_count += "<tr>";
				body_count += "<td>"+no+"</td>";
				body_count += "<td>"+value.employee_id+"</td>";
				body_count += "<td>"+value.employee_name+"</td>";
				body_count += "<td>"+value.bagian+"</td>";
				body_count += "<td>"+value.count+"</td>";
				body_count += "</tr>";
				no++;
			})

			$("#kz_top_count").append(body_count);

			body_each = "";
			no = 1;

			$.each(result.excellent, function(index, value){
				body_each += "<tr onclick='detail_excellent("+value.id+")'>";
				body_each += "<td>"+no+"</td>";
				body_each += "<td>"+value.employee_id+"</td>";
				body_each += "<td>"+value.employee_name+"</td>";
				body_each += "<td>"+value.bagian+"</td>";
				body_each += "<td>"+value.score+"</td>";
				body_each += "</tr>";
				no++;
			})

			$("#kz_top_each_score").append(body_each);

			body_each = "";
			no = 1;

			$.each(result.true_excellent, function(index, value){
				body_each += "<tr onclick='detail_excellent("+value.id+")'>";
				body_each += "<td colspan='2'>"+value.employee_id+"</td>";
				body_each += "<td>"+value.employee_name+"</td>";
				body_each += "<td>"+value.bagian+"</td>";
				body_each += "<td>"+value.score+"</td>";
				body_each += "</tr>";
				no++;
			})

			$("#kz_excellent").append(body_each);
		});
}

function detail_excellent(id) {
	$('#modal_excellent').modal('show');

	data = {
		id:id
	}

	$.get('{{ url("fetch/kaizen/detail") }}', data, function(result) {
		if (result.aksi && result.datas[0].remark == null) {
			$("#eksekusi").show();
			id_kz = id;
		} else {
			$("#eksekusi").hide();
		}

		$("#kz_title").text(result.datas[0].title);
		$("#kz_nik").text(result.datas[0].employee_id + " / "+ result.datas[0].employee_name);
		$("#kz_section").text(result.datas[0].section);
		$("#kz_leader").text(result.datas[0].leader_name);
		$("#kz_tanggal").text(result.datas[0].date);
		$("#kz_area").text(result.datas[0].area);
		$("#kz_before").html(result.datas[0].condition);
		$("#kz_after").html(result.datas[0].improvement);
		$("#foreman_point1").text(result.datas[0].foreman_point_1 * 40);
		$("#foreman_point2").text(result.datas[0].foreman_point_2 * 30);
		$("#foreman_point3").text(result.datas[0].foreman_point_3 * 30);
		$("#foreman_total").text((result.datas[0].foreman_point_1 * 40) + (result.datas[0].foreman_point_2 * 30) + (result.datas[0].foreman_point_3 * 30));
		$("#manager_point1").text(result.datas[0].manager_point_1 * 40);
		$("#manager_point2").text(result.datas[0].manager_point_2 * 30);
		$("#manager_point3").text(result.datas[0].manager_point_3 * 30);
		$("#manager_total").text((result.datas[0].manager_point_1 * 40) + (result.datas[0].manager_point_2 * 30) + (result.datas[0].manager_point_3 * 30));

		$("#tabel_nilai").empty();
		if (result.datas[0].cost_name) {
			bd = "";
			tot = 0;
			bd += "<tr style='font-size: 13px;'><th>Estimasi Hasil : </th></tr>";
			$.each(result.datas, function(index, value){
				bd += "<tr>";
				var unit = "";

				if (value.cost_name == "Manpower") {
					unit = "menit";
					sub_tot = parseInt(value.sub_total_cost) * 20;
					tot += sub_tot;
				}  else if (value.cost_name == "Tempat") {
					unit = value.unit+"<sup>2</sup>";
					sub_tot = parseInt(value.sub_total_cost);
					tot += sub_tot;
				}
				else {
					unit = value.unit;
					sub_tot = value.sub_total_cost;
					tot += sub_tot;
				}

				sub_tot = sub_tot.toLocaleString('es-ES');

				bd += "<th>"+value.cost_name+"</th>";
				bd += "<td><b>"+value.cost+"</b> "+unit+" X <b>Rp "+value.std_cost+",-</b></td>";
				bd += "<td><b>Rp "+sub_tot+",- / bulan</b></td>";
				bd += "</tr>";
			});

			tot = tot.toLocaleString('es-ES');

			bd += "<tr style='font-size: 18px;'>";
			bd += "<th colspan='2' style='text-align: right;padding-right:5px'>Total : </th>";
			bd += "<td><b>Rp "+tot+",-</b></td>";
			bd += "</tr>";

			$("#tabel_nilai").append(bd);
		}
		$("#modalDetail").modal('show');
	})
}

function eksekusi_kaizen(status) {
	var data = {
		status : status,
		id: id_kz
	}
	$.post('{{ url("execute/kaizen/excellent") }}', data, function(result) {
		openSuccessGritter('Success!', result.message);
	})
}

$("#modal_excellent").on('hide.bs.modal', function(){
	id_kz = 0;
});

$('#tgl').datepicker({
	autoclose: true,
	format: "yyyy-mm",
	viewMode: "months", 
	minViewMode: "months"
});

function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '2000'
	});
}

function openErrorGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-danger',
		image: '{{ url("images/image-stop.png") }}',
		sticky: false,
		time: '2000'
	});
}
</script>
@endsection
