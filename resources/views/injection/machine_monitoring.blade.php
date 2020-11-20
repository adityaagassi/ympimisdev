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
	table{
		border-radius:6px;
	}
	table.table{
		border-radius:6px;
	}
	table.table-bordered{
		border:2px solid white;
		border-radius:6px;
	}

	table.table-bordered > thead > tr > th{
		border:2px solid white;
	}
	table.table-bordered > tbody > tr > td{
		border:2px solid white;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:2px solid white;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	.dataTable > thead > tr > th[class*="sort"]:after{
		content: "" !important;
	}
	#queueTable.dataTable {
		margin-top: 0px!important;
	}
	.color {
		width: 50px;
		height: 50px;
		-webkit-animation: blinks 1s infinite;  /* Safari 4+ */
		-moz-animation: blinks 1s infinite;  /* Fx 5+ */
		-o-animation: blinks 1s infinite;  /* Opera 12+ */
		animation: blinks 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes blinks {
		0%, 49% {
			background-color: #fffcb7;
		}
		50%, 100% {
			background-color: rgb(255,100,120);
		}
	}

	.color2 {
		width: 50px;
		height: 50px;
		-webkit-animation: sukses 1s infinite;  /* Safari 4+ */
		-moz-animation: sukses 1s infinite;  /* Fx 5+ */
		-o-animation: sukses 1s infinite;  /* Opera 12+ */
		animation: sukses 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes sukses {
		0%, 49% {
			background-color: #fffcb7;
		}
		50%, 100% {
			background-color: rgb(100,250,120);
		}
	}

	#sedang {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: sedang 1s infinite;  /* Safari 4+ */
		-moz-animation: sedang 1s infinite;  /* Fx 5+ */
		-o-animation: sedang 1s infinite;  /* Opera 12+ */
		animation: sedang 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes sedang {
		0%, 49% {
			background: #222222;
		}
		50%, 100% {
			background-color: #16a600;
		}
	}

	#shot {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: shot 1s infinite;  /* Safari 4+ */
		-moz-animation: shot 1s infinite;  /* Fx 5+ */
		-o-animation: shot 1s infinite;  /* Opera 12+ */
		animation: shot 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes shot {
		0%, 49% {
			background: rgba(0, 0, 0, 0);
		}
		50%, 100% {
			background-color: #16a600;
		}
	}

	#ng {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: ng 1s infinite;  /* Safari 4+ */
		-moz-animation: ng 1s infinite;  /* Fx 5+ */
		-o-animation: ng 1s infinite;  /* Opera 12+ */
		animation: ng 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes ng {
		0%, 49% {
			background: rgba(0, 0, 0, 0);
		}
		50%, 100% {
			background-color: #16a600;
		}
	}

	#loading, #error { display: none; }
	.content-wrapper{
		padding-top: 0px;
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">
	<h1>
		
	</h1>
</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-left: 10px; padding-right: 10px;">
	<div class="row">
		<div class="col-xs-12" style="padding-top: 0px;" id="tableMonitoring">
			
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

	jQuery(document).ready(function() {
		fillTable();
		setInterval(fillTable,10000);
	});

	function fillTable() {
		$.get('{{ url("fetch/injection/machine_monitoring") }}', function(result, status, xhr){
			if (result.status) {
				if (result.data.length > 0) {
					var divMesin = "";
					$('#tableMonitoring').empty();
					var index = 0;
					var progress = [];
					$.each(result.data, function(key, value) {

						if (value.part != "") {
							var color = '#a4fa98';
							var bgcolor = '#0a991d';
						}else{
							var color = 'white';
							var bgcolor = '#222222';
						}

						var total = 0;

						divMesin += '<div class="col-xs-4" style="padding:5px;padding-top:0px;">';
						divMesin += '<table class="table table-bordered" style="margin-bottom:10px">';
						divMesin += '<tr>';
						divMesin += '<td colspan="3" style="border:1px solid white;color:white;padding:0;font-size:30px;font-weight:bold;background-color:#222222">'+value.mesin+'<br>';

						divMesin += '<div class="col-md-12" style="width:100%">';
						divMesin += '<div class="row">';
						divMesin += '<div class="progress-group" id="progress_div">';
						divMesin += '<div class="progress" style="height: 40px; border-style: solid;border-width: 1px;padding-top: -20px; border-color: #d3d3d3;">';
						divMesin += '<span class="progress-text" id="progress_text_machine'+index+'" style="font-size: 20px;padding-top:0px;color:black"></span>';
						divMesin += '<div class="progress-bar progress-bar-success progress-bar-striped" id="progress_bar_machine'+index+'" style="font-size: 2px;"></div>';
						divMesin += '</div>';
						divMesin += '</div>';
						divMesin += '</div>';
						divMesin += '</div>';

						divMesin += '</td>';
						divMesin += '</tr>';
						divMesin += '<td colspan="3" style="border:1px solid white;color:white;padding:0;font-size:30px;font-weight:bold;background-color:#222222">';

						

						divMesin += '</td>';
						divMesin += '</tr>';

						divMesin += '<tr>';
						divMesin += '<td style="border:1px solid black;border-bottom:2px solid black;color:black;padding:0;font-size:20px;background-color:#dcdcdc;font-weight:bold">PART</td>';
						divMesin += '<td style="border:1px solid black;border-bottom:2px solid black;color:black;padding:0;font-size:20px;background-color:#dcdcdc;font-weight:bold">QTY</td>';
						divMesin += '<td style="border:1px solid black;border-bottom:2px solid black;color:black;padding:0;font-size:20px;background-color:#dcdcdc;font-weight:bold">NG</td>';
						divMesin += '</tr>';
						divMesin += '<tr>';
						if (value.part != "") {
							divMesin += '<td style="border:1px solid white;color:white;padding:0;font-size:20px;background-color:'+bgcolor+'" id="sedang">'+value.part+value.type+'</td>';
						}else{
							divMesin += '<td style="border:1px solid white;color:white;padding:0;font-size:20px;background-color:'+bgcolor+'">-<br>-<br>-</td>';
						}
						if (value.shot_mesin != 0) {
							divMesin += '<td style="border:1px solid white;color:white;padding:0;font-size:30px;background-color:'+bgcolor+'" id="sedang">'+value.shot_mesin+'</td>';
						}else{
							divMesin += '<td style="border:1px solid white;color:white;padding:0;font-size:30px;background-color:'+bgcolor+'">0</td>';
						}
						if (value.ng_count != "") {
							var ng = value.ng_count.split(',');
							for (var i = 0; i < ng.length; i++) {
								var total = total + parseFloat(ng[i]);
							}
							divMesin += '<td style="border:1px solid white;color:white;padding:0;font-size:30px;background-color:'+bgcolor+'" id="sedang">'+total+'</td>';
						}else{
							divMesin += '<td style="border:1px solid white;color:white;padding:0;font-size:30px;background-color:'+bgcolor+'">0</td>';
						}
						divMesin += '</tr>';
						divMesin += '</table>';
						divMesin += '</div>';
						index++;
					})

					divMesin += '</table>';

					$('#tableMonitoring').append(divMesin);
					var index2 = 0;

					var persen = 0;

					$.each(result.data, function(key, value) {
						persen = (parseFloat(value.shot_molding) / 15000) * 100;
						if (value.molding != '-') {
							$('#progress_text_machine'+index2).html("Shot "+value.molding+" : "+value.shot_molding);
						}
						$('#progress_bar_machine'+index2).css('width', persen.toFixed(1)+'%');
						$('#progress_bar_machine'+index2).css('color', 'white');
						$('#progress_bar_machine'+index2).css('font-weight', 'bold');
						index2++;
					});
				}
			}else{
				alert('Attempt to retrieve data failed.');
			}
		})
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

