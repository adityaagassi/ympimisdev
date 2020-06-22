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
			<a href="javascript:void(0)" onclick="openModalCreate()" class="btn btn-md bg-purple" style="color:white"><i class="fa fa-plus"></i> Buat {{ $page }}</a>
		</li>
	</ol>
</section>
@endsection

@section('content')

<section class="content"  style="padding:5px">
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

	<section class="content" style="padding:5px">
		<div class="row">
			<div class="col-xs-12">
				<div class="box no-border" style="margin-bottom: 5px;">
					<div class="box-header" style="margin-top: 10px">
						<h3 class="box-title">Detail Filter <span class="text-purple"> フィルター詳細</span></span></h3>
					</div>
					<div class="row">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="col-xs-12">
							<div class="col-md-3">
								<div class="form-group">
									<label>Tanggal Pengajuan</label>
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
									<label>Tanggal Pengajuan Sampai</label>
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
									<label>Departemen</label>
									<select class="form-control select2" multiple="multiple" name="department" id='department' data-placeholder="Select Department" style="width: 100%;">
										<option value=""></option>
										@foreach($dept as $dept)
										<option value="{{ $dept }}">{{ $dept }}</option>
										@endforeach
									</select>
								</div>
							</div>	
							<div class="col-md-3">
								<div class="form-group">
									<div class="col-md-6" style="padding-right: 0;">
										<label style="color: white;"> x</label>
										<button class="btn btn-primary form-control" onclick="fillTable()">Cari</button>
									</div>
									<div class="col-md-6" style="padding-right: 0;">
										<label style="color: white;"> x</label>
										<button class="btn btn-danger form-control" onclick="clearConfirmation()">Reset</button>
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
							<!-- <button class="btn btn-success" data-toggle="modal" data-target="#download" style="width: 
								16%">Download</button> -->
							</div>
							<div class="box-body" style="padding-top: 0;">
								<table id="prTable" class="table table-bordered table-striped table-hover">
									<thead style="background-color: rgba(126,86,134,.7);">
										<tr>
											<th style="width: 2%">Nomor PR</th>
											<th style="width: 2%">Departemen</th>
											<th style="width: 2%">Tanggal Pengajuan</th>
											<th style="width: 2%">User</th>
											<th style="width: 2%">Nomor Budget</th>
											<th style="width: 2%">Catatan</th>
											<th style="width: 1%">Att</th>
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

	<form id="importForm" name="importForm" method="post" action="{{ url('create/purchase_requisition') }}" enctype="multipart/form-data">
		<input type="hidden" value="{{csrf_token()}}" name="_token" />
		<div class="modal fade" id="modalCreate">
			<div class="modal-dialog modal-lg" style="width: 1300px">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title">Create Purchase Requisition</h4>
						<br>
						<div class="nav-tabs-custom tab-danger">
							<ul class="nav nav-tabs">
								<li class="vendor-tab active disabledTab"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Informasi PR</a></li>
								<li class="vendor-tab disabledTab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Informasi Budget PR</a></li>
								<li class="vendor-tab disabledTab"><a href="#tab_3" data-toggle="tab" id="tab_header_3">Detail Item PR</a></li>
							</ul>
						</div>
						<div class="tab-content">
							<div class="tab-pane active" id="tab_1">
								<div class="row">
									<div class="col-md-12">
										<div class="col-md-6">
											<div class="form-group">
												<label>Identitas<span class="text-red">*</span></label>
												<input type="text" class="form-control" value="{{$employee->employee_id}} - {{$employee->name}}" readonly="">
												<input type="hidden" id="emp_id" name="emp_id" value="{{$employee->employee_id}}">
												<input type="hidden" id="emp_name" name="emp_name" value="{{$employee->name}}">
											</div>
											<div class="form-group">
												<label>Departemen<span class="text-red">*</span></label>
												<input type="text" class="form-control" value="{{$employee->department}} {{$employee->group}}" readonly="">
												<input type="hidden" id="department" name="department" value="{{$employee->department}}">
												<input type="hidden" id="group" name="group" value="{{$employee->group}}">
											</div>
											<div class="form-group">
												<label>File Terlampir (Optional)</label>
												<input type="file" id="reportAttachment" name="reportAttachment[]" multiple="">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Tanggal Pengajuan<span class="text-red">*</span></label>
												<div class="input-group date">
													<div class="input-group-addon">	
														<i class="fa fa-calendar"></i>
													</div>
													<input type="text" class="form-control pull-right" id="sd" name="sd" value="<?= date('d F Y')?>" readonly="">
													<input type="hidden" class="form-control pull-right" id="submission_date" name="submission_date" value="<?= date('Y-m-d')?>" readonly="">
												</div>
											</div>
											<div class="form-group">
												<label>Nomor PR<span class="text-red">*</span></label>
												<input type="text" class="form-control" id="no_pr" name="no_pr" readonly="">
											</div>
											<div class="form-group">
												<label>Catatan / Keterangan<span class="text-red">*</span></label>
												<textarea class="form-control pull-right" id="note" name="note"></textarea>
											</div>
										</div>
									</div>
									<div class="col-md-12" style="padding-right: 30px;padding-top: 10px">
										<a class="btn btn-primary btnNext pull-right">Selanjutnya</a>
									</div>

									<div class="col-md-12" style="padding-right: 30px;padding-top: 10px">
										<div class="box box-primary" style="border-top-color: orange" id="noteimportant">
									      <div class="callout callout" style="background-color: #fbc02d;border-left: 0;color: black">
									        <h4><i class="fa fa-bullhorn"></i> Catatan!</h4>
									        <p>
									          <b>Request Date </b> Harus Disesuaikan. Contoh : 
									        </p>
									        <!-- sesuai dengan standar dan ketentuan dari masing - masing departemen.</p> -->
									    </div>
									  </div>
									</div>
								</div>
							</div>
							<div class="tab-pane" id="tab_2">
								<div class="row">
									<div class="col-md-12">
										<div class="col-md-12">
											<div class="form-group">
												<label>Budget<span class="text-red">*</span></label>
												<!-- <input type="text" class="form-control" id="budget_no" name="budget_no"> -->
												<select class="form-control select2" data-placeholder="Pilih Nomor Budget" name="budget_no" id="budget_no" style="width: 100% height: 35px;" required onchange="pilihBudget(this)"> 
													<option></option>
												</select>
											</div>
										</div>
										<div id="budgetket" style="display: none">

											<div class="col-xs-12">
												<div class="form-group">
													<b>Detail Budget</b> 
												</div>
											</div>

											<div class="col-xs-12">

												<table class="table" style="border:none">
													<tr>
														<td style="border:none;text-align: left;padding-left: 0;width: 8%">Deskripsi</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 2%">:</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 90%"><label id="budget_description" name="budget_description"></label></td>
													</tr>
													<tr>
														<td style="border:none;text-align: left;padding-left: 0;width: 8%">Nama Akun</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 2%">:</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 90%"><label id="budget_account" name="budget_account"></label></td>
													</tr>
													<tr>
														<td style="border:none;text-align: left;padding-left: 0;width: 8%">Kategori</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 2%">:</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 90%"><label id="budget_category" name="budget_category"></label></td>
													</tr>
													<tr>
														<td style="border:none;text-align: left;padding-left: 0;width: 8%">Budget 1 Tahun</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 2%">:</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 90%"><label id="budget_amount" name="budget_amount"></label></td>
													</tr>
													<tr>
														<td style="border:none;text-align: left;padding-left: 0;width: 8%">Sisa Budget (Total)</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 2%">:</td>
														<td style="border:none;text-align: left;padding-left: 0;width: 90%">
															<!-- <label id="sisa_budget" name="sisa_budget"></label> -->
															Dalam Pengembangan 
														</td>
													</tr>
												</table>

											</div>

										</div>
									</div>
									<div class="col-md-12">
										<a class="btn btn-primary btnNext2 pull-right">Selanjutnya</a>
										<span class="pull-right">&nbsp;</span>
										<a class="btn btn-info btnPrevious pull-right">Kembali</a>
									</div>
								</div>
							</div>
							<div class="tab-pane" id="tab_3">
								<div class="row">
									<div class="col-md-12" style="margin-bottom : 5px">
										<div class="col-xs-1" style="padding:5px;">
											<b>Kode Item</b>
										</div>
										<div class="col-xs-2" style="padding:5px;">
											<b>Deskripsi</b>
										</div>
										<div class="col-xs-2" style="padding:5px;">
											<b>Spesifikasi</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>UOM</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Request Date</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Mata Uang</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Harga</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Quantity</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Total</b>
										</div>
										<div class="col-xs-1" style="padding:5px;">
											<b>Aksi</b>
										</div>

										<input type="text" name="lop" id="lop" value="1" hidden>
										<div class="col-xs-1" style="padding:5px;">
											<select class="form-control select4" data-placeholder="Choose Item" name="item_code1" id="item_code1" style="width: 100% height: 35px;" onchange="pilihItem(this)">
											 <!-- @foreach($items as $item)
							                  <option value="{{ $item->kode_item }}" id="kode_item">{{ $item->kode_item }} - {{ $item->deskripsi }}</option>
							                  @endforeach -->
							              </select>

							              <!-- <input type="text" class="form-control" id="item_code1" name="item_code1" placeholder="Enter Item Code"> -->
							          </div>
							        <div class="col-xs-2" style="padding:5px;">
							          	<input type="text" class="form-control" id="item_desc1" name="item_desc1" placeholder="Description" required="">
						          	</div>
						          	<div class="col-xs-2" style="padding:5px;">
						          		<input type="text" class="form-control" id="item_spec1" name="item_spec1" placeholder="Specification">
						          	</div>
						          	<div class="col-xs-1" style="padding:5px;">
						          		<select class="form-control select4" id="uom1" name="uom1" data-placeholder="UOM" style="width: 100%;">
							              <option></option>
							              @foreach($uom as $um)
							              <option value="{{ $um }}">{{ $um }}</option>
							              @endforeach
							            </select>
						          		<!-- <input type="text" class="form-control" id="uom1" name="uom1" placeholder="UOM"> -->
						          	</div>
						          	<div class="col-xs-1" style="padding:5px;">
						          		<div class="input-group date">
						          			<div class="input-group-addon">
						          				<i class="fa fa-calendar" style="font-size: 10px"></i>
						          			</div>
						          			<input type="text" class="form-control pull-right datepicker" id="req_date1" name="req_date1" placeholder="Tanggal" readonly="" required="">
						          		</div>
						          	</div>

						          	<div class="col-xs-1" style="padding: 5px">
						          		<select class="form-control select2" id="item_currency1" name="item_currency1" data-placeholder='Currency' style="width: 100%" onchange="currency(this)">
						          			<option value="">&nbsp;</option>
						          			<option value="USD">USD</option>
						          			<option value="ID">ID</option>
						          			<option value="JPN">JPN</option>
						          		</select>
						          		<input type="text" class="form-control" id="item_currency_text1" name="item_currency_text1" style="display:none">
						          	</div>

						          	<div class="col-xs-1" style="padding:5px;">

						          		<div class="input-group"> 
						          			<span class="input-group-addon" id="ket_harga1">?</span>
						          			<input type="text" class="form-control currency" id="item_price1" name="item_price1" placeholder="Harga" data-number-to-fixed="2" data-number-stepfactor="100"required="">
						          		</div>
						          		<!-- input type="text" class="form-control" id="item_price1" name="item_price1" placeholder="Price" required="" onkeyup='getTotal(this.id)'> -->
						          	</div>

						          	<div class="col-xs-1" style="padding:5px;">
						          		<input type="number" class="form-control" id="qty1" name="qty1" placeholder="Qty" required="" onkeyup='getTotal(this.id)'>
						          	</div>
						          	<div class="col-xs-1" style="padding:5px;">
						          		<input type="text" class="form-control" id="amount1" name="amount1" placeholder="Total" required="" readonly="">
						          	</div>
						          	<div class="col-xs-1" style="padding:5px;">
						          		<a type="button" class="btn btn-success" onclick='tambah("tambah","lop");'><i class='fa fa-plus' ></i></a>
						          	</div>	
						          </div>

						          <div id="tambah"></div>

						          <div class="col-md-2 col-md-offset-5">
						          	<p><b>Total Dollar</b></p>
						          	<div class="input-group">
						          		<span class="input-group-addon">$ </span><input type="text" id="total_usd" class="form-control" readonly>
						          	</div>
						          </div>

						          <div class="col-md-2">
						          	<p><b>Total Rupiah</b></p>
						          	<div class="input-group">
						          		<span class="input-group-addon">Rp. </span><input type="text" id="total_id" class="form-control" readonly>
						          	</div>
						          </div>

						          <div class="col-md-2">
						          	<p><b>Total Yen</b></p>
						          	<div class="input-group">
						          		<span class="input-group-addon">¥ </span><input type="text" id="total_yen" class="form-control" readonly>
						          	</div>
						          </div>

						          <div class="col-md-3 col-md-offset-8" style="margin-top: 20px">
						          	<p><b>Total Keseluruhan</b></p>
						          	<div class="input-group">
						          		<span class="input-group-addon">$ </span><input type="text" id="total_keseluruhan" class="form-control" readonly>
						          	</div>
						          </div>

						          <div class="col-md-6" style="margin-top: 20px">
						          	<p><b>Informasi Budget</b></p>
						          	<table class="table table-striped text-center">
						          		<tr>
						          			<th>Quarter</th>
						          			<th>Budget Per Quarter</th>
						          			<th>Total Pembelian</th>
						          			<th>Sisa</th>
						          		</tr>
						          		<tr>
						          			<td>
						          				Q1
						          			</td>
						          			<td>
						          				<label id="budgetQ1" name="budgetQ1"></label>
						          			</td>
						          			<td>
						          				<label id="totalPembelianQ1" name="totalPembelianQ1"></label>
						          			</td>
						          			<td>
						          				<label id="sisaBudgetQ1" name="sisaBudgetQ1"></label>
						          			</td>
						          		</tr>
						          		<tr>
						          			<td>
						          				Q2
						          			</td>
						          			<td>
						          				<label id="budgetQ2" name="budgetQ2"></label>
						          			</td>
						          			<td>
						          				<label id="totalPembelianQ2" name="totalPembelianQ2"></label>
						          			</td>
						          			<td>
						          				<label id="sisaBudgetQ2" name="sisaBudgetQ2"></label>
						          			</td>
						          		</tr>
						          		<tr>
						          			<td>
						          				Q3
						          			</td>
						          			<td>
						          				<label id="budgetQ3" name="budgetQ3"></label>
						          			</td>
						          			<td>
						          				<label id="totalPembelianQ3" name="totalPembelianQ3"></label>
						          			</td>
						          			<td>
						          				<label id="sisaBudgetQ3" name="sisaBudgetQ3"></label>
						          			</td>
						          		</tr>
						          		<tr>
						          			<td>
						          				Q4
						          			</td>
						          			<td>
						          				<label id="budgetQ4" name="budgetQ4"></label>
						          			</td>
						          			<td>
						          				<label id="totalPembelianQ4" name="totalPembelianQ4"></label>
						          			</td>
						          			<td>
						          				<label id="sisaBudgetQ4" name="sisaBudgetQ4"></label>
						          			</td>
						          		</tr>
						          	</table>
						          </div>
						          <div class="col-md-12">
						          	<br>
						          	<button class="btn btn-success pull-right" onclick="submitForm()">Konfirmasi</button>
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
		<form id ="importFormEdit" name="importFormEdit" method="post" action="{{ url('update/purchase_requisition') }}">
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
								<div class="col-md-6">
									<div class="form-group" id="modalDetailBodyEditHeader">
										

									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group" id="modalDetailBodyEditHeader2">

									</div>
								</div>
							</div>
						</div>

						<div  id="modalDetailBodyEditHeader"></div>

						<div  id="modalDetailBodyEdit"></div><br>

						<div id="tambah2">
							<input type="text" name="lop2" id="lop2" value="1" hidden="">
						</div>
						
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-warning">Update</button>
					</div>
				</div>
			</div>
		</form>
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



		var no = 2;
		var limitdate = "";
		Q1 = "";
		Q2 = "";
		Q3 = "";
		Q4 = "";
		hasil_konversi_yen = 0;
		hasil_konversi_id = 0;
		item = [];
		budget_list = "";
		item_list = "";
		countusd = 1;
		countyen = 1;
		countid = 1;
		total_usd = 0;
		total_id = 0;
		total_yen = 0;
		exchange_rate = [];
		quarter = "";

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		jQuery(document).ready(function() {

		// data table
		fillTable();		

        //Get Detail Item Berdasarkan code_item
        getItemList();

        usd = document.getElementById('total_usd');
        usd.value = 0;

        id = document.getElementById('total_id');
        id.value = 0;

        yen = document.getElementById('total_yen');
        yen.value = 0;

        limitdate = new Date();
        limitdate.setDate(limitdate.getDate());
        // limitdate.setDate(limitdate.getDate() + 21);

        $('#datefrom').datepicker({
        	autoclose: true,
        	todayHighlight: true
        });

        $('#dateto').datepicker({
        	autoclose: true,
        	todayHighlight: true,
        });

        $('#sd').datepicker({
        	autoclose: true,
        	format: 'yyyy-mm-dd',
        	todayHighlight: true
        });

        $('.datepicker').datepicker({
        	autoclose: true,
        	format: 'yyyy-mm-dd',
        	startDate: limitdate
        });

        $('.btnNext').click(function(){
        	var emp_id = $('#emp_id').val();
        	var no_pr = $('#no_pr').val();
        	var catatan = $('#note').val();

        	if(emp_id == '' || no_pr == '' || catatan == ''){
        		alert('All field must be filled');	
        	}
        	else{
        		$('.nav-tabs > .active').next('li').find('a').trigger('click');
        	}
        });

        $('.btnNext2').click(function(){
        	var budget_no = $('#budget_no').val();
        	if(budget_no == ""){
        		alert('Budget field must be filled');	
        	}
        	else{
        		$('.nav-tabs > .active').next('li').find('a').trigger('click');
        	}
        });

        $('.btnPrevious').click(function(){
        	$('.nav-tabs > .active').prev('li').find('a').trigger('click');
        });
    });

	// Submit Form

	function submitForm() {
		var conf = confirm("Apakah Anda Yakin Ingin Submit PR Ini?");
		if (conf == true) {
			$('[name=importForm]').submit();
		} else {

		}
	}

	//Datatable 

	function fillTable(){
		$('#prTable').DataTable().clear();
		$('#prTable').DataTable().destroy();

		var datefrom = $('#datefrom').val();
		var dateto = $('#dateto').val();
		var department = $('#department').val();
		
		var data = {
			datefrom:datefrom,
			dateto:dateto,
			department:department,
		}

		var table = $('#prTable').DataTable({
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
				"url" : "{{ url("fetch/purchase_requisition") }}",
				"data" : data
			},
			"columns": [
			{ "data": "no_pr" },
			{ "data": "department" },
			{ "data": "submission_date" },
			{ "data": "emp_name" },
			{ "data": "no_budget" },
			{ "data": "note" },
			{ "data": "file" },
			{ "data": "status" },
			{ "data": "action" },
			],
		});

		$('#prTable tfoot th').each( function () {
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

		$('#prTable tfoot tr').appendTo('#prTable thead');
	}


	//Open Modal + Get Nomor PR

	function openModalCreate(){
		$('#modalCreate').modal('show');

		//nomor PR auto generate
		var nomorpr = document.getElementById("no_pr");

		$.ajax({
			url: "{{ url('purchase_requisition/get_nomor_pr') }}?dept=<?= $employee->department ?>", 
			type : 'GET', 
			success : function(data){
				var obj = jQuery.parseJSON(data);
				var no = obj.no_urut;
				var tahun = obj.tahun;
				var bulan = obj.bulan;
				var dept = obj.dept;

				nomorpr.value = dept+tahun+bulan+no;
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

		getBudget();
	}

	function clearConfirmation(){
		location.reload(true);		
	}

	//Get Item + Pilih Item

	function pilihItem(elem)
	{
		var no = elem.id.match(/\d/g);
		no = no.join("");

		if (elem.value == "kosong") {
			$('#item_code'+no).val("");
			$('#item_desc'+no).val("").attr('readonly', false);
			$('#item_spec'+no).val("").attr('readonly', false);
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
					$('#qty'+no).val("0");
					$('#amount'+no).val("0");
					$('#item_currency'+no).next(".select2-container").hide();
					$('#item_currency'+no).hide();
					$('#item_currency_text'+no).show();
					$('#item_currency_text'+no).val(obj.currency).show().attr('readonly', true);
					if (obj.currency == "USD") {
						$('#ket_harga'+no).text("$");
					}else if (obj.currency == "JPN") {
						$('#ket_harga'+no).text("¥");
					}else if (obj.currency == "ID"){
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

	function getItemList() {
		$.get('{{ url("fetch/purchase_requisition/itemlist") }}', function(result, status, xhr) {
			item_list += "<option></option> ";
			// item_list += "<option value='kosong'>kosong</option> ";
			$.each(result.item, function(index, value){
				// item.push({
				// 	'kode_item' :  value.kode_item, 
				// 	'deskripsi' :  value.deskripsi,
				// });
				// item_list += "<option></option>";
				item_list += "<option value="+value.kode_item+">"+value.kode_item+ " - " +value.deskripsi+"</option> ";
			});
			$('#item_code1').append(item_list);
		})
	}

	//get Budget + Pilih budget

	function getBudget() {

		data = {
			department:"{{ $employee->department }}",
		}

		$.get('{{ url("fetch/purchase_requisition/budgetlist") }}', data, function(result, status, xhr) {
			$.each(result.budget, function(index, value){
				budget_list += "<option value="+value.budget_no+">"+value.budget_no+" - "+value.description+"</option> ";
			});
			$('#budget_no').append(budget_list);
		})
	}

	function pilihBudget(elem)
	{

		$('#budgetket').show();

		$.ajax({

			url: "{{ route('admin.prgetbudgetdesc') }}?budget_no="+elem.value,
			method: 'GET',
			success: function(data) {
				var json = data,
				obj = JSON.parse(json);
				$('#budget_description').text(obj.description);
				$('#budget_account').text(obj.account);
				$('#budget_category').text(obj.category);
				$('#budget_amount').text("$"+obj.amount);

	            // $('#sisa_budget').text("$"+obj.apr);                
	            Q1 = "Q1";

	            totalq1 = obj.apr + obj.may + obj.jun;
	            $('#budget'+Q1).text("$"+totalq1.toFixed(2));

	            Q2 = "Q2";

	            totalq2 = obj.jul + obj.aug + obj.sep;
	            $('#budget'+Q2).text("$"+totalq2.toFixed(2));

	            Q3 = "Q3";

	            totalq3 = obj.oct + obj.nov + obj.dec;
	            $('#budget'+Q3).text("$"+totalq3.toFixed(2));

	            Q4 = "Q4";

	            totalq4 = obj.jan + obj.feb + obj.mar;
	            $('#budget'+Q4).text("$"+totalq4.toFixed(2));


	        } 
	    });
	}

	//Get total amount + Per Currency

	function getTotal(id) {
		// console.log(id);
		var num = id.match(/\d/g);
		num = num.join("");

		var price = document.getElementById("item_price"+num).value;
	    var prc = price.replace(/\D/g, ""); //get angka saja

	    var qty = document.getElementById("qty"+num).value;
      	var hasil = parseInt(qty) * parseInt(prc); //Dikalikan qty

      	// console.log(no);

      	if (!isNaN(hasil)) {

    		//isi amount + amount dirubah
    		var amount = document.getElementById('amount'+num);
    		amount.value = rubah(hasil);

    		total_usd = 0;
    		total_id = 0;
    		total_yen = 0;
    		total_yen2 = 0;
    		total_usd_q1 = 0;
    		total_usd_q2 = 0;
    		total_usd_q3 = 0;
    		total_usd_q4 = 0;
    		total_yen_q1 = 0;
    		total_yen_q2 = 0;
    		total_yen_q3 = 0;
    		total_yen_q4 = 0;
    		total_id_q1 = 0;
    		total_id_q2 = 0;
    		total_id_q3 = 0;
    		total_id_q4 = 0;
    		total_yen_q1_konversi = 0;
    		total_yen_q2_konversi = 0;
    		total_yen_q3_konversi = 0;
    		total_yen_q4_konversi = 0;
    		total_id_q1_konversi = 0;
    		total_id_q2_konversi = 0;
    		total_id_q3_konversi = 0;
    		total_id_q4_konversi = 0;
        	//mata uang

        	for (var i = 1; i < no; i++) {


        		var req_date = $('#req_date'+i).val();
        		date_js = new Date(req_date);
        		req_bulan = date_js.getMonth()+1;

        		var mata_uang = $('#item_currency'+i).val();
        		var mata_uang_text = $('#item_currency_text'+i).val();

        		if (mata_uang == "USD" || mata_uang_text == "USD" ) {
        			total_beli_usd = document.getElementById('amount'+i).value;
        			usd = total_beli_usd.replace(/\D/g, "");
        			total_usd += parseInt(usd);
        		}
        		else if (mata_uang == "JPN" || mata_uang_text == "JPN"){
        			total_beli_yen = document.getElementById('amount'+i).value;
        			yen = total_beli_yen.replace(/\D/g, "");
        			total_yen += parseInt(yen);
        		}

        		else if (mata_uang == "ID" || mata_uang_text == "ID"){
        			total_beli_id = document.getElementById('amount'+i).value;
        			idn = total_beli_id.replace(/\D/g, "");
        			total_id += parseInt(idn);


        		}
		        	// console.log(total_yen);
		        	document.getElementById('total_usd').value = rubah(total_usd);
		        	document.getElementById('total_yen').value = rubah(total_yen);
		        	document.getElementById('total_id').value = rubah(total_id);

		        	var curr;
		        	if (mata_uang != "") {
		        		curr = mata_uang;
		        	}
		        	else if(mata_uang_text != ""){
		        		curr = mata_uang_text;
		        	}

    	    	//Get Amount BY Quarter

    	    	tot_usd = document.getElementById('amount'+i).value;
    	    	tot_yen = document.getElementById('amount'+i).value;
    	    	tot_id = document.getElementById('amount'+i).value;

    	    	if (req_bulan == "04" || req_bulan == "05" || req_bulan == "06") {
    	    		if (mata_uang == "USD" || mata_uang_text == "USD" ) {
    	    			total_usd_q1 += parseInt(tot_usd.replace(/\D/g, ""));
    	    		}
    	    		else if (mata_uang == "JPN" || mata_uang_text == "JPN"){
    	    			total_yen_q1 += parseInt(tot_yen.replace(/\D/g, ""));

    	    			total_yen_q1_konversi = parseFloat(konversi("JPN","USD",total_yen_q1));
    	    		}
    	    		else if (mata_uang == "ID" || mata_uang_text == "ID"){
    	    			total_id_q1 += parseInt(tot_id.replace(/\D/g, ""));

    	    			total_id_q1_konversi = parseFloat(konversi("ID","USD",total_id_q1));
    	    		}

    	    	}

    	    	else if (req_bulan == "07" || req_bulan == "08" || req_bulan == "09"){
    	    		if (mata_uang == "USD" || mata_uang_text == "USD" ) {
    	    			total_usd_q2 += parseInt(tot_usd.replace(/\D/g, ""));
    	    		}
    	    		else if (mata_uang == "JPN" || mata_uang_text == "JPN"){
    	    			total_yen_q2 += parseInt(tot_yen.replace(/\D/g, ""));

    	    			total_yen_q2_konversi = parseFloat(konversi("JPN","USD",total_yen_q2));
    	    		}
    	    		else if (mata_uang == "ID" || mata_uang_text == "ID"){
    	    			total_id_q2 += parseInt(tot_id.replace(/\D/g, ""));

    	    			total_id_q2_konversi = parseFloat(konversi("ID","USD",total_id_q2));
    	    		}
    	    	}


    	    	else if (req_bulan == "10" || req_bulan == "11" || req_bulan == "12"){
    	    		if (mata_uang == "USD" || mata_uang_text == "USD" ) {
    	    			total_usd_q3 += parseInt(tot_usd.replace(/\D/g, ""));
    	    		}
    	    		else if (mata_uang == "JPN" || mata_uang_text == "JPN"){
    	    			total_yen_q3 += parseInt(tot_yen.replace(/\D/g, ""));

    	    			total_yen_q3_konversi = parseFloat(konversi("JPN","USD",total_yen_q3));
    	    		}
    	    		else if (mata_uang == "ID" || mata_uang_text == "ID"){
    	    			total_id_q3 += parseInt(tot_id.replace(/\D/g, ""));

    	    			total_id_q3_konversi = parseFloat(konversi("ID","USD",total_id_q3));
    	    		}
    	    	}


    	    	else if (req_bulan == "01" || req_bulan == "02" || req_bulan == "03"){
    	    		if (mata_uang == "USD" || mata_uang_text == "USD" ) {
    	    			total_usd = tot_usd.replace(/\D/g, "");
    	    			total_usd_q4 += parseInt(tot_usd.replace(/\D/g, ""));
    	    		}
    	    		else if (mata_uang == "JPN" || mata_uang_text == "JPN"){
    	    			total_yen_q4 += parseInt(tot_yen.replace(/\D/g, ""));

    	    			total_yen_q4_konversi = parseFloat(konversi("JPN","USD",total_yen_q4));
    	    		}
    	    		else if (mata_uang == "ID" || mata_uang_text == "ID"){
    	    			total_id_q4 += parseInt(tot_id.replace(/\D/g, ""));

    	    			total_id_q4_konversi = parseFloat(konversi("ID","USD",total_id_q4));
    	    		}
    	    	}


    	    	//Quarter1

    	    	totalQ1 = total_usd_q1 + total_yen_q1_konversi + total_id_q1_konversi;
    	    	budgetQ1 = $('#budget'+Q1).text();
    	    	$('#totalPembelian'+Q1).text("$"+totalQ1);

    	    	var sisaQ1 = parseFloat(budgetQ1.substr(1)) - parseFloat(totalQ1);
    	    	$('#sisaBudget'+Q1).text("$"+sisaQ1.toFixed(2));

	            //Quarter2

	            totalQ2 = total_usd_q2 + total_yen_q2_konversi + total_id_q2_konversi;
	            budgetQ2 = $('#budget'+Q2).text();
	            $('#totalPembelian'+Q2).text("$"+totalQ2);
	            
	            var sisaQ2 = parseFloat(budgetQ2.substr(1)) - parseFloat(totalQ2);
	            $('#sisaBudget'+Q2).text("$"+sisaQ2.toFixed(2));

	            //Quarter3

	            totalQ3 = total_usd_q3 + total_yen_q3_konversi + total_id_q3_konversi;
	            budgetQ3 = $('#budget'+Q3).text();
	            $('#totalPembelian'+Q3).text("$"+totalQ3);

	            var sisaQ3 = parseFloat(budgetQ3.substr(1)) - parseFloat(totalQ3);
	            $('#sisaBudget'+Q3).text("$"+sisaQ3.toFixed(2));

	            //Quarter4

	            totalQ4 = total_usd_q4 + total_yen_q4_konversi + total_id_q4_konversi;
	            budgetQ4 = $('#budget'+Q4).text();
	            $('#totalPembelian'+Q4).text("$"+totalQ4);

	            var sisaQ4 = parseFloat(budgetQ4.substr(1)) - parseFloat(totalQ4);
	            $('#sisaBudget'+Q4).text("$"+sisaQ4.toFixed(2));

	            document.getElementById('total_keseluruhan').value = konversiToUSD(curr,'USD');
	        }
	    }
	}

	function konversi(from, to, amount){

			var obj = exchange_rate;

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

	function konversiToUSD(from, to){

		var obj = exchange_rate;
		var fromrate = 0;
		var torate = 0;

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
        if (from == "JPN") {
        	hasil_konversi_yen = (total_yen / fromrate) * torate;
    	}
    	if (from == "ID") {
    		hasil_konversi_id = (total_id / fromrate) * torate;
	    }

	    var hasil= total_usd + parseFloat(hasil_konversi_yen.toFixed(2)) + parseFloat(hasil_konversi_id.toFixed(2));
    	return hasil.toFixed(2);

    	// document.getElementById('total_keseluruhan').value = total_usd + parseFloat(hasil_konversi_yen.toFixed(2)) + parseFloat(hasil_konversi_id.toFixed(2));
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

    	var divdata = $("<div id='"+no+"' class='col-md-12' style='margin-bottom : 5px'><div class='col-xs-1' style='padding:5px;'><select class='form-control select3' data-placeholder='Choose Item' name='item_code"+no+"' id='item_code"+no+"' onchange='pilihItem(this)'><option></option></select></div><div class='col-xs-2' style='padding:5px;'><input type='text' class='form-control' id='item_desc"+no+"' name='item_desc"+no+"' placeholder='Description' required=''></div><div class='col-xs-2' style='padding:5px;'><input type='text' class='form-control' id='item_spec"+no+"' name='item_spec"+no+"' placeholder='Specification' required=''></div><div class='col-xs-1' style='padding:5px;'><select class='form-control select3' id='uom"+no+"' name='uom"+no+"' data-placeholder='UOM' style='width: 100%;'><option></option>@foreach($uom as $um)<option value='{{ $um }}'>{{ $um }}</option>@endforeach</select></div><div class='col-xs-1' style='padding:5px;'><div class='input-group date'><div class='input-group-addon'><i class='fa fa-calendar' style='font-size: 10px'></i> </div><input type='text' class='form-control pull-right datepicker' id='req_date"+no+"' name='req_date"+no+"' placeholder='Tanggal' required=''></div></div> <div class='col-xs-1' style='padding: 5px'><select class='form-control select2' id='item_currency"+no+"' name='item_currency"+no+"'data-placeholder='Currency' style='width: 100%' onchange='currency(this)'><option value=''>&nbsp;</option><option value='USD'>USD</option><option value='ID'>ID</option><option value='JPN'>JPN</option></select><input type='text' class='form-control' id='item_currency_text"+no+"' name='item_currency_text"+no+"' style='display:none'></div> <div class='col-xs-1' style='padding:5px;'><div class='input-group'><span class='input-group-addon' id='ket_harga"+no+"'>?</span><input type='text' class='form-control currency' id='item_price"+no+"' name='item_price"+no+"' placeholder='Harga' data-number-to-fixed='2' data-number-stepfactor='100' required=''></div></div><div class='col-xs-1' style='padding:5px;'><input type='number' class='form-control' id='qty"+no+"' name='qty"+no+"' placeholder='Qty' onkeyup='getTotal(this.id)' required=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='amount"+no+"' name='amount"+no+"' placeholder='Total' required='' readonly></div><div class='col-xs-1' style='padding:5px;'>&nbsp;<button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button> <button type='button' onclick='tambah(\""+id+"\",\""+lop+"\"); ' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div>");

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

    		});
    	})

		// $("#"+id).select2().trigger('change');
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
		$("#item_code"+newid).attr("name","item_code"+oldid);
		$("#item_desc"+newid).attr("name","item_desc"+oldid);
		$("#item_spec"+newid).attr("name","item_spec"+oldid);
		$("#item_price"+newid).attr("name","item_price"+oldid);
		$("#qty"+newid).attr("name","qty"+oldid);
		$("#uom"+newid).attr("name","uom"+oldid);
		$("#amount"+newid).attr("name","amount"+oldid);
		$("#item_currency"+newid).attr("name","item_currency"+oldid);
		$("#item_currency_text"+newid).attr("name","item_currency_text"+oldid);
		$("#req_date"+newid).attr("name","req_date"+oldid);

		$("#item_code"+newid).attr("id","item_code"+oldid);
		$("#item_desc"+newid).attr("id","item_desc"+oldid);
		$("#item_spec"+newid).attr("id","item_spec"+oldid);
		$("#item_price"+newid).attr("id","item_price"+oldid);
		$("#qty"+newid).attr("id","qty"+oldid);
		$("#uom"+newid).attr("id","uom"+oldid);
		$("#amount"+newid).attr("id","amount"+oldid);
		$("#item_currency"+newid).attr("id","item_currency"+oldid);
		$("#item_currency_text"+newid).attr("id","item_currency_text"+oldid);
		$("#req_date"+newid).attr("id","req_date"+oldid);

		no-=1;
		var a = no -1;

		for (var i =  ids; i <= a; i++) {	
			var newid = parseInt(i) + 1;
			var oldid = newid - 1;
			$("#"+newid).attr("id",oldid);
			$("#item_code"+newid).attr("name","item_code"+oldid);
			$("#item_desc"+newid).attr("name","item_desc"+oldid);
			$("#item_spec"+newid).attr("name","item_spec"+oldid);
			$("#item_price"+newid).attr("name","item_price"+oldid);
			$("#qty"+newid).attr("name","qty"+oldid);
			$("#uom"+newid).attr("name","uom"+oldid);
			$("#amount"+newid).attr("name","amount"+oldid);
			$("#item_currency"+newid).attr("name","item_currency"+oldid);
			$("#item_currency_text"+newid).attr("name","item_currency_text"+oldid);
			$("#req_date"+newid).attr("name","req_date"+oldid);

			$("#item_code"+newid).attr("id","item_code"+oldid);
			$("#item_desc"+newid).attr("id","item_desc"+oldid);
			$("#item_spec"+newid).attr("id","item_spec"+oldid);
			$("#item_price"+newid).attr("id","item_price"+oldid);
			$("#qty"+newid).attr("id","qty"+oldid);
			$("#uom"+newid).attr("id","uom"+oldid);
			$("#amount"+newid).attr("id","amount"+oldid);
			$("#item_currency"+newid).attr("id","item_currency"+oldid);
			$("#item_currency_text"+newid).attr("id","item_currency_text"+oldid);
			$("#req_date"+newid).attr("id","req_date"+oldid);


			// alert(i)
		}
		document.getElementById(lop).value = a;

		getTotal("qty"+a);	

	}

	//Change to format uang

	function currency(elem){

		var no = elem.id.match(/\d/g);
		no = no.join("");

		var mata_uang = $('#item_currency'+no).val();
		var mata_uang_text = $('#item_currency_text'+no).val();

		if (mata_uang == "USD") {
			$('#ket_harga'+no).text("$");
			var harga = document.getElementById("item_price"+no);
			harga.addEventListener("keyup", function(e) {
				harga.value = formatUang(this.value, "");
			});
		}

		else if (mata_uang == "ID") {
			$('#ket_harga'+no).text("Rp. ");

			var harga = document.getElementById("item_price"+no);
			harga.addEventListener("keyup", function(e) {
				harga.value = formatUang(this.value, "");
			});
		}

		else if (mata_uang == "JPN") {
			$('#ket_harga'+no).text("¥");

			var harga = document.getElementById("item_price"+no);
			harga.addEventListener("keyup", function(e) {
				harga.value = formatUang(this.value, "");
			});
		}
	}

	/* Fungsi formatUang */
	function formatUang(angka, prefix) {
		var number_string = angka.replace(/[^,\d]/g, "").toString(),
		split = number_string.split(","),
		sisa = split[0].length % 3,
		rupiah = split[0].substr(0, sisa),
		ribuan = split[0].substr(sisa).match(/\d{3}/gi);

      // tambahkan titik jika yang di input sudah menjadi angka ribuan
      if (ribuan) {
      	separator = sisa ? "." : "";
      	rupiah += separator + ribuan.join(".");
      }

      rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
      return prefix == undefined ? rupiah : rupiah ? "" + rupiah : "";
  }

  function rubah(angka){
  	var reverse = angka.toString().split('').reverse().join(''),
  	ribuan = reverse.match(/\d{1,3}/g);
  	ribuan = ribuan.join('.').split('').reverse().join('');
  	return ribuan;
  }

  $('.select2').select2({
    	allowClear: true,
    	dropdownAutoWidth : true
	});

  $(function () {
  	$('.select4').select2({
  		dropdownParent: $("#tab_3"),
  		allowClear:true,
  		dropdownAutoWidth : true
  	});
  })



  function editPR(id){

		var isi = "";
		$('#modalEdit').modal("show");
	    
	    var data = {
		    id:id
		};
	      
	    $.get('{{ url("edit/purchase_requisition") }}', data, function(result, status, xhr){
	        $('#modalDetailBodyEdit').html('');
			$('#modalDetailBodyEditHeader').html('');
			$('#modalDetailBodyEditHeader2').html('');
			
			
						
			$(function () {
				$('.select6').select2({
					dropdownAutoWidth : true,
					dropdownParent: $("#"+id),
					allowClear: true
				});
			})


	    });

	}

</script>

@endsection