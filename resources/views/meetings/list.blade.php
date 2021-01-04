@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
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
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding: 0px;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
		vertical-align: middle;
		background-color: rgb(126,86,134);
		color: #FFD700;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#loading, #error { display: none; }

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
@stop
@section('header')
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">
	<div class="row" style="margin-right: 1%; margin-left: 1%;">
		<input type="hidden" id="meetingID">
		<div class="col-xs-8" style="padding-right: 0; padding-left: 0">
			<div class="input-group" style="padding-bottom: 5px;">
				<input type="text" style="text-align: center; border-color: black;" class="form-control input-lg" id="tag" name="tag" placeholder="Scan ID Card Here..." required>
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
					<i class="glyphicon glyphicon-credit-card"></i>
				</div>
			</div>
			<table id="tableAttendance" class="table table-bordered table-hover">
				<thead style="background-color: rgba(126,86,134,.7); color: white; font-size: 20px;">
					<tr>
						<th style="width: 1%">#</th>
						<th style="width: 1%">ID</th>
						<th style="width: 7%">Name</th>
						<th style="width: 4%">Department</th>
						<th style="width: 3%">Status</th>
						<th style="width: 3%">Attendance Time</th>
					</tr>
				</thead>
				<tbody id="tableAttendanceBody">
				</tbody>
				<tfoot>
				</tfoot>
			</table>
		</div>
		<div class="col-xs-4">
			<div class="box box-solid">
				<div class="box-header" style="background-color: rgba(126,86,134,.7);">
					<center><span style="font-size: 22px; font-weight: bold; color: black;" id="meetingSubject"></span></center>
				</div>
				<ul class="nav nav-pills nav-stacked">
					<li>
						<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Organizer
							<span class="pull-right text-green" id="meet1">0</span>
						</a>
					</li>
					<li>
						<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Start
							<span class="pull-right text-green" id="meet2">0</span>
						</a>
					</li>
					<li>
						<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">End
							<span class="pull-right text-green" id="meet3">0</span>
						</a>
					</li>
					<li>
						<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Durasi
							<span class="pull-right text-green" id="meet4">0</span>
						</a>
					</li>
					<li>
						<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Undangan
							<span class="pull-right text-green" id="meet9">0</span>
						</a>
					</li>
					<li>
						<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Hadir
							<span class="pull-right text-green" id="meet5">0</span>
						</a>
					</li>
					<li>
						<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Belum Hadir
							<span class="pull-right text-green" id="meet6">0</span>
						</a>
					</li>
					<li>
						<a href="#" style="font-size: 18px; font-weight: bold; padding-top: 5px; padding-bottom: 5px;">Hadir Tanpa Undangan
							<span class="pull-right text-green" id="meet8">0</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalMeeting">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3>Select a Meeting</h3></center>
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<select class="form-control select2" onchange="fetchAttendance(value)" name="id" id="id" data-placeholder="Select Meeting Here..." style="width: 100%; font-size: 20px;">
							<option></option>
							@foreach($meetings as $meeting)
							<?php
							$name = explode(' ', $meeting->name);
							if (ISSET($name[1])) {
								$employee_name = $name[0].' '.$name[1];
							}else{
								$employee_name = $name[0];
							}
							?>
							@if($meeting->id == $_GET['id'])
							<option value="{{ $meeting->id }}" selected>{{ ucwords($meeting->subject) }} || {{ $meeting->date }} ({{ $meeting->duration }})</option>
							@else
							<option value="{{ $meeting->id }}">{{ ucwords($meeting->subject) }} || {{ $meeting->date }} ({{ $meeting->duration }})</option>
							@endif
							@endforeach
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		var id = "{{$_GET['id']}}";
		$('#tag').val('');

		if(id == ""){
			$('#modalMeeting').modal({
				backdrop: 'static',
				keyboard: false
			});
		}
		else{
			fetchAttendance(id);
		}

		setInterval(foc, 20000);

		$('.select2').select2();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function foc(){
		$('#tag').focus();
	}

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if(this.value.length > 7){
				scanTag(this.value);
			}
			else{
				$('#tag').val("");
				$('#tag').focus();
				openErrorGritter('Error!', 'ID Card invalid');
			}
		}
	});

	function scanTag(id){
		var meeting_id = $('#meetingID').val();
		var data = {
			tag:id,
			meeting_id:meeting_id
		}

		$.post('{{ url("scan/meeting/attendance") }}', data, function(result, status, xhr){
			if(result.status){
				audio_ok.play();
				$('#tag').val("");
				$('#tag').focus();
				fetchAttendance(meeting_id);
				openSuccessGritter('Success!', result.message);
			}
			else{
				audio_error.play();
				$('#tag').val("");
				$('#tag').focus();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function fetchAttendance(id){
		$('#meetingID').val(id);
		var data = {
			id:id
		}
		$.get('{{ url("fetch/meeting/attendance") }}', data, function(result, status, xhr){
			if(result.status){
				$('#meetingSubject').text(result.attendances[0].subject);
				$('#meet1').text(result.attendances[0].organizer_name);
				$('#meet2').text(result.attendances[0].start_time);
				$('#meet3').text(result.attendances[0].end_time);
				$('#meet4').text(result.attendances[0].diff+ " Minute(s)");

				$('#modalMeeting').modal('hide');

				var tableData = "";
				var no = 1;

				$('#tableAttendanceBody').html("");

				var total_undangan = 0;
				var total_hadir = 0;
				var total_belum_hadir = 0;
				var total_hadir_tanpa = 0;				

				$.each(result.attendances, function(key, value){
					if (no % 2 === 0 ) {
						color = 'style="background-color: #fffcb7"';
					} else {
						color = 'style="background-color: #ffd8b7"';
					}

					if(value.status == '0' || value.status == '1'){
						total_undangan += 1;
					}
					if(value.status == '2' || value.status == '1'){
						total_hadir += 1;
					}
					if(value.status == '0'){
						total_belum_hadir += 1;
					}
					if(value.status == '2'){
						total_hadir_tanpa += 1;
					}

					tableData += "<tr "+color+">";
					tableData += "<td style='font-size: 18px;'>"+no+"</td>";
					tableData += "<td style='font-size: 18px;'>"+value.employee_id+"</td>";
					tableData += "<td style='font-size: 18px;'>"+value.name+"</td>";
					tableData += "<td style='font-size: 18px;'>"+value.department+"</td>";
					if(value.status == 0){
						tableData += "<td style='background-color: RGB(255,204,255);'>"+value.status+" - Belum Hadir</td>";
					}
					if(value.status == 1){
						tableData += "<td style='background-color: RGB(204,255,255);'>"+value.status+" - Hadir</td>";
					}
					if(value.status == 2){
						tableData += "<td style='background-color: RGB(204,255,255);'>"+value.status+" - Hadir</td>";
					}
					if(value.attend_time == null){
						tableData += "<td style='font-size: 18px;'>-</td>";
					}
					else{
						tableData += "<td style='font-size: 18px;'>"+value.attend_time+"</td>";						
					}
					tableData += "</tr>";
					no++;
				});

				$('#meet5').text(total_hadir);
				$('#meet6').text(total_belum_hadir);
				$('#meet8').text(total_hadir_tanpa);
				$('#meet9').text(total_undangan);

				$('#tableAttendanceBody').append(tableData);

				var table = $('#tableAttendance').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'paging': false,
					'lengthChange': false,
					// 'pageLength': 10,
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
				$('#tag').focus();
				openSuccessGritter('Success!', result.message);
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

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

	$.date = function(dateObject) {
		var d = new Date(dateObject);
		var day = d.getDate();
		var month = d.getMonth() + 1;
		var year = d.getFullYear();
		if (day < 10) {
			day = "0" + day;
		}
		if (month < 10) {
			month = "0" + month;
		}
		var date = day + "/" + month + "/" + year;

		return date;
	};
</script>
@endsection
