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
		vertical-align: middle;
		text-align: center;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(150,150,150);
		padding:0;
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
			background: rgba(0, 0, 0, 0);
			/*opacity: 0;*/
		}
		50%, 100% {
			background-color: rgb(243, 156, 18);
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
<section class="content" style="padding: 0px;">
	<input type="hidden" value="{{ $mrpc }}" id="mrpc">
	<input type="hidden" value="{{ $hpl }}" id="hpl">
	<div class="row">
		<div class="col-xs-12">
			<table id="buffingTable" class="table table-bordered">
				<thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;">
					<tr>
						<th style="width: 0.66%; padding: 0;">WS</th>
						<th style="width: 0.66%; padding: 0;">Operator</th>
						<th style="width: 0.66%; padding: 0;">Sedang</th>
						<th style="width: 0.66%; padding: 0;">Akan</th>
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
						<th style="width: 0.66%; padding: 0;">Selesai</th>
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
		fetchTable();
		setInterval(fetchTable, 1000);
	});

	function fetchTable(){
		var hpl = $('#hpl').val().split(',');
		var data = {
			mrpc : $('#mrpc').val(),
			hpl : hpl,
		}
		$.get('{{ url("fetch/middle/buffing_board") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#buffingTableBody').html("");
					var buffingTableBody = "";
					var i = 0;
					var color2 = "";
					var colorSelesai = "";

					$.each(result.boards, function(index, value){
						if (i % 2 === 0 ) {
							if (value.employee_id) {
								color = '';

								if (!value.akan)
									color2 = 'class="akan"';

								if (value.selesai)
									colorSelesai = 'class="selesai"';
							}
							else {
								// color = 'style="background-color: RGB(255,0,0)"';
								color = '';
								color2 = '';
								colorSelesai = '';
							}
						} else {
							if (value.employee_id) {
								color = 'style="background-color: RGB(100,100,100)"';

								if (!value.akan)
									color2 = 'class="akan"';

								if (value.selesai)
									colorSelesai = 'class="selesai"';
							}
							else {
								// color = 'style="background-color: RGB(255,0,0)"';
								color = 'style="background-color: RGB(100,100,100)"';
								color2 = '';
								colorSelesai = '';
							}
						}

						buffingTableBody += '<tr '+color+'>';
						buffingTableBody += '<td '+color2+'>'+value.ws.split("-")[1]+'-'+value.ws.split("-")[2]+'</td>';
						buffingTableBody += '<td '+color2+'>'+value.employee_id+'<br>'+value.employee_name.split(' ')[0]+'</td>';
						buffingTableBody += '<td>'+value.sedang+'</td>';
						buffingTableBody += '<td '+color2+'>'+value.akan+'</td>';
						buffingTableBody += '<td>'+value.queue_1+'</td>';
						buffingTableBody += '<td>'+value.queue_2+'</td>';
						buffingTableBody += '<td>'+value.queue_3+'</td>';
						buffingTableBody += '<td>'+value.queue_4+'</td>';
						buffingTableBody += '<td>'+value.queue_5+'</td>';
						buffingTableBody += '<td>'+value.queue_6+'</td>';
						buffingTableBody += '<td>'+value.queue_7+'</td>';
						buffingTableBody += '<td>'+value.queue_8+'</td>';
						buffingTableBody += '<td>'+value.queue_9+'</td>';
						buffingTableBody += '<td>'+value.queue_10+'</td>';
						buffingTableBody += '<td '+colorSelesai+'>'+value.selesai+'</td>';
						buffingTableBody += '</tr>';
						i += 1;
					});


					$('#buffingTableBody').append(buffingTableBody);
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