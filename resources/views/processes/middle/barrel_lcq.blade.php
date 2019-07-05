@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
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
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
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
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
			<p style="position: absolute; color: White; top: 45%; left: 35%;">
				<span style="font-size: 40px">Loading, please wait...<i class="fa fa-spin fa-refresh"></i></span>
			</p>
		</div>
		<div id="error" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(255,102,102); z-index: 30001; opacity: 0.8;">
			<p id="pError" style="position: absolute; color: White; top: 45%; left: 35%;">

			</p>
		</div>

		<input type="hidden" id="hpl" value="{{ $hpl }}">
		<input type="hidden" id="mrpc" value="{{ $mrpc }}">
		<input type="hidden" id="surface" value="{{ $surface }}">
		<input type="hidden" id="code">
		<div class="col-xs-12">
			<table id="tableMachine" class="table table-bordered table-striped" style="background-color: rgb(204,255,255);">
				<thead>
					<tr>
						<th onclick="changeColor(this)" id="1" style="padding: 0px; cursor: pointer; width: 1%">Machine #1</th>
						<th onclick="changeColor(this)" id="2" style="padding: 0px; cursor: pointer; width: 1%">Machine #2</th>
						<th onclick="changeColor(this)" id="3" style="padding: 0px; cursor: pointer; width: 1%">Machine #3</th>
						<th onclick="changeColor(this)" id="4" style="padding: 0px; cursor: pointer; width: 1%">Machine #4</th>
						<th onclick="changeColor(this)" id="5" style="padding: 0px; cursor: pointer; width: 1%">Machine #5</th>
						<th onclick="changeColor(this)" id="6" style="padding: 0px; cursor: pointer; width: 1%">Machine #6</th>
					</tr>
				</thead>
			</table>
		</div>
		<div class="col-xs-12">
			<table id="tableJob" class="table table-bordered table-striped" style="margin-bottom: 0;">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 1%;">Jig</th>
						<th style="width: 1%;">Key</th>
						<th style="width: 1%;">Model</th>
						<th style="width: 1%;">Surface</th>
						<th style="width: 2%;">Picking Material</th>
						<th style="width: 15%;">Picking Description</th>
						<th style="width: 1%;">Check</th>
					</tr>
				</thead>
				<tbody id="tableJobBody">
				</tbody>
			</table>
			<center>
				<span style="font-weight: bold; font-size: 20px;">No. Machine: #</span>
				<span id="machine" style="font-weight: bold; font-size: 24px; color: red;"></span>
				<span style="font-weight: bold; font-size: 20px;">Material Picked: </span>
				<span id="picked" style="font-weight: bold; font-size: 24px; color: red;"></span>
				<span style="font-weight: bold; font-size: 16px; color: red;">of</span>
				<span id="total" style="font-weight: bold; font-size: 16px; color: red;"></span>
			</center>
			<button class="btn btn-primary" style="width: 100%; font-size: 22px; margin-bottom: 30px;" onclick="printJob(this)"><i class="fa fa-print"></i> PRINT</button>
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

	var jig_arr = [];
	var total;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		fillTable();
		headCreate();
		setInterval(headCreate, 1000);
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function changeColor(element) {
		$("#1").css("background-color","rgb(204,255,255)");
		$("#2").css("background-color","rgb(204,255,255)");
		$("#3").css("background-color","rgb(204,255,255)");
		$("#4").css("background-color","rgb(204,255,255)");
		$("#5").css("background-color","rgb(204,255,255)");
		$("#6").css("background-color","rgb(204,255,255)");
		$(element).css("background-color","#ff4d4d");
		$("#machine").html(element.id);
	}

	function headCreate() {
		var antrian = 0;
		var total = 0;
		
		$.get('{{ url("fetch/middle/barrel_machine_status") }}', function(result, status, xhr){
			$.each(result.machine_stat, function(index, value) {
				$("#"+value.machine).empty();
				var jam = "" , menit = "";
				if (value.jam != 0) {
					jam = ("0" + value.jam).slice(-2)+"H";
				}

				if (value.menit != 0) {
					menit = ("0" + value.menit).slice(-2)+"M";
				}

				detik = ("0" + value.detik).slice(-2)+"S";

				$("#"+value.machine).append("Machine #"+value.machine+"<br>"+value.status.toUpperCase()+" : "+jam+" "+menit+" "+detik);
			})
		})
	}

	function printJob(element){	
		if($('#code').val() == 'BARREL'){
			if ($("#machine").text() == "") {
				openErrorGritter('Error', 'No Machine Selected');
				return false;
			}
			else {
				$("#loading").show();
				$(element).attr('disabled',true);
			}
		}

		// if($('input[type=checkbox]:checked').length !== $('input[type=checkbox]').length){
			if(confirm("ID slip will be printed, contiune?")){
				var d = [];
				$("input[type=checkbox]:checked").each(function() {
					d.push([this.id, this.name]);
				});

				var data = {
					tag : d,
					code : $('#code').val(),
					surface : $('#surface').val(),
					no_machine : $('#machine').text(),
				}

				$.post('{{ url("print/middle/barrel") }}', data, function(result, status, xhr){
					if(xhr.status == 200){
						if(result.status){
							$("#loading").hide();
							$(element).removeAttr('disabled');
							openSuccessGritter('Success', result.message);
							fillTable();
							$("#1").css("background-color","rgb(204,255,255)");
							$("#2").css("background-color","rgb(204,255,255)");
							$("#3").css("background-color","rgb(204,255,255)");
							$("#4").css("background-color","rgb(204,255,255)");
							$("#5").css("background-color","rgb(204,255,255)");
							$("#6").css("background-color","rgb(204,255,255)");
							$('#machine').text('');
						}
						else{
							$("#loading").hide();
							$(element).removeAttr('disabled');
							audio_error.play();
							openErrorGritter('Error', result.message);
						}
					}
					else{
						$("#loading").hide();
						$(element).removeAttr('disabled');
						audio_error.play();
						alert('Disconnected from server');
						fillTable();
					}
				});
			}
		// }
		// else{
		// 	var d = [];
		// 	$("input[type=checkbox]:checked").each(function() {
		// 		d.push([this.id, this.name]);
		// 	});

		// 	var data = {
		// 		tag : d,
		// 		code : $('#code').val(),
		// 		surface : $('#surface').val(),
		// 		no_machine : $('#machine').text(),
		// 	}

		// 	$.post('{{ url("print/middle/barrel") }}', data, function(result, status, xhr){
		// 		if(xhr.status == 200){
		// 			if(result.status){
		// 				$("#loading").hide();
		// 				$(element).removeAttr('disabled');
		// 				openSuccessGritter('Success', result.message);
		// 				fillTable();
		// 				$("#1").css("background-color","rgb(204,255,255)");
		// 				$("#2").css("background-color","rgb(204,255,255)");
		// 				$("#3").css("background-color","rgb(204,255,255)");
		// 				$("#4").css("background-color","rgb(204,255,255)");
		// 				$("#5").css("background-color","rgb(204,255,255)");
		// 				$("#6").css("background-color","rgb(204,255,255)");
		// 				$('#machine').text('');
		// 			}
		// 			else{
		// 				$("#loading").hide();
		// 				$(element).removeAttr('disabled');
		// 				audio_error.play();
		// 				openErrorGritter('Error', result.message);
		// 			}
		// 		}
		// 		else{
		// 			$("#loading").hide();
		// 			$(element).removeAttr('disabled');
		// 			audio_error.play();
		// 			alert('Disconnected from server');
		// 			fillTable();
		// 		}
		// 	});
		// }
	}

	function fillTable(){
		var hpl = $('#hpl').val().split(',');
		var data = {
			mrpc : $('#mrpc').val(),
			hpl : hpl,
			surface : $('#surface').val(),
		}

		$.get('{{ url("fetch/middle/barrel") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$('#code').val(result.code);
					var jig_arr = [];
					var arr = result.queues;
					if(result.code == 'BARREL'){
						var tag = [];
						var jig = 1;
						for (var i = 0; i < 8; i++) {
							var first_arr = [];
							var tot_spring = 0;
							var springs = 0;
							$.each(arr, function(index, value) {
								if(value.spring !== 'FLANEL'){
									if($.inArray(value.tag, tag) == -1){
										if(first_arr.length == 0){
											first_arr.push([value.hpl, value.spring]);
										}
										if(value.spring == null){
											$("#error").show();
											$("#pError").html('<span style="font-size: 40px"><i class="fa fa-unlink"></i>Error!<br>There is key without jig data.</span>');
											return false;
										}
										if(value.hpl == first_arr[0][0] && value.spring == first_arr[0][1]){
											tot_spring += value.lot;
											if(tot_spring <= 4){
												jig_arr.push([value.hpl, value.spring, value.key, value.surface, value.tag, value.lot, value.model, value.material_child, value.material_description, jig]);
												springs += value.lot;
												tag.push(value.tag);
											}
										}
									}
								}
							});
							jig +=1;
						}

						$('#tableJobBody').html('');
						var tableJobBody = "";
						for (var z = 0; z < jig_arr.length; z++) {
							tableJobBody += '<tr>';
							tableJobBody += '<td>'+jig_arr[z][9]+'</td>';
							tableJobBody += '<td>'+jig_arr[z][2]+'</td>';
							tableJobBody += '<td>'+jig_arr[z][6]+'</td>';
							tableJobBody += '<td>'+jig_arr[z][3]+'</td>';
							tableJobBody += '<td>'+jig_arr[z][7]+'</td>';
							tableJobBody += '<td>'+jig_arr[z][8]+'</td>';
							tableJobBody += '<td><input type="checkbox" id="'+jig_arr[z][4]+'" name="'+jig_arr[z][9]+'" onclick="count_picked(this)" checked></center></td>';
							tableJobBody += '</tr>';
						}
						$('#tableJobBody').append(tableJobBody);
						$('#total').html(jig_arr.length);
						$('#picked').html(jig_arr.length);
						total = jig_arr.length;	
					}
					else{
						$.each(arr, function(index, value) {
							if(value.spring == 'FLANEL'){
								jig_arr.push([value.hpl, value.spring, value.key, value.surface, value.tag, value.lot, value.model, value.material_child, value.material_description]);
							}
							else{
								return false;
							}
						});
						$('#tableJobBody').html('');
						var tableJobBody = "";
						for (var z = 0; z < jig_arr.length; z++) {
							tableJobBody += '<tr>';
							tableJobBody += '<td>'+jig_arr[z][1]+'</td>';
							tableJobBody += '<td>'+jig_arr[z][2]+'</td>';
							tableJobBody += '<td>'+jig_arr[z][6]+'</td>';
							tableJobBody += '<td>'+jig_arr[z][3]+'</td>';
							tableJobBody += '<td>'+jig_arr[z][7]+'</td>';
							tableJobBody += '<td>'+jig_arr[z][8]+'</td>';
							tableJobBody += '<td><input type="checkbox" id="'+jig_arr[z][4]+'" name="'+jig_arr[z][1]+'" onclick="count_picked(this)" checked></center></td>';
							tableJobBody += '</tr>';
						}
						$('#tableJobBody').append(tableJobBody);
						$('#total').html(jig_arr.length);
						$('#picked').html(jig_arr.length);
						total = jig_arr.length;
					}					
				}
				else{
					audio_error.play();
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				audio_error.play();
				alert('Disconnected from server');
			}
		});
	}

	function count_picked(element){
		if(element.checked == true) {
			total +=1;
		}
		else {
			total--;	
		}

		$("#picked").html(total);
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