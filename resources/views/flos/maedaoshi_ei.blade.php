@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style>
table {
	table-layout:fixed;
}
td{
	overflow:hidden;
	text-overflow: ellipsis;
}
td:hover {
	overflow: visible;
}
</style>
@endsection
@section('header')
<section class="content-header">
	<h1>
		Maedaoshi <span class="text-purple">前倒し</span>
		<small>Educational Instrument <span class="text-purple">教育管楽器</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<button href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reprintModal">
				<i class="fa fa-print"></i>&nbsp;&nbsp;Reprint Maedaoshi
			</button>
			<a href="{{ url("/index/after_maedaoshi_ei") }}" class="btn btn-primary btn-sm" style="color:white"><i class="fa fa-fast-forward"></i>&nbsp;&nbsp;After Maedaoshi</a>
		</li>
	</ol>
</section>
@endsection

@section('content')
<section class="content">
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Success!</h4>
		{{ session('status') }}
	</div>   
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-danger">
				<div class="box-header">
					<h3 class="box-title">Scan Maedaoshi<span class="text-purple"> 前倒しスキャン</span></h3>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-3">
							<div class="input-group col-md-12">
								<div class="input-group-addon" id="icon-material">
									<i class="glyphicon glyphicon-barcode"></i>
								</div>
								<input type="text" style="text-align: center" class="form-control" id="material" placeholder="Material Number" required>
							</div>
							{{-- &nbsp;
							<div class="input-group col-md-12">
								<div class="input-group-addon" id="icon-serial">
									<i class="glyphicon glyphicon-barcode"></i>
								</div>
								<input type="text" style="text-align: center" class="form-control" id="serial" placeholder="Serial Number" required>
							</div> --}}
						</div>
						<div class="col-md-9">
							<div class="input-group col-md-8 col-md-offset-2">
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold">Maedaoshi
								</div>
								<input type="text" style="text-align: center; font-size: 22" class="form-control" id="maedaoshi" placeholder="Not Available" required>
								<div class="input-group-addon" id="icon-serial">
									<i class="glyphicon glyphicon-lock"></i>
								</div>
							</div>
							&nbsp;
							<table id="maedaoshiTable" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th style="font-size: 14">Serial</th>
										<th style="font-size: 14">Material</th>
										<th style="font-size: 14">Description</th>
										<th style="font-size: 14">Qty</th>
										<th style="font-size: 14">Del.</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal modal-default fade" id="reprintModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="titleModal">Reprint Maedaoshi</h4>
			</div>
			<form class="form-horizontal" role="form" method="get" action="{{url('reprint/maedaoshi')}}">
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="modal-body" id="messageModal">
					<label>Maedaoshi</label>
					<select class="form-control select2" name="maedaoshiReprint" style="width: 100%;" data-placeholder="Choose a FLO..." id="flo_number_reprint" required>
						<option value=""></option>
						@foreach($flos as $flo)
						<option value="{{ $flo->flo_number }}">{{ $flo->flo_number }}</option>
						@endforeach
					</select>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button id="modalReprintButton" type="submit" class="btn btn-danger"><i class="fa fa-print"></i>&nbsp; Reprint</button>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$(function () {
			$('.select2').select2()
		});

		refresh();

		var delay = (function(){
			var timer = 0;
			return function(callback, ms){
				clearTimeout (timer);
				timer = setTimeout(callback, ms);
			};
		})();

		$("#material").on("input", function() {
			delay(function(){
				if ($("#material").val().length < 7) {
					$("#material").val("");
				}
			}, 300 );
		});

		$('#material').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#material").val().length == 7){
					scanMaterial();
					return false;
				}
				else{
					openErrorGritter('Error!', 'Material number invalid.');
					audio_error.play();
					$("#material").val("");
				}
			}
		});

	});

	function scanMaterial(){
		$('#material').prop('disabled', true);
		var material = $('#material').val();
		var data = {
			material:material,
			type:'pd',
		}
		$.get('{{ url("scan/maedaoshi_material") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#maedaoshiTable').DataTable().destroy();
					fillMaedaoshiTable(result.maedaoshi);
					$('#maedaoshi').val(result.maedaoshi);
					scanSerial();
				}
				else{
					$('#material').prop('disabled', false);
					openErrorGritter('Error!', result.message);
					audio_error.play();
					$("#material").val('');
				}
			}
			else{
				$('#material').prop('disabled', false);
				$('#material').val('');
				audio_error.play();
				alert('Disconnected from server.');
				$('#material').focus();
			}
		});
	}

	function scanSerial(){
		var material = $('#material').val();
		var maedaoshi = $('#maedaoshi').val();
		var data = {
			material:material,
			maedaoshi:maedaoshi,
			type:'pd',
		}
		$.get('{{ url("scan/maedaoshi_serial") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					if(result.status_code == 'new'){
						$('#maedaoshi').val(result.maedaoshi);
						$('#maedaoshiTable').DataTable().destroy();
						fillMaedaoshiTable(result.maedaoshi);
					}
					else{
						$('#maedaoshiTable').DataTable().ajax.reload();
					}
					$('#material').prop('disabled', false);
					$('#material').val('');
					$('#material').focus();
				}
				else{
					openErrorGritter('Error!', result.message);
					audio_error.play();
					$("#material").val("");
					$("#material").prop("disabled", false);
					$("#material").focus();
				}
			}
			else{
				alert('Disconnected from server');
				audio_error.play();
				$("#material").val("");
				$("#material").prop("disabled", true);
				$("#material").focus();
			}
		});
	}

	function fillMaedaoshiTable(maedaoshi){
		var index_maedaoshi = maedaoshi;
		var data_maedaoshi = {
			maedaoshi : index_maedaoshi
		}
		var t = $('#maedaoshiTable').DataTable( {
			"sDom": '<"top"i>rt<"bottom"flp><"clear">',
			'paging'      	: false,
			'lengthChange'	: false,
			'searching'   	: false,
			'ordering'    	: false,
			'info'       	: true,
			'autoWidth'		: false,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"infoCallback": function( settings, start, end, max, total, pre ) {
				return "<b>Total "+ total +" pc(s)</b>";
			},
			"processing": true,
			"serverSide": true,
			"ajax": {
				"type" : "get",
				"url" : "{{ url("fetch/maedaoshi") }}",
				"data": data_maedaoshi
			},
			"columns": [
			{ "data": "serial_number", "sWidth": "14%" },
			{ "data": "material_number", "sWidth": "12%" },
			{ "data": "material_description", "sWidth": "62%" },
			{ "data": "quantity", "sWidth": "5%" },
			{ "data": "action", "sWidth": "4%" }
			]
		});
	}

	function deleteConfirmation(id){
		var maedaoshi = $("#maedaoshi").val(); 
		var data = {
			id: id,
			maedaoshi : maedaoshi
		};
		if(confirm("Are you sure you want to delete this data?")){
			$.post('{{ url("destroy/maedaoshi") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);

				if(xhr.status == 200){
					if(result.status){
						$('#maedaoshiTable').DataTable().ajax.reload();
						$("#material").prop('disabled', false);
						$("#material").val("");
						$("#material").focus();
						openSuccessGritter('Success!', result.message);
					}
					else{
						openErrorGritter('Error!', result.message);
						audio_error.play();
					}
				}
				else{
					openErrorGritter('Error!', 'Disconnected from server');
					audio_error.play();
				}
			});
		}
		else{
			return false;
		}
	}

	function refresh(){
		$('#material').val("");
		$('#maedaoshi').val('');
		$('#maedaoshi').prop('disabled', true);
		$('#material').focus();
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

	function openInfoGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-info',
			image: '{{ url("images/image-unregistered.png") }}',
			sticky: false,
			time: '2000'
		});
	}

</script>
@endsection