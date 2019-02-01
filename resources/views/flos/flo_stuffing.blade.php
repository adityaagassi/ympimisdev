@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style>
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
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Final Line Outputs <span class="text-purple">ファイナルライン出力</span>
		<small>Containers Stuffing <span class="text-purple">コンテナ積み込</span></small>
	</h1>
	<ol class="breadcrumb">
		<li>
			<button href="javascript:void(0)" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reprintModal">
				<i class="fa fa-print"></i>&nbsp;&nbsp;Reprint FLO
			</button>
		</li>
	</ol>
</section>
@stop

@section('content')
<section class= "content">
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
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Finished Goods Export <span class="text-purple">完成品出荷</span></h3>
				</div>
				<div class="box-body">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-6 col-md-offset-3">
								<div class="col-md-10">
									<div class="input-group">
										<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
											<i class="glyphicon glyphicon-list-alt"></i>
										</div>
										<input type="text" class="form-control" id="invoice_number" name="invoice_number" placeholder="Invoice Number" required>
									</div>
									<br>
								</div>
							</div>
							<div class="col-md-6 col-md-offset-3">
								<div class="col-md-10">
									<div class="input-group">
										<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
											<i class="fa fa-bus"></i>
										</div>
										<select class="form-control select2" id="container_id" name="container_id" style="width: 100%;" data-placeholder="Choose a Container ID" required>
											<option></option>
											@foreach($container_schedules as $container_schedule)
											<option value="{{ $container_schedule->container_id }}">
												{{ $container_schedule->container_id. ' | ' .$container_schedule->container_code. ' | ' .date('d-M-Y', strtotime($container_schedule->shipment_date)). ' | ' .$container_schedule->weeklycalendar->week_name. ' | ' .$container_schedule->destination->destination_shortname }}
												{{-- {{ $container_schedule->container_id }} --}}
											</option>
											@endforeach
										</select>
									</div>
									<br>
								</div>
								<div class="col-md-2">
									<input id="toggle_lock" data-toggle="toggle" data-on="Lock" data-off="Open" type="checkbox">
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="input-group col-md-8 col-md-offset-2">
								<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
									<i class="glyphicon glyphicon-barcode"></i>
								</div>
								<input type="text" style="text-align: center; font-size: 22" class="form-control" id="flo_number_settlement" name="flo_number_settlement" placeholder="Scan FLO Here..." required>
								<div class="input-group-addon" id="icon-serial">
									<i class="glyphicon glyphicon-ok"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<br>
							<table id="flo_table" class="table table-bordered table-striped">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 5%">FLO</th>
										<th style="width: 10%">Dest.</th>
										<th style="width: 5%">Ship. Date</th>
										<th style="width: 5%">By</th>
										<th style="width: 5%">Material</th>
										<th style="width: 30%">Description</th>
										<th style="width: 5%">Qty</th>
										<th style="width: 5%">I/V</th>
										<th style="width: 5%">Cont. ID</th>
										<th style="width: 15%">Stuff. Date</th>
										<th style="width: 5%">Cancel</th>
									</tr>
								</thead>
								<tbody>
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
		</div>
	</section>
	@stop

	@section('scripts')
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
	<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
	<script>
		jQuery(document).ready(function() {

			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			fillFloTableSettlement();

			refresh();
			
			$('#toggle_lock').change(function(){				
				if(this.checked){
					$('#invoice_number').prop('disabled', true);
					$('#container_id').prop('disabled', true);
					$('#flo_number_settlement').prop('disabled', false);
					$('#flo_number_settlement').focus();
				}
				else{
					$('#invoice_number').prop('disabled', false);
					$('#invoice_number').val('');
					$('#container_id').prop('disabled', false);
					$("#container_id").val('').change();
					$('#flo_number_settlement').prop('disabled', true);
					$('#invoice_number').focus();
				}
			});

			$(function () {
				$('.select2').select2();
			});

			$('#flo_number_settlement').keydown(function(event) {
				if (event.keyCode == 13 || event.keyCode == 9) {
					if($("#invoice_number").val().length == 6 && $("#container_id").val() != ""){
						if($("#flo_number_settlement").val().length > 7){
							scanFloNumber();
							return false;
						}
						else{
							openErrorGritter('Error!', 'FLO number invalid.');
							audio_error.play();
							$("#flo_number_settlement").val("");
						}
					}
					else{
						openErrorGritter('Error!', 'Invoice number invalid or container ID required.');
						audio_error.play();
						refresh();
					}
				}
			});
		});

		var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

		function scanFloNumber(){
			var flo_number = $("#flo_number_settlement").val();
			var invoice_number = $("#invoice_number").val();
			var container_id = $("#container_id").val();
			var data = {
				flo_number : flo_number,
				status : '3',
				invoice_number : invoice_number,
				container_id : container_id,
			}
			$.post('{{ url("scan/flo_settlement") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);
				if(xhr.status == 200){
					if(result.status){
						openSuccessGritter('Success!', result.message);
						$('#flo_table').DataTable().ajax.reload();
						$('#flo_number_settlement').val('');
					}
					else{
						openErrorGritter('Error!', result.message);
						$('#flo_number_settlement').val('');
						audio_error.play();
					}
				}
				else{
					openErrorGritter('Error', 'Disconnected from server');
					audio_error.play();
					$('#flo_number_settlement').val('');
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

		function refresh(){
			$('#invoice_number').val('');
			$('#container_id').val('').change();
			$('#toggle_lock').prop('checked', false).change();
			$('#flo_number_settlement').prop('disabled', true);
			$('#flo_number_settlement').val('');
			$('#invoice_number').focus();			
		}

		function cancelConfirmation(id){
			var flo_number = $("#flo_number_settlement").val(); 
			var data = {
				id: id,
				flo_number : flo_number,
				status : '3',
			};
			if(confirm("Are you sure you want to cancel this settlement?")){
				$.post('{{ url("cancel/flo_settlement") }}', data, function(result, status, xhr){
					if(xhr.status == 200){
						if(result.status){
							openSuccessGritter('Success!', result.message);
							$('#flo_table').DataTable().ajax.reload();
							$("#flo_number_settlement").val("");
							$("#flo_number_settlement").focus();          
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

		function fillFloTableSettlement(){
			var data = {
				status : '3'
			}
			$('#flo_table tfoot th').each( function () {
				var title = $(this).text();
				$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
			});
			var table = $('#flo_table').DataTable( {
				'paging'      	: true,
				'lengthChange'	: true,
				'searching'   	: true,
				'ordering'    	: true,
				'order'       : [],
				'info'       	: true,
				'autoWidth'		: true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
				"serverSide": true,
				"ajax": {
					"type" : "post",
					"url" : "{{ url("index/flo") }}",
					"data" : data,
				},
				"columns": [
				{ "data": "flo_number" },
				{ "data": "destination_shortname" },
				{ "data": "st_date" },
				{ "data": "shipment_condition_name" },
				{ "data": "material_number" },
				{ "data": "material_description" },
				{ "data": "actual" },
				{ "data": "invoice_number" },
				{ "data": "container_id" },
				{ "data": "updated_at" },
				{ "data": "action" }
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
				});
			});

			$('#flo_table tfoot tr').appendTo('#flo_table thead');
		}
	</script>
	@stop