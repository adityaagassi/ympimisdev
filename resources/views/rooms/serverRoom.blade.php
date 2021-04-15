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
			<h3 class="box-title" style="color: white;margin-top: 10px;font-size: 28px;font-weight: bold;background-color: #3f51b5;padding: 10px">Server Room Control<span class="text-purple"></span></h3>
		</div>

		<div class="col-xs-4" style="text-align: center;margin-top: ">
			<span style="font-size: 30px; color: black;"><i class="fa fa-angle-double-down"></i> Data <i class="fa fa-angle-double-down"></i></span>
			<a href="" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">History Data</a>
		</div>
		<div class="col-xs-8" style="text-align: center;">
			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/server_room/app_status') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;margin-top: 5px">Apps Status</a>
			<a href="{{ url('index/server_room/mirai_status') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;margin-top: 5px">MIRAI Server Status</a>
			<a href="{{ url('index/server_room/database') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;margin-top: 5px">Database Status</a>
			<a href="{{ url('index/server_room/ping') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;margin-top: 5px">Internet & VPN Status</a>
			<a href="{{ url('index/server_room/speedtest') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;margin-top: 5px">Speed Test</a>
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