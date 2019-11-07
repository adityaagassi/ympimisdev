@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">
	#main tbody>tr>td {
		text-align:center;
	}

	thead>tr>th {
		background-color: white;
		text-align: center;
		/*font-size: 1vw;*/
	}

	tbody>tr>td {
		color: white;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $page }}<span class="text-purple"> {{ $jpn }}</span>
		{{-- <small>Flute <span class="text-purple"> ??? </span></small> --}}
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<!-- <div class="col-xs-12">
			<div class="row" style="margin:0px;">
					<div class="col-xs-2">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Select Date From">
						</div>
					</div>

					<div class="col-xs-2">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" id="tanggal2" name="tanggal2" placeholder="Select Date To">
						</div>
					</div>

					<div class="col-xs-1">
						<div class="form-group">
							<button class="btn btn-success" type="button" onclick="">Update Schedule</button>
						</div>
					</div>
			</div>
		</div> -->
		<div class="col-xs-12">
			<div class="col-xs-2">
				<div class="input-group date" style="margin-top: 10px;">
					<div class="input-group-addon bg-green" style="border: none;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Select Date From">
				</div>

				<div class="input-group date" style="margin-top: 10px;">
					<div class="input-group-addon bg-green" style="border: none;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="tanggal2" name="tanggal2" placeholder="Select Date To">
				</div>
			</div>
			

			<div class="col-xs-2">
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 1</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 2</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
			</div>  

			<div class="col-xs-2">
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 3</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 4</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
			</div> 

			<div class="col-xs-2">
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 5</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 6</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
			</div> 

			<div class="col-xs-2">
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 7</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 8</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
			</div> 

			<div class="col-xs-2">
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 9</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
				<div class="input-group margin">
					<div class="input-group-btn">
						<button type="button" class="btn btn-info" style="margin-right: 20px">Mesin 11</button>
					</div>
					<input type="checkbox" checked data-toggle="toggle" data-onstyle="success" data-offstyle="danger">
				</div>  
			</div> 

		</div>

		
		<div class="col-xs-12">
			<br><br>
			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main">
				<tr>
					<td width="15%" rowspan="3" style="text-align: center;
					vertical-align: middle;">Mesin 1</td	>

				</tr>
				<tr id="HeadMesin1">

				</tr>
				<tr id="BodyMesin1">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main">
				<tr>
					<td width="10%" rowspan="3" style="text-align: center;
					vertical-align: middle;">Mesin 2</td>

				</tr>
				<tr id="HeadMESIN2">

				</tr>
				<tr id="BodyMESIN2">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main">
				<tr>
					<td width="10%" rowspan="3" style="text-align: center;
					vertical-align: middle;">Mesin 3</td>

				</tr>
				<tr id="HeadMESIN3">

				</tr>
				<tr id="BodyMESIN3">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main">
				<tr>
					<td width="10%" rowspan="3" style="text-align: center;
					vertical-align: middle;">Mesin 4</td>

				</tr>
				<tr id="HeadMESIN4">

				</tr>
				<tr id="BodyMESIN4">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main">
				<tr>
					<td width="10%" rowspan="3" style="text-align: center;
					vertical-align: middle;">Mesin 5</td>

				</tr>
				<tr id="HeadMESIN5">

				</tr>
				<tr id="BodyMESIN5">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main">
				<tr>
					<td width="10%" rowspan="3" style="text-align: center;
					vertical-align: middle;">Mesin 6</td>

				</tr>
				<tr id="HeadMESIN6">

				</tr>
				<tr id="BodyMESIN6">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main">
				<tr>
					<td width="10%" rowspan="3" style="text-align: center;
					vertical-align: middle;">Mesin 7</td>

				</tr>
				<tr id="HeadMESIN7">

				</tr>
				<tr id="BodyMESIN7">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main">
				<tr>
					<td width="10%" rowspan="3" style="text-align: center;
					vertical-align: middle;">Mesin 8</td>

				</tr>
				<tr id="HeadMESIN8">

				</tr>
				<tr id="BodyMESIN8">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main">
				<tr>
					<td width="10%" rowspan="3" style="text-align: center;
					vertical-align: middle;">Mesin 9</td>

				</tr>
				<tr id="HeadMESIN9">

				</tr>
				<tr id="BodyMESIN9">


				</tr>
			</table>

			<table class="table table-bordered" style="color:white; font-size: 2vw" id="main">
				<tr>
					<td width="10%" rowspan="3" style="text-align: center;
					vertical-align: middle;">Mesin 11</td>

				</tr>
				<tr id="HeadMESIN11">

				</tr>
				<tr id="BodyMESIN11">


				</tr>
			</table>
		</div>
		
		
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
<script>
	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});
	$(function() {
    // $('#toggle-one').bootstrapToggle();
})
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		getMesin();
		makeSchedule();
	});

	function unique(list) {
    var result = [];
    $.each(list, function(i, e) {
        if ($.inArray(e, result) == -1) result.push(e);
    });
    return result;
}

				var mesin1_r2 = [[],[],[],[],[],[],[]];
				var MESIN2_r2 = [[],[],[],[],[],[],[]];
				var MESIN3_r2 = [[],[],[],[],[],[],[]];
				var MESIN4_r2 = [[],[],[],[],[],[],[]];
				var MESIN5_r2 = [[],[],[],[],[],[],[]];
				var MESIN6_r2 = [[],[],[],[],[],[],[]];
				var MESIN7_r2 = [[],[],[],[],[],[],[]];
				var MESIN8_r2 = [[],[],[],[],[],[],[]];
				var MESIN9_r2 = [[],[],[],[],[],[],[]];
				var MESIN11_r2 = [[],[],[],[],[],[],[]];

	function makeSchedule() {

		$.get('{{ url("fetch/Schedulepart") }}',  function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					var TargetAllMesin = [];
					var Mesin1detail = [];

					var MESIN1 = [];
					var MESIN2 = [];
					var MESIN3 = [];
					var MESIN4 = [];
					var MESIN5 = [];
					var MESIN6 = [];
					var MESIN7 = [];
					var MESIN8 = [];
					var MESIN9 = [];
					var MESIN11 = [];

					var MesinQty =[];
					var a = [];
					var unik = [];
					var judul = "";

					var HeadMesin1 = '';
					var BodyMesin1 = '';

					// for (var i = 0; i < result.part.length; i++) {
					// 	if (String(result.part[i].working).match(/MESIN1.*/)) {				
					// 		Mesin1.push([result.part[i].part,result.part[i].part_code,result.part[i].color,result.part[i].target_hako_qty]);		
					// 	}						
					// }

					// for (var i = 0; i < Mesin1.length; i++) {
					// 	alert(Mesin1[i][0])
					// }

					// for (var i = 0; i < result.part.length; i++) {
						
					// 	if (String(result.part[i].working).match(/MESIN1.*/)) {

					// 	for (var qty = result.part[0].target_hako_qty; qty > result.part[0].max_day; qty--) {
					// 	qty -= result.part[0].max_day;
					// 	Mesin1.push([result.part[0].part,result.part[0].part_code,result.part[0].color,result.part[0].target_hako_qty, qty]);
					// 	}		

					// 	}						
					// }
					// var qty = result.part[0].target_hako_qty;
					// var qty = 0;
					// var max = result.part[0].max_day;

					// for (var i = 0; i < 10; i++) {						
					// 	qty += parseInt(result.part[0].max_day);
					// 	if (qty >= result.part[0].target_hako_qty) {
					// 		qty = qty - result.part[0].target_hako_qty
					// 	}
					// 	Mesin1.push(qty, max);
					// 	// if (qty > 10 ) {
					// 	// 	break;
					// 	// }	
					// }

					

						// var d = result.part[0].target_hako_qty;

						// while (d - result.part[0].max_day >= result.part[0].max_day) {
						// 	alert(d + "-"+result.part[0].max_day)

					 // 	 Mesin1.push([result.part[0].part,result.part[0].part_code,result.part[0].color,result.part[0].target_hako_qty, d]);
					 // 	 d-=result.part[0].max_day;
						// }

						$.each(result.part, function(key, value) {
							var qty = value.target_hako_qty;

							if (value.part==value.part) {

								while(qty >= value.max_day){						
									TargetAllMesin.push([value.part_code,value.color,value.part,parseInt(value.max_day),value.working,value.max_day,value.cycle,value.shoot]);
									if (qty > value.max_day) {
										qty-=value.max_day;
									}else{
										qty=qty;
									}

								}
								TargetAllMesin.push([value.part_code,value.color,value.part,qty,value.working,value.max_day,value.cycle,value.shoot]);
							}
						});


						for (var i = 0; i < TargetAllMesin.length; i++) {
							a = TargetAllMesin[i][4];
							var m = "";

							if (a.match(/,.*/) ) {
								m = a.split(',');

								for (var y = 0; y < m.length; y++) {
									eval(m[y]).push([TargetAllMesin[i][0],TargetAllMesin[i][1],TargetAllMesin[i][2],TargetAllMesin[i][3],TargetAllMesin[i][5],TargetAllMesin[i][6],TargetAllMesin[i][7],(((TargetAllMesin[i][3] / TargetAllMesin[i][7]) * TargetAllMesin[i][6]) / 60)]);
								}

						// alert(m[0])
					}else{

						eval(a).push([TargetAllMesin[i][0],TargetAllMesin[i][1],TargetAllMesin[i][2],TargetAllMesin[i][3],TargetAllMesin[i][5],TargetAllMesin[i][6],TargetAllMesin[i][7],(((TargetAllMesin[i][3] / TargetAllMesin[i][7]) * TargetAllMesin[i][6]) / 60)]);
					}
				}

				
				// BodyMesin1 += '<td style="padding: 0px">';
				// BodyMesin1 +='<table border="1" width="100%" >';
				// for (var i = 0; i < MESIN1.length; i++) {
				// 	if (MESIN1[i][3] != MESIN1[i][4]) {	

				// 		BodyMesin1 +='<tr>';
				// 		BodyMesin1 +='<td>'+MESIN1[i][0] +' '+MESIN1[i][1]+'</td>';
				// 		BodyMesin1 +='<td>'+MESIN1[i][2]+'</td>';
				// 		BodyMesin1 +='<td>'+((MESIN1[i][3] / MESIN1[i][6]) * MESIN1[i][5]) / 60+'</td>';
				// 		BodyMesin1 +='</tr>';

				// 	}					
				// }
				// BodyMesin1 +='</table>';
				// BodyMesin1 +='</td>';

				// for (var i = 0; i < MESIN1.length; i++) {
				// 	if (MESIN1[i][3] == MESIN1[i][4]){
				// 		BodyMesin1 += '<td style="padding: 0px">';
				// 		BodyMesin1 +='<table border="1" width="100%" >';	
				// 		BodyMesin1 +='<tr>';
				// 		BodyMesin1 +='<td>'+MESIN1[i][0] +' '+MESIN1[i][1]+'</td>';
				// 		BodyMesin1 +='<td>'+MESIN1[i][2]+'</td>';
				// 		BodyMesin1 +='<td>'+((MESIN1[i][3] / MESIN1[i][6]) * MESIN1[i][5]) / 60	+'</td>';
				// 		BodyMesin1 +='</tr>';
				// 		BodyMesin1 +='</table>';
				// 		BodyMesin1 +='</td>';
				// 	}
				// }

				var x = 0;
				// var mesin1_r = [[],[],[],[],[],[],[]];
				
				var z = 0;
				var j = 0;
				var c = 0;
				var d = 0;
				var lp = 0;

				// ---------------- KEEP ------------------

				// for (var i = 0; i < MESIN1.length; i++) {
				// 	if (typeof MESIN1[i+1] === 'undefined') {
				// 		mesin1_r[x].push([MESIN1[i][2], MESIN1[i][3]]);
				// 	} else {
				// 		if (((MESIN1[i][7] + MESIN1[i+1][7]) / 60) > 22.9) {
				// 			mesin1_r[x].push([MESIN1[i][2], MESIN1[i][3]]);

				// 			x+=1;
				// 		} else {

				// 			z += MESIN1[i][7];


				// 			if (((z + MESIN1[i+2][7]) / 60) > 22.9) {
				// 				var d = Math.floor((((22.9 - ((MESIN1[i][7] + MESIN1[i+1][7]) / 60)).toFixed(1) * 60)*60)/ MESIN1[i+1][5]) * MESIN1[i+1][6];
				// 				console.log(d);
				// 				MESIN1[i+2][3] -= d;
				// 				mesin1_r[x].push([MESIN1[i][2], MESIN1[i][3]]);

				// 			}else{
				// 				mesin1_r[x].push([MESIN1[i+2][2], d]);
				// 			}
				// 		}
				// 	}
				// }

				// console.log(mesin1_r);

				// ---------------- END KEEP ------------------

				// ---------------- Mesin1 KEEP ------------------
				// x=0;
				// c=0;
				// for (var i = 0; i < MESIN1.length; i++) {
				// 	if (typeof MESIN1[i+1] === 'undefined') {
				// 		mesin1_r2[x].push([(MESIN1[i][0]+' '+MESIN1[i][1]),MESIN1[i][2], MESIN1[i][3]]);

				// 	} else {
				// 		if (c == 0) {
				// 			j = MESIN1[i][7];
				// 		}
				// 		if (((j + MESIN1[i+1][7]) / 60) > 22.9) {
				// 			mesin1_r2[x].push([(MESIN1[i][0]+' '+MESIN1[i][1]),MESIN1[i][2], MESIN1[i][3]]);
				// 			if (c == 1) {
				// 				d = Math.floor((((22.9 - (j / 60)).toFixed(1) * 60)*60)/ MESIN1[i+1][5]) * MESIN1[i+1][6];
				// 				mesin1_r2[x].push([(MESIN1[i][0]+' '+MESIN1[i][1]),MESIN1[i][2], d]);
				// 				MESIN1[i+1][3] -= d;
				// 				MESIN1[i+1][7] = Math.floor((Math.floor(MESIN1[i+1][3] / MESIN1[i+1][6]) * MESIN1[i+1][5]) / 60);
				// 			} else {
				// 				if (MESIN1[i][3] != MESIN1[i][4]) {
									
				// 					// ------minus


				// 					if ((Math.floor(((((22.9 - (MESIN1[i][7] / 60)).toFixed(1))*60)*60) / MESIN1[i+1][5])* MESIN1[i+1][6]) < 0) {

				// 						mesin1_r2[x].push([(MESIN1[i][0]+' '+MESIN1[i][1]),MESIN1[i+1][2], 
				// 						(Math.floor(((((22.9 - (MESIN1[i][7] / 60)).toFixed(1))*60)*60) / MESIN1[i+1][5])* MESIN1[i+1][6])
				// 						]);

				// 						MESIN1[i+1][3] -= 0;
				// 					}else{

				// 						mesin1_r2[x].push([(MESIN1[i][0]+' '+MESIN1[i][1]),MESIN1[i+1][2], 
				// 						(Math.floor(((((22.9 - (MESIN1[i][7] / 60)).toFixed(1))*60)*60) / MESIN1[i+1][5])* MESIN1[i+1][6])
				// 						]);

										
				// 					MESIN1[i+1][3] -= (Math.floor(((((22.9 - (MESIN1[i][7] / 60)).toFixed(1))*60)*60) / MESIN1[i+1][5])* MESIN1[i+1][6]);
				// 				}

				// 				// ------ end minus

				// 				}

				// 				j = 0;
				// 			}

				// 			c = 0;
				// 			x+=1;
				// 		} else {
				// 			j = MESIN1[i][7] + MESIN1[i+1][7];
				// 			// d = MESIN1[i][3] + MESIN1[i+1][3];
				// 			mesin1_r2[x].push([(MESIN1[i][0]+' '+MESIN1[i][1]),MESIN1[i][2], MESIN1[i][3]]);
				// 			c = 1;
				// 		}
				// 	}

				// }
				// console.log(mesin1_r2);

				// ---------------- End Mesin1 KEEP ------------------

				
				// ---------------- Mesin11 KEEP ------------------
				// x=0;
				// c=0;
				// for (var i = 0; i < MESIN11.length; i++) {
				// 	if (typeof MESIN11[i+1] === 'undefined') {
				// 		MESIN11_r2[x].push([MESIN11[i][2], MESIN11[i][3]]);

				// 	} else {
				// 		if (c == 0) {
				// 			j = MESIN11[i][7];
				// 		}
				// 		if (((j + MESIN11[i+1][7]) / 60) > 22.9) {
				// 			MESIN11_r2[x].push([MESIN11[i][2], MESIN11[i][3]]);
				// 			if (c == 1) {
				// 				d = Math.floor((((22.9 - (j / 60)).toFixed(1) * 60)*60)/ MESIN11[i+1][5]) * MESIN11[i+1][6];
				// 				MESIN11_r2[x].push([MESIN11[i+1][2], d]);
				// 				MESIN11[i+1][3] -= d;
				// 				MESIN11[i+1][7] = Math.floor((Math.floor(MESIN11[i+1][3] / MESIN11[i+1][6]) * MESIN11[i+1][5]) / 60);
				// 			} else {

				// 				// ---------------- INI PENYEBABNYA -------------------
				// 				if (MESIN11[i][3] != MESIN11[i][4]) {
									
				// 					if ((Math.floor(((((22.9 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6]) < 0) {

				// 						MESIN11_r2[x].push([MESIN11[i+1][2], 
				// 						(Math.floor(((((22.9 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6])
				// 						]);

				// 						MESIN11[i+1][3] -= 0;
				// 					}else{

				// 						MESIN11_r2[x].push([MESIN11[i+1][2], 
				// 						(Math.floor(((((22.9 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6])
				// 						]);


				// 					MESIN11[i+1][3] -= (Math.floor(((((22.9 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6]);
				// 				}

				// 				}

				// 				j = 0;
				// 			}

				// 			c = 0;
				// 			x+=1;
				// 		} else {
				// 			j = MESIN11[i][7] + MESIN11[i+1][7];
				// 			console.log(j);
				// 			// d = MESIN11[i][3] + MESIN11[i+1][3];
				// 			MESIN11_r2[x].push([MESIN11[i][2], MESIN11[i][3]]);
				// 			c = 1;
				// 		}
				// 	}

				// }

				// ---------------- End Mesin11 KEEP ------------------

				// console.log(MESIN11_r2);
				
				// x=0;
				// for (var i = 0; i < MESIN11.length; i++) {
				// 	if (typeof MESIN11[i+1] === 'undefined') {
				// 		MESIN11_r2[x].push([MESIN11[i][2], MESIN11[i][3]]);

				// 	} else {
				// 		if (c == 0) {
				// 			j = MESIN11[i][7];
				// 		}

				// 		if ((j  / 60) > 22.9) {
				// 			lp++;
				// 			for (var lpp = 0; lpp < lp; lpp++) {
				// 			 alert(	MESIN11[i+lpp][2]);
				// 			}

				// 		}
				// 		if (((j + MESIN11[i+1][7]) / 60) > 22.9) {
				// 			MESIN11_r2[x].push([MESIN11[i][2], MESIN11[i][3]]);
				// 			if (c == 1) {
				// 				d = Math.floor((((22.9 - (j / 60)).toFixed(1) * 60)*60)/ MESIN11[i+1][5]) * MESIN11[i+1][6];
				// 				MESIN11_r2[x].push([MESIN11[i+1][2], d]);
				// 				MESIN11[i+1][3] -= d;
				// 				MESIN11[i+1][7] = Math.floor((Math.floor(MESIN11[i+1][3] / MESIN11[i+1][6]) * MESIN11[i+1][5]) / 60);
				// 			} else {

				// 				// ---------------- INI PENYEBABNYA -------------------
								

				// 				j = 0;
				// 			}

				// 			c = 0;
				// 			x+=1;
				// 		} else {
				// 			j = MESIN11[i][7] + MESIN11[i+1][7];
				// 			console.log(j);
				// 			// d = MESIN11[i][3] + MESIN11[i+1][3];
				// 			MESIN11_r2[x].push([MESIN11[i][2], MESIN11[i][3]]);
				// 			c = 1;
				// 		}
				// 	}

				// }


				// ---------------- Mesin1  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN1.length; i++) {
					if (typeof MESIN1[i+1] === 'undefined') {
						mesin1_r2[x].push([(MESIN1[i][0]+' '+MESIN1[i][1]),MESIN1[i][2], MESIN1[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN1[i][7];
						}
						if (((j + MESIN1[i+1][7]) / 60) > 22.9) {
							mesin1_r2[x].push([(MESIN1[i][0]+' '+MESIN1[i][1]),MESIN1[i][2], MESIN1[i][3]]);
							if (c == 1) {
								d = Math.floor((((22.9 - (j / 60)).toFixed(1) * 60)*60)/ MESIN1[i+1][5]) * MESIN1[i+1][6];
								mesin1_r2[x].push([(MESIN1[i][0]+' '+MESIN1[i][1]),MESIN1[i][2], d]);
								MESIN1[i+1][3] -= d;
								MESIN1[i+1][7] = Math.floor((Math.floor(MESIN1[i+1][3] / MESIN1[i+1][6]) * MESIN1[i+1][5]) / 60);
							} else {
								if (MESIN1[i][3] != MESIN1[i][4]) {
									
									// ------minus


									if ((Math.floor(((((22.9 - (MESIN1[i][7] / 60)).toFixed(1))*60)*60) / MESIN1[i+1][5])* MESIN1[i+1][6]) < 0) {

										mesin1_r2[x].push([(MESIN1[i][0]+' '+MESIN1[i][1]),MESIN1[i+1][2], 
										(Math.floor(((((22.9 - (MESIN1[i][7] / 60)).toFixed(1))*60)*60) / MESIN1[i+1][5])* MESIN1[i+1][6])
										]);

										MESIN1[i+1][3] -= 0;
									}else{

										mesin1_r2[x].push([(MESIN1[i][0]+' '+MESIN1[i][1]),MESIN1[i+1][2], 
										(Math.floor(((((22.9 - (MESIN1[i][7] / 60)).toFixed(1))*60)*60) / MESIN1[i+1][5])* MESIN1[i+1][6])
										]);

										
									MESIN1[i+1][3] -= (Math.floor(((((22.9 - (MESIN1[i][7] / 60)).toFixed(1))*60)*60) / MESIN1[i+1][5])* MESIN1[i+1][6]);
								}

								// ------ end minus

								}

								j = 0;
							}

							c = 0;
							x+=1;
						} else {
							j = MESIN1[i][7] + MESIN1[i+1][7];
							// d = MESIN1[i][3] + MESIN1[i+1][3];
							mesin1_r2[x].push([(MESIN1[i][0]+' '+MESIN1[i][1]),MESIN1[i][2], MESIN1[i][3]]);
							c = 1;
						}
					}

				}
				console.log(mesin1_r2);

				// ---------------- End Mesin1  ------------------

				// ---------------- MESIN2  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN2.length; i++) {
					if (typeof MESIN2[i+1] === 'undefined') {
						MESIN2_r2[x].push([(MESIN2[i][0]+' '+MESIN2[i][1]),MESIN2[i][2], MESIN2[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN2[i][7];
						}
						if (((j + MESIN2[i+1][7]) / 60) > 22.9) {
							MESIN2_r2[x].push([(MESIN2[i][0]+' '+MESIN2[i][1]),MESIN2[i][2], MESIN2[i][3]]);
							if (c == 1) {
								d = Math.floor((((22.9 - (j / 60)).toFixed(1) * 60)*60)/ MESIN2[i+1][5]) * MESIN2[i+1][6];
								MESIN2_r2[x].push([(MESIN2[i][0]+' '+MESIN2[i][1]),MESIN2[i][2], d]);
								MESIN2[i+1][3] -= d;
								MESIN2[i+1][7] = Math.floor((Math.floor(MESIN2[i+1][3] / MESIN2[i+1][6]) * MESIN2[i+1][5]) / 60);
							} else {
								if (MESIN2[i][3] != MESIN2[i][4]) {
									
									// ------minus


									if ((Math.floor(((((22.9 - (MESIN2[i][7] / 60)).toFixed(1))*60)*60) / MESIN2[i+1][5])* MESIN2[i+1][6]) < 0) {

										MESIN2_r2[x].push([(MESIN2[i][0]+' '+MESIN2[i][1]),MESIN2[i+1][2], 
										(Math.floor(((((22.9 - (MESIN2[i][7] / 60)).toFixed(1))*60)*60) / MESIN2[i+1][5])* MESIN2[i+1][6])
										]);

										MESIN2[i+1][3] -= 0;
									}else{

										MESIN2_r2[x].push([(MESIN2[i][0]+' '+MESIN2[i][1]),MESIN2[i+1][2], 
										(Math.floor(((((22.9 - (MESIN2[i][7] / 60)).toFixed(1))*60)*60) / MESIN2[i+1][5])* MESIN2[i+1][6])
										]);

										
									MESIN2[i+1][3] -= (Math.floor(((((22.9 - (MESIN2[i][7] / 60)).toFixed(1))*60)*60) / MESIN2[i+1][5])* MESIN2[i+1][6]);
								}

								// ------ end minus

								}

								j = 0;
							}

							c = 0;
							x+=1;
						} else {
							j = MESIN2[i][7] + MESIN2[i+1][7];
							// d = MESIN2[i][3] + MESIN2[i+1][3];
							MESIN2_r2[x].push([(MESIN2[i][0]+' '+MESIN2[i][1]),MESIN2[i][2], MESIN2[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN2_r2);

				// ---------------- End MESIN2  ------------------

				// ---------------- MESIN3  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN3.length; i++) {
					if (typeof MESIN3[i+1] === 'undefined') {
						MESIN3_r2[x].push([(MESIN3[i][0]+' '+MESIN3[i][1]),MESIN3[i][2], MESIN3[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN3[i][7];
						}
						if (((j + MESIN3[i+1][7]) / 60) > 22.9) {
							MESIN3_r2[x].push([(MESIN3[i][0]+' '+MESIN3[i][1]),MESIN3[i][2], MESIN3[i][3]]);
							if (c == 1) {
								d = Math.floor((((22.9 - (j / 60)).toFixed(1) * 60)*60)/ MESIN3[i+1][5]) * MESIN3[i+1][6];
								MESIN3_r2[x].push([(MESIN3[i][0]+' '+MESIN3[i][1]),MESIN3[i][2], d]);
								MESIN3[i+1][3] -= d;
								MESIN3[i+1][7] = Math.floor((Math.floor(MESIN3[i+1][3] / MESIN3[i+1][6]) * MESIN3[i+1][5]) / 60);
							} else {
								if (MESIN3[i][3] != MESIN3[i][4]) {
									
									// ------minus


									if ((Math.floor(((((22.9 - (MESIN3[i][7] / 60)).toFixed(1))*60)*60) / MESIN3[i+1][5])* MESIN3[i+1][6]) < 0) {

										MESIN3_r2[x].push([(MESIN3[i][0]+' '+MESIN3[i][1]),MESIN3[i+1][2], 
										(Math.floor(((((22.9 - (MESIN3[i][7] / 60)).toFixed(1))*60)*60) / MESIN3[i+1][5])* MESIN3[i+1][6])
										]);

										MESIN3[i+1][3] -= 0;
									}else{

										MESIN3_r2[x].push([(MESIN3[i][0]+' '+MESIN3[i][1]),MESIN3[i+1][2], 
										(Math.floor(((((22.9 - (MESIN3[i][7] / 60)).toFixed(1))*60)*60) / MESIN3[i+1][5])* MESIN3[i+1][6])
										]);

										
									MESIN3[i+1][3] -= (Math.floor(((((22.9 - (MESIN3[i][7] / 60)).toFixed(1))*60)*60) / MESIN3[i+1][5])* MESIN3[i+1][6]);
								}

								// ------ end minus

								}

								j = 0;
							}

							c = 0;
							x+=1;
						} else {
							j = MESIN3[i][7] + MESIN3[i+1][7];
							// d = MESIN3[i][3] + MESIN3[i+1][3];
							MESIN3_r2[x].push([(MESIN3[i][0]+' '+MESIN3[i][1]),MESIN3[i][2], MESIN3[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN3_r2);

				// ---------------- End MESIN3  ------------------

				// ---------------- MESIN4  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN4.length; i++) {
					if (typeof MESIN4[i+1] === 'undefined') {
						MESIN4_r2[x].push([(MESIN4[i][0]+' '+MESIN4[i][1]),MESIN4[i][2], MESIN4[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN4[i][7];
						}
						if (((j + MESIN4[i+1][7]) / 60) > 22.9) {
							MESIN4_r2[x].push([(MESIN4[i][0]+' '+MESIN4[i][1]),MESIN4[i][2], MESIN4[i][3]]);
							if (c == 1) {
								d = Math.floor((((22.9 - (j / 60)).toFixed(1) * 60)*60)/ MESIN4[i+1][5]) * MESIN4[i+1][6];
								MESIN4_r2[x].push([(MESIN4[i][0]+' '+MESIN4[i][1]),MESIN4[i][2], d]);
								MESIN4[i+1][3] -= d;
								MESIN4[i+1][7] = Math.floor((Math.floor(MESIN4[i+1][3] / MESIN4[i+1][6]) * MESIN4[i+1][5]) / 60);
							} else {
								if (MESIN4[i][3] != MESIN4[i][4]) {
									
									// ------minus


									if ((Math.floor(((((22.9 - (MESIN4[i][7] / 60)).toFixed(1))*60)*60) / MESIN4[i+1][5])* MESIN4[i+1][6]) < 0) {

										MESIN4_r2[x].push([(MESIN4[i][0]+' '+MESIN4[i][1]),MESIN4[i+1][2], 
										(Math.floor(((((22.9 - (MESIN4[i][7] / 60)).toFixed(1))*60)*60) / MESIN4[i+1][5])* MESIN4[i+1][6])
										]);

										MESIN4[i+1][3] -= 0;
									}else{

										MESIN4_r2[x].push([(MESIN4[i][0]+' '+MESIN4[i][1]),MESIN4[i+1][2], 
										(Math.floor(((((22.9 - (MESIN4[i][7] / 60)).toFixed(1))*60)*60) / MESIN4[i+1][5])* MESIN4[i+1][6])
										]);

										
									MESIN4[i+1][3] -= (Math.floor(((((22.9 - (MESIN4[i][7] / 60)).toFixed(1))*60)*60) / MESIN4[i+1][5])* MESIN4[i+1][6]);
								}

								// ------ end minus

								}

								j = 0;
							}

							c = 0;
							x+=1;
						} else {
							j = MESIN4[i][7] + MESIN4[i+1][7];
							// d = MESIN4[i][3] + MESIN4[i+1][3];
							MESIN4_r2[x].push([(MESIN4[i][0]+' '+MESIN4[i][1]),MESIN4[i][2], MESIN4[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN4_r2);

				// ---------------- End MESIN4  ------------------

				// ---------------- MESIN5  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN5.length; i++) {
					if (typeof MESIN5[i+1] === 'undefined') {
						MESIN5_r2[x].push([(MESIN5[i][0]+' '+MESIN5[i][1]),MESIN5[i][2], MESIN5[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN5[i][7];
						}
						if (((j + MESIN5[i+1][7]) / 60) > 22.9) {
							MESIN5_r2[x].push([(MESIN5[i][0]+' '+MESIN5[i][1]),MESIN5[i][2], MESIN5[i][3]]);
							if (c == 1) {
								d = Math.floor((((22.9 - (j / 60)).toFixed(1) * 60)*60)/ MESIN5[i+1][5]) * MESIN5[i+1][6];
								MESIN5_r2[x].push([(MESIN5[i][0]+' '+MESIN5[i][1]),MESIN5[i][2], d]);
								MESIN5[i+1][3] -= d;
								MESIN5[i+1][7] = Math.floor((Math.floor(MESIN5[i+1][3] / MESIN5[i+1][6]) * MESIN5[i+1][5]) / 60);
							} else {
								if (MESIN5[i][3] != MESIN5[i][4]) {
									
									// ------minus


									if ((Math.floor(((((22.9 - (MESIN5[i][7] / 60)).toFixed(1))*60)*60) / MESIN5[i+1][5])* MESIN5[i+1][6]) < 0) {

										MESIN5_r2[x].push([(MESIN5[i][0]+' '+MESIN5[i][1]),MESIN5[i+1][2], 
										(Math.floor(((((22.9 - (MESIN5[i][7] / 60)).toFixed(1))*60)*60) / MESIN5[i+1][5])* MESIN5[i+1][6])
										]);

										MESIN5[i+1][3] -= 0;
									}else{

										MESIN5_r2[x].push([(MESIN5[i][0]+' '+MESIN5[i][1]),MESIN5[i+1][2], 
										(Math.floor(((((22.9 - (MESIN5[i][7] / 60)).toFixed(1))*60)*60) / MESIN5[i+1][5])* MESIN5[i+1][6])
										]);

										
									MESIN5[i+1][3] -= (Math.floor(((((22.9 - (MESIN5[i][7] / 60)).toFixed(1))*60)*60) / MESIN5[i+1][5])* MESIN5[i+1][6]);
								}

								// ------ end minus

								}

								j = 0;
							}

							c = 0;
							x+=1;
						} else {
							j = MESIN5[i][7] + MESIN5[i+1][7];
							// d = MESIN5[i][3] + MESIN5[i+1][3];
							MESIN5_r2[x].push([(MESIN5[i][0]+' '+MESIN5[i][1]),MESIN5[i][2], MESIN5[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN5_r2);

				// ---------------- End MESIN5  ------------------

				// ---------------- MESIN6  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN6.length; i++) {
					if (typeof MESIN6[i+1] === 'undefined') {
						MESIN6_r2[x].push([(MESIN6[i][0]+' '+MESIN6[i][1]),MESIN6[i][2], MESIN6[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN6[i][7];
						}
						if (((j + MESIN6[i+1][7]) / 60) > 22.9) {
							MESIN6_r2[x].push([(MESIN6[i][0]+' '+MESIN6[i][1]),MESIN6[i][2], MESIN6[i][3]]);
							if (c == 1) {
								d = Math.floor((((22.9 - (j / 60)).toFixed(1) * 60)*60)/ MESIN6[i+1][5]) * MESIN6[i+1][6];
								MESIN6_r2[x].push([(MESIN6[i][0]+' '+MESIN6[i][1]),MESIN6[i][2], d]);
								MESIN6[i+1][3] -= d;
								MESIN6[i+1][7] = Math.floor((Math.floor(MESIN6[i+1][3] / MESIN6[i+1][6]) * MESIN6[i+1][5]) / 60);
							} else {
								if (MESIN6[i][3] != MESIN6[i][4]) {
									
									// ------minus


									if ((Math.floor(((((22.9 - (MESIN6[i][7] / 60)).toFixed(1))*60)*60) / MESIN6[i+1][5])* MESIN6[i+1][6]) < 0) {

										MESIN6_r2[x].push([(MESIN6[i][0]+' '+MESIN6[i][1]),MESIN6[i+1][2], 
										(Math.floor(((((22.9 - (MESIN6[i][7] / 60)).toFixed(1))*60)*60) / MESIN6[i+1][5])* MESIN6[i+1][6])
										]);

										MESIN6[i+1][3] -= 0;
									}else{

										MESIN6_r2[x].push([(MESIN6[i][0]+' '+MESIN6[i][1]),MESIN6[i+1][2], 
										(Math.floor(((((22.9 - (MESIN6[i][7] / 60)).toFixed(1))*60)*60) / MESIN6[i+1][5])* MESIN6[i+1][6])
										]);

										
									MESIN6[i+1][3] -= (Math.floor(((((22.9 - (MESIN6[i][7] / 60)).toFixed(1))*60)*60) / MESIN6[i+1][5])* MESIN6[i+1][6]);
								}

								// ------ end minus

								}

								j = 0;
							}

							c = 0;
							x+=1;
						} else {
							j = MESIN6[i][7] + MESIN6[i+1][7];
							// d = MESIN6[i][3] + MESIN6[i+1][3];
							MESIN6_r2[x].push([(MESIN6[i][0]+' '+MESIN6[i][1]),MESIN6[i][2], MESIN6[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN6_r2);

				// ---------------- End MESIN6  ------------------

				// ---------------- MESIN7  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN7.length; i++) {
					if (typeof MESIN7[i+1] === 'undefined') {
						MESIN7_r2[x].push([(MESIN7[i][0]+' '+MESIN7[i][1]),MESIN7[i][2], MESIN7[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN7[i][7];
						}
						if (((j + MESIN7[i+1][7]) / 60) > 22.9) {
							MESIN7_r2[x].push([(MESIN7[i][0]+' '+MESIN7[i][1]),MESIN7[i][2], MESIN7[i][3]]);
							if (c == 1) {
								d = Math.floor((((22.9 - (j / 60)).toFixed(1) * 60)*60)/ MESIN7[i+1][5]) * MESIN7[i+1][6];
								MESIN7_r2[x].push([(MESIN7[i][0]+' '+MESIN7[i][1]),MESIN7[i][2], d]);
								MESIN7[i+1][3] -= d;
								MESIN7[i+1][7] = Math.floor((Math.floor(MESIN7[i+1][3] / MESIN7[i+1][6]) * MESIN7[i+1][5]) / 60);
							} else {
								if (MESIN7[i][3] != MESIN7[i][4]) {
									
									// ------minus


									if ((Math.floor(((((22.9 - (MESIN7[i][7] / 60)).toFixed(1))*60)*60) / MESIN7[i+1][5])* MESIN7[i+1][6]) < 0) {

										MESIN7_r2[x].push([(MESIN7[i][0]+' '+MESIN7[i][1]),MESIN7[i+1][2], 
										(Math.floor(((((22.9 - (MESIN7[i][7] / 60)).toFixed(1))*60)*60) / MESIN7[i+1][5])* MESIN7[i+1][6])
										]);

										MESIN7[i+1][3] -= 0;
									}else{

										MESIN7_r2[x].push([(MESIN7[i][0]+' '+MESIN7[i][1]),MESIN7[i+1][2], 
										(Math.floor(((((22.9 - (MESIN7[i][7] / 60)).toFixed(1))*60)*60) / MESIN7[i+1][5])* MESIN7[i+1][6])
										]);

										
									MESIN7[i+1][3] -= (Math.floor(((((22.9 - (MESIN7[i][7] / 60)).toFixed(1))*60)*60) / MESIN7[i+1][5])* MESIN7[i+1][6]);
								}

								// ------ end minus

								}

								j = 0;
							}

							c = 0;
							x+=1;
						} else {
							j = MESIN7[i][7] + MESIN7[i+1][7];
							// d = MESIN7[i][3] + MESIN7[i+1][3];
							MESIN7_r2[x].push([(MESIN7[i][0]+' '+MESIN7[i][1]),MESIN7[i][2], MESIN7[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN7_r2);

				// ---------------- End MESIN7  ------------------

				// ---------------- MESIN8  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN8.length; i++) {
					if (typeof MESIN8[i+1] === 'undefined') {
						MESIN8_r2[x].push([(MESIN8[i][0]+' '+MESIN8[i][1]),MESIN8[i][2], MESIN8[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN8[i][7];
						}
						if (((j + MESIN8[i+1][7]) / 60) > 22.9) {
							MESIN8_r2[x].push([(MESIN8[i][0]+' '+MESIN8[i][1]),MESIN8[i][2], MESIN8[i][3]]);
							if (c == 1) {
								d = Math.floor((((22.9 - (j / 60)).toFixed(1) * 60)*60)/ MESIN8[i+1][5]) * MESIN8[i+1][6];
								MESIN8_r2[x].push([(MESIN8[i][0]+' '+MESIN8[i][1]),MESIN8[i][2], d]);
								MESIN8[i+1][3] -= d;
								MESIN8[i+1][7] = Math.floor((Math.floor(MESIN8[i+1][3] / MESIN8[i+1][6]) * MESIN8[i+1][5]) / 60);
							} else {
								if (MESIN8[i][3] != MESIN8[i][4]) {
									
									// ------minus


									if ((Math.floor(((((22.9 - (MESIN8[i][7] / 60)).toFixed(1))*60)*60) / MESIN8[i+1][5])* MESIN8[i+1][6]) < 0) {

										MESIN8_r2[x].push([(MESIN8[i][0]+' '+MESIN8[i][1]),MESIN8[i+1][2], 
										(Math.floor(((((22.9 - (MESIN8[i][7] / 60)).toFixed(1))*60)*60) / MESIN8[i+1][5])* MESIN8[i+1][6])
										]);

										MESIN8[i+1][3] -= 0;
									}else{

										MESIN8_r2[x].push([(MESIN8[i][0]+' '+MESIN8[i][1]),MESIN8[i+1][2], 
										(Math.floor(((((22.9 - (MESIN8[i][7] / 60)).toFixed(1))*60)*60) / MESIN8[i+1][5])* MESIN8[i+1][6])
										]);

										
									MESIN8[i+1][3] -= (Math.floor(((((22.9 - (MESIN8[i][7] / 60)).toFixed(1))*60)*60) / MESIN8[i+1][5])* MESIN8[i+1][6]);
								}

								// ------ end minus

								}

								j = 0;
							}

							c = 0;
							x+=1;
						} else {
							j = MESIN8[i][7] + MESIN8[i+1][7];
							// d = MESIN8[i][3] + MESIN8[i+1][3];
							MESIN8_r2[x].push([(MESIN8[i][0]+' '+MESIN8[i][1]),MESIN8[i][2], MESIN8[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN8_r2);

				// ---------------- End MESIN8  ------------------

				// ---------------- MESIN9  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN9.length; i++) {
					if (typeof MESIN9[i+1] === 'undefined') {
						MESIN9_r2[x].push([(MESIN9[i][0]+' '+MESIN9[i][1]),MESIN9[i][2], MESIN9[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN9[i][7];
						}
						if (((j + MESIN9[i+1][7]) / 60) > 22.9) {
							MESIN9_r2[x].push([(MESIN9[i][0]+' '+MESIN9[i][1]),MESIN9[i][2], MESIN9[i][3]]);
							if (c == 1) {
								d = Math.floor((((22.9 - (j / 60)).toFixed(1) * 60)*60)/ MESIN9[i+1][5]) * MESIN9[i+1][6];
								MESIN9_r2[x].push([(MESIN9[i][0]+' '+MESIN9[i][1]),MESIN9[i][2], d]);
								MESIN9[i+1][3] -= d;
								MESIN9[i+1][7] = Math.floor((Math.floor(MESIN9[i+1][3] / MESIN9[i+1][6]) * MESIN9[i+1][5]) / 60);
							} else {
								if (MESIN9[i][3] != MESIN9[i][4]) {
									
									// ------minus


									if ((Math.floor(((((22.9 - (MESIN9[i][7] / 60)).toFixed(1))*60)*60) / MESIN9[i+1][5])* MESIN9[i+1][6]) < 0) {

										MESIN9_r2[x].push([(MESIN9[i][0]+' '+MESIN9[i][1]),MESIN9[i+1][2], 
										(Math.floor(((((22.9 - (MESIN9[i][7] / 60)).toFixed(1))*60)*60) / MESIN9[i+1][5])* MESIN9[i+1][6])
										]);

										MESIN9[i+1][3] -= 0;
									}else{

										MESIN9_r2[x].push([(MESIN9[i][0]+' '+MESIN9[i][1]),MESIN9[i+1][2], 
										(Math.floor(((((22.9 - (MESIN9[i][7] / 60)).toFixed(1))*60)*60) / MESIN9[i+1][5])* MESIN9[i+1][6])
										]);

										
									MESIN9[i+1][3] -= (Math.floor(((((22.9 - (MESIN9[i][7] / 60)).toFixed(1))*60)*60) / MESIN9[i+1][5])* MESIN9[i+1][6]);
								}

								// ------ end minus

								}

								j = 0;
							}

							c = 0;
							x+=1;
						} else {
							j = MESIN9[i][7] + MESIN9[i+1][7];
							// d = MESIN9[i][3] + MESIN9[i+1][3];
							MESIN9_r2[x].push([(MESIN9[i][0]+' '+MESIN9[i][1]),MESIN9[i][2], MESIN9[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN9_r2);

				// ---------------- End MESIN9  ------------------

				// ---------------- MESIN11  ------------------
				x=0;
				c=0;
				for (var i = 0; i < MESIN11.length; i++) {
					if (typeof MESIN11[i+1] === 'undefined') {
						MESIN11_r2[x].push([(MESIN11[i][0]+' '+MESIN11[i][1]),MESIN11[i][2], MESIN11[i][3]]);

					} else {
						if (c == 0) {
							j = MESIN11[i][7];
						}
						if (((j + MESIN11[i+1][7]) / 60) > 22.9) {
							MESIN11_r2[x].push([(MESIN11[i][0]+' '+MESIN11[i][1]),MESIN11[i][2], MESIN11[i][3]]);
							if (c == 1) {
								d = Math.floor((((22.9 - (j / 60)).toFixed(1) * 60)*60)/ MESIN11[i+1][5]) * MESIN11[i+1][6];
								MESIN11_r2[x].push([(MESIN11[i][0]+' '+MESIN11[i][1]),MESIN11[i][2], d]);
								MESIN11[i+1][3] -= d;
								MESIN11[i+1][7] = Math.floor((Math.floor(MESIN11[i+1][3] / MESIN11[i+1][6]) * MESIN11[i+1][5]) / 60);
							} else {
								if (MESIN11[i][3] != MESIN11[i][4]) {
									
									// ------minus


									if ((Math.floor(((((22.9 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6]) < 0) {

										MESIN11_r2[x].push([(MESIN11[i][0]+' '+MESIN11[i][1]),MESIN11[i+1][2], 
										(Math.floor(((((22.9 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6])
										]);

										MESIN11[i+1][3] -= 0;
									}else{

										MESIN11_r2[x].push([(MESIN11[i][0]+' '+MESIN11[i][1]),MESIN11[i+1][2], 
										(Math.floor(((((22.9 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6])
										]);

										
									MESIN11[i+1][3] -= (Math.floor(((((22.9 - (MESIN11[i][7] / 60)).toFixed(1))*60)*60) / MESIN11[i+1][5])* MESIN11[i+1][6]);
								}

								// ------ end minus

								}

								j = 0;
							}

							c = 0;
							x+=1;
						} else {
							j = MESIN11[i][7] + MESIN11[i+1][7];
							// d = MESIN11[i][3] + MESIN11[i+1][3];
							MESIN11_r2[x].push([(MESIN11[i][0]+' '+MESIN11[i][1]),MESIN11[i][2], MESIN11[i][3]]);
							c = 1;
						}
					}

				}
				console.log(MESIN11_r2);

				// ---------------- End MESIN11  ------------------




				// ---------------- Mesin1 Table  ------------------

				for (var i = 0; i < mesin1_r2.length; i++) {	
					unik = [];
					lp = "";


					if (mesin1_r2[i].length === 1) {
						HeadMesin1 +='<td style="font-size:20px;">'+mesin1_r2[i][0][0]+'</td>';

						BodyMesin1 += '<td style="padding: 0px">';
						BodyMesin1 +='<table border="1"  >';	
						BodyMesin1 +='<tr>';
						BodyMesin1 +='<td style="font-size:20px;">'+mesin1_r2[i][0][0] +'</td>';
						BodyMesin1 +='<td style="font-size:20px;">'+mesin1_r2[i][0][1]+'</td>';
						BodyMesin1 +='<td style="font-size:20px;">'+mesin1_r2[i][0][2]+'</td>';
						BodyMesin1 +='</tr>';
						BodyMesin1 +='</table>';
						BodyMesin1 +='</td>';
					}

					if (mesin1_r2[i].length > 1) {
						

						BodyMesin1 += '<td style="padding: 0px">';
						BodyMesin1 +='<table border="1"  >';

						for (var a = 0; a < mesin1_r2[i].length; a++) {
							
							if (mesin1_r2[i][a][2] > 0) {

								BodyMesin1 +='<tr>';

								if (typeof mesin1_r2[i][a+1] === 'undefined' ) {
									BodyMesin1 +='<td style="font-size:20px; ">'+mesin1_r2[i][a][0] +'</td>';								

								}else{

									if (mesin1_r2[i][a][0] != mesin1_r2[i][a+1][0]) {
									BodyMesin1 +='<td style="font-size:20px; background-color: #ffd03a;">'+mesin1_r2[i][a][0] +'</td>';									
									}else{
									BodyMesin1 +='<td style="font-size:20px; ">'+mesin1_r2[i][a][0] +'</td>';
									}
								}

								BodyMesin1 +='<td style="font-size:20px;">'+mesin1_r2[i][a][1]+'</td>';
								BodyMesin1 +='<td style="font-size:20px;">'+mesin1_r2[i][a][2]+'</td>';
								BodyMesin1 +='</tr>';							
																
							}
						unik.push(mesin1_r2[i][a][0]);
																			
						}

						if (unique(unik).length > 1	) {
						HeadMesin1 +='<td style="font-size:20px; background-color: #ffd03a;">'+unique(unik)+'</td>';	
						}else{
						HeadMesin1 +='<td style="font-size:20px; ">'+unique(unik)+'</td>';
						}		
						
						BodyMesin1 +='</table>';
						BodyMesin1 +='</td>';						
					}

				}			

				$('#HeadMesin1').append(HeadMesin1);
				$('#BodyMesin1').append(BodyMesin1);

				// ---------------- End Mesin1 Table  ------------------

				// ---------------- MESIN2 Table  ------------------

				for (var i = 0; i < MESIN2_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN2_r2[i].length === 1) {
						HeadMESIN2 +='<td style="font-size:20px;">'+MESIN2_r2[i][0][0]+'</td>';

						BodyMESIN2 += '<td style="padding: 0px">';
						BodyMESIN2 +='<table border="1"  >';	
						BodyMESIN2 +='<tr>';
						BodyMESIN2 +='<td style="font-size:20px;">'+MESIN2_r2[i][0][0] +'</td>';
						BodyMESIN2 +='<td style="font-size:20px;">'+MESIN2_r2[i][0][1]+'</td>';
						BodyMESIN2 +='<td style="font-size:20px;">'+MESIN2_r2[i][0][2]+'</td>';
						BodyMESIN2 +='</tr>';
						BodyMESIN2 +='</table>';
						BodyMESIN2 +='</td>';
					}

					if (MESIN2_r2[i].length > 1) {
						

						BodyMESIN2 += '<td style="padding: 0px">';
						BodyMESIN2 +='<table border="1"  >';

						for (var a = 0; a < MESIN2_r2[i].length; a++) {
							
							if (MESIN2_r2[i][a][2] > 0) {

								BodyMESIN2 +='<tr>';

								if (typeof MESIN2_r2[i][a+1] === 'undefined' ) {
									BodyMESIN2 +='<td style="font-size:20px; ">'+MESIN2_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN2_r2[i][a][0] != MESIN2_r2[i][a+1][0]) {
									BodyMESIN2 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN2_r2[i][a][0] +'</td>';									
									}else{
									BodyMESIN2 +='<td style="font-size:20px; ">'+MESIN2_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN2 +='<td style="font-size:20px;">'+MESIN2_r2[i][a][1]+'</td>';
								BodyMESIN2 +='<td style="font-size:20px;">'+MESIN2_r2[i][a][2]+'</td>';
								BodyMESIN2 +='</tr>';							
																
							}
						unik.push(MESIN2_r2[i][a][0]);
																			
						}

						if (unique(unik).length > 1	) {
						HeadMESIN2 +='<td style="font-size:20px; background-color: #ffd03a;">'+unique(unik)+'</td>';	
						}else{
						HeadMESIN2 +='<td style="font-size:20px; ">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN2 +='</table>';
						BodyMESIN2 +='</td>';						
					}

				}			

				$('#HeadMESIN2').append(HeadMESIN2);
				$('#BodyMESIN2').append(BodyMESIN2);

				// ---------------- End MESIN2 Table  ------------------



				// ---------------- MESIN3 Table  ------------------

				for (var i = 0; i < MESIN3_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN3_r2[i].length === 1) {
						HeadMESIN3 +='<td style="font-size:20px;">'+MESIN3_r2[i][0][0]+'</td>';

						BodyMESIN3 += '<td style="padding: 0px">';
						BodyMESIN3 +='<table border="1"  >';	
						BodyMESIN3 +='<tr>';
						BodyMESIN3 +='<td style="font-size:20px;">'+MESIN3_r2[i][0][0] +'</td>';
						BodyMESIN3 +='<td style="font-size:20px;">'+MESIN3_r2[i][0][1]+'</td>';
						BodyMESIN3 +='<td style="font-size:20px;">'+MESIN3_r2[i][0][2]+'</td>';
						BodyMESIN3 +='</tr>';
						BodyMESIN3 +='</table>';
						BodyMESIN3 +='</td>';
					}

					if (MESIN3_r2[i].length > 1) {
						

						BodyMESIN3 += '<td style="padding: 0px">';
						BodyMESIN3 +='<table border="1"  >';

						for (var a = 0; a < MESIN3_r2[i].length; a++) {
							
							if (MESIN3_r2[i][a][2] > 0) {

								BodyMESIN3 +='<tr>';

								if (typeof MESIN3_r2[i][a+1] === 'undefined' ) {
									BodyMESIN3 +='<td style="font-size:20px; ">'+MESIN3_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN3_r2[i][a][0] != MESIN3_r2[i][a+1][0]) {
									BodyMESIN3 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN3_r2[i][a][0] +'</td>';									
									}else{
									BodyMESIN3 +='<td style="font-size:20px; ">'+MESIN3_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN3 +='<td style="font-size:20px;">'+MESIN3_r2[i][a][1]+'</td>';
								BodyMESIN3 +='<td style="font-size:20px;">'+MESIN3_r2[i][a][2]+'</td>';
								BodyMESIN3 +='</tr>';							
																
							}
						unik.push(MESIN3_r2[i][a][0]);
																			
						}

						if (unique(unik).length > 1	) {
						HeadMESIN3 +='<td style="font-size:20px; background-color: #ffd03a;">'+unique(unik)+'</td>';	
						}else{
						HeadMESIN3 +='<td style="font-size:20px; ">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN3 +='</table>';
						BodyMESIN3 +='</td>';						
					}

				}			

				$('#HeadMESIN3').append(HeadMESIN3);
				$('#BodyMESIN3').append(BodyMESIN3);

				// ---------------- End MESIN3 Table  ------------------


				// ---------------- MESIN4 Table  ------------------

				for (var i = 0; i < MESIN4_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN4_r2[i].length === 1) {
						HeadMESIN4 +='<td style="font-size:20px;">'+MESIN4_r2[i][0][0]+'</td>';

						BodyMESIN4 += '<td style="padding: 0px">';
						BodyMESIN4 +='<table border="1"  >';	
						BodyMESIN4 +='<tr>';
						BodyMESIN4 +='<td style="font-size:20px;">'+MESIN4_r2[i][0][0] +'</td>';
						BodyMESIN4 +='<td style="font-size:20px;">'+MESIN4_r2[i][0][1]+'</td>';
						BodyMESIN4 +='<td style="font-size:20px;">'+MESIN4_r2[i][0][2]+'</td>';
						BodyMESIN4 +='</tr>';
						BodyMESIN4 +='</table>';
						BodyMESIN4 +='</td>';
					}

					if (MESIN4_r2[i].length > 1) {
						

						BodyMESIN4 += '<td style="padding: 0px">';
						BodyMESIN4 +='<table border="1"  >';

						for (var a = 0; a < MESIN4_r2[i].length; a++) {
							
							if (MESIN4_r2[i][a][2] > 0) {

								BodyMESIN4 +='<tr>';

								if (typeof MESIN4_r2[i][a+1] === 'undefined' ) {
									BodyMESIN4 +='<td style="font-size:20px; ">'+MESIN4_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN4_r2[i][a][0] != MESIN4_r2[i][a+1][0]) {
									BodyMESIN4 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN4_r2[i][a][0] +'</td>';									
									}else{
									BodyMESIN4 +='<td style="font-size:20px; ">'+MESIN4_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN4 +='<td style="font-size:20px;">'+MESIN4_r2[i][a][1]+'</td>';
								BodyMESIN4 +='<td style="font-size:20px;">'+MESIN4_r2[i][a][2]+'</td>';
								BodyMESIN4 +='</tr>';							
																
							}
						unik.push(MESIN4_r2[i][a][0]);
																			
						}

						if (unique(unik).length > 1	) {
						HeadMESIN4 +='<td style="font-size:20px; background-color: #ffd03a;">'+unique(unik)+'</td>';	
						}else{
						HeadMESIN4 +='<td style="font-size:20px; ">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN4 +='</table>';
						BodyMESIN4 +='</td>';						
					}

				}			

				$('#HeadMESIN4').append(HeadMESIN4);
				$('#BodyMESIN4').append(BodyMESIN4);

				// ---------------- End MESIN4 Table  ------------------


				// ---------------- MESIN5 Table  ------------------

				for (var i = 0; i < MESIN5_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN5_r2[i].length === 1) {
						HeadMESIN5 +='<td style="font-size:20px;">'+MESIN5_r2[i][0][0]+'</td>';

						BodyMESIN5 += '<td style="padding: 0px">';
						BodyMESIN5 +='<table border="1"  >';	
						BodyMESIN5 +='<tr>';
						BodyMESIN5 +='<td style="font-size:20px;">'+MESIN5_r2[i][0][0] +'</td>';
						BodyMESIN5 +='<td style="font-size:20px;">'+MESIN5_r2[i][0][1]+'</td>';
						BodyMESIN5 +='<td style="font-size:20px;">'+MESIN5_r2[i][0][2]+'</td>';
						BodyMESIN5 +='</tr>';
						BodyMESIN5 +='</table>';
						BodyMESIN5 +='</td>';
					}

					if (MESIN5_r2[i].length > 1) {
						

						BodyMESIN5 += '<td style="padding: 0px">';
						BodyMESIN5 +='<table border="1"  >';

						for (var a = 0; a < MESIN5_r2[i].length; a++) {
							
							if (MESIN5_r2[i][a][2] > 0) {

								BodyMESIN5 +='<tr>';

								if (typeof MESIN5_r2[i][a+1] === 'undefined' ) {
									BodyMESIN5 +='<td style="font-size:20px; ">'+MESIN5_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN5_r2[i][a][0] != MESIN5_r2[i][a+1][0]) {
									BodyMESIN5 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN5_r2[i][a][0] +'</td>';									
									}else{
									BodyMESIN5 +='<td style="font-size:20px; ">'+MESIN5_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN5 +='<td style="font-size:20px;">'+MESIN5_r2[i][a][1]+'</td>';
								BodyMESIN5 +='<td style="font-size:20px;">'+MESIN5_r2[i][a][2]+'</td>';
								BodyMESIN5 +='</tr>';							
																
							}
						unik.push(MESIN5_r2[i][a][0]);
																			
						}

						if (unique(unik).length > 1	) {
						HeadMESIN5 +='<td style="font-size:20px; background-color: #ffd03a;">'+unique(unik)+'</td>';	
						}else{
						HeadMESIN5 +='<td style="font-size:20px; ">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN5 +='</table>';
						BodyMESIN5 +='</td>';						
					}

				}			

				$('#HeadMESIN5').append(HeadMESIN5);
				$('#BodyMESIN5').append(BodyMESIN5);

				// ---------------- End MESIN5 Table  ------------------



				// ---------------- MESIN6 Table  ------------------

				for (var i = 0; i < MESIN6_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN6_r2[i].length === 1) {
						HeadMESIN6 +='<td style="font-size:20px;">'+MESIN6_r2[i][0][0]+'</td>';

						BodyMESIN6 += '<td style="padding: 0px">';
						BodyMESIN6 +='<table border="1"  >';	
						BodyMESIN6 +='<tr>';
						BodyMESIN6 +='<td style="font-size:20px;">'+MESIN6_r2[i][0][0] +'</td>';
						BodyMESIN6 +='<td style="font-size:20px;">'+MESIN6_r2[i][0][1]+'</td>';
						BodyMESIN6 +='<td style="font-size:20px;">'+MESIN6_r2[i][0][2]+'</td>';
						BodyMESIN6 +='</tr>';
						BodyMESIN6 +='</table>';
						BodyMESIN6 +='</td>';
					}

					if (MESIN6_r2[i].length > 1) {
						

						BodyMESIN6 += '<td style="padding: 0px">';
						BodyMESIN6 +='<table border="1"  >';

						for (var a = 0; a < MESIN6_r2[i].length; a++) {
							
							if (MESIN6_r2[i][a][2] > 0) {

								BodyMESIN6 +='<tr>';

								if (typeof MESIN6_r2[i][a+1] === 'undefined' ) {
									BodyMESIN6 +='<td style="font-size:20px; ">'+MESIN6_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN6_r2[i][a][0] != MESIN6_r2[i][a+1][0]) {
									BodyMESIN6 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN6_r2[i][a][0] +'</td>';									
									}else{
									BodyMESIN6 +='<td style="font-size:20px; ">'+MESIN6_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN6 +='<td style="font-size:20px;">'+MESIN6_r2[i][a][1]+'</td>';
								BodyMESIN6 +='<td style="font-size:20px;">'+MESIN6_r2[i][a][2]+'</td>';
								BodyMESIN6 +='</tr>';							
																
							}
						unik.push(MESIN6_r2[i][a][0]);
																			
						}

						if (unique(unik).length > 1	) {
						HeadMESIN6 +='<td style="font-size:20px; background-color: #ffd03a;">'+unique(unik)+'</td>';	
						}else{
						HeadMESIN6 +='<td style="font-size:20px; ">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN6 +='</table>';
						BodyMESIN6 +='</td>';						
					}

				}			

				$('#HeadMESIN6').append(HeadMESIN6);
				$('#BodyMESIN6').append(BodyMESIN6);

				// ---------------- End MESIN6 Table  ------------------



				// ---------------- MESIN7 Table  ------------------

				for (var i = 0; i < MESIN7_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN7_r2[i].length === 1) {
						HeadMESIN7 +='<td style="font-size:20px;">'+MESIN7_r2[i][0][0]+'</td>';

						BodyMESIN7 += '<td style="padding: 0px">';
						BodyMESIN7 +='<table border="1"  >';	
						BodyMESIN7 +='<tr>';
						BodyMESIN7 +='<td style="font-size:20px;">'+MESIN7_r2[i][0][0] +'</td>';
						BodyMESIN7 +='<td style="font-size:20px;">'+MESIN7_r2[i][0][1]+'</td>';
						BodyMESIN7 +='<td style="font-size:20px;">'+MESIN7_r2[i][0][2]+'</td>';
						BodyMESIN7 +='</tr>';
						BodyMESIN7 +='</table>';
						BodyMESIN7 +='</td>';
					}

					if (MESIN7_r2[i].length > 1) {
						

						BodyMESIN7 += '<td style="padding: 0px">';
						BodyMESIN7 +='<table border="1"  >';

						for (var a = 0; a < MESIN7_r2[i].length; a++) {
							
							if (MESIN7_r2[i][a][2] > 0) {

								BodyMESIN7 +='<tr>';

								if (typeof MESIN7_r2[i][a+1] === 'undefined' ) {
									BodyMESIN7 +='<td style="font-size:20px; ">'+MESIN7_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN7_r2[i][a][0] != MESIN7_r2[i][a+1][0]) {
									BodyMESIN7 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN7_r2[i][a][0] +'</td>';									
									}else{
									BodyMESIN7 +='<td style="font-size:20px; ">'+MESIN7_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN7 +='<td style="font-size:20px;">'+MESIN7_r2[i][a][1]+'</td>';
								BodyMESIN7 +='<td style="font-size:20px;">'+MESIN7_r2[i][a][2]+'</td>';
								BodyMESIN7 +='</tr>';							
																
							}
						unik.push(MESIN7_r2[i][a][0]);
																			
						}

						if (unique(unik).length > 1	) {
						HeadMESIN7 +='<td style="font-size:20px; background-color: #ffd03a;">'+unique(unik)+'</td>';	
						}else{
						HeadMESIN7 +='<td style="font-size:20px; ">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN7 +='</table>';
						BodyMESIN7 +='</td>';						
					}

				}			

				$('#HeadMESIN7').append(HeadMESIN7);
				$('#BodyMESIN7').append(BodyMESIN7);

				// ---------------- End MESIN7 Table  ------------------



				// ---------------- MESIN8 Table  ------------------

				for (var i = 0; i < MESIN8_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN8_r2[i].length === 1) {
						HeadMESIN8 +='<td style="font-size:20px;">'+MESIN8_r2[i][0][0]+'</td>';

						BodyMESIN8 += '<td style="padding: 0px">';
						BodyMESIN8 +='<table border="1"  >';	
						BodyMESIN8 +='<tr>';
						BodyMESIN8 +='<td style="font-size:20px;">'+MESIN8_r2[i][0][0] +'</td>';
						BodyMESIN8 +='<td style="font-size:20px;">'+MESIN8_r2[i][0][1]+'</td>';
						BodyMESIN8 +='<td style="font-size:20px;">'+MESIN8_r2[i][0][2]+'</td>';
						BodyMESIN8 +='</tr>';
						BodyMESIN8 +='</table>';
						BodyMESIN8 +='</td>';
					}

					if (MESIN8_r2[i].length > 1) {
						

						BodyMESIN8 += '<td style="padding: 0px">';
						BodyMESIN8 +='<table border="1"  >';

						for (var a = 0; a < MESIN8_r2[i].length; a++) {
							
							if (MESIN8_r2[i][a][2] > 0) {

								BodyMESIN8 +='<tr>';

								if (typeof MESIN8_r2[i][a+1] === 'undefined' ) {
									BodyMESIN8 +='<td style="font-size:20px; ">'+MESIN8_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN8_r2[i][a][0] != MESIN8_r2[i][a+1][0]) {
									BodyMESIN8 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN8_r2[i][a][0] +'</td>';									
									}else{
									BodyMESIN8 +='<td style="font-size:20px; ">'+MESIN8_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN8 +='<td style="font-size:20px;">'+MESIN8_r2[i][a][1]+'</td>';
								BodyMESIN8 +='<td style="font-size:20px;">'+MESIN8_r2[i][a][2]+'</td>';
								BodyMESIN8 +='</tr>';							
																
							}
						unik.push(MESIN8_r2[i][a][0]);
																			
						}

						if (unique(unik).length > 1	) {
						HeadMESIN8 +='<td style="font-size:20px; background-color: #ffd03a;">'+unique(unik)+'</td>';	
						}else{
						HeadMESIN8 +='<td style="font-size:20px; ">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN8 +='</table>';
						BodyMESIN8 +='</td>';						
					}

				}			

				$('#HeadMESIN8').append(HeadMESIN8);
				$('#BodyMESIN8').append(BodyMESIN8);

				// ---------------- End MESIN8 Table  ------------------



				// ---------------- MESIN9 Table  ------------------

				for (var i = 0; i < MESIN9_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN9_r2[i].length === 1) {
						HeadMESIN9 +='<td style="font-size:20px;">'+MESIN9_r2[i][0][0]+'</td>';

						BodyMESIN9 += '<td style="padding: 0px">';
						BodyMESIN9 +='<table border="1"  >';	
						BodyMESIN9 +='<tr>';
						BodyMESIN9 +='<td style="font-size:20px;">'+MESIN9_r2[i][0][0] +'</td>';
						BodyMESIN9 +='<td style="font-size:20px;">'+MESIN9_r2[i][0][1]+'</td>';
						BodyMESIN9 +='<td style="font-size:20px;">'+MESIN9_r2[i][0][2]+'</td>';
						BodyMESIN9 +='</tr>';
						BodyMESIN9 +='</table>';
						BodyMESIN9 +='</td>';
					}

					if (MESIN9_r2[i].length > 1) {
						

						BodyMESIN9 += '<td style="padding: 0px">';
						BodyMESIN9 +='<table border="1"  >';

						for (var a = 0; a < MESIN9_r2[i].length; a++) {
							
							if (MESIN9_r2[i][a][2] > 0) {

								BodyMESIN9 +='<tr>';

								if (typeof MESIN9_r2[i][a+1] === 'undefined' ) {
									BodyMESIN9 +='<td style="font-size:20px; ">'+MESIN9_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN9_r2[i][a][0] != MESIN9_r2[i][a+1][0]) {
									BodyMESIN9 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN9_r2[i][a][0] +'</td>';									
									}else{
									BodyMESIN9 +='<td style="font-size:20px; ">'+MESIN9_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN9 +='<td style="font-size:20px;">'+MESIN9_r2[i][a][1]+'</td>';
								BodyMESIN9 +='<td style="font-size:20px;">'+MESIN9_r2[i][a][2]+'</td>';
								BodyMESIN9 +='</tr>';							
																
							}
						unik.push(MESIN9_r2[i][a][0]);
																			
						}

						if (unique(unik).length > 1	) {
						HeadMESIN9 +='<td style="font-size:20px; background-color: #ffd03a;">'+unique(unik)+'</td>';	
						}else{
						HeadMESIN9 +='<td style="font-size:20px; ">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN9 +='</table>';
						BodyMESIN9 +='</td>';						
					}

				}			

				$('#HeadMESIN9').append(HeadMESIN9);
				$('#BodyMESIN9').append(BodyMESIN9);

				// ---------------- End MESIN9 Table  ------------------



				// ---------------- MESIN11 Table  ------------------

				for (var i = 0; i < MESIN11_r2.length; i++) {	
					unik = [];
					lp = "";


					if (MESIN11_r2[i].length === 1) {
						HeadMESIN11 +='<td style="font-size:20px;">'+MESIN11_r2[i][0][0]+'</td>';

						BodyMESIN11 += '<td style="padding: 0px">';
						BodyMESIN11 +='<table border="1"  >';	
						BodyMESIN11 +='<tr>';
						BodyMESIN11 +='<td style="font-size:20px;">'+MESIN11_r2[i][0][0] +'</td>';
						BodyMESIN11 +='<td style="font-size:20px;">'+MESIN11_r2[i][0][1]+'</td>';
						BodyMESIN11 +='<td style="font-size:20px;">'+MESIN11_r2[i][0][2]+'</td>';
						BodyMESIN11 +='</tr>';
						BodyMESIN11 +='</table>';
						BodyMESIN11 +='</td>';
					}

					if (MESIN11_r2[i].length > 1) {
						

						BodyMESIN11 += '<td style="padding: 0px">';
						BodyMESIN11 +='<table border="1"  >';

						for (var a = 0; a < MESIN11_r2[i].length; a++) {
							
							if (MESIN11_r2[i][a][2] > 0) {

								BodyMESIN11 +='<tr>';

								if (typeof MESIN11_r2[i][a+1] === 'undefined' ) {
									BodyMESIN11 +='<td style="font-size:20px; ">'+MESIN11_r2[i][a][0] +'</td>';								

								}else{

									if (MESIN11_r2[i][a][0] != MESIN11_r2[i][a+1][0]) {
									BodyMESIN11 +='<td style="font-size:20px; background-color: #ffd03a;">'+MESIN11_r2[i][a][0] +'</td>';									
									}else{
									BodyMESIN11 +='<td style="font-size:20px; ">'+MESIN11_r2[i][a][0] +'</td>';
									}
								}

								BodyMESIN11 +='<td style="font-size:20px;">'+MESIN11_r2[i][a][1]+'</td>';
								BodyMESIN11 +='<td style="font-size:20px;">'+MESIN11_r2[i][a][2]+'</td>';
								BodyMESIN11 +='</tr>';							
																
							}
						unik.push(MESIN11_r2[i][a][0]);
																			
						}

						if (unique(unik).length > 1	) {
						HeadMESIN11 +='<td style="font-size:20px; background-color: #ffd03a;">'+unique(unik)+'</td>';	
						}else{
						HeadMESIN11 +='<td style="font-size:20px; ">'+unique(unik)+'</td>';
						}		
						
						BodyMESIN11 +='</table>';
						BodyMESIN11 +='</td>';						
					}

				}			

				$('#HeadMESIN11').append(HeadMESIN11);
				$('#BodyMESIN11').append(BodyMESIN11);

				// ---------------- End MESIN11 Table  ------------------
				
	

				// console.table(TargetAllMesin);
				// console.table(MESIN1);
				// console.table(MESIN2);
				// console.table(MESIN3);
				// console.table(MESIN4);
				// console.table(MESIN5);
				// console.table(MESIN6);
				// console.table(MESIN7);
				// console.table(MESIN8);
				// console.table(MESIN9);
				// console.table(MESIN11);

				openSuccessGritter('Success!', result.message);
				fjudul();


			}
			else{
				audio_error.play();

			}
		}
		else{
			audio_error.play();
			alert('Disconnected from sever');
		}
	});

}

function fjudul() {
	var akhir = [];
	var total = [];
	var max = [];

	for (var i = 0; i < mesin1_r2.length; i++) {		
			if (mesin1_r2[i].length >= 1) {
				akhir.push(i);
			}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	akhir = [];

	for (var i = 0; i < MESIN2_r2.length; i++) {		
			if (MESIN2_r2[i].length >= 1) {
				akhir.push(i);
			}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	akhir = [];

	for (var i = 0; i < MESIN3_r2.length; i++) {		
			if (MESIN3_r2[i].length >= 1) {
				akhir.push(i);
			}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	akhir = [];

	for (var i = 0; i < MESIN4_r2.length; i++) {		
			if (MESIN4_r2[i].length >= 1) {
				akhir.push(i);
			}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	akhir = [];

	for (var i = 0; i < MESIN5_r2.length; i++) {		
			if (MESIN5_r2[i].length >= 1) {
				akhir.push(i);
			}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	akhir = [];

	for (var i = 0; i < MESIN6_r2.length; i++) {		
			if (MESIN6_r2[i].length >= 1) {
				akhir.push(i);
			}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	akhir = [];

	for (var i = 0; i < MESIN7_r2.length; i++) {		
			if (MESIN7_r2[i].length >= 1) {
				akhir.push(i);
			}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	akhir = [];

	for (var i = 0; i < MESIN8_r2.length; i++) {		
			if (MESIN8_r2[i].length >= 1) {
				akhir.push(i);
			}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	akhir = [];

	for (var i = 0; i < MESIN9_r2.length; i++) {		
			if (MESIN9_r2[i].length >= 1) {
				akhir.push(i);
			}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	akhir = [];

	for (var i = 0; i < MESIN11_r2.length; i++) {		
			if (MESIN11_r2[i].length >= 1) {
				akhir.push(i);
			}		
	}
	total.push(parseInt(akhir.reverse().slice(0,1))+1);
	akhir = [];



	// alert(total.sort(function(a, b){return b - a}).slice(0,1));

	for (var i = 0; i < total.length; i++) {
	
		// if (total[i] <= total.sort(function(a, b){return b - a}).slice(0,1)) {

			max.push(total.sort(function(a, b){return b - a}).slice(0,1) - total[i])
			alert(max +' '+ total[i])
// 
		// }
		// Things[i]
	}
	
}

function getMesin() {

	$.get('{{ url("fetch/getStatusMesin") }}',  function(result, status, xhr){
		console.log(status);
		console.log(result);
		console.log(xhr);
		if(xhr.status == 200){
			if(result.status){

				openSuccessGritter('Success!', result.message);


			}
			else{
				audio_error.play();

			}
		}
		else{
			audio_error.play();
			alert('Disconnected from sever');
		}
	});
	
}

function openErrorGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-danger',
		image: '{{ url("images/image-stop.png") }}',
		sticky: false,
		time: '2000'
	});
}

function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '2000'
	});
}
</script>
@endsection