@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:center;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:center;
	}
	tfoot>tr>th{
		text-align:center;
	}
	th:hover {
		overflow: visible;
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
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}

	.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small>WIP Control <span class="text-purple"> ??</span></small>
		<button href="javascript:void(0)" class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#create_modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add Queue
		</button>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
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
	<div class="row">
		<div class="col-xs-3">
			<div class="form-group">
				<select class="form-control select2" multiple="multiple" id='grup' data-placeholder="Select Group" style="width: 100%;">
					<option value="SXKEY-82">SXKEY-82</option>
					<option value="SXKEY-C">SXKEY-C</option>
					<option value="SXKEY-D">SXKEY-D</option>
					<option value="SXKEY-E">SXKEY-E</option>
					<option value="SXKEY-F">SXKEY-F</option>
					<option value="SXKEY-G">SXKEY-G</option>
					<option value="SXKEY-H">SXKEY-H</option>
					<option value="SXKEY-J">SXKEY-J</option>
				</select>
			</div>
		</div>
		<div class="col-xs-2" style="padding: 0px;">
			<button class="btn btn-success" onclick="fillTable()">Show Queue</button>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-8 pull-left">
			<table id="tableAdjust" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th>id</th>
						<th width="15%">Rack</th>
						<th width="10%">Material</th>
						<th>Material Description</th>
						<th width="1%">Quantity</th>
						<th>Created at</th>
						<th width="1%"> Check</th>				
					</tr>
				</thead>
				<tbody id="tableAdjustBody">
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
		<div class="col-xs-4 pull-right">
			<div class="row">
				<div class="col-xs-12" style="padding-right: 0px">
					<b style="font-size: 13pt">item selected : </b><br>
					<div id="selected1"></div>
				</div>
				<div class="col-xs-12" style="padding-left: 0px">
					<button class="btn btn-danger pull-right" onclick="deleteQueue()" style="margin-bottom: 6px">Delete <i class="fa fa-mail-forward"></i></button>
				</div>
			</div>		
		</div>
	</div>

	<div class="modal modal-default fade" id="create_modal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Add Buffing Queue
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="form-group row" align="right">
									<label class="col-sm-4">Rack<span class="text-red">*</span></label>
									<div class="col-sm-3" align="left">
										<select class="form-control select2" data-placeholder="Select Rack" name="rack" id="rack" style="width: 100%">
											<option value=""></option>
											<option value="SXKEY-82">SXKEY-82</option>
											<option value="SXKEY-C">SXKEY-C</option>
											<option value="SXKEY-D">SXKEY-D</option>
											<option value="SXKEY-E">SXKEY-E</option>
											<option value="SXKEY-F">SXKEY-F</option>
											<option value="SXKEY-G">SXKEY-G</option>
											<option value="SXKEY-H">SXKEY-H</option>
											<option value="SXKEY-J">SXKEY-J</option>

										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Material<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select2" data-placeholder="Select Material" name="material" id="material" style="width: 100%">
											<option value=""></option>
											@foreach($materials as $material) 
											<option value="{{ $material->material_number }}-{{ $material->model }}">{{ $material->material_description }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Kanban Qty.<span class="text-red">*</span></label>
									<div class="col-sm-3">
										<input type="number" class="form-control" id="kanban" placeholder="Kanban Qty" required>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Position<span class="text-red">*</span></label>
									<div class="col-sm-3" align="left">
										<select class="form-control select2" data-placeholder="Select Position" name="add" id="add" style="width: 100%">
											<option value=""></option>
											<option value="top">Top</option>
											<option value="bottom">Bottom</option>
										</select>
									</div>


								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="addQueue()"><i class="fa fa-plus"></i> Add Queue</button>
				</div>
			</div>
		</div>
	</div>




</section>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr = [];
	var arr2 = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.select2').select2();

		fillTable();
	});	

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

	function fillTable(){
		$('#tableAdjust').DataTable().destroy();

		var grup = $('#grup').val();

		var data = {
			grup:grup,
		}

		$.get('{{ url("fetch/middle/buffing_adjustment") }}', data, function(result, status, xhr) {
			if(result.status){

			}
		});

		$('#tableAdjust tfoot th').each(function(){
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
		});


		var table = $('#tableAdjust').DataTable({
			'dom': 'Brtip',
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
				}
				]},
				'paging': true,
				'lengthChange': true,
				'searching': true,
				'ordering': false,
				'order': [],
				'info': true,
				'autoWidth': true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
				"ajax": {
					"type" : "get",
					"url" : "{{ url("fetch/middle/buffing_adjustment") }}",
					"data" : data
				},
				"columns": [
				{ "data": "idx"},
				{ "data": "rack"},
				{ "data": "material_num"},
				{ "data": "material_description" },
				{ "data": "material_qty"},
				{ "data": "created_at"},
				{ "data": "check" }]
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

		$('#tableAdjust tfoot tr').appendTo('#tableAdjust thead');
	}


	function showSelected(elem) {
		var id = $(elem).attr("id");
		var data = id.split("+");
		if ($(elem).is(':checked')) {
			arr.push(data);
			$("#selected1").empty();

			$.each( arr, function( key, value ) {
				$("#selected1").append(value[0]+" - "+value[1]+"<br>");
			});

		} else {
			arr.splice($.inArray(id, arr),1);
			$("#selected1").empty();

			$.each( arr, function( key, value ) {
				$("#selected1").append(value[0]+" - "+value[1]+"<br>");
			});
		}
	}

	function deleteQueue() {
		var data = [];
		var idx = [];

		$.each( arr, function( key, value ) {
			idx.push(arr[key][0]);
		})

		var arrs = {
			idx:idx
		}

		$.post('{{ url("post/middle/buffing_delete_queue") }}', arrs, function(result, status, xhr){
			if (result.status) {
				$('#tableAdjust').DataTable().ajax.reload();
				
				arr = [];
				$("#selected1").empty();
				openSuccessGritter('Success','Delete Queue Success');
			} else {
				openErrorGritter('Error','Delete Queue Failed');
			}
		});
	}

	function addQueue() {
		var rack = $('#rack').val();
		var material = $('#material').val();
		var kanban = $('#kanban').val();
		var add = $('#add').val();

		if (rack != "" && material != "" && kanban != "" && add != "") {
			var data = {
				rack:rack,
				material:material,
				kanban:kanban,
				add:add,
			}
			
			$.post('{{ url("post/middle/buffing_add_queue") }}', data, function(result, status, xhr){
				if(result.status){
					$("#rack").val("");
					$("#material").val("");
					$("#kanban").val("");
					$("#add").val("");

					$("#create_modal").modal('hide');
					
					$('#tableAdjust').DataTable().ajax.reload();
					openSuccessGritter('Success','Insert Inactive Success');
				} else {
					audio_error.play();
					openErrorGritter('Error','Insert Failed');
				}
			})
		} else {
			audio_error.play();
			openErrorGritter('Error','Invalid Value');
		}

	}


</script>
@endsection