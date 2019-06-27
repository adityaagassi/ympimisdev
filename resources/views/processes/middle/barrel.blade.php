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
		<input type="hidden" id="hpl" value="{{ $hpl }}">
		<input type="hidden" id="mrpc" value="{{ $mrpc }}">
		<input type="hidden" id="surface" value="{{ $surface }}">
		<div class="col-xs-12">
			<table id="tableMachine" class="table table-bordered table-striped" style="background-color: rgb(204,255,255);">
				<thead>
					<tr>
						<th onclick="changeColor(this)" id="1" style="padding: 0px; cursor: pointer; width: 1%">Machine 1</th>
						<th onclick="changeColor(this)" id="2" style="padding: 0px; cursor: pointer; width: 1%">Machine 2</th>
						<th onclick="changeColor(this)" id="3" style="padding: 0px; cursor: pointer; width: 1%">Machine 3</th>
						<th onclick="changeColor(this)" id="4" style="padding: 0px; cursor: pointer; width: 1%">Machine 4</th>
						<th onclick="changeColor(this)" id="5" style="padding: 0px; cursor: pointer; width: 1%">Machine 5</th>
						<th onclick="changeColor(this)" id="6" style="padding: 0px; cursor: pointer; width: 1%">Machine 6</th>
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
				<span style="font-weight: bold; font-size: 20px;">No Machine: #</span>
				<span id="machine" style="font-weight: bold; font-size: 24px; color: red;"></span>
				<span style="font-weight: bold; font-size: 20px;">Material Picked: </span>
				<span id="picked" style="font-weight: bold; font-size: 24px; color: red;"></span>
				<span style="font-weight: bold; font-size: 16px; color: red;">/</span>
				<span id="total" style="font-weight: bold; font-size: 16px; color: red;"></span>
			</center>
			<button class="btn btn-primary" style="width: 100%; font-size: 22px; margin-bottom: 30px;" onclick="printJob()"><i class="fa fa-print"></i> PRINT</button>
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
		$(element).css("background-color","rgb(255,0,0)");
		$("#machine").html(element.id);
	}

	function headCreate() {
		$.get('{{ url("fetch/middle/barrel_machine_status") }}', function(result, status, xhr){
			$.each(result.machine_stat, function(index, value) {
				$("#"+value.machine).empty();
				var jam = "" , menit = "";
				if (value.jam != 0) {
					jam = value.jam +" h";
				}

				if (value.menit != 0 && value.jam != 0) {
					menit = value.menit +" min";
				}

				detik = value.detik + " sec";

				$("#"+value.machine).append("Machine "+value.machine+"<br>"+value.status.toUpperCase()+"<br>"+jam+" "+menit+" "+detik);
			})
		})
	}

	function printJob(){

		if ($("#machine").text() == "") {
			openErrorGritter('Error', 'No Machine Selected');
			return false;
		}

		if($('input[type=checkbox]:checked').length !== $('input[type=checkbox]').length){
			alert("cek tidak semua");
		}
		else{
			var d = [];
			$("input[type=checkbox]:checked").each(function() {
				d.push([this.id, this.name]);
			});

			var data = {
				tag : d,
				// mrpc : $('#mrpc').val(),
				// hpl : $('#hpl').val(),
				surface : $('#surface').val(),
				no_machine : $('#machine').text(),
			}

			$.post('{{ url("print/middle/barrel") }}', data, function(result, status, xhr){
				if(xhr.status == 200){
					if(result.status){
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
						audio_error.play();
						openErrorGritter('Error', result.message);
					}
				}
				else{
					audio_error.play();
					alert('Disconnected from server');
					fillTable();
				}
			});

		}
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
					var arr = result.queues;
					var tag = [];
					var jig_arr = [];
					var jig_baru = [];

					for (var i = 0; i < 8; i++) {
						var first_arr = [];
						var tot_spring = 0;
						var springs = 0;
						$.each(arr, function(index, value) {
							if($.inArray(value.tag, tag) == -1){
								if(first_arr.length == 0){
									first_arr.push([value.hpl, value.spring]);
								}
								if(value.hpl == first_arr[0][0] && value.spring == first_arr[0][1]){
									tot_spring += value.lot;
									if(tot_spring <= 4){
										jig_arr.push([value.hpl, value.spring, value.key, value.surface, value.tag, value.lot, value.model, value.material_child, value.material_description]);
										springs += value.lot;
										tag.push(value.tag);
									}
								}

								if (springs > 3) {
									jig_baru.push();
								}

							}
						});
					}

					console.log(jig_arr);
					return false;

					var jig = 1;
					var tmp = 0;

					$('#tableJobBody').html('');
					var tableJobBody = "";
					for (var z = 0; z < jig_arr.length; z++) {
						tmp += jig_arr[z][5];
						if(tmp >= 4) {jig++; tmp = 0;}
						tableJobBody += '<tr>';
						tableJobBody += '<td>'+jig+'</td>';
						tableJobBody += '<td>'+jig_arr[z][2]+'</td>';
						tableJobBody += '<td>'+jig_arr[z][6]+'</td>';
						tableJobBody += '<td>'+jig_arr[z][3]+'</td>';
						tableJobBody += '<td>'+jig_arr[z][7]+'</td>';
						tableJobBody += '<td>'+jig_arr[z][8]+'</td>';
						tableJobBody += '<td><input type="checkbox" id="'+jig_arr[z][4]+'" name="'+jig+'" onclick="count_picked(this)" checked></center></td>';
						tableJobBody += '</tr>';
					}
					$('#tableJobBody').append(tableJobBody);
					$('#total').html(jig_arr.length);
					$('#picked').html(jig_arr.length);
					total = jig_arr.length;
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