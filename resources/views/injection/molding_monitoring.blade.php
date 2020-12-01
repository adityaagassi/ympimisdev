@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

	table.table-bordered{
  border:1px solid black;
}
/*table.table-bordered > thead > tr > th{
  border:1px solid rgb(54, 59, 56) !important;
  text-align: center;
  background-color: #212121;  
  color:white;
}
table.table-bordered > tbody > tr > td{
  border:1px solid rgb(54, 59, 56);
  background-color: #212121;
  color: white;
  vertical-align: middle;
  text-align: center;
  padding:3px;
}
table.table-condensed > thead > tr > th{   
  color: black
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(150,150,150);
  padding:0;
}
table.table-bordered > tbody > tr > td > p{
  color: #abfbff;
}

table.table-striped > thead > tr > th{
  border:1px solid black !important;
  text-align: center;
  background-color: rgba(126,86,134,.7) !important;  
}

table.table-striped > tbody > tr > td{
  border: 1px solid #eeeeee !important;
  border-collapse: collapse;
  color: black;
  padding: 3px;
  vertical-align: middle;
  text-align: center;
  background-color: white;
}

thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
}
tfoot>tr>th{
  text-align:center;
}
td:hover {
  overflow: visible;
}
table > thead > tr > th{
  border:2px solid #f4f4f4;
  color: white;
}*/
	.content{
		color: white;
		font-weight: bold;
	}
	#loading, #error { display: none; }

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}

  .gambar {
    width: 300px;
    background-color: none;
    border-radius: 5px;
    margin-left: 5px;
    margin-top: 15px;
    display: inline-block;
    border: 2px solid white;
  }

  #table-count{
  	border: 1px solid #000 !important;
  	padding: 5px;
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
		background-color: #3c3c3c;
		color: white;
	}
	50%, 100% {
		background-color: #91ff5e;
		color: #3c3c3c;
	}
}

#warning {
	/*width: 50px;
	height: 50px;*/
	-webkit-animation: warning 1s infinite;  /* Safari 4+ */
	-moz-animation: warning 1s infinite;  /* Fx 5+ */
	-o-animation: warning 1s infinite;  /* Opera 12+ */
	animation: warning 1s infinite;  /* IE 10+, Fx 29+ */
}

@-webkit-keyframes warning {
	0%, 49% {
		background-color: #3c3c3c;
		color: white;
	}
	50%, 100% {
		background-color: #ffea5e;
		color: #3c3c3c;
	}
}

#danger {
	/*width: 50px;
	height: 50px;*/
	-webkit-animation: danger 1s infinite;  /* Safari 4+ */
	-moz-animation: danger 1s infinite;  /* Fx 5+ */
	-o-animation: danger 1s infinite;  /* Opera 12+ */
	animation: danger 1s infinite;  /* IE 10+, Fx 29+ */
}

@-webkit-keyframes danger {
	0%, 49% {
		background-color: #3c3c3c;
		color: white;
	}
	50%, 100% {
		background-color: #ff5e5e;
		color: #3c3c3c;
	}
}

