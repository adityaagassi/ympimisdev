@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	#history tbody > tr > td {
		cursor: pointer;
	}
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
		border:1px solid black;
		vertical-align: middle;
		padding:5px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:5px;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#fill_kaizen > thead > tr > th[class*="sort"]:after{
		content: "" !important;
	}
	#queueTable.dataTable {
		margin-top: 0px !important;
	}
	#loading, #error { display: none; }
	.post .user-block {
		margin-bottom: 5px
	}
	#chat {
		height:480px;
		overflow-y: scroll;
	}
	#kz_detail_1 > tbody > tr > td, #kz_detail_2 > tbody > tr > td, #kz_detail_3 > tbody > tr > td, #kz_detail_4 > tbody > tr > td {
		text-align: left;
	}
	#kz_detail_1, #kz_detail_2, #kz_detail_3, #kz_detail_4{
		margin-bottom: 10px
	}
	#tabelDetail > tbody > tr > td {
		text-align: left;
	}
	#tabel_Kz > tbody > tr > td {
		text-align: left;
		vertical-align: top;
		padding: 2px;
	}
	#tabel_Kz > tbody > tr > th {
		padding: 2px;
		background-color: #7e5686;
		color: white;
	}
	#tabel_nilai > tbody > tr > td {
		text-align: left;
	}
	#tabel_assess > tbody > tr > td, #tabel_assess > tbody > tr > th {
		text-align: center;
	}
	#tabel_assess > tbody > tr > th {
		background-color: #7e5686;
		color: white;
	}
	#tabel_nilai_all tbody > tr > th {
		text-align: center;
		background-color: #7e5686;
		color: white;
	}
	#kz_before > p > img {
		max-width:420px;
	}
	#kz_after > p > img {
		max-width:420px;
	}
	span > a {
		color: white
	}
	span > a:hover {
		color: white
	}

