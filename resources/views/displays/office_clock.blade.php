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
		background-color: rgb(75,30,120) !important;
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
		<!-- <div class="col-xs-12" style="margin-top: 0px;"> -->
			<!-- <table style="padding-bottom: 0px;border: 0px;width: 100%">
				<tr style="padding-top: 0px;margin-top: 0px;border: 0px">
					<td style="background-color: rgb(75,30,120);color: #fff;padding-top: 0px;margin-top: 0px;">
						
					</td>
				</tr>
				<tr style="border: 1px solid black">
					<td colspan="3">
						
					</td>
				</tr>
			</table> -->
			<h1 id="jam" style="margin-top: 0px;padding-top: 30px;font-size: 30em;font-weight: bold;text-align: center;margin-bottom: -70px"></h1>
			<h1 id="visitor_info" style="margin-top: 0px;padding-top: 30px;font-size: 30em;font-weight: bold;text-align: center;margin-bottom: -70px"></h1>
			<center><span id="tanggal" style="font-size: 80px;background-color: rgb(75,30,120);color: #fff"></span></center>
		<!-- </div> -->
	</div>

	<div class="modal modal-default fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">VISITOR INFORMATION</h4>
				</div>
				<div class="modal-body">
					Are you sure delete?
				</div>
				<!-- <div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
				</div> -->
			</div>
		</div>
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

	var audio_clock = new Audio('{{ url("sounds/airport.mp3") }}');
	var myvar = setInterval(waktu,1000);

	jQuery(document).ready(function(){

		$('.visitor').marquee({
		  duration: 10000,
		  gap: 20,
		  delayBeforeStart: 0,
		  direction: 'left',
		  // duplicated: true
		});
		
		// fillChart();
		// setInterval(fillChart, 7000);
		$('#tanggal').html('{{$dateTitle}}');
		$('#visitor_info').hide();
		// fillVisitor();
		setInterval(fillVisitor,40000);

		setInterval(refresh,3600000);
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

	// window.setTimeout("waktu()", 1000);
 
	function waktu() {
		var time = new Date();
		// var myvar = setInterval("time()", 1000);
		document.getElementById("jam").style.fontSize = '30em';
		document.getElementById("jam").style.marginBottom = '-70px';
		document.getElementById("jam").innerHTML = addZero(time.getHours())+':'+addZero(time.getMinutes());
	}

	function fillVisitor() {
		$.get('{{ url("fetch/office_clock/visitor") }}', function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					// clearInterval(myvar);
					if (result.visitors.length > 0) {
						for (var i = 0; i < result.visitors.length; i++) {
							$('#visitor_info').show();
							$('#jam').hide();
							document.getElementById("visitor_info").innerHTML = result.visitors[i].company+'<br>('+result.visitors[i].name+' - '+result.visitors[i].department+')<br>';
							document.getElementById("visitor_info").style.fontSize = '7em';
							document.getElementById("visitor_info").style.marginBottom = '10px';
							// $("#myModal").modal('show');
							audio_clock.play();
							// console.clear();
						}
						// document.getElementById("visitor").innerHTML = "PT. YAMAHA MUSICAL PRODUCTS INDONESIA";
					}
				}else{
					$('#jam').show();
					$('#visitor_info').hide();
					// document.getElementById("visitor").innerHTML = "PT. YAMAHA MUSICAL PRODUCTS INDONESIA";
					// myvar = setInterval(waktu,1000);
					// $("#myModal").modal('hide');
					// console.clear();
				}
			}
		});
	}
</script>
@endsection