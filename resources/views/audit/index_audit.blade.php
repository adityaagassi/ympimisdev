@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
<!-- 	<h1>
		List Patrol<small class="text-purple"></small>
	</h1> -->
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-12" style="text-align: center;margin-bottom: 10px;">
			<h3 class="box-title" style="color: white;margin-top: 10px;font-size: 28px;font-weight: bold;background-color: #3f51b5;padding: 10px">List Audit<span class="text-purple"></span></h3>
		</div>

		<div class="col-xs-2" style="text-align: center;margin-top: ">
			<span style="font-size: 30px; color: black;"><i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i></span>

		</div>
		<div class="col-xs-5" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/audit?category=kanban') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Audit Kanban (かんばん監査)</a>
			<a href="{{ url('index/audit_iso/create_audit') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Audit ISO (ISO内部監査のCPARを作成)</a>
		</div>
		<div class="col-xs-5" style="text-align: center;">
			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/audit_kanban/monitoring') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Audit Kanban Monitoring (監査かんばんに対する監視)</a>
			<div style="width: 100%;margin-top: 5px">
				<a href="{{ url('index/audit_iso/monitoring2') }}" class="btn btn-default" style="font-size: 24px; border-color: red; width: 49%;display: inline-block;">Audit ISO 14001</a>&nbsp;&nbsp;
				<a href="{{ url('index/audit_iso/monitoring') }}" class="btn btn-default" style="font-size: 24px; border-color: red; width: 49%;display: inline-block;">Audit ISO 45001</a>
			</div>

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