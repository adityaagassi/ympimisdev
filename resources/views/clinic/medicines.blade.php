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
		<small><span class="text-purple"> {{ $title_jp }}</span></small>
		<button href="javascript:void(0)" class="btn btn-success btn-sm pull-right" data-toggle="modal"  data-target="#create_modal" style="margin-right: 5px">
			<i class="fa fa-plus"></i>&nbsp;&nbsp;Add New Medicines
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

	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>


	<div class="row">
		<div class="col-xs-12 ">
			<table id="tableMed" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
				<thead style="background-color: rgb(126,86,134); color: #FFD700;">
					<tr>
						<th width="1%">No</th>
						<th width="20%">Medicine Name</th>
						<th width="9%">Quantity</th>
						<th width="5%">Action</th>				
					</tr>
				</thead>
				<tbody id="tableMedBody">
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
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Add New Medicine
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<div class="form-group row" align="right">
									<label class="col-sm-4">Type<span class="text-red">*</span></label>
									<div class="col-sm-5" align="left">
										<select class="form-control select2" data-placeholder="Select Type" name="type" id="type" style="width: 100%">
											<option style="color:grey;" value="">Select Type</option>
											<option value="medicine">Medicine</option>
											<option value="equipment">Equipment</option>

										</select>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Medicine<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="medicines" placeholder="Medicine" required>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="addQueue()"><i class="fa fa-save"></i> Submit</button>
				</div>
			</div>
		</div>
	</div>


	<div class="modal modal-default fade" id="stock_modal">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Add Stock Medicine
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<input type="hidden" id="add_id" >
								
								<div class="form-group row" align="right">
									<label class="col-sm-4">Medicine Name<span class="text-red">&nbsp;</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="add_medicine" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Current Stock<span class="text-red">&nbsp;</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="add_curent" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Add Stock<span class="text-red">*</span></label>
									<div class="col-sm-5">
										<input type="text" class="form-control" id="add_quantity" placeholder="Medicine" required>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="save()"><i class="fa fa-save"></i> Save</button>
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
			time: '2000'
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

	function fillTable(){
		$('#tableMed').DataTable().destroy();

		var grup = $('#grup').val();

		var data = {
			grup:grup,
		}

		$('#tableMed tfoot th').each(function(){
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
		});


		var table = $('#tableMed').DataTable({
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
				'info': true,
				'autoWidth': true,
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
				"ajax": {
					"type" : "get",
					"url" : "{{ url("fetch/medicines") }}",
					"data" : data
				},

				"columns": [
				{ "data": "id"},
				{ "data": "medicine_name"},
				{ "data": "quantity"},
				{ "data": "button" }
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

		$('#tableMed tfoot tr').appendTo('#tableMed thead');

	}


	function addStock(elem) {
		var id = $(elem).attr("id");

		var data = id.split("#");

		$("#add_id").val(data[0]);
		$("#add_medicine").val(data[1]);
		$("#add_curent").val(data[2]);

		$('#stock_modal').modal('show');

	}

	function save() {
		
		var id = $('#add_id').val();
		var quantity = $('#add_quantity').val();

		if (quantity != "") {
			var data = {
				id:id,
				quantity:quantity,
			}
			$("#loading").show();

			
			$.post('{{ url("edit/medicine_stock") }}', data, function(result, status, xhr){
				if(result.status){
					$("#loading").hide();

					$("#add_id").val("");
					$("#add_medicine").val("");
					$("#add_curent").val("");
					$("#add_quantity").val("");

					$("#stock_modal").modal('hide');
					
					$('#tableMed').DataTable().ajax.reload();
					openSuccessGritter('Success','Add Stock Success');
				} else {
					$("#loading").hide();
					audio_error.play();
					openErrorGritter('Error','Add Stock Failed');
				}
			})
		} else {
			audio_error.play();
			openErrorGritter('Error','Stock Empty');
		}

	}


</script>
@endsection