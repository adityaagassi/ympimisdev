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
		Food Item List <span class="text-purple">{{ $title_jp }}</span>
	</h1>
	<ol class="breadcrumb">
		<?php if(Auth::user()->role_code == "MIS" || Auth::user()->role_code == "PCH" || Auth::user()->role_code == "PCH-SPL") { ?>
		<li>
			<a href="{{ url("canteen/purchase_item/create_category")}}" class="btn btn-md bg-blue" style="color:white">
				<i class="fa fa-plus"></i> Create New Food Item Category
			</a>
			<a href="{{ url("canteen/purchase_item/create")}}" class="btn btn-md bg-purple" style="color:white"><i class="fa fa-plus"></i> Create New Food Item</a>
		</li>

		<?php } ?>
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
								<label>Keyword</label>
								<input type="text" class="form-control" id="keyword2" placeholder="Masukkan Kata Kunci">
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label>Kategori</label>
								<select class="form-control select2" id="category" data-placeholder='Kategori Item' style="width: 100%">
					              <option value="">&nbsp;</option>
					              @foreach($item_category as $cat)
					              <option value="{{$cat->category_id}}">{{$cat->category_id}} - {{$cat->category_name}}</option>
					              @endforeach
					            </select>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label>Status</label>
								<select class="form-control select2" multiple="multiple" id="uom" data-placeholder="Select UOM" style="width: 100%;">
									<option></option>
									@foreach($uom as $um)
									<option value="{{ $um }}">{{ $um }}</option>
									@endforeach
								</select>
							</div>
						</div>
						
						<div class="col-md-3">
							<div class="form-group">
								<div class="col-md-6" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-primary form-control" onclick="fetchTable()">Search</button>
								</div>
								<div class="col-md-6" style="padding-right: 0;">
									<label style="color: white;"> x</label>
									<button class="btn btn-danger form-control" onclick="clearSearch()">Clear</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="box no-border">
						<div class="box-body">
							<table id="itemtable" class="table table-bordered table-striped table-hover">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width:5%;">Item Code</th>
										<th style="width:3%;">Category</th>
										<th style="width:15%;">Description</th>
										<th style="width:2%;">Uom</th>
										<th style="width:4%;">Price</th>
										<th style="width:3%;">Currency</th>
										<th style="width:3%;">Gambar</th>
										<th style="width:8%;">Action</th>
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

	});

	$('#keyword2').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			fetchTable();
		}
	});

	function clearSearch(){
		location.reload(true);
	}

	function loadingPage(){
		$("#loading").show();
	}

	function fetchTable(){
		$('#itemtable').DataTable().destroy();

		var keyword = $('#keyword2').val();
		var category = $('#category').val();
		var uom = $('#uom').val();

		var data = {
			keyword:keyword,
			category:category,
			uom:uom
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
				"url" : "{{ url("canteen/fetch/purchase_item") }}",
				"data" : data
			},
			"columns": [
			{ "data": "kode_item"},
			{ "data": "kategori"},
			{ "data": "deskripsi"},
			{ "data": "uom"},
			{ "data": "harga"},
			{ "data": "currency"},
			{ "data": "image"},
			{ "data": "action"},
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

