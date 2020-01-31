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
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process PHS <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/welding/kensa", "phs-visual-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">PHS Kensa Visual</a>
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process HSA <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/welding/kensa", "hsa-visual-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">HSA Kensa Visual</a>
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process HTS <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/process_stamp_sx_1") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Stamp <b><i>IoT</i></b></a>

			<!-- <a href="{{ url("index/process_stamp_sx_2") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Assy - Print</a>
			<a href="{{ url("index/process_stamp_sx_check") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Assy - Print Check Sheet</a>
			<a href="{{ url("index/process_stamp_sx_3") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Assy - Print Label</a> -->
			
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> NG <i class="fa fa-angle-double-down"></i></span>
			<!-- <a href="{{ url("/index/repairSx") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Return/Repair</a>
				<a href="{{ url("index/ngSx") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Ng</a> -->
			</div>
			<div class="col-xs-4" style="text-align: center; color: purple;">
				<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
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