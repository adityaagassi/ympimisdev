@extends('layouts.master')
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
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding: 0px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#loading, #error { display: none; }

	.blink {
		-webkit-animation: blinking 1s infinite;  /* Safari 4+ */
		-moz-animation: blinking 1s infinite;  /* Fx 5+ */
		-o-animation: blinking 1s infinite;  /* Opera 12+ */
		animation: blinking 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes blinking {
		0%, 49% {
			background-color: rgb(117, 209, 63);
		}
		50%, 100% {
			background-color: none;
		}
	}

	.blink2 {
		-webkit-animation: blinking_dua 1s infinite;  /* Safari 4+ */
		-moz-animation: blinking_dua 1s infinite;  /* Fx 5+ */
		-o-animation: blinking_dua 1s infinite;  /* Opera 12+ */
		animation: blinking_dua 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes blinking_dua {
		0%, 49% {
			background-color: rgb(255, 0, 0);
		}
		50%, 100% {
			background-color: none;
		}
	}
</style>
@stop
@section('header')
{{-- <section class="content-header">
	<h1>
		{{ $title }}
		<small>WIP Control <span class="text-purple"> 仕掛品管理</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>

		</li>
	</ol>
</section> --}}
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">	
			<table id="ququeTable" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th style="font-size: 25px;" >Key</th>
						<th style="font-size: 25px;" id="head_mesin1">Machine 1</th>
						<th style="font-size: 25px;" id="head_mesin2">Machine 2</th>
						<th style="font-size: 25px;" id="head_mesin3">Machine 3</th>
						<th style="font-size: 25px;" id="head_mesin4">Machine 4</th>
						<th style="font-size: 25px;" id="head_mesin5">Machine 5</th>
						<th style="font-size: 25px;" id="head_mesin6">Machine 6</th>
					</tr>
					<tr>
						<th style="font-size: 25px;">Status</th>						
						<th style="font-size: 25px;" id="status_mesin1">-</th>
						<th style="font-size: 25px;" id="status_mesin2">-</th>
						<th style="font-size: 25px;" id="status_mesin3">-</th>
						<th style="font-size: 25px;" id="status_mesin4">-</th>
						<th style="font-size: 25px;" id="status_mesin5">-</th>
						<th style="font-size: 25px;" id="status_mesin6">-</th>
					</tr>
					<tr>
						<th style="font-size: 25px;">Duration</th>						
						<th style="font-size: 25px;" id="dur_mesin1">00:00:00</th>
						<th style="font-size: 25px;" id="dur_mesin2">00:00:00</th>
						<th style="font-size: 25px;" id="dur_mesin3">00:00:00</th>
						<th style="font-size: 25px;" id="dur_mesin4">00:00:00</th>
						<th style="font-size: 25px;" id="dur_mesin5">00:00:00</th>
						<th style="font-size: 25px;" id="dur_mesin6">00:00:00</th>
					</tr>
				</thead>
				<tbody id="tableBody" style="font-weight: bold; font-size: 18px">
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="C-1">C-1</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="C-2">C-2</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="C-3">C-3</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="C-4">C-4</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="C-5">C-5</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="D-1">D-1</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="D-2">D-2</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="D-3">D-3</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="D-4">D-4</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="D-5">D-5</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="E-1">E-1</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="E-2">E-2</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="E-3">E-3</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="E-4">E-4</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="E-5">E-5</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="E-6">E-6</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="E-7">E-7</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="E-8">E-8</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="F-1">F-1</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="F-2">F-2</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="F-3">F-3</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="F-4">F-4</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="G-1">G-1</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="G-2">G-2</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="H-1">H-1</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="H-2">H-2</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="H-3">H-3</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="H-4">H-4</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="H-5">H-5</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="J-1">J-1</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="J-2">J-2</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="J-3">J-3</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="J-4">J-4</td></tr>
					<tr><td style="vertical-align: middle; font-size: 20px; font-weight: bold; padding:0;" class="konten" id="J-6">J-6</td></tr>

				</tbody>
			</table>
		</div>
	</div>


</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var keys = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.konten').each(function () {
			keys.push($(this).text());
		});
		create_content_container();
		setInterval(getMachineStatus, 1000);
		// setInterval(getMachineKey, 1000);
	});

	function getMachineStatus() {
		var tmp, tes = 0, s;
		kunci = [];
		$.get('{{ url("fetch/middle/get_barrel_machine") }}', function(result, status, xhr){

			//  ------------------ HEADS ----------------

			$.each(result.datas, function(index, value) {
				if(value.status == 'running'){
					$("#head_mesin"+value.machine).removeClass("blink2");
					$("#status_mesin"+value.machine).removeClass("blink2");
					$("#dur_mesin"+value.machine).removeClass("blink2");
					if(value.hour >= 3) {
						$("#head_mesin"+value.machine).addClass("blink");
						$("#status_mesin"+value.machine).addClass("blink");
						$("#dur_mesin"+value.machine).addClass("blink");
					}
					else {
						$("#head_mesin"+value.machine).removeClass("blink");
						$("#status_mesin"+value.machine).removeClass("blink");
						$("#duration_mesin"+value.machine).removeClass("blink");
						$("#status_mesin"+value.machine).css("background-color","rgb(204,255,255)");
						$("#dur_mesin"+value.machine).css("background-color","rgb(204,255,255)");
					}

				}

				if(value.status == 'idle'){
					$("#head_mesin"+value.machine).addClass("blink2");
					$("#status_mesin"+value.machine).addClass("blink2");
					$("#dur_mesin"+value.machine).addClass("blink2");
					$("#status_mesin"+value.machine).css("background-color","rgb(255,204,255)");
					$("#dur_mesin"+value.machine).css("background-color","rgb(255,204,255)");
				}

				if(value.status == 'racking'){
					$("#head_mesin"+value.machine).removeClass("blink");
					$("#status_mesin"+value.machine).removeClass("blink");
					$("#duration_mesin"+value.machine).removeClass("blink");
					$("#head_mesin"+value.machine).removeClass("blink2");
					$("#status_mesin"+value.machine).removeClass("blink2");
					$("#dur_mesin"+value.machine).removeClass("blink2");
					$("#status_mesin"+value.machine).css("background-color","rgb(255,255,102)");
					$("#dur_mesin"+value.machine).css("background-color","rgb(255,255,102)");
				}

				$("#status_mesin"+value.machine).text(value.status.toUpperCase());
				$("#dur_mesin"+value.machine).text(value.duration);
			});


			//  -------------- CONTENTS ------------

			for (var i = 0; i < keys.length; i++) {
				
				for (var k = 6; k >= 1; k--) {
					var d = 0;
					for (var z = 0; z < result.contents.length; z++) {
						if (d == 0) {
							if(result.contents[z].key == keys[i] && result.contents[z].machine == k){
								tmp = result.contents[z].content;
								d = 1;
							}
							else {
								tmp = "-";
							}
						}
						s = tmp;
					}
					var arr = [keys[i], k, s]; 
					kunci.push(arr);
					// $("#"+).after("<td class='kunci'></td>");
				}
			}
			// console.log(kunci);

			var r = 1;
			for (var p = 0; p < kunci.length; p++) {
				// $("#"+kunci[p][0]).after("<td class='kunci'>"+kunci[p][2]+"</td>");
				$("#"+kunci[p][0]+"_"+r).text(kunci[p][2]);
				if (r >= 6) { r = 1; } else { r++ }

				// console.log(kunci[p][0]+"_"+kunci[p][2]);
			}
		});
	}

	function create_content_container() {
		for (var i = 0; i < keys.length; i++) {
			for (var k = 1; k <= 6; k++) {
				$("#"+keys[i]).after("<td id='"+keys[i]+"_"+k+"'></td>");
			}
		}
	}


	

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