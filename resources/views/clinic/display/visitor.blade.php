@extends('layouts.visitor')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
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
		border:none;
		background-color: rgba(126,86,134);
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
	.content{
		color: white;
		font-weight: bold;
	}
	.patient-duration{
		margin: 0px;
		padding: 0px;
	}
	#ada{
		background-color: rgba(118,255,3,.65);
	}
	#tidak-ada{
		background-color: rgba(255,0,0,.85);
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-4">
			<table class="table" id="tableMedic" style="font-size: 2vw;">
				<tbody id="tableBodyMedic">
				</tbody>
			</table>
		</div>
		<div class="col-xs-4 pull-right">
			<table class="table" id="tableDoctor" style="font-size: 2vw;">
				<tbody id="tableBodyDoctor">
				</tbody>
			</table>
		</div>
		<div class="col-xs-12">
			<h2 style="text-align: center; font-weight: bold;">CLINIC VISITOR LIST</h2>
			<table class="table table-bordered" id="tableList">
				<thead>
					<tr>
						<th style="width: 4%;">#</th>
						<th style="width: 10%; text-align: center;">NIK</th>
						<th style="width: 21%; text-align: center;">Name</th>
						<th style="width: 21%; text-align: center;">Department</th>
						<th style="width: 14%; text-align: center;">In Time</th>
						<th style="width: 10%; text-align: center;">Duration</th>
						<th style="width: 20%; text-align: center;">Keperluan</th>
					</tr>					
				</thead>
				<tbody id="tableBodyList">
				</tbody>
			</table>
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



<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fillVisitor();
		setTime();

		setInterval(fillVisitor, 10000);
		setInterval(setTime, 1000);

	});

	var in_time = [];
	function setTime() {
		for (var i = 0; i < in_time.length; i++) {
			var duration = diff_seconds(new Date(), in_time[i]);
			document.getElementById("hours"+i).innerHTML = pad(parseInt(duration / 3600));
			document.getElementById("minutes"+i).innerHTML = pad(parseInt((duration % 3600) / 60));
			document.getElementById("seconds"+i).innerHTML = pad(duration % 60);
		}
	}

	function pad(val) {
		var valString = val + "";
		if (valString.length < 2) {
			return "0" + valString;
		} else {
			return valString;
		}
	}

	function diff_seconds(dt2, dt1){
		var diff = (dt2.getTime() - dt1.getTime()) / 1000;
		return Math.abs(Math.round(diff));
	}

	function fillVisitor(){

		$('#tableBodyMedic').html("");
		$('#tableBodyDoctor').html("");

		var paramedic = "";
		paramedic += '<tr>';
		paramedic += '<td style="border-width:0px;">PARAMEDIC</td>';
		paramedic += '<td style="border-width:0px;"> : </td>';
		paramedic += '<td style="border-width:0px;"><mark id="ada">Elis Kurniawati</mark></td>';
		paramedic += '</tr>';

		var doctor = "";
		doctor += '<tr>';
		doctor += '<td style="border-width:0px;">DOCTOR</td>';
		doctor += '<td style="border-width:0px;"> : </td>';
		doctor += '<td style="border-width:0px;"><mark id="tidak-ada">Tidak Ada</mark></td>';
		doctor += '</tr>';

		$('#tableBodyMedic').append(paramedic);
		$('#tableBodyDoctor').append(doctor);

		$.get('{{ url("fetch/display_patient") }}', function(result, status, xhr){
			if(result.status){
				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$('#tableBodyList').html("");

				var tableData = "";
				var count = 0;
				in_time = [];
				for (var i = 0; i < result.visitor.length; i++) {
					tableData += '<tr>';
					tableData += '<td>'+ ++count +'</td>';
					tableData += '<td>'+ result.visitor[i].employee_id +'</td>';
					tableData += '<td>'+ result.visitor[i].name +'</td>';
					tableData += '<td>'+ result.visitor[i].department +'</td>';
					tableData += '<td>'+ result.visitor[i].in_time +'</td>';
					in_time.push(new Date(result.visitor[i].in_time));
					tableData += '<td><p class="patient-duration">';
					tableData += '<label id="hours'+ i +'">'+ pad(parseInt(diff_seconds(new Date(), in_time[i]) / 3600)) +'</label>:';
					tableData += '<label id="minutes'+ i +'">'+ pad(parseInt((diff_seconds(new Date(), in_time[i]) % 3600) / 60)) +'</label>:';
					tableData += '<label id="seconds'+ i +'">'+ pad(diff_seconds(new Date(), in_time[i]) % 60) +'</label>';
					tableData += '</p></td>';
					if(result.visitor[i].purpose == null){
						tableData += '<td>'+ '-' +'</td>';
					}else{
						tableData += '<td>'+ result.visitor[i].purpose +'</td>';
					}
					tableData += '</tr>';
				}
				$('#tableBodyList').append(tableData);
				$('#tableList').DataTable({
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
					'pageLength': 10,
					'searching': true,
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

		});
	}

</script>
@endsection