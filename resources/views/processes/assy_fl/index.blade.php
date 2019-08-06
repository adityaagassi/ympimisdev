@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Flute Subassy-Assembly WIP<span class="text-purple"> FL仮組・組立の仕掛品</span>
		{{-- <small>Flute <span class="text-purple"> ??? </span></small> --}}
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/process_assy_fl_1") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Stamp <b><i>IoT</i></b></a>
			<a href="{{ url("index/process_assy_fl_0") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Stamp-Kariawase</a>
			<a href="{{ url("index/process_assy_fl_2") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Tanpoawase</a>
			<a href="{{ url("index/process_assy_fl_3") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Seasoning-Kanggou</a>
			<a href="{{ url("index/process_assy_fl_4") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Chousei</a>
			<a href="{{ url("index/label_fl") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Assy FL - Print Label</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> NG <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("/index/repairFl") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Return/Repair</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("/stamp/log") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Log Process</a>
			<a href="{{ url("/stamp/resumes") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Production Result</a>
			<a href="{{ url("/index/displayWipFl") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Chart Inventory</a>
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