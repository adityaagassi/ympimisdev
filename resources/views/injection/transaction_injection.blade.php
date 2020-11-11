@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
thead>tr>th{
	text-align:center;
}
tbody>tr>td{
	text-align:center;
}
tfoot>tr>th{
	text-align:center;
}
td:hover {
	overflow: visible;
}
table.table-bordered{
	border:1px solid black;
}
table.table-bordered > thead > tr > th{
	border:1px solid black;
}
table.table-bordered > tbody > tr > td{
	border:1px solid rgb(211,211,211);
	padding-top: 0px;
	padding-bottom: 0px;
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header" >
	<h1>
		{{ $page }} - {{ $status }}<span class="text-purple"> {{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<section class="content" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-6" style="text-align: center;">
			<?php if ($status == 'OUT'): ?>
				<div class="row">
					<div class="col-xs-9">
						<input type="text" style="text-align: center; border-color: red; font-size: 1.5vw; height: 50px" class="form-control" id="operator_name" name="operator_name" placeholder="" readonly>
					</div>
					<div class="col-xs-3">
						<button class="btn btn-danger" style="width: 100%;height: 50px" onclick="cancelTag()">CANCEL</button>
					</div>
				</div>
				<div class="input-group col-md-12" style="padding-top: 20px;">
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 2vw; border-color: red;">
						<i class="glyphicon glyphicon-barcode"></i>
					</div>
					<input type="text" style="text-align: center; border-color: red; font-size: 2vw; height: 50px" class="form-control" id="tag_product" name="tag_product" placeholder="Scan Tag Here ..." required>
					<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 2vw; border-color: red;">
						<i class="glyphicon glyphicon-barcode"></i>
					</div>
				</div>
			<?php endif ?>
			<div class="row">
				<div class="col-md-12" style="">
					<span style="font-size: 24px;">Transaction List:</span> 
					<table id="resultScan" class="table table-bordered table-striped table-hover" style="width: 100%;">
						<input type="hidden" id="operator_id">
			            <thead style="background-color: rgba(126,86,134,.7);">
			                <tr>
			                  <th style="width: 5%;">Material Number</th>
			                  <th style="width: 5%;">Part Name</th>
			                  <th style="width: 5%;">Part Type</th>
			                  <th style="width: 5%;">Color</th>
			                  <th style="width: 6%;">Cavity</th>
			                  <th style="width: 6%;">Qty</th>
			                  <th style="width: 6%;">Status</th>
			                </tr>
			            </thead >
			            <tbody id="resultScanBody">
						</tbody>
		            </table>
				</div>
			</div>
			<?php if ($status == "OUT"): ?>
				<div class="row">
					<div class="col-md-12">
						<span style="font-size: 24px;">NG List:</span> 
						<table id="resultNG" class="table table-bordered table-striped table-hover" style="width: 100%;">
				            <thead style="background-color: rgba(126,86,134,.7);">
				                <tr>
				                  <th style="width: 5%;">NG</th>
				                  <th style="width: 17%;">Quantity</th>
				                </tr>
				            </thead >
				            <tbody id="resultNGBody">
							</tbody>
			            </table>
					</div>
				</div>
			<?php endif ?>
		</div>

		<div class="col-xs-6" style="padding-left: 0px">
			<div class="col-md-12">
				<div class="box box-solid">
					<div class="box-body">
						<span style="font-size: 20px;text-align: left;">Transaction History</span> 
						<table id="tableHistory" class="table table-bordered table-striped table-hover" style="width: 100%">
							<thead style="background-color: rgba(126,86,134,.7);">
								<tr>
									<th>Part Name</th>
									<th>Part Code - Color</th>
									<th>Qty</th>
									<th>Loc</th>
									<th>Created At</th>
								</tr>
							</thead>
							<tbody id="tableHistoryBody">
							</tbody>
							<tfoot style="background-color: RGB(252, 248, 227);">
								<tr>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalOperator">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<div class="modal-body table-responsive no-padding">
						<div class="form-group">
							<label for="exampleInputEmail1">Employee ID</label>
							<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Scan ID Card" required>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalCompletion">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12">
						<center><h3 style="font-weight: bold;background-color: #17b80b;color: white;padding-top: 5px;padding-bottom: 5px">CEK DATA TRANSAKSI</h3></center>
					</div>
					<div class="modal-body" id="tableCompletion">

					</div>
					<div class="col-xs-12">
						<button class="btn btn-success btn-block" style="font-weight: bold;font-size: 25px" onclick="completion()">
							PROSES TRANSAKSI
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
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

	var counter = 0;
	var arrPart = [];

	jQuery(document).ready(function() {
		var status = '{{$status}}';
		if (status == 'OUT') {
			$('#modalOperator').modal({
				backdrop: 'static',
				keyboard: false
			});
		}
		fillResult();

		if ('{{$status}}' == 'IN') {
			setInterval(checkInjections,10000);
		}
		// checkInjections();

      $('body').toggleClass("sidebar-collapse");
		$("#tag_product").val("");
		$('#tag_product').focus();
		$("#operator").val("");
		$('#operator').focus();
		$("#operator_id").val("");
		$("#operator_name").val("-");
	});

	$('#modalOperator').on('shown.bs.modal', function () {
		$('#operator').focus();
	});

	$('#operator').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#operator").val().length >= 8){
				var data = {
					employee_id : $("#operator").val()
				}
				
				$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#modalOperator').modal('hide');
						$('#operator_name').val(result.employee.employee_id+' - '+result.employee.name);
						$('#operator_id').val(result.employee.employee_id);
						$('#tag_product').focus();
					}
					else{
						audio_error.play();
						openErrorGritter('Error', result.message);
						$('#operator').val('');
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Employee ID Invalid.');
				audio_error.play();
				$("#operator").val("");
			}			
		}
	});

	$('#tag_product').keyup(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_product").val().length >= 7){
				var stts = '{{$status}}';
				var data = {
					tag : $("#tag_product").val(),
					status : stts,
				}

				var bodyScan = "";
				$('#resultScanBody').html("");
				var ngScan = "";
				$('#resultNGBody').html("");
				var statustransaction = '{{$status}}';

				var jumlah = 0;

				$.get('{{ url("scan/tag_product") }}', data, function(result, status, xhr){
					if(result.status){
						openSuccessGritter('Success!', 'Scan Tag Success');
						$('#tag_product').prop('disabled',true);
						$.each(result.data, function(key, value) {
							bodyScan += '<tr>';
							bodyScan += '<td id="material_number">'+value.material_number+'</td>';
							bodyScan += '<td id="part_name">'+value.part_name+'</td>';
							bodyScan += '<td id="part_type">'+value.part_type+'</td>';
							bodyScan += '<td id="color">'+value.color+'</td>';
							bodyScan += '<td id="cavity">'+value.cavity+'</td>';
							bodyScan += '<td id="qty">'+value.shot+'</td>';
							bodyScan += '<td id="status">'+statustransaction+'</td>';
							bodyScan += '</tr>';
							bodyScan += '<tr>';
							bodyScan += '<td colspan="7" style="padding:10px"><button class="btn btn-danger pull-left" onclick="cancel()">CANCEL</button><button class="btn btn-success pull-right" onclick="completion()">SUBMIT</button></td>';
							bodyScan += '</tr>';

							if (stts == 'OUT') {
								if (value.ng_name != null) {
									ng_arr = value.ng_name.split(',');
									qty_arr = value.ng_count.split(',');

									for(var i = 0; i < ng_arr.length; i++){
										ngScan += '<tr>';
										ngScan += '<td id="ng_name">'+ng_arr[i]+'</td>';
										ngScan += '<td id="ng_qty">'+qty_arr[i]+'</td>';
										ngScan += '</tr>';
										jumlah = jumlah + parseInt(qty_arr[i]);
									}

									ngScan += '<tr style="background-color:rgba(126,86,134,.7);">';
									ngScan += '<td style="border:1px solid black;border-top:1px solid black" id="total_ng_name"><b>TOTAL</b></td>';
									ngScan += '<td style="border:1px solid black;border-top:1px solid black" id="total_ng_qty"><b>'+jumlah+'</b></td>';
									ngScan += '</tr>';
								}
							}

							if (statustransaction == 'IN') {
								$('#operator_id').val(value.operator_id);
							}
						})

						$('#resultScanBody').append(bodyScan);
						if (stts == 'OUT') {
							$('#resultNGBody').append(ngScan);
						}
					}
					else{
						openErrorGritter('Error!', 'Tag Invalid');
						audio_error.play();
						$("#tag_product").val("");
						$("#tag_product").focus();
					}
				});
			}
			else{
				openErrorGritter('Error!', 'Tag Invalid');
				audio_error.play();
				$("#tag_product").val("");
				$("#tag_product").focus();
			}			
		}
	});

	function checkInjections() {
		var data = {
			status : '{{$status}}'
		}

		var stts = '{{$status}}';

		$.get('{{ url("fetch/injection/check_injections") }}', data, function(result, status, xhr){
			if(result.status){
				// openSuccessGritter('Success!', 'Transaction Success');
				var bodyScan = "";
				$('#resultScanBody').html("");
				var ngScan = "";
				$('#resultNGBody').html("");
				var statustransaction = '{{$status}}';

				var jumlah = 0;
				fillResult();
				$.each(result.data, function(key, value) {
					bodyScan += '<tr style="cursor:pointer;font-size:20px" onclick="showModalCompletion(\''+value.injection_id+'\',\''+value.tag_rfid+'\',\''+value.material_number+'\',\''+value.part_name+'\',\''+value.part_type+'\',\''+value.color+'\',\''+value.cavity+'\',\''+value.shot+'\',\''+statustransaction+'\')">';
					bodyScan += '<td>'+value.material_number+'</td>';
					bodyScan += '<td>'+value.part_name+'</td>';
					bodyScan += '<td>'+value.part_type+'</td>';
					bodyScan += '<td>'+value.color+'</td>';
					bodyScan += '<td>'+value.cavity+'</td>';
					bodyScan += '<td>'+value.shot+'</td>';
					bodyScan += '<td>'+statustransaction+'</td>';
					bodyScan += '</tr>';
					bodyScan += '<tr>';
					// bodyScan += '<td colspan="7" style="padding:10px"><button class="btn btn-danger pull-left" onclick="cancel()">CANCEL</button><button class="btn btn-success pull-right" onclick="completion()">SUBMIT</button></td>';
					bodyScan += '</tr>';

					if (stts == 'OUT') {
						ng_arr = value.ng_name.split(',');
						qty_arr = value.ng_count.split(',');

						for(var i = 0; i < ng_arr.length; i++){
							ngScan += '<tr>';
							ngScan += '<td id="ng_name">'+ng_arr[i]+'</td>';
							ngScan += '<td id="ng_qty">'+qty_arr[i]+'</td>';
							ngScan += '</tr>';
							jumlah = jumlah + parseInt(qty_arr[i]);
						}

						ngScan += '<tr style="background-color:rgba(126,86,134,.7);">';
						ngScan += '<td style="border:1px solid black;border-top:1px solid black" id="total_ng_name"><b>TOTAL</b></td>';
						ngScan += '<td style="border:1px solid black;border-top:1px solid black" id="total_ng_qty"><b>'+jumlah+'</b></td>';
						ngScan += '</tr>';
					}

					if (statustransaction == 'IN') {
						$('#operator_id').val(value.operator_id);
					}
				})

				$('#resultScanBody').append(bodyScan);
				if (stts == 'OUT') {
					$('#resultNGBody').append(ngScan);
				}
				// $('#tag_product').removeAttr("disabled");
				// $("#tag_product").val("");
				// $("#tag_product").focus();
				// $('#operator_id').val("");
			}
			else{
				openErrorGritter('Error!', 'Upload Failed.');
				audio_error.play();
			}
		});
	}

	function showModalCompletion(id,tag_rfid,material_number,part_name,part_type,color,cavity,shot,status) {
		$('#tableCompletion').empty();
		var table = "";
		table += '<table class="table table-bordered table-responsive">';
		table += '<tr>';
		table += '<td style="background-color:#17b80b;color:white;font-weight:bold">Tag</td>';
		table += '<td id="tag_product_rfid">'+tag_rfid+'</td>';
		table += '</tr>';
		table += '<tr>';
		table += '<td style="background-color:#17b80b;color:white;font-weight:bold">Material</td>';
		table += '<td id="material_number">'+material_number+'</td>';
		table += '</tr>';
		table += '<tr>';
		table += '<td style="background-color:#17b80b;color:white;font-weight:bold">Part Name</td>';
		table += '<td id="part_name">'+part_name+'</td>';
		table += '</tr>';
		table += '<tr>';
		table += '<td style="background-color:#17b80b;color:white;font-weight:bold">Part Type</td>';
		table += '<td id="part_type">'+part_type+'</td>';
		table += '</tr>';
		table += '<tr>';
		table += '<td style="background-color:#17b80b;color:white;font-weight:bold">Color</td>';
		table += '<td id="color">'+color+'</td>';
		table += '</tr>';
		table += '<tr>';
		table += '<td style="background-color:#17b80b;color:white;font-weight:bold">Cavity</td>';
		table += '<td id="cavity">'+cavity+'</td>';
		table += '</tr>';
		table += '<tr>';
		table += '<td style="background-color:#17b80b;color:white;font-weight:bold">Qty</td>';
		table += '<td id="qty">'+shot+'</td>';
		table += '</tr>';
		table += '<tr>';
		table += '<td style="background-color:#17b80b;color:white;font-weight:bold">Status</td>';
		table += '<td id="status">'+status+'</td>';
		table += '</tr>';
		table += '</table>';
		$('#tableCompletion').append(table);
		$('#modalCompletion').modal('show');
	}

	function completion() {
		$('#loading').show();
		if ('{{$status}}' == 'IN') {
			var data = {
				tag:$('#tag_product_rfid').text(),
				material_number:$('#material_number').text(),
				part_name:$('#part_name').text(),
				part_type:$('#part_type').text(),
				color:$('#color').text(),
				cavity:$('#cavity').text(),
				qty:$('#qty').text(),
				status:$('#status').text(),
				operator_id:$('#operator_id').val()
			}
		}else{
			var data = {
				tag:$('#tag_product').val(),
				material_number:$('#material_number').text(),
				part_name:$('#part_name').text(),
				part_type:$('#part_type').text(),
				color:$('#color').text(),
				cavity:$('#cavity').text(),
				qty:$('#qty').text(),
				status:$('#status').text(),
				operator_id:$('#operator_id').val()
			}
		}

		$.post('{{ url("index/injection/completion") }}', data, function(result, status, xhr){
			if(result.status){
				openSuccessGritter('Success!', 'Transaction Success');
				$('#loading').hide();
				$('#resultScanBody').html("");
				$('#resultNGBody').html("");
				fillResult();
				checkInjections();
				$('#modalCompletion').modal('hide');
				$('#tag_product').removeAttr("disabled");
				$("#tag_product").val("");
				$("#tag_product").focus();
				$('#operator_id').val("");
			}
			else{
				openErrorGritter('Error!', 'Upload Failed.');
				audio_error.play();
			}
		});
	}

	function cancel(){
		$('#resultScanBody').html("");
		$('#resultNGBody').html("");
		$('#tag_product').removeAttr("disabled");
		$('#tag_product').val("");
		$('#tag_product').focus();
		$('#operator_id').val("");
	}

	function cancelTag(){
		location.reload();
	}

	function fillResult() {
		var data = {
			status:'{{$status}}'
		}
		$.get('{{ url("fetch/injection/transaction") }}',data, function(result, status, xhr){
			if(result.status){
				$('#tableHistory').DataTable().clear();
				$('#tableHistory').DataTable().destroy();
				$('#tableHistoryBody').html("");
				var tableData = "";
				if (result.data.length > 0) {
					$.each(result.data, function(key, value) {
						tableData += '<tr>';
						tableData += '<td>'+ value.part_name +'</td>';
						tableData += '<td>'+ value.part_code +' - '+ value.color +'</td>';
						tableData += '<td>'+ value.quantity +'</td>';
						tableData += '<td>'+ value.location +'</td>';
						tableData += '<td>'+ value.created_at +'</td>';
						tableData += '</tr>';
					});
				}
				$('#tableHistoryBody').append(tableData);

				$('#tableHistory tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				});
				
				var table = $('#tableHistory').DataTable({
					"sDom": '<"top"i>rt<"bottom"flp><"clear">',
					'paging'      	: true,
					'lengthChange'	: false,
					'searching'   	: true,
					'ordering'		: false,
					'info'       	: true,
					'autoWidth'		: false,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"infoCallback": function( settings, start, end, max, total, pre ) {
						return "<b>Total "+ total +" pc(s)</b>";
					}
				});
			}
			else{
				alert('Attempt to retrieve data failed');
			}
		});
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '2000'
		});
	}
</script>
@endsection