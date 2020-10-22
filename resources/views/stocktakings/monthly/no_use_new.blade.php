@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">

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
	#master:hover {
		cursor: pointer;
	}
	#master {
		font-size: 17px;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
		color: white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 9px;
		padding-bottom: 9px;
		vertical-align: middle;
		background-color: white;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#loading, #error { display: none; }

	#qr_code {
		text-align: center;
		font-weight: bold;
	}
	#lot {
		text-align: center;
		font-weight: bold;
	}
	#z1 {
		text-align: center;
		font-weight: bold;
	}
	#total {
		text-align: center;
		font-weight: bold;
	}
	#progress-text {
		text-align: center;
		font-weight: bold;
		font-size: 1.5vw;
		color: #fff;
	}

	#loading, #error { display: none; }


</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row" style="margin-left: 1%; margin-right: 1%;" id="main">
		<div class="col-xs-12 col-md-6 col-md-offset-3" style="padding-left: 0px;">
			<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
				<div class="input-group input-group-lg">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: none; font-size: 18px;">
						<i class="fa fa-qrcode"></i>
					</div>
					<input type="text" class="form-control" placeholder="SCAN QR CODE" id="qr_code">
					<span class="input-group-btn">
						<button style="font-weight: bold;" href="javascript:void(0)" class="btn btn-success btn-flat" data-toggle="modal" data-target="#scanModal"><i class="fa fa-camera"></i>&nbsp;&nbsp;Scan</button>
					</span>
				</div>
			</div>
		</div>

		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-top: 0%;overflow-x: scroll">
			<table class="table table-bordered" id="store_table">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 25px;" colspan="9" id='store_title'>NO USE</th>
					</tr>
					<tr>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">#</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">GROUP</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">LOCATION</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">STORE</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">SUB STORE</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">CATEGORY</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">MATERIAL NUMBER</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">MATERIAL DESCRIPTION</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">ACTION</th>
					</tr>
				</thead>
				<tbody id="store_body">
				</tbody>
			</table>
		</div>

		<div class="col-xs-12" style="padding: 0px;" id="confirm">
			<div class="col-xs-3 pull-right" align="right" style="padding: 0px;">
				<button type="button" style="font-size:20px; height: 40px; font-weight: bold; padding: 15%; padding-top: 0px; padding-bottom: 0px;" onclick="conf()" class="btn btn-success">SUBMIT</button>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="scanModal">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title text-center"><b>SCAN QR CODE HERE</b></h4>
				</div>
				<div class="modal-body">
					<div id='scanner' class="col-xs-12">
						<div class="col-xs-10 col-xs-offset-1">
							<div id="loadingMessage">
								🎥 Unable to access video stream
								(please make sure you have a webcam enabled)
							</div>
							<canvas style="width: 100%; height: 300px;" id="canvas" hidden></canvas>
							<div id="output" hidden>
								<div id="outputMessage">No QR code detected.</div>
							</div>
						</div>									
					</div>

					<p style="visibility: hidden;">camera</p>
					<input type="hidden" id="code">
				</div>
			</div>
		</div>
	</div>


