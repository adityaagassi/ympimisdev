@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	input {
		line-height: 22px;
	}
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
		padding: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}

	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	.content-wrapper{
		 padding-top: 10px !important;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <span class="text-purple">{{ $title_jp }}</span>
		<button class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#create_modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Operator
		</button>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content"  style="padding-top: 0px">
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
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading. Please Wait. <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>		
	<div class="row">
		<div class="col-xs-12" style="padding-bottom: 10px">
			<div class="col-xs-2" style="padding-left: 0;">
				<div class="input-group date">
					<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 0;">
				<div class="input-group date">
					<div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 0;">
				<button class="btn btn-success" onclick="fillList()" style="font-weight: bold;">
					Search
				</button>
			</div>
		</div>
		<div class="col-xs-12" style="padding-bottom: 10px">
			<div id="container" style="width: 100%;"></div>
		</div>
		<div class="col-xs-9">
			<div id="container2" style="width: 100%;"></div>
		</div>
		<div class="col-xs-3">
			<div class="row" style="padding-right: 15px">
				<div class="box box-solid">
					<div class="box-header" style="background-color: #2d2d2e;">
						<center><span style="font-size: 20px; font-weight: bold; color: white;">AKUMULASI HINGGA HARI INI</span></center>
					</div>
					<table class="table table-responsive" style="height: 300px">
						<tr style="background-color: #545454;color:white">
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.5vw;padding-top: 0px;padding-bottom: 0px">Belum Repair</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.5vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right" style="color:#7cff73" id="blm_repair">0</span></th>
						</tr>
						<tr style="background-color: #545454;color:white">
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.5vw;padding-top: 0px;padding-bottom: 0px">Menunggu Parts</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.5vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right" style="color:#7cff73" id="mng_part">0</span></th>
						</tr>
						<tr style="background-color: #545454;color:white">
							<th style="vertical-align: middle;font-weight: bold;font-size: 1.5vw;padding-top: 0px;padding-bottom: 0px">Belum Kensa</th>
							<th style="vertical-align: middle;font-weight: bold;font-size: 3vw;padding-top: 0px;padding-bottom: 0px"><span class="pull-right" style="color:#ff7373" id="blm_kensa">0</span></th>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="row" style="padding-left: 15px;padding-right: 15px;">
				<table class="table table-bordered table-striped" id="tableOutstanding" style="width: 100%;color: white">
					<thead style="background-color: rgb(255,255,255); font-size: 12px;font-weight: bold">
						<tr style="background-color: #2d2d2e;border-bottom: 3px solid white;border-top: 1px solid #2d2d2e">
							<th rowspan="2" style="vertical-align: middle;">No.</th>
							<th rowspan="2" style="vertical-align: middle;">Jig ID</th>
							<th rowspan="2" style="vertical-align: middle;">Jig Name</th>
							<th colspan="3" style="border-right: 2px solid red;border-left: 2px solid red">Kensa</th>
							<th colspan="3" style="border-right: 2px solid red">Repair</th>
							<th rowspan="2" style="vertical-align: middle;">Jig Status</th>
							<th rowspan="2" style="vertical-align: middle;">Due Date</th>
						</tr>
						<tr style="background-color: #2d2d2e;border-bottom: 3px solid white;border-top: 1px solid #2d2d2e">
							<th style="border-right: 2px solid red;border-left: 2px solid red">Kensa Time</th>
							<th style="border-right: 2px solid red">Kensa PIC</th>
							<th style="border-right: 2px solid red">Kensa Status</th>
							<th style="border-right: 2px solid red">Repair Time</th>
							<th style="border-right: 2px solid red">Repair PIC</th>
							<th style="border-right: 2px solid red">Repair Status</th>
						</tr>
					</thead>
					<tbody id="bodyTableOutstanding">
					</tbody>
				</table>
			</div>
		</div>
	</div>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<center><h3 style="font-weight: bold;" id="judul_detail"></h3></center>
					<div class="col-md-12" id="bodyDetail">
			          
			        </div>
				</div>
			</div>
			<div class="modal-footer">
	          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
	        </div>
		</div>
	</div>
</div>
</section>
@endsection
@section('scripts')

<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		fillList();
		setInterval(fillList,60000);
	});

	function fillList(){
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
		}
		$.get('{{ url("fetch/welding/monitoring_jig") }}',data, function(result, status, xhr){
			if(result.status){
				var date = [];
				var series = [];
				var before_kensa = [];
				var after_kensa = [];
				var before_repair = [];
				var waiting_part = [];

				for (var i = 0; i < result.monitoring.length; i++) {
					date.push(result.monitoring[i].week_date);
					before_kensa.push(parseInt(result.monitoring[i].before_kensa));
					after_kensa.push(parseInt(result.monitoring[i].after_kensa));
					before_repair.push(parseInt(result.monitoring[i].before_repair));
					waiting_part.push(parseInt(result.monitoring[i].waiting_part));
				}

				Highcharts.chart('container', {
				    chart: {
				        type: 'column',
				        height: '350px'
				    },
				    title: {
				        text: 'KENSA JIG MONITORING',
				        style: {
			                fontSize: '30px',
			                fontWeight: 'bold'
			              }
				    },
				    xAxis: {
				        categories: date,
				        type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '15px'
							}
						},
				    },
				    yAxis: {
							title: {
								text: 'Total Kensa Jig',
								style: {
			                        color: '#eee',
			                        fontSize: '13px',
			                        fill: '#6d869f'
			                    }
							},
							labels:{
					        	style:{
									fontSize:"15px"
								}
					        },
							type: 'linear'
						},
				    legend: {
			              itemStyle:{
			                color: "white",
			                fontSize: "12px",
			                fontWeight: "bold",

			              },
					},	
				    tooltip: {
				        headerFormat: '<b>{point.x}</b><br/>',
				        pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
				    },
				    plotOptions: {
				        column: {
			                  color:  Highcharts.ColorString,
			                  borderRadius: 1,
			                  dataLabels: {
			                      enabled: true
			                  }
			              },
				        series:{
							cursor: 'pointer',
			                point: {
			                  events: {
			                    click: function () {
			                      ShowModal(this.category,this.series.name);
			                    }
			                  }
			                },
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: false,
							pointPadding: 0.9,
							groupPadding: 0.93,
							borderWidth: 0,
							cursor: 'pointer'
						},
				    },
				    credits:{
				    	enabled:false
				    },
				    series: [{
				        name: 'Schedule Kensa',
				        data: before_kensa,
				        animation: false,
				        colorByPoint:false,
					    color:'#ff6666',
					    // stacking: 'normal',
				    }, {
				        name: 'Sudah Kensa',
				        data: after_kensa,
				        animation: false,
				        colorByPoint:false,
					    color:'#5cb85c',
					    stacking: 'normal',
				    }, {
				        name: 'Belum Repair',
				        data: before_repair,
				        animation: false,
				        colorByPoint:false,
					    color:'#f0ad4e',
					    stacking: 'normal',
				    }, {
				        name: 'Menunggu Part',
				        data: waiting_part,
				        animation: false,
				        colorByPoint:false,
					    color:'#448aff',
					    stacking: 'normal',
				    }]
				});

				var date_periode = [];
				var series_periode = [];
				var before_kensa_periode = [];
				var after_kensa_periode = [];
				var before_repair_periode = [];
				var waiting_part_periode = [];

				for (var i = 0; i < result.monitoring_quartal.length; i++) {
					date_periode.push(result.monitoring_quartal[i].dates);
					before_kensa_periode.push(parseInt(result.monitoring_quartal[i].before_kensa));
					after_kensa_periode.push(parseInt(result.monitoring_quartal[i].after_kensa));
					before_repair_periode.push(parseInt(result.monitoring_quartal[i].before_repair));
					waiting_part_periode.push(parseInt(result.monitoring_quartal[i].waiting_part));
				}

				Highcharts.chart('container2', {
				    chart: {
				        type: 'column',
				        height: '350px'
				    },
				    title: {
				        text: 'RESUME BY PERIODE',
				        style: {
			                fontSize: '30px',
			                fontWeight: 'bold'
			              }
				    },
				    xAxis: {
				        categories: date_periode,
				        type: 'category',
						gridLineWidth: 1,
						gridLineColor: 'RGB(204,255,255)',
						lineWidth:2,
						lineColor:'#9e9e9e',
						labels: {
							style: {
								fontSize: '15px'
							}
						},
				    },
				    yAxis: {
							title: {
								text: 'Total Kensa Jig',
								style: {
			                        color: '#eee',
			                        fontSize: '13px',
			                        fill: '#6d869f'
			                    }
							},
							labels:{
					        	style:{
									fontSize:"15px"
								}
					        },
							type: 'linear'
						},
				    legend: {
			              itemStyle:{
			                color: "white",
			                fontSize: "12px",
			                fontWeight: "bold",

			              },
					},	
				    tooltip: {
				        headerFormat: '<b>{point.x}</b><br/>',
				        pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
				    },
				    plotOptions: {
				        column: {
			                  color:  Highcharts.ColorString,
			                  borderRadius: 1,
			                  dataLabels: {
			                      enabled: true
			                  }
			              },
				        series:{
							cursor: 'pointer',
			                point: {
			                  events: {
			                    click: function () {
			                      ShowModalPeriode(this.category,this.series.name);
			                    }
			                  }
			                },
							dataLabels: {
								enabled: true,
								format: '{point.y}',
								style:{
									fontSize: '1vw'
								}
							},
							animation: false,
							pointPadding: 0.9,
							groupPadding: 0.93,
							borderWidth: 0,
							cursor: 'pointer'
						},
				    },
				    credits:{
				    	enabled:false
				    },
				    series: [{
				        name: 'Schedule Kensa',
				        data: before_kensa_periode,
				        animation: false,
				        colorByPoint:false,
					    color:'#ff6666',
					    // stacking: 'normal',
				    }, {
				        name: 'Sudah Kensa',
				        data: after_kensa_periode,
				        animation: false,
				        colorByPoint:false,
					    color:'#5cb85c',
					    stacking: 'normal',
				    }, {
				        name: 'Belum Repair',
				        data: before_repair_periode,
				        animation: false,
				        colorByPoint:false,
					    color:'#f0ad4e',
					    stacking: 'normal',
				    }, {
				        name: 'Menunggu Part',
				        data: waiting_part_periode,
				        animation: false,
				        colorByPoint:false,
					    color:'#448aff',
					    stacking: 'normal',
				    }]
				});

				var before_kensa = 0;
				var waiting_part = 0;
				var before_repair = 0;
				var tgl_before_kensa = [];

				$.each(result.resume, function(key, value) {
					before_kensa = before_kensa + parseInt(value.before_kensa);
					before_repair = before_repair + parseInt(value.before_repair);
					waiting_part = waiting_part + parseInt(value.waiting_part);
					if (parseInt(value.before_kensa) > 0) {
						tgl_before_kensa.push(value.week_date);
					}
				});

				$('#blm_repair').html(before_repair);
				$('#mng_part').html(waiting_part);
				$('#blm_kensa').html(before_kensa);

				$('#bodyTableOutstanding').empty();
				var outstanding = "";
				var index = 1;
				$.each(result.outstanding, function(key, value) {
					if (index % 2 == 0) {
						if (tgl_before_kensa.includes(value.schedule_date)) {
							var background_color = '#d94e4e';
							var color = '#fff';
						}else{
							var background_color = '#383838';
							var color = '#fff';
						}
					}else{
						if (tgl_before_kensa.includes(value.schedule_date)) {
							var background_color = '#d94e4e';
							var color = '#fff';
						}else{
							var background_color = '#292929';
							var color = '#fff';
						}
					}
					outstanding += '<tr style="background-color: '+background_color+';color: '+color+';font-size:15px">';
					outstanding += '<td>'+index+'</td>';
					outstanding += '<td>'+value.jig_id+'</td>';
					outstanding += '<td>'+value.jig_name+'</td>';
					outstanding += '<td>'+value.kensa_time+'</td>';
					outstanding += '<td>'+value.kensa_pic+'</td>';
					if (value.kensa_status != "") {
						outstanding += '<td><span class="label label-success">'+value.kensa_status+'</span></td>';
					}else{
						outstanding += '<td>'+value.kensa_status+'</td>';
					}
					outstanding += '<td>'+value.repair_time+'</td>';
					outstanding += '<td>'+value.repair_pic+'</td>';
					outstanding += '<td>'+value.repair_status+'</td>';
					if (value.schedule_status == 'Close') {
						outstanding += '<td><span class="label label-success">'+value.schedule_status+'</span></td>';
					}else{
						outstanding += '<td><span class="label label-danger">'+value.schedule_status+'</span></td>';
					}
					outstanding += '<td>'+value.schedule_date+'</td>';
					outstanding += '</tr>';

					index++;
				});

				$('#bodyTableOutstanding').append(outstanding);


			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	function ShowModal(date,kondisi) {
		$('#loading').show();
		var data = {
			date:date,
			status:kondisi
		}

		$.get('{{ url("fetch/welding/detail_monitoring_jig") }}',data, function(result, status, xhr){
			if(result.status){
				$('#modalDetail').modal('show');

				var detail = "";
				$('#bodyDetail').empty();
				var index = 1;

				$('#judul_detail').html(result.judul);

				$.each(result.schedule, function(key, value) {
			        // detail += '<center><h3 class="box-title"></h3></center>';
			        if (kondisi === 'Sudah Kensa' || kondisi === 'Belum Repair' || kondisi === 'Menunggu Part') {
			        	detail += '<div class="box-body">';
				        detail += '<table class="table table-bordered" style="font-size:15px" id="tableDetail_'+index+'">';
				        detail += '<thead>';
				        detail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#7e5686;color:white;font-size:20px">';
				        detail += '<th colspan="11">'+value.jig_id+' - '+value.jig_name+'</th>';
				        detail += '</tr>';
				        detail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cc8fc1;color:black;font-size:15px">';
				        detail += '<th colspan="5">Schedule : '+value.schedule_date+'</th>';
				        detail += '<th colspan="6" style="border-left:3px solid black">Kensa : '+value.kensa_time+'</th>';
				        detail += '</tr>';
				        detail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">';
				        detail += '<th>No.</th>';
				        detail += '<th>Jig ID</th>';
				        detail += '<th>Jig Name</th>';
				        detail += '<th>Jig Point Check</th>';
				        detail += '<th>Jig Part</th>';
				        detail += '<th>Lower</th>';
				        detail += '<th>Upper</th>';
				        detail += '<th>Value</th>';
				        detail += '<th>Result</th>';
				        detail += '<th>Status</th>';
				        detail += '<th>Operator</th>';
				        detail += '</tr>';
				        detail += '</thead>';
				        var index2 = 1;
						$.each(result.detail[value.jig_id], function(key2, value2) {
							if (value2.result == 'NG') {
								var color = '#ffbaba';
							}else{
								var color = '#f2f2f2';
							}
							detail += '<tbody style="border:1px solid black">';
							detail += '<tr style="border:1px solid black;padding:2px;background-color:'+color+'">';
							detail += '<td style="border:1px solid black;padding:2px">'+index2+'</td>';
							detail += '<td style="border:1px solid black;padding:2px">'+value2.jig_id+'</td>';
							detail += '<td style="border:1px solid black;padding:2px">'+value2.jig_name+'</td>';
							detail += '<td style="border:1px solid black;padding:2px">'+value2.check_name+'</td>';
							detail += '<td style="border:1px solid black;padding:2px">'+value2.jig_child+'</td>';
							detail += '<td style="border:1px solid black;padding:2px">'+value2.lower_limit+'</td>';
							detail += '<td style="border:1px solid black;padding:2px">'+value2.upper_limit+'</td>';
							detail += '<td style="border:1px solid black;padding:2px">'+value2.value+'</td>';
							detail += '<td style="border:1px solid black;padding:2px">'+value2.result+'</td>';
							if (value2.status ==  null) {
								detail += '<td style="border:1px solid black;padding:2px">OK</td>';
							}else{
								detail += '<td style="border:1px solid black;padding:2px">'+value2.status+'</td>';
							}
							detail += '<td style="border:1px solid black;padding:2px">'+value2.kensaemp+'</td>';
							detail += '</tr>';
							detail += '</tbody>';
							index2++;
						});
						detail += '</table>';
						detail += '</div>';
			        }else{
			        	detail += '<div class="box-body">';
				        detail += '<table class="table table-bordered" style="font-size:20px" id="tableDetail_'+index+'">';
				        detail += '<thead>';
				        detail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#7e5686;color:white;font-size:20px">';
				        detail += '<th colspan="2">JIG SCHEDULE</th>';
				        detail += '</tr>';
				        detail += '<tr style="border-bottom:1px solid black;border-top:1px solid black;background-color:#f2f2f2;font-size:15px">';
				        detail += '<th>Jig ID</th>';
				        detail += '<th>'+value.jig_id+'</th>';
				        detail += '</tr>';
				        detail += '<tr style="border-bottom:1px solid black;border-top:1px solid black;background-color:#f2f2f2;font-size:15px">';
				        detail += '<th>Jig Name</th>';
				        detail += '<th>'+value.jig_name+'</th>';
				        detail += '</tr>';
				        detail += '</tr>';
				        detail += '<tr style="border-bottom:1px solid black;border-top:1px solid black;background-color:#f2f2f2;font-size:15px">';
				        detail += '<th>Due Date</th>';
				        detail += '<th>'+value.schedule_date+'</th>';
				        detail += '</tr>';
				        detail += '</thead>';
						detail += '</table>';
						detail += '</div>';
			        }

					detail += '<hr style="border:2px solid blue">';					

					index++;
				});
				$('#bodyDetail').append(detail);

				$('#loading').hide();
			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
	}

	function ShowModalPeriode(date,kondisi) {
		$('#loading').show();
		var data = {
			date:date,
			status:kondisi
		}

		$.get('{{ url("fetch/welding/detail_monitoring_jig_periode") }}',data, function(result, status, xhr){
			if(result.status){
				$('#modalDetail').modal('show');

				var detail = "";
				$('#bodyDetail').empty();
				var index = 1;

				$('#judul_detail').html(result.judul);

				$.each(result.schedule, function(key, value) {
			        // detail += '<center><h3 class="box-title"></h3></center>';
			        if (kondisi === 'Sudah Kensa' || kondisi === 'Belum Repair' || kondisi === 'Menunggu Part') {
			        	detail += '<div class="box-body">';
				        detail += '<table class="table table-bordered" style="font-size:15px" id="tableDetail_'+index+'">';
				        detail += '<thead>';
				        detail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#7e5686;color:white;font-size:20px">';
				        detail += '<th colspan="11">'+value.jig_id+' - '+value.jig_name+'</th>';
				        detail += '</tr>';
				        detail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cc8fc1;color:black;font-size:15px">';
				        detail += '<th colspan="5">Schedule : '+value.schedule_date+'</th>';
				        detail += '<th colspan="6" style="border-left:3px solid black">Kensa : '+value.kensa_time+'</th>';
				        detail += '</tr>';
				        detail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">';
				        detail += '<th>No.</th>';
				        detail += '<th>Jig ID</th>';
				        detail += '<th>Jig Name</th>';
				        detail += '<th>Jig Point Check</th>';
				        detail += '<th>Jig Part</th>';
				        detail += '<th>Lower</th>';
				        detail += '<th>Upper</th>';
				        detail += '<th>Value</th>';
				        detail += '<th>Result</th>';
				        detail += '<th>Status</th>';
				        detail += '<th>Operator</th>';
				        detail += '</tr>';
				        detail += '</thead>';
				        var index2 = 1;
						$.each(result.detail[value.jig_id], function(key2, value2) {
							if (value2.result == 'NG') {
								var color = '#ffbaba';
							}else{
								var color = '#f2f2f2';
							}
							detail += '<tbody style="border:1px solid black">';
							detail += '<tr style="border:1px solid black;padding:2px;background-color:'+color+'">';
							detail += '<td style="border:1px solid black;padding:2px">'+index2+'</td>';
							detail += '<td style="border:1px solid black;padding:2px">'+value2.jig_id+'</td>';
							detail += '<td style="border:1px solid black;padding:2px">'+value2.jig_name+'</td>';
							detail += '<td style="border:1px solid black;padding:2px">'+value2.check_name+'</td>';
							detail += '<td style="border:1px solid black;padding:2px">'+value2.jig_child+'</td>';
							detail += '<td style="border:1px solid black;padding:2px">'+value2.lower_limit+'</td>';
							detail += '<td style="border:1px solid black;padding:2px">'+value2.upper_limit+'</td>';
							detail += '<td style="border:1px solid black;padding:2px">'+value2.value+'</td>';
							detail += '<td style="border:1px solid black;padding:2px">'+value2.result+'</td>';
							if (value2.status ==  null) {
								detail += '<td style="border:1px solid black;padding:2px">OK</td>';
							}else{
								detail += '<td style="border:1px solid black;padding:2px">'+value2.status+'</td>';
							}
							detail += '<td style="border:1px solid black;padding:2px">'+value2.kensaemp+'</td>';
							detail += '</tr>';
							detail += '</tbody>';
							index2++;
						});
						detail += '</table>';
						detail += '</div>';
			        }else{
			        	detail += '<div class="box-body">';
				        detail += '<table class="table table-bordered" style="font-size:20px" id="tableDetail_'+index+'">';
				        detail += '<thead>';
				        detail += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#7e5686;color:white;font-size:20px">';
				        detail += '<th colspan="2">JIG SCHEDULE</th>';
				        detail += '</tr>';
				        detail += '<tr style="border-bottom:1px solid black;border-top:1px solid black;background-color:#f2f2f2;font-size:15px">';
				        detail += '<th>Jig ID</th>';
				        detail += '<th>'+value.jig_id+'</th>';
				        detail += '</tr>';
				        detail += '<tr style="border-bottom:1px solid black;border-top:1px solid black;background-color:#f2f2f2;font-size:15px">';
				        detail += '<th>Jig Name</th>';
				        detail += '<th>'+value.jig_name+'</th>';
				        detail += '</tr>';
				        detail += '</tr>';
				        detail += '<tr style="border-bottom:1px solid black;border-top:1px solid black;background-color:#f2f2f2;font-size:15px">';
				        detail += '<th>Due Date</th>';
				        detail += '<th>'+value.schedule_date+'</th>';
				        detail += '</tr>';
				        detail += '</thead>';
						detail += '</table>';
						detail += '</div>';
			        }

					detail += '<hr style="border:2px solid blue">';					

					index++;
				});
				$('#bodyDetail').append(detail);

				$('#loading').hide();
			}
			else{
				alert('Attempt to retrieve data failed');
				$('#loading').hide();
			}
		});
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
	      [0, '#2a2a2b']
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