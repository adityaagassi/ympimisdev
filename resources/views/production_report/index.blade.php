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
					<a href="{{ url("index/activity_list/filter/".$id."/".$no) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Daily Check FG</a>
				@elseif($activity_list->activity_type == "Laporan Aktivitas")
					<a href="{{ url("index/activity_list/filter/".$id."/".$no) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Laporan Aktivitas Audit IK</a>
				@elseif($activity_list->activity_type == "Pengecekan")
					<a href="{{ url("index/activity_list/filter/".$id."/".$no) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Audit Implementasi Pengecekan</a>
				@elseif($activity_list->activity_type == "Labelisasi")
					<a href="{{ url("index/activity_list/filter/".$id."/".$no) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Labeling Safety Sign</a>
				@else
					<a href="{{ url("index/activity_list/filter/".$id."/".$no) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">{{ $activity_list->activity_type }}</a>
				@endif
			<?php $no++ ?>
			@endforeach
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span>
			{{-- <a href="{{ url("index/middle/display/Flute") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Display Buffing</a> --}}
		</div>
		<div class="col-xs-4" style="text-align: center; color: purple;">
			<span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url("index/production_report/report_all/".$id) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Leader Task Monitoring</a>
			<a href="{{ url("index/production_audit/report_audit/".$id) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Report Audit</a>
			<a href="{{ url("index/training_report/report_training/".$id) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Report Training</a>
			<a href="{{ url("index/sampling_check/report_sampling_check/".$id) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Report Sampling Check</a>
			<a href="{{ url("index/audit_report_activity/report_audit_activity/".$id) }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: purple;">Report Laporan Aktivitas</a>
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