@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	#loading, #error { display: none; }
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-md-8">
			<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
				<p style="position: absolute; color: White; top: 42%; left: 45%;">
					<span style="font-size: 4vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
				</p>
			</div>
			<div>
				<div class="col-md-12" style="background-color: #90ee7e; text-align: center; font-size: 2.5vw; margin-bottom: 15px;" id="employee">
					@if($employee == '-')
					-
					@else
					{{ $employee->employee_id }} - {{ $employee->name }}
					@endif
				</div>
				<div class="col-md-12" style="background-color: #ff851b; vertical-align: middle; text-align: center; font-size: 16vw; font-weight: bold; margin-bottom: 15px; height: 450px;" id="temperature">
					0&deg;C
				</div>
				<div class="col-md-12" style="background-color: #90ee7e; text-align: center; font-size: 7vw; font-weight: bold; padding:0;" id="kondisi">
					-
				</div>
				<div class="col-md-12">
					<input type="text" id="tag" style="background-color: #3c3c3c; border-color: #3c3c3c;">
				</div>
				<input type="hidden" id="employee_tag" value="{{ $employee == '-' ? '' : $employee->tag }}">
			</div>
		</div>
		<div class="col-md-4" style="padding-left: 0;">
			<img style="width: 100%;" src="{{ asset('images/omron_manual.jpg') }}">
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		setInterval(fetchOmron, 1000);
	});

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

	function fetchOmron(){
		$('#tag').focus();
		var employee_tag = $('#employee_tag').val();
		var data = {
			id:3,
			tag:employee_tag
		}
		console.log(data);
		$.get('{{ url("fetch/temperature/omron") }}', data, function(result, status, xhr) {
			$('#temperature').html(result.suhu+"&deg;C");
			if(result.suhu > 37.5){
				$('#kondisi').text('SUSPECT');	
			}
			else{
				$('#kondisi').text('GOOD');			
			}
			if(result.suhu == 0){
				$('#kondisi').text('-');					
			}
		});
	}

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag").val().length >= 10){
				scanTag($('#tag').val());
			}
			else{
				$('#tag').val("");
				$('#tag').focus();				
				openErrorGritter('Error', 'Tag karyawan tidak sesuai');
			}
		}
	});

	function scanTag(id){
		$('#loading').show();
		var data = {
			id:3,
			tag:id
		}
		$.post('{{ url("input/temperature/omron_operator") }}', data, function(result, status, xhr) {
			if(result.status){
				if(result.cat == 'login'){
					$('#employee').text(result.employee.employee_id+" - "+result.employee.name);
					$('#employee_tag').val(result.employee.tag);				
				}
				else{
					$('#employee_tag').val("-");			
					$('#employee').text("-");
				}

				$('#tag').val("");
				$('#tag').focus();
				$('#loading').hide();
				openSuccessGritter('Success', result.cat+" berhasil");
			}
			else{
				$('#loading').hide();
				$('#tag').val("");
				$('#tag').focus();				
				openErrorGritter('Error', result.message);
			}
		});
	}

</script>
@endsection