@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Manpower Information<span class="text-purple"> 社員構成</span>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Manpower Information <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/report/stat") }}" target="_blank" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">by Status Kerja</a>
			<a href="{{ url("index/report/gender") }}" target="_blank" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">by Gender</a>
			<a href="{{ url("index/report/grade") }}" target="_blank" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">by Grade</a>
			<a href="{{ url("index/report/department") }}" target="_blank" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">by Department</a>
			<a href="{{ url("index/report/jabatan") }}" target="_blank" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">by Jabatan</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Manpower Overtime <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/report/overtime_monthly") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Monthly Overtime Control</a>
			<a href="{{ url("index/buffing") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Management by Section</a>
			<a href="{{ url("index/buffing") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Management by Employee</a>
			<a href="{{ url("index/buffing") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Overtime Control</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Attendance Information<i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/report_middle", "slip-fulfillment") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Presence by Month</a>
			<a href="{{ url("index/report_middle", "slip-fulfillment") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Presence</a>
			<a href="{{ url("index/report_middle", "slip-fulfillment") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Absence</a>
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