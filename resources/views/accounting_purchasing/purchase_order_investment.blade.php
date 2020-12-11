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
		padding: 2px 2px;
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
					<h3 class="box-title">Outstanding Investment yang Belum Di PO</span></h3>

					<div class="row">
						<div class="col-xs-12">
							<div class="box no-border">
								<div class="box-header">
								</div>
								<div class="box-body" style="padding-top: 0;">
									<table id="outstandingTable" class="table table-bordered table-striped table-hover">
										<thead style="background-color: rgba(126,86,134,.7);">
											<tr>
												<th style="width: 2%">Nomor Investment</th>
												<th style="width: 1%">Tanggal Pengajuan</th>
												<th style="width: 2%">Departemen</th>
												<th style="width: 1%">Applicant</th>
												<th style="width: 1%">Kategori</th>
												<th style="width: 1%">Judul</th>
												<th style="width: 3%">Vendor</th>
												<th style="width: 2%">Tipe</th>
												<th style="width: 1%">Att</th>
												<th style="width: 10%">Action</th>
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
										<th style="width: 3%">No PO</th>
										<th style="width: 1%">Remark</th>
										<th style="width: 2%">Tanggal PO</th>
										<th style="width: 3%">Supplier</th>
										<th style="width: 2%">No PO SAP</th>
										<th style="width: 2%">Status</th>
										<th style="width: 4%">Action</th>
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
					<h4 class="modal-title">Create Purchase Order Investment</h4>
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
												<input type="hidden" class="form-control" id="remark" name="remark" value="Investment">
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
											<label>Authorized 2<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="authorized2_name" name="authorized2_name" readonly="" value="{{$authorized2->name}}">
											<input type="hidden" class="form-control" id="authorized2" name="authorized2" readonly="" value="{{$authorized2->employee_id}}">
										</div>
										<div class="form-group">
											<label>Authorized 3<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="authorized3_name" name="authorized3_name" readonly="" value="{{$authorized3->name}}">
											<input type="hidden" class="form-control" id="authorized3" name="authorized3" readonly="" value="{{$authorized3->employee_id}}">
										</div>
										<div class="form-group">
											<label>Authorized 4<span class="text-red">*</span></label>
											<select class="form-control select4" id="authorized4" name="authorized4" data-placeholder='Pilih Authorized 4' style="width: 100%" onchange="getAuthorized4(this);">
												<option value="">&nbsp;</option>
												@foreach($authorized4 as $author4)
												<option value="{{$author4->employee_id}}">{{$author4->name}}</option>
												@endforeach
											</select>
											<input type="hidden" class="form-control" id="authorized4_name" name="authorized4_name" readonly="">
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
									<div class="col-xs-1" style="padding:5px;">
										<b>No Invest</b>
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
										<select class="form-control select2" data-placeholder="Reff Number" name="reff_number1" id="reff_number1" style="width: 100% height: 35px;" onchange="pilihInvestment(this)">
										</select>
									</div>

									<div class="col-xs-1" style="padding:5px;">
										<select class="form-control select2" data-placeholder="Item" name="no_item1" id="no_item1" style="width: 100% height: 35px;" onchange="pilihItem(this)">
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
										<input type="text" class="form-control" id="service_price1" name="service_price1" placeholder="Service" required="" readonly="">
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
									<a class="btn btn-success pull-right" onclick="submitForm()">Konfirmasi</a>
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
											<label>Authorized 2<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="authorized2_name" name="authorized2_name" readonly="" value="{{$authorized2->name}}">
											<input type="hidden" class="form-control" id="authorized2" name="authorized2" readonly="" value="{{$authorized2->employee_id}}">
										</div>
										<div class="form-group">
											<label>Authorized 3<span class="text-red">*</span></label>
											<input type="text" class="form-control" id="authorized3_name" name="authorized3_name" readonly="" value="{{$authorized3->name}}">
											<input type="hidden" class="form-control" id="authorized3" name="authorized3" readonly="" value="{{$authorized3->employee_id}}">
										</div>
										<div class="form-group">
											<label>Authorized 4<span class="text-red">*</span></label>
											<select class="form-control select5" id="authorized4_edit" name="authorized4_edit" data-placeholder='Pilih Authorized 3' style="width: 100%" onchange="getAuthorized4Edit(this)">
												<option value="">&nbsp;</option>
												@foreach($authorized4 as $author4)
												<option value="{{$author4->employee_id}}">{{$author4->name}}</option>
												@endforeach
											</select>
											<input type="hidden" class="form-control" id="authorized4_name_edit" name="authorized4_name_edit" readonly="">
										</div>
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


