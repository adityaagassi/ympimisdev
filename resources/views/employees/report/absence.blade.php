@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

	table.table-bordered{
  border:2px solid rgba(150, 150, 150, 0);
}
table.table-bordered > thead > tr > th{
  border:2px solid rgb(54, 59, 56) !important;
  text-align: center;
  background-color: #f0f0ff;  
  color:black;
}
table.table-bordered > tbody > tr > td{
  border-collapse: collapse !important;
  border:2px solid rgb(54, 59, 56)!important;
  background-color: #f0f0ff;
  color: black;
  vertical-align: middle;
  text-align: center;
  padding:3px;
}
table.table-condensed > thead > tr > th{   
  color: black
}
table.table-bordered > tfoot > tr > th{
  border:2px solid rgb(150,150,150);
  padding:0;
}
table.table-bordered > tbody > tr > td > p{
  color: #abfbff;
}

table.table-striped > thead > tr > th{
  border:2px solid black !important;
  text-align: center;
  background-color: rgba(126,86,134,.7) !important;  
}

table.table-striped > tbody > tr > td{
  border: 2px solid #eeeeee !important;
  border-collapse: collapse;
  color: black;
  padding: 3px;
  vertical-align: middle;
  text-align: center;
  background-color: white;
}

thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
}
tfoot>tr>th{
  text-align:center;
}
td:hover {
  overflow: visible;
}
table > thead > tr > th{
  border:2px solid #f4f4f4;
  color: white;
}
	#tableResume > thead > tr > th{
		 border: 2px solid black;
	}
	#tableResume > tbody > tr > td{
		 cursor: pointer;
		 border: 2px solid black;
	}

#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
</section>
@endsection


@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 50%;">
			<span style="font-size: 40px"><i class="fa fa-spinner fa-spin" id="loadingDetail" style="font-size: 80px;"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-md-2">
			<div class="input-group date">
				<div class="input-group-addon bg-green" style="border-color: #00a65a">
					<i class="fa fa-calendar"></i>
				</div>
				<input type="text" class="form-control datepicker" id="tgl" onchange="drawChart()" placeholder="Select Date" style="border-color: #00a65a">
			</div>
		</div>
		<div class="col-md-12" style="padding-top: 10px">
			<table class="table table-bordered" style="width: 100%;margin-top: 0px !important" id="tableResume">
				
			</table>
			<!-- <div class="nav-tabs-custom">
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

				</div>
			</div>
