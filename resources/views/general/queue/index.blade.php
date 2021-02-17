@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

	table.table-bordered{
  border:1px solid rgb(150,150,150);
}
table.table-bordered > thead > tr > th{
  border:1px solid rgb(54, 59, 56) !important;
  text-align: center;
  background-color: #212121;  
  color:white;
}
table.table-bordered > tbody > tr > td{
  border:1px solid rgb(54, 59, 56);
  background-color: #212121;
  color: white;
  vertical-align: middle;
  text-align: center;
  padding:3px;
}
table.table-condensed > thead > tr > th{   
  color: black;
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(150,150,150);
  padding:0;
}
table.table-bordered > tbody > tr > td > p{
  color: #abfbff;
}

table.table-striped > thead > tr > th{
  border:1px solid black !important;
  text-align: center;
  background-color: rgba(126,86,134,.7) !important;  
}

table.table-striped > tbody > tr > td{
  border: 1px solid #eeeeee !important;
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
	.content-wrapper{
		color: white;
		font-weight: bold;
		background-color: #313132 !important;
	}
	#loading, #error { display: none; }

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}

	.dataTables_info,
	.dataTables_length {
		color: white;
		align-content: left
	}

	div.dataTables_filter label, 
     div.dataTables_wrapper div.dataTables_info {
	     color: white;
	}
	

