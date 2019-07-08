@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Saxophone Surface Treatment<span class="text-purple"> 表面処理</span>
		<small>WIP Control <span class="text-purple"> 仕掛品管理</span></small>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process Barrel <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/process_middle_barrel", "barrel-sx-lcq") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Lacquering</a>
			<a href="{{ url("index/process_middle_barrel", "barrel-sx-plt") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Plating</a>
			<a href="{{ url("index/process_middle_barrel", "barrel-sx-flanel") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Flanel</a>
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process Lacquering <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/process_middle_kensa", "incoming-lcq") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Incoming Check</a>
			<a href="{{ url("index/process_middle_kensa", "incoming-lcq2") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Incoming Check (After Treatment)</a>
			{{-- <a href="{{ url("index/process_middle_kensa", "incoming-lcq-body") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">IC LCQ Body</a> --}}
			<a href="{{ url("index/process_middle_kensa", "kensa-lcq") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Kensa</a>
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process Plating <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/process_middle_kensa", "incoming-plt-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Incoming Check</a>
			<a href="{{ url("index/process_middle_kensa", "kensa-plt-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Kensa</a>
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Repair <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/process_middle_return", "buffing") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Repair/Return to Buffing</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Display Barrel <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/middle/barrel_board/barrel-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Barrel Board</a>
			<a href="{{ url("index/middle/barrel_machine") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Machine Activity</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Report Barrel<i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/process_middle_barrel", "barrel-sx-lcq") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Set & Reset</a>
			<a href="{{ url("index/process_middle_barrel", "barrel-sx-lcq") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Machine Log</a>

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