@extends('layouts.master')
@section('stylesheets')
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
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
		padding-top: 0;
		padding-bottom: 0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { display: none; }
	.disabledTab{
		pointer-events: none;
	}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}<span class="text-purple"> </span>
	</h1>
	<ol class="breadcrumb">
		<li>
			<!-- <a href="javascript:void(0)" onclick="openModalCreate()" class="btn btn-sm bg-purple" style="color:white">Create {{ $page }}</a> -->
		</li>
	</ol>
</section>
@stop
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-body">
					<center><h1><i class="fa fa-angle-double-down"></i>&nbsp;Daftar WJO yang Selesai&nbsp;<i class="fa fa-angle-double-down"></i></h1></center>
					<table id="masterTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">Tanggal Pengajuan</th>
								<th style="width: 1%;">Pemohon</th>
								<th style="width: 1%;">Bagian</th>
								<th style="width: 3%;">WJO</th>
								<th style="width: 4%;">Prioritas</th>
								<th style="width: 7%;">Jenis Pekerjaan</th>
								<th style="width: 12%;">Nama Barang</th>
								<th style="width: 1%;">Jumlah</th>
								<th style="width: 1%;">Target</th>
								<th style="width: 1%;">Att</th>
								<th style="width: 1%;">Action</th>
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

		<div class="col-xs-12">
			<div class="box">
				<div class="box-body">
					<center><h1><i class="fa fa-angle-double-down"></i>&nbsp;Daftar WJO yang Sudah Diambil&nbsp;<i class="fa fa-angle-double-down"></i></h1></center>
					<table id="pickedTable" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 1%;">Tanggal Pengajuan</th>
								<th style="width: 1%;">Pemohon</th>
								<th style="width: 1%;">Bagian</th>
								<th style="width: 3%;">WJO</th>
								<th style="width: 4%;">Prioritas</th>
								<th style="width: 7%;">Jenis Pekerjaan</th>
								<th style="width: 12%;">Nama Barang</th>
								<th style="width: 1%;">Jumlah</th>
								<th style="width: 1%;">Penerima</th>
								<th style="width: 1%;">Att</th>
								<th style="width: 1%;">Action</th>
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
</section>

