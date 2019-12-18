@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $page }}<span class="text-purple"> ???</span>
		{{-- <small>Flute <span class="text-purple"> ??? </span></small> --}}
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<!-- <a href="{{ url("index/process_stamp_sx_1") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Stamp <b><i>IoT</i></b></a> -->
			<a href="{{ url("index/press/create/Saxophone") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Saxophone</a>
			<a href="{{ url("index/press/create/Flute") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Flute</a>
			<a href="{{ url("index/press/create/Clarinet") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Clarinet</a>
			{{-- <a href="{{ url("index/press/vn") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Venova</a> --}}
			{{-- <a href="{{ url("index/process_stamp_sx_check") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Assy - Print Check Sheet</a>
			<a href="{{ url("index/process_stamp_sx_3") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Assy - Print Label</a>
			<a href="{{ url("index/fetchResultSaxnew") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Picking Schedule</a> --}}
			{{-- 
			<a href="{{ url("index/process_assy_fl_2") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Tanpoawase</a>
			<a href="{{ url("index/process_assy_fl_3") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Seasoning-Kanggou</a>
			<a href="{{ url("index/process_assy_fl_4") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Chousei</a> --}}
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
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
	jQuery(document).ready(function() {
		
	});
</script>
@endsection