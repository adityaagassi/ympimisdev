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
		font-size: 1vw;
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
			<td >MJB IVORY</td>
			<td >MJB IVORY</td>
			<td >MJB IVORY</td>
			
		</tr>
		<tr id="BodyMesin1">
			
			
		</tr>
	</table>

<table class="table table-bordered" style="color:white; font-size: 2vw" id="main">
		<tr>
			<td width="15%" rowspan="3" style="text-align: center;
		vertical-align: middle;">Mesin 1</td	>
			
		</tr>
		<tr id="HeadMesin2">
			<td >MJB IVORY</td>
			<td >MJB IVORY</td>
			<td >MJB IVORY</td>
			
		</tr>
		<tr id="BodyMesin2">
			
			
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
								TargetAllMesin.push([value.part_code,value.color,value.part,parseInt(value.max_day),value.working,value.max_day]);
								if (qty > value.max_day) {
									qty-=value.max_day;
								}else{
									qty=qty;
								}

							}
							TargetAllMesin.push([value.part_code,value.color,value.part,qty,value.working,value.max_day]);
						}
					});

				
				for (var i = 0; i < TargetAllMesin.length; i++) {
					a = TargetAllMesin[i][4];

					eval(a).push([TargetAllMesin[i][0],TargetAllMesin[i][1],TargetAllMesin[i][2],TargetAllMesin[i][3],TargetAllMesin[i][5]]);
				}

				BodyMesin1 += '<td style="padding: 0px">';
				BodyMesin1 +='<table border="1" width="100%" >';
				for (var i = 0; i < MESIN1.length; i++) {
					if (MESIN1[i][3] != MESIN1[i][4]) {	
						
					BodyMesin1 +='<tr>';
					BodyMesin1 +='<td>'+MESIN1[i][0] +' '+MESIN1[i][1]+'</td>';
					BodyMesin1 +='<td>'+MESIN1[i][2]+'</td>';
					BodyMesin1 +='<td>'+MESIN1[i][3]+'</td>';
					BodyMesin1 +='</tr>';
					
					}					
				}
				BodyMesin1 +='</table>';
				BodyMesin1 +='</td>';

				for (var i = 0; i < MESIN1.length; i++) {
				if (MESIN1[i][3] == MESIN1[i][4]){
					BodyMesin1 += '<td style="padding: 0px">';
					BodyMesin1 +='<table border="1" width="100%" >';	
					BodyMesin1 +='<tr>';
					BodyMesin1 +='<td>'+MESIN1[i][0] +' '+MESIN1[i][1]+'</td>';
					BodyMesin1 +='<td>'+MESIN1[i][2]+'</td>';
					BodyMesin1 +='<td>'+MESIN1[i][3]+'</td>';
					BodyMesin1 +='</tr>';
					BodyMesin1 +='</table>';
					BodyMesin1 +='</td>';
					}
				}

				$('#BodyMesin1').append(BodyMesin1);

					console.table(TargetAllMesin);
					console.table(MESIN1);
					console.table(MESIN2);
					console.table(MESIN3);
					console.table(MESIN4);
					console.table(MESIN5);
					console.table(MESIN6);
					console.table(MESIN7);
					console.table(MESIN8);
					console.table(MESIN9);
					console.table(MESIN11);

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