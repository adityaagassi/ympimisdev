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
		Audit Internal ISO
	</h1>
	<ol class="breadcrumb">
     <?php $user = STRTOUPPER(Auth::user()->username) ?>

     @if(Auth::user()->role_code == "MIS" || $user == "PI1211001" || $user == "PI0904007")
     <a class="btn btn-primary btn-sm" style="margin-right: 5px" href="{{ url("/index/audit_iso/check") }}">
       <i class="fa fa-plus"></i>&nbsp;&nbsp;Point Check & Hasil Audit
     </a>
     @endif

     <button class="btn btn-success btn-sm" style="margin-right: 5px" onclick="location.reload()">
       <i class="fa fa-edit"></i>&nbsp;&nbsp;Ganti Lokasi
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
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 0px">
					<thead>
						<tr>
							<th style="width:15%; background-color: #673ab7; text-align: center; color: white; padding:0;font-size: 18px;border: 0" colspan="3">General Information</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="padding: 0px; background-color: #5c6bc0; text-align: center; color: white; font-size:20px; width: 30%;border: 1px solid black">Audit Date</td>
							<td colspan="2" style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;border: 1px solid black"><?= date("d-m-Y") ?></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: #5c6bc0; text-align: center; color: white; font-size:20px; width: 30%;border: 1px solid black">Auditor</td>
							<td style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;border: 1px solid black" id="employee_id">{{ $employee->employee_id }} - {{ $employee->name }}</td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: #5c6bc0; text-align: center; color: white; font-size:20px; width: 30%;border: 1px solid black">Category</td>
							<td colspan="2" style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;border: 1px solid black" id="category"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: #5c6bc0; text-align: center; color: white; font-size:20px; width: 30%;border: 1px solid black">Location</td>
							<td colspan="2" style="padding: 0px; background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 20px;border: 1px solid black" id="location"></td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="col-xs-12" style="padding-right: 0; padding-left: 0;">
				<table class="table table-bordered" style="width: 100%; color: white;" id="tableResult">
					<thead style="font-weight: bold; color: black; background-color: #cddc39;">
						<tr>
							<th>No.</th>
							<th>Klausul</th>
							<th>Subject</th>
							<th>Question</th>
							<th>OK</th>
							<th>Note</th>
							<th>Evidence</th>
						</tr>
					</thead>
					<tbody id="body_cek">
						
					</tbody>
				</table>
				<br>
				<button class="btn btn-success" style="width: 100%" onclick="cek()"><i class="fa fa-check"></i>Check</button>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="modalFirst">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label>Pilih Kategori ISO</label>
						<select class="form-control select2" id="selectCategory" data-placeholder="Pilih Kategori..." style="width: 100%; font-size: 20px;">
							<option></option>
							@foreach($category as $cat)
							<option value="{{ $cat->kategori }}">{{ $cat->kategori }}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label>Pilih Lokasi</label>
						<select class="form-control select2" id="selectLocation" data-placeholder="Pilih Lokasi Anda..." style="width: 100%; font-size: 20px;">
							<option></option>
							@foreach($location as $loc)
							<option value="{{ $loc->lokasi }}">{{ $loc->lokasi }}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<button class="btn btn-success" onclick="selectData()">Submit</button>
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
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				<a id="modalDeleteButton" href="#" type="button" class="btn btn-success">Buat Laporan Audit ISO</a>
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

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').prop('selectedIndex', 0).change();
		$('.select2').select2({
			minimumResultsForSearch : -1
		});

		$('#modalFirst').modal({
			backdrop: 'static',
			keyboard: false
		});
	})

	function selectData(id){

		var kategori = $('#selectCategory').val();
		var lokasi = $('#selectLocation').val()

		if(kategori == ""){
			$("#loading").hide();
            alert("Kolom Kategori Harap diisi");
            $("html").scrollTop(0);
			return false;
		}

		if(lokasi == ""){
			$("#loading").hide();
            alert("Kolom Lokasi Harap diisi");
            $("html").scrollTop(0);
			return false;
		}

		$('#modalFirst').modal('hide');

		$('#category').html(kategori);
		$('#location').html(lokasi);

		get_check();
	}

	function get_check() {

		var location = $('#location').text();
		var category = $('#category').text();

		var data = {
			location:location,
			category:category
		}

		$("#loading").show();

		$.get('{{ url("fetch/audit_iso/create_audit") }}', data, function(result, status, xhr){
			$("#loading").hide();
			openSuccessGritter("Success","Data Has Been Load");
			var body = "";
			$('#tableResult').DataTable().clear();
		    $('#tableResult').DataTable().destroy();
		    $('#body_cek').html("");

		    count = 1;

			$.each(result.lists, function(index, value){
				body += "<tr>";
				body += "<td width='1%'>"+count+"</td>";
				body += "<td width='5%' id='klausul_"+count+"'>"+value.klausul+"<input type='hidden' id='id_point_"+count+"' value='"+value.id+"'><input type='hidden' id='jumlah_point_"+count+"' value='"+result.lists.length+"'></td>";
				body += "<td width='10%' id='point_judul_"+count+"'>"+value.point_judul+"</td>";
				body += "<td width='20%' id='point_question_"+count+"'>"+value.point_question+"</td>";
				body += "<td><label class='radio' style='margin-top: 5px;margin-left: 5px'>Good<input type='radio' id='status_"+count+"' name='status_"+count+"' value='Good'><span class='checkmark'></span></label><label class='radio' style='margin-top: 5px'>Not Good<input type='radio' id='status_"+count+"' name='status_"+count+"' value='Not Good'><span class='checkmark'></span></label></td>";
				body += "<td width='20%'><textarea id='note_"+count+"' height='50%'></textarea></td>";
				var idid = '#file_'+count;
				body += '<td width="20%"><input type="file" style="display:none" onchange="readURL(this,\''+count+'\');" id="file_'+count+'"><button class="btn btn-primary btn-lg" id="btnImage_'+count+'" value="Photo" onclick="buttonImage(\''+idid+'\')">Photo</button><img width="150px" id="blah_'+count+'" src="" style="display: none" alt="your image" /></td>';
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

	function buttonImage(idfile) {
		$(idfile).click();
	}

	function readURL(input,idfile) {
	      if (input.files && input.files[0]) {
	          var reader = new FileReader();

	          reader.onload = function (e) {
	            $('#blah_'+idfile).show();
	              $('#blah_'+idfile)
	                  .attr('src', e.target.result);
	          };

	          reader.readAsDataURL(input.files[0]);
	      }
	      $('#btnImage_'+idfile).hide();
	}

	function cek() {
		if (confirm('Apakah Anda yakin?')) {
			$('#loading').show();
			var audit = [];
			var countpoint = parseInt($('#jumlah_point_1').val());

			var count = 0;

			for(var i = 0; i < countpoint; i++){
				var a = i+1;

				var tanggal = '{{date("Y-m-d")}}';
				var kategori =  $('#category').text();
				var lokasi =  $('#location').text();
				var auditor_id =  '{{$employee->employee_id}}';
				var auditor_name =  '{{$employee->name}}';
				var klausul =  $('#klausul_'+a).text();
				var point_judul =  $('#point_judul_'+a).text();
				var point_question =  $('#point_question_'+a).text();
				var note =  $('#note_'+a).val();
				var idstatus = 'input[id="status_'+a+'"]:checked';
				var status = $(idstatus).val();
				var id_point = $('#id_point_'+a).val();
				var fileData  = $('#file_'+a).prop('files')[0];

				var file=$('#file_'+a).val().replace(/C:\\fakepath\\/i, '').split(".");

				var formData = new FormData();
				formData.append('fileData', fileData);
				formData.append('tanggal', tanggal);
				formData.append('kategori', kategori);
				formData.append('lokasi', lokasi);
				formData.append('auditor_id', auditor_id);
				formData.append('auditor_name', auditor_name);
				formData.append('klausul', klausul);
				formData.append('point_judul', point_judul);
				formData.append('point_question', point_question);
				formData.append('status', status);
				formData.append('note', note);
				formData.append('id_point', id_point);
				formData.append('extension', file[1]);
				formData.append('foto_name', file[0]);


				$.ajax({
				   url:"{{ url('input/audit_iso/create_audit') }}",
				   method:"POST",
				   data:formData,
				   dataType:'JSON',
				   contentType: false,
				   cache: false,
				   processData: false,
				   success:function(data)
				   {
					// openSuccessGritter('Success','Input Data Audit Berhasil');
				   }
				})

				count++;
			}
			
			if (count == countpoint) {
				$('#loading').hide();
				$('#myModal').modal('show');
				$('.modal-body').html("Terima Kasih telah mengisi Audit ISO.<br>Jika Anda akan membuat Laporan Audit ISO, silahkan klik tombol di bawah ini.");
				$('#modalDeleteButton').attr("href", '{{ url("/index/audit_iso/create") }}');
			}

		}
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