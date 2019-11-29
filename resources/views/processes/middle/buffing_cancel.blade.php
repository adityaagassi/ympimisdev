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
		border:1px solid black;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header" style="text-align: center;">
	<span style="font-size: 3vw; color: red;"><i class="fa fa-angle-double-down"></i> Buffing Cancel <i class="fa fa-angle-double-down"></i></span>
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
		<div class="col-md-8 col-md-offset-2" style="margin-bottom: 2%">
			<div class="input-group col-md-12" style="text-align: center;">
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 3vw; border-color: red;">
					<i class="glyphicon glyphicon-barcode"></i>
				</div>
				<input type="text" style="text-align: center; border-color: red; font-size: 3vw; height: 70px" class="form-control" id="tag" name="tag" placeholder="Scan Kanban Here" required>
				<div class="input-group-addon" id="icon-serial" style="font-weight: bold; font-size: 3vw; border-color: red;">
					<i class="glyphicon glyphicon-barcode"></i>
				</div>
			</div>
		</div>
		<br>
	</div>

	<div class="row">
		<div class="input-group col-md-8 col-md-offset-2">
			<div class="box box-danger">
				<div class="box-body">
					<table id="returnTable" class="table table-bordered table-striped table-hover" style="width: 100%; margin-bottom: 1%;">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th>Tag</th>
								<th>Operator</th>
								<th>Material Number</th>
								<th>Model</th>
								<th>Key</th>
								<th>Finish Buffing</th>
							</tr>
						</thead>
						<tbody id='cancel'>
						</tbody>
					</table>

					<div class="col-md-2 pull-right" id="delete_button">
					</div>

				</div>
			</div>
		</div>
	</div>

	{{-- Modal Delete --}}
	<div class="modal modal-danger fade" id="delete_modal">
		<div class="modal-dialog modal-xs">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">
							&times;
						</span>
					</button>
					<h4 class="modal-title">
						Delete Buffing Log 
					</h4>
				</div>
				<div class="modal-body">
					<div class="modal-body">
						<h5 id="delete_confirmation_text"></h5>
					</div>
					<input type="hidden" id="idx">
					<input type="hidden" id="operator_id">
					<input type="hidden" id="tag">
					<input type="hidden" id="model">
					<input type="hidden" id="key">
					<input type="hidden" id="qty">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button class="btn btn-default" onclick="deleteBuff()"><span><i class="fa fa-trash"></i> Delete</span></button>
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
		$("#tag").val("");
		$('#tag').focus();
		
		$('#tag').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#tag").val().length >= 10){
					scanSerialNumber();
					return false;
				}
				else{
					openErrorGritter('Error!', 'Kanban invalid.');
					audio_error.play();
					$("#tag").val("");
					$("#tag").focus();
				}
			}
		});

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

	function deleteBuff(){

		var idx = $("#idx").val();
		var operator_id = $("#operator_id").val();
		var tag = $("#tag").val();
		var model = $("#model").val();
		var key = $("#key").val();
		var qty = $("#qty").val();
		
		var data = {
			idx : idx,
			operator_id : operator_id,
			tag : tag,
			model : model,
			key : key,
			qty : qty
		}

		$.post('{{ url("delete/middle/buffing_canceled") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$("#delete_modal").modal('hide');
					$("#tag").val("");
					$("#tag").focus();
					$('#cancel').html("");
					$('#delete_button').html("");
					openSuccessGritter('Success!', result.message);
				}
				else{
					openErrorGritter('Error!', result.message);
					audio_error.play();
					$("#tag").val("");
					$("#tag").focus();
				}
			}
			else{
				alert('Disconnected from server');
			}
		});

	}

	function showModal(tag){
		$("#delete_confirmation_text").append().empty();
		$("#delete_confirmation_text").append("Are you sure want to delete buffing log tag <b>"+ tag +"</b> ?");
		$("#delete_modal").modal('show');

	}

	function scanSerialNumber(){
		var tag = $("#tag").val();
		
		var data = {
			tag : tag
		}

		$.get('{{ url("fetch/middle/buffing_canceled") }}', data, function(result, status, xhr){
			if(xhr.status == 200){
				if(result.status){
					$("#tag").val("");     
					$("#tag").focus();
					openSuccessGritter('Success!', result.message);

					$('#cancel').html("");
					$('#delete_button').html("");

					var body = '';
					for (var i = 0; i < result.cancel.length; i++) {
						body += '<th>'+result.cancel[i].material_tag_id+'</th>';
						body += '<th>'+result.operator[i].name+'</th>';
						body += '<th>'+result.cancel[i].material_number+'</th>';
						body += '<th>'+result.cancel[i].model+'</th>';
						body += '<th>'+result.cancel[i].key+'</th>';
						body += '<th>'+result.cancel[i].selesai_start_time+'</th>';
					}

					$('#cancel').append('<tr>'+body+'</tr>');
					$('#cancel').css('border-color', '#2a2a2b');

					var button = '<div class="col-xs-12" style="padding-left: 0px"><button class="btn btn-danger pull-right" onclick="showModal('+result.cancel[0].material_tag_id+')" style="margin-bottom: 6px"><i class="fa fa-trash"></i>  Delete</button></div>';
					$('#delete_button').append(button);

					document.getElementById("idx").value = result.cancel[0].idx;
					document.getElementById("operator_id").value = result.cancel[0].operator_id;
					document.getElementById("tag").value = result.cancel[0].material_tag_id;
					document.getElementById("model").value = result.cancel[0].model;
					document.getElementById("key").value = result.cancel[0].key;
					document.getElementById("qty").value = result.cancel[0].material_qty;

				}
				else{
					openErrorGritter('Error!', result.message);
					audio_error.play();
					$("#tag").val("");
					$("#tag").focus();
				}
			}
			else{
				alert('Disconnected from server');
			}
		});

		
	}


</script>
@endsection