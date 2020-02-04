@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Saxophone Welding WIP<span class="text-purple"> サックス仮組　～　仕掛品組み立て</span>
		{{-- <small>Flute <span class="text-purple"> ??? </span></small> --}}
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process Rozuke <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/welding/kensa", "phs-visual-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">PHS Kensa Visual</a>
			<a href="{{ url("index/welding/kensa", "hsa-visual-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">HSA Kensa Visual</a>
			<a href="" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Kensa Dimensi</a>
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process Handatsuke <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/process_stamp_sx_1") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Stamp <b><i>IoT</i></b></a>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Display Rozuke <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/welding/display_production_result?tanggal=&location=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Production Result</a>
			<a href="{{ url("index/welding/ng_rate?tanggal=&location=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">NG Rate</a>
			<a href="" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">OP NG Rate</a>
			<a href="" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">OP Efficiency</a>
			<a href="{{ url("index/middle/request/display/043?filter=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Middle Process Material Request</a>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: purple;"><i class="fa fa-angle-double-down"></i> Report Rozuke<i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/welding/report_ng") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Not Good Record</a>
			<a href="{{ url("stamp/resumes_sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Production Result</a>
			<a href="{{ url("index/welding/report_hourly") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Hourly Report</a>
			<a href="" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Resume</a>
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