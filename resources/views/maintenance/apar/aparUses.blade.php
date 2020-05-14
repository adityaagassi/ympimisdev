@extends('layouts.display')
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
	#loading { display: none; }

	#qr_apar {
		text-align: center;
		font-weight: bold;
	}

</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<input type="hidden" id="apar_id">
	<input type="hidden" id="operator_id" value="{{ $employee_id }}">
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
			<p style="position: absolute; color: White; top: 45%; left: 35%;">
				<span style="font-size: 40px">Loading, please wait...<i class="fa fa-spin fa-refresh"></i></span>
			</p>
		</div>

		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 0px">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;" colspan="2">Operator</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;" id="op">{{ $employee_id }}</td>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="op2">{{ $name }}</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">

			<div class="input-group input-group-lg">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black; font-size: 20px;">
					<i class="fa fa-qrcode"></i>
				</div>
				<input type="text" class="form-control" placeholder="APAR CODE / HYDRANT CODE" id="qr_apar" readonly>
				<span class="input-group-btn">
					<button type="button" class="btn btn-success btn-flat" data-toggle="modal" data-target="#scanModal"><i class="fa fa-qrcode"></i> Scan QR</button>
				</span>
			</div>
		</div>

		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 0px" id="table_use_list">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;" colspan="5">History of APAR use</th>
					</tr>
					<tr>
						<th>APAR Code</th>
						<th>APAR Name</th>
						<th>Location</th>
						<th>Type</th>
						<th>Use Date/Time</th>
					</tr>
				</thead>
				<tbody id="body_use_list">
				</tbody>
			</table>
		</div>

		<div class="modal fade" id="scanModal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title text-center" id="judul" style="font-weight: bold;">SCAN QR HERE</h4>
					</div>
					<div class="modal-body">
						<div id='scanner' class="col-xs-12">
							<div class="col-xs-12">
								<div id="loadingMessage">
									🎥 Unable to access video stream (please make sure you have a webcam enabled)
								</div>
								<canvas style="width: 100%;" id="canvas" hidden></canvas>
								<div id="output" hidden>
									<div id="outputMessage">No QR code detected.</div>
								</div>
							</div>									
						</div>

						<div id="status" class="col-xs-12" style="display: none">
							<table width="100%" border="1">
								<tr>
									<td style="padding: 0px; text-align: center; color: white; background-color: #7e5686; font-size:20px; width: 30%;">CATEGORY</td>
									<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="apar_category"></td>
								</tr>
								<tr>
									<td style="padding: 0px; text-align: center; color: white; background-color: #7e5686; font-size:20px; width: 30%;">APAR CODE</td>
									<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="apar_code"></td>
								</tr>
								<tr>
									<td style="padding: 0px; text-align: center; color: white; background-color: #7e5686; font-size:20px; width: 30%;">APAR NAME</td>
									<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="apar_name"></td>
								</tr>
								<tr>
									<td style="padding: 0px; text-align: center; color: white; background-color: #7e5686; font-size:20px; width: 30%;">LOCATION</td>
									<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="apar_location"></td>
								</tr>
								<tr>
									<td style="padding: 0px; text-align: center; color: white; background-color: #7e5686; font-size:20px; width: 30%;">TYPE</td>
									<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="apar_type"></td>
								</tr>
							</table>
							<br>
							<table width="100%">
								<tr>
									<td><button class="btn btn-success pull-left" onclick="use(true)"><i class="fa fa-check"></i> YES</button></td>
									<td><button class="btn btn-danger pull-right" onclick="use(false)"><i class="fa fa-close"></i> NO</button></td>
								</tr>
							</table>
						</div>

						<p style="visibility: hidden;">camera</p>
						<input type="hidden" id="apar_code">
						<input type="hidden" id="apar_id">
					</div>
				</div>
			</div>
		</div>
	</section>

	@endsection
	@section('scripts')
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
	<script src="{{ url("js/jsQR.js")}}"></script>
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		var vdo;
		apar = [];

		jQuery(document).ready(function() {
			getAparList();
			getAparUseList();
		});

		function getAparList() {
			$.get('{{ url("fetch/maintenance/apar/list") }}', function(result, status, xhr) {
				$.each(result.apar, function(index, value){
					apar.push({
						'apar_id'  : value.id,
						'apar_code' :  value.utility_code, 
						'apar_name' :  value.utility_name,
						'jenis' :  value.type,
						'lokasi' :  value.group,
						'kapasitas' :  value.capacity,
						'lokasi2' :  value.location,
						'exp_date' :  value.exp_date,
						'age_left' :  value.age_left,
						'item' :  value.remark,
					});
				});
			})
		}

		function getAparUseList() {
			$.get('{{ url("fetch/maintenance/apar/use/list") }}', function(result, status, xhr) {
				$('#table_use_list').DataTable().clear();
				$('#table_use_list').DataTable().destroy();
				$("#body_use_list").empty();

				body = "";
				$.each(result.use_list, function(index, value){
					body += "<tr>";
					body += "<td>"+value.utility_code+"</td>";
					body += "<td>"+value.utility_name+"</td>";
					body += "<td>"+value.group+" - "+value.location+"</td>";
					body += "<td>"+value.remark+"</td>";
					body += "<td>"+value.created_at+"</td>";
					body += "</tr>";
				});
				$("#body_use_list").append(body);

				var table = $('#table_use_list').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'paging': true,
					'lengthChange': false,
					'searching': false,
					'ordering': true,
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bAutoWidth": false
				});
			})
		}

		function stopScan() {
			$('#scanModal').modal('hide');
		}

		function videoOff() {
			vdo.pause();
			vdo.src = "";
			vdo.srcObject.getTracks()[0].stop();
		}

		$( "#scanModal" ).on('shown.bs.modal', function(){
			showCheck();
		});

		$('#scanModal').on('hidden.bs.modal', function () {
			videoOff();
		});

		function showCheck() {
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

						document.getElementById("qr_apar").value = code.data;

						checkCode(video, code.data);

					} else {
						outputMessage.hidden = false;
					}
				}
				requestAnimationFrame(tick);
			}

			$('#scanner').show();

			$('#field-name').hide();
			$('#field-key').hide();
			$('#btn-check').hide();

			$('#check-modal').modal('show');
			$('#input_employee_id').val("");
			$('#input_employee_id').focus();
		}

		function checkCode(video, code) {
			var stat = false;
			var arr_selected = [];
			$.each(apar, function(index, value){
				if (value.apar_code == code.split("/")[0]) {
					arr_selected = value;
					stat = true;
				}
			})

			if (stat) {
				$('#scanner').hide();
				$('#status').show();
				$('#apar_category').text(arr_selected.item);
				$('#apar_code').text(arr_selected.apar_code);
				$('#apar_name').text(arr_selected.apar_name);
				$('#apar_location').text(arr_selected.lokasi2+" - "+arr_selected.lokasi);
				$('#apar_type').text(arr_selected.jenis);
				$('#apar_id').val(arr_selected.apar_id);
				$('#judul').text('Use This Fire Estiquisher?');

				videoOff();
				openSuccessGritter('Success', 'QR Code Successfully');
			} else {
				openErrorGritter('Error', 'QR Code Not Registered');
				audio_error.play();
			}
			$('#check-modal').modal('hide');

		}

		function use(param) {
			if (param) {
				var data = {
					utility_id : $('#apar_id').val()
				}
				$.post('{{ url("use/maintenance/apar") }}', data, function(result, status, xhr) {
					openSuccessGritter('Success', 'Digunakan');
					getAparUseList();
					$("#scanModal").modal('hide');
				})
			} else {
				openInfoGritter('Success', 'Tidak Jadi digunakan');
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

		function openInfoGritter(title, message){
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-info',
				image: '{{ url("images/image-info.png") }}',
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

	</script>
	@endsection