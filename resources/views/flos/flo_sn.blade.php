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
		<li><button href="javascript:void(0)" class="btn btn-info btn-sm" data-toggle="modal" data-target="#reprintModal">
			<i class="fa fa-print"></i>&nbsp;&nbsp;Reprint FLO
		</button></li>
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
					<h3 class="box-title">Fulfillment</h3>
				</div>
				"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
			</div>
		</div>

		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Settlement</h3>
				</div>
				<!-- /.box-header -->
				"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
				<!-- /.box-body -->
			</div>
			<!-- /.box -->
		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->
	<div class="modal modal-default fade" id="reprintModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="titleModal">Reprint FLO</h4>
				</div>
				<form class="form-horizontal" role="form" method="post" action="{{url('reprint/flo')}}">
					<input type="hidden" value="{{csrf_token()}}" name="_token" />
					<div class="modal-body" id="messageModal">
						<label>FLO Number</label>
						<select class="form-control select2" name="flo_number_reprint" style="width: 100%;" data-placeholder="Choose a FLO..." id="flo_number_reprint" required>
							<option value=""></option>
							@foreach($flos as $flo)
							<option value="{{ $flo->flo_number }}">{{ $flo->flo_number }} || {{ $flo->shipmentschedule->material_number }} || {{ $flo->shipmentschedule->material->material_description }}</option>
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

</section>


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

		$("#flo_number_reprint").val("").change();

		// var delay = (function(){
		// 	var timer = 0;
		// 	return function(callback, ms){
		// 		clearTimeout (timer);
		// 		timer = setTimeout(callback, ms);
		// 	};
		// })();

		// $("#flo_number").on("input", function() {
		// 	delay(function(){
		// 		if ($("#flo_number").val().length < 8) {
		// 			$("#flo_number").val("");
		// 		}
		// 	}, 20 );
		// });

	});



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

</script>
@stop