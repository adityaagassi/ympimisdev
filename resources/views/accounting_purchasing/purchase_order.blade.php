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

	input.currency {
		text-align: left;
		padding-right: 15px;
	}

	.input-group-addon {
		padding: 6px 6px;
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
							<table id="poTable" class="table table-bordered table-striped table-hover">
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
											<select class="form-control select4" id="supplier" name="supplier" data-placeholder='Supplier' style="width: 100%" onchange="getSupplier(this)">
												<option value="">&nbsp;</option>
												@foreach($vendor as $ven)
												<option value="{{$ven->supplier_name}}">{{$ven->supplier_name}}</option>
												@endforeach
											</select>
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
												<option value="">None</option>
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
												<option value="ID">IDR</option>
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
											<input type="text" class="form-control" id="authorized2name" name="authorized2name" readonly="" value="{{$authorized2->name}}">
											<input type="hidden" class="form-control" id="authorized2" name="authorized2" readonly="" value="{{$authorized2->employee_id}}">
										</div>
										<div class="form-group">
											<label>Authorized 3<span class="text-red">*</span></label>
											<select class="form-control select4" id="authorized3" name="authorized3" data-placeholder='Pilih Authorized 3' style="width: 100%">
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
									<div class="col-xs-1" style="padding:5px;">
										<b>NO PR</b>
									</div>
									<div class="col-xs-2" style="padding:5px;">
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
										<b>Aksi</b>
									</div>

									<input type="text" name="lop" id="lop" value="1" hidden>

									<div class="col-xs-1" style="padding:5px;">
										<select class="form-control select2" data-placeholder="PR" name="no_pr1" id="no_pr1" style="width: 100% height: 35px;" onchange="pilihPR(this)">
										</select>
									</div>

									<div class="col-xs-2" style="padding:5px;">
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
											<select class="form-control select5" id="supplier_edit" name="supplier_edit" data-placeholder='Supplier' style="width: 100%" onchange="getSupplierEdit(this)">
												<option value="">&nbsp;</option>
												@foreach($vendor as $ven)
												<option value="{{$ven->supplier_name}}">{{$ven->supplier_name}}</option>
												@endforeach
											</select>
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
												<option value="">None</option>
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
												<option value="ID">IDR</option>
												<option value="JPN">JPN</option>
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
											<input type="text" class="form-control" id="authorized2name" name="authorized2name" readonly="" value="{{$authorized2->name}}">
											<input type="hidden" class="form-control" id="authorized2" name="authorized2" readonly="" value="{{$authorized2->employee_id}}">
											
										</div>
										<div class="form-group">
											<label>Authorized 3<span class="text-red">*</span></label>
											<select class="form-control select5" id="authorized3_edit" name="authorized3_edit" data-placeholder='Pilih Authorized 3' style="width: 100%">
												<option value="">&nbsp;</option>
												@foreach($authorized3 as $author3)
												<option value="{{$author3->employee_id}}">{{$author3->name}}</option>
												@endforeach
											</select>
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
									<div class="col-xs-2" style="padding:5px;">
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
										<b>Aksi</b>
									</div>
									<div id="modalDetailBodyEdit"></div><br>
									<div id="tambah2">
										<input type="text" name="lop2" id="lop2" value="1" hidden="">
										<input type="text" name="looping" id="looping" hidden="">
									</div>

									<div class="col-md-12">
										<br>
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

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fillTable();
		getPRList();
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
			var supplier = $('#supplier').val();
			var material = $('#material').val();
			var price_vat = $('#price_vat').val();
			var delivery_term = $('#delivery_term').val();
			var currency = $('#currency').val();
			var authorized3 = $('#authorized3').val();

			if(no_po2 == '' || supplier == "" || material == "" || price_vat == "" || delivery_term == "" || currency == "" || authorized3 == ""){
				alert('All field must be filled');	
			}	
			else{
				$('.nav-tabs > .active').next('li').find('a').trigger('click');
			}
		});

		$('.btnNextEdit').click(function(){
			var supplier = $('#supplier_edit').val();
			var material = $('#material_edit').val();
			var price_vat = $('#price_vat_edit').val();
			var delivery_term = $('#delivery_term_edit').val();
			var currency = $('#currency_edit').val();
			var authorized3 = $('#authorized3_edit').val();

			if( supplier == "" || material == "" || price_vat == "" || delivery_term == "" || currency == "" || authorized3 == ""){
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
			{ "data": "tgl_po" },
			{ "data": "supplier" },
			{ "data": "material" },
			{ "data": "vat" },
			{ "data": "transportation" },
			{ "data": "delivery_term" },
			{ "data": "holding_tax" },
			{ "data": "currency" },
			{ "data": "note" },
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
			url: "{{ route('admin.pogetsupplier') }}?supplier_name="+elem.value,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$('#supplier_due_payment').val(obj.duration);
				$('#supplier_status').val(obj.status);
			} 
		});
	}

	function getSupplierEdit(elem){

		$.ajax({
			url: "{{ route('admin.pogetsupplier') }}?supplier_name="+elem.value,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$('#supplier_due_payment_edit').val(obj.duration);
				$('#supplier_status_edit').val(obj.status);
			} 
		});
	}

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
				$('#qty'+no).attr('readonly', false).val(obj.item_qty);
				$('#nama_item'+no).attr('readonly', false).val(obj.item_desc);
				$('#uom'+no).val(obj.item_uom).change();
				$('#item_budget'+no).attr('readonly', false).val(obj.no_budget);
				$('#delivery_date'+no).attr('readonly', false).val(obj.item_request_date);
				if (obj.item_currency == "USD") {
					$('#ket_harga'+no).text("$");
				}else if (obj.item_currency == "JPN") {
					$('#ket_harga'+no).text("¥");
				}else if (obj.item_currency == "ID"){
					$('#ket_harga'+no).text("Rp.");
				}
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
		var qty = document.getElementById("qty"+num).value;
		var hasil = parseInt(qty) * parseInt(harga_goods);
	    // var prc = price.replace(/\D/g, ""); //get angka saja

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
		var qty = document.getElementById("qty"+num).value;
		var hasil = parseInt(qty) * parseInt(harga_goods);
	    // var prc = price.replace(/\D/g, ""); //get angka saja

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

		var divdata = $("<div id='"+no+"' class='col-md-12' style='margin-bottom : 5px'><div class='col-xs-1' style='padding:5px;'><select class='form-control select3' data-placeholder='PR' name='no_pr"+no+"' id='no_pr"+no+"' style='width: 100% height: 35px;' onchange='pilihPR(this)'></select></div><div class='col-xs-2' style='padding:5px;'><select class='form-control select3' data-placeholder='Item' name='no_item"+no+"' id='no_item"+no+"' style='width: 100% height: 35px;' onchange='pilihItem(this)'></select><input type='hidden' class='form-control' id='nama_item"+no+"' name='nama_item"+no+"' placeholder='Nama Item' readonly=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_budget"+no+"' name='item_budget"+no+"' placeholder='Budget' required='' readonly=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control datepicker' id='delivery_date"+no+"' name='delivery_date"+no+"' placeholder='Delivery Date' required='' readonly=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='qty"+no+"' name='qty"+no+"' placeholder='Qty' required='' readonly='' onkeyup='getkonversi(this)'></div> <div class='col-xs-1' style='padding:5px;'><select class='form-control select3' id='uom"+no+"' name='uom"+no+"' data-placeholder='UOM' style='width: 100%;'><option></option>@foreach($uom as $um)<option value='{{ $um }}'>{{ $um }}</option>@endforeach</select></div><div class='col-xs-1' style='padding:5px;'><div class='input-group'><span class='input-group-addon' id='ket_harga"+no+"'>?</span><input type='text' class='form-control currency' id='goods_price"+no+"' name='goods_price"+no+"' placeholder='Goods Price' required='' readonly='' onkeyup='getkonversi(this)'></div></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='last_price"+no+"' name='last_price"+no+"' placeholder='Last Price' readonly=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='service_price"+no+"' name='service_price"+no+"' placeholder='Service' required='' readonly=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='konversi_dollar"+no+"' name='konversi_dollar"+no+"' placeholder='Konversi Dollar' required='' readonly=''></div><div class='col-xs-1' style='padding:5px;'><button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button> <button type='button' onclick='tambah(\""+id+"\",\""+lop+"\"); ' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div>");

		$("#"+id).append(divdata);
		$("#no_pr"+no).append(pr_list);

		$(function () {
			$('.select3').select2({
				dropdownAutoWidth : true,
				dropdownParent: $("#"+id),
				allowClear: true
			});
		})

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

		$("#no_pr"+newid).attr("id","no_pr"+oldid);
		$("#no_item"+newid).attr("id","no_item"+oldid);
		$("#item_budget"+newid).attr("id","item_budget"+oldid);
		$("#delivery_date"+newid).attr("id","delivery_date"+oldid);
		$("#qty"+newid).attr("id","qty"+oldid);
		$("#uom"+newid).attr("id","uom"+oldid);
		$("#price"+newid).attr("id","price"+oldid);
		$("#currency"+newid).attr("id","currency"+oldid);
		$("#konversi_dollar"+newid).attr("id","konversi_dollar"+oldid);

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


			$("#no_pr"+newid).attr("id","no_pr"+oldid);
			$("#no_item"+newid).attr("id","no_item"+oldid);
			$("#item_budget"+newid).attr("id","item_budget"+oldid);
			$("#delivery_date"+newid).attr("id","delivery_date"+oldid);
			$("#qty"+newid).attr("id","qty"+oldid);
			$("#uom"+newid).attr("id","uom"+oldid);
			$("#price"+newid).attr("id","price"+oldid);
			$("#currency"+newid).attr("id","currency"+oldid);
			$("#konversi_dollar"+newid).attr("id","konversi_dollar"+oldid);

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

    	var data = {
    		id:id
    	};

    	$.get('{{ url("edit/purchase_order") }}', data, function(result, status, xhr){

    		$("#id_edit").val(id);
    		$("#no_po_edit").val(result.purchase_order.no_po);
    		$("#tgl_po_edit").val(result.purchase_order.tgl_po);
    		$("#supplier_edit").val(result.purchase_order.supplier).trigger('change.select2');
    		$("#material_edit").val(result.purchase_order.material).trigger('change.select2');
    		$("#price_vat_edit").val(result.purchase_order.vat).trigger('change.select2');
    		$("#transportation_edit").val(result.purchase_order.transportation).trigger('change.select2');
    		$("#delivery_term_edit").val(result.purchase_order.delivery_term).trigger('change.select2');
    		$("#holding_tax_edit").val(result.purchase_order.holding_tax);
    		$("#currency_edit").val(result.purchase_order.currency).trigger('change.select2');
    		$("#buyer_id_edit").val(result.purchase_order.buyer_id);
    		$("#buyer_name_edit").val(result.purchase_order.buyer_name);
    		$("#authorized3_edit").val(result.purchase_order.authorized3).trigger('change.select2');
    		$("#note_edit").val(result.purchase_order.note).trigger('change.select2');


    		$('#modalDetailBodyEdit').html("");

	        // var no = 1;

		    var ids = [];
	        $.each(result.purchase_order_detail, function(key, value) {
		    	// console.log(result.purchase_order_detail);
		    	var tambah2 = "tambah2";
		    	var	lop2 = "lop2";

		    	isi = "<div id='"+value.id+"' class='col-md-12' style='margin-bottom : 5px'>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='hidden' class='form-control' name='id_edit' id='id_edit' value='"+value.id+"'><input type='text' class='form-control' name='no_pr"+value.id+"' id='no_pr"+value.id+"' value='"+ value.no_pr +"' readonly=''></div>";
		    	isi += "<div class='col-xs-2' style='padding:5px;'><input type='text' class='form-control' name='no_item"+value.id+"' id='no_item"+value.id+"' value='"+ value.no_item +"' readonly=''><input type='hidden' class='form-control' id='nama_item"+value.id+"' name='nama_item"+value.id+"' placeholder='Nama Item' readonly='' value='"+ value.nama_item +"'></div> ";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_budget"+value.id+"' name='item_budget"+value.id+"' placeholder='Budget' required='' value="+value.budget_item+"></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control datepicker' id='delivery_date"+value.id+"' name='delivery_date"+value.id+"' placeholder='Delivery Date' required='' value="+value.delivery_date+"></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='qty"+value.id+"' name='qty"+value.id+"' placeholder='Qty' required='' onkeyup='getkonversiEdit(this)' value="+value.qty+"></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='hidden' name='uomhide"+value.id+"' id='uomhide"+value.id+"' value='"+ value.uom +"'><select class='form-control select6' id='uom"+value.id+"' name='uom"+value.id+"' data-placeholder='UOM' style='width: 100%;'><option></option>@foreach($uom as $um)<option value='{{ $um }}'>{{ $um }}</option>@endforeach</select></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><div class='input-group'><span class='input-group-addon' id='ket_harga"+value.id+"'>?</span><input type='text' class='form-control currency' id='goods_price"+value.id+"' name='goods_price"+value.id+"' placeholder='Goods Price' required='' onkeyup='getkonversiEdit(this)' value="+value.goods_price+"></div></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='last_price"+value.id+"' name='last_price"+value.id+"' placeholder='Last Price' readonly=''></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='service_price"+value.id+"' name='service_price"+value.id+"' placeholder='Service' required='' value="+value.service_price+"></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='konversi_dollar"+value.id+"' name='konversi_dollar"+value.id+"' placeholder='Konversi Dollar' required='' value="+value.konversi_dollar+"></div>";
		    	isi += "<div class='col-xs-1' style='padding:5px;'><a href='javascript:void(0);' id='b"+ value.id +"' onclick='deleteConfirmation(\""+ value.nama_item +"\","+value.id +");' class='btn btn-danger' data-toggle='modal' data-target='#modaldanger'><i class='fa fa-close'></i> </a> <button type='button' class='btn btn-success' onclick='tambah(\""+ tambah2 +"\",\""+ lop2 +"\");'><i class='fa fa-plus' ></i></button></div> "
		    	isi += "</div>";

		    	ids.push(value.id);


		    	$('#modalDetailBodyEdit').append(isi);
		    	$("#no_pr"+value.id).append(pr_list);

		    	if (value.currency == "USD") {
		    		$('#ket_harga'+value.id).text("$");
		    	}else if (value.currency == "JPN") {
		    		$('#ket_harga'+value.id).text("¥");
		    	}else if (value.currency == "ID"){
		    		$('#ket_harga'+value.id).text("Rp.");
		    	}


		    	var uom = $('#uomhide'+value.id).val();
		    	$("#uom"+value.id).val(uom).trigger("change");


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

</script>

@endsection