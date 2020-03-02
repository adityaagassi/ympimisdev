@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150);
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		border-top: 2px solid white;
		vertical-align: middle;
		text-align: center;
		padding:1px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(150,150,150);
		padding:0;
	}
	table.table-bordered > tbody > tr > td > p{
		color: #abfbff;
	}
	.content{
		color: white;
		font-weight: bold;
	}

	hr {
		margin: 0px;
	}

	.sedang {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: sedang 1s infinite;  /* Safari 4+ */
		-moz-animation: sedang 1s infinite;  /* Fx 5+ */
		-o-animation: sedang 1s infinite;  /* Opera 12+ */
		animation: sedang 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes sedang {
		0%, 49% {
			background: rgba(0, 0, 0, 0);
		}
		50%, 100% {
			background-color: #f73939;
		}
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">
	<!-- <h1>
		<span class="text-yellow">
			{{ $title }}
		</span>
		<small>
			<span style="color: #FFD700;"> {{ $title_jp }}</span>
		</small>
	</h1> -->
</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding: 0px;">
	<input type="hidden" value="{{ $loc }}" id="loc">
	<span style="padding-top: 0px">
		<center><h1><b>{{ $page }}</b></h1></center>
	</span>
	<div class="row">
		<div class="col-xs-12">
			<table id="buffingTable" class="table table-bordered">
				<!-- <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;">
					<tr>
						<th style="width: 0.66%; padding: 0;">#1</th>
						<th style="width: 0.66%; padding: 0;">#2</th>
						<th style="width: 0.66%; padding: 0;">#3</th>
						<th style="width: 0.66%; padding: 0;">#4</th>
						<th style="width: 0.66%; padding: 0;">#5</th>
						<th style="width: 0.66%; padding: 0;">#6</th>
						<th style="width: 0.66%; padding: 0;">#7</th>
						<th style="width: 0.66%; padding: 0;">#8</th>
						<th style="width: 0.66%; padding: 0;">#9</th>
						<th style="width: 0.66%; padding: 0;">#10</th>
					</tr>
				</thead> -->
				<tbody id="weldingTableBody">
					<tr style="color:white">
						<td id="q0" style="height: 50px"></td>
						<td id="q1"></td>
						<td id="q2"></td>
						<td id="q3"></td>
						<td id="q4"></td>
						<td id="q5"></td>
						<td id="q6"></td>
						<td id="q7"></td>
						<td id="q8"></td>
						<td id="q9"></td>
					</tr>
					<tr style="color:white">
						<td id="q10" style="height: 50px"></td>
						<td id="q11"></td>
						<td id="q12"></td>
						<td id="q13"></td>
						<td id="q14"></td>
						<td id="q15"></td>
						<td id="q16"></td>
						<td id="q17"></td>
						<td id="q18"></td>
						<td id="q19"></td>
					</tr>
					<tr style="color:white">
						<td id="q20" style="height: 50px"></td>
						<td id="q21"></td>
						<td id="q22"></td>
						<td id="q23"></td>
						<td id="q24"></td>
						<td id="q25"></td>
						<td id="q26"></td>
						<td id="q27"></td>
						<td id="q28"></td>
						<td id="q29"></td>
					</tr>
					<tr style="color:white">
						<td id="q30" style="height: 50px"></td>
						<td id="q31"></td>
						<td id="q32"></td>
						<td id="q33"></td>
						<td id="q34"></td>
						<td id="q35"></td>
						<td id="q36"></td>
						<td id="q37"></td>
						<td id="q38"></td>
						<td id="q39"></td>
					</tr>
					<tr style="color:white">
						<td id="q40" style="height: 50px"></td>
						<td id="q41"></td>
						<td id="q42"></td>
						<td id="q43"></td>
						<td id="q44"></td>
						<td id="q45"></td>
						<td id="q46"></td>
						<td id="q47"></td>
						<td id="q48"></td>
						<td id="q49"></td>
					</tr>
				</tbody>
				<tfoot>
				</tfoot>
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

	jQuery(document).ready(function() {
		fetchTable();
		setInterval(fetchTable, 1000);
	});

	var akan_wld = [];
	var sedang = [];

	var totalAkan = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
	var totalSedang = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];


	function setTimeSedang(index) {
		if(sedang[index]){
			totalSedang[index]++;
			return pad(parseInt(totalSedang[index] / 3600)) + ':' + pad(parseInt((totalSedang[index] % 3600) / 60)) + ':' + pad((totalSedang[index] % 3600) % 60);
		}else{
			return '';
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

	function fetchTable(){
		var loc = $('#loc').val();

		var data = {
			loc : loc
		}
		$.get('{{ url("fetch/welding/welding_board") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					for (var i = 0; i < result.boards.length; i++) {
						$('#q'+i).html(result.boards[i].queue);
					}
				}
				else{
					alert('Attempt to retrieve data failed.');
				}
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