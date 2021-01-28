@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	input {
		line-height: 22px;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
		vertical-align: middle;
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
		font-size: 18px;
		padding-top: 1px;
		padding-bottom: 1px;
		border:1px solid black;
		background-color: rgba(126,86,134);
	}
	table.table-bordered > tbody > tr > td{
		font-size: 16px;
		border:1px solid black;
		padding-top: 3px;
		padding-bottom: 3px;
		background-color: #8CD790;
		color: #000;
	}
	table.table-bordered > tfoot > tr > th{
		font-size: 16px;
		border:1px solid black;
		background-color: #ffffc2;
	}

	.sedang {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: sedang 1s infinite;  /* Safari 4+ */
		-moz-animation: sedang 1s infinite;  /* Fx 5+ */
		-o-animation: sedang 1s infinite;  /* Opera 12+ */
		animation: sedang 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes sedang {
		0%, 49% {
			background: #ff0033;
			color: white;
		}
		50%, 100% {
			background-color: #ffccff;
		}
	}

	.dataTables_info,
	.dataTables_length {
		color: white;
	}

	div.dataTables_filter label, 
     div.dataTables_wrapper div.dataTables_info {
	     color: white;
	}

	 div#tableDetail_info.dataTables_info,
	 div#tableDetail_filter.dataTables_filter label,
	 div#tableDetail_wrapper.dataTables_wrapper{
		color: black;
	}

	#tableDetail_info.dataTables_info,
	#tableDetail_info.dataTables_length {
		color: black;
	}

	div#tableDetailCheck_info.dataTables_info,
	 div#tableDetailCheck_filter.dataTables_filter label,
	 div#tableDetailCheck_wrapper.dataTables_wrapper{
		color: black;
	}

	#tableDetailCheck_info.dataTables_info,
	#tableDetailCheck_info.dataTables_length {
		color: black;
	}

	#tableTotalOfc tr td {
		cursor: pointer;
	}

	#tableTotalPrd tr td {
		cursor: pointer;
	}

