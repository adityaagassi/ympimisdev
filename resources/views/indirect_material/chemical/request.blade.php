@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	table {
		table-layout:fixed;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	td:hover {
		overflow: visible;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
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
		border:1px solid rgb(211,211,211);
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}

	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
	
	#loading { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<button class="btn btn-danger btn-sm pull-right" data-toggle="modal" data-target="#modalOut" style="margin-right: 5px">
		<i class="fa fa-trash"></i>&nbsp;&nbsp;Scan Habis
	</button>

	<h1>
		{{ $title }}<span class="text-purple"> {{ $title_jp }}</span>
		<small id="location_text"></small>
	</h1>
	
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="row">
				<div class="col-xs-12">
					<div class="box box-danger">
						<div class="box-body">
							<div class="col-xs-12">
								<table class="table table-hover table-bordered table-striped" id="tableList">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 5%;">Due date</th>
											<th style="width: 15%;">Category</th>
											<th style="width: 20%;">Larutan/Bak</th>
											<th style="width: 5%;">Material</th>
											<th style="width: 30%;">Description</th>
											<th style="width: 15%;">Storage Loc.</th>
											<th style="width: 5%;">Qty</th>
											<th style="width: 1%;">Bun</th>
										</tr>					
									</thead>
									<tbody id="tableBodyList">
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>

				<div class="col-xs-6">
					<div class="row">
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 20px;">Request</span>
							<input type="text" id="schedule_id" hidden>
						</div>
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Larutan/Bak:</span>
							<input type="text" id="larutan" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" readonly>
						</div>


						<div class="col-xs-7">
							<span style="font-weight: bold; font-size: 16px;">Category:</span>
							<input type="text" id="category" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" readonly>
						</div>		

						<div class="col-xs-5">
							<span style="font-weight: bold; font-size: 16px;">Material Number:</span>
							<input type="text" id="material_number" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" readonly>
						</div>

						<div class="col-xs-7">
							<span style="font-weight: bold; font-size: 16px;">Storage Location:</span>
							<input type="text" id="storage_location" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" readonly>
						</div>
						<div class="col-xs-3">
							<span style="font-weight: bold; font-size: 16px;">Request Qty:</span>
							<input type="text" id="quantity" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" readonly>
						</div>
						<div class="col-xs-2">
							<span style="font-weight: bold; font-size: 16px;">Uom:</span>
							<input type="text" id="bun" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" readonly>
						</div>
						
						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 16px;">Material Description:</span>
							<input type="text" id="material_description" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" readonly>
						</div>

						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 20px;">Stock</span>
						</div>
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Stock Qty:</span>
							<input type="text" id="stock" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" readonly>
						</div>
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Out:</span>
							<input type="text" id="out" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" readonly>
						</div>
					</div>
				</div>

				<div class="col-xs-6">
					<div class="row">						
						<div class="col-xs-12" style="margin-top: 5%;">
							<br>
							<button class="btn btn-primary" data-toggle="modal"  data-target="#modalScan" style="font-size: 30px; width: 100%; font-weight: bold; padding: 0;"><span><i class="fa fa-camera"></i></span>&nbsp;&nbsp;&nbsp;SCAN
							</button>
						</div>

						<div class="col-xs-12">
							<table id="pick" class="table table-bordered table-hover" style="width: 100%;">
								<thead style="background-color: white">
									<tr>
										<th style="width: 20%">QR Code</th>
										<th style="width: 15%">Material</th>
										<th style="width: 45%">Material Description</th>
										<th style="width: 10%">Status</th>
										<th style="width: 10%">Delete</th>
									</tr>
								</thead>
								<tbody id="pick-body" style="background-color: white">
								</tbody>
							</table>
						</div>

						<div class="col-xs-12" style="margin-top: 5%;">
							<button class="btn btn-success" onclick="saveChm()" style="font-size: 30px; width: 100%; font-weight: bold; padding: 0;">SUBMIT
							</button>
						</div>

					</div>
				</div>
			</div>
		</div>

		{{-- <div class="col-xs-12" style="margin-top: 2%;">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
					<li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Daily Picking Result</a></li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="tab_1">
						<table id="daily_pick" class="table table-bordered table-striped table-hover" style="width: 100%;">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 2%">Material Number</th>
									<th style="width: 5%">Material Description</th>
									<th style="width: 2%">Quantity</th>
									<th style="width: 1%">Uom</th>
									<th style="width: 3%">Picked By</th>
									<th style="width: 3%">Picked At</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div> --}}
	</div>

	<div class="modal fade" id="modalScan">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<center><h2 style="background-color: #dd4b39; padding: 1%;">Scan QR Code</h2></center>
					<div class="modal-body table-responsive no-padding">
						<div id='scanner' class="col-xs-12">
							<div class="col-xs-12">
								<center>
									<div id="loadingMessage">
										🎥 Unable to access video stream
										(please make sure you have a webcam enabled)
									</div>
									<canvas style="height:300px;" id="canvas" hidden></canvas>
									<div id="output" hidden>
										<div id="outputMessage">No QR code detected.</div>
									</div>
								</center>
							</div>									
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalOut">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<center><h2 style="background-color: #f44336; padding: 1%;">Scan QR Code Out</h2></center>
					<div class="modal-body table-responsive no-padding">
						<div id='scannerOut' class="col-xs-12">
							<div class="col-xs-12">
								<center>
									<div id="loadingMessageOut">
										🎥 Unable to access video stream
										(please make sure you have a webcam enabled)
									</div>
									<canvas style="height:300px;" id="canvasOut" hidden></canvas>
									<div id="outputOut" hidden>
										<div id="outputMessageOut">No QR code detected.</div>
									</div>
								</center>
							</div>									
						</div>
						<div id="confirmOut" style="width:100%;">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalLocation">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label for="exampleInputEmail1">Location</label>
							<select class="form-control select2" id='location' onchange="changeLoc()" data-placeholder="Select Location" style="width: 100%;">
								<option value="">Select Location</option>
								@foreach($locations as $location)
								<option value="{{ $location->id }}">{{ $location->section }} - {{ $location->location }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


</section>


@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jsQR.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.select2').select2();

		$('#modalLocation').modal({
			backdrop: 'static',
			keyboard: false
		});

		// $('#pick').hide();


	});

	var vdo;

	function stopScan() {
		$('#modalScan').modal('hide');
		$('#modalOut').modal('hide');
	}

	function videoOff() {
		vdo.pause();
		vdo.src = "";
		vdo.srcObject.getTracks()[0].stop();
	}

	function videoOn() {
		vdo.pause();
		vdo.src = "";
		vdo.srcObject.getTracks()[0].stop();
	}

	$('#modalOut').on('shown.bs.modal', function(){
		showOut('123');
		$('#confirmOut').html("");
		
	});	

	$('#modalOut').on('hidden.bs.modal', function () {
		videoOff();
	});

	function showOut(kode) {
		$('#scannerOut').show();

		var video = document.createElement("video");
		vdo = video;
		var canvasElement = document.getElementById("canvasOut");
		var canvas = canvasElement.getContext("2d");
		var loadingMessage = document.getElementById("loadingMessageOut");

		var outputContainer = document.getElementById("outputOut");
		var outputMessage = document.getElementById("outputMessageOut");

		function drawLine(begin, end, color) {
			canvas.beginPath();
			canvas.moveTo(begin.x, begin.y);
			canvas.lineTo(end.x, end.y);
			canvas.lineWidth = 4;
			canvas.strokeStyle = color;
			canvas.stroke();
		}

		navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
			video.srcObject = stream;
			video.setAttribute("playsinline", true);
			video.play();
			requestAnimationFrame(tick);
		});

		function tick() {
			loadingMessage.innerText = "⌛ Loading video..."
			if (video.readyState === video.HAVE_ENOUGH_DATA) {
				loadingMessage.hidden = true;
				canvasElement.hidden = false;

				canvasElement.height = video.videoHeight;
				canvasElement.width = video.videoWidth;
				canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
				var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
				var code = jsQR(imageData.data, imageData.width, imageData.height, {
					inversionAttempts: "dontInvert",
				});

				if (code) {
					drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
					drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
					drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
					drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
					outputMessage.hidden = true;
					videoOff();

					checkOut(video, code.data);

				} else {
					outputMessage.hidden = false;
				}
			}
			requestAnimationFrame(tick);
		}
	}

	function checkOut(video, code) {
		$('#scannerOut').hide();

		var location = $('#location').val();

		var data = {
			qr : code,
			location : location
		}

		$.get('{{ url("fetch/check_out") }}', data, function(result, status, xhr){
			if(result.status){
				var re = "";
				$('#confirmOut').html("");
				re += '<table style="text-align: center; width:100%;"><tbody>';
				re += '<tr><td style="font-size: 36px; font-weight: bold; background-color:black; color:white;" colspan="2">'+result.data.qr_code+'</td></tr>';
				re += '<tr><td style="font-size: 36px; font-weight: bold;" colspan="2">'+result.data.material_number+'</td></tr>';
				re += '<tr><td style="font-size: 26px; font-weight: bold;" colspan="2">'+result.data.material_description+'</td></tr>';
				re += '<tr>';	
				re += '<td><button class="btn btn-danger" style="width: 95%; font-size: 30px; font-weight:bold;" data-dismiss="modal">CANCEL</button></td>';
				re += '<td><button id="'+result.data.qr_code+'" class="btn btn-success" style="width: 95%; font-size: 30px; font-weight:bold;" onclick="confirmOut(id)">SUBMIT</button></td>';
				re += '</tr>';
				re += '</tbody></table>';

				$('#confirmOut').append(re);
			}
			else{
				$('#confirmOut').html("");
				showCheck();
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
			}

		});
	}

	function confirmOut(id) {
		var data = {
			qr : id

		}

		$.post('{{ url("delete/chm_out") }}', data, function(result, status, xhr){
			if(result.status){
				$('#scanner').hide();
				$('#modalOut').modal('hide');
				$(".modal-backdrop").remove();

				openSuccessGritter('Success', result.message);		
			}else{
				
				openErrorGritter('Error!', result.message);
			}
		});
	}



	$('#modalScan').on('shown.bs.modal', function(){
		showCheck('123');
	});	

	$('#modalScan').on('hidden.bs.modal', function () {
		videoOff();
	});

	function showCheck(kode) {
		$('#scanner').show();

		var video = document.createElement("video");
		vdo = video;
		var canvasElement = document.getElementById("canvas");
		var canvas = canvasElement.getContext("2d");
		var loadingMessage = document.getElementById("loadingMessage");

		var outputContainer = document.getElementById("output");
		var outputMessage = document.getElementById("outputMessage");

		function drawLine(begin, end, color) {
			canvas.beginPath();
			canvas.moveTo(begin.x, begin.y);
			canvas.lineTo(end.x, end.y);
			canvas.lineWidth = 4;
			canvas.strokeStyle = color;
			canvas.stroke();
		}

		navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
			video.srcObject = stream;
			video.setAttribute("playsinline", true);
			video.play();
			requestAnimationFrame(tick);
		});

		function tick() {
			loadingMessage.innerText = "⌛ Loading video..."
			if (video.readyState === video.HAVE_ENOUGH_DATA) {
				loadingMessage.hidden = true;
				canvasElement.hidden = false;

				canvasElement.height = video.videoHeight;
				canvasElement.width = video.videoWidth;
				canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
				var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
				var code = jsQR(imageData.data, imageData.width, imageData.height, {
					inversionAttempts: "dontInvert",
				});

				if (code) {
					drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
					drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
					drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
					drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
					outputMessage.hidden = true;
					videoOff();

					checkQR(video, code.data);

				} else {
					outputMessage.hidden = false;
				}
			}
			requestAnimationFrame(tick);
		}
	}

	function checkQR(video, code) {
		var material_number = $('#material_number').val();
		var location = $('#location').val();
		var schedule_id = $('#schedule_id').val();

		var data = {
			qr : code,
			material_number : material_number,
			location : location,
			schedule_id : schedule_id
		}

		$.get('{{ url("fetch/check_qr") }}', data, function(result, status, xhr){
			if(result.status){
				fillPicked();
				openSuccessGritter('Success', result.message);

			}else{
				openErrorGritter('Error!', result.message);
			}

		});

		$('#scanner').hide();
		$('#modalScan').modal('hide');
		$(".modal-backdrop").remove();
	}

	function fillPicked(){
		// $('#pick').show();

		var location = $('#location').val();
		var schedule_id = $('#schedule_id').val();

		var data = {
			location : location,
			schedule_id : schedule_id
		}

		$('#pick').DataTable().destroy();
		var table = $('#pick').DataTable( {
			'paging'        : true,
			'dom': 'Bfrtip',
			'responsive': true,
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'buttons': {
				buttons:[]
			},
			'lengthChange': true,
			'searching': false,
			'ordering' : false,
			'info': false,
			'autoWidth' : true,
			"paging": false,
			"bJQueryUI": true,
			"bPaginate": false,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/chm_picked") }}",
				"data" : data,
			},
			"columns": [
			{ "data": "qr_code" },
			{ "data": "material_number" },
			{ "data": "material_description" },
			{ "data": "remark" },
			{ "data": "delete" }
			]
		});
	}

	function deletePicked(id) {
		data = {
			id: id
		}

		if(confirm("Apa anda yakin akan menghapus data?")){
			$.post('{{ url("delete/chm_picked") }}', data,  function(result, status, xhr){
				if(result.status){
					fillPicked();
					openSuccessGritter('Success', result.message);

				}else{
					openErrorGritter('Error!', result.message);
				}
			});
		}		
	}

	function saveChm() {
		var location = $('#location').val();
		var schedule_id = $('#schedule_id').val();

		var data = {
			location : location,
			schedule_id : schedule_id
		}

		if(confirm("Apa anda ingin yakin menyimpan pengambilan chemical ?\nData yang disimpan tidak bisa dikembalikan")){
			$.post('{{ url("input/chm_picked") }}', data,  function(result, status, xhr){
				if(result.status){
					fillTableList();
					fillPicked();

					$('#schedule_id').val('');
					$('#category').val('');
					$('#larutan').val('');
					$('#material_number').val('');
					$('#quantity').val('');
					$('#bun').val('');
					$('#material_description').val('');
					$('#storage_location').val('');
					$('#stock').val('');
					$('#out').val('');

					openSuccessGritter('Success', result.message);

				}else{
					openErrorGritter('Error!', result.message);
				}
			});
		}
		
	}


	function changeLoc() {
		var location = $("#location option:selected").text();
		$("#location_text").text(location);
		
		fillTableList();
		fillPicked();
	}


	function fillField(id) {
		data = {
			id: id
		}

		$.get('{{ url("fetch/chm_picking_schedule_detail") }}', data,  function(result, status, xhr){
			if(result.inventory != null || result.out != null){
				$('#schedule_id').val(id);
				$('#category').val(result.data.category);
				$('#larutan').val(result.data.solution_name);
				$('#material_number').val(result.data.material_number);
				$('#quantity').val(result.data.quantity);
				$('#bun').val(result.data.bun);
				$('#material_description').val(result.data.material_description);
				$('#storage_location').val(result.data.storage_location);

				if(result.inventory == null){
					$('#stock').val('0');
				}else{
					$('#stock').val(result.inventory.quantity);
				}
				
				$('#out').val(result.out.length);

				fillPicked();

			}else{
				openErrorGritter('Error!', 'Stock ' +result.data.material_number+ ' Habis');
			}
		});
	}

	function fillTableList(){
		$('#modalLocation').modal('hide');

		var request = 'request';
		var location = $('#location').val();

		var data = {
			request:request,
			location:location
		}

		$.get('{{ url("fetch/chm_picking_schedule")}}', data,  function(result, status, xhr){
			$('#tableList').DataTable().clear();
			$('#tableList').DataTable().destroy();
			$('#tableBodyList').html("");

			var tableData = "";
			$.each(result.data, function(key, value) {
				tableData += '<tr onclick="fillField(\''+value.id+'\')">';
				tableData += '<td>'+ value.schedule_date +'</td>';
				tableData += '<td>'+ value.category +'</td>';
				tableData += '<td>'+ value.solution_name +'</td>';
				tableData += '<td>'+ value.material_number +'</td>';
				tableData += '<td>'+ value.material_description +'</td>';
				tableData += '<td>'+ value.storage_location +'</td>';
				tableData += '<td>'+ value.quantity +'</td>';
				tableData += '<td>'+ value.bun +'</td>';
				tableData += '</tr>';
			});
			$('#tableBodyList').append(tableData);

			var table_list = $('#tableList').DataTable({
				"language": {
					"emptyTable": "There is no schedule"
				},
				'dom': 'Bfrtip',
				'responsive': true,
				'lengthMenu': [
				[ 25, 50, 100, -1 ],
				[ '25 rows', '50 rows', '100 rows', 'Show all' ]
				],
				"pageLength": 25,
				'buttons': {
					buttons:[
					{
						extend: 'pageLength',
						className: 'btn btn-default',
					},
					{
						extend: 'copy',
						className: 'btn btn-success',
						text: '<i class="fa fa-copy"></i> Copy',
						exportOptions: {
							columns: ':not(.notexport)'
						}
					},
					{
						extend: 'excel',
						className: 'btn btn-info',
						text: '<i class="fa fa-file-excel-o"></i> Excel',
						exportOptions: {
							columns: ':not(.notexport)'
						}
					},
					{
						extend: 'print',
						className: 'btn btn-warning',
						text: '<i class="fa fa-print"></i> Print',
						exportOptions: {
							columns: ':not(.notexport)'
						}
					}
					]
				},
				'paging': true,
				'lengthChange': true,
				'searching': true,
				'ordering': true,
				'order': [],
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true
			});
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

</script>
@endsection