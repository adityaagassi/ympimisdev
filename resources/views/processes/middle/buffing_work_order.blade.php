@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
		padding:0;
	}
	table.table-bordered > thead > tr > th{
		text-align: center;
		border:1px solid black;
		background-color: rgb(255,255,255); 
		color: rgb(0,0,0); 
		font-size: 16px;
	}
	table.table-bordered > tbody > tr > th{
		text-align: center;
		border:1px solid black;
		background-color: rgb(255,255,255); 
		color: rgb(0,0,0); 
		font-size: 16px;
	}
	.content{
		color: white;
		font-weight: bold;
	}

	.akan {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: akan 1s infinite;  /* Safari 4+ */
		-moz-animation: akan 1s infinite;  /* Fx 5+ */
		-o-animation: akan 1s infinite;  /* Opera 12+ */
		animation: akan 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes akan {
		0%, 49% {
			/*border:1px solid rgb(150,150,150);*/
			background: rgba(0, 0, 0, 0);
			/*opacity: 0;*/
		}
		50%, 100% {
			background-color: rgb(243, 156, 18);
			/*border:1px solid rgb(150,150,150);*/
		}
	}

	.selesai {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: selesai 1s infinite;  /* Safari 4+ */
		-moz-animation: selesai 1s infinite;  /* Fx 5+ */
		-o-animation: selesai 1s infinite;  /* Opera 12+ */
		animation: selesai 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes selesai {
		0%, 49% {
			background: rgba(0, 0, 0, 0);
		}
		50%, 100% {
			background-color: rgb(0, 166, 90);
		}
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">
	<h1>
		<span class="text-yellow">
			{{ $title }}
		</span>
		<small>
			<span style="color: #FFD700;"> {{ $title_jp }}</span>
		</small>
	</h1>
</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding: 0px; overflow-y:hidden; overflow-x:scroll;">
	<input type="hidden" value="{{ $mrpc }}" id="mrpc">
	<input type="hidden" value="{{ $hpl }}" id="hpl">
	<div class="row">
		<div class="col-xs-12">
			<table id="buffingTable" class="table table-bordered">
				<!-- <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;"> -->
					<thead>
						<tr>
							<th id="ws" style='width: 0.66%;'>WS</th>
						</tr>
						<tr>
							<th id="op" style='width: 0.66%;'>Operator</th>
						</tr>
						<tr>
							<th id="sedang" style='width: 0.66%;'>Sedang</th>
						</tr>
						<tr>
							<th id="akan" style='width: 0.66%;'>Akan</th>
						</tr>
						<tr>
							<th id="selesai" style='width: 0.66%;'>Selesai</th>
						</tr>
					</thead>
					<tbody id="antrian">
						
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

		jQuery(document).ready(function() {
			fetchTable();
			setInterval(fetchTable, 2000);
		});

		function fetchTable(){
			var hpl = $('#hpl').val().split(',');
			var data = {
				mrpc : $('#mrpc').val(),
				hpl : hpl,
			}
			$.get('{{ url("fetch/middle/buffing_board_reverse") }}', data, function(result, status, xhr){
				if(xhr.status == 200){
					if(result.status){
						var ws = "";
						var op = "";
						var sedang = "";
						var akan = "";
						var selesai = "";

						var antrian_tmp = [];
						var indexs = [];
						var antrian = [];

						var color2 = "";
						var colorSelesai = "";
						var x = 0;
						var num = 1;

						$.each(result.boards, function(index, value){
							if (x % 2 === 0 ) {
								if (value.employee_id) {
									color = '';
									if (!value.akan)
										color2 = 'class="akan"';
									else
										color2 = '';

									if (value.selesai)
										colorSelesai = 'class="selesai"';
								}
								else {
									color = '';
									color2 = '';
									colorSelesai = '';
								}
							} else {
								if (value.employee_id) {
									color = 'style="background-color: RGB(100,100,100)"';
									if (!value.akan)
										color2 = 'class="akan"';
									else
										color2 = '';

									if (value.selesai)
										colorSelesai = 'class="selesai"';
								}
								else {
									color = 'style="background-color: RGB(100,100,100)"';
									color2 = '';
									colorSelesai = '';
								}
							}


							ws += "<td "+color+" "+color2+">"+value.ws+"</td>";
							op += "<td "+color+" "+color2+">"+value.employee_id+"<br>"+value.employee_name.split(' ').slice(0,2).join(' ')+"</td>";

							if (value.sedang != "") {
								$.each(result.materials, function(index2, value2){
									if (value.sedang == value2.material_number) {
										sedang += "<td "+color+">"+value2.isi+"</td>";
									}
								})
							} else {
								sedang += "<td "+color+"></td>";
							}

							if (value.akan != "") {

								$.each(result.materials, function(index3, value3){
									if (value.akan == value3.material_number) {
										akan += "<td "+color+" "+color2+">"+value3.isi+"</td>";
									}
								})
							} else {
								akan += "<td "+color+" "+color2+"></td>";
							}

							if (value.selesai != "") {

								$.each(result.materials, function(index4, value4){
									if (value.selesai == value4.material_number) {
										selesai += "<td "+color+" "+colorSelesai+">"+value4.isi+"</td>";
									}
								})
							} else {
								selesai += "<td "+color+"></td>";
							}

							$.each(result.boards[index].queues, function(index5, value5){
								$.each(result.materials, function(index6, value6){
									if (result.boards[index].queues[index5] == value6.material_number) {
										antrian_tmp.push({id: value.id, ws: value.ws, antrian: value6.isi});
										antrian.push({id: value.id, ws: value.ws, antrian: value6.isi, num: num});
									}
								})
							})

							if (typeof result.boards[index+1] !== 'undefined') {
								if (result.boards[index].ws != result.boards[index+1].ws) {
									num++;
								}
							} else {
								if (result.boards[index].ws != result.boards[index-1].ws) {
									num++;
								}
							}

							indexs.push(antrian_tmp.length);
							antrian_tmp = [];
							x++;
						})

						// console.table(antrian);

						$("#antrian").html("");
						$("#buffingTable").find("td").remove();  

						$(ws).insertAfter("#ws");
						$(op).insertAfter("#op");
						$(sedang).insertAfter("#sedang");
						$(akan).insertAfter("#akan");
						$(selesai).insertAfter("#selesai");


						antrian_len = Math.max.apply(null, indexs);

						for (var i = 1; i <= antrian_len; i++) {
							$("#antrian").append("<tr id='queue"+i+"'><th>queue #"+i+"</th></tr>");
						}

						$.each(result.boards, function(index7, value7){
							var z = 1;
							var stat = 0;
							var cd = "";

							$.each(antrian, function(index8, value8){
								if (value8.num %2 === 0) {
									cls = 'style="background-color: #646464"';
								}
								else {
									cls = '';
								}

								if (value7.id == value8.id) {
									$("#queue"+z).append("<td "+cls+">"+value8.antrian+"</td>");
									cd = cls;
									z++;
								}
							});

							if (z <= antrian_len) {
								for (var i = z; i <= antrian_len; i++) {
									$("#queue"+i).append("<td "+cd+"></td>");
								}
							}
						})
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