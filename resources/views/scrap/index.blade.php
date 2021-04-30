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
	<div class="row">
		<div class="col-xs-5">
			<label style="font-weight: bold; font-size: 16px;">Category : <span class="text-red">*</span></label>
			<!-- <span style="font-weight: bold; font-size: 16px;">Category:</span> -->
			<select class="form-select select2" id="category" name="category" data-placeholder='Select' style="width: 100%; height: 80px; font-size: 30px; text-align: center;" onchange="selectType(this.value)" required >
				<option value="">&nbsp;</option>
				<option value="ASSY">ASSY</option>
				<option value="SINGLE">SINGLE</option>
			</select><br>
			<div class="box">
				<div class="box-body">
					<!-- <span style="font-size: 20px; font-weight: bold;">DAFTAR ITEM:</span> -->
					<table class="table table-hover table-striped table-bordered" id="tableList" style="width: 100%;" >
						<thead style="background-color: rgb(126,86,134); color: #FFD700;">
							<tr>
								<th style="width: 1%;">No</th>
								<th style="width: 1%;">GMC</th>
								<th style="width: 7%;">Part's Name</th>
								<th style="width: 1%;">Location</th>
								<!-- <th style="width: 1%;">Spt</th> -->
							</tr>					
						</thead>
						<tbody id="tableBodyList">
						</tbody>
						<!-- <tfoot>
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</tfoot> -->
					</table>
				</div>
			</div>
		</div>
		<div class="col-xs-7">
			<div class="row">
				<div class="col-xs-6">
					<label style="font-weight: bold; font-size: 16px;">GMC : </label>
					<!-- <span style="font-weight: bold; font-size: 16px;">GMC:</span> -->
					<input type="text" id="material" style="width: 100%; height: 50px; font-size: 30px; text-align: center; color: red" disabled value="">
				</div>
				<div class="col-xs-6">
					<label style="font-weight: bold; font-size: 16px;">From Location : </label>
					<!-- <span style="font-weight: bold; font-size: 16px;">From Location:</span> -->
					<input type="text" id="issue" name="issue_location" style="width: 100%; height: 50px; font-size: 30px; text-align: center; color: red" disabled>
				</div>
				<div class="col-xs-12">
					<label style="font-weight: bold; font-size: 16px;">Part's Name : </label>
					<!-- <span style="font-weight: bold; font-size: 16px;">Part's Name:</span> -->
					<input type="text" id="description" name="material_description" style="width: 100%; height: 50px; font-size: 24px; text-align: center; color: red" disabled>
				</div>
				<!-- <div class="col-xs-6">
					<label style="font-weight: bold; font-size: 16px;">Category : <span class="text-red">*</span></label>
					<select class="form-control select2" id="category" name="category" data-placeholder='Select' style="width: 100%; height: 50px; font-size: 30px; text-align: center; color: red" required>
						<option value="">&nbsp;</option>
						@foreach($category as $category)
						<option value="{{ $category }}">{{ $category }}</option>
						@endforeach
					</select>
				</div> -->
				
				<!-- <div class="col-xs-6">
					<label style="font-weight: bold; font-size: 16px;">Category Reason : <span class="text-red">*</span></label>
					<select class="form-control select2" id="category_reason" name="category_reason" data-placeholder='Select' style="width: 100%; height: 50px; font-size: 30px; text-align: center;" required>
						<option value="">&nbsp;</option>
						@foreach($category_reason as $category_reason)
						<option value="{{ $category_reason }}">{{ $category_reason }}</option>
						@endforeach
					</select>
				</div> -->
					
				<div class="col-xs-6">
					<div class="row">
						<div class="col-xs-6">
							<span style="font-weight: bold; font-size: 16px;">Summary:</span>
							<textarea id="summary" name="summary" style="width: 218%; height: 100px; font-size: 12px; text-align: left" required></textarea>
						</div>
					</div>
					
					<!-- <div class="row">
						<div class="col-xs-4">
							
						</div>
					</div> -->
				</div>
				<div class="col-xs-6">
					<label style="font-weight: bold; font-size: 16px;">To Location : <span class="text-red">*</span></label>
					<!-- <span style="font-weight: bold; font-size: 16px;">To Location:</span> -->
					<select class="form-control select2" id="receive_location" name="receive_location" data-placeholder='Select' style="width: 100%; height: 50px; font-size: 30px; text-align: center;" required>
						<option value="">&nbsp;</option>
						@foreach($reicive as $reicive)
						<option value="{{ $reicive }}">{{ $reicive }}</option>
						@endforeach
					</select>
				</div>
				<div class="col-xs-6">
					<label style="font-weight: bold; font-size: 16px;">Reason : <span class="text-red">*</span></label>
					<select class="form-control select2" id="reason" name="reason" data-placeholder='Select' style="width: 100%; height: 50px; font-size: 30px; text-align: center;" required>
						<option value="">&nbsp;</option>
						@foreach($reason as $rea)
						<option value="{{$rea->reason}}">{{$rea->reason}} - {{$rea->reason_name}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-xs-6" hidden="hidden">
					<div class="row">
						<div class="col-xs-6">
							<label style="font-weight: bold; font-size: 16px;">SPT : </label>
							<input type="text" id="spt" name="spt" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled value="">
						</div>
					</div>
				</div>
				<div class="col-xs-6" hidden="hidden">
					<div class="row">
						<div class="col-xs-6">
							<label style="font-weight: bold; font-size: 16px;">VALCL : </label>
							<input type="text" id="valcl" name="valcl" style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled value="">
						</div>
					</div>
				</div>
				<!-- <div class="col-xs-6">
					<label style="font-weight: bold; font-size: 16px;">QTY : <span class="text-red">*</span></label>
							<div class="input-group">
								<div class="input-group-btn">
									<button type="button" class="btn btn-danger" style="font-size: 35px; height: 60px; text-align: center;"><span class="fa fa-minus" onclick="minusCount()"></span></button>
								</div>
								<input id="quantity" name="quantity" style="font-size: 50px; height: 60px; text-align: center;" type="number" class="form-control numpad" value="0">

								<div class="input-group-btn">
									<button type="button" class="btn btn-success" style="font-size: 35px; height: 60px; text-align: center;"><span class="fa fa-plus" onclick="plusCount()"></span></button>
								</div>
							</div>
				</div> -->	

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
							<button class="btn btn-primary" onclick="printScrap()" style="font-size: 40px; width: 100%; font-weight: bold; padding: 0;">Tambah
							</button>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="box">
						<div class="box-body">
							<span style="font-size: 20px; font-weight: bold;">List Scrap Tanggal : <?php echo e(date('d-M-Y')); ?></span>
							<!-- <button style="margin: 1%;" class="btn btn-info pull-right" onClick="refreshTable();"><i class="fa fa-refresh"></i> Refresh Tabel</button> -->
							<table class="table table-hover table-striped table-bordered1" id="tableResume">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th>No</th>
										<th>Slip Number</th>
										<th>Part's Name</th>
										<th>Receive Location</th>
										<th>Category</th>
										<th>Qty</th>
										<th>Creator</th>
										<th>Created</th>
										<th>Delete</th>
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
						<select class="form-control select2" onchange="fetchScrapList(value)" data-placeholder="Pilih Lokasi Anda..." style="width: 100%; font-size: 20px;">
							<option></option>
							@foreach($storage_locations as $storage_location)
							<option value="{{ $storage_location }}">{{ $storage_location }}</option>
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

		setInterval(function(){
	      fetchResume($('#location').val());
	    }, 15000);
		
	});


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

		$.get('<?php echo e(url("fetch/scrap")); ?>', x, function(result, status, xhr){
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

	function printScrap(){


		$('#loading').show();
		var material = $('#material').val();
		var issue = $('#issue').val();
		var receive = $('#receive').val();
		var description = $('#description').val();
		var category = $('#category').val();
		var quantity = $('#quantity').val();
		var reason = $('#reason').val();
		var receive_location = $('#receive_location').val();
		var summary = $('#summary').val();
		var category_reason = $('#category_reason').val();
		var spt = $('#spt').val();
		var valcl = $('#valcl').val();


		if(material == '' || receive_location == '' || reason == ''){
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
			material:material,
			issue:issue,
			receive:receive,
			quantity:quantity,
			description:description,
			category:category,
			reason:reason,
			receive_location:receive_location,
			summary:summary,
			category_reason:category_reason,
			spt:spt,
			valcl:valcl
		}
		$.post('<?php echo e(url("print/scrap")); ?>', data, function(result, status, xhr){
			if(result.status){
				fetchResume(issue);
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

	function fetchReturn(material, description, issue, spt, valcl){
		$('#material').val(material);
		$('#description').val(description);
		$('#issue').val(issue);
		$('#spt').val(spt);
		$('#valcl').val(valcl);
	}

	function reset(){
		$('#material').val("");
		$('#issue').val("");
		$('#receive').val("");
		$('#description').val("");
		// $('#category').val("").trigger('change');
		$('#reason').val("").trigger('change');;
		$('#receive_location').val("").trigger('change');
		$('#summary').val("");
		$('#category_reason').val("").trigger('change');
		$('#quantity').val(0);
		$('#spt').val("");
		$('#valcl').val("");
	}

	// function reprint(id){
	// 	var data = {
	// 		id:id
	// 	}
	// 	$.get('<?php echo e(url("reprint/scrap")); ?>', data, function(result, status, xhr){
	// 		if(result.status){
	// 			openSuccessGritter('Success!', result.message);
	// 		}
	// 		else{
	// 			openErrorGritter('Error!', result.message);
	// 		}
	// 	});
	// }

	function fetchResume(loc){
		var data = {
			loc:loc
		}
		$.get('<?php echo e(url("fetch/scrap/resume")); ?>', data, function(result, status, xhr){
			$('#tableBodyResume').html("");
			var tableData = "";
			var count = 1;
			$.each(result.resumes, function(key, value) {
				if (value.remark == "0") {
					tableData += '<tr style="background-color: #F0E68C;">';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.slip +'-SC</td>';
					tableData += '<td>'+ value.material_description +'</td>';
					// tableData += '<td>'+ value.issue_location +'</td>';
					tableData += '<td>'+ value.receive_location +'</td>';
					tableData += '<td>'+ value.category +'</td>';
					tableData += '<td>'+ value.quantity +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.tanggal +'</td>';
					tableData += '<td><center><button class="btn btn-danger" onclick="deleteScrap('+value.id+')"><i class="fa fa-trash"></i></button></center></td>';
					tableData += '</tr>';	
				}else{
					tableData += '<tr>';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.slip +'-SC</td>';
					tableData += '<td>'+ value.material_description +'</td>';
					// tableData += '<td>'+ value.issue_location +'</td>';
					tableData += '<td>'+ value.receive_location +'</td>';
					tableData += '<td>'+ value.category +'</td>';
					tableData += '<td>'+ value.quantity +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.tanggal +'</td>';
					tableData += '<td><center><button class="btn btn-danger" onclick="deleteScrap('+value.id+')"><i class="fa fa-trash"></i></button></center></td>';
					tableData += '</tr>';
				}
				
				count += 1;
			});
			$('#tableBodyResume').append(tableData);
		});
	}

	function selectType(type){

		var loc = $('#location').val();
		var tipe = type;

		var data = {
			loc:loc,
			cat:tipe
		}

		$.get('<?php echo e(url("fetch/scrap/list")); ?>', data, function(result, status, xhr){
			if(result.status){

				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				var tableData = '';
				$('#tableBodyList').html("");
				$('#tableBodyList').empty();
				
				var count = 1;
				$.each(result.lists, function(key, value) {
					var str = value.description;
					var desc = str.replace("'", "");
					tableData += '<tr onclick="fetchReturn(\''+value.material_number+'\''+','+'\''+desc+'\''+','+'\''+value.issue_location+'\''+','+'\''+value.spt+'\''+','+'\''+value.valcl+'\')">';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ desc +'</td>';
					tableData += '<td>'+ value.issue_location +'</td>';
					// tableData += '<td>'+ value.spt +'</td>';
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

				openSuccessGritter('Success!', result.message);
				$('#modalLocation').modal('hide');
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});

	}

	function deleteScrap(id){

		if(confirm("Apa anda yakin mendelete slip scrap?")){
			var data = {
				id:id
			}
			$.post('{{ url("delete/scrap") }}', data, function(result, status, xhr){
				if(result.status){
					var loc = $('#location').val();	

					fetchResume(loc);
					openSuccessGritter('Success!', result.message);
					// console.log(result);
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

	function fetchScrapList(loc){
		fetchResume(loc);
		$('#location').val(loc);
		var data = {
			loc:loc
		}
		$.get('<?php echo e(url("fetch/scrap/list")); ?>', data, function(result, status, xhr){
			if(result.status){

				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				var tableData = '';
				$('#tableBodyList').html("");
				$('#tableBodyList').empty();
				
				var count = 1;
				$.each(result.lists, function(key, value) {
					var str = value.description;
					var desc = str.replace("'", "");
					// tableData += '<tr onclick="fetchReturn(\''+value.material_number+'\''+','+'\''+desc+'\''+','+'\''+value.issue_location+'\''+','+'\''+spt+'\')">';
					// tableData += '<td>'+ count +'</td>';
					// tableData += '<td>'+ value.material_number +'</td>';
					// tableData += '<td>'+ desc +'</td>';
					// tableData += '<td>'+ value.issue_location +'</td>';
					// tableData += '<td>'+ value.spt +'</td>';
					// tableData += '</tr>';

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