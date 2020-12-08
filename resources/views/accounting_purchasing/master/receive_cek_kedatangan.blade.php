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
		Receive Report List <span class="text-purple">{{ $title_jp }}</span>
	</h1>
	<ol class="breadcrumb">
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
	@if (session('status'))
	  <div class="alert alert-success alert-dismissible">
	    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
	    {{ session('status') }}
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
						
						<div class="col-md-3">
							<div class="form-group">
								<label>Tanggal Terima</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="tanggal" name="tanggal" onchange="fetchTable()">
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="box no-border">
						<!-- <div class="box-header">
							<button class="btn btn-success" data-toggle="modal" data-target="#importModal" style="width: 
							16%">Import</button>
						</div> -->
						<div class="box-body">
							<table id="itemtable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:4%;">Nomor PR / Inv</th>
										<th style="width:4%;">Nomor PO</th>
										<th style="width:8%;">Nama Item</th>
										<th style="width:2%;">Qty Receive</th>
										<th style="width:2%;">Date Receive</th>
										<th style="width:3%;">Surat Jalan</th>
										<th style="width:3%;">Unit Price</th>
										<th style="width:3%;">Amount</th>
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
		fetchTable();
		$('body').toggleClass("sidebar-collapse");

		$('#tanggal').datepicker({
        	autoclose: true,
        	todayHighlight: true,
      		format: "yyyy-mm-dd"
        });
	});

	function clearSearch(){
		location.reload(true);
	}

	function loadingPage(){
		$("#loading").show();
	}

	function fetchTable(){
		$('#itemtable').DataTable().destroy();

		var tanggal = $('#tanggal').val();

		var data = {
			tanggal:tanggal
		}
		
		$('#itemtable tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input id="search" style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
		} );

		var table = $('#itemtable').DataTable({
			'dom': 'Bfrtip',
			'responsive': true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			"pageLength": 10,
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
				"url" : "{{ url("fetch/warehouse/cek_kedatangan") }}",
				"data" : data
			},
			"columns": [
			{ "data": "no_pr"},
			{ "data": "no_po"},
			{ "data": "nama_item"},
			{ "data": "qty_receive"},
			{ "data": "date_receive"},
			{ "data": "surat_jalan"},
			{ "data": "price","className" : "text-right"},
			{ "data": "amount_po","className" : "text-right"}
			]
		});

		table.columns().every( function () {
			var that = this;

			$('#search', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			} );
		} );
		
		$('#itemtable tfoot tr').appendTo('#itemtable thead');
	}
</script>
@endsection

	