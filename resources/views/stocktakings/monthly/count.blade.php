@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">


<style type="text/css">
	/*Start CSS Numpad*/
	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}
	/*End CSS Numpad*/

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

</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		
		<div class="col-xs-7" style="padding-left: 0px;">
			<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
				<div class="input-group input-group-lg">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: none; font-size: 18px;">
						<i class="fa fa-qrcode"></i>
					</div>
					<input type="text" class="form-control" placeholder="SCAN QR CODE" id="qr_code">
					<span class="input-group-btn">
						<button style="font-weight: bold;" href="javascript:void(0)" class="btn btn-success btn-flat" data-toggle="modal" data-target="#scanModal"><i class="fa fa-camera"></i>&nbsp;Scan QR</button>
					</span>
				</div>
			</div>

			<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 0px">
					<thead>
						<tr>
							<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;" colspan="2">SUMMARY OF COUNTING</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Store</td>
							<td style="padding: 0px; padding-left: 5px; padding-left: 5px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;" id="store"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Category</td>
							<td style="padding: 0px; padding-left: 5px; padding-left: 5px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;" id="category"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Material Number</td>
							<td style="padding: 0px; padding-left: 5px; padding-left: 5px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;" id="material_number"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Location</td>
							<td style="padding: 0px; padding-left: 5px; padding-left: 5px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;" id="location"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Material Desc.</td>
							<td style="padding: 0px; padding-left: 5px; padding-left: 5px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;" id="material_description"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Model Key Surface</td>
							<td style="padding: 0px; padding-left: 5px; padding-left: 5px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;" id="model_key_surface"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Lot</td>
							<td style="padding: 0px; padding-left: 5px; padding-left: 5px; background-color: rgb(204,255,255); text-align: left; color: #000000; font-size: 20px;" id="lot_uom"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="col-xs-5" style="margin-top: 5%;">
			<div class="col-xs-12">
				<div class="form-group row" align="right">
					<label class="col-xs-3" style="padding: 0px; color: yellow; font-size:2vw;">Lot</label>
					<label class="col-xs-3" style="padding: 0px; color: yellow; font-size:2vw;" id="text_lot"></label>
					<div class="col-xs-6" align="right">
						<input type="text" style="font-size:25px; height: 45px;" onchange="changeVal()" class="form-control numpad" placeholder="INPUT LOT HERE" id="lot">
					</div>
				</div>	
			</div>
			<div class="col-xs-12">	
				<div class="form-group row" align="right">
					<label class="col-xs-3" style="padding: 0px; color: yellow; font-size:2vw;">Z1</label>
					<div class="col-xs-6 col-xs-offset-3" align="right">
						<input type="text" style="font-size:25px; height: 45px;" onchange="changeVal()" class="form-control numpad" placeholder="INPUT Z1 HERE" id="z1">
					</div>
				</div>
			</div>
			<div class="col-xs-12">		
				<div class="form-group row" align="right">
					<label class="col-xs-3" style="padding: 0px; color: yellow; font-size:2vw;">Total</label>
					<div class="col-xs-6 col-xs-offset-3" align="right">
						<input type="text" style="font-size:30px; height: 45px;" class="form-control" id="total" readonly="">
					</div>
				</div>
			</div>
			<div class="col-xs-12" align="right">
				<div class="input-group input-group-lg">
					<button type="button" style="font-size:25px; height: 45px; font-weight: bold; padding-top: 0px; padding-bottom: 0px;" onclick="canc()" class="btn btn-danger">&nbsp;Cancel&nbsp;</button>

					<button type="button" style="font-size:25px; height: 45px; font-weight: bold; padding-top: 0px; padding-bottom: 0px;" onclick="save()" class="btn btn-success">&nbsp;<i class="fa fa-save"></i> &nbsp;Save&nbsp;</button>
				</div>
			</div>
		</div>
		
		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-top: 2%;">
			<table class="table table-bordered" id="store_table">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 25px;" colspan="7" id='store_title'>STORE</th>
					</tr>
					<tr>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">#</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">STORE</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">CATEGORY</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">MATERIAL NUMBER</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">MATERIAL DESCRIPTION</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">REMARK</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">COUNT PI</th>
					</tr>
				</thead>
				<tbody id="store_body">
				</tbody>
			</table>
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

		$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
		$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
		$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
		$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
		$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
		$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

		var vdo;
		var lot_uom;
		
		jQuery(document).ready(function() {

			$('.numpad').numpad({
				hidePlusMinusButton : true,
				decimalSeparator : '.'
			});

			$('#qr_code').blur();

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

				if(numberValidation(id)){
					var data = {
						id : id
					}

					$.get('{{ url("fetch/stocktaking/material_detail") }}', data, function(result, status, xhr){

						if (result.status) {
							if(result.material[0].remark == 'USE'){
								$('#qr_code').prop('disabled', true);
								openSuccessGritter('Success', 'QR Code Successfully');

								$("#store").text(result.material[0].store);
								$("#category").text(result.material[0].category);
								$("#material_number").text(result.material[0].material_number);
								$("#location").text(result.material[0].location);
								$("#material_description").text(result.material[0].material_description);
								$("#model_key_surface").text((result.material[0].model || '')+' '+(result.material[0].key || '')+' '+(result.material[0].surface || ''));
								$("#lot_uom").text((result.material[0].lot || '-') + ' ' + result.material[0].bun);
								lot_uom = (result.material[0].lot || 1);

								if(result.material[0].lot > 0){
									$("#text_lot").text(result.material[0].lot + ' x');
								}else{
									$("#text_lot").text('- x');
									$('#lot').prop('disabled', true);
								}

								fillStore(result.material[0].store);
							}else{
								canc();
								openErrorGritter('Error', 'QR Code No Use');
							}					

						} else {
							openErrorGritter('Error', 'QR Code Not Registered');
						}

						$('#scanner').hide();
						$('#scanModal').modal('hide');
						$(".modal-backdrop").remove();
					});
				}else{
					canc();
					openErrorGritter('Error', 'QR Code Tidak Terdaftar');
				}	
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

						checkCode(video, code.data);

					} else {
						outputMessage.hidden = false;
					}
				}
				requestAnimationFrame(tick);
			}

		}

		function checkCode(video, code) {

			var data = {
				id : code
			}

			$.get('{{ url("fetch/stocktaking/material_detail") }}', data, function(result, status, xhr){

				if (result.status) {
					if(result.material[0].remark == 'USE'){
						$('#qr_code').prop('disabled', true);
						openSuccessGritter('Success', 'QR Code Successfully');

						$("#store").text(result.material[0].store);
						$("#category").text(result.material[0].category);
						$("#material_number").text(result.material[0].material_number);
						$("#location").text(result.material[0].location);
						$("#material_description").text(result.material[0].material_description);
						$("#model_key_surface").text((result.material[0].model || '')+' '+(result.material[0].key || '')+' '+(result.material[0].surface || ''));
						$("#lot_uom").text((result.material[0].lot || '-') + ' ' + result.material[0].bun);
						lot_uom = (result.material[0].lot || 1);

						if(result.material[0].lot > 0){
							$("#text_lot").text(result.material[0].lot + ' x');
						}else{
							$("#text_lot").text('- x');
							$('#lot').prop('disabled', true);
						}

						fillStore(result.material[0].store);
					}else{
						canc();
						openErrorGritter('Error', 'QR Code No Use');
					}					

				} else {
					openErrorGritter('Error', 'QR Code Tidak Terdaftar');
				}

				$('#scanner').hide();
				$('#scanModal').modal('hide');
				$(".modal-backdrop").remove();
			});

		}

		function fillStore(store){
			var data = {
				store : store
			}

			$.get('{{ url("fetch/stocktaking/store_list") }}', data, function(result, status, xhr){
				if (result.status) {
					$("#store_body").empty();
					$("#store_title").text("");
					$("#store_title").text("STORE : " + store);


					var body = '';
					var num = '';
					for (var i = 0; i < result.store.length; i++) {
						if(result.store[i].remark == 'USE'){
							if(result.store[i].category == 'SINGLE'){
								var css = 'style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 15px;"';
							}else{
								var css = 'style="padding: 0px; background-color: rgb(250,250,210); text-align: center; color: #000000; font-size: 15px;"';
							}
						}else{
							var css = 'style="padding: 0px; text-align: center; color: #000000; font-size: 15px;"';
						}						

						num++;

						body += '<tr>';
						body += '<td '+css+'>'+num+'</td>';
						body += '<td '+css+'>'+result.store[i].store+'</td>';
						body += '<td '+css+'>'+result.store[i].category+'</td>';
						body += '<td '+css+'>'+result.store[i].material_number+'</td>';
						body += '<td '+css+'>'+result.store[i].material_description+'</td>';
						body += '<td '+css+'>'+result.store[i].remark+'</td>';
						if(result.store[i].quantity != null){
							body += '<td '+css+'>'+result.store[i].quantity+'</td>';
						}else{
							body += '<td '+css+'>'+'-'+'</td>';							
						}
						body += '</tr>';

					}
					$("#store_body").append(body);
				}
			});
		}

		function canc(){
			$('#store').html("");
			$('#category').html("");
			$('#material_number').html("");
			$('#location').html("");
			$('#material_description').html("");
			$('#model_key_surface').html("");
			$('#lot_uom').html("");
			$('#text_lot').html("");
			$('#lot').prop('disabled', false);

			document.getElementById("lot").value = '';
			document.getElementById("z1").value = '';
			document.getElementById("total").value = '';

			lot_uom = 0;			

			$('#qr_code').val("");
			$('#qr_code').prop('disabled', false);
			$('#qr_code').focus();
			$('#qr_code').blur();
		}

		function changeVal(){
			
			var lot = $("#lot").val();
			var z1 = $("#z1").val();

			lot = (lot || 0);
			lot_uom = (lot_uom || 0);
			z1 = (z1 || 0);

			var total = (parseFloat(lot) * parseFloat(lot_uom)) + parseFloat(z1);
			document.getElementById("total").value = total;
		}

		function save(){
			var id = $("#qr_code").val();
			var quantity = $("#total").val();

			var data = {
				id : id,
				quantity : quantity
			}

			$.post('{{ url("fetch/stocktaking/update_count") }}', data, function(result, status, xhr){
				if (result.status) {
					openSuccessGritter('Success', result.message);
					var store = $("#store").text();
					
					fillStore(store);
					canc();
				}else{
					openSuccessGritter('Error', result.message);
				}
			});
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
			return year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
		}
	</script>
	@endsection