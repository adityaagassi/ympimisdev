@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
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
		padding:3px;
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
	<input type="hidden" id="hpl" value="{{ $hpl }}">
	<input type="hidden" id="mrpc" value="{{ $mrpc }}">
	<h1>
		{{ $title }}
		<small>WIP Control <span class="text-purple"> 仕掛品管理</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="col-sx-12">
		<div class="row">
			<div class="col-xs-6" style="left: 25%;">
				<div class="input-group">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
						<i class="glyphicon glyphicon-qrcode"></i>
					</div>
					<input type="text" style="text-align: center;" class="form-control" id="qr" placeholder="Scan QR Here...">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
						<i class="glyphicon glyphicon-qrcode"></i>
					</div>
				</div>
				<br>
			</div>
			<div class="col-xs-12">
				<h4>Queue Return :</h4>
				<table class="table table-bordered">
					<thead style="background-color: rgb(126,86,134); color: #FFD700;">
						<tr>
							<th style="width: 1%; padding: 0;">No</th>
							<th style="width: 2%; padding: 0;">Tag</th>
							<th style="width: 1%; padding: 0;">Model</th>
							<th style="width: 1%; padding: 0;">Key</th>
							<th style="width: 1%; padding: 0;">Surface</th>
							<th style="width: 1%; padding: 0;">Material</th>
							<th style="width: 7%; padding: 0;">Description</th>
							<th style="width: 1%; padding: 0;">Quantity</th>
							<th style="width: 3%; padding: 0;">Created At</th>
							<th style="width: 1%; padding: 0;">Action</th>
						</tr>
					</thead>
					<tbody id="returnTableBody">
					</tbody>
				</table>
			</div>
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

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('#qr').focus();
		filltable();

		$('#qr').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				var qr = $("#qr").val();
				// if(confirm("Add "+qr+" to queues?")){
					returnBarrel(qr);
					$("#qr").val("");
					$('#qr').focus();
					openSuccessGritter('Success', 'Success Adding to Queues');
				// }
			}
		});
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function returnBarrel(qr) {
		data = {
			qr : qr
		};

		$.post('{{ url("post/middle_return/barrel_return") }}', data, function(result, status, xhr){
			filltable();
		});
	}

	function filltable() {
		var hpl = $('#hpl').val().split(',');
		var data = {
			mrpc : $('#mrpc').val(),
			hpl : hpl,
		}
		$.get('{{ url("fetch/middle_return/barrel_return") }}', data, function(result, status, xhr){
			$("#returnTableBody").empty();
			var body = "", no = 1;
			$.each(result.datas, function(index, value){
				body += "<tr>";
				body += "<td>"+no+"</td>";
				body += "<td>"+value.tag+"</td>";
				body += "<td>"+value.model+"</td>";
				body += "<td>"+value.key+"</td>";
				body += "<td>"+value.surface+"</td>";
				body += "<td>"+value.material_number+"</td>";
				body += "<td>"+value.material_description+"</td>";
				body += "<td>"+value.quantity+"</td>";
				body += "<td>"+value.created_at+"</td>";
				body += "<td><button class='btn btn-xs btn-danger' onclick='toInventories(\""+value.tag+"\",\""+value.remark+"\",\""+value.material_number+"\","+value.quantity+")'><i class='fa fa-rotate-left'></i> Cancel</td>";
				body += "</tr>";
				no++;
			})

			$("#returnTableBody").append(body);

		});
	}

	function toInventories(tag, remark, material, qty) {
		if(confirm("Return "+tag+" from queues to inventories?")){
			var location =  remark.split("+");
			var data = {
				tag : tag,
				material : material,
				location : location[1],
				quantity : qty
			}
			$.post('{{ url("post/middle_return/return_inventory") }}', data, function(result, status, xhr){
				openSuccessGritter('Success', 'Success Return to inventories');
				filltable();
			})
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
