@extends('layouts.master')
@section('stylesheets')
<style>

</style>
@endsection
@section('header')
<section class="content-header">

	<h1>
		Final Line Outputs <span class="text-purple">ファイナルライン出力</span>
		<small>After Stuffing & Loading <span class="text-purple">---</span></small>
	</h1>
	<ol class="breadcrumb">

	</ol>
</section>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<input type="hidden" value="{{csrf_token()}}" name="_token" />
<section class='content'>
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Containers Number & Attachment <span class="text-purple">--</span></span></h3>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<br>
							<table id="iv_table" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th style="font-size: 14">Container ID</th>
										<th style="font-size: 14">Cont. Code</th>
										<th style="font-size: 14">Destination</th>
										<th style="font-size: 14">Ship. Date</th>
										<th style="font-size: 14">Ship. Cond.</th>
										<th style="font-size: 14">Cont. Number</th>
										<th style="font-size: 14">Action</th>
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

<div class="modal fade" id="attModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Input Container Number & Photos</h4>
				</div>
				<form id="attForm" method="post" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-6 col-md-offset-3">

								<div class="col-md-12">
									<div class="input-group">
										<div class="input-group-addon" id="icon-serial" style="font-weight: bold">
											<i class="fa fa-truck"></i>
										</div>
										<input type="text" class="form-control" id="container_number" name="container_number" placeholder="Container Number..." required>
									</div>
									<br>
								</div>
							</div>
						</div>

						<div class="col-md-12">
							<div class="col-md-6 col-md-offset-3">
								<div class="col-md-12">
									<div class="input-group">
										<label for="exampleInputEmail1">Select pictures <i class="fa fa-picture-o"></i></label>
										<input type="file" id="att_photo" name="att_photo">
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<input type="text" name="container_id" id="container_id" value="">
						<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Confirm</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	@endsection

	@section('scripts')
	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		jQuery(document).ready(function() {
			fillIvTable()

			$('#attForm').on('submit', function(event){
				event.preventDefault();
				var form_data = new FormData(this);
				$.ajax({
					url:"{{url('update/container_att')}}",
					method:'post',
					data:form_data,
					dataType:"json",
					processData: false,
					contentType: false,
					cache: false,
					success:function(data){
							
					}
				});
			});
		});

		function attConfirmation(id) {
			$.ajax({
				url:"{{url('fetch/container_att')}}",
				method:'get',
				data:{
					id: id,
				},
				dataType:'json',
				success:function(data){
					$('#container_id').val(id);
					$('#container_number').val(data.container_number);
					$('#attModal').modal('show');
				}
			});
		}

		function fillIvTable(){
			$('#iv_table').DataTable( {
				'paging'      	: true,
				'lengthChange'	: true,
				'searching'   	: true,
				'ordering'    	: true,
				'order'       	: [],
				'info'       	: true,
				'autoWidth'		: true,
				"sPaginationType": "full_numbers",
				"bJQueryUI": true,
				"bAutoWidth": false,
				"processing": true,
				"serverSide": true,
				"ajax": {
					"type" : "post",
					"url" : "{{ url("index/flo_container") }}",
				},

				"columns": [
				{ "data": "container_id" },
				{ "data": "container_code" },
				{ "data": "destination_shortname" },
				{ "data": "shipment_date" },
				{ "data": "shipment_condition_name" },
				{ "data": "container_number" },
				{ "data": "action" }
				]
			});
		}
	</script>
	@endsection