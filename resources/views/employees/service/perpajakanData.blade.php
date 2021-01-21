@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">	
	.head {
		text-align: center;
		font-weight: bold;
	}
	.label-pad{
		padding-top: 0.25%;
	}
	.header-tab{
		font-size: 20px;
		font-weight: bold;
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

	.row{
		margin-left: 5px;
	}

	#loading { display: none; }
	

</style>
@stop
@section('header')
<h1>
	<span class="text-yellow">
		{{ $title }}
	</span>
	<small>
		<span style="color: #FFD700;"> {{ $title_jp }}</span>
	</small>
</h1>
<br>
@endsection
@section('content')
@php
$avatar = 'images/avatar/'.Auth::user()->avatar;
@endphp
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%; text-align: center;">
			<span style="font-size: 5vw; "><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-10 col-xs-offset-1">
			<div class="box">
				<div class="box-body">
					<h2 class="head">UPDATE DATA PERPAJAKAN & NPWP KARYAWAN YMPI 2021</h2>
					<h4 class="head" style="font-weight: bold; text-align: center;">
					</h4>



					<div class="form-group row" align="left">
						<label class="col-xs-12 label-pad">
							Untuk memudahkan dalam pengisian form, Siapkan data data berikut: 
						<br>
						<ol>
							<li><span class="text-red">Kartu Tanda Penduduk</span></li>
							<li><span class="text-red">Status Pernikahan / Lajang</span>, Kondisi Per 1 Januari 2021</li>
							<li><span class="text-red">Data Tanggungan Anak Maksimal 3 Orang</span> (belum menikah dan belum bekerja)</li>
							<li>Khusus untuk <span class="text-red">Karyawati sudah menikah</span>, status pernikahan diisi "<span class="text-red">Tidak Kawin(TK)</span>" karena ikut suami</li>
							<li><span class="text-red">Foto Kartu NPWP milik sendiri</span> atau <span class="text-red">Foto Kartu NPWP Suami</span> (Untuk karyawati kawin yang NPWP-nya mengikuti suami)
								<br>Apabila <span class="text-red">kartu NPWP tidak ada / belum dikirimkan</span> Kantor Pajak bisa diganti dengan Surat Keterangan Terdaftar / Bukti Laporan Pajak Tahunan<br>Karyawan eFilling 1770S/SS yang menunjukkan data NPWP</li>
						</ol>
						</label>
						<label class="col-xs-12 label-pad">
							Catatan : 
							<ul>
								<li>Tanda <span class="text-red">*</span> Harus Diisi </li>
								<li>Harap Diisi Dengan Huruf <span class="text-red">Capital/Besar</span></li>
							</ul>
						</label>
					</div>

					<form id="form_pajak" method="post" action="upload" enctype="multipart/form-data">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />

						<div class="form-group row" style="margin-top: 3%;background-color: #b464f5;color: white;padding: 10px;margin:10px;border-radius: 5px">
							<label class="col-xs-6 header-tab">A. Data Pribadi</label>
						</div>

						<div class="form-group row" align="left">
							<label class="col-xs-3 label-pad">Nomor Induk Karyawan<span class="text-red">*</span></label>
							<div class="col-xs-5" align="left">
								<input type="text" class="form-control" id="employee_id" name="employee_id" placeholder="Nomor Induk Karyawan" value="{{ $employee_id }}" readonly="">
							</div>
						</div>

						<div class="form-group row" align="left">
							<label class="col-xs-3 label-pad">Nama Lengkap<span class="text-red">*</span></label>
							<div class="col-xs-5" align="left">
								<input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap (Sesuai KTP)" required>
							</div>
						</div>


						<div class="form-group row" align="left">
							<label class="col-xs-3 label-pad">Nomor Induk Kependudukan<span class="text-red">*</span></label>
							<div class="col-xs-5" align="left">
								<input type="text" class="form-control" id="nik" name="nik" placeholder="Nomor Induk Kependudukan. Contoh : 35730112323..." required>
							</div>
						</div>

						<div class="form-group row" align="left">
							<label class="col-xs-3 label-pad">Tempat Lahir<span class="text-red">*</span></label>
							<div class="col-xs-5" align="left">
								<input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" placeholder="Tempat Lahir" required>
							</div>
						</div>

						<div class="form-group row" align="left">
							<label class="col-xs-3 label-pad">Tanggal Lahir<span class="text-red">*</span></label>
							<div class="col-xs-5" align="left">
								<div class="input-group date">
									<div class="input-group-addon bg-green" style="border: none;">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="tanggal_lahir" name="tanggal_lahir" placeholder="Format : dd-mm-yyyy" required>
								</div>
							</div>
						</div>		

						<div class="form-group row" align="left">
							<label class="col-xs-3 label-pad">Jenis Kelamin<span class="text-red">*</span></label>
							<div class="col-xs-5" align="left">
								<select class="form-control select2" data-placeholder="Pilih Jenis Kelamin" id="jenis_kelamin" name="jenis_kelamin" style="width: 100%" required>
									<option style="color:grey;" value="">Pilih Jenis Kelamin</option>
									<option value="LAKI-LAKI">LAKI-LAKI</option>
									<option value="PEREMPUAN">PEREMPUAN</option>
								</select>
							</div>
						</div>			

						<div class="form-group row" style="margin-top: 3%;background-color: #b464f5;color: white;padding: 10px;margin:10px;border-radius: 5px">
							<label class="col-xs-6 header-tab">B. Data Alamat</label>
						</div>

						<div class="form-group row" align="left">
							<label class="col-xs-3 label-pad">Alamat (Nama Jalan / Nama Desa)<span class="text-red">*</span></label>
							<div class="col-xs-5" align="left">
								<input type="text" class="form-control" id="jalan" name="jalan" placeholder="Nama Jalan / Nama Desa" required>
							</div>
						</div>

						<div class="form-group row" align="left">
							<label class="col-xs-3 label-pad">RT / RW<span class="text-red">*</span></label>
							<div class="col-xs-5" align="left">
								<input type="text" class="form-control" id="rtrw" name="rtrw" placeholder="RT / RW" required>
							</div>
						</div>

						<div class="form-group row" align="left">
							<label class="col-xs-3 label-pad">Kelurahan<span class="text-red">*</span></label>
							<div class="col-xs-5" align="left">
								<input type="text" class="form-control" id="kelurahan" name="kelurahan" placeholder="Kelurahan" required>
							</div>
						</div>

						<div class="form-group row" align="left">
							<label class="col-xs-3 label-pad">Kecamatan<span class="text-red">*</span></label>
							<div class="col-xs-5" align="left">
								<input type="text" class="form-control" id="kecamatan" name="kecamatan" placeholder="Kecamatan" required>
							</div>
						</div>

						<div class="form-group row" align="left">
							<label class="col-xs-3 label-pad">Kabupaten / Kota<span class="text-red">*</span></label>
							<div class="col-xs-5" align="left">
								<input type="text" class="form-control" id="kota" name="kota" placeholder="Contoh : Kota Pasuruan | Kabupaten Pasuruan" required>
							</div>
						</div>

						<div class="form-group row" style="margin-top: 3%;background-color: #b464f5;color: white;padding: 10px;margin:10px;border-radius: 5px">
							<label class="col-xs-12 header-tab">C. STATUS PERNIKAHAN DIISI SESUAI KONDISI PER 1 JANUARI 2021</label>
						</div>

						<div class="form-group row" align="left">
							<label class="col-xs-3 label-pad">Status Perkawinan<span class="text-red">*</span></label>
							<div class="col-xs-5" align="left">
								<select class="form-control select2" data-placeholder="Pilih Status" id="status_perkawinan" name="status_perkawinan" style="width: 100%" onchange="statuskawin(this)" required>
									<option style="color:grey;" value="">Pilih Status</option>
									<option value="TIDAK KAWIN">TK (TIDAK KAWIN)</option>
									<option value="KAWIN">K (KAWIN)</option>
									<option value="CERAI">HB (HIDUP BERPISAH / CERAI)</option>
									<option value="PISAH HARTA">PH (PISAH HARTA)</option>
								</select>
							</div>
						</div>


						{{-- Suami/Istri --}}
						<div class="form-group row" align="left" id="istri_data" style="display: none">
							<label class="col-xs-3 label-pad">Istri<span class="text-red">*</span></label>
							<div class="col-xs-2" align="left">
								<div class="input-group date">
									<div class="input-group-addon bg-green" style="border: none;">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="tanggal_nikah" name="tanggal_nikah" placeholder="Tanggal Menikah">
								</div>
							</div>
							<div class="col-xs-2" align="left" style="padding-left: 0px;">
								<input type="text" class="form-control" id="nama_istri" name="nama_istri" placeholder="Nama Istri">
							</div>
							<div class="col-xs-2" align="left" style="padding-left: 0px;">
								<div class="input-group date">
									<div class="input-group-addon bg-green" style="border: none;">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="tanggal_lahir_istri" name="tanggal_lahir_istri" placeholder="Tanggal Lahir Istri">
								</div>
							</div>
							<div class="col-xs-3" align="left" style="padding-left: 0px;">
								<input type="text" class="form-control" id="pekerjaan_istri" name="pekerjaan_istri" placeholder="Pekerjaan Istri" >
							</div>
						</div>


						{{-- Anak1 --}}
						<div class="form-group row anak_data" align="left" style="display: none">
							<label class="col-xs-3 label-pad">Anak 1</label>
							<div class="col-xs-2" align="left">
								<input type="text" class="form-control" id="nama_anak1" name="nama_anak1" placeholder="Nama Anak 1">
							</div>
							<div class="col-xs-1" align="left" style="padding-left: 0px;">
								<select class="form-control select2" data-placeholder="L/P" id="kelamin_anak1" name="kelamin_anak1" style="width: 100%">
									<option style="color:grey;" value="">L/P</option>
									<option value="L">L</option>
									<option value="P">P</option>
								</select>
							</div>
							<div class="col-xs-2" align="left" style="padding-left: 0px;">
								<input type="text" class="form-control" id="tempat_lahir_anak1" name="tempat_lahir_anak1" placeholder="Tempat Lahir Anak 1">
							</div>
							<div class="col-xs-2" align="left" style="padding-left: 0px;">
								<div class="input-group date">
									<div class="input-group-addon bg-green" style="border: none;">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="tanggal_lahir_anak1" name="tanggal_lahir_anak1" placeholder="Tanggal Lahir Anak 1" >
								</div>
							</div>
							<div class="col-xs-2" align="left" style="padding-left: 0px;">
								<select class="form-control select2" data-placeholder="Status Anak 1" id="status_anak1" name="status_anak1" style="width: 100%">
									<option style="color:grey;" value="">Status Anak</option>
									<option value="Anak Kandung">Anak Kandung</option>
									<option value="Anak Tiri">Anak Tiri</option>
									<option value="Anak Adopsi Yang Diakui Secara Hukum">Anak Adopsi Yang Diakui Secara Hukum</option>
								</select>
							</div>
						</div>

						{{-- Anak2 --}}
						<div class="form-group row anak_data" align="left" style="display: none">
							<label class="col-xs-3 label-pad">Anak 2</label>
							<div class="col-xs-2" align="left">
								<input type="text" class="form-control" id="nama_anak2" name="nama_anak2" placeholder="Nama Anak 2">
							</div>
							<div class="col-xs-1" align="left" style="padding-left: 0px;">
								<select class="form-control select2" data-placeholder="L/P" id="kelamin_anak2" name="kelamin_anak2" style="width: 100%">
									<option style="color:grey;" value="">L/P</option>
									<option value="L">L</option>
									<option value="P">P</option>
								</select>
							</div>
							<div class="col-xs-2" align="left" style="padding-left: 0px;">
								<input type="text" class="form-control" id="tempat_lahir_anak2" name="tempat_lahir_anak2" placeholder="Tempat Lahir Anak 2">
							</div>
							<div class="col-xs-2" align="left" style="padding-left: 0px;">
								<div class="input-group date">
									<div class="input-group-addon bg-green" style="border: none;">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="tanggal_lahir_anak2" name="tanggal_lahir_anak2" placeholder="Tanggal Lahir Anak 2" >
								</div>
							</div>
							<div class="col-xs-2" align="left" style="padding-left: 0px;">
								<select class="form-control select2" data-placeholder="Status Anak 2" id="status_anak2" name="status_anak2" style="width: 100%">
									<option style="color:grey;" value="">Status Anak</option>
									<option value="Anak Kandung">Anak Kandung</option>
									<option value="Anak Tiri">Anak Tiri</option>
									<option value="Anak Adopsi Yang Diakui Secara Hukum">Anak Adopsi Yang Diakui Secara Hukum</option>
								</select>
							</div>
						</div>

						{{-- Anak3 --}}
						<div class="form-group row anak_data" align="left" style="display: none">
							<label class="col-xs-3 label-pad">Anak 3</label>
							<div class="col-xs-2" align="left">
								<input type="text" class="form-control" id="nama_anak3" name="nama_anak3" placeholder="Nama Anak 3">
							</div>
							<div class="col-xs-1" align="left" style="padding-left: 0px;">
								<select class="form-control select2" data-placeholder="L/P" id="kelamin_anak3" name="kelamin_anak3" style="width: 100%">
									<option style="color:grey;" value="">L/P</option>
									<option value="L">L</option>
									<option value="P">P</option>
								</select>
							</div>
							<div class="col-xs-2" align="left" style="padding-left: 0px;">
								<input type="text" class="form-control" id="tempat_lahir_anak3" name="tempat_lahir_anak3" placeholder="Tempat Lahir Anak 3">
							</div>
							<div class="col-xs-2" align="left" style="padding-left: 0px;">
								<div class="input-group date">
									<div class="input-group-addon bg-green" style="border: none;">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" id="tanggal_lahir_anak3" name="tanggal_lahir_anak3" placeholder="Tanggal Lahir Anak 3" >
								</div>
							</div>
							<div class="col-xs-2" align="left" style="padding-left: 0px;">
								<select class="form-control select2" data-placeholder="Status Anak 3" id="status_anak3" name="status_anak3" style="width: 100%">
									<option style="color:grey;" value="">Status Anak</option>
									<option value="Anak Kandung">Anak Kandung</option>
									<option value="Anak Tiri">Anak Tiri</option>
									<option value="Anak Adopsi Yang Diakui Secara Hukum">Anak Adopsi Yang Diakui Secara Hukum</option>
								</select>
							</div>
						</div>

						

						<div class="form-group row" style="margin-top: 3%;background-color: #b464f5;color: white;padding: 10px;margin:10px;border-radius: 5px">
							<label class="col-xs-12 header-tab">D. DATA NOMOR POKOK WAJIB PAJAK (NPWP)</label>
						</div>

						<div class="form-group row" align="left">
							<label class="col-xs-3 label-pad">Apakah anda sudah memiliki NPWP? <span class="text-red">*</span></label>
							<div class="col-xs-5" align="left">
								<select class="form-control select2" data-placeholder="Pilih Opsi Berikut" id="npwp_kepemilikan" name="npwp_kepemilikan" style="width: 100%" onchange="npwp_stat(this)" required>
									<option style="color:grey;" value="">Pilih Status</option>
									<option value="Iya">Iya</option>
									<option value="Tidak">Tidak</option>
								</select>
							</div>
						</div>

						<div class="form-group row data_npwp" align="left">
							<label class="col-xs-3 label-pad">Apakah NPWP anda atas nama sendiri atau ikut NPWP suami? <span class="text-red">*</span></label>
							<div class="col-xs-5" align="left">
								<select class="form-control select2" data-placeholder="Pilih NPWP" id="npwp_status" name="npwp_status" style="width: 100%" onchange="npwp_milik(this)">
									<option style="color:grey;" value="">Pilih Status</option>
									<option value="Nama Sendiri">Nama Sendiri</option>
									<option value="Ikut Suami">Ikut Suami</option>
								</select>
							</div>
						</div>

						<div class="form-group row data_npwp" align="left">
							<label class="col-xs-3 label-pad">Nama sesuai NPWP <span class="keterangan_npwp"></span><span class="text-red">*</span></label>
							<div class="col-xs-5" align="left">
								<input type="text" class="form-control" id="npwp_nama" name="npwp_nama" placeholder="Nama Sesuai NPWP">
							</div>
						</div>

						<div class="form-group row data_npwp" align="left">
							<label class="col-xs-3 label-pad">Nomor NPWP <span class="keterangan_npwp"></span><span class="text-red">*</span></label>
							<div class="col-xs-5" align="left">
								<input type="text" pattern="\d*" maxlength="15" class="form-control" id="npwp_nomor" name="npwp_nomor" placeholder="Contoh Penulisan : 948507199624000 (Tuliskan 15 Digit Angka)">
							</div>
						</div>

						<div class="form-group row data_npwp" align="left">
							<label class="col-xs-3 label-pad">Alamat Sesuai NPWP <span class="keterangan_npwp"></span><span class="text-red">*</span></label>
							<div class="col-xs-5" align="left">
								<textarea id="npwp_alamat" name="npwp_alamat" class="form-control" rows="3" placeholder="Alamat Sesuai NPWP"></textarea>
							</div>
						</div>

						<div class="form-group row data_npwp">
							<label class="col-xs-3 label-pad">Foto Kartu NPWP (Upload Foto kartu jika ada, format .jpg)</label>
							<div class="col-xs-5" align="left">
									<input type="file" id="attach" name="attach[]" multiple="">
									<label><i class="fa fa-paperclip"></i> </label>
							</div>
						</div>

						<div id="file_upload" style="margin-left: 50px"></div>

						<div class="form-group row" align="center" style="margin-top: 5%; margin-bottom: 5%;">
							<div class="col-xs-2">
								<a style="margin-right: 3%;" class="btn btn-lg btn-danger" href="{{ route('emp_service', ['id' =>'1']) }}"><i class="fa fa-arrow-left"></i> BACK</a>
							</div>

							<div class="col-xs-3 col-xs-offset-7"><!-- 
								<a style="margin-right: 3%;" class="btn btn-lg btn-danger" href="{{ route('emp_service', ['id' =>'1']) }}"><i class="fa fa-close"></i> CANCEL</a> -->
								<button type="submit" class="btn btn-lg btn-success"><i class="fa fa-save"></i> SIMPAN</button>
							</div>
						</div>

					</form>
				</div>
			</div>
		</div>
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
	
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.select2').select2({
			allowClear: true
		});

		$('.anak_data').hide();
		$('#istri_data').hide();
    	$('.data_npwp').hide();
    	$('.keterangan_npwp').html("");

		$('.datepicker').datepicker({
			<?php $tgl_max = date('d-m-Y') ?>
			format: 'dd-mm-yyyy',
			endDate: '<?php echo $tgl_max ?>'

		})

		fillForm();
	});

	function fillForm() {
		var emp_id = $("#employee_id").val();
		var data = {
			emp_id : emp_id
		}
		
		$.get('{{ url("fetch/fill_perpajakan_data") }}', data, function(result, status, xhr){
			if(result.status){
				if(result.data != null){
					$("#employee_id").val(result.data.employee_id);
					$("#nama_lengkap").val(result.data.nama);
					$("#nik").val(result.data.nik);
					$("#tempat_lahir").val(result.data.tempat_lahir);
					$("#tanggal_lahir").val(result.data.tgl_lahir);
					$('#jenis_kelamin').val(result.data.jenis_kelamin).trigger('change.select2');

					$("#jalan").val(result.data.jalan);
					$("#rtrw").val(result.data.rtrw);
					$("#kelurahan").val(result.data.kelurahan);
					$("#kecamatan").val(result.data.kecamatan);
					$("#kota").val(result.data.kota);

					$('#status_perkawinan').val(result.data.status_perkawinan).trigger('change.select2');

					if (result.data.status_perkawinan == "TIDAK KAWIN" || result.data.status_perkawinan == "PISAH HARTA") {
						$('.anak_data').hide();
						$('#istri_data').hide();
					}
					else if (result.data.status_perkawinan == "CERAI") {
						$('.anak_data').show();
						$('#istri_data').hide();
					}
					else if(result.data.status_perkawinan == "KAWIN"){
						$('.anak_data').show();
						$('#istri_data').show();
					}

					var istri = result.data.istri.split('_');
					$("#tanggal_nikah").val(istri[0]);
					$("#nama_istri").val(istri[1]);
					$("#tanggal_lahir_istri").val(istri[2]);
					$("#pekerjaan_istri").val(istri[3]);	

					var anak1 = result.data.anak1.split('_');
					$("#nama_anak1").val(anak1[0]);
					$('#kelamin_anak1').val(anak1[1]).trigger('change.select2');
					$("#tempat_lahir_anak1").val(anak1[2]);
					$("#tanggal_lahir_anak1").val(anak1[3]);
					$("#status_anak1").val(anak1[4]).trigger('change.select2');

					var anak2 = result.data.anak2.split('_');
					$("#nama_anak2").val(anak2[0]);
					$('#kelamin_anak2').val(anak2[1]).trigger('change.select2');
					$("#tempat_lahir_anak2").val(anak2[2]);
					$("#tanggal_lahir_anak2").val(anak2[3]);
					$("#status_anak2").val(anak2[4]).trigger('change.select2');	

					var anak3 = result.data.anak3.split('_');
					$("#nama_anak3").val(anak3[0]);
					$('#kelamin_anak3').val(anak3[1]).trigger('change.select2');
					$("#tempat_lahir_anak3").val(anak3[2]);
					$("#tanggal_lahir_anak3").val(anak3[3]);
					$("#status_anak3").val(anak3[4]).trigger('change.select2');	


					$('#npwp_kepemilikan').val(result.data.npwp_kepemilikan).trigger('change.select2');
					$('#npwp_status').val(result.data.npwp_status).trigger('change.select2');
					$('#npwp_nama').val(result.data.npwp_nama);
					$('#npwp_nomor').val(result.data.npwp_nomor);
					$('#npwp_alamat').val(result.data.npwp_alamat);

					if (result.data.npwp_file != null) {  
						var obj = JSON.parse(result.data.npwp_file);
						$.each(obj, function(key, value) {
							$('#file_upload').html('<img Src="'+'{{ url("/tax_files/") }}/'+value+'" width="200">');
				        });

					}				
				}	
			}
		});
}

