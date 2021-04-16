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

	input.currency {
		text-align: left;
		padding: 2px 5px;
	}

	.input-group-addon {
		padding: 2px 5px;
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

	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>   
	@endif

	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif

	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
	    <p style="position: absolute; color: White; top: 45%; left: 35%;">
	      <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
	    </p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box no-border" style="margin-bottom: 5px;">
				<div class="box-header" style="margin-top: 10px">
					<h3 class="box-title">Outstanding PR yang Belum Di PO</span></h3>

					<div class="row">
						<div class="col-xs-12">
							<div class="box no-border">
								<div class="box-header">
								</div>
								<div class="box-body" style="padding-top: 0;">
									<table id="outstandingTable" class="table table-bordered table-striped table-hover">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th style="width: 1%">Nomor PR</th>
												<th style="width: 2%">Departemen</th>
												<th style="width: 1%">Tanggal Pengajuan</th>
												<th style="width: 1%">User</th>
												<th style="width: 1%">Nomor Budget</th>
												<th style="width: 1%">Att</th>
												<th style="width: 2%">Action</th>
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
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box no-border" style="margin-bottom: 5px;">
				<div class="box-header" style="margin-top: 10px">
					<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<div class="row">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<form method="GET" action="{{ url("export/purchase_order/list") }}">
						<div class="col-xs-12">
							<div class="col-md-2">
								<div class="form-group">
									<label>Date From</label>
									<div class="input-group date">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										<input type="text" class="form-control pull-right" id="datefrom" name="datefrom">
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
										<input type="text" class="form-control pull-right" id="dateto" name="dateto">
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<div class="col-md-4" style="padding-right: 0;">
										<label style="color: white;"> x</label>
										<button type="button" class="btn btn-primary form-control" onclick="fillTable()">Search</button>
									</div>
									<div class="col-md-4" style="padding-right: 0;">
										<label style="color: white;"> x</label>
										<button type="button" class="btn btn-danger form-control" onclick="clearConfirmation()">Clear</button>
									</div>
									<div class="col-md-4" style="padding-right: 0;">
										<label style="color: white;"> x</label>
										<button type="submit" class="btn btn-success form-control"><i class="fa fa-download"></i> Export List PO</button>
									</div>
								</div>
							</div>
							
						</div>
					</form>

				</div>
			</div>
			
			<div class="row">
				<div class="col-xs-12">
					<div class="box no-border">
						<div class="box-header">
						</div>
						<div class="box-body" style="padding-top: 0;">
							<table id="poTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 2%">No PO</th>
										<th style="width: 5%">Buyer</th>
										<th style="width: 1%">Tanggal PO</th>
										<th style="width: 4%">Supplier</th>
										<th style="width: 1%">No PO SAP</th>
										<th style="width: 1%">Status</th>
										<th style="width: 6%">Action</th>
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
						</ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane active" id="tab_1">
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-4">
										<div class="col-md-6" style="padding:0">
											<div class="form-group">
												<label>Nomor PO<span class="text-red">*</span></label>
												<input type="text" class="form-control" id="no_po1" name="no_po1" readonly="">
												<input type="hidden" class="form-control" id="remark" name="remark" value="PR">
											</div>
										</div>
										<div class="col-md-6" style="padding:0">
											<div class="form-group">
												<label>&nbsp;</label>
												<input type="text" class="form-control" id="no_po2" name="no_po2" placeholder="E.g. : 001-IT">
											</div>
										</div>
										<div class="form-group">
											<label>Tanggal PO<span class="text-red">*</span></label>
											<div class="input-group date">
												<div class="input-group-addon">	
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control pull-right" id="sd" name="sd" value="<?= date('d F Y')?>" readonly="">
												<input type="hidden" class="form-control pull-right" id="tgl_po" name="tgl_po" value="<?= date('Y-m-d H:i:s')?>" readonly="">
											</div>
										</div>
										<div class="form-group">
											<label>Supplier<span class="text-red">*</span></label>
											<select class="form-control select4" id="supplier_code" name="supplier_code" data-placeholder='Supplier' style="width: 100%" onchange="getSupplier(this)">
												<option value="">&nbsp;</option>
												@foreach($vendor as $ven)
												<option value="{{$ven->vendor_code}}">{{$ven->vendor_code}} - {{$ven->supplier_name}}</option>
												@endforeach
											</select>

											<input type="hidden" class="form-control" id="supplier_name" name="supplier_name" readonly="">
										</div>
										<div class="form-group">
											<label>Due Payment (Vendor)<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="supplier_due_payment" name="supplier_due_payment" readonly="">
										</div>
										<div class="form-group">
											<label>Status (Vendor)<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="supplier_status" name="supplier_status" readonly="">
										</div>
										<div class="form-group">
											<label>Material<span class="text-red">*</span></label>
											<select class="form-control select4" id="material" name="material" data-placeholder='Material Status' style="width: 100%">
												<option value="">&nbsp;</option>
												<option value="None">None</option>
												<option value="Dipungut PPNBM">Dipungut PPNBM</option>
												<option value="Tidak Dipungut PPNB">Tidak Dipungut PPNB</option>
											</select>
										</div>
									</div>
									<div class="col-md-4">
										
										<div class="form-group">
											<label>Price VAT<span class="text-red">*</span></label>
											<select class="form-control select4" id="price_vat" name="price_vat" data-placeholder='Price VAT' style="width: 100%">
												<option value="">&nbsp;</option>
												<option value="Include VAT">Include VAT</option>
												<option value="Exclude VAT">Exclude VAT</option>
												<option value="None">None</option>
											</select>
										</div>
										<div class="form-group">
											<label>Transportation</label>
											<select class="form-control select4" id="transportation" name="transportation" data-placeholder='Transportation' style="width: 100%">
												<option value="">&nbsp;</option>
												@foreach($transportation as $trans)
												<option value="{{$trans}}">{{$trans}}</option>
												@endforeach
											</select>
										</div>
										<div class="form-group">
											<label>Delivery Term<span class="text-red">*</span></label>
											<select class="form-control select4" id="delivery_term" name="delivery_term" data-placeholder='Delivery Term' style="width: 100%">
												<option value="">&nbsp;</option>
												@foreach($delivery as $deliver)
												<option value="{{$deliver}}">{{$deliver}}</option>
												@endforeach
											</select>
										</div>
										<div class="form-group">
											<label>Holding Tax</label>
											<input type="text" class="form-control" id="holding_tax" name="holding_tax">
										</div>
										<div class="form-group">
											<label>Currency<span class="text-red">*</span></label>
											<select class="form-control select4" id="currency" name="currency" data-placeholder='Currency' style="width: 100%">
												<option value="">&nbsp;</option>
												<option value="USD">USD</option>
												<option value="IDR">IDR</option>
												<option value="JPY">JPY</option>
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
											<label>Authorized 2 / Manager<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="authorized2_name" name="authorized2_name" readonly="" value="{{$authorized2->name}}">
											<input type="hidden" class="form-control" id="authorized2" name="authorized2" readonly="" value="{{$authorized2->employee_id}}">
										</div>
										<div class="form-group">
											<label>Authorized 3 / General Manager<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="authorized3_name" name="authorized3_name" readonly="" value="{{$authorized3->name}}">
											<input type="hidden" class="form-control" id="authorized3" name="authorized3" readonly="" value="{{$authorized3->employee_id}}">
										</div>
										<!-- <div class="form-group">
											<label>Authorized 4<span class="text-red">*</span></label>
											<select class="form-control select4" id="authorized4" name="authorized4" data-placeholder='Pilih Authorized 4' style="width: 100%" onchange="getAuthorized4(this);">
												<option value="">&nbsp;</option>

											</select>
											<input type="hidden" class="form-control" id="authorized4_name" name="authorized4_name" readonly="">
										</div> -->
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
									<div class="col-xs-1" style="padding:5px;">
										<b>NO PR</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>No Item</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>No Budget</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Delivery Date</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Qty</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>UOM</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Goods Price</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Last Price</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Service Price</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Konversi</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>GL Number</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Aksi</b>
									</div>

									<input type="text" name="lop" id="lop" value="1" hidden>

									<div class="col-xs-1" style="padding:5px;">
										<select class="form-control select2" data-placeholder="PR" name="no_pr1" id="no_pr1" style="width: 100% height: 35px;" onchange="pilihPR(this)" required=''>
										</select>
									</div>

									<div class="col-xs-1" style="padding:5px;">
										<select class="form-control select2" data-placeholder="Item" name="no_item1" id="no_item1" style="width: 100% height: 35px;" onchange="pilihItem(this)" required=''>
										</select>

										<input type="hidden" class="form-control" id="nama_item1" name="nama_item1" placeholder="Nama Item" readonly="">
									</div>

									<div class="col-xs-1" style="padding:5px;">
										<input type="text" class="form-control" id="item_budget1" name="item_budget1" placeholder="Budget" required="" readonly="">
									</div>

									<div class="col-xs-1" style="padding:5px;">
										<input type="text" class="form-control datepicker" id="delivery_date1" name="delivery_date1" placeholder="Delivery Date" required="" readonly="">
									</div>

									<div class="col-xs-1" style="padding:5px;">
										<input type="text" class="form-control" id="qty1" name="qty1" placeholder="Qty" required="" readonly="" onkeyup="getkonversi(this)">
									</div>

									<div class="col-xs-1" style="padding:5px;">
										<select class="form-control select2" id="uom1" name="uom1" data-placeholder="UOM" style="width: 100%;">
											<option></option>
											@foreach($uom as $um)
											<option value="{{ $um }}">{{ $um }}</option>
											@endforeach
										</select>
										<!-- <input type="text" class="form-control" id="uom1" name="uom1" placeholder="UOM" required="" readonly=""> -->
									</div>

									<div class="col-xs-1" style="padding:5px;">
										<div class="input-group"> 
											<span class="input-group-addon" id="ket_harga1">?</span>
											<input type="text" class="form-control currency" id="goods_price1" name="goods_price1" placeholder="Goods Price" required="" onkeyup="getkonversi(this)" readonly="">
										</div>
									</div>

									<div class="col-xs-1" style="padding:5px;">
										<input type="text" class="form-control" id="last_price1" name="last_price1" placeholder="Last Price" readonly="">
									</div>

									<div class="col-xs-1" style="padding:5px;">
										<input type="text" class="form-control" id="service_price1" name="service_price1" placeholder="Service" required="" onkeyup="getkonversi(this)" readonly="">
									</div>

									<div class="col-xs-1" style="padding:5px;">
										<input type="text" class="form-control" id="konversi_dollar1" name="konversi_dollar1" placeholder="Konversi Dollar" required="" readonly="">
									</div>

									<div class="col-xs-1" style="padding:5px;">
										<!-- <input type="text" class="form-control" id="gl_number1" name="gl_number1" placeholder="GL Number" required=""> -->
										<select class="form-control select2" data-placeholder="GL Number" name="gl_number1" id="gl_number1" style="width: 100% height: 35px;" required=''>
											<option value=""></option>
											<option value="63300000">63300000 - Factory Constool</option>
											<option value="64500000">64500000 - Repair Maintenance (Material)</option>
											<option value="64500100">64500100 - Repair Maintenance (Jasa)</option>
											<option value="62300200">62300200 - Transport Expense</option>
											<option value="63401000">63401000 - Information System</option>
											<option value="63300400">63300400 - Office Supply</option>
											<option value="63100000">63100000 - Traveling Expense</option>
											<option value="64450000">64450000 - Rent</option>
											<option value="63704000">63704000 - Profesional Fee STD, QA, CH, dan PE</option>
											<option value="63700000">63700000 - Miscellaneous Expense</option>


											<option value="53704020">53704020 - Profesional Fee</option>
											<option value="54450000">54450000 - Rent</option>
											<option value="53500000">53500000 - Postage & Telecomm</option>
											<option value="54500000">54500000 - Repair Maintenance (Material)</option>
											<option value="54500100">54500100 - Repair Maintenance (Jasa)</option>
											<option value="53200000">53200000 - Insurance expenses</option>
											<option value="53400000">53400000 - Office supplies</option>
											<option value="53401000">53401000 - Information System</option>
											<option value="53100000">53100000 - Traveling Expenses</option>
											<option value="53601000">53601000 - Training & Education</option>
											<option value="52300110">52300110 - Transport Expenses</option>
											<option value="55100000">55100000 - Books & Period Exp</option>
											<option value="56200000">56200000 - Tax & Public Dues</option>
											<option value="53703000">53703000 - Recruiting Expenses</option>
											<option value="58500301">58500301 - Expatriate Permittance</option>
											<option value="53700000">53700000 - Miscellaneous Expense</option>
											<option value="54100100">54100100 - General Activity (Pembelian Seragam)</option>
											<option value="51400300">51400300 - Medical Allowance</option>

											<option value="15700000">15700000 - CIP</option>
											<option value="15100000">15100000 - Building</option>
											<option value="15300000">15300000 - Machinary & Equipment</option>
											<option value="15400000">15400000 - Vehicle</option>
											<option value="15500000">15500000 - Tools, Furniture & Fixture</option>
										</select>
									</div>


									<div class="col-xs-1" style="padding:5px;">
										<a type="button" class="btn btn-success" onclick='tambah("tambah","lop");'><i class='fa fa-plus' ></i></a>
									</div>
								</div>
								
								<div id="tambah"></div>

								<div class="col-md-12">
									<br>
									<!-- <a class="btn btn-success pull-right" onclick="submitForm()">Konfirmasi</a> -->
									<button type="submit" class="btn btn-success pull-right">Konfirmasi</button> 									
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

<div class="modal fade in" id="modalEdit">
	<form id ="importFormEdit" name="importFormEdit" method="post" action="{{ url('update/purchase_order') }}">
		<input type="hidden" value="{{csrf_token()}}" name="_token" />
		<div class="modal-dialog modal-lg" style="width: 1300px">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Edit Purchase Order</h4>
					<br>
					<div class="nav-tabs-custom tab-danger">
						<ul class="nav nav-tabs">
							<li class="vendor-tab active disabledTab"><a href="#tab_1_edit" data-toggle="tab" id="tab_header_1">Informasi PO</a></li>
							<li class="vendor-tab disabledTab"><a href="#tab_2_edit" data-toggle="tab" id="tab_header_2">Detail PO</a></li>
						</ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane active" id="tab_1_edit">
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-4">
										<div class="form-group">
											<label>Nomor PO<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="no_po_edit" name="no_po_edit" readonly="">
										</div>
										<div class="form-group">
											<label>Tanggal PO<span class="text-red">*</span></label>
											<div class="input-group date">
												<div class="input-group-addon">	
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control pull-right" id="tgl_po_edit" name="tgl_po_edit" readonly="">
											</div>
										</div>
										<div class="form-group">
											<label>Supplier<span class="text-red">*</span></label>
											<select class="form-control select5" id="supplier_code_edit" name="supplier_code_edit" data-placeholder='Supplier' style="width: 100%" onchange="getSupplierEdit(this)">
												<option value="">&nbsp;</option>
												@foreach($vendor as $ven)
												<option value="{{$ven->vendor_code}}">{{$ven->vendor_code}} - {{$ven->supplier_name}}</option>
												@endforeach
											</select>

											<input type="hidden" class="form-control" id="supplier_name_edit" name="supplier_name_edit" readonly="">
										</div>
										<div class="form-group">
											<label>Due Payment (Vendor)<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="supplier_due_payment_edit" name="supplier_due_payment_edit" readonly="">
										</div>
										<div class="form-group">
											<label>Status (Vendor)<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="supplier_status_edit" name="supplier_status_edit" readonly="">
										</div>
										<div class="form-group">
											<label>Material<span class="text-red">*</span></label>
											<select class="form-control select2" id="material_edit" name="material_edit" data-placeholder='Material Status' style="width: 100%">
												<option value="">&nbsp;</option>
												<option value="None">None</option>
												<option value="Dipungut PPNBM">Dipungut PPNBM</option>
												<option value="Tidak Dipungut PPNB">Tidak Dipungut PPNB</option>
											</select>
										</div>
									</div>
									<div class="col-md-4">
										
										<div class="form-group">
											<label>Price VAT<span class="text-red">*</span></label>
											<select class="form-control select5" id="price_vat_edit" name="price_vat_edit" data-placeholder='Price VAT' style="width: 100%">
												<option value="">&nbsp;</option>
												<option value="Include VAT">Include VAT</option>
												<option value="Exclude VAT">Exclude VAT</option>
												<option value="None">None</option>
											</select>
										</div>
										<div class="form-group">
											<label>Transportation</label>
											<select class="form-control select5" id="transportation_edit" name="transportation_edit" data-placeholder='Transportation' style="width: 100%">
												<option value="">&nbsp;</option>
												@foreach($transportation as $trans)
												<option value="{{$trans}}">{{$trans}}</option>
												@endforeach
											</select>
										</div>
										<div class="form-group">
											<label>Delivery Term<span class="text-red">*</span></label>
											<select class="form-control select5" id="delivery_term_edit" name="delivery_term_edit" data-placeholder='Delivery Term' style="width: 100%">
												<option value="">&nbsp;</option>
												@foreach($delivery as $deliver)
												<option value="{{$deliver}}">{{$deliver}}</option>
												@endforeach
											</select>
										</div>
										<div class="form-group">
											<label>Holding Tax</label>
											<input type="text" class="form-control" id="holding_tax_edit" name="holding_tax_edit">
										</div>
										<div class="form-group">
											<label>Currency<span class="text-red">*</span></label>
											<select class="form-control select5" id="currency_edit" name="currency_edit" data-placeholder='Currency' style="width: 100%">
												<option value="">&nbsp;</option>
												<option value="USD">USD</option>
												<option value="IDR">IDR</option>
												<option value="JPY">JPY</option>
											</select>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>Authorized 1 / Buyer<span class="text-red">*</span></label>
											<input type="hidden" class="form-control" id="buyer_id_edit" name="buyer_id_edit" readonly="">
											<input type="text" class="form-control" id="buyer_name_edit" name="buyer_name_edit" readonly="">
										</div>
										<div class="form-group">
											<label>Authorized 2 / Manager<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="authorized2_name" name="authorized2_name" readonly="" value="{{$authorized2->name}}">
											<input type="hidden" class="form-control" id="authorized2" name="authorized2" readonly="" value="{{$authorized2->employee_id}}">
										</div>
										<div class="form-group">
											<label>Authorized 3 / General Manager<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="authorized3_name" name="authorized3_name" readonly="" value="{{$authorized3->name}}">
											<input type="hidden" class="form-control" id="authorized3" name="authorized3" readonly="" value="{{$authorized3->employee_id}}">
										</div>
										<!-- <div class="form-group">
											<label>Authorized 4<span class="text-red">*</span></label>
											<select class="form-control select5" id="authorized4_edit" name="authorized4_edit" data-placeholder='Pilih Authorized 3' style="width: 100%" onchange="getAuthorized4Edit(this)">
												<option value="">&nbsp;</option>
											</select>
											<input type="hidden" class="form-control" id="authorized4_name_edit" name="authorized4_name_edit" readonly="">
										</div> -->
										<div class="form-group">
											<label>Catatan / Keterangan</label>
											<textarea class="form-control pull-right" id="note_edit" name="note_edit"></textarea>
										</div>
									</div>
								</div>
								<div class="col-md-12"  style="padding-right: 30px;padding-top: 10px">
									<a class="btn btn-primary btnNextEdit pull-right">Selanjutnya</a>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab_2_edit">
							<div class="row">
								<div class="col-md-12">
									<div class="col-xs-1" style="padding:5px;">
										<b>NO PR</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>No Item</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>No Budget</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Delivery Date</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Qty</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>UOM</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Goods Price</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Last Price</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Service Price</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Konversi</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>GL Number</b>
									</div>
									<div class="col-xs-1" style="padding:5px;">
										<b>Aksi</b>
									</div>
									<div id="modalDetailBodyEdit"></div><br>
									<div id="tambah2">
										<input type="text" name="lop2" id="lop2" value="1" hidden="">
										<input type="text" name="looping" id="looping" hidden="">
									</div>

									<div class="col-md-12">
										<br>
										<input type="hidden" id="id_edit" name="id_edit">
										<a class="btn btn-success pull-right" onclick="submitFormEdit()">Konfirmasi</a>
										<!-- <button type="submit" class="btn btn-success pull-right">Konfirmasi</button>  -->
										<span class="pull-right">&nbsp;</span>
										<a class="btn btn-primary btnPreviousEdit pull-right">Kembali</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal fade in" id="modalEditPR">
    <form id ="importFormEditPR" name="importFormEditPR" method="post" action="{{ url('update/purchase_requisition/po') }}">
      <input type="hidden" value="{{csrf_token()}}" name="_token" />
      <div class="modal-dialog modal-lg" style="width: 1300px">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">Edit Purchase Requisition</h4>
            <br>
            <h4 class="modal-title" id="modalDetailTitle"></h4>
            <div class="row">
              <div class="col-md-12">

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Identitas</label>
                    <input type="text" class="form-control" id="identitas_edit" name="identitas_edit" placeholder="Identitas">
                  </div>
                  <div class="form-group">
                    <label>No PR</label>
                    <input type="text" class="form-control" id="no_pr_edit" name="no_pr_edit" placeholder="PR Number">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Departemen</label>
                    <input type="text" class="form-control" id="departemen_edit" name="departemen_edit" placeholder="Departemen">
                  </div>
                  <div class="form-group">
                    <label>No Budget</label>
                    <input type="text" class="form-control" id="no_budget_edit" name="no_budget_edit" placeholder="No Budget">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Tanggal Pengajuan</label>
                    <input type="text" class="form-control" id="tgl_pengajuan_edit" name="tgl_pengajuan_edit" placeholder="Tanggal Pengajuan">
                  </div>
                </div>


              </div>
            </div>
            <div>
              <div class="col-md-12" style="margin-bottom : 5px">
                <div class="col-xs-1" style="padding:5px;">
                  <b>Kode Item</b>
                </div>
                <div class="col-xs-2" style="padding:5px;">
                  <b>Deskripsi</b>
                </div>
                <div class="col-xs-1" style="padding:5px;">
                  <b>Spesifikasi</b>
                </div>
                <div class="col-xs-1" style="padding:5px;">
                  <b>Stock</b>
                </div>
                <div class="col-xs-1" style="padding:5px;">
                  <b>UOM</b>
                </div>
                <div class="col-xs-1" style="padding:5px;">
                  <b>Tgl Kedatangan</b>
                </div>
                <div class="col-xs-1" style="padding:5px;">
                  <b>Mata Uang</b>
                </div>
                <div class="col-xs-1" style="padding:5px;">
                  <b>Harga</b>
                </div>
                <div class="col-xs-1" style="padding:5px;">
                  <b>Jumlah</b>
                </div>
                <div class="col-xs-1" style="padding:5px;">
                  <b>Total</b>
                </div>
                <div class="col-xs-1" style="padding:5px;">
                  <b>Aksi</b>
                </div>
              </div>

              <div  id="modalDetailBodyEditPR">
              </div>
            </div>
            <br>

            <div id="tambah3">
              <input type="text" name="lop3" id="lop3" value="1" hidden="">
              <input type="text" name="looping_pr" id="looping_pr" hidden="">
            </div>

            <div class="col-md-11" style="margin-top: 20px">
	          	<p><b>Informasi Budget</b></p>
	          	<table class="table table-striped text-center">
	          		<tr>
	          			<th>Bulan</th>
	          			<th>Budget Bulanan</th>
	          			<th>Total Pembelian</th>
	          			<th>Sisa Budget</th>
	          		</tr>
	          		<tr>
	          			<td>
	          				<label id="bulanbudgetedit" name="bulanbudgetedit"></label>
	          			</td>
	          			<td>
	          				<label id="budgetLabelEdit" name="budgetLabelEdit"></label>
	          			</td>
	          			<td>
	          				<label id="TotalPembelianEditLabel" name="TotalPembelianEditLabel"></label>
	          				<input type="hidden" id="TotalPembelianEdit" name="TotalPembelianEdit">
	          			</td>
	          			<td>
	          				<label id="SisaBudgetLabelEdit" name="SisaBudgetLabelEdit"></label>
	          				<input type="hidden" id="SisaBudgetEdit" name="SisaBudgetEdit">
	          			</td>
	          		</tr>
	          	</table>
	        </div>
	    </div>
      	<div class="modal-footer">
            <input type="hidden" class="form-control" id="id_edit_pr" name="id_edit_pr" placeholder="ID">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-warning">Update</button>
      	</div>
    </div>
    </div>
 	</form>
	</div>

  	<div class="modal fade in" id="modalDetailPR">
      	<input type="hidden" value="{{csrf_token()}}" name="_token" />
      	<div class="modal-dialog modal-lg" style="width: 1300px">
	        <div class="modal-content">
	          <div class="modal-header">
	            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	              <span aria-hidden="true">&times;</span>
	            </button>
	            <h4 class="modal-title">Detail Item Purchase Requisition</h4>
	            <br>
	            <h4 class="modal-title" id="modalDetailTitle"></h4>

	            <div >
	              <div class="col-md-12" style="margin-bottom : 5px">
	              	<table class="table table-hover table-bordered table-striped" id="tableDetail">
		              	<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">No</th>
								<th style="width: 2%;">Kode Item</th>
								<th style="width: 5%;">Deskripsi</th>
								<th style="width: 4%;">Spesifikasi</th>
								<th style="width: 2%;">Stock</th>
								<th style="width: 3%;">Tgl Kedatangan</th>
								<th style="width: 3%;">Mata Uang</th>
								<th style="width: 4%;">Harga</th>
								<th style="width: 2%;">Jumlah</th>
								<th style="width: 4%;">Total</th>
								<th style="width: 4%;">Action</th>
							</tr>
						</thead>

		              	<tbody id="tableDetailBody">
						
						</tbody>
					</table>

	              </div>

	            </div>
	            <br>

	          </div>
	          <div class="modal-footer">
	            <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
	          </div>
	        </div>
      </div>
  </div>

<div class="modal fade" id="modalSAP" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog modal-sm">
    	<div class="modal-content">
      		<div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        	<h4 class="modal-title" id="myModalLabel">Edit Nomor SAP</h4>
      		</div>
	      	<div class="modal-body">
		        <div class="box-body">
		          <input type="hidden" value="{{csrf_token()}}" name="_token" />
		          <div class="row">
			          <div class="col-xs-12">
			            <label for="po_sap">No PO SAP<span class="text-red">*</span></label>
			            <input type="text" class="form-control" name="no_po_sap" id="no_po_sap">
			           </div>
		          	</div>
		        </div>
	     	</div>
		    <div class="modal-footer">
		      <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
		      <input type="hidden" id="id_edit_sap">
		      <button type="button" onclick="edit_sap()" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-pencil"></i> Edit</button>
		    </div>
	  	</div>
	</div>
</div>


<div class="modal modal-danger fade in" id="modaldanger">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">Hapus Item</h4>
			</div>
			<div class="modal-body" id="modalDeleteBody">
				<p></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
				<a id="a" name="modalDeleteButton" type="button" onclick="delete_item(this.id)" class="btn btn-danger">Delete</a>
			</div>
		</div>
	</div>
</div>

<div class="modal modal-danger fade in" id="modaldangerPR">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
					<h4 class="modal-title">Hapus Item PR</h4>
				</div>
				<div class="modal-body" id="modalDeleteBodyPR">
					<p></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
					<a id="a" name="modalDeleteButtonPR" type="button" onclick="delete_item_pr(this.id)" class="btn btn-danger">Delete</a>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-danger fade" id="modalcancelPO" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4 class="modal-title" id="myModalLabel">Konfirmasi Hapus Data</h4>
	      </div>
	      <div class="modal-body">
	        Apakah anda yakin ingin cancel PO Ini ?
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
	        <a id="a" name="modalbuttoncancel" type="button"  onclick="cancel_po(this.id)" class="btn btn-danger">Yes</a>
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
{{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>

	no = 2;
	pr_list = "";
	exchange_rate = [];
	item_list = "";
    limitdate = "";

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fillTableOutstanding();
		fillTable();
		getPRList();
		getExchangeRate();
		getItemList();

	    limitdate = new Date();
	    limitdate.setDate(limitdate.getDate());

		$('body').toggleClass("sidebar-collapse");
		$('#datefrom').datepicker({
			autoclose: true,
			todayHighlight: true
		});
		$('#dateto').datepicker({
			autoclose: true,
			todayHighlight: true
		});

		$('.datepicker').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd'
		});

		$('.btnNext').click(function(){
			var no_po2 = $('#no_po2').val();
			var supplier = $('#supplier_code').val();
			var material = $('#material').val();
			var price_vat = $('#price_vat').val();
			var delivery_term = $('#delivery_term').val();
			var currency = $('#currency').val();
			// var authorized4 = $('#authorized4').val();

			if(no_po2 == '' || supplier == "" || material == "" || price_vat == "" || delivery_term == "" || currency == ""){
				alert('All field must be filled');	
			}	
			else{
				$('.nav-tabs > .active').next('li').find('a').trigger('click');
			}
		});

		$('.btnNextEdit').click(function(){
			var supplier = $('#supplier_code_edit').val();
			var material = $('#material_edit').val();
			var price_vat = $('#price_vat_edit').val();
			var delivery_term = $('#delivery_term_edit').val();
			var currency = $('#currency_edit').val();
			// var authorized4 = $('#authorized4_edit').val();

			if( supplier == "" || material == "" || price_vat == "" || delivery_term == "" || currency == ""){
				alert('All field must be filled');	
			}	
			else{
				$('.nav-tabs > .active').next('li').find('a').trigger('click');
			}
		});

		$('.btnPrevious').click(function(){
			$('.nav-tabs > .active').prev('li').find('a').trigger('click');
		});

		$('.btnPreviousEdit').click(function(){
			$('.nav-tabs > .active').prev('li').find('a').trigger('click');
		});

		$("#importForm").submit(function(){
				if (!confirm("Apakah Anda Yakin Ingin Membuat PO Ini??")) {
					return false;
				} else {
					this.submit();
				}
			});


	});

	// Submit Form

	function submitForm() {
		var conf = confirm("Apakah Anda yakin ingin membuat PO Ini?");
		if (conf == true) {
			$('[name=importForm]').submit();
		} else {

		}
	}

	function submitFormEdit() {
		var conf = confirm("Apakah Anda yakin ingin mengubah PO Ini?");
		if (conf == true) {
			$('[name=importFormEdit]').submit();
		} else {

		}
	}

	function clearConfirmation(){
		location.reload(true);		
	}

	function fillTableOutstanding(){
		$('#outstandingTable').DataTable().clear();
		$('#outstandingTable').DataTable().destroy();

		var table = $('#outstandingTable').DataTable({
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
				"url" : "{{ url("fetch/purchase_order_pr") }}",
			},
			"columns": [
			{ "data": "no_pr" },
			{ "data": "department" },
			{ "data": "submission_date" },
			{ "data": "emp_name" },
			{ "data": "no_budget" },
			{ "data": "file" },
			{ "data": "action" },

			],
		});

		$('#outstandingTable tfoot th').each( function () {
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
		$('#outstandingTable tfoot tr').appendTo('#outstandingTable thead');
	}

	function fillTable(){
		$('#poTable').DataTable().clear();
		$('#poTable').DataTable().destroy();

		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var department = $('#department').val();
		
		var data = {
			datefrom:datefrom,
			dateto:dateto,
			department:department,
		}

		var table = $('#poTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 20, 25, 50, -1 ],
			[ '20 rows', '25 rows', '50 rows', 'Show all' ]
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
			{ "data": "buyer_name" },
			{ "data": "tgl_po" },
			{ "data": "supplier_name" },
			{ "data": "no_po_sap" },
			{ "data": "status" },
			{ "data": "action" },

			],
		});

		$('#poTable tfoot th').each( function () {
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
		$('#poTable tfoot tr').appendTo('#poTable thead');
	}

	$('.select2').select2({
		dropdownAutoWidth : true,
		allowClear: true
	});

	$(function () {
		$('.select4').select2({
			dropdownParent: $("#tab_1"),
			allowClear:true,
			dropdownAutoWidth : true
		});

		$('.select5').select2({
			dropdownParent: $("#tab_1_edit"),
			allowClear:true,
			dropdownAutoWidth : true
		});
	})

	function openModalCreate(){
		$('#modalCreate').modal('show');

		//nomor PO Auto
		var nomorpo1 = document.getElementById("no_po1");

		$.ajax({
			url: "{{ url('purchase_order/get_nomor_po') }}", 
			type : 'GET', 
			success : function(data){
				var obj = jQuery.parseJSON(data);
				var tahun = obj.tahun;
				var bulan = obj.bulan;

				nomorpo1.value = "EQ"+tahun+bulan;
			}
		});

	}

	function getExchangeRate(){
		$.ajax({
			url: "{{ url('purchase_requisition/get_exchange_rate') }}", 
			type : 'GET', 
			success : function(data){
				var obj = jQuery.parseJSON(data);
				for (var i = 0; i < obj.length; i++) {
            		var currency = obj[i].currency; // currency
	            	var rate = obj[i].rate; //nilai tukar

	            	exchange_rate.push({
	            		'currency' :  obj[i].currency, 
	            		'rate' :  obj[i].rate,
	            	});
	            }
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
				$('#supplier_due_payment').val(obj.duration);
				$('#supplier_status').val(obj.status);
			} 
		});
	}

	function getSupplierEdit(elem){

		$.ajax({
			url: "{{ route('admin.pogetsupplier') }}?supplier_code="+elem.value,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$('#supplier_name_edit').val(obj.name);
				$('#supplier_due_payment_edit').val(obj.duration);
				$('#supplier_status_edit').val(obj.status);
			} 
		});
	}

	// function getAuthorized4(elem){

	// 	$.ajax({
	// 		url: "{{ route('admin.pogetname') }}?authorized4="+elem.value,
	// 		method: 'GET',
	// 		success: function(data) {
	// 			var json = data,
	// 			obj = JSON.parse(json);
	// 			$('#authorized4_name').val(obj.name);
	// 		} 
	// 	});
	// }

	// function getAuthorized4Edit(elem){

	// 	$.ajax({
	// 		url: "{{ route('admin.pogetname') }}?authorized4="+elem.value,
	// 		method: 'GET',
	// 		success: function(data) {
	// 			var json = data,
	// 			obj = JSON.parse(json);
	// 			$('#authorized4_name_edit').val(obj.name);
	// 		} 
	// 	});
	// }

	function getPRList() {
		$.get('{{ url("fetch/purchase_order/prlist") }}', function(result, status, xhr) {
			pr_list += "<option></option> ";
			$.each(result.pr, function(index, value){
				pr_list += "<option value="+value.no_pr+">"+value.no_pr+"</option> ";
			});
			$('#no_pr1').append(pr_list);
		})
	}

	function pilihPR(elem)
	{
		var no = elem.id.match(/\d/g);
		no = no.join("");

		$.ajax({
			url: "{{ url('fetch/purchase_order/pilih_pr') }}?no_pr="+elem.value,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$("#no_item"+no).html(obj);
				$('#qty'+no).attr('readonly', true).val("");
				$('#uom'+no).attr('readonly', true).val("");
				$('#item_budget'+no).attr('readonly', true).val("");
				$('#delivery_date'+no).attr('readonly', true).val("");
				$('#goods_price'+no).attr('readonly', true).val("");
			} 
		});
	}

	function pilihItem(elem)
	{
		var no = elem.id.match(/\d/g);
		no = no.join("");

		var no_pr = $("#no_pr"+no).val();

		$.ajax({
			url: "{{ url('purchase_order/get_item') }}?item_code="+elem.value+"&no_pr="+no_pr,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$('#qty'+no).attr('readonly', true).val(obj.item_qty);
				$('#nama_item'+no).attr('readonly', false).val(obj.item_desc);
				$('#uom'+no).val(obj.item_uom).change();
				$('#item_budget'+no).attr('readonly', true).val(obj.no_budget);
				$('#delivery_date'+no).attr('readonly', false).val(obj.item_request_date);
				if (obj.item_currency == "USD") {
					$('#ket_harga'+no).text("$");
				}else if (obj.item_currency == "JPY") {
					$('#ket_harga'+no).text("¥");
				}else if (obj.item_currency == "IDR"){
					$('#ket_harga'+no).text("Rp.");
				}
				$('#last_price'+no).attr('readonly', true).val(obj.last_price);
				$('#goods_price'+no).attr('readonly', false).val(obj.item_price);
				$('#service_price'+no).attr('readonly', false).val(0);

				var total = obj.item_qty * obj.item_price;
				var conf = konversi(obj.item_currency,"USD",total);
				$('#konversi_dollar'+no).attr('readonly', false).val(conf);

			}
		});
	}

	function getkonversi(elem)
	{
		var num = elem.id.match(/\d/g);
		num = num.join("");
		var currency = $('#currency').val();

		$('#ket_harga'+num).text(currency);

		var harga_goods = document.getElementById("goods_price"+num).value;
		var harga_service = document.getElementById("service_price"+num).value;

		var qty = document.getElementById("qty"+num).value;
		if (harga_goods != 0) {
			var hasil = parseInt(qty) * parseFloat(harga_goods);			
		}
		else if (harga_service != 0){
			var hasil = parseInt(qty) * parseFloat(harga_service);
		}

	    // var prc = price.replace(/\D/g, ""); //get angka saja

	    var harga_konversi = parseFloat(konversi(currency,"USD", hasil));
	    $('#konversi_dollar'+num).val(harga_konversi);
	}

	function getkonversiEdit(elem)
	{
		var num = elem.id.match(/\d/g);
		num = num.join("");
		var currency = $('#currency_edit').val();
		$('#ket_harga_edit'+num).text(currency);

		var harga_goods = document.getElementById("goods_price"+num).value;
		var harga_service = document.getElementById("service_price"+num).value;
		var qty = document.getElementById("qty"+num).value;
		
		if (harga_goods != 0) {
			var hasil = parseInt(qty) * parseFloat(harga_goods);			
		}
		else if (harga_service != 0){
			var hasil = parseInt(qty) * parseFloat(harga_service);
		}

	    var harga_konversi = parseFloat(konversi(currency,"USD", hasil));
	    $('#konversi_dollar'+num).val(harga_konversi);
	}

	//Fungsi Tambah PO

	function tambah(id,lop) {
		var id = id;

		var lop = "";

		if (id == "tambah"){
			lop = "lop";
		}else{
			lop = "lop2";
		}

		var divdata = $("<div id='"+no+"' class='col-md-12' style='margin-bottom : 5px'><div class='col-xs-1' style='padding:5px;'><select class='form-control select3' data-placeholder='PR' name='no_pr"+no+"' id='no_pr"+no+"' style='width: 100% height: 35px;' onchange='pilihPR(this)' required=''></select></div><div class='col-xs-1' style='padding:5px;'><select class='form-control select3' data-placeholder='Item' name='no_item"+no+"' id='no_item"+no+"' style='width: 100% height: 35px;' onchange='pilihItem(this)' required=''></select><input type='hidden' class='form-control' id='nama_item"+no+"' name='nama_item"+no+"' placeholder='Nama Item' readonly=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_budget"+no+"' name='item_budget"+no+"' placeholder='Budget' required='' readonly=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control datepicker' id='delivery_date"+no+"' name='delivery_date"+no+"' placeholder='Delivery Date' required='' readonly=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='qty"+no+"' name='qty"+no+"' placeholder='Qty' required='' readonly='' onkeyup='getkonversi(this)'></div> <div class='col-xs-1' style='padding:5px;'><select class='form-control select3' id='uom"+no+"' name='uom"+no+"' data-placeholder='UOM' style='width: 100%;'><option></option>@foreach($uom as $um)<option value='{{ $um }}'>{{ $um }}</option>@endforeach</select></div><div class='col-xs-1' style='padding:5px;'><div class='input-group'><span class='input-group-addon' id='ket_harga"+no+"'>?</span><input type='text' class='form-control currency' id='goods_price"+no+"' name='goods_price"+no+"' placeholder='Goods Price' required='' readonly='' onkeyup='getkonversi(this)'></div></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='last_price"+no+"' name='last_price"+no+"' placeholder='Last Price' readonly=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='service_price"+no+"' name='service_price"+no+"' placeholder='Service' onkeyup='getkonversi(this)' required='' readonly=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='konversi_dollar"+no+"' name='konversi_dollar"+no+"' placeholder='Konversi Dollar' required='' readonly=''></div><div class='col-xs-1' style='padding:5px;'><select class='form-control select3' data-placeholder='GL Number' name='gl_number"+no+"' id='gl_number"+no+"' style='width: 100% height: 35px;' required=''><option value=''></option><option value='63300000'>63300000 - Factory Constool</option><option value='64500000'>64500000 - Repair Maintenance (Material)</option><option value='64500100'>64500100 - Repair Maintenance (Jasa)</option><option value='62300200'>62300200 - Transport Expense</option><option value='63401000'>63401000 - Information System</option><option value='63300400'>63300400 - Office Supply</option><option value='63100000'>63100000 - Traveling Expense</option><option value='64450000'>64450000 - Rent</option><option value='63704000'>63704000 - Profesional Fee STD, QA, CH, dan PE</option><option value='63700000'>63700000 - Miscellaneous Expense</option><option value='53704020'>53704020 - Profesional Fee</option><option value='54450000'>54450000 - Rent</option><option value='53500000'>53500000 - Postage & Telecomm</option><option value='54500000'>54500000 - Repair Maintenance (Material)</option><option value='54500100'>54500100 - Repair Maintenance (Jasa)</option><option value='53200000'>53200000 - Insurance expenses</option><option value='53400000'>53400000 - Office supplies</option><option value='53401000'>53401000 - Information System</option><option value='53100000'>53100000 - Traveling Expenses</option><option value='53601000'>53601000 - Training & Education</option><option value='52300110'>52300110 - Transport Expenses</option><option value='55100000'>55100000 - Books & Period Exp</option><option value='56200000'>56200000 - Tax & Public Dues</option><option value='53703000'>53703000 - Recruiting Expenses</option><option value='58500301'>58500301 - Expatriate Permittance</option><option value='53700000'>53700000 - Miscellaneous Expense</option><option value='54100100'>54100100 - General Activity (Pembelian Seragam)</option><option value='51400300'>51400300 - Medical Allowance</option><option value='15700000'>15700000 - CIP</option><option value='15100000'>15100000 - Building</option><option value='15300000'>15300000 - Machinary & Equipment</option><option value='15400000'>15400000 - Vehicle</option><option value='15500000'>15500000 - Tools, Furniture & Fixture</option></select></div><div class='col-xs-1' style='padding:5px;'><button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button> <button type='button' onclick='tambah(\""+id+"\",\""+lop+"\"); ' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div>");

		$("#"+id).append(divdata);
		$("#no_pr"+no).append(pr_list);

		$(function () {
			$('.select3').select2({
				dropdownAutoWidth : true,
				dropdownParent: $("#"+id),
				allowClear: true
			});
		})

		$('.datepicker').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight : true
		});

		document.getElementById(lop).value = no;
		no+=1;
	}

	//Fungsi Kurang

	function kurang(elem,lop) {

		var lop = lop;
		var ids = $(elem).parent('div').parent('div').attr('id');
		var oldid = ids;
		$(elem).parent('div').parent('div').remove();
		var newid = parseInt(ids) + 1;

		$("#"+newid).attr("id",oldid);
		$("#no_pr"+newid).attr("name","no_pr"+oldid);
		$("#no_item"+newid).attr("name","no_item"+oldid);
		$("#item_budget"+newid).attr("name","item_budget"+oldid);
		$("#delivery_date"+newid).attr("name","delivery_date"+oldid);
		$("#qty"+newid).attr("name","qty"+oldid);
		$("#uom"+newid).attr("name","uom"+oldid);
		$("#price"+newid).attr("name","price"+oldid);
		$("#currency"+newid).attr("name","currency"+oldid);
		$("#konversi_dollar"+newid).attr("name","konversi_dollar"+oldid);
		$("#gl_number"+newid).attr("name","gl_number"+oldid);

		$("#no_pr"+newid).attr("id","no_pr"+oldid);
		$("#no_item"+newid).attr("id","no_item"+oldid);
		$("#item_budget"+newid).attr("id","item_budget"+oldid);
		$("#delivery_date"+newid).attr("id","delivery_date"+oldid);
		$("#qty"+newid).attr("id","qty"+oldid);
		$("#uom"+newid).attr("id","uom"+oldid);
		$("#price"+newid).attr("id","price"+oldid);
		$("#currency"+newid).attr("id","currency"+oldid);
		$("#konversi_dollar"+newid).attr("id","konversi_dollar"+oldid);
		$("#gl_number"+newid).attr("id","gl_number"+oldid);

		no-=1;
		var a = no -1;

		for (var i =  ids; i <= a; i++) {	
			var newid = parseInt(i) + 1;
			var oldid = newid - 1;
			$("#"+newid).attr("id",oldid);
			$("#no_pr"+newid).attr("name","no_pr"+oldid);
			$("#no_item"+newid).attr("name","no_item"+oldid);
			$("#item_budget"+newid).attr("name","item_budget"+oldid);
			$("#delivery_date"+newid).attr("name","delivery_date"+oldid);
			$("#qty"+newid).attr("name","qty"+oldid);
			$("#uom"+newid).attr("name","uom"+oldid);
			$("#price"+newid).attr("name","price"+oldid);
			$("#currency"+newid).attr("name","currency"+oldid);
			$("#konversi_dollar"+newid).attr("name","konversi_dollar"+oldid);
			$("#gl_number"+newid).attr("name","gl_number"+oldid);

			$("#no_pr"+newid).attr("id","no_pr"+oldid);
			$("#no_item"+newid).attr("id","no_item"+oldid);
			$("#item_budget"+newid).attr("id","item_budget"+oldid);
			$("#delivery_date"+newid).attr("id","delivery_date"+oldid);
			$("#qty"+newid).attr("id","qty"+oldid);
			$("#uom"+newid).attr("id","uom"+oldid);
			$("#price"+newid).attr("id","price"+oldid);
			$("#currency"+newid).attr("id","currency"+oldid);
			$("#konversi_dollar"+newid).attr("id","konversi_dollar"+oldid);
			$("#gl_number"+newid).attr("id","gl_number"+oldid);

		}
		document.getElementById(lop).value = a;
	}

	function konversi(from, to, amount){
		var obj = exchange_rate;

        // console.log(obj);
		for (var i = 0; i < obj.length; i++) {
    		var currency = obj[i].currency; // currency
        	var rate = obj[i].rate; //nilai tukar


        	if (from == currency) {
        		fromrate = rate;
        	}

        	if (to == currency) {
        		torate = rate;
        	}
        }
        hasil_konversi = (amount / fromrate) * torate;
        return hasil_konversi.toFixed(2);		    
    }

    function editPO(id){

    	var isi = "";
    	$('#modalEdit').modal("show");

    	var data = {
    		id:id
    	};

    	$.get('{{ url("edit/purchase_order") }}', data, function(result, status, xhr){

    		$("#id_edit").val(id);
    		$("#no_po_edit").val(result.purchase_order.no_po);
    		$("#tgl_po_edit").val(result.purchase_order.tgl_po);
    		$("#supplier_code_edit").val(result.purchase_order.supplier_code).trigger('change.select2');
    		$("#material_edit").val(result.purchase_order.material).trigger('change.select2');
    		$("#price_vat_edit").val(result.purchase_order.vat).trigger('change.select2');
    		$("#transportation_edit").val(result.purchase_order.transportation).trigger('change.select2');
    		$("#delivery_term_edit").val(result.purchase_order.delivery_term).trigger('change.select2');
    		$("#holding_tax_edit").val(result.purchase_order.holding_tax);
    		$("#currency_edit").val(result.purchase_order.currency).trigger('change.select2');
    		$("#buyer_id_edit").val(result.purchase_order.buyer_id);
    		$("#buyer_name_edit").val(result.purchase_order.buyer_name);
    		// $("#authorized4_edit").val(result.purchase_order.authorized4).trigger('change.select2');
    		$("#note_edit").val(result.purchase_order.note).trigger('change.select2');


    		$('#modalDetailBodyEdit').html("");

	        // var no = 1;

		    var ids = [];


	        $.each(result.purchase_order_detail, function(key, value) {
		    	var tambah2 = "tambah2";
		    	var	lop2 = "lop2";

		    	isi = "<div id='"+value.id+"' class='col-md-12' style='margin-bottom : 5px'>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' name='no_pr"+value.id+"' id='no_pr"+value.id+"' value='"+ value.no_pr +"' readonly=''></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' name='no_item"+value.id+"' id='no_item"+value.id+"' value='"+ value.no_item +"' readonly=''><input type='hidden' class='form-control' id='nama_item"+value.id+"' name='nama_item"+value.id+"' placeholder='Nama Item' readonly='' value='"+ value.nama_item +"'></div> ";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_budget"+value.id+"' name='item_budget"+value.id+"' placeholder='Budget' required='' value="+value.budget_item+" readonly=''></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control datepicker' id='delivery_date"+value.id+"' name='delivery_date"+value.id+"' placeholder='Delivery Date' required='' value="+value.delivery_date+"></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='qty"+value.id+"' name='qty"+value.id+"' placeholder='Qty' required='' onkeyup='getkonversiEdit(this)' value="+value.qty+"></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='hidden' name='uomhide"+value.id+"' id='uomhide"+value.id+"' value='"+ value.uom +"'><select class='form-control select6' id='uom_edit"+value.id+"' name='uom_edit"+value.id+"' data-placeholder='UOM' style='width: 100%;'><option></option>@foreach($uom as $um)<option value='{{ $um }}'>{{ $um }}</option>@endforeach</select></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><div class='input-group'><span class='input-group-addon' id='ket_harga_edit"+value.id+"'>?</span><input type='text' class='form-control currency' id='goods_price"+value.id+"' name='goods_price"+value.id+"' placeholder='Goods Price' required='' onkeyup='getkonversiEdit(this)' value="+value.goods_price+"></div></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='last_price"+value.id+"' name='last_price"+value.id+"' placeholder='Last Price' value="+value.last_price+" readonly=''></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='service_price"+value.id+"' name='service_price"+value.id+"' placeholder='Service' required='' onkeyup='getkonversiEdit(this)' value="+value.service_price+"></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='konversi_dollar"+value.id+"' name='konversi_dollar"+value.id+"' placeholder='Konversi Dollar' required='' value="+value.konversi_dollar+"></div>";

		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='hidden' name='glhide"+value.id+"' id='glhide"+value.id+"' value='"+ value.gl_number +"'><select class='form-control select6' id='gl_edit"+value.id+"' name='gl_edit"+value.id+"' data-placeholder='GL Number' style='width: 100%;' required=''><option></option><option value='63300000'>63300000 - Factory Constool</option><option value='64500000'>64500000 - Repair Maintenance (Material)</option><option value='64500100'>64500100 - Repair Maintenance (Jasa)</option><option value='62300200'>62300200 - Transport Expense</option><option value='63401000'>63401000 - Information System</option><option value='63300400'>63300400 - Office Supply</option><option value='63100000'>63100000 - Traveling Expense</option><option value='64450000'>64450000 - Rent</option><option value='63704000'>63704000 - Profesional Fee STD, QA, CH, dan PE</option><option value='63700000'>63700000 - Miscellaneous Expense</option><option value='53704020'>53704020 - Profesional Fee</option><option value='54450000'>54450000 - Rent</option><option value='53500000'>53500000 - Postage & Telecomm</option><option value='54500000'>54500000 - Repair Maintenance (Material)</option><option value='54500100'>54500100 - Repair Maintenance (Jasa)</option><option value='53200000'>53200000 - Insurance expenses</option><option value='53400000'>53400000 - Office supplies</option><option value='53401000'>53401000 - Information System</option><option value='53100000'>53100000 - Traveling Expenses</option><option value='53601000'>53601000 - Training & Education</option><option value='52300110'>52300110 - Transport Expenses</option><option value='55100000'>55100000 - Books & Period Exp</option><option value='56200000'>56200000 - Tax & Public Dues</option><option value='53703000'>53703000 - Recruiting Expenses</option><option value='58500301'>58500301 - Expatriate Permittance</option><option value='53700000'>53700000 - Miscellaneous Expense</option><option value='54100100'>54100100 - General Activity (Pembelian Seragam)</option><option value='51400300'>51400300 - Medical Allowance</option><option value='15700000'>15700000 - CIP</option><option value='15100000'>15100000 - Building</option><option value='15300000'>15300000 - Machinary & Equipment</option><option value='15400000'>15400000 - Vehicle</option><option value='15500000'>15500000 - Tools, Furniture & Fixture</option></select></div>";

		    	// isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='gl_number"+value.id+"' name='gl_number"+value.id+"' placeholder='GL Number' required='' value="+value.gl_number+"></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><a href='javascript:void(0);' id='b"+ value.id +"' onclick='deleteConfirmation(\""+ value.nama_item +"\","+value.id +");' class='btn btn-danger' data-toggle='modal' data-target='#modaldanger'><i class='fa fa-close'></i> </a> <button type='button' class='btn btn-success' onclick='tambah(\""+ tambah2 +"\",\""+ lop2 +"\");'><i class='fa fa-plus' ></i></button> </div> "
		    	isi += "</div>";

		    	// 

		    	ids.push(value.id);


		    	$('#modalDetailBodyEdit').append(isi);
		    	$("#no_pr"+value.id).append(pr_list);

		    	if (value.currency == "USD") {
		    		$('#ket_harga_edit'+value.id).text("$");
		    	}else if (value.currency == "JPY") {
		    		$('#ket_harga_edit'+value.id).text("¥");
		    	}else if (value.currency == "IDR"){
		    		$('#ket_harga_edit'+value.id).text("Rp.");
		    	}


		    	$('.datepicker').datepicker({
					autoclose: true,
					format: 'yyyy-mm-dd'
				});


		    	var uom = $('#uomhide'+value.id).val();
		    	$("#uom_edit"+value.id).val(uom).trigger("change");

		    	var gl = $('#glhide'+value.id).val();
		    	$("#gl_edit"+value.id).val(gl).trigger("change");


		    	$(function () {
		    		$('.select6').select2({
		    			dropdownAutoWidth : true,
		    			dropdownParent: $("#"+value.id),
		    			allowClear: true
		    		});
		    	})

		    	$("#looping").val(ids);
				// no += 1;
			});
		});

	}

	function editSAP(id,nomor){
    	$('#modalSAP').modal("show");
    	$("#id_edit_sap").val(id);
    	$("#no_po_sap").val(nomor);
    }

    function edit_sap() {

      var data = {
        id: $("#id_edit_sap").val(),
        no_po_sap : $("#no_po_sap").val()
      };

      $.post('{{ url("purchase_order/edit_sap") }}', data, function(result, status, xhr){
        if (result.status == true) {
          $('#poTable').DataTable().ajax.reload(null, false);
          openSuccessGritter("Success","Nomor PO has been edited.");
        } else {
          openErrorGritter("Error","Failed to edit.");
        }
      })
    }

	function deleteConfirmation(name, id) {
		$('#modalDeleteBody').text("Are you sure want to delete ' " + name + " '");
		$('[name=modalDeleteButton]').attr("id",id);
	}

	function deleteConfirmationPR(name, id) {
		$('#modalDeleteBodyPR').text("Are you sure want to delete ' " + name + " '");
		$('[name=modalDeleteButtonPR]').attr("id",id);
	}
	function cancelPO(id) {
		$('[name=modalbuttoncancel]').attr("id",id);
	}

	function delete_item(id) {
		var data = {
			id:id,
		}

		$.post('{{ url("delete/purchase_order_item") }}', data, function(result, status, xhr){

		});

		$('#modaldanger').modal('hide');
		$('#'+id).css("display","none");
	}

	function delete_item_pr(id) {
		var data = {
			id:id,
		}

		$.post('{{ url("delete/purchase_requisition_item") }}', data, function(result, status, xhr){

		});

		$('#modaldangerPR').modal('hide');
		$('#'+id).css("display","none");
	}

	function cancel_po(id){

		var data = {
			id:id,
		}

		$("#loading").show();

		$.post('{{ url("cancel/purchase_order") }}', data, function(result, status, xhr){
			if (result.status == true) {
	        	openSuccessGritter("Success","Data Berhasil Diupdate");
	        	$("#loading").hide();
	        	setTimeout(function(){  window.location.reload() }, 2500);
			}
			else{
				openErrorGritter("Success","Data Gagal Diupdate");
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
          time: '2000'
        });
    }

	function sendEmail(id) {

      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim PO ini ke Manager?")) {
        return false;
      }
      else{
      	$("#loading").show();
      }

      $.get('{{ url("purchase_order/sendemail") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Berhasil Terkirim");
      	$("#loading").hide();
        setTimeout(function(){  window.location.reload() }, 2500);
      })
    }

    
    //EditPR

    function tambahPR(id,lop) {
      var id = id;

      var lop = "";

      if (id == "tambah"){
        lop = "lop";
      }else{
        lop = "lop3";
      }

      var divdata = $("<div id='"+no+"' class='col-md-12' style='margin-bottom : 5px'><div class='col-xs-1' style='padding:5px;'><select class='form-control select3' data-placeholder='Choose Item' name='item_code"+no+"' id='item_code"+no+"' onchange='pilihItemPR(this)'><option></option></select></div><div class='col-xs-2' style='padding:5px;'><input type='text' class='form-control' id='item_desc"+no+"' name='item_desc"+no+"' placeholder='Description' required=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_spec"+no+"' name='item_spec"+no+"' placeholder='Specification' required=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_stock"+no+"' name='item_stock"+no+"' placeholder='Stock'></div><div class='col-xs-1' style='padding:5px;'><select class='form-control select3' id='uom"+no+"' name='uom"+no+"' data-placeholder='UOM' style='width: 100%;'><option></option>@foreach($uom as $um)<option value='{{ $um }}'>{{ $um }}</option>@endforeach</select></div><div class='col-xs-1' style='padding:5px;'><div class='input-group date'><div class='input-group-addon'><i class='fa fa-calendar' style='font-size: 10px'></i> </div><input type='text' class='form-control pull-right datepicker' id='req_date"+no+"' name='req_date"+no+"' placeholder='Tanggal' required=''></div></div> <div class='col-xs-1' style='padding: 5px'><select class='form-control select2' id='item_currency"+no+"' name='item_currency"+no+"'data-placeholder='Currency' style='width: 100%' onchange='currency(this)'><option value=''>&nbsp;</option><option value='USD'>USD</option><option value='IDR'>IDR</option><option value='JPY'>JPY</option></select><input type='text' class='form-control' id='item_currency_text"+no+"' name='item_currency_text"+no+"' style='display:none'></div> <div class='col-xs-1' style='padding:5px;'><div class='input-group'><span class='input-group-addon' id='ket_harga"+no+"' style='padding:3px'>?</span><input type='text' class='form-control currency' id='item_price"+no+"' name='item_price"+no+"' placeholder='Harga' data-number-to-fixed='2' data-number-stepfactor='100' required='' style='padding:6px 6px'></div></div><div class='col-xs-1' style='padding:5px;'><input type='number' class='form-control' id='qty"+no+"' name='qty"+no+"' placeholder='Qty' onkeyup='getTotal(this.id)' required=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='amount"+no+"' name='amount"+no+"' placeholder='Total' required='' readonly><input type='hidden' class='form-control' id='konversi_dollar"+no+"' name='konversi_dollar"+no+"' placeholder='Total' required='' readonly=''></div><div class='col-xs-1' style='padding:5px;'>&nbsp;<button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button> <button type='button' onclick='tambahPR(\""+id+"\",\""+lop+"\"); ' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div>");

      $("#"+id).append(divdata);
      $("#item_code"+no).append(item_list);


      $('.datepicker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        startDate: limitdate
      });

      $(function () {
        $('.select3').select2({
          dropdownAutoWidth : true,
          dropdownParent: $("#"+id),
          allowClear:true,
          minimumInputLength: 3
        });
      })

    // $("#"+id).select2().trigger('change');
    document.getElementById(lop).value = no;
    no+=1;
  }

    function getItemList() {
      $.get('{{ url("fetch/purchase_requisition/itemlist") }}', function(result, status, xhr) {
        item_list += "<option></option> ";
        $.each(result.item, function(index, value){
          item_list += "<option value="+value.kode_item+">"+value.kode_item+ " - " +value.deskripsi+"</option> ";
        });

      })
    }

    function currency(elem){

		var no = elem.id.match(/\d/g);
		no = no.join("");

		var mata_uang = $('#item_currency'+no).val();
		var mata_uang_text = $('#item_currency_text'+no).val();

		if (mata_uang == "USD") {
			$('#ket_harga'+no).text("$");
		}

		else if (mata_uang == "IDR") {
			$('#ket_harga'+no).text("Rp. ");		
		}

		else if (mata_uang == "JPY") {
			$('#ket_harga'+no).text("¥");
		}
	}

    function editPR(id){
      var isi = "";
      $('#modalEditPR').modal("show");
        
        var data = {
          id:id
      };
          
      $.get('{{ url("edit/purchase_requisition") }}', data, function(result, status, xhr){  

        	$("#identitas_edit").val(result.purchase_requisition.emp_id+' - '+result.purchase_requisition.emp_name).attr('readonly', true);
			$("#departemen_edit").val(result.purchase_requisition.department).attr('readonly', true);
			$("#no_pr_edit").val(result.purchase_requisition.no_pr).attr('readonly', true);
			$("#no_budget_edit").val(result.purchase_requisition.no_budget).attr('readonly', true);
			$("#tgl_pengajuan_edit").val(result.purchase_requisition.submission_date).attr('readonly', true);
			$("#id_edit_pr").val(result.purchase_requisition.id).attr('readonly', true);

	        $('#modalDetailBodyEditPR').html('');
	        var ids = [];
	        var total_amount = 0;

			$.ajax({
				url: "{{ route('admin.prgetbudgetdesc') }}?budget_no="+result.purchase_requisition.no_budget,
				method: 'GET',
				success: function(data) {
					var json = data,
					obj = JSON.parse(json);
		            // $('#SisaBudgetLabelEdit').text("$"+obj.budget_now.toFixed(2));
		            // $('#SisaBudgetEdit').val(obj.budget_now.toFixed(2));
	    	    	$('#bulanbudgetedit').text(obj.namabulan);
		        }
		    });


        $.each(result.purchase_requisition_item, function(key, value) {

	          // console.log(result.purchase_requisition_item);
	          var tambah3 = "tambah3";
	            var lop3 = "lop3";

	          isi = "<div id='"+value.id+"' class='col-md-12' style='margin-bottom : 5px'>";
	          if (value.item_code != null) {

	            isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_code_edit"+value.id+"' name='item_code_edit"+value.id+"' value="+value.item_code+"></div>";

	            // isi += "<div class='col-xs-1' style='padding:5px;'><select class='form-control select5 item_code_edit' data-placeholder='Choose Item' name='item_code_edit"+value.id+"' id='item_code_edit"+value.id+"' style='width: 100% height: 35px;' onchange='pilihItemEditPR(this)'><option></option></select></div>";

	          }else{
	            isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_code_edit"+value.id+"' name='item_code_edit"+value.id+"'></div>";
	          }
	          
	          isi += "<div class='col-xs-2' style='padding:5px;'><input type='text' class='form-control' id='item_desc_edit"+value.id+"' name='item_desc_edit"+value.id+"' placeholder='Description' required='' value='"+value.item_desc+"'></div>";

	          if (value.item_spec != null) {
	          		isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_spec_edit"+value.id+"' name='item_spec_edit"+value.id+"' placeholder='Specification' value='"+value.item_spec+"'></div>";
		  		}
		  		else{
		  			isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_spec_edit"+value.id+"' name='item_spec_edit"+value.id+"' placeholder='Specification' value=''></div>";
		  		}

	          if (value.item_stock != null) {
				  isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_stock_edit"+value.id+"' name='item_stock_edit"+value.id+"' placeholder='Stock' value='"+value.item_stock+"' readonly=''></div>";					
			  } else {
				  isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_stock_edit"+value.id+"' name='item_stock_edit"+value.id+"' placeholder='Stock' readonly=''></div>";
			  }

	          isi += "<div class='col-xs-1' style='padding:5px;'><input type='hidden' name='uomhide"+value.id+"' id='uomhide"+value.id+"' value='"+value.item_uom+"'><select class='form-control select5' id='uom_edit"+value.id+"' name='uom_edit"+value.id+"' readonly='' data-placeholder='UOM' style='width: 100%;' ><option></option>@foreach($uom as $um)<option value='{{ $um }}'>{{ $um }}</option>@endforeach</select></div>";
	          isi += "<div class='col-xs-1' style='padding:5px;'><div class='input-group date'><div class='input-group-addon'><i class='fa fa-calendar' style='font-size: 10px'></i> </div><input type='text' class='form-control pull-right datepicker' id='req_date_edit"+value.id+"' name='req_date_edit"+value.id+"' placeholder='Tanggal' required='' value='"+value.item_request_date+"''  readonly=''></div></div>";
	          isi += "<div class='col-xs-1' style='padding: 5px'><input type='text' class='form-control' id='item_currency_edit"+value.id+"' name='item_currency_edit"+value.id+"' value='"+value.item_currency+"' readonly=''><input type='text' class='form-control' id='item_currency_text_edit"+value.id+"' name='item_currency_text_edit"+value.id+"' style='display:none'  readonly=''></div>";
	          isi += "<div class='col-xs-1' style='padding:5px;'><div class='input-group'><span class='input-group-addon' id='ket_harga_edit"+value.id+"' style='padding:3px'>?</span><input type='text' class='form-control currency' id='item_price_edit"+value.id+"' name='item_price_edit"+value.id+"' placeholder='Harga' data-number-to-fixed='2' data-number-stepfactor='100' required='' value="+value.item_price+" style='padding: 6px 6px'  readonly=''></div></div>";
	          isi += "<div class='col-xs-1' style='padding:5px;'><input type='number' class='form-control' id='qty_edit"+value.id+"' name='qty_edit"+value.id+"' placeholder='Qty' onkeyup='getTotalEdit(this.id)' required='' value='"+value.item_qty+"'  readonly=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='amount_edit"+value.id+"' name='amount_edit"+value.id+"' placeholder='Total' required='' value='"+value.item_amount+"' readonly=''><input type='hidden' class='form-control' id='konversi_dollar"+value.id+"' name='konversi_dollar"+value.id+"' placeholder='Total' required='' readonly='' value="+value.amount+"></div>";
	          isi += "<div class='col-xs-1' style='padding:5px;'><a href='javascript:void(0);' id='b"+ value.id +"' onclick='deleteConfirmationPR(\""+ value.item_desc +"\","+value.id +");' class='btn btn-danger' data-toggle='modal' data-target='#modaldangerPR'><i class='fa fa-close'></i> </a> <button type='button' class='btn btn-success' onclick='tambahPR(\""+ tambah3 +"\",\""+ lop3 +"\");'><i class='fa fa-plus' ></i></button></div>";
	          isi += "</div>";

	          ids.push(value.id);

	          $('#modalDetailBodyEditPR').append(isi);

	          // $('.item_code_edit').append(item_list);


	          	if (value.item_currency == "USD") {
	              $('#ket_harga_edit'+value.id).text("$");
	            }else if (value.item_currency == "JPY") {
	              $('#ket_harga_edit'+value.id).text("¥");
	            }else if (value.item_currency == "IDR"){
	              $('#ket_harga_edit'+value.id).text("Rp.");
	            }

	          	var uom = $('#uomhide'+value.id).val();
	            $("#uom_edit"+value.id).val(uom).trigger("change");

	            // var item_code = $('#item_code_edit_hide'+value.id).val();
	            // $("#item_code_edit"+value.id).val(item_code).trigger("change");

	            $('.datepicker').datepicker({
		            autoclose: true,
		            format: 'yyyy-mm-dd'
		          });

		        $(function () {
		            $('.select5').select2({
		              dropdownAutoWidth : true,
		              dropdownParent: $("#"+value.id),
		              allowClear: true,
		              minimumInputLength: 3
		            });
		        })

	          	$("#looping_pr").val(ids);

	          	date_budget = new Date(value.budget_date);
        		budget_bulan = date_budget.getMonth()+1;
        		total_amount = total_amount + value.amount;
        		total_sisa = value.beg_bal - total_amount;

        		$("#budgetLabelEdit").text("$"+value.beg_bal).attr('readonly', true);

				$("#TotalPembelianEdit").val(total_amount.toFixed(2)).attr('readonly', true);
				$("#TotalPembelianEditLabel").text("$"+total_amount.toFixed(2)).attr('readonly', true);

				$('#SisaBudgetLabelEdit').text("$"+total_sisa.toFixed(2));
		        $('#SisaBudgetEdit').val(total_sisa.toFixed(2));

        	});
      	});
    }

   function pilihItemPR(elem)
   {
    var no = elem.id.match(/\d/g);
    no = no.join("");

    if (elem.value == "kosong") {
      $('#item_code'+no).val("");
      $('#item_desc'+no).val("").attr('readonly', false);
      $('#item_spec'+no).val("").attr('readonly', false);
      $('#item_stock'+no).val("").attr('readonly', false);
      $('#item_price'+no).val("").attr('readonly', false);
      $('#uom'+no).val("").attr('readonly', false);
      $('#item_currency'+no).val("");
      $('#item_currency'+no).next(".select2-container").show();
      $('#item_currency'+no).next(".select3-container").show();
      $('#item_currency'+no).show();
      $('#item_currency_text'+no).val("");
      $('#item_currency_text'+no).hide();
      $('#ket_harga'+no).text("?");
    }

    else{

      $.ajax({
        url: "{{ route('admin.prgetitemdesc') }}?kode_item="+elem.value,
        method: 'GET',
        success: function(data) {
          var json = data,
          obj = JSON.parse(json);
          $('#item_desc'+no).val(obj.deskripsi).attr('readonly', true);
          $('#item_spec'+no).val(obj.spesifikasi).attr('readonly', true);
          $('#item_price'+no).val(obj.price).attr('readonly', true);
          $('#uom'+no).val(obj.uom).change();
          // $('#qty'+no).val("0");
          $('#amount'+no).val("0");
          $('#item_currency'+no).next(".select2-container").hide();
          $('#item_currency'+no).hide();
          $('#item_currency_text'+no).show();
          $('#item_currency_text'+no).val(obj.currency).show().attr('readonly', true);
          if (obj.currency == "USD") {
            $('#ket_harga'+no).text("$");
          }else if (obj.currency == "JPY") {
            $('#ket_harga'+no).text("¥");
          }else if (obj.currency == "IDR"){
            $('#ket_harga'+no).text("Rp.");
          }

          var $datepicker = $('#req_date'+no).attr('readonly', false);
          $datepicker.datepicker();
          $datepicker.datepicker('setDate', limitdate);

        } 
      });

    }
      // alert(sel.value);
  }

    function pilihItemEditPR(elem)
    {
      var no = elem.id.match(/\d/g);
      no = no.join("");

      $.ajax({
        url: "{{ route('admin.prgetitemdesc') }}?kode_item="+elem.value,
        method: 'GET',
        success: function(data) {
          var json = data,
          obj = JSON.parse(json);
          $('#item_desc_edit'+no).val(obj.deskripsi).attr('readonly', true);
          $('#item_spec_edit'+no).val(obj.spesifikasi).attr('readonly', true);
          $('#item_price_edit'+no).val(obj.price).attr('readonly', true);
          $('#uom_edit'+no).val(obj.uom).change();
          // $('#qty_edit'+no).val("0");
          // $('#amount_edit'+no).val("0");
          $('#item_currency_edit'+no).next(".select2-container").hide();
          $('#item_currency_edit'+no).hide();
          $('#item_currency_text_edit'+no).show();
          $('#item_currency_text_edit'+no).val(obj.currency).show().attr('readonly', true);
          if (obj.currency == "USD") {
            $('#ket_harga_edit'+no).text("$");
          }else if (obj.currency == "JPY") {
            $('#ket_harga_edit'+no).text("¥");
          }else if (obj.currency == "IDR"){
            $('#ket_harga_edit'+no).text("Rp.");
          }

          var $datepicker = $('#req_date_edit'+no).attr('readonly', false);
          $datepicker.datepicker();
          $datepicker.datepicker('setDate', limitdate);

        } 
      });

        // alert(sel.value);
    }

    function getTotal(id) {
      // console.log(id);
      var num = id.match(/\d/g);
      num = num.join("");

      var price = document.getElementById("item_price"+num).value;
        var prc = price.replace(/\D/g, ""); //get angka saja

        var qty = document.getElementById("qty"+num).value;
          var hasil = parseInt(qty) * parseFloat(prc); //Dikalikan qty

          if (!isNaN(hasil)) {

	            var amount = document.getElementById('amount'+num);
	            // amount.value = rubah(hasil);
	            amount.value = hasil.toFixed(2);
	            
		        var mata_uang = $('#item_currency'+num).val();
				var mata_uang_text = $('#item_currency_text'+num).val();

		    	if (mata_uang == "USD" || mata_uang_text == "USD" ) {
	    			$("#konversi_dollar"+num).val(konversi("USD","USD",hasil));
	    		}
	    		else if (mata_uang == "JPY" || mata_uang_text == "JPY"){
	    			$("#konversi_dollar"+num).val(konversi("JPY","USD",hasil));
	    		}
	    		else if (mata_uang == "IDR" || mata_uang_text == "IDR"){
	    			$("#konversi_dollar"+num).val(konversi("IDR","USD",hasil));
	    		}
        }
    }

    function getTotalEdit(id) {
		// console.log(id);
		var num = id.match(/\d/g);
		num = num.join("");

		var price = $("#item_price_edit"+num).val();
	    var prc = price.replace(/\D/g, ""); //get angka saja

	    var qty = $("#qty_edit"+num).val();
      	var hasil = parseInt(qty) * parseInt(prc); //Dikalikan qty

      	if (!isNaN(hasil)) {
    		$("#amount_edit"+num).val(hasil);
	    	var dollar = document.getElementById('konversi_dollar'+num);

	    	total_usd = 0;
    		total_id = 0;
    		total_yen = 0;
    		total_usd_arr = [0,0,0,0,0,0,0,0,0,0,0,0];
    		total_yen_arr = [0,0,0,0,0,0,0,0,0,0,0,0];
    		total_id_arr = [0,0,0,0,0,0,0,0,0,0,0,0];
    		total_yen_konversi = [0,0,0,0,0,0,0,0,0,0,0,0];
    		total_id_konversi = [0,0,0,0,0,0,0,0,0,0,0,0];
    		total_beli = [0,0,0,0,0,0,0,0,0,0,0,0];


    		var req_date = $('#req_date_edit'+num).val();
    		date_js = new Date(req_date);
    		req_bulan = date_js.getMonth()+1;

			var mata_uang = $('#item_currency_edit'+num).val();
			var mata_uang_text = $('#item_currency_text_edit'+num).val();

	    	if (mata_uang == "USD" || mata_uang_text == "USD" ) {
    			total_usd += parseInt(hasil);

    			$("#konversi_dollar"+num).val(konversi("USD","USD",hasil));
    		}
    		else if (mata_uang == "JPY" || mata_uang_text == "JPY"){
    			total_yen += parseInt(hasil);

    			$("#konversi_dollar"+num).val(konversi("JPY","USD",hasil));
    		}

    		else if (mata_uang == "IDR" || mata_uang_text == "IDR"){
    			total_id += parseInt(hasil);

    			$("#konversi_dollar"+num).val(konversi("IDR","USD",hasil));
    		}


	    	if (mata_uang == "USD" || mata_uang_text == "USD" ) {	
    			total_usd_arr[parseInt(req_bulan) - 1] += parseInt(total_usd);
    		}
    		else if (mata_uang == "JPY" || mata_uang_text == "JPY"){
				total_yen_arr[parseInt(req_bulan) - 1] += parseInt(total_yen);    	    			
    			total_yen_konversi[parseInt(req_bulan) - 1] = parseFloat(konversi("JPY","USD",total_yen_arr[parseInt(req_bulan) - 1]));
    		}
    		else if (mata_uang == "IDR" || mata_uang_text == "IDR"){
    			total_id_arr[parseInt(req_bulan) - 1] += parseInt(total_id);    	    			
    			total_id_konversi[parseInt(req_bulan) - 1] = parseFloat(konversi("IDR","USD",total_id_arr[parseInt(req_bulan) - 1]));
    		}


    		for (var j = 0; j < total_usd_arr.length; j++) {
    	    	total_beli[j] = total_usd_arr[j] + total_yen_konversi[j] + total_id_konversi[j];

    	    	budget = $('#BudgetEdit'+parseInt(j+1)).text();
    	    	
    	    	if (total_beli[j] > 0) {
	    	    	$('#TotalPembelianEditLabel'+parseInt(j+1)).text("$"+total_beli[j]);
	    	    	$('#TotalPembelianEdit'+parseInt(j+1)).val(total_beli[j])
	    	    }else{
	    	    	$('#TotalPembelianEditLabel'+parseInt(j+1)).text("");
	    	    }

    	    	var sisa = parseFloat(budget.substr(1)) - parseFloat(total_beli[j]);

    	    	if (total_beli[j] > 0) {
	    	    	if (sisa < 0) {
		    	    	$('#SisaBudgetEdit'+parseInt(j+1)).text("$"+sisa.toFixed(2)).css("color", "red");	    	    		
	    	    	}else if(sisa > 0){
	    	    		$('#SisaBudgetEdit'+parseInt(j+1)).text("$"+sisa.toFixed(2)).css("color", "green");
	    	    	}
	    	    	else{
	    	    		$('#SisaBudgetEdit'+parseInt(j+1)).text("$"+sisa.toFixed(2));
	    	    	}
    	    	}
    	    	else{
    	    		$('#SisaBudgetEdit'+parseInt(j+1)).text("");
    	    	}
    		}
	    }
	}

    function rubah(angka){
      var reverse = angka.toString().split('').reverse().join(''),
      ribuan = reverse.match(/\d{1,3}/g);
      ribuan = ribuan.join('.').split('').reverse().join('');
      return ribuan;
    }



    function detailPR(id){
	    var isi = "";
	    $('#modalDetailPR').modal("show");
	        
	    var data = {
	        id:id
	    };
          
        $.get('{{ url("detail/purchase_requisition/po") }}', data, function(result, status, xhr){  


        var tableData = "";
		var count = 1;
		$('#tableDetailBody').html("");

		$.each(result.purchase_requisition_item, function(key, value) {
			tableData += "<tr id='row"+value.id+"''>";
			tableData += "<td>"+count+"</td>";
			tableData += "<td>"+value.item_code+"</td>";
			tableData += "<td>"+value.item_desc+"</td>";
			tableData += "<td>"+value.item_spec+"</td>";
			tableData += "<td>"+value.item_stock+"</td>";
			tableData += "<td>"+value.item_request_date+"</td>";
			tableData += "<td>"+value.item_currency+"</td>";
			tableData += "<td>"+value.item_price+"</td>";
			tableData += "<td>"+value.item_qty+" "+value.item_uom+"</td>";
			tableData += "<td>"+value.item_amount+"</td>";

			if(value.sudah_po == null || value.sudah_po == ""){
				tableData += "<td style='background-color: RGB(255,204,255);'>Belum PO</td>";
			}
			else if(value.sudah_po != ""){
				tableData += "<td style='background-color: RGB(204,255,255);'>Sudah PO</td>";
			}
			tableData += "</tr>";
			count += 1;
		});
		$('#tableDetailBody').append(tableData);
            
      });
    }


</script>

@endsection