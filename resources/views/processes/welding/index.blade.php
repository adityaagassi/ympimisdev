@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Saxophone Welding WIP<span class="text-purple"> サックス仮組 仕掛品組み立て</span>
		{{-- <small>Flute <span class="text-purple"> ??? </span></small> --}}
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">

			@foreach(Auth::user()->role->permissions as $perm)
			@php
			$navs[] = $perm->navigation_code;
			@endphp
			@endforeach

			@if(in_array('A10', $navs))
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Master Kosuha <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/welding/operator') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Master Operator</a>
			<a href="{{ url('index/welding/master_kanban','hsa-sx') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Master Kanban HSA</a>
			<a href="{{ url('index/welding/master_kanban','phs-sx') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Master Kanban PHS</a>
			<a href="{{ url('index/welding/master_kanban','hpp-sx') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Master Kanban HPP</a>
			<a href="{{ url("index/middle/buffing_target/wld") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Welding Target</a>
			@endif

			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process Kosuha <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/welding/kensa", "phs-visual-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">PHS Kensa Visual</a>
			<a href="{{ url("index/welding/kensa", "hsa-visual-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">HSA Kensa Visual</a>
			<a href="{{ url("index/welding/kensa", "hsa-dimensi-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Kensa Dimensi</a>
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process Handatsuke <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/process_stamp_sx_1") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Stamp <b><i>IoT</i></b></a>
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/welding/display_production_result?tanggal=&location=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Production Result</a>
			
			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Display Kosuha <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/welding/welding_board','hsa-sx') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">HSA Welding Board</a>
			<a href="{{ url('index/welding/welding_board','phs-sx') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">PHS Welding Board</a>
			<a href="{{ url('index/welding/welding_board','hpp-sx') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">HPP Welding Board</a>
			<a href="{{ url('index/welding/welding_board','cuci-solder') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Cuci Asam Welding Board</a>
			<a href="{{ url("index/middle/request/display/043?filter=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Middle Process Material Request</a>
			<a href="{{ url("index/welding/ng_rate?tanggal=&location=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">NG Rate</a>
			<a href="{{ url("index/welding/op_trend") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Operator Trends</a>
			<a href="{{ url("index/welding/op_ng?tanggal=&location=&group=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Operator NG Rate</a>
			<a href="{{ url("index/welding/welding_eff?tanggal=&group=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Operator Efficiency</a>
			<a href="{{ url("index/welding/welding_op_eff?tanggal=&group=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Operator Overall Efficiency</a>
			<a href="{{ url("index/welding/op_analysis") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Welding OP Analysis</a>
			<a href="{{ url("index/welding/group_achievement?tanggal=&ws=&time_from=&time_to=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">WS Achievement</a>
			<a href="{{ url("index/welding/eff_handling?tanggal=&location=") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Average Working Time</a>
			<a href="{{ url("index/welding/current_welding") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Ongoing Welding</a>

			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Display Handatsuke <i class="fa fa-angle-double-down"></i></span>
			<a href="" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Middle Process Material Request</a>
			
		</div>
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: purple;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/welding/report_ng") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Not Good Record</a>
			<a href="{{ url("index/welding/production_result") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Production Result</a>
			<a href="{{ url("index/welding/report_hourly") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Hourly Report</a>
			<span style="font-size: 30px; color: purple;"><i class="fa fa-angle-double-down"></i> Report Kosuha <i class="fa fa-angle-double-down"></i></span>			
			<a href="{{ url("index/welding/resume?bulan=&fy=&key=", "phs-visual-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">PHS Resume</a>
			<a href="{{ url("index/welding/resume?bulan=&fy=&key=", "hsa-visual-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">HSA Resume</a>
			<a href="{{ url("index/welding/resume?bulan=&fy=&key=", "hsa-dimensi-sx") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Dimensi Resume</a>
			<span style="font-size: 30px; color: purple;"><i class="fa fa-angle-double-down"></i> Report Handatsuke <i class="fa fa-angle-double-down"></i></span>
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
		$('body').toggleClass("sidebar-collapse");

	});
</script>
@endsection