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
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding: 0px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
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

</style>
@stop
@section('header')

<section class="content-header" style="padding-top: 0; padding-bottom: 0;">
	<h1>
		<span class="text-yellow">{{ $title }}<span>
			<small>
				<span style="color: #FFD700;"> ??</span>
			</small>
		</h1>
	</section>
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
									<th colspan="3">Machine #1</th>
								</tr>
								<tr>
									<th colspan="3" id="status1"> &nbsp;</th>
								</tr>

							</thead>
							<thead style="color: #FFD700;">
								<tr>
									<th>Jig</th>
									<th>Key</th>
									<th>Qty</th>
								</tr>
							</thead>
							<tbody id="tbody1">
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2">Total</th>
									<th id="total1"></th>
								</tr>
							</tfoot>
						</table>
					</div>

					<div class="col-xs-2" style="padding:1px">
						<table class="table table-responsive table-bordered table-stripped">
							<thead id="mesin2">
								<tr>
									<th colspan="3">Machine #2</th>
								</tr>
								<tr>
									<th colspan="3" id="status2"> &nbsp;</th>
								</tr>

							</thead>
							<thead style="color: #FFD700;">
								<tr>
									<th>Jig</th>
									<th>Key</th>
									<th>Qty</th>
								</tr>
							</thead>
							<tbody id="tbody2">
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2">Total</th>
									<th id="total2"></th>
								</tr>
							</tfoot>
						</table>
					</div>

					<div class="col-xs-2" style="padding:1px">
						<table class="table table-responsive table-bordered table-stripped">
							<thead id="mesin3">
								<tr>
									<th colspan="3">Machine #3</th>
								</tr>
								<tr>
									<th colspan="3" id="status3"> &nbsp;</th>
								</tr>

							</thead>
							<thead style="color: #FFD700;">
								<tr>
									<th>Jig</th>
									<th>Key</th>
									<th>Qty</th>
								</tr>
							</thead>
							<tbody id="tbody3">
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2">Total</th>
									<th id="total3"></th>
								</tr>
							</tfoot>
						</table>
					</div>

					<div class="col-xs-2" style="padding:1px">
						<table class="table table-responsive table-bordered table-stripped">
							<thead id="mesin4">
								<tr>
									<th colspan="3">Machine #4</th>
								</tr>
								<tr>
									<th colspan="3" id="status4"> &nbsp;</th>
								</tr>

							</thead>
							<thead style="color: #FFD700;">
								<tr>
									<th>Jig</th>
									<th>Key</th>
									<th>Qty</th>
								</tr>
							</thead>
							<tbody id="tbody4">
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2">Total</th>
									<th id="total4"></th>
								</tr>
							</tfoot>
						</table>
					</div>

					<div class="col-xs-2" style="padding:1px">
						<table class="table table-responsive table-bordered table-stripped">
							<thead id="mesin5">
								<tr>
									<th colspan="3">Machine #5</th>
								</tr>
								<tr>
									<th colspan="3" id="status5"> &nbsp;</th>
								</tr>

							</thead>
							<thead style="color: #FFD700;">
								<tr>
									<th>Jig</th>
									<th>Key</th>
									<th>Qty</th>
								</tr>
							</thead>
							<tbody id="tbody5">
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2">Total</th>
									<th id="total5"></th>
								</tr>
							</tfoot>
						</table>
					</div>

					<div class="col-xs-2" style="padding:1px">
						<table class="table table-responsive table-bordered table-stripped">
							<thead id="mesin6">
								<tr>
									<th colspan="3">Machine #6</th>
								</tr>
								<tr>
									<th colspan="3" id="status6"> &nbsp;</th>
								</tr>

							</thead>
							<thead style="color: #FFD700;">
								<tr>
									<th>Jig</th>
									<th>Key</th>
									<th>Qty</th>
								</tr>
							</thead>
							<tbody id="tbody6">
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2">Total</th>
									<th id="total6"></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>

			</div>

			<div class="col-xs-12">
				<div class="row">
					<div class="col-xs-2" style="padding:1px">
						<table class="table table-responsive table-bordered table-stripped">
							<thead style="color: #FFD700;">
								<tr>
									<th colspan="3">Queue Machine #1</th>
								</tr>
							</thead>
							<tbody id="queue1">
							</tbody>
							
						</table>
					</div>

					<div class="col-xs-2" style="padding:1px">
						<table class="table table-responsive table-bordered table-stripped">
							<thead style="color: #FFD700;">
								<tr>
									<th colspan="3">Queue Machine #2</th>
								</tr>
							</thead>
							<tbody id="queue2">
							</tbody>
							
						</table>
					</div>

					<div class="col-xs-2" style="padding:1px">
						<table class="table table-responsive table-bordered table-stripped">
							<thead style="color: #FFD700;">
								<tr>
									<th colspan="3">Queue Machine #3</th>
								</tr>
							</thead>
							<tbody id="queue3">
							</tbody>
							
						</table>
					</div>

					<div class="col-xs-2" style="padding:1px">
						<table class="table table-responsive table-bordered table-stripped">
							<thead style="color: #FFD700;">
								<tr>
									<th colspan="3">Queue Machine #4</th>
								</tr>
							</thead>
							<tbody id="queue4">
							</tbody>
							
						</table>
					</div>

					<div class="col-xs-2" style="padding:1px">
						<table class="table table-responsive table-bordered table-stripped">
							<thead style="color: #FFD700;">
								<tr>
									<th colspan="3">Queue Machine #5</th>
								</tr>
							</thead>
							<tbody id="queue5">
							</tbody>
							
						</table>
					</div>

					<div class="col-xs-2" style="padding:1px">
						<table class="table table-responsive table-bordered table-stripped">
							<thead style="color: #FFD700;">
								<tr>
									<th colspan="3">Queue Machine #6</th>
								</tr>
							</thead>
							<tbody id="queue6">
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

			setInterval(getBarrelMachine, 1000);
		// getBarrelMachine();
		// setInterval(getMachineStatus, 1000);
	});

		function getBarrelMachine() {
			$.get('{{ url("fetch/middle/get_barrel") }}', function(result, status, xhr){
				var antrian = 0;
				var total = 0;
				var total2 = 0;

				$.each(result.machine_stat, function(index, value) {
					var jam = "" , menit = "";
					if (value.jam != 0) {
						jam = ("0" + value.jam).slice(-2)+"H";
					}

					if (value.menit != 0) {
						menit = ("0" + value.menit).slice(-2)+"M";
					}

					detik = ("0" + value.detik).slice(-2)+"S";


					if (value.status == "idle") {
						$("#mesin"+value.machine).css("background-color","rgb(255,77,77)");
					}
					else if (value.status == "running"){

						if (value.jam >= 3) {
							$("#mesin"+value.machine).css("background-color","#ffc544");
						}
						else {
							$("#mesin"+value.machine).css("background-color","rgb(77,255,77)");
						}
					}

					$("#status"+value.machine).text(value.status.toUpperCase()+" : "+jam+" "+menit+" "+detik);
				// $("#duration"+value.machine).text();
			})

				for (var i = 1; i <= 6; i++) {
					$("#tbody"+i).empty();
					$("#queue"+i).empty();
				}

				$.each(result.datas, function(index, value) {
					var mesin = value.machine;
					var color = "";

					if (value.jig % 2 === 0 ) {
						color = 'style="background-color: #fffcb7"';
					} else {
						color = 'style="background-color: #ffd8b7"';
					}

					if (value.status == 'running') {
						if (result.datas[index].machine == result.datas[index+1].machine) {
							total += value.qty;
						} else {
							total += value.qty;
							$("#total"+result.datas[index].machine).text(total);
							total = 0;
						}
						$("#tbody"+mesin).append("<tr "+color+"><td>"+value.jig+"</td><td>"+value.model+"S "+value.key+"</td><td>"+value.qty+"</td></tr>");
					}
					else if(value.status == 'queue'){
						if (antrian <=2) {
							$("#queue"+mesin).append("<tr "+color+"><td>"+value.jig+"</td><td>"+value.model+"S "+value.key+"</td><td>"+value.qty+"</td></tr>");
						}
					}
				})
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