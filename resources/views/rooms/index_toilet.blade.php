@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Toilet Availability Information<span class="text-purple"> ??</span>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Location <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/room/toilet", "buffing") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Buffing</a>
			<a href="{{ url("index/room/toilet", "office") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Office</a>
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