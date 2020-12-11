@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">

	#loading, #error { display: none; }

	table.table-bordered > thead > tr > th{
		color: black;
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
		Audit Patrol 5S
	</h1>
	<ol class="breadcrumb">
		<?php $user = STRTOUPPER(Auth::user()->username) ?>

		<!-- <button class="btn btn-success btn-sm" style="margin-right: 5px" onclick="location.reload()">
			<i class="fa fa-refresh"></i>&nbsp;&nbsp;Ganti Lokasi
		</button> -->

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
			<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 0px">
					<thead>
						<tr>
							<th style="width:15%; background-color: #0e92eb; color: white; text-align: center; padding:0;font-size: 18px;border: 1px solid black" colspan="3">General Information</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="padding: 0px; background-color: rgb(32, 145, 121); text-align: center; color: white; font-size:20px; width: 30%;border: 1px solid black">Patrol Date</td>
							<td colspan="2" style="padding: 0px; background-color: rgb(0, 217, 255); text-align: center; color: #000000; font-size: 20px;border: 1px solid black"><?= date("d F Y") ?></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(32, 145, 121); text-align: center; color: white; font-size:20px; width: 30%;border: 1px solid black">Auditor</td>
							<td colspan="2" style="padding: 0px; background-color: rgb(0, 217, 255); text-align: center; color: #000000; font-size: 20px;border: 1px solid black" id="employee_name"><input type="hidden" id="employee_id"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: rgb(32, 145, 121); text-align: center; color: white; font-size:20px; width: 30%;border: 1px solid black">Category</td>
							<td colspan="2" style="padding: 0px; background-color: rgb(0, 217, 255); text-align: center; color: #000000; font-size: 20px;border: 1px solid black" id="category"></td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="col-xs-12" style="overflow-x: scroll;padding: 0">

				<div class="pull-right">
					<a type="button" class="btn btn-success" onclick='tambah();'><i class='fa fa-plus' ></i> Tambah Data</a>
				</div>

				<br><br>
				

				<table class="table table-bordered" style="width: 100%; color: white;" id="tableResult">
					<thead style="font-weight: bold; color: white; background-color: #000000;">
						<tr>
							<th style="color: white;border-right: 1px solid white">Location</th>
							<th style="color: white;border-right: 1px solid white">Patrol Detail / Topic</th>
							<th style="color: white;border-right: 1px solid white">Photo</th>
							<th style="color: white;border-right: 1px solid white">Problem</th>
							<th style="color: white;border-right: 1px solid white">PIC</th>
							<th style="color: white;border-right: 1px solid white;width: 1%">Act</th>
						</tr>
					</thead>
					<tbody id="body_cek">
						<tr class="member">
							<td width="10%">
								<select class="form-control select2 patrol_lokasi" id="patrol_lokasi" data-placeholder="Pilih Lokasi..." style="width: 100%; font-size: 20px;" required>
									<option></option>
									@foreach($location as $loc)
									<option value="{{ $loc }}">{{ $loc }}</option>
									@endforeach
								</select>
							</td>
							<td width="10%">
								<input type="text" class="form-control patrol_detail" id="patrol_detail" name="patrol_detail" placeholder="Patrol Detail" required="">
							</td>
							<td width="10%">
								<input type="file" onchange="readURL(this,'');" id="file" style="display:none" class="file">
								<button class="btn btn-primary btn-lg" id="btnImage" value="Photo" onclick="buttonImage(this)">Photo</button>

								<img width="150px" id="blah" src="" style="display: none" alt="your image" />
							</td>
							<td width="10%">
								<textarea id="patrol_note" height="50%" class="note"></textarea>
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
							<td><button class="btn btn-danger" onclick="delete_confirmation(this)"><i class="fa fa-close"></i></button></td>
						</tr>
					</tbody>
				</table>
				<br>
				<button class="btn btn-success" style="width: 100%" onclick="cek()"><i class="fa fa-check"></i>Check</button>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalFirst">
	<div class="modal-dialog modal-sm" style="width: 400px">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label>Pilih Kategori</label>
						<select class="form-control select2" id="selectCategory" data-placeholder="Pilih Kategori..." style="width: 100%; font-size: 20px;">
							<option value="5S Patrol GM">5S Patrol GM</option>
							<option value="5S Patrol Presdir">5S Patrol Presdir</option>
							<!-- <option value="MIS Patrol">MIS Patrol</option> -->
						</select>
					</div>

					<div class="form-group">
						<button class="btn btn-success pull-right" onclick="selectData()" style="width: 33%">Submit</button>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
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
				minimumResultsForSearch : -1,
				dropdownAutoWidth : true,
				allowClear:true
			});

			$('#modalFirst').modal({
				backdrop: 'static',
				keyboard: false
			});
		})

		function selectData(id){

			var kategori = $('#selectCategory').val();

			if(kategori == ""){
				$("#loading").hide();
				alert("Kolom Kategori Harap diisi");
				$("html").scrollTop(0);
				return false;
			}
			else if(kategori == "5S Patrol Presdir"){
				$('#employee_name').html("Hiroshi Ura");
				$('#employee_id').val("PI1301001");
			}
			else if(kategori == "5S Patrol GM"){
				$('#employee_name').html("Yukitaka Hayakawa");
				$('#employee_id').val("PI1206001");
			}
			else{
				$('#employee_name').html("{{$employee->name}}");
				$('#employee_id').val("{{$employee->employee_id}}");
			}

			$('#modalFirst').modal('hide');

			$('#category').html(kategori);

		}

		function get_check() {

			var category = $('#category').text();

			var data = {
				category:category
			}

			$("#loading").show();

			$.get('{{ url("fetch/audit_patrol") }}', data, function(result, status, xhr){
				$("#loading").hide();
				openSuccessGritter("Success","Data Has Been Load");
				var body = "";

				$('#tableResult').DataTable().clear();
				$('#tableResult').DataTable().destroy();
			// $('#body_cek').html("");

			count = 1;

			$.each(result.lists, function(index, value){
				body += "<tr>";
				body += "<td width='1%'>"+count+"</td>";
				body += "<td width='5%' id='klausul_"+count+"'>"+value.klausul+"<input type='hidden' id='id_point_"+count+"' value='"+value.id+"'><input type='hidden' id='jumlah_point_"+count+"' value='"+result.lists.length+"'></td>";
				body += "<td width='15%' id='point_judul_"+count+"'>"+value.point_judul+"</td>";
				body += "<td width='20%' id='point_question_"+count+"'>"+value.point_question+"</td>";
				body += "<td width='20%'><label class='radio' style='margin-top: 5px;margin-left: 5px'>Good<input onclick='goodchoice(this.id)' type='radio' id='status_"+count+"' name='status_"+count+"' value='Good'><span class='checkmark'></span></label><label class='radio' style='margin-top: 5px;margin-left: 5px'>Not Good<input type='radio' id='status_"+count+"' name='status_"+count+"' value='Not Good' onclick='notgoodchoice(this.id)'><span class='checkmark'></span></label></td>";
				body += "<td width='20%'><textarea id='note_"+count+"' height='50%' style='display:none'></textarea></td>";
				var idid = '#file_'+count;
				body += '<td width="15%"><input type="file" style="display:none" onchange="readURL(this,\''+count+'\');" id="file_'+count+'"><button class="btn btn-primary btn-lg" id="btnImage_'+count+'" value="Photo" style="display:none" onclick="buttonImage(this)">Photo</button><img width="150px" id="blah_'+count+'" src="" style="display: none" alt="your image" /></td>';
				body += "</tr>";
				count++;
			})

			$("#body_cek").append(body);

			var table = $('#tableResult').DataTable( {
				responsive: true,
				paging: false,
				searching: false,
				bInfo : false
			} );
		})
		}

		function tambah(){

			body = "";

			body += '<tr class="member">';
			body += '<td width="10%"><select class="form-control select3 patrol_lokasi" id="patrol_lokasi" data-placeholder="Pilih Lokasi..." style="width: 100%; font-size: 20px;" required><option></option>@foreach($location as $loc)<option value="{{ $loc }}">{{ $loc }}</option>@endforeach</select></td>'; 
			body += '<td width="10%"><input type="text" class="form-control patrol_detail" id="patrol_detail" name="patrol_detail" placeholder="Patrol Detail" required=""></td>';
			body += '<td width="10%"><input type="file" onchange="readURL(this,\'\');" id="file2" style="display:none" class="file"><button class="btn btn-primary btn-lg" id="btnImage" value="Photo" onclick="buttonImage(this)">Photo</button><img width="150px" id="blah" src="" style="display: none" alt="your image" /></td>';
			body += '<td width="10%"><textarea id="patrol_note" height="50%" class="note"></textarea></td>';
			body += '<td width="10%"><select class="form-control select3 patrol_pic" id="patrol_pic"  data-placeholder="Pilih PIC" style="width: 100%; font-size: 20px;"><option></option>@foreach($auditee as $audite)<option value="{{ $audite->name }}">{{ $audite->employee_id }} - {{ $audite->name }}</option>@endforeach</select></td>';
			body +=	'<td><button class="btn btn-danger" onclick="delete_confirmation(this)"><i class="fa fa-close"></i></button></td>';

			body += '</tr>';

			$("#body_cek").append(body);

			$(function () {
				$('.select3').select2({
					dropdownAutoWidth : true,
					allowClear:true
				});
			});

		}

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
			if (confirm('Apakah Anda yakin?')) {
				$('#loading').show();

				var audit_data = [];
				var patrol_lokasi = [];
				var patrol_detail = [];
				var note = [];
				var patrol_pic = [];
				var file = [];

				

				// $('.patrol_lokasi').each(function(i, obj) {
				// 	patrol_lokasi.push($(this).val());
				// })

				// $('.patrol_detail').each(function(i, obj) {
				// 	patrol_detail.push($(this).val());
				// })

				// $('.note').each(function(i, obj) {
				// 	note.push($(this).val());
				// })

				// $('.patrol_pic').each(function(i, obj) {
				// 	patrol_pic.push($(this).val());
				// })	

				// $('.file').each(function(i, obj) {
				// 	formData.append('file_datas_'+i, $(this).prop('files')[0]);
				// })	
				

				// $('.member').each(function(index, value) {
				// 	var ids = $(this).attr('id');


				// 	var auditor_id =  '{{$employee->employee_id}}';
				// 	var auditor_name =  '{{$employee->name}}';


				// 	audit_data.push({
				// 		'category': $('#category').text(),
				// 		'auditor_id': $('#employee_id').text(),
				// 		'auditor_name' : $('#employee_name').text(),
				// 		'lokasi': $('#patrol_lokasi').val(),
				// 		'detail': $('#patrol_detail').val(),
				// 		'note': $('#patrol_note').val(),
				// 		'pic': $('#patrol_pic').val()
				// 	});
				// });w

				// var data = {
				// 	patrol_lokasi : patrol_lokasi,
				// 	patrol_detail : patrol_detail,
				// 	note : note,
				// 	patrol_pic : patrol_pic,
				// 	category : $('#category').text(),
				// 	auditor_id : $('#employee_id').text(),
				// 	auditor_name  : $('#employee_name').text()
				// }

				// $.post('{{ url("post/audit_patrol") }}', data,  function(result, status, xhr){
				// 	openSuccessGritter("Success", "Audit Berhasil Dilaksanakan");
				// 	location.reload();
				// })

				var len = $('.member').length;



				var formData = new FormData();

				formData.append('jumlah', len);				
				formData.append('category', $('#category').text());
				formData.append('auditor_id', $('#employee_id').text());
				formData.append('auditor_name',  $('#employee_name').text());

				$('.file').each(function(i, obj) {
					formData.append('file_datas_'+i, $(this).prop('files')[0]);
					var file=$(this).val().replace(/C:\\fakepath\\/i, '').split(".");

					formData.append('extension_'+i, file[1]);
					formData.append('foto_name_'+i, file[0]);
				})	

				$('.patrol_lokasi').each(function(i, obj) {
					formData.append('patrol_lokasi_'+i, $(this).val());
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
					url:"{{ url('post/audit_patrol_file') }}",
					method:"POST",
					data:formData,
					dataType:'JSON',
					contentType: false,
					cache: false,
					processData: false,
					success: function (response) {
						openSuccessGritter("Success", "Audit Berhasil Diinput");
						location.reload();
					},
					error: function (response) {
						console.log(response.message);
					},
				})
			}
		}

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