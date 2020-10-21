@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">

	#loading, #error { display: none; }

	table.table-bordered > thead > tr > th{
		color: white;
		text-align: center;
		vertical-align : middle;
		border: 1px solid black !important;
	}
	table.table-bordered > tbody > tr > td{
	  	color: black;
	  	border: 1px solid black !important;
	}

	#loading { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		Report Audit {{ $_GET['category'] }}
	</h1>
	<ol class="breadcrumb">
     
     @if(Auth::user()->role_code == "MIS" || Auth::user()->username == "PI1211001" || Auth::user()->username == "PI0904007")
     <a class="btn btn-primary btn-sm" style="margin-right: 5px" href="{{ url("/index/audit_iso/check") }}">
       <i class="fa fa-plus"></i>&nbsp;&nbsp;Point & Hasil Check Audit
     </a>
     @endif

     <button class="btn btn-success btn-sm" style="margin-right: 5px" onclick="location.reload()">
       <i class="fa fa-edit"></i>&nbsp;&nbsp;Reload Page
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

			<div class="col-xs-12" style="padding-right: 0; padding-left: 0;">

				<input type="hidden" name="kategori" id="kategori">
				<input type="hidden" name="lokasi" id="lokasi">
				<input type="hidden" name="auditor" id="auditor">
				<input type="hidden" name="date" id="date">
							
				<table class="table table-bordered" style="width: 100%; color: white;" id="tableResult">
					<thead style="font-weight: bold; color: white; background-color: #607d8b;">
						<!-- <tr>
							<th colspan="2">PT. Yamaha Musical Products Indonesia</th>
							<th rowspan="2">Tanggal<br> 21 Maret 2019</th>
							<th rowspan="2" colspan="2">Revisi  00</th>
							<th rowspan="2" colspan="2">ISO 45001:2018</th>
						</tr> -->
						<tr>
							<th colspan="7" style="border-right: 1px solid black;background: #7e5686">
							Laporan Hasil Internal Audit ISO
							</th>
						</tr>

						<tr>
							<th rowspan="2" style="background-color: #cddc39;color: black">Ref</th>
							<th rowspan="2" style="background-color: #cddc39;color: black">Requirement & Question</th>
							<th colspan="2" style="background-color: #cddc39;color: black">Status</th>
							<th rowspan="2" style="border-left: 1px solid black;background-color: #cddc39;color: black">Note</th>
							<th rowspan="2" style="background-color: #cddc39;color: black">Evidence</th>
							<th rowspan="2" style="background-color: #cddc39;color: black">Action</th>
						</tr>
						<tr>
							<th style="background-color: #cddc39;color: black">Good</th>
							<th style="border-right: 1px solid black;background-color: #cddc39;color: black">No Good</th>
						</tr>

					</thead>
					<tbody id="body_cek">
						
					</tbody>
				</table>
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
						<label>Kategori</label>
						<input type="text" class="form-control" id="selectCategory" name="selectCategory" readonly="" value="{{$_GET['category']}}">
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
						<label>Pilih Auditor</label>
						<select class="form-control select2" id="selectAuditor" data-placeholder="Pilih Auditor..." style="width: 100%; font-size: 20px;">
							<option></option>
							@foreach($auditor as $audit)
							<option value="{{ $audit->auditor_name }}">{{ $audit->auditor_name }}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label>Pilih Tanggal</label>
						<div class="input-group date">
		                  <div class="input-group-addon">
		                    <i class="fa fa-calendar"></i>
		                  </div>
		                  <input type="text" class="form-control pull-right" id="selectDate" name="selectDate" placeholder="Select Tanggal">
		                </div>
					</div>

					<div class="form-group">
						<button class="btn btn-success" onclick="selectData()">Submit</button>
					</div>
				</div>
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

		$('#selectDate').datepicker({
	        format: "yyyy-mm-dd",
	        autoclose: true,
	        todayHighlight: true
	      });
	})

	function selectData(id){

		var kategori = $('#selectCategory').val();
		var lokasi = $('#selectLocation').val();
		var auditor = $('#selectAuditor').val();
		var date = $('#selectDate').val();

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

		if(auditor == ""){
			$("#loading").hide();
            alert("Kolom Auditor Harap diisi");
            $("html").scrollTop(0);
			return false;
		}

		if(date == ""){
			$("#loading").hide();
            alert("Kolom Date Harap diisi");
            $("html").scrollTop(0);
			return false;
		}

		$('#modalFirst').modal('hide');

		$('#kategori').val(kategori);
		$('#lokasi').val(lokasi);
		$('#auditor').val(auditor);
		$('#date').val(date);

		cek_report();
	}

	function cek_report() {

		var location = $('#lokasi').val();
		var category = $('#kategori').val();
		var auditor = $('#auditor').val();
		var date = $('#date').val();


		var data = {
			location:location,
			category:category,
			auditor:auditor,
			date:date
		}

		$("#loading").show();

		$.get('{{ url("fetch/audit_iso/cek_report") }}', data, function(result, status, xhr){
			$("#loading").hide();

			$('#tableResult').DataTable().clear();
		    $('#tableResult').DataTable().destroy();

			var body = "";
		    $('#body_cek').html("");

		    count = 1;
			$.each(result.lists, function(index, value){
				// console.log(count);

				body += "<tr>";
				body += "<td width='5%'>"+value.klausul+"</td>";
				body += "<td width='30%'>"+value.point_judul+"<br>"+value.point_question+"</td>";

				if (value.status == "Good") {
					body += "<td style='color:green'>Good</td>";					
				}else{
					body += "<td>-</td>";
				}
				
				if(value.status == "Not Good"){
					body += "<td style='color:red'>Not Good</td>";
				}else{
					body += "<td>-</td>";
				}

				if(value.note != null){
					body += "<td width='20%'>"+value.note+"</td>";
				}
				else{
					body += "<td width='20%'>-</td>";
				}
				
				if (value.foto != null) {
					body += "<td width='20%'><img src={{url('files/audit_iso/')}}/"+value.foto+" width='200'></td>";					
				}
				else{
					body += "<td width='20%'>Tidak Ada Foto</td>";
				}

				if (value.status == "Not Good") {
					if (value.status_ditangani == null) {
						body += "<td width='10%'><a href={{url('index/audit_iso/create/')}}/"+value.id+" class='btn btn-success'>Buat Laporan</a></td>";
					}else{
						body += "<td width='10%'><span style='color:green'>Sudah Dibuat Laporan</span></td>";
					}
				}
				else{
					body += "<td width='10%'>-</td>";
				}

				body += "</tr>";
				count++;
			})

			// console.log(body);

			$("#body_cek").append(body);

			var table = $('#tableResult').DataTable( {
				responsive: true,
				paging: false,
				searching: false,
				bInfo : false,
				sorting: false
			} );
		})
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