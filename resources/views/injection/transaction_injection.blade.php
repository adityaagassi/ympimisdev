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
			<div class="row">
				<div class="col-md-12" style="">
					<span style="font-size: 24px;">Transaction List:</span> 
					<table id="resultScan" class="table table-bordered table-striped table-hover" style="width: 100%;">
						<input type="hidden" id="operator_id">
						<input type="hidden" id="operator_name">
			            <thead style="background-color: rgba(126,86,134,.7);">
			                <tr>
			                  <th style="width: 5%;">Material Number</th>
			                  <th style="width: 5%;">Part Name</th>
			                  <th style="width: 5%;">Part Type</th>
			                  <th style="width: 5%;">Color</th>
			                  <th style="width: 6%;">Cavity</th>
			                  <th style="width: 6%;">Qty</th>
			                  <th style="width: 6%;">Status</th>
			                  <th style="width: 6%;">Action</th>
			                </tr>
			            </thead >
			            <tbody id="resultScanBody">
						</tbody>
		            </table>
				</div>
			</div>
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
									<th>By</th>
									<th>At</th>
									<?php if ($status == 'OUT') { ?>
										<th>Detail</th>
									<?php } ?>
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
						<center><h3 style="font-weight: bold;background-color: #17b80b;color: white;padding-top: 10px;padding-bottom: 10px;font-size: 30px">CEK DATA TRANSAKSI</h3></center>
					</div>
					<div class="modal-body" id="tableCompletion">

					</div>
					<?php if ($status == "OUT"): ?>
						<div class="col-md-12">
							<span style="font-size: 24px;">NG List:</span> 
							<table id="resultNG" class="table table-bordered table-striped table-hover" style="width: 100%;">
					            <thead style="background-color: #17b80b;">
					                <tr>
					                  <th style="width: 5%;">NG</th>
					                  <th style="width: 17%;">Quantity</th>
					                </tr>
					            </thead >
					            <tbody id="resultNGBody">
								</tbody>
				            </table>
						</div>
					<?php endif ?>
					<div class="col-xs-12">
						<button class="btn btn-success btn-block" style="font-weight: bold;font-size: 25px" onclick="completion()">
							PROSES TRANSAKSI
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalDetail">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12">
						<center><h3 style="font-weight: bold;background-color: #17b80b;color: white;padding-top: 10px;padding-bottom: 10px;font-size: 30px">DETAIL TRANSAKSI</h3></center>
					</div>
					<div class="modal-body" id="tableDetail">

					</div>
					<div class="col-md-12">
						<span style="font-size: 24px;">NG List:</span> 
						<table id="resultNGDetail" class="table table-bordered table-striped table-hover" style="width: 100%;">
				            <thead style="background-color: #17b80b;">
				                <tr>
				                  <th style="width: 10%;font-size: 25px;color: white;padding: 0px">NG</th>
				                  <th style="width: 5%;font-size: 25px;color: white;padding: 0px">Quantity</th>
				                </tr>
				            </thead >
				            <tbody id="resultNGDetailBody">
							</tbody>
			            </table>
					</div>
					<div class="col-xs-12">
						<button class="btn btn-danger pull-right" style="font-weight: bold;" data-dismiss="modal">
							CLOSE
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
		// if (status == 'OUT') {
		// 	$('#modalOperator').modal({
		// 		backdrop: 'static',
		// 		keyboard: false
		// 	});
		// }
		fillResult();
		checkInjections();

		// if ('{{$status}}' == 'IN') {
			setInterval(checkInjections,10000);
		// }
		// checkInjections();

      $('body').toggleClass("sidebar-collapse");
		$("#tag_product").val("");
		$('#tag_product').focus();
		$("#operator").val("");
		$('#operator').focus();
		$("#operator_id").val("");
		$("#operator_name").val("-");
	});

	// $('#modalOperator').on('shown.bs.modal', function () {
	// 	$('#operator').focus();
	// });

	// $('#operator').keydown(function(event) {
	// 	if (event.keyCode == 13 || event.keyCode == 9) {
	// 		if($("#operator").val().length >= 8){
	// 			var data = {
	// 				employee_id : $("#operator").val()
	// 			}
				
	// 			$.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
	// 				if(result.status){
	// 					openSuccessGritter('Success!', result.message);
	// 					$('#modalOperator').modal('hide');
	// 					$('#operator_name').val(result.employee.employee_id+' - '+result.employee.name);
	// 					$('#operator_id').val(result.employee.employee_id);
	// 					$('#tag_product').focus();
	// 				}
	// 				else{
	// 					audio_error.play();
	// 					openErrorGritter('Error', result.message);
	// 					$('#operator').val('');
	// 				}
	// 			});
	// 		}
	// 		else{
	// 			openErrorGritter('Error!', 'Employee ID Invalid.');
	// 			audio_error.play();
	// 			$("#operator").val("");
	// 		}			
	// 	}
	// });

	// $('#tag_product').keyup(function(event) {
	// 	if (event.keyCode == 13 || event.keyCode == 9) {
	// 		if($("#tag_product").val().length >= 7){
	// 			var stts = '{{$status}}';
	// 			var data = {
	// 				tag : $("#tag_product").val(),
	// 				status : stts,
	// 			}

	// 			var bodyScan = "";
	// 			$('#resultScanBody').html("");
	// 			var ngScan = "";
	// 			$('#resultNGBody').html("");
	// 			var statustransaction = '{{$status}}';

	// 			var jumlah = 0;

	// 			$.get('{{ url("scan/tag_product") }}', data, function(result, status, xhr){
	// 				if(result.status){
	// 					openSuccessGritter('Success!', 'Scan Tag Success');
	// 					$('#tag_product').prop('disabled',true);
	// 					$.each(result.data, function(key, value) {
	// 						bodyScan += '<tr>';
	// 						bodyScan += '<td id="material_number">'+value.material_number+'</td>';
	// 						bodyScan += '<td id="part_name">'+value.part_name+'</td>';
	// 						bodyScan += '<td id="part_type">'+value.part_type+'</td>';
	// 						bodyScan += '<td id="color">'+value.color+'</td>';
	// 						bodyScan += '<td id="cavity">'+value.cavity+'</td>';
	// 						bodyScan += '<td id="qty">'+value.shot+'</td>';
	// 						bodyScan += '<td id="status">'+statustransaction+'</td>';
	// 						bodyScan += '</tr>';
	// 						bodyScan += '<tr>';
	// 						bodyScan += '<td colspan="7" style="padding:10px"><button class="btn btn-danger pull-left" onclick="cancel()">CANCEL</button><button class="btn btn-success pull-right" onclick="completion()">SUBMIT</button></td>';
	// 						bodyScan += '</tr>';

	// 						if (stts == 'OUT') {
	// 							if (value.ng_name != null) {
	// 								ng_arr = value.ng_name.split(',');
	// 								qty_arr = value.ng_count.split(',');

	// 								for(var i = 0; i < ng_arr.length; i++){
	// 									ngScan += '<tr>';
	// 									ngScan += '<td id="ng_name">'+ng_arr[i]+'</td>';
	// 									ngScan += '<td id="ng_qty">'+qty_arr[i]+'</td>';
	// 									ngScan += '</tr>';
	// 									jumlah = jumlah + parseInt(qty_arr[i]);
	// 								}

	// 								ngScan += '<tr style="background-color:rgba(126,86,134,.7);">';
	// 								ngScan += '<td style="border:1px solid black;border-top:1px solid black" id="total_ng_name"><b>TOTAL</b></td>';
	// 								ngScan += '<td style="border:1px solid black;border-top:1px solid black" id="total_ng_qty"><b>'+jumlah+'</b></td>';
	// 								ngScan += '</tr>';
	// 							}
	// 						}

	// 						if (statustransaction == 'IN') {
	// 							$('#operator_id').val(value.operator_id);
	// 						}
	// 					})

	// 					$('#resultScanBody').append(bodyScan);
	// 					if (stts == 'OUT') {
	// 						$('#resultNGBody').append(ngScan);
	// 					}
	// 				}
	// 				else{
	// 					openErrorGritter('Error!', 'Tag Invalid');
	// 					audio_error.play();
	// 					$("#tag_product").val("");
	// 					$("#tag_product").focus();
	// 				}
	// 			});
	// 		}
	// 		else{
	// 			openErrorGritter('Error!', 'Tag Invalid');
	// 			audio_error.play();
	// 			$("#tag_product").val("");
	// 			$("#tag_product").focus();
	// 		}			
	// 	}
	// });

	function checkInjections() {
		var data = {
			status : '{{$status}}'
		}

		var stts = '{{$status}}';

		$.get('{{ url("fetch/injection/check_injections") }}', data, function(result, status, xhr){
			if(result.status){
				var bodyScan = "";
				$('#resultScanBody').html("");
				var statustransaction = '{{$status}}';

				var jumlah = 0;
				fillResult();
				$.each(result.data, function(key, value) {
					bodyScan += '<tr style="cursor:pointer;font-size:25px">';
					bodyScan += '<td>'+value.material_number+'</td>';
					bodyScan += '<td>'+value.part_name+'</td>';
					bodyScan += '<td>'+value.part_type+'</td>';
					bodyScan += '<td>'+value.color+'</td>';
					bodyScan += '<td>'+value.cavity+'</td>';
					bodyScan += '<td>'+value.shot+'</td>';
					bodyScan += '<td>'+statustransaction+'</td>';
					bodyScan += '<td><button class="btn btn-success" style="height:100%;font-weight:bold" onclick="showModalCompletion(\''+value.process_id+'\',\''+value.injection_id+'\',\''+value.tag_rfid+'\',\''+value.material_number+'\',\''+value.part_name+'\',\''+value.part_type+'\',\''+value.color+'\',\''+value.cavity+'\',\''+value.shot+'\',\''+statustransaction+'\',\''+value.employee_id+'\',\''+value.name+'\')">TRANSAKSIKAN</button></td>';
					bodyScan += '</tr>';
					bodyScan += '<tr>';
					bodyScan += '</tr>';

					if (statustransaction == 'IN') {
						$('#operator_id').val(value.operator_id);
					}
				});

				if (result.operator != "") {
					$.each(result.operator, function(key, value) {
						$('#operator_id').val(value.tag);
						$('#operator_name').val(value.name);
					});
				}

				$('#resultScanBody').append(bodyScan);
			}
			else{
				openErrorGritter('Error!', 'Upload Failed.');
				audio_error.play();
			}
		});
	}

	function showModalCompletion(process_id,id,tag_rfid,material_number,part_name,part_type,color,cavity,shot,status,employee_id,name) {
		$('#tableCompletion').empty();
		var table = "";
		table += '<table class="table table-bordered table-responsive">';
		table += '<tr>';
		table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">Tag</td>';
		table += '<td style="font-size:20px" id="tag_product_rfid">'+tag_rfid+'</td>';
		table += '</tr>';
		table += '<tr>';
		table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">Material</td>';
		table += '<td style="font-size:20px" id="material_number">'+material_number+'</td>';
		table += '</tr>';
		table += '<tr>';
		table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">Part Name</td>';
		table += '<td style="font-size:20px" id="part_name">'+part_name+'</td>';
		table += '</tr>';
		table += '<tr>';
		table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">Part Type</td>';
		table += '<td style="font-size:20px" id="part_type">'+part_type+'</td>';
		table += '</tr>';
		table += '<tr>';
		table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">Color</td>';
		table += '<td style="font-size:20px" id="color">'+color+'</td>';
		table += '</tr>';
		table += '<tr>';
		table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">Cavity</td>';
		table += '<td style="font-size:20px" id="cavity">'+cavity+'</td>';
		table += '</tr>';
		table += '<tr>';
		table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">Qty</td>';
		table += '<td style="font-size:20px" id="qty">'+shot+'</td>';
		table += '</tr>';
		table += '<tr>';
		table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">Operator</td>';
		if (status == 'IN') {
			table += '<td style="font-size:20px" id="operator_injeksi">'+employee_id+' - '+name+'</td>';
		}else{
			table += '<td style="font-size:20px" id="operator_injeksi">'+$('#operator_id').val()+' - '+$('#operator_name').val()+'</td>';
		}
		table += '</tr>';
		table += '</table>';
		$('#tableCompletion').append(table);

		if ('{{$status}}' == 'OUT') {
			var data = {
				id:process_id,
				status : '{{$status}}'
			}
			$.get('{{ url("fetch/injection/check_ng") }}', data, function(result, status, xhr){
				if(result.status){
					var ngScan = "";
					$('#resultNGBody').html("");
					var jumlah = 0;
					$.each(result.data, function(key, value) {
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

							ngScan += '<tr style="background-color:#17b80b">';
							ngScan += '<td style="border:1px solid black;border-top:1px solid black" id="total_ng_name"><b>TOTAL</b></td>';
							ngScan += '<td style="border:1px solid black;border-top:1px solid black" id="total_ng_qty"><b>'+jumlah+'</b></td>';
							ngScan += '</tr>';
						}
					});
					$('#resultNGBody').append(ngScan);
				}else{
					openErrorGritter('Error!','Get Data Failed');
				}
			});
		}

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
				status:'IN',
				operator_id:$('#operator_id').val()
			}
		}else{
			var data = {
				tag:$('#tag_product_rfid').text(),
				material_number:$('#material_number').text(),
				part_name:$('#part_name').text(),
				part_type:$('#part_type').text(),
				color:$('#color').text(),
				cavity:$('#cavity').text(),
				qty:$('#qty').text(),
				status:'OUT',
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
					// console.table(result.data);
					$.each(result.data, function(key, value) {
						tableData += '<tr>';
						tableData += '<td>'+ value.part_name +'</td>';
						tableData += '<td>'+ value.part_code +' - '+ value.color +'</td>';
						tableData += '<td>'+ value.quantity +'</td>';
						tableData += '<td>'+ value.location +'</td>';
						if (value.name != null) {
							tableData += '<td>'+ value.employee_id +' - '+ value.name.split(' ').slice(0,2).join(' ') +'</td>';
						}else{
							tableData += '<td></td>';
						}
						tableData += '<td>'+ value.created_at +'</td>';
						if ('{{$status}}' == 'OUT') {
							tableData += '<td><button class="btn btn-primary" onclick="showModalDetail(\''+value.id+'\',)">Detail</button></td>';
						}
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

	function showModalDetail(id) {
		if ('{{$status}}' == 'OUT') {
			var data = {
				id:id,
				status : '{{$status}}'
			}
			$.get('{{ url("fetch/injection/detail_transaction") }}', data, function(result, status, xhr){
				if(result.status){
					$('#tableDetail').empty();
					var table = "";
					var ngScan = "";
					$('#resultNGDetailBody').html("");
					var jumlah = 0;
					$.each(result.data, function(key, value) {
						table += '<table class="table table-bordered table-responsive">';
						table += '<tr>';
						table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">Tag</td>';
						table += '<td style="font-size:20px">'+value.tag+'</td>';
						table += '</tr>';
						table += '<tr>';
						table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">Material</td>';
						table += '<td style="font-size:20px">'+value.material_number+'</td>';
						table += '</tr>';
						table += '<tr>';
						table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">Part Name</td>';
						table += '<td style="font-size:20px">'+value.part_name+'</td>';
						table += '</tr>';
						table += '<tr>';
						table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">Part Type</td>';
						table += '<td style="font-size:20px">'+value.part_code+'</td>';
						table += '</tr>';
						table += '<tr>';
						table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">Color</td>';
						table += '<td style="font-size:20px">'+value.color+'</td>';
						table += '</tr>';
						table += '<tr>';
						table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">Cavity</td>';
						table += '<td style="font-size:20px">'+value.cavity+'</td>';
						table += '</tr>';
						table += '<tr>';
						table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">Qty</td>';
						table += '<td style="font-size:20px">'+value.quantity+'</td>';
						table += '</tr>';
						table += '<tr>';
						table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">Mesin</td>';
						table += '<td style="font-size:20px">'+value.mesin+'</td>';
						table += '</tr>';
						table += '<tr>';
						table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">OP Injeksi</td>';
						table += '<td style="font-size:20px">'+value.employee_id+' - '+value.name+'</td>';
						table += '</tr>';
						table += '<tr>';
						table += '<td style="background-color:#17b80b;color:white;font-weight:bold;font-size:20px">OP Assy</td>';
						table += '<td style="font-size:20px">'+value.empidambil+' - '+value.nameambil+'</td>';
						table += '</tr>';
						table += '</table>';

						if (value.ng_name != null) {
							ng_arr = value.ng_name.split(',');
							qty_arr = value.ng_count.split(',');

							for(var i = 0; i < ng_arr.length; i++){
								ngScan += '<tr>';
								ngScan += '<td style="font-size:20px" id="ng_name">'+ng_arr[i]+'</td>';
								ngScan += '<td style="font-size:20px" id="ng_qty">'+qty_arr[i]+'</td>';
								ngScan += '</tr>';
								jumlah = jumlah + parseInt(qty_arr[i]);
							}

							ngScan += '<tr style="background-color:#17b80b">';
							ngScan += '<td style="border:1px solid black;border-top:1px solid black;font-size:25px;color:white" id="total_ng_name"><b>TOTAL</b></td>';
							ngScan += '<td style="border:1px solid black;border-top:1px solid black;font-size:25px;color:white" id="total_ng_qty"><b>'+jumlah+'</b></td>';
							ngScan += '</tr>';
						}
					});
					$('#tableDetail').append(table);
					$('#resultNGDetailBody').append(ngScan);
				}else{
					openErrorGritter('Error!','Get Data Failed');
				}
			});
		}

		$('#modalDetail').modal('show');
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