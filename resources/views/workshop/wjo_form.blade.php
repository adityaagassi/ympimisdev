@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
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
		border:1px solid rgb(211,211,211);
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		List of {{ $page }}s
		<small>it all starts here</small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<a href="javascript:void(0)" id="5" onclick="fetchTable(id)">Rejected ({{ $rejected }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" id="0" onclick="fetchTable(id)">Requested ({{ $requested }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" id="1" onclick="fetchTable(id)">Listed ({{ $listed }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" id="2" onclick="fetchTable(id)">Approved ({{ $approved }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" id="3" onclick="fetchTable(id)">InProgress ({{ $inprogress }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" id="4" onclick="fetchTable(id)">Finished ({{ $finished }})</a>
		</li>
		<li>
			<a href="javascript:void(0)" id="all" onclick="fetchTable(id)">All ({{ $rejected+$requested+$listed+$approved+$inprogress+$finished }})</a>
		</li>
		<li>
			<a data-toggle="modal" data-target="#createModal" class="btn btn-success btn-md" style="color:white"><i class="fa fa-plus"></i>Buat WJO Baru</a>
		</li>
	</ol>
</section>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<input type="hidden" value="{{ Auth::user()->username }}" id="username" />
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	
	<div class="col-md-12" style="padding-top: 10px;">
		<div class="row">
			<table id="traceabilityTable" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="width: 5%">Tanggal Pengajuan</th>
						<th style="width: 5%">WJO</th>
						<th style="width: 5%">Prioritas</th>
						<th style="width: 10%">Jenis Pekerjaan</th>
						<th style="width: 15%">Nama Barang</th>
						<th style="width: 5%">Jumlah</th>
						<th style="width: 9%">Material</th>
						<th style="width: 5%">Target</th>
						<th style="width: 5%">Status</th>
						<th style="width: 7%">PIC</th>
						<th style="width: 8%">Action</th>
					</tr>
				</thead>
				<tbody id="tableBody">
				</tbody>
				<tfoot>
				</tfoot>
			</table>
		</div>
	</div>

	<div class="modal fade" id="detailModal" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Workshop Job Orders Detail</b></h4>
					<h5 class="modal-title" style="text-align: center;" id="judul"></h5>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-7">
							<table id="data" class="table table-striped table-bordered" style="width: 100%;"> 
								<tbody id="data-log-body">
								</tbody>
							</table>
						</div>
						<div class="col-xs-5">
							
						</div>

					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #00a65a;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Pembuatan Form WJO</h1>
					</div>
					<form id="data" method="post" enctype="multipart/form-data" autocomplete="off">
						<div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Bagian:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-6">
								<select class="form-control select3" data-placeholder="Pilih Bagian" name="sub_section" id="sub_section" style="width: 100% height: 35px; font-size: 15px;" required>
									<option value=""></option>
									@php
									$group = array();
									@endphp
									@foreach($sections as $section)
									@if($section->group == null)
									<option value="{{ $section->department }}_{{ $section->section }}">{{ $section->department }} - {{ $section->section }}</option>
									@else
									<option value="{{ $section->section }}_{{ $section->group }}">{{ $section->section }} - {{ $section->group }}</option>
									@endif
									@endforeach
								</select>
							</div>
						</div>					

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Prioritas:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<select class="form-control select3" data-placeholder="Pilih Prioritas Pengerjaan" name="priority" id="priority" style="width: 100% height: 35px; font-size: 15px;" required>
									<option value=""></option>
									<option value="Normal">Normal</option>
									<option value="Urgent">Urgent</option>
								</select>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Jenis Pekerjaan:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<select class="form-control select3" data-placeholder="Pilih Jenis Pekerjaan" name="type" id="type" style="width: 100% height: 35px; font-size: 15px;" required>
									<option value=""></option>
									<option value="Pembuatan Baru">Pembuatan Baru</option>
									<option value="Perbaikan Ketidaksesuaian">Perbaikan Ketidaksesuaian</option>
									<option value="Lain-lain">Lain-lain</option>
								</select>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Kategori:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<select class="form-control select3" data-placeholder="Pilih Kategori" name="category" id="category" style="width: 100% height: 35px; font-size: 15px;" required>
									<option value=""></option>
									<option value="Molding">Molding</option>
									<option value="Jig">Jig</option>
									<option value="Equipment">Equipment</option>
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
								<select class="form-control select3" data-placeholder="Pilih Material Awal" name="material" id="material" style="width: 100% height: 35px; font-size: 15px;" required>
									<option value=""></option>
									@foreach($materials as $material)
									@if($material->remark == 'raw')
									<option value="{{ $material->item_description }}">{{ $material->item_description }}</option>
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

						<div class="col-xs-12" style="padding-bottom: 1%;" id="request">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Request Selesai:<span class="text-red">*</span></span>
							</div>
							<div class="col-xs-4">
								<div class="input-group date">
									<div class="input-group-addon bg-default">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker" name="request_date" id="request_date" placeholder="Pilih Tanggal">
								</div>
							</div>
						</div>

						<div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Lampiran:&nbsp;&nbsp;</span>
							</div>
							<div class="col-xs-8">
								<input style="height: 37px;" class="form-control" type="file" name="upload_file" id="upload_file">
							</div>
						</div>

						{{-- <div class="col-xs-12" style="padding-bottom: 1%;">
							<div class="col-xs-3" align="right" style="padding: 0px;">
								<span style="font-weight: bold; font-size: 16px;">Pilih Drawing:&nbsp;&nbsp;</span></span>
							</div>
							<div class="col-xs-5">
								<select class="form-control select2" data-placeholder="Pilih Drawing" name="drawing" id="drawing" style="width: 100% height: 35px; font-size: 15px;">
									<option value=""></option>
									@foreach($materials as $material)
									@if($material->remark == 'drawing')
									<option value="{{ $material->item_number }}">{{ $material->item_number }} - {{ $material->item_description }}</option>
									@endif
									@endforeach
								</select>
							</div>
						</div> --}}

						<div class="col-xs-12" style="padding-right: 12%;">
							<br>
							<span class="pull-left" style="font-weight: bold; background-color: yellow; color: rgb(255,0,0);">Tanda bintang (*) wajib diisi.</span>
							<button type="submit" class="btn btn-success pull-right">Submit</button>
						</div>

					</form>
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
		var opt = $("#sub_section option").sort(function (a,b) { return a.value.toUpperCase().localeCompare(b.value.toUpperCase()) });
		$("#sub_section").append(opt);
		$('#sub_section').prop('selectedIndex', 0).change();

		$('#request').hide();	

		$('#material-other').hide();

		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		fetchTable('all');
	});


	// $('#createModal').on('shown.bs.modal', function () {
	// 	$('.select2').select2();
	// });

	$(function () {
      $('.select2').select2()
    })

	 $(function () {
      $('.select3').select2({
        dropdownParent: $('#createModal')
      });
    })

	$('#material').on('change', function() {
		if(this.value == 'Lainnya'){
			$('#material-other').show();
		}else{
			$('#material-other').hide();
		}
	});

	$('#priority').on('change', function() {
		if(this.value == 'Urgent'){
			$('#request').show();
		}else if(this.value == 'Normal'){
			$('#request').hide();
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
		$("#loading").show();		

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("create/workshop/wjo") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();

				$('#sub_section').prop('selectedIndex', 0).change();
				$("#category").val("");
				$("#item_name").val("");
				$("#quantity").val("");
				$("#request_date").val("");
				$('#priority').prop('selectedIndex', 0).change();
				$('#type').prop('selectedIndex', 0).change();
				$("#material").prop('selectedIndex', 0).change();
				$("#material-other").val("");
				$("#problem_desc").val("");
				$("#upload_file").val("");
				$("#drawing").prop('selectedIndex', 0).change();

				$('#createModal').modal('hide');

				openSuccessGritter('Success', result.message);

				location.reload(true);		

			},
			error: function(result, status, xhr){
				$("#loading").hide();
				
				openErrorGritter('Error!', result.message);
			},
			cache: false,
			contentType: false,
			processData: false
		});
	});

	function fetchTable(id){
		var username = $('#username').val();
		var data = {
			remark: id,
			username: username,
			order: 'order_no desc'
		}
		$.get('{{ url("fetch/workshop/list_wjo") }}', data, function(result, status, xhr){
			if(result.status){
				$('#traceabilityTable').DataTable().clear();
				$('#traceabilityTable').DataTable().destroy();
				$('#tableBody').html("");

				var tableData = "";
				for (var i = 0; i < result.tableData.length; i++) {

					tableData += '<tr>';
					tableData += '<td>'+ result.tableData[i].created_at +'</td>';
					tableData += '<td>'+ result.tableData[i].order_no +'</td>';
					if(result.tableData[i].priority == 'Urgent'){
						var priority = '<span style="font-size: 13px;" class="label label-danger">Urgent</span>';
					}else{
						var priority = '<span style="font-size: 13px;" class="label label-default">Normal</span>';
					}
					tableData += '<td>'+ priority +'</td>';
					tableData += '<td>'+ result.tableData[i].type +'</td>';
					tableData += '<td>'+ result.tableData[i].item_name +'</td>';
					tableData += '<td>'+ result.tableData[i].quantity +'</td>';
					tableData += '<td>'+ result.tableData[i].material +'</td>';
					tableData += '<td>'+ (result.tableData[i].target_date || '-') +'</td>';
					tableData += '<td>'+ result.tableData[i].process_name +'</td>';	
					tableData += '<td>'+ (result.tableData[i].pic || '-') +'</td>';	
					if(result.tableData[i].remark == '0' || result.tableData[i].remark == '1'){
						tableData += '<td>';
						tableData += '<a style="padding: 10%; padding-top: 2%; padding-bottom: 2%; margin-right: 2%;" href="javascript:void(0)" onClick="modalEdit(\''+result.tableData[i].id+'\')" class="btn btn-warning">Edit</a>';
						tableData += '<a style="padding: 5%; padding-top: 2%; padding-bottom: 2%;" href="javascript:void(0)" onClick="showDetail(\''+result.tableData[i].id+'\')" class="btn btn-primary">Detail</a>';
						tableData += '</td>';
					}else{
						tableData += '<td><a style="padding: 5%; padding-top: 2%; padding-bottom: 2%;" href="javascript:void(0)" onClick="showDetail(\''+result.tableData[i].id+'\')" class="btn btn-primary">Detail</a></td>';							
					}
					tableData += '</tr>';	
				}

				$('#tableBody').append(tableData);
				$('#traceabilityTable').DataTable({
					'dom': 'Bfrtip',
					'responsive':true,
					'lengthMenu': [
					[ 10, 25, 50, -1 ],
					[ '10 rows', '25 rows', '50 rows', 'Show all' ]
					],
					'buttons': {
						buttons:[
						{
							extend: 'pageLength',
							className: 'btn btn-default',
						},
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true
				});
			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	function showDetail(id) {
		$('#detailModal').modal('show');
		
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