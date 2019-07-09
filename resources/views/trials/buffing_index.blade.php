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
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
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
	#loading, #error { display: none; }
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
<section class="content" style="padding-left: 0px; padding-right: 0px;">
	<div class="row">
		<div class="col-xs-12">
			<table id="buffingTable" class="table table-bordered">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th style="width: 1%; padding: 0;">WS</th>
						<th style="width: 1%; padding: 0;">Operator</th>
						<th style="width: 1%; padding: 0;">Sedang</th>
						<th style="width: 1%; padding: 0;">Akan</th>
						<th style="width: 1%; padding: 0;">Selesai</th>
						<th style="width: 1%; padding: 0;">Time</th>
						<th style="width: 1%; padding: 0;">#1</th>
						<th style="width: 1%; padding: 0;">#2</th>
						<th style="width: 1%; padding: 0;">#3</th>
						<th style="width: 1%; padding: 0;">#4</th>
						<th style="width: 1%; padding: 0;">#5</th>
						<th style="width: 1%; padding: 0;">#6</th>
						<th style="width: 1%; padding: 0;">#7</th>
						<th style="width: 1%; padding: 0;">#8</th>
						<th style="width: 1%; padding: 0;">#9</th>
						<th style="width: 1%; padding: 0;">#10</th>
					</tr>
				</thead>
				<tbody id="buffingTableBody">
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
		// fetchTable();
		// setInterval(fetchTable, 1000);
		fetchTable();
	});

	function fetchTable(){
		$.get('{{ url("fetch/buffing") }}', function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){

					$('#buffingTableBody').html("");
					var body = "", sedang = "-", akan = "-", selesai = "-", emp = "-";
					var ws = result.work_stations;
					var colorAKan = "";
					var colorSelesai = "";
					// console(result.akans)
					for (var i = 0; i < ws.length; i++) {
						var body = "";

						if (i % 2 === 0 ) {
							color = 'style="background-color: #fffcb7"';
						} else {
							color = 'style="background-color: #ffd8b7"';
						}

						if (result.employees[i]) {
							emp = result.employees[i].employee_id+'<br>'+result.employees[i].name.split(' ')[1]+' '+result.employees[i].name.split(' ')[2];
						} else emp = '-';

						if (result.sedangs[i]) {
							sedang = result.sedangs[i].material_number+'<br>'+result.sedangs[i].model+' '+result.sedangs[i].key;
						} else sedang = '-';

						if (result.akans[i]) {
							akan = result.akans[i].material_number+'<br>'+result.akans[i].model+' '+result.akans[i].key;
							colorAKan = "";
						} else {
							colorAKan = "class='color'";
							akan = '-';
						}

						if (result.selesais[i]) {
							colorSelesai = "class='color2'";
							selesai = result.selesais[i].material_number+'<br>'+result.selesais[i].model+' '+result.selesais[i].key;
						} else {
							selesai = '-';
							colorSelesai = "";
						}

						body += '<tr '+color+'>';
						body += '<td '+colorAKan+'>'+ws[i].dev_name+'</td>';
						body += "<td "+colorAKan+">"+emp+"</td>";
						body += "<td>"+sedang+"</td>";
						body += "<td "+colorAKan+">"+akan+"</td>";
						body += "<td "+colorSelesai+">"+selesai+"</td>";
						body += "<td>-</td>";
						body += "<td>-</td>";
						body += "<td>-</td>";
						body += "<td>-</td>";
						body += "<td>-</td>";
						body += "<td>-</td>";
						body += "<td>-</td>";
						body += "<td>-</td>";
						body += "<td>-</td>";
						body += "<td>-</td>";
						body += "<td>-</td>";
						body += '</tr>';
					}
					$('#buffingTableBody').append(body);

					setTimeout(fetchTable, 1000);
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

