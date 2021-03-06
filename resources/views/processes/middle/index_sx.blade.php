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
	@foreach(Auth::user()->role->permissions as $perm)
	@php
	$navs[] = $perm->navigation_code;
	@endphp
	@endforeach


	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			@if(in_array('A9', $navs))
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Master Buffing <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("/index/middle/buffing_operator", "bff-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Buffing Operator</a>
			<a href="{{ url("/index/middle/buffing_target", "bff") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Buffing Target</a>
			@endif

			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process Buffing <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/middle/request/043?filter=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Request Saxophone</a>
			<a href="{{ url("index/middle/buffing_work_order", "bff-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Saxophone Work Order</a>
			<a href="{{ url("index/process_buffing_kensa", "bff-kensa-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Kensa</a>
			@if(in_array('A9', $navs))
			<a href="{{ url("/index/middle/buffing_canceled") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Buffing Cancel</a>
			@endif
			

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
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/middle/op_analysis?dateFrom=&dateTo=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">OP Analysis</a>
			<a href="{{ url("index/middle/display_monitoring?location=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Kanban WIP Monitoring</a>
			<a href="{{ url("index/middle/display_kensa_time?tanggal=&location=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Operator Kensa ΣTime</a>
			<a href="{{ url("index/middle/display_production_result?tanggal=&location=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Production Result</a>
			<a href="{{ url("index/middle/request/display/043?filter=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Material Request Soldering</a>
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Display Buffing <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/middle/buffing_board/buffing-sx?page=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Buffing Board</a>
			{{-- <a href="{{ url("index/middle/buffing_daily_ng_rate") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Daily NG Rate</a> --}}
			<a href="{{ url("index/middle/buffing_ng") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">NG Rate</a>
			<a href="{{ url("index/middle/buffing_op_ranking?bulan=&target=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Resume NG Rate & Productivity</a>
			{{-- <a href="{{ url("index/middle/buffing_trend_op_eff") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Daily Operator Trends</a> --}}
			<a href="{{ url("index/middle/buffing_op_ng?tanggal=&group=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">NG Rate by Operator</a>
			<a href="{{ url("index/middle/buffing_op_eff?tanggal=&group=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Operator Overall Efficiency</a>
			<a href="{{ url("index/middle/buffing_resume_konseling") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Resume Operator Counseling</a>
			<a href="{{ url("index/middle/buffing_group_achievement") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Group Achievement</a>
			<a href="{{ url("index/middle/buffing_group_balance") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Group Work Balance</a>
			<a href="{{ url("index/middle/buffing_operator_assesment") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Operator Evaluation</a>
			{{-- <a href="{{ url("index/middle/buffing_daily_op_ng_rate") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Daily NG Rate by Operator</a> --}}
			{{-- <a href="{{ url("index/middle/muzusumashi") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Mizusumashi Monitoring</a> --}}
			<a href="{{ url("index/middle/buffing_ic_atokotei") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Incoming Check Lacquering</a>
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Display Barrel <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/middle/barrel_board/barrel-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Barrel Board</a>
			<a href="{{ url("index/middle/barrel_machine") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Machine Activity</a>
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Display Lacquering <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/middle/buffing_ic_atokotei_subassy") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Incoming Check Subassy</a>

		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/middle/report_ng") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Not Good</a>
			<a href="{{ url("index/middle/report_production_result") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Production Result</a>
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Report Buffing <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/middle/report_buffing_ng?bulan=&fy=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Resume</a>
			<a href="{{ url("index/middle/report_buffing_operator_time") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Operator Time</a>
			<a href="{{ url("index/middle/report_buffing_traing_ng_operator") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Training NG Operator</a>
			<a href="{{ url("index/middle/report_buffing_traing_eff_operator") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Training Efficiency Operator</a>
			<a href="{{ url("index/middle/report_buffing_canceled_log") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Buffing Canceled Log</a>
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Report Barrel<i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/report_middle", "slip-fulfillment") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">ID Slip Fulfillment</a>
			<a href="{{ url("index/middle/barrel_log") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Barrel Log</a>
			<a href="{{ url("index/middle/stock_monitoring") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Stock Monitoring</a>
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Report Lacquering <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/middle/report_lcq_ng?bulan=&fy=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Resume</a>
			<a href="{{ url("index/middle/report_hourly_lcq") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Hourly Report</a>
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Report Plating <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/middle/report_plt_ng?bulan=&fy=", "sax") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Resume Saxophone</a>
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