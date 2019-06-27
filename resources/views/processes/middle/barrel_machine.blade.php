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
	thead {
		background-color: rgba(126,86,134,.7);
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
			<div class="row">
				<div class="col-xs-2" style="padding:1px">
					<table class="table table-responsive table-bordered table-stripped">
						<thead id="mesin1">
							<tr>
								<th colspan="3">Machine 1</th>
							</tr>
							<tr>
								<th colspan="3" id="status1"> &nbsp;</th>
							</tr>
							<tr>
								<th colspan="3" id="duration1"> &nbsp;</th>
								
							</tr>
							<tr>
								<th>Jig</th>
								<th>Key</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody id="tbody1">
						</tbody>
					</table>
				</div>

				<div class="col-xs-2" style="padding:1px">
					<table class="table table-responsive table-bordered table-stripped">
						<thead id="mesin2">
							<tr>
								<th colspan="3">Machine 2</th>
							</tr>
							<tr>
								<th colspan="3" id="status2"> &nbsp;</th>
							</tr>
							<tr>
								<th colspan="3" id="duration2"> &nbsp;</th>
								
							</tr>
							<tr>
								<th>Jig</th>
								<th>Key</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody id="tbody2">
						</tbody>
					</table>
				</div>

				<div class="col-xs-2" style="padding:1px">
					<table class="table table-responsive table-bordered table-stripped">
						<thead id="mesin3">
							<tr>
								<th colspan="3">Machine 3</th>
							</tr>
							<tr>
								<th colspan="3" id="status3"> &nbsp;</th>
							</tr>
							<tr>
								<th colspan="3" id="duration3"> &nbsp;</th>
								
							</tr>
							<tr>
								<th>Jig</th>
								<th>Key</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody id="tbody3">
						</tbody>
					</table>
				</div>

				<div class="col-xs-2" style="padding:1px">
					<table class="table table-responsive table-bordered table-stripped">
						<thead id="mesin4">
							<tr>
								<th colspan="3">Machine 4</th>
							</tr>
							<tr>
								<th colspan="3" id="status4"> &nbsp;</th>
							</tr>
							<tr>
								<th colspan="3" id="duration4"> &nbsp;</th>
								
							</tr>
							<tr>
								<th>Jig</th>
								<th>Key</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody id="tbody4">
						</tbody>
					</table>
				</div>

				<div class="col-xs-2" style="padding:1px">
					<table class="table table-responsive table-bordered table-stripped">
						<thead id="mesin5">
							<tr>
								<th colspan="3">Machine 5</th>
							</tr>
							<tr>
								<th colspan="3" id="status5"> &nbsp;</th>
							</tr>
							<tr>
								<th colspan="3" id="duration5"> &nbsp;</th>
							</tr>
							<tr>
								<th>Jig</th>
								<th>Key</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody id="tbody5">
						</tbody>
					</table>
				</div>

				<div class="col-xs-2" style="padding:1px">
					<table class="table table-responsive table-bordered table-stripped">
						<thead id="mesin6">
							<tr>
								<th colspan="3">Machine 6</th>
							</tr>
							<tr>
								<th colspan="3" id="status6"> &nbsp;</th>
							</tr>
							<tr>
								<th colspan="3" id="duration6"> &nbsp;</th>
							</tr>
							<tr>
								<th>Jig</th>
								<th>Key</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody id="tbody6">
						</tbody>
					</table>
				</div>
			</div>

		</div>

		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-2" style="padding:1px">
					<table class="table table-responsive table-bordered table-stripped">
						<thead>
							<tr>
								<th>Jig</th>
								<th>Key</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody id="queue1">
						</tbody>
					</table>
				</div>

				<div class="col-xs-2" style="padding:1px">
					<table class="table table-responsive table-bordered table-stripped">
						<thead>
							<tr>
								<th>Jig</th>
								<th>Key</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody id="queue2">
							<tr>
								<td>asdds</td>
								<td>ddd</td>
								<td>3</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="col-xs-2" style="padding:1px">
					<table class="table table-responsive table-bordered table-stripped">
						<thead>
							<tr>
								<th>Jig</th>
								<th>Key</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody id="queue3">
							<tr>
								<td>asdds</td>
								<td>ddd</td>
								<td>1</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="col-xs-2" style="padding:1px">
					<table class="table table-responsive table-bordered table-stripped">
						<thead>
							<tr>
								<th>Jig</th>
								<th>Key</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody id="queue4">
							<tr>
								<td>asdds</td>
								<td>ddd</td>
								<td>9</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="col-xs-2" style="padding:1px">
					<table class="table table-responsive table-bordered table-stripped">
						<thead>
							<tr>
								<th>Jig</th>
								<th>Key</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody id="queue5">
							<tr>
								<td>asdds</td>
								<td>ddd</td>
								<td>7</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="col-xs-2" style="padding:1px">
					<table class="table table-responsive table-bordered table-stripped">
						<thead>
							<tr>
								<th>Jig</th>
								<th>Key</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody id="queue6">
							<tr>
								<td>asdds</td>
								<td>ddd</td>
								<td>3</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
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
		
		// setInterval(getBarrelMachine, 1000);
		getBarrelMachine();
		// setInterval(getMachineStatus, 1000);
	});

	function getBarrelMachine() {
		$.get('{{ url("fetch/middle/get_barrel") }}', function(result, status, xhr){
			var antrian = 0;

			$.each(result.machine_stat, function(index, value) {
				var jam = "" , menit = "";
				if (value.jam != 0) {
					jam = value.jam +" h";
				}

				if (value.menit != 0) {
					menit = value.menit +" min";
				}

				detik = value.detik + " sec";


				if (value.status == "idle") {
					$("#mesin"+value.machine).removeClass("blink");
					$("#mesin"+value.machine).addClass("blink2");
				}
				else if (value.status == "running" && value.jam >= 4){
					$("#mesin"+value.machine).removeClass("blink2");
					$("#mesin"+value.machine).addClass("blink");
				}
				else{
					$("#mesin"+value.machine).removeClass("blink");
					$("#mesin"+value.machine).removeClass("blink2");
				}

				$("#duration"+value.machine).text(jam+" "+menit+" "+detik);
				$("#status"+value.machine).text(value.status.toUpperCase());
			})

			for (var i = 1; i <= 6; i++) {
				$("#tbody"+i).empty();
				$("#queue"+i).empty();
			}

			$.each(result.datas, function(index, value) {
				var mesin = value.machine;
				if (value.status == 'running') {
					$("#tbody"+mesin).append("<tr><td>"+value.jig+"</td><td>"+value.model+" "+value.key+"</td><td>"+value.qty+"</td></tr>");
				}
				else {
					var d = value.remark;
					if (antrian <=2) {
						$("#queue"+mesin).append("<tr><td>"+value.jig+"</td><td>"+value.model+" "+value.key+"</td><td>"+value.qty+"</td></tr>");
					}
				}
			})
		})
	}

	// function getMachineStatus() {
	// 	var tmp, tes = 0, s;
	// 	kunci = [];
	// 	$.get('{{ url("fetch/middle/get_barrel_machine") }}', function(result, status, xhr){

	// 		//  ------------------ HEADS ----------------

	// 		$.each(result.datas, function(index, value) {
	// 			if(value.status == 'running'){
	// 				$("#head_mesin"+value.machine).removeClass("blink2");
	// 				$("#status_mesin"+value.machine).removeClass("blink2");
	// 				$("#dur_mesin"+value.machine).removeClass("blink2");
	// 				if(value.hour >= 3) {
	// 					$("#head_mesin"+value.machine).addClass("blink");
	// 					$("#status_mesin"+value.machine).addClass("blink");
	// 					$("#dur_mesin"+value.machine).addClass("blink");
	// 				}
	// 				else {
	// 					$("#head_mesin"+value.machine).removeClass("blink");
	// 					$("#status_mesin"+value.machine).removeClass("blink");
	// 					$("#duration_mesin"+value.machine).removeClass("blink");
	// 					$("#status_mesin"+value.machine).css("background-color","rgb(204,255,255)");
	// 					$("#dur_mesin"+value.machine).css("background-color","rgb(204,255,255)");
	// 				}

	// 			}

	// 			if(value.status == 'idle'){
	// 				$("#head_mesin"+value.machine).addClass("blink2");
	// 				$("#status_mesin"+value.machine).addClass("blink2");
	// 				$("#dur_mesin"+value.machine).addClass("blink2");
	// 				$("#status_mesin"+value.machine).css("background-color","rgb(255,204,255)");
	// 				$("#dur_mesin"+value.machine).css("background-color","rgb(255,204,255)");
	// 			}

	// 			if(value.status == 'racking'){
	// 				$("#head_mesin"+value.machine).removeClass("blink");
	// 				$("#status_mesin"+value.machine).removeClass("blink");
	// 				$("#duration_mesin"+value.machine).removeClass("blink");
	// 				$("#head_mesin"+value.machine).removeClass("blink2");
	// 				$("#status_mesin"+value.machine).removeClass("blink2");
	// 				$("#dur_mesin"+value.machine).removeClass("blink2");
	// 				$("#status_mesin"+value.machine).css("background-color","rgb(255,255,102)");
	// 				$("#dur_mesin"+value.machine).css("background-color","rgb(255,255,102)");
	// 			}

	// 			$("#status_mesin"+value.machine).text(value.status.toUpperCase());
	// 			$("#dur_mesin"+value.machine).text(value.duration);
	// 		});


	// 		//  -------------- CONTENTS ------------

	// 		for (var i = 0; i < keys.length; i++) {

	// 			for (var k = 6; k >= 1; k--) {
	// 				var d = 0;
	// 				for (var z = 0; z < result.contents.length; z++) {
	// 					if (d == 0) {
	// 						if(result.contents[z].key == keys[i] && result.contents[z].machine == k){
	// 							tmp = result.contents[z].content;
	// 							d = 1;
	// 						}
	// 						else {
	// 							tmp = "-";
	// 						}
	// 					}
	// 					s = tmp;
	// 				}
	// 				var arr = [keys[i], k, s]; 
	// 				kunci.push(arr);
	// 				// $("#"+).after("<td class='kunci'></td>");
	// 			}
	// 		}
	// 		// console.log(kunci);

	// 		var r = 1;
	// 		for (var p = 0; p < kunci.length; p++) {
	// 			// $("#"+kunci[p][0]).after("<td class='kunci'>"+kunci[p][2]+"</td>");
	// 			$("#"+kunci[p][0]+"_"+r).text(kunci[p][2]);
	// 			if (r >= 6) { r = 1; } else { r++ }

	// 			// console.log(kunci[p][0]+"_"+kunci[p][2]);
	// 		}
	// 	});
	// }

	// function create_content_container() {
	// 	for (var i = 0; i < keys.length; i++) {
	// 		for (var k = 1; k <= 6; k++) {
	// 			$("#"+keys[i]).after("<td id='"+keys[i]+"_"+k+"'></td>");
	// 		}
	// 	}
	// }


	

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