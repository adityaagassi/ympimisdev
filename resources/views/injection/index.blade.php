@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $page }}<span class="text-purple"> {{ $jpn }}</span>
		{{-- <small>Flute <span class="text-purple"> ??? </span></small> --}}
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 3vw; color: red;"><i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/Op") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Master Part</a>
			
			
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/in") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> Stock - In</a>

			<a href="{{ url("index/out") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> Stock - Out</a>

			<a href="{{ url("index/Schedule") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Make Schedule</a>

			<a href="{{ url("index/opmesin") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Machine Operationing</a>

			<a href="{{ url("index/machine_operational") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Like Asprova View</a>
			
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			 <a href="{{ url("index/dailyStock") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Daily Stock After Injection</a>

		
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