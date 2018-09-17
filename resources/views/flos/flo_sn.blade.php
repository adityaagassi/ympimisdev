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
		Final Line Outputs
		<small>Band Instrument</small>
	</h1>
	<ol class="breadcrumb">
		{{-- <li><a href="{{ url("create/destination")}}" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a></li> --}}
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
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-danger">
				<div class="box-header">
					<h3 class="box-title">FLO <i class="fa fa-angle-right"></i> Print</h3>
				</div>
				<!-- /.box-header -->
				<form class="form-horizontal" role="form" method="post" action="{{url('print/flo_sn')}}">
					<div class="box-body">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						<div class="form-group">
							<label for="material_number" class="col-sm-1 control-label">Material</label>

							<div class="col-md-4">
								<select class="form-control select2" name="material_number" style="width: 100%;" data-placeholder="Choose a Material..." id="material_number" required>
									<option value=""></option>
									@foreach($materials as $material)
									<option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
									@endforeach
								</select>

							</div>
							<button type="submit" class="btn btn-danger col-sm-14"><i class="fa fa-print"></i>&nbsp;&nbsp;Print FLO</button>
						</div>
						<!-- /.box-body -->
					</div>
				</form>
				<!-- /.box -->
			</div>
			<!-- /.col -->
		</div>

		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">FLO <i class="fa fa-angle-right"></i> Fulfillment</h3>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="row">
						<div class="col-md-4">
							<br>
							<label>Scan FLO Number</label>
							<div class="input-group col-md-12">
								
								<div class="input-group-addon">
									<i class="glyphicon glyphicon-barcode"></i>
								</div>
								<input style="text-align: center" type="text" class="form-control" id="flo_number" name="flo_number" placeholder="Enter FLO Number" required>
							</div>
							<div class="input-group col-md-12">
								<hr id="line-flo" style="border: 1px solid #3498DB">
							</div>
							<div class="input-group col-md-12">
								<div class="input-group-addon" id="icon-material">
									<i class="glyphicon glyphicon-barcode"></i>
								</div>
								<input type="text" class="form-control" id="material" name="material" placeholder="Enter Material Number" required>
							</div>
							&nbsp;
							<div class="input-group col-md-12">
								<div class="input-group-addon" id="icon-serial">
									<i class="glyphicon glyphicon-barcode"></i>
								</div>
								<input type="text" class="form-control" id="serial" name="serial" placeholder="Enter Serial Number" required>
							</div>
							<br>
							<div class="input-group col-md-12">
								<center><button id="finish" class="btn btn-danger col-sm-14"><i class="fa fa-minus-circle"></i>&nbsp;&nbsp;Finish</button></center>
							</div>
						</div>
						<div class="col-md-8">
							<div class="form-group">
								<table id="flo_table" class="table table-bordered table-striped">
									<thead>
										<tr>
											<th>#</th>
											<th>Material</th>
											<th>Description</th>
											<th>Serial</th>
											<th>Del.</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
									{{-- @foreach($destinations as $destination) --}}
{{-- 										<tr>
											<td style="font-size: 14">999</td>
											<td style="font-size: 14">WWWWWWW</td>
											<td style="font-size: 14">WWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWW</td>
											<td style="font-size: 14">99999999999</td>
											<td>
												<button class="btn btn-danger btn-sm">
													<i class="glyphicon glyphicon-trash"></i>
													
												</button>
											</td>
										</tr> --}}
										{{-- @endforeach --}}
									</table>
								</div>
							</div>
						</div>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

	</section>
	<div class="modal modal-danger fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="titleModal">Error</h4>
				</div>
				<div class="modal-body" id="messageModal">
					{{-- Are you sure delete? --}}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	@stop
	@section('scripts')
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
	<script>

		$(function () {
			$('.select2').select2()
		});

		jQuery(document).ready(function() {
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			$("#material").hide();
			$("#serial").hide();
			$("#icon-material").hide();
			$("#icon-serial").hide();
			$("#line-flo").hide();
			$("#finish").hide();
			$("#flo_table").hide();
			$("#flo_number").val("");
			$("#material").val("");
			$("#serial").val("");

		// $("#flo_number").on("input", function() {
		// 	delay(function(){
		// 		if ($("#flo_number").val().length < 8) {
		// 			$("#flo_number").val("");
		// 		}
		// 	}, 20 );
		// });

		$("#finish").click(function(){
			var table = $('#flo_table').DataTable();

			table.destroy();

			$("#flo_number").prop('disabled', false);
			
			$("#material").hide();
			$("#serial").hide();
			$("#icon-material").hide();
			$("#icon-serial").hide();
			$("#line-flo").hide();
			$("#finish").hide();
			$("#flo_table").hide();
			$("#flo_number").val("");
			$("#material").val("");
			$("#serial").val("");
			$("#flo_number").focus();
		});


		$('#flo_number').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#flo_number").val().length > 0){
					scanFLO();
					return false;
				}
			}
		});


		$('#material').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#material").val().length > 0){
					scanMaterial();
					return false;
				}
			}
		});

		$('#serial').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 9) {
				if($("#serial").val().length > 0){
					scanSerial();
					return false;
				}
			}
		});

		// $('.delete').click(function(){
		// 	var id = $(this).attr('id');
		// 	var data = {
		// 		id: id,
		// 		_token: token
		// 	};
		// 	openErrorGritter('Error!', 'Disconnected');
		// 	if(confirm("Are you sure you want to delete this data?")){
		// 		$.get('{{ url("destroy/serial_number_sn") }}', data, function(result, status, xhr){
		// 			console.log(status);
		// 			console.log(result);
		// 			console.log(xhr);

		// 			if(xhr.status == 200){
		// 				if(result.status){
		// 					$('#flo_table').DataTable().ajax.reload();
		// 					$("#serial").prop('disabled', true);
		// 					$("#material").prop('disabled', false);
		// 					$("#serial").val("");
		// 					$("#material").val("");
		// 					$("#material").focus();
		// 					openSuccessGritter('Success!', result.message);
		// 				}
		// 				else{
		// 					openErrorGritter('Error!', result.message);
		// 					$("#flo_number").val("");
		// 				}
		// 			}
		// 			else{
		// 				openErrorGritter('Error!', 'Disconnected');
		// 				$("#flo_number").val("");
		// 			}
		// 		});
		// 	}
		// 	else{
		// 		return false;
		// 	}
		// });

	});

		function scanFLO() {


			var token = '{{ Session::token() }}';
			var flo_number = $("#flo_number").val();
			var data = {
				flo_number: flo_number,
				_token: token
			};
			$.post('{{ url("scan/flo_number_sn") }}', data, function(result, status, xhr){

				console.log(status);
				console.log(result);
				console.log(xhr);

				if(xhr.status == 200){
					if(result.status){
						$("#material").show();
						$("#serial").show();
						$("#icon-material").show();
						$("#icon-serial").show();
						$("#line-flo").show();
						$("#finish").show();
						$("#flo_table").show();
						$("#flo_number").prop('disabled', true);
						$("#serial").prop('disabled', true);
						$('#flo_table').DataTable( {
							'paging'      	: false,
							'lengthChange'	: false,
							'searching'   	: false,
							'ordering'    	: false,
							'info'       	: true,
							'autoWidth'		: false,
							"sPaginationType": "full_numbers",
							"bJQueryUI": true,
						"bAutoWidth": false, // Disable the auto width calculation 
						"infoCallback": function( settings, start, end, max, total, pre ) {
							return " Total "+ total +" pc(s)";
						},
						"processing": true,
						"serverSide": true,
						"ajax": {
							"type" : "post",
							"url" : "{{ url("index/scan/flo_number_sn") }}",
							"data": data
						},
						"columns": [
						{ "data": "id",
						render: function (data, type, row, meta) {
							return meta.row + meta.settings._iDisplayStart + 1;
						}, "sWidth": "2%" },
						{ "data": "material_number", "sWidth": "12%" },
						{ "data": "material_description", "sWidth": "65%" },
						{ "data": "serial_number", "sWidth": "14%" },
						{ "data": "action", "sWidth": "4%" }
						]

					});
						openSuccessGritter('Success!', result.message);
						$("#material").focus();
					}
					else{
						openErrorGritter('Error!', result.message);
						$("#flo_number").val("");
					}
				}
				else{
					openErrorGritter('Error!', 'Disconnected');
					$("#flo_number").val("");
				}
			});
		}

		function scanMaterial(){

			var token = '{{ Session::token() }}';
			var flo_number = $("#flo_number").val();
			var material_number = $("#material").val();
			var data = {
				flo_number: flo_number,
				material_number: material_number,
				_token: token
			};

			$.post('{{ url("scan/material_number_sn") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);

				if(xhr.status == 200){
					if(result.status){
						$("#serial").prop('disabled', false);
						$("#material").prop('disabled', true);
						$("#serial").focus();
						openSuccessGritter('Success!', result.message);
					}
					else{
						openErrorGritter('Error!', result.message);
						$("#material").val("");
					}
				}
				else{
					openErrorGritter('Error!', result.message);
					$("#material").val("");
				}

			});
		}

		function scanSerial(){

			var token = '{{ Session::token() }}';
			var flo_number = $("#flo_number").val();
			var serial_number = $("#serial").val();
			var data = {
				flo_number: flo_number,
				serial_number: serial_number,
				_token: token
			};
			$.post('{{ url("scan/serial_number_sn") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){
						$("#serial").prop('disabled', true);
						$('#flo_table').DataTable().ajax.reload();
						$("#material").val("");
						$("#serial").val("");
						$("#material").prop('disabled', false);
						openSuccessGritter('Success!', result.message);
						$("#material").focus();
					}
					else{
						openErrorGritter('Error!', result.message);
						$("#serial").val("");
					}

				}
				else{
					openErrorGritter('Error!', 'Disconnected');
					$("#serial").val("");
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
				time: '1000'
			});
		}

		function openSuccessGritter(title, message){
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-success',
				image: '{{ url("images/image-screen.png") }}',
				sticky: false,
				time: '1000'
			});
		}

		function deleteConfirmation(id){
			var token = '{{ Session::token() }}';
			var data = {
				id: id
			};
			if(confirm("Are you sure you want to delete this data?")){
				$.post('{{ url("destroy/serial_number_sn") }}', data, function(result, status, xhr){
					console.log(status);
					console.log(result);
					console.log(xhr);

					if(xhr.status == 200){
						if(result.status){
							$('#flo_table').DataTable().ajax.reload();
							$("#serial").prop('disabled', true);
							$("#material").prop('disabled', false);
							$("#serial").val("");
							$("#material").val("");
							$("#material").focus();
							openSuccessGritter('Success!', result.message);
						}
						else{
							openErrorGritter('Error!', result.message);
						}
					}
					else{
						openErrorGritter('Error!', 'Disconnected');
					}
				});
			}
			else{
				return false;
			}
		}

		var delay = (function(){
			var timer = 0;
			return function(callback, ms){
				clearTimeout (timer);
				timer = setTimeout(callback, ms);
			};
		})();

		
	</script>
	@stop