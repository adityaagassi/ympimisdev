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
		<table style="text-align:center;width:100%">
			<tr>
				<td rowspan="2" style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 3%">#</td>
				<td rowspan="2" style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 3%">PRODUCT</td>
				<td colspan="2" style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">HEAD</td>
				<td colspan="2" style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">MIDDLE / BODY</td>
				<td colspan="2" style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">FOOT / STOPPER</td>
				<td colspan="2" style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%" id="tdblock">BLOCK</td>
			</tr>
			<tr>
				<td style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">CAVITY</td>
				<td style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">KANBAN</td>
				<td style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">CAVITY</td>
				<td style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">KANBAN</td>
				<td style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">CAVITY</td>
				<td style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">KANBAN</td>
				<td style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">CAVITY</td>
				<td style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">KANBAN</td>
			</tr>
			<tr>
				<td style="border: 1px solid #fff !important;background-color: #50a534;color: white;font-size: 80px">NOW</td>
				<td style="border: 1px solid #fff !important;background-color: #50a534;color: white;font-size: 80px"><span id="product_active_yrs"></span></td>
				<td style="border: 1px solid #fff !important;background-color: #9ec3ff;color: black;font-size: 80px"><span id="cavity_head">1-8</span>
				</td>
				<td style="border: 1px solid #fff !important;background-color: #9ec3ff;color: black;font-size: 80px"><span id="kanban_head">10</span>
				</td>
				<td style="border: 1px solid #fff !important;background-color: #fdff9e;color: black;font-size: 80px"><span id="cavity_middle">1-8</span>
				</td>
				<td style="border: 1px solid #fff !important;background-color: #fdff9e;color: black;font-size: 80px"><span id="kanban_middle">2</span>
				</td>
				<td style="border: 1px solid #fff !important;background-color: #ffa59e;color: black;font-size: 80px"><span id="cavity_foot">1-8</span>
				</td>
				<td style="border: 1px solid #fff !important;background-color: #ffa59e;color: black;font-size: 80px"><span id="kanban_foot">2</span>
				</td>
				<td id="tdblock2" style="border: 1px solid #fff !important;background-color: #9efcff;color: black;font-size: 80px"><span id="cavity_block">1-8</span>
				</td>
				<td id="tdblock3" style="border: 1px solid #fff !important;background-color: #9efcff;color: black;font-size: 80px"><span id="kanban_block">2</span>
				</td>
			</tr>
		</table>
		<table style="text-align:center;width:100%;margin-top: 50px" id="table_all">
			<thead>
				<tr>
					<td rowspan="2" style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 3%">EMP</td>
					<td rowspan="2" style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 3%">PRODUCT</td>
					<td colspan="2" style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">HEAD</td>
					<td colspan="2" style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">MIDDLE / BODY</td>
					<td colspan="2" style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">FOOT / STOPPER</td>
					<td colspan="2" style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%" id="tdblock">BLOCK</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">CAVITY</td>
					<td style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">KANBAN</td>
					<td style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">CAVITY</td>
					<td style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">KANBAN</td>
					<td style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">CAVITY</td>
					<td style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">KANBAN</td>
					<td style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">CAVITY</td>
					<td style="border: 1px solid #fff !important;background-color: black;color: white;font-size: 25px;width: 1%">KANBAN</td>
				</tr>
			</thead>
			<tbody id="body_all">
				
			</tbody>
		</table>
		

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
							$('#tdblock').show();
							$('#tdblock2').show();
							$('#tdblock2').show();
							$('#product_active_yrs').html(value.product);
							if (value.part_type == 'HJ') {
								$('#cavity_head').html(value.cavity);
								$('#kanban_head').html(value.no_kanban_injection);
							}
							if (value.part_type.match(/MJ/gi)) {
								$('#cavity_middle').html(value.cavity);
								$('#kanban_middle').html(value.no_kanban_injection);
							}
							if (value.part_type == 'FJ') {
								$('#cavity_foot').html(value.cavity);
								$('#kanban_foot').html(value.no_kanban_injection);
							}
							if (value.part_type == 'BJ') {
								$('#cavity_block').html(value.cavity);
								$('#kanban_block').html(value.no_kanban_injection);
							}
						}else{
							$('#gambaryrs').hide();
							$('#gambaryrf').show();
							$('#tdblock').hide();
							$('#tdblock2').hide();
							$('#tdblock2').hide();
							if (value.part_type == 'A YRF H') {
								$('#cavity_head').html(value.cavity);
								$('#kanban_head').html(value.no_kanban_injection);
							}
							if (value.part_type == 'A YRF B') {
								$('#cavity_middle').html(value.cavity);
								$('#kanban_middle').html(value.no_kanban_injection);
							}
							if (value.part_type == 'A YRF S') {
								$('#cavity_foot').html(value.cavity);
								$('#kanban_foot').html(value.no_kanban_injection);
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

					$('#body_all').html('');
					var body_all = "";
					$.each(result.kensa, function(key2,value2){
						if (value2.product.match(/YRS/gi)) {
							var no_kanban = value2.no_kanban.split(',');
							var cavity = value2.cavity.split(',');
							body_all += '<tr>';
								body_all += '<td style="border: 1px solid #fff !important;background-color: #c800c6;color: white;font-size: 80px">'+value2.name+'</td>';
								body_all += '<td style="border: 1px solid #fff !important;background-color: #c800c6;color: white;font-size: 80px"><span>'+value2.product+'</span></td>';
								body_all += '<td style="border: 1px solid #fff !important;background-color: #9ec3ff;color: black;font-size: 80px"><span>'+cavity[0]+'</span>';
								body_all += '</td>';
								body_all += '<td style="border: 1px solid #fff !important;background-color: #9ec3ff;color: black;font-size: 80px"><span>'+no_kanban[0]+'</span>';
								body_all += '</td>';
								body_all += '<td style="border: 1px solid #fff !important;background-color: #fdff9e;color: black;font-size: 80px"><span>'+cavity[1]+'</span>';
								body_all += '</td>';
								body_all += '<td style="border: 1px solid #fff !important;background-color: #fdff9e;color: black;font-size: 80px"><span>'+no_kanban[1]+'</span>';
								body_all += '</td>';
								body_all += '<td style="border: 1px solid #fff !important;background-color: #ffa59e;color: black;font-size: 80px"><span>'+cavity[2]+'</span>';
								body_all += '</td>';
								body_all += '<td style="border: 1px solid #fff !important;background-color: #ffa59e;color: black;font-size: 80px"><span>'+no_kanban[2]+'</span>';
								body_all += '</td>';
								body_all += '<td style="border: 1px solid #fff !important;background-color: #9efcff;color: black;font-size: 80px"><span>'+cavity[3]+'</span>';
								body_all += '</td>';
								body_all += '<td style="border: 1px solid #fff !important;background-color: #9efcff;color: black;font-size: 80px"><span>'+no_kanban[3]+'</span>';
								body_all += '</td>';
							body_all += '</tr>';
						}else{

						}
					});

					$('#body_all').append(body_all);
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