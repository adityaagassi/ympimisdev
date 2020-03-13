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
			background-color: #4ff05a;
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
	<input type="hidden" value="{{ $loc }}" id="loc">
	<!-- <span style="padding-top: 0px">
		<center><h1><b>{{ $page }}</b></h1></center>
	</span> -->
	<div class="row">
		<div class="col-xs-12">
			<table id="buffingTable" class="table table-bordered">
				<thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 16px;">
					<tr>
						<th style="width: 0.66%; padding: 0;">WS</th>
						<th style="width: 0.66%; padding: 0;">Operator</th>
						<th style="width: 0.66%; padding: 0; background-color:#4ff05a;">Sedang</th>
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
					</tr>
				</thead>
				<tbody id="weldingTableBody" style="font-size: 19px">
				</tbody>
				<tfoot>
				</tfoot>
			</table>
		</div>
	</div>

	<div class="modal fade" id="myModal">
	    <div class="modal-dialog" style="width:1250px;">
	      <div class="modal-content">
	        <div class="modal-header" style="color: black">
	          <h4 style="float: right;" id="modal-title"></h4>
	          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
	          <br><h4 class="modal-title" id="judul_table"></h4>
	        </div>
	        <div class="modal-body">
	          <div class="row">
	            <div class="col-md-12">
	              <table id="tableDetail" class="table table-striped table-hover" style="width: 100%;">
	                <thead style="background-color: rgba(126,86,134,.7);">
	                  <tr>
	                  	<th width="1%">No.</th>
	                    <th>Material Number</th>
	                    <th>Material Description</th> 
	                    <th>WS</th>
	                  </tr>
	                </thead>
	                <tbody id="bodyTableDetail" style="color: black">
	                </tbody>
	              </table>
	            </div>
	          </div>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
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
					akan_wld = [];
					sedang = [];
					selesai = [];

					$('#weldingTableBody').html("");
					var weldingTableBody = "";
					var i = 0;
					var color2 = "";
					var colorShift = "";

					$.each(result.boards, function(index, value){
						var startA = '{{ $startA }}';
						var finishA = '{{ $finishA }}';
						var startB = '{{ $startB }}';
						var finishB = '{{ $finishB }}';
						var startC = '{{ $startC }}';
						var finishC = '{{ $finishC }}';

						if (value.shift == 'A') {
							if (value.jam_shift > startA || value.jam_shift < finishA) {
								colorShift = '';
							}else{
								colorShift = 'style="background-color: #f73939;"';
							}
						}
						if (value.shift == 'B') {
							if (value.jam_shift > startB || value.jam_shift < finishB) {
								colorShift = '';
							}else{
								colorShift = 'style="background-color: #f73939;"';
							}
						}
						if (value.shift == 'C') {
							if (value.jam_shift > startC || value.jam_shift < finishC) {
								colorShift = '';
							}else{
								colorShift = 'style="background-color: #f73939;"';
							}
						}

						if (i % 2 === 0 ) {
							if (value.employee_id) {
								color = '';

								if (value.akan == "<br>")
									color2 = 'class="akan"';
								else
									color2 = 'style="color:#ffd03a"';


								if (value.sedang == "<br>")
									color3 = 'class="sedang"';
								else
									color3 = 'style="color:#a4fa98"';
							}
							else {
								// color = 'style="background-color: RGB(255,0,0)"';
								color = '';
								color2 = '';
								color3 = '';
							}
						} else {
							if (value.employee_id) {
								color = 'style="background-color: #575c57"';

								if (value.akan == "<br>")
									color2 = 'class="akan"';
								else
									color2 = 'style="color:#ffd03a"';

								if (value.sedang == "<br>")
									color3 = 'class="sedang"';
								else
									color3 = 'style="color:#a4fa98"';
							}
							else {
								// color = 'style="background-color: RGB(255,0,0)"';
								color = 'style="background-color: #575c57"';
								color2 = '';
								color3 = '';
							}
						}

						//JIKA Sedang buffing
						if (value.sedang != "<br>") {
							sedang_time = value.sedang_time;
							var sedang2 = value.sedang;
							sedang.push(true);
						} else {
							var sedang2 = "";
							sedang_time = "";
							sedang.push(false);
							totalSedang[index] = 0;
						}

						// var key = [['C','D','E'],['F','G','H','J','82']];

						var key = {first:['C','D','E'], second:['F','G','H','J','82']};

						// console.log(page);

						// if(page != ''){
						// 	if(key[page].includes(value.ws.split("-")[1])){
						// 		weldingTableBody += '<tr '+color+'>';
						// 		weldingTableBody += '<td height="5%">'+value.ws.split("-")[1]+'</td>';
						// 		weldingTableBody += '<td>'+value.employee_id+'<br>'+value.employee_name.split(' ').slice(0,2).join(' ')+'</td>';
						// 		weldingTableBody += '<td style="color:#a4fa98">'+sedang2+'<p></p>'+setTimeSedang(index)+'</td>';
						// 		weldingTableBody += '<td '+color2+'>'+akan+'<p>'+setTimeAkan(index)+'</p></td>';
						// 		weldingTableBody += '<td style="color:#fcff38">'+value.queue_1+'</td>';
						// 		weldingTableBody += '<td style="color:#fcff38">'+value.queue_2+'</td>';
						// 		weldingTableBody += '<td style="color:#fcff38">'+value.queue_3+'</td>';
						// 		weldingTableBody += '<td style="color:#fcff38">'+value.queue_4+'</td>';
						// 		weldingTableBody += '<td style="color:#fcff38">'+value.queue_5+'</td>';
						// 		weldingTableBody += '<td style="color:#fcff38">'+value.queue_6+'</td>';
						// 		weldingTableBody += '<td style="color:#fcff38">'+value.queue_7+'</td>';
						// 		weldingTableBody += '<td style="color:#fcff38">'+value.queue_8+'</td>';
						// 		weldingTableBody += '<td style="color:#fcff38">'+value.queue_9+'</td>';
						// 		weldingTableBody += '<td style="color:#fcff38">'+value.queue_10+'</td>';
						// 		weldingTableBody += '<td '+colorSelesai+'>'+value.selesai+'<p>'+setTimeSelesai(index)+'</p></td>';
						// 		weldingTableBody += '</tr>';
						// 	}
						// }else{
							var colorJumlah = 'style="background-color: #f73939;font-size:30px"';
							if (value.employee_id == null) {
								weldingTableBody += '<tr '+color+'>';
								weldingTableBody += '<td height="5%">'+value.ws+'</td>';
								weldingTableBody += '<td '+colorShift+'>Not Found</td>';
								weldingTableBody += '<td '+color3+'>'+sedang2+'<p></p>'+setTimeSedang(index)+'</td>';
							}else{
								weldingTableBody += '<tr '+color+'>';
								weldingTableBody += '<td height="5%">'+value.ws+'</td>';
								weldingTableBody += '<td '+colorShift+'>'+value.employee_id+'<br>'+value.employee_name.split(' ').slice(0,2).join(' ')+'</td>';
								weldingTableBody += '<td '+color3+'>'+sedang2+'<p></p>'+setTimeSedang(index)+'</td>';
							}
							weldingTableBody += '<td style="color:#fcff38">'+value.queue_1+'</td>';
							weldingTableBody += '<td style="color:#fcff38">'+value.queue_2+'</td>';
							weldingTableBody += '<td style="color:#fcff38">'+value.queue_3+'</td>';
							weldingTableBody += '<td style="color:#fcff38">'+value.queue_4+'</td>';
							weldingTableBody += '<td style="color:#fcff38">'+value.queue_5+'</td>';
							weldingTableBody += '<td style="color:#fcff38">'+value.queue_6+'</td>';
							weldingTableBody += '<td style="color:#fcff38">'+value.queue_7+'</td>';
							weldingTableBody += '<td style="color:#fcff38">'+value.queue_8+'</td>';
							weldingTableBody += '<td style="color:#fcff38">'+value.queue_9+'</td>';
							weldingTableBody += '<td style="color:#fcff38">'+value.queue_10+'</td>';
							weldingTableBody += '<td '+colorJumlah+' onclick="ShowModal(\''+value.ws_name+'\')">'+value.jumlah_urutan+'</td>';
							weldingTableBody += '</tr>';
						// }						

						i += 1;

						data2 = {
							employee_id: value.employee_id
						}
					});

