@extends('layouts.master')
@section('stylesheets')
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
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}

	#loading, #error { display: none; }
	.disabledTab{
		pointer-events: none;
	}
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<a href="javascript:void(0)" onclick="openModalCreate()" class="btn btn-md bg-purple" style="color:white"><i class="fa fa-plus"></i> Create {{ $page }}</a>
		</li>
	</ol>
</section>
@endsection

@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box no-border" style="margin-bottom: 5px;">
				<div class="box-header" style="margin-top: 10px">
					<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<div class="row">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="col-xs-12">
						<div class="col-md-3">
							<div class="form-group">
								<label>Date From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="datefrom">
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Date To</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="dateto">
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<div class="col-md-6" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-primary form-control" onclick="fillTable()">Search</button>
								</div>
								<div class="col-md-6" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-danger form-control" onclick="clearConfirmation()">Clear</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-xs-12">
					<div class="box no-border">
						<div class="box-header">
						</div>
						<div class="box-body" style="padding-top: 0;">
							<table id="invTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 1%">No PO</th>
										<th style="width: 1%">Tanggal PO</th>
										<th style="width: 1%">Supplier</th>
										<th style="width: 1%">Material</th>
										<th style="width: 1%">VAT</th>
										<th style="width: 1%">Transportation</th>
										<th style="width: 1%">Delivery Term</th>
										<th style="width: 1%">Holding Tax</th>
										<th style="width: 1%">Currency</th>
										<th style="width: 1%">Catatan</th>
										<th style="width: 1%">Status</th>
										<th style="width: 1%">Action</th>
									</tr>
								</thead>
								<tbody>
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
										<th></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<form id="importForm" name="importForm" method="post" action="{{ url('create/purchase_order') }}" enctype="multipart/form-data">
	<input type="hidden" value="{{csrf_token()}}" name="_token" />
	<div class="modal fade" id="modalCreate">
		<div class="modal-dialog modal-lg" style="width: 1300px">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Create Purchase Order</h4>
					<br>
					<div class="nav-tabs-custom tab-danger">
						<ul class="nav nav-tabs">
							<li class="vendor-tab active disabledTab"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Informasi PO</a></li>
							<li class="vendor-tab disabledTab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Detail PO</a></li>
							<li class="vendor-tab disabledTab"><a href="#tab_3" data-toggle="tab" id="tab_header_3">Detail Item</a></li>
						</ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane active" id="tab_1">
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-4">
										<div class="form-group">
											<label>Nomor PO<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="no_po" name="no_po" readonly="">
										</div>
										<div class="form-group">
											<label>Tanggal PO<span class="text-red">*</span></label>
											<div class="input-group date">
												<div class="input-group-addon">	
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control pull-right" id="sd" name="sd" value="<?= date('d F Y')?>" readonly="">
												<input type="hidden" class="form-control pull-right" id="tgl_po" name="tgl_po" value="<?= date('Y-m-d')?>" readonly="">
											</div>
										</div>
										<div class="form-group">
											<label>Supplier<span class="text-red">*</span></label>
											<select class="form-control select2" id="supplier_po" data-placeholder='Supplier' style="width: 100%" onchange="getSupplier(this)">
											  <option value="">&nbsp;</option>
											  @foreach($vendor as $ven)
											  <option value="{{$ven->supplier_name}}">{{$ven->supplier_name}}</option>
											  @endforeach
											</select>
										</div>
										<div class="form-group">
											<label>Due Payment (Vendor)<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="vendor_due_payment" name="vendor_due_payment" readonly="">
										</div>
										<div class="form-group">
											<label>Status (Vendor)<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="vendor_status" name="vendor_status" readonly="">
										</div>
										<div class="form-group">
											<label>Material<span class="text-red">*</span></label>
											<select class="form-control select2" id="material_po" data-placeholder='Material Status' style="width: 100%">
											  <option value="">&nbsp;</option>
											  <option value="">None</option>
											  <option value="Dipungut PPNBM">Dipungut PPNBM</option>
											  <option value="Tidak Dipungut PPNB">Tidak Dipungut PPNB</option>
											</select>
										</div>
									</div>
									<div class="col-md-4">
										
										<div class="form-group">
											<label>Price VAT<span class="text-red">*</span></label>
											<select class="form-control select2" id="price_vat" data-placeholder='Price VAT' style="width: 100%">
											  <option value="">&nbsp;</option>
											  <option value="Include VAT">Include VAT</option>
											  <option value="Exclude VAT">Exclude VAT</option>
											</select>
										</div>
										<div class="form-group">
											<label>Transportation<span class="text-red">*</span></label>
											<select class="form-control select2" id="transportation" data-placeholder='Transportation' style="width: 100%">
											  <option value="">&nbsp;</option>
											  @foreach($transportation as $trans)
											  <option value="{{$trans}}">{{$trans}}</option>
											  @endforeach
											</select>
										</div>
										<div class="form-group">
											<label>Delivery Term<span class="text-red">*</span></label>
											<select class="form-control select2" id="delivery_term" data-placeholder='Delivery Term' style="width: 100%">
											  <option value="">&nbsp;</option>
											  @foreach($delivery as $deliver)
											  <option value="{{$deliver}}">{{$deliver}}</option>
											  @endforeach
											</select>
										</div>
										<div class="form-group">
											<label>Holding Tax<span class="text-red">*</span></label>
											<select class="form-control select2" id="holding_tax" data-placeholder='Holding Tax' style="width: 100%">
											  <option value="">&nbsp;</option>
											  <option value="0">0 (Purchase Order)</option>
											  <option value="2">2 (Job Order)</option>
											  <option value="10">10 (Investment)</option>
											</select>
										</div>
										<div class="form-group">
											<label>Currency<span class="text-red">*</span></label>
											<select class="form-control select2" id="currency" data-placeholder='Currency' style="width: 100%">
											  <option value="">&nbsp;</option>
											  <option value="USD">USD</option>
											  <option value="IDR">IDR</option>
											  <option value="JPN">JPN</option>
											</select>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>Authorized 1 / Buyer<span class="text-red">*</span></label>
											<input type="text" class="form-control" value="{{$employee->employee_id}} - {{$employee->name}}" readonly="">
											<input type="hidden" id="buyer_id" name="buyer_id" value="{{$employee->employee_id}}">
											<input type="hidden" id="buyer_name" name="buyer_name" value="{{$employee->name}}">
										</div>
										<div class="form-group">
											<label>Authorized 2<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="authorized2" name="authorized2" readonly="" value="{{$authorized2->name}}">
										</div>
										<div class="form-group">
											<label>Authorized 3<span class="text-red">*</span></label>
											<select class="form-control select2" id="authorized3" data-placeholder='Pilih Authorized 3' style="width: 100%">
											  <option value="">&nbsp;</option>
											  @foreach($authorized3 as $author3)
											  <option value="{{$author3->employee_id}}">{{$author3->name}}</option>
											  @endforeach
											</select>
										</div>
										<div class="form-group">
											<label>Catatan / Keterangan</label>
											<textarea class="form-control pull-right" id="note" name="note"></textarea>
										</div>
									</div>
								</div>
								<div class="col-md-12"  style="padding-right: 30px;padding-top: 10px">
									<a class="btn btn-primary btnNext pull-right">Selanjutnya</a>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab_2">
							<div class="row">
								<div class="col-md-12">
									<div class="col-xs-2" style="padding:5px;">
										<b>NO PR</b>
									</div>
									<div class="col-xs-2" style="padding:5px;">
										<b>No Item</b>
									</div>
									<div class="col-xs-2" style="padding:5px;">
										<b>No Budget</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Del Date</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Qty</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>UOM</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Price</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Nama</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Aksi</b>
									</div>

									<input type="text" name="lop" id="lop" value="1" hidden>
									

								</div>
								
								<div id="tambah"></div>

								<div class="col-md-12">
									<a class="btn btn-primary btnNext2 pull-right">Selanjutnya</a>
									<span class="pull-right">&nbsp;</span>
									<a class="btn btn-info btnPrevious pull-right">Kembali</a>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab_3">
							<div class="row">
								<div class="col-md-12">
									<br>
									<button class="btn btn-success pull-right" onclick="$('[name=importForm]').submit();">Konfirmasi</button>
									<span class="pull-right">&nbsp;</span>
									<a class="btn btn-primary btnPrevious pull-right">Kembali</a>
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

