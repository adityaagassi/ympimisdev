@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/fixedHeader.dataTables.min.css") }}" rel="stylesheet">
<style type="text/css">
	html {
		transition: color 300ms, background-color 300ms;
	}


	thead>tr>th{
		text-align:center;
		color:white;
		font-weight: bold;
		font-size: 1vw;
		border-bottom: 1px solid white;
		background-color: #955da8;
		padding: 2px;
		border-right: 1px solid white;
	}
	tbody>tr>td{
		text-align:center;
		color:white;
		/*border-top: 1px solid #333333 !important;*/
		/*font-weight: bold;*/
		font-size: 0.7vw;
	}

	tbody>tr>th{
		text-align:left;
		color:black;
		/*border-top: 1px solid #333333 !important;*/
		font-weight: bold;
		font-size: 16px;
	}

	.datepicker table tr td span.focused, .datepicker table tr td span:hover {
		background: #955da8;
	}
	tfoot>tr>th{
		text-align:center;
		color:white;
	}
	td:hover {
		overflow: visible;
	}
	table {
		/*background-color: #212121;*/
	}

	#master>tbody>tr>td {
		padding: 2px;
	}

	.card-title {
		font-family: inherit;
		font-weight: 500;
		line-height: 1.2;
		font-size: 25px;
	}

	/*table.fixedHeader-floating{
		background-color: #212121 !important;
		color: white;
		}*/

		#loading, #error { display: none; }
	</style>
	@stop
	@section('header')
	<section class="content-header">
		<input type="hidden" id="green">
		<h1>
			{{ $page }}
		</h1>
	</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-2 pull-left">
				<button type="button" class="btn btn-success btn-sm">History</button>
			</div>
			<div class="col-xs-2 pull-right">
				<div class="input-group date">
					<div class="input-group-addon bg-purple" style="border: none;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="tgl" onchange="change_date(this)" placeholder="Pilih Bulan">
				</div>
			</div>
		</div>
		<!-- DAILY  -->
		<div style="padding: 0px" class="col-xs-12">
			<div style="overflow-x: scroll; overflow-y: hidden; border: 1px solid yellow; width: 50%; float: left">
				<label style="color: white">DAILY</label>
				<table id="master_utl" width="1000px">
					<thead id="head_master_utl">
					</thead>

					<tbody id="body_master_utl">
					</tbody>
				</table>
			</div>
			<div style="overflow-x: scroll; overflow-y: hidden; border: 1px solid yellow; width: 50%; float: left">
				<label style="color: white">DAILY</label>
				<table id="master_mp" width="1000px">
					<thead id="head_master_mp">
					</thead>

					<tbody id="body_master_mp">
					</tbody>
				</table>
			</div>
		</div>

		<!-- WEEKLY -->
		<div style="padding: 0px" class="col-xs-12">
			<div style="overflow-x: scroll; overflow-y: hidden; border: 1px solid yellow; width: 50%; float: left">
				<label style="color: white">WEEKLY</label>
				<table id="weekly_utl" width="1000px">
					<thead id="head_weekly_utl">
					</thead>

					<tbody id="body_weekly_utl">
					</tbody>
				</table>
			</div>
			<div style="overflow-x: scroll; overflow-y: hidden; border: 1px solid yellow; width: 50%; float: left">
				<label style="color: white">WEEKLY</label>
				<table id="weekly_utl" width="1000px">
					<thead id="head_weekly_mp">
					</thead>

					<tbody id="body_weekly_mp">
					</tbody>
				</table>
			</div>
		</div>

		<!-- MONTHLY -->
		<div style="padding: 0px" class="col-xs-12">
			<div style="overflow-x: scroll; overflow-y: hidden; border: 1px solid yellow; width: 50%; float: left">
				<label style="color: white">MONTHLY</label>
				<table id="monthly_utl" width="1000px">
					<thead id="head_monthly_utl">
					</thead>

					<tbody id="body_monthly_utl">
					</tbody>
				</table>
			</div>
			<div style="overflow-x: scroll; overflow-y: hidden; border: 1px solid yellow; width: 50%; float: left">
				<label style="color: white">MONTHLY</label>
				<table id="weekly_utl" width="1000px">
					<thead id="head_monthly_mp">
					</thead>

					<tbody id="body_monthly_mp">
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.fixedHeader.min.js") }}"></script>
<script src="{{ url("js/dataTables.responsive.min.js") }}"></script>

<script src="{{ url("js/highcharts-gantt.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>

