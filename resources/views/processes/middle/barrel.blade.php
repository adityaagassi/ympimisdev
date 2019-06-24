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
	<ol class="breadcrumb">
		<li>

		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">
	<div class="row">
		<input type="hidden" id="hpl" value="{{ $hpl }}">
		<input type="hidden" id="mrpc" value="{{ $mrpc }}">
		<div class="col-xs-12">
			<center>
				<span style="font-weight: bold; font-size: 16px;">Machine No: </span> <span id="no_machine" style="font-weight: bold; font-size: 26px; color: red;"></span>
				<span style="font-size: 16px;">Max Capacity: </span> <span id="capacity" style="font-size: 26px; color: red;"></span><span style="font-size: 16px; color: red;"> Kanban</span>
			</center>
			<table id="tableJob" class="table table-bordered table-striped">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 1%;">No</th>
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
				<span style="font-weight: bold; font-size: 16px;">Total: </span><span id="total_kanban" style="font-weight: bold; font-size: 26px; color: red;"></span><span style="font-size: 16px; color: red;"> Kanban</span>
				<span style="font-weight: bold; font-size: 16px;">Material Picked: </span><span id="total_picked" style="font-weight: bold; font-size: 26px; color: red;">0</span><span style="font-size: 16px; color: red;"> Kanban</span>
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

	var picked = 0;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		setInterval(function(){
			fillMachine();
		}, 1000);
		fillTable();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function count_picked(element){

		if(element.checked == true) {
			picked +=1;
		}
		else {
			picked -=1;	
		}

		$("#total_picked").text(picked);
	}

	function printJob(){
		if($('#no_machine').text() == 'FULL'){
			audio_error.play();
			openErrorGritter('Error!', 'No Machine Available');
			return false;
		}
		if($('#total_picked').text() == 0){
			audio_error.play();
			openErrorGritter('Error!', 'No Material Picked');
			return false;
		}

		if($('#total_picked').text() < $('#total_kanban').text()){
			alert('asdad');
		}
		else{
			var d = [];
			$("input[type=checkbox]:checked").each(function() {
				d.push(this.id);
			});

			var data = {
				tag : d,
				mrpc : $('#mrpc').val(),
				hpl : $('#hpl').val(),
				no_machine : $('#no_machine').text(),
			}

			$.post('{{ url("print/middle/barrel") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){
						openSuccessGritter('Succes', result.message);
						fillTable();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						fillTable();
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

	function fillMachine(){
		var data = {
			mrpc : $('#mrpc').val(),
			hpl : $('#hpl').val()
		}
		$.get('{{ url("fetch/middle/barrel_machine") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#no_machine').html("");
					$('#capacity').html("");
					$('#no_machine').html(result.no_machine);
					$('#capacity').html(result.capacity);
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

	function fillTable(){
		var data = {
			mrpc : $('#mrpc').val(),
			hpl : $('#hpl').val()
		}
		$.get('{{ url("fetch/middle/barrel") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#no_machine').html(result.no_machine);
					$('#capacity').html(result.capacity);

					var tableJobBody = "";
					$('#tableJobBody').html("");
					$('#total_kanban').html("");
					var c = 1;
					var total_kanban = 0;

					$.each(result.jobs, function(index, value) {
						tableJobBody += "<tr>";
						tableJobBody += "<td>"+ c +"</td>";
						tableJobBody += "<td>"+ value.jig +"</td>";
						tableJobBody += "<td>"+ value.key +"</td>";
						if(value.model !== null){
							tableJobBody += "<td>"+ value.model +"</td>";
							tableJobBody += "<td>"+ value.surface +"</td>";
							tableJobBody += "<td>"+ value.material_child +"</td>";
							tableJobBody += "<td>"+ value.material_description +"</td>";
							tableJobBody += "<td><input type='checkbox' id='" + value.tag+ "' onclick='count_picked(this)'></center></td>";
							total_kanban += 1;
						}					
						tableJobBody += "</tr>";
						c += 1;
					});
					$('#tableJobBody').append(tableJobBody);
					$('#total_kanban').html(total_kanban);
					$('#total_picekd').html(0);

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