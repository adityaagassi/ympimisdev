<?php $__env->startSection('stylesheets'); ?>
<link href="<?php echo e(url("css/jquery.gritter.css")); ?>" rel="stylesheet">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		font-size: 16px;
	}
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#tableBodyResume > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
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

	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}
	
	#loading { display: none; }
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('header'); ?>
<section class="content-header">
	<h1>
		<?php echo e($title); ?>

		<small><span class="text-purple"> <?php echo e($title_jp); ?></span></small>
	</h1>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Sedang memproses, tunggu sebentar <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="location">
	<div class="row">
		<div class="col-xs-5">
			<div class="box">
				<div class="box-body">
					<span style="font-size: 20px; font-weight: bold;">DAFTAR ITEM:</span>
					<table class="table table-hover table-striped" id="tableList" style="width: 100%;">
						<thead>
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 1%;">Material</th>
								<th style="width: 7%;">Description</th>
								<th style="width: 1%;">Kirim</th>
								<th style="width: 1%;">Terima</th>
							</tr>					
						</thead>
						<tbody id="tableBodyList">
						</tbody>
						<tfoot>
							<tr>
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
		<div class="col-xs-7">
			<div class="row">
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Material:</span>
					<input type="text" id="material_number" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
				</div>
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Description:</span>
					<input type="text" id="material_description" style="width: 100%; height: 50px; font-size: 24px; text-align: center;" disabled>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Issue Location:</span>
							<input type="text" id="issue" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Receive Location:</span>
							<input type="text" id="receive" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<span style="font-weight: bold; font-size: 16px;">Add Count:</span>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-7">
							<div class="input-group">
								<div class="input-group-btn">
									<button type="button" class="btn btn-danger" style="font-size: 35px; height: 60px; text-align: center;"><span class="fa fa-minus" onclick="minusCount()"></span></button>
								</div>
								<input id="quantity" style="font-size: 50px; height: 60px; text-align: center;" type="number" class="form-control numpad" value="0">

								<div class="input-group-btn">
									<button type="button" class="btn btn-success" style="font-size: 35px; height: 60px; text-align: center;"><span class="fa fa-plus" onclick="plusCount()"></span></button>
								</div>
							</div>
						</div>
						<div class="col-xs-5" style="padding-bottom: 10px;">
							<button class="btn btn-primary" onclick="printReturn()" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;">
								<i class="fa fa-print"></i> CETAK
							</button>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="box">
						<div class="box-body">
							<span style="font-size: 20px; font-weight: bold;">RETURN BELUM DI KONFIRMASI (<?php echo e(date('d-M-Y')); ?>)</span>
							<table class="table table-hover table-striped table-bordered" id="tableResume">
								<thead>
									<tr>
										<th style="width: 1%;">#</th>
										<th style="width: 1%;">Material</th>
										<th style="width: 6%;">Description</th>
										<th style="width: 1%;">Issue</th>
										<th style="width: 1%;">Receive</th>
										<th style="width: 1%;">Qty</th>
										<th style="width: 1%;">Creator</th>
										<th style="width: 1%;">Created</th>
										<th style="width: 1%;">Delete</th>
										<th style="width: 1%;">Reprint</th>
									</tr>
								</thead>
								<tbody id="tableBodyResume">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalLocation">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<center><h3 style="background-color: #00a65a;">Pilih Lokasi Anda</h3></center>
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<select class="form-control select2" onchange="fetchReturnList(value)" data-placeholder="Pilih Lokasi Anda..." style="width: 100%; font-size: 20px;">
							<option></option>
							@foreach($storage_locations as $storage_location)
							<option value="{{ $storage_location }}">{{ $storage_location }}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>
			<div class="modal-header">
				<center><h3 style="background-color: #ff851b;">Terima Material Return</h3></center>
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
					<div id="receiveReturn" style="width:100%;">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(url("js/jquery.gritter.min.js")); ?>"></script>
