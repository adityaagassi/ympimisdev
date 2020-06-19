@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Leader Task Report<span class="text-purple"> リーダータスリポート</span>
		<small>{{$dept}}</small>
	</h1>
	<ol class="breadcrumb">
    {{-- <li></li> --}}
    	<li><a href="{{ url("index/production_report/index/".$id)}}" class="btn btn-warning" style="color:white">Back</a></li>
  	</ol>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
		</div>
		<div class="col-xs-4" style="text-align: center; color: green;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Leader Name <i class="fa fa-angle-double-down"></i></span>
			@foreach($leader as $leader)
				<a href="{{ url("index/leader_task_report/leader_task_list/".$id."/".$leader->leader_dept) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">{{ $leader->leader_dept }}</a>
			@endforeach
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
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