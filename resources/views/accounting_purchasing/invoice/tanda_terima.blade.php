@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	#listTableBody > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;

	}
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>
	<div class="box">
		<div class="box-header">
			<input type="hidden" value="{{csrf_token()}}" name="_token" />
			<form method="GET" action="{{ url("export/invoice/tanda_terima") }}">
				<div class="col-md-2" style="padding: 0">
					<div class="form-group">
						<label>Date From</label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control pull-right datepicker" id="datefrom" name="datefrom">
						</div>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label>Date To</label>
						<div class="input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input type="text" class="form-control pull-right datepicker" id="dateto" name="dateto">
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<div class="col-md-4" style="padding-right: 0;">
							<label style="color: white;"> x</label>
							<button type="submit" class="btn btn-primary form-control"><i class="fa fa-download"></i> Export List Tanda Terima</button>
						</div>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
							<label style="color: white;"> x</label>
							<a class="btn btn-success pull-right" style="width: 100%" onclick="newData('new')"><i class="fa fa-plus"></i> &nbsp;Buat Tanda Terima</a>
					</div>
				</div>
			</form>
		</div>
		<div class="box-body">
			<div class="row">
				
			</div>

			<table id="listTable" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th>#</th>
						<th>Invoice Date</th>
						<th>Supplier</th>
						<th>Invoice No</th>
						<th>Surat Jalan</th>
						<th>PO Number</th>
						<th>Payment Term</th>
						<th>Currency</th>
						<th>Amount</th>
						<th>Due Date</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody id="listTableBody">
				</tbody>
				<tfoot>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
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
</section>