<div class="modal fade in" id="modalEditInv">
    <form id ="importFormEditInv" name="importFormEditInv" method="post" action="{{ url('update/investment/po') }}">
      <input type="hidden" value="{{csrf_token()}}" name="_token" />
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">Edit Investment</h4>
            <br>
            <div>
              <div class="col-md-12" style="margin-bottom : 5px">
                <div class="col-xs-2" style="padding:5px;">
                  <b>Kode Item</b>
                </div>
                <div class="col-xs-2" style="padding:5px;">
                  <b>Deskripsi</b>
                </div>
                <div class="col-xs-2" style="padding:5px;">
                  <b>Qty</b>
                </div>
                <div class="col-xs-2" style="padding:5px;">
                  <b>Harga</b>
                </div>
                <div class="col-xs-2" style="padding:5px;">
                  <b>Total</b>
                </div>
                <div class="col-xs-2" style="padding:5px;">
                  <b>Dollar</b>
                </div>
              </div>
              <div id="modalDetailBodyEditInv">
              </div>
            </div>
            <br>
            <div id="tambah3">
              <input type="text" name="lop" id="lop" value="1" hidden="">
			  <input type="text" name="looping_inv" id="looping_inv" hidden="">
            </div>
	    </div>
      	<div class="modal-footer">
            <input type="hidden" class="form-control" id="id_edit_inv" name="id_edit_inv" placeholder="ID">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-warning">Update</button>
      	</div>
    </div>
   	</div>
	</form>
