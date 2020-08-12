@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		vertical-align: middle;
	}
	tbody>tr>td{
		text-align:center;
		vertical-align: middle;
	}
	tfoot>tr>th{
		text-align:center;
		vertical-align: middle;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	#loading { display: none; }
	.nama_operator:hover{
		color: #33c570;
		cursor: pointer;
	}
	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <span class="text-purple">{{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<section class="content" style="padding-top: 0;">
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i><br>Loading...</span>
			</center>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12" style="margin-top: 20px">
			<button class="btn btn-warning" style="padding-left: 10px;font-weight: bold;" onclick="showModalSkillMaster();">Skill Master</button>
			<button class="btn btn-primary" onclick="showModalEmployeeAdjustment();" style="font-weight: bold;">Employee Adjustment</button>
			<button class="btn btn-success" onclick="showModalResume();" style="font-weight: bold;">Resume</button>
			<div class="pull-right">
				<div class="input-group">
					<select class="form-control select4" multiple="multiple" id="processSelect" data-placeholder="Select Process" onchange="changeProcess()">
						@foreach($process as $process)
						<option value="{{ $process->process }}">{{ $process->process }}</option>
						@endforeach
					</select>
					<input type="text" name="processFix" id="processFix" hidden>
					<button class="btn btn-success" onclick="fetchSkillMap()">Search</button>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-top: 20px">
			<div id="tableSkillMap"  style="overflow-x: scroll;">
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalSkillAdjusment">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center style="background-color: #ffac26;color: white">
					<span style="font-weight: bold; font-size: 1.5vw;">Skill Adjustment</span><br>
					<span style="font-weight: bold; font-size: 1.2vw;padding-top: 0px" id="employee_details"></span>
					<span style="font-size: 1.2vw;padding-top: 0px;background-color: red" id="warning_details"></span>
				</center>
				<hr>
				<div class="modal-body" style="min-height: 100px; padding-bottom: 5px;">
					<div class="col-xs-6">
						<div class="row" style="padding-right: 10px" id="container_detail"></div>
					</div>
					<div class="col-xs-6">
						<div class="row" style="padding-left: 10px" id="table_detail"></div>
					</div>
					<div class="col-xs-12" style="padding-top: 20px">
							<span style="font-weight: bold; font-size: 1.2vw;padding-top: 0px;padding-left: 0px" id="title_required"></span>
						<input type="hidden" id="required_skill_length">
						<div class="row" id="table_required"></div>
					</div>

					<div class="col-xs-12" style="padding-top: 10px">
						<span style="font-weight: bold; font-size: 1.2vw;padding-top: 0px;padding-left: 0px" id="title_other"></span>
						<input type="hidden" id="other_skill_length">
						<div class="row" id="table_other"></div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="col-xs-12">
						<div class="row" id="skillFooter">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalSkillMaster">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body">
					<div class="box-body">
						<div class="col-xs-6">
							<div class="col-xs-12">
								<center style="background-color: #ffac26;color: white">
									<span style="font-weight: bold; font-size: 3vw;">Skill Master</span><br>
								</center>
								<hr>
								<div class="row">
									<input type="hidden" id="condition" value="INPUT">
									<input type="hidden" id="id_skill">
									<div class="form-group">
										<label for="" class="col-sm-2 control-label">Skill Code<span class="text-red">*</span></label>
										<div class="col-sm-10">
											<input type="text"  class="form-control" name="skill_code" id="skill_code" placeholder="Masukkan Kode Skill">
										</div>
									</div>
									<div class="form-group" style="padding-top: 30px">
										<label for="" class="col-sm-2 control-label">Skill Name<span class="text-red">*</span></label>
										<div class="col-sm-10">
											<input type="text"  class="form-control" name="skill" id="skill" placeholder="Masukkan Nama Skill">
										</div>
									</div>
									<div class="form-group" style="padding-top: 30px">
										<label for="" class="col-sm-2 control-label">Process<span class="text-red">*</span></label>
										<div class="col-sm-10">
											<select class="form-control select2" data-placeholder="Select Process" name="process_choice" id="process_choice" style="width: 100%" onchange="processChoice(this.value)">
												<option value=""></option>
											</select>
										</div>
									</div>
									<div class="form-group" style="padding-top: 30px" id="process_new">
										<label for="" class="col-sm-2 control-label">New Process<span class="text-red">*</span></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" name="process" id="process" placeholder="Masukkan Proses Baru">
										</div>
									</div>
									<div class="form-group" style="padding-top: 30px">
										<label for="" class="col-sm-2 control-label">Required Value<span class="text-red">*</span></label>
										<div class="col-sm-10">
											<input type="number" class="form-control" name="value" id="value" placeholder="Masukkan Required Value">
										</div>
									</div>
								</div>
								<div class="form-group" style="padding-top: 20px">
									<button class="btn btn-danger pull-right" style="margin-left: 5px" onclick="clearMaster()"><b>CLEAR</b></button>
									<button class="btn btn-success pull-right" onclick="saveMaster()"><b>SAVE</b></button>
								</div>
							</div>
							<div class="col-xs-12" style="padding-top: 30px">
								<div class="row">
									<table id="tableMaster" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
										<thead style="background-color: rgb(126,86,134); color: #FFD700;">
											<tr>
												<th>Skill Code</th>
												<th>Skill Name</th>
												<th>Process</th>
												<th>Value</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody id="bodyTableMaster">
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-xs-6">
							<div class="col-xs-12">
								<center style="background-color: #ffac26;color: white">
									<span style="font-weight: bold; font-size: 3vw;">Value Description</span><br>
								</center>
								<hr>
								<div class="row">
									<input type="hidden" id="condition_value" value="INPUT">
									<input type="hidden" id="id_value">
									<div class="form-group">
										<label for="" class="col-sm-2 control-label">Skill Value<span class="text-red">*</span></label>
										<div class="col-sm-10">
											<input type="number"  class="form-control" name="skill_value" id="skill_value" placeholder="Masukkan Skill Value">
										</div>
									</div>
									<div class="form-group" style="padding-top: 30px">
										<label for="" class="col-sm-2 control-label">Desc.<span class="text-red">*</span></label>
										<div class="col-sm-10">
											<input type="text"  class="form-control" name="description" id="description" placeholder="Masukkan Deskripsi Value">
										</div>
									</div>
								</div>
								<div class="form-group" style="padding-top: 20px">
									<button class="btn btn-danger pull-right" style="margin-left: 5px" onclick="clearValue()"><b>CLEAR</b></button>
									<button class="btn btn-success pull-right" onclick="saveValue()"><b>SAVE</b></button>
								</div>
							</div>
							<div class="col-xs-12" style="padding-top: 30px">
								<div class="row">
									<table id="tableValue" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
										<thead style="background-color: rgb(126,86,134); color: #FFD700;">
											<tr>
												<th>Skill Value</th>
												<th>Description</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody id="bodyTableValue">
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="col-xs-12">
						<div class="row" id="skillFooter">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalEmployeeAdjustment">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center style="background-color: #ffac26;color: white">
					<span style="font-weight: bold; font-size: 3vw;">Employee Adjusment</span><br>
				</center>
				<hr>
				<div class="modal-body">
					<div class="box-body">
						<div class="col-xs-12">
							<div class="row">
								<input type="hidden" id="condition_adjustment" value="INPUT">
								<input type="hidden" id="id_employee">
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Employees<span class="text-red">*</span></label>
									<div class="col-sm-10">
										<select class="form-control select3" data-placeholder="Select Employees" name="employee_choice" id="employee_choice" style="width: 100%">
											<option value=""></option>
										</select>
									</div>
								</div>
								<div class="form-group" style="padding-top: 30px">
									<label for="" class="col-sm-2 control-label">Process<span class="text-red">*</span></label>
									<div class="col-sm-10">
										<select class="form-control select3" data-placeholder="Select Process" name="process_adjustment" id="process_adjustment" style="width: 100%">
											<option value=""></option>
										</select>
									</div>
								</div>
							</div>
							<div class="form-group" style="padding-top: 20px">
								<button class="btn btn-danger pull-left" onclick="clearEmployee()"><b>CLEAR</b></button>
								<button class="btn btn-success pull-right" onclick="saveEmployee()"><b>SAVE</b></button>
							</div>
						</div>
						<div class="col-xs-12" style="padding-top: 30px">
							<div class="row">
								<table id="tableEmployee" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
									<thead style="background-color: rgb(126,86,134); color: #FFD700;">
										<tr>
											<th>Employee ID</th>
											<th>Name</th>
											<th>Process</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody id="bodyTableEmployee">
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="col-xs-12">
						<div class="row" id="skillFooter">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalResume">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center style="background-color: #ffac26;color: white">
					<span style="font-weight: bold; font-size: 3vw;">Skill Resume</span><br>
				</center>
				<hr>
				<div class="modal-body" style="min-height: 100px; padding-bottom: 5px;">
					<div class="col-xs-12">
						<div class="row" style="padding-top: 0px">
							<div id="container_resume"></div>
						</div>
						<div class="row" style="padding-top: 20px">
							<table id="tableResume" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th>Skill</th>
										<th>Jumlah Nilai Skill ≥ 3</th>
										<th>Jumlah orang dengan nilai skill 1</th>
										<th>Jumlah orang dengan nilai skill 2</th>
										<th>Jumlah orang dengan nilai skill 3</th>
										<th>Jumlah orang dengan nilai skill 4</th>
										<th>Prosentase kekuatan proses dengan skill 3</th>
										<th>Prosentase kekuatan proses dengan skill 1-4</th>
									</tr>
								</thead>
								<tbody id="bodyTableResume">
								</tbody>
							</table>
						</div>
						<div class="row" style="padding-top: 20px">
							<div style="padding-bottom: 20px;">
								<center style="background-color: #ffac26;color: white">
									<span style="font-weight: bold; font-size: 2vw;">Skill yang Belum Terpenuhi</span>
								</center>
							</div>
							<table id="tableUnfulfilled" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th>Employee ID</th>
										<th>Name</th>
										<th>Process</th>
										<th>Skill Code</th>
										<th>Skill</th>
										<th>Current</th>
										<th>Required</th>
										<th>Remark</th>
									</tr>
								</thead>
								<tbody id="bodyTableUnfulfilled">
								</tbody>
							</table>
						</div>
						<div class="row" style="padding-top: 20px">
							<div style="padding-bottom: 20px;">
								<center style="background-color: #ffac26;color: white">
									<span style="font-weight: bold; font-size: 2vw;">Adjustment Employee History</span>
								</center>
							</div>
							<table id="tableMutationLog" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th>Employee ID</th>
										<th>Name</th>
										<th>Process From</th>
										<th>Process To</th>
										<th>Remark</th>
										<th>Adjusted By</th>
										<th>Adjusted At</th>
									</tr>
								</thead>
								<tbody id="bodyTableMutationLog">
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="col-xs-12">
						<div class="row" id="footer_resume">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts-for-polar.js") }}"></script>
<script src="{{ url("js/highcharts-more-for-polar.js") }}"></script>
<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/exporting-new.js")}}"></script>
<script src="{{ url("js/export-data-new.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		fetchSkillMap();
		clearMaster();
		clearValue();
		clearEmployee();
		$('#process_new').hide();
		$('.select2').select2({
			dropdownParent: $('#modalSkillMaster')
		});
		$('.select3').select2({
			dropdownParent: $('#modalEmployeeAdjustment')
		});
		$('.select4').select2({
		});
	});

	function changeProcess() {
		$("#processFix").val($("#processSelect").val());
	}
	
	$('.timepicker').timepicker({
		use24hours: true,
		showInputs: false,
		showMeridian: false,
		minuteStep: 30,
		defaultTime: '00:00',
		timeFormat: 'h:mm'
	});


	function fetchSkillMap(){
		var data = {
			location:'{{$location}}',
			process:$('#processFix').val()
		}
		$.get('{{ url("fetch/skill_map") }}',data, function(result, status, xhr){
			if(result.status){
				var employee_id = [];
				var name = [];
				var tableSkill = "";
				$('#tableSkillMap').empty();
				var indexindex = 0;
				var index2 = 0;
				var index1 = 0;
				var index5 = 0;
				tableSkill += '<table class="table table-bordered" style="width:100%;border:2px solid black;">';
				$.each(result.process, function(key, value) {
					var index3 = 0;
					var modal = '#modalSkillAdjusment';
					tableSkill += '<tr>';
					tableSkill += '<td style="color:black;width:50px;font-size:15px;border:2px solid black" id="process_display_'+index1+'"></td>';
					for(var i = 0;i < result.emp[key].length; i++){
						tableSkill += '<td onclick="showModalSkillAdjusment(\''+modal+'\',this.id,\''+result.emp[key][i].name+'\',\''+value.process+'\')" class="nama_operator" id="'+result.emp[key][i].employee_id+'" style="color:black;border:2px solid black;"><div style="width:100%;background-color:#a4eb34;padding-bottom:0px"><span style="font-weight:bold;">'+result.emp[key][i].employee_id+'</span><br><span style="">'+result.emp[key][i].name.split(' ').slice(0,2).join(' ')+'</span></div><div id="container_'+indexindex+'" style="width:300px;padding-top:0px"></div></td>';
							indexindex++;
							index3++;
					}
					index1++;
					tableSkill += '</tr>';
				})
				tableSkill += '</table>';
				$('#tableSkillMap').append(tableSkill);

				$.each(result.process, function(key, value) {
					var index4 = 0;
					for(var i = 0;i < result.emp[key].length; i++){
						var skills = [];
						var nilais = [];
						var nilaitetap = [];
						var status = 0;

						if (result.skill_map[key][i].length > 0) {
							for(var j = 0; j < result.skill_map[key][i].length;j++){
								skills.push(result.skill_map[key][i][j].skill_code);
								nilais.push({y: parseInt(result.skill_map[key][i][j].nilai), name: result.skill_map[key][i][j].skill});
								nilaitetap.push(parseInt(result.skill_map[key][i][j].nilai_tetap));
							}
						}
						for(var k = 0; k < result.skill_required[key][i].length;k++){
							if (result.skill_required[key][i][k].skill_now == null || result.skill_required[key][i][k].nilai_now < 3) {
								status++;
							}
						}
						if (status > 0) {
							document.getElementById(result.emp[key][i].employee_id).style.backgroundColor = '#ff6161';
						}
						index4++;
						
						if (skills.length > 0) {
							Highcharts.chart('container_'+index2, {
							    chart: {
							        polar: true,
							        type: 'line',
							        height:'200px',
							    },

							    pane: {
							        size: '70%'
							    },
							    title: {
									text: ''
								},

							    xAxis: {
							        categories: skills,
							        tickmarkPlacement: 'on',
							        lineWidth: 0
							    },

							    yAxis: {
							        gridLineInterpolation: 'polygon',
							        lineWidth: 0,
							        min: 0
							    },
							    tooltip: {
							        shared: true,
							        pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y:,.0f}</b><span><br/>'
							    },

							    legend: {
							        enabled:false
							    },

							    series: [{
							        name: 'Current',
							        data: nilais,
							        pointPlacement: 'on',
							        color: '#ff6161',
							        lineWidth: 1,
							        marker: {
					                    enabled: true,
					                    radius: 2
					                }
							    },
							    {
							        name: 'Required',
							        data: nilaitetap,
							        pointPlacement: 'on',
							        color:'#64ff61',
							        lineWidth: 1,
							        marker: {
					                    enabled: true,
					                    radius: 2
					                }
							    }],

							    responsive: {
							        rules: [{
							            condition: {
							                maxWidth: 200
							            },
							            chartOptions: {
							                legend: {
							                    align: 'center',
							                    verticalAlign: 'bottom',
							                    layout: 'horizontal'
							                },
							                pane: {
							                    size: '100%'
							                }
							            }
							        }]
							    },
							    exporting: {
								    enabled: false
								},
								credits: {
								    enabled: false
								},

							});
						}
						index2++;
					}
					$('#process_display_'+index5).html('<b>'+value.process+'<br>('+index4+')</b>');
					index5++;
				})
			}
			else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed.');
			}
		});
}

