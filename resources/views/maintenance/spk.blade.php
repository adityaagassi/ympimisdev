@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
	}
	#master {
		font-size: 17px;
	}

	#table_after label {
		color: white;
	}

	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		padding-top: 0;
		padding-bottom: 0;
		vertical-align: middle;
		color: white;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		padding-top: 9px;
		padding-bottom: 9px;
		vertical-align: middle;
		background-color: white;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}

	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}

	#loading, #error { display: none; }

	.kedip {
		/*width: 50px;
		height: 50px;*/
		-webkit-animation: pulse 1s infinite;  /* Safari 4+ */
		-moz-animation: pulse 1s infinite;  /* Fx 5+ */
		-o-animation: pulse 1s infinite;  /* Opera 12+ */
		animation: pulse 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes pulse {
		0%, 49% {
			background-color: #00a65a;
			color: white;
		}
		50%, 100% {
			background-color: #ffffff;
			color: #444;
		}
	}

	.foto {
		opacity: 0;
		/*position: absolute;*/
		/*display: none;*/
		visibility: hidden;
		z-index: -1;
	}

	.txt_foto:hover, .txt_reset:hover {
		cursor: pointer;
	}

	/* Chrome, Safari, Edge, Opera */
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	/* Firefox */
	input[type=number] {
		-moz-appearance: textfield;
	}

</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Mohon tunggu sebentar...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<input type="hidden" id="order_no">
	<input type="hidden" id="operator_id" value="{{ $employee_id }}">
	<div class="row" style="margin-left: 1%; margin-right: 1%;">
		<div class="col-xs-12" style="padding-right: 0; padding-left: 0;">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 2%;">
				<thead>
					<tr>
						<th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 18px;" colspan="2">Operator</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:20px; width: 30%;" id="op">{{ $employee_id }}</td>
						<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;" id="op2">{{ $name }}</td>
					</tr>
				</tbody>
			</table>

			<div id="div_master">
				<table class="table table-bordered" style="width: 100%" id="table_master">
					<thead>
						<tr>
							<th width="5%">Nomor SPK</th>
							<th width="20%">Bagian</th>
							<th width="5%">Jenis Pekerjaan</th>
							<th width="50%">Deskripsi</th>
							<th width="5%">Prioritas</th>
							<th width="5%">Status</th>
							<th width="10%">Start</th>
							<th width="5%">Action</th>
						</tr>
					</thead>
					<tbody id="master">
					</tbody>
				</table>
			</div>

			<div id="div_after" style="display: none">
				<button class="btn btn-sm btn-success" id="btn_back"><i class="fa fa-arrow-left"></i>&nbsp;Kembali</button>
				<table class="table" style="width: 100%; margin-bottom: 0px" id="table_after">
					<tr>
						<td>
							<div class="row">
								<div class="col-xs-6">
									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Nomor SPK</label>
										<div class="col-xs-7" align="left">
											<input type="text" class="form-control" id="spk_detail" readonly>
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Pekerjaan</label>
										<div class="col-xs-7" align="left">
											<input type="text" class="form-control" id="pekerjaan_detail" readonly>
										</div>
									</div>
								</div>

								<div class="col-xs-6">
									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Tanggal Pengajuan</label>
										<div class="col-xs-7" align="left">
											<input type="text" class="form-control" id="tanggal_detail" readonly>
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-4" style="margin-top: 1%;">Bagian Pengaju</label>
										<div class="col-xs-7" align="left">
											<input type="text" class="form-control" id="bagian_detail" readonly>
										</div>
									</div>
								</div>
								<div class="col-xs-12">
									<div class="form-group row" align="right">
										<label class="col-xs-2" style="margin-top: 1%;">Deskripsi</label>
										<div class="col-xs-10" align="left">
											<textarea class="form-control" id="desc_detail" readonly></textarea>
										</div>
									</div>

									<hr style="margin-top: 10px; margin-bottom: 10px">
								</div>
								<div class="col-xs-12">
									<div class="form-group row" align="right">
										<label class="col-xs-2" style="margin-top: 1%;">Penyebab<span class="text-red">*</span></label>
										<div class="col-xs-8" align="left">
											<textarea class="form-control" id="penyebab_detail" placeholder="Isikan Penyebab Kerusakan"></textarea>
										</div>
										<div class="col-xs-1">
											<img src="" id="img_penyebab_1" style="width: 60%">
											<input type="file" id="foto_penyebab_1" style="display: none">
											<label class="btn btn-success btn-xs" id="btn_penyebab_1" for="foto_penyebab_1"><i class="fa fa-plus"></i></label>
										</div>

										<div class="col-xs-1">
											<img src="" id="img_penyebab_2" style="width: 60%">
											<input type="file" id="foto_penyebab_2" style="display: none">
											<label class="btn btn-success btn-xs" id="btn_penyebab_2" for="foto_penyebab_2"><i class="fa fa-plus"></i></label>
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-2" style="margin-top: 1%;">Penanganan<span class="text-red">*</span></label>
										<div class="col-xs-8" align="left">
											<textarea class="form-control" id="penanganan_detail" placeholder="Isikan Penanganan yang dilakukan"></textarea>
										</div>
										<div class="col-xs-1">
											<img src="" id="img_penanganan_1" style="width: 60%">
											<input type="file" id="foto_penanganan_1" style="display: none">
											<label class="btn btn-success btn-xs" id="btn_penanganan_1" for="foto_penanganan_1"><i class="fa fa-plus"></i></label>
										</div>

										<div class="col-xs-1">
											<img src="" id="img_penanganan_2" style="width: 60%">
											<input type="file" id="foto_penanganan_2" style="display: none">
											<label class="btn btn-success btn-xs" id="btn_penanganan_2" for="foto_penanganan_2"><i class="fa fa-plus"></i></label>
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-2" style="margin-top: 1%;">Pencegahan<span class="text-red">*</span></label>
										<div class="col-xs-8" align="left">
											<textarea class="form-control" id="pencegahan_detail" placeholder="Isikan Pencegahan Agar tidak terjadi lagi"></textarea>
										</div>

										<div class="col-xs-1">
											<img src="" id="img_pencegahan_1" style="width: 60%">
											<input type="file" id="foto_pencegahan_1" style="display: none">
											<label class="btn btn-success btn-xs" id="btn_pencegahan_1" for="foto_pencegahan_1"><i class="fa fa-plus"></i></label>
										</div>

										<div class="col-xs-1">
											<img src="" id="img_pencegahan_2" style="width: 60%">
											<input type="file" id="foto_pencegahan_2" style="display: none">
											<label class="btn btn-success btn-xs" id="btn_pencegahan_2" for="foto_pencegahan_2"><i class="fa fa-plus"></i></label>
										</div>
									</div>

									<div class="form-group row" align="right">
										<label class="col-xs-2" style="margin-top: 1%; margin-bottom: 0px">Spare Part</label>
										<div class="col-xs-5" align="left">
											<select class="form-control part" data-placeholder="Pilih Part yang digunakan" style="width: 100%;" id="part_detail_1" onchange="get_stock(this)">
												<option value=""></option>
											</select>
										</div>
										<div class="col-xs-2" align="left">
											<div class="input-group">
												<span class="input-group-addon"><b>Stock</b></span>
												<input type="number" id="stock_1" class="form-control" style="text-align: center;" readonly>
											</div>
										</div>
										<div class="col-xs-2" align="left">
											<div class="input-group">
												<span class="input-group-addon"><b>Qty</b></span>
												<!-- <input type="number" id="qty_1" class="form-control"> -->
												<input id="qty_1" style="text-align: center;" type="number" class="form-control numpad" value="0" placeholder="Qty">
											</div>
										</div>
										<div class="col-xs-1" align="left">
											<button class="btn btn-success spare_part" onclick="add_part()" id="btn_1"><i class="fa fa-plus"></i></button>
										</div>

										<div id="sp_other">
										</div>

										<!-- SPARE PART LAIN -->
										<label class="col-xs-2" style="margin-top: 5px">Spare Part lain</label>
										<div class="col-xs-5" align="left" style="margin-top: 5px">
											<input type="text" class="form-control" placeholder="Isikan Spare part (Apabila tidak ada dalam daftar)" id="spare_part_lain">
										</div>
									</div>
								<!-- 	<div class="form-group row" align="right">
										<label class="col-xs-2" style="margin-top: 1%; margin-bottom: 0px">Spare Part Lain</label>
										<div class="col-xs-5" align="left">
											<select class="form-control part" data-placeholder="Pilih Part yang digunakan" style="width: 100%;" id="part_detail_1" onchange="get_stock(this)">
												<option value=""></option>
											</select>
										</div>
										<div class="col-xs-2" align="left">
											<div class="input-group">
												<span class="input-group-addon"><b>Nama Spare Part</b></span>
												<input type="number" id="part_lain_1" class="form-control" style="text-align: center;" readonly>
											</div>
										</div>
										<div class="col-xs-1" align="left">
											<button class="btn btn-success spare_part" onclick="add_part_lain()" id="btn_lain_1"><i class="fa fa-plus"></i></button>
										</div>
									</div> -->

									<div class="form-group row" align="right">
										<label class="col-xs-2" style="margin-top: 1%;">Foto<span class="text-red">*</span></label>
										<div class="col-xs-10" align="left">
											<div id="box">
												<img src="" id="profile-img1" style="max-width: 33%" />
												<img src="" id="profile-img2" style="max-width: 33%" />
											</div>
											<label class="text-red pull-right txt_reset" onclick="reset()"><i class="fa fa-refresh"> Reset</i></label>
											<label for="foto1" class="text-green txt_foto" id="txt_foto1"><i class="fa fa-plus"></i> Tambah</label>
											<input type="file" name="foto" id="foto1" class="foto">

											<label for="foto2" class="text-green txt_foto" id="txt_foto2"><i class="fa fa-plus"></i> Tambah</label>
											<input type="file" name="foto" id="foto2" class="foto">

										</div>
									</div>

									<!-- <div class="form-group row" align="right" id="no_part" style="display: none">
										<hr style="margin-top: 10px; margin-bottom: 10px">
										<label class="col-xs-2" style="margin-top: 1%;">Spare Part</label>
										<div class="col-xs-5" align="left">
											<select id="part_detail_1" class="form-control select3" data-placeholder="Pilih Spare Part yang Digunakan" style="width: 100%">
											</select>
										</div>
										<div class="col-xs-3" align="left">
											<div class="input-group">
												<span class="input-group-addon"><b>Qty</b></span>
												<input type="number" id="part_qty_1" class="form-control">
											</div>
										</div>
										<div class="col-xs-2" align="left">
											<button class="btn btn-success spare_part" onclick="add_part()" id="btn_1"><i class="fa fa-plus"></i></button>
										</div>

										
									</div> -->
								</div>

								<div class="col-xs-12">
									<button type="button" class="btn btn-warning pull-left" onclick="pending_action(this, 'Vendor')" id="btn_vendor" style="margin-right: 3px"><i class="fa fa-exclamation-circle"></i> Vendor</button>
									<button type="button" class="btn btn-primary pull-left" onclick="pending_action(this, 'masih WJO')" id="btn_wjo" style="margin-right: 3px"><i class="fa fa-exclamation-circle"></i> masih WJO</button>
									<button type="button" class="btn btn-primary pull-left" onclick="pending_action(this, 'Part Tidak Ada')" id="btn_no_part" style="margin-right: 3px"><i class="fa fa-exclamation-circle"></i> Part Tidak Ada</button>
									<button type="button" style='margin-right:3px' class="btn btn-primary pull-left" onclick="pending_action(this, 'Call Friend')" id="btn_friend"><i class="fa fa-exclamation-circle"></i> Call Friend</button>
									<button class='btn btn-danger pull-left' onclick='jeda(this)'><i class='fa fa-pause'></i>&nbsp; Jeda</button>

									<!-- <div class="btn-group">
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu" style="z-index: 5000">
											<li><a href="#">Dropdown link</a></li>
											<li><a href="#">Dropdown link</a></li>
										</ul>
									</div> -->

									<button type="button" class="btn btn-success pull-left" style="display: none; margin-right: 5px" onclick="pending_action(this, 'No Part')" id="btn_no_part_yes"><i class="fa fa-check"></i> YES</button>
									<button type="button" class="btn btn-danger pull-left" style="display: none" onclick="pending_action(this, 'No Part')" id="btn_no_part_no"><i class="fa fa-close"></i> NO</button>

									<button type="button" class="btn btn-success pull-right" onclick="postFinish()" id="btn_selesai"><i class="fa fa-thumbs-up"></i>SPK Selesai</button>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</div>


		</div>
	</div>

	<div class="modal fade" id="modalWork" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #3c8dbc;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Detail SPK</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-6">
							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Nomor SPK</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="spk_work" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Pekerjaan</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="pekerjaan_work" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Prioritas</label>
								<div class="col-xs-7" align="left" id="prioritas_work">
								</div>
							</div>
						</div>

						<div class="col-xs-6">
							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Tanggal Pengajuan</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="tanggal_work" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Nama Pengajuan</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="nama_work" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Bagian Pengaju</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="bagian_work" readonly>
								</div>
							</div>
						</div>

						<div class="col-xs-12">
							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Tanggal Target</label>
								<div class="col-xs-3" align="left">
									<input type="text" class="form-control" id="target_work" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Nama Mesin</label>
								<div class="col-xs-3" align="left">
									<input type="text" class="form-control" id="nama_mesin" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Deskripsi Pekerjaan</label>
								<div class="col-xs-9" align="left">
									<textarea class="form-control" id="desc_work" readonly></textarea>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Catatan Safety</label>
								<div class="col-xs-9" align="left">
									<textarea class="form-control" id="safety_work" readonly></textarea>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Lampiran</label>
								<div class="col-xs-9" align="left" id="alamat_lampiran">
								</div>
							</div>
						</div>

						<div class="col-xs-12">
							<button type="button" class="btn btn-danger pull-left" data-dismiss='modal'><i class="fa fa-close"></i> Close</button>

							<button type="button" class="btn btn-primary pull-right" style="display: none" onclick="startWork()" id="btn_work"><i class="fa fa-wrench"></i> Kerjakan</button>

							<button type="button" class="btn btn-warning pull-right" style="display: none" onclick="startPending()" id="btn_resume"><i class="fa fa-wrench"></i> Lanjutkan</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="scanModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title text-center"><b>SCAN QR HERE</b></h4>
				</div>
				<div class="modal-body">
					<div id='scanner' class="col-xs-12">
						<div class="col-xs-12">
							<div id="loadingMessage">
								🎥 Unable to access video stream (please make sure you have a webcam enabled)
							</div>
							<canvas style="width: 100%;" id="canvas" hidden></canvas>
							<div id="output" hidden>
								<div id="outputMessage">No QR code detected.</div>
							</div>
						</div>									
					</div>

					<p style="visibility: hidden;">camera</p>
					<input type="hidden" id="employee">
				</div>
			</div>
		</div>
	</div>

	<!-- <div class="modal fade" id="modalAfterWork" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #3c8dbc;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Laporan SPK</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-6">
							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Nomor SPK</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="spk_detail" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Pekerjaan</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="pekerjaan_detail" readonly>
								</div>
							</div>
						</div>

						<div class="col-xs-6">
							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Tanggal Pengajuan</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="tanggal_detail" readonly>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-4" style="margin-top: 1%;">Bagian Pengaju</label>
								<div class="col-xs-7" align="left">
									<input type="text" class="form-control" id="bagian_detail" readonly>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Deskripsi</label>
								<div class="col-xs-10" align="left">
									<textarea class="form-control" id="desc_detail" readonly></textarea>
								</div>
							</div>
							
							<hr style="margin-top: 10px; margin-bottom: 10px">
						</div>
						<div class="col-xs-12">
							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Penyebab<span class="text-red">*</span></label>
								<div class="col-xs-10" align="left">
									<textarea class="form-control" id="penyebab_detail" placeholder="Isikan Penyebab Kerusakan"></textarea>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Penanganan<span class="text-red">*</span></label>
								<div class="col-xs-10" align="left">
									<textarea class="form-control" id="penanganan_detail" placeholder="Isikan Penanganan yang dilakukan"></textarea>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Spare Part</label>
								<div class="col-xs-10" align="left">
									<select class="form-control" multiple="" data-placeholder="Select a State" style="width: 100%;" id="part_detail">
										<option value=""></option>
									</select>
								</div>
							</div>

							<div class="form-group row" align="right">
								<label class="col-xs-2" style="margin-top: 1%;">Foto</label>
								<div class="col-xs-10" align="left">
									<div id="box">
										<img src="" id="profile-img1" style="max-width: 33%" />
										<img src="" id="profile-img2" style="max-width: 33%" />
										<img src="" id="profile-img3" style="max-width: 33%" />
									</div>
									<label class="text-red pull-right txt_reset" onclick="reset()"><i class="fa fa-refresh"> Reset</i></label>
									<label for="foto1" class="text-green txt_foto" id="txt_foto1"><i class="fa fa-plus"></i> Tambah</label>
									<input type="file" name="foto" id="foto1" class="foto">

									<label for="foto2" class="text-green txt_foto" id="txt_foto2"><i class="fa fa-plus"></i> Tambah</label>
									<input type="file" name="foto" id="foto2" class="foto">

									<label for="foto3" class="text-green txt_foto" id="txt_foto3"><i class="fa fa-plus"></i> Tambah</label>
									<input type="file" name="foto" id="foto3" class="foto">
								</div>
							</div>

							<div class="form-group row" align="right" id="no_part" style="display: none">
								<hr style="margin-top: 10px; margin-bottom: 10px">
								<label class="col-xs-2" style="margin-top: 1%;">Spare Part</label>
								<div class="col-xs-5" align="left">
									<select id="part_detail_1" class="form-control select3" data-placeholder="Pilih Spare Part yang Digunakan" style="width: 100%">
									</select>
								</div>
								<div class="col-xs-3" align="left">
									<div class="input-group">
										<span class="input-group-addon"><b>Qty</b></span>
										<input type="number" id="part_qty_1" class="form-control">
									</div>
								</div>
								<div class="col-xs-2" align="left">
									<button class="btn btn-success spare_part" onclick="add_part()" id="btn_1"><i class="fa fa-plus"></i></button>
								</div>

								<div id="sp_other">
								</div>
							</div>
						</div>

						<div class="col-xs-12">
							<button type="button" class="btn btn-warning pull-left" onclick="pending_action(this, 'Vendor')" id="btn_vendor" style="margin-right: 3px"><i class="fa fa-exclamation-circle"></i> Vendor</button>
							<button type="button" class="btn btn-warning pull-left" onclick="pending_action(this, 'masih WJO')" id="btn_wjo" style="margin-right: 3px"><i class="fa fa-exclamation-circle"></i> masih WJO</button>
							<button type="button" class="btn btn-primary pull-left" onclick="noPart()" id="btn_no_part"><i class="fa fa-exclamation-circle"></i> Part Tidak Ada</button>

							<button type="button" class="btn btn-success pull-left" style="display: none; margin-right: 5px" onclick="pending_action(this, 'No Part')" id="btn_no_part_yes"><i class="fa fa-check"></i> YES</button>
							<button type="button" class="btn btn-danger pull-left" style="display: none" onclick="pending_action(this, 'No Part')" id="btn_no_part_no"><i class="fa fa-close"></i> NO</button>

							<button type="button" class="btn btn-success pull-right" onclick="postFinish()" id="btn_selesai"><i class="fa fa-thumbs-up"></i> Selesai</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div> -->
</section>

@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jquery.numpad.js") }}"></script>
<script src="{{ url("js/jsQR.js")}}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	var start_working = [];
	var part_list = [];
	var no = 1;
	var desc_new = [];

	jQuery(document).ready(function() {
		$('.select3').select2({
			dropdownParent: $('#modalAfterWork'),
			allowClear: true,
		});

		$("#part_detail_1").select2({
			allowClear: true,
			minimumInputLength: 3
		});

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		get_spk();
		get_parts();
		$("#txt_foto2").hide();
		$("#txt_foto3").hide();
	});

	$("#btn_back").click(function() {
		$("#div_master").show();
		$("#div_after").hide();
	});

	function get_spk() {
		$("#master").empty();
		var body = "";

		$.get('{{ url("fetch/maintenance/spk") }}', function(result, status, xhr){
			$.each(result.datas, function(index, value){
				body += "<tr>";
				body += "<td>"+value.order_no+"</td>";
				body += "<td>"+value.section+"</td>";
				body += "<td>"+value.type+"</td>";
				body += "<td>"+value.description+"</td>";

				if(value.priority == 'Urgent'){
					var priority = '<span style="font-size: 13px;" class="label label-danger">Urgent</span>';
				}else{
					var priority = '<span style="font-size: 13px;" class="label label-default">Normal</span>';
				}
				body += "<td>"+priority+"</td>";
				body += "<td>"+value.process_name+"<br><span>"+(value.stat || '')+"</span></td>";

				op = [];
				var start_actual = "";
				var stat = 0;

				// $.each(result.proses_log, function(index2, value2){
				// 	if (value.order_no == value2.order_no) {
				// 		if (value2.operator_id.toUpperCase() == "{{ Auth::user()->username }}".toUpperCase()) {
				// 			op = [];
				// 			op.push(value2.start_actual);
				// 			start_actual = value2.start_actual;
				// 			stat = 1;
				// 		} else {
				// 			if (stat == 0) {
				// 				op.push(value2.name);
				// 			}
				// 		}

				// 	}
				// })

				var op = [];

				$.each(result.op_list, function(index2, value2){
					if (value2.order_no == value.order_no) {
						// if (value2.op_name) {}
						var op = value2.op_name.split(',');
						if (value.start_actual) {
							body += "<td>"+value.start_actual+"<br>";
							$.each(op, function(index3, value3){
								body += "<span class='label label-success'>"+value3+"</span><br>";
							})
							body += "</td>";
						} else {
							body += "<td>";
							$.each(op, function(index3, value3){
								body += "<span class='label label-success'>"+value3+"</span><br>";
							})
							body += "</td>";
						}
					}
				})


				if (value.start_actual != null) {
					if (value.remark == '5') {
						body += "<td><button class='btn btn-warning' onclick='modalWork(\""+value.order_no+"\",\""+value.type+" - "+value.category+"\",\""+value.request_date+"\",\""+value.section+"\",\""+value.name.replace("'","")+"\",\""+value.target_date+"\",\""+value.safety_note+"\",\""+value.priority+"\", \"rework\", "+index+", \""+value.machine_desc+"\", \""+value.att+"\", \""+value.machine_remark+"\")'><i class='fa fa-rocket'></i>&nbsp; Lanjutkan</button></td>";
						desc_new.push(value.description);
					} else if (value.remark == '9') {
						body += "<td><button class='btn btn-warning' onclick='modalWork(\""+value.order_no+"\",\""+value.type+" - "+value.category+"\",\""+value.request_date+"\",\""+value.section+"\",\""+value.name.replace("'","")+"\",\""+value.target_date+"\",\""+value.safety_note+"\",\""+value.priority+"\", \"rework\", "+index+", \""+value.machine_desc+"\", \""+value.att+"\", \""+value.machine_remark+"\")'><i class='fa fa-play'></i>&nbsp; Resume</button></td>";
						desc_new.push(value.description);
					} else {
						body += "<td><button class='btn btn-success' onclick='modalAfterWork(\""+value.order_no+"\",\""+$("#op").text()+"\",\""+value.type+" - "+value.category+"\",\""+value.request_date+"\",\""+value.section+"\", "+index+")'><i class='fa fa-file'></i>&nbsp; Buat Laporan</button></td>";
						desc_new.push(value.description);

					}
				} else {

					body += "<td><button class='btn btn-primary' onclick='modalWork(\""+value.order_no+"\",\""+value.type+" - "+value.category+"\",\""+value.request_date+"\",\""+value.section+"\",\""+value.name.replace("'","")+"\",\""+value.target_date+"\",\""+value.safety_note+"\",\""+value.priority+"\", \"work\", "+index+", \""+value.machine_desc+"\", \""+value.att+"\", \""+value.machine_remark+"\")'><i class='fa fa-gears'></i>&nbsp; Kerjakan</button></td>";
					desc_new.push(value.description);
				}

				body += "</tr>";
			})
			$('#table_master').DataTable().clear();
			$('#table_master').DataTable().destroy();

			$("#master").append(body);

			var table = $('#table_master').DataTable({
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
					]
				},
				// 'order': [5, 'desc'],
				'paging': true,
				'lengthChange': true,
				'searching': true,
				'ordering': false,
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
			});

				// setTime();
				// setInterval(setTime, 1000);
			})
}