</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jsQR.js")}}"></script>
<script src="{{ url("js/jquery.numpad.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var vdo;
	
	jQuery(document).ready(function() {

		// $('#qr_code').blur();
		$('#qr_code').focus();

		$('#confirm').hide();

	});

	function stopScan() {
		$('#scanModal').modal('hide');
	}

	function videoOff() {
		vdo.pause();
		vdo.src = "";
		vdo.srcObject.getTracks()[0].stop();
	}

	$( "#scanModal" ).on('shown.bs.modal', function(){
		showCheck('123');
	});

	$('#scanModal').on('hidden.bs.modal', function () {
		videoOff();
	});

	$('#qr_code').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var id = $("#qr_code").val();
			// if(numberValidation(id)){
				checkCode(id);
			// }else{
			// 	canc();
			// 	openErrorGritter('Error', 'QR Code Tidak Terdaftar');
			// }
		}
	});

	function numberValidation(id){
		var number = /^[0-9]+$/;

		if(!id.match(number)){
			return false;
		}else{
			return true;
		}
	}

	function showCheck(kode) {
		$(".modal-backdrop").add();
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
					document.getElementById("qr_code").value = code.data;

					checkCode(code.data);

				} else {
					outputMessage.hidden = false;
				}
			}
			requestAnimationFrame(tick);
		}
	}

	var list = [];

	function checkCode(code) {

		var data = {
			id : code
		}

		$.get('{{ url("fetch/stocktaking/material_detail_new") }}', data, function(result, status, xhr){

			if (result.status) {
				if(result.material.length > 0){
					$('#scanner').hide();
					$('#scanModal').modal('hide');
					$(".modal-backdrop").remove();


					if(result.material[0].process > 1){
						canc();
						openErrorGritter('Error', 'Proses tidak sesuai urutan');
						return false;
					}

					if(result.material[0].remark == 'NO USE'){
						canc();
						openErrorGritter('Error', 'QR Code No Use');
						return false;
					}

					var fillList = true;
					for (var i = 0; i < list.length; i++) {
						if(result.material[0].id == list[i][0]){
							canc();
							openErrorGritter('Error', 'Material sudah di Scan<br>Cek Tabel No Use');
							fillList = false;
						}
					}

					if(fillList){
						var data = [];
						data.push(result.material[0].id);
						data.push(result.material[0].area);
						data.push(result.material[0].location);
						data.push(result.material[0].store);
						data.push(result.material[0].sub_store);
						data.push(result.material[0].category);
						data.push(result.material[0].material_number);
						data.push(result.material[0].material_description);
						list.push(data);

						canc();
						fillStore();
					}
				} else {
					canc();
					openErrorGritter('Error', 'QR Code Tidak Terdaftar');
				}			

			} else {
				canc();
				openErrorGritter('Error', 'QR Code Tidak Terdaftar');
			}

			$('#scanner').hide();
			$('#scanModal').modal('hide');
			$(".modal-backdrop").remove();
		});

	}


	function fillStore(){
		$("#store_body").empty();

		var body = '';
		var num = '';
		for (var i = 0; i < list.length; i++) {
			var css = 'style="padding: 0px; text-align: center; color: #000000; font-size: 15px;"';

			num++;
			body += '<tr>';
			body += '<td '+css+'>'+num+'</td>';
			body += '<td '+css+'>'+list[i][1]+'</td>';
			body += '<td '+css+'>'+list[i][2]+'</td>';
			body += '<td '+css+'>'+list[i][3]+'</td>';
			body += '<td '+css+'>'+list[i][4]+'</td>';
			body += '<td '+css+'>'+list[i][5]+'</td>';		
			body += '<td '+css+'>'+list[i][6]+'</td>';		
			body += '<td '+css+'>'+list[i][7]+'</td>';
			body += '<td '+css+'><button style="width: 50%; height: 100%;" onclick="cancNoUse(\''+list[i][0]+'\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-close"></i></span></button></td>';

			body += '</tr>';

		}
		$("#store_body").append(body);

		if(list.length > 0){
			$('#confirm').show();
		}

		$('#qr_code').focus();

	}

	function canc(){
		$('#qr_code').val("");
		$('#qr_code').focus();
		// $('#qr_code').blur();

	}

	function cancNoUse(id) {
		if(confirm("Hapus material dari List No Use ?")){
			var index = -1;
			for (var i = 0; i < list.length; i++) {
				if(list[i][0] == id){
					index = i;
				}
			}

			if (index > -1) {
				list.splice(index, 1);
			}

			fillStore();
		}
	}

	function conf() {
		$("#loading").show();

		var id = [];

		for (var i = 0; i < list.length; i++) {
			id.push(list[i][0]);
		}

		var data = {
			id : id
		}

		if(confirm("Data akan simpan oleh sistem.\nData tidak dapat dikembalikan.")){

			$.post('{{ url("fetch/stocktaking/update_no_use_new") }}', data, function(result, status, xhr){
				if (result.status) {
					openSuccessGritter('Success', result.message);

					$("#store_body").empty();
					$('#confirm').hide();
					$("#loading").hide();
					
					list = [];
				}else{
					$("#loading").hide();
					openErrorGritter('Error', result.message);
				}
			});
		}else{
			$("#loading").hide();
		}
		
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '4000'
		});
	}

</script>
@endsection