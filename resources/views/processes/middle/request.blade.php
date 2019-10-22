@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0;">
	<div class="input-group">
		<span class="input-group-addon"><i class="fa fa-barcode"></i></span>
		<input type="text" class="form-control input-lg" placeholder="Scan Kanban Solder . . ." style="text-align: center" id="tag">
		<span class="input-group-addon"><i class="fa fa-barcode"></i></span>
	</div>

	<table class="table table-bordered" style="color:white; font-size: 2vw" id="main">
		<tr>
			<th width="60%">Material Number</th>
			<td id="material_number"></td>
		</tr>
		<tr>
			<th>Item</th>
			<td id="item"></td>
		</tr>
		<tr>
			<th>Count (Qty) <div style="display: none" id="qty">20</div></th>
			<td id="kanban"></td>
		</tr>
	</table>

	<table class="table table-bordered" id="logs">
		<thead>
			<tr>
				<th>Material</th>
				<th>Material Descriptions</th>
				<th>Item</th>
				<th>Quantity</th>
			</tr>
		</thead>
		<tbody id="bodys">
		</tbody>
	</table>
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

	// var option = "{{$option}}";

	// if (option == "Saxophone") {
	// 	var code = "SX";
	// } else if (option == "Flute") {
	// 	var code = "FL";
	// } else if (option == "Clarinet") {
	// 	var code = "CL";
	// }

	jQuery(document).ready(function() {
		$("#tag").focus();

		drawTable();
		// setInterval(drawTable, 2000);
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	$('#tag').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var str = $("#tag").val();
			// if (str.substring(0, 2) != code) {
			// 	$("#tag").val("");
			// 	audio_error.play();
			// 	openErrorGritter('Error', 'Incorrect item');
			// 	return false;
			// }

			scanTag(str);
			$("#tag").focus();
		}
	});

	function scanTag(tag) {
		var data = {
			material_number:$("#tag").val(),
			quantity:$("#qty").text(),
			item:"{{$option}}"
		}

		$("#material_number").text("");
		$("#item").text("");

		$.get('{{ url("scan/middle/request") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
				drawTable();
				$("#material_number").text(result.datas.material_number);
				$("#item").text(result.datas.origin_group_name);
				$("#kanban").text(result.datas_log.quantity);
				$("#qty").text();
				$("#tag").val("");
				$('#tag').focus();
			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.error);
			}
		});
	}

	function drawTable() {
		var data = {
			option:"{{$option}}"
		}

		$.get('{{ url("fetch/middle/request") }}', data, function(result, status, xhr){
			if(result.status){
				var tableData = "";
				$('#logs').DataTable().destroy();
				$("#bodys").empty();
				$.each(result.datas, function(index, value) {
					tableData += "<tr>";
					tableData += "<td>"+value.model+" "+value.key+"</td>";
					tableData += "<td>"+value.material_description+"</td>";
					tableData += "<td>"+value.item+"</td>";
					tableData += "<td>"+value.quantity+"</td>";
					tableData += "</tr>";
				})

				$("#bodys").append(tableData);
				$('#logs').DataTable({
					"paging": true,
					'searching': false,
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'ordering': false,
					'lengthChange': false,
					'info': false,
					'sPaginationType': "full_numbers",
					"columnDefs": [ {
						"targets": 0,
						"orderable": false
					} ]
				});
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
</script>
@endsection