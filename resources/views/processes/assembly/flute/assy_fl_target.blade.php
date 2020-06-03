@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<style type="text/css">
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
</style>
@stop
@section('header')
<section class="content-header" style="text-align: center;">
	<span style="font-size: 3vw; color: red;"><i class="fa fa-angle-double-down"></i> Assembly Flute Target <i class="fa fa-angle-double-down"></i></span>
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
		<div class="input-group col-md-8 col-md-offset-2">
			<div class="box box-danger">
				<div class="box-body">
					<table id="tableList" class="table table-bordered table-striped" style="width: 100%; margin-bottom: 1%;">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>#</th>
								<th>Target Name</th>
								<th>Location</th>
								<th>Target</th>
								<th>Unit</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody id='tableBodyList'>
						</tbody>
					</table>

					<div class="col-md-2 pull-right" id="delete_button">
					</div>

				</div>
			</div>
		</div>
	</div>

	{{-- Modal Update --}}
	<div class="modal modal-default fade" id="edit_modal">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Edit Target
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12">
							<div class="box-body">
								<input type="hidden" value="{{csrf_token()}}" name="_token" />

								<input type="hidden" id="id">
								<div class="form-group row" align="right">
									<label class="col-sm-4">Target Name</label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="target_name" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Location</label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="location" readonly>
									</div>
								</div>

								<div class="form-group row" align="right">
									<label class="col-sm-4">Unit</label>
									<div class="col-sm-5" align="left">
										<input type="text" class="form-control" id="unit" readonly>
									</div>
								</div>
								<div class="form-group row" align="right">
									<label class="col-sm-4">Target</label>
									<div class="col-sm-3" align="left">
										<input style="text-align: center;" type="number" class="form-control" id="target">
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-success" onclick="edit()"><span><i class="fa fa-save"></i> Save</span></button>
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
		fillTable();		
	});

	function fillTable(){

		$.get('{{ url("fetch/middle/buffing_target", "assy_fl") }}', function(result, status, xhr) {
			if(result.status){
				$('#tableList').DataTable().clear();
				$('#tableList').DataTable().destroy();
				$('#tableBodyList').html("");

				var tableData = "";
				var count = 0;
				for (var i = 0; i < result.target.length; i++) {
					tableData += '<tr>';
					tableData += '<td>'+ ++count +'</td>';
					tableData += '<td>'+ result.target[i].target_name +'</td>';
					tableData += '<td>'+ result.target[i].location +'</td>';
					tableData += '<td>'+ result.target[i].target +'</td>';
					tableData += '<td>'+ result.target[i].unit +'</td>';
					tableData += '<td style="text-align: center;">';
					tableData += '<button style="width: 50%;" onClick="showModal(this)" id="'+result.target[i].id+'+'+ result.target[i].target_name +'+'+ result.target[i].location +'+'+ result.target[i].target +'+'+ result.target[i].unit +'" class="btn btn-xs btn-warning">Edit</button>';
					tableData += '</td>';
					tableData += '</tr>';
				}

				$('#tableBodyList').append(tableData);
				$('#tableList').DataTable({
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

		});

	}

	function showModal(elem){
		var target = $(elem).attr("id");
		var data = target.split("+");

		var id = data[0];
		var target_name = data[1];
		var location = data[2];
		var target = data[3];
		var unit = data[4];

		document.getElementById("id").value = id;
		document.getElementById("target_name").value = target_name;
		document.getElementById("location").value = location;
		document.getElementById("unit").value = unit;

		$("#edit_modal").modal('show');
	}

	function edit(){
		var id = $("#id").val();
		var target = $("#target").val();

		if(!isNumeric(target)){
			openErrorGritter('Error!', 'The target value must be in number format');
			return false;
		}

		var data = {
			id : id,
			target : target,
		}

		$.post('{{ url("update/middle/buffing_target") }}', data,  function(result, status, xhr){
			if(result.status){
				fillTable();
				openSuccessGritter('Success', result.message);
				location.reload(true);
				$("#edit_modal").modal('hide');
			}else{
				openErrorGritter('Error!', result.message);
				location.reload(true);		
			}
		});
	}

	function isNumeric(n) {
		return !isNaN(parseFloat(n)) && isFinite(n);
	}

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

</script>
@endsection