<script src="<?php echo e(url("js/dataTables.buttons.min.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.flash.min.js")); ?>"></script>
<script src="<?php echo e(url("js/jszip.min.js")); ?>"></script>
<script src="<?php echo e(url("js/vfs_fonts.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.html5.min.js")); ?>"></script>
<script src="<?php echo e(url("js/buttons.print.min.js")); ?>"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script src="<?php echo e(url("js/jsQR.js")); ?>"></script>

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

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		
		$('.select2').select2();
		$('#modalLocation').modal({
			backdrop: 'static',
			keyboard: false
		});
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
	});

	var vdo;

	function stopScan() {
		$('#modalLocation').modal('hide');
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


	$( "#modalLocation" ).on('shown.bs.modal', function(){
		showCheck('123');
	});

	$('#modalLocation').on('hidden.bs.modal', function () {
		videoOff();
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

					receiveReturn(video, code.data);

				} else {
					outputMessage.hidden = false;
				}
			}
			requestAnimationFrame(tick);
		}

	}

	function plusCount(){
		$('#quantity').val(parseInt($('#quantity').val())+1);
	}

	function minusCount(){
		$('#quantity').val(parseInt($('#quantity').val())-1);
	}

	function receiveReturn(video, data){
		$('#scanner').hide();
		$('#modalReceive').modal('hide');
		$(".modal-backdrop").remove();

		var x = {
			id:data
		}
		$.get('<?php echo e(url("fetch/return")); ?>', x, function(result, status, xhr){
			if(result.status){
				var re = "";
				$('#receiveReturn').html("");
				re += '<table style="text-align: center; width:100%;"><tbody>';
				re += '<tr><td style="font-size: 36px; font-weight: bold;" colspan="2">'+result.return.material_number+'</td></tr>';
				re += '<tr><td style="font-size: 36px; font-weight: bold;" colspan="2">'+result.return.receive_location+' -> '+result.return.issue_location+'</td></tr>';
				re += '<tr><td style="font-size: 26px; font-weight: bold;" colspan="2">'+result.return.material_description+'</td></tr>';
				re += '<tr><td style="font-size: 50px; font-weight: bold; background-color:black; color:white;" colspan="2">'+result.return.quantity+' PC(s)</td></tr>';
				re += '<tr><td style="font-size: 26px; font-weight: bold;" colspan="2">'+result.return.name+'</td></tr>';
				re += '<tr>';
				re += '<td><button id="reject+'+result.return.id+'" class="btn btn-danger" style="width: 95%; font-size: 30px; font-weight:bold;" onclick="confirmReceive(id)">TOLAK</button></td>';
				re += '<td><button id="receive+'+result.return.id+'" class="btn btn-success" style="width: 95%; font-size: 30px; font-weight:bold;" onclick="confirmReceive(id)">TERIMA</button></td>';
				re += '</tr>';
				re += '</tbody></table>';

				$('#receiveReturn').append(re);
			}
			else{
				$('#receiveReturn').html("");
				showCheck();
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function confirmReceive(id){
		$('#loading').show();
		var data = {
			id:id
		}
		$.post('<?php echo e(url("confirm/return")); ?>', data, function(result, status, xhr){
			if(result.status){

				$('#receiveReturn').html("");
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

	function printReturn(){
		$('#loading').show();
		var material = $('#material_number').val();
		var issue = $('#issue').val();
		var receive = $('#receive').val();
		var description = $('#material_description').val();
		var quantity = $('#quantity').val();

		if(material == ''){
			$('#loading').hide();
			openErrorGritter('Error!', 'Pilih material yang akan di return');
			return false;
		}
		if(quantity == '' || quantity == 0){
			$('#loading').hide();
			openErrorGritter('Error!', 'Isikan quantity yang akan di return');
			return false;
		}

		var data = {
			material:material,
			issue:issue,
			receive:receive,
			quantity:quantity,
			description:description
		}
		$.post('<?php echo e(url("print/return")); ?>', data, function(result, status, xhr){
			if(result.status){
				fetchResume(receive);
				$('#material_number').val("");
				$('#issue').val("");
				$('#receive').val("");
				$('#material_description').val("");
				$('#quantity').val(0);

				$('#loading').hide();
				openSuccessGritter('Success', result.message);
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function fetchReturn(material, description,issue,receive){
		$('#material_number').val(material);
		$('#material_description').val(description);
		$('#issue').val(issue);
		$('#receive').val(receive);
	}

	function reprint(id){
		var data = {
			id:id
		}
		$.get('<?php echo e(url("reprint/return")); ?>', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', result.message);
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function fetchResume(loc){
		var data = {
			loc:loc
		}
		$.get('<?php echo e(url("fetch/return/resume")); ?>', data, function(result, status, xhr){
			$('#tableBodyResume').html("");
			var tableData = "";
			var count = 1;
			$.each(result.resumes, function(key, value) {
				tableData += '<tr>';
				tableData += '<td>'+ count +'</td>';
				tableData += '<td>'+ value.material_number +'</td>';
				tableData += '<td>'+ value.material_description +'</td>';
				tableData += '<td>'+ value.issue_location +'</td>';
				tableData += '<td>'+ value.receive_location +'</td>';
				tableData += '<td>'+ value.quantity +'</td>';
				tableData += '<td>'+ value.name +'</td>';
				tableData += '<td>'+ value.created_at +'</td>';
				tableData += '<td><center><button class="btn btn-danger" onclick="deleteReturn('+value.id+')"><i class="fa fa-trash"></i></button></center></td>';
				tableData += '<td><center><button class="btn btn-primary" onclick="reprint('+value.id+')"><i class="fa fa-print"></i></button></center></td>';
				tableData += '</tr>';

				count += 1;
			});
			$('#tableBodyResume').append(tableData);
		});
	}

	function deleteReturn(id){

		if(confirm("Apa Anda yakin anda akan mendelete slip return?")){
			var data = {
				id:id
			}
			$.post('{{ url("delete/return") }}', data, function(result, status, xhr){
				if(result.status){
					fetchResume(result.receive);
					openSuccessGritter('Success!', result.message);
				}
				else{
					openErrorGritter('Error!', result.message);
				}

			});
		}
		else{
			return false;
		}
	}

	function fetchReturnList(loc){
		fetchResume(loc);
		$('#location').val(loc);
		var data = {
			loc:loc
		}
		$.get('<?php echo e(url("fetch/return/list")); ?>', data, function(result, status, xhr){
			if(result.status){
				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$('#tableBodyList').html("");
				var tableData = "";
				var count = 1;
				$.each(result.lists, function(key, value) {
					var str = value.description;
					var desc = str.replace("'", "");
					tableData += '<tr onclick="fetchReturn(\''+value.material_number+'\''+','+'\''+desc+'\''+','+'\''+value.issue_location+'\''+','+'\''+value.receive_location+'\')">';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ desc +'</td>';
					tableData += '<td>'+ value.receive_location +'</td>';
					tableData += '<td>'+ value.issue_location +'</td>';
					tableData += '</tr>';

					count += 1;
				});
				$('#tableBodyList').append(tableData);

				$('#tableList tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="4"/>' );
				});

				var tableList = $('#tableList').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
							// dom: {
							// 	button: {
							// 		tag:'button',
							// 		className:''
							// 	}
							// },
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
							},
							]
						},
						'paging': true,
						'lengthChange': true,
						'pageLength': 20,
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

				tableList.columns().every( function () {
					var that = this;

					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				} );

				$('#tableList tfoot tr').appendTo('#tableList thead');

				openSuccessGritter('Success!', result.message);
				$('#modalLocation').modal('hide');
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});

	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '<?php echo e(url("images/image-screen.png")); ?>',
			sticky: false,
			time: '2000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '<?php echo e(url("images/image-stop.png")); ?>',
			sticky: false,
			time: '2000'
		});
	}

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>