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
			<input type="text" id="kz_bagian" class="form-control" value="Management Information System" readonly>
		</div>
		<div class="col-xs-4">
			<label for="kz_sub_leader">Nama Leader</label><br>
			<select id="kz_sub_leader" class="form-control select2" style=" width: 100% !important;">
				<option value="">Pilih Leader</option>
				@foreach($subleaders as $subleader)
				<option value="{{ $subleader->name }}">{{ $subleader->name }} - {{ $subleader->position }}</option>
				@endforeach
			</select>
			<!-- <input type="text" id="kz_sub_leader" class="form-control"> -->
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
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
			<label><input type="radio" name="radio1"> Rp 0,- s/d Rp 100.000,-</label>
			<label><input type="radio" name="radio1"> Rp 100.001,- s/d Rp 500.000,-</label>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<button type="button" class="btn btn-primary" id="kz_buat"><i class="fa fa-edit"></i>&nbsp; Buat Kaizen</button>
			<button type="button" class="btn btn-default pull-right" onclick="window.history.back();" ><i class="fa fa-share"></i>&nbsp; Kembali</button>
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

	$("#kz_buat").click( function() {
		var data = {
			employee_id: $("#kz_nik").val(),
			employee_name: $("#kz_nama").val(),
			propose_date: $("#kz_tanggal").val(),
			section: 'dd',
			sub_leader: $("#kz_sub_leader").val(),
			title: $("#kz_judul").val(),
			condition: CKEDITOR.instances.kz_sekarang.getData(),
			improvement: CKEDITOR.instances.kz_perbaikan.getData()
		};

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