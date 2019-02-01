@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
@endsection
@section('header')
<section class="content-header">

	<h1>
		Final Line Outputs <span class="text-purple">ファイナルライン出力</span>
		<small>Container Departure <span class="text-purple">コンテナー出発</span></small>
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
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Containers Number & Attachment <span class="text-purple">コンテナ番号・付属品</span></span></h3>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<br>
							<table id="iv_table" class="table table-bordered table-striped table-hover" style="width: 100%;">
								<thead style="background-color: rgba(126,86,134,.7);">
									<tr>
										<th style="width: 5%">Container ID</th>
										<th style="width: 5%">Cont. Code</th>
										<th style="width: 7%">Destination</th>
										<th style="width: 5%">Ship. Date</th>
										<th style="width: 5%">Ship. Cond.</th>
										<th style="width: 20%">Cont. Number</th>
										<th style="width: 5%">Action</th>
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
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>		
	</div>
</section>

<div class="modal fade" id="attModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Input Container Number & Photos</h4>
				</div>
				<form id="form_container" method="post" action="upload" enctype="multipart/form-data">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
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
							<div class="col-md-4">
								<div class="input-group">
									<label for="exampleInputEmail1">Before <i class="fa fa-picture-o"></i></label>
									<input type="file" id="container_before" name="container_before[]" multiple="" accept=".jpg,.jpeg">
								</div>
							</div>
							<div class="col-md-4">
								<div class="input-group">
									<label for="exampleInputEmail1">Process <i class="fa fa-picture-o"></i></label>
									<input type="file" id="container_process" name="container_process[]" multiple="" accept=".jpg,.jpeg">
								</div>
							</div>
							<div class="col-md-4">
								<div class="input-group">
									<label for="exampleInputEmail1">After <i class="fa fa-picture-o"></i></label>
									<input type="file" id="container_after" name="container_after[]" multiple="" accept=".jpg,.jpeg">
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-12">
								<div class="input-group">
									<p class="help-block" style="font-size: 12">Allowed file type: .jpg .jpeg; max size: 500kb</p>
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="col-md-4" id="imagePreviewBefore"></div>
							<div class="col-md-4" id="imagePreviewProcess"></div>
							<div class="col-md-4" id="imagePreviewAfter"></div>					
						</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="container_id" id="container_id" value="">
						<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary">Confirm</button>
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
			fillIvTable()

			$('#form_container').on('submit', function(event){
				event.preventDefault();
				var formdata = new FormData(this);

				$.ajax({
					url:"{{url('update/flo_container')}}",
					method:'post',
					data:formdata,
					dataType:"json",
					processData: false,
					contentType: false,
					cache: false,
					success:function(data){
						$('#container_after').val('');
						$('#container_process').val('');
						$('#container_before').val('');
						$('#attModal').modal('hide');
						$('#iv_table').DataTable().ajax.reload();
						openSuccessGritter('Success', data.message);
					}
				});


			});
		});

		function updateConfirmation(id){
			var data = {
				id:id,
			}
			$.get('{{ url("fetch/flo_container") }}', data, function(result, status, xhr){
				console.log(status);
				console.log(result);
				console.log(xhr);

				if(xhr.status == 200){
					if(result.status){
						$('#container_id').val(result.container_id);
						$('#container_number').val(result.container_number);
						$('#imagePreviewBefore').html("");
						$('#imagePreviewProcess').html("");
						$('#imagePreviewAfter').html("");
						$.each(result.file_before, function( index, value ) {
							if(value.length > 0){
								var conf = value.split('/');
								$('#imagePreviewBefore').append('<div class="col-md-4"><img height="90" width="110" src="'+value+'"></div><div class="col-md-1"><button type="button" href="javascript:void(0)" id="'+conf[8]+'" class="btn btn-danger btn-xs" onClick="deleteConfirmation(id)">x</button></div>'
									);
							}
						});
						$.each(result.file_process, function( index, value ) {
							if(value.length > 0){
								var conf = value.split('/');
								$('#imagePreviewProcess').append('<div class="col-md-4"><img height="90" width="110" src="'+value+'"></div><div class="col-md-1"><button type="button" href="javascript:void(0)" id="'+conf[8]+'" class="btn btn-danger btn-xs" onClick="deleteConfirmation(id)">x</button></div>'
									);
							}
						});
						$.each(result.file_after, function( index, value ) {
							if(value.length > 0){
								var conf = value.split('/');
								$('#imagePreviewAfter').append('<div class="col-md-4"><img height="90" width="110" src="'+value+'"></div><div class="col-md-1"><button type="button" href="javascript:void(0)" id="'+conf[8]+'" class="btn btn-danger btn-xs" onClick="deleteConfirmation(id)">x</button></div>'
									);
							}
						});
						$('#attModal').modal('show');
					}
				}
				else{
					openErrorGritter('Error!', 'Disconnected from server');
					audio_error.play();
				}
			});
		}

		function fillIvTable(){
			$('#iv_table tfoot th').each( function () {
				var title = $(this).text();
				$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" />' );
			});
			var table = $('#iv_table').DataTable( {
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

			$('#iv_table tfoot tr').appendTo('#iv_table thead');
		}

		function deleteConfirmation(id){
			if(confirm("Are you sure you want to delete this attachment?")){
				var data = {
					id:id,
				}
				$.post('{{ url("destroy/flo_attachment") }}', data, function(result, status, xhr){
					console.log(status);
					console.log(result);
					console.log(xhr);
					if(xhr.status == 200){
						if(result.status){
							$('#container_after').val('');
							$('#container_process').val('');
							$('#container_before').val('');
							$('#attModal').modal('hide');
							$('#iv_table').DataTable().ajax.reload();
							openSuccessGritter('Success!', result.message);
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

		function openErrorGritter(title, message) {
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-danger',
				image: '{{ url("images/image-stop.png") }}',
				sticky: false,
				time: '4000'
			});
		}

		function openSuccessGritter(title, message){
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-success',
				image: '{{ url("images/image-screen.png") }}',
				sticky: false,
				time: '4000'
			});
		}

		function openInfoGritter(title, message){
			jQuery.gritter.add({
				title: title,
				text: message,
				class_name: 'growl-info',
				image: '{{ url("images/image-unregistered.png") }}',
				sticky: false,
				time: '4000'
			});
		}
	</script>
	@endsection