</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="padding-left: 5px;padding-right: 5px">
			<div class="col-xs-3" style="padding-right: 5px">
				<div class="info-box" style="min-height: 150px;vertical-align: middle;">
					<span class="info-box-icon" style="background-color: #605ca8; color: white; height: 150px;"><i class="glyphicon glyphicon-tasks"></i></span>

					<div class="info-box-content">
						<span class="info-box-text" style="color: rgba(96, 92, 168);font-size: 30px">REGISTRATION <span style="color: rgba(96, 92, 168);"></span></span>
						<span class="info-box-text" style="font-size: 60px;color: rgba(96, 92, 168);padding: 0px" id="total_registrasi"></span>
					</div>
				</div>
				<table id="tableRegistrasi" class="table table-bordered table-striped table-responsive">
					<thead>
						<tr>
							<th>ID</th>
							<th>Name</th>
							<th>Dept</th>
						</tr>
					</thead>
					<tbody id="bodyTableRegistrasi">
						
					</tbody>
				</table>
			</div>
			<div class="col-xs-3" style="padding-left: 5px;padding-right: 5px">
				<div class="info-box" style="min-height: 150px;vertical-align: middle;">
					<span class="info-box-icon" style="background-color: #cddc39; color: white; height: 150px;"><i class="glyphicon glyphicon-tasks"></i></span>

					<div class="info-box-content">
						<span class="info-box-text" style="color: rgba(96, 92, 168);font-size: 30px">CLINIC <span style="color: rgba(96, 92, 168);"></span></span>
						<span class="info-box-text" style="font-size: 60px;color: rgba(96, 92, 168);padding: 0px" id="total_clinic"></span>
					</div>
				</div>
				<table id="tableClinic" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>ID</th>
							<th>Name</th>
							<th>Dept</th>
						</tr>
					</thead>
					<tbody id="bodyTableClinic">
						
					</tbody>
				</table>
			</div>
			<div class="col-xs-3" style="padding-left: 5px;padding-right: 5px">
				<div class="info-box" style="min-height: 150px;vertical-align: middle;">
					<span class="info-box-icon" style="background-color: #cc8fc1; color: white; height: 150px;"><i class="glyphicon glyphicon-tasks"></i></span>

					<div class="info-box-content">
						<span class="info-box-text" style="color: rgba(96, 92, 168);font-size: 30px">THORAX <span style="color: rgba(96, 92, 168);"></span></span>
						<span class="info-box-text" style="font-size: 60px;color: rgba(96, 92, 168);padding: 0px" id="total_thorax"></span>
					</div>
				</div>
				<table id="tableThorax" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>ID</th>
							<th>Name</th>
							<th>Dept</th>
						</tr>
					</thead>
					<tbody id="bodyTableThorax">
						
					</tbody>
				</table>
			</div>
			<div class="col-xs-3" style="padding-left: 5px;padding-right: 5px">
				<div class="info-box" style="min-height: 150px;vertical-align: middle;">
					<span class="info-box-icon" style="background-color: #ffbaba; color: white; height: 150px;"><i class="glyphicon glyphicon-tasks"></i></span>

					<div class="info-box-content">
						<span class="info-box-text" style="color: rgba(96, 92, 168);font-size: 30px">AUDIOMETRI <span style="color: rgba(96, 92, 168);"></span></span>
						<span class="info-box-text" style="font-size: 60px;color: rgba(96, 92, 168);padding: 0px" id="total_audiometri"></span>
					</div>
				</div>
				<table id="tableAudiometri" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>ID</th>
							<th>Name</th>
							<th>Dept</th>
						</tr>
					</thead>
					<tbody id="bodyTableAudiometri">
						
					</tbody>
				</table>
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

	jQuery(document).ready(function(){
		$('.select2').select2();
		fetchQueue();
		// setInterval(fetchQueue, 5000);
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: "dd-mm-yyyy",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function fetchQueue() {
		// var data = {
			var remark = '{{$remark}}';
		// }
		$.get('{{ url("fetch/general/queue") }}'+"/"+remark, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					var total_registrasi = 0;
					var total_clinic = 0;
					var total_thorax = 0;
					var total_audiometri = 0;

					$('#bodyTableAudiometri').html("");
					$('#bodyTableThorax').html("");
					$('#bodyTableClinic').html("");
					$('#bodyTableRegistrasi').html("");

					var table_registrasi = "";
					var table_clinic = "";
					var table_thorax = "";
					var table_audiometri = "";

					$('#tableRegistrasi').DataTable().clear();
					$('#tableRegistrasi').DataTable().destroy();
					$('#tableClinic').DataTable().clear();
					$('#tableClinic').DataTable().destroy();
					$('#tableThorax').DataTable().clear();
					$('#tableThorax').DataTable().destroy();
					$('#tableAudiometri').DataTable().clear();
					$('#tableAudiometri').DataTable().destroy();

					$.each(result.data_registrasi, function(key,value){
						if (result.section.length > 0) {
							if (value.section == result.section) {
								var color = "style='background-color:#d4f05b'";
							}else{
								var color = "style='background-color:#e6e6e6'";
							}
						}else{
							var color = "style='background-color:#e6e6e6'";
						}
						table_registrasi += "<tr "+color+">";
						table_registrasi += "<td "+color+">"+value.employee_id+"</td>";
						table_registrasi += "<td "+color+">"+value.name+"</td>";
						table_registrasi += "<td "+color+">"+value.department_shortname+"</td>";
						table_registrasi += "</tr>";
						total_registrasi++;
					});
					$('#bodyTableRegistrasi').append(table_registrasi);

					$.each(result.data_clinic, function(key,value){
						if (result.section.length > 0) {
							if (value.section == result.section) {
								var color = "style='background-color:#d4f05b'";
							}else{
								var color = "style='background-color:#e6e6e6'";
							}
						}else{
							var color = "style='background-color:#e6e6e6'";
						}
						table_clinic += "<tr "+color+">";
						table_clinic += "<td "+color+">"+value.employee_id+"</td>";
						table_clinic += "<td "+color+">"+value.name+"</td>";
						table_clinic += "<td "+color+">"+value.department_shortname+"</td>";
						table_clinic += "</tr>";
						total_clinic++;
					});
					$('#bodyTableClinic').append(table_clinic);

					$.each(result.data_thorax, function(key,value){
						if (result.section.length > 0) {
							if (value.section == result.section) {
								var color = "style='background-color:#d4f05b'";
							}else{
								var color = "style='background-color:#e6e6e6'";
							}
						}else{
							var color = "style='background-color:#e6e6e6'";
						}
						table_thorax += "<tr "+color+">";
						table_thorax += "<td "+color+">"+value.employee_id+"</td>";
						table_thorax += "<td "+color+">"+value.name+"</td>";
						table_thorax += "<td "+color+">"+value.department_shortname+"</td>";
						table_thorax += "</tr>";
						total_thorax++;
					});
					$('#bodyTableThorax').append(table_thorax);

					$.each(result.data_audiometri, function(key,value){
						if (result.section.length > 0) {
							if (value.section == result.section) {
								var color = "style='background-color:#d4f05b'";
							}else{
								var color = "style='background-color:#e6e6e6'";
							}
						}else{
							var color = "style='background-color:#e6e6e6'";
						}
						table_audiometri += "<tr "+color+">";
						table_audiometri += "<td "+color+">"+value.employee_id+"</td>";
						table_audiometri += "<td "+color+">"+value.name+"</td>";
						table_audiometri += "<td "+color+">"+value.department_shortname+"</td>";
						table_audiometri += "</tr>";
						total_audiometri++;
					});
					$('#bodyTableAudiometri').append(table_audiometri);

					$('#total_registrasi').html(total_registrasi);
					$('#total_clinic').html(total_clinic);
					$('#total_thorax').html(total_thorax);
					$('#total_audiometri').html(total_audiometri);

					var table = $('#tableRegistrasi').DataTable({
						'dom': 'Bfrtip',
						'responsive':true,
						'paging': true,
						'lengthChange': false,
						'pageLength': 10,
						'searching': true	,
						'ordering': true,
						'order': [],
						'info': false,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
					});

					var table = $('#tableClinic').DataTable({
						'dom': 'Bfrtip',
						'responsive':true,
						'paging': true,
						'lengthChange': false,
						'pageLength': 10,
						'searching': true	,
						'ordering': true,
						'order': [],
						'info': false,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
					});

					var table = $('#tableThorax').DataTable({
						'dom': 'Bfrtip',
						'responsive':true,
						'paging': true,
						'lengthChange': false,
						'pageLength': 10,
						'searching': true	,
						'ordering': true,
						'order': [],
						'info': false,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
					});

					var table = $('#tableAudiometri').DataTable({
						'dom': 'Bfrtip',
						'responsive':true,
						'paging': true,
						'lengthChange': false,
						'pageLength': 10,
						'searching': true	,
						'ordering': true,
						'order': [],
						'info': false,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
					});
				}
			}
		});
	}

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ['#90ee7e', '#2b908f', '#eeaaee', '#ec407a', '#7798BF', '#f45b5b',
		'#ff9800', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
		chart: {
			backgroundColor: {
				linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
				stops: [
				[0, '#2a2a2b'],
				[1, '#2a2a2b']
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

	function getActualDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year;
	}


</script>
@endsection