</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<center><h4 style="font-weight: bold;font-size: 35px;padding: 10px;;background-color: #42d4f5;color: black">MOLDING TERPASANG</h4></center>
				<div id="cont" style="margin-top: 0px"></div>
			</div>
		</div>

		<div class="col-xs-12">
			<div class="row">
				<center><h4 style="font-weight: bold;font-size: 35px;padding: 10px;;background-color: #69f542;color: black">MOLDING READY</h4></center>
				<div id="cont3"></div>
			</div>
		</div>

		<div class="col-xs-12">
			<div class="row">
				<center><h4 style="font-weight: bold;font-size: 35px;padding: 10px;;background-color: #f59042;color: black">MOLDING PERIODIK</h4></center>
				<div id="cont2"></div>
			</div>
		</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<script src="{{ url("js/solid-gauge.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		fillChart();
		setInterval(fillChart, 10000);
	});

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

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
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	function getActualDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		return day + "-" + month + "-" + year;
	}

	function fillChart() {

		$.get('{{ url("fetch/molding_monitoring") }}', function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					$('#cont').empty();
					$('#cont2').empty();
					$('#cont3').empty();

					var part = [];
					var product = [];
					var last_counter = [];
					var ng_count = [];
					var color = [];
					var status_mesin = [];
					var data = [];
					var body = '';

					for (var i = 0; i < result.query_pasang.length; i++) {
						part.push(result.query_pasang[i].part);
						product.push(result.query_pasang[i].product);
						last_counter.push(parseInt((parseInt(result.query_pasang[i].last_counter) / parseInt(result.query_pasang[i].qty_shot)).toFixed(0)));
						ng_count.push(parseInt(result.query_pasang[i].ng_count));
						if (result.query_pasang[i].status_mesin == null) {
							status_mesin.push('STORAGE');
						}else{
							status_mesin.push(result.query_pasang[i].status_mesin);
						}
						data.push([parseInt((parseInt(result.query_pasang[i].last_counter) / parseInt(result.query_pasang[i].qty_shot)).toFixed(0))]);

						var a = i+1;
						body += '<div class="gambar" style="margin-top:0px" id="container'+a+'"></div>';
					}
					$('#cont').append(body);

					for (var j = 0; j < part.length; j++) {
						var a = j+1;
						var container = 'container'+a;
						var containerin = "";

						containerin += '<table style="text-align:center;">';

						containerin += '<tr><td colspan="3" style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:black;background-color:white;font-size:30px">'+part[j]+'<br>';
						containerin += '</td></tr>';

						// containerin += '<tr><td colspan="3" style="padding:0px">';
						// containerin += '</td></tr>';

						containerin += '<tr>';
						containerin += '<td colspan="3" style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">';

						containerin += '<div class="progress-group" id="progress_div" style="padding-bottom:0px;margin-bottom:0px">';
						containerin += '<div class="progress" style="height: 30px; border-style: solid;border-width: 1px; border-color: #d3d3d3;padding-bottom:0px;margin-bottom:0px">';
						containerin += '<span class="progress-text" id="progress_text_pasang'+j+'" style="font-size: 20px;padding-top:20px;color:black"></span>';
						containerin += '<div id="progress_bar_pasang'+j+'" style="font-size: 2px;"></div>';
						containerin += '</div>';
						containerin += '</div>';

						containerin += '</td>';
						containerin += '</tr>';

						if (last_counter[j] == 0) {
							containerin += '<tr><td style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">SHOTS</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter[j]+'</td></tr>';
						}else if (last_counter[j] > 0 && last_counter[j] <= 10000) {
							containerin += '<tr><td style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">SHOTS</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td id="sedang" style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter[j]+'</td></tr>';
						}else if (last_counter[j] > 10000  && last_counter[j] <= 15000){
							containerin += '<tr><td style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">SHOTS</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td id="warning" style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter[j]+'</td></tr>';
						}else if (last_counter[j] > 15000){
							containerin += '<tr><td style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">SHOTS</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td id="danger" style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter[j]+'</td></tr>';
						}

		            	if (ng_count[j] == 0) {
		            		containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px">NG</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:30px;padding-right:30px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+ng_count[j]+' ( 0% )</td></tr>';
		            	}else{
		            		containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px">NG</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:30px;padding-right:30px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+ng_count[j]+' ( '+((ng_count[j] / last_counter[j])*100).toFixed(0)+'% )</td></tr>';
		            	}

		            	containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px">LOC</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+status_mesin[j]+'</td></tr>';

		            	containerin += '</table>';

		            	$('#'+container).append(containerin);
					}

					for (var j = 0; j < part.length; j++) {
						persen = (parseFloat(last_counter[j]) / 15000) * 100;
						// $('#progress_text_pasang'+j).html(persen.toFixed(1)+"%");
						$('#progress_bar_pasang'+j).css('width', persen.toFixed(1)+'%');
						$('#progress_bar_pasang'+j).css('color', 'white');
						$('#progress_bar_pasang'+j).css('font-weight', 'bold');
						if (last_counter[j] > 0 && last_counter[j] <= 10000) {
							$('#progress_bar_pasang'+j).attr('class', 'progress-bar-success progress-bar progress-bar-striped');
						}else if(last_counter[j] > 10000 && last_counter[j] <= 15000){
							$('#progress_bar_pasang'+j).attr('class', 'progress-bar-warning progress-bar progress-bar-striped');
						}else{
							$('#progress_bar_pasang'+j).attr('class', 'progress-bar-danger progress-bar progress-bar-striped');
						}
					};

					var part_maintenance = [];
					var product_maintenance = [];
					var last_counter_maintenance = [];
					var ng_count_maintenance = [];
					var color_maintenance = [];
					var status_mesin_maintenance = [];
					var data_maintenance = [];
					var body_maintenance = '';

					for (var i = 0; i < result.query_not_ready.length; i++) {
						part_maintenance.push(result.query_not_ready[i].part);
						product_maintenance.push(result.query_not_ready[i].product);
						last_counter_maintenance.push(parseInt((parseInt(result.query_not_ready[i].last_counter) / parseInt(result.query_not_ready[i].qty_shot)).toFixed(0)));
						ng_count_maintenance.push(parseInt(result.query_not_ready[i].ng_count));
						if (result.query_not_ready[i].status_mesin == null && result.query_not_ready[i].status == "LEPAS") {
							status_mesin_maintenance.push('STORAGE');
						}else if(result.query_not_ready[i].status_mesin == null && result.query_not_ready[i].status == "DIPERBAIKI"){
							status_mesin_maintenance.push('PERIODIK');
						}else{
							status_mesin_maintenance.push(result.query_not_ready[i].status_mesin);
						}
						data_maintenance.push([parseInt((parseInt(result.query_not_ready[i].last_counter) / parseInt(result.query_not_ready[i].qty_shot)).toFixed(0))]);

						var a = i+1;
						body_maintenance += '<div class="gambar" id="container2'+a+'"></div>';
					}
					$('#cont2').append(body_maintenance);

					for (var k = 0; k < part_maintenance.length; k++) {
						var a = k+1;
						var container2 = 'container2'+a;
						var containerin = "";

						containerin += '<table style="text-align:center;">';

						containerin += '<tr><td colspan="3" style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:black;background-color:white;font-size:30px">'+part_maintenance[k]+'</td></tr>';

						containerin += '<tr>';
						containerin += '<td colspan="3" style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">';

						containerin += '<div class="progress-group" id="progress_div" style="padding-bottom:0px;margin-bottom:0px">';
						containerin += '<div class="progress" style="height: 30px; border-style: solid;border-width: 1px; border-color: #d3d3d3;padding-bottom:0px;margin-bottom:0px">';
						containerin += '<span class="progress-text" id="progress_text_maintenance'+j+'" style="font-size: 20px;padding-top:20px;color:black"></span>';
						containerin += '<div id="progress_bar_maintenance'+k+'" style="font-size: 2px;"></div>';
						containerin += '</div>';
						containerin += '</div>';

						containerin += '</td>';
						containerin += '</tr>';

						if (last_counter_maintenance[k] == 0) {
							containerin += '<tr><td style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">SHOTS</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter_maintenance[k]+'</td></tr>';
						}else if (last_counter_maintenance[k] > 0 && last_counter_maintenance[k] <= 10000) {
							containerin += '<tr><td style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">SHOTS</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td  style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter_maintenance[k]+'</td></tr>';
						}else if (last_counter_maintenance[k] > 10000  && last_counter_maintenance[k] <= 15000){
							containerin += '<tr><td style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">SHOTS</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td  style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter_maintenance[k]+'</td></tr>';
						}else if (last_counter_maintenance[k] > 15000){
							containerin += '<tr><td style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">SHOTS</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td  style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter_maintenance[k]+'</td></tr>';
						}

		            	if (ng_count_maintenance[k] == 0) {
		            		containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px">NG</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:30px;padding-right:30px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+ng_count_maintenance[k]+' ( 0% )</td></tr>';
		            	}else{
		            		containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px">NG</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:30px;padding-right:30px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+ng_count_maintenance[k]+' ( '+((ng_count_maintenance[k] / last_counter_maintenance[k])*100).toFixed(0)+'% )</td></tr>';
		            	}

		            	containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px">LOC</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+status_mesin_maintenance[k]+'</td></tr>';

		            	containerin += '</table>';

		            	$('#'+container2).append(containerin);
					}

					for (var j = 0; j < part_maintenance.length; j++) {
						persen = (parseFloat(last_counter_maintenance[j]) / 15000) * 100;
						// $('#progress_text_pasang'+j).html(persen.toFixed(1)+"%");
						$('#progress_bar_maintenance'+j).css('width', persen.toFixed(1)+'%');
						$('#progress_bar_maintenance'+j).css('color', 'white');
						$('#progress_bar_maintenance'+j).css('font-weight', 'bold');
						if (last_counter_maintenance[j] > 0 && last_counter_maintenance[j] <= 10000) {
							$('#progress_bar_maintenance'+j).attr('class', 'progress-bar-success progress-bar progress-bar-striped');
						}else if(last_counter_maintenance[j] > 10000 && last_counter_maintenance[j] <= 15000){
							$('#progress_bar_maintenance'+j).attr('class', 'progress-bar-warning progress-bar progress-bar-striped');
						}else{
							$('#progress_bar_maintenance'+j).attr('class', 'progress-bar-danger progress-bar progress-bar-striped');
						}
					};

					var part_ready = [];
					var product_ready = [];
					var last_counter_ready = [];
					var ng_count_ready = [];
					var color_ready = [];
					var status_mesin_ready = [];
					var data_ready = [];
					var body_ready = '';

					for (var i = 0; i < result.query_ready.length; i++) {
						part_ready.push(result.query_ready[i].part);
						product_ready.push(result.query_ready[i].product);
						last_counter_ready.push(parseInt((parseInt(result.query_ready[i].last_counter) / parseInt(result.query_ready[i].qty_shot)).toFixed(0)));
						ng_count_ready.push(parseInt(result.query_ready[i].ng_count));
						if (result.query_ready[i].status_mesin == null && result.query_ready[i].status == "LEPAS") {
							status_mesin_ready.push('STORAGE');
						}else if(result.query_ready[i].status_mesin == null && result.query_ready[i].status == "DIPERBAIKI"){
							status_mesin_ready.push('PERIODIK');
						}else{
							status_mesin_ready.push(result.query_ready[i].status_mesin);
						}
						data_ready.push([parseInt((parseInt(result.query_ready[i].last_counter) / parseInt(result.query_ready[i].qty_shot)).toFixed(0))]);

						var a = i+1;
						body_ready += '<div class="gambar" id="container3'+a+'"></div>';
					}
					$('#cont3').append(body_ready);

					for (var k = 0; k < part_ready.length; k++) {
						var a = k+1;
						var container3 = 'container3'+a;
						var containerin = "";

						containerin += '<table style="text-align:center;">';

						containerin += '<tr><td colspan="3" style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:0px;padding-bottom:2px;color:black;background-color:white;font-size:30px">'+part_ready[k]+'</td></tr>';

						containerin += '<tr>';
						containerin += '<td colspan="3" style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">';

						containerin += '<div class="progress-group" id="progress_div" style="padding-bottom:0px;margin-bottom:0px">';
						containerin += '<div class="progress" style="height: 30px; border-style: solid;border-width: 1px; border-color: #d3d3d3;padding-bottom:0px;margin-bottom:0px">';
						containerin += '<span class="progress-text" id="progress_text_ready'+j+'" style="font-size: 20px;padding-top:20px;color:black"></span>';
						containerin += '<div id="progress_bar_ready'+k+'" style="font-size: 2px;"></div>';
						containerin += '</div>';
						containerin += '</div>';

						containerin += '</td>';
						containerin += '</tr>';

						if (last_counter_ready[k] == 0) {
							containerin += '<tr><td style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">SHOTS</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter_ready[k]+'</td></tr>';
						}else if (last_counter_ready[k] > 0 && last_counter_ready[k] <= 10000) {
							containerin += '<tr><td style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">SHOTS</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td  style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter_ready[k]+'</td></tr>';
						}else if (last_counter_ready[k] > 10000  && last_counter_ready[k] <= 15000){
							containerin += '<tr><td style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">SHOTS</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td  style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter_ready[k]+'</td></tr>';
						}else if (last_counter_ready[k] > 15000){
							containerin += '<tr><td style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">SHOTS</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td  style="border: 1px solid #fff !important;padding-left:50px;padding-right:50px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter_ready[k]+'</td></tr>';
						}

		            	if (ng_count_ready[k] == 0) {
		            		containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px">NG</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:30px;padding-right:30px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+ng_count_ready[k]+' ( 0% )</td></tr>';
		            	}else{
		            		containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px">NG</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:30px;padding-right:30px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+ng_count_ready[k]+' ( '+((ng_count_ready[k] / last_counter_ready[k])*100).toFixed(0)+'% )</td></tr>';
		            	}

		            	containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px">LOC</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+status_mesin_ready[k]+'</td></tr>';

		            	containerin += '</table>';

		            	$('#'+container3).append(containerin);
					}

					for (var j = 0; j < part_ready.length; j++) {
						persen = (parseFloat(last_counter_ready[j]) / 15000) * 100;
						// $('#progress_text_pasang'+j).html(persen.toFixed(1)+"%");
						$('#progress_bar_ready'+j).css('width', persen.toFixed(1)+'%');
						$('#progress_bar_ready'+j).css('color', 'white');
						$('#progress_bar_ready'+j).css('font-weight', 'bold');
						if (last_counter_ready[j] > 0 && last_counter_ready[j] <= 10000) {
							$('#progress_bar_ready'+j).attr('class', 'progress-bar-success progress-bar progress-bar-striped');
						}else if(last_counter_ready[j] > 10000 && last_counter_ready[j] <= 15000){
							$('#progress_bar_ready'+j).attr('class', 'progress-bar-warning progress-bar progress-bar-striped');
						}else{
							$('#progress_bar_ready'+j).attr('class', 'progress-bar-danger progress-bar progress-bar-striped');
						}
					};
				}
			}
		});

	}


</script>
@endsection