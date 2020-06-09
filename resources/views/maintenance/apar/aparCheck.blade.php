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
			<table class="table table-bordered" style="width: 100%; margin-bottom: 0px">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;" colspan="2">APAR / HYDRANT Information</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">APAR Code</td>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="apar_code"></td>
					</tr>
					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">APAR Name</td>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="apar_name"></td>
					</tr>
					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Location</td>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="apar_location"></td>
					</tr>
					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Type</td>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="apar_type"></td>
					</tr>
					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Capacity</td>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="apar_capacity"></td>
					</tr>
					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;">Exp. date</td>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="apar_expired"></td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 0px">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;" colspan="2">APAR / HYDRANT Check List</th>
					</tr>
				</thead>
				<tbody id="body_check_list">

				</tbody>
			</table>
		</div>

		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;" id="check">
			<button class="btn btn-lg btn-success" style="width: 100%" id="btn-check"><i class="fa fa-check"></i> Check</button>
		</div>


		<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;" colspan="4">History APAR / HYDRANT Check</th>
					</tr>
					<tr>
						<th width="12%" style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">Check Date</th>
						<th width="20%" style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">PIC</th>
						<th width="10%" style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">Check</th>
						<th style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:18px;">Note</th>
					</tr>
				</thead>
				<tbody id="history_body">
				</tbody>
			</table>
		</div>

		<div class="modal fade" id="scanModal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title text-center"><b>SCAN QR HERE</b></h4>
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

						<p style="visibility: hidden;">camera</p>
						<input type="hidden" id="apar_code">
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

		apar = [];
		var vdo;
		var apar_checks = [];
		hasil_check = "";

		jQuery(document).ready(function() {
			// $("#check").hide();
			$('#check').children().hide();
			getAparList();
			apar_checks = <?php echo json_encode($check_list); ?>;
			console.log(apar_checks);
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

		function showCheck(kode) {
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

			document.getElementById("apar_code").value = kode;
			$('#scanner').show();

			$('#field-name').hide();
			$('#field-key').hide();
			$('#btn-check').hide();

			$('#check-modal').modal('show');
			$('#input_employee_id').val("");
			$('#input_employee_id').focus();
		}

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

		function get_history(datas) {
			var data = {
				utility_id: datas
			}

			$("#history_body").empty();
			var bd = "";

			$.get('{{ url("fetch/maintenance/apar/history") }}', data, function(result, status, xhr) {
				$.each(result.check, function(index, value){
					
					// if (typeof value.check !== 'undefined') {
					// 	return false;
					// }

					style = 'style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 15px;"';

					bd += "<tr>";
					bd += "<td "+style+">"+value.check_date2+"</td>";
					bd += "<td "+style+">"+value.name+"</td>";

					var cek = "BAIK";
					if (~value.check.indexOf("0")) {
						cek = "KURANG";
					}

					if (index == 0) {
						hasil_check = cek;
					}

					bd += "<td "+style+">"+cek+"</td>";

					arrCek = value.check.split(',');
					remark = "";

					var  i = 0;
					$.each(apar_checks, function(index2, value2){
						if (value.remark2 == value2.remark) {
							if (arrCek[i] == '0') {
								remark += value2.check_point+",";
							}
							i++;
						}
					})

					bd += "<td "+style+">"+remark.slice(0,-1)+"</td>";

					bd += "</tr>";
				})

				$("#history_body").append(bd);
			})
		}

		$("#btn-check").click(function() {
			var check_list = [];
			$(".check").each(function( i ) {
				if ($(this).is(':checked')) {
					check_list.push(1);
				} else {
					check_list.push(0);
				}
			});

			var data = {
				check : check_list,
				remark : $("#keterangan").val(),
				utility_id : $("#apar_id").val(),
			}
			$.post('{{ url("post/maintenance/apar/check") }}', data, function(result, status, xhr) {
				if (result.status) {
					get_history($("#apar_id").val());

					$(".check").prop('checked', false).parent().removeClass('active');

					openSuccessGritter('Success', 'Check Berhasil Ditambahkan');
					console.log(hasil_check);

					var check_date_2 = "-";
					if(result.checked_apar.length > 1){
						check_date_2 = result.checked_apar[1].check_date;
					}

					if (hasil_check == "") {
						hasil_check = "-";
					}

					// window.open('{{ url("print/apar/qr/".'+result.checked_apar.utility_code+'."/".'+result.checked_apar.utility_name+'."/".'+result.checked_apark.exp_date+'."/".'+result.checked_apark.last_check+'."/".'+hasil_check+') }}', '_blank');
					window.open('{{ url("print/apar/qr/") }}/'+result.checked_apar[0].utility_code+'/'+result.checked_apar[0].utility_name+'/'+result.checked_apar[0].exp_date+'/'+result.checked_apar[0].check_date+'/'+check_date_2+'/'+hasil_check+'/'+result.checked_apar[0].remark, '_blank');
				} else {
					openErrorGritter("Error", "Cek Koneksi Wifi Anda");
				}
			})
		})

		function checkCode(video, code) {
			var stat = false;
			var arr_selected = [];
			$.each(apar, function(index, value){
				if (value.apar_code == code.split("/")[0] && value.item == code.split("/")[1]) {
					// console.log(value.apar_name);
					arr_selected = value;
					stat = true;
				}
			})

			if (stat) {
				$('#scanner').hide();
				$('#scanModal').modal('hide');
				// $("#check").show();
				$('#check').children().show();

				videoOff();
				openSuccessGritter('Success', 'QR Code Successfully');
				// console.log(arr_selected);
				$("#apar_id").val(arr_selected.apar_id);
				$("#apar_code").text(arr_selected.apar_code);
				$("#apar_name").text(arr_selected.apar_name);
				$("#apar_location").text(arr_selected.lokasi2+" - "+arr_selected.lokasi);
				$("#apar_type").text(arr_selected.jenis);
				$("#apar_capacity").text(arr_selected.kapasitas);

				var exp_date = "-";
				if (arr_selected.item == "APAR") {
					exp_date = arr_selected.age_left+" bulan lagi - "+arr_selected.exp_date;
				}
				$("#apar_expired").text(exp_date);

				t_body = "";
				$("#body_check_list").empty();

				// console.log(apar_checks);

				$.each(apar_checks, function(index, value){
					var cek = "";
					if (arr_selected.item == value.remark) {
						t_body += "<tr>";
						t_body += "<td style='padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 90%;'>"+value.check_point+"</td>";
						t_body += "<td style='padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;'><div class='checkbox'>";
						t_body += "<label><input type='checkbox' class='check'>OK</label></div></td>";
						t_body += "</tr>";
					}
				});

				$("#body_check_list").append(t_body);

				get_history(arr_selected.apar_id);

			} else {
				openErrorGritter('Error', 'QR Code Not Registered');
				audio_error.play();
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