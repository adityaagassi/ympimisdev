@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">

	#loading, #error { display: none; }

	table.table-bordered > thead > tr > th{
		color: white;
		background-color: black;
	}
	table.table-bordered > tbody > tr > td{
		color: black;
		background-color: white;
	}

	#loading { display: none; }


	.radio {
		display: inline-block;
		position: relative;
		padding-left: 35px;
		margin-bottom: 12px;
		cursor: pointer;
		font-size: 16px;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}

	/* Hide the browser's default radio button */
	.radio input {
		position: absolute;
		opacity: 0;
		cursor: pointer;
	}

	/* Create a custom radio button */
	.checkmark {
		position: absolute;
		top: 0;
		left: 0;
		height: 25px;
		width: 25px;
		background-color: #ccc;
		border-radius: 50%;
	}

	/* On mouse-over, add a grey background color */
	.radio:hover input ~ .checkmark {
		background-color: #ccc;
	}

	/* When the radio button is checked, add a blue background */
	.radio input:checked ~ .checkmark {
		background-color: #2196F3;
	}

	/* Create the indicator (the dot/circle - hidden when not checked) */
	.checkmark:after {
		content: "";
		position: absolute;
		display: none;
	}

	/* Show the indicator (dot/circle) when checked */
	.radio input:checked ~ .checkmark:after {
		display: block;
	}

	/* Style the indicator (dot/circle) */
	.radio .checkmark:after {
		top: 9px;
		left: 9px;
		width: 8px;
		height: 8px;
		border-radius: 50%;
		background: white;
	}

	#tableResult > thead > tr > th {
		border: 1px solid black;
	}

	#tableResult > tbody > tr > td {
		border: 1px solid #b0bec5;
	}

</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		Audit Stocktaking
	</h1>
	<ol class="breadcrumb">
		<?php $user = STRTOUPPER(Auth::user()->username) ?>

		<button class="btn btn-success btn-sm" style="margin-right: 5px" onclick="location.reload()">
			<i class="fa fa-refresh"></i>&nbsp;&nbsp;Reload Page
		</button>

	</ol>
