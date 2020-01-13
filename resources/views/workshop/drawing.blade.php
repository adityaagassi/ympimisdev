@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	
	input {
		line-height: 22px;
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
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}
	#loading, #error { 
		display: none;
	}
	#tableBodyList > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}
	.urgent{
		background-color: red;
	}

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
		<button href="javascript:void(0)" class="btn btn-success btn-md pull-right" data-toggle="modal" data-target="#modal-add" style="margin-right: 1%;">
			<i class="glyphicon glyphicon-plus"></i>&nbsp;&nbsp;Add Drawing
		</button>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<table id="tableList" class="table table-bordered table-striped table-hover">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 2%;">#</th>
								<th style="width: 15%;">Item Number</th>
								<th style="width: 25%;">Description</th>
								<th style="width: 25%;">Created By</th>
								<th style="width: 20%;">Created At</th>
								<th style="width: 5%;">Attach.</th>
								<th style="width: 5%;">Edit</th>
							</tr>
						</thead>
						<tbody id="tableBodyList">
						</tbody>
						<tfoot>
							<tr style="color: black">
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


{{-- Modal Add --}}
<div class="modal modal-default fade" id="modal-add">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #00a65a;">
					<h1 style="text-align: center; margin:5px; font-weight: bold;">Add Drawing</h1>
				</div>
			</div>
			<form id="add" method="post" enctype="multipart/form-data" autocomplete="off">
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body" style="padding-left: 0%;">
								<div class="form-group row" align="right">
									<label class="col-xs-4">No. Drawing<span class="text-red">*</span></label>
									<div class="col-xs-6" align="left">
										<input type="text" class="form-control" name="item_number" id="item_number"  placeholder="Nomor Drawing" style="width: 100%; font-size: 15px;" required>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4">Deskripsi<span class="text-red">*</span></label>
									<div class="col-xs-6" align="left">
										<textarea class="form-control" rows='3' name="item_description" id="item_description" placeholder="Deskripsi Drawing" style="width: 100%; font-size: 15px;" required></textarea>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4">Lampiran<span class="text-red">*</span></label>
									<div class="col-xs-6" align="left">
										<input style="height: 37px;" class="form-control" type="file" name="upload_file" id="upload_file" accept="text/plain" required>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="padding-right: 20%;">
					<button type="submit" class="btn btn-success pull-right"><i class="fa fa-save"></i> Save</button>
				</div>
			</form>
		</div>
	</div>
</div>

{{-- Modal Add --}}
<div class="modal modal-default fade" id="modal-edit">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12" style="background-color: #e08e0b;">
					<h1 style="text-align: center; margin:5px; font-weight: bold;">Edit Drawing</h1>
				</div>
			</div>
			<form id="edit" method="post" enctype="multipart/form-data" autocomplete="off">
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<input type="text" name="edit_id" id="edit_id" hidden>

							<div class="box-body" style="padding-left: 0px;">
								<div class="form-group row" align="right">
									<label class="col-xs-4">No. Drawing<span class="text-red">*</span></label>
									<div class="col-xs-6" align="left">
										<input type="text" class="form-control" name="edit_item_number" id="edit_item_number"  placeholder="Nomor Drawing" style="width: 100%; font-size: 15px;" required>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4">Deskripsi<span class="text-red">*</span></label>
									<div class="col-xs-6" align="left">
										<textarea class="form-control" rows='3' name="edit_item_description" id="edit_item_description" placeholder="Deskripsi Drawing" style="width: 100%; font-size: 15px;" required></textarea>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-xs-4">Lampiran<span class="text-red">*</span></label>
									<div class="col-xs-6" align="left">
										<input style="height: 37px;" class="form-control" type="file" name="edit_file" id="edit_file" accept="text/plain">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="padding-right: 20%;">
					<button type="submit" class="btn btn-warning pull-right"><i class="glyphicon glyphicon-edit"></i> Edit</button>
				</div>
			</form>
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

		fillTable();
	});

	
	function fillTable() {
		$.get('{{ url("fetch/workshop/drawing") }}', function(result, status, xhr){
			if(result.status){
				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$('#tableBodyList').html("");

				var tableData = "";
				var count = 0
				for (var i = 0; i < result.drawing.length; i++) {
					tableData += '<tr>';
					tableData += '<td>'+ (++count) +'</td>';
					tableData += '<td>'+ (result.drawing[i].item_number || '-') +'</td>';
					tableData += '<td>'+ (result.drawing[i].item_description || '-') +'</td>';
					tableData += '<td>'+ (result.drawing[i].name || '-') +'</td>';
					tableData += '<td>'+ (result.drawing[i].created_at || '-') +'</td>';
					
					if(result.drawing[i].file_name){
						tableData += '<td><a href="javascript:void(0)" onClick="downloadDrw(\''+result.drawing[i].file_name+'\')" class="fa fa-paperclip"></a></td>';
					}else{
						tableData += '<td>-</td>';							
					}

					tableData += '<td style="text-align: center;">';
					tableData += '<button style="width: 25%; height: 100%;" onclick="showEditDrw(\''+result.drawing[i].item_number+'\')" class="btn btn-xs btn-warning form-control"><span><i class="glyphicon glyphicon-edit"></i></span></button>';
					tableData += '</td>';
					
					tableData += '</tr>';	
				}

				$('#tableBodyList').append(tableData);

				$('#tableList tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				});

				var table = $('#tableList').DataTable({
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

				$('#tableList tfoot tr').appendTo('#tableList thead');


				table.on( 'order.dt search.dt', function () {
					table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
						cell.innerHTML = i+1;
					} );
				} ).draw();

			}
			else{
				openErrorGritter('Error!', result.message);
			}
		});
	}

	$("form#add").submit(function(e) {
		$("#loading").show();		

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("create/workshop/drawing") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();

				$("#item_number").val("");
				$("#item_description").val("");
				$("#upload_file").val("");

				$('#modal-add').modal('hide');

				fillTable();			

				openSuccessGritter('Success', result.message);
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


	$("form#edit").submit(function(e) {
		$("#loading").show();		

		e.preventDefault();    
		var formData = new FormData(this);

		$.ajax({
			url: '{{ url("edit/workshop/drawing") }}',
			type: 'POST',
			data: formData,
			success: function (result, status, xhr) {
				$("#loading").hide();

				$("#edit_id").val("");
				$("#edit_item_number").val("");
				$("#edit_item_description").val("");
				$("#edit_file").val("");

				$('#modal-edit').modal('hide');

				fillTable();			

				openSuccessGritter('Success', result.message);
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


	function showEditDrw(id){

		var data = {
			item_number:id
		}

		$.get('{{ url("fetch/workshop/edit_drawing") }}', data, function(result, status, xhr){
			if(result.status){
				document.getElementById("edit_id").value = result.drawing.id;
				document.getElementById("edit_item_number").value = result.drawing.item_number;
				document.getElementById("edit_item_description").value = result.drawing.item_description;
				$("#modal-edit").modal('show');
			}
		});

	}

	function downloadDrw(attachment) {
		var data = {
			file:attachment
		}
		$.get('{{ url("download/workshop/drawing") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					window.open(result.file_path);
				}
				else{
					alert('Attempt to retrieve data failed');
				}
			}
			else{
				alert('Disconnected from server');
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
			time: '3000'
		});
	}

</script>
@endsection