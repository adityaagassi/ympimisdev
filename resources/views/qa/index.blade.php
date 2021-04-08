@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $page }}<span class="text-purple"> 成形</span>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			
			<a href="{{ url('index/qa/incoming_check','wi1') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> WI 1 - Incoming Check</a>
			<a href="{{ url('index/qa/incoming_check','wi2') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> WI 2 - Incoming Check</a>
			<a href="{{ url('index/qa/incoming_check','ei') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> EI - Incoming Check</a>
			<a href="{{ url('index/qa/incoming_check','sx') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> Sax Body - Incoming Check</a>
			<a href="{{ url('index/qa/incoming_check','cs') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> Case - Incoming Check</a>
			<a href="{{ url('index/qa/incoming_check','ps') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;"> Pipe Silver - Incoming Check</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>

			<a href='{{ url("index/qa/display/incoming/lot_status") }}' class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red;">QA Incoming Check Lot Out Monitoring</a>
			<a href='{{ url("index/qa/display/incoming/material_defect") }}' class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red;">QA Incoming Check Material Defect</a>
			<a href='{{ url("index/qa/display/incoming/ng_rate") }}' class="btn btn-default btn-block" style="font-size: 1.5vw; border-color: red;">Daily NG Rate Incoming Check QA</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/qa/report/incoming') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Incoming Check QA Report</a>
			<a href="{{ url('index/qa/report/incoming/lot_out') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Lot Out Report</a>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
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