function cancel(modal) {
	$(modal).modal('hide');
}

function showModalSkillAdjusment(modal,employee_id,name,proces) {
	$(modal).modal('show');
	skillAdjusment(employee_id,name,proces);
}

function skillAdjusment(employee_id,name,proces) {
	$('#employee_details').empty();
	$('#warning_details').html("");
	$('#employee_details').html(employee_id+' - '+name+' - '+proces);

	var data = {
		location:'{{$location}}',
		employee_id:employee_id,
		process:proces,
	}
	$.get('{{ url("fetch/skill_map_detail") }}',data, function(result, status, xhr){
		if (result.status) {
			var skills = [];
			var nilais = [];
			var nilaitetap = [];

			var tableDetail = "";
			$('#table_detail').empty();
			var tableRequired = "";
			$('#table_required').empty();
			var tableOther = "";
			$('#table_other').empty();
			var footer = "";
			$('#skillFooter').empty();
			if (result.skill_map.length > 0) {
				tableDetail += '<table style="padding:0px" id="tableDetail" class="table table-bordered">';
				tableDetail += '<thead>';
				tableDetail += '<tr>';
				tableDetail += '<th style="background-color: rgb(126,86,134); color: #FFD700;"><center>Skill</center></th>';
				tableDetail += '<th style="background-color: rgb(126,86,134); color: #FFD700;"><center>Value</center></th>';
				tableDetail += '<th style="background-color: rgb(126,86,134); color: #FFD700;"><center>Required</center></th>';
				tableDetail += '<th style="background-color: rgb(126,86,134); color: #FFD700;"><center>Result</center></th>';
				tableDetail += '</tr>';
				tableDetail += '</thead>';
				for(var j = 0; j < result.skill_map.length;j++){
					skills.push(result.skill_map[j].skill_code);
					nilais.push({y: parseInt(result.skill_map[j].nilai), name: result.skill_map[j].skill});
					nilaitetap.push(parseInt(result.skill_map[j].nilai_tetap));
					if (result.skill_map[j].nilai < 3) {
						var color = '#ffccff';
					}else{
						var color = '#ccffff';
					}
					tableDetail += '<tr style="background-color:'+color+'">';
					tableDetail += '<td style="padding:0px">'+result.skill_map[j].skill+'</td>';
					tableDetail += '<td style="padding:0px">'+result.skill_map[j].nilai+'</td>';
					tableDetail += '<td style="padding:0px">'+result.skill_map[j].nilai_tetap+'</td>';
					tableDetail += '<td style="padding:0px">'+result.skill_map[j].description+'</td>';
					tableDetail += '</tr>';
				}
				tableDetail += '</table>';
				$('#table_detail').append(tableDetail);
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
							}
							]
						},
						'paging': true,
						'lengthChange': true,
						'pageLength': 5,
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
			}

			if (result.skill_required.length > 0) {
				$('#required_skill_length').val(result.skill_required.length);
				tableRequired += '<table style="padding:0px" class="table table-bordered">';
				tableRequired += '<thead>';
				tableRequired += '<tr>';
				tableRequired += '<th style="padding:0px"><center>Required Skill</center></th>';
				tableRequired += '<th style="padding:0px"><center>Required Value</center></th>';
				tableRequired += '<th style="padding:0px"><center>Current Skill</center></th>';
				tableRequired += '<th colspan="4" style="padding:0px"><center>Current Value</center></th>';
				tableRequired += '</tr>';
				tableRequired += '</thead>';
				for(var i = 0; i < result.skill_required.length;i++){
					if (i % 2 === 0 ) {
						var color = 'style="background-color: #fffcb7"';
					} else {
						var color = 'style="background-color: #ffd8b7"';
					}

					var k = i+1;

					tableRequired += '<tr '+color+'>';
					tableRequired += '<td style="font-size: 15px;padding-top:5px;padding-bottom:5px">'+result.skill_required[i].skill+'</td>';
					tableRequired += '<td style="font-weight: bold; font-size: 30px;padding-top:5px;padding-bottom:5px; background-color: rgb(100,100,100); color: yellow;"><span>'+result.skill_required[i].nilai+'</span></td>';
					if (result.skill_required[i].skill_now != null) {
						tableRequired += '<td id="skill'+k+'" style="font-size: 15px;padding-top:5px;padding-bottom:5px">'+result.skill_required[i].skill_now+'</td>';
					}else{
						tableRequired += '<td id="skill'+k+'" style="font-size: 15px;padding-top:5px;padding-bottom:5px">'+result.skill_required[i].skill+'</td>';
					}
					tableRequired += '<td id="minus" onclick="minus('+k+')" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 30px;padding-top:5px;padding-bottom:5px; cursor: pointer;" class="unselectable">-</td>';
					if (result.skill_required[i].nilai_now != null) {
						tableRequired += '<td style="font-weight: bold; font-size: 30px;padding-top:5px;padding-bottom:5px; background-color: rgb(100,100,100); color: yellow;"><span id="count'+k+'">'+result.skill_required[i].nilai_now+'</span></td>';
					}else{
						tableRequired += '<td style="font-weight: bold; font-size: 30px;padding-top:5px;padding-bottom:5px; background-color: rgb(100,100,100); color: yellow;"><span id="count'+k+'">0</span></td>';
					}
					tableRequired += '<td id="plus" onclick="plus('+k+')" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 30px;padding-top:5px;padding-bottom:5px; cursor: pointer;" class="unselectable">+</td>';
					if (result.skill_required[i].id_skill_now == null) {
						tableRequired += '<td id="delete_other" style="background-color: rgb(255,99,99); font-weight: bold; font-size: 20px;padding-top:5px;padding-bottom:5px; cursor: pointer;" class="unselectable"></td>';
					}else{
						tableRequired += '<td id="delete_other" onclick="delete_skill('+result.skill_required[i].id_skill_now+',\''+employee_id+'\',\''+name+'\',\''+proces+'\')" style="background-color: rgb(255,99,99); font-weight: bold; font-size: 20px;padding-top:5px;padding-bottom:5px; cursor: pointer;color:white" class="unselectable"><i class="fa fa-trash" aria-hidden="true"></i></td>';
					}
					if (result.skill_required[i].nilai_now == null || result.skill_required[i].nilai_now < result.skill_required[i].nilai) {
						$('#warning_details').html("<br>Karyawan ini <b>tidak memiliki nilai Skill yang sesuai</b> dengan posisinya. Lakukan <b>Upgrade Skill</b> segera.");
					}
					tableRequired += '</tr>';
				}
				tableRequired += '</table>';
				$('#table_required').append(tableRequired);
				$('#title_required').html("Skill for Process "+proces);



				var modal = '#modalSkillAdjusment';
				footer += '<hr>';
				footer += '<a class="btn btn-danger pull-left" style="font-weight: bold;" id="btnCancel" onclick="cancel(\''+modal+'\')">CLOSE</a>';
				footer += '<a class="btn btn-primary pull-right" onclick="saveSkill(\''+employee_id+'\',\''+name+'\',\''+proces+'\')" style="font-weight: bold;" id="btnSave">SAVE</a>';
				$('#skillFooter').append(footer);
			}

			if (result.other_skill.length > 0) {
				$('#other_skill_length').val(result.other_skill.length);
				tableOther += '<table style="padding:0px" class="table table-bordered">';
				tableOther += '<thead>';
				tableOther += '<tr>';
				tableOther += '<th style="padding:0px"><center>Required Skill</center></th>';
				tableOther += '<th style="padding:0px"><center>Required Value</center></th>';
				tableOther += '<th style="padding:0px"><center>Current Skill</center></th>';
				tableOther += '<th colspan="4" style="padding:0px"><center>Current Value</center></th>';
				tableOther += '</tr>';
				tableOther += '</thead>';
				for(var l = 0; l < result.other_skill.length;l++){
					if (l % 2 === 0 ) {
						var color = 'style="background-color: #fffcb7"';
					} else {
						var color = 'style="background-color: #ffd8b7"';
					}

					var m = l+1;

					tableOther += '<tr '+color+'>';
					tableOther += '<td id="skill_other'+m+'" style="font-size: 15px;padding-top:5px;padding-bottom:5px">'+result.other_skill[l].skill+'</td>';
					tableOther += '<td style="font-weight: bold; font-size: 30px;padding-top:5px;padding-bottom:5px; background-color: rgb(100,100,100); color: yellow;"><span>'+result.other_skill[l].nilai+'</span></td>';
					if (result.other_skill[l].skill_now != null) {
						tableOther += '<td style="font-size: 15px;padding-top:5px;padding-bottom:5px">'+result.other_skill[l].skill_now+'</td>';
					}else{
						tableOther += '<td id="other'+m+'" style="font-size: 15px;padding-top:5px;padding-bottom:5px">'+result.other_skill[l].skill+'</td>';
					}
					tableOther += '<td id="minus_other" onclick="minus_other('+m+')" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 30px;padding-top:5px;padding-bottom:5px; cursor: pointer;" class="unselectable">-</td>';
					if (result.other_skill[l].nilai_now != null) {
						tableOther += '<td style="font-weight: bold; font-size: 30px;padding-top:5px;padding-bottom:5px; background-color: rgb(100,100,100); color: yellow;"><span id="count_other'+m+'">'+result.other_skill[l].nilai_now+'</span></td>';
					}else{
						tableOther += '<td style="font-weight: bold; font-size: 30px;padding-top:5px;padding-bottom:5px; background-color: rgb(100,100,100); color: yellow;"><span id="count_other'+m+'">0</span></td>';
					}
					tableOther += '<td id="plus_other" onclick="plus_other('+m+')" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 30px;padding-top:5px;padding-bottom:5px; cursor: pointer;" class="unselectable">+</td>';
					if (result.other_skill[l].id_skill_now == null) {
						tableOther += '<td id="delete_other" style="background-color: rgb(255,99,99); font-weight: bold; font-size: 20px;padding-top:5px;padding-bottom:5px; cursor: pointer;" class="unselectable"></td>';
					}else{
						tableOther += '<td id="delete_other" onclick="delete_skill('+result.other_skill[l].id_skill_now+',\''+employee_id+'\',\''+name+'\',\''+proces+'\')" style="background-color: rgb(255,99,99); font-weight: bold; font-size: 20px;padding-top:5px;padding-bottom:5px; cursor: pointer;color:white" class="unselectable"><i class="fa fa-trash" aria-hidden="true"></i></td>';
					}
					tableOther += '</tr>';
				}
				tableOther += '</table>';
				$('#table_other').append(tableOther);
				$('#title_other').html("Add Other Skill");
			}

			Highcharts.chart('container_detail', {
			    chart: {
			        polar: true,
			        type: 'line',
			        height:'200px',
			    },

			    pane: {
			        size: '80%'
			    },
			    title: {
					text: ''
				},

			    xAxis: {
			        categories: skills,
			        tickmarkPlacement: 'on',
			        lineWidth: 0,
			        title: {
						style: {
							color: '#000'
						}
					}
			    },

			    yAxis: {
			        gridLineInterpolation: 'polygon',
			        lineWidth: 0,
			        min: 0,
			        title: {
						style: {
							color: '#000'
						}
					}
			    },
			    tooltip: {
			        shared: true,
			        pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y:,.0f}</b><span><br/>'
			    },

			    legend: {
			        enabled:false
			    },

			    series: [{
			        name: 'Current',
			        data: nilais,
			        pointPlacement: 'on',
			        color: '#ff6161',
			        marker: {
	                    enabled: true,
	                    radius: 2
	                },
					lineWidth: 1
			    },
			    {
			        name: 'Required',
			        data: nilaitetap,
			        pointPlacement: 'on',
			        color:'#64ff61',
			        marker: {
	                    enabled: true,
	                    radius: 2
	                },
					lineWidth: 1
			    }],

			    responsive: {
			        rules: [{
			            condition: {
			                maxWidth: 200
			            },
			            chartOptions: {
			                legend: {
			                    align: 'center',
			                    verticalAlign: 'bottom',
			                    layout: 'horizontal'
			                },
			                pane: {
			                    size: '100%'
			                }
			            }
			        }]
			    },
			    exporting: {
				    enabled: false
				},
				credits: {
				    enabled: false
				},

			});
		}else{
			alert('Attempt to retrieve data failed.');
		}
	})
}