<div class="modal fade" id="modalNew">
	<div class="modal-dialog" style="width: 80%">
		<div class="modal-content">
			<div class="modal-header" style="padding-top: 0;">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: 10px">
					<span aria-hidden="true">&times;</span>
				</button>
				<center><h3 style="font-weight: bold; padding: 3px;" id="modalNewTitle"></h3></center>
				<div class="row">
					<input type="hidden" id="id_edit">
					<div class="col-md-6">

						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="invoice_date" class="col-sm-3 control-label">Invoice Date<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<div class="input-group date">
									<div class="input-group-addon">	
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker" id="invoice_date" name="invoice_date" value="<?php echo date('Y-m-d') ?>" placeholder="Invoice Date">
								</div>
							</div>
						</div>
						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="supplier_code" class="col-sm-3 control-label">Supplier Name<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select4" id="supplier_code" name="supplier_code" data-placeholder='Choose Supplier Name' style="width: 100%" onchange="getSupplier(this)">
									<option value="">&nbsp;</option>
									@foreach($vendor as $ven)
									<option value="{{$ven->vendor_code}}">{{$ven->vendor_code}} - {{$ven->supplier_name}}</option>
									@endforeach
								</select>
								<input type="hidden" class="form-control" id="supplier_name" name="supplier_name" readonly="">
							</div>
						</div>
						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="kwitansi" class="col-sm-3 control-label">Kwitansi</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" id="kwitansi" name="kwitansi" placeholder="Kwitansi">
							</div>
						</div>
						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="invoice_no" class="col-sm-3 control-label">Invoice No<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<input type="text" class="form-control pull-right" id="invoice_no" name="invoice_no" placeholder="Invoice Number">							
							</div>
						</div>
						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="surat_jalan" class="col-sm-3 control-label">Surat Jalan</label>
							<div class="col-sm-9">
								<!-- <select class="form-control select4" id="surat_jalan" name="surat_jalan" data-placeholder='Pilih Surat Jalan' style="width: 100%">
									<option value="">&nbsp;</option>
									@foreach($surat_jalan as $sj)
									<option value="{{$sj->invoice_no}}">{{$sj->invoice_no}}</option>
									@endforeach
								</select> -->

								<input type="text"  class="form-control" id="surat_jalan" name="surat_jalan" placeholder="Surat Jalan">
							</div>
						</div>
						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="bap" class="col-sm-3 control-label">BAP</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" id="bap" name="bap" placeholder="Masukkan Berita Acara Pemeriksaan">
							</div>
						</div>
						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="npwp" class="col-sm-3 control-label">NPWP</label>
							<div class="col-sm-9">
								<input type="text" class="form-control pull-right" id="npwp" name="npwp" placeholder="Nomor Pokok Wajib Pajak">
							</div>
						</div>
						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="faktur_pajak" class="col-sm-3 control-label">Faktur Pajak</label>
							<div class="col-sm-9">
								<input type="text" class="form-control pull-right" id="faktur_pajak" name="faktur_pajak"  placeholder="Faktur Pajak">
							</div>
						</div>
						
					</div>
					<div class="col-md-6">
						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="po_number" class="col-sm-3 control-label">PO Number<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<!-- <select class="form-control select4" id="po_number" name="po_number" data-placeholder='Pilih Nomor PO' style="width: 100%">
									<option value="">&nbsp;</option>
									@foreach($no_po as $np)
									<option value="{{$np->no_po_sap}}">{{$np->no_po_sap}}</option>
									@endforeach
								</select> -->
								<input type="text"  class="form-control" id="po_number" name="po_number" placeholder="PO Number">
							</div>
						</div>
						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="payment_term" class="col-sm-3 control-label">Payment Term<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select4" id="payment_term" name="payment_term" data-placeholder='Pilih Metode Pembayaran' style="width: 100%">
									<option value="">&nbsp;</option>
									@foreach($payment_term as $pt)
									<option value="{{$pt->payment_term}}">{{$pt->payment_term}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-12" style="margin-bottom: 5px">
							<label for="Currency" class="col-sm-3 control-label">Currency<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select4" id="currency" name="currency" data-placeholder='Currency' style="width: 100%">
									<option value="">&nbsp;</option>
									<option value="USD">USD</option>
									<option value="IDR">IDR</option>
									<option value="JPY">JPY</option>
								</select>
							</div>
						</div>
						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="amount" class="col-sm-3 control-label">Amount<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" id="amount" name="amount" placeholder="Total Amount">
							</div>
						</div>
						<div class="col-md-12" style="margin-bottom: 5px">
							<label for="do_date" class="col-sm-3 control-label">DO Date<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<div class="input-group date">
									<div class="input-group-addon">	
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker" id="do_date" name="do_date" placeholder="Document Date">
								</div>
							</div>
						</div>
						<!-- <div class="col-md-12" style="margin-bottom: 5px;">
							<label for="detail" class="col-sm-3 control-label">Detail</label>
							<div class="col-sm-9">
								<textarea class="form-control" id="detail" name="detail" placeholder="Enter detail"></textarea>
							</div>
						</div> -->
						<div class="col-md-12" style="margin-bottom: 5px">
							<label for="do_date" class="col-sm-3 control-label">Due Date<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<div class="input-group date">
									<div class="input-group-addon">	
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker" id="due_date" name="due_date" placeholder="Due Date">
								</div>
							</div>
						</div>
						<!-- <div class="col-md-12" style="margin-bottom: 5px">
							<label for="do_date" class="col-sm-3 control-label">Transfer Date<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<div class="input-group date">
									<div class="input-group-addon">	
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker" id="transfer_date" name="transfer_date" placeholder="Transfer Date">
								</div>
							</div>
						</div> -->
						<div class="col-md-12" style="margin-bottom: 5px">
							<label for="do_date" class="col-sm-3 control-label">Distribution Date</label>
							<div class="col-sm-9">
								<div class="input-group date">
									<div class="input-group-addon">	
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker" id="distribution_date" name="distribution_date" placeholder="Distribution Date">
								</div>
							</div>
						</div>
						<!-- <div class="col-md-12" style="margin-bottom: 5px;">
							<label for="remark" class="col-sm-3 control-label">Remark<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<input type="text" class="form-control" id="remark" name="remark" placeholder="Enter Remark">
							</div>
						</div> -->
					</div>
					<div class="col-md-12">
						<a class="btn btn-success pull-right" onclick="SaveInvoice('new')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="newButton">CREATE</a>
						<a class="btn btn-info pull-right" onclick="SaveInvoice('update')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="updateButton">UPDATE</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {

    	$('body').toggleClass("sidebar-collapse");

		fetchTable();
	});

	$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});

	$('.select2').select2({
		dropdownAutoWidth : true,
		allowClear: true
	});

	$(function () {
		$('.select4').select2({
			allowClear:true,
			dropdownAutoWidth : true,
			tags: true
		});
	})

	 $("#amount").change(function(){
		var output = parseFloat($('#amount').val()); 
		var output2 = output.toLocaleString(undefined,{'minimumFractionDigits':2,'maximumFractionDigits':2});
		$('#amount').val(output2);
  	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function newData(id){

		if(id == 'new'){

			$('#modalNewTitle').text('Buat Tanda Terima');
			$('#newButton').show();
			$('#updateButton').hide();
			clearNew();
			$('#modalNew').modal('show');
		}
		else{
			$('#newButton').hide();
			$('#updateButton').show();
			var data = {
				id:id
			}
			$.get('{{ url("invoice/tanda_terima_detail") }}', data, function(result, status, xhr){
				if(result.status){

					$('#supplier_code').html('');
					// $('#surat_jalan').html('');
					// $('#po_number').html('');
					$('#payment_term').html('');
					$('#currency').html('');

					var supplier_code = "";
					// var surat_jalan = "";
					// var po_number = "";
					var payment_term = "";
					var currency = "";

					$('#invoice_date').val(result.invoice.invoice_date);

					$.each(result.vendor, function(key, value){
						if(value.vendor_code == result.invoice.supplier_code){
							supplier_code += '<option value="'+value.vendor_code+'" selected>'+value.vendor_code+' - '+value.supplier_name+'</option>';
						}
						else{
							supplier_code += '<option value="'+value.vendor_code+'">'+value.vendor_code+' - '+value.supplier_name+'</option>';
						}
					});

					$('#supplier_code').append(supplier_code);

					$('#supplier_name').val(result.invoice.supplier_name);
					$('#invoice_no').val(result.invoice.invoice_no);
					$('#kwitansi').val(result.invoice.kwitansi);

					// $.each(result.surat_jalan, function(key, value){
					// 	if(value.invoice_no == result.invoice.surat_jalan){
					// 		surat_jalan += '<option value="'+value.invoice_no+'" selected>'+value.invoice_no+'</option>';
					// 	}
					// 	else{
					// 		surat_jalan += '<option value="'+value.invoice_no+'">'+value.invoice_no+'</option>';
					// 	}
					// });


					// $('#surat_jalan').append(surat_jalan);
					$('#surat_jalan').val(result.invoice.surat_jalan);
					
					$('#bap').val(result.invoice.bap);
					$('#npwp').val(result.invoice.npwp);
					$('#faktur_pajak').val(result.invoice.faktur_pajak);

					// $.each(result.no_po, function(key, value){
					// 	if(value.no_po_sap == result.invoice.po_number){
					// 		po_number += '<option value="'+value.no_po_sap+'" selected>'+value.no_po_sap+'</option>';
					// 	}
					// 	else{
					// 		po_number += '<option value="'+value.no_po_sap+'">'+value.no_po_sap+'</option>';
					// 	}
					// });

					// $('#po_number').append(po_number);
					$('#po_number').val(result.invoice.po_number);


					$.each(result.payment_term, function(key, value){
						if(value.payment_term == result.invoice.payment_term){
							payment_term += '<option value="'+value.payment_term+'" selected>'+value.payment_term+'</option>';
						}
						else{
							payment_term += '<option value="'+value.payment_term+'">'+value.payment_term+'</option>';
						}
					});

					$('#payment_term').append(payment_term);

					if(result.invoice.currency == "USD"){
						currency += '<option value="USD" selected>USD</option>';
						currency += '<option value="IDR">IDR</option>';
						currency += '<option value="JPY">JPY</option>';
					}
					else if (result.invoice.currency == "IDR"){
						currency += '<option value="USD">USD</option>';
						currency += '<option value="IDR" selected>IDR</option>';
						currency += '<option value="JPY">JPY</option>';
					}
					else if (result.invoice.currency == "JPY"){
						currency += '<option value="USD">USD</option>';
						currency += '<option value="IDR">IDR</option>';
						currency += '<option value="JPY" selected>JPY</option>';
					}

					$('#currency').append(currency);

					$('#amount').val(result.invoice.amount);
					$('#do_date').val(result.invoice.do_date);
					$('#due_date').val(result.invoice.due_date);
					$('#distribution_date').val(result.invoice.distribution_date);

					$('#id_edit').val(result.invoice.id);
					$('#modalNewTitle').text('Update Tanda Terima');
					$('#loading').hide();
					$('#modalNew').modal('show');
				}
				else{
					openErrorGritter('Error', result.message);
					$('#loading').hide();
					audio_error.play();
				}
			});
		}
	}

	function SaveInvoice(id){
		$('#loading').show();

		if(id == 'new'){
			if($("#invoice_date").val() == "" || $('#supplier_code').val() == null || $('#invoice_no').val() == "" || $('#po_number').val() == "" || $('#payment_term').val() == "" || $('#currency').val() == "" || $('#amount').val() == "" || $('#do_date').val() == "" || $('#due_date').val() == ""){
				
				$('#loading').hide();
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}

			var formData = new FormData();
			formData.append('invoice_date', $("#invoice_date").val());
			formData.append('supplier_code', $("#supplier_code").val());
			formData.append('supplier_name', $("#supplier_name").val());
			formData.append('kwitansi', $("#kwitansi").val());
			formData.append('invoice_no', $("#invoice_no").val());
			formData.append('surat_jalan', $("#surat_jalan").val());
			formData.append('bap', $("#bap").val());
			formData.append('npwp', $("#npwp").val());
			formData.append('faktur_pajak', $("#faktur_pajak").val());

			formData.append('po_number', $("#po_number").val());
			formData.append('payment_term', $("#payment_term").val());
			formData.append('currency', $("#currency").val());
			formData.append('amount', $("#amount").val());
			formData.append('do_date', $("#do_date").val());
			formData.append('due_date', $("#due_date").val());
			formData.append('distribution_date', $("#distribution_date").val());

			$.ajax({
				url:"{{ url('create/invoice/tanda_terima') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success', data.message);
						audio_ok.play();
						$('#loading').hide();
						$('#modalNew').modal('hide');
						clearNew();
						fetchTable();
					}else{
						openErrorGritter('Error!',data.message);
						$('#loading').hide();
						audio_error.play();
					}

				}
			});
		}
		else{
			if($("#invoice_date").val() == "" || $('#supplier_code').val() == "" || $('#invoice_no').val() == "" || $('#po_number').val() == "" || $('#payment_term').val() == "" || $('#currency').val() == "" || $('#amount').val() == "" || $('#do_date').val() == "" || $('#due_date').val() == ""){
				
				$('#loading').hide();
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}

			var formData = new FormData();
			formData.append('id_edit', $("#id_edit").val());
			formData.append('invoice_date', $("#invoice_date").val());
			formData.append('supplier_code', $("#supplier_code").val());
			formData.append('supplier_name', $("#supplier_name").val());
			formData.append('kwitansi', $("#kwitansi").val());
			formData.append('invoice_no', $("#invoice_no").val());
			formData.append('surat_jalan', $("#surat_jalan").val());
			formData.append('bap', $("#bap").val());
			formData.append('npwp', $("#npwp").val());
			formData.append('faktur_pajak', $("#faktur_pajak").val());

			formData.append('po_number', $("#po_number").val());
			formData.append('payment_term', $("#payment_term").val());
			formData.append('currency', $("#currency").val());
			formData.append('amount', $("#amount").val());
			formData.append('do_date', $("#do_date").val());
			formData.append('due_date', $("#due_date").val());
			formData.append('distribution_date', $("#distribution_date").val());

			$.ajax({
				url:"{{ url('edit/invoice/tanda_terima') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success', data.message);
						audio_ok.play();
						$('#loading').hide();
						$('#modalNew').modal('hide');
						clearNew();
						fetchTable();
					}else{
						openErrorGritter('Error!',data.message);
						$('#loading').hide();
						audio_error.play();
					}

				}
			});
		}
	}

	function clearNew(){
		$('#id_edit').val('');
		$("#invoice_date").val('');
		$("#supplier_code").val('').trigger('change');
		$("#supplier_name").val('');
		$('#invoice_no').val('');
		$('#kwitansi').val('');
		$('#surat_jalan').val('');
		$('#bap').val("");
		$('#npwp').val("");
		$('#faktur_pajak').val('');
		$("#po_number").val('');
		$("#payment_term").val('').trigger('change');
		$("#currency").val('').trigger('change');
		$('#amount').val('');
		$('#do_date').val('');
		$('#due_date').val('');
		$('#distribution_date').val('');
	}

	function fetchTable(){
		$('#loading').show();
		$.get('{{ url("fetch/invoice/tanda_terima") }}', function(result, status, xhr){
			if(result.status){
				$('#listTable').DataTable().clear();
				$('#listTable').DataTable().destroy();				
				$('#listTableBody').html("");
				var listTableBody = "";
				var count_all = 0;

				$.each(result.invoice, function(key, value){
					listTableBody += '<tr>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:0.1%;">'+parseInt(key+1)+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:1%;">'+value.invoice_date+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:3%;">'+value.supplier_code+' - '+value.supplier_name+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:2%;">'+value.invoice_no+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:1%;">'+value.surat_jalan+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:1%;">'+value.po_number+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:3%;">'+value.payment_term+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:0.1%;">'+value.currency+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:1%;text-align:right">'+value.amount+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:1%;">'+value.due_date+'</td>';
					listTableBody += '<td style="width:2%;"><center><button class="btn btn-md btn-warning" onclick="newData(\''+value.id+'\')"><i class="fa fa-eye"></i> </button>  <a class="btn btn-md btn-danger" target="_blank" href="{{ url("invoice/report") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a></center></td>';
					listTableBody += '</tr>';


					count_all += 1;
				});

				$('#count_all').text(count_all);

				$('#listTableBody').append(listTableBody);

				$('#listTable tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				} );

				var table = $('#listTable').DataTable({
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

				table.columns().every( function () {
					var that = this;

					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				} );

				$('#listTable tfoot tr').appendTo('#listTable thead');

				$('#loading').hide();

			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#loading').hide();
			}
		});
	}

	function getSupplier(elem){

			$.ajax({
				url: "{{ route('admin.pogetsupplier') }}?supplier_code="+elem.value,
				method: 'GET',
				success: function(data) {
					var json = data,
					obj = JSON.parse(json);
					$('#supplier_name').val(obj.name);
				} 
			});
		}

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

