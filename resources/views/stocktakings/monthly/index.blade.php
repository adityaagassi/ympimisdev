@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Monthly Stock Taking<span class="text-purple"> 表面処理</span>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ secure_url("index/stocktaking/count") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Count</a>
			<a href="{{ url("index/stocktaking/audit/"."1") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Audit 1</a>
			<a href="{{ url("index/stocktaking/audit/"."2") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Audit 2</a>


			<a href="{{ url("index/stocktaking/summary_of_counting") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Summary of Counting</a>
		</div>
		<div class="col-xs-9" style="text-align: center; color: red;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
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