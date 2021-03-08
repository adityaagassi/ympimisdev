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

			<span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Molding <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/reed/molding_verification') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Setup Molding Verification</a>
			

			<span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Injection Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/reed/injection_verification') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Injection Verification</a>
			<a href="{{ url('index/reed/injection_delivery') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">After Injection Delivery</a>

			{{-- <span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Laser Marking <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/reed/molding_verification') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Laser Marking Verification</a> --}}

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