function showModalSkillMaster() {
	$('#modalSkillMaster').modal('show');
	fillTableMaster();
	fillTableValue();
}

function saveSkill(employee_id,name,proces) {
	$('#loading').show();
	var skill = [];
	var count = [];

	var jumlah = $('#required_skill_length').val();
	for (var i = 1; i <= jumlah; i++ ) {
		if($('#count'+i).text() != 0){
			skill.push($('#skill'+i).text());
			count.push($('#count'+i).text());
		}
	}

	var skill_other = [];
	var count_other = [];

	var jumlah_other = $('#other_skill_length').val();
	for (var i = 1; i <= jumlah_other; i++ ) {
		if($('#count_other'+i).text() != 0){
			skill_other.push($('#skill_other'+i).text());
			count_other.push($('#count_other'+i).text());
		}
	}

	var data = {
		employee_id:employee_id,
		process:proces,
		location:'{{$location}}',
		count:count,
		skill:skill,
		skill_other:skill_other,
		count_other:count_other
	}

	$.post('{{ url("input/skill_adjustment") }}',data, function(result, status, xhr){
		if(result.status){
			$('#loading').hide();
			openSuccessGritter('Success!', result.message);
			fetchSkillMap();
			skillAdjusment(employee_id,name,proces);
		}
		else{
			$('#loading').hide();
			audio_error.play();
			openErrorGritter('Error!', result.message);
		}
	})
}

