@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

	input {
		line-height: 22px;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
		color: black;
	}
	tfoot>tr>th{
		text-align:center;
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

	.content-wrapper{
		color: white;
		font-weight: bold;
		background-color: #313132 !important;
	}
	#loading, #error { display: none; }

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}

	.gambar {
	    width: 700px;
	    background-color: none;
	    border-radius: 5px;
	    margin-left: 15px;
	    margin-top: 15px;
	    display: inline-block;
	    border: 2px solid white;
	  }
	  .gambar2 {
	    width: 400px;
	    background-color: none;
	    border-radius: 5px;
	    margin-left: 15px;
	    margin-top: 15px;
	    display: inline-block;
	    border: 2px solid white;
	  }
	

</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row" style="text-align: center;margin-left: 5px;margin-right: 5px">
		<!-- <div class="col-xs-12" style="margin-left: 0px;margin-right: 0px;padding-bottom: 10px;padding-left: 0px">
			<div class="col-xs-4" style="background-color: rgb(126,86,134);padding-left: 5px;padding-right: 5px;height:30px;vertical-align: middle;">
				<span style="font-size: 20px;color: white;width: 100%;" id="periode"></span>
			</div>
			<div class="col-xs-2" style="padding-left: 10px;">
				<div class="input-group date">
					<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From" style="height:30px;">
				</div>
			</div>
			<div class="col-xs-2" style="padding-left: 0;">
				<div class="input-group date">
					<div class="input-group-addon" style="border: none; background-color: rgb(126,86,134); color: white;">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To" style="height:30px;">
				</div>
			</div>
			<div class="col-xs-1" style="padding-left: 0;">
				<button class="btn btn-default pull-left" onclick="fetchLotStatus()" style="font-weight: bold;height:30px;background-color: rgb(126,86,134);color: white">
					Search
				</button>
			</div>
			<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;"></div>
		</div> -->
		<div class="gambar" style="margin-top:0px" id="gambaryrs">
			<table style="text-align:center;width:100%">
				<tr>
					<td colspan="4" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 20px" id="product_active_yrs">YRS-24B //ID
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: #bfffd0;color: black;font-size: 20px;width: 25%;">HEAD
					</td>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: #ffe3bf;color: black;font-size: 20px;width: 25%;">MIDDLE
					</td>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: #bff5ff;color: black;font-size: 20px;width: 25%;">FOOT
					</td>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: #fabfff;color: black;font-size: 20px;width: 25%;">BLOCK
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="material_head">W958620</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="material_middle">W958670</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="material_foot">W958640</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="material_block">W958610</span>
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="part_head">W958620</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="part_middle">W958670</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="part_foot">W958640</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="part_block">W958610</span>
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="tag_head">1941691383</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="tag_middle">1941691383</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="tag_foot">1941691383</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="tag_block">1941691383</span>
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="color_head">IVORY</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="color_middle">IVORY</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="color_foot">IVORY</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="color_block">BEIGE</span>
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="cavity_head">1-8</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="cavity_middle">1-8</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="cavity_foot">1-8</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;"><span id="cavity_block">1-8</span>
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;">Kanban No. <span style="font-size: 20px" id="kanban_head">10</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;">Kanban No. <span style="font-size: 20px" id="kanban_middle">2</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;">Kanban No. <span style="font-size: 20px" id="kanban_foot">2</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;">Kanban No. <span style="font-size: 20px" id="kanban_block">2</span>
					</td>
				</tr>
			</table>
		</div>

		<div class="gambar" style="margin-top:0px" id="gambaryrf">
			<table style="text-align:center;width:100%">
				<tr>
					<td colspan="4" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 20px" id="product_active_yrf">YRF-21//ID
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: #bfffd0;color: black;font-size: 20px;width: 25%;">HEAD
					</td>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: #ffe3bf;color: black;font-size: 20px;width: 25%;">BODY
					</td>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: #bff5ff;color: black;font-size: 20px;width: 25%;">STOPPER
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;" id="material_head_yrf"><span>W958670</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;" id="material_body_yrf"><span>W958640</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;" id="material_stopper_yrf"><span>W958610</span>
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;" id="part_head_yrf"><span>W958670</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;" id="part_body_yrf"><span>W958640</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;" id="part_stopper_yrf"><span>W958610</span>
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;" id="tag_head_yrf"><span>1941691383</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;" id="tag_body_yrf"><span>1941691383</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;" id="tag_stopper_yrf"><span>1941691383</span>
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;" id="color_head_yrf"><span>IVORY</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;" id="color_body_yrf"><span>IVORY</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;" id="color_stopper_yrf"><span>BEIGE</span>
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;" id="cavity_head_yrf"><span>1-8</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;" id="cavity_body_yrf"><span>1-8</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;" id="cavity_stopper_yrf"><span>1-8</span>
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;">Kanban No. <span style="font-size: 20px" id="kanban_head_yrf">10</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;">Kanban No. <span style="font-size: 20px" id="kanban_middle_yrf">2</span>
					</td>
					<td style="border: 1px solid #fff;color: white;font-size: 20px;">Kanban No. <span style="font-size: 20px" id="kanban_foot_yrf">2</span>
					</td>
				</tr>
			</table>
		</div>
		<div class="col-xs-12" id="div_all">
			
		</div>

		<!-- <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
			<div class="box-body">
				<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
					<table id="table_all" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
						<thead style="background-color: rgb(126,86,134);">
							<tr>
								<th rowspan="2" style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">ID</th>
								<th rowspan="2" style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Name</th>
								<th rowspan="2" style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Product</th>
								<th colspan="5" style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Head</th>
								<th colspan="5" style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Middle / Body</th>
								<th colspan="5" style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Foot / Stopper</th>
								<th colspan="5" style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Block</th>
							</tr>
							<tr>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Material</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Tag</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Color</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Cavity</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">No. Kanban</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Material</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Tag</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Color</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Cavity</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">No. Kanban</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Material</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Tag</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Color</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Cavity</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">No. Kanban</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Material</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Tag</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Color</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Cavity</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">No. Kanban</th>
							</tr>
						</thead>
						<tbody id="body_table_all" style="text-align:center;">
							
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div> -->
</section>
@endsection
@section('scripts')

