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

	.laktasi {
		width: 100px;
		height: 160px;
		background-color: rgba(118,255,3,.6);
		border-radius: 15px;
		margin-top: 15px;
		display: inline-block;
		border: 2px solid white;
	}

	.laktasi img {
		width: 85px;
		height: 110px;
		margin-top: 10px; 
		height:auto;
		display: block;
		margin-left: auto;
		margin-right: auto;
		vertical-align:middle;
	}

	.bed {
		width: 100px;
		height: 160px;
		background-color: rgba(255,0,0,.85);
		border-radius: 15px;
		margin-top: 15px;
		display: inline-block;
		border: 2px solid white;
	}

	.bed img {
		width: 85px;
		height: 110px;
		margin-top: 10px; 
		height:auto;
		display: block;
		margin-left: auto;
		margin-right: auto;
		vertical-align:middle;
	}

	.content-wrapper {
		padding: 0px !important;
	}

	.text_stat {
		color: white;
		text-align: center;
		font-weight: bold;
		font-size: 15px;
		vertical-align: top;
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
	<div class="row">
		<div class="col-xs-4" style="padding-top: 2.5%;">
			<table class="table" id="tableMedic" style="font-size: 2vw; padding: 0px;">
				<tbody id="tableBodyMedic">
				</tbody>
			</table>
		</div>
		<div class="col-xs-6 pull-right">
			<div class="col-xs-2" style="padding: 0px;">
				<div class="laktasi" id="laktasi-1">
					<img src="{{ url("images/Laktasi.png") }}">
					<p class='text_stat' id='text-laktasi-1'>VACANT</p>
				</div>
			</div>
			<div class="col-xs-2" style="padding: 0px;">
				<div class="laktasi" id="laktasi-2">
					<img src="{{ url("images/Laktasi.png") }}">
					<p class='text_stat' id='text-laktasi-2'>VACANT</p>
				</div>
			</div>
			<div class="col-xs-2" style="padding: 0px;">
				<div class="laktasi" id="laktasi-3">
					<img src="{{ url("images/Laktasi.png") }}">
					<p class='text_stat' id='text-laktasi-3'>VACANT</p>
				</div>
			</div>
			<div class="col-xs-2" style="padding: 0px;">
				<div class="laktasi" id="laktasi-4">
					<img src="{{ url("images/Laktasi.png") }}">
					<p class='text_stat' id='text-laktasi-4'>VACANT</p>
				</div>
			</div>
			<div class="col-xs-2" style="padding: 0px;">
				<div class="bed" id="bed-1">
					<img src="{{ url("images/Bed.png") }}">
					<p class='text_stat' id='text-bed-1'>VACANT</p>
				</div>
			</div>
			<div class="col-xs-2" style="padding: 0px;">
				<div class="bed" id="bed-2">
					<img src="{{ url("images/Bed.png") }}">
					<p class='text_stat' id='text-bed-2'>VACANT</p>
				</div>
			</div>
		</div>
		{{-- <div class="col-xs-2">
			<table class="table" id="tableQty" style="font-size: 2vw; padding: 0px;">
				<tbody id="tableBodyQty">
				</tbody>
			</table>
		</div> --}}
		<div class="col-xs-12" style="padding-top: 2%;">
			<table class="table table-bordered" id="tableList">
				<thead>
					<tr>
						<th style="width: 4%;">#</th>
						<th style="width: 10%; text-align: center;">NIK</th>
						<th style="width: 21%; text-align: center;">Name</th>
						<th style="width: 21%; text-align: center;">Section</th>
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

		var laktasi = [];
		var bed = [];

		var paramedic = "";
		paramedic += '<tr>';
		paramedic += '<td style="border-width:0px;">PARAMEDIC</td>';
		paramedic += '<td style="border-width:0px;">:</td>';
		paramedic += '<td style="border-width:0px;"><mark id="ada">Elis Kurniawati</mark></td>';
		paramedic += '</tr>';
		paramedic += '<tr>';
		paramedic += '<td style="border-width:0px;">DOCTOR</td>';
		paramedic += '<td style="border-width:0px;">:</td>';
		paramedic += '<td style="border-width:0px;"><mark id="tidak-ada">Tidak Ada</mark></td>';
		paramedic += '</tr>';

		$('#tableBodyMedic').append(paramedic);

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
					tableData += '<td>'+ (result.visitor[i].name || 'Not Found') +'</td>';
					tableData += '<td>'+ (result.visitor[i].section || 'Not Found') +'</td>';
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


					if(result.visitor[i].bed == 'Yes'){
						bed.push(result.visitor[i].bed);
					}


					if(result.visitor[i].purpose == 'Laktasi'){
						bed.push(result.visitor[i].purpose);
					}





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
					'searching': false,
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