@endsection

@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
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
		fillTable();
		$('#datefrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#dateto').datepicker({
			autoclose: true,
			todayHighlight: true
		});

		$('.btnNext').click(function(){
			var no_po = $('#no_po').val();
			var supplier_po = $('#supplier_po').val();
			var material_po = $('#material_po').val();
			var price_vat = $('#price_vat').val();
			var transportation = $('#transportation').val();
			var delivery_term = $('#delivery_term').val();
			var holding_tax = $('#holding_tax').val();
			var currency = $('#currency').val();
			var authorized = $('#authorized3').val();

			if(no_po == '' || supplier_po == "" || material_po == ""){
				alert('All field must be filled');	
			}
			else{
				$('.nav-tabs > .active').next('li').find('a').trigger('click');
			}
		});

		$('.btnPrevious').click(function(){
			$('.nav-tabs > .active').prev('li').find('a').trigger('click');
		});


	});

	function clearConfirmation(){
		location.reload(true);		
	}

	function fillTable(){
		$('#invTable').DataTable().clear();
		$('#invTable').DataTable().destroy();

		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var department = $('#department').val();
		
		var data = {
			datefrom:datefrom,
			dateto:dateto,
			department:department,
		}

		var table = $('#invTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'buttons': {
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default',
					// text: '<i class="fa fa-print"></i> Show',
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
				"type" : "get",
				"url" : "{{ url("fetch/purchase_order") }}",
				"data" : data
			},
			"columns": [
			{ "data": "no_po" },
			{ "data": "tgl_po" },
			{ "data": "supplier" },
			{ "data": "material" },
			{ "data": "vat" },
			{ "data": "transportation" },
			{ "data": "delivery_term" },
			{ "data": "holding_tax" },
			{ "data": "currency" },
			{ "data": "catatan" },
			{ "data": "status" },
			{ "data": "action" },

			],
		});

		$('#invTable tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="3"/>' );
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
		$('#invTable tfoot tr').appendTo('#invTable thead');
	}

	$('.select2').select2();



	function openModalCreate(){
		$('#modalCreate').modal('show');

		//nomor PR auto generate
	    var nomorpo = document.getElementById("no_po");

		 $.ajax({
           url: "{{ url('purchase_order/get_nomor_po') }}?dept=<?= $employee->department ?>", 
           type : 'GET', 
           success : function(data){
              var obj = jQuery.parseJSON(data);
              var no = obj.no_urut;
              var tahun = obj.tahun;
              var bulan = obj.bulan;
              var dept = obj.dept;

              nomorpo.value = "EQ"+tahun+bulan+no+"-"+dept;
           }
        });
	}

	function getSupplier(elem){

		$.ajax({
            url: "{{ route('admin.pogetsupplier') }}?supplier_name="+elem.value,
            method: 'GET',
            success: function(data) {
                var json = data,
                obj = JSON.parse(json);
                $('#vendor_due_payment').val(obj.duration);
                $('#vendor_status').val(obj.status);            } 
        });
	}

</script>

@endsection