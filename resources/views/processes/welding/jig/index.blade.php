@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Welding Jig Handling<span class="text-purple"> ??</span>
		{{-- <small>Flute <span class="text-purple"> ??? </span></small> --}}
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/welding/jig_data") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">JIG Data</a>
			<a href="{{ url("") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">JIG BOM </a>
			<a href="{{ url("") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Schedule Check</a>
			<a href="{{ url("") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Point Check Kensa</a>
			<a href="{{ url("") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Point Check Repair</a>
			<a href="{{ url("") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Spare Part Data</a>
			<a href="{{ url("") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Key Data</a>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/welding/kensa_jig") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Kensa Jig</a>
			<a href="{{ url("") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Repair Jig</a>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: purple;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Schedule Monitoring</a>
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

	});
</script>
@endsection