function setTime() {
	for (var i = 0; i < start_working.length; i++) {
		if (start_working[i][0] != "") {
			if (start_working[i][1] != "") {
				var duration = diff_seconds(start_working[i][1], start_working[i][0]);
				document.getElementById("hours"+i).innerHTML = pad(parseInt(duration / 3600));
				document.getElementById("minutes"+i).innerHTML = pad(parseInt((duration % 3600) / 60));
				document.getElementById("seconds"+i).innerHTML = pad(duration % 60);
			} else {
				var duration = diff_seconds(new Date(), start_working[i][0]);
				document.getElementById("hours"+i).innerHTML = pad(parseInt(duration / 3600));
				document.getElementById("minutes"+i).innerHTML = pad(parseInt((duration % 3600) / 60));
				document.getElementById("seconds"+i).innerHTML = pad(duration % 60);
			}
		}
	}
}

function modalWork(order_no, pekerjaan, request_date, bagian, nama, target_date, safety_note, priority, stat, index, machine_desc, att, machine_other) {
	if (stat == "work") {
		$("#btn_work").show();
		$("#btn_resume").hide();
	} else {
		$("#btn_resume").show();
		$("#btn_work").hide();
	}

	$("#modalWork").modal('show');

	$("#spk_work").val(order_no);
	$("#pekerjaan_work").val(pekerjaan);
	$("#tanggal_work").val(request_date);
	$("#alamat_lampiran").empty();
	$("#alamat_lampiran").append("<a href='{{ url('maintenance/spk_att/') }}/"+att+"' target='_blank'>"+att+"</a>");


	if(priority == 'Urgent'){
		var prioritas = '<span style="font-size: 13px;" class="label label-danger">Urgent</span>';
	}else{
		var prioritas = '<span style="font-size: 13px;" class="label label-default">Normal</span>';
	}

	$("#prioritas_work").html(prioritas);
	$("#bagian_work").val(bagian);
	$("#nama_work").val(nama);
	$("#target_work").val(target_date);
	$("#nama_mesin").val(machine_desc+" | "+machine_other);

	$("#desc_work").text(desc_new[index]);

	if (safety_note != 'null') {
		$("#safety_work").text(safety_note);
	}
}

