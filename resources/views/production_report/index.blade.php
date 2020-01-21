@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		Activity Lists<span class="text-purple"> 活動リスト</span>
		<small>Assembly (WI-A) <span class="text-purple"> アセンブリ（WI-A）</span></small>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process Report <i class="fa fa-angle-double-down"></i></span>
			<?php $no = 1 ?>
			@foreach($activity_list as $activity_list)
				@if($activity_list->activity_type == "Pengecekan Foto")
					<a href="{{ url("index/activity_list/filter/".$id."/".$no) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Daily Check FG / KD</a>
				@elseif($activity_list->activity_type == "Laporan Aktivitas")
					<a href="{{ url("index/activity_list/filter/".$id."/".$no) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Laporan Aktivitas Audit IK</a>
				@elseif($activity_list->activity_type == "Pengecekan")
					<a href="{{ url("index/activity_list/filter/".$id."/".$no) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Audit Pengecekan Product Pertama</a>
				@elseif($activity_list->activity_type == "Labelisasi")
					<a href="{{ url("index/activity_list/filter/".$id."/".$no) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Labeling Safety Sign</a>
				@elseif($activity_list->activity_type == "Audit")
					<a href="{{ url("index/activity_list/filter/".$id."/".$no) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Audit NG Jelas</a>
				@else
					<a href="{{ url("index/activity_list/filter/".$id."/".$no) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">{{ $activity_list->activity_type }}</a>
				@endif
			<?php $no++ ?>
			@endforeach
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/production_report/report_all/".$id) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Leader Task Monitoring</a>
			<a href="{{ url("index/production_report/report_by_task/".$id) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Leader Tasks</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<?php if($role_code == "PROD-SPL" || $role_code == "MIS" || $role_code == "S"){ ?>
				<a href="{{ url("index/production_report/approval/".$id) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Approval</a>
			<?php } ?>
				<a href="{{ url("index/leader_task_report/index/".$id) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Leader Task Report</a>
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