<div class="modal fade in" id="modalComfirm">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h4 style="text-align: center; margin:5px; font-weight: bold; background-color: #3c8dbc; font-size: 36px">WJO RECEIPT</h4>
					<!-- <div class="col-xs-12" style="background-color: #3c8dbc;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Penugasan WJO</h1>
					</div> -->

					<div class="row">
						<div class="col-sm-12">
							<form class="form-horizontal">
								<div class="form-group">
									<label class="col-sm-3 control-label">WJO Number</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="wjo_num" readonly>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">Bagian</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="bagian" readonly>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">Nama Barang</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="nama_barang" readonly>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">Jumlah</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="qty" readonly>
									</div>
								</div>
							</form>
						</div>
						<div class="col-sm-12">
							<center><h2><i class="fa fa-angle-double-down"></i>&nbsp;Scan Tag&nbsp;<i class="fa fa-angle-double-down"></i></h2></center>
							<input type="text" name="scan_tag" id="scan_tag" class="form-control form-lg" placeholder="Scan Tag Penerima" style="text-align: center; font-size: 20px">
						</div>
					</div>					
				</div>
			</div>
		</div>
	</div>
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
			fillMasterTable();
			fillSecondTable();
		});

		function fillMasterTable(){
			$('#masterTable').DataTable().destroy();
			$('#masterTable tfoot th').each( function () {
				var title = $(this).text();
				$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
			});
			var table = $('#masterTable').DataTable({
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
				'paging'        : true,
				'lengthChange'  : true,
				'searching'     : true,
				'ordering'      : true,
				'info'        : true,
				'order'       : [],
				'autoWidth'   : true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
				"serverSide": true,
				"ajax": {
					"type" : "get",
					"url" : "{{ url("fetch/workshop/receipt") }}",
				},
				"columns": [
				{ "data": "tgl_pengajuan"},
				{ "data": "name"},
				{ "data": "bagian"},
				{ "data": "order_no"},
				{ "data": "priority"},
				{ "data": "type"},
				{ "data": "item_name"},
				{ "data": "quantity"},
				{ "data": "target_date"},
				{ "data": "att"},
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
			});

			$('#masterTable tfoot tr').appendTo('#masterTable thead');

			$('#masterTable tbody').on('click', 'tr', function () {
				var data = table.row( this ).data();
				openModal(data.order_no, data.bagian, data.item_name, data.quantity);
			} );
		}


		function fillSecondTable() {
			$('#pickedTable').DataTable().destroy();
			$('#pickedTable tfoot th').each( function () {
				var title = $(this).text();
				$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
			});
			var table = $('#pickedTable').DataTable({
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
				'paging'        : true,
				'lengthChange'  : true,
				'searching'     : true,
				'ordering'      : true,
				'info'        : true,
				'order'       : [],
				'autoWidth'   : true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
				"serverSide": true,
				"ajax": {
					"type" : "get",
					"url" : "{{ url("fetch/workshop/receipt/after") }}",
				},
				"columns": [
				{ "data": "tgl_pengajuan"},
				{ "data": "name"},
				{ "data": "bagian"},
				{ "data": "order_no"},
				{ "data": "priority"},
				{ "data": "type"},
				{ "data": "item_name"},
				{ "data": "quantity"},
				{ "data": "receiver"},
				{ "data": "att"},
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
			});

			$('#pickedTable tfoot tr').appendTo('#pickedTable thead');
		}

		function openModal(order_no, bagian, item, quantity) {
			$("#modalComfirm").modal("show");
			$("#wjo_num").val(order_no);
			$("#bagian").val(bagian);
			$("#nama_barang").val(item);
			$("#qty").val(quantity);
		}

		$('#scan_tag').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#scan_tag").val().length >= 9){
					var tag = $("#scan_tag").val();

					var data = {
						tag : tag,
						wjo: $("#wjo_num").val()
					}
					
					$.get('{{ url("scan/workshop/receipt") }}', data, function(result, status, xhr){
						if (result.status) {
							openSuccessGritter('Success!',result.message);
							$("#modalComfirm").modal("hide");
							fillMasterTable();
							fillSecondTable();
						} else {
							openErrorGritter('Error!',result.message);
						}
					})
				}
			}
		})

		function fillPickedTable() {
			$('#pickedTable').DataTable().destroy();
			$('#pickedTable tfoot th').each( function () {
				var title = $(this).text();
				$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
			});
			var table = $('#pickedTable').DataTable({
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
				'paging'        : true,
				'lengthChange'  : true,
				'searching'     : true,
				'ordering'      : true,
				'info'        : true,
				'order'       : [],
				'autoWidth'   : true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
				"serverSide": true,
				"ajax": {
					"type" : "get",
					"url" : "{{ url("fetch/workshop/picked") }}",
				},
				"columns": [
				{ "data": "tgl_pengajuan"},
				{ "data": "name"},
				{ "data": "bagian"},
				{ "data": "order_no"},
				{ "data": "priority"},
				{ "data": "type"},
				{ "data": "item_name"},
				{ "data": "quantity"},
				{ "data": "target_date"},
				{ "data": "att"},
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
			});

			$('#pickedTable tfoot tr').appendTo('#pickedTable thead');

		}


		$('#modalComfirm').on('shown.bs.modal', function () {
			$("#scan_tag").focus();
		});


		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

		function openSuccessGritter(title, message){
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-success',
				image: '{{ url("images/image-screen.png") }}',
				sticky: false,
				time: '4000'
			});
		}

		function openErrorGritter(title, message) {
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-danger',
				image: '{{ url("images/image-stop.png") }}',
				sticky: false,
				time: '4000'
			});
		}

	</script>
	@endsection