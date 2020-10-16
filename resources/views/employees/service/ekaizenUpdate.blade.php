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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-4">
			<label for="kz_tanggal">Tanggal</label>
			<input type="text" id="kz_tanggal" class="form-control" value="{{ $data[0]->propose_date }}" readonly>
		</div>
		<div class="col-xs-4">
			<label for="kz_nik">NIK</label>
			<input type="text" id="kz_nik" class="form-control" value="{{ $data[0]->employee_id }}" readonly>
		</div>
		<div class="col-xs-4">
			<label for="kz_nama">Nama</label>
			<input type="text" id="kz_nama" class="form-control" value="{{ $data[0]->employee_name}}" readonly>
		</div>
		<div class="col-xs-4">
			<label for="kz_bagian">Bagian</label>
			<input type="text" id="kz_bagian" class="form-control" value="{{ $data[0]->section }}" readonly>
		</div>
		<div class="col-xs-4">
			<label for="kz_leader">Nama Leader</label><br>
			<select id="kz_leader" class="form-control select2" style=" width: 100% !important;">
				<option value="">Pilih Leader</option>
				@foreach($subleaders as $subleader)
				@if($subleader->employee_id == $data[0]->leader)
				<option value="{{ $subleader->employee_id }}" selected>{{ $subleader->name }} - {{ $subleader->position }}</option>
				@else
				<option value="{{ $subleader->employee_id }}">{{ $subleader->name }} - {{ $subleader->position }}</option>
				@endif
				@endforeach
			</select>
		</div>
		<div class="col-xs-4">
			<label for="kz_tujuan">Pilih Area Kaizen</label><br>
			<select id="kz_tujuan" class="form-control select2" style="width: 100% !important;">
				<option value="">Pilih Area Kaizen</option>
				@foreach($sc as $scc)
				@if($scc->section == $data[0]->area)
				<option value="{{ $scc->section }}" selected>{{ $scc->section }}</option>
				@else
				<option value="{{ $scc->section }}">{{ $scc->section }}</option>
				@endif
				@endforeach
			</select>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-3">
			<label for="kz_judul">Purpose Kaizen</label>
			<select class="form-control select2" id="kz_purpose" data-placeholder='Pilih Purpose'>
				<option value="">&nbsp;</option>
				<option <?php if($data[0]->purpose == "Save Cost") echo "selected"; ?> >Save Cost</option>
				<option <?php if($data[0]->purpose == "5S") echo "selected"; ?> >5S</option>
				<option <?php if($data[0]->purpose == "Safety") echo "selected"; ?> >Safety</option>
				<option <?php if($data[0]->purpose == "Lingkungan") echo "selected"; ?> >Lingkungan</option>
			</select>
		</div>
		<div class="col-xs-9">
			<label for="kz_judul">Judul Usulan</label>
			<input type="text" id="kz_judul" class="form-control" placeholder="Judul usulan" value="{{ $data[0]->title }}">
		</div>
	</div>
	<div class="row">
		<div class="col-xs-6">
			<label for="kz_sekarang">Kondisi Sekarang</label>
			<textarea class="form-control" id="kz_sekarang"><?php echo $data[0]->condition ?></textarea>
		</div>
		<div class="col-xs-6">
			<label for="kz_perbaikan">Usulan Perbaikan</label>
			<textarea class="form-control" id="kz_perbaikan"><?php echo $data[0]->improvement ?></textarea>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<label>Catatan</label>
			<table class="table" style="color: white">
				<tr><th>Foreman</th><th>Manager</th></tr>
				<tr><td><?php echo $data[0]->foreman_note ?></td><td><?php echo $data[0]->manager_note ?></td></tr>
			</table>
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
						<th>Manpower<span class="text-red">*</span></th>
						<td>:&nbsp;<input type="text" class="form-control" placeholder="Dalam satu hari" id="kz_mp" style="width: 70%; display: inline-block;">&nbsp; Menit</td>
						<td>X &nbsp;  Rp <d id="std_mp"></d></td>
						<td>= &nbsp;Rp. &nbsp;<input type="text" class="form-control" id="kz_mp_bulan" style="width: 75%; display: inline-block;" readonly value="0">&nbsp; / bulan</td>
					</tr>
					<tr>
						<th>Tempat<span class="text-red">*</span></th>
						<td>:&nbsp;<input type="text" class="form-control" placeholder="Total" id="kz_space" style="width: 70%; display: inline-block;">&nbsp; m<sup>2</sup></td>
						<td>X &nbsp; Rp <d id="std_space"></d></td>
						<td>= &nbsp;Rp &nbsp;<input type="text" class="form-control" id="kz_space_bulan" style="width: 75%; display: inline-block;" readonly value="0"></td>
					</tr>
					<tr>
						<th>Lainnya<span class="text-red">*</span></th>
						<td>:&nbsp; &nbsp;  
							<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalLainnya">Pilih <i class="fa fa-hand-pointer-o"></i></button>
						</td>
						<td></td>
						<td>= &nbsp;Rp &nbsp;<input type="text" class="form-control" id="kz_material_bulan" style="width: 75%; display: inline-block;" readonly value="0">&nbsp; / bulan</td>
					</tr>
					<tr>
						<td colspan="2">
							<span class="text-red">* Diisi minimal salah satu</span>
						</td>
						<td style="text-align: right; vertical-align: middle; font-size: 30px; padding-right: 0px">Total = Rp &nbsp; </td>
						<td><input type="text" class="form-control" style="width: 100%; display: inline-block; font-size: 22px; font-weight: bold" readonly id="total"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<button type="button" class="btn btn-primary pull-right" id="kz_buat"><i class="fa fa-edit"></i>&nbsp; Update Kaizen</button>
			<button type="button" class="btn btn-default" onclick="window.history.back();" ><i class="fa fa-share"></i>&nbsp; Kembali</button>
		</div>
	</div>

	<div class="modal fade" id="modalLainnya">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<table class="table table-bordered" id="tabel_other">
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
	var std_mp = 0;
	var std_space = 0;
	var other = [];
	var tmp = [];
	var oth = 0;
	var cal = [];
	var kz_mp = 0, kz_area = 0;

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		$('.select2').select2({
			language : {
				noResults : function(params) {
					return "There is no data";
				}
			}
		});


		getCost();
	})

	function getCost() {
		var last = 0;
		var other_child = "";

		$.get('{{ url("fetch/cost") }}', function(result, status, xhr){
			$.each(result, function(index, value){
				if (value.category == "manpower") {
					std_mp = value.cost;
					$("#std_mp").text(std_mp);
				} else if(value.category == "space") {
					std_space = value.cost;
					$("#std_space").text(std_space);
				} else if (value.category == "other") {
					other.push([value.cost_name, value.cost, value.unit, value.frequency, value.id]);
				}
			})

			tmp = new Array(other.length);

			var arr = <?php echo json_encode($data)?>;
			var kz_mp2 = 0;
			var kz_space2 = 0;
			tots = 0;

			$.each(other, function(index, value){
				$.each(arr, function(index2, value2){
					if (value2.id_cost == other[index][4]) {
						v = " value="+parseInt(value2.cost);
						cst = parseInt(value2.cost);

						if (parseInt(other[index][1]) != 1) {
							cst = cst * parseInt(other[index][1]) * 20;
						}

						v2 = " value="+cst.toLocaleString('es-ES');
						tots += parseInt(cst);
						arr.splice(index2, 1);
						return false;
					} else if (value2.id_cost == 1) {
						kz_mp2 = value2.cost * parseFloat($("#std_mp").text()) * 20;
						$("#kz_mp").val(value2.cost);
						$("#kz_mp_bulan").val((value2.cost * parseFloat($("#std_mp").text()) * 20).toLocaleString('es-ES'));
						v = "";
						v2 = "";
						arr.splice(index2, 1);
						return false;
					}
					else if (value2.id_cost == 2) {
						kz_space2 = value2.cost * parseFloat($("#std_space").text()) * 20;
						$("#kz_space").val(value2.cost);
						$("#kz_space_bulan").val((value2.cost * parseFloat($("#std_space").text()) * 20).toLocaleString('es-ES'));
						v = "";
						v2 = "";
						arr.splice(index2, 1);
						return false;
					}
					else {
						v = "";
						v2 = "";
					}
				})

					// console.log(value2.id_cost+" "+other[index][4]);

					other_child += "<tr><th>"+other[index][0]+"</th><td width='20%'><input type='text' id='other_input_"+index+"' class='form-control' onkeyup='otherFill(this.id,"+other[index][1]+")' placeholder='Dlm satu bulan' "+v+"></td><td>&nbsp; "+other[index][2]+" &nbsp; X &nbsp; Rp "+other[index][1]+" </td><td width='30%'><div class='input-group'><span class='input-group-addon'>Rp </span><input type='text' id='other_"+index+"' class='form-control' "+v2+" readonly></div></td></tr>";

					last = index;
				})
			other_child += "<tr><td colspan='3' style='padding-right:5px'><p class='pull-right'><b>Total</b></p></td><td><div class='input-group'><span class='input-group-addon'>Rp </span><input type='text' id='other_total' class='form-control' value='"+tots.toLocaleString('es-ES')+"' readonly></div></td></tr>";

			$("#tabel_other").append(other_child);
			$("#kz_material_bulan").val(tots.toLocaleString('es-ES'));
			kz_mp = kz_mp2;
			kz_area = kz_space2;
			$("#total").val((kz_mp2 + kz_space2 + tots).toLocaleString('es-ES'));
			oth = tots;

			$.each(other, function(index, value){
				$("#other_input_"+index).on('keypress keyup blur', function() {
					$(this).val($(this).val().replace(/[^\d].+/, ""));
					if ((event.which < 48 || event.which > 57)) {
						event.preventDefault();
					}
				})
			})
		})
	}

	$("#kz_mp").on('keypress keyup blur', function() {
		$(this).val($(this).val().replace(/[^\d].+/, ""));
		if ((event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	})

	$("#kz_mp").on('change keyup paste', function() {
		kz_mp = ($(this).val() * std_mp).toFixed(2) * 20;
		$("#kz_mp_bulan").val((($(this).val() * std_mp).toFixed(2) * 20).toLocaleString('es-ES'));
		total();
	})

	$("#kz_space").on('keypress keyup blur', function() {
		$(this).val($(this).val().replace(/[^\d].+/, ""));
		if ((event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	})

	$("#kz_space").on('change keyup paste', function() {
		var temp = ($(this).val() * std_space).toFixed(0);
		kz_area = temp;
		$("#kz_space_bulan").val(temp.toLocaleString('es-ES'));
		console.log(temp.toLocaleString('es-ES'));
		total();
	})

	function total() {
		var total = parseInt(kz_mp) + parseInt(kz_area) + parseInt(oth);

		if (isNaN(total)) {
			total = 0;
		}

		$("#total").val(total.toLocaleString('es-ES'));
	}

	function otherFill(elem, std) {
		ids = elem.split("_")[2];
		var dd = $("#"+elem).val();
		$("#other_"+ids).val((dd * std).toLocaleString('es-ES'));
		tmp[ids] = dd * std;

		var total = 0;
		for (var i = 0; i < tmp.length; i++) {
			total += tmp[i] << 0;
		}

		$("#other_total").val(total.toLocaleString('es-ES'));
		$("#kz_material_bulan").val(total.toLocaleString('es-ES'));

		oth = total;

		var total2 = parseInt(kz_mp) + parseInt(kz_area) + parseInt(total);

		if (isNaN(total2)) {
			total2 = 0;
		}

		$("#total").val(total2.toLocaleString('es-ES'));
	}

	$("#kz_buat").click( function() {
		if ($("#kz_leader").val() == "") {
			alert("Kolom Leader Harap diisi");
			$("html").scrollTop(0);
			return false;
		}

		if ($("#kz_tujuan").val() == "") {
			alert("Kolom Area Kaizen Harap diisi");
			$("html").scrollTop(0);
			return false;
		}

		if ($("#kz_purpose").val() == "") {
			alert("Kolom Purpose Harap diisi");
			$("html").scrollTop(0);
			return false;
		}

		if ($("#kz_judul").val() == "") {
			alert("Kolom Judul Usulan Harap diisi");
			$("html").scrollTop(0);
			return false;
		}


		cal = [];

		if ($("#kz_mp").val() != "" && $("#kz_mp").val() != "0") {
			cal.push([1,parseInt($("#kz_mp").val())]);
		}

		if ($("#kz_space").val() != "" && $("#kz_space").val() != "0") {
			cal.push([2,parseInt($("#kz_space").val())]);
		}

		for (var i = 0; i < other.length; i++) {
			if ($("#other_input_"+i).val() != "" && $("#other_input_"+i).val() != "0") {
				cal.push([other[i][4], parseInt($("#other_input_"+i).val())]);
			}
		}

		$("#loading").show();

		var data = {
			id: "{{ $data[0]->id }}",
			employee_id: $("#kz_nik").val(),
			employee_name: $("#kz_nama").val(),
			propose_date: $("#kz_tanggal").val(),
			section: $("#kz_bagian").val(),
			leader: $("#kz_leader").val(),
			title: $("#kz_judul").val(),
			area_kz: $("#kz_tujuan").val(),
			purpose: $("#kz_purpose").val(),
			estimasi: cal,
			condition: CKEDITOR.instances.kz_sekarang.getData(),
			improvement: CKEDITOR.instances.kz_perbaikan.getData()
		};

		// console.log(data);

				// if ($("kz_sub_leader").val() != '' && $("kz_judul").val() != '') {
					$.post('{{ url("update/ekaizen") }}', data, function(result, status, xhr){
						$("#loading").hide();
						window.history.go(-1);
					})
				// }

			});

	CKEDITOR.replace('kz_sekarang' ,{
		filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
	});

	CKEDITOR.replace('kz_perbaikan' ,{
		filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}'
	});
</script>
@endsection