<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var mons = ['april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december', 'january', 'february', 'march']

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		getData();
	})

	function getData() {
		var today = new Date(),
		day = 1000 * 60 * 60 * 24,
		map = Highcharts.map,
		dateFormat = Highcharts.dateFormat;
		var series = [];

		today.setUTCHours(0);
		today.setUTCMinutes(0);
		today.setUTCSeconds(0);
		today.setUTCMilliseconds(0);
		today = today.getTime();

		var data = {

		}

		$.get('{{ url("fetch/maintenance/pm/schedule") }}', data, function(result, status, xhr){
			$("#head_master_utl").empty();
			$("#body_master_utl").empty();
			$("#head_master_mp").empty();
			$("#body_master_mp").empty();

			body_utl = "";
			body_mp = "";

			head_utl = "";
			head_mp = "";

			//HEAD UTILITY

			head_utl += "<tr>";
			head_utl += "<th>Machine Name</th>";
			for (var i = 1; i <= parseInt(result.dt); i++) {
				head_utl += "<th>"+i+"</th>";
			}
			head_utl += "</tr>";
			$("#head_master_utl").append(head_utl);
			$("#head_master_mp").append(head_utl);

			// HEAD MP

			// head_mp += "<tr>";
			// head_mp += "<th>Machine Name</th>";
			// for (var i = 1; i <= parseInt(result.dt); i++) {
			// 	head_mp += "<th>"+i+"</th>";
			// }
			// head_mp += "</tr>";

			$.each(result.daily, function(index, value){
				if (value.category == 'Utility') {
					body_utl += "<tr>";
					body_utl += "<td>"+value.machine_name+"</td>";
					for (var i = 1; i <= parseInt(result.dt); i++) {
						if (i <= parseInt(result.now)) {
							color = 'red';
						} else {
							color = 'green';
						}

						body_utl += "<td style='background-color:"+color+"'></td>";
					}
					body_utl += "</tr>";
				} else if (value.category == "Production Machine") {
					body_mp += "<tr>";
					body_mp += "<td>"+value.machine_name+"</td>";
					for (var z = 1; z <= parseInt(result.dt); z++) {
						if (z <= parseInt(result.now)) {
							color = 'red';
						} else {
							color = 'green';
						}

						body_mp += "<td style='background-color:"+color+"'></td>";
					}
					body_mp += "</tr>";
				}		
			});

			$("#body_master_utl").append(body_utl);
			$("#body_master_mp").append(body_mp);

			// -----  WEEKLY ---------

			$("#head_weekly_utl").empty();
			$("#body_weekly_utl").empty();

			body_w_utl = "";
			head_w_utl = "";

			body_w_mp = "";
			// head_w_mp = "";

			head_w_utl += "<tr>";
			head_w_utl += "<th>Machine Name</th>";

			// head_w_mp += "<tr>";
			// head_w_mp += "<th>Machine Name</th>";

			for (var i = 1; i < result.week.length; i++) {
				head_w_utl += "<th>"+result.week[i].week_name+"</th>";
			}
			head_w_utl += "</tr>";
			$("#head_weekly_utl").append(head_w_utl);
			$("#head_weekly_mp").append(head_w_utl);

			$.each(result.weekly, function(index, value){
				if (value.category == 'Utility') {
					body_w_utl += "<tr>";
					body_w_utl += "<td style='width:20%'>"+value.machine_name+"</td>";
					for (var i = 1; i < result.week.length; i++) {
						// if (i <= parseInt(result.now)) {
						// 	color = 'red';
						// } else {
							color = 'green';
						// 	}

						body_w_utl += "<td style='background-color:"+color+"; width:20%'></td>";
					}
					body_w_utl += "</tr>";
				} else if (value.category == "Production Machine") {
					body_w_mp += "<tr>";
					body_w_mp += "<td style='width:20%'>"+value.machine_name+"</td>";
					for (var i = 1; i < result.week.length; i++) {
					// 	if (i <= parseInt(result.now)) {
					// 		color = 'red';
					// 	} else {
						color = 'green';
						// 	}

						body_w_mp += "<td style='background-color:"+color+"; width:20%'></td>";
					}
					body_w_mp += "</tr>";
				}		
			});

			$("#body_weekly_utl").append(body_w_utl);
			$("#body_weekly_mp").append(body_w_mp);


			// -----  MONTHLY ---------

			$("#head_monthly_utl").empty();
			$("#body_monthly_utl").empty();

			body_m_utl = "";
			head_m_utl = "";

			body_m_mp = "";
			// head_w_mp = "";

			head_m_utl += "<tr>";
			head_m_utl += "<th>Machine Name</th>";

			// head_w_mp += "<tr>";
			// head_w_mp += "<th>Machine Name</th>";

			for (var i = 1; i < result.mon.length; i++) {
				head_m_utl += "<th>"+result.mon[i].mon+"</th>";
			}
			head_m_utl += "</tr>";
			$("#head_monthly_utl").append(head_m_utl);
			$("#head_monthly_mp").append(head_m_utl);

			$.each(result.monthly, function(index, value){
				if (value.category == 'Utility') {
					body_m_utl += "<tr>";
					body_m_utl += "<td style='width:20%'>"+value.machine_name+"</td>";
					for (var i = 1; i < result.mon.length; i++) {
						// if (i <= parseInt(result.now)) {
						// 	color = 'red';
						// } else {
							color = 'green';
						// 	}

						body_m_utl += "<td style='background-color:"+color+"; width:20%'></td>";
					}
					body_w_utl += "</tr>";
				} else if (value.category == "Production Machine") {
					body_m_mp += "<tr>";
					body_m_mp += "<td style='width:20%'>"+value.machine_name+"</td>";
					for (var i = 1; i < result.mon.length; i++) {
					// 	if (i <= parseInt(result.now)) {
					// 		color = 'red';
					// 	} else {
						color = 'green';
						// 	}

						body_m_mp += "<td style='background-color:"+color+"; width:20%'></td>";
					}
					body_m_mp += "</tr>";
				}		
			});

			$("#body_monthly_utl").append(body_m_utl);
			$("#body_monthly_mp").append(body_m_mp);
		})
}

$('#tgl').datepicker({
	autoclose: true,
	format: "yyyy-mm",
	startView: "months", 
	minViewMode: "months",
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