<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('.select2').select2();
		fetchKensa();
		setInterval(fetchKensa, 5000);
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function fetchKensa() {
		// $('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

		$.get('{{ url("fetch/recorder/display/kensa") }}',function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$.each(result.initial, function(key,value){
						if (value.product.match(/YRS/gi)) {
							$('#gambaryrs').show();
							$('#gambaryrf').hide();
							$('#product_active_yrs').html(value.product);
							if (value.part_type == 'HJ') {
								$('#material_head').html(value.material_number);
								$('#part_head').html(value.mat_desc.split(' ').slice(0,2).join(' '));
								$('#tag_head').html(value.tag);
								$('#color_head').html(value.color);
								$('#cavity_head').html(value.cavity);
								$('#kanban_head').html(value.no_kanban_injection);
							}
							if (value.part_type.match(/MJ/gi)) {
								$('#material_middle').html(value.material_number);
								$('#part_middle').html(value.mat_desc.split(' ').slice(0,2).join(' '));
								$('#tag_middle').html(value.tag);
								$('#color_middle').html(value.color);
								$('#cavity_middle').html(value.cavity);
								$('#kanban_middle').html(value.no_kanban_injection);
							}
							if (value.part_type == 'FJ') {
								$('#material_foot').html(value.material_number);
								$('#part_foot').html(value.mat_desc.split(' ').slice(0,2).join(' '));
								$('#tag_foot').html(value.tag);
								$('#color_foot').html(value.color);
								$('#cavity_foot').html(value.cavity);
								$('#kanban_foot').html(value.no_kanban_injection);
							}
							if (value.part_type == 'BJ') {
								$('#material_block').html(value.material_number);
								$('#part_block').html(value.mat_desc.split(' ').slice(0,2).join(' '));
								$('#tag_block').html(value.tag);
								$('#color_block').html(value.color);
								$('#cavity_block').html(value.cavity);
								$('#kanban_block').html(value.no_kanban_injection);
							}
						}else{
							$('#gambaryrs').hide();
							$('#gambaryrf').show();
							if (value.part_type == 'A YRF H') {
								$('#material_head_yrf').html(value.material_number);
								$('#part_head_yrf').html(value.mat_desc.split(' ').slice(0,2).join(' '));
								$('#tag_head_yrf').html(value.tag);
								$('#color_head_yrf').html(value.color);
								$('#cavity_head_yrf').html(value.cavity);
								$('#kanban_head_yrf').html(value.no_kanban_injection);
							}
							if (value.part_type == 'A YRF B') {
								$('#material_body_yrf').html(value.material_number);
								$('#part_body_yrf').html(value.mat_desc.split(' ').slice(0,2).join(' '));
								$('#tag_body_yrf').html(value.tag);
								$('#color_body_yrf').html(value.color);
								$('#cavity_body_yrf').html(value.cavity);
								$('#kanban_body_yrf').html(value.no_kanban_injection);
							}
							if (value.part_type == 'A YRF S') {
								$('#material_stopper_yrf').html(value.material_number);
								$('#part_stopper_yrf').html(value.mat_desc.split(' ').slice(0,2).join(' '));
								$('#tag_stopper_yrf').html(value.tag);
								$('#color_stopper_yrf').html(value.color);
								$('#cavity_stopper_yrf').html(value.cavity);
								$('#kanban_stopper_yrf').html(value.no_kanban_injection);
							}
						}
					});

					// $('#body_table_all').html("");
					// var body_all = "";

					// $.each(result.kensa, function(key2,value2){
					// 	body_all += '<tr>';
					// 	body_all += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.employee_id+'</td>';
					// 	body_all += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.name+'</td>';
					// 	body_all += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.product+'</td>';
					// 	var material_number = value2.material_number.split(',');
					// 	var tag = value2.tag.split(',');
					// 	var color = value2.color.split(',');
					// 	var no_kanban = value2.no_kanban.split(',');
					// 	var cavity = value2.cavity.split(',');
					// 	var mat_desc = value2.mat_desc.split(',');
					// 	console.log(material_number.length);
					// 	for (var i = 0;i < material_number.length;i++) {
					// 		body_all += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+material_number[i]+'</td>';
					// 		body_all += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+tag[i]+'</td>';
					// 		body_all += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+color[i]+'</td>';
					// 		body_all += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+cavity[i]+'</td>';
					// 		body_all += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+no_kanban[i]+'</td>';
					// 	}
					// 	body_all += '</tr>';
					// });

					// $('#body_table_all').append(body_all);

					$('#div_all').html('');
					var div_all = "";
					$.each(result.kensa, function(key2,value2){
						if (value2.product.match(/YRS/gi)) {
							div_all += '<div class="gambar2" style="margin-top:0px">';
								div_all += '<table style="text-align:center;width:100%">';
									div_all += '<tr>';
										div_all += '<td colspan="4" style="border: 1px solid #fff !important;background-color: #d0c4ff;color: black;font-size: 13px">'+value2.name.split(' ').slice(0,2).join(' ');
										div_all += '</td>';
									div_all += '</tr>';
									div_all += '<tr>';
										div_all += '<td colspan="4" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 13px">'+value2.serial_number+' - '+value2.product;
										div_all += '</td>';
									div_all += '</tr>';
									div_all += '<tr>';
										div_all += '<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: #bfffd0;color: black;font-size: 13px;width: 25%;">HEAD';
										div_all += '</td>';
										div_all += '<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: #ffe3bf;color: black;font-size: 13px;width: 25%;">MIDDLE';
										div_all += '</td>';
										div_all += '<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: #bff5ff;color: black;font-size: 13px;width: 25%;">FOOT';
										div_all += '</td>';
										div_all += '<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: #fabfff;color: black;font-size: 13px;width: 25%;">BLOCK</td>';
									div_all += '</tr>';
									var material_number = value2.material_number.split(',');
									var tag = value2.tag.split(',');
									var color = value2.color.split(',');
									var no_kanban = value2.no_kanban.split(',');
									var cavity = value2.cavity.split(',');
									var mat_desc = value2.mat_desc.split(',');
									div_all += '<tr>';
										for(var i = 0;i<material_number.length;i++){
											div_all += '<td style="border: 1px solid #fff;color: white;font-size: 13px;"><span>'+material_number[i]+'</span>';
										}
									div_all += '</tr>';
									div_all += '<tr>';
										for(var i = 0;i<mat_desc.length;i++){
											div_all += '<td style="border: 1px solid #fff;color: white;font-size: 13px;"><span>'+mat_desc[i].split(' ').slice(0,2).join(' ')+'</span>';
										}
									div_all += '</tr>';
									div_all += '<tr>';
										for(var i = 0;i<tag.length;i++){
											div_all += '<td style="border: 1px solid #fff;color: white;font-size: 13px;"><span>'+tag[i]+'</span>';
										}
									div_all += '</tr>';
									div_all += '<tr>';
										for(var i = 0;i<color.length;i++){
											div_all += '<td style="border: 1px solid #fff;color: white;font-size: 13px;"><span>'+color[i]+'</span>';
										}
									div_all += '</tr>';
									div_all += '<tr>';
										for(var i = 0;i<cavity.length;i++){
											div_all += '<td style="border: 1px solid #fff;color: white;font-size: 13px;"><span>'+cavity[i]+'</span>';
										}
									div_all += '</tr>';
									div_all += '<tr>';
										for(var i = 0;i<no_kanban.length;i++){
											div_all += '<td style="border: 1px solid #fff;color: white;font-size: 13px;">Kanban No. <span style="font-size: 13px">'+no_kanban[i]+'</span>';
											div_all += '</td>';
										}
									div_all += '</tr>';
								div_all += '</table>';
							div_all += '</div>';
						}else{
							div_all += '<div class="gambar2" style="margin-top:0px">';
								div_all += '<table style="text-align:center;width:100%">';
									div_all += '<tr>';
										div_all += '<td colspan="3" style="border: 1px solid #fff !important;background-color: #d0c4ff;color: black;font-size: 13px">'+value2.name.split(' ').slice(0,2).join(' ');
										div_all += '</td>';
									div_all += '</tr>';
									div_all += '<tr>';
										div_all += '<td colspan="3" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 13px">'+value2.serial_number+' - '+value2.product;
										div_all += '</td>';
									div_all += '</tr>';
									div_all += '<tr>';
										div_all += '<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: #bfffd0;color: black;font-size: 13px;width: 25%;">HEAD';
										div_all += '</td>';
										div_all += '<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: #ffe3bf;color: black;font-size: 13px;width: 25%;">BODY';
										div_all += '</td>';
										div_all += '<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: #bff5ff;color: black;font-size: 13px;width: 25%;">STOPPER';
										div_all += '</td>';
									div_all += '</tr>';
									var material_number = value2.material_number.split(',');
									var tag = value2.tag.split(',');
									var color = value2.color.split(',');
									var no_kanban = value2.no_kanban.split(',');
									var cavity = value2.cavity.split(',');
									var mat_desc = value2.mat_desc.split(',');
									div_all += '<tr>';
										for(var i = 0;i<material_number.length;i++){
											div_all += '<td style="border: 1px solid #fff;color: white;font-size: 13px;"><span>'+material_number[i]+'</span>';
										}
									div_all += '</tr>';
									div_all += '<tr>';
										for(var i = 0;i<mat_desc.length;i++){
											div_all += '<td style="border: 1px solid #fff;color: white;font-size: 13px;"><span>'+mat_desc[i].split(' ').slice(0,2).join(' ')+'</span>';
										}
									div_all += '</tr>';
									div_all += '<tr>';
										for(var i = 0;i<tag.length;i++){
											div_all += '<td style="border: 1px solid #fff;color: white;font-size: 13px;"><span>'+tag[i]+'</span>';
										}
									div_all += '</tr>';
									div_all += '<tr>';
										for(var i = 0;i<color.length;i++){
											div_all += '<td style="border: 1px solid #fff;color: white;font-size: 13px;"><span>'+color[i]+'</span>';
										}
									div_all += '</tr>';
									div_all += '<tr>';
										for(var i = 0;i<cavity.length;i++){
											div_all += '<td style="border: 1px solid #fff;color: white;font-size: 13px;"><span>'+cavity[i]+'</span>';
										}
									div_all += '</tr>';
									div_all += '<tr>';
										for(var i = 0;i<no_kanban.length;i++){
											div_all += '<td style="border: 1px solid #fff;color: white;font-size: 13px;">Kanban No. <span style="font-size: 13px">'+no_kanban[i]+'</span>';
											div_all += '</td>';
										}
									div_all += '</tr>';
								div_all += '</table>';
							div_all += '</div>';
						}
					});

					$('#div_all').append(div_all);
				}
			}
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

	function getActualDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year;
	}


</script>
@endsection