</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<form method="GET" action="{{ action('TemperatureController@indexBodyTempMonitoring') }}">
					<div class="col-xs-2" style="padding-right: 0;">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal_from" name="tanggal_from" placeholder="Select Date" onchange="fetchTemperature()">
						</div>
					</div>
					<div class="col-md-3" style="padding-left: 3px;padding-right: 0px">
						<div class="input-group">
							<div class="input-group-addon bg-blue">
								<i class="fa fa-search"></i>
							</div>
							<select class="form-control select2" multiple="multiple" id="group" data-placeholder="Select Group" style="border-color: #605ca8" onchange="fetchTemperature()">
								@foreach($group as $group)
								<option value="{{ $group->grp }}">{{ $group->grp }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 1vw	;font-size: 1vw;color: white"></div>
				</form>
			</div>
		</div>
		<div class="col-xs-12" style="padding-bottom: 5px;">
			<div class="row">
				<div class="col-xs-5">
					<span style="color: white; font-size: 1.7vw; font-weight: bold;"><i class="fa fa-caret-right"></i> Office</span>
					<table class="table table-bordered" id="tableTotalOfc" style="margin-bottom: 5px;">
						<thead>
							<tr>
								<th style="width:2%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Shift Schedule</th>
								<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Hadir</th>
								<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Belum Hadir</th>
								<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw;border-bottom: 2px solid black">Total</th>
							</tr>
						</thead>
						<tbody id="tableTotalBodyOfc">
							<tr>
								<td style="font-size: 1.7vw; font-weight: bold;color: black;" id="">Shift 1</td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black;" id="total_check_ofc_1"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black;" id="total_uncheck_ofc_1"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black;" id="total_person_ofc_1"></td>
							</tr>
							<tr>
								<td style="font-size: 1.7vw; font-weight: bold;color: black;" id="">Shift 2</td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black;" id="total_check_ofc_2"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black;" id="total_uncheck_ofc_2"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black;" id="total_person_ofc_2"></td>
							</tr>
							<!-- <tr>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="">Shift 3</td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_check_ofc_3"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_uncheck_ofc_3"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_person_ofc_3"></td>
							</tr> -->
						</tbody>
					</table>
					<span style="color: white; font-size: 1.7vw; font-weight: bold;"><i class="fa fa-caret-right"></i> Production</span>
					<table class="table table-bordered" id="tableTotalPrd" style="margin-bottom: 5px;">
						<thead>
							<tr>
								<th style="width:2%; text-align: center;color: white; font-size: 1.2vw;">Shift Schedule</th>
								<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw;">Hadir</th>
								<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw">Belum Hadir</th>
								<th style="width: 3%; text-align: center;color: white; font-size: 1.2vw">Total</th>
							</tr>
						</thead>
						<tbody id="tableTotalBodyPrd">
							<tr>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="">Shift 1</td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_check_prd_1"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_uncheck_prd_1"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_person_prd_1"></td>
							</tr>
							<tr>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="">Shift 2</td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_check_prd_2"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_uncheck_prd_2"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_person_prd_2"></td>
							</tr>
							<tr>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="">Shift 3</td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_check_prd_3"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_uncheck_prd_3"></td>
								<td style="font-size: 1.7vw; font-weight: bold;color: black" id="total_person_prd_3"></td>
							</tr>
						</tbody>
					</table>
					<span style="color: white; font-size: 1.7vw; font-weight: bold;"><i class="fa fa-caret-right"></i> Detail Cek Suhu >= 37.5 °C</span>
					<table class="table table-bordered" id="tableAbnormal" style="margin-bottom: 5px;">
						<thead>
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 3%;">ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 3%;">Dept</th>
								<th style="width: 3%;">Shift</th>
								<th style="width: 2%;">Time</th>
								<th style="width: 2%;">Temp</th>
							</tr>			
						</thead>
						<tbody id="tableAbnormalBody">
						</tbody>
					</table>
				</div>
				<div class="col-xs-7">
					<div id="container1" class="container1" style="width: 100%;height: 600px"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="padding-bottom: 15px" class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loadingDetail" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr style="color: white">
								<th style="width: 1%;">#</th>
								<th style="width: 3%;">ID</th>
								<th style="width: 9%;">Name</th>
								<th style="width: 9%;">Dept</th>
								<th style="width: 9%;">Sect</th>
								<th style="width: 9%;">Group</th>
								<th style="width: 9%;">Point</th>
								<th style="width: 3%;">Time</th>
								<th style="width: 2%;">Temp</th>
							</tr>
						</thead>
						<tbody id="tableDetailBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalDetailCheck">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="padding-bottom: 15px" class="modal-title" id="modalDetailTitleCheck"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center>
						<i class="fa fa-spinner fa-spin" id="loadingDetailCheck" style="font-size: 80px;"></i>
					</center>
					<table class="table table-hover table-bordered table-striped" id="tableDetailCheck">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr style="color: white">
								<th style="color:white;width: 1%; font-size: 1.2vw;">#</th>
								<th style="color:white;width: 5%; font-size: 1.2vw; text-align: center;">ID</th>
								<th style="color:white;width: 30%; font-size: 1.2vw; text-align: center;">Name</th>
								<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Dept</th>
								<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Sect</th>
								<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Group</th>
								<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Shift</th>
								<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Attendance</th>
								<th style="color:white;width: 10%; font-size: 1.2vw; text-align: center;">Time In</th>
							</tr>
						</thead>
						<tbody id="tableDetailCheckBody">
						</tbody>
						<!-- <tfoot>
							<tr>
								<th colspan="5">Total Duration</th>
								<th id="totalDetail">9</th>
							</tr>
						</tfoot> -->
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var intervaltemp;

	jQuery(document).ready(function() {
		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$('.select2').select2();
		fetchTemperature();
		intervaltemp = setInterval(fetchTemperature,300000);
	});

	var detail_all = [];


	function fetchTemperature(){
		var tanggal_from = $('#tanggal_from').val();
		var group = $('#group').val();

		var data = {
			tanggal_from:tanggal_from,
			group:group,
			location:'{{$loc}}',
		}

		$.get('{{ url("fetch/temperature/minmoe_monitoring") }}', data, function(result, status, xhr) {
			if (xhr.status == 200) {
				if (result.status) {
					$('#tableNoCheck').DataTable().clear();
					$('#tableNoCheck').DataTable().destroy();
					// $('#tableNoCheckBody').html('');

					var index = 1;

					var check_ofc_1 = 0;
					var uncheck_ofc_1 = 0;
					var check_ofc_2 = 0;
					var uncheck_ofc_2 = 0;
					var total_ofc_1 = 0;
					var total_ofc_2 = 0;

					var check_prd_1 = 0;
					var uncheck_prd_1 = 0;
					var check_prd_2 = 0;
					var uncheck_prd_2 = 0;
					var check_prd_3 = 0;
					var uncheck_prd_3 = 0;
					var total_prd_1 = 0;
					var total_prd_2 = 0;
					var total_prd_3 = 0;

					var detail_check_ofc_1 = [];
					var detail_uncheck_ofc_1 = [];
					var detail_check_ofc_2 = [];
					var detail_uncheck_ofc_2 = [];
					var detail_total_ofc_1 = [];
					var detail_total_ofc_2 = [];

					var detail_check_prd_1 = [];
					var detail_uncheck_prd_1 = [];
					var detail_check_prd_2 = [];
					var detail_uncheck_prd_2 = [];
					var detail_check_prd_3 = [];
					var detail_uncheck_prd_3 = [];
					var detail_total_prd_1 = [];
					var detail_total_prd_2 = [];
					var detail_total_prd_3 = [];

					var detail_abnormal = [];
					// var detail_all = [];

					var resultData = "";

					var dataPersonTemperature = [];
					var dataTemperature = [];

					$.each(result.attendance, function(key, value) {
						var emp_no = value.employee_id;
						if (value.temperature != '-' && value.temperature >= 37.5) {
							detail_abnormal.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups,temp:value.temperature});
						}
						detail_all.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups,temp:value.temperature});
						if (value.remark == 'OFC' || value.remark == 'Jps') {
							if (value.checks == null) {

								if (value.shiftdaily_code.match(/Shift_1/gi)) {
									uncheck_ofc_1++;
									total_ofc_1++;
									detail_uncheck_ofc_1.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups,section:value.section,group:value.groups});
									detail_total_ofc_1.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
								}else if(value.shiftdaily_code.match(/Shift_2/gi)){
									uncheck_ofc_2++;
									total_ofc_2++;
									detail_uncheck_ofc_2.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
									detail_total_ofc_2.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
								}else{
									uncheck_ofc_1++;
									total_ofc_1++;
									detail_uncheck_ofc_1.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
									detail_total_ofc_1.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
								}
							}else{

								if (value.shiftdaily_code.match(/Shift_1/gi)) {
									check_ofc_1++;
									total_ofc_1++;
									detail_check_ofc_1.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
									detail_total_ofc_1.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
								}else if(value.shiftdaily_code.match(/Shift_2/gi)){
									check_ofc_2++;
									total_ofc_2++;
									detail_check_ofc_2.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
									detail_total_ofc_2.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
								}else if(value.shiftdaily_code.match(/Shift_3/gi)){
									check_ofc_3++;
									total_ofc_3++;
									detail_check_ofc_3.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
									detail_total_ofc_3.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
								}else{
									check_ofc_1++;
									total_ofc_1++;
									detail_check_ofc_1.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
									detail_total_ofc_1.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
								}
							}
						}else{
							if (value.checks == null) {

								if (value.shiftdaily_code.match(/Shift_1/gi)) {
									uncheck_prd_1++;
									total_prd_1++;
									detail_uncheck_prd_1.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
									detail_total_prd_1.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
								}else if(value.shiftdaily_code.match(/Shift_2/gi)){
									uncheck_prd_2++;
									total_prd_2++;
									detail_uncheck_prd_2.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
									detail_total_prd_2.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
								}else if(value.shiftdaily_code.match(/Shift_3/gi)){
									uncheck_prd_3++;
									total_prd_3++;
									detail_uncheck_prd_3.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
									detail_total_prd_3.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
								}else{
									uncheck_prd_1++;
									total_prd_1++;
									detail_uncheck_prd_1.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
									detail_total_prd_1.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
								}
							}else{

								if (value.shiftdaily_code.match(/Shift_1/gi)) {
									check_prd_1++;
									total_prd_1++;
									detail_check_prd_1.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
									detail_total_prd_1.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
								}else if(value.shiftdaily_code.match(/Shift_2/gi)){
									check_prd_2++;
									total_prd_2++;
									detail_check_prd_2.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
									detail_total_prd_2.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
								}else if(value.shiftdaily_code.match(/Shift_3/gi)){
									check_prd_3++;
									total_prd_3++;
									detail_check_prd_3.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
									detail_total_prd_3.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
								}else{
									check_prd_1++;
									total_prd_1++;
									detail_check_prd_1.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
									detail_total_prd_1.push({employee_id: value.employee_id,name:value.name, dept: value.department_shortname, shift: value.shiftdaily_code,attend_code:value.attend_code,time_in:value.time_in,section:value.section,group:value.groups});
								}
							}
						}
						
					});

					$('#total_check_ofc_1').html(check_ofc_1);
					$('#total_uncheck_ofc_1').html(uncheck_ofc_1);
					$('#total_person_ofc_1').html(total_ofc_1);

					$('#total_check_ofc_2').html(check_ofc_2);
					$('#total_uncheck_ofc_2').html(uncheck_ofc_2);
					$('#total_person_ofc_2').html(total_ofc_2);

					var elem_total_check_ofc_1 = document.getElementById('total_check_ofc_1');

					elem_total_check_ofc_1.addEventListener('click', function(){
					    checkDetails(detail_check_ofc_1);
					});

					var elem_total_uncheck_ofc_1 = document.getElementById('total_uncheck_ofc_1');

					elem_total_uncheck_ofc_1.addEventListener('click', function(){
					    checkDetails(detail_uncheck_ofc_1);
					});

					var elem_total_person_ofc_1 = document.getElementById('total_person_ofc_1');

					elem_total_person_ofc_1.addEventListener('click', function(){
					    checkDetails(detail_total_ofc_1);
					});

					var elem_total_check_ofc_2 = document.getElementById('total_check_ofc_2');

					elem_total_check_ofc_2.addEventListener('click', function(){
					    checkDetails(detail_check_ofc_2);
					});

					var elem_total_uncheck_ofc_2 = document.getElementById('total_uncheck_ofc_2');

					elem_total_uncheck_ofc_2.addEventListener('click', function(){
					    checkDetails(detail_uncheck_ofc_2);
					});

					var elem_total_person_ofc_2 = document.getElementById('total_person_ofc_2');

					elem_total_person_ofc_2.addEventListener('click', function(){
					    checkDetails(detail_total_ofc_2);
					});

					$('#total_check_prd_1').html(check_prd_1);
					$('#total_uncheck_prd_1').html(uncheck_prd_1);
					$('#total_person_prd_1').html(total_prd_1);

					$('#total_check_prd_2').html(check_prd_2);
					$('#total_uncheck_prd_2').html(uncheck_prd_2);
					$('#total_person_prd_2').html(total_prd_2);

					$('#total_check_prd_3').html(check_prd_3);
					$('#total_uncheck_prd_3').html(uncheck_prd_3);
					$('#total_person_prd_3').html(total_prd_3);

					var elem_total_check_prd_1 = document.getElementById('total_check_prd_1');

					elem_total_check_prd_1.addEventListener('click', function(){
					    checkDetails(detail_check_prd_1);
					});

					var elem_total_uncheck_prd_1 = document.getElementById('total_uncheck_prd_1');

					elem_total_uncheck_prd_1.addEventListener('click', function(){
					    checkDetails(detail_uncheck_prd_1);
					});

					var elem_total_person_prd_1 = document.getElementById('total_person_prd_1');

					elem_total_person_prd_1.addEventListener('click', function(){
					    checkDetails(detail_total_prd_1);
					});

					var elem_total_check_prd_2 = document.getElementById('total_check_prd_2');

					elem_total_check_prd_2.addEventListener('click', function(){
					    checkDetails(detail_check_prd_2);
					});

					var elem_total_uncheck_prd_2 = document.getElementById('total_uncheck_prd_2');

					elem_total_uncheck_prd_2.addEventListener('click', function(){
					    checkDetails(detail_uncheck_prd_2);
					});

					var elem_total_person_prd_2 = document.getElementById('total_person_prd_2');

					elem_total_person_prd_2.addEventListener('click', function(){
					    checkDetails(detail_total_prd_2);
					});

					var elem_total_check_prd_3 = document.getElementById('total_check_prd_3');

					elem_total_check_prd_3.addEventListener('click', function(){
					    checkDetails(detail_check_prd_3);
					});

					var elem_total_uncheck_prd_3 = document.getElementById('total_uncheck_prd_3');

					elem_total_uncheck_prd_3.addEventListener('click', function(){
					    checkDetails(detail_uncheck_prd_3);
					});

					var elem_total_person_prd_3 = document.getElementById('total_person_prd_3');

					elem_total_person_prd_3.addEventListener('click', function(){
					    checkDetails(detail_total_prd_3);
					});

					$('#tableAbnormalBody').html('');

					var index = 1;
					var resultDataAbnormal = "";

					$.each(detail_abnormal, function(key, value) {
						resultDataAbnormal += '<tr>';
						resultDataAbnormal += '<td class="sedang" style="font-size: 15px;vertical-align:middle; font-weight: bold; background-color: #ffccff">'+index+'</td>';
						resultDataAbnormal += '<td class="sedang" style="font-size: 15px;vertical-align:middle; font-weight: bold; background-color: #ffccff">'+value.employee_id+'</td>';
						resultDataAbnormal += '<td class="sedang" style="font-size: 15px;vertical-align:middle; font-weight: bold; background-color: #ffccff">'+ value.name +'</td>';
						resultDataAbnormal += '<td class="sedang" style="font-size: 15px;vertical-align:middle; font-weight: bold; background-color: #ffccff">'+ value.dept +'</td>';
						resultDataAbnormal += '<td class="sedang" style="font-size: 15px;vertical-align:middle; font-weight: bold; background-color: #ffccff">'+ value.shift +'</td>';
						resultDataAbnormal += '<td class="sedang" style="font-size: 15px;vertical-align:middle; font-weight: bold; background-color: #ffccff">'+ value.time_in +'</td>';
						resultDataAbnormal += '<td class="sedang" style="font-size: 15px;vertical-align:middle; font-weight: bold; background-color: #ffccff">'+ value.temp +'</td>';
						resultDataAbnormal += '</tr>';
						index++;
					});

					$('#tableAbnormalBody').append(resultDataAbnormal);

					var categories1 = [];
					var series1 = [];
					var temp = [];
					var counts = result.attendance.reduce((p, c) => {
					  var name = c.temperature;
					  if (!p.hasOwnProperty(name)) {
					    p[name] = 0;
					  }
					  p[name]++;
					  return p;
					}, {});

					// console.log(counts['36.5']);
					// $.each(counts, function(key, value) {
					// 	if (key != "-" && key != "null") {
					// 		categories1.push(key+' °C');
					// 		temp.push(parseFloat(value));
					// 		series1.push({y:parseFloat(value),key:key});
					// 	}
					// });

					$.each(result.datatoday, function(key, value) {
						// console.log(value);
						categories1.push(value.temperature+' °C');
						temp.push(parseFloat(value.temperature));
						series1.push({y:parseFloat(value.count),key:value.temperature});
					});

					var total = 0;
					for(var i = 0; i < temp.length; i++) {
						total += temp[i];
					}
					var avg = total / temp.length;

					$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Update: '+ getActualFullDate() +'</p>');

					var chart = Highcharts.chart('container1', {
						chart: {
							type: 'column',
							backgroundColor: null
						},
						title: {
							text: 'Employees Temperature Monitoring <br>On '+result.dateTitle,
							style: {
								fontSize: '25px',
								fontWeight: 'bold'
							}
						},
						yAxis: {
							title: {
								text: 'Count Person(s)'
							}
						},
						xAxis: {
							categories: categories1,
							type: 'category',
							gridLineWidth: 1,
							gridLineColor: 'RGB(204,255,255)',
							labels: {
								style: {
									fontSize: '20px'
								}
							},
						},
						credits: {
							enabled:false
						},
						plotOptions: {
							series:{
								dataLabels: {
									enabled: true,
									format: '{point.y}',
									style:{
										textOutline: false,
										fontSize: '20px'
									}
								},
								animation: false,
								pointPadding: 0.93,
								groupPadding: 0.93,
								borderWidth: 0.93,
								cursor: 'pointer',
								point: {
									events: {
										click: function () {
											fetchTemperatureDetail(this.key);
										}
									}
								}
							}
						},
						series: [{
							name:'Person(s)',
							type: 'column',
							data: series1,
							showInLegend: false,
							color: '#00a65a'
						}]

					});
				}else{
					alert('Failed to Retrieve Data');
				}
			}
		});		
}

