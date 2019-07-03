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
	.dataTable > thead > tr > th[class*="sort"]:after{
		content: "" !important;
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

		<input type="hidden" id="hpl" value="{{ $hpl }}">
		<input type="hidden" id="mrpc" value="{{ $mrpc }}">
		<input type="hidden" id="surface" value="{{ $surface }}">
		<div class="col-xs-12">
			<table id="tableJob" class="table table-bordered table-striped" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th style="width: 1%;">No</th>
						<th style="width: 1%;">Key</th>
						<th style="width: 1%;">Model</th>
						<th style="width: 1%;">Surface</th>
						<th style="width: 2%;">Picking Material</th>
						<th style="width: 15%;">Picking Description</th>
						<th style="width: 1%;">Qty</th>
						<th style="width: 1%;">Check</th>
					</tr>
				</thead>
				<tbody id="tableJobBody">
				</tbody>
			</table>
			<center>
				<span style="font-weight: bold; font-size: 20px;">Material Picked: </span>
				<span id="picked" style="font-weight: bold; font-size: 24px; color: red;">0</span>
				<span style="font-weight: bold; font-size: 16px; color: red;">of</span>
				<span id="total" style="font-weight: bold; font-size: 16px; color: red;">0</span>
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

	var total;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		fillTable();
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function count_picked(element){
		if(element.checked == true) {
			total +=1;
		}
		else {
			total--;	
		}

		$("#picked").html(total);
	}

	function printJob(element){

		$("#loading").show();
		$(element).attr('disabled',true);

		var tag = [];
		$("input[type=checkbox]:checked").each(function() {
			tag.push([this.id, this.name]);
		});

		var data = {
			tag : tag,
			code : 'FLANEL',
			surface : $('#surface').val(),
		}

		$.post('{{ url("print/middle/barrel") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$("#loading").hide();
					$(element).removeAttr('disabled');
					openSuccessGritter('Success', result.message);
					fillTable();
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
					var tableJobBody = "";
					$('#tableJobBody').html("");
					$('#tableJob').DataTable().clear();
					$('#tableJob').DataTable().destroy();

					var no = 1;
					$.each(result.queues, function(index, value){
						tableJobBody += '<tr>';
						tableJobBody += '<td>'+no+'</td>';
						tableJobBody += '<td>'+value.key+'</td>';
						tableJobBody += '<td>'+value.model+'</td>';
						tableJobBody += '<td>'+value.surface+'</td>';
						tableJobBody += '<td>'+value.material_child+'</td>';
						tableJobBody += '<td>'+value.material_description+'</td>';
						tableJobBody += '<td>'+value.quantity+'</td>';
						tableJobBody += '<td><input type="checkbox" id="'+value.tag+'" name="PLT" onclick="count_picked(this)"></center></td>';
						tableJobBody += '</tr>';
						no += 1;
					});
					$('#tableJobBody').append(tableJobBody);
					$('#total').html(result.queues.length);
					$('#picked').html(0);
					total = 0;

					$('#tableJob').DataTable({
						'responsive':true,
						"pageLength": 40,
						'paging': true,
						'lengthChange': false,
						'searching': false,
						'ordering': false,
						'order': [],
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
					});
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