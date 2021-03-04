@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

table.table-bordered{
  /*border:1px solid rgb(150,150,150);*/
}
table.table-bordered > thead > tr > th{
  /*border:1px solid rgb(54, 59, 56) !important;*/
  text-align: center;
  background-color: #212121;  
  color:white;
}
table.table-bordered > tbody > tr > td{
  /*border:1px solid rgb(54, 59, 56);*/
  background-color: #212121;
  color: white;
  vertical-align: middle;
  text-align: center;
  padding:3px;
}
table.table-condensed > thead > tr > th{   
  color: black;
}
table.table-bordered > tfoot > tr > th{
  /*border:1px solid rgb(150,150,150);*/
  padding:0;
}
table.table-bordered > tbody > tr > td > p{
  color: #abfbff;
}

table.table-striped > thead > tr > th{
  /*border:1px solid black !important;*/
  padding-bottom: 0px;
  text-align: center; 
}

table.table-striped > tbody > tr > td{
  /*border: 1px solid #eeeeee !important;*/
  padding-bottom: 0px;
  border-collapse: collapse;
  vertical-align: middle;
  text-align: center;
  /*background-color: white;*/
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
  /*border:2px solid #f4f4f4;*/
  color: white;
}

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
	.content-wrapper{
		padding-top: 0px !important;
		padding-bottom: 0px !important;
		/*background-color: rgb(75,30,120) !important;*/
	}
	.visitor {
	  margin: auto;
	  width: 100vw;
	  /*font-size: 20px;*/
	  line-height:1.2;
	  height: 1.2em;
	  overflow: hidden;
	  vertical-align: middle;
	}

</style>
@endsection
@section('header')
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
@endsection
@section('content')
<section class="content" style="padding-top: 0px;padding-bottom: 0px;background-color: rgb(75,30,120)">
	<div class="row" style="padding-bottom: 0px;">
		<h1 id="jam" style="margin-top: 0px;padding-top: 30px;font-size: 30em;font-weight: bold;text-align: center;margin-bottom: -70px"></h1>
		<h1 id="visitor_info" style="margin-top: 0px;padding-top: 30px;font-size: 30em;font-weight: bold;text-align: center;margin-bottom: -70px"></h1>
		<h1 id="istirahat_info" style="margin-top: 0px;padding-top: 30px;font-size: 30em;font-weight: bold;text-align: center;margin-bottom: -70px"></h1>
		<center id="tanggal_all"><span id="tanggal" style="font-size: 80px;background-color: rgb(75,30,120);color: #fff"></span></center>
	</div>
</section>