function plus(id){
	var count = $('#count'+id).text();
	$('#count'+id).text(parseInt(count)+1);
}

function minus(id){
	var count = $('#count'+id).text();
	if(count > 0)
	{
		$('#count'+id).text(parseInt(count)-1);
	}
}

function plus_other(id){
	var count = $('#count_other'+id).text();
	$('#count_other'+id).text(parseInt(count)+1);
}

function minus_other(id){
	var count = $('#count_other'+id).text();
	if(count > 0)
	{
		$('#count_other'+id).text(parseInt(count)-1);
	}
}

function delete_skill(id_skill,employee_id,name,proces){
	var data = {
		id_skill:id_skill
	}
	$.post('{{ url("destroy/skill_maps") }}',data, function(result, status, xhr){
		if(result.status){
			openSuccessGritter('Success!', result.message);
			fetchSkillMap();
			skillAdjusment(employee_id,name,proces);
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', result.message);
		}
	})
}

function fillTableMaster() {
	clearMaster();
	var data = {
		location:'{{$location}}'
	}
	$.get('{{ url("fetch/skill_master") }}',data, function(result, status, xhr){
		if(result.status){
			$('#tableMaster').DataTable().clear();
			$('#tableMaster').DataTable().destroy();
			$('#bodyTableMaster').html("");
			var tableData = "";
			$('#process_choice').html("");
			var process_choice = "";
			$.each(result.skill, function(key, value) {
				tableData += '<tr>';
				tableData += '<td>'+ value.skill_code +'</td>';
				tableData += '<td>'+ value.skill +'</td>';
				tableData += '<td>'+ value.process +'</td>';
				tableData += '<td>'+ value.value +'</td>';
				tableData += '<td><button class="btn btn-warning btn-sm" onclick="editMaster('+value.id+')"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>  <button class="btn btn-danger btn-sm" onclick="deleteMaster('+value.id+')"><i class="fa fa-trash" aria-hidden="true"></i></button></td>';
				tableData += '</tr>';
			});
			$('#bodyTableMaster').append(tableData);

			process_choice += '<option value=""></option>';			
			$.each(result.process, function(key, value) {
				process_choice += '<option value="'+value.process+'">'+value.process+'</option>';
			})
			process_choice += '<option value="Lain-lain">Lain-lain</option>';
			$('#process_choice').append(process_choice);


			var table = $('#tableMaster').DataTable({
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
		}
		else{
			alert('Attempt to retrieve data failed');
		}
	});
}

	function processChoice(value) {
		if (value === 'Lain-lain') {
			$('#process_new').show();
		}else{
			$('#process_new').hide();
		}
	}

	function clearMaster() {
		$('#skill_code').val("");
		$('#skill').val("");
		$('#process').val("");
		$('#condition').val("INPUT");
		$('#id_skill').val("");
		$('#value').val("");
		$('#process_choice').val("").trigger('change.select2');
		$('#process_new').hide();
	}

	function clearValue() {
		$('#skill_value').val("");
		$('#description').val("");
		$('#condition_value').val("INPUT");
		$('#id_value').val("");
	}

	function saveMaster() {
		if ($('#skill_code').val() == "" || $('#skill').val() == "" || $('#value').val() == "") {
			alert('Semua data harus diisi.');
		}else{
			$('#loading').show();
			if ($('#process_choice').val() == 'Lain-lain') {
				var proces = $('#process').val();
			}else{
				var proces = $('#process_choice').val();
			}
			if (proces == '') {
				alert('Semua data harus diisi.');
			}else{
				var data = {
					location:'{{$location}}',
					skill_code:$('#skill_code').val(),
					skill:$('#skill').val(),
					process:proces,
					condition:$('#condition').val(),
					id_skill:$('#id_skill').val(),
					value:$('#value').val(),
				}

				$.post('{{ url("input/skill_master") }}',data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						fillTableMaster();
						clearMaster();
						$('#loading').hide();
					}
					else{
						$('#loading').hide();
						audio_error.play();
						openErrorGritter('Error!', result.message);
					}
				})
			}
		}
	}

	function deleteMaster(id) {
		var data = {
			location:'{{$location}}',
			id:id,
		}

		if (confirm('Apakah Anda yakin akan menghapus data ini?')) {
			$.post('{{ url("destroy/skill_master") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					fillTableMaster();
					clearMaster();
				}
				else{
					audio_error.play();
					openErrorGritter('Error!', result.message);
				}
			})
		}
	}

	function editMaster(id) {
		var data = {
			location:'{{$location}}',
			id:id,
		}
		$.get('{{ url("get/skill_master") }}',data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				$('#skill_code').val(result.skill.skill_code);
				$('#skill').val(result.skill.skill);
				$('#process_choice').val(result.skill.process).trigger('change.select2');
				$('#condition').val("UPDATE");
				$('#id_skill').val(id);
				$('#value').val(result.skill.value);
				$('#skill_code').focus();
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		})
	}

	function showModalEmployeeAdjustment() {
		$('#modalEmployeeAdjustment').modal('show');
		fillTableEmployee();
	}

	function clearEmployee() {
		$('#employee_choice').val("").trigger('change.select2');
		$('#process_adjustment').val("").trigger('change.select2');
		$('#condition_adjustment').val("INPUT");
	}

	function fillTableEmployee() {
		clearEmployee();
		var data = {
			location:'{{$location}}',
			dept:'{{$dept}}',
			section:'{{$section}}',
		}
		$.get('{{ url("fetch/skill_employee") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableEmployee').DataTable().clear();
				$('#tableEmployee').DataTable().destroy();
				$('#bodyTableEmployee').html("");
				var tableData = "";
				$('#employee_choice').html("");
				var employee_choice = "";
				$('#process_adjustment').html("");
				var process_adjustment = "";
				$.each(result.employee, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ value.employee_id +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.process +'</td>';
					tableData += '<td><button class="btn btn-warning btn-sm" onclick="editEmployee('+value.id+')"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>  <button class="btn btn-danger btn-sm" onclick="deleteEmployee('+value.id+')"><i class="fa fa-trash" aria-hidden="true"></i></button></td>';
					tableData += '</tr>';
				});
				$('#bodyTableEmployee').append(tableData);

				employee_choice += '<option value=""></option>';			
				$.each(result.employees, function(key, value) {
					employee_choice += '<option value="'+value.employee_id+'">'+value.employee_id+' - '+value.name+'</option>';
				})
				$('#employee_choice').append(employee_choice);

				process_adjustment += '<option value=""></option>';			
				$.each(result.process, function(key, value) {
					process_adjustment += '<option value="'+value.process+'">'+value.process+'</option>';
				})
				$('#process_adjustment').append(process_adjustment);

				var table = $('#tableEmployee').DataTable({
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
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function saveEmployee() {
		if ($('#employee_choice').val() == "" || $('#process_adjustment').val() == "") {
			alert('Semua data harus diisi.');
		}else{
			$('#loading').show();
			var data = {
				location:'{{$location}}',
				process:$('#process_adjustment').val(),
				condition:$('#condition_adjustment').val(),
				employee_id:$('#employee_choice').val(),
				id_employee:$('#id_employee').val(),
			}

			$.post('{{ url("input/skill_employee") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					$('#loading').hide();
					fillTableEmployee();
					clearEmployee();
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', result.message);
				}
			})
		}
	}

	function deleteEmployee(id) {
		var data = {
			location:'{{$location}}',
			id:id,
		}

		if (confirm('Apakah Anda yakin akan menghapus data ini?')) {
			$.post('{{ url("destroy/skill_employee") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					fetchSkillMap();
					fillTableEmployee();
					clearEmployee();
				}
				else{
					audio_error.play();
					openErrorGritter('Error!', result.message);
				}
			})
		}
	}

	function editEmployee(id) {
		var data = {
			location:'{{$location}}',
			id:id,
		}
		$.get('{{ url("get/skill_employee") }}',data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				$('#employee_choice').val(result.employee.employee_id).trigger('change.select2');
				$('#process_adjustment').val(result.employee.process).trigger('change.select2');
				$('#condition_adjustment').val("UPDATE");
				$('#id_employee').val(id);
				$('#employee_choice').focus();
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		})
	}

	function fillTableValue() {
		clearValue();
		var data = {
			location:'{{$location}}'
		}
		$.get('{{ url("fetch/skill_value") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableValue').DataTable().clear();
				$('#tableValue').DataTable().destroy();
				$('#bodyTableValue').html("");
				var tableData = "";
				$.each(result.skill_value, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ value.value +'</td>';
					tableData += '<td>'+ value.description +'</td>';
					tableData += '<td><button class="btn btn-warning btn-sm" onclick="editValue('+value.id+')"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>  <button class="btn btn-danger btn-sm" onclick="deleteValue('+value.id+')"><i class="fa fa-trash" aria-hidden="true"></i></button></td>';
					tableData += '</tr>';
				});
				$('#bodyTableValue').append(tableData);

				var table = $('#tableValue').DataTable({
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
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function saveValue() {
		if ($('#skill_value').val() == "" || $('#description').val() == "") {
			alert('Semua data harus diisi.');
		}else{
			$('#loading').show();
			var data = {
				location:'{{$location}}',
				value:$('#skill_value').val(),
				description:$('#description').val(),
				condition_value:$('#condition_value').val(),
				id_value:$('#id_value').val(),
			}

			$.post('{{ url("input/skill_value") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					fillTableValue();
					clearValue();
					fetchSkillMap();
					$('#loading').hide();
				}
				else{
					$('#loading').hide();
					audio_error.play();
					openErrorGritter('Error!', result.message);
				}
			})
		}
	}

	function deleteValue(id) {
		var data = {
			location:'{{$location}}',
			id:id,
		}

		if (confirm('Apakah Anda yakin akan menghapus data ini?')) {
			$.post('{{ url("destroy/skill_value") }}',data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					fillTableValue();
					clearValue();
					fetchSkillMap();
				}
				else{
					audio_error.play();
					openErrorGritter('Error!', result.message);
				}
			})
		}
	}

	function editValue(id) {
		var data = {
			location:'{{$location}}',
			id:id,
		}
		$.get('{{ url("get/skill_value") }}',data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				$('#condition_value').val("UPDATE");
				$('#id_value').val(id);
				$('#skill_value').val(result.skill.value);
				$('#description').val(result.skill.description);
				$('#skill_value').focus();
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		})
	}

	function showModalResume() {
		$('#modalResume').modal('show');
		fillResume();
	}

	function fillResume() {
		var data = {
			location:'{{$location}}'
		}
		$.get('{{ url("fetch/skill_resume") }}',data, function(result, status, xhr){
			if (result.status) {
				$('#tableResume').DataTable().clear();
				$('#tableResume').DataTable().destroy();
				$('#bodyTableResume').html("");
				var tableData = "";

				var skills = [];
				var nilais = [];

				$.each(result.resume, function(key, value) {
					var jumlah_all = ((value.jumlah_satu + value.jumlah_dua + value.jumlah_tiga +value.jumlah_empat)/value.jumlah_orang) * 100;
					tableData += '<tr style="font-size:15px;">';
					tableData += '<td>'+ value.skill +'</td>';
					tableData += '<td>'+ value.jumlah_lebih_tiga +'</td>';
					tableData += '<td>'+ value.jumlah_satu +'</td>';
					tableData += '<td>'+ value.jumlah_dua +'</td>';
					tableData += '<td>'+ value.jumlah_tiga +'</td>';
					tableData += '<td>'+ value.jumlah_empat +'</td>';
					tableData += '<td>'+ Math.round(value.persen_lebih_tiga) +' %</td>';
					tableData += '<td>'+ Math.round(jumlah_all) +' %</td>';
					tableData += '</tr>';

					skills.push(value.skill_code);
					nilais.push({y: parseInt(value.average), name: value.skill});
				});
				$('#bodyTableResume').append(tableData);

				var table = $('#tableResume').DataTable({
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

				$('#tableUnfulfilled').DataTable().clear();
				$('#tableUnfulfilled').DataTable().destroy();
				$('#bodyTableUnfulfilled').html("");
				var tableDataUnfulfilled = "";

				$.each(result.unfulfilled, function(key, value) {
					if (value.unfulfilled_remark == 'Nilai Skill Kurang') {
						var color = '#fffa70';
					}else{
						var color = '#ffccff';
					}
					tableDataUnfulfilled += '<tr style="background-color:'+color+';font-size:15px;">';
					tableDataUnfulfilled += '<td>'+ value.employee_id +'</td>';
					tableDataUnfulfilled += '<td>'+ value.name +'</td>';
					tableDataUnfulfilled += '<td>'+ value.process +'</td>';
					tableDataUnfulfilled += '<td>'+ value.skill_code +'</td>';
					tableDataUnfulfilled += '<td>'+ value.skill +'</td>';
					tableDataUnfulfilled += '<td>'+ value.value +'</td>';
					tableDataUnfulfilled += '<td>'+ value.required +'</td>';
					tableDataUnfulfilled += '<td>'+ value.unfulfilled_remark +'</td>';
					tableDataUnfulfilled += '</tr>';
				});

				$('#bodyTableUnfulfilled').append(tableDataUnfulfilled);

				var table = $('#tableUnfulfilled').DataTable({
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

				$('#tableMutationLog').DataTable().clear();
				$('#tableMutationLog').DataTable().destroy();
				$('#bodyTableMutationLog').html("");
				var tableMutationLog = "";

				$.each(result.mutation, function(key, value) {
					tableMutationLog += '<tr style="font-size:15px;">';
					tableMutationLog += '<td>'+ value.employee_id +'</td>';
					tableMutationLog += '<td>'+ value.name +'</td>';
					tableMutationLog += '<td>'+ value.process_from +'</td>';
					tableMutationLog += '<td>'+ value.process_to +'</td>';
					tableMutationLog += '<td>'+ value.mutation_remark +'</td>';
					tableMutationLog += '<td>'+ value.adjusted_by +'</td>';
					tableMutationLog += '<td>'+ value.mutation_created_at +'</td>';
					tableMutationLog += '</tr>';
				});

				$('#bodyTableMutationLog').append(tableMutationLog);

				var table = $('#tableMutationLog').DataTable({
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

				var title = '{{$subtitle}}';

				Highcharts.chart('container_resume', {
				    chart: {
				        polar: true,
				        type: 'line',
				        height:'500px',
				    },

				    pane: {
				        size: '80%'
				    },
				    title: {
						text: 'Skill Map Resume - '+title
					},

				    xAxis: {
				        categories: skills,
				        tickmarkPlacement: 'on',
				        lineWidth: 0,
				        labels: {
				            style: {
				                color: 'white',
				                fontSize:'20px'
				            }
				        }
				    },

				    yAxis: {
				        gridLineInterpolation: 'polygon',
				        lineWidth: 0,
				        min: 0
				    },
				    tooltip: {
				        shared: true,
				        pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y:,.0f}</b><span><br/>'
				    },

				    legend: {
				        enabled:false
				    },

				    series: [{
				        name: 'Value',
				        data: nilais,
				        pointPlacement: 'on',
				        color: '#64ff61',
				        lineWidth: 2,
				        marker: {
		                    enabled: true,
		                    radius: 3
		                }
				    }],

				    responsive: {
				        rules: [{
				            condition: {
				                maxWidth: 200
				            },
				            chartOptions: {
				                legend: {
				                    align: 'center',
				                    verticalAlign: 'bottom',
				                    layout: 'horizontal'
				                },
				                pane: {
				                    size: '100%'
				                }
				            }
				        }]
				    },
					credits: {
					    enabled: false
					},

				});
			}else{
				audio_error.play();
				alert('Attempt to retrieve data failed');
			}
		})
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

Highcharts.setOptions({
	global: {
		useUTC: true,
		timezoneOffset: -420
	}
});

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '3000'
	});
}

function openErrorGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-danger',
		image: '{{ url("images/image-stop.png") }}',
		sticky: false,
		time: '3000'
	});
}


</script>


@endsection