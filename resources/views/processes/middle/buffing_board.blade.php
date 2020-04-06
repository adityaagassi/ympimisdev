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
			background-color: #f73939;
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
				<thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 18px;">
					<tr>
						<th style="width: 0.66%; padding: 0;">WS</th>
						<th style="width: 0.66%; padding: 0;">Operator</th>
						<th style="width: 0.66%; padding: 0; background-color:#4ff05a;">Sedang</th>
						<th style="width: 0.66%; padding: 0; background-color:#ffd03a">Akan</th>
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
						<th style="width: 0.66%; padding: 0;">Jumlah</th>
						<th style="width: 0.66%; padding: 0; background-color: #f76a6a">Selesai</th>
					</tr>
				</thead>
				<tbody id="buffingTableBody" style="font-size: 18px;">
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
		// setInterval(fetchTable, 1000);
	});

	var akan_bff = [];
	var sedang = [];
	var selesai = [];

	var totalAkan = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
	var totalSedang = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
	var totalSelesai = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];


	function setTimeSelesai(index) {
		if(selesai[index]){
			totalSelesai[index]++;
			return pad(parseInt(totalSelesai[index] / 3600)) + ':' + pad(parseInt((totalSelesai[index] % 3600) / 60)) + ':' + pad((totalSelesai[index] % 3600) % 60);
		}else{
			return '';
		}
	}

	function setTimeSedang(index) {
		if(sedang[index]){
			totalSedang[index]++;
			return pad(parseInt(totalSedang[index] / 3600)) + ':' + pad(parseInt((totalSedang[index] % 3600) / 60)) + ':' + pad((totalSedang[index] % 3600) % 60);
		}else{
			return '';
		}
	}

	function setTimeAkan(index) {
		if(!akan_bff[index]){
			totalAkan[index]++;
			return pad(parseInt(totalAkan[index] / 3600)) + ':' + pad(parseInt((totalAkan[index] % 3600) / 60)) + ':' + pad((totalAkan[index] % 3600) % 60);
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
		var hpl = $('#hpl').val().split(',');
		var page = "{{$_GET['page']}}";

		var data = {
			mrpc : $('#mrpc').val(),
			hpl : hpl,
		}
		$.get('{{ url("fetch/middle/buffing_board") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					akan_bff = [];
					sedang = [];
					selesai = [];

					$('#buffingTableBody').html("");
					var buffingTableBody = "";
					var i = 0;
					var color2 = "";
					var colorSelesai = "";

					$.each(result.boards, function(index, value){
						if (i % 2 === 0 ) {
							if (value.employee_id) {
								color = '';

								if (value.dev_akan_detected == 0)
									color2 = 'class="akan"';
								else
									color2 = 'style="color:#ffd03a"';

								if (value.selesai)
									colorSelesai = 'class="selesai"';
								else
									colorSelesai = '';
							}
							else {
								// color = 'style="background-color: RGB(255,0,0)"';
								color = '';
								color2 = '';
								colorSelesai = '';
							}
						} else {
							if (value.employee_id) {
								color = 'style="background-color: #575c57"';

								if (value.dev_akan_detected == 0)
									color2 = 'class="akan"';
								else
									color2 = 'style="color:#ffd03a"';

								if (value.selesai)
									colorSelesai = 'class="selesai"';
								else
									colorSelesai = '';
							}
							else {
								// color = 'style="background-color: RGB(255,0,0)"';
								color = 'style="background-color: #575c57"';
								color2 = '';
								colorSelesai = '';
							}
						}

						//JIKA Akan
						if (value.dev_akan_detected == 0) {
							akan_time = value.akan_time;
							akan = "";
							akan_bff.push(false);
						} else {
							akan = value.akan;
							akan_time = "";
							akan_bff.push(true);
							totalAkan[index] = 0;
						}

						//JIKA Sedang buffing
						if (value.dev_sedang_detected == 1) {
							sedang_time = value.sedang_time;
							var sedang2 = value.sedang;
							sedang.push(true);
						} else {
							var sedang2 = "";
							sedang_time = "";
							sedang.push(false);
							totalSedang[index] = 0;
						}

						//JIKA Selesai
						if (value.dev_selesai_detected == 1) {
							selesai_time = value.selesai_time;
							selesai.push(true);											
						} else {
							selesai_time = "";
							selesai.push(false);
							totalSelesai[index] = 0;
						}

						var key = {first:['82','C','D'], second:['E','F'], third:['G','H','J']};

						if(page != ''){
							if(key[page].includes(value.ws.split("-")[1])){
								buffingTableBody += '<tr '+color+'>';
								buffingTableBody += '<td height="5%" style="font-size:2vw;">'+value.ws.split("-")[1]+'</td>';
								buffingTableBody += '<td>'+value.employee_id+'<br>'+value.employee_name.split(' ').slice(0,2).join(' ')+'</td>';
								buffingTableBody += '<td style="color:#a4fa98">'+sedang2+'<p></p>'+setTimeSedang(index)+'</td>';
								buffingTableBody += '<td '+color2+'>'+akan+'<p>'+setTimeAkan(index)+'</p></td>';
								buffingTableBody += '<td style="color:#fcff38">'+value.queue_1+'</td>';
								buffingTableBody += '<td style="color:#fcff38">'+value.queue_2+'</td>';
								buffingTableBody += '<td style="color:#fcff38">'+value.queue_3+'</td>';
								buffingTableBody += '<td style="color:#fcff38">'+value.queue_4+'</td>';
								buffingTableBody += '<td style="color:#fcff38">'+value.queue_5+'</td>';
								buffingTableBody += '<td style="color:#fcff38">'+value.queue_6+'</td>';
								buffingTableBody += '<td style="color:#fcff38">'+value.queue_7+'</td>';
								buffingTableBody += '<td style="color:#fcff38">'+value.queue_8+'</td>';
								buffingTableBody += '<td style="color:#fcff38">'+value.queue_9+'</td>';
								buffingTableBody += '<td style="color:#fcff38">'+value.queue_10+'</td>';
								buffingTableBody += '<td style="color:#fff; font-size:2vw;">'+value.jumlah+'</td>';
								buffingTableBody += '<td '+colorSelesai+'>'+value.selesai+'<p>'+setTimeSelesai(index)+'</p></td>';
								buffingTableBody += '</tr>';
							}
						}else{
							buffingTableBody += '<tr '+color+'>';
							buffingTableBody += '<td height="5%" style="font-size:2vw;">'+value.ws.split("-")[1]+'</td>';
							buffingTableBody += '<td>'+value.employee_id+'<br>'+value.employee_name.split(' ').slice(0,2).join(' ')+'</td>';
							buffingTableBody += '<td style="color:#a4fa98">'+sedang2+'<p></p>'+setTimeSedang(index)+'</td>';
							buffingTableBody += '<td '+color2+'>'+akan+'<p>'+setTimeAkan(index)+'</p></td>';
							buffingTableBody += '<td style="color:#fcff38">'+value.queue_1+'</td>';
							buffingTableBody += '<td style="color:#fcff38">'+value.queue_2+'</td>';
							buffingTableBody += '<td style="color:#fcff38">'+value.queue_3+'</td>';
							buffingTableBody += '<td style="color:#fcff38">'+value.queue_4+'</td>';
							buffingTableBody += '<td style="color:#fcff38">'+value.queue_5+'</td>';
							buffingTableBody += '<td style="color:#fcff38">'+value.queue_6+'</td>';
							buffingTableBody += '<td style="color:#fcff38">'+value.queue_7+'</td>';
							buffingTableBody += '<td style="color:#fcff38">'+value.queue_8+'</td>';
							buffingTableBody += '<td style="color:#fcff38">'+value.queue_9+'</td>';
							buffingTableBody += '<td style="color:#fcff38">'+value.queue_10+'</td>';
							buffingTableBody += '<td style="color:#fff; font-size:2vw;">'+value.jumlah+'</td>';
							buffingTableBody += '<td '+colorSelesai+'>'+value.selesai+'<p>'+setTimeSelesai(index)+'</p></td>';
							buffingTableBody += '</tr>';
						}						

						i += 1;

						data2 = {
							employee_id: value.employee_id
						}

					});

$('#buffingTableBody').append(buffingTableBody);

fetchTable();

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