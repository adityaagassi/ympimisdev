@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}<span class="text-purple"> {{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">

		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Laser Marking <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/reed/laser_verification') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Laser Marking Verification</a>


			<span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Annealing <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/reed/annealing_verification') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Annealing Verification</a>

			<span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Packing <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/reed/packing_order') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Create Packing Order</a>
			<a href="{{ url('index/reed/picking_verification') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Picking Verification</a>

			<a href="{{ url('index/reed/case_paper_verification') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Case & Suport Paper Verification</a>

			<a href="{{ url('index/reed/packing_verification') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Packing Verification</a>



		</div>






		<div class="col-xs-4" style="text-align: center; color: red;">
			{{-- <span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span> --}}
			{{-- <a href="" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Maintenance Molding Monitoring</a> --}}
		</div>






		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Verification History</a>
		</div>
	</div>

	
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
	});

	
</script>
@endsection