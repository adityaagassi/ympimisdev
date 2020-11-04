@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		vertical-align: middle;
	}
	tbody>tr>td{
		text-align:center;
		vertical-align: middle;
	}
	tfoot>tr>th{
		text-align:center;
		vertical-align: middle;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	#loading { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i><br>Loading...</span>
			</center>
		</div>
	</div>

	<div class="row">
		@if($user)
		@if($user->department == 'Management Information System' || $user->group == 'Standardization')
		<div class="col-xs-12 pull-right" style="padding-left: 0;">
			<button class="btn btn-success pull-right" style="margin-left: 5px; width: 16%;" data-toggle="modal" data-target="#modalCreate"><i class="fa fa-plus"></i>&nbsp;Add Stock</button>
		</div>
		@endif
		@endif

		<div class="col-xs-6" style="margin-top: 1%; margin-bottom: 1%;">
			<div id="container1"></div>
		</div>
		<div class="col-xs-3" style="margin-top: 1%; margin-bottom: 1%;">
			<div id="container2"></div>
		</div>
		<div class="col-xs-3" style="margin-top: 1%; margin-bottom: 1%;">
			<div id="container3"></div>
		</div>

		<div class="col-xs-10">
			<table class="table table-hover table-bordered table-striped" id="tableRequest">
				<thead style="background-color: rgba(126,86,134); color: white;">
					<tr>
						<th colspan="7">REQUEST LIST (ドライバー予約の一覧)</th>
					</tr>
					<tr>
						<th style="width: 1%;">ID</th>
						<th style="width: 1%;">Tanggal</th>
						<th style="width: 5%;">Pemohon</th>
						<th style="width: 1%;">Laki-laki</th>
						<th style="width: 1%;">Perempuan</th>
						<th style="width: 1%;">Reprint</th>
						<th style="width: 1%;">Details</th>
					</tr>
				</thead>
				<tbody id="tableRequestBody">
				</tbody>
			</table>
		</div>

		<div class="col-xs-2" style="padding-left: 0;">
			@if($user)
			@if(strpos(strtolower($user->position), 'operator') !== false || strpos(strtolower($user->position), 'Sub') !== false) 
			
			@else
			<button data-toggle="modal" class="btn btn-primary" style="width: 100%; margin-bottom: 5px;" ata-toggle="modal" data-target="#modalRequest"><i class="fa fa-file-text-o"></i>&nbsp;&nbsp;&nbsp;Create Request</button>
			@endif


			@if(!strpos(strtolower($user->position), 'operator'))
			@if($user->department == 'Management Information System' || ($user->section == 'Warehouse' && ($user->assignment == 'FRM' || $user->assignment == 'LDR')))
			<button data-toggle="modal" data-target="#modalScan" class="btn btn-success" style="width: 100%; margin-bottom: 5px;"><i class="fa fa-camera"></i>&nbsp;&nbsp;&nbsp;Scan Request</button>
			@endif
			@endif

			@endif

			<a href="{{ url('index/std_control/safety_shoes_log') }}" target="_blank" class="btn btn-info" style="width: 100%; margin-bottom: 5px;"><i class="fa fa-list-ul"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Safety Shoes Log</a>

		</div>
	</div>
</section>

<div class="modal fade" id="modalCreate">
	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<span style="font-weight: bold; font-size: 1.5vw;">Add Safety Shoes Stock</span>
				</center>
				<hr>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<div class="col-xs-12" style="padding-bottom: 5px;">

						<div class="col-xs-12">
							<span style="font-weight: bold; font-size: 1vw;">Safety Shoes<span class="text-red">*</span></span>
							<table class="table table-hover table-bordered table-striped" id="tableAddStock">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%;">NIK</th>
										<th style="width: 4%;">Nama</th>
										<th style="width: 1%;">Gender</th>
										<th style="width: 4%;">Bagian</th>
										<th style="width: 4%;">Sepatu</th>
										<th style="width: 1%;">Qty</th>
										<th style="width: 1%;">Status</th>
										<th style="width: 1%;">#</th>
									</tr>
								</thead>
								<tbody id="tableAddStockBody">
								</tbody>
							</table>
						</div>
						<div class="col-xs-8 col-xs-offset-2">
							<div class="col-xs-6" style="padding-left: 0px; padding-right: 0px;">
								<select class="form-control select4" name="addEmp" id="addEmp" data-placeholder="Pilih Karyawan" style="width: 100%;">
									<option></option>
									@foreach($employees as $employee)
									<option value="{{ $employee->employee_id }}_{{ $employee->name }}_{{ $employee->gender }}_{{ $employee->group }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
									@endforeach								
								</select>
							</div>

							<div class="col-xs-6" style="padding-left: 1%; padding-right: 0px;">
								<select class="form-control select4" name="addSizeUk" id="addSizeUk" data-placeholder="Pilih Sepatu" style="width: 100%;">
								</select>
							</div>
						</div>
						<div class="col-xs-8 col-xs-offset-2" style="margin-top: 1%;">

							<div class="col-xs-3" style="padding-left: 0%; padding-right: 0px;">
								<input style="text-align: center;" class="form-control" type="text" id="addSize" name="addSize" placeholder="Ukuran IND" readonly>
							</div>
							
							<div class="col-xs-3" style="padding-left: 1%; padding-right: 0px;">
								<select class="form-control select4" name="addStatus" id="addStatus" data-placeholder="Pilih Status" style="width: 100%;">
									<option></option>
									<option value="Simpan">Simpan</option>
									<option value="Buang">Buang</option>
								</select>
							</div>

							<div class="col-xs-3" style="padding-left: 1%; padding-right: 0px;">
								<input style="text-align: center;" class="form-control" type="number" min="1" value="1" id="addQty" name="addQty" placeholder="input Qty">
							</div>

							<div class="col-xs-3" style="padding-left: 1%; padding-right: 0px;">
								<a class="btn btn-success" style="width: 100%;" onclick="addStock()">Tambahkan</a>
							</div>
						</div>

					</div>
				</div>
				<div class="box-footer">
					<div class="col-xs-12">
						<a class="btn btn-primary pull-right" onclick="submitStock()" style="font-size: 1.5vw; width: 100%; font-weight: bold;">CONFIRM</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalRequest">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<span style="font-weight: bold; font-size: 1.5vw;">Create Safety Shoes Request</span>
				</center>
				<hr>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<div class="col-xs-12" style="padding-bottom: 5px;">

						<div class="col-xs-6 col-xs-offset-3">
							<div class="col-xs-12" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Pilih Printer:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-12" style="padding-left: 0px; padding-right: 0px;">
								<select class="form-control select3" name="reqPrinter" id="reqPrinter" data-placeholder="Pilih Nama Printer" style="width: 100%;">
									<option></option>
									@foreach($printers as $printer)
									<option value="{{ $printer }}">{{ $printer }}</option>
									@endforeach								
								</select>
							</div>
						</div>


						<div class="col-xs-12" style="margin-top: 3%;">
							<table class="table table-hover table-bordered table-striped" id="tableCreateReq">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 3%;">NIK</th>
										<th style="width: 4%;">Nama</th>
										<th style="width: 3%;">Gender</th>
										<th style="width: 3%;">Bagian</th>
										<th style="width: 3%;">Shoes</th>
										<th style="width: 1%;">#</th>
									</tr>
								</thead>
								<tbody id="tableCreateReqBody">
								</tbody>
							</table>
						</div>
						<div class="col-xs-12">
							<div class="col-xs-5" style="padding-left: 0px; padding-right: 0px;">
								<select class="form-control select3" name="reqEmp" id="reqEmp" data-placeholder="Pilih Karyawan" style="width: 100%;">
									<option></option>
									@foreach($employees as $employee)
									@if($employee->end_date == null)
									<option value="{{ $employee->employee_id }}_{{ $employee->name }}_{{ $employee->gender }}_{{ $employee->group }}_{{ $employee->department }}">{{ $employee->employee_id }} - {{ $employee->name }}</option>
									@endif
									@endforeach								
								</select>
							</div>

							<div class="col-xs-5" style="padding-left: 1%; padding-right: 0px;">
								<select class="form-control select3" name="reqSizeUk" id="reqSizeUk" data-placeholder="Pilih Sepatu" style="width: 100%;">
								</select>
							</div>

							<div class="col-xs-2" style="padding-left: 1%; padding-right: 0px;">
								<a class="btn btn-success" style="width: 100%;" onclick="addReq()">Tambahkan</a>
							</div>
						</div>

					</div>
				</div>
				<div class="box-footer">
					<div class="col-xs-12">
						<a class="btn btn-primary pull-right" onclick="submitReq()" style="font-size: 1.5vw; width: 100%; font-weight: bold;">CONFIRM</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalScan">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #ff851b;">Scan Request</h3></center>
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
					<div id="receiveShoes" style="width:100%;">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalReprint">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #ff851b;" id="re_request_id"></h3></center>
				<div class="modal-body table-responsive no-padding">
					<input id="repRequestId" hidden>
					<div class="col-xs-12" style="padding-left: 0px; padding-right: 0px;">
						<select class="form-control select1" name="repPrinter" id="repPrinter" data-placeholder="Pilih Nama Printer" style="width: 100%;">
							<option></option>
							@foreach($printers as $printer)
							<option value="{{ $printer }}">{{ $printer }}</option>
							@endforeach								
						</select>
					</div>
				</div>
			</div>
			<div class="box-footer">
				<div class="col-xs-12">
					<a class="btn btn-primary pull-right" onclick="submitReprint()" style="font-size: 1.5vw; width: 100%; font-weight: bold;">REPRINT</a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<span id="detail_request_id" style="font-weight: bold; font-size: 1.5vw;"></span>
					<br>
					<span id="detail_requester" style="font-weight: bold; font-size: 1vw;"></span>
				</center>
				<hr>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<div class="col-xs-12" style="padding-bottom: 5px;">
						<table class="table table-hover table-bordered table-striped" id="tableDetailRequest">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 1%;">Employee ID</th>
									<th style="width: 4%;">Name</th>
									<th style="width: 1%;">Gender</th>
									<th style="width: 5%;">Department</th>
									<th style="width: 4%;">Section</th>
									<th style="width: 4%;">Group</th>
									<th style="width: 1%;">Size</th>
								</tr>
							</thead>
							<tbody id="tableDetailRequestBody">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalStock">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<span id="detail_stock" style="font-weight: bold; font-size: 1.5vw;"></span>
				</center>
				<hr>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<div class="col-xs-12" style="padding-bottom: 5px;">
						<table class="table table-hover table-bordered table-striped" id="tableDetailStock">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th style="width: 4%;">Merk</th>
									<th style="width: 1%;">Gender</th>
									<th style="width: 2%;">Size</th>
									<th style="width: 2%;">Size</th>
								</tr>
							</thead>
							<tbody id="tableDetailStockBody">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts-gantt.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="<?php echo e(url("js/jsQR.js")); ?>"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		clearAll();
		
		fetchStock();
		setInterval(fetchStock, 50000);

		fetchRequest();
		setInterval(fetchRequest, 50000);
	});

	var stock = [];
	var employee = [];
	var vdo;
	var shoesName = '';


	var sizeChart = [
	{type : 'Cheetah', gender : 'L', uk : '4', ind:'37'},
	{type : 'Cheetah', gender : 'L', uk : '5', ind:'38'},
	{type : 'Cheetah', gender : 'L', uk : '6', ind:'39-40'},
	{type : 'Cheetah', gender : 'L', uk : '7', ind:'41'},
	{type : 'Cheetah', gender : 'L', uk : '8', ind:'42'},
	{type : 'Cheetah', gender : 'L', uk : '9', ind:'43'},
	{type : 'Cheetah', gender : 'L', uk : '10', ind:'44-45'},
	{type : 'Cheetah', gender : 'L', uk : '11', ind:'46'},
	{type : 'Cheetah', gender : 'L', uk : '12', ind:'47'},
	{type : 'Kings', gender : 'L', uk : '3', ind:'36'},
	{type : 'Kings', gender : 'L', uk : '4', ind:'37'},
	{type : 'Kings', gender : 'L', uk : '5', ind:'38'},
	{type : 'Kings', gender : 'L', uk : '6', ind:'39-40'},
	{type : 'Kings', gender : 'L', uk : '7', ind:'41'},
	{type : 'Kings', gender : 'L', uk : '8', ind:'42'},
	{type : 'Kings', gender : 'L', uk : '9', ind:'43'},
	{type : 'Kings', gender : 'L', uk : '10', ind:'44-45'},
	{type : 'Kings', gender : 'L', uk : '11', ind:'46'},
	{type : 'Krisbow', gender : 'L', uk : '5', ind:'38'},
	{type : 'Krisbow', gender : 'L', uk : '6', ind:'39'},
	{type : 'Krisbow', gender : 'L', uk : '6.5', ind:'40'},
	{type : 'Krisbow', gender : 'L', uk : '7', ind:'41'},
	{type : 'Krisbow', gender : 'L', uk : '8', ind:'42'},
	{type : 'Krisbow', gender : 'L', uk : '9', ind:'43'},
	{type : 'Krisbow', gender : 'L', uk : '10', ind:'44'},
	{type : 'Cheetah', gender : 'P', uk : '2.5', ind:'35'},
	{type : 'Cheetah', gender : 'P', uk : '3.5', ind:'36'},
	{type : 'Cheetah', gender : 'P', uk : '4', ind:'37'},
	{type : 'Cheetah', gender : 'P', uk : '5', ind:'38'},
	{type : 'Cheetah', gender : 'P', uk : '6', ind:'39'},
	{type : 'Cheetah', gender : 'P', uk : '6.5', ind:'40'},
	{type : 'Kings', gender : 'P', uk : '3', ind:'35'},
	{type : 'Kings', gender : 'P', uk : '4', ind:'36'},
	{type : 'Kings', gender : 'P', uk : '5', ind:'37'},
	{type : 'Kings', gender : 'P', uk : '6', ind:'38'},
	{type : 'Kings', gender : 'P', uk : '7', ind:'39'},
	{type : 'Kings', gender : 'P', uk : '8', ind:'40'},
	];

	function stopScan() {
		$('#modalScan').modal('hide');
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

	$('#modalScan').on('shown.bs.modal', function(){
		showCheck('123');
	});

	$('#modalScan').on('hidden.bs.modal', function () {
		videoOff();
		$('#receiveShoes').html("");
	});

	$('#modalReprint').on('hidden.bs.modal', function () {
		$("#repPrinter").prop('selectedIndex', 0).change();
	});


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

					receive(video, code.data);

				} else {
					outputMessage.hidden = false;
				}
			}
			requestAnimationFrame(tick);
		}
	}

	function receive(video, data){		
		$('#scanner').hide();

		var request_id = data;
		var data = {
			request_id:request_id
		}
		$.get('<?php echo e(url("scan/std_control/safety_shoes")); ?>', data, function(result, status, xhr){
			if(result.status){
				if(result.data.length > 0){
					$('#receiveShoes').html("");
					var re = "";
					re += '<p style="font-size: 30px; font-weight: bold; text-align: center; margin: 0px;">Requester :</p>';
					re += '<p style="font-size: 30px; font-weight: bold; text-align: center; margin: 0px;">'+result.data[0].name+'</p>';
					re += '<br>';
					re += '<table style="text-align: center; width:100%;"><tbody>';	
					for (var i = 0; i < result.data.length; i++) {
						re += '<tr>';
						re += '<td style="font-size: 2vw; font-weight: bold;">('+result.data[i].gender+')</td>';
						re += '<td style="font-size: 2vw; font-weight: bold;">Size '+result.data[i].size+'</td>';
						re += '<td style="font-size: 2vw; font-weight: bold;">-></td>';
						re += '<td style="font-size: 2vw; font-weight: bold;">'+result.data[i].qty+' Pasang</td>';
						re += '</tr>';
					}
					re += '<tr>';
					re += '<td colspan="2"><button id="reject+'+request_id+'" class="btn btn-danger" style="margin-top: 10%; width: 95%; font-size: 30px; font-weight:bold;" onclick="confirmReceive(id)">TOLAK</button></td>';
					re += '<td colspan="2"><button id="receive+'+request_id+'" class="btn btn-success" style="margin-top: 10%; width: 95%; font-size: 30px; font-weight:bold;" onclick="confirmReceive(id)">TERIMA</button></td>';
					re += '</tr>';
					re += '</tbody></table>';

					$('#receiveShoes').append(re);

				}else{
					$('#receiveShoes').html("");
					showCheck();
				}

			}else{
				$('#receiveShoes').html("");
				showCheck();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function confirmReceive(id) {
		var data = id.split('+');

		var msg = data[0];
		var request_id = data[1];

		var data = {
			msg : msg,
			request_id : request_id
		}

		$('#loading').show();

		$.post('<?php echo e(url("input/std_control/receive_safety_shoes")); ?>', data, function(result, status, xhr){
			if(result.status){

				fetchStock();
				fetchRequest();
				clearAll();

				$('#receiveShoes').html("");
				showCheck();
				$('#loading').hide();
				openSuccessGritter('Success!', result.message);
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
			}
		});
	}


	function clearAll(){
		stock = [];
		employee = [];
		$('#tableAddStockBody').html('');
		$("#addEmp").prop('selectedIndex', 0).change();			
		$("#addSize").val('');			
		$("#addSizeUk").prop('selectedIndex', 0).change();			
		$("#addStatus").prop('selectedIndex', 0).change();
		$("#addQty").val(1);

		$('#tableCreateReqBody').html('');
		$("#reqPrinter").prop('selectedIndex', 0).change();
		$("#reqEmp").prop('selectedIndex', 0).change();
		$("#reqSizeUk").prop('selectedIndex', 0).change();
		$("#reqSize").val('');

		$("#repPrinter").prop('selectedIndex', 0).change();

	}

	$(function () {
		$('.select4').select2({
			dropdownParent: $('#modalCreate')
		});
	})

	$(function () {
		$('.select3').select2({
			dropdownParent: $('#modalRequest')
		});
	})

	$(function () {
		$('.select1').select2({
			dropdownParent: $('#modalReprint')
		});
	})

	$("#addEmp").change(function(){
		$("#addSizeUk").html('');

		var message = '<option value=""></option>';
		for(var i = 0; i < sizeChart.length; i++) {
			message += '<option value="'+sizeChart[i].ind+'(ime)( '+sizeChart[i].gender+' ) - '+sizeChart[i].type+' - Size UK.'+sizeChart[i].uk+'  EUR.'+sizeChart[i].ind+'">( '+sizeChart[i].gender+' ) - '+sizeChart[i].type+' - Size UK.'+sizeChart[i].uk+'  EUR.'+sizeChart[i].ind+'</option>';
		}

		$("#addSizeUk").html(message);
	});

	$("#addSizeUk").change(function(){
		var message = $("#addSizeUk").val();
		var data = message.split('(ime)');
		$("#addSize").val(data[0]);
		shoesName = data[1];
	});


	function addStock(){
		if($('#addEmp').val() != "" && $('#addSize').val() != ""  && $('#addStatus').val() != "" && $('#addQty').val() != ""){
			
			var emp = $('#addEmp').val();
			var size = $('#addSize').val();
			var status = $('#addStatus').val();
			var qty = $('#addQty').val();


			var data = emp.split('_');

			var employee_id = data[0];
			var name = data[1];
			var gender = data[2];
			var group = data[3];

			var data1 = shoesName.replaceAll("(", "");
			var data2 = data1.replaceAll(")", "");
			var data3 = data2.replaceAll(" ", "");
			var data = data3.split('-');

			tableData = "";
			tableData += "<tr id='rowStock"+employee_id+size+data[0]+qty+status+"'>";
			tableData += '<td>'+employee_id+'</td>';
			tableData += '<td>'+name+'</td>';
			tableData += '<td>'+gender+'</td>';
			tableData += '<td>'+group+'</td>';
			tableData += '<td>'+shoesName+'</td>';
			tableData += '<td>'+qty+'</td>';
			tableData += '<td>'+status+'</td>';
			tableData += "<td><a href='javascript:void(0)' onclick='remStock(id)' id='"+employee_id+size+data[0]+qty+status+"' class='btn btn-danger btn-xs' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
			tableData += '</tr>';

			stock.push({
				'employee_id' : employee_id,
				'merk' : data[1],
				'gender' : data[0],
				'size' : size,
				'qty' : qty,
				'status' : status
			});

			$('#tableAddStockBody').append(tableData);

			$("#addEmp").prop('selectedIndex', 0).change();			
			$("#addSizeUk").prop('selectedIndex', 0).change();			
			$("#addStatus").prop('selectedIndex', 0).change();
			$("#addSize").val('');
			$("#addQty").val(1);

			console.log(stock);
		}
		else{
			openErrorGritter('Error!', 'Pilih Ukuran, Gender dan Qty terlebih dahulu');	
		}
	}

	function remStock(id) {
		console.log(id);

		$('#rowStock'+id).remove();

		for (var i = 0; i < stock.length; i++) {
			if((stock[i].employee_id + stock[i].size + stock[i].gender + stock[i].qty + stock[i].status) == id){
				stock.splice(i, 1);
			}
		}
		
		console.log(stock);
	}

	function submitStock(){
		$('#loading').show();

		if(stock.length > 0){
			var data = {
				stock : stock
			}

			$.post('{{ url("input/std_control/safety_shoes") }}', data, function(result, status, xhr){
				if(result.status){
					$('#modalCreate').modal('hide');
					openSuccessGritter('Success!', result.message);

					fetchStock();
					fetchRequest();
					clearAll();
					$('#loading').hide();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!', result.message);
				}
			});
		}else{
			$('#loading').hide();
			openErrorGritter('Error!', 'Semua point form harus diisi');
		}	
	}


	$("#reqEmp").change(function(){
		$("#reqSizeUk").html('');

		var message = $("#reqEmp").val();

		var data = message.split('_');
		var department = data[4];

		var shoesMerk = '';
		if(department != 'Maintenance' && department != 'Production Engineering'){
			shoesMerk = 'Cheetah';
		}

		var message = '<option value=""></option>';
		for(var i = 0; i < sizeChart.length; i++) {
			if(shoesMerk == 'Cheetah'){
				if(sizeChart[i].type == 'Cheetah'){
					message += '<option value="'+sizeChart[i].ind+'(ime)( '+sizeChart[i].gender+' ) - '+sizeChart[i].type+' - Size UK.'+sizeChart[i].uk+'  EUR.'+sizeChart[i].ind+'">( '+sizeChart[i].gender+' ) - '+sizeChart[i].type+' - Size UK.'+sizeChart[i].uk+'  EUR.'+sizeChart[i].ind+'</option>';
				}
			}else{
				message += '<option value="'+sizeChart[i].ind+'(ime)( '+sizeChart[i].gender+' ) - '+sizeChart[i].type+' - Size UK.'+sizeChart[i].uk+'  EUR.'+sizeChart[i].ind+'">( '+sizeChart[i].gender+' ) - '+sizeChart[i].type+' - Size UK.'+sizeChart[i].uk+'  EUR.'+sizeChart[i].ind+'</option>';
			}
		}
		$("#reqSizeUk").html(message);
	});

	function addReq(){
		if($('#reqEmp').val() != ""  && $('#reqSizeUk').val()){

			var emp = $('#reqEmp').val();
			var shoesName = $('#reqSizeUk').val();

			var data = shoesName.split('(ime)');
			var size = data[0];
			var shoes = data[1];

			var data1 = shoes.replaceAll("(", "");
			var data2 = data1.replaceAll(")", "");
			var data3 = data2.replaceAll(" ", "");
			var data4 = data3.split('-');
			var merk = data4[1];

			var data = emp.split('_');

			var employee_id = data[0];
			var name = data[1];
			var gender = data[2];
			var group = data[3];

			tableData = "";
			tableData += "<tr id='rowReq"+employee_id+"'>";
			tableData += '<td>'+employee_id+'</td>';
			tableData += '<td>'+name+'</td>';
			tableData += '<td>'+gender+'</td>';
			tableData += '<td>'+group+'</td>';
			tableData += '<td>'+shoes+'</td>';
			tableData += "<td><a href='javascript:void(0)' onclick='remReq(id)' id='"+employee_id+"' class='btn btn-danger btn-xs' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";
			tableData += '</tr>';

			employee.push({
				'employee_id' : employee_id,
				'merk' : merk,
				'size' : size,
				'gender' : gender
			});

			$('#tableCreateReqBody').append(tableData);

			$("#reqEmp").prop('selectedIndex', 0).change();
			$("#reqSizeUk").prop('selectedIndex', 0).change();			


			console.log(employee);
		}
		else{
			openErrorGritter('Error!', 'Pilih Karyawan dan Ukuran Sepatu terlebih dahulu');	
		}
	}

	function remReq(id){
		console.log(id);

		$('#rowReq'+id).remove();

		for (var i = 0; i < employee.length; i++) {
			if(employee[i].employee_id == id){
				employee.splice(i, 1);
			}
		}
		
		console.log(employee);
	}

	function submitReq(){
		$('#loading').show();

		if(employee.length > 0 && $('#reqPrinter').val() != ""){
			var printer = $('#reqPrinter').val();

			var data = {
				employee : employee,
				printer : printer
			}

			$.post('{{ url("input/std_control/req_safety_shoes") }}', data, function(result, status, xhr){
				if(result.status){
					$('#modalRequest').modal('hide');
					openSuccessGritter('Success!', result.message);

					fetchStock();
					fetchRequest();
					clearAll();
					$('#loading').hide();
				}else{
					$('#loading').hide();
					openErrorGritter('Error!', result.message);
				}
			});
		}else{
			$('#loading').hide();
			openErrorGritter('Error!', 'Semua point form harus diisi');
		}		
	}





	function fetchRequest(){
		$.get('{{ url("fetch/std_control/request_safety_shoes") }}', function(result, status, xhr){
			if(result.status){
				var tableData = "";
				$('#tableRequestBody').html('');
				var color = "";
				var no = 1;
				for (var i = 0; i < result.request.length; i++) {
					if (no % 2 === 0 ) {
						color = 'style="background-color: #ffd8b7"';
					} else {
						color = 'style="background-color: #fffcb7"';
					}
					tableData += '<tr '+color+'>';
					tableData += '<td style="padding:0;">'+result.request[i].request_id+'</td>';
					tableData += '<td style="padding:0;">'+result.request[i].created_at+'</td>';
					tableData += '<td style="padding:0;">'+result.request[i].name+'</td>';
					tableData += '<td style="padding:0;">'+result.request[i].l+'</td>';
					tableData += '<td style="padding:0;">'+result.request[i].p+'</td>';
					tableData += '<td style="padding:0;"><button class="btn btn-primary btn-sm" onclick="reprintSlip(\''+result.request[i].request_id+'\')"><i class="fa fa-print"></i></button></td>';
					tableData += '<td style="padding:0;"><button class="btn btn-success btn-sm" onclick="fetchDetailRequest(\''+result.request[i].request_id+'\')"><i class="fa fa-list"></i></button></td>';
					tableData += '</tr>';
					no ++;
				}

				$('#tableRequestBody').append(tableData);
			}
			else{
				openErrorGritter('Error!', 'Attempt to retrieve data failed');
			}
		});
	}

	function reprintSlip(id){
		$('#modalReprint').modal('show');
		$('#repRequestId').val(id);		
	}

	function submitReprint(){
		var printer = $('#repPrinter').val();
		var re_request_id = $('#repRequestId').val();

		var data = {
			request_id : re_request_id,
			printer : printer
		}

		$.get('{{ url("reprint/std_control/safety_shoes") }}', data, function(result, status, xhr){
			if(result.status){
				$("#repPrinter").prop('selectedIndex', 0).change();
				$("#repRequestId").val('');
				$('#modalReprint').modal('hide');

				openSuccessGritter('Success!', 'Reprint Success');
			}else{
				openErrorGritter('Error!', 'Attempt to retrieve data failed');
			}
		});
	}

	function fetchDetailRequest(id){
		$('#loading').show();

		var data = {
			request_id:id
		}

		$.get('{{ url("fetch/std_control/detail_safety_shoes") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableDetailRequestBody').html("");

				$('#detail_request_id').text(id);
				$('#detail_requester').text('Requester : '+ result.data[0].requester);

				var detail = '';
				$.each(result.data, function(key, value){
					detail += '<tr>';
					detail += '<td>'+value.employee_id+'</td>';
					detail += '<td>'+value.name+'</td>';
					detail += '<td>'+value.gender+'</td>';
					detail += '<td>'+value.department+'</td>';
					detail += '<td>'+value.section+'</td>';
					detail += '<td>'+(value.group || '')+'</td>';
					detail += '<td>'+value.size+'</td>';
					detail += '</tr>';
				});

				$('#tableDetailRequestBody').append(detail);

				$('#modalDetail').modal('show');
				$('#loading').hide();
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function fetchStock(){
		$.get('{{ url("fetch/std_control/safety_shoes") }}', function(result, status, xhr){
			if(result.status){

				var series = [];
				var male = [];
				var female = [];

				for (var g = 0; g < result.data.length; g++) {
					if(!series.includes('Size '+result.data[g].size)){
						series.push('Size '+result.data[g].size);
					}
				}

				series.sort();

				for (var h = 0; h < series.length; h++) {
					var maleInserted = false;
					var femaleInserted = false;

					for (var i = 0; i < result.resume.length; i++) {
						if(result.resume[i].gender == 'L' && 'Size '+result.resume[i].size == series[h]){
							male.push(parseInt(result.resume[i].quantity));
							maleInserted = true;
						}
					}

					for (var i = 0; i < result.resume.length; i++) {
						if(result.resume[i].gender == 'P' && 'Size '+result.resume[i].size == series[h]){
							female.push(parseInt(result.resume[i].quantity));
							femaleInserted = true;
						}
					}

					if(!maleInserted){
						male.push(0);
					}

					if(!femaleInserted){
						female.push(0);
					}		
				}

				Highcharts.chart('container1', {
					chart: {
						type: 'column',
						backgroundColor: null
					},
					title: {
						text: 'Safety Shoes Stock'
					},
					xAxis: {
						categories: series,
						crosshair: true
					},
					yAxis: {
						min: 0,
						title: {
							text: 'Pair(s)'
						}
					},
					tooltip: {
						headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
						pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y}</b></td></tr>',
						footerFormat: '</table>',
						shared: true,
						useHTML: true
					},
					credits: {
						enabled:false
					},
					plotOptions: {
						column: {
							pointPadding: 0.2,
							borderWidth: 0,
							point: {
								events: {
									click: function (event) {
										showStockDetail(event.point.series.name, event.point.category);

									}
								}
							},
						},
						series:{
							animation: false,
							borderWidth: 0.93,
						},
					},
					series: [{
						name: 'Laki-Laki',
						data: male,
						color: '#aab6fe'
					}, {
						name: 'Perempuan',
						data: female,
						color: '#ff5c8d'
					}]
				});





				var total_male = 0;
				var total_female = 0;

				for (var i = 0; i < male.length; i++) {
					total_male += male[i] << 0;
				}

				for (var i = 0; i < female.length; i++) {
					total_female += female[i] << 0;
				}

				Highcharts.chart('container2', {
					chart: {
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false,
						type: 'pie',
						backgroundColor: null
					},
					title: {
						text: null
					},
					accessibility: {
						point: {
							valueSuffix: '%'
						}
					},
					credits: {
						enabled: false
					},
					plotOptions: {
						pie: {
							size: 230,
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								format: '<b>{point.name}</b><br>{point.y} Pasang',
								distance: -50,
								style:{
									textOutline: false,
									fontSize: '1vw'
								}
							},
							showInLegend: true
						}
					},
					tooltip: {
						pointFormat: 'Stok: <b>{point.y} Pasang</b>'
					},
					series: [{
						data: [{
							name: 'Laki-laki',
							y: total_male,
							color: '#aab6fe'
						}, {
							name: 'Perempuan',
							y: total_female,
							color: '#ff5c8d'
						}]
					}]
				});





				var total_cheetah = 0;
				var total_kings = 0;
				var total_krisbow = 0;

				for (var i = 0; i < result.data.length; i++) {
					if(result.data[i].merk == 'Cheetah'){
						total_cheetah += result.data[i].quantity;
					}else if(result.data[i].merk == 'Kings'){
						total_kings += result.data[i].quantity;
					}else if(result.data[i].merk == 'Krisbow'){
						total_krisbow += result.data[i].quantity;
					}
				}

				Highcharts.chart('container3', {
					chart: {
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false,
						type: 'pie',
						backgroundColor: null
					},
					title: {
						text: null
					},
					accessibility: {
						point: {
							valueSuffix: '%'
						}
					},
					credits: {
						enabled: false
					},
					plotOptions: {
						pie: {
							size: 230,
							allowPointSelect: true,
							cursor: 'pointer',
							showInLegend: true
						}
					},
					tooltip: {
						pointFormat: 'Stok: <b>{point.y} Pasang</b>'
					},
					series: [{
						data: [{
							name: 'Cheetah',
							y: total_cheetah,
							color: '#2b908f'
						}, {
							name: 'Kings',
							y: total_kings,
							color: '#90ee7e'
						}, {
							name: 'Krisbow',
							y: total_krisbow,
							color: '#f45b5b'
						}]
					}]
				});				

			}else{
				alert('Attempt to retrieve data failed.');
			}
		});
}

function showStockDetail(series, size) {
	$('#loading').show();

	var data = {
		gender: series[0],
		size: size.replaceAll('Size ', '')
	}

	$.get('{{ url("fetch/std_control/safety_shoes_detail") }}', data, function(result, status, xhr){
		if(result.status){
			$('#tableDetailStockBody').html("");

			$('#detail_stock').text('Stock Safety Shoes ' + series + ' ' + size);

			var detail = '';
			$.each(result.data, function(key, value){
				detail += '<tr>';
				detail += '<td>'+value.merk+'</td>';
				detail += '<td>'+value.gender+'</td>';
				detail += '<td>'+value.size+'</td>';
				detail += '<td>'+value.quantity+'</td>';
				detail += '</tr>';
			});

			$('#tableDetailStockBody').append(detail);

			$('#modalStock').modal('show');
			$('#loading').hide();
		}
		else{
			openErrorGritter('Error!', result.message);
		}
	});
}

Highcharts.createElement('link', {
	href: '{{ url("fonts/UnicaOne.css")}}',
	rel: 'stylesheet',
	type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
	colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
	'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
	chart: {
		backgroundColor: {
			linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
			stops: [
			[0, '#2a2a2b'],
			[1, '#3e3e40']
			]
		},
		style: {
			fontFamily: 'sans-serif'
		},
		plotBorderColor: '#606063'
	},
	title: {
		style: {
			color: '#E0E0E3',
			textTransform: 'uppercase',
			fontSize: '20px'
		}
	},
	subtitle: {
		style: {
			color: '#E0E0E3',
			textTransform: 'uppercase'
		}
	},
	xAxis: {
		gridLineColor: '#707073',
		labels: {
			style: {
				color: '#E0E0E3'
			}
		},
		lineColor: '#707073',
		minorGridLineColor: '#505053',
		tickColor: '#707073',
		title: {
			style: {
				color: '#A0A0A3'

			}
		}
	},
	yAxis: {
		gridLineColor: '#707073',
		labels: {
			style: {
				color: '#E0E0E3'
			}
		},
		lineColor: '#707073',
		minorGridLineColor: '#505053',
		tickColor: '#707073',
		tickWidth: 1,
		title: {
			style: {
				color: '#A0A0A3'
			}
		}
	},
	tooltip: {
		backgroundColor: 'rgba(0, 0, 0, 0.85)',
		style: {
			color: '#F0F0F0'
		}
	},
	plotOptions: {
		series: {
			dataLabels: {
				color: 'white'
			},
			marker: {
				lineColor: '#333'
			}
		},
		boxplot: {
			fillColor: '#505053'
		},
		candlestick: {
			lineColor: 'white'
		},
		errorbar: {
			color: 'white'
		}
	},
	legend: {
		itemStyle: {
			color: '#E0E0E3'
		},
		itemHoverStyle: {
			color: '#FFF'
		},
		itemHiddenStyle: {
			color: '#606063'
		}
	},
	credits: {
		style: {
			color: '#666'
		}
	},
	labels: {
		style: {
			color: '#707073'
		}
	},

	drilldown: {
		activeAxisLabelStyle: {
			color: '#F0F0F3'
		},
		activeDataLabelStyle: {
			color: '#F0F0F3'
		}
	},

	navigation: {
		buttonOptions: {
			symbolStroke: '#DDDDDD',
			theme: {
				fill: '#505053'
			}
		}
	},

	rangeSelector: {
		buttonTheme: {
			fill: '#505053',
			stroke: '#000000',
			style: {
				color: '#CCC'
			},
			states: {
				hover: {
					fill: '#707073',
					stroke: '#000000',
					style: {
						color: 'white'
					}
				},
				select: {
					fill: '#000003',
					stroke: '#000000',
					style: {
						color: 'white'
					}
				}
			}
		},
		inputBoxBorderColor: '#505053',
		inputStyle: {
			backgroundColor: '#333',
			color: 'silver'
		},
		labelStyle: {
			color: 'silver'
		}
	},

	navigator: {
		handles: {
			backgroundColor: '#666',
			borderColor: '#AAA'
		},
		outlineColor: '#CCC',
		maskFill: 'rgba(255,255,255,0.1)',
		series: {
			color: '#7798BF',
			lineColor: '#A6C7ED'
		},
		xAxis: {
			gridLineColor: '#505053'
		}
	},

	scrollbar: {
		barBackgroundColor: '#808083',
		barBorderColor: '#808083',
		buttonArrowColor: '#CCC',
		buttonBackgroundColor: '#606063',
		buttonBorderColor: '#606063',
		rifleColor: '#FFF',
		trackBackgroundColor: '#404043',
		trackBorderColor: '#404043'
	},

	legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
	background2: '#505053',
	dataLabelsColor: '#B0B0B3',
	textColor: '#C0C0C0',
	contrastTextColor: '#F0F0F3',
	maskColor: 'rgba(255,255,255,0.3)'
};
Highcharts.setOptions(Highcharts.theme);

Highcharts.setOptions({
	global: {
		useUTC: true,
		timezoneOffset: -420
	}
});

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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