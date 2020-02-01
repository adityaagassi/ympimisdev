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
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
		padding:0;
		font-size: 12px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		padding:0;
		vertical-align: middle;
		text-align: center;
	}
	.content{
		color: white;
		font-weight: bold;
	}
	.progress {
		background-color: rgba(0,0,0,0);
	}
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div class="row">
		<div class="col-xs-12" style="margin-top: 0px;">
			<div class="row" style="margin:0px;">
				<form method="GET" action="{{ action('MiddleProcessController@indexDisplayProductionResult') }}">
					<div class="col-xs-2">
						<div class="input-group date">
							<div class="input-group-addon bg-green" style="border: none;">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control datepicker" name="tanggal" placeholder="Select Date">
						</div>
					</div>
					<div class="col-xs-3">
						<div class="form-group">
							<select class="form-control select2" multiple="multiple" id="locationSelect" data-placeholder="Select Location" onchange="change()">
								@foreach($locations as $location)
								<option value="{{ $location }}">{{ $location }}</option>
								@endforeach
							</select>
							<input type="text" name="location" id="location" hidden>
						</div>
					</div>
					<div class="col-xs-1">
						<div class="form-group">
							<button class="btn btn-success" type="submit">Search</button>
						</div>
					</div>
				</form>
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 2vw;"></div>
			</div>
			<div class="col-xs-12" style="padding: 0px; margin-top: 0;">
				<table id="s3" class="table table-bordered" style="margin:0">
					<thead id="head_s3">
					</thead>
					<tbody id="body_s3">
					</tbody>
				</table>
				<table id="s1" class="table table-bordered" style="margin:0">
					<thead id="head_s1">
					</thead>
					<tbody id="body_s1">
					</tbody>
				</table>
				<table id="s2" class="table table-bordered" style="margin:0">
					<thead id="head_s2">
					</thead>
					<tbody id="body_s2">
					</tbody>
				</table>
			</div>
		</div>
	</div>

</section>