$('#weldingTableBody').append(weldingTableBody);
}
else{
	alert('Attempt to retrieve data failed.');
}
}
})
}

		function ShowModal(ws_name) {
		    $('#tableDetail').DataTable().clear();
		    $('#tableDetail').DataTable().destroy();

		    $("#myModal").modal("show");

		    var data = {
				loc : '{{$loc}}',
				ws_name : ws_name
			}
		    $.get('{{ url("fetch/welding/fetch_detail") }}', data, function(result, status, xhr){
				if(xhr.status == 200){
					if(result.status){
						akan_wld = [];
						sedang = [];
						selesai = [];

						$('#bodyTableDetail').html("");
						var bodyTableDetail = "";
						var i = 1;
						$.each(result.list_antrian, function(index, value){
							bodyTableDetail += '<tr>';
							bodyTableDetail += '<td>'+i+'</td>';
							bodyTableDetail += '<td>'+value.gmc+'</td>';
							bodyTableDetail += '<td>'+value.gmcdesc+'</td>';
							bodyTableDetail += '<td>'+value.ws_name+'</td>';
							bodyTableDetail += '</tr>';
							i++;
						});
						$('#bodyTableDetail').append(bodyTableDetail);
						$('#tableDetail').DataTable({
							'dom': 'Bfrtip',
							'responsive':true,
							'lengthMenu': [
							[ 20, 50, 100, -1 ],
							[ '20 rows', '50 rows', '100 rows', 'Show all' ]
							],
							'buttons': {
								buttons:[
								{
									extend: 'pageLength',
									className: 'btn btn-default',
								},
								
								]
							},
							'paging': true,
							'lengthChange': true,
							'pageLength': 20,
							'searching': true,
							'ordering': true,
							'order': [],
							'info': true,
							'autoWidth': true,
							"sPaginationType": "full_numbers",
							"bJQueryUI": true,
							"bAutoWidth": false,
							"processing": true
						});
					}
				}
			});

		    $('#judul_table').append().empty();
		    $('#judul_table').append('<center>List Antrian di <b>'+ws_name+'</b></center>');
		    
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