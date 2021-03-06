@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Saxophone Subassy-Assembly WIP<span class="text-purple"> サックス仮組　～　仕掛品組み立て</span>
		{{-- <small>Flute <span class="text-purple"> ??? </span></small> --}}
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Stock Taking <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/stocktaking/daily", "sx_assembly") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Body</a>
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> NG <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/process_assembly_kensa", "subassy-incoming-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Incoming Check Key</a>
			<a href="{{ url("/index/repairSx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Return/Repair</a>
			<a href="{{ url("index/ngSx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Ng</a>
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<!-- <a href="{{ url("index/process_stamp_sx_1") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Stamp <b><i>IoT</i></b></a> -->
			<a href="{{ url("index/process_stamp_sx_2") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Assy - Print</a>
			<a href="{{ url("index/process_stamp_sx_check") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Assy - Print Check Sheet</a>
			<a href="{{ url("index/process_stamp_sx_3") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Assy - Print Label</a>
			<a href="{{ url("index/fetchResultSaxnew") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Picking Schedule</a>
			{{-- 
			<a href="{{ url("index/process_assy_fl_2") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Tanpoawase</a>
			<a href="{{ url("index/process_assy_fl_3") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Seasoning-Kanggou</a>
			<a href="{{ url("index/process_assy_fl_4") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Chousei</a> --}}
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>

			<a href="{{ url("index/middle/buffing_ic_atokotei_subassy") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Incoming Check Subassy</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<!-- <a href="{{ url("/stamp/log") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Log Process</a> -->
			<a href="{{ url("stamp/resumes_sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Production Result</a>
			<!-- <a href="{{ url("/index/displayWipFl") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Chart Inventory</a>  -->

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