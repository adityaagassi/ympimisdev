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
	    width: 180px;
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
		<div class="col-xs-12" style="margin-left: 0px;margin-right: 0px;padding-bottom: 10px;padding-left: 0px">
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
		</div>
		@foreach($location as $location)
		<?php $locs = explode("_", $location) ?>
		<div class="gambar" style="margin-top:0px" id="container_{{$locs[0]}}">
			<table style="text-align:center;width:100%">
				<?php
				if ($locs[0] == 'wi1') {
		  			$loc = 'WI 1';
		  		}else if ($locs[0] == 'wi2') {
		  			$loc = 'WI 2';
		  		}else if($locs[0] == 'ei'){
		  			$loc = 'EI';
		  		}else if($locs[0] == 'sx'){
		  			$loc = 'Sax Body';
		  		}else if ($locs[0] == 'cs'){
		  			$loc = 'Case';
		  		}else if($locs[0] == 'ps'){
		  			$loc = 'Pipe Silver';
		  		} ?>
				<tr>
					<td colspan="2" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 20px">{{$loc}}
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 15px;width: 50%;">LOT OK
					</td>
					<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 15px;width: 50%;">LOT OUT
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid #fff;color: white;font-size: 80px;" id="lot_ok_td_{{$locs[0]}}"><span id="lot_ok_{{$locs[0]}}">0</span>
					</td>
					<td style="border: 1px solid #fff;font-size: 80px;" id="lot_out_td_{{$locs[0]}}"><span id="lot_out_{{$locs[0]}}">0</span>
					</td>
				</tr>
			</table>
		</div>
		@endforeach
		<div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
			<div class="box-body">
				<div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
					<table id="table_lot" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
						<thead style="background-color: rgb(126,86,134);">
							<tr>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Check Date</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Loc</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Invoice</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 5%">Check PIC</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Lot Number</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 7%">Material</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Vendor</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">Check Qty</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">NG Qty</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 1%">NG (%)</th>
								<th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 4%">Defect</th>
							</tr>
						</thead>
						<tbody id="body_table_lot" style="text-align:center;">
							
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
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
		fetchLotStatus();
		setInterval(fetchLotStatus, 5000);
	});

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function fetchLotStatus() {
		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
		}
		$.get('{{ url("fetch/qa/display/incoming/lot_status") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					$.each(result.lot_count, function(key,value){
						$('#lot_ok_'+value.location).html(value.lot_ok);
						$('#lot_out_'+value.location).html(value.lot_out);

						if (parseInt(value.lot_ok) > 0) {
							$('#lot_ok_td_'+value.location).css("background-color","rgb(0, 166, 90)",'important');
							$('#lot_ok_td_'+value.location).css("color","white",'important');
						}else{
							$('#lot_ok_td_'+value.location).css("background-color","white",'important');
							$('#lot_ok_td_'+value.location).css("color","black",'important');
						}

						if (parseInt(value.lot_out) > 0) {
							$('#lot_out_td_'+value.location).css("background-color","#dd4b39",'important');
							$('#lot_out_td_'+value.location).css("color","white",'important');
						}else{
							$('#lot_out_td_'+value.location).css("background-color","white",'important');
							$('#lot_out_td_'+value.location).css("color","black",'important');
						}
					});

					$('#body_table_lot').html("");
					var body_lot = "";

					$.each(result.lot_detail, function(key2,value2){
						if (value2.location == 'wi1') {
				  			var loc = 'WI 1';
				  		}else if (value2.location == 'wi2') {
				  			var loc = 'WI 2';
				  		}else if(value2.location == 'ei'){
				  			var loc = 'EI';
				  		}else if(value2.location == 'sx'){
				  			var loc = 'Sax Body';
				  		}else if (value2.location == 'cs'){
				  			var loc = 'Case';
				  		}else if(value2.location == 'ps'){
				  			var loc = 'Pipe Silver';
				  		}
						body_lot += '<tr>';
						body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.date_lot+'</td>';
						body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+loc+'</td>';
						body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.invoice+'</td>';
						body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.employee_id+'<br>'+value2.name.replace(/(.{14})..+/, "$1&hellip;")+'</td>';
						body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.lot_number+'</td>';
						body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.material_number+'<br>'+value2.material_description.replace(/(.{25})..+/, "$1&hellip;")+'</td>';
						body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.vendor_shortname+'</td>';
						body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.qty_check+' Pc(s)</td>';
						body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.total_ng+' Pc(s)</td>';
						body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.ng_ratio.toFixed(2)+' %</td>';
						if (value2.ng_name != null) {
							body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.ng_name.replace(/(.{25})..+/, "$1&hellip;")+'</td>';
						}else{
							body_lot += '<td style="font-size: 1vw; padding-top: 2px; padding-bottom: 2px;"></td>';
						}
						body_lot += '</tr>';
					});

					$('#body_table_lot').append(body_lot);

					$('#periode').html('Periode On '+result.monthTitle);
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