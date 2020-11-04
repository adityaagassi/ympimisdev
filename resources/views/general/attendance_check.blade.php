@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	tbody>tr>td{
		text-align:center;
		vertical-align: middle;
		font-weight: bold;
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
	.blink {
		animation-duration: 1s;
		animation-name: blink;
		animation-iteration-count: infinite;
		animation-direction: alternate;
		animation-timing-function: ease-in-out;
	}
	@keyframes blink {
		50% {
			opacity: 1;
		}
		100% {
			opacity: 0;
		}
	}
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i><br>Loading...</span>
			</center>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-7">
			<span id="purpose_code" style="color:yellow; font-weight: bold; font-size: 2vw;"></span><span style="color: yellow; font-weight: bold; font-size: 2vw;" id="counter">999/999</span>
		</div>
		<div class="col-xs-5 pull-right" style="text-align: right;">
			<input id="tag" type="text" style="border:0; background-color: #3c3c3c; width: 50px; text-align: center; font-size: 1vw">
		</div>
		<div class="col-xs-12" id="container">
			{{-- <button class="btn btn-success pull-right" style="margin-left: 5px;"><i class="fa fa-plus"></i> </button> --}}
		</div>
	</div>
	<input type="hidden" id="purpose_code"> 
</section>

<div class="modal fade" id="modalPurpose">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label>Pilih Jenis Kehadiran</label>
						<select class="form-control select2" onchange="selectPurpose(value)" id="selectPurpose" data-placeholder="Pilih Lokasi Anda..." style="width: 100%; font-size: 20px;">
							<option></option>
							@foreach($purposes as $purpose)
							<option value="{{ $purpose->purpose_code }}">{{ $purpose->purpose_code }}</option>
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
		$('#tag').val('');
		$('.select2').prop('selectedIndex', 0).change();
		$('.select2').select2();
		$('#modalPurpose').modal({
			backdrop: 'static',
			keyboard: false
		});
	});

	function selectPurpose(id){
		$('#purpose_code').val(id);
		$('#modalPurpose').modal('hide');
		fetchAttendanceList();
	}

	function focusTag(){
		$('#tag').focus();
	}

	function scanAttendance(id){
		var purpose_code = $('#purpose_code').val();
		var data = {
			tag:id,
			purpose_code:purpose_code
		}
		$.post('{{ url("scan/general/attendance_check") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success', result.message);
				fetchAttendanceList();
				audio_ok.play();
				$('#tag').val('');
				$('#tag').focus();
			}
			else{
				$('#tag').val('');
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag").val().length >= 9){
				scanAttendance($("#tag").val());
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', 'Panjang tag tidak sesuai');
				$("#tag").val('');
			}
		}
	});

	function fetchAttendanceList(){
		var purpose_code = $('#purpose_code').val();
		var data = {
			purpose_code:purpose_code
		}
		$.get('{{ url("fetch/general/attendance_check") }}', data, function(result, status, xhr){
			if(result.status){
				var count_ok = 0;
				var count_all = 0;
				setInterval(focusTag, 1000);
				var attendance_data = "";
				$('#container').html("");
				$.each(result.attendance_lists, function(key, value){
					var name = value.NAME;

					if(key == 0){
						attendance_data += '<div class="col-xs-12" style="padding:2px;padding-top:0px">';
						if(value.attend_date == null){
							attendance_data += '<table class="table table-bordered" style="background-color: #ffccff; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
						}else{
							count_ok += 1;
							attendance_data += '<table class="table table-bordered blink" style="background-color: #ccff90; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
						}
						attendance_data += '<tbody>';
						attendance_data += '<tr>';
						attendance_data += '<td style="padding: 0; width:70%; font-size:3vw;">'+value.employee_id+'</td>';
						attendance_data += '<td style="padding: 0; width:30%; font-size:3vw;">'+value.department+'</td>';
						attendance_data += '</tr>';
						attendance_data += '<tr>';
						attendance_data += '<td colspan="2" style="padding: 0; font-size:5vw;">'+name.substring(0,30).toUpperCase()+'</td>';
						attendance_data += '</tr>';
						attendance_data += '</tbody>';
						attendance_data += '</table>';
						attendance_data += '</div>';
					}
					else{						
						attendance_data += '<div class="col-xs-2" style="padding:2px;padding-top:0px">';
						if(value.attend_date == null){
							attendance_data += '<table class="table table-bordered" style="background-color: #ffccff; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
						}else{
							count_ok += 1;
							attendance_data += '<table class="table table-bordered" style="background-color: #ccffff; width: 100%;padding-bottom:0px;margin-bottom:2px;">';
						}
						attendance_data += '<tbody>';
						attendance_data += '<tr>';
						attendance_data += '<td style="padding: 0; width:70%;">'+value.employee_id+'</td>';
						attendance_data += '<td style="padding: 0; width:30%;">'+value.department+'</td>';
						attendance_data += '</tr>';
						attendance_data += '<tr>';
						attendance_data += '<td colspan="2" style="padding: 0;">'+name.substring(0,30)+'</td>';
						attendance_data += '</tr>';
						attendance_data += '</tbody>';
						attendance_data += '</table>';
						attendance_data += '</div>';
					}

					count_all += 1;

				});
				$('#purpose_code').text(result.attendance_lists[0].purpose_code);

				console.log(result.attendance_lists[0].purpose_code);
				$('#counter').text(' ('+count_ok+' of '+count_all+')');
				$('#container').append(attendance_data);
			}
			else{
				audio_error.play();
				openErrorGritter('Error!', result.message);
			}
		});
	}
	
	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

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