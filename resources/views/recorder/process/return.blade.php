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
			<div class="box box-solid">
				<div class="box-body">
					<span style="font-size: 15px; font-weight: bold;">ITEM LIST:</span>
					<table class="table table-hover table-striped" id="tableList">
						<thead>
							<tr>
								<th style="width: 1%;">#</th>
								<th style="width: 2%;">Material Number</th>
								<th style="width: 5%;">Desc</th>
								<th style="width: 2%;">Part</th>
								<th style="width: 2%;">Color</th>
							</tr>					
						</thead>
						<tbody id="tableBodyList">
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col-xs-7">
			<div class="row">
				<div class="col-xs-12">
					<div class="row">
						<span style="font-weight: bold; font-size: 16px;">Material:</span>
						<input type="text" id="material_number" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
					
						<span style="font-weight: bold; font-size: 16px;">Description:</span>
						<input type="text" id="material_description" style="width: 100%; height: 50px; font-size: 24px; text-align: center;" disabled>
					
						<div class="row">
							<div class="col-xs-4">
								<span style="font-weight: bold; font-size: 16px;">Part:</span>
								<input type="text" id="part_code" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
							</div>
							<div class="col-xs-4">
								<span style="font-weight: bold; font-size: 16px;">Type:</span>
								<input type="text" id="part_type" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
							</div>
							<div class="col-xs-4">
								<span style="font-weight: bold; font-size: 16px;">Color:</span>
								<input type="text" id="color" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
							</div>
						</div>
					
						<span style="font-weight: bold; font-size: 16px;">Add Count:</span>
					
					
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
								<button class="btn btn-primary" onclick="finishTransaction()" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;">
									FINISH
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="box box-solid">
							<div class="box-body">
								<span style="font-size: 20px; font-weight: bold;">RETURN HARI INI (<?php echo e(date('d-M-Y')); ?>)</span>
								<table class="table table-hover table-striped table-bordered" id="tableResume">
									<thead>
										<tr>
											<th style="width: 1%;">#</th>
											<th style="width: 1%;">Material</th>
											<th style="width: 6%;">Desc</th>
											<th style="width: 1%;">Issue</th>
											<th style="width: 1%;">Receive</th>
											<th style="width: 1%;">Qty</th>
											<th style="width: 1%;">Creator</th>
											<th style="width: 1%;">Created</th>
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
	</div>
</section>

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

		fetchProductList();

		fetchResume();

		$('#material_number').val('');
		$('#material_description').val('');
		$('#part_code').val('');
		$('#part_type').val('');
		$('#color').val('');
		$('#quantity').val('0');
		
		$('.select2').select2();
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
	});

	function plusCount(){
		$('#quantity').val(parseInt($('#quantity').val())+1);
	}

	function minusCount(){
		$('#quantity').val(parseInt($('#quantity').val())-1);
	}

	function fetchProductList(){
		$.get('{{ url("fetch/recorder/return/product") }}',function(result, status, xhr){
			if(result.status){
				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$('#tableBodyList').html("");
				var tableData = "";
				var count = 1;
				$.each(result.datas, function(key, value) {
					var part = value.part_name.split(' ');
					tableData += '<tr onclick="fetchProduct(\''+value.gmc+'\''+','+'\''+value.part_name+'\''+','+'\''+value.part_code.toUpperCase()+'\''+','+'\''+value.part_type.toUpperCase()+'\''+','+'\''+value.color+'\')">';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.gmc +'</td>';
					tableData += '<td>'+ value.part_name +'</td>';
					tableData += '<td>'+ value.part_code.toUpperCase() +'<br>'+ value.part_type.toUpperCase() +'</td>';
					tableData += '<td>'+ value.color +'</td>';
					tableData += '</tr>';

					count += 1;
				});
				$('#tableBodyList').append(tableData);

				var table = $('#tableList').DataTable({
					'dom': 'Bfrtip',
						'responsive':true,
						'lengthMenu': [
						[ 10, 25, 50, -1 ],
						[ '10 rows', '25 rows', '50 rows', 'Show all' ]
						],
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
							
							]
						},
						'paging': true,
						'lengthChange': true,
						'pageLength': 5,
						'searching': true	,
						'ordering': true,
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
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function fetchProduct(material_number,material_description,part_code,part_type,color) {
		$('#material_number').val(material_number);
		$('#material_description').val(material_description);
		$('#part_code').val(part_code);
		$('#part_type').val(part_type);
		$('#color').val(color);
	}

	function finishTransaction() {
		if ($('#quantity').val() == 0) {
			alert('Semua Data Harus Diisi');
		}else{
			if (confirm("Apakah Anda Yakin?")) {
				var material_number = $('#material_number').val();
				var material_description = $('#material_description').val();
				var part_code = $('#part_code').val();
				var part_type = $('#part_type').val();
				var color = $('#color').val();
				var quantity = $('#quantity').val();

				var data = {
					material_number:material_number,
					material_description:material_description,
					part_code:part_code,
					part_type:part_type,
					color:color,
					quantity:quantity,
				}

				$.post('{{ url("input/recorder/return") }}',data,function(result, status, xhr){
					if (result.status) {
						fetchResume();
						$('#material_number').val('');
						$('#material_description').val('');
						$('#part_code').val('');
						$('#part_type').val('');
						$('#color').val('');
						$('#quantity').val('0');
						openSuccessGritter('Success',result.message);
					}else{
						openErrorGritter('Error!',result.message);
					}
				});
			}
		}
	}

	function fetchResume(){
		$("#loading").show();
		$.get('<?php echo e(url("fetch/recorder/return/resume")); ?>', function(result, status, xhr){
			$('#tableBodyResume').html("");
			var tableData = "";
			var count = 1;
			$.each(result.return, function(key, value) {
				tableData += '<tr>';
				tableData += '<td>'+ count +'</td>';
				tableData += '<td>'+ value.material_number +'</td>';
				tableData += '<td>'+ value.material_description +'</td>';
				tableData += '<td>'+ value.part_code +'</td>';
				tableData += '<td>'+ value.part_type +'</td>';
				tableData += '<td>'+ value.color +'</td>';
				tableData += '<td>'+ value.quantity +'</td>';
				tableData += '<td>'+ value.name +'</td>';
				tableData += '<td>'+ value.created +'</td>';

				count += 1;
			});
			$('#tableBodyResume').append(tableData);
			$("#loading").hide();
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