function fetchTemperatureDetail(temperature){
	clearInterval(intervaltemp);
	$('#modalDetail').modal('show');
	$('#loadingDetail').show();
	$('#modalDetailTitle').html("");
	$('#tableDetail').hide();

	var tanggal_from = $('#tanggal_from').val();
	var group = $('#group').val();

	var data = {
		tanggal_from:tanggal_from,
		temperature:temperature,
		group:group,
		location:'{{$loc}}'
	}

	$.get('{{ url("fetch/temperature/detail_minmoe_monitoring") }}', data, function(result, status, xhr) {
		if(result.status){

			$('#tableDetailBody').html('');

			$('#tableDetail').DataTable().clear();
			$('#tableDetail').DataTable().destroy();

			var index = 1;
			var resultData = "";
			var total = 0;

			$.each(result.details, function(key, value) {
				// if (value.temp === temperature) {
					resultData += '<tr>';
					resultData += '<td>'+ index +'</td>';
					resultData += '<td>'+ value.employee_id +'</td>';
					resultData += '<td>'+ value.name +'</td>';
					resultData += '<td>'+ value.department_shortname +'</td>';
					resultData += '<td>'+ value.section +'</td>';
					resultData += '<td>'+ value.groups +'</td>';
					resultData += '<td>'+ value.point +'</td>';
					resultData += '<td>'+ value.date_in +'</td>';
					resultData += '<td>'+ value.temperature +' °C</td>';
					resultData += '</tr>';
					index += 1;
				// }
			});
			$('#tableDetailBody').append(resultData);
			$('#modalDetailTitle').html("<center><span style='font-size: 20px; font-weight: bold;'>Detail Employees on "+temperature+" °C</span></center>");

			$('#loadingDetail').hide();
			$('#tableDetail').show();
			var table = $('#tableDetail').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			intervaltemp = setInterval(fetchTemperature,300000);
		}
		else{
			alert('Attempt to retrieve data failed');
		}
	});
}

