@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.tagsinput.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		font-size: 16px;
	}
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#tableBodyResume > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		font-size: 13px;
		text-align: center;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:10px;
		font-size: 13px;
		text-align: center;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}

	table.table-bordered1{
		border:1px solid black;
	}
	table.table-bordered1 > thead > tr > th{
		border:1px solid black;
		font-size: 10px;
		text-align: center;
	}
	table.table-bordered1 > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:0;
		font-size: 10px;
		text-align: center;
		padding:10px;
	}
	table.table-bordered1 > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}

	.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}

	/*table.table-bordered > thead > tr > th{
	    border:1px solid rgb(54, 59, 56);
	    text-align: center;
	    background-color: rgba(126,86,134);  
	    color:white;
	    font-size: 13px;
  	}
  	table.table-bordered > tbody > tr > td{
	    border-collapse: collapse !important;
	    border:1px solid rgb(54, 59, 56);
	    /*background-color: #ffffff;*/
	    color: black;
	    vertical-align: middle;
	    text-align: center;
	    padding:10px;
	    font-size: 13px;
  	}*/

  	/*table.table-bordered1 > thead > tr > th{
	    border:1px solid rgb(54, 59, 56);
	    text-align: center;
	    background-color: rgba(126,86,134);  
	    color:white;
	    font-size: 10px;
  	}
  	table.table-bordered1 > tbody > tr > td{
	    border-collapse: collapse !important;
	    border:1px solid rgb(54, 59, 56);
	    /*background-color: #ffffff;*/
	    color: black;
	    vertical-align: middle;
	    text-align: center;
	    padding:10px;
	    font-size: 10px;
  	}*/

	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
	/*.select2-results { background-color: #00f; }
	.select2-search { background-color: #00f; }
	.select2-search input { background-color: #00f; }*/

	.nmpd-grid {border: none; padding: 20px;}
	.nmpd-grid>tbody>tr>td {border: none;}
	
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Upload Master Ideal <span class="text-purple">{{ $title_jp }}</span>
	</h1>
	<ol class="breadcrumb">
		<li>
			<!-- <a href="{{ url("index/budget/create")}}" class="btn btn-md bg-purple" style="color:white"><i class="fa fa-plus"></i> Create New budget</a> -->
		</li>
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if (session('success'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('success') }}
	</div>   
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row" style="margin-top: 5px">
		<div class="col-xs-12">
			<div class="box no-border" style="margin-bottom: 5px;">
				<div class="box-header">
					<h3 class="box-title">Detail Master Ideal<span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<form method="GET" action="{{ url("stock/ideal/download") }}">
							<div class="form-group">
						<div class="col-md-2">
							
								<label>Location</label>
								<input type="hidden" id="location">
								<select class="form-control select2" onchange="FetchData()" data-placeholder="Select Location" style="width: 100%; font-size: 20px;" id="select_loc" name="select_loc">
									<option></option>
									@foreach($storage_locations as $storage_location)
									<option value="{{ $storage_location->storage_location }}">{{ $storage_location->storage_location }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-10" style="padding-bottom: 20px">
							<div class="form-group">
								<div class="col-md-2" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<!-- <button class="btn btn-danger form-control" onclick="clearSearch()"><i class="fa fa-close"></i> Clear</button> -->
									<a class="btn btn-danger form-control" onclick="clearSearch()"><i class="fa fa-close"></i> Clear</a>
								</div>
								<div class="col-md-2" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button type="submit" class="btn btn-warning form-control"><i class="fa fa-download"></i> Download Stock Ideal</button>
								</div>
								<div class="col-md-4">
									<label style="color: white;"> x</label><br>
									<!-- <button class="btn btn-success " data-toggle="modal"  data-target="#upload_stock" style="margin-right: 5px">
										<i class="fa fa-upload"></i>&nbsp;&nbsp;Upload Stock Ideal
									</button> -->
									<a class="btn btn-success " data-toggle="modal"  data-target="#upload_stock" style="margin-right: 5px">
										<i class="fa fa-upload"></i>&nbsp;&nbsp;Upload Stock Ideal</a>
								</div>
							</div>
						</div>
							</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- <table class="table table-hover table-striped table-bordered" id="tableList" style="width: 100%;" >
						<thead style="background-color: rgb(126,86,134); color: #FFD700;">
							<tr>
								<th style="width: 1%;">No</th>
								<th style="width: 2%;">Material Number</th>
								<th style="width: 6%;">Material Description</th>
								<th style="width: 1%;">Category</th>
							</tr>					
						</thead>
						<tbody id="tableBodyList">
						</tbody>
					</table> -->
			<div class="row">
				<div class="col-xs-12">
					<div class="box no-border">
						<div class="box-body" style="padding-top: 0;">
							<table class="table table-hover table-striped table-bordered" id="tableList" style="width: 100%;" >
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th>No</th>
										<th>Location</th>
										<th>Store</th>
										<th>Material Number</th>
										<th>Material Description</th>
										<th>Category</th>
										<th>Stock Ideal</th>
										<!-- <th style="width:5%;">GL Number</th>
										<th style="width:5%;">Post Date</th>
										<th style="width:5%;">Local Amount ($)</th>	
										<th style="width:5%;">Original Amount</th>
										<th style="width:5%;">Investment No</th>
										<th style="width:5%;">Action</th> -->
									</tr>
								</thead>
								<tbody id="tableBodyList">
								</tbody>
								<!-- <tfoot>
					              <tr>
					                <th></th>
					                <th></th>
					                <th></th>
					                <th></th>
					                <th></th>
					                <th></th>		                
					              </tr>
					            </tfoot> -->
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal modal-default fade" id="upload_stock">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<form id="importForm" method="post" enctype="multipart/form-data" autocomplete="off">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Upload Confirmation</h4>
						Format: <i class="fa fa-arrow-down"></i> Seperti yang Tertera Pada Attachment Dibawah ini <i class="fa fa-arrow-down"></i><br>
						Sample: <a href="{{ url('uploads/receive/sample/transaksi_diluar_po.xlsx') }}">data_materials.xlsx</a>
					</div>
					<div class="modal-body">
						Upload Excel file here:<span class="text-red">*</span>
						<input type="file" name="upload_file" id="upload_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button id="modalImportButton" type="submit" class="btn btn-success">Upload</button>
					</div>
				</form>
			</div>
		</div>
	</div>

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	// var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('.select2').select2();
		// fetchTable();
		$('body').toggleClass("sidebar-collapse");
	});

	function clearSearch(){
		location.reload(true);
	}

	function loadingPage(){
		$("#loading").show();
	}

	$("form#importForm").submit(function(e) {
		if ($('#upload_file').val() == '') {
			openErrorGritter('Error!', 'You need to select file');
			return false;
		}

		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("stock/ideal/import") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				if(result.status){
					$("#loading").hide();
					// $('#TranskasiTable').DataTable().ajax.reload();
					$("#upload_file").val('');
					$('#upload_stock').modal('hide');
					openSuccessGritter('Success', result.message);

				}else{
					$("#loading").hide();

					openErrorGritter('Error!', result.message);
				}
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


	function FetchData(){
		$('#location').val($("#select_loc").val());
		var loc = $('#location').val();

		// fetchResume($('#location').val());

		var data = {
			loc:loc
		}
		$.get('<?php echo e(url("stock/aktual/list")); ?>', data, function(result, status, xhr){
			if(result.status){

				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				var tableData = '';
				$('#tableBodyList').html("");
				$('#tableBodyList').empty();
				
				var count = 1;
				$.each(result.lists, function(key, value) {
					// var str = value.description;
					// var desc = str.replace("'", "");
					tableData += '<tr>';
					tableData += '<td>'+ count +'</td>';
					tableData += '<td>'+ value.location +'</td>';
					tableData += '<td>'+ value.store +'</td>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ value.material_description +'</td>';
					if(value.category == 'SINGLE'){
						tableData += '<td style="background-color: rgb(250,250,210); text-align: center;">'+ value.category +'</td>';
					}
					else{
						tableData += '<td style="background-color: rgb(135,206,250); text-align: center;">'+ value.category +'</td>';
					}
					tableData += '<td>'+ value.ideal +'</td>';
					tableData += '</tr>';
					count += 1;
				});

				$('#tableBodyList').append(tableData);
				var tableList = $('#tableList').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 20,
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

				// openSuccessGritter('Success!', result.message);
				$('#modalLocation').modal('hide');
			}
			else{
				openErrorGritter('Error!', result.message);
			}
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