</div>

  <div class="modal fade in" id="modalDetailInvestment">
      <input type="hidden" value="{{csrf_token()}}" name="_token" />
      <div class="modal-dialog modal-lg" style="width: 1300px">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">Detail Item Investment</h4>
            <br>
            <h4 class="modal-title" id="modalDetailTitle"></h4>

            <div >
              <div class="col-md-12" style="margin-bottom : 5px">
              	<table class="table table-hover table-bordered table-striped" id="tableDetail">
	              	<thead style="background-color: rgba(126,86,134,.7);">
						<tr>
							<th style="width: 1%;">No</th>
							<th style="width: 2%;">No Item</th>
							<th style="width: 5%;">Detail</th>
							<th style="width: 4%;">Qty</th>
							<th style="width: 2%;">Price</th>
							<th style="width: 3%;">Amount</th>
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
            <input type="hidden" class="form-control" id="id_edit" name="id_edit" placeholder="ID">
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
				<a id="a" name="modalDeleteButton" href="#" type="button" onclick="delete_item(this.id)" class="btn btn-danger">Delete</a>
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
	inv_list = "";
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
		getExchangeRate();
		getInvList();

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
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});

		$('.btnNext').click(function(){
			var no_po2 = $('#no_po2').val();
			var supplier = $('#supplier_code').val();
			var material = $('#material').val();
			var price_vat = $('#price_vat').val();
			var delivery_term = $('#delivery_term').val();
			var currency = $('#currency').val();
			var authorized4 = $('#authorized4').val();

			if(no_po2 == '' || supplier == "" || material == "" || price_vat == "" || delivery_term == "" || currency == "" || authorized4 == ""){
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
			var authorized4 = $('#authorized4_edit').val();

			if( supplier == "" || material == "" || price_vat == "" || delivery_term == "" || currency == "" || authorized4 == ""){
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
				"url" : "{{ url("fetch/po_investment_outstanding") }}",
			},
			"columns": [
			{ "data": "reff_number" },
			{ "data": "submission_date" },
			{ "data": "applicant_department" },
			{ "data": "applicant_name" },
			{ "data": "category" },
			{ "data": "subject" },
			{ "data": "supplier_name" },
			{ "data": "type" },
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
			{ "data": "remark" },
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

	function getAuthorized4(elem){

		$.ajax({
			url: "{{ route('admin.pogetname') }}?authorized4="+elem.value,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$('#authorized4_name').val(obj.name);
			} 
		});
	}

	function getAuthorized4Edit(elem){

		$.ajax({
			url: "{{ route('admin.pogetname') }}?authorized4="+elem.value,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$('#authorized4_name_edit').val(obj.name);
			} 
		});
	}

	function getInvList() {
		$.get('{{ url("fetch/purchase_order/invlist") }}', function(result, status, xhr) {
			inv_list += "<option></option> ";
			$.each(result.investment, function(index, value){
				inv_list += "<option value="+value.reff_number+">"+value.reff_number+"</option> ";
			});
			$('#reff_number1').append(inv_list);
		})
	}

	function pilihInvestment(elem)
	{
		var no = elem.id.match(/\d/g);
		no = no.join("");

		$.ajax({
			url: "{{ url('fetch/purchase_order/pilih_investment') }}?reff_number="+elem.value,
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

		var reff_number = $("#reff_number"+no).val();

		$.ajax({
			url: "{{ url('purchase_order/investment_get_item') }}?no_item="+elem.value+"&reff_number="+reff_number,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$('#qty'+no).attr('readonly', true).val(obj.qty);
				$('#nama_item'+no).attr('readonly', false).val(obj.deskripsi);
				$('#uom'+no).val(obj.uom).change();
				$('#item_budget'+no).attr('readonly', true).val(obj.budget_no);
				$('#delivery_date'+no).attr('readonly', false).val(obj.delivery_date);
				if (obj.currency == "USD") {
					$('#ket_harga'+no).text("$");
				}else if (obj.currency == "JPY") {
					$('#ket_harga'+no).text("¥");
				}else if (obj.currency == "IDR"){
					$('#ket_harga'+no).text("Rp.");
				}
				$('#last_price'+no).attr('readonly', true).val(obj.last_price);
				$('#goods_price'+no).attr('readonly', false).val(obj.price);
				$('#service_price'+no).attr('readonly', false).val(0);

				var total = obj.qty * obj.price;
				var conf = konversi(obj.currency,"USD",total);
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

	    var harga_konversi = parseFloat(konversi(currency,"USD", hasil));
	    $('#konversi_dollar'+num).val(harga_konversi);
	}

	function getkonversiEdit(elem)
	{
		var num = elem.id.match(/\d/g);
		num = num.join("");
		var currency = $('#currency_edit').val();
		// console.log(currency);

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

	    var harga_konversi = parseFloat(konversi(currency,"USD", hasil));
	    $('#konversi_dollar'+num).val(harga_konversi);
	}

	//Fungsi Tambah

	function tambah(id,lop) {
		var id = id;

		var lop = "";

		if (id == "tambah"){
			lop = "lop";
		}else{
			lop = "lop2";
		}

		var divdata = $("<div id='"+no+"' class='col-md-12' style='margin-bottom : 5px'><div class='col-xs-1' style='padding:5px;'><select class='form-control select3' data-placeholder='Reff Number' name='reff_number"+no+"' id='reff_number"+no+"' style='width: 100% height: 35px;' onchange='pilihInvestment(this)'></select></div><div class='col-xs-1' style='padding:5px;'><select class='form-control select3' data-placeholder='Item' name='no_item"+no+"' id='no_item"+no+"' style='width: 100% height: 35px;' onchange='pilihItem(this)'></select><input type='hidden' class='form-control' id='nama_item"+no+"' name='nama_item"+no+"' placeholder='Nama Item' readonly=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_budget"+no+"' name='item_budget"+no+"' placeholder='Budget' required='' readonly=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control datepicker' id='delivery_date"+no+"' name='delivery_date"+no+"' placeholder='Delivery Date' required='' readonly=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='qty"+no+"' name='qty"+no+"' placeholder='Qty' required='' readonly='' onkeyup='getkonversi(this)'></div> <div class='col-xs-1' style='padding:5px;'><select class='form-control select3' id='uom"+no+"' name='uom"+no+"' data-placeholder='UOM' style='width: 100%;'><option></option>@foreach($uom as $um)<option value='{{ $um }}'>{{ $um }}</option>@endforeach</select></div><div class='col-xs-1' style='padding:5px;'><div class='input-group'><span class='input-group-addon' id='ket_harga"+no+"'>?</span><input type='text' class='form-control currency' id='goods_price"+no+"' name='goods_price"+no+"' placeholder='Goods Price' required='' readonly='' onkeyup='getkonversi(this)'></div></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='last_price"+no+"' name='last_price"+no+"' placeholder='Last Price' readonly=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='service_price"+no+"' name='service_price"+no+"' placeholder='Service' required='' readonly=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='konversi_dollar"+no+"' name='konversi_dollar"+no+"' placeholder='Konversi Dollar' required='' readonly=''></div><div class='col-xs-1' style='padding:5px;'><select class='form-control select3' data-placeholder='GL Number' name='gl_number"+no+"' id='gl_number"+no+"' style='width: 100% height: 35px;' required=''><option value=''></option><option value='63300000'>63300000 - Factory Constool</option><option value='64500000'>64500000 - Repair Maintenance (Material)</option><option value='64500100'>64500100 - Repair Maintenance (Jasa)</option><option value='62300200'>62300200 - Transport Expense</option><option value='63401000'>63401000 - Information System</option><option value='63300400'>63300400 - Office Supply</option><option value='63100000'>63100000 - Traveling Expense</option><option value='64450000'>64450000 - Rent</option><option value='63704000'>63704000 - Profesional Fee STD, QA, CH, dan PE</option><option value='63700000'>63700000 - Miscellaneous Expense</option><option value='53704020'>53704020 - Profesional Fee</option><option value='54450000'>54450000 - Rent</option><option value='53500000'>53500000 - Postage & Telecomm</option><option value='54500000'>54500000 - Repair Maintenance (Material)</option><option value='54500100'>54500100 - Repair Maintenance (Jasa)</option><option value='53200000'>53200000 - Insurance expenses</option><option value='53400000'>53400000 - Office supplies</option><option value='53401000'>53401000 - Information System</option><option value='53100000'>53100000 - Traveling Expenses</option><option value='53601000'>53601000 - Training & Education</option><option value='52300110'>52300110 - Transport Expenses</option><option value='55100000'>55100000 - Books & Period Exp</option><option value='56200000'>56200000 - Tax & Public Dues</option><option value='53703000'>53703000 - Recruiting Expenses</option><option value='58500301'>58500301 - Expatriate Permittance</option><option value='53700000'>53700000 - Miscellaneous Expense</option><option value='54100100'>54100100 - General Activity (Pembelian Seragam)</option><option value='51400300'>51400300 - Medical Allowance</option><option value='15700000'>15700000 - CIP</option><option value='15100000'>15100000 - Building</option><option value='15300000'>15300000 - Machinary & Equipment</option><option value='15400000'>15400000 - Vehicle</option><option value='15500000'>15500000 - Tools, Furniture & Fixture</option></select></div><div class='col-xs-1' style='padding:5px;'><button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button> <button type='button' onclick='tambah(\""+id+"\",\""+lop+"\"); ' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div>");

		$("#"+id).append(divdata);
		$("#reff_number"+no).append(inv_list);

		$(function () {
			$('.select3').select2({
				dropdownAutoWidth : true,
				dropdownParent: $("#"+id),
				allowClear: true
			});
		})

		$('.datepicker').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd'
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
		$("#reff_number"+newid).attr("name","reff_number"+oldid);
		$("#no_item"+newid).attr("name","no_item"+oldid);
		$("#item_budget"+newid).attr("name","item_budget"+oldid);
		$("#delivery_date"+newid).attr("name","delivery_date"+oldid);
		$("#qty"+newid).attr("name","qty"+oldid);
		$("#uom"+newid).attr("name","uom"+oldid);
		$("#price"+newid).attr("name","price"+oldid);
		$("#currency"+newid).attr("name","currency"+oldid);
		$("#konversi_dollar"+newid).attr("name","konversi_dollar"+oldid);
		$("#gl_number"+newid).attr("name","gl_number"+oldid);

		$("#reff_number"+newid).attr("id","reff_number"+oldid);
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
			$("#reff_number"+newid).attr("name","reff_number"+oldid);
			$("#no_item"+newid).attr("name","no_item"+oldid);
			$("#item_budget"+newid).attr("name","item_budget"+oldid);
			$("#delivery_date"+newid).attr("name","delivery_date"+oldid);
			$("#qty"+newid).attr("name","qty"+oldid);
			$("#uom"+newid).attr("name","uom"+oldid);
			$("#price"+newid).attr("name","price"+oldid);
			$("#currency"+newid).attr("name","currency"+oldid);
			$("#konversi_dollar"+newid).attr("name","konversi_dollar"+oldid);
			$("#gl_number"+newid).attr("name","gl_number"+oldid);

			$("#reff_number"+newid).attr("id","reff_number"+oldid);
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
    		$("#authorized4_edit").val(result.purchase_order.authorized4).trigger('change.select2');
    		$("#note_edit").val(result.purchase_order.note).trigger('change.select2');


    		$('#modalDetailBodyEdit').html("");

	        // var no = 1;

		    var ids = [];
	        $.each(result.purchase_order_detail, function(key, value) {
		    	// console.log(result.purchase_order_detail);
		    	var tambah2 = "tambah2";
		    	var	lop2 = "lop2";

		    	isi = "<div id='"+value.id+"' class='col-md-12' style='margin-bottom : 5px'>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' name='reff_number"+value.id+"' id='reff_number"+value.id+"' value='"+ value.no_pr +"' readonly=''></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' name='no_item"+value.id+"' id='no_item"+value.id+"' value='"+ value.no_item +"' readonly=''><input type='hidden' class='form-control' id='nama_item"+value.id+"' name='nama_item"+value.id+"' placeholder='Nama Item' readonly='' value='"+ value.nama_item +"'></div> ";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_budget"+value.id+"' name='item_budget"+value.id+"' placeholder='Budget' required='' value="+value.budget_item+" readonly=''></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control datepicker' id='delivery_date"+value.id+"' name='delivery_date"+value.id+"' placeholder='Delivery Date' required='' value="+value.delivery_date+"></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='qty"+value.id+"' name='qty"+value.id+"' placeholder='Qty' required='' onkeyup='getkonversiEdit(this)' value="+value.qty+"></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='hidden' name='uomhide"+value.id+"' id='uomhide"+value.id+"' value='"+ value.uom +"'><select class='form-control select6' id='uom_edit"+value.id+"' name='uom_edit"+value.id+"' data-placeholder='UOM' style='width: 100%;'><option></option>@foreach($uom as $um)<option value='{{ $um }}'>{{ $um }}</option>@endforeach</select></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><div class='input-group'><span class='input-group-addon' id='ket_harga_edit"+value.id+"'>?</span><input type='text' class='form-control currency' id='goods_price"+value.id+"' name='goods_price"+value.id+"' placeholder='Goods Price' required='' onkeyup='getkonversiEdit(this)' value="+value.goods_price+"></div></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='last_price"+value.id+"' name='last_price"+value.id+"' placeholder='Last Price' value="+value.last_price+" readonly=''></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='service_price"+value.id+"' name='service_price"+value.id+"' placeholder='Service' required='' value="+value.service_price+"></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='konversi_dollar"+value.id+"' name='konversi_dollar"+value.id+"' placeholder='Konversi Dollar' required='' value="+value.konversi_dollar+"></div>";
		    	
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='hidden' name='glhide"+value.id+"' id='glhide"+value.id+"' value='"+ value.gl_number +"'><select class='form-control select6' id='gl_edit"+value.id+"' name='gl_edit"+value.id+"' data-placeholder='GL Number' style='width: 100%;'><option></option><option value='63300000'>63300000 - Factory Constool</option><option value='64500000'>64500000 - Repair Maintenance (Material)</option><option value='64500100'>64500100 - Repair Maintenance (Jasa)</option><option value='62300200'>62300200 - Transport Expense</option><option value='63401000'>63401000 - Information System</option><option value='63300400'>63300400 - Office Supply</option><option value='63100000'>63100000 - Traveling Expense</option><option value='64450000'>64450000 - Rent</option><option value='63704000'>63704000 - Profesional Fee STD, QA, CH, dan PE</option><option value='63700000'>63700000 - Miscellaneous Expense</option><option value='53704020'>53704020 - Profesional Fee</option><option value='54450000'>54450000 - Rent</option><option value='53500000'>53500000 - Postage & Telecomm</option><option value='54500000'>54500000 - Repair Maintenance (Material)</option><option value='54500100'>54500100 - Repair Maintenance (Jasa)</option><option value='53200000'>53200000 - Insurance expenses</option><option value='53400000'>53400000 - Office supplies</option><option value='53401000'>53401000 - Information System</option><option value='53100000'>53100000 - Traveling Expenses</option><option value='53601000'>53601000 - Training & Education</option><option value='52300110'>52300110 - Transport Expenses</option><option value='55100000'>55100000 - Books & Period Exp</option><option value='56200000'>56200000 - Tax & Public Dues</option><option value='53703000'>53703000 - Recruiting Expenses</option><option value='58500301'>58500301 - Expatriate Permittance</option><option value='53700000'>53700000 - Miscellaneous Expense</option><option value='54100100'>54100100 - General Activity (Pembelian Seragam)</option><option value='51400300'>51400300 - Medical Allowance</option><option value='15700000'>15700000 - CIP</option><option value='15100000'>15100000 - Building</option><option value='15300000'>15300000 - Machinary & Equipment</option><option value='15400000'>15400000 - Vehicle</option><option value='15500000'>15500000 - Tools, Furniture & Fixture</option></select></div>";

		    	// isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='gl_number"+value.id+"' name='gl_number"+value.id+"' placeholder='GL Number' required='' value="+value.gl_number+"></div>";

		    	isi += "<div class='col-xs-1' style='padding:5px;'><a href='javascript:void(0);' id='b"+ value.id +"' onclick='deleteConfirmation(\""+ value.nama_item +"\","+value.id +");' class='btn btn-danger' data-toggle='modal' data-target='#modaldanger'><i class='fa fa-close'></i> </a> <button type='button' class='btn btn-success' onclick='tambah(\""+ tambah2 +"\",\""+ lop2 +"\");'><i class='fa fa-plus' ></i></button> </div> "
		    	isi += "</div>";

		    	// 

		    	ids.push(value.id);


		    	$('#modalDetailBodyEdit').append(isi);
		    	$("#reff_number"+value.id).append(inv_list);

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

	function editSAP(id){
    	$('#modalSAP').modal("show");
    	$("#id_edit_sap").val(id);
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

	function delete_item(id) {
		var data = {
			id:id,
		}

		$.post('{{ url("delete/purchase_order_item") }}', data, function(result, status, xhr){

		});

		$('#modaldanger').modal('hide');
		$('#'+id).css("display","none");
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
        setTimeout(function(){  window.location.reload() }, 3000);
      })
    }

    


    function detailInvestment(id){
	    var isi = "";
	    $('#modalDetailInvestment').modal("show");
	        
	    var data = {
	        id:id
	    };
          
        $.get('{{ url("fetch/investment_item_detail") }}', data, function(result, status, xhr){  


        var tableData = "";
		var count = 1;
		$('#tableDetailBody').html("");

		$.each(result.investment_item, function(key, value) {
			tableData += "<tr id='row"+value.id+"''>";
			tableData += "<td>"+count+"</td>";
			tableData += "<td>"+value.no_item+"</td>";
			tableData += "<td>"+value.detail+"</td>";
			tableData += "<td>"+value.qty+"</td>";
			tableData += "<td>"+value.price+"</td>";
			tableData += "<td>"+value.amount+"</td>";

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

	function editInvestment(id){
      var isi = "";
      $('#modalEditInv').modal("show");
        
        var data = {
          id:id
      };
          
      $.get('{{ url("edit/investment") }}', data, function(result, status, xhr){  
		
		$("#id_edit_inv").val(result.investment.id).attr('readonly', true);
		$('#modalDetailBodyEditInv').html('');

		var gg = [];

        $.each(result.investment_detail, function(key, value) {
	          isi = "<div id='"+value.id+"' class='col-md-12' style='margin-bottom : 5px'>";

	          if (value.no_item != null) {
	            isi += "<div class='col-xs-2' style='padding:5px;'><input type='text' class='form-control' id='no_item_edit"+value.id+"' name='no_item_edit"+value.id+"' value="+value.no_item+"></div>";
	          }else{
	            isi += "<div class='col-xs-2' style='padding:5px;'><input type='text' class='form-control' id='no_item_edit"+value.id+"' name='no_item_edit"+value.id+"'></div>";
	          }
	          
	          isi += "<div class='col-xs-2' style='padding:5px;'><input type='text' class='form-control' id='detail_edit"+value.id+"' name='detail_edit"+value.id+"' placeholder='Description' required='' value='"+value.detail+"'></div>";
	          isi += "<div class='col-xs-2' style='padding:5px;'><input type='number' class='form-control' id='qty_edit"+value.id+"' name='qty_edit"+value.id+"' placeholder='Qty' required='' value='"+value.qty+"' disabled=''></div>";
	          isi += "<div class='col-xs-2' style='padding:5px;'><div class='input-group'><span class='input-group-addon' id='ket_harga_edit2"+value.id+"'>?</span><input type='text' class='form-control currency' id='price_edit"+value.id+"' name='price_edit"+value.id+"' placeholder='Goods Price' required='' value="+value.price+" disabled=''></div></div>";	          
	          isi += "<div class='col-xs-2' style='padding:5px;'><div class='input-group'><span class='input-group-addon' id='ket_harga_edit3"+value.id+"'>?</span><input type='text' class='form-control currency' id='amount_edit"+value.id+"' name='amount_edit"+value.id+"' placeholder='value' required='' value="+value.amount+" disabled=''></div></div>";
	          isi += "<div class='col-xs-2' style='padding:5px;'><input type='text' class='form-control' id='dollar_edit"+value.id+"' name='dollar_edit"+value.id+"' placeholder='Dollar' required='' value='"+value.dollar+"' disabled=''></div>";
	          isi += "</div>";

	          gg.push(value.id);
	         
	          	$('#modalDetailBodyEditInv').append(isi);

	          	if (result.investment.currency == "USD") {
	              $('#ket_harga_edit2'+value.id).text("$");
	              $('#ket_harga_edit3'+value.id).text("$");
	            }else if (result.investment.currency == "JPY") {
	              $('#ket_harga_edit2'+value.id).text("¥");
	              $('#ket_harga_edit3'+value.id).text("¥");
	            }else if (result.investment.currency == "IDR"){
	              $('#ket_harga_edit2'+value.id).text("Rp.");
	              $('#ket_harga_edit3'+value.id).text("Rp.");	              
	            }

	            $("#looping_inv").val(gg);

        	});
      	});
    }


</script>

@endsection