@endsection
@section('scripts')
<script src="{{ url("js/highstock.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="https://cdn.jsdelivr.net/jquery.marquee/1.4.0/jquery.marquee.min.js"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_clock = new Audio('{{ url("sounds/railway_security.mp3") }}');
	var audio_clock_lobby = new Audio('{{ url("sounds/railway_lobby.mp3") }}');
	var myvar = setInterval(waktu,1000);
	var timeref;
	var istirahat = null;
	var visitor = null;
	var x;
	var countDownDate;

	jQuery(document).ready(function(){

		$('.visitor').marquee({
		  duration: 10000,
		  gap: 20,
		  delayBeforeStart: 0,
		  direction: 'left',
		});
		$('#tanggal').html('{{$dateTitle}}');
		$('#visitor_info').hide();
		$('#istirahat_info').hide();
		setInterval(fillVisitor,30000);
		$(".content-wrapper").css("background-color",'rgb(75, 30, 120)','important');
	});

	function refresh() {
		window.location.reload();
	}

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
 
	function waktu() {
		var time = new Date();
		document.getElementById("jam").style.fontSize = '30em';
		document.getElementById("jam").style.marginBottom = '-70px';
		document.getElementById("jam").innerHTML = addZero(time.getHours())+':'+addZero(time.getMinutes());
		timeref = addZero(time.getHours())+':'+addZero(time.getMinutes())+':'+addZero(time.getSeconds());
		if (timeref == '06:00:00') {
			location.reload();
		}
		if (timeref == '07:00:00') {
			location.reload();
		}
		if (timeref == '08:00:00') {
			location.reload();
		}
		if (timeref == '09:00:00') {
			location.reload();
		}
		if (timeref == '10:00:00') {
			location.reload();
		}
		if (timeref == '11:00:00') {
			location.reload();
		}
		if (timeref == '12:00:00') {
			location.reload();
		}
		if (timeref == '13:00:00') {
			location.reload();
		}
		if (timeref == '14:00:00') {
			location.reload();
		}
		if (timeref == '15:00:00') {
			location.reload();
		}
		if (timeref == '15:30:00') {
			location.reload();
		}
		if (timeref == '16:00:00') {
			location.reload();
		}

		if (visitor == null) {
			if ('{{$dayTitle}}' == 'Friday') {
				if(timeref >= '09:30:00' && timeref <= '09:40:00'){
					$('#istirahat_info').show();
					$('#visitor_info').hide();
					$('#jam').hide();
					document.getElementById("istirahat_info").innerHTML = "<span style='background-color:#FFFF00;padding-left:70px;padding-right:70px'>WAKTU ISTIRAHAT 休憩時間</span><br><span style='color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'>(09:30 - 09:40)</span><br><span id='timeristirahat' style='font-size:2em;color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'></span><br><span style='font-size:0.75em;color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'>("+addZero(time.getHours())+':'+addZero(time.getMinutes())+")</span>";
					document.getElementById("istirahat_info").style.fontSize = '7em';
					document.getElementById("istirahat_info").style.marginBottom = '10px';
					$(".content-wrapper").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					$(".content-wrapper").css("background-size","100%",'important');
					$(".content-wrapper").css("background-repeat","no-repeat",'important');
					// $(".content").css("background-color",'#a6ffa6','important');
					$(".content").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					$(".content").css("background-size","100%",'important');
					$(".content").css("background-repeat","no-repeat",'important');
						// $("#tanggal_all").css("background-color",'#a6ffa6');
					// $("#tanggal").css("background-color",'#a6ffa6');
					// $("#tanggal_all").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					document.getElementById('tanggal').style.backgroundColor = "transparent";
					$("#tanggal").css("color",'#FFFF00');
					$("#istirahat_info").css("color",'#29006b');
					$("#tanggal_all").css("background-color",'transparent');
					$("#tanggal").css("background-color",'transparent');
					$("#tanggal").css("text-shadow",'-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000');
					$("#istirahat_info").css("text-shadow",'-5px -5px 0 #fff, 5px -5px 0 #fff, -5px 5px 0 #fff, 5px 5px 0 #fff');
					// $("#istirahat_info").css("background-color",'#a6ffa6');
					istirahat = "Istirahat";

					var currentdate = new Date(); 
					var dateend = addZero(time.getFullYear())+'-'+addZero((time.getMonth()+1))+'-'+addZero(time.getDate())+' 09:40:00';
					countDownDate = new Date(dateend).getTime();
					x = setInterval(countdown,1000);
				}else if(timeref >= '12:00:00' && timeref <= '13:10:00'){
					$('#istirahat_info').show();
					$('#visitor_info').hide();
					$('#jam').hide();
					document.getElementById("istirahat_info").innerHTML = "<span style='background-color:#FFFF00;padding-left:70px;padding-right:70px'>WAKTU ISTIRAHAT 休憩時間</span><br><span style='color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'>(12:00 - 13:10)</span><br><span id='timeristirahat' style='font-size:2em;color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'></span><br><span style='font-size:0.75em;color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'>("+addZero(time.getHours())+':'+addZero(time.getMinutes())+")</span>";
					document.getElementById("istirahat_info").style.fontSize = '7em';
					document.getElementById("istirahat_info").style.marginBottom = '10px';
					$(".content-wrapper").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					$(".content-wrapper").css("background-size","100%",'important');
					$(".content-wrapper").css("background-repeat","no-repeat",'important');
					// $(".content").css("background-color",'#a6ffa6','important');
					$(".content").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					$(".content").css("background-size","100%",'important');
					$(".content").css("background-repeat","no-repeat",'important');
						// $("#tanggal_all").css("background-color",'#a6ffa6');
					// $("#tanggal").css("background-color",'#a6ffa6');
					// $("#tanggal_all").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					document.getElementById('tanggal').style.backgroundColor = "transparent";
					$("#tanggal").css("color",'#FFFF00');
					$("#istirahat_info").css("color",'#29006b');
					$("#tanggal_all").css("background-color",'transparent');
					$("#tanggal").css("background-color",'transparent');
					$("#tanggal").css("text-shadow",'-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000');
					$("#istirahat_info").css("text-shadow",'-5px -5px 0 #fff, 5px -5px 0 #fff, -5px 5px 0 #fff, 5px 5px 0 #fff');
					// $("#istirahat_info").css("background-color",'#a6ffa6');
					istirahat = "Istirahat";

					var currentdate = new Date(); 
					var dateend = addZero(time.getFullYear())+'-'+addZero((time.getMonth()+1))+'-'+addZero(time.getDate())+' 13:10:00';
					countDownDate = new Date(dateend).getTime();
					x = setInterval(countdown,1000);
				}else if(timeref >= '15:00:00' && timeref <= '15:10:00'){
					$('#istirahat_info').show();
					$('#visitor_info').hide();
					$('#jam').hide();
					document.getElementById("istirahat_info").innerHTML = "<span style='background-color:#FFFF00;padding-left:70px;padding-right:70px'>WAKTU ISTIRAHAT 休憩時間</span><br><span style='color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'>(15:00 - 15:10)</span><br><span id='timeristirahat' style='font-size:2em;color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'></span><br><span style='font-size:0.75em;color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'>("+addZero(time.getHours())+':'+addZero(time.getMinutes())+")</span>";
					document.getElementById("istirahat_info").style.fontSize = '7em';
					document.getElementById("istirahat_info").style.marginBottom = '10px';
					$(".content-wrapper").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					$(".content-wrapper").css("background-size","100%",'important');
					$(".content-wrapper").css("background-repeat","no-repeat",'important');
					// $(".content").css("background-color",'#a6ffa6','important');
					$(".content").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					$(".content").css("background-size","100%",'important');
					$(".content").css("background-repeat","no-repeat",'important');
						// $("#tanggal_all").css("background-color",'#a6ffa6');
					// $("#tanggal").css("background-color",'#a6ffa6');
					// $("#tanggal_all").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					document.getElementById('tanggal').style.backgroundColor = "transparent";
					$("#tanggal").css("color",'#FFFF00');
					$("#istirahat_info").css("color",'#29006b');
					$("#tanggal_all").css("background-color",'transparent');
					$("#tanggal").css("background-color",'transparent');
					$("#tanggal").css("text-shadow",'-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000');
					$("#istirahat_info").css("text-shadow",'-5px -5px 0 #fff, 5px -5px 0 #fff, -5px 5px 0 #fff, 5px 5px 0 #fff');
					// $("#istirahat_info").css("background-color",'#a6ffa6');
					istirahat = "Istirahat";

					var currentdate = new Date(); 
					var dateend = addZero(time.getFullYear())+'-'+addZero((time.getMonth()+1))+'-'+addZero(time.getDate())+' 15:10:00';
					countDownDate = new Date(dateend).getTime();
					x = setInterval(countdown,1000);
				}else{
					if (visitor == null) {
						$('#istirahat_info').hide();
						$('#jam').show();
						$(".content-wrapper").css("background-color",'rgb(75, 30, 120)','important');
						$(".content").css("background-color",'rgb(75, 30, 120)','important');
						$("#tanggal_all").css("background-color",'rgb(75, 30, 120)');
						$("#tanggal").css("background-color",'rgb(75, 30, 120)');
						$("#tanggal").css("color",'#fff');
						istirahat = null;
						countDownDate = null;
					}
				}
			}else{
				if(timeref >= '09:30:00' && timeref <= '09:40:00'){
					$('#istirahat_info').show();
					$('#visitor_info').hide();
					$('#jam').hide();
					document.getElementById("istirahat_info").innerHTML = "<span style='background-color:#FFFF00;padding-left:70px;padding-right:70px'>WAKTU ISTIRAHAT 休憩時間</span><br><span style='color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'>(09:30 - 09:40)</span><br><span id='timeristirahat' style='font-size:2em;color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'></span><br><span style='font-size:0.75em;color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'>("+addZero(time.getHours())+':'+addZero(time.getMinutes())+")</span>";
					document.getElementById("istirahat_info").style.fontSize = '7em';
					document.getElementById("istirahat_info").style.marginBottom = '10px';
					$(".content-wrapper").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					$(".content-wrapper").css("background-size","100%",'important');
					$(".content-wrapper").css("background-repeat","no-repeat",'important');
					// $(".content").css("background-color",'#a6ffa6','important');
					$(".content").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					$(".content").css("background-size","100%",'important');
					$(".content").css("background-repeat","no-repeat",'important');
						// $("#tanggal_all").css("background-color",'#a6ffa6');
					// $("#tanggal").css("background-color",'#a6ffa6');
					// $("#tanggal_all").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					document.getElementById('tanggal').style.backgroundColor = "transparent";
					$("#tanggal").css("color",'#FFFF00');
					$("#istirahat_info").css("color",'#29006b');
					$("#tanggal_all").css("background-color",'transparent');
					$("#tanggal").css("background-color",'transparent');
					$("#tanggal").css("text-shadow",'-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000');
					$("#istirahat_info").css("text-shadow",'-5px -5px 0 #fff, 5px -5px 0 #fff, -5px 5px 0 #fff, 5px 5px 0 #fff');
					// $("#istirahat_info").css("background-color",'#a6ffa6');
					istirahat = "Istirahat";

					var currentdate = new Date(); 
					var dateend = addZero(time.getFullYear())+'-'+addZero((time.getMonth()+1))+'-'+addZero(time.getDate())+' 09:40:00';
					countDownDate = new Date(dateend).getTime();
					x = setInterval(countdown,1000);
				}else if(timeref >= '12:15:00' && timeref <= '12:55:00'){
					$('#istirahat_info').show();
					$('#visitor_info').hide();
					$('#jam').hide();
					document.getElementById("istirahat_info").innerHTML = "<span style='background-color:#FFFF00;padding-left:70px;padding-right:70px'>WAKTU ISTIRAHAT 休憩時間</span><br><span style='color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'>(12:15 - 12:55)</span><br><span id='timeristirahat' style='font-size:2em;color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'></span><br><span style='font-size:0.75em;color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'>("+addZero(time.getHours())+':'+addZero(time.getMinutes())+")</span>";
					document.getElementById("istirahat_info").style.fontSize = '7em';
					document.getElementById("istirahat_info").style.marginBottom = '10px';
					$(".content-wrapper").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					$(".content-wrapper").css("background-size","100%",'important');
					$(".content-wrapper").css("background-repeat","no-repeat",'important');
					// $(".content").css("background-color",'#a6ffa6','important');
					$(".content").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					$(".content").css("background-size","100%",'important');
					$(".content").css("background-repeat","no-repeat",'important');
						// $("#tanggal_all").css("background-color",'#a6ffa6');
					// $("#tanggal").css("background-color",'#a6ffa6');
					// $("#tanggal_all").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					document.getElementById('tanggal').style.backgroundColor = "transparent";
					$("#tanggal").css("color",'#FFFF00');
					$("#istirahat_info").css("color",'#29006b');
					$("#tanggal_all").css("background-color",'transparent');
					$("#tanggal").css("background-color",'transparent');
					$("#tanggal").css("text-shadow",'-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000');
					$("#istirahat_info").css("text-shadow",'-5px -5px 0 #fff, 5px -5px 0 #fff, -5px 5px 0 #fff, 5px 5px 0 #fff');
					// $("#istirahat_info").css("background-color",'#a6ffa6');
					istirahat = "Istirahat";

					var currentdate = new Date(); 
					var dateend = addZero(time.getFullYear())+'-'+addZero((time.getMonth()+1))+'-'+addZero(time.getDate())+' 12:55:00';
					countDownDate = new Date(dateend).getTime();
					x = setInterval(countdown,1000);
				}else if(timeref >= '14:20:00' && timeref <= '14:30:00'){
					$('#istirahat_info').show();
					$('#visitor_info').hide();
					$('#jam').hide();
					document.getElementById("istirahat_info").innerHTML = "<span style='background-color:#FFFF00;padding-left:70px;padding-right:70px'>WAKTU ISTIRAHAT 休憩時間</span><br><span style='color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'>(14:20 - 14:30)</span><br><span id='timeristirahat' style='font-size:2em;color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'></span><br><span style='font-size:0.75em;color:#FFFF00;text-shadow:-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000'>("+addZero(time.getHours())+':'+addZero(time.getMinutes())+")</span>";
					document.getElementById("istirahat_info").style.fontSize = '7em';
					document.getElementById("istirahat_info").style.marginBottom = '10px';
					$(".content-wrapper").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					$(".content-wrapper").css("background-size","100%",'important');
					$(".content-wrapper").css("background-repeat","no-repeat",'important');
					// $(".content").css("background-color",'#a6ffa6','important');
					$(".content").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					$(".content").css("background-size","100%",'important');
					$(".content").css("background-repeat","no-repeat",'important');
						// $("#tanggal_all").css("background-color",'#a6ffa6');
					// $("#tanggal").css("background-color",'#a6ffa6');
					// $("#tanggal_all").css("background-image","url({{url('data_file/sakura.jpg')}})",'important');
					document.getElementById('tanggal').style.backgroundColor = "transparent";
					$("#tanggal").css("color",'#FFFF00');
					$("#istirahat_info").css("color",'#29006b');
					$("#tanggal_all").css("background-color",'transparent');
					$("#tanggal").css("background-color",'transparent');
					$("#tanggal").css("text-shadow",'-5px -5px 0 #000, 5px -5px 0 #000, -5px 5px 0 #000, 5px 5px 0 #000');
					$("#istirahat_info").css("text-shadow",'-5px -5px 0 #fff, 5px -5px 0 #fff, -5px 5px 0 #fff, 5px 5px 0 #fff');
					// $("#istirahat_info").css("background-color",'#a6ffa6');
					istirahat = "Istirahat";

					var currentdate = new Date(); 
					var dateend = addZero(time.getFullYear())+'-'+addZero((time.getMonth()+1))+'-'+addZero(time.getDate())+' 14:30:00';
					countDownDate = new Date(dateend).getTime();
					x = setInterval(countdown,1000);
				}else{
					if (visitor == null) {
						$('#istirahat_info').hide();
						$('#jam').show();
						$(".content-wrapper").css("background-image",'none','important');
						$(".content-wrapper").css("background-color",'rgb(75, 30, 120)','important');
						$(".content").css("background-image",'none','important');
						$(".content").css("background-color",'rgb(75, 30, 120)','important');
						$("#tanggal_all").css("background-color",'rgb(75, 30, 120)');
						$("#tanggal").css("background-color",'rgb(75, 30, 120)');
						$("#tanggal").css("color",'#fff');
						$("#tanggal").css("text-shadow",'0px');
						istirahat = null;
						countDownDate = null;
					}
				}
			}
		}
	}

	function fillVisitor() {
		$.get('{{ url("fetch/office_clock/visitor") }}', function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$('#istirahat_info').hide();
					if (result.visitors.length > 0) {
						for (var i = 0; i < result.visitors.length; i++) {
							$('#visitor_info').show();
							$('#jam').hide();
							$('#istirahat_info').hide();
							
							if (result.visitors[i].department == null && result.visitors[i].name == 'Budhi Apriyanto') {
								document.getElementById("visitor_info").innerHTML = result.visitors[i].company+'<br>('+result.visitors[i].name.split(' ').slice(0,2).join(' ')+' - Production Engineering)<br>AT SECURITY<br>';
							}else if (result.visitors[i].department == null && result.visitors[i].name == 'Arief Soekamto') {
								document.getElementById("visitor_info").innerHTML = result.visitors[i].company+'<br>('+result.visitors[i].name.split(' ').slice(0,2).join(' ')+' - Human Resources)<br>AT SECURITY<br>';
							}else{
								document.getElementById("visitor_info").innerHTML = result.visitors[i].company+'<br>('+result.visitors[i].name.split(' ').slice(0,2).join(' ')+' - '+result.visitors[i].department+')<br>AT SECURITY<br>';
							}
							document.getElementById("visitor_info").style.fontSize = '7em';
							document.getElementById("visitor_info").style.marginBottom = '10px';
							$(".content-wrapper").css("background-color",'rgb(75, 30, 120)','important');
							$(".content").css("background-color",'rgb(75, 30, 120)','important');
							$(".content-wrapper").css("background-image",'none','important');
							$(".content").css("background-image",'none','important');
							$("#tanggal_all").css("background-color",'rgb(75, 30, 120)');
							$("#tanggal").css("background-color",'rgb(75, 30, 120)');
							$("#tanggal").css("color",'#fff');
							$("#tanggal").css("text-shadow",'none');
							$("#visitor_info").css("color",'#fff');
							$("#visitor_info").css("background-color",'rgb(75, 30, 120)');
							audio_clock.play();
						}
					}
					if (result.visitors_lobby.length > 0) {
						for (var i = 0; i < result.visitors_lobby.length; i++) {
							$('#visitor_info').show();
							$('#jam').hide();
							$('#istirahat_info').hide();

							if (result.visitors_lobby[i].department == null && result.visitors_lobby[i].name == 'Budhi Apriyanto') {
								document.getElementById("visitor_info").innerHTML = result.visitors_lobby[i].company+'<br>('+result.visitors_lobby[i].name.split(' ').slice(0,2).join(' ')+' - Production Engineering)<br>AT LOBBY<br>';
							}else if (result.visitors_lobby[i].department == null && result.visitors_lobby[i].name == 'Arief Soekamto') {
								document.getElementById("visitor_info").innerHTML = result.visitors_lobby[i].company+'<br>('+result.visitors_lobby[i].name.split(' ').slice(0,2).join(' ')+' - Human Resources)<br>AT LOBBY<br>';
							}else{
								document.getElementById("visitor_info").innerHTML = result.visitors_lobby[i].company+'<br>('+result.visitors_lobby[i].name.split(' ').slice(0,2).join(' ')+' - '+result.visitors_lobby[i].department+')<br>AT LOBBY<br>';
							}

							document.getElementById("visitor_info").style.fontSize = '7em';
							document.getElementById("visitor_info").style.marginBottom = '10px';
							$(".content-wrapper").css("background-color",'rgb(255, 247, 0)','important');
							$(".content").css("background-color",'rgb(255, 247, 0)','important');
							$(".content-wrapper").css("background-image",'none','important');
							$(".content").css("background-image",'none','important');
  							$("#tanggal_all").css("background-color",'rgb(255, 247, 0)');
							$("#tanggal").css("background-color",'rgb(255, 247, 0)');
							$("#tanggal").css("color",'#1100ff');
							$("#tanggal").css("text-shadow",'none');
							$("#visitor_info").css("color",'#1100ff');
							$("#visitor_info").css("background-color",'rgb(255, 247, 0)');
							audio_clock_lobby.play();
						}
					}
					visitor = 'Visitor';
				}else{
					if (istirahat == null) {
						$('#visitor_info').hide();
						$('#jam').show();
						$(".content-wrapper").css("background-color",'rgb(75, 30, 120)','important');
						$(".content").css("background-color",'rgb(75, 30, 120)','important');
						$("#tanggal_all").css("background-color",'rgb(75, 30, 120)');
						$("#tanggal").css("background-color",'rgb(75, 30, 120)');
						$("#tanggal").css("color",'#fff');
						$("#tanggal").css("text-shadow",'0px');
					}
					visitor = null;
				}
			}
		});
	}

	function countdown() {
	  var now = new Date().getTime();

	  // Find the distance between now and the count down date
	  var distance = countDownDate - now;

	  // Time calculations for days, hours, minutes and seconds
	  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
	  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
	  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
	  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

	  // Display the result in the element with id="demo"
	  document.getElementById("timeristirahat").innerHTML = addZero(minutes) + ":" + addZero(seconds);

	  // If the count down is finished, write some text
	  if (distance <= 0) {
	    clearInterval(x);
	    document.getElementById("timeristirahat").innerHTML = "";
	  }
	}
</script>
@endsection