-->
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
										<th>ID</th>
										<th>Name</th>
										<th>Dept</th>
										<th>Sect</th>
										<th>Group</th>
										<th>Shift</th>
										<th>Attend Code</th>
										<th>Time In</th>
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

	var detail_all_shift_1 = [];
	var detail_all_shift_2 = [];
	var detail_all_shift_3 = [];
	var detail_hadir = [];
	var detail_tidak_hadir = [];
	// var detail_off = [];
	var detail_cuti = [];
	var detail_sakit = [];
	var detail_izin = [];
	var detail_alpa = [];

	function drawChart(){
		var tanggal = $('#tgl').val();
		$("#loading").show();
		
		var data = {
			tgl:tanggal
		}

		$.get('{{ url("fetch/report/absence") }}', data, function(result, status, xhr) {  
			$("#loading").hide();

			// if(result.absence.length > 0){
				// absences = result.absence;

				// var counts = {};
				
				// for(var i = 0; i < absences.length; i++){
				// 	var key = absences[i].Attend_Code;
				// 	if(!counts[key]) counts[key] = 0;
				// 	counts[key]++;
				// }

				// shift = Object.keys(counts);
				// jml = Object.values(counts);

				// $('#tidak_ada_data').append().empty();

				// var titleChart = result.titleChart;

				// Highcharts.chart('absence', {
				// 	chart: {
				// 		type: 'column'
				// 	},
				// 	title: {
				// 		text: '<span style="font-size: 18pt;">Absence Data</span><br><center><span style="color: rgba(96, 92, 168);">'+ titleChart +'</center></span>',
				// 		useHTML: true
				// 	},
				// 	xAxis: {
				// 		categories: shift
				// 	},
				// 	yAxis: {
				// 		title: {
				// 			text: 'Total Manpower'
				// 		}
				// 	},
				// 	legend : {
				// 		enabled: false
				// 	},
				// 	tooltip: {
				// 		headerFormat: '',
				// 		pointFormat: '<span style="color:{point.color}">{point.category}</span>: <b>{point.y}</b> <br/>'
				// 	},
				// 	plotOptions: {
				// 		series: {
				// 			cursor: 'pointer',
				// 			point: {
				// 				events: {
				// 					click: function (event) {
				// 						showDetail(event.point.category, result.tgl);
				// 					}
				// 				}
				// 			},
				// 			borderWidth: 0,
				// 			dataLabels: {
				// 				enabled: true,
				// 				format: '{point.y}'
				// 			}
				// 		}
				// 	},
				// 	credits: {
				// 		enabled: false
				// 	},
				// 	series: [
				// 	{
				// 		"colorByPoint": true,
				// 		name: shift,
				// 		data: jml,
				// 	}
				// 	]
				// });

				var all_shift_1 = 0;
				var all_shift_2 = 0;
				var all_shift_3 = 0;
				var all_presentase = 0;

				var all_total = 0;

				var hadir_shift_1 = 0;
				var hadir_shift_2 = 0;
				var hadir_shift_3 = 0;
				var hadir_presentase = 0;

				var tidak_hadir_shift_1 = 0;
				var tidak_hadir_shift_2 = 0;
				var tidak_hadir_shift_3 = 0;
				var tidak_hadir_presentase = 0;

				var cuti_shift_1 = 0;
				var cuti_shift_2 = 0;
				var cuti_shift_3 = 0;
				var cuti_presentase = 0;

				var sakit_shift_1 = 0;
				var sakit_shift_2 = 0;
				var sakit_shift_3 = 0;
				var sakit_presentase = 0;

				// var off_shift_1 = 0;
				// var off_shift_2 = 0;
				// var off_shift_3 = 0;
				// var off_presentase = 0;

				var sakit_shift_1 = 0;
				var sakit_shift_2 = 0;
				var sakit_shift_3 = 0;
				var sakit_presentase = 0;

				var izin_shift_1 = 0;
				var izin_shift_2 = 0;
				var izin_shift_3 = 0;
				var izin_presentase = 0;

				var alpa_shift_1 = 0;
				var alpa_shift_2 = 0;
				var alpa_shift_3 = 0;
				var alpa_presentase = 0;

				var cuti_shift_1 = 0;
				var cuti_shift_2 = 0;
				var cuti_shift_3 = 0;
				var cuti_presentase = 0;

				detail_all_shift_1 = [];
				detail_all_shift_2 = [];
				detail_all_shift_3 = [];
				detail_hadir = [];
				detail_tidak_hadir = [];
				// detail_off = [];
				detail_cuti = [];
				detail_sakit = [];
				detail_izin = [];
				detail_alpa = [];

				for(var i = 0; i < result.absenceResume.length; i++){

					if(result.absenceResume[i].shiftdaily_code.match(/OFF/gi)){
						// if (result.absenceResume[i].shiftdaily_code.match(/Shift_1/gi)) {
						// 	off_shift_1++;
						// 	off_presentase++;
						// 	all_shift_1++;
						// 	detail_all_shift_1.push({
						// 		employee_id: result.absenceResume[i].employee_id,
						// 		name:result.absenceResume[i].name,
						// 		dept: result.absenceResume[i].department_shortname,
						// 		shift: result.absenceResume[i].shiftdaily_code,
						// 		attend_code:result.absenceResume[i].attend_code,
						// 		time_in:result.absenceResume[i].time_in,
						// 		section:result.absenceResume[i].section,
						// 		group:result.absenceResume[i].group});
						// }else if(result.absenceResume[i].shiftdaily_code.match(/Shift_2/gi)){
						// 	off_shift_2++;
						// 	off_presentase++;
						// 	all_shift_2++;
						// 	detail_all_shift_2.push({
						// 		employee_id: result.absenceResume[i].employee_id,
						// 		name:result.absenceResume[i].name,
						// 		dept: result.absenceResume[i].department_shortname,
						// 		shift: result.absenceResume[i].shiftdaily_code,
						// 		attend_code:result.absenceResume[i].attend_code,
						// 		time_in:result.absenceResume[i].time_in,
						// 		section:result.absenceResume[i].section,
						// 		group:result.absenceResume[i].group});
						// }else if(result.absenceResume[i].shiftdaily_code.match(/Shift_3/gi)){
						// 	off_shift_3++;
						// 	off_presentase++;
						// 	all_shift_3++;
						// 	detail_all_shift_3.push({
						// 		employee_id: result.absenceResume[i].employee_id,
						// 		name:result.absenceResume[i].name,
						// 		dept: result.absenceResume[i].department_shortname,
						// 		shift: result.absenceResume[i].shiftdaily_code,
						// 		attend_code:result.absenceResume[i].attend_code,
						// 		time_in:result.absenceResume[i].time_in,
						// 		section:result.absenceResume[i].section,
						// 		group:result.absenceResume[i].group});
						// }else{
						// 	off_shift_1++;
						// 	off_presentase++;
						// 	all_shift_1++;
						// 	detail_all_shift_1.push({
						// 		employee_id: result.absenceResume[i].employee_id,
						// 		name:result.absenceResume[i].name,
						// 		dept: result.absenceResume[i].department_shortname,
						// 		shift: result.absenceResume[i].shiftdaily_code,
						// 		attend_code:result.absenceResume[i].attend_code,
						// 		time_in:result.absenceResume[i].time_in,
						// 		section:result.absenceResume[i].section,
						// 		group:result.absenceResume[i].group});
						// }
						// detail_off.push({
						// 	employee_id: result.absenceResume[i].employee_id,
						// 	name:result.absenceResume[i].name,
						// 	dept: result.absenceResume[i].department_shortname,
						// 	shift: result.absenceResume[i].shiftdaily_code,
						// 	attend_code:result.absenceResume[i].attend_code,
						// 	time_in:result.absenceResume[i].time_in,
						// 	section:result.absenceResume[i].section,
						// 	group:result.absenceResume[i].group});
					}else{
						if (result.absenceResume[i].shiftdaily_code.match(/Shift_1/gi)) {
							all_shift_1++;
							detail_all_shift_1.push({
								employee_id: result.absenceResume[i].employee_id,
								name:result.absenceResume[i].name,
								dept: result.absenceResume[i].department_shortname,
								shift: result.absenceResume[i].shiftdaily_code,
								attend_code:result.absenceResume[i].attend_code,
								time_in:result.absenceResume[i].time_in,
								section:result.absenceResume[i].section,
								group:result.absenceResume[i].group});
							if (result.absenceResume[i].attend_code != null) {
								if (result.absenceResume[i].attend_code.match(/SAKIT/gi)) {
									sakit_shift_1++;
									sakit_presentase++;
									detail_sakit.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});

									tidak_hadir_shift_1++;
									tidak_hadir_presentase++;
									detail_tidak_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}
								if (result.absenceResume[i].attend_code.match(/Izin/gi)) {
									izin_shift_1++;
									izin_presentase++;
									detail_izin.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});

									tidak_hadir_shift_1++;
									tidak_hadir_presentase++;
									detail_tidak_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}
								if (result.absenceResume[i].attend_code.match(/ABS/gi)) {
									alpa_shift_1++;
									alpa_presentase++;
									detail_alpa.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});

									tidak_hadir_shift_1++;
									tidak_hadir_presentase++;
									detail_tidak_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}else{
									hadir_shift_1++;
									hadir_presentase++;
									detail_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}
								if (result.absenceResume[i].attend_code.match(/CUTI/gi)) {
									cuti_shift_1++;
									cuti_presentase++;
									detail_cuti.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});

									tidak_hadir_shift_1++;
									tidak_hadir_presentase++;
									detail_tidak_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}
								if (result.absenceResume[i].attend_code.match(/CK/gi)) {
									cuti_shift_1++;
									cuti_presentase++;
									detail_cuti.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});

									tidak_hadir_shift_1++;
									tidak_hadir_presentase++;
									detail_tidak_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}
							}
							else{
								tidak_hadir_shift_1++;
								tidak_hadir_presentase++;
								detail_tidak_hadir.push({
									employee_id: result.absenceResume[i].employee_id,
									name:result.absenceResume[i].name,
									dept: result.absenceResume[i].department_shortname,
									shift: result.absenceResume[i].shiftdaily_code,
									attend_code:result.absenceResume[i].attend_code,
									time_in:result.absenceResume[i].time_in,
									section:result.absenceResume[i].section,
									group:result.absenceResume[i].group});

								alpa_shift_1++;
								alpa_presentase++;
								detail_alpa.push({
									employee_id: result.absenceResume[i].employee_id,
									name:result.absenceResume[i].name,
									dept: result.absenceResume[i].department_shortname,
									shift: result.absenceResume[i].shiftdaily_code,
									attend_code:result.absenceResume[i].attend_code,
									time_in:result.absenceResume[i].time_in,
									section:result.absenceResume[i].section,
									group:result.absenceResume[i].group});
							}
						}else if (result.absenceResume[i].shiftdaily_code.match(/Shift_2/gi)) {
							all_shift_2++;
							detail_all_shift_2.push({
								employee_id: result.absenceResume[i].employee_id,
								name:result.absenceResume[i].name,
								dept: result.absenceResume[i].department_shortname,
								shift: result.absenceResume[i].shiftdaily_code,
								attend_code:result.absenceResume[i].attend_code,
								time_in:result.absenceResume[i].time_in,
								section:result.absenceResume[i].section,
								group:result.absenceResume[i].group});
							if (result.absenceResume[i].attend_code != null) {
								if (result.absenceResume[i].attend_code.match(/SAKIT/gi)) {
									sakit_shift_2++;
									sakit_presentase++;
									detail_sakit.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});

									tidak_hadir_shift_2++;
									tidak_hadir_presentase++;
									detail_tidak_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}
								if (result.absenceResume[i].attend_code.match(/Izin/gi)) {
									izin_shift_2++;
									izin_presentase++;
									detail_izin.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});

									tidak_hadir_shift_2++;
									tidak_hadir_presentase++;
									detail_tidak_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}
								if (result.absenceResume[i].attend_code.match(/ABS/gi)) {
									alpa_shift_2++;
									alpa_presentase++;
									detail_alpa.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});

									tidak_hadir_shift_2++;
									tidak_hadir_presentase++;
									detail_tidak_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}else{
									hadir_shift_2++;
									hadir_presentase++;
									detail_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}
								if (result.absenceResume[i].attend_code.match(/CUTI/gi)) {
									cuti_shift_2++;
									cuti_presentase++;
									detail_cuti.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});

									tidak_hadir_shift_2++;
									tidak_hadir_presentase++;
									detail_tidak_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}
								if (result.absenceResume[i].attend_code.match(/CK/gi)) {
									cuti_shift_2++;
									cuti_presentase++;
									detail_cuti.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});

									tidak_hadir_shift_2++;
									tidak_hadir_presentase++;
									detail_tidak_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}
							}
							else{
								tidak_hadir_shift_2++;
								tidak_hadir_presentase++;
								detail_tidak_hadir.push({
									employee_id: result.absenceResume[i].employee_id,
									name:result.absenceResume[i].name,
									dept: result.absenceResume[i].department_shortname,
									shift: result.absenceResume[i].shiftdaily_code,
									attend_code:result.absenceResume[i].attend_code,
									time_in:result.absenceResume[i].time_in,
									section:result.absenceResume[i].section,
									group:result.absenceResume[i].group});

								alpa_shift_2++;
								alpa_presentase++;
								detail_alpa.push({
									employee_id: result.absenceResume[i].employee_id,
									name:result.absenceResume[i].name,
									dept: result.absenceResume[i].department_shortname,
									shift: result.absenceResume[i].shiftdaily_code,
									attend_code:result.absenceResume[i].attend_code,
									time_in:result.absenceResume[i].time_in,
									section:result.absenceResume[i].section,
									group:result.absenceResume[i].group});
							}
						}else if (result.absenceResume[i].shiftdaily_code.match(/Shift_3/gi)){
							all_shift_3++;
							detail_all_shift_3.push({
								employee_id: result.absenceResume[i].employee_id,
								name:result.absenceResume[i].name,
								dept: result.absenceResume[i].department_shortname,
								shift: result.absenceResume[i].shiftdaily_code,
								attend_code:result.absenceResume[i].attend_code,
								time_in:result.absenceResume[i].time_in,
								section:result.absenceResume[i].section,
								group:result.absenceResume[i].group});
							if (result.absenceResume[i].attend_code != null) {
								if (result.absenceResume[i].attend_code.match(/SAKIT/gi)) {
									sakit_shift_3++;
									sakit_presentase++;
									detail_sakit.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});

									tidak_hadir_shift_3++;
									tidak_hadir_presentase++;
									detail_tidak_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}
								if (result.absenceResume[i].attend_code.match(/Izin/gi)) {
									izin_shift_3++;
									izin_presentase++;
									detail_izin.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});

									tidak_hadir_shift_3++;
									tidak_hadir_presentase++;
									detail_tidak_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}
								if (result.absenceResume[i].attend_code.match(/ABS/gi)) {
									alpa_shift_3++;
									alpa_presentase++;
									detail_alpa.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});

									tidak_hadir_shift_3++;
									tidak_hadir_presentase++;
									detail_tidak_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}else{
									hadir_shift_3++;
									hadir_presentase++;
									detail_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}
								if (result.absenceResume[i].attend_code.match(/CUTI/gi)) {
									cuti_shift_3++;
									cuti_presentase++;
									detail_cuti.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});

									tidak_hadir_shift_3++;
									tidak_hadir_presentase++;
									detail_tidak_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}
								if (result.absenceResume[i].attend_code.match(/CK/gi)) {
									cuti_shift_3++;
									cuti_presentase++;
									detail_cuti.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});

									tidak_hadir_shift_3++;
									tidak_hadir_presentase++;
									detail_tidak_hadir.push({
										employee_id: result.absenceResume[i].employee_id,
										name:result.absenceResume[i].name,
										dept: result.absenceResume[i].department_shortname,
										shift: result.absenceResume[i].shiftdaily_code,
										attend_code:result.absenceResume[i].attend_code,
										time_in:result.absenceResume[i].time_in,
										section:result.absenceResume[i].section,
										group:result.absenceResume[i].group});
								}
							}
							else{
								tidak_hadir_shift_3++;
								tidak_hadir_presentase++;
								detail_tidak_hadir.push({
									employee_id: result.absenceResume[i].employee_id,
									name:result.absenceResume[i].name,
									dept: result.absenceResume[i].department_shortname,
									shift: result.absenceResume[i].shiftdaily_code,
									attend_code:result.absenceResume[i].attend_code,
									time_in:result.absenceResume[i].time_in,
									section:result.absenceResume[i].section,
									group:result.absenceResume[i].group});

								alpa_shift_3++;
								alpa_presentase++;
								detail_alpa.push({
									employee_id: result.absenceResume[i].employee_id,
									name:result.absenceResume[i].name,
									dept: result.absenceResume[i].department_shortname,
									shift: result.absenceResume[i].shiftdaily_code,
									attend_code:result.absenceResume[i].attend_code,
									time_in:result.absenceResume[i].time_in,
									section:result.absenceResume[i].section,
									group:result.absenceResume[i].group});
							}
						}
					}
					all_presentase++;
					all_total++;
				}

				$('#tableResume').html("");
				var tableResume = '';

				var total_shift_1 = 0;
				var total_shift_2 = 0;
				var total_shift_3 = 0;

				var shift_1 = 'Shift_1';
				var shift_2 = 'Shift_2';
				var shift_3 = 'Shift_3';

				var type = '';

				tableResume += '<thead>';
					tableResume += '<tr>';
						tableResume += '<th style="width: 1%; padding: 0;vertical-align: middle;font-size: 18px;background-color: #5e00a6;color:white;">DETAIL</th>';
						tableResume += '<th style="width: 2%; padding: 0;vertical-align: middle;font-size: 18px;background-color: #5e00a6;color:white;">Shift 1</th>';
						tableResume += '<th style="width: 2%; padding: 0;vertical-align: middle;font-size: 18px;background-color: #5e00a6;color:white;">Shift 2</th>';
						tableResume += '<th style="width: 2%; padding: 0;vertical-align: middle;font-size: 18px;background-color: #5e00a6;color:white;">Shift 3</th>';
						tableResume += '<th style="width: 2%; padding: 0;vertical-align: middle;font-size: 18px;background-color: #5e00a6;color:white;">Total</th>';
						tableResume += '<th style="width: 2%; padding: 0;vertical-align: middle;font-size: 18px;background-color: #5e00a6;color:white;">Presentase</th>';
					tableResume += '</tr>';
				tableResume += '</thead>';

				tableResume += '<tbody>';
					// tableResume += '<tr>';
					// type = 'all';
					// 	tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold">Total Karyawan</td>';
					// 	tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_1+'\')">'+all_shift_1+'</td>';
					// 	tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_2+'\')">'+all_shift_2+'</td>';
					// 	tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_3+'\')">'+all_shift_3+'</td>';
					// 	tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+all_presentase+'</td>';
					// 	tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+((parseInt(all_total) / parseInt(all_presentase))*100).toFixed(2)+' %</td>';
					// tableResume += '</tr>';

					tableResume += '<tr>';
					type = 'hadir';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold">Hadir</td>';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_1+'\')">'+hadir_shift_1+'</td>';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_2+'\')">'+hadir_shift_2+'</td>';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_3+'\')">'+hadir_shift_3+'</td>';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+hadir_presentase+'</td>';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+((parseInt(hadir_presentase) / parseInt(all_total))*100).toFixed(2)+' %</td>';
					tableResume += '</tr>';

					tableResume += '<tr>';
					type = 'tidak_hadir';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold">Tidak Hadir</td>';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_1+'\')">'+tidak_hadir_shift_1+'</td>';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_2+'\')">'+tidak_hadir_shift_2+'</td>';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_3+'\')">'+tidak_hadir_shift_3+'</td>';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+tidak_hadir_presentase+'</td>';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+((parseInt(tidak_hadir_presentase) / parseInt(all_total))*100).toFixed(2)+' %</td>';
					tableResume += '</tr>';

					// tableResume += '<tr>';
					// type = 'off';
					// 	tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold">OFF</td>';
					// 	tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+off_shift_1+'</td>';
					// 	tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+off_shift_2+'</td>';
					// 	tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+off_shift_3+'</td>';
					// 	tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_1+'\')">'+off_presentase+'</td>';
					// 	tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+((parseInt(off_presentase) / parseInt(all_total))*100).toFixed(2)+' %</td>';
					// tableResume += '</tr>';

					tableResume += '<tr>';
					type = 'all';
						tableResume += '<td style="background-color: #00a65a; color: white; font-size: 16px;font-weight: bold">Total</td>';
						tableResume += '<td style="background-color: #00a65a; color: white; font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_1+'\')">'+all_shift_1+'</td>';
						tableResume += '<td style="background-color: #00a65a; color: white; font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_2+'\')">'+all_shift_2+'</td>';
						tableResume += '<td style="background-color: #00a65a; color: white; font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_3+'\')">'+all_shift_3+'</td>';
						tableResume += '<td style="background-color: #00a65a; color: white; font-size: 16px;font-weight: bold">'+all_presentase+'</td>';
						tableResume += '<td style="background-color: #00a65a; color: white; font-size: 16px;font-weight: bold">'+((parseInt(all_presentase) / parseInt(all_total))*100).toFixed(2)+' %</td>';
					tableResume += '</tr>';

					tableResume += '<tr>';
					tableResume += '<td colspan="6" style="background-color: #0056a6; color:white; font-size: 16px;font-weight: bold">DETAIL ATTENDANCE</td>';
					tableResume += '</tr>';

					tableResume += '<tr>';
					type = 'cuti';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold">Cuti</td>';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_1+'\')">'+cuti_shift_1+'</td>';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_2+'\')">'+cuti_shift_2+'</td>';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_3+'\')">'+cuti_shift_3+'</td>';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+cuti_presentase+'</td>';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+((parseInt(cuti_presentase) / parseInt(all_total))*100).toFixed(2)+' %</td>';
					tableResume += '</tr>';

					tableResume += '<tr>';
					type = 'sakit';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold">Sakit</td>';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_1+'\')">'+sakit_shift_1+'</td>';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_2+'\')">'+sakit_shift_2+'</td>';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_3+'\')">'+sakit_shift_3+'</td>';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+sakit_presentase+'</td>';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+((parseInt(sakit_presentase) / parseInt(all_total))*100).toFixed(2)+' %</td>';
					tableResume += '</tr>';

					tableResume += '<tr>';
					type = 'izin';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold">Izin</td>';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_1+'\')">'+izin_shift_1+'</td>';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_2+'\')">'+izin_shift_2+'</td>';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_3+'\')">'+izin_shift_3+'</td>';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+izin_presentase+'</td>';
						tableResume += '<td style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+((parseInt(izin_presentase) / parseInt(all_total))*100).toFixed(2)+' %</td>';
					tableResume += '</tr>';

					tableResume += '<tr>';
					type = 'alpa';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold">Alpa</td>';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_1+'\')">'+alpa_shift_1+'</td>';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_2+'\')">'+alpa_shift_2+'</td>';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold" onclick="showDetail(\''+type+'\',\''+shift_3+'\')">'+alpa_shift_3+'</td>';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+alpa_presentase+'</td>';
						tableResume += '<td style="background-color: rgb(242, 242, 242); color: rgb(0,0,0); font-size: 16px;font-weight: bold">'+((parseInt(alpa_presentase) / parseInt(all_total))*100).toFixed(2)+' %</td>';
					tableResume += '</tr>';
				tableResume += '</tbody>';
				$('#tableResume').append(tableResume);

			// }else{
			// 	$('#absence').append().empty();
			// 	$('#tidak_ada_data').append().empty();
			// 	$('#tidak_ada_data').append('<br><div class="alert alert-warning alert-dismissible" data-dismiss="alert" aria-hidden="true" style="margin-right: 3.3%;margin-left: 2%"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h4><i class="icon fa fa-warning"></i> Data Hari ini belum diupload!</h4></div>');
			// }		

		});

	}

	function showDetail(type, shift) {
		$('#tabel_detail').DataTable().clear();
		$('#tabel_detail').DataTable().destroy();

		// var tanggal = parseInt(tgl.slice(0, 2));
		// var bulan = parseInt(tgl.slice(3, 5));
		// var tahun = tgl.slice(6, 10);
		// var bulanText = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

		$("#body_detail").empty();
		body = "";
		if (type == 'all') {
			var shifts = shift.split('_');
			var ss = shifts[0]+' '+shifts[1];
			var condition = 'All Employee '+ss;
			if (shift === 'Shift_1'){
				$.each(detail_all_shift_1, function(index, value){
					body += "<tr>";
					body += "<td>"+value.employee_id+"</td>";
					body += "<td>"+value.name+"</td>";
					body += "<td>"+(value.dept || "")+"</td>";
					body += "<td>"+(value.section || "")+"</td>";
					body += "<td>"+(value.group || "")+"</td>";
					body += "<td>"+value.shift+"</td>";
					body += "<td>"+(value.attend_code || "")+"</td>";
					body += "<td>"+(value.time_in || "")+"</td>";
					body += "</tr>";
				});
			}else if (shift === 'Shift_2'){
				$.each(detail_all_shift_2, function(index, value){
					body += "<tr>";
					body += "<td>"+value.employee_id+"</td>";
					body += "<td>"+value.name+"</td>";
					body += "<td>"+(value.dept || "")+"</td>";
					body += "<td>"+(value.section || "")+"</td>";
					body += "<td>"+(value.group || "")+"</td>";
					body += "<td>"+value.shift+"</td>";
					body += "<td>"+(value.attend_code || "")+"</td>";
					body += "<td>"+(value.time_in || "")+"</td>";
					body += "</tr>";
				});
			}else if (shift === 'Shift_3'){
				$.each(detail_all_shift_3, function(index, value){
					body += "<tr>";
					body += "<td>"+value.employee_id+"</td>";
					body += "<td>"+value.name+"</td>";
					body += "<td>"+(value.dept || "")+"</td>";
					body += "<td>"+(value.section || "")+"</td>";
					body += "<td>"+(value.group || "")+"</td>";
					body += "<td>"+value.shift+"</td>";
					body += "<td>"+(value.attend_code || "")+"</td>";
					body += "<td>"+(value.time_in || "")+"</td>";
					body += "</tr>";
				});
			}
		}else if (type == 'hadir') {
			var shifts = shift.split('_');
			var ss = shifts[0]+' '+shifts[1];
			var condition = 'Present Employee '+ss;
			$.each(detail_hadir, function(index, value){
				var shifts = new RegExp(shift, 'g');
				if (value.shift.match(/OFF/gi)) {

				}else{
					if (value.shift.match(shifts)){
						if (value.attend_code.match(/ABS/gi)) {

						}else{
							body += "<tr>";
							body += "<td>"+value.employee_id+"</td>";
							body += "<td>"+value.name+"</td>";
							body += "<td>"+(value.dept || "")+"</td>";
							body += "<td>"+(value.section || "")+"</td>";
							body += "<td>"+(value.group || "")+"</td>";
							body += "<td>"+value.shift+"</td>";
							body += "<td>"+(value.attend_code || "")+"</td>";
							body += "<td>"+(value.time_in || "")+"</td>";
							body += "</tr>";
						}
					}
				}
			});
		}else if (type == 'tidak_hadir') {
			var shifts = shift.split('_');
			var ss = shifts[0]+' '+shifts[1];
			var condition = 'Absence Employee '+ss;
			$.each(detail_tidak_hadir, function(index, value){
				var shifts = new RegExp(shift, 'g');
				if (value.shift.match(/OFF/gi)) {

				}else{
					if (value.shift.match(shifts)){
						body += "<tr>";
						body += "<td>"+value.employee_id+"</td>";
						body += "<td>"+value.name+"</td>";
						body += "<td>"+(value.dept || "")+"</td>";
						body += "<td>"+(value.section || "")+"</td>";
						body += "<td>"+(value.group || "")+"</td>";
						body += "<td>"+value.shift+"</td>";
						body += "<td>"+(value.attend_code || "")+"</td>";
						body += "<td>"+(value.time_in || "")+"</td>";
						body += "</tr>";
					}
				}
			});
		}
		// else if (type == 'off') {
		// 	$.each(detail_off, function(index, value){
		// 		body += "<tr>";
		// 		body += "<td>"+value.employee_id+"</td>";
		// 		body += "<td>"+value.name+"</td>";
		// 		body += "<td>"+(value.dept || "")+"</td>";
		// 		body += "<td>"+(value.section || "")+"</td>";
		// 		body += "<td>"+(value.group || "")+"</td>";
		// 		body += "<td>"+value.shift+"</td>";
		// 		body += "<td>"+(value.attend_code || "")+"</td>";
		// 		body += "<td>"+(value.time_in || "")+"</td>";
		// 		body += "</tr>";
		// 	});
		// }
		else if(type == 'cuti'){
			var shifts = shift.split('_');
			var ss = shifts[0]+' '+shifts[1];
			var condition = 'Employee Cuti '+ss;
			$.each(detail_cuti, function(index, value){
				var shifts = new RegExp(shift, 'g');
				if (value.shift.match(/OFF/gi)) {
				}else{
					if (value.shift.match(shifts)){
						body += "<tr>";
						body += "<td>"+value.employee_id+"</td>";
						body += "<td>"+value.name+"</td>";
						body += "<td>"+(value.dept || "")+"</td>";
						body += "<td>"+(value.section || "")+"</td>";
						body += "<td>"+(value.group || "")+"</td>";
						body += "<td>"+value.shift+"</td>";
						body += "<td>"+(value.attend_code || "")+"</td>";
						body += "<td>"+(value.time_in || "")+"</td>";
						body += "</tr>";
					}
				}
			})
		}else if(type == 'sakit'){
			var shifts = shift.split('_');
			var ss = shifts[0]+' '+shifts[1];
			var condition = 'Employee Sakit '+ss;
			$.each(detail_sakit, function(index, value){
				var shifts = new RegExp(shift, 'g');
				if (value.shift.match(/OFF/gi)) {
				}else{
					if (value.shift.match(shifts)){
						body += "<tr>";
						body += "<td>"+value.employee_id+"</td>";
						body += "<td>"+value.name+"</td>";
						body += "<td>"+(value.dept || "")+"</td>";
						body += "<td>"+(value.section || "")+"</td>";
						body += "<td>"+(value.group || "")+"</td>";
						body += "<td>"+value.shift+"</td>";
						body += "<td>"+(value.attend_code || "")+"</td>";
						body += "<td>"+(value.time_in || "")+"</td>";
						body += "</tr>";
					}
				}
			})
		}else if(type == 'izin'){var shifts = shift.split('_');
			var ss = shifts[0]+' '+shifts[1];
			var condition = 'Employee Izin '+ss;
			$.each(detail_izin, function(index, value){
				var shifts = new RegExp(shift, 'g');
				if (value.shift.match(/OFF/gi)) {
				}else{
					if (value.shift.match(shifts)){
						body += "<tr>";
						body += "<td>"+value.employee_id+"</td>";
						body += "<td>"+value.name+"</td>";
						body += "<td>"+(value.dept || "")+"</td>";
						body += "<td>"+(value.section || "")+"</td>";
						body += "<td>"+(value.group || "")+"</td>";
						body += "<td>"+value.shift+"</td>";
						body += "<td>"+(value.attend_code || "")+"</td>";
						body += "<td>"+(value.time_in || "")+"</td>";
						body += "</tr>";
					}
				}
			})
		}else if(type == 'alpa'){
			var shifts = shift.split('_');
			var ss = shifts[0]+' '+shifts[1];
			var condition = 'Employee Absence '+ss;
			$.each(detail_alpa, function(index, value){
				var shifts = new RegExp(shift, 'g');
				if (value.shift.match(/OFF/gi)) {
				}else{
					if (value.shift.match(shifts)){
						body += "<tr>";
						body += "<td>"+value.employee_id+"</td>";
						body += "<td>"+value.name+"</td>";
						body += "<td>"+(value.dept || "")+"</td>";
						body += "<td>"+(value.section || "")+"</td>";
						body += "<td>"+(value.group || "")+"</td>";
						body += "<td>"+value.shift+"</td>";
						body += "<td>"+(value.attend_code || "")+"</td>";
						body += "<td>"+(value.time_in || "")+"</td>";
						body += "</tr>";
					}
				}
			})
		}
		

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
		// $('#judul_table').append('<center>Absence '+shift+' in '+tanggal+' '+bulanText[bulan-1]+' '+tahun+'<center>');
		$('#judul_table').append('<center><b>Detail '+condition+'</b><center>');

	}

</script>


@stop