function checkDetails(checkParam) {
	clearInterval(intervaltemp);
	$('#modalDetailCheck').modal('show');
	$('#loadingDetailCheck').show();
	$('#modalDetailTitleCheck').html("");
	$('#tableDetailCheck').hide();

	$('#tableDetailCheckBody').html('');

	$('#tableDetailCheck').DataTable().clear();
	$('#tableDetailCheck').DataTable().destroy();

	var index = 1;
	var resultData = "";
	var total = 0;

	$.each(checkParam, function(key, value) {
		resultData += '<tr>';
		resultData += '<td>'+ index +'</td>';
		resultData += '<td>'+ value.employee_id +'</td>';
		resultData += '<td>'+ value.name +'</td>';
		resultData += '<td>'+ value.dept +'</td>';
		resultData += '<td>'+ value.section +'</td>';
		resultData += '<td>'+ value.group +'</td>';
		resultData += '<td>'+ value.shift +'</td>';
		resultData += '<td>'+ value.attend_code +'</td>';
		resultData += '<td>'+ value.time_in +'</td>';
		resultData += '</tr>';
		index += 1;
	});
	$('#tableDetailCheckBody').append(resultData);
	$('#modalDetailTitleCheck').html("<center><span style='font-size: 20px; font-weight: bold;'>Detail Employees</span></center>");

	$('#loadingDetailCheck').hide();
	$('#tableDetailCheck').show();
	var table = $('#tableDetailCheck').DataTable({
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
				}
				]
			},
			'paging': true,
			'lengthChange': true,
			'pageLength': 10,
			'searching': true	,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true
		});
	intervaltemp = setInterval(fetchTemperature,300000);
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
			[0, '#2a2a2b'],
			[1, '#3e3e40']
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
	return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
}
</script>
@endsection
