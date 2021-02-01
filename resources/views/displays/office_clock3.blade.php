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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
@endsection
@section('content')
<section class="content" style="padding-top: 0px;padding-bottom: 0px;background-color: rgb(75,30,120)">
	<div class="row" style="padding-bottom: 0px;">
		<h1 id="jam" style="margin-top: 0px;padding-top: 30px;font-size: 30em;font-weight: bold;text-align: center;margin-bottom: -70px"></h1>
		<h1 id="visitor_info" style="margin-top: 0px;padding-top: 30px;font-size: 30em;font-weight: bold;text-align: center;margin-bottom: -70px"></h1>
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

	jQuery(document).ready(function(){

		$('.visitor').marquee({
		  duration: 10000,
		  gap: 20,
		  delayBeforeStart: 0,
		  direction: 'left',
		});
		$('#tanggal').html('{{$dateTitle}}');
		$('#visitor_info').hide();
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
		var timeref = addZero(time.getHours())+':'+addZero(time.getMinutes())+':'+addZero(time.getSeconds());
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
	}

	function fillVisitor() {
		$.get('{{ url("fetch/office_clock/visitor3") }}', function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					if (result.visitors.length > 0) {
						for (var i = 0; i < result.visitors.length; i++) {
							$('#visitor_info').show();
							$('#jam').hide();
							
							if (result.visitors[i].department == null && result.visitors[i].name == 'Budhi Apriyanto') {
								document.getElementById("visitor_info").innerHTML = result.visitors[i].company+'<br>('+result.visitors[i].name.split(' ').slice(0,2).join(' ')+' - Production Engineering)<br>AT SECURITY<br>';
							}else if (result.visitors[i].department == null && result.visitors[i].name == 'Arief Soekamto') {
								document.getElementById("visitor_info").innerHTML = result.visitors[i].company+'<br>('+result.visitors[i].name.split(' ').slice(0,2).join(' ')+' - Human Resources)<br>AT SECURITY<br>';
							}else{
								document.getElementById("visitor_info").innerHTML = result.visitors[i].company+'<br>('+result.visitors[i].name.split(' ').slice(0,2).join(' ')+' - '+result.visitors[i].department+')<br>AT SECURITY<br>';
							}
							document.getElementById("visitor_info").style.fontSize = '7em';
							document.getElementById("visitor_info").style.marginBottom = '10px';
							$("#visitor_info").css("color",'#fff');
							audio_clock.play();
						}
					}
					if (result.visitors_lobby.length > 0) {
						for (var i = 0; i < result.visitors_lobby.length; i++) {
							$('#visitor_info').show();
							$('#jam').hide();

							if (result.visitors_lobby[i].department == null && result.visitors_lobby[i].name == 'Budhi Apriyanto') {
								document.getElementById("visitor_info").innerHTML = result.visitors_lobby[i].company+'<br>('+result.visitors_lobby[i].name.split(' ').slice(0,2).join(' ')+' - Production Engineering)<br>AT LOBBY<br>';
							}else if (result.visitors_lobby[i].department == null && result.visitors_lobby[i].name == 'Arief Soekamto') {
								document.getElementById("visitor_info").innerHTML = result.visitors_lobby[i].company+'<br>('+result.visitors_lobby[i].name.split(' ').slice(0,2).join(' ')+' - Human Resources)<br>AT LOBBY<br>';
							}else{
								document.getElementById("visitor_info").innerHTML = result.visitors_lobby[i].company+'<br>('+result.visitors_lobby[i].name.split(' ').slice(0,2).join(' ')+' - '+result.visitors_lobby[i].department+')<br>AT LOBBY<br>';
							}
							document.getElementById("visitor_info").style.fontSize = '7em';
							document.getElementById("visitor_info").style.marginBottom = '10px';
							// document.getElementsByClassName("content").style.backgroundColor = '#f07400';
							$(".content-wrapper").css("background-color",'rgb(255, 247, 0)','important');
							$(".content").css("background-color",'rgb(255, 247, 0)','important');
							// var x = document.querySelectorAll(".content-wrapper");
  					// 		x[0].setAttribute('style', 'background-color: #fff700 !important');
  							$("#tanggal_all").css("background-color",'rgb(255, 247, 0)');
							$("#tanggal").css("background-color",'rgb(255, 247, 0)');
							$("#tanggal").css("color",'#1100ff');
							$("#visitor_info").css("color",'#1100ff');
							$("#visitor_info").css("background-color",'rgb(255, 247, 0)');
							audio_clock_lobby.play();
						}
					}
				}else{
					$('#visitor_info').hide();
					$('#jam').show();
					$(".content-wrapper").css("background-color",'rgb(75, 30, 120)','important');
					$(".content").css("background-color",'rgb(75, 30, 120)','important');
					// $(".content-wrapper").css("background-color",'rgb(75, 30, 120)');
					$("#tanggal_all").css("background-color",'rgb(75, 30, 120)');
					$("#tanggal").css("background-color",'rgb(75, 30, 120)');
					$("#tanggal").css("color",'#fff');
				}
			}
		});
	}
</script>
@endsection