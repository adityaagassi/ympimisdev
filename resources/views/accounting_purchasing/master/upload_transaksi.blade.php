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
	  text-align:center;
	  overflow:hidden;
	  padding: 3px;
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
	  padding-top: 0;
	  padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
	  border:1px solid rgb(211,211,211);
	}
	td{
	    overflow:hidden;
	    text-overflow: ellipsis;
	  }
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		Upload Transaksi Non PO <span class="text-purple">{{ $title_jp }}</span>
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
					<h3 class="box-title">Detail Filters<span class="text-purple"> フィルター詳細</span></span></h3>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<div class="col-md-2">
							<div class="form-group">
								<label>Fiscal Year</label>
								<select class="form-control select2" multiple="multiple" id='periode' data-placeholder="Select Periode" style="width: 100%;">
									<option value="FY196">FY196</option>
									<option value="FY197">FY197</option>
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Month</label>
								<select class="form-control select2" id='month' data-placeholder="Select Month" style="width: 100%;">
									<option value=""></option>
									<option value="jan">jan</option>
									<option value="feb">feb</option>
									<option value="mar">mar</option>
									<option value="apr">apr</option>
									<option value="may">may</option>
									<option value="jun">jun</option>
									<option value="jul">jul</option>
									<option value="aug">aug</option>
									<option value="sep">sep</option>
									<option value="oct">oct</option>
									<option value="nov">nov</option>
									<option value="dec">dec</option>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<div class="col-md-4" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-primary form-control" onclick="fetchTable()"><i class="fa fa-search"></i> Search</button>
								</div>
								<div class="col-md-4" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-danger form-control" onclick="clearSearch()"><i class="fa fa-close"></i> Clear</button>
								</div>
								<div class="col-md-4">
									<label style="color: white;"> x</label><br>
									<button class="btn btn-success " data-toggle="modal"  data-target="#upload_transaksi" style="margin-right: 5px">
										<i class="fa fa-upload"></i>&nbsp;&nbsp;Upload Transaksi
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="box no-border">
						<div class="box-body" style="padding-top: 0;">
							<table id="TranskasiTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:5%;">Budget No</th>
										<th style="width:5%;">Document No</th>
										<th style="width:4%;">Type</th>
										<th style="width:6%;">Description</th>
										<th style="width:5%;">Reference</th>
										<th style="width:5%;">GL Number</th>
										<th style="width:5%;">Post Date</th>
										<th style="width:5%;">Local Amount ($)</th>	
										<th style="width:5%;">Original Amount</th>
										<th style="width:5%;">Investment No</th>
										<th style="width:5%;">Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
					              <tr>
					                <th></th>
					                <th></th>
					                <th></th>
					                <th></th>
					                <th></th>
					                <th></th>
					                <th></th>
					                <th></th>
					                <th></th>
					                <th></th>	
					                <th></th>				                
					              </tr>
					            </tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal modal-default fade" id="upload_transaksi">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<form id="importForm" method="post" enctype="multipart/form-data" autocomplete="off">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Upload Confirmation</h4>
						Format: <i class="fa fa-arrow-down"></i> Seperti yang Tertera Pada Attachment Dibawah ini <i class="fa fa-arrow-down"></i><br>
						Sample: <a href="{{ url('uploads/receive/sample/transaksi_diluar_po.xlsx') }}">transaksi_diluar_po.xlsx</a>
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
			url: '{{ url("import/transaksi") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				if(result.status){
					$("#loading").hide();
					$('#TranskasiTable').DataTable().ajax.reload();
					$("#upload_file").val('');
					$('#upload_transaksi').modal('hide');
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


	function fetchTable(){
		$('#TranskasiTable').DataTable().destroy();
		
		var periode = $('#periode').val();
		var month = $('#month').val();
		var data = {
			periode:periode,
			month:month
		}
		
		$('#TranskasiTable tfoot th').each( function () {
	      var title = $(this).text();
	      $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
	    } );

		var table = $('#TranskasiTable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 25,
			'buttons': {
				// dom: {
				// 	button: {
				// 		tag:'button',
				// 		className:''
				// 	}
				// },
				buttons:[
				{
					extend: 'pageLength',
					className: 'btn btn-default',
					// text: '<i class="fa fa-print"></i> Show',
				},
				{
					extend: 'copy',
					className: 'btn btn-success',
					text: '<i class="fa fa-copy"></i> Copy',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				{
					extend: 'excel',
					className: 'btn btn-info',
					text: '<i class="fa fa-file-excel-o"></i> Excel',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				{
					extend: 'print',
					className: 'btn btn-warning',
					text: '<i class="fa fa-print"></i> Print',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				}
				]
			},
			'paging': true,
			'lengthChange': true,
			'searching': true,
			'ordering': true,
			'order': [],
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/transaksi") }}",
				"data" : data
			},
			"columns": [
				{ "data": "budget_no"},
				{ "data": "document_no", "width":"7%"},
				{ "data": "type", "width":"7%"},
				{ "data": "description", "width":"15%"},
				{ "data": "reference"},
				{ "data": "gl_number"},
				{ "data": "post_date"},
				{ "data": "local_amount"},
				{ "data": "amount"},
				{ "data": "investment_no"},
				{ "data": "action"}
			]
		});

		table.columns().every( function () {
	        var that = this;

	        $( 'input', this.footer() ).on( 'keyup change', function () {
	          if ( that.search() !== this.value ) {
	            that
	            .search( this.value )
	            .draw();
	          }
	        } );
	      } );
		
      	$('#TranskasiTable tfoot tr').appendTo('#TranskasiTable thead');
	}

	function modalDelete(id) {
      var data = {
        id: id
      };

      if (!confirm("Apakah anda yakin ingin menghapus ini?")) {
        return false;
      }

      $.post('{{ url("delete/actual/transaksi") }}', data, function(result, status, xhr){
        $('#TranskasiTable').DataTable().ajax.reload(null, false);
        openSuccessGritter("Success","Berhasil Hapus Data Actual");
      })
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

