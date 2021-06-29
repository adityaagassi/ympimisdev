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

	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		font-size: 13px;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:10px;
		font-size: 13px;
		text-align: center;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}

	table.table-bordered1{
		border:1px solid black;
	}
	table.table-bordered1 > thead > tr > th{
		border:1px solid black;
		font-size: 10px;
		text-align: center;
	}
	table.table-bordered1 > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:0;
		font-size: 10px;
		text-align: center;
		padding:10px;
	}
	table.table-bordered1 > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}

	.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}

	/*table.table-bordered > thead > tr > th{
	    border:1px solid rgb(54, 59, 56);
	    text-align: center;
	    background-color: rgba(126,86,134);  
	    color:white;
	    font-size: 13px;
  	}
  	table.table-bordered > tbody > tr > td{
	    border-collapse: collapse !important;
	    border:1px solid rgb(54, 59, 56);
	    /*background-color: #ffffff;*/
	    color: black;
	    vertical-align: middle;
	    text-align: center;
	    padding:10px;
	    font-size: 13px;
  	}*/

  	/*table.table-bordered1 > thead > tr > th{
	    border:1px solid rgb(54, 59, 56);
	    text-align: center;
	    background-color: rgba(126,86,134);  
	    color:white;
	    font-size: 10px;
  	}
  	table.table-bordered1 > tbody > tr > td{
	    border-collapse: collapse !important;
	    border:1px solid rgb(54, 59, 56);
	    /*background-color: #ffffff;*/
	    color: black;
	    vertical-align: middle;
	    text-align: center;
	    padding:10px;
	    font-size: 10px;
  	}*/

	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
	/*.select2-results { background-color: #00f; }
	.select2-search { background-color: #00f; }
	.select2-search input { background-color: #00f; }*/

	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}
	
	#loading { display: none; }
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('header'); ?>
<section class="content-header">
	<h1>
		<?php echo e($title); ?>

		<!-- <small><span class="text-purple"> <?php echo e($title_jp); ?></span></small> -->
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
	<input type="hidden" id="id">
	<div class="row">
		<div class="col-xs-5">
			<!-- <label style="font-weight: bold; font-size: 16px;">Store : <span class="text-red">*</span></label> -->
			<div>
				<select class="form-select select2" id="store" name="store" data-placeholder='Select Store ...' style="width: 100%; height: 80px; font-size: 30px; text-align: center;" onchange="FetchData(this.value)" required >
				<option></option>
				@foreach($stores as $store)
				<option value="{{ $store->store }}">{{ $store->store }}</option>
				@endforeach
			</select>	
			</div>
			<div class="box">
				<div class="box-body">
					<table class="table table-hover table-striped table-bordered" id="tableList" style="width: 100%;" >
						<thead style="background-color: rgb(126,86,134); color: #FFD700;">
							<tr>
								<th style="width: 1%;">No</th>
								<th style="width: 2%;">Material Number</th>
								<th style="width: 6%;">Material Description</th>
								<th style="width: 1%;">Category</th>
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
					<label style="font-weight: bold; font-size: 16px;">Material Number : </label>
					<input type="text" id="material" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
				</div>
				<div class="col-xs-12">
					<label style="font-weight: bold; font-size: 16px;">Material Description : </label>
					<input type="text" id="description" name="material_description" style="width: 100%; height: 50px; font-size: 24px; text-align: center;" disabled>
				</div>
				<div class="col-xs-12">
					<label style="font-weight: bold; font-size: 16px;">Category : </label>
					<input type="text" id="category" name="category" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
				</div>
				<div class="col-xs-12">
					<label style="font-weight: bold; font-size: 16px;">QTY : <span class="text-red">*</span></label>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-8">
							<div class="input-group">
								<div class="input-group-btn">
									<button type="button" class="btn btn-danger" style="font-size: 35px; height: 60px; text-align: center;"  onclick="minusCount()"><span class="fa fa-minus"></span></button>
								</div>
								<input id="quantity" name="quantity" style="font-size: 40px; height: 60px; text-align: center;" type="number" class="form-control numpad" value="0">

								<div class="input-group-btn">
									<button type="button" class="btn btn-success" style="font-size: 35px; height: 60px; text-align: center;" onclick="plusCount()"><span class="fa fa-plus"></span></button>
								</div>
							</div>
						</div>
						<div class="col-xs-4" style="padding-bottom: 10px;">
							<button class="btn btn-primary" onclick="UpdateQty()" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;">Checked
							</button>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
						<!-- <div>
							<select class="form-select select2" id="store1" name="store1" data-placeholder='Select Store ...' style="width: 100%; height: 80px; font-size: 30px; text-align: center;" onchange="fetchResume(this.value)" required >
							<option></option>
							@foreach($stores as $store)
							<option value="{{ $store->store }}">{{ $store->store }}</option>
							@endforeach
						</select>	
						</div> -->
					<div class="box">
						<div class="box-body">
							<span style="font-size: 20px; font-weight: bold;">Audit Stock Actual : <?php echo e(date('d-M-Y')); ?></span>
							<!-- <button style="margin: 1%;" class="btn btn-info pull-right" onClick="refreshTable();"><i class="fa fa-refresh"></i> Refresh Tabel</button> -->
							<table class="table table-hover table-striped table-bordered" id="tableResume">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th>No</th>
										<th>Material Number</th>
										<th>Material Description</th>
										<th>Category</th>
										<th>Ideal Stock</th>
										<th>Actual Stock</th>
										<th>Auditor</th>
										<th>Date</th>
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
						<select class="form-control select2" onchange="FetchData()" data-placeholder="Select Your Locations..." style="width: 100%; font-size: 20px;" id="select_loc">
							<option></option>
							@foreach($storage_locations as $storage_location)
							<option value="{{ $storage_location->storage_location }}">{{ $storage_location->storage_location }}</option>
							@endforeach
						</select>
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

		// setInterval(function(){
	 //      fetchResume($('#location').val());
	 //    }, 15000);

	 	// selectType();
		
	});


	function plusCount(){
		$('#quantity').val(parseInt($('#quantity').val())+1);
	}

	function minusCount(){
		$('#quantity').val(parseInt($('#quantity').val())-1);
	}


	function confirmReceive(id){
		$('#loading').show();
		var data = {
			id:id
		}
		$.post('<?php echo e(url("confirm/scrap")); ?>', data, function(result, status, xhr){
			if(result.status){

				$('#receiveScrap').html("");
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

	function UpdateQty(){


		$('#loading').show();
		var material = $('#material').val();
		var description = $('#description').val();
		var category = $('#category').val();
		var quantity = $('#quantity').val();
		var id = $('#id').val();
		$('#location').val($("#select_loc").val());
		var loc = $('#location').val();


		if(material == '' || description == '' || category == '' || quantity == ''){
			$('#loading').hide();
			openErrorGritter('Error!', 'Isi data secara lengkap');
			return false;
		}
		if(quantity == '' || quantity == 0){
			$('#loading').hide();
			openErrorGritter('Error!', 'Isikan quantity yang akan di scrap');
			return false;
		}

		var data = {
			id:id,
			material:material,
			description:description,
			category:category,
			quantity:quantity,
			loc:loc
		}

		$.post('<?php echo e(url("stock/aktual/update")); ?>', data, function(result, status, xhr){
			if(result.status){
				fetchResume(loc);
				reset();

				$('#loading').hide();
				openSuccessGritter('Success', result.message);
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
			}

		});
	}


	function fetchReturn(material, description, category, id ){
		$('#material').val(material);
		$('#description').val(description);
		$('#id').val(id);

		if(category == 'SINGLE'){
			$('#category').val(category).css('background-color', 'rgb(250,250,210)');
		}
		else{
			$('#category').val(category).css('background-color', 'rgb(135,206,250)');
		}
	}

	function reset(){
		$('#material').val("");
		$('#description').val("");
		$('#category').val("");
		$('#quantity').val(0);
	} 

	function fetchResume(type){
		$('#location').val($("#select_loc").val());
		var loc = $('#location').val();
		var tipe = type;

		var data = {
			loc:loc,
			store1:type
		}
		$.get('<?php echo e(url("stock/aktual/resume")); ?>', data, function(result, status, xhr){
			$('#tableBodyResume').html("");
			var tableData = "";
			var count = 1;
			$.each(result.resumes, function(key, value) {
			
				tableData += '<tr style="background-color: #F0E68C;">';
				tableData += '<td>'+ count +'</td>';
				tableData += '<td>'+ value.material_number +'</td>';
				tableData += '<td>'+ value.material_description +'</td>';
				if(value.category == 'SINGLE'){
					tableData += '<td style="background-color: rgb(250,250,210); text-align: center;">'+ value.category +'</td>';
				}
				else{
					tableData += '<td style="background-color: rgb(135,206,250); text-align: center;">'+ value.category +'</td>';
				}
				tableData += '<td>'+ value.ideal +'</td>';
				tableData += '<td>'+ value.actual +'</td>';
				tableData += '<td>'+ value.name +'</td>';
				tableData += '<td>'+ value.updated_at +'</td>';
				tableData += '</tr>';	
				
				
				count += 1;
			});
			$('#tableBodyResume').append(tableData);
		});
	}


	function FetchData(type){
		$('#location').val($("#select_loc").val());
		var loc = $('#location').val();
		var tipe = type;

		fetchResume($('#location').val());

		var data = {
			loc:loc,
			store:type
		}
		$.get('<?php echo e(url("stock/aktual/list")); ?>', data, function(result, status, xhr){
			if(result.status){

				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				var tableData = '';
				$('#tableBodyList').html("");
				$('#tableBodyList').empty();
				
				var count = 1;
				$.each(result.lists, function(key, value) {
					// var str = value.description;
					// var desc = str.replace("'", "");
					tableData += '<tr onclick="fetchReturn(\''+value.material_number+'\''+','+'\''+value.material_description+'\''+', '+'\''+value.category+'\''+', '+'\''+value.id+'\''+')">';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ value.material_description +'</td>';
					if(value.category == 'SINGLE'){
						tableData += '<td style="background-color: rgb(250,250,210); text-align: center;">'+ value.category +'</td>';
					}
					else{
						tableData += '<td style="background-color: rgb(135,206,250); text-align: center;">'+ value.category +'</td>';
					}
					tableData += '</tr>';
					count += 1;
				});

				$('#tableBodyList').append(tableData);
				var tableList = $('#tableList').DataTable({
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
						}
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

				// openSuccessGritter('Success!', result.message);
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