</section>
@stop
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if ($errors->has('password'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Alert!</h4>
		{{ $errors->first() }}
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
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, Please Wait . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-12" style="text-align: center;margin-top:-30px;padding: 0">
				<h3 class="box-title" style="color: white;margin-top: 10px;font-size: 28px;font-weight: bold;background-color: #009688;padding: 10px">Patrol Daily Shift 1 & 2<span class="text-purple"></span></h3>

			</div>
			<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
				<span style="padding: 0px;color: white; font-size: 30px;font-weight: bold;">Audit Date :</span>
				<span style="padding: 0px;color: white; font-size: 30px;font-weight: bold;"><?= date("d F Y") ?></span>
				<br>
				<span style="padding: 0px;color: white; font-size: 30px;font-weight: bold;">Auditor :</span>
				<span style="padding: 0px;color: white; font-size: 30px;font-weight: bold;" id="employee_name"></span>


				<span id="category" style="display: none"></span>
			</div>

			<input type="hidden" id="employee_id" class="employee_id" >

			<div class="col-xs-12" style="padding: 0">

				<table class="table table-bordered" style="width: 100%; color: white;" id="tableResult">
					<thead style="font-weight: bold; background-color: rgb(220,220,220);">
						<tr>
							<th style="border-right: 1px solid white">Location</th>
							<th style="border-right: 1px solid white">Audit Type</th>
							<th style="border-right: 1px solid white">Photo</th>
							<th style="border-right: 1px solid white">Problem</th>
							<th style="border-right: 1px solid white">PIC</th>
							<!-- <th style="color: white;border-right: 1px solid white;width: 1%">Act</th> -->
						</tr>
					</thead>
					<tbody id="body_cek">
						<tr class="member">
							<td width="10%">
								<select class="form-control select3" id="location" data-placeholder="Location" style="width: 100%; font-size: 20px;">
									<option></option>
									@foreach($location as $loc)
									<option value="{{ $loc }}">{{ $loc }}</option>
									@endforeach
								</select>
							</td>
							<td width="10%">
								<!-- <input type="text" class="form-control patrol_detail" id="patrol_detail" name="patrol_detail" placeholder="Patrol Detail" required=""> -->
								<select class="form-control select3 patrol_detail" id="patrol_detail" data-placeholder="Jenis Temuan" style="width: 100%; font-size: 20px;">
									<option></option>
									<option value="Positive Finding">Positive Finding</option>
									<option value="Negative Finding">Negative Finding</option>
								</select>
							</td>
							<td width="10%">
								<center>
									<input type="file" onchange="readURL(this,'');" id="file" style="display:none" class="file">
									<button class="btn btn-primary btn-lg" id="btnImage" value="Photo" onclick="buttonImage(this)">Photo</button>

									<img width="150px" id="blah" src="" style="display: none" alt="your image" />
								</center>
							</td>
							<td width="10%">
								<textarea id="patrol_note" height="100%" class="form-control note"></textarea>
							</td>
							<td width="10%">
								<select class="form-control select2 patrol_pic" id="patrol_pic" data-placeholder="Pilih PIC" style="width: 100%; font-size: 20px;">
									<option></option>
									@foreach($auditee as $audite)
									<option value="{{ $audite->name }}">{{ $audite->employee_id }} - {{ $audite->name }}</option>
									@endforeach
								</select>
								<!-- <input type="hidden" id="patrol_pic_name"> -->
							</td>
							<!-- <td><button class="btn btn-danger" onclick="delete_confirmation(this)"><i class="fa fa-close"></i></button></td> -->
						</tr>
					</tbody>
				</table>
				<br>
				<button class="btn btn-success" style="width: 100%;font-size: 25px" onclick="cek()"><i class="fa fa-check"></i> Submit</button>
			</div>
		</div>
	</div>
</section>
	
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Input Confirmation</h4>
			</div>
			<div class="modal-body">
				
			</div>
			<div class="modal-footer">
				<a  class="btn btn-danger" href="{{ url('') }}">Tutup</button>
					<!-- <a href="{{ url("index/audit_iso/cek_report/")}}" class="btn btn-success btn-sm" target="_blank" style="color:white;margin-right: 5px"><i class="fa fa-file-pdf-o"></i> Cek Laporan Hasil {{ $page }} </a> -->
					<a id="modalDeleteButton" href="#" type="button" class="btn btn-success">Lihat Report Hasil Audit</a>
				</div>
			</div>
		</div>
	</div>
	@endsection
	@section('scripts')
	<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
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


		var counter = 2;
		var add_point = [];

		jQuery(document).ready(function() {
			$('body').toggleClass("sidebar-collapse");
			$('.select2').prop('selectedIndex', 0).change();
			$('.select2').select2({
				dropdownAutoWidth : true,
				allowClear:true
			});

			$('.select3').select2({
				minimumResultsForSearch : -1,
				dropdownAutoWidth : true,
				allowClear:true
			});
		})


		$('#category').html("Audit Stocktaking");
		$('#employee_name').html("{{$employee->name}}");
		$('#employee_id').val("{{$employee->employee_id}}");

		function buttonImage(elem) {
			$(elem).closest("td").find("input").click();
			// console.log(input);
		}

		function readURL(input,idfile) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();

				reader.onload = function (e) {
					var img = $(input).closest("td").find("img");
					$(img).show();
					$(img)
					.attr('src', e.target.result);
				};

				reader.readAsDataURL(input.files[0]);
			}

			$(input).closest("td").find("button").hide();
			// $('#btnImage'+idfile).hide();
		}

		function cek() {
			// if (confirm('Apakah Anda yakin?')) {
				$('#loading').show();

				var audit_data = [];
				var patrol_detail = [];
				var note = [];
				var patrol_pic = [];
				var file = [];

				var len = $('.member').length;

				var formData = new FormData();

				formData.append('jumlah', len);				
				formData.append('category', $('#category').text());
				formData.append('location', $('#location').val());
				formData.append('auditor_id', $('#employee_id').val());
				formData.append('auditor_name',  $('#employee_name').text());

				$('.file').each(function(i, obj) {
					formData.append('file_datas_'+i, $(this).prop('files')[0]);
					var file=$(this).val().replace(/C:\\fakepath\\/i, '').split(".");

					formData.append('extension_'+i, file[1]);
					formData.append('foto_name_'+i, file[0]);
				})	

				$('.patrol_detail').each(function(i, obj) {
					formData.append('patrol_detail_'+i, $(this).val());
				})

				$('.note').each(function(i, obj) {
					formData.append('note_'+i, $(this).val());
				})

				$('.patrol_pic').each(function(i, obj) {
					formData.append('patrol_pic_'+i, $(this).val());
				})	


				$.ajax({
					url:"{{ url('post/audit_patrol_stocktaking') }}",
					method:"POST",
					data:formData,
					dataType:'JSON',
					contentType: false,
					cache: false,
					processData: false,
					success: function (response) {
						$("#loading").hide();
						$("#patrol_detail").val("").trigger("change");
						$("#patrol_note").val("");
						$("#patrol_pic").val("").trigger("change");
						$("#blah").hide();
						$("#btnImage").show();
						openSuccessGritter("Success", "Audit Berhasil Disimpan");
						// location.reload();
					},
					error: function (response) {
						console.log(response.message);
					},
				})
			}
		// }

	// function selectemployee(){
	//          var pic = document.getElementById("patrol_pic").value;

	//          $.ajax({
	//           url: "{{ url('index/audit_iso/get_nama') }}?auditee=" +pic, 
	//           type : 'GET', 
	//           success : function(data){
	//            var obj = jQuery.parseJSON(data);
	//            $('#patrol_pic_nama').val(obj[0].name);
	//          }
	//        });      
	//    }

	function delete_confirmation(elem) {
		$(elem).closest('tr').remove();
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '2000'
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

</script>
@endsection