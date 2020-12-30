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
	  vertical-align: middle;
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
		{{ $title }} <span class="text-purple">{{ $title_jp }}</span>
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
								<button class="btn btn-success " data-toggle="modal"  data-target="#upload_receive" style="margin-right: 5px">
									<i class="fa fa-upload"></i>&nbsp;&nbsp;Upload Receive
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="box no-border">
						<div class="box-body" style="padding-top: 0;">
							<table id="resumeTable" class="table table-bordered table-striped table-hover" style="margin-bottom: 20px;">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 14%; text-align: center; font-size: 1.5vw;">Total Uploaded</th>
										<th style="width: 14%; text-align: center; font-size: 1.5vw;">Total Data</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td id="count_all" style="text-align: center; font-size: 1.8vw; font-weight: bold;"></td>
										<td id="count_data" style="text-align: center; font-size: 1.8vw; font-weight: bold;"></td>
									</tr>
								</tbody>
							</table>
							<table id="ReceiveTable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:5%;">Nomor</th>
										<th style="width:6%;">Uploaded Date</th>
										<th style="width:5%;">Action</th>
									</tr>
								</thead>
								<tbody id="tableBodyHasil">
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal modal-default fade" id="upload_receive">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<form id="importForm" method="post" enctype="multipart/form-data" autocomplete="off">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Upload Confirmation</h4>
						Format: <i class="fa fa-arrow-down"></i> Seperti yang Tertera Pada Attachment Dibawah ini <i class="fa fa-arrow-down"></i><br>
						Sample: <a href="{{ url('uploads/receive/sample/receive_sample.xlsx') }}">receive_sample.xlsx</a>
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

	<div class="modal fade" id="myModal">
	    <div class="modal-dialog" style="width:1250px;">
	      <div class="modal-content">
	        <div class="modal-header">
	          <h4 style="float: right;" id="modal-title"></h4>
	          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
	          <br><h4 class="modal-title" id="judul_table"></h4>
	        </div>
	        <div class="modal-body">
	          <div class="row">
	            <div class="col-md-12">
	              <table id="tableResult" class="table table-striped table-bordered table-hover" style="width: 100%;"> 
	                <thead style="background-color: rgba(126,86,134,.7);">
	                  <tr>
	                    <th width="10%">Receive Date</th>
	                    <th width="10%">Vendor</th>
	                    <th width="10%">Surat Jalan</th>
	                    <th width="10%">Category</th>
	                    <th width="10%">Detail Item</th>
	                    <th width="10%">Amount ($)</th>
	                  </tr>
	                </thead>
	                <tbody id="tableBodyResult">
	                </tbody>
	                <tfoot style="background-color: RGB(252, 248, 227);">
	                <th></th>
	                <th></th>
	                <th></th>
	                <th></th>
	                <th>Total</th>
	                <th id="resultTotal"></th>
	              </tfoot>
	              </table>
	            </div>
	          </div>
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
	        </div>
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
		fillTableResult();
		$('body').toggleClass("sidebar-collapse");
	});

	function clearSearch(){
		location.reload(true);
	}

	function loadingPage(){
		$("#loading").show();
	}

	function fillTableResult() {
	    $.get('{{ url("invoice/fetch_receive") }}', function(result, status, xhr) {

	    if(result.status){
	      $('#ReceiveTable').DataTable().clear();
	      $('#ReceiveTable').DataTable().destroy();
	      $('#tableBodyHasil').html("");
	      var tableIsi = "";
	      var count = 1;
	      var count_all = 0;
	      
	      $.each(result.invoice, function(key, value) {
	        tableIsi += '<tr>';
	        tableIsi += '<td width="5%" style="padding:5px">'+ count +'</td>';
	        tableIsi += '<td width="30%" style="padding:5px">'+ value.tanggal_upload +'</td>';
	        tableIsi += '<td><a target="_blank" class="btn btn-primary btn-md" onclick="detail(\''+value.tanggal_upload+'\')"><i class="fa fa-eye"></i> Cek Data</a></td>';

	        tableIsi += '</tr>';
	        count += 1;
	        count_all += 1;
	      });

		  $('#count_all').text(count_all +" File(s)");
		  $('#count_data').text(result.jumlah[0].jumlah +" Data");
	      $('#tableBodyHasil').append(tableIsi);

	      var table2 = $('#ReceiveTable').DataTable({
	          'dom': 'Bfrtip',
	          'responsive':true,
	          'lengthMenu': [
	          [ 5, 10, 25, -1 ],
	          [ '5 rows', '10 rows', '25 rows', 'Show all' ]
	          ],
	          'buttons': {
	            buttons:[
	            {
	              extend: 'pageLength',
	              className: 'btn btn-default',
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
	            },
	            ]
	          },
	          'paging': true,
	          'lengthChange': true,
	          'pageLength': 5,
	          'searching': false,
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
	        alert('Attempt to retrieve data failed');
	      }


	  });

	}

	// function fetchTable(){
	// 	$('#ReceiveTable').DataTable().destroy();
		
	// 	var category = $('#category').val();
	// 	var data = {
	// 		category:category
	// 	}
		
	// 	$('#ReceiveTable tfoot th').each( function () {
	//       var title = $(this).text();
	//       $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
	//     } );

	// 	var table = $('#ReceiveTable').DataTable({
	// 		'dom': 'Bfrtip',
	// 		'responsive': true,
	// 		'lengthMenu': [
	// 		[ 10, 25, 50, -1 ],
	// 		[ '10 rows', '25 rows', '50 rows', 'Show all' ]
	// 		],
	// 		"pageLength": 25,
	// 		'buttons': {
	// 			// dom: {
	// 			// 	button: {
	// 			// 		tag:'button',
	// 			// 		className:''
	// 			// 	}
	// 			// },
	// 			buttons:[
	// 			{
	// 				extend: 'pageLength',
	// 				className: 'btn btn-default',
	// 				// text: '<i class="fa fa-print"></i> Show',
	// 			},
	// 			{
	// 				extend: 'copy',
	// 				className: 'btn btn-success',
	// 				text: '<i class="fa fa-copy"></i> Copy',
	// 				exportOptions: {
	// 					columns: ':not(.notexport)'
	// 				}
	// 			},
	// 			{
	// 				extend: 'excel',
	// 				className: 'btn btn-info',
	// 				text: '<i class="fa fa-file-excel-o"></i> Excel',
	// 				exportOptions: {
	// 					columns: ':not(.notexport)'
	// 				}
	// 			},
	// 			{
	// 				extend: 'print',
	// 				className: 'btn btn-warning',
	// 				text: '<i class="fa fa-print"></i> Print',
	// 				exportOptions: {
	// 					columns: ':not(.notexport)'
	// 				}
	// 			}
	// 			]
	// 		},
	// 		'paging': true,
	// 		'lengthChange': true,
	// 		'searching': true,
	// 		'ordering': true,
	// 		'order': [],
	// 		'info': true,
	// 		'autoWidth': true,
	// 		"sPaginationType": "full_numbers",
	// 		"bJQueryUI": true,
	// 		"bAutoWidth": false,
	// 		"processing": true,
	// 		"serverSide": true,
	// 		"ajax": {
	// 			"type" : "get",
	// 			"url" : "{{ url("invoice/fetch_receive") }}",
	// 			"data" : data
	// 		},
	// 		"columns": [
	// 			{ "data": "receive_date", "width":"8%"},
	// 			{ "data": "document_no", "width":"8%"},
	// 			{ "data": "vendor_code", "width":"20%"},
	// 			{ "data": "no_po_sap"},
	// 			{ "data": "category"},
	// 			{ "data": "item_description"},
	// 			{ "data": "gl_number"},
	// 			{ "data": "cost_center"},
	// 			{ "data": "action"}
	// 		]
	// 	});

	// 	table.columns().every( function () {
	//         var that = this;

	//         $( 'input', this.footer() ).on( 'keyup change', function () {
	//           if ( that.search() !== this.value ) {
	//             that
	//             .search( this.value )
	//             .draw();
	//           }
	//         } );
	//       } );
		
 //      	$('#ReceiveTable tfoot tr').appendTo('#ReceiveTable thead');
	// }

	$("form#importForm").submit(function(e) {
		if ($('#upload_file').val() == '') {
			openErrorGritter('Error!', 'You need to select file');
			return false;
		}

		$("#loading").show();

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("invoice/import_receive") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				if(result.status){
					$("#loading").hide();
					$("#upload_file").val('');
					$('#upload_receive').modal('hide');
					openSuccessGritter('Success', result.message);
					fillTableResult();
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

	function detail(tanggal){
	    $("#myModal").modal("show");

	    var data = {
	        tanggal:tanggal
	    }

	    $("#loading").show();
	    $.get('{{ url("invoice/fetch_receive_data") }}', data, function(result, status, xhr) {


	      $("#loading").hide();
	      if(result.status){
	        $('#tableResult').DataTable().clear();
	        $('#tableResult').DataTable().destroy();
	        $('#tableBodyResult').html("");

	        var tableData = "";
	        var total = 0;
	        var count = 1;
	        
	        $.each(result.datas, function(key, value) {
		        tableData += '<tr>';
	            tableData += '<td>'+ value.receive_date +'</td>';
	            tableData += '<td>'+ value.vendor_code +' - '+ value.vendor_name +'</td>';
	            tableData += '<td>'+ value.invoice_no +'</td>';
	            tableData += '<td>'+ value.category +'</td>';
	            tableData += '<td>'+ value.item_description+ '</td>';
	            tableData += '<td>'+ value.amount_dollar.toLocaleString() +'</td>'; 
	            total += parseFloat(value.amount_dollar);           

	          	tableData += '</tr>';
	         	count += 1;
	        });

	        $('#tableBodyResult').append(tableData);
	        $('#resultTotal').html('');
	        $('#resultTotal').append(total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));


	        $('#tableResult').DataTable({
		      'dom': 'Bfrtip',
		      'responsive': true,
		      'lengthMenu': [
		      [ 10, 25, 50, -1 ],
		      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
		      ],
		      'buttons': {
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
		        },
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
		    });
	      }
	      else{
	        alert('Attempt to retrieve data failed');
	      }

	    });

	    $('#judul_table').append().empty();
	    $('#judul_table').append('<center><b> List Data uploaded Tanggal '+tanggal+'</center></b>');
	    
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

