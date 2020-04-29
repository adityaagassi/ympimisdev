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
	#master {
		font-size: 17px;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
		color: white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 9px;
		padding-bottom: 9px;
		vertical-align: middle;
		background-color: white;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#loading, #error { display: none; }

	.kedip {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: pulse 1s infinite;  /* Safari 4+ */
		-moz-animation: pulse 1s infinite;  /* Fx 5+ */
		-o-animation: pulse 1s infinite;  /* Opera 12+ */
		animation: pulse 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes pulse {
		0%, 49% {
			background-color: #00a65a;
			color: white;
		}
		50%, 100% {
			background-color: #ffffff;
			color: #444;
		}
	}

</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<input type="hidden" id="order_no">
	<input type="hidden" id="operator_id" value="{{ $employee_id }}">
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div class="col-xs-12" style="padding-right: 0; padding-left: 0;">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 2%;">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;" colspan="2">Operator</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;" id="op">{{ $employee_id }}</td>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="op2">{{ $name }}</td>
					</tr>
				</tbody>
			</table>

			<table class="table table-bordered" style="width: 100%">
				<thead>
					<tr>
						<th>Nomor SPK</th>
						<th>Bagian</th>
						<th>Jenis Pekerjaan</th>
						<th>Kategori</th>
						<th>Prioritas</th>
						<th>Time</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody id="master">
				</tbody>
			</table>
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
			// setInterval(setTime, 1000);
			get_spk();
		});


		function get_spk() {
			$("#master").empty();
			var body = "";

			$.get('{{ url("fetch/maintenance/spk") }}', function(result, status, xhr){
				$.each(result.datas, function(index, value){
					body += "<tr>";
					body += "<td>"+value.order_no+"</td>";
					body += "<td>"+value.section+"</td>";
					body += "<td>"+value.type+"</td>";
					body += "<td>"+value.category+"</td>";
					body += "<td>"+value.priority+"</td>";
					body += "<td>-</td>";
					body += "<td><button class='btn btn-success' onclick='modalDetail(\""+value.order_no+"\")'>Kerjakan</button></td>";
					body += "</tr>";
				})
				$("#master").append(body);
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
				time: '4000'
			});
		}

		function openErrorGritter(title, message) {
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-danger',
				image: '{{ url("images/image-stop.png") }}',
				sticky: false,
				time: '4000'
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

		function addZero(i) {
			if (i < 10) {
				i = "0" + i;
			}
			return i;
		}

		function getActualFullDate() {
			var d = new Date();
			var day = addZero(d.getDate());
			var month = addZero(d.getMonth()+1);
			var year = addZero(d.getFullYear());
			var h = addZero(d.getHours());
			var m = addZero(d.getMinutes());
			var s = addZero(d.getSeconds());
			return year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
		}
	</script>
	@endsection
