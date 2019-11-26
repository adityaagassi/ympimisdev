@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	label {
		color: white;
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
		<div class="col-xs-4">
			<label for="kz_tanggal">Tanggal</label>
			<input type="text" id="kz_tanggal" class="form-control" value="<?php echo date('Y-m-d') ?>" readonly>
		</div>
		<div class="col-xs-4">
			<label for="kz_nik">NIK</label>
			<input type="text" id="kz_nik" class="form-control" value="{{$emp_id}}" readonly>
		</div>
		<div class="col-xs-4">
			<label for="kz_nama">Nama</label>
			<input type="text" id="kz_nama" class="form-control" value="{{$name}}" readonly>
		</div>
		<div class="col-xs-4">
			<label for="kz_bagian">Bagian</label>
			<input type="text" id="kz_bagian" class="form-control" value="{{$section}} - {{$group}}" readonly>
		</div>
		<div class="col-xs-4">
			<label for="kz_leader">Nama Leader</label><br>
			<select id="kz_leader" class="form-control select2" style=" width: 100% !important;">
				<option value="">Pilih Leader</option>
				@foreach($subleaders as $subleader)
				<option value="{{ $subleader->employee_id }}">{{ $subleader->name }} - {{ $subleader->position }}</option>
				@endforeach
			</select>
			<!-- <input type="text" id="kz_sub_leader" class="form-control"> -->
		</div>
		<div class="col-xs-4">
			<label for="kz_tujuan">Area Kaizen</label><br>
			<select id="kz_tujuan" class="form-control select2" style="width: 100% !important;">
				<option value="">Pilih Area</option>
				@foreach($sc as $scc)
				<option value="{{ $scc->section }}">{{ $scc->section }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-3">
			<label for="kz_judul">Purpose Kaizen</label>
			<select class="form-control select2" id="kz_purpose" data-placeholder='Pilih Purpose'>
				<option value="">&nbsp;</option>
				<option>Save time</option>
				<option>5S</option>
				<option>Safety</option>
				<option>Lingkungan</option>
				<option>Save Cost (Selain Time)</option>
			</select>
		</div>
		<div class="col-xs-9">
			<label for="kz_judul">Judul Usulan</label>
			<input type="text" id="kz_judul" class="form-control" placeholder="Judul usulan">
		</div>
	</div>
	<div class="row">
		<div class="col-xs-6">
			<label for="kz_sekarang">Kondisi Sekarang</label>
			<!-- <textarea id="kz_sekarang" class="form-control" placeholder="Kodisi Sekarang . . ."></textarea> -->
			<textarea class="form-control" id="kz_sekarang"></textarea>
		</div>
		<div class="col-xs-6">
			<label for="kz_perbaikan">Usulan Perbaikan</label>
			<textarea class="form-control" id="kz_perbaikan"></textarea>
			<!-- <textarea id="kz_perbaikan" class="form-control" placeholder="Usulan Perbaikan . . ."></textarea> -->
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<label>Estimasi Hasil</label><br>
			<table class="table" style="color:white">
				<thead>
					<tr>
						<th colspan="4">Perhitungan Kaizen / Efek</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>Manpower</th>
						<td>:&nbsp;<input type="text" class="form-control" placeholder="Dalam satu bulan" id="kz_mp" style="width: 70%; display: inline-block;">&nbsp; Menit</td>
						<td>X &nbsp; 500</td>
						<td>= &nbsp;Rp. &nbsp;<input type="text" class="form-control" id="kz_mp_bulan" style="width: 80%; display: inline-block;" readonly>&nbsp; / bulan</td>
					</tr>
					<tr>
						<th>Space</th>
						<td>:&nbsp;<input type="text" class="form-control" placeholder="Total" id="kz_space" style="width: 70%; display: inline-block;">&nbsp; m<sup>2</sup></td>
						<td>X &nbsp; 500</td>
						<td>= &nbsp;Rp. &nbsp;<input type="text" class="form-control" id="kz_space_bulan" style="width: 80%; display: inline-block;" readonly>&nbsp; / bulan</td>
					</tr>
					<tr>
						<th>Other (Material,listrik, kertas, dll)</th>
						<td>:&nbsp;<input type="text" class="form-control" placeholder="" id="kz_material" style="width: 70%; display: inline-block;"></td>
						<td>X &nbsp; 500</td>
						<td>= &nbsp;Rp. &nbsp;<input type="text" class="form-control" id="kz_material_bulan" style="width: 80%; display: inline-block;" readonly>&nbsp; / bulan</td>
					</tr>
					<tr>
						<td colspan="3" style="text-align: right; vertical-align: middle; font-size: 30px; padding-right: 0px">Total = Rp. &nbsp; </td>
						<td><input type="text" class="form-control" style="width: 100%; display: inline-block; font-size: 22px; font-weight: bold" readonly id="total"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<button type="button" class="btn btn-primary pull-right" id="kz_buat"><i class="fa fa-edit"></i>&nbsp; Buat Kaizen</button>
			<button type="button" class="btn btn-default" onclick="window.history.back();" ><i class="fa fa-share"></i>&nbsp; Kembali</button>
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

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no data";
				}
			}
		});
	})

	$("#kz_mp").on('keypress keyup blur', function() {
		$(this).val($(this).val().replace(/[^\d].+/, ""));
		if ((event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	})

	$("#kz_mp").on('change keyup paste', function() {
		$("#kz_mp_bulan").val($(this).val() * 500);
		total();
	})

	$("#kz_space").on('keypress keyup blur', function() {
		$(this).val($(this).val().replace(/[^\d].+/, ""));
		if ((event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	})

	$("#kz_space").on('change keyup paste', function() {
		$("#kz_space_bulan").val($(this).val() * 500);
		total();
	})

	$("#kz_material").on('keypress keyup blur', function() {
		$(this).val($(this).val().replace(/[^\d].+/, ""));
		if ((event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	})

	$("#kz_material").on('change keyup paste', function() {
		$("#kz_material_bulan").val($(this).val() * 500);
		total();
	})

	function total() {
		var total = parseInt($("#kz_mp_bulan").val()) + parseInt($("#kz_space_bulan").val()) + parseInt($("#kz_material_bulan").val());

		if (isNaN(total)) {
			total = 0;
		}

		$("#total").val(total);
	}

	$("#kz_buat").click( function() {
		var data = {
			employee_id: $("#kz_nik").val(),
			employee_name: $("#kz_nama").val(),
			propose_date: $("#kz_tanggal").val(),
			section: $("#kz_bagian").val(),
			leader: $("#kz_leader").val(),
			title: $("#kz_judul").val(),
			area_kz: $("#kz_tujuan").val(),
			purpose: $("#kz_purpose").val(),
			condition: CKEDITOR.instances.kz_sekarang.getData(),
			improvement: CKEDITOR.instances.kz_perbaikan.getData()
		};

		// console.log(data);

				// if ($("kz_sub_leader").val() != '' && $("kz_judul").val() != '') {
					$.post('{{ url("post/ekaizen") }}', data, function(result, status, xhr){
						console.log(result.datas);
					})
				// }

			});

	CKEDITOR.replace('kz_sekarang' ,{
		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	});

	CKEDITOR.replace('kz_perbaikan' ,{
		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	});
</script>
@endsection