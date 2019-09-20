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
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process Buffing <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/middle/buffing_work_order", "bff-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Saxophone Work Order</a>
			<a href="{{ url("index/process_buffing_kensa", "bff-kensa") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Kensa</a>
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process Barrel <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/process_middle_barrel", "barrel-sx-lcq") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Lacquering</a>
			<a href="{{ url("index/process_middle_barrel", "barrel-sx-plt") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Plating</a>
			<a href="{{ url("index/process_middle_barrel", "barrel-sx-flanel") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Flanel</a>
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process Lacquering <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/process_middle_kensa", "lcq-incoming") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Incoming Check</a>
			<a href="{{ url("index/process_middle_kensa", "lcq-incoming2") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Incoming Check (After Treatment)</a>
			{{-- <a href="{{ url("index/process_middle_kensa", "incoming-lcq-body") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">IC LCQ Body</a> --}}
			<a href="{{ url("index/process_middle_kensa", "lcq-kensa") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Kensa</a>
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process Plating <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/process_middle_kensa", "plt-incoming-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Incoming Check</a>
			<a href="{{ url("index/process_middle_kensa", "plt-kensa-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Kensa</a>
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Repair <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/process_middle_return", "buffing") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Repair/Return to Buffing</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Display Buffing <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/middle/buffing_board/buffing-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Buffing Board</a>
			<a href="{{ url("index/middle/buffing_performance/op_ng") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Operators Performance</a>
			<a href="{{ url("index/middle/buffing_ng_rate") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Daily NG Rate</a>
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Display Barrel <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/middle/barrel_board/barrel-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Barrel Board</a>
			<a href="{{ url("index/middle/barrel_machine") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Machine Activity</a>
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Display Lacquering <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/middle/display_production_result?tanggal=&location=") }}" class="btn btn-default btn-block" target="blank" style="font-size: 24px; border-color: red;">Production Result</a>
			<a href="{{ url("index/middle/display_picking?tanggal=&surface=&model=&key=") }}" class="btn btn-default btn-block" target="blank" style="font-size: 24px; border-color: red;">Picking Schedule</a>
			<a href="{{ url("index/middle/display_monitoring?location=") }}" class="btn btn-default btn-block" target="blank" style="font-size: 24px; border-color: red;">Monitoring</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/middle/report_ng") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Not Good</a>
			<a href="{{ url("index/middle/report_production_result") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Production Result</a>
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Report Buffing <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/middle/report_buffing_ng") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Resume</a>
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Report Barrel<i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/report_middle", "slip-fulfillment") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">ID Slip Fulfillment</a>
			<a href="{{ url("index/middle/barrel_log") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Barrel Log</a>
			<a href="{{ url("index/middle/stock_monitoring") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Stock Monitoring</a>
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Report Lacquering <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/middle/report_lcq_ng?bulan=&fy=") }}" class="btn btn-default btn-block" target="blank" style="font-size: 24px; border-color: purple;">Resume</a>
			<a href="{{ url("index/middle/report_hourly_lcq") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Hourly Report</a>

			
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