@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		MIRAI Mobile Report<span class="text-purple"> モバイルMIRAIの記録</span>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 3vw; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<!-- <a href="{{ url("index/process_assy_fl_4") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: green;">Chousei</a> -->
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("/index/mirai_mobile/healthy_report") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Attendance & Health Report</a>
			<a href="{{ url("/radar_covid") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Radar Covid19</a>
			<a href="{{ url("index/corona_information") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Daily Corona Data</a>
			<a href="{{ url("index/mirai_mobile/corona_map") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">YMPI Corona Map</a>
			<hr style="border: 1px solid red">
			<a href="{{ url("index/survey_covid") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Survey Covid</a>
			<a href="{{ url("index/survey") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: red;">Emergency Survey</a>
			<a href="" class="btn btn-default btn-block" style="font-size: 2vw;">&nbsp;</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/mirai_mobile/report_attendance") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Attendance Data</a>
			<a href="{{ url("index/mirai_mobile/report_shift") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Group Data</a> 
			<!-- <a href="#" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Location Data</a>  -->
			<a href="{{ url("index/mirai_mobile/report_location") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Location Data</a> 
			<a href="{{ url("index/mirai_mobile/report_indication") }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Health Indication Data</a>
			<hr style="border: 1px solid red">
			<a href="{{ url('index/survey_covid/report') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Survey Covid Report</a>
			<!-- <a href="" class="btn btn-default btn-block" style="font-size: 2vw;">&nbsp;</a> -->
			<a href="{{ url('index/guest_assessment/report') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Guest Assessment Report</a>
			<a href="{{ url('index/vendor_assessment/report') }}" class="btn btn-default btn-block" style="font-size: 2vw; border-color: purple;">Vendor Assessment Report</a>
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