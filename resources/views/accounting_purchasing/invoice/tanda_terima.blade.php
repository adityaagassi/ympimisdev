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
			<h3 class="box-title">Invoice Lists<span class="text-purple"> ??</span></span></h3>
			<button class="btn btn-success pull-right" onclick="newData('new')">Create New Invoice</button>
		</div>
		<div class="box-body">
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
						<th>Amount</th>
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
				<center><h3 style="font-weight: bold; padding: 3px;" id="modalNewTitle"></h3></center>
				<div class="row">
					<input type="hidden" id="newId">
					<div class="col-md-6">

						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="invoice_date" class="col-sm-3 control-label">Invoice Date<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<div class="input-group date">
									<div class="input-group-addon">	
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right datepicker" id="invoice_date" name="invoice_date" value="<?= date('Y-m-d') ?>">
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
							<label for="surat_jalan" class="col-sm-3 control-label">Surat Jalan<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<input type="text" class="form-control pull-right" id="surat_jalan" name="surat_jalan" placeholder="Surat Jalan">
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
							<label for="faktur" class="col-sm-3 control-label">Faktur Pajak<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<input type="text" class="form-control pull-right" id="faktur" name="faktur"  placeholder="Faktur Pajak">
							</div>
						</div>
						
					</div>
					<div class="col-md-6">
						<div class="col-md-12" style="margin-bottom: 5px;">
							<label for="po_number" class="col-sm-3 control-label">PO Number<span class="text-red">*</span></label>
							<div class="col-sm-9">
								<select class="form-control select4" id="po_number" name="po_number" data-placeholder='Pilih Nomor PO' style="width: 100%">
									<option value="">&nbsp;</option>
									@foreach($no_po as $np)
									<option value="{{$np->no_po_sap}}">{{$np->no_po_sap}}</option>
									@endforeach
								</select>
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

<div class="modal fade" id="modalDownload">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Download Attachment</h4>
			</div>
			<div class="modal-body">
				<input type="hidden" id="downloadId">
				<center>
					<div class="form-group">
						<label>Select File(s) to Download</label>
						<select multiple class="form-control" style="height: 180px;" id="selectDownload">
						</select>
					</div>
				</center>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="downloadAtt()">Download</button>
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
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});

		fetchTable();
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
			$('#modalNewTitle').text('Create New Invoice');
			$('#newButton').show();
			$('#updateButton').hide();
			clearNew();
			$('#modalNew').modal('show');
		}
		else{
			$('#newAttachment').val('');
			$('#newButton').hide();
			$('#updateButton').show();
			var data = {
				id:id
			}
			$.get('{{ url("fetch/general/agreement_detail") }}', data, function(result, status, xhr){
				if(result.status){

					$('#newDepartment').html('');
					$('#newStatus').html('');

					var newDepartment = "";
					var newStatus = "";

					$.each(result.employees, function(key, value){
						if(value.department == result.agreement.department){
							newDepartment += '<option value="'+value.department+'" selected>'+value.department+'</option>';
						}
						else{
							newDepartment += '<option value="'+value.department+'">'+value.department+'</option>';
						}
					});
					$('#newDepartment').append(newDepartment);
					$('#newVendor').val(result.agreement.vendor);
					$('#newDescription').val(result.agreement.description);
					$('#newValidFrom').val(result.agreement.valid_from);
					$('#newValidTo').val(result.agreement.valid_to);
					$.each(result.agreement_statuses, function(key, value){
						if(value == result.agreement.status){
							newStatus += '<option value="'+value+'" selected>'+value+'</option>';
						}
						else{
							newStatus += '<option value="'+value+'">'+value+'</option>';
						}
					});
					$('#newStatus').append(newStatus);
					$('#newRemark').val(result.agreement.remark);
					$('#newId').val(result.agreement.id);

					$('#modalNewTitle').text('Update Invoice');
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
			if($("#newDepartment").val() == "" || $('#newVendor').val() == "" || $('#newDescription').val() == "" || $('#newStatus').val() == "" || $('#validFrom').val() == "" || $('#validTo').val() == ""){
				$('#loading').modal('hide');
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}

			var formData = new FormData();
			var newAttachment  = $('#newAttachment').prop('files')[0];
			var file = $('#newAttachment').val().replace(/C:\\fakepath\\/i, '').split(".");

			formData.append('newAttachment', newAttachment);
			formData.append('newDepartment', $("#newDepartment").val());
			formData.append('newVendor', $("#newVendor").val());
			formData.append('newDescription', $("#newDescription").val());
			formData.append('newValidFrom', $("#newValidFrom").val());
			formData.append('newValidTo', $("#newValidTo").val());
			formData.append('newStatus', $("#newStatus").val());
			formData.append('newRemark', $("#newRemark").val());

			formData.append('extension', file[1]);
			formData.append('file_name', file[0]);

			$.ajax({
				url:"{{ url('create/general/agreement') }}",
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
			if($("#newDepartment").val() == "" || $('#newVendor').val() == "" || $('#newDescription').val() == "" || $('#newStatus').val() == "" || $('#validFrom').val() == "" || $('#validTo').val() == ""){
				$('#loading').modal('hide');
				openErrorGritter('Error', "Please fill field with (*) sign.");
				return false;
			}

			var formData = new FormData();
			var newAttachment  = $('#newAttachment').prop('files')[0];
			var file = $('#newAttachment').val().replace(/C:\\fakepath\\/i, '').split(".");

			formData.append('newId', $("#newId").val());
			formData.append('newAttachment', newAttachment);
			formData.append('newDepartment', $("#newDepartment").val());
			formData.append('newVendor', $("#newVendor").val());
			formData.append('newDescription', $("#newDescription").val());
			formData.append('newValidFrom', $("#newValidFrom").val());
			formData.append('newValidTo', $("#newValidTo").val());
			formData.append('newStatus', $("#newStatus").val());
			formData.append('newRemark', $("#newRemark").val());

			formData.append('extension', file[1]);
			formData.append('file_name', file[0]);

			$.ajax({
				url:"{{ url('edit/general/agreement') }}",
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
		$("#newDepartment").prop('selectedIndex', 0).change();
		$('#newVendor').val('');
		$('#newDescription').val('');
		$('#newValidFrom').val("");
		$('#newValidTo').val("");
		$('#newAttachment').val('');
		$("#newStatus").prop('selectedIndex', 0).change();
		$('#newRemark').val('');
		$('#newId').val('');
		$('#downloadId').val('');
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
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:3%;">'+value.supplier_name+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:8%;">'+value.invoice_no+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:0.5%;">'+value.surat_jalan+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:0.5%;">'+value.po_number+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:0.5%;">'+value.payment_term+'</td>';
					listTableBody += '<td onclick="newData(\''+value.id+'\')" style="width:5%;">'+value.amount+'</td>';
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