@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('.select2').select2();
		fillTable();
		setInterval(fillTable, 10000);
	});

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

	function change() {
		$("#location").val($("#locationSelect").val());
	}

	$('.datepicker').datepicker({
		<?php $tgl_max = date('d-m-Y') ?>
		autoclose: true,
		format: "dd-mm-yyyy",
		endDate: '<?php echo $tgl_max ?>'
	});

	function fillTable() {
		var data = {
			tgl:"{{$_GET['tanggal']}}",
			location:"{{$_GET['location']}}"
		}

		$.get('{{ url("fetch/welding/display_production_result") }}', data, function(result, status, xhr) {

			if(xhr.status == 200){
				if(result.status){
					var title = result.title;
					$('#last_update').html('<b>'+ title +'</b>');

					var as_shift3 = [];
					var as_shift1 = [];
					var as_shift2 = [];
					var as_key = [];
					var as_model = [];
					for (var i = 0; i < result.alto.length; i++) {
						as_shift3.push(result.alto[i].shift3);
						as_shift1.push(result.alto[i].shift1);
						as_shift2.push(result.alto[i].shift2);
						as_key.push(result.alto[i].key);
						as_model.push(result.alto[i].model);
					}

					var ts_shift3 = [];
					var ts_shift1 = [];
					var ts_shift2 = [];
					var ts_key = [];
					var ts_model = [];
					for (var i = 0; i < result.tenor.length; i++) {
						ts_shift3.push(result.tenor[i].shift3);
						ts_shift1.push(result.tenor[i].shift1);
						ts_shift2.push(result.tenor[i].shift2);
						ts_key.push(result.tenor[i].key);
						ts_model.push(result.tenor[i].model);
					}

					$('#head_s3').append().empty();
					$('#body_s3').append().empty();
					$('#head_s1').append().empty();
					$('#body_s1').append().empty();
					$('#head_s2').append().empty();
					$('#body_s2').append().empty();

					//------------------------------------ Shift 3
					$('#head_s3').html("");
					var key = [];
					var head = '<tr style="background-color: rgba(126,86,134,.7);"><th style="padding: 0px;background-color: rgba(126,86,134,.7);width:1.5%;">Shift 3</th>';

					for (var i = 0; i < result.key.length; i++) {
						key.push(result.key[i].key);
						head += '<th style="padding: 0px;width:1%;">'+ key[i] +'</th>';
					}
					head += '<th style="padding: 0px;width:1%;">Total</th>';
					head += '</tr>';

					var sum = [];
					var body = '';

					//Alto Body
					var model_alto = [];
					var alto = [];
					var alto_key = [];

					for (var i = 0; i < result.model_alto.length; i++) {
						model_alto.push(result.model_alto[i].model);
						body += '<tr>';
						body += '<td style="background-color: #ffff66;color:black;">'+ model_alto[i] +'</td>';
						sum = [];
						for (var j = 0; j < result.key.length; j++) {
							var is = true;
							for (var k = 0; k < result.alto.length; k++) {
								if((model_alto[i] == as_model[k]) && (key[j] == as_key[k])){
									if(as_shift3[k] > 0){
										body += '<td style="background-color: #ffff66;color:black;">'+ as_shift3[k] +'</td>';
										sum.push(parseInt(as_shift3[k]));
									}
									else{
										body += '<td>'+ as_shift3[k] +'</td>';
										sum.push(parseInt(as_shift3[k]));

									}
									is = false;
								}
							}
							if(is){
								body += '<td>0</td>';
								sum.push(parseInt(0));
							}
						}
						alto.push(sum);
						body += '<td style="background-color: #ffff66;color:black;">'+sum.reduce(function(a,b){return a+b})+'</td>';
						body += '</tr>';
					}
					body += '<tr>';
					body += '<td style="background-color: #ffff66;color:black;">Total</td>';
					for (var i = 0; i < result.key.length; i++) {
						var temp = 0;
						for(var j = 0; j < result.model_alto.length; j++){
							temp += alto[j][i];
						}
						alto_key.push(temp);
						body += '<td style="background-color: #ffff66;color:black;">'+alto_key[i]+'</td>';
					}
					body += '<td style="background-color: #ffff66;color:black;">'+alto_key.reduce(function(a,b){return a+b})+'</td>';
					body += '<tr>';
					//End Alto Body

					//Tenor Body
					var tenor = [];
					var tenor_key = [];
					var model_tenor = [];
					for (var i = 0; i < result.model_tenor.length; i++) {
						model_tenor.push(result.model_tenor[i].model);
						body += '<tr>';
						body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+ model_tenor[i] +'</td>';
						sum = [];
						for (var j = 0; j < result.key.length; j++) {
							var is = true;
							for (var k = 0; k < result.alto.length; k++) {
								if((model_tenor[i] == ts_model[k]) && (key[j] == ts_key[k])){
									if(ts_shift3[k] > 0){
										body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+ ts_shift3[k] +'</td>';
										sum.push(parseInt(ts_shift3[k]));
									}
									else{
										body += '<td>'+ ts_shift3[k] +'</td>';
										sum.push(parseInt(ts_shift3[k]));
									}
									is = false;
								}
							}
							if(is){
								body += '<td>0</td>';
								sum.push(parseInt(0));

							}			
						}
						tenor.push(sum);
						body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+sum.reduce(function(a,b){return a+b})+'</td>';
						body += '</tr>';
					}
					body += '<tr>';
					body += '<td style="background-color: rgb(157, 255, 105);color:black;">Total</td>';
					for (var i = 0; i < result.key.length; i++) {
						var temp = 0;
						for(var j = 0; j < result.model_tenor.length; j++){
							temp += tenor[j][i];
						}
						tenor_key.push(temp);
						body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+tenor_key[i]+'</td>';
					}
					body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+tenor_key.reduce(function(a,b){return a+b})+'</td>';
					body += '<tr>';
					//End Tenor Body

					$('#head_s3').append(head);
					$('#body_s3').append(body);






					//------------------------------------------ Shift 1
					$('#head_s1').html("");
					var key = [];
					var head = '<tr style="background-color: rgba(126,86,134,.7);"><th style="padding: 0px;background-color: rgba(126,86,134,.7);width:1.5%;">Shift 1</th>';

					for (var i = 0; i < result.key.length; i++) {
						key.push(result.key[i].key);
						head += '<th style="padding: 0px;width:1%;">'+ key[i] +'</th>';
					}
					head += '<th style="padding: 0px;width:1%;">Total</th>';
					head += '</tr>';

					var sum = [];
					var body = '';

					//Alto Body
					var model_alto = [];
					var alto = [];
					var alto_key = [];					
					for (var i = 0; i < result.model_alto.length; i++) {
						model_alto.push(result.model_alto[i].model);
						body += '<tr>';
						body += '<td style="background-color: #ffff66;color:black;">'+ model_alto[i] +'</td>';
						sum = [];
						for (var j = 0; j < result.key.length; j++) {
							var is = true;
							for (var k = 0; k < result.alto.length; k++) {
								if((model_alto[i] == as_model[k]) && (key[j] == as_key[k])){
									if(as_shift1[k] > 0){
										body += '<td style="background-color: #ffff66;color:black;">'+ as_shift1[k] +'</td>';
										sum.push(parseInt(as_shift1[k]));
									}
									else{
										body += '<td>'+ as_shift1[k] +'</td>';
										sum.push(parseInt(as_shift1[k]));
									}
									is = false;
								}
							}
							if(is){
								body += '<td>0</td>';
								sum.push(parseInt(0));

							}			
						}
						alto.push(sum);
						body += '<td style="background-color: #ffff66;color:black;">'+sum.reduce(function(a,b){return a+b})+'</td>';
						body += '</tr>';
					}
					body += '<tr>';
					body += '<td style="background-color: #ffff66;color:black;">Total</td>';
					for (var i = 0; i < result.key.length; i++) {
						var temp = 0;
						for(var j = 0; j < result.model_alto.length; j++){
							temp += alto[j][i];
						}
						alto_key.push(temp);
						body += '<td style="background-color: #ffff66;color:black;">'+alto_key[i]+'</td>';
					}
					body += '<td style="background-color: #ffff66;color:black;">'+alto_key.reduce(function(a,b){return a+b})+'</td>';
					body += '<tr>';
					//End Alto Body


					//Tenor Body
					var model_tenor = [];
					var tenor = [];
					var tenor_key = [];
					for (var i = 0; i < result.model_tenor.length; i++) {
						model_tenor.push(result.model_tenor[i].model);
						body += '<tr>';
						body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+ model_tenor[i] +'</td>';
						sum = [];
						for (var j = 0; j < result.key.length; j++) {
							var is = true;
							for (var k = 0; k < result.alto.length; k++) {
								if((model_tenor[i] == ts_model[k]) && (key[j] == ts_key[k])){
									if(ts_shift1[k] > 0){
										body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+ ts_shift1[k] +'</td>';
										sum.push(parseInt(ts_shift1[k]));
									}
									else{
										body += '<td>'+ ts_shift1[k] +'</td>';
										sum.push(parseInt(ts_shift1[k]));
									}
									is = false;
								}
							}
							if(is){
								body += '<td>0</td>';
								sum.push(parseInt(0));
							}			
						}
						tenor.push(sum);
						body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+sum.reduce(function(a,b){return a+b})+'</td>';
						body += '</tr>';
					}
					body += '<tr>';
					body += '<td style="background-color: rgb(157, 255, 105);color:black;">Total</td>';
					for (var i = 0; i < result.key.length; i++) {
						var temp = 0;
						for(var j = 0; j < result.model_tenor.length; j++){
							temp += tenor[j][i];
						}
						tenor_key.push(temp);
						body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+tenor_key[i]+'</td>';
					}
					body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+tenor_key.reduce(function(a,b){return a+b})+'</td>';
					body += '<tr>';
					//End Tenor Body

					$('#head_s1').append(head);
					$('#body_s1').append(body);




					//----------------------------------- Shift 2
					$('#head_alto_s2').html("");
					var key = [];
					var head = '<tr style="background-color: rgba(126,86,134,.7);"><th style="padding: 0px;background-color: rgba(126,86,134,.7);width:1.5%;">Shift 2</th>';

					for (var i = 0; i < result.key.length; i++) {
						key.push(result.key[i].key);
						head += '<th style="padding: 0px;width:1%;">'+ key[i] +'</th>';
					}
					head += '<th style="padding: 0px;width:1%;">Total</th>';
					head += '</tr>';

					var body = '';
					var sum = [];
					
					//Alto Body
					var model_alto = [];
					var alto = [];
					var alto_key = [];	
					for (var i = 0; i < result.model_alto.length; i++) {
						model_alto.push(result.model_alto[i].model);
						body += '<tr>';
						body += '<td style="background-color: #ffff66;color:black;">'+ model_alto[i] +'</td>';
						sum = [];
						for (var j = 0; j < result.key.length; j++) {
							var is = true;
							for (var k = 0; k < result.alto.length; k++) {
								if((model_alto[i] == as_model[k]) && (key[j] == as_key[k])){
									if(as_shift2[k] > 0){
										body += '<td style="background-color: #ffff66;color:black;">'+ as_shift2[k] +'</td>';
										sum.push(parseInt(as_shift2[k]));
									}
									else{
										body += '<td>'+ as_shift2[k] +'</td>';
										sum.push(parseInt(as_shift2[k]));

									}
									is = false;
								}
							}
							if(is){
								body += '<td>0</td>';
								sum.push(parseInt(0));

							}			
						}
						alto.push(sum);
						body += '<td style="background-color: #ffff66;color:black;">'+sum.reduce(function(a,b){return a+b})+'</td>';
						body += '</tr>';
					}
					body += '<tr>';
					body += '<td style="background-color: #ffff66;color:black;">Total</td>';
					for (var i = 0; i < result.key.length; i++) {
						var temp = 0;
						for(var j = 0; j < result.model_alto.length; j++){
							temp += alto[j][i];
						}
						alto_key.push(temp);
						body += '<td style="background-color: #ffff66;color:black;">'+alto_key[i]+'</td>';
					}
					body += '<td style="background-color: #ffff66;color:black;">'+alto_key.reduce(function(a,b){return a+b})+'</td>';
					body += '<tr>';
					//End Alto Body
					

					//Tenor Body
					var model_tenor = [];
					var tenor = [];
					var tenor_key = [];
					for (var i = 0; i < result.model_tenor.length; i++) {
						model_tenor.push(result.model_tenor[i].model);
						body += '<tr>';
						body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+ model_tenor[i] +'</td>';
						sum = [];
						for (var j = 0; j < result.key.length; j++) {
							var is = true;
							for (var k = 0; k < result.alto.length; k++) {
								if((model_tenor[i] == ts_model[k]) && (key[j] == ts_key[k])){
									if(ts_shift2[k] > 0){
										body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+ ts_shift2[k] +'</td>';
										sum.push(parseInt(ts_shift2[k]));
									}else{
										body += '<td>'+ ts_shift2[k] +'</td>';
										sum.push(parseInt(ts_shift2[k]));
									}
									is = false;
								}
							}
							if(is){
								body += '<td>0</td>';
								sum.push(parseInt(0));
							}			
						}
						tenor.push(sum);
						body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+sum.reduce(function(a,b){return a+b})+'</td>';
						body += '</tr>';
					}
					body += '<tr>';
					body += '<td style="background-color: rgb(157, 255, 105);color:black;">Total</td>';
					for (var i = 0; i < result.key.length; i++) {
						var temp = 0;
						for(var j = 0; j < result.model_tenor.length; j++){
							temp += tenor[j][i];
						}
						tenor_key.push(temp);
						body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+tenor_key[i]+'</td>';
					}
					body += '<td style="background-color: rgb(157, 255, 105);color:black;">'+tenor_key.reduce(function(a,b){return a+b})+'</td>';
					body += '<tr>';
					//End Tenor Body

					$('#head_s2').append(head);
					$('#body_s2').append(body);

				}
			}
		});
}

</script>
@stop