function startWork() {
	var data = {
		order_no : $("#spk_work").val()
	};

	$.get('{{ url("work/maintenance/spk") }}', data, function(result, status, xhr){
		openSuccessGritter('Success', '');

		$("#modalWork").modal('hide');
		get_spk();
	})
}

		// function changepart(elem) {
		// 	// console.log($(elem).val());
		// 	var ido = $(elem).attr("id");
		// 	tmp_ido = ido.split("_");

		// 	status = "";

		// 	$.each(part_list, function(index, value){
		// 		if (value.part_number == $(elem).val()) {
		// 			status = value.stock;
		// 		}
		// 	})
		// }

		function modalAfterWork(order_no, operator_id, pekerjaan, request_date, bagian, index) {
			$("#div_after").show();
			$("#div_master").hide();

			// $("#modalAfterWork").modal('show');

			$("#spk_detail").val(order_no);
			$("#pekerjaan_detail").val(pekerjaan);
			$("#tanggal_detail").val(request_date);
			$("#bagian_detail").val(bagian);
			$("#desc_detail").text(desc_new[index]);

		}

		function postFinish() {
			var penyebab = $("#penyebab_detail").val();
			var penanganan = $("#penanganan_detail").val();
			var pencegahan = $("#pencegahan_detail").val();
			var spk_detail = $("#spk_detail").val();
			var pekerjaan_detail = $("#pekerjaan_detail").val();

			if (penyebab == "" || penanganan == "" || pencegahan == "") {
				if (pencegahan == "" && (pekerjaan_detail.split(' - ')[0] == 'Perbaikan' || pekerjaan_detail.split(' - ')[0] == 'Penggantian')) {
					openErrorGritter('Error', 'Ada Kolom yang Kosong');
					return false;
				} else if(pekerjaan_detail.split(' - ')[0] != 'Perbaikan' || pekerjaan_detail.split(' - ')[0] != 'Penggantian') {
					openErrorGritter('Error', 'Ada Kolom yang Kosong');
					return false;
				}
			}

			if ($("#img_penyebab_1").attr("src") == "" || $("#img_penanganan_1").attr("src") == "" || $("#img_pencegahan_1").attr("src")  == "") {
				if ($("#img_pencegahan_1").attr("src")  == "" && (pekerjaan_detail.split(' - ')[0] == 'Perbaikan' || pekerjaan_detail.split(' - ')[0] == 'Penggantian')) {
					openErrorGritter('Error', 'Foto Harap diisi');
					return false;
				} else if(pekerjaan_detail.split(' - ')[0] != 'Perbaikan' || pekerjaan_detail.split(' - ')[0] != 'Penggantian') {
					openErrorGritter('Error', 'Foto Harap diisi');
					return false;	
				}
			}

			var foto = [];
			var part = [];

			$('#box > img').each(function () {
				foto.push($(this).attr("src"));
			});

			$('.part').each(function(index, value) {
				ids = $(this).attr("id");
				tmp_ids = ids.split('_')[2];

				if ($("#part_detail_"+tmp_ids).val() != "") {
					part.push({'part_number' : $("#part_detail_"+tmp_ids).val(), 'qty' : $("#qty_"+tmp_ids).val()});
				}
			});

			var part_lain = $("#spare_part_lain").val();

			var data = {
				order_no : spk_detail,
				penyebab : penyebab,
				penanganan : penanganan,
				pencegahan : pencegahan,
				spare_part : part,
				foto : foto,
				other_part : part_lain,
				foto_penyebab : [$("#img_penyebab_1").attr("src"), $("#img_penyebab_2").attr("src")],
				foto_penanganan : [$("#img_penanganan_1").attr("src"), $("#img_penanganan_2").attr("src")],
				foto_pencegahan : [$("#img_pencegahan_1").attr("src"), $("#img_pencegahan_2").attr("src")]
			}


			console.log(data);
			// return false;

			if ($("#profile-img1").attr("src") != "" || $("#profile-img2").attr("src") != "") {
				$("#loading").show();
				$.post('{{ url("report/maintenance/spk") }}', data, function(result, status, xhr){
					if (result.status) {
						$("#loading").hide();
						openSuccessGritter('Success', 'SPK Terselesaikan');
						$("#div_master").show();
						$("#div_after").hide();
						$("#part_detail_1").val("");
						$("#qty_1").val("");
						$("#penyebab_detail").empty();
						$("#penanganan_detail").empty();
						$("#sp_other").empty();
						reset();
						get_spk();
					} else {
						openErrorGritter('Error', result.message);
					}
				})
			} else {
				openErrorGritter('Gagal', 'Foto Harap Diisi');
				return false;
			}
		}

		function get_parts() {
			var option_part = "";
			option_part += '<option></option>';

			$.get('{{ url("fetch/maintenance/inven/list") }}', function(result, status, xhr){
				$.each(result.inventory, function(index, value){
					spec = value.specification.replace(/['"]+/g, '');
					part_name = value.part_name.replace(/['"]+/g, '');

					part_list.push({'part_number' : value.part_number,'spare_part' : part_name+' - '+spec, 'stock' : value.stock});

					option_part += "<option value='"+value.part_number+"'>"+part_name+" - "+spec+"</option>";
				});

				$("#part_detail_1").append(option_part);

				$("#part_detail_1").select2({
					allowClear: true,
					// minimumInputLength: 3
				});

			})
		}

		function add_part() {
			no++;
			var input_part = "";
			var option_part = "";

			input_part += "<div id='row_"+no+"' class='spare_part'>";
			input_part += "<label class='col-xs-2' style='margin-top: 1%;'></label>";
			input_part += "<div class='col-xs-5' align='left' style='margin-top: 1%;'>";
			input_part += '<select id="part_detail_'+no+'" class="form-control part" data-placeholder="Pilih Part yang digunakan" style="width: 100%" onchange="get_stock(this)"></select></div>';
			input_part += '<div class="col-xs-2" align="left" style="margin-top: 1%;">';
			input_part += '<div class="input-group">';
			input_part += '<span class="input-group-addon"><b>Stock</b></span>'
			input_part += '<input type="number" id="stock_'+no+'" class="form-control" style="text-align: center;" readonly></div></div>';
			input_part += '<div class="col-xs-2" align="left" style="margin-top: 1%;">';
			input_part += '<div class="input-group">';
			input_part += '<span class="input-group-addon"><b>Qty</b></span>';
			input_part += '<input id="qty_'+no+'" style="text-align: center;" type="number" class="form-control numpad" value="0" placeholder="Qty">';
			input_part += '</div></div>';
			input_part += '<div class="col-xs-1" align="left" style="margin-top: 1%;">';
			input_part += '<button class="btn btn-danger" onclick="remove_part(this)"><i class="fa fa-minus"></i></button></div></div>';

			option_part += '<option></option>';
			$.each(part_list, function(index, value){
				option_part += "<option value='"+value.part_number+"'>"+value.spare_part+"</option>";
			});

			$("#sp_other").append(input_part);
			$("#part_detail_"+no).append(option_part);

			$(function () {
				$('.part').select2({
					allowClear: true,
					// minimumInputLength: 3
				});
			})

			$('.numpad').numpad({
				hidePlusMinusButton : true,
				decimalSeparator : '.'
			});
		}

		function remove_part(elem) {
			var dd = $(elem).parent().parent().attr("id");
			// console.log(dd);
			$("#"+dd).remove();
		}

		function pending_action(elem, stat) {
			ido = $(elem).attr('id');

			var penyebab = $("#penyebab_detail").val();
			var penanganan = $("#penanganan_detail").val();
			var spk_detail = $("#spk_detail").val();

			if (penyebab == "" || penanganan == "") {
				openErrorGritter('Error', 'Ada Kolom yang Kosong');
				return false;
			}

			var foto = [];
			var part = [];

			$('#box > img').each(function () {
				foto.push($(this).attr("src"));
			});

			if ($("#profile-img1").attr("src") == "") {
				openErrorGritter('Error', 'Foto Harap Diisi');
				return false;
			}

			$('.part').each(function(index, value) {
				ids = $(this).attr("id");
				tmp_ids = ids.split('_')[2];

				if ($("#part_detail_"+tmp_ids).val() != "") {
					part.push({'part_number' : $("#part_detail_"+tmp_ids).val(), 'qty' : $("#qty_"+tmp_ids).val()});
				}
			});

			if (confirm("Apakah Anda Yakin Pending SPK '"+stat+"' ?")) {
				var data = {
					order_no : spk_detail,
					penyebab : penyebab,
					penanganan : penanganan,
					spare_part : part,
					foto : foto,
					status : stat,
					other_part : $("#spare_part_lain").val()
				}

				if (ido != "btn_no_part") {
					$.post('{{ url("report/maintenance/spk/pending") }}', data, function(result, status, xhr){
						if (result.status) {
							openSuccessGritter('Success', 'SPK Status Pending');
							$("#div_master").show();
							$("#div_after").hide();
							get_spk();
						} else {
							openErrorGritter('Error', result.message);
						}
					})
				} else {
					if (part.length > 0 || $("#spare_part_lain").val()) {
						$.post('{{ url("report/maintenance/spk/pending") }}', data, function(result, status, xhr){
							if (result.status) {
								openSuccessGritter('Success', 'SPK Status Pending');
								$("#div_master").show();
								$("#div_after").hide();
								get_spk();
							} else {
								openErrorGritter('Error', result.message);
							}
						})
					} else {
						openErrorGritter('Gagal', 'Part Harus Diisi');
					}
				}
			}
		}

		function readURL(input) {

			if (input.files && input.files[0]) {

				var reader = new FileReader();

				num = input.id.replace(/[^\d]+/, '');

				reader.onload = function (e) {

					$('#profile-img'+num).attr('src', e.target.result);

				}

				reader.readAsDataURL(input.files[0]);
			}
		}

		$("#foto1, #foto2, #foto3").change(function(){
			readURL(this);

			$(".txt_foto").hide();

			var num = $(this).attr('id').replace(/[^\d]+/, '');			

			if ($("#profile-img"+(parseInt(num)+1)).attr("src") == "") {
				$("#txt_foto"+(parseInt(num)+1)).show();
			}
		});

		$("#profile-img1").click(function() {
			$("input[id='foto1']").click();
		});

		$("#profile-img2").click(function() {
			$("input[id='foto2']").click();
		});

		function reset() {
			$("#profile-img1").attr("src", "");
			$("#profile-img2").attr("src", "");

			$(".txt_foto").hide();
			$("#txt_foto1").show();
		}

		$("#foto_penyebab_1, #foto_penyebab_2").change(function(){
			if (this.files && this.files[0]) {

				var reader = new FileReader();

				num = this.id.replace(/[^\d]+/, '');

				reader.onload = function (e) {

					$('#img_penyebab_'+num).attr('src', e.target.result);

				}

				reader.readAsDataURL(this.files[0]);
			}
		});

		$("#foto_penanganan_1, #foto_penanganan_2").change(function(){
			if (this.files && this.files[0]) {

				var reader = new FileReader();

				num = this.id.replace(/[^\d]+/, '');

				reader.onload = function (e) {

					$('#img_penanganan_'+num).attr('src', e.target.result);

				}

				reader.readAsDataURL(this.files[0]);
			}
		});


		$("#foto_pencegahan_1, #foto_pencegahan_2").change(function(){
			if (this.files && this.files[0]) {

				var reader = new FileReader();

				num = this.id.replace(/[^\d]+/, '');

				reader.onload = function (e) {

					$('#img_pencegahan_'+num).attr('src', e.target.result);

				}

				reader.readAsDataURL(this.files[0]);
			}
		});

		function get_stock(elem) {
			var num = $(elem).attr('id').split("_")[2];

			$.each(part_list, function(index, value){
				if ($(elem).val() == value.part_number) {
					$("#stock_"+num).val(value.stock);
					return false;
				}
			})
		}

		function jeda(elem) {
			var penyebab = $("#penyebab_detail").val();
			var penanganan = $("#penanganan_detail").val();
			var spk_detail = $("#spk_detail").val();

			if (penyebab == "" || penanganan == "") {
				openErrorGritter('Error', 'Ada Kolom yang Kosong');
				return false;
			}

			var foto = [];
			var part = [];

			$('#box > img').each(function () {
				foto.push($(this).attr("src"));
			});

			if ($("#profile-img1").attr("src") == "") {
				openErrorGritter('Error', 'Foto Harap Diisi');
				return false;
			}

			$('.part').each(function(index, value) {
				ids = $(this).attr("id");
				tmp_ids = ids.split('_')[2];

				if ($("#part_detail_"+tmp_ids).val() != "") {
					part.push({'part_number' : $("#part_detail_"+tmp_ids).val(), 'qty' : $("#qty_"+tmp_ids).val()});
				}
			});

			if (confirm("Apakah Anda Yakin Jeda SPK Ini ?")) {
				var data = {
					order_no : spk_detail,
					penyebab : penyebab,
					penanganan : penanganan,
					spare_part : part,
					foto : foto
				}

				

				$.post('{{ url("report/maintenance/spk/jeda") }}', data, function(result, status, xhr){
					if (result.status) {
						openSuccessGritter('Success', 'SPK Status Paused');
						$("#div_master").show();
						$("#div_after").hide();
						get_spk();
					} else {
						openErrorGritter('Error', result.message);
					}
				})
			}
		}

		function startPending() {
			var data = {
				order_no : $("#spk_work").val()
			};

			$.get('{{ url("rework/maintenance/spk") }}', data, function(result, status, xhr){
				openSuccessGritter('Success', '');

				$("#modalWork").modal('hide');
				get_spk();
			})
		}

		function modalScan(order_no) {
			$("#scanModal").modal('show');

			// order_no = $("#order_no_ket").val();
			showCheck(order_no);
		}

		function stopScan() {
			$('#scanModal').modal('hide');
		}

		function videoOff() {
			vdo.pause();
			vdo.src = "";
			vdo.srcObject.getTracks()[0].stop();
		}

		// $( "#scanModal" ).on('shown.bs.modal', function(){
		// 	showCheck();
		// });

		$('#scanModal').on('hidden.bs.modal', function () {
			videoOff();
		});

		function showCheck(order_no) {
			var video = document.createElement("video");
			vdo = video;
			var canvasElement = document.getElementById("canvas");
			var canvas = canvasElement.getContext("2d");
			var loadingMessage = document.getElementById("loadingMessage");

			var outputContainer = document.getElementById("output");
			var outputMessage = document.getElementById("outputMessage");

			function drawLine(begin, end, color) {
				canvas.beginPath();
				canvas.moveTo(begin.x, begin.y);
				canvas.lineTo(end.x, end.y);
				canvas.lineWidth = 4;
				canvas.strokeStyle = color;
				canvas.stroke();
			}

			navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
				video.srcObject = stream;
				video.setAttribute("playsinline", true);
				video.play();
				requestAnimationFrame(tick);
			});

			function tick() {
				loadingMessage.innerText = "⌛ Loading video..."
				if (video.readyState === video.HAVE_ENOUGH_DATA) {
					loadingMessage.hidden = true;
					canvasElement.hidden = false;

					canvasElement.height = video.videoHeight;
					canvasElement.width = video.videoWidth;
					canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
					var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
					var code = jsQR(imageData.data, imageData.width, imageData.height, {
						inversionAttempts: "dontInvert",
					});

					if (code) {
						drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
						drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
						drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
						drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
						outputMessage.hidden = true;

						document.getElementById("employee").value = code.data;

						checkCode(video, code.data, order_no);

					} else {
						outputMessage.hidden = false;
					}
				}
				requestAnimationFrame(tick);
			}

			$('#scanner').show();

			$('#field-name').hide();
			$('#field-key').hide();
		}

		function checkCode(video, data, order_no) {
			var datas = {
				employee_id : data,
				order_no : order_no
			}

			$.post('{{ url("post/maintenance/spk/receipt") }}', datas, function(result, status, xhr){
				openSuccessGritter('Success', 'SPK Sukses Diterima');
				$('#scanner').hide();
				$('#scanModal').modal('hide');
				videoOff();
				get_spk();
			})
		}

		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

		function openSuccessGritter(title, message){
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-success',
				image: '{{ url("images/image-screen.png") }}',
				sticky: false,
				time: '4000'
			});
		}

		function openErrorGritter(title, message) {
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-danger',
				image: '{{ url("images/image-stop.png") }}',
				sticky: false,
				time: '4000'
			});
		}

		$.date = function(dateObject) {
			var d = new Date(dateObject);
			var day = d.getDate();
			var month = d.getMonth() + 1;
			var year = d.getFullYear();
			if (day < 10) {
				day = "0" + day;
			}
			if (month < 10) {
				month = "0" + month;
			}
			var date = day + "/" + month + "/" + year;

			return date;
		};

		function addZero(i) {
			if (i < 10) {
				i = "0" + i;
			}
			return i;
		}

		function pad(val) {
			var valString = val + "";
			if (valString.length < 2) {
				return "0" + valString;
			} else {
				return valString;
			}
		}

		function diff_seconds(dt2, dt1){
			var diff = (dt2.getTime() - dt1.getTime()) / 1000;
			return Math.abs(Math.round(diff));
		}

		function getActualFullDate() {
			var d = new Date();
			var day = addZero(d.getDate());
			var month = addZero(d.getMonth()+1);
			var year = addZero(d.getFullYear());
			var h = addZero(d.getHours());
			var m = addZero(d.getMinutes());
			var s = addZero(d.getSeconds());
			return year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
		}
	</script>
	@endsection
