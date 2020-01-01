@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	h1{
		text-align: center;
		font-weight: bold;
	}
	thead>tr>th{
		font-size: 16px;
	}
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	#loading { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-8 col-xs-offset-2">
			<div class="box">
				<div class="box-title">
					<h1>
						{{ $title }}
						<small><span class="text-purple"> {{ $title_jp }}</span></small>
					</h1>
				</div>
				<div class="box-body" style="padding: 30px;">
					<div class="tab-content">
						<div class="row">
							<form id="data" method="post" enctype="multipart/form-data" autocomplete="off">

								
								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" align="right" style="padding: 0px;">
										<span style="font-weight: bold; font-size: 16px;">Bagian:<span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<select class="form-control select2" data-placeholder="Pilih Bagian" name="sub_section" id="sub_section" style="width: 100% height: 35px; font-size: 15px;" required>
											<option value=""></option>
											@php
											$group = array();
											@endphp
											@foreach($employees as $employee)
											@if(!in_array($employee->section.'-'.$employee->group, $group))
											<option value="{{ $employee->section }}">{{ $employee->section }}-{{ $employee->group }}</option>
											@php
											array_push($group, $employee->section.'-'.$employee->group);
											@endphp
											@endif
											@endforeach
										</select>
									</div>
								</div>					

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" align="right" style="padding: 0px;">
										<span style="font-weight: bold; font-size: 16px;">Prioritas:<span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<select class="form-control select2" data-placeholder="Pilih Prioritas Pengerjaan" name="priority" id="priority" style="width: 100% height: 35px; font-size: 15px;" required>
											<option value=""></option>
											<option value="normal">Normal</option>
											<option value="urgent">Urgent</option>
										</select>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" align="right" style="padding: 0px;">
										<span style="font-weight: bold; font-size: 16px;">Jenis Pekerjaan:<span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<select class="form-control select2" data-placeholder="Pilih Jenis Pekerjaan" name="type" id="type" style="width: 100% height: 35px; font-size: 15px;" required>
											<option value=""></option>
											<option value="pembuatan baru">Pembuatan Baru</option>
											<option value="perbaikan ketidaksesuain">Perbaikan Ketidaksesuain</option>
											<option value="lain-lain">Lain-lain</option>
										</select>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" align="right" style="padding: 0px;">
										<span style="font-weight: bold; font-size: 16px;">Nama Barang:<span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<input type="text" class="form-control" name="item_name" id="item_name" rows='1' placeholder="Nama Barang" style="width: 100%; font-size: 15px;" required>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" align="right" style="padding: 0px;">
										<span style="font-weight: bold; font-size: 16px;">Jumlah:<span class="text-red">*</span></span>
									</div>
									<div class="col-xs-4">
										<input class="form-control" type="number" name="quantity" id="quantity" placeholder="Jumlah Barang" style="width: 100%; height: 33px; font-size: 15px;" required>
									</div>
								</div>
								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" align="right" style="padding: 0px;">
										<span style="font-weight: bold; font-size: 16px;">Material Awal:<span class="text-red">*</span></span>
									</div>
									<div class="col-xs-4">
										<select class="form-control select2" data-placeholder="Pilih Material Awal" name="material" id="material" style="width: 100% height: 35px; font-size: 15px;" required>
											<option value=""></option>
											@foreach($materials as $material)
											@if($material->remark == 'raw')
											<option value="{{ $material->material_description }}">{{ $material->material_description }}</option>
											@endif
											@endforeach
											<option value="Lainnya">LAINNYA</option>
										</select>
									</div>
									<div class="col-xs-4">
										<input class="form-control" type="text" name="material-other" id="material-other" placeholder="Material Lainnya" style="width: 100% height: 35px; font-size: 15px;">
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" align="right" style="padding: 0px;">
										<span style="font-weight: bold; font-size: 16px;">Uraian Permintaan:<span class="text-red">*</span></span>
									</div>
									<div class="col-xs-8">
										<textarea class="form-control" rows='3' name="problem_desc" id="problem_desc" placeholder="Uraian Permintaan / Masalah" style="width: 100%; font-size: 15px;" required></textarea>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" align="right" style="padding: 0px;">
										<span style="font-weight: bold; font-size: 16px;">Target Selesai:<span class="text-red">*</span></span>
									</div>
									<div class="col-xs-4">
										<div class="input-group date">
											<div class="input-group-addon bg-default">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control datepicker" name="request_date" id="request_date" placeholder="Pilih Tanggal" required>
										</div>
									</div>
								</div>

								<div class="col-xs-12" style="padding-bottom: 1%;">
									<div class="col-xs-3" align="right" style="padding: 0px;">
										<span style="font-weight: bold; font-size: 16px;">Lampiran:&nbsp;&nbsp;</span>
									</div>
									<div class="col-xs-8">
										<input style="height: 37px;" class="form-control" type="file" name="upload_file" id="upload_file" accept="text/plain">
									</div>
								</div>

								<div class="col-xs-12" style="padding-right: 12%;">
									<br>
									<button type="submit" class="btn btn-success pull-right">Submit</button>
								</div>

							</form>

						</div>
					</div>
				</div>

			</div>			
		</div>
	</div>


</section>

@endsection
@section('scripts')
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>



<script>

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");	
		$('.select2').select2();
		var opt = $("#sub_section option").sort(function (a,b) { return a.value.toUpperCase().localeCompare(b.value.toUpperCase()) });
		$("#sub_section").append(opt);
		$('#sub_section').prop('selectedIndex', 0).change();

		$('#material-other').hide();


		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});
	});

	$('#material').on('change', function() {
		if(this.value == 'Lainnya'){
			$('#material-other').show();
		}else{
			$('#material-other').hide();
		}
	});


	$('form').on('focus', 'input[type=number]', function (e) {
		$(this).on('wheel.disableScroll', function (e) {
			e.preventDefault()
		})
	})

	$('form').on('blur', 'input[type=number]', function (e) {
		$(this).off('wheel.disableScroll')
	})

	$("form#data").submit(function(e) {
		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("workshop/create_wjo") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {

				$('#sub_section').prop('selectedIndex', 0).change();
				$("#item_name").val("");
				$("#quantity").val("");
				$("#request_date").val("");
				$('#priority').prop('selectedIndex', 0).change();
				$('#type').prop('selectedIndex', 0).change();
				$("#material").prop('selectedIndex', 0).change();
				$("#material-other").val("");
				$("#problem_desc").val("");
				$("#upload_file").val("");

				openSuccessGritter('Success', result.message);
			},
			error: function(result, status, xhr){
				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});


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