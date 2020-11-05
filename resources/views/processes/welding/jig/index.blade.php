@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Welding Jig Handling<small class="text-purple">溶接冶具のハンドリング</small>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: black;"><i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/welding/jig_data') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: black;">Jig Data</a>
			<a href="{{ url('index/welding/jig_bom') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: black;">Jig BOM</a>
			<a href="{{ url('index/welding/jig_schedule') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: black;">Schedule Check</a>
			<a href="{{ url('index/welding/kensa_point') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: black;">Point Check Kensa</a>
			<a href="{{ url('index/welding/jig_part') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: black;">Spare Part Data</a>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/welding/kensa_jig') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Kensa Jig</a>
			<a href="{{ url('index/welding/repair_jig') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Repair Jig</a>
			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/welding/monitoring_jig') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Kensa Jig Monitoring</a>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: purple;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/welding/kensa_jig_report') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Kensa Jig Report</a>
			<a href="{{ url('index/welding/repair_jig_report') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Repair Jig Report</a>
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