$('#form_pajak').on('submit', function(event){
	if(!validateForm()){
		return false;
	}

	if(confirm("Data Pajak yang anda input akan disimpan oleh sistem.\nApakah anda yakin ?")){
		event.preventDefault();
		var formdata = new FormData(this);

		$("#loading").show();

		$.ajax({
			url:"{{url('fetch/update_perpajakan_data')}}",
			method:'post',
			data:formdata,
			dataType:"json",
			processData: false,
			contentType: false,
			cache: false,
			success:function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success', result.message);
					$("#loading").hide();
					location.reload();
				}else{
					openErrorGritter('Error!', result.message);
					$("#loading").hide();
				}

			},
			error: function(result, status, xhr){
				$("#loading").hide();				
				openErrorGritter('Error!', result.message);
			}
		});
	}
	else{
		return false;
	}

	
});

function validateForm() {


	if($("#employee_id").val() == ''){
		openErrorGritter('Error!', 'Nomor Induk Karyawan Harus Diisi');
		$("#employee_id").focus();
		return false;
	}

	if($("#nama_lengkap").val() == ''){
		openErrorGritter('Error!', 'Nama Lengkap Harus Diisi');
		$("#nama_lengkap").focus();
		return false;
	}

	if($("#nik").val() == ''){
		openErrorGritter('Error!', 'NIK Harus Diisi');
		$("#nik").focus();
		return false;
	}

	if($("#tempat_lahir").val() == ''){
		openErrorGritter('Error!', 'Tempat Lahir Harus Diisi');
		$("#tempat_lahir").focus();
		return false;
	}

	if($("#tanggal_lahir").val() == ''){
		openErrorGritter('Error!', 'Tanggal Lahir Harus Diisi');
		$("#tanggal_lahir").focus();
		return false;
	}

	if($("#jenis_kelamin").val() == ''){
		openErrorGritter('Error!', 'Jenis Kelamin Harus Diisi');
		$("#jenis_kelamin").focus();
		return false;
	}

	if($("#jalan").val() == ''){
		openErrorGritter('Error!', 'Nama Jalan Harus Diisi');
		$("#jalan").focus();
		return false;
	}

	if($("#rtrw").val() == ''){
		openErrorGritter('Error!', 'Kolom RT RW Harus Diisi');
		$("#rtrw").focus();
		return false;
	}

	if($("#kelurahan").val() == ''){
		openErrorGritter('Error!', 'Kolom Kelurahan Harus Diisi');
		$("#kelurahan").focus();
		return false;
	}

	if($("#kecamatan").val() == ''){
		openErrorGritter('Error!', 'Kolom Kecamatan Harus Diisi');
		$("#kecamatan").focus();
		return false;
	}

	if($("#status_perkawinan").val() == ''){
		openErrorGritter('Error!', 'Status Perkawinan Harus Diisi');
		$("#status_perkawinan").focus();
		return false;
	}


	if($("#tanggal_nikah").val() == '' && $("#status_perkawinan").val() == 'KAWIN'){
		openErrorGritter('Error!', 'Tanggal Nikah Harus Diisi');
		$("#tanggal_nikah").focus();
		return false;
	}

	if($("#nama_istri").val() == '' && $("#status_perkawinan").val() == 'KAWIN'){
		openErrorGritter('Error!', 'Nama Istri Harus Diisi');
		$("#nama_istri").focus();
		return false;
	}

	if($("#tanggal_lahir_istri").val() == '' && $("#status_perkawinan").val() == 'KAWIN'){
		openErrorGritter('Error!', 'Tanggal Lahir Istri Harus Diisi');
		$("#tanggal_lahir_istri").focus();
		return false;
	}

	if($("#pekerjaan_istri").val() == '' && $("#status_perkawinan").val() == 'KAWIN'){
		openErrorGritter('Error!', 'Pekerjaan Istri Harus Diisi');
		$("#pekerjaan_istri").focus();
		return false;
	}

	if($("#npwp_kepemilikan").val() == ''){
		openErrorGritter('Error!', 'Kolom NPWP Harus Diisi');
		$("#npwp_kepemilikan").focus();
		return false;
	}

	if($("#npwp_status").val() == '' && $("#npwp_kepemilikan").val() == 'Iya'){
		openErrorGritter('Error!', 'Kolom NPWP Harus Diisi');
		$("#npwp_status").focus();
		return false;
	}

	if($("#npwp_nama").val() == '' && $("#npwp_kepemilikan").val() == 'Iya'){
		openErrorGritter('Error!', 'Kolom NPWP Harus Diisi');
		$("#npwp_nama").focus();
		return false;
	}

	if($("#npwp_nomor").val() == '' && $("#npwp_kepemilikan").val() == 'Iya') {
		openErrorGritter('Error!', 'Kolom NPWP Harus Diisi Dengan 15 Digit Angka');
		$("#npwp_nomor").focus();
		return false;
	}

	if($("#npwp_alamat").val() == '' && $("#npwp_kepemilikan").val() == 'Iya'){
		openErrorGritter('Error!', 'Kolom NPWP Harus Diisi');
		$("#npwp_alamat").focus();
		return false;
	}


	return true;		
}


function statuskawin(elem){
    var isi = elem.value;

    if (isi == "KAWIN") {
    	$('.anak_data').show();
		$('#istri_data').show();
    }
    else if (isi == "CERAI"){
    	$('.anak_data').show();
		$('#istri_data').hide();
    }
    else{
    	$('.anak_data').hide();
		$('#istri_data').hide();
    }

}

function npwp_stat(elem){
    var npwp = elem.value;

    if (npwp == "Iya") {
    	$('.data_npwp').show();
    }
    else if (npwp == "Tidak"){
    	$('.data_npwp').hide();
    }

}

function npwp_milik(elem){
    var npwp = elem.value;

    if (npwp == "Nama Sendiri") {
    	$('.keterangan_npwp').html("Sendiri");
    }
    else if (npwp == "Ikut Suami"){
    	$('.keterangan_npwp').html("Suami");
    }

}


function openHRq() {
	window.open('{{ route('emp_service', ['id' =>'1']) }}', '_self');
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

</script>
@endsection