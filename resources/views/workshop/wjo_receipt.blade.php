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
			<div class="box box-primary box-solid">
				<div class="box-body">
					<form>
						<div class="col-md-5">
							<div class="form-group">
								<label>Pemohon</label>
								<div class="input-group" style="width: 100%;">
									<input type="text" placeholder="Tulisakan Nama Pemohon" class="form-control" name="s_pemohon" id="s_pemohon">
								</div>
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								<label>Bagian</label>
								<div class="input-group" style="width: 100%;">
									<select class="form-control select2" data-placeholder="Pilih Bagan" name="s_bagian" id="s_bagian" style="width: 100% height: 35px; font-size: 15px;" required>
										<option value=""></option>
										@foreach($bagian as $bg)
										<option value="{{$bg->section}}">{{$bg->section}}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<label>&nbsp;</label>
							<div class="input-group" style="width: 100%;">
								<a href="javascript:void(0)" onClick="fillMasterTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</a>
								<button onclick="clearSearch()" class="btn btn-danger" type="button"> Clear</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary box-solid">
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
						<tbody id="body_selesai">
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
	<hr style="margin-top: 0px; border: 3px solid black">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary box-solid">
				<div class="box-body">
					<form>
						<div class="col-md-2">
							<div class="form-group">
								<label>Tanggal Pengajuan</label>
								<div class="input-group" style="width: 100%;">
									<input type="text" placeholder="Pilih tanggal" class="form-control" name="s_tgl2" id="s_tgl2">
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Pemohon</label>
								<div class="input-group" style="width: 100%;">
									<input type="text" placeholder="Tulisakan Nama Pemohon" class="form-control" name="s_pemohon2" id="s_pemohon2">
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Bagian</label>
								<div class="input-group" style="width: 100%;">
									<select class="form-control select2" data-placeholder="Pilih Bagan" name="s_bagian2" id="s_bagian2" style="width: 100% height: 35px; font-size: 15px;" required>
										<option value=""></option>
										@foreach($bagian as $bg)
										<option value="{{$bg->section}}">{{$bg->section}}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<label>&nbsp;</label>
							<div class="input-group" style="width: 100%;">
								<a href="javascript:void(0)" onClick="fillSecondTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</a>
								<button onclick="clearSearch2()" class="btn btn-danger" type="button"> Clear</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary box-solid">
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
						<tbody id="body_diambil">
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

	<div class="modal fade" id="detailModal" style="color: black;">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div class="col-xs-12" style="background-color: #3c8dbc;">
						<h1 style="text-align: center; margin:5px; font-weight: bold;">Penugasan WJO</h1>
					</div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="col-xs-12">
								<div id="detail_body" style="padding: 2%;">									
									<div class="row">
										<div class="col-xs-6">
											<div class="form-group row" align="right">
												<label class="col-xs-4" style="margin-top: 1%;">Prioritas</label>
												<div class="col-xs-8" align="left">
													<input type="text" class="form-control" id="detail_priority" readonly>
												</div>
											</div>
											<div class="form-group row" align="right">
												<label class="col-xs-4" style="margin-top: 1%;">Order No.</label>
												<div class="col-xs-8" align="left">
													<input type="text" class="form-control" name="detail_order_no" id="detail_order_no" readonly>
												</div>
											</div>
											<div class="form-group row" align="right">
												<label class="col-xs-4" style="margin-top: 1%;">Dept.</label>
												<div class="col-xs-8" align="left">
													<input type="text" class="form-control" id="detail_department" readonly>
												</div>
											</div>
											<div class="form-group row" align="right">
												<label class="col-xs-4" style="margin-top: 1%;">Bagian</label>
												<div class="col-xs-8" align="left">
													<input type="text" class="form-control" id="detail_bagian" readonly>
												</div>
											</div>
										</div>
										<div class="col-xs-6">
											<div class="form-group row" align="right">
												<label class="col-xs-4" style="margin-top: 1%;">Tipe Pekerjaan</label>
												<div class="col-xs-8" align="left">
													<input type="text" class="form-control" name="detail_type" id="detail_type" readonly>
												</div>
											</div>

											<div class="form-group row" align="right">
												<label class="col-xs-4" style="margin-top: 1%;">Nama Barang</label>
												<div class="col-xs-8" align="left">
													<input type="text" class="form-control" name="detail_item_name" id="detail_item_name" readonly>
												</div>
											</div>

											<div class="form-group row" align="right">
												<label class="col-xs-4" style="margin-top: 1%;">Jumlah</label>
												<div class="col-xs-8" align="left">
													<input type="text" class="form-control" name="detail_quantity" id="detail_quantity" readonly>
												</div>
											</div>

											<div class="form-group row" align="right">
												<label class="col-xs-4" style="margin-top: 1%;">Material</label>
												<div class="col-xs-8" align="left">
													<input type="text" class="form-control" name="detail_material" id="detail_material" readonly>
												</div>
											</div>


										</div>
										<div class="col-xs-12">
											<div class="form-group row" align="right">
												<label class="col-xs-2" style="margin-top: 1%;">Uraian Permintaan</label>
												<div class="col-xs-10" align="left">
													<textarea class="form-control" name="detail_problem_desc" id="detail_problem_desc" rows="3" readonly></textarea>
												</div>
											</div>
										</div>
										<div class="col-xs-6" style="margin-top: 2%;">
											<div class="form-group row" align="right">
												<label class="col-xs-4" style="margin-top: 1%;">Target Selesai</label>
												<div class="col-xs-8">
													<div class="input-group date">
														<div class="input-group-addon bg-default">
															<i class="fa fa-calendar"></i>
														</div>
														<input type="text" class="form-control" name="detail_target_date" id="detail_target_date" readonly>
													</div>
												</div>
											</div>
											<div class="form-group row" align="right">
												<label class="col-xs-4" style="margin-top: 1%;">Kategori Part</label>
												<div class="col-xs-8" align="left">
													<input type="text" class="form-control" name="detail_category" id="detail_category" readonly>
												</div>
											</div>
										</div>
										<div class="col-xs-6" id="drawing2" style="margin-top: 2%;">
											<div class="form-group row" align="right">
												<label class="col-xs-4" style="margin-top: 1%;">Nama Drawing</label>
												<div class="col-xs-8" align="left">
													<input type="text" class="form-control" name="detail_drawing_name" id="detail_drawing_name" readonly>
												</div>
											</div>
											<div class="form-group row" align="right">
												<label class="col-xs-4" style="margin-top: 1%;">No. Drawing</label>
												<div class="col-xs-8" align="left">
													<input type="text" class="form-control" name="detail_drawing_number" id="detail_drawing_number" readonly>
												</div>
											</div>
											<div class="form-group row" align="right">
												<label class="col-xs-4" style="margin-top: 1%;">No. Part</label>
												<div class="col-xs-8" align="left">
													<input type="text" class="form-control" name="detail_part_number" id="detail_part_number" readonly>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
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

			$('.select2').select2();
		});

		function fillMasterTable(){
			var s_pemohon = $('#s_pemohon').val();
			var s_bagian = $('#s_bagian').val();
			
			var data = {
				pemohon:s_pemohon,
				bagian:s_bagian
			}
			$.get('{{ url("fetch/workshop/receipt") }}', data, function(result, status, xhr){
				if(result.status){
					$('#masterTable').DataTable().clear();
					$('#masterTable').DataTable().destroy();
					$('#body_selesai').html("");

					var body = "";

					$.each(result.data, function(index, value){
						body += "<tr onclick='openModal(\""+value.order_no+"\", \""+value.bagian+"\", \""+value.item_name+"\", \""+value.quantity+"\");'>";
						body += "<td>"+value.tgl_pengajuan+"</td>";
						body += "<td>"+value.name+"</td>";
						body += "<td>"+value.bagian+"</td>";
						body += "<td>"+value.order_no+"</td>";
						body += "<td>"+value.priority+"</td>";
						body += "<td>"+value.type+"</td>";
						body += "<td>"+value.item_name+"</td>";
						body += "<td>"+value.quantity+"</td>";
						body += "<td>"+value.target_date+"</td>";

						if(value.attachment != null){
							body += '<td><a href="javascript:void(0)" onClick="downloadAtt(\''+value.attachment+'\')" class="fa fa-paperclip"></a></td>';
						}else{
							body += '<td>-</td>';							
						}

						body += "<td><a href='javascript:void(0)' class='btn btn-xs btn-info' onClick='detailReport(\""+value.order_no+"\")'>Details</a></td>";

						body += "</tr>";
					})

					$("#body_selesai").append(body);

					$('#masterTable tfoot th').each(function(){
						var title = $(this).text();
						$(this).html( '<input style="text-align: center;" class="input1" type="text" placeholder="Search '+title+'" size="8"/>' );
					});


					var table = $('#masterTable').DataTable({
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
						'searching': true,
						'ordering': true,
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true,
					});


					table.columns().every( function () {
						var that = this;

						$( '.input1', this.footer() ).on( 'keyup change', function () {
							if ( that.search() !== this.value ) {
								that
								.search( this.value )
								.draw();
							}
						} );
					} );

					$('#masterTable tfoot tr').appendTo('#masterTable thead');
				}
			})

		}


		function fillSecondTable() {
			s_tgl2
			s_pemohon2
			s_bagian2

			var s_tgl = $('#s_tgl2').val();
			var s_pemohon = $('#s_pemohon2').val();
			var s_bagian = $('#s_bagian2').val();
			
			var data = {
				tgl:s_tgl,
				pemohon:s_pemohon,
				bagian:s_bagian
			}

			$.get('{{ url("fetch/workshop/receipt/after") }}', data, function(result, status, xhr){
				if(result.status){
					$('#pickedTable').DataTable().clear();
					$('#pickedTable').DataTable().destroy();
					$('#body_diambil').html("");

					var body = "";

					$.each(result.data, function(index, value){
						body += "<tr>";
						body += "<td>"+value.tgl_pengajuan+"</td>";
						body += "<td>"+value.name+"</td>";
						body += "<td>"+value.bagian+"</td>";
						body += "<td>"+value.order_no+"</td>";
						body += "<td>"+value.priority+"</td>";
						body += "<td>"+value.type+"</td>";
						body += "<td>"+value.item_name+"</td>";
						body += "<td>"+value.quantity+"</td>";
						body += "<td>"+value.receiver+"</td>";

						if(value.attachment != null){
							body += '<td><a href="javascript:void(0)" onClick="downloadAtt(\''+value.attachment+'\')" class="fa fa-paperclip"></a></td>';
						}else{
							body += '<td>-</td>';							
						}

						body += "<td><a href='javascript:void(0)' class='btn btn-xs btn-info' onClick='detailReport(\""+value.order_no+"\")'>Details</a></td>";

						body += "</tr>";
					})

					$("#body_diambil").append(body);

					$('#pickedTable tfoot th').each(function(){
						var title = $(this).text();
						$(this).html( '<input style="text-align: center;" class="input2" type="text" placeholder="Search '+title+'" size="8"/>' );
					});


					var table2 = $('#pickedTable').DataTable({
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
						'searching': true,
						'ordering': true,
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true,
					});


					table2.columns().every( function () {
						var that = this;

						$( '.input2', this.footer() ).on( 'keyup change', function () {
							if ( that.search() !== this.value ) {
								that
								.search( this.value )
								.draw();
							}
						} );
					} );

					$('#pickedTable tfoot tr').appendTo('#pickedTable thead');
				}
			})



			// $('#pickedTable').DataTable().destroy();
			// $('#pickedTable tfoot th').each( function () {
			// 	var title = $(this).text();
			// 	$(this).html( '<input style="text-align: center;" class="input" type="text" placeholder="Search '+title+'" />' );
			// });
			// var table = $('#pickedTable').DataTable({
			// 	'dom': 'Bfrtip',
			// 	'responsive': true,
			// 	'lengthMenu': [
			// 	[ 10, 25, 50, -1 ],
			// 	[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			// 	],
			// 	'buttons': {
			// 		buttons:[
			// 		{
			// 			extend: 'pageLength',
			// 			className: 'btn btn-default',
			// 		},
			// 		{
			// 			extend: 'copy',
			// 			className: 'btn btn-success',
			// 			text: '<i class="fa fa-copy"></i> Copy',
			// 			exportOptions: {
			// 				columns: ':not(.notexport)'
			// 			}
			// 		},
			// 		{
			// 			extend: 'excel',
			// 			className: 'btn btn-info',
			// 			text: '<i class="fa fa-file-excel-o"></i> Excel',
			// 			exportOptions: {
			// 				columns: ':not(.notexport)'
			// 			}
			// 		},
			// 		{
			// 			extend: 'print',
			// 			className: 'btn btn-warning',
			// 			text: '<i class="fa fa-print"></i> Print',
			// 			exportOptions: {
			// 				columns: ':not(.notexport)'
			// 			}
			// 		},
			// 		]
			// 	},
			// 	'paging'        : true,
			// 	'lengthChange'  : true,
			// 	'searching'     : true,
			// 	'ordering'      : true,
			// 	'info'        : true,
			// 	'order'       : [],
			// 	'autoWidth'   : true,
			// 	"sPaginationType": "full_numbers",
			// 	"bJQueryUI": true,
			// 	"bAutoWidth": false,
			// 	"processing": true,
			// 	"serverSide": true,
			// 	"ajax": {
			// 		"type" : "get",
			// 		"url" : "{{ url("fetch/workshop/receipt/after") }}",
			// 	},
			// 	"columns": [
			// 	{ "data": "tgl_pengajuan"},
			// 	{ "data": "name"},
			// 	{ "data": "bagian"},
			// 	{ "data": "order_no"},
			// 	{ "data": "priority"},
			// 	{ "data": "type"},
			// 	{ "data": "item_name"},
			// 	{ "data": "quantity"},
			// 	{ "data": "receiver"},
			// 	{ "data": "att"},
			// 	{ "data": "action"}
			// 	]
			// });

			// table.columns().every( function () {
			// 	var that = this;

			// 	$( '.input', this.footer() ).on( 'keyup change', function () {
			// 		if ( that.search() !== this.value ) {
			// 			that
			// 			.search( this.value )
			// 			.draw();
			// 		}
			// 	} );
			// });

			// $('#pickedTable tfoot tr').appendTo('#pickedTable thead');
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
							$("#wjo_num").val("");
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

		function detailReport(order_no) {
			$('#detail_target_date').val("");
			$('#detail_order_no').val("");
			$('#detail_bagian').val("");
			$('#detail_department').val("");
			$('#detail_priority').val("");
			$('#detail_type').val("");
			$('#detail_item_name').val("");
			$('#detail_quantity').val("");
			$('#detail_material').val("");
			$('#detail_problem_desc').val("");
			$('#detail_category').val("");

			$('#detailModal').modal('show');

			var data = {
				order_no : order_no
			}

			$.get('{{ url("fetch/workshop/process_detail") }}', data, function(result, status, xhr){
				var group = result.detail.sub_section.split("_");

				if (result.detail.category == 'Molding' || result.detail.category == 'Jig') {
					$("#drawing2").show();
				} else {
					$("#drawing2").hide();
				}

				if (result.detail.reject_reason != null) {
					$("#reject").show();
					$('#detail_reject_reason').val(result.detail.reject_reason);
				} else {
					$("#reject").hide();
					$('#detail_reject_reason').val("");
				}

				$('#detail_target_date').val(result.detail.target_date);
				$('#detail_order_no').val(result.detail.order_no);
				$('#detail_bagian').val(group[1]);
				$('#detail_department').val(group[0]);
				$('#detail_priority').val(result.detail.priority);
				$('#detail_type').val(result.detail.type);
				$('#detail_item_name').val(result.detail.item_name);
				$('#detail_quantity').val(result.detail.quantity);
				$('#detail_material').val(result.detail.material);
				$('#detail_problem_desc').val(result.detail.problem_description);
				$('#detail_category').val(result.detail.category);

				$('#detail_drawing_name').val(result.detail.drawing_name);
				$('#detail_drawing_number').val(result.detail.item_number);
				$('#detail_part_number').val(result.detail.part_number);
			})
		}

		// function fillPickedTable() {
		// 	$('#pickedTable').DataTable().destroy();
		// 	$('#pickedTable tfoot th').each( function () {
		// 		var title = $(this).text();
		// 		$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
		// 	});
		// 	var table = $('#pickedTable').DataTable({
		// 		'dom': 'Bfrtip',
		// 		'responsive': true,
		// 		'lengthMenu': [
		// 		[ 10, 25, 50, -1 ],
		// 		[ '10 rows', '25 rows', '50 rows', 'Show all' ]
		// 		],
		// 		'buttons': {
		// 			buttons:[
		// 			{
		// 				extend: 'pageLength',
		// 				className: 'btn btn-default',
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
		// 			},
		// 			]
		// 		},
		// 		'paging'        : true,
		// 		'lengthChange'  : true,
		// 		'searching'     : true,
		// 		'ordering'      : true,
		// 		'info'        : true,
		// 		'order'       : [],
		// 		'autoWidth'   : true,
		// 		"sPaginationType": "full_numbers",
		// 		"bJQueryUI": true,
		// 		"bAutoWidth": false,
		// 		"processing": true,
		// 		"serverSide": true,
		// 		"ajax": {
		// 			"type" : "get",
		// 			"url" : "{{ url("fetch/workshop/picked") }}",
		// 		},
		// 		"columns": [
		// 		{ "data": "tgl_pengajuan"},
		// 		{ "data": "name"},
		// 		{ "data": "bagian"},
		// 		{ "data": "order_no"},
		// 		{ "data": "priority"},
		// 		{ "data": "type"},
		// 		{ "data": "item_name"},
		// 		{ "data": "quantity"},
		// 		{ "data": "target_date"},
		// 		{ "data": "att"},
		// 		{ "data": "action"}
		// 		]
		// 	});

		// 	table.columns().every( function () {
		// 		var that = this;

		// 		$( 'input', this.footer() ).on( 'keyup change', function () {
		// 			if ( that.search() !== this.value ) {
		// 				that
		// 				.search( this.value )
		// 				.draw();
		// 			}
		// 		} );
		// 	});

		// 	$('#pickedTable tfoot tr').appendTo('#pickedTable thead');

		// }

		$('#modalComfirm').on('shown.bs.modal', function () {
			$("#scan_tag").focus();
		});

		function clearSearch() {
			$("#s_bagian").val('').trigger('change') ;
			$("#s_pemohon").val("");
		}

		function clearSearch2() {
			$("#s_bagian2").val('').trigger('change') ;
			$("#s_pemohon2").val("");
		}

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