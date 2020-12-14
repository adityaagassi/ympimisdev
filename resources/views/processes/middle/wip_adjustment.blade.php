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
		<button href="javascript:void(0)" class="btn bg-purple btn-sm pull-right" data-toggle="modal"  data-target="#import_modal">
			<i class="fa fa-download"></i>&nbsp;&nbsp;Import Inactive
		</button>
		<button href="javascript:void(0)" class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#create_modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;New Inactive
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
		<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
			<p style="position: absolute; color: White; top: 45%; left: 35%;">
				<span style="font-size: 40px">Loading, please wait...<i class="fa fa-spin fa-refresh"></i></span>
			</p>
		</div>

		<div class="col-xs-7">
			<table id="tableAdjust" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th width="15%">Tag</th>
						<th width="10%">Material</th>
						<th>Material Description</th>
						<th width="1%">Location</th>
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
			<div class="row">
				<div class="col-xs-10" style="padding-right: 0px">
					<b style="font-size: 13pt">item selected from WIP : </b><br>
					<div id="selected1"></div>
				</div>
				<div class="col-xs-2" style="padding-left: 0px">
					<button class="btn btn-warning pull-right" onclick="insertInactive()" style="margin-bottom: 6px">Inactive <i class="fa fa-mail-forward"></i></button>
				</div>
			</div>			
		</div>


		<div class="col-xs-5">
			<table id="tableInactive" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th width="15%">Tag</th>
						<th width="7%">Material</th>
						<th >Material Description</th>
						<th width="1%">Quantity</th>
					</tr>
				</thead>
				<tbody id="tableInactiveBody">
				</tbody>
				<tfoot>
					<tr style="color: black">
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
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
						Add Inactive Material
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />
								<div class="form-group row" align="right">
									<label class="col-sm-4">Tag<span class="text-red">*</span></label>
									<div class="col-sm-4">
										<input type="text" class="form-control" id="tag" placeholder="Enter Tag Code" required>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Material<span class="text-red">*</span></label>
									<div class="col-sm-4">
										<input type="text" class="form-control" id="material" placeholder="Enter Material" required>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Quantity<span class="text-red">*</span></label>
									<div class="col-sm-2">
										<input type="text" class="form-control" id="quantity" placeholder="Enter Quantity" required>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="tambahInAktif()"><i class="fa fa-plus"></i> Add Inactive</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal modal-default fade" id="import_modal">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Import Inactive Material
					</h4>
				</div>
				<form method="post" action="{{ url('import/barrel_inactive') }}" enctype="multipart/form-data">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-body">
						<div class="row">
							<div class="col-xs-12">
								<div class="box-body">
									<input type="file" name="inactive_material" accept="text/plain">
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-sm bg-purple" type="submit"><i class="fa fa-download"></i> Import</button>
					</div>
				</form>
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

		tableAdjust();
		tableInactive();
		$('.datetime').datetimepicker({
			format: 'YYYY-MM-DD HH:mm:ss'
		});
	});	
	

	// -------------------- TABEL ADJUST ----------------------

	function tableAdjust() {
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
					"url" : "{{ url("fetch/middle/wip") }}"
				},
				"columns": [
				{ "data": "tag"},
				{ "data": "material_number"},
				{ "data": "material_description" },
				{ "data": "location"},
				{ "data": "quantity"},
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

	// ----------------- TABEL INACTIVE ----------------------

	function tableInactive() {
		$('#tableInactive tfoot th').each(function(){
			var title2 = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title2+'" size="8"/>' );
		});


		var table2 = $('#tableInactive').DataTable({
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
			// "sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/middle/barrel_inactive/wip") }}"
			},
			"columns": [
			{ "data": "tag"},
			{ "data": "material_number"},
			{ "data": "material_description" },
			{ "data": "quantity"}]
		});

		table2.columns().every( function () {
			var that2 = this;

			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that2.search() !== this.value ) {
					that2
					.search( this.value )
					.draw();
				}
			} );
		} );

		$('#tableInactive tfoot tr').appendTo('#tableInactive thead');


	}


	function insertInactive() {
		var data = [];
		var tag = [];
		var material = [];
		var qty = [];
		var stat = [];

		$.each( arr, function( key, value ) {
			tag.push(arr[key][0]);
			material.push(arr[key][1]);
			qty.push(arr[key][2]);
			stat.push(arr[key][3]);
		})

		data.push({tag,material,qty,stat});

		var arrs = {
			data:data
		}

		$.post('{{ url("post/middle/barrel_inactive_wip") }}', arrs, function(result, status, xhr){
			if (result.status) {
				$('#tableAdjust').DataTable().ajax.reload();
				$('#tableInactive').DataTable().ajax.reload();

				arr = [];
				$("#selected1").empty();

				openSuccessGritter('Success','Insert Inactive Success');
			} else {
				openErrorGritter('Error','Insert Failed');
			}
			
		});
	}

	function inactive(elem) {
		var id = $(elem).attr("id");
		var data = id.split("+");
		if ($(elem).is(':checked')) {
			arr.push(data);
			$("#selected1").empty();

			$.each( arr, function( key, value ) {
				$("#selected1").append(value[0]+" ");
			});

		} else {
			arr.splice($.inArray(id, arr),1);
			$("#selected1").empty();

			$.each( arr, function( key, value ) {
				$("#selected1").append(value[0]+" ");
			});
		}

	}

	function tambahInAktif() {
		var tag = $("#tag").val();
		var qty = $("#quantity").val();
		var mat = $("#material").val();

		if ($.isNumeric(qty) && qty != "" && tag != "" && mat != "") {
			var data = {
				tag:tag,
				material:mat,
				quantity:qty
			};

			$.post('{{ url("post/middle/new/barrel_inactive") }}', data, function(result, status, xhr){
				if (result.status) {
					$("#tag").val("");
					$("#quantity").val("");
					$("#material").val("");
					$('#tableAdjust').DataTable().ajax.reload();
					$('#tableInactive').DataTable().ajax.reload();
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

	function importInactive() {
		console.log($("#import_inactive").val());
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

	$('.timepicker').timepicker({
		use24hours: true,
		showInputs: false,
		showMeridian: false,
		minuteStep: 10,
		defaultTime: '00:00',
		timeFormat: 'h:mm'
	})

	$('.datepicker').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd',
		todayHighlight: true
	});

</script>
@endsection