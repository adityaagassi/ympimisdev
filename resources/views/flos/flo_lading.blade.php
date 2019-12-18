@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Final Line Outputs <span class="text-purple">ファイナルライン出力</span>
		<small>Lading <span class="text-purple">荷揚げ</span></small>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">FLO Lading <span class="text-purple">FLO荷揚げ</span></span></h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">

					<div class="col-md-12 col-md-offset-4">
						<div class="col-md-4">
							<div class="form-group">
								<select class="form-control select2" data-placeholder="Select Invoice Number" name="invoice_number" id="invoice_number" style="width: 100%;" required>
									<option></option>
									@foreach($invoices as $invoice)
									<option value="{{ $invoice->invoice_number }}">{{ $invoice->invoice_number }}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<label>BL Date</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="bl_date" nama="bl_date" required>
								</div>
							</div>
							<div class="form-group pull-right">
								<button href="javascript:void(0)" id="confirm" onClick="inputBlDate()" class="btn btn-primary">Confirm</button>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<table id="flo_invoice_table" class="table table-bordered table-striped">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 5%">Invoice Number</th>
										<th style="width: 10%">Ship. Date</th>
										<th style="width: 5%">Dest.</th>
										<th style="width: 15%">Dest. Name</th>
										<th style="width: 10%">Actual BL Date</th>
										<th style="width: 5%" class="notexport">Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="editModal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Edit BL Date</h4>
			</div>
			<div class="modal-body">
				<input type="text" style="text-align: center;" class="form-control" name="modal_invoice_number" id="modal_invoice_number" disabled>
				<br>
				<label>
					BL Date
				</label>
				<div class="input-group date">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input type="text" class="form-control pull-right" id="modal_bl_date" nama="modal_bl_date" required>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Confirm</button>
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
<script src="{{ url("js/pdfmake.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('#invoice_number').val('').change();
		$('#bl_date').val('');
		$('#bl_date').datepicker({
			autoclose: true
		});
		$('#modal_bl_date').datepicker({
			autoclose: true
		});
		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no invoice with empty BL Date";
				}
			}
		});
		fillInvoiceTable();
	});

	function inputBlDate(){
		if($('#invoice_number').val() != '' && $('#bl_date').val() != ''){
			var invoice_number = $('#invoice_number').val();
			var bl_date = $('#bl_date').val();
			var data = {
				invoice_number : invoice_number,
				bl_date : bl_date,
			}
			$.post('{{ url("input/flo_lading") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){
						$('#flo_invoice_table').DataTable().ajax.reload();
						$('#invoice_number').val('').change();
						$('#bl_date').val('');
						openSuccessGritter('Success!', result.message);
					}
				}
				else{
					openErrorGritter('Error!', 'Disconnected from server');
					audio_error.play();
				}
			});
		}
		else{
			openErrorGritter('Error!', 'Invoice number and bl date required');
			audio_error.play();
		}
	}

	function editConfirmation(id){
		var data = {
			id:id,
		}
		$.get('{{ url("fetch/flo_lading") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#modal_invoice_number').val(result.invoice_number);
					$('#modal_bl_date').val(result.bl_date);
					$('#editModal').modal('show');
				}
			}
			else{
				openErrorGritter('Error!', 'Disconnected from server');
				audio_error.play();
			}
		});
	}

	function fillInvoiceTable(){
		$('#flo_invoice_table').DataTable().destroy();

		$('#flo_invoice_table tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
		});
		var table = $('#flo_invoice_table').DataTable({
			'dom': 'Bfrtip',
			'buttons': {
				dom: {
					button: {
						tag:'button',
						className:''
					}
				},
				buttons:[
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
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "post",
				"url" : "{{ url("index/flo_invoice") }}",
			},
			"columns": [
			{ "data": "invoice_number" },
			{ "data": "st_date" },
			{ "data": "destination_code" },
			{ "data": "destination_name" },
			{ "data": "actual_bl_date" },
			{ "data": "action" }
			]
		});

		table.columns().every( function () {
			var that = this;

			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			});
		});

		$('#flo_invoice_table tfoot tr').appendTo('#flo_invoice_table thead');
	}

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

	function openInfoGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-info',
			image: '{{ url("images/image-unregistered.png") }}',
			sticky: false,
			time: '2000'
		});
	}
</script>
@endsection