</style>
@stop
@section('header')
{{-- <section class="content-header" style="padding-top: 0; padding-bottom: 0;"> --}}
	<h1>
		<span class="text-yellow">
			{{ $title }}
		</span>
		<small>
			<span style="color: #FFD700;"> {{ $title_jp }}</span>
		</small>
	</h1>
	<br>
{{-- </section> --}}
@endsection
@section('content')
@php
$avatar = 'images/avatar/'.Auth::user()->avatar;
@endphp
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div class="row">
		<div class="col-md-3">
			<!-- Profile Image -->
			<div class="box">
				<div class="box-body box-profile">
					<img class="profile-user-img img-responsive img-circle" src="{{ url($avatar) }}" alt="User profile picture">

					<h3 class="profile-username text-center">{{ $profil[0]->name }}</h3>

					<p class="text-muted text-center">{{ strtoupper($emp_id) }}</p>

					<ul class="list-group list-group-unbordered" style="margin:0">
						<li class="list-group-item">
							<b>Sisa Cuti</b> <a class="pull-right">
								<span class="label label-danger" style="color: black">
									@if(isset($employee[0]->remaining))
									{{ round($employee[0]->remaining) }} hari
									@endif
								</span>
							</a>
						</li>
						<li class="list-group-item">
							<b>Penugasan</b> <a class="pull-right">
								<span class="label label-success">{{ $profil[0]->position }}</span>
							</a>
						</li>
					</ul>
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->

			<!-- About Me Box -->
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">Tentang Saya</h3>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<strong><i class="fa fa-briefcase margin-r-5"></i> Bagian</strong>

					<p class="text-muted">
						{{ $profil[0]->division }} - {{$profil[0]->department}} - {{$profil[0]->section}} - {{$profil[0]->group}} - {{$profil[0]->sub_group}}
					</p>

					<hr style="margin:2px;">

					<strong><i class="fa fa-cc margin-r-5"></i> Alamat</strong>

					<p class="text-muted" style="margin:2px;">{{$profil[0]->address}}</p>

					<hr style="margin:2px;">

					<strong><i class="fa fa-calendar margin-r-5"></i> Tanggal Masuk</strong>

					<p class="text-muted" style="margin:2px;">{{$profil[0]->hire_date}}</p>

					<hr style="margin:2px;">

					<strong><i class="fa fa-star margin-r-5"></i> Grade</strong>

					<p class="text-muted" style="margin:2px;">{{$profil[0]->grade_code}} - {{$profil[0]->grade_name}}</p>

					<hr style="margin:2px;">

					<strong><i class="fa fa-phone margin-r-5"></i> Nomor Telepon</strong>
					<!-- <div class="pull-right"><button class="btn btn-sm btn-primary" style="padding: 2px 5px 2px 5px" data-toggle="modal" data-target="#editModal"><u><i class="fa fa-pencil"></i> Edit</button></u></div> -->

					<p class="text-muted"><i class="fa fa-mobile-phone margin-r-5"></i>&nbsp;&nbsp; {{$profil[0]->phone}}<br>
						<i class="fa fa-whatsapp margin-r-5"></i> {{$profil[0]->wa_number}}</p>
						<p style="color: red">* UPDATE NOMOR TELEPON HANYA BISA DILAKUKAN MELALUI SUNFISH OLEH ADMIN MASING MASING BAGIAN</p>

					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			<!-- /.col -->
			<div class="col-md-9" style="margin-bottom: 10px">
				<button class="btn btn-success" onclick="questionForm()" id="btnTanya"><i class="fa fa-question-circle"></i>&nbsp; Tanya HR &nbsp;<i class="fa fa-angle-double-right"></i></button>
				<button class="btn btn-default" onclick="kembali()" style="display: none" id="btnKembali"><i class="fa fa-angle-double-left"></i>&nbsp; Kembali</button>

				<?php if (strpos($profil[0]->position, 'Operator') !== false) { ?>
				<button class="btn btn-primary" onclick="ekaizen()" id="btnKaizen"><i class="fa  fa-bullhorn"></i>&nbsp; e - Kaizen &nbsp;<i class="fa fa-angle-double-right"></i></button>
				<?php } ?>
			</div>
			<div class="col-md-9">
				<div class="box" id="boxing">
					<div class="box-header">
						<h3 class="box-title">Resume Absensi & Lembur</h3>
						<div class="pull-right">
							<select class="form-control select2">
								<option>2020</option>
							</select>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<table class="table table-bordered table-striped" id="history">
							<thead style="background-color: rgb(126,86,134); color: #FFD700;">
								<tr>
									<th style="width: 10%">Periode</th>
									<th style="width: 10%">Mangkir</th>
									<th style="width: 10%">Izin</th>
									<th style="width: 10%">Sakit</th>
									<th style="width: 10%">Terlambat</th>
									<th style="width: 10%">Pulang Cepat</th>
									<th style="width: 10%">Cuti</th>
									<th style="width: 10%">Tunjangan Disiplin</th>
									<th style="width: 10%">Lembur (Jam)</th>
								</tr>
							</thead>
							<tbody>
								@if(isset($presences))
								@foreach ($presences as $presence)
								<tr>
									<td>{{$presence->periode}}</td>
									<td onclick="cek('Mangkir','{{$presence->periode}}')">
										@if ($presence->mangkir > 0) 
										<span class="badge bg-yellow"><a href="javascript:void(0)">{{$presence->mangkir}}</a></span>
										@else 
										- 
										@endif
									</td>
									<td onclick="cek('Izin','{{$presence->periode}}')">
										@if ($presence->izin > 0) 
										<span class="badge bg-yellow"><a href="javascript:void(0)">{{$presence->izin}}</a></span>
										@else 
										- 
										@endif
									</td>
									<td onclick="cek('Sakit','{{$presence->periode}}')">
										@if ($presence->sakit > 0)
										<span class="badge bg-yellow"><a href="javascript:void(0)">{{$presence->sakit}}</a></span>
										@else 
										- 
										@endif
									</td>
									<td onclick="cek('Terlambat','{{$presence->periode}}')">
										@if ($presence->terlambat > 0)
										<span class="badge bg-yellow"><a href="javascript:void(0)">{{$presence->terlambat}}</a></span>
										@else 
										- 
										@endif
									</td>
									<td onclick="cek('Pulang Cepat','{{$presence->periode}}')">
										@if ($presence->pulang_cepat > 0)
										<span class="badge bg-yellow"><a href="javascript:void(0)">{{$presence->pulang_cepat}}</a></span>
										@else 
										- 
										@endif
									</td>
									<td onclick="cek('Cuti','{{$presence->periode}}')">
										@if ($presence->cuti > 0) 
										<span class="badge bg-yellow"><a href="javascript:void(0)">{{$presence->cuti}}</a></span>
										@else 
										- 
										@endif
									</td>
									<td>
										@if ($presence->tunjangan > 0)
										<i class="fa fa-close" style="color: red"></i>
										@else 
										<i class="fa fa-check" style="color: #18c40c"></i>
										@endif
									</td>
									<td>
										@if ($presence->overtime > 0)
										<span class="badge bg-yellow">{{$presence->overtime}}</span>
										@else 
										- 
										@endif
									</td>
								</tr>
								@endforeach
								@endif
								<!-- <p style="font-size: 28px; font-weight: bold;">Server SUNFISH sedang bermasalah, mohon bersabar dan maaf atas ketidaknyamanannya.</p> -->
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4">ABS (Mangkir) bisa dikarenakan data cek log belum diupload bagian HR.</td>
								</tr>									
							</tfoot>
						</table>
						<!-- <small style="color: red; background-color: yellow">NB : Untuk Melihat data detail bisa dilakukan dengan menekan angka pada kategori.</small> -->
					</div>
					<!-- /.box-body -->
				</div>

				<!-- QUESTION & ANSWER -->

				<div class="box" id="question" style="display: none;">
					<div class="box-header">
						<h3 class="box-title">Question & Answer</h3>
					</div>
					<div class="box-body">
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-2">
									<select class="form-control select2" style="width: 100%" id="category">
										<option disabled selected value="">Category</option>
										<option value="Great Day">Great Day</option>
										<option value="Absensi">Absensi</option>
										<option value="Lembur">Lembur</option>
										<option value="Cuti">Cuti</option>
										<option value="PKB">PKB</option>
										<option value="Penggajian">Penggajian</option>
										<option value="BPJS Kes">BPJS Kes</option>
										<option value="BPJS TK">BPJS TK</option>
									</select>
								</div>
								<div class="col-xs-10">
									<div class="input-group input-group">
										<input type="text" class="form-control" id="msg" placeholder="Write a Message...">
										<span class="input-group-btn">
											<button type="button" class="btn btn-success btn-flat" onclick="posting()"><i class="fa fa-send-o"></i>&nbsp; Post</button>
										</span>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<hr>
							<div id="chat">
							</div>
						</div>
					</div>
				</div>

				<!-- E-KAIZEN -->

				<div class="box" id="kaizen" style="display: none;">
					<div class="box-header">
						<h3 class="box-title">E-Kaizen</h3>
						<?php $grp = str_replace("/"," ",$profil[0]->group); ?>
						<a class="btn btn-primary pull-right" 
						href="{{ url("create/ekaizen/".$emp_id."/".$profil[0]->name."/".$profil[0]->section."/".$grp) }}"><i class="fa fa-bullhorn"></i>&nbsp; Buat Kaizen</a>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-1">
								<label>Filter :</label>
							</div>
							<div class="col-xs-4">
								<input type="text" id="bulanAwal" class="form-control datepicker" placeholder="Tanggal dari..">
							</div>
							<div class="col-xs-4">
								<input type="text" id="bulanAkhir" class="form-control datepicker" placeholder="Tanggal sampai..">
							</div>
							<div class="col-xs-2">
								<button class="btn btn-default" onclick="fill_kaizen()">Cari</button>
							</div>
						</div>
						<hr>
						<table class="table table-bordered" id="tableKaizen" width="100%">
							<thead style="background-color: rgb(126,86,134); color: #FFD700;">
								<tr>
									<th style="width: 900px">Id</th>
									<th>Tanggal</th>
									<th>Usulan</th>
									<th>Kategori</th>
									<th>Status</th>
									<th>Aplikasi</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
						<font style="color: red">* NB : Jika kategori "Terdapat Catatan" maka terdapat catatan dari Foreman / Manager, tekan tombol "details" untuk melihat catatan dan lakukan perubahan pada kaizen teian</font>
					</div>
				</div>

			</div>

			<div class="col-md-9" style="padding-top: 0;">
				<ul class="timeline">
					<li class="time-label">
						<span style="background-color: red; color: white;">
							20 April 2020
						</span>
					</li>
					<li>
						<i class="fa fa-info-circle" style="background-color: red; color: white;"></i>
						<div class="timeline-item">
							<h3 class="timeline-header" style="color: red; font-weight: bold;">Informasi Terkait Pelanggaran Kode Etik Power Harassment (Pelecehan Kekuasaan)</h3>
							<div class="timeline-body">
								Surat informasi dapat didownload melalui link di bawah ini:
								<br>
								<a href="{{ asset('\files\info\Pengumuman_Kasus_COC.pdf') }}"><i class="fa fa-angle-double-right"></i><i class="fa fa-angle-double-right"></i> Pengumuman Kasus COC <i class="fa fa-angle-double-left"></i><i class="fa fa-angle-double-left"></i></a>
								<br>
								Buku kepatuhan kode etik karyawan dapat didownload melalui link di bawah ini:
								<br>
								<a href="{{ asset('\files\info\Kode_Etik_Kepatuhan_rev4.pdf') }}"><i class="fa fa-angle-double-right"></i><i class="fa fa-angle-double-right"></i> Kode Etik Kepatuhan Rev4.0 <i class="fa fa-angle-double-left"></i><i class="fa fa-angle-double-left"></i></a>
							</div>
						</div>
					</li>
					<li class="time-label">
						<span style="background-color: #00a65a; color: white;">
							24 January 2019
						</span>
					</li>
					<li>
						<i class="fa fa-info-circle" style="background-color: #00a65a; color: white;"></i>
						<div class="timeline-item">
							<h3 class="timeline-header" style="color: #00a65a; font-weight: bold;">Yamaha Group Helpline</h3>
							<div class="timeline-body">
								Karyawan dapat menyampaikan informasi terkait tindakan ketidaksesuaian terhadap Kode Etik Kepatuhan (Compliance Code of Conduct) pada link berikut:
								<br>
								<a href="http://ml.helpline.jp/yamahacompliance/"><i class="fa fa-angle-double-right"></i><i class="fa fa-angle-double-right"></i> Link Yamaha Helpline <i class="fa fa-angle-double-left"></i><i class="fa fa-angle-double-left"></i></a>
								<br>
								Username: <b>yamaha</b>
								<br>
								Password: <b>helpline</b>
							</div>
						</div>
					</li>
					<li class="time-label">
						<span style="background-color: #605ca8; color: white;">
							01 January 2020
						</span>
					</li>
					<li>
						<i class="fa fa-info-circle" style="background-color: #605ca8; color: white;"></i>
						<div class="timeline-item">
							<h3 class="timeline-header" style="color: #605ca8; font-weight: bold;">Sunfish</h3>
							<div class="timeline-body">
								Diinformasikan bahwa per tanggal <i style="color: red;">01 Januari 2020</i>, pembuatan <i style="color: red;">form lembur</i> menggunakan <i style="color: red;">Sunfish</i> pada link berikut:
								<br>
								<a href="http://172.17.128.8/sf6/"><i class="fa fa-angle-double-right"></i><i class="fa fa-angle-double-right"></i> Link Sunfish Overtime <i class="fa fa-angle-double-left"></i><i class="fa fa-angle-double-left"></i></a>
							</div>
						</div>
					</li>
					<li>
						<i class="fa fa-dot-circle-o bg-gray"></i>
					</li>
				</ul>
			</div>
		</div>
		<!-- /.col -->
	</div>

	<!-- DETAIL -->

	<div class="modal fade" id="modalDetail">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<p style="font-size: 25px; font-weight: bold; text-align: center" id="kz_title"></p>
							<table id="tabelDetail" width="100%">
								<tr>
									<th>NIK/Name </th>
									<td> : </td>
									<td id="kz_nik"></td>
									<th>Date</th>
									<td> : </td>
									<td id="kz_tanggal"></td>
								</tr>
								<tr>
									<th>Section</th>
									<td> : </td>
									<td id="kz_section"></td>
									<th>Area Kaizen</th>
									<td> : </td>
									<td id="kz_area"></td>
								</tr>
								<tr>
									<th>Leader</th>
									<td> : </td>
									<td id="kz_leader"></td>
								</tr>
								<tr>
									<td colspan="6"><hr style="margin: 5px 0px 5px 0px; border-color: black"></td>
								</tr>
							</table>
							<table width="100%" border="1" id="tabel_Kz">
								<tr>
									<th style="border-bottom: 1px solid black" width="50%">BEFORE :</th>
									<th style="border-bottom: 1px solid black; border-left: 1px" width="50%">AFTER :</th>
								</tr>
								<tr>
									<td id="kz_before"></td>
									<td id="kz_after"></td>
								</tr>
							</table>
							<table id="tableEstimasi" style="border: 1px solid black" width="100%"></table>
							<table width="100%" id="tabel_note">
								<tr><th colspan="2">Note :</th></tr>
								<tr><th style="border: 1px solid black;" width="50%">Foreman</th><th style="border: 1px solid black;" width="50%">Manager</th></tr>
								<tr><td style="text-align: left; border: 1px solid" id="note_foreman"></td><td style="text-align: left; border: 1px solid" id="note_manager"></td></tr>
							</table>
							<br>
							<table width="100%" border="1" id="tabel_assess">
								<tr>
									<th colspan="4">TABEL NILAI KAIZEN</th>
								</tr>
								<tr>
									<th width="5%">No</th>
									<th>Kategori</th>
									<th>Foreman / Chief</th>
									<th>Manager</th>
								</tr>
								<tr>
									<th>1</th>
									<th>Estimasi Hasil</th>
									<td id="foreman_point1"></td>
									<td id="manager_point1"></td>
								</tr>
								<tr>
									<th>2</th>
									<th>Ide</th>
									<td id="foreman_point2"></td>
									<td id="manager_point2"></td>
								</tr>
								<tr>
									<th>3</th>
									<th>Implementasi</th>
									<td id="foreman_point3"></td>
									<td id="manager_point3"></td>
								</tr>
								<tr>
									<th colspan="2"> TOTAL</th>
									<td id="foreman_total" style="font-weight: bold;"></td>
									<td id="manager_total" style="font-weight: bold;"></td>
								</tr>
							</table>
							<br>
							<table width="100%" id="tabel_nilai_all" border="1">
								<tr>
									<th>No</th>
									<th>Total Nilai</th>
									<th>Point</th>
									<th>Keterangan</th>
									<th>Reward Aplikasi</th>
								</tr>
								<tr>
									<td>1</td>
									<td><300</td>
									<td>2</td>
									<td>Kurang</td>
									<td>Rp 2.000,-</td>
								</tr>

								<tr>
									<td>2</td>
									<td>300 - 350</td>
									<td>4</td>
									<td>Cukup</td>
									<td>Rp 5.000,-</td>
								</tr>

								<tr>
									<td>3</td>
									<td>351 - 400</td>
									<td>6</td>
									<td>Baik</td>
									<td>Rp 10.000,-</td>
								</tr>

								<tr>
									<td>4</td>
									<td>401 - 450</td>
									<td>8</td>
									<td>Sangat Baik</td>
									<td>Rp 25,000,-</td>
								</tr>

								<tr>
									<td>5</td>
									<td>> 450</td>
									<td>10</td>
									<td>Potensi Excellent</td>
									<td>Rp 50,000,-</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
		</div>
	</div>

	<!-- DELETE -->
	<div class="modal fade" id="modalDelete">
		<div class="modal-dialog modal-md modal-danger">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><b>Apakah anda yakin ingin menghapus kaizen ?</b></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<p id="kz_title_delete"></p>
							<input type="hidden" id="id_delete">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success pull-left" data-dismiss="modal" onclick="deleteKaizen()"><i class="fa fa-close"></i> YES</button>
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> NO</button>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>

	<!-- Modal Absen Detail -->
	<div class="modal fade" id="modalAbsenceDetail">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-bordered">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th>Tanggal</th>
										<th>Cek Log Masuk</th>
										<th>Cek Log Pulang</th>
										<th>Keterangan</th>
									</tr>
									<tr id="laoding_absence">
										<th colspan="4"><i class="fa fa-spinner fa-pulse"></i> Loading</th>
									</tr>
								</thead>
								<tbody id="body_absence"></tbody>
							</table>
						</div>
					</div>
				</div>				
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>

	<!-- MODAL ANNOUNCEMENT -->
	<div class="modal fade" id="modalBerita">
		<div class="modal-dialog modal-lg modal-default">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<center><h4 class="modal-title"><b>YMPI Announcement</b></h4></center>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
								<!-- Wrapper for slides -->
								<div class="carousel-inner">
									<div class="item active">
										<img class="img-responsive" src="{{url('images/announchement3.jpg')}}" alt="...">
										<!-- <div class="carousel-caption">
											Another Image
										</div> -->
									</div>
									<div class="item">
										<center><img class="img-responsive" src="{{url('images/corona.jpg')}}" alt="..." width="600px"></center>
										<!-- <div class="carousel-caption">
											Alur Penanganan Corona Virus
										</div> -->
									</div>
									<div class="item">
										<img class="img-responsive" src="{{url('images/announchement2.png')}}" alt="...">
										<!-- <div class="carousel-caption">
											Another Image
										</div> -->
									</div>
									<!-- <div class="item">
										<img class="img-responsive" src="http://placehold.it/1200x600/fcf00c/000&text=Three" alt="...">
										<div class="carousel-caption">
											Another Image
										</div>
									</div> -->
								</div>
								<!-- Controls -->
								<a class="left carousel-control" style="color: black;" href="#carousel-example-generic" role="button" data-slide="prev">
									<span class="glyphicon glyphicon-chevron-left"></span>
								</a>
								<a class="right carousel-control" style="color: black;" href="#carousel-example-generic" role="button" data-slide="next">
									<span class="glyphicon glyphicon-chevron-right"></span>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});			

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var chat = 0;
	var name = "";

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no data";
				}
			}
		});

		$("#modalBerita").modal('show');

		// $('#kz_sub_leader').select2({ width: 'resolve' });

		name = "{{ $profil[0]->name }}";
		name = name.replace('&#039;','');

		$("#phone_number").val("{{$profil[0]->phone}}");
		$("#wa_number").val("{{$profil[0]->wa_number}}");

		fill_chat();

		$('.datepicker').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
		})
	});

	$(window).on('pageshow', function(){
		$("#bulanAwal").val("");
		$("#bulanAkhir").val("");
		fill_kaizen();
	});

	function check_chart() {
		if (!$(".komen").is(':focus') && chat == 1) {
			fill_chat();
		}
	}

	function fill_chat() {
		var data = {
			employee_id: '{{ $emp_id }}_'+name.split(' ').slice(0,2).join('-')
		}

		$.get('{{ url("fetch/chat/hrqa") }}', data, function(result, status, xhr){
			if(result.status){
				$("#chat").empty();
				var xCategories2 = [];

				for(var i = 0; i < result.chats.length; i++){
					ctg = result.chats[i].id+"_"+result.chats[i].message+"_"+result.chats[i].category+"_"+result.chats[i].created_at_new;

					if(xCategories2.indexOf(ctg) === -1){
						xCategories2[xCategories2.length] = ctg;
					}
				}


				$.each(xCategories2, function(index, value){
					var chat_history = "";
					var chats = value.split("_");
					chat_history += '<div class="post">';
					chat_history += '<div class="user-block">'
					chat_history += '<img class="img-circle img-bordered-sm" src="'+result.base_avatar+'/{{ $emp_id }}.jpg" alt="image">';
					chat_history += '<span class="username">{{ $emp_id }}_'+name.split(' ').slice(0,2).join('-')+'</span>';
					chat_history += '<span class="description">'+chats[3]+'</span></div>';
					chat_history += '<p>'+chats[1]+'</p>';

					var stat = 0;
					var rev = 0;

					$.each(result.chats, function(index2, value2){
						if (chats[0] == value2.id) { 
							if (value2.message_detail) {
								if (stat == 0) {
									chat_history += '<div style="margin-left: 30px">';
								} else {
									chat_history += '<div>';
								}

								chat_history += '<div class="post">'
								chat_history += '<div class="user-block">';
								chat_history += '<img class="img-circle img-bordered-sm" src="'+result.base_avatar+'/'+value2.avatar+'.jpg" alt="image">';
								chat_history += '<span class="username">'+value2.dari+' &nbsp; ';
								chat_history += '<span style="color:#999; font-size:13px">'+value2.created_at_new+'</span></span>';
								chat_history += '<span class="description" style="color:#666">'+value2.message_detail+'</span></div>';
								// chat_history += '<p>'+value2.message_detail+'</p>';

								stat = 1;

								if (typeof result.chats[index2+1] === 'undefined') {
									rev = 1;
									chat_history += '<input class="form-control input-sm komen" type="text" placeholder="Type a comment" id="comment_'+value2.id+'"></div>';
								} else {
									if (result.chats[index2].id != result.chats[index2+1].id) {
										rev = 1;
										chat_history += '<input class="form-control input-sm komen" type="text" placeholder="Type a comment" id="comment_'+value2.id+'"></div>';
									}
								}
							} else {
								if (rev == 0) {
									chat_history += '<input class="form-control input-sm komen" type="text" placeholder="Type a comment" id="comment_'+value2.id+'">';	
								}
							}
						}

					})
					chat_history += '</div>';

					$("#chat").append(chat_history);
				})

				$(".komen").keypress(function() {
					var keycode = (event.keyCode ? event.keyCode : event.which);
					if(keycode == '13'){
						if (this.value != "") {
							var id2 = this.id.split("_")[1];
							// alert(id+" "+this.value+" HR");
							var data = {
								id:id2,
								message:this.value,
								from:"{{ $emp_id }}_"+name.split(' ').slice(0,2).join('-')
							}

							$.post('{{ url("post/chat/comment") }}', data, function(result, status, xhr){
								fill_chat();
							})
						} else {
							alert('Komentar tidak boleh kosong'); 
						}
					}
				});
			}
		})
	}

	function posting() {
		var msg = $("#msg").val();
		var cat = $("#category").val();

		if (msg == "" && cat == "") {
			openErrorGritter('Error!','Pesan harus diisi');
			return false;
		}

		var data = {
			message:msg,
			category:cat,
			from:"{{ $emp_id }}_"+name.split(' ').slice(0,2).join('-')
		}

		$.post('{{ url("post/hrqa") }}', data, function(result, status, xhr){
			openSuccessGritter('Success','');
			$("#msg").val("");
			fill_chat();
		})
	}

	function fill_kaizen() {
		if ($("#bulanAwal").val() != "" && $("#bulanAkhir").val() == "") {
			alert("Bulan Sampai harap diisi");
			return false;
		} else if ($("#bulanAwal").val() == "" && $("#bulanAkhir").val() != "") {
			alert("Bulan Dari harap diisi");
			return false;
		}

		var data = {
			employee_id : "{{ $emp_id }}",
			bulanAwal : $("#bulanAwal").val(),
			bulanAkhir : $("#bulanAkhir").val(),
		}
		$('#tableKaizen').DataTable().destroy();
		var table2 = $('#tableKaizen').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/report/kaizen") }}",
				"data" : data
			},
			"columns": [
			{ "data": "id" },
			{ "data": "propose_date" },
			{ "data": "title" },
			{ "data": "stat" },
			{ "data": "posisi" },
			{ "data": "application" },
			{ "data": "action" }
			],
					// "columnDefs": [
					// { "width": "2%", "targets": 0 },
					// { "width": "3%", "targets": 1 },
					// { "width": "5%", "targets": [3,4,5,6] }
					// ]
				});

		$('#tableKaizen tfoot tr').appendTo('#tableKaizen thead');
	}

	function cekDetail(id) {
		data = {
			id:id
		}

		$.get('{{ url("fetch/kaizen/detail") }}', data, function(result) {
			$("#kz_title").text(result.datas[0].title);
			$("#kz_nik").text(result.datas[0].employee_id + " / "+ result.datas[0].employee_name);
			$("#kz_section").text(result.datas[0].section);
			$("#kz_leader").text(result.datas[0].leader_name);
			$("#kz_tanggal").text(result.datas[0].date);
			$("#kz_area").text(result.datas[0].area);
			$("#kz_before").html(result.datas[0].condition);
			$("#kz_after").html(result.datas[0].improvement);
			$("#note_foreman").html(result.datas[0].foreman_note);
			$("#note_manager").html(result.datas[0].manager_note);

			$("#tableEstimasi").empty();
			bd = "";
			tot = 0;
			if (result.datas[0].cost_name) {
				$.each(result.datas, function(index, value){
					bd += "<tr>";
					var unit = "";

					if (value.cost_name == "Manpower") {
						unit = "menit";
						sub_tot = (value.sub_total_cost * 20);
						tot += parseInt(sub_tot);
					} else if (value.cost_name == "Tempat") {
						unit = value.unit+"<sup>2</sup>";
						sub_tot = parseInt(value.sub_total_cost);
						tot += sub_tot;
					}
					else {
						unit = value.frequency;
						sub_tot = value.sub_total_cost;
						tot += parseInt(sub_tot);
					}

					bd += "<th>"+value.cost_name+"</th>";
					bd += "<td><b>"+value.cost+"</b> "+unit+" X <b>Rp "+value.std_cost+",-</b></td>";
					bd += "<td><b>Rp "+sub_tot+",- / bulan</b></td>";
					bd += "</tr>";
				});

				bd += "<tr style='font-size: 18px;'>";
				bd += "<th colspan='2' style='text-align: right;padding-right:5px'>Total</th>";
				bd += "<td><b>Rp "+tot+",-</b></td>";
				bd += "</tr>";

				$("#tableEstimasi").append(bd);
			}

			$("#foreman_point1").text(result.datas[0].foreman_point_1 * 40);
			$("#foreman_point2").text(result.datas[0].foreman_point_2 * 30);
			$("#foreman_point3").text(result.datas[0].foreman_point_3 * 30);
			$("#foreman_total").text((result.datas[0].foreman_point_1 * 40) + (result.datas[0].foreman_point_2 * 30) + (result.datas[0].foreman_point_3 * 30));
			$("#manager_point1").text(result.datas[0].manager_point_1 * 40);
			$("#manager_point2").text(result.datas[0].manager_point_2 * 30);
			$("#manager_point3").text(result.datas[0].manager_point_3 * 30);
			$("#manager_total").text((result.datas[0].manager_point_1 * 40) + (result.datas[0].manager_point_2 * 30) + (result.datas[0].manager_point_3 * 30));
			$("#modalDetail").modal('show');
		})
	}

	function load_leader() {
		$.get('{{ url("fetch/sub_leader") }}', function(result, status, xhr){

			fill_chat();
		})
	}

	function cek(kode, period) {
		var data = {
			attend_code: kode,
			period: period
		}
		$("#modalAbsenceDetail").modal('show');
		$("#laoding_absence").show();
		$("#body_absence").empty();

		$.get('{{ url("fetch/absence/employee") }}', data, function(result, status, xhr){
			$("#laoding_absence").hide();
			var body = "";

			$.each(result.datas, function(index2, value2){
				body += "<tr>";
				body += "<td>"+value2.tanggal+"</td>";
				body += "<td>"+value2.starttime+"</td>";
				body += "<td>"+value2.endtime+"</td>";
				body += "<td>"+value2.Attend_Code+"</td>";
				body += "</tr>";
			})

			$("#body_absence").append(body);

		})
	}

			// function modalEditKaizen(id) {
			// 	alert(id);
			// }

			function deleteKaizen() {
				var ids = $("#id_delete").val();

				var data = {
					id : ids
				}

				$.get('{{ url("delete/kaizen") }}', data, function(result, status, xhr){
					openSuccessGritter('Success','Kaizen Teian berhasil dihapus..');
					fill_kaizen();
				})
			}

			function openDeleteDialog(id, title, date) {
				$('#modalDelete').modal({
					backdrop: 'static',
					keyboard: false
				})

				$("#kz_title_delete").text('"'+title+'" ?');
				$("#id_delete").val(id);
			}

			function questionForm() {
				$("#boxing").hide();
				$("#question").show();
				$("#btnTanya").hide();
				$("#btnKembali").show();
				$("#btnKaizen").hide();
				chat = 1;
			}

			function kembali() {
				$("#boxing").show();
				$("#question").hide();
				$("#kaizen").hide();
				$("#btnKembali").hide();
				$("#btnTanya").show();
				$("#btnKaizen").show();
				chat = 0;
			}

			function ekaizen() {
				$("#boxing").hide();
				$("#kaizen").show();
				$("#btnTanya").hide();
				$("#btnKaizen").hide();
				$("#btnKembali").show();
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