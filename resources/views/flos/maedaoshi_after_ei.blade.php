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
@stop
@section('header')
<section class="content-header">
	<h1>
		After Maedaoshi <span class="text-purple">?????</span>
		<small>Educational Instrument <span class="text-purple">教育管楽器</span></small>
	</h1>
	<ol class="breadcrumb">
		<a href="{{ url("/index/maedaoshi_ei") }}" class="btn btn-primary btn-sm" style="color:white"><i class="fa fa-arrow-left"></i>&nbsp;&nbsp;Back To Maedaoshi</a>
	</ol>
</section>
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
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
					<h3 class="box-title">Fulfillment <span class="text-purple">充足</span></h3>
				</div>
				<form class="form-horizontal" role="form" method="post" action="{{url('print/flo')}}">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="box-body">
						<div class="row">
							<div class="col-md-3">
								<div class="input-group col-md-12">
									<label id="labelYMJ">
										<input type="checkbox" class="minimal-red" id="ymj">&nbsp;<i class="fa fa-arrow-left text-red"></i>
										<br>
										<span class="text-red">&nbsp;<i class="fa fa-arrow-up"></i>&nbsp; Check if product for YMJ &nbsp;<i class="fa fa-exclamation"></i></span>
									</label>
								</div>
								<br>
								<div class="input-group col-md-12">
									<div class="input-group-addon" id="icon-material">
										<i class="glyphicon glyphicon-barcode"></i>
									</div>
									<input type="text" style="text-align: center" class="form-control" id="material" name="material_number" placeholder="Material Number" required>
								</div>
							</div>
							<div class="col-md-9">
								<div class="input-group col-md-8 col-md-offset-2">
									<div class="input-group-addon" id="icon-serial" style="font-weight: bold">FLO
									</div>
									<input type="text" style="text-align: center; font-size: 22" class="form-control" id="flo_number" name="flo_number" placeholder="Not Available" required>
									<div class="input-group-addon" id="icon-serial">
										<i class="glyphicon glyphicon-lock"></i>
									</div>
								</div>
								&nbsp;
								<table id="flo_detail_table" class="table table-bordered table-striped">
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
				</form>
			</div>
		</div>
	</div>


</section>


@stop
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	jQuery(document).ready(function() {

		$("#ymj").prop('checked', false);
		$('input[type="checkbox"].minimal-red').iCheck({
			checkboxClass: 'icheckbox_minimal-red',
			radioClass   : 'iradio_minimal-red'
		});

		refresh();

		var delay = (function(){
			var timer = 0;
			return function(callback, ms){
				clearTimeout (timer);
				timer = setTimeout(callback, ms);
			};
		})();

		$("#material_number").on("input", function() {
			delay(function(){
				if ($("#material_number").val().length < 7) {
					$("#material_number").val("");
				}
			}, 20 );
		});

		$('#material').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#material").val().length == 7){
					scanMaterialNumber();
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

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function scanMaterialNumber(){
		$("#material").prop('disabled',true);
		var material_number = $("#material").val();
		var ymj = $("#ymj").is(":checked");
		var data = {
			material_number : material_number,
			ymj : ymj,
			type : 'pd',
		}
		$.get('{{ url("scan/after_maedaoshi_material") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					$('#flo_detail_table').DataTable().destroy();
					fillFloTable(result.flo_number);
					$("#flo_number").val(result.flo_number);
					scanSerialNumber();
				}
				else{
					openErrorGritter('Error!', result.message);
					audio_error.play();
					$("#material").prop('disabled', false);
					$("#material").val("");
				}
			}
			else{
				openErrorGritter('Error!', 'Disconnected from server');
				audio_error.play();
				$("#material").prop('disabled', false);
				$("#material").val("");
			}
		});
	}

	function scanSerialNumber(){
		var material_number = $("#material").val();
		var flo_number = $("#flo_number").val();
		var ymj = $("#ymj").is(":checked");
		var data = {
			material_number : material_number,
			flo_number : flo_number,
			ymj : ymj,
			type: 'ei',
		}
		$.get('{{ url("scan/after_maedaoshi_serial") }}', data, function(result, status, xhr){
			console.log(status);
			console.log(result);
			console.log(xhr);
			if(xhr.status == 200){
				if(result.status){
					openSuccessGritter('Success!', result.message);
					if(result.status_code == 'new'){
						$("#flo_number").val(result.flo_number);
						$('#flo_detail_table').DataTable().destroy();
						fillFloTable(result.flo_number);
					}
					else{
						$('#flo_detail_table').DataTable().ajax.reload();
					}

					$("#material").val("");
					$("#material").prop("disabled", false);
					$("#material").focus();
				}
				else{
					openErrorGritter('Error!', result.message);
					$("#material").val("");
					$("#material").prop("disabled", false);
					$("#material").focus();
					audio_error.play();
				}
			}
			else{
				openErrorGritter('Error!', 'Disconnected from server');
				audio_error.play();
				$("#material").val("");
				$("#material").prop("disabled", false);
				$("#material").focus();
			}
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

	function fillFloTable(flo_number){
		var index_flo_number = flo_number;
		var data_flo = {
			flo_number : index_flo_number
		}
		var t = $('#flo_detail_table').DataTable( {
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
				"type" : "post",
				"url" : "{{ url("index/flo_detail") }}",
				"data": data_flo
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
		var flo_number = $("#flo_number").val(); 
		var data = {
			id: id,
			flo_number : flo_number
		};
		if(confirm("Are you sure you want to delete this data?")){
			$.post('{{ url("destroy/serial_number") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);

				if(xhr.status == 200){
					if(result.status){
						$('#flo_detail_table').DataTable().ajax.reload();
						$("#serial").prop('disabled', true);
						$("#material").prop('disabled', false);
						$("#serial").val("");
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
		$('#labelYMJ').hide();
		$("#flo_number").prop('disabled', true);
		$("#material").val('');
		$('#flo_number').val('');
		$("#material").prop('disabled', false);
		$("#material").focus();
	}

</script>
@stop