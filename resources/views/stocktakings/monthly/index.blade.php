@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	#loading, #error { display: none; }
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-3" style="text-align: center;">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>

			<a href="{{ url("index/stocktaking/summary_of_counting") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Summary of Counting</a>
			<a href="{{ secure_url("index/stocktaking/count") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Count</a>
			<a href="{{ url("index/stocktaking/audit/"."1") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Audit 1</a>
			<a href="{{ url("index/stocktaking/audit/"."2") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Audit 2</a>
			<a href="javascript:void(0)" onClick="countPI()" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Count PI</a>


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


	function countPI() {
		$("#loading").show();

		$.get('{{ url("index/stocktaking/count_pi") }}', function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				openSuccessGritter('Success', result.message);
			}else{
				$("#loading").hide();
				openErrorGritter('Success', result.message);
			}

		});
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '4000'
		});
	}

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '4000'
		});
	}



</script>
@endsection