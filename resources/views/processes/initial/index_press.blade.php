@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Press Material Process<span class="text-purple"> ??</span>
		<small>WIP Control <span class="text-purple"> 仕掛品管理</span></small>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			{{-- <span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Stock Taking <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/stocktaking/silver", "fl_mpro") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Silver</a> --}}
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Press Machine <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/press/create") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Press Machine</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/press/report") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Press Machine Report</a>
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