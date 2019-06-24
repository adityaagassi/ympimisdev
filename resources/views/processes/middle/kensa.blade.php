@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	.unselectable {
		-webkit-touch-callout: none;
		-webkit-user-select: none;
		-khtml-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	table {
		table-layout:fixed;
	}
	thead>tr>th{
		text-align:center;
		overflow:hidden;
		text-overflow: ellipsis;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		padding:0;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small>WIP Control <span class="text-purple"> 仕掛品管理</span></small>
		<a data-toggle="modal" data-target="#modalOperator" class="btn btn-success btn-sm pull-right" style="color:white">Check In</a>
	</h1>
	{{-- <ol class="breadcrumb">
		<li>
			
		</li>
	</ol> --}}
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<input type="hidden" id="loc" value="{{ $loc }}">
		<div class="col-xs-3" style="padding-right: 0">
			<div class="input-group">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
					<i class="glyphicon glyphicon-barcode"></i>
				</div>
				<input type="text" style="text-align: center; font-size: 25px; height: 50px; border-color: black;" class="form-control" id="tag" name="tag" placeholder="Scan Barcode Here..." required>
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
					<i class="glyphicon glyphicon-ok"></i>
				</div>
			</div>
			<div style="padding-top: 5px;">
				<table style="width: 100%;" border="1">
					<tbody>
						<tr>
							<td style="width: 20%; font-size: 18px; font-weight: bold; background-color: rgb(220,220,220);">Key</td>
							<td id="key" style="font-size: 18px; font-weight: bold; background-color: rgb(100,100,100); color: yellow;">-</td>
						</tr>
					</tbody>
				</table>
				<input type="hidden" id="tag_material">
				<input type="hidden" id="material_number">
			</div>
			<center>
				<button style="width: 100%; margin-top: 10px; font-size: 2vw; padding:0; font-weight: bold; border-color: black; color: yellow;" onclick="confirm()" class="btn btn-success">CONFIRM</button>
			</center>
			<div style="padding-top: 5px;">
				<table class="table table-striped table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 10%; background-color: rgb(220,220,220); padding:0;">#</th>
							<th style="width: 65%; background-color: rgb(220,220,220); padding:0;">NG Name</th>
							<th style="width: 10%; background-color: rgb(220,220,220); padding:0;">#</th>
							<th style="width: 15%; background-color: rgb(220,220,220); padding:0;">Count</th>
						</tr>
					</thead>
					<tbody>
						@foreach($ng_lists as $nomor => $ng_list)
						<input type="hidden" id="loop" value="{{$loop->count}}">
						<tr>
							<td id="minus" onclick="minus({{$nomor+1}})" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 18px; cursor: pointer;" class="unselectable">-</td>
							<td id="ng{{$nomor+1}}" style="font-size: 12px;">{{ $ng_list->ng_name }}</td>
							<td id="plus" onclick="plus({{$nomor+1}})" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 18px; cursor: pointer;" class="unselectable">+</td>
							<td style="font-weight: bold; font-size: 18px; background-color: rgb(100,100,100); color: yellow;"><span id="count{{$nomor+1}}">0</span></td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-xs-9">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; font-size: 18px; padding:0;">Date</th>
						<th style="width:35%; background-color: rgb(220,220,220); text-align: center; color: black; font-size: 18px; padding:0;">Op Kensa</th>
						<th style="width:5%; background-color: rgb(220,220,220); text-align: center; color: black; font-size: 18px; padding:0;">Group</th>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; font-size: 18px; padding:0;">Prod Total</th>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; font-size: 18px; padding:0;">NG Total</th>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; font-size: 18px; padding:0;">Rate Total</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="background-color: null; text-align: center; color: #000000; font-size: 2vw;" id="prodDate">-</td>
						<td style="background-color: null; text-align: center; color: #000000; font-size: 2vw;" id="opKensa">-</td>
						<td style="background-color: null; text-align: center; color: #000000; font-size: 2vw;" id="group">-</td>
						<td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;" id="result">0</td>
						<td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 2vw;" id="notGood">0</td>
						<td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 2vw;" id="ngRate">0%</td>
					</tr>
				</tbody>
			</table>
			<table id="tableDetail" class="table table-bordered table-striped table-hover">
				<thead id="tableDetailHead" style="background-color: rgba(126,86,134,.7);"></thead>
				<tbody id="tableDetailBody"></tbody>
				<tfoot id="tableDetailFoot" style="background-color: RGB(252, 248, 227);"></tfoot>
			</table>
		</div>
	</div>
</section>

<div class="modal fade" id="modalOperator">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				{{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button> --}}
				<h4 class="modal-title" id="modalOperatorTitle" style="text-align: center;">Check In</h4>
				<div class="modal-body table-responsive no-padding">
					<input type="hidden" class="form-control pull-right" id="prod_date" value="<?php echo date('d/m/Y') ?>">
					<div class="form-group">
						<label for="exampleInputEmail1">NIK</label>
						<input class="form-control" style="width: 100%;" type="text" id="incoming_op" placeholder="Enter NIK" required>
					</div>
					
					<div class="form-group">
						<a href="javascript:void(0)" class="btn btn-primary pull-right" onclick="checkIn()">Confirm</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

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

		$('body').toggleClass("sidebar-collapse");
		$('#tag').val("");

		// $('#prod_date').datepicker({
		// 	autoclose: true,
		// 	todayHighlight: true
		// });
		// $('#tag').focus();
		$('#modalOperator').modal({
			backdrop: 'static',
			keyboard: false
		});
		// $('#incoming_op').focus();
	});

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#incoming_op').focus();
	})

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	var delay = (function(){
		var timer = 0;
		return function(callback, ms){
			clearTimeout (timer);
			timer = setTimeout(callback, ms);
		};
	})();

	// $("#tag").on("input", function() {
	// 	delay(function(){
	// 		if ($("#tag").val().length < 8) {
	// 			$("#tag").val("");
	// 		}
	// 	}, 100 );
	// });

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag").val().length >= 11){
				if($('#prodDate').text() != '-'){
					scanTag();
					return false;
				}
				else{
					var loop = $('#loop').val();
					for (var i = 1; i <= loop; i++) {
						$('#count'+i).text('0');
					}
					openErrorGritter('Error!', 'Check in first.');
					audio_error.play();
					$("#tag").val("");
				}
			}
			else{
				var loop = $('#loop').val();
				for (var i = 1; i <= loop; i++) {
					$('#count'+i).text('0');
				}
				openErrorGritter('Error!', 'Invalid.');
				audio_error.play();
				$("#tag").val("");
			}
		}
	});

	function plus(id){
		var count = $('#count'+id).text();
		if($('#key').text() != '-'){
			$('#count'+id).text(parseInt(count)+1);
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', 'Scan material first.');
			$("#tag").val("");
			$("#tag").focus();
			$('#tag').blur();
		}
	}

	function minus(id){
		var count = $('#count'+id).text();
		if($('#key').text() != '-'){
			if(count > 0)
			{
				$('#count'+id).text(parseInt(count)-1);
			}
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', 'Scan material first.');
			$("#tag").val("");
			$("#tag").focus();
			$('#tag').blur();
		}
	}

	function fillResult(){
		var data = {
			location: $('#loc').val(),
			prodDate: $('#prodDate').text(),
			group: $('#group').text()
		}
		$.get('{{ url("fetch/result_middle_kensa") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#result').text(result.resume[0].ok);
					$('#notGood').text(result.resume[0].ng);
					$('#ngRate').text(Math.round(result.resume[0].rate*100, 2)+'%');

					$('#tableDetailHead').html("");
					$('#tableDetailBody').html("");
					$('#tableDetailFoot').html("");
					var tableDetailHead = '';
					var tableDetailBody = '';
					var tableDetailFoot = '';
					var width = 0;
					var ng_list = [];

					width = 84/result.ng_count;
					tableDetailHead += '<tr>';
					tableDetailHead += '<th style="width: 8%; font-size:12px;">Key</th>';
					tableDetailHead += '<th style="width: 8%; font-size:12px;">Total</th>';
					$.each(result.ng_lists, function(index, value) {
						ng_list.push(value.ng_name);
						tableDetailHead += '<th style="width:'+ width +'%; font-size:10px;">';
						tableDetailHead += value.ng_name;
						tableDetailHead += '</th>';
					});
					tableDetailHead += '</tr>';
					$('#tableDetailHead').append(tableDetailHead);

					var keys = [];
					var total_result = 0;
					$.each(result.detail, function(index, value){
						if ($.inArray(value.model, keys)==-1) {
							keys.push(value.model);
							var key = value.model.split(' ');
							tableDetailBody += '<tr>';
							tableDetailBody += '<td style="font-weight: bold;">'+value.model+'</td>';
							tableDetailBody += '<td style="font-size: 14px; font-weightz: bold;">'+value.result+'</td>';
							$.each(result.detail, function(index2, value2){
								if(value2.model == value.model){
									tableDetailBody += '<td style="font-size: 14px; font-weightz: bold;">'+value2.ng_qty+'</td>';
								}
							});
							tableDetailBody += '</tr>';
							total_result += value.result;
						}
					});
					$('#tableDetailBody').append(tableDetailBody);

					tableDetailFoot += '<tr>';
					tableDetailFoot += '<th style="font-size: 16px; font-weightz: bold;">Total</th>';
					tableDetailFoot += '<th style="font-size: 16px; font-weightz: bold;">' + total_result + '</th>';
					$.each(result.ng, function(index, value) {
						tableDetailFoot += '<th style="font-size: 16px; font-weightz: bold;">' + value.ng_qty + '</th>';
					});
					tableDetailFoot += '</tr>';
					$('#tableDetailFoot').append(tableDetailFoot);
					$("#tag").focus();
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected froms server');
			}
		});
	}

	function scanTag(){
		var loop = $('#loop').val();
		for (var i = 1; i <= loop; i++) {
			$('#count'+i).text('0');
		}
		var material_number = $('#tag').val().substring(4,11);
		var tag = $('#tag').val();
		var data = 
		{
			sLoc: 'SX51',
			location: $('#loc').val(),
			tag:tag,
			materialNumber:material_number
		}
		$.get('{{ url("scan/middle_kensa") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#key').text(result.model);
					$('#tag_material').val(result.tag);
					openSuccessGritter('Success!', result.message);
				}
				else{
					audio_error.play();
					openErrorGritter('Error!', result.message);
				}
			}
			else{
				audio_error.play();
				alert("Disconnected from server");
			}
		});
	}

	function confirm(){
		var tag_material = $('#tag_material').val();
		var loop = $('#loop').val();
		var total = 0;
		var count_ng = 0;
		var ng_name = [];
		var ng_qty = [];
		var count_text = [];
		for (var i = 1; i <= loop; i++) {
			if($('#count'+i).text() > 0){
				ng_qty.push($('#count'+i).text());
				ng_name.push($('#ng'+i).text());
				count_text.push('#count'+i);
				total += parseInt($('#count'+i).text());
				count_ng += 1;
			}
		}

		if(total != 0){
			var data = {
				tag: $('#tag_material').val(),
				location: $('#loc').val(),
				opKensa: $('#opKensa').text(),
				prodDate: $('#prodDate').text(),
				group: $('#group').text(),
				ng_qty: ng_qty,
				ng_name: ng_name,
				count_text: count_text,
			}
			$.post('{{ url("input/ng_middle_kensa") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){
						$('#tag_material').val('');
						$('#key').text('-');
						$('#tag').val('');

						$.each(result.success_count, function( index, value ) {
							$(value).text('0');
						});
						fillResult()
						openSuccessGritter('Success Result!', result.message);
						$('#tag').focus();
						$('#tag').blur();
					}
					else{
						fillResult()
						audio_error.play();
						openErrorGritter('Error!', result.message);
					}
				}
				else{
					audio_error.play();
					alert('Disconnected from server');
				}
			});
		}

		if(total == 0){
			var data = {
				tag: $('#tag_material').val(),
				location: $('#loc').val(),
				opKensa: $('#opKensa').text(),
				prodDate: $('#prodDate').text(),
				group: $('#group').text(),
			}
			$.post('{{ url("input/result_middle_kensa") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){
						$('#tag_material').val('');
						$('#key').text('-');
						$('#tag').val('');
						fillResult()
						openSuccessGritter('Success Result!', result.message);
						$('#tag').focus();
						$('#tag').blur();
					}
					else{
						fillResult()
						audio_error.play();
						openErrorGritter('Error!', result.message);
					}
				}
				else{
					audio_error.play();
					alert('Disconnected from server');
				}
			});
		}
	}

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

	function checkIn(){
		val = $("#incoming_op").val();

		if($('#incoming_op').val() != ""){
			if (val.length < 8) {
				openErrorGritter('Error!', 'Please enter valid NIK');
				return false;
			}

			$('#prodDate').text($('#prod_date').val());
			$('#opKensa').text(val);
			openSuccessGritter('Success!', 'Check in complete');
			$('#modalOperator').modal('hide');
			$('#incoming_op').val('');
			$('#tag').val('');
			fillResult();
			$('#tag').focus();
			$('#tag').blur();
		}
		else{
			audio_error.play();
			openErrorGritter('Error!', 'Please